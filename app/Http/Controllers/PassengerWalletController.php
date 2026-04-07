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
use App\Models\BillingAddressSettingDetail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Stripe\PaymentIntent;
use Stripe\PaymentMethod;
use Stripe\Stripe;
use Stripe\Customer;

class PassengerWalletController extends Controller
{
    public function index($lang = null)
    {

        $walletSettingPage = MyWalletSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        $ProfilePage = ProfilePageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        $ProfileSetting = ProfileSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        $reviewSetting = MyReviewSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        $messages = $this->successMessage;

        $user = auth()->user();

        $myRides = Booking::where('user_id', $user->id)->select('id', 'ride_id', 'seats', 'status', 'booking_credit', 'fare', 'tax_amount', 'ride_detail_id', 'departure', 'destination', 'price')
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
                        $query->active(); // Exclude soft-deleted drivers
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

        return view('passenger_wallet_rides', [
            'reviewSetting' => $reviewSetting,
            'ProfileSetting' => $ProfileSetting,
            'ProfilePage' => $ProfilePage,
            'myRides' => $myRides,
            'walletSettingPage' => $walletSettingPage,
            'messages' => $messages
        ]);
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

        $paymentSettingDetail = BillingAddressSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);

        $cards = Card::where('user_id', $user_id)->orderBy('id', 'desc')->get();

        Stripe::setApiKey(config('stripe.secret'));
        // Fetch card details from Stripe
        foreach ($cards as $card) {
            if ($card->stripe_payment_method_id) {
                $card->paymentMethod = PaymentMethod::retrieve($card->stripe_payment_method_id);
            }
        }

        return view('buy_balance', [
            'reviewSetting' => $reviewSetting,
            'paymentSettingDetail' => $paymentSettingDetail,
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
            'stripeToken' => 'required_if:card_id,credit_card',
        ]);

        $message = $this->successMessage;

        $user = auth()->user();
        $amount = (float) $request->dr_amount;
        $amountCents = (int) round($amount * 100);

        // Helper to redirect back with a generic error
        $genericErrorResponse = function () use ($message) {
            return redirect()->back()->with(['error' => $message->general_error_message ?? 'Payment could not be completed. Please try again.']);
        };

        // Helper: after successful charge, store top-up and send receipt
        $handleSuccessfulTopUp = function (string $provider, string $providerId, ?Card $card = null) use ($user, $amount, $message) {
            $storeTopUpBalance = TopUpBalance::create([
                'user_id'   => $user->id,
                'dr_amount' => $amount,
                'stripe_id' => $provider === 'stripe' ? $providerId : null,
                'paypal_id' => $provider === 'paypal' ? $providerId : null,
                'added_date' => Carbon::now(),
            ]);

            $data = [
                'full_name'        => $user->first_name . ' ' . $user->last_name,
                'amount'           => $amount,
                'transaction_id'   => $storeTopUpBalance->random_id,
                'transaction_date' => Carbon::now()->format('F j, Y \a\t H:i \E\S\T'),
                'payment_method'   => $card?->payment_method_type ?? $provider,
                'card_type'        => $card?->card_type ?? '',
            ];

            if (isset($user->email_notification) && $user->email_notification == 1) {
                Mail::to($user->email)->send(new TopUpReceiptMail($data));
            }

            $selectedLanguage = session('selectedLanguage');
            if ($selectedLanguage) {
                $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            } else {
                $selectedLanguage = Language::where('is_default', 1)->first();
            }

            return redirect()->route('get_top_up_balance', ['lang' => $selectedLanguage->abbreviation])
                ->with(['error' => $message->topup_balance_success_message ?? 'You have successfully bought top up balance']);
        };

        // Helper: charge via Stripe PaymentIntent using a Stripe payment method id (card / Google Pay / Apple Pay)
        $chargeWithStripePaymentMethod = function (string $stripePaymentMethodId) use ($user, $amountCents, $genericErrorResponse) {
            if (!$user->stripe_customer_id) {
                return $genericErrorResponse();
            }

            Stripe::setApiKey(config('stripe.secret'));

            try {
                $paymentIntent = PaymentIntent::create([
                    'amount'        => $amountCents,
                    'currency'      => 'CAD',
                    'customer'      => $user->stripe_customer_id,
                    'payment_method' => $stripePaymentMethodId,
                    'off_session'   => true,
                    'confirm'       => true,
                ]);

                return $paymentIntent;
            } catch (\Stripe\Exception\CardException $e) {
                // Card declined or similar error
                return redirect()->back()->with(['error' => $e->getMessage()]);
            } catch (\Stripe\Exception\ApiErrorException $e) {
                // General Stripe API error
                return redirect()->back()->with(['error' => $e->getMessage()]);
            } catch (\Throwable $e) {
                return $genericErrorResponse();
            }
        };

