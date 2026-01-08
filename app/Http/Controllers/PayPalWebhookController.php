<?php

namespace App\Http\Controllers;

use App\Models\CoffeeWallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PayPalWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $payload = $request->all();
        Log::info('PayPal Webhook Received:', $payload);

        if ($payload['event_type'] == 'PAYMENT.SALE.COMPLETED') {
            $userId = $payload['resource']['billing_agreement_id']; // Subscription ID
            $amount = $payload['resource']['amount']['total'];
            $transactionId = $payload['resource']['id'];

            CoffeeWallet::create([
                'name' => null,
                'email' => null,
                'phone' => null,
                'anonymous' => 0,
                'designation' => null,
                'package_id' => null,
                'frequency' => null,
                'dr_amount' => $amount,
                'paypal_id' => isset($transactionId) ? $transactionId : null,
                'stripe_id' => null,
                'subscription_id' => isset($userId) ? $userId : null,
                'payment_method' => 'stripe',
                'status' => 'completed',
            ]);
        } elseif ($payload['event_type'] == 'PAYMENT.SALE.DENIED') {
            //
        }

        return response()->json(['status' => 'success']);
    }
}
