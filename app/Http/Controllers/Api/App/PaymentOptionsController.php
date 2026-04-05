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
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Stripe\PaymentMethod;
use Stripe\SetupIntent;
use Stripe\Stripe;

class PaymentOptionsController extends Controller
{
    use StatusResponser;

    public function index(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        $user_id = $user->id;
        $cards = Card::where('user_id', $user_id)->where('payment_method_type', 'card')->orderBy('primary_card', 'desc')->orderBy('id', 'desc')->paginate($request->paginate_limit);

        
        $paymentOptionPage = PaymentSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        $messages = $this->successMessage;

        $data = [
            'cards' => $cards,
            'paymentOptionPage' => $paymentOptionPage,
            'messages' => $messages,
            'stripeConfig' => [
                'country' => config('stripe.account_country'),
                'currency' => config('stripe.account_currency'),
            ],
        ];
        return $this->successResponse($data, 'Get cards successfully');
    }

    public function createSetupIntent(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            if (! $user->stripe_customer_id) {
                $customer = Customer::create([
                    'email' => $user->email,
                    'name' => trim($user->first_name.' '.$user->last_name) ?: $user->first_name,
                    'address' => [
                        'country' => config('stripe.account_country'),
                    ],
                ]);

                User::whereId($user->id)->update([
                    'stripe_customer_id' => $customer->id,
                ]);

                $user = User::whereId($user->id)->first();
            }

            $setupIntent = SetupIntent::create([
                'customer' => $user->stripe_customer_id,
                'payment_method_types' => ['card'],
            ]);

            $publishableKey = (string) config('stripe.key', '');

            return $this->successResponse([
                'clientSecret' => $setupIntent->client_secret,
                'setupIntentId' => $setupIntent->id,
                /** Matches server account; safe for authenticated clients (same as Stripe.js). */
                'publishableKey' => $publishableKey,
                'stripeConfig' => [
                    'country' => config('stripe.account_country'),
                    'currency' => config('stripe.account_currency'),
                ],
            ], 'Setup intent created');
        } catch (\Exception $e) {
            Log::error('PaymentOptionsController createSetupIntent failed', ['exception' => $e->getMessage()]);

            return $this->apiErrorResponse('Could not start card setup. Please try again.', 200);
        }
    }

    public function store(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        $user_id = $user->id;

        $request->validate([
            'name_on_card' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s\-]+$/'],
            'address' => ['required_without:setup_intent_id', 'nullable', 'string', 'max:2000'],
            'setup_intent_id' => ['nullable', 'string', 'regex:/^seti_[a-zA-Z0-9]+$/'],
            'payment_method_id' => ['nullable', 'string', 'regex:/^pm_[a-zA-Z0-9]+$/'],
            'stripeToken' => ['nullable', 'string'],
            'billing_line1' => ['nullable', 'string', 'max:255'],
            'billing_line2' => ['nullable', 'string', 'max:255'],
            'billing_city' => ['nullable', 'string', 'max:255'],
            'billing_state' => ['nullable', 'string', 'max:255'],
            'billing_postal_code' => ['nullable', 'string', 'max:32'],
            'billing_country' => ['nullable', 'string', 'size:2'],
        ], [
            'name_on_card.regex' => 'Cardholder name can only contain letters, spaces, and hyphens',
        ]);

        if (! $request->filled('payment_method_id') && ! $request->filled('stripeToken') && ! $request->filled('setup_intent_id')) {
            throw ValidationException::withMessages([
                'payment_method_id' => ['A valid card is required. Please try again.'],
            ]);
        }

        // Set Stripe API key
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $message = null;

        try {
            if (! $user->stripe_customer_id) {
                $customer = Customer::create([
                    'email' => $user->email,
                    'name' => trim($user->first_name.' '.$user->last_name) ?: $user->first_name,
                    'address' => [
                        'country' => config('stripe.account_country'),
                    ],
                ]);

                User::whereId($user_id)->update([
                    'stripe_customer_id' => $customer->id,
                ]);

                $user = User::whereId($user_id)->first();
            }

            $addressForDb = trim((string) $request->input('address', ''));

            if ($request->filled('setup_intent_id')) {
                $setupIntent = SetupIntent::retrieve($request->setup_intent_id);
                if (($setupIntent->customer ?? '') !== $user->stripe_customer_id) {
                    return $this->apiErrorResponse('Invalid card setup session.', 200);
                }
                if ($setupIntent->status !== 'succeeded') {
                    return $this->apiErrorResponse('Card setup was not completed. Please try again.', 200);
                }
                $pmId = $setupIntent->payment_method;
                if (is_object($pmId) && isset($pmId->id)) {
                    $pmId = $pmId->id;
                }
                if (! is_string($pmId) || $pmId === '') {
                    return $this->apiErrorResponse('No payment method on setup.', 200);
                }
                $paymentMethod = PaymentMethod::retrieve($pmId);
                if ($paymentMethod->type !== 'card') {
                    throw new \InvalidArgumentException('Invalid payment method type');
                }
                $mergedBilling = $this->mergedBillingDetailsForPaymentMethod($paymentMethod, $request->name_on_card);
                PaymentMethod::update($paymentMethod->id, [
                    'billing_details' => $mergedBilling,
                ]);
                $paymentMethod = PaymentMethod::retrieve($paymentMethod->id);
                $fromPm = $this->addressLineFromPaymentMethod($paymentMethod);
                $addressForDb = $fromPm !== '' ? $fromPm : $addressForDb;
            } elseif ($request->filled('payment_method_id')) {
                $billingDetails = $this->stripeBillingDetailsFromRequest($request);
                $paymentMethod = PaymentMethod::retrieve($request->payment_method_id);
                if ($paymentMethod->type !== 'card') {
                    throw new \InvalidArgumentException('Invalid payment method type');
                }
                if ($paymentMethod->customer && $paymentMethod->customer !== $user->stripe_customer_id) {
                    return $this->apiErrorResponse('This card is already linked to another account.', 200);
                }
                if (! $paymentMethod->customer) {
                    $paymentMethod->attach(['customer' => $user->stripe_customer_id]);
                }
                PaymentMethod::update($paymentMethod->id, [
                    'billing_details' => $billingDetails,
                ]);
                $paymentMethod = PaymentMethod::retrieve($paymentMethod->id);
            } else {
                $billingDetails = $this->stripeBillingDetailsFromRequest($request);
                $paymentMethod = PaymentMethod::create([
                    'type' => 'card',
                    'card' => ['token' => $request->stripeToken],
                    'billing_details' => $billingDetails,
                ]);
                if (! $paymentMethod->customer) {
                    $paymentMethod->attach(['customer' => $user->stripe_customer_id]);
                }
                PaymentMethod::update($paymentMethod->id, [
                    'billing_details' => $billingDetails,
                ]);
                $paymentMethod = PaymentMethod::retrieve($paymentMethod->id);
            }

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
                $dupMsg = (isset($message) && is_object($message) && ! empty($message->already_added_card_message))
                    ? strip_tags($message->already_added_card_message)
                    : 'This card is already saved.';

                return $this->apiErrorResponse($dupMsg, 200);
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
                'address' => $addressForDb !== '' ? $addressForDb : '—',
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
            $okMsg = (isset($message) && is_object($message) && ! empty($message->card_add_message))
                ? strip_tags($message->card_add_message)
                : 'Card added successfully';

            return $this->successResponse($data, $okMsg);
        } catch (\Exception $e) {
            Log::error('PaymentOptionsController store card failed', ['exception' => $e->getMessage()]);

            $errText = 'An error occurred while processing your card. Please try again';
            if (isset($message) && is_object($message) && ! empty($message->general_error_message)) {
                $errText = strip_tags($message->general_error_message);
            }

            return $this->apiErrorResponse($errText, 200);
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
                    Mail::to($user->email)->queue(new CardRemovedEmail($emailData));
                }

                $notification = Notification::create([
                    'type' => null,
                    'category' => 'system',
                    'receiver_id' => $user->id,
                    'posted_by' => $user->id,
                    'message' => getNotificationMessageText(
                        'card_removed_from_profile',
                        $user,
                        [],
                        'Card removed from your profile'
                    ),
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
                Log::error('PaymentOptionsController destroy card failed', ['exception' => $e->getMessage()]);

                $errText = 'An error occurred while deleting your card. Please try again';
                if (isset($message) && is_object($message) && ! empty($message->general_error_message)) {
                    $errText = strip_tags($message->general_error_message);
                }

                return $this->apiErrorResponse($errText, 200);
            }
        }

        return $this->apiErrorResponse('Card not found', 404);
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

        $fail = null;
        $selectedLanguage = app()->getLocale();
        if ($selectedLanguage) {
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            if ($selectedLanguage) {
                $fail = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('general_error_message')->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $fail = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('general_error_message')->first();
            }
        }
        $failMsg = (isset($fail) && is_object($fail) && ! empty($fail->general_error_message))
            ? strip_tags($fail->general_error_message)
            : 'Card not found';

        return $this->apiErrorResponse($failMsg, 404);
    }

    /**
     * Billing details for Stripe (Canada-friendly: postal_code, province, country).
     */
    private function stripeBillingDetailsFromRequest(Request $request): array
    {
        $defaultCountry = config('stripe.account_country');
        $country = strtoupper((string) $request->input('billing_country', $defaultCountry));
        if (strlen($country) !== 2) {
            $country = $defaultCountry;
        }

        $line1 = trim((string) $request->input('billing_line1', ''));
        $line2 = trim((string) $request->input('billing_line2', ''));
        $city = trim((string) $request->input('billing_city', ''));
        $state = trim((string) $request->input('billing_state', ''));
        $postal = trim((string) $request->input('billing_postal_code', ''));

        if ($line1 === '') {
            $line1 = trim((string) $request->input('address', ''));
        }

        if ($line1 === '') {
            $line1 = Str::limit(trim(preg_replace('/\s+/', ' ', (string) $request->input('address', ''))), 255, '');
        }

        $address = array_filter([
            'line1' => $line1 !== '' ? $line1 : null,
            'line2' => $line2 !== '' ? $line2 : null,
            'city' => $city !== '' ? $city : null,
            'state' => $state !== '' ? $state : null,
            'postal_code' => $postal !== '' ? $postal : null,
        ], fn ($v) => $v !== null && $v !== '');
        $address['country'] = $country;

        return [
            'name' => $request->name_on_card,
            'address' => $address,
        ];
    }

    /**
     * Single-line address for cards table from a Stripe PaymentMethod.
     */
    private function addressLineFromPaymentMethod(PaymentMethod $paymentMethod): string
    {
        $addr = $paymentMethod->billing_details->address ?? null;
        if (! $addr) {
            return '';
        }
        $parts = array_filter([
            $addr->line1 ?? '',
            $addr->line2 ?? '',
            $addr->city ?? '',
            $addr->state ?? '',
            $addr->country ?? '',
            $addr->postal_code ?? '',
        ], fn ($v) => $v !== '' && $v !== null);

        return implode(', ', $parts);
    }

    /**
     * Merge app cardholder name into existing Stripe billing details (keeps PM address from Payment Sheet).
     */
    private function mergedBillingDetailsForPaymentMethod(PaymentMethod $paymentMethod, string $nameOnCard): array
    {
        $bd = $paymentMethod->billing_details;
        $out = [
            'name' => $nameOnCard,
        ];
        if (! empty($bd->email)) {
            $out['email'] = $bd->email;
        }
        if (! empty($bd->phone)) {
            $out['phone'] = $bd->phone;
        }
        $addr = $bd->address ?? null;
        $address = [];
        if ($addr) {
            foreach (['line1', 'line2', 'city', 'state', 'postal_code', 'country'] as $k) {
                if (! empty($addr->{$k})) {
                    $address[$k] = $addr->{$k};
                }
            }
        }
        if ($address === []) {
            $address['country'] = config('stripe.account_country');
        } elseif (empty($address['country'])) {
            $address['country'] = config('stripe.account_country');
        }
        $out['address'] = $address;

        return $out;
    }
}
