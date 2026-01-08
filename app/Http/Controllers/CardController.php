<?php

namespace App\Http\Controllers;

use App\Mail\CardAddedEmail;
use App\Mail\CardRemovedEmail;
use App\Models\Card;
use App\Models\Language;
use App\Models\Notification;
use App\Models\SuccessMessagesSettingDetail;
use App\Models\BillingAddressSettingDetail;
use App\Models\FCMToken;
use App\Models\MyReviewSettingDetail;
use App\Models\ProfilePageSettingDetail;
use App\Models\ProfileSettingDetail;
use App\Models\User;
use App\Services\FCMService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Stripe\PaymentMethod;
use Stripe\Customer;
use Stripe\Stripe;

class CardController extends Controller
{
    public function index($lang = null)
    {

        $paymentSettingDetail = null;
        $languages = Language::all();
        // Store the selected language in the session
        if ($lang && in_array($lang, $languages->pluck('abbreviation')->toArray())) {
            session(['selectedLanguage' => $lang]);
        }
        $selectedLanguage = session('selectedLanguage');
        $ProfilePage = null;
        $ProfileSetting = null;
        $reviewSetting = null;
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            $paymentSettingDetail = BillingAddressSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $ProfilePage = ProfilePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $ProfileSetting = ProfileSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $reviewSetting = MyReviewSettingDetail::where('language_id', $selectedLanguage->id)->select('review_left_label', 'review_received_label')->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            $paymentSettingDetail = BillingAddressSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $ProfilePage = ProfilePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $ProfileSetting = ProfileSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $reviewSetting = MyReviewSettingDetail::where('language_id', $selectedLanguage->id)->select('review_left_label', 'review_received_label')->first();
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

        $cards = Card::where('user_id', $user_id)->orderByRaw('`primary_card` DESC')->orderBy('id', 'desc')->get();

        Stripe::setApiKey(env('STRIPE_SECRET'));

        // Fetch card details from Stripe
        foreach ($cards as $card) {
            if ($card->stripe_payment_method_id) {
                $card->paymentMethod = PaymentMethod::retrieve($card->stripe_payment_method_id);
            }
        }
        return view('my_cards', ['reviewSetting' => $reviewSetting, 'ProfilePage' => $ProfilePage, 'ProfileSetting' => $ProfileSetting, 'cards' => $cards, 'notifications' => $notifications, 'languages' => $languages, 'selectedLanguage' => $selectedLanguage, 'paymentSettingDetail' => $paymentSettingDetail]);
    }

    public function sessionData(Request $request)
    {


        session(['bookingId' => $request->bookingId]);
        session(['rideDetailId' => $request->rideDetailId]);
        session(['rideId' => $request->rideId]);
        session(['type' => $request->type]);
    }

