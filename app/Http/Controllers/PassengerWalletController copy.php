<?php

namespace App\Http\Controllers;

use App\Mail\TopUpReceiptMail;
use App\Models\Booking;
use App\Models\Card;
use App\Models\Language;
use App\Models\MyReviewSettingDetail;
use App\Models\Notification;
use App\Models\PostRidePageSettingDetail;
use App\Models\Ride;
use App\Models\TopUpBalance;
use App\Models\MyWalletSettingDetail;
use App\Models\ProfilePageSettingDetail;
use App\Models\ProfileSettingDetail;
use App\Models\RewardPoint;
use App\Models\RewardPointSettingDetail;
use App\Models\SuccessMessagesSettingDetail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Stripe\PaymentIntent;
use Stripe\PaymentMethod;
use Stripe\Stripe;

class PassengerWalletController extends Controller
{
    public function index($lang = null)
    {

        $walletSettingPage = null;
        $messages = null;

        $rides = Ride::where('added_by', auth()->user()->id)
            ->where('status', '!=', 2)
            ->where(function ($query) {
                $query->where(function ($query) {
                    $query->whereDate('completed_date', '>', now()->toDateString())
                        ->orWhere(function ($query) {
                            $query->whereDate('completed_date', '=', now()->toDateString())
                                ->whereTime('completed_time', '>=', now()->toTimeString());
                        });
                });
            })
            ->orderBy('id', 'desc')
            ->paginate(6);

        $languages = Language::getAllCached();
        // Store the selected language in the session
        if ($lang && in_array($lang, $languages->pluck('abbreviation')->toArray())) {
            session(['selectedLanguage' => $lang]);
        }
        $selectedLanguage = session('selectedLanguage');
        $ProfilePage = null;
        $ProfileSetting = null;
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            if ($selectedLanguage) {
                $walletSettingPage = MyWalletSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('withdraw_message')->first();
                $ProfilePage = ProfilePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $ProfileSetting = ProfileSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $reviewSetting = MyReviewSettingDetail::where('language_id', $selectedLanguage->id)->select('review_left_label', 'review_received_label')->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $walletSettingPage = MyWalletSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('withdraw_message')->first();
                $ProfilePage = ProfilePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $ProfileSetting = ProfileSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $reviewSetting = MyReviewSettingDetail::where('language_id', $selectedLanguage->id)->select('review_left_label', 'review_received_label')->first();
            }
        }

        $notifications = null;
        if (auth()->user()) {
            $user_id = auth()->user()->id;
            $notifications = Notification::where('is_delete', '0')->where(function ($query) use ($user_id) {
                // Ratings where type is 1 and ride_id belongs to the user
                $query->where('type', '1')
                    ->whereHas('ride', function ($query) use ($user_id) {
                        $query->where('added_by', $user_id);
                    });
            })
                ->orWhere(function ($query) use ($user_id) {
                    // Ratings where type is 2 and booking_id belongs to the user
                    $query->where('type', '2')
                        ->whereHas('booking', function ($query) use ($user_id) {
                            $query->where('user_id', $user_id);
                        });
                })
                ->orWhere(function ($query) use ($user_id) {
                    // Ratings where type is null and receiver_id belongs to the user
                    $query->where('type', null)
                        ->whereHas('receiver', function ($query) use ($user_id) {
                            $query->where('id', $user_id);
                        });
                })
                ->orderBy('id', 'desc')
                ->get();
        }

