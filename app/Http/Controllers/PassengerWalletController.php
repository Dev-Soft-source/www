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
    public function index($lang = null){

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

        $languages = Language::all();
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

        $myRides = Booking::where('user_id', $user_id)->select('id', 'ride_id' , 'seats', 'status', 'booking_credit', 'fare', 'tax_amount', 'ride_detail_id', 'departure', 'destination', 'price')
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

        return view('passenger_wallet_rides',['reviewSetting' => $reviewSetting,'ProfileSetting' => $ProfileSetting,'ProfilePage' => $ProfilePage,'myRides' => $myRides,'notifications' => $notifications,'languages' => $languages,'selectedLanguage' => $selectedLanguage, 'walletSettingPage' => $walletSettingPage, 'messages' => $messages]);
    }

    public function reward($lang = null){
        $walletSettingPage = null;

        $languages = Language::all();
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

        $rewardPointSettings = RewardPointSettingDetail::whereHas('rewardPointSetting', function ($query) {
            $query->where('type', 'student');
        })->with('rewardPointSetting')->where('language_id', $selectedLanguage->id)->get();

        $studentTotalRewardPoint = RewardPoint::where('type', 'student')->where('user_id', $user_id)->where('status', 'pending')->sum('point');

        return view('passenger_wallet_rewards',['reviewSetting' => $reviewSetting,'rewardPointSettings' => $rewardPointSettings,'studentTotalRewardPoint' => $studentTotalRewardPoint,'notifications' => $notifications,'languages' => $languages,'selectedLanguage' => $selectedLanguage, 'walletSettingPage' => $walletSettingPage,'ProfileSetting' => $ProfileSetting,'ProfilePage' => $ProfilePage]);
    }

    public function getTopUpBalance($lang = null){

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

        $languages = Language::all();
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

        return view('my_balance',['balance' => $balance,'reviewSetting' => $reviewSetting,'ProfilePage' => $ProfilePage,'ProfileSetting' => $ProfileSetting,'topUpBalances' => $topUpBalances,'notifications' => $notifications,'languages' => $languages,'selectedLanguage' => $selectedLanguage, 'walletSettingPage' => $walletSettingPage, 'messages' => $messages]);
    }

    public function createTopUpBalance($lang = null){
        $user_id = auth()->user()->id;
        $user = User::whereId($user_id)->first();
        // Check if user has suspanded
        if ($user->suspand === '1') {
            return back()->with('message', 'Your account has been suspended by the admin');
        }

        $languages = Language::all();
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
                // Retrieve the HomePageSettingDetail associated with the selected language
                $postRidePage = PostRidePageSettingDetail::where('language_id', $selectedLanguage->id)->select('booking_option1', 'booking_option2', 'payment_methods_option1', 'payment_methods_option2', 'smoking_option1', 'animals_option1', 'animals_option2', 'animals_option3','features_option1','features_option2', 'features_option3', 'features_option4', 'features_option5', 'features_option6', 'features_option7', 'features_option8', 'features_option9', 'features_option10', 'features_option11', 'features_option12', 'features_option13', 'features_option14', 'features_option15', 'features_option16')->first();
                $ProfilePage = ProfilePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $ProfileSetting = ProfileSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $reviewSetting = MyReviewSettingDetail::where('language_id', $selectedLanguage->id)->select('review_left_label', 'review_received_label')->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                // Retrieve the HomePageSettingDetail associated with the selected language
                $postRidePage = PostRidePageSettingDetail::where('language_id', $selectedLanguage->id)->select('booking_option1', 'booking_option2', 'payment_methods_option1', 'payment_methods_option2', 'smoking_option1', 'animals_option1', 'animals_option2', 'animals_option3', 'features_option1','features_option2', 'features_option3', 'features_option4', 'features_option5', 'features_option6', 'features_option7', 'features_option8', 'features_option9', 'features_option10', 'features_option11', 'features_option12', 'features_option13', 'features_option14', 'features_option15', 'features_option16')->first();
                $ProfilePage = ProfilePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $ProfileSetting = ProfileSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $reviewSetting = MyReviewSettingDetail::where('language_id', $selectedLanguage->id)->select('review_left_label', 'review_received_label')->first();
            }
        }
        
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

        $cards = Card::where('user_id',$user_id)->orderBy('id', 'desc')->get();

        Stripe::setApiKey(env('STRIPE_SECRET'));
        // Fetch card details from Stripe
        foreach ($cards as $card) {
            if ($card->stripe_payment_method_id) {
                $card->paymentMethod = PaymentMethod::retrieve($card->stripe_payment_method_id);
            }
        }

        return view('buy_balance',['reviewSetting' => $reviewSetting,'ProfilePage' => $ProfilePage,'ProfileSetting' => $ProfileSetting,'cards' => $cards,'notifications' => $notifications,'languages' => $languages,'selectedLanguage' => $selectedLanguage,'postRidePage' => $postRidePage]);
    }

    public function storeTopUpBalance(Request $request){
        $validated = $request->validate([
            'payment_method' => 'required',
            'card_id' => $request->payment_method == 'credit_card' && $request->gPayApplePayId == '0' ? 'required' : 'nullable',
            'dr_amount' => 'required',
        ]);

        
        $selectedLanguage = session('selectedLanguage');

        $message = null;
        if ($selectedLanguage) {
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            if ($selectedLanguage) {
                $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('general_error_message', 'topup_balance_success_message','card_expiry_message')->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('general_error_message', 'topup_balance_success_message','card_expiry_message')->first();
            }
        }

        

        $user = auth()->user();

        if ($request->payment_method == 'paypal') {
            $paypal = new PayPalClient;
            $paypal->setApiCredentials(config('paypal'));
            $token = $paypal->getAccessToken();
            $paypal->setAccessToken($token);

            if ($request->dr_amount > '0') {
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
            }

            if (isset($order['id'])) {
                foreach ($order['links'] as $link) {
                    if ($link['rel'] == 'approve') {
                        return redirect()->away($link['href']);
                    }
                }
            }

            return redirect()->route('paypal.cancel');
        } elseif ($request->payment_method == 'credit_card') {

            $stripId = "";
            if(isset($request->gPayApplePayId) && $request->gPayApplePayId != '0'){
                $stripId = $request->gPayApplePayId;
            }else{
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
                'full_name' => $user->first_name.' '.$user->last_name,
                'amount' => $request->dr_amount,
                'transaction_id' => $storeTopUpBalance->random_id, 
                'transaction_date' => Carbon::now()->format('F j, Y \a\t H:i \E\S\T'),
                'payment_method' => 'credit_card',
                'card_type' => isset($request->gPayApplePayId) && $request->gPayApplePayId != '0' ? 'Gpay/ApplePay' : $card->card_type, 
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

        return redirect()->back()->with(['error' => $message->general_error_message ?? 'top up not found']);
    }

    public function successTransaction($dr_amount, Request $request)
    {

        $selectedLanguage = session('selectedLanguage');

        $message = null;
        if ($selectedLanguage) {
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            if ($selectedLanguage) {
                $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('general_error_message', 'topup_balance_success_message','card_expiry_message')->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('general_error_message', 'topup_balance_success_message','card_expiry_message')->first();
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
                'full_name' => $user->first_name.' '.$user->last_name,
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