    public function create($lang = null, Request $request)
    {
        $paymentSettingDetail = null;
        $languages = Language::all();
        // Store the selected language in the session
        if ($lang && in_array($lang, $languages->pluck('abbreviation')->toArray())) {
            session(['selectedLanguage' => $lang]);
        }
        $selectedLanguage = session('selectedLanguage');
        $ProfilePage = null;
        $ProfileSetting = null;
        if ($selectedLanguage) {
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            $paymentSettingDetail = BillingAddressSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $ProfilePage = ProfilePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $ProfileSetting = ProfileSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $reviewSetting = MyReviewSettingDetail::where('language_id', $selectedLanguage->id)->select('review_left_label', 'review_received_label')->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            $paymentSettingDetail = BillingAddressSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $ProfilePage = ProfilePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $ProfileSetting = ProfileSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $reviewSetting = MyReviewSettingDetail::where('language_id', $selectedLanguage->id)->select('review_left_label', 'review_received_label')->first();
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

        return view('create_card', ['reviewSetting' => $reviewSetting, 'ProfilePage' => $ProfilePage, 'ProfileSetting' => $ProfileSetting, 'notifications' => $notifications, 'languages' => $languages, 'selectedLanguage' => $selectedLanguage, 'paymentSettingDetail' => $paymentSettingDetail]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $user_id = $user->id;

        // Validate the form data
        $validatedData = $request->validate([
            'name_on_card' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s\-]+$/'],
            // 'card_type' => 'required',
            'street_address' => 'required',
            'house_apartment_number' => 'nullable',
            'city' => 'required',
            'province' => 'required',
            'country' => 'required',
            'postal_code' => 'required|string|max:255',
            'stripeToken' => 'required',
        ], [
            'name_on_card.regex' => 'Cardholder name can only contain letters, spaces, and hyphens',
        ]);

        // Set Stripe API key
        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            if (!$user->stripe_customer_id) {
                // Create a new Stripe customer
                $customer = Customer::create([
                    'email' => $user->email,
                    'name' => $user->first_name,
                ]);

                User::whereId($user_id)->update([
                    'stripe_customer_id' => $customer->id,
                ]);

                $user = User::whereId($user_id)->first();
            }

            // Create a PaymentMethod with Stripe using the token
            $paymentMethod = PaymentMethod::create([
                'type' => 'card',
                'card' => ['token' => $request->stripeToken],
                'billing_details' => [
                    'name' => $request->name_on_card,
                    'address' => [
                        'line1' => $request->street_address . $request->house_apartment_number . $request->city . $request->province . $request->country . $request->postal_code,
                    ],
                ],
            ]);

            $message = null;
            $selectedLanguage = session('selectedLanguage', Language::where('is_default', 1)->first()->abbreviation);
            if ($selectedLanguage) {
                // Find the language by abbreviation
                $language = Language::where('abbreviation', $selectedLanguage)->first();
                $message = SuccessMessagesSettingDetail::where('language_id', $language->id)->select('card_add_message', 'already_added_card_message')->first();
            }

            // Check if the card fingerprint already exists
            $existingCard = Card::where('user_id', $user_id)->where('fingerprint', $paymentMethod->card->fingerprint)->first();
            if ($existingCard) {
                // If the fingerprint exists, return back with an error message
                return back()->withErrors(['error' => $message->already_added_card_message])->withInput();
            }

            // Handle primary card setting
            // Check if this is user's first card - auto-set as primary
            $userCardCount = Card::where('user_id', $user_id)->count();
            if ($userCardCount == 0) {
                $primary_card = 1;
            } else {
                $primary_card = $request->filled('primary_card') ? $request->primary_card : 0;
                if ($primary_card == 1) {
                    Card::where('user_id', $user_id)->update(['primary_card' => 0]);
                }
            }

            // Store card details in the database
            $card = Card::create([
                'user_id' => $user_id,
                'name_on_card' => $request->name_on_card,
                'card_number' => $paymentMethod->card->last4,
                'card_type' => $paymentMethod->card->brand,
                'exp_month' => $paymentMethod->card->exp_month,
                'exp_year' => $paymentMethod->card->exp_year,
                'address' => $request->street_address . "," . $request->house_apartment_number . "," . $request->city . "," . $request->province . "," . $request->country . "," . $request->postal_code,
                'primary_card' => $primary_card,
                'fingerprint' => $paymentMethod->card->fingerprint,
                'stripe_payment_method_id' => $paymentMethod->id,
            ]);
            if (isset($user->email_notification) && $user->email_notification == 1) {
            $emailData = [
                'first_name' => $user->first_name,
            ];
            Mail::to($user->email)->send(new CardAddedEmail($emailData));
            }
            // if(isset($request->param) && $request->param=='booking'){

            //     return redirect()->back();
            // }
            $bookingId = session('bookingId');
            $rideDetailId = session('rideDetailId');
            $rideId = session('rideId');
            $type = session('type');
            session()->forget(['rideDetailId', 'rideId', 'type']);

            if ($type == 'booking') {
                return redirect()->route('booking', ['lang' => $selectedLanguage, 'id' => $rideId, 'rideDetailId' => $rideDetailId]);
            }
            
            if ($type == 'edit-booking') {
                return redirect()->route('booking.edit', ['lang' => $selectedLanguage, 'id' => $bookingId]);
            }
            return redirect()->route('my_cards', ['lang' => $selectedLanguage])->with('message', $message->card_add_message);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'An error occurred while processing your card. Please try again.']);
        }
    }

    public function primary($id)
    {
        $user = auth()->user();
        $card = Card::find($id);

        // Ensure the card belongs to the authenticated user
        if ($card->user_id != $user->id) {
            return back()->withErrors(['error' => 'You do not have permission to set this card as primary']);
        }

        Card::where('user_id', $card->user_id)->update(['primary_card' => 0]);

        $card->primary_card = 1;
        $card->save();

        $message = null;
        $selectedLanguage = session('selectedLanguage', Language::where('is_default', 1)->first()->abbreviation);
        if ($selectedLanguage) {
            $language = Language::where('abbreviation', $selectedLanguage)->first();
            $message = SuccessMessagesSettingDetail::where('language_id', $language->id)->select('card_primary_message')->first();
        }
        return redirect()->route('my_cards', ['lang' => $selectedLanguage])->with('message', $message->card_primary_message);
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $card = Card::find($id);

        // Ensure the card belongs to the authenticated user
        if ($card->user_id != $user->id) {
            return back()->withErrors(['error' => 'You do not have permission to delete this card']);
        }

        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            $paymentMethod = PaymentMethod::retrieve($card->stripe_payment_method_id);

            // Check if the payment method is attached to a customer
            if ($paymentMethod->customer) {
                // Detach the payment method from the customer on Stripe
                $paymentMethod->detach();
            }

            // Check if we're deleting the primary card
            $wasPrimary = $card->primary_card == '1' || $card->primary_card == 1;
            $userId = $card->user_id;

            // Delete the card record from the database
            $card->delete();

            // If we deleted the primary card, set the most recent remaining card as primary
            if ($wasPrimary) {
                $firstRemainingCard = Card::where('user_id', $userId)
                    ->orderBy('id', 'desc')
                    ->first();
                if ($firstRemainingCard) {
                    $firstRemainingCard->update(['primary_card' => '1']);
                }
            }

            if (isset($user->email_notification) && $user->email_notification == 1) {
                $emailData = [
                    'first_name' => $user->first_name,
                ];
                Mail::to($user->email)->send(new CardRemovedEmail($emailData));
            }

            $notification = Notification::create([
                'type' => null,
                'category' => 'system',
                'receiver_id' => $user->id,
                'posted_by' => $user->id,
                'message' =>  'Card removed from your profile',
                'status' => 'payment_option',
                'notification_type' => 'payment_option',
            ]);
    
            $fcmToken = $user->mobile_fcm_token;
            $body = $notification->message;
            $fcmService = new FCMService();
    
            if ($fcmToken) {
                // Send the booking notification
                $fcmService->sendNotification($fcmToken, $body);
            }
    
            $fcm_tokens = FCMToken::where('user_id', $user->id)->get();
    
            foreach ($fcm_tokens as $fcm_token) {
                try {
                    $fcmService->sendNotification($fcm_token->token, $body);
                } catch (\Exception $e) {
                    Log::error("FCM Notification failed for token: $fcm_token, Error: " . $e->getMessage());
                }
            }

            $message = null;
            $selectedLanguage = session('selectedLanguage', Language::where('is_default', 1)->first()->abbreviation);
            if ($selectedLanguage) {
                $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('card_delete_message')->first();
            }
            return redirect()->route('my_cards', ['lang' => $selectedLanguage])->with('message', $message->card_delete_message);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'An error occurred while deleting your card. Please try again']);
        }
    }
}
