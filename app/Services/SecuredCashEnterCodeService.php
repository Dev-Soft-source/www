<?php

namespace App\Services;

use App\Jobs\NotifySecuredCashCodeSuccessJob;
use App\Models\Booking;
use App\Models\TopUpBalance;
use App\Models\Transaction;
use Illuminate\Support\Facades\Log;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Stripe\Refund;
use Stripe\Stripe;

/**
 * Shared persistence when a driver enters the correct secured-cash release code (web + API).
 *
 * Clears secured-cash fields, refunds type=1 transactions (PayPal / Stripe / account balance),
 * records type=3 refund rows, then queues notifications.
 */
class SecuredCashEnterCodeService
{
    /**
     * @return array{ok: true}|array{ok: false, error: string}
     */
    public function applySuccessfulCode(Booking $booking, bool $failOnStripeError = false): array
    {
        $booking->update([
            'secured_cash' => null,
            'secured_cash_code' => null,
        ]);

        $transactions = Transaction::where('booking_id', $booking->id)
            ->where('type', '1')
            ->get();

        foreach ($transactions as $transaction) {
            if (!$transaction) {
                continue;
            }

            $refundId = '';

            if ($transaction->pay_by_account == 0) {
                if ($transaction->paypal_id) {
                    $paypal = new PayPalClient;
                    $paypal->setApiCredentials(config('paypal'));
                    $token = $paypal->getAccessToken();
                    $paypal->setAccessToken($token);
                    $amount = (float) $transaction->price - (float) $transaction->booking_fee;
                    $response = $paypal->refundCapturedPayment(
                        $transaction->paypal_id,
                        'Invoice-' . $transaction->paypal_id,
                        $amount,
                        'Refund issued.'
                    );
                    $refundId = isset($response['id']) ? $response['id'] : '';
                } elseif ($transaction->stripe_id) {
                    Stripe::setApiKey(config('stripe.secret'));
                    $amountCents = (int) round(((float) $transaction->price - (float) $transaction->booking_fee) * 100);

                    try {
                        $refund = Refund::create([
                            'payment_intent' => $transaction->stripe_id,
                            'amount' => $amountCents,
                        ]);
                        $refundId = $refund->id;
                    } catch (\Stripe\Exception\ApiErrorException $e) {
                        Log::info($e->getMessage());
                        if ($failOnStripeError) {
                            return [
                                'ok' => false,
                                'error' => $e->getMessage(),
                            ];
                        }
                    }
                }
            } else {
                TopUpBalance::create([
                    'booking_id' => $transaction->booking_id,
                    'user_id' => $booking->user_id,
                    'dr_amount' => $transaction->price - $transaction->booking_fee,
                    'added_date' => date('Y-m-d'),
                ]);
            }

            Transaction::create([
                'booking_id' => $transaction->booking_id,
                'ride_id' => $booking->ride_id,
                'parent_id' => $transaction->id,
                'type' => '3',
                'price' => $transaction->price,
                'paypal_id' => isset($transaction->paypal_id) ? $refundId : null,
                'stripe_id' => isset($transaction->stripe_id) ? $refundId : null,
            ]);
        }

        NotifySecuredCashCodeSuccessJob::dispatch($booking->id);

        return ['ok' => true];
    }
}
