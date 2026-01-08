<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Mail\CardAddedEmail;
use App\Mail\CardRemovedEmail;
use App\Models\Card;
use App\Models\FCMToken;
use App\Models\User;
use App\Models\PaymentSettingDetail;
use App\Models\Language;
use App\Models\Notification;
use App\Models\SuccessMessagesSettingDetail;
use App\Services\FCMService;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Customer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Stripe\PaymentMethod;
use Stripe\Stripe;

class PaymentOptionsController extends Controller
{
    use StatusResponser;

    public function index(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        $user_id = $user->id;
        $cards = Card::where('user_id', $user_id)->orderBy('primary_card', 'desc')->orderBy('id', 'desc')->paginate($request->paginate_limit);

        $paymentOptionPage = null;
        if ($request->lang_id && $request->lang_id != 0) {
            $paymentOptionPage = PaymentSettingDetail::where('language_id', $request->lang_id)->first();
            $messages = SuccessMessagesSettingDetail::where('language_id', $request->lang_id)->select('delete_card_message')->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $paymentOptionPage = PaymentSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('delete_card_message')->first();
            }
        }

        $data = ['cards' => $cards, 'paymentOptionPage' => $paymentOptionPage, 'messages' => $messages];
        return $this->successResponse($data, 'Get cards successfully');
    }

    public function store(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        $user_id = $user->id;

        // Validate the form data
        $request->validate([
            'name_on_card' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s\-]+$/'],
            'address' => 'required',
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
                        'line1' => $request->address,
                    ],
                ],
            ]);

            $message = null;
            $selectedLanguage = app()->getLocale();
            if ($selectedLanguage) {
                // Find the language by abbreviation
                $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();

                if ($selectedLanguage) {
                    // Retrieve the HomePageSettingDetail associated with the selected language
                    $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('card_add_message', 'already_added_card_message','general_error_message')->first();
                }
            } else {
                $selectedLanguage = Language::where('is_default', 1)->first();
                if ($selectedLanguage) {
                    $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('card_add_message', 'already_added_card_message','general_error_message')->first();
                }
            }

            // Check if the card already exists for the user
            $existingCard = Card::where('user_id', $user_id)->where('fingerprint', $paymentMethod->card->fingerprint)->first();
            if ($existingCard) {
                return $this->apiErrorResponse(strip_tags($message->already_added_card_message), 200);
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
                'address' => $request->address,
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

            $data = ['card' => $card];
            return $this->successResponse($data, strip_tags($message->card_add_message));
        } catch (\Exception $e) {
            return $this->apiErrorResponse($messages->general_error_message ?? "An error occurred while processing your card. Please try again", 200);
        }
    }

    public function edit(Request $request)
    {
        $card = Card::whereId($request->id)->first();

        $data = ['card' => $card];
        return $this->successResponse($data, 'Get card successfully');
    }

    public function update(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        $user_id = $user->id;

        $request->validate([
            'name_on_card' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s\-]+$/'],
            'address' => 'required',
            'stripeToken' => 'required',
        ], [
            'name_on_card.regex' => 'Cardholder name can only contain letters, spaces, and hyphens',
        ]);

        // Set Stripe API key
        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            $card = Card::findOrFail($request->id);

            // Update the PaymentMethod with Stripe using the new token
            $paymentMethod = PaymentMethod::retrieve($card->stripe_payment_method_id);
            $paymentMethod->card = ['token' => $request->stripeToken];
            $paymentMethod = PaymentMethod::update($paymentMethod->id, [
                'card' => ['token' => $request->stripeToken],
                'billing_details' => [
                    'name' => $request->name_on_card,
                    'address' => [
                        'line1' => $request->address,
                    ],
                ],
            ]);

            $stripe_payment_method_id = $paymentMethod->id;

            // Handle primary card setting
            $primary_card = $request->filled('primary_card') ? $request->primary_card : 0;

            if ($primary_card == 1) {
                $cards = Card::where('user_id', $user_id)->get();
                foreach ($cards as $card) {
                    $card->update([
                        'primary_card' => 0,
                    ]);
                }
            }

            // Update card details in the database
            $card->update([
                'name_on_card' => $request->name_on_card,
                'card_number' => $paymentMethod->card->last4,
                'card_type' => $paymentMethod->card->brand,
                'exp_month' => $paymentMethod->card->exp_month,
                'exp_year' => $paymentMethod->card->exp_year,
                'address' => $request->address,
                'primary_card' => $primary_card,
                'stripe_payment_method_id' => $stripe_payment_method_id,
            ]);

            $card = Card::whereId($request->id)->first();
            $data = ['card' => $card];
            return $this->successResponse($data, 'Card updated successfully');
        } catch (\Exception $e) {
            return $this->apiErrorResponse("An error occurred while processing your card. Please try again", 200);
        }
    }

    public function destroy(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        $card = Card::find($request->card_id);

        if ($card) {
            
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
                $selectedLanguage = app()->getLocale();
                if ($selectedLanguage) {
                    // Find the language by abbreviation
                    $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();

                    if ($selectedLanguage) {
                        // Retrieve the HomePageSettingDetail associated with the selected language
                        $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('card_delete_message', 'general_error_message')->first();
                    }
                } else {
                    $selectedLanguage = Language::where('is_default', 1)->first();
                    if ($selectedLanguage) {
                        $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('card_delete_message', 'general_error_message')->first();
                    }
                }

                return $this->successResponse('', strip_tags($message->card_delete_message));
            } catch (\Exception $e) {
                return $this->apiErrorResponse($messages->general_error_message ?? "An error occurred while deleting your card. Please try again", 200);
            }
        }

        return $this->apiErrorResponse($messages->general_error_message ?? "Card not found", 404);
    }

    public function setCardPrimary(Request $request)
    {
        $card = Card::find($request->card_id);
        
        if ($card) {
            DB::table('cards')->where('user_id', $card->user_id)->update(['primary_card' => 0]);
            
            $card->primary_card = 1;
            $card->save();

            $message = null;
            $selectedLanguage = app()->getLocale();
            if ($selectedLanguage) {
                // Find the language by abbreviation
                $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();

                if ($selectedLanguage) {
                    // Retrieve the HomePageSettingDetail associated with the selected language
                    $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('card_primary_message', 'general_error_message')->first();
                }
            } else {
                $selectedLanguage = Language::where('is_default', 1)->first();
                if ($selectedLanguage) {
                    $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('card_primary_message','general_error_message')->first();
                }
            }

            $data = ['card' => $card];
            return $this->successResponse($data, strip_tags($message->card_primary_message));
        }

        return $this->apiErrorResponse($messages->general_error_message ?? "Card not found", 404);
    }



}
