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

use Stripe\Customer;
use Stripe\Stripe;
use Stripe\SetupIntent;
use Stripe\PaymentMethod as StripePaymentMethod;
use App\Models\PaymentMethod;

class PaymentMethodController extends Controller
{
    public function index()
    {
        $user_id = auth()->id();
        $user = auth()->user();
        $methods = PaymentMethod::where('user_id', $user_id)->get();

        $paymentSettingDetail = BillingAddressSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        $ProfilePage = ProfilePageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        $ProfileSetting = ProfileSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        $reviewSetting = MyReviewSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);

        Stripe::setApiKey(env('STRIPE_SECRET'));

        // Create Stripe customer if not exists
        if (!$user->stripe_customer_id) {
            $customer = Customer::create([
                'email' => $user->email,
                'name'  => $user->name,
            ]);

            $user->stripe_customer_id = $customer->id;
            $user->save();
        }

        $setupIntent = SetupIntent::create([
            'customer' => $user->stripe_customer_id,
            'automatic_payment_methods' => [
                'enabled' => true,
            ],
        ]);
        $clientSecret = $setupIntent->client_secret;

        return view('payment_methods', compact('methods','user', 'paymentSettingDetail', 'ProfilePage', 'ProfileSetting', 'reviewSetting', 'clientSecret'));
    }

    /**
     * Store Stripe payment method (Card / Apple Pay / Google Pay)
     */
    public function storeStripe(Request $request)
    {
        $request->validate([
            'payment_method_id' => 'required|string'
        ]);

        $user = auth()->user();

        Stripe::setApiKey(env('STRIPE_SECRET'));

        // Create Stripe customer if not exists
        if (!$user->stripe_customer_id) {
            $customer = Customer::create([
                'email' => $user->email,
                'name'  => $user->name,
            ]);

            $user->stripe_customer_id = $customer->id;
            $user->save();
        }

        // Retrieve payment method from Stripe
        $stripePaymentMethod = StripePaymentMethod::retrieve(
            $request->payment_method_id
        );

        // Attach to customer
        $stripePaymentMethod->attach([
            'customer' => $user->stripe_customer_id,
        ]);

        // Determine payment method type
        $type = 'card'; // default
        if (isset($stripePaymentMethod->type) && $stripePaymentMethod->type !== 'card') {
            $type = $stripePaymentMethod->type;
        } elseif (isset($stripePaymentMethod->card->wallet)) {
            // Check if it's a wallet payment (Google Pay, Apple Pay)
            $walletType = $stripePaymentMethod->card->wallet->type ?? null;
            if ($walletType === 'google_pay') {
                $type = 'google_pay';
            } elseif ($walletType === 'apple_pay') {
                $type = 'apple_pay';
            }
        }

        // Save locally
        PaymentMethod::create([
            'user_id' => $user->id,
            'gateway' => 'stripe',
            'payment_method_id' => $stripePaymentMethod->id,
            'type' => $type,
            'brand' => $stripePaymentMethod->card->brand ?? null,
            'last4' => $stripePaymentMethod->card->last4 ?? null,
            'is_default' => false
        ]);

        return redirect()->back()->with('success', 'Card saved successfully.');
    }

    /**
     * Store PayPal account after successful capture
     */
    public function storePaypal(Request $request)
    {
        try {
            $request->validate([
                'payer_id' => 'required|string',
                'email' => 'required|email'
            ]);

            // Check if PayPal account already exists (by payer_id or email)
            $existingPayPal = PaymentMethod::where('user_id', auth()->id())
                ->where('gateway', 'paypal')
                ->where(function($query) use ($request) {
                    $query->where('payment_method_id', $request->payer_id)
                          ->orWhere('email', $request->email);
                })
                ->first();

            if ($existingPayPal) {
                if ($request->expectsJson() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'This PayPal account is already added.',
                        'duplicate' => true
                    ], 400);
                }
                return redirect()->back()->withErrors(['error' => 'This PayPal account is already added.']);
            }

            $paymentMethod = PaymentMethod::create([
                'user_id' => auth()->id(),
                'gateway' => 'paypal',
                'payment_method_id' => $request->payer_id,
                'type' => 'paypal',
                'email' => $request->email,
                'is_default' => false
            ]);

            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'PayPal account saved successfully',
                    'payment_method' => $paymentMethod
                ]);
            }

            return redirect()->back()->with('success', 'PayPal account saved.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error saving PayPal payment method: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'request' => $request->all()
            ]);

            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to save PayPal payment method: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->withErrors(['error' => 'Failed to save PayPal payment method']);
        }
    }

    /**
     * Delete payment method
     */
    public function destroy($id)
    {
        $method = PaymentMethod::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if ($method->gateway === 'stripe') {
            Stripe::setApiKey(env('STRIPE_SECRET'));

            try {
                StripePaymentMethod::retrieve($method->payment_method_id)
                    ->detach();
            } catch (\Exception $e) {
                // optional: log error
            }
        }

        $method->delete();

        return redirect()->back()->with('success', 'Payment method removed.');
    }

    /**
     * Set default payment method
     */
    public function setDefault($id)
    {
        $userId = auth()->id();

        PaymentMethod::where('user_id', $userId)
            ->update(['is_default' => false]);

        PaymentMethod::where('id', $id)
            ->where('user_id', $userId)
            ->update(['is_default' => true]);

        return redirect()->back()->with('success', 'Default payment method updated.');
    }
}