        if ($request->card_id == 'paypal') {
            // Direct PayPal checkout (not using a saved PayPal card)
            $paypal = new PayPalClient;
            $paypal->setApiCredentials(config('paypal'));
            $token = $paypal->getAccessToken();
            $paypal->setAccessToken($token);

            $order = $paypal->createOrder([
                "intent" => "CAPTURE",
                "purchase_units" => [
                    [
                        "amount" => [
                            "currency_code" => env('DEFAULT_CURRENCY', 'CAD'),
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
        } elseif ($request->card_id == 'google_pay' || $request->card_id == 'apple_pay' || $request->card_id == 'credit_card') {
            // Use the inline Stripe token for a newly entered card, otherwise use the user's primary saved method
            $typeMap = [
                'google_pay'  => 'google_pay',
                'apple_pay'   => 'apple_pay',
                'credit_card' => 'card',
            ];

            $mappedType = $typeMap[$request->card_id] ?? null;
            if (!$mappedType) {
                return $genericErrorResponse();
            }

            if ($request->card_id === 'credit_card') {
                if (!$user->stripe_customer_id) {
                    Stripe::setApiKey(config('stripe.secret'));
                    $customer = Customer::create([
                        'email' => $user->email,
                        'name' => $user->first_name,
                    ]);
                    User::whereId($user->id)->update(['stripe_customer_id' => $customer->id]);
                    $user = User::whereId($user->id)->first();
                }

                Stripe::setApiKey(config('stripe.secret'));
                $stripeToken = $request->stripeToken;

                if (str_starts_with($stripeToken, 'tok_')) {
                    $paymentMethod = PaymentMethod::create([
                        'type' => 'card',
                        'card' => [
                            'token' => $stripeToken,
                        ],
                    ]);
                } elseif (str_starts_with($stripeToken, 'pm_')) {
                    $paymentMethod = PaymentMethod::retrieve($stripeToken);
                } else {
                    return redirect()->back()->with(['error' => $message->general_error_message ?? 'Payment method not found. Please try again.']);
                }

                $paymentMethod->attach(['customer' => $user->stripe_customer_id]);

                $paymentIntentOrResponse = $chargeWithStripePaymentMethod($paymentMethod->id);
                if ($paymentIntentOrResponse instanceof \Illuminate\Http\RedirectResponse) {
                    return $paymentIntentOrResponse;
                }

                /** @var \Stripe\PaymentIntent $paymentIntentOrResponse */
                if ($paymentIntentOrResponse->status !== 'succeeded') {
                    return $genericErrorResponse();
                }

                return $handleSuccessfulTopUp('stripe', $paymentIntentOrResponse->id);
            }

            $card = Card::where('user_id', $user->id)
                ->where('payment_method_type', $mappedType)
                ->where('primary_card', 1)
                ->first();

            if (!$card || !$card->stripe_payment_method_id) {
                return redirect()->back()->with(['error' => $message->general_error_message ?? 'Payment method not found. Please add a card first.']);
            }

            $paymentIntentOrResponse = $chargeWithStripePaymentMethod($card->stripe_payment_method_id);
            if ($paymentIntentOrResponse instanceof \Illuminate\Http\RedirectResponse) {
                // An error response was returned from the helper
                return $paymentIntentOrResponse;
            }

            /** @var \Stripe\PaymentIntent $paymentIntentOrResponse */
            if ($paymentIntentOrResponse->status !== 'succeeded') {
                return $genericErrorResponse();
            }

            return $handleSuccessfulTopUp('stripe', $paymentIntentOrResponse->id, $card);
        } elseif ($request->card_id == 'google_pay') {
            // Kept for backward compatibility; primary Google Pay handling is above.
            return $genericErrorResponse();
        } elseif ($request->card_id == 'apple_pay') {
            // Kept for backward compatibility; primary Apple Pay handling is above.
            return $genericErrorResponse();
        } elseif ($request->card_id == 'credit_card') {
            // Kept for backward compatibility; primary card handling is above.
            return $genericErrorResponse();
        } else {
            // card_id is expected to be an ID of a saved card in the cards table
            $card = Card::where('id', $request->card_id)
                ->where('user_id', $user->id)
                ->first();

            if (!$card) {
                return redirect()->back()->with(['error' => $message->general_error_message ?? 'Payment method not found. Please try again.']);
            }

            if ($card->payment_method_type === 'paypal') {
                // For saved PayPal "cards", we still redirect through PayPal checkout
                $paypal = new PayPalClient;
                $paypal->setApiCredentials(config('paypal'));
                $token = $paypal->getAccessToken();
                $paypal->setAccessToken($token);

                $order = $paypal->createOrder([
                    "intent" => "CAPTURE",
                    "purchase_units" => [
                        [
                            "amount" => [
                                "currency_code" => env('DEFAULT_CURRENCY', 'CAD'),
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
            }

            // Stripe-based saved payment methods (card / Google Pay / Apple Pay)
            if (!$card->stripe_payment_method_id) {
                return redirect()->back()->with(['error' => $message->general_error_message ?? 'Saved payment method is not properly configured.']);
            }

            $paymentIntentOrResponse = $chargeWithStripePaymentMethod($card->stripe_payment_method_id);
            if ($paymentIntentOrResponse instanceof \Illuminate\Http\RedirectResponse) {
                return $paymentIntentOrResponse;
            }

            /** @var \Stripe\PaymentIntent $paymentIntentOrResponse */
            if ($paymentIntentOrResponse->status !== 'succeeded') {
                return $genericErrorResponse();
            }

            return $handleSuccessfulTopUp('stripe', $paymentIntentOrResponse->id, $card);
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
                Mail::to($user->email)->queue(new TopUpReceiptMail($data));
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
