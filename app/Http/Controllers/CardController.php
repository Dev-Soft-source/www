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
        Log::info($paymentSettingDetail);
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

        // Fetch card details from Stripe for card payment methods
        foreach ($cards as $card) {
            if ($card->stripe_payment_method_id && $card->payment_method_type === 'card') {
                try {
                    $card->paymentMethod = PaymentMethod::retrieve($card->stripe_payment_method_id);
                } catch (\Exception $e) {
                    Log::error('Error retrieving payment method: ' . $e->getMessage());
                }
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
    
    public function createSetupIntent(Request $request)
    {
        $user = auth()->user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        Stripe::setApiKey(env('STRIPE_SECRET'));
        
        if (!$user->stripe_customer_id) {
            $customer = Customer::create([
                'email' => $user->email,
                'name' => $user->first_name,
            ]);
            User::whereId($user->id)->update(['stripe_customer_id' => $customer->id]);
            $user = User::whereId($user->id)->first();
        }
        
        try {
            $setupIntent = \Stripe\SetupIntent::create([
                'customer' => $user->stripe_customer_id,
                'payment_method_types' => ['card'],
            ]);
            
            return response()->json([
                'clientSecret' => $setupIntent->client_secret
            ]);
        } catch (\Exception $e) {
            Log::error('SetupIntent creation error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to create setup intent'], 500);
        }
    }

    public function create($lang = null, Request $request)
    {
        // Preserve return URL if it exists in session (from booking/ride detail pages)
        // If not set and we have a referrer, store it
        $returnUrl = session('return_url_after_action');
        if (!$returnUrl) {
            $referrer = request()->headers->get('referer');
            if ($referrer && !str_contains($referrer, 'card') && !str_contains($referrer, 'create-card')) {
                session(['return_url_after_action' => $referrer]);
            }
        }

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
        $paymentMethodType = $request->input('payment_method_type', 'card');
        
        $selectedLanguage = session('selectedLanguage', Language::where('is_default', 1)->first()->abbreviation);
        $message = null;
        if ($selectedLanguage) {
            $language = Language::where('abbreviation', $selectedLanguage)->first();
            $message = SuccessMessagesSettingDetail::where('language_id', $language->id)->select('card_add_message', 'already_added_card_message')->first();
        }

        try {
            // Handle different payment method types
            if ($paymentMethodType === 'card') {
                return $this->storeCard($request, $user, $user_id, $message, $selectedLanguage);
            } elseif ($paymentMethodType === 'paypal') {
                return $this->storePayPal($request, $user, $user_id, $message, $selectedLanguage);
            } elseif ($paymentMethodType === 'apple_pay') {
                return $this->storeApplePay($request, $user, $user_id, $message, $selectedLanguage);
            } elseif ($paymentMethodType === 'google_pay') {
                return $this->storeGooglePay($request, $user, $user_id, $message, $selectedLanguage);
            } else {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => 'Invalid payment method type'], 400);
                }
                return back()->withErrors(['error' => 'Invalid payment method type']);
            }
        } catch (\Exception $e) {
            Log::error('Payment method store error: ' . $e->getMessage());
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'An error occurred while processing your payment method. Please try again.'], 500);
            }
            return back()->withErrors(['error' => 'An error occurred while processing your payment method. Please try again.']);
        }
    }
    
    private function storeCard(Request $request, $user, $user_id, $message, $selectedLanguage)
    {
        $validatedData = $request->validate([
            'name_on_card' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s\-]+$/'],
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

        Stripe::setApiKey(env('STRIPE_SECRET'));

        if (!$user->stripe_customer_id) {
            $customer = Customer::create([
                'email' => $user->email,
                'name' => $user->first_name,
            ]);
            User::whereId($user_id)->update(['stripe_customer_id' => $customer->id]);
            $user = User::whereId($user_id)->first();
        }

        // Handle both Token (tok_...) and PaymentMethod (pm_...) IDs
        $stripeToken = $request->stripeToken;
        $paymentMethod = null;
        
        if (str_starts_with($stripeToken, 'tok_')) {
            // It's a token - create a PaymentMethod from the token
            $paymentMethod = PaymentMethod::create([
                'type' => 'card',
                'card' => [
                    'token' => $stripeToken,
                ],
            ]);
        } elseif (str_starts_with($stripeToken, 'pm_')) {
            // It's already a PaymentMethod ID
            $paymentMethod = PaymentMethod::retrieve($stripeToken);
        } else {
            throw new \Exception('Invalid payment method identifier. Expected token (tok_...) or PaymentMethod ID (pm_...).');
        }
        
        // Attach payment method to customer
        $paymentMethod->attach(['customer' => $user->stripe_customer_id]);

        // Check if the card fingerprint already exists
        if (isset($paymentMethod->card->fingerprint)) {
            $existingCard = Card::where('user_id', $user_id)
                ->where('fingerprint', $paymentMethod->card->fingerprint)
                ->where('payment_method_type', 'card')
                ->first();
            if ($existingCard) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => $message->already_added_card_message ?? 'This card is already added'], 400);
                }
                return back()->withErrors(['error' => $message->already_added_card_message])->withInput();
            }
        }

        // Handle primary card setting
        $userCardCount = Card::where('user_id', $user_id)->count();
        $primary_card = ($userCardCount == 0) ? 1 : ($request->filled('primary_card') ? $request->primary_card : 0);
        if ($primary_card == 1) {
            Card::where('user_id', $user_id)->update(['primary_card' => 0]);
        }

        $card = Card::create([
            'user_id' => $user_id,
            'name_on_card' => $request->name_on_card,
            'card_number' => $paymentMethod->card->last4 ?? '',
            'card_type' => $paymentMethod->card->brand ?? '',
            'exp_month' => $paymentMethod->card->exp_month ?? '',
            'exp_year' => $paymentMethod->card->exp_year ?? '',
            'address' => $request->street_address . "," . ($request->house_apartment_number ?? '') . "," . $request->city . "," . $request->province . "," . $request->country . "," . $request->postal_code,
            'primary_card' => $primary_card,
            'fingerprint' => $paymentMethod->card->fingerprint ?? null,
            'stripe_payment_method_id' => $paymentMethod->id,
            'payment_method_type' => 'card',
        ]);

        if (isset($user->email_notification) && $user->email_notification == 1) {
            $emailData = ['first_name' => $user->first_name];
            Mail::to($user->email)->send(new CardAddedEmail($emailData));
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $message->card_add_message ?? 'Card added successfully']);
        }

        return redirect()->route('my_cards', ['lang' => $selectedLanguage])->with('message', $message->card_add_message ?? 'Card added successfully');
    }
    
    private function storePayPal(Request $request, $user, $user_id, $message, $selectedLanguage)
    {
        try {
            $request->validate([
                'paypal_email' => 'required|email',
                'paypal_payer_id' => 'required|string',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }

        // Check if PayPal account already exists
        $existingPayPal = Card::where('user_id', $user_id)
            ->where('payment_method_type', 'paypal')
            ->where('paypal_payer_id', $request->paypal_payer_id)
            ->first();
            
        if ($existingPayPal) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'This PayPal account is already added'], 400);
            }
            return back()->withErrors(['error' => 'This PayPal account is already added']);
        }

        $userCardCount = Card::where('user_id', $user_id)->count();
        $primary_card = ($userCardCount == 0) ? 1 : 0;
        if ($primary_card == 1) {
            Card::where('user_id', $user_id)->update(['primary_card' => 0]);
        }

        $card = Card::create([
            'user_id' => $user_id,
            'payment_method_type' => 'paypal',
            'paypal_email' => $request->paypal_email,
            'paypal_payer_id' => $request->paypal_payer_id,
            'primary_card' => $primary_card,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'PayPal account added successfully']);
        }

        return redirect()->route('my_cards', ['lang' => $selectedLanguage])->with('message', 'PayPal account added successfully');
    }
    
    private function storeApplePay(Request $request, $user, $user_id, $message, $selectedLanguage)
    {
        try {
            $request->validate([
                'payment_method_details' => 'required|array',
                'apple_pay_token' => 'required',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }

        Stripe::setApiKey(env('STRIPE_SECRET'));

        if (!$user->stripe_customer_id) {
            $customer = Customer::create([
                'email' => $user->email,
                'name' => $user->first_name,
            ]);
            User::whereId($user_id)->update(['stripe_customer_id' => $customer->id]);
        }

        // Create payment method from Apple Pay token
        try {
            $paymentMethod = PaymentMethod::create([
                'type' => 'card',
                'card' => ['token' => $request->apple_pay_token],
            ]);
            
            $paymentMethod->attach(['customer' => $user->stripe_customer_id]);
            
            // Refresh payment method to get full card details including exp_month and exp_year
            $paymentMethod = PaymentMethod::retrieve($paymentMethod->id);
        } catch (\Exception $e) {
            Log::error('Apple Pay tokenization error: ' . $e->getMessage());
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Failed to process Apple Pay'], 400);
            }
            return back()->withErrors(['error' => 'Failed to process Apple Pay']);
        }

        $userCardCount = Card::where('user_id', $user_id)->count();
        $primary_card = ($userCardCount == 0) ? 1 : 0;
        if ($primary_card == 1) {
            Card::where('user_id', $user_id)->update(['primary_card' => 0]);
        }

        $card = Card::create([
            'user_id' => $user_id,
            'payment_method_type' => 'apple_pay',
            'payment_method_details' => $request->payment_method_details,
            'stripe_payment_method_id' => $paymentMethod->id,
            'exp_month' => $paymentMethod->card->exp_month ?? '',
            'exp_year' => $paymentMethod->card->exp_year ?? '',
            'primary_card' => $primary_card,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Apple Pay added successfully']);
        }

        return redirect()->route('my_cards', ['lang' => $selectedLanguage])->with('message', 'Apple Pay added successfully');
    }
    
    private function storeGooglePay(Request $request, $user, $user_id, $message, $selectedLanguage)
    {
        try {
            $request->validate([
                'payment_method_details' => 'required|array',
                'google_pay_token' => 'required',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }

        Stripe::setApiKey(env('STRIPE_SECRET'));

        if (!$user->stripe_customer_id) {
            $customer = Customer::create([
                'email' => $user->email,
                'name' => $user->first_name,
            ]);
            User::whereId($user_id)->update(['stripe_customer_id' => $customer->id]);
        }

        // Create payment method from Google Pay token
        try {
            $token = $request->google_pay_token;
            
            // Try to decode if it's a JSON string, otherwise use as-is
            $tokenData = json_decode($token, true);
            if (json_last_error() === JSON_ERROR_NONE && isset($tokenData['id'])) {
                $tokenToUse = $tokenData['id'];
            } else {
                // If it's already a string token or can't be decoded, use it directly
                $tokenToUse = is_string($token) ? $token : $token;
            }
            
            $paymentMethod = PaymentMethod::create([
                'type' => 'card',
                'card' => ['token' => $tokenToUse],
            ]);
            
            $paymentMethod->attach(['customer' => $user->stripe_customer_id]);
            
            // Refresh payment method to get full card details including exp_month and exp_year
            $paymentMethod = PaymentMethod::retrieve($paymentMethod->id);
        } catch (\Exception $e) {
            Log::error('Google Pay tokenization error: ' . $e->getMessage(), [
                'token_preview' => is_string($request->google_pay_token) ? substr($request->google_pay_token, 0, 50) : 'non-string',
                'user_id' => $user_id
            ]);
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Failed to process Google Pay: ' . $e->getMessage()], 400);
            }
            return back()->withErrors(['error' => 'Failed to process Google Pay']);
        }

        $userCardCount = Card::where('user_id', $user_id)->count();
        $primary_card = ($userCardCount == 0) ? 1 : 0;
        if ($primary_card == 1) {
            Card::where('user_id', $user_id)->update(['primary_card' => 0]);
        }

        $card = Card::create([
            'user_id' => $user_id,
            'payment_method_type' => 'google_pay',
            'payment_method_details' => $request->payment_method_details,
            'stripe_payment_method_id' => $paymentMethod->id,
            'exp_month' => $paymentMethod->card->exp_month ?? '',
            'exp_year' => $paymentMethod->card->exp_year ?? '',
            'primary_card' => $primary_card,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Google Pay added successfully']);
        }

        return redirect()->route('my_cards', ['lang' => $selectedLanguage])->with('message', 'Google Pay added successfully');
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
                $language = Language::where('abbreviation', $selectedLanguage)->first();
                $message = SuccessMessagesSettingDetail::where('language_id', $language->id)->select('card_delete_message')->first();
            }
            return redirect()->route('my_cards', ['lang' => $selectedLanguage])->with('message', $message->card_delete_message);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'An error occurred while deleting your card. Please try again']);
        }
    }
}