        $myRides = Booking::where('user_id', $user_id)->select('id', 'ride_id', 'seats', 'status', 'booking_credit', 'fare', 'tax_amount', 'ride_detail_id', 'departure', 'destination', 'price')
            ->where('status', '!=', '4')
            ->whereHas('ride', function ($query) {
                $query->where(function ($query) {
                    $query->whereDate('completed_date', '<=', now()->toDateString())
                        ->orWhere(function ($query) {
                            $query->whereDate('completed_date', '=', now()->toDateString())
                                ->whereTime('completed_time', '<=', now()->toTimeString());
                        });
                })
                    ->whereHas('driver', function ($query) {
                        $query->whereNull('deleted_at'); // Exclude soft-deleted drivers
                    });
            })
            ->with(['ride' => function ($query) {
                $query->with(['driver' => function ($query) {
                    $query->select('id', 'first_name', 'last_name', 'gender', 'profile_image', 'dob'); // Specify the columns to select
                }]);
            }])
            ->with(['booking_transaction_sum', 'booking_cancel_transaction_sum', 'booking_credit_sum', 'booking_credit_cancel_sum'])
            ->orderBy('ride_id', 'desc')
            ->get();

        return view('passenger_wallet_rides', ['reviewSetting' => $reviewSetting, 'ProfileSetting' => $ProfileSetting, 'ProfilePage' => $ProfilePage, 'myRides' => $myRides, 'notifications' => $notifications, 'languages' => $languages, 'selectedLanguage' => $selectedLanguage, 'walletSettingPage' => $walletSettingPage, 'messages' => $messages]);
    }

    public function reward($lang = null)
    {
        $walletSettingPage = null;

        $languages = Language::getAllCached();
        // Store the selected language in the session
        if ($lang && in_array($lang, $languages->pluck('abbreviation')->toArray())) {
            session(['selectedLanguage' => $lang]);
        }
        $selectedLanguage = session('selectedLanguage');
        $ProfilePage = null;
        $ProfileSetting = null;
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            if ($selectedLanguage) {
                $walletSettingPage = MyWalletSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $ProfilePage = ProfilePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $ProfileSetting = ProfileSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $reviewSetting = MyReviewSettingDetail::where('language_id', $selectedLanguage->id)->select('review_left_label', 'review_received_label')->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $walletSettingPage = MyWalletSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $ProfilePage = ProfilePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $ProfileSetting = ProfileSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $reviewSetting = MyReviewSettingDetail::where('language_id', $selectedLanguage->id)->select('review_left_label', 'review_received_label')->first();
            }
        }

        $notifications = null;
        if (auth()->user()) {
            $user_id = auth()->user()->id;
            $notifications = Notification::where('is_delete', '0')->where(function ($query) use ($user_id) {
                // Ratings where type is 1 and ride_id belongs to the user
                $query->where('type', '1')
                    ->whereHas('ride', function ($query) use ($user_id) {
                        $query->where('added_by', $user_id);
                    });
            })
                ->orWhere(function ($query) use ($user_id) {
                    // Ratings where type is 2 and booking_id belongs to the user
                    $query->where('type', '2')
                        ->whereHas('booking', function ($query) use ($user_id) {
                            $query->where('user_id', $user_id);
                        });
                })
                ->orWhere(function ($query) use ($user_id) {
                    // Ratings where type is null and receiver_id belongs to the user
                    $query->where('type', null)
                        ->whereHas('receiver', function ($query) use ($user_id) {
                            $query->where('id', $user_id);
                        });
                })
                ->orderBy('id', 'desc')
                ->get();
        }

        // Use student rewards for students, passenger rewards for non-students
        $rewardType = (auth()->user()->student != 0) ? 'student' : 'passenger';

        $rewardPointSettings = RewardPointSettingDetail::whereHas('rewardPointSetting', function ($query) use ($rewardType) {
            $query->where('type', $rewardType);
        })->with('rewardPointSetting')->where('language_id', $selectedLanguage->id)->get();

        $studentTotalRewardPoint = RewardPoint::where('type', $rewardType)->where('user_id', $user_id)->where('status', 'pending')->sum('point');

        return view('passenger_wallet_rewards', ['reviewSetting' => $reviewSetting, 'rewardPointSettings' => $rewardPointSettings, 'studentTotalRewardPoint' => $studentTotalRewardPoint, 'notifications' => $notifications, 'languages' => $languages, 'selectedLanguage' => $selectedLanguage, 'walletSettingPage' => $walletSettingPage, 'ProfileSetting' => $ProfileSetting, 'ProfilePage' => $ProfilePage]);
    }

    public function getTopUpBalance($lang = null)
    {

        $walletSettingPage = null;
        $messages = null;
        $rides = Ride::where('added_by', auth()->user()->id)
            ->where('status', '!=', 2)
            ->where(function ($query) {
                $query->where(function ($query) {
                    $query->whereDate('completed_date', '>', now()->toDateString())
                        ->orWhere(function ($query) {
                            $query->whereDate('completed_date', '=', now()->toDateString())
                                ->whereTime('completed_time', '>=', now()->toTimeString());
                        });
                });
            })
            ->orderBy('id', 'desc')
            ->paginate(6);

        $languages = Language::getAllCached();
        // Store the selected language in the session
        if ($lang && in_array($lang, $languages->pluck('abbreviation')->toArray())) {
            session(['selectedLanguage' => $lang]);
        }
        $selectedLanguage = session('selectedLanguage');
        $ProfilePage = null;
        $ProfileSetting = null;
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            if ($selectedLanguage) {
                $walletSettingPage = MyWalletSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('withdraw_message')->first();
                $ProfilePage = ProfilePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $ProfileSetting = ProfileSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $reviewSetting = MyReviewSettingDetail::where('language_id', $selectedLanguage->id)->select('review_left_label', 'review_received_label')->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $walletSettingPage = MyWalletSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('withdraw_message')->first();
                $ProfilePage = ProfilePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $ProfileSetting = ProfileSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $reviewSetting = MyReviewSettingDetail::where('language_id', $selectedLanguage->id)->select('review_left_label', 'review_received_label')->first();
            }
        }

        $notifications = null;
        if (auth()->user()) {
            $user_id = auth()->user()->id;
            $notifications = Notification::where('is_delete', '0')->where(function ($query) use ($user_id) {
                // Ratings where type is 1 and ride_id belongs to the user
                $query->where('type', '1')
                    ->whereHas('ride', function ($query) use ($user_id) {
                        $query->where('added_by', $user_id);
                    });
            })
                ->orWhere(function ($query) use ($user_id) {
                    // Ratings where type is 2 and booking_id belongs to the user
                    $query->where('type', '2')
                        ->whereHas('booking', function ($query) use ($user_id) {
                            $query->where('user_id', $user_id);
                        });
                })
                ->orWhere(function ($query) use ($user_id) {
                    // Ratings where type is null and receiver_id belongs to the user
                    $query->where('type', null)
                        ->whereHas('receiver', function ($query) use ($user_id) {
                            $query->where('id', $user_id);
                        });
                })
                ->orderBy('id', 'desc')
                ->get();
        }

        $topUpBalances = TopUpBalance::with(['booking:id,user_id', 'user:id,first_name,last_name'])
            ->where('user_id', $user_id)
            ->get();

        $getDrAmount = $topUpBalances->sum('dr_amount');
        $getCrAmount = $topUpBalances->sum('cr_amount');
        $balance = round(($getDrAmount - $getCrAmount), 0);

        return view('my_balance', ['balance' => $balance, 'reviewSetting' => $reviewSetting, 'ProfilePage' => $ProfilePage, 'ProfileSetting' => $ProfileSetting, 'topUpBalances' => $topUpBalances, 'notifications' => $notifications, 'languages' => $languages, 'selectedLanguage' => $selectedLanguage, 'walletSettingPage' => $walletSettingPage, 'messages' => $messages]);
    }

    public function createTopUpBalance($lang = null)
    {
        $user_id = auth()->user()->id;
        $user = User::whereId($user_id)->first();
        // Check if user has suspanded
        if ($user->suspand === '1') {
            return back()->with('message', $this->successMessage['admin_block_account_message'] ?? 'Your account has been suspended by the admin');
        }

        $postRidePage = PostRidePageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        $ProfilePage = ProfilePageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        $ProfileSetting = ProfileSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        $reviewSetting = MyReviewSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);

        $cards = Card::where('user_id', $user_id)->orderBy('id', 'desc')->get();

        Stripe::setApiKey(env('STRIPE_SECRET'));
        // Fetch card details from Stripe
        foreach ($cards as $card) {
            if ($card->stripe_payment_method_id) {
                $card->paymentMethod = PaymentMethod::retrieve($card->stripe_payment_method_id);
            }
        }

        return view('buy_balance', [
            'reviewSetting' => $reviewSetting,
            'ProfilePage' => $ProfilePage,
            'ProfileSetting' => $ProfileSetting,
            'cards' => $cards,
            'postRidePage' => $postRidePage
        ]);
    }

    public function storeTopUpBalance(Request $request)
    {
        $validated = $request->validate([
            'card_id' => 'required',
            'dr_amount' => ['required', 'numeric', 'gt:0'],
            'name_on_card' => 'required_if:card_id,credit_card',
            'card_element' => 'required_if:card_id,credit_card',
        ]);

        $message = $this->successMessage;

        $user = auth()->user();

        if ($request->card_id == 'paypal') {
            $paypal = new PayPalClient;
            $paypal->setApiCredentials(config('paypal'));
            $token = $paypal->getAccessToken();
            $paypal->setAccessToken($token);

            $order = $paypal->createOrder([
                "intent" => "CAPTURE",
                "purchase_units" => [
                    [
                        "amount" => [
                            "currency_code" => "USD",
                            "value" => $request->dr_amount
                        ]
                    ]
                ],
                "application_context" => [
                    "cancel_url" => route('paypal.cancel'),
                    "return_url" => route('paypal.success.top-up', ['dr_amount' => $request->dr_amount]),
                ]
            ]);

            if (isset($order['id'])) {
                foreach ($order['links'] as $link) {
                    if ($link['rel'] == 'approve') {
                        return redirect()->away($link['href']);
                    }
                }
            }

            return redirect()->route('paypal.cancel');
        } elseif ($request->card_id == 'credit_card') {

            $stripId = "";
            if (isset($request->gPayApplePayId) && $request->gPayApplePayId != '0') {
                Stripe::setApiKey(env('STRIPE_SECRET'));
                try {
                    $paymentIntent = PaymentIntent::retrieve($request->gPayApplePayId);
                    if ($paymentIntent->status !== 'succeeded') {
                        return redirect()->back()->with(['error' => $message->general_error_message ?? 'Payment was not completed. Please try again.']);
                    }
                    $expectedAmount = (int) round((float) $request->dr_amount * 100);
                    if ($paymentIntent->amount !== $expectedAmount) {
                        return redirect()->back()->with(['error' => $message->general_error_message ?? 'Payment amount does not match. Please try again.']);
                    }
                    $stripId = $request->gPayApplePayId;
                } catch (\Stripe\Exception\ApiErrorException $e) {
                    return redirect()->back()->with(['error' => $message->general_error_message ?? 'Invalid payment. Please try again.']);
                }
            } else {
                $card = Card::where('id', $request->card_id)
                    ->where('user_id', $user->id)
                    ->firstOrFail();
                Stripe::setApiKey(env('STRIPE_SECRET'));

                try {
                    $paymentMethod = PaymentMethod::retrieve($card->stripe_payment_method_id);
                    $paymentMethod->attach(['customer' => $user->stripe_customer_id]);

                    $paymentIntent = PaymentIntent::create([
                        'amount' => $request->input('dr_amount') * 100,
                        'currency' => 'usd',
                        'customer' => $user->stripe_customer_id,
                        'payment_method' => $paymentMethod->id,
                        'off_session' => true,
                        'confirm' => true,
                    ]);

                    $stripId = $paymentIntent->id;
                } catch (\Stripe\Exception\CardException $e) {
                    // Handle Stripe card-related errors
                    if ($e->getError()->code === 'card_declined' && $e->getError()->decline_code === 'expired_card') {
                        return redirect()->back()->with(['error' => $message->card_expiry_message ?? 'The card has expired. Please use a different card']);
                    }

                    // General Stripe card-related error message
                    return redirect()->back()->with(['error' => $e->getMessage()]);
                } catch (\Stripe\Exception\ApiErrorException $e) {
                    // Handle error
                    return redirect()->back()->with(['error' => $e->getMessage()]);
                }
            }

            // Payment successful, handle booking logic here
            $storeTopUpBalance = TopUpBalance::create([
                'user_id' => $user->id,
                'dr_amount' => $request->dr_amount,
                'stripe_id' => $stripId,
                'added_date' => Carbon::now(),
            ]);

            $data = [
                'full_name' => $user->first_name . ' ' . $user->last_name,
                'amount' => $request->dr_amount,
                'transaction_id' => $storeTopUpBalance->random_id,
                'transaction_date' => Carbon::now()->format('F j, Y \a\t H:i \E\S\T'),
                'payment_method' => 'credit_card',
                'card_type' => isset($request->gPayApplePayId) && $request->gPayApplePayId != '0' ? 'Gpay/ApplePay' : $card->card_type,
            ];

            if (isset($user->email_notification) && $user->email_notification == 1) {
                Mail::to($user->email)->queue(new TopUpReceiptMail($data));
            }

            return redirect()->route('get_top_up_balance', ['lang' => $this->selectedLanguage->abbreviation])->with(['error' => $message->topup_balance_success_message ?? "You have successfully buy top up balance"]);
        } else {
            // 
            $card_id = $request->card_id;
        }

        return redirect()->back()->with(['error' => $message->general_error_message ?? 'top up not found']);
    }

    public function successTransaction($dr_amount, Request $request)
    {

        $selectedLanguage = session('selectedLanguage');

        $message = null;
        if ($selectedLanguage) {
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            if ($selectedLanguage) {
                $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('general_error_message', 'topup_balance_success_message', 'card_expiry_message')->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('general_error_message', 'topup_balance_success_message', 'card_expiry_message')->first();
            }
        }

        $paypal = new PayPalClient;
        $paypal->setApiCredentials(config('paypal'));
        $token = $paypal->getAccessToken();
        $paypal->setAccessToken($token);

        $result = $paypal->capturePaymentOrder($request->get('token'));

        if ($result['status'] == 'COMPLETED') {
            $captureId = $result['purchase_units'][0]['payments']['captures'][0]['id'] ?? null;

            // Payment successful, handle Top Up  Balance logic here
            $storeTopUpBalance = TopUpBalance::create([
                'user_id' => auth()->user()->id,
                'dr_amount' => $dr_amount,
                'paypal_id' => $captureId,
                'added_date' => Carbon::now(),
            ]);
            $user = auth()->user();
            $data = [
                'full_name' => $user->first_name . ' ' . $user->last_name,
                'amount' => $dr_amount,
                'transaction_id' => $storeTopUpBalance->random_id, // Use the random_id from top_up_balances
                'transaction_date' => Carbon::now()->format('F j, Y'),
                'payment_method' => 'paypal',
                'paypal_email' => $user->email, // Make sure this field exists in your users table
            ];

            if (isset($user->email_notification) && $user->email_notification == 1) {
                Mail::to($user->email)->send(new TopUpReceiptMail($data));
            }

            $selectedLanguage = session('selectedLanguage');
            if ($selectedLanguage) {
                // Find the language by abbreviation
                $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            } else {
                $selectedLanguage = Language::where('is_default', 1)->first();
            }

            return redirect()->route('get_top_up_balance', ['lang' => $selectedLanguage->abbreviation])->with(['error' => $message->topup_balance_success_message ?? "You have successfully buy top up balance"]);
        }

        return redirect()
            ->route('home')
            ->with('message', 'Transaction failed.');
    }
}
