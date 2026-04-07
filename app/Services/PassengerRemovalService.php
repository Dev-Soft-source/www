<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\CancellationHistory;
use App\Models\CoffeeWallet;
use App\Models\Ride;
use App\Models\SeatDetail;
use App\Models\TopUpBalance;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Stripe\Refund;
use Stripe\Stripe;

/**
 * Shared persistence for "driver removes passenger" flows (web + API).
 *
 * Responsibilities:
 * - Refund all type=1 transactions for the booking (cash vs non‑cash handling).
 * - Update booking status / block fields.
 * - Release seats back to the ride (SeatDetail reset).
 * - Create cancellation history and revoke Extra Care.
 */
class PassengerRemovalService
{
    /**
     * @return array{ok: true, ride: Ride, booking: Booking}|array{ok: false, error: string}
     */
    public function remove(
        Ride $ride,
        Booking $booking,
        int $removedPermanently,
        ?string $removeType,
        ?int $blockDay
    ): array {
        $blockDateTime = null;

        if ($removedPermanently === 1 && $removeType === 'temporarily' && $blockDay !== null) {
            $currentDate = Carbon::now();
            $blockDateTime = $currentDate->copy()->addDays($blockDay)->format('Y-m-d H:i:s');
        } elseif ($removedPermanently === 1 && $removeType === 'permanently') {
            $blockDay = 1000;
            $currentDate = Carbon::now();
            $blockDateTime = $currentDate->copy()->addDays($blockDay)->format('Y-m-d H:i:s');
        }

        $transactions = Transaction::where('booking_id', $booking->id)
            ->where('type', '1')
            ->get();

        foreach ($transactions as $transaction) {
            if (!$transaction) {
                continue;
            }

            $refundId = '';

            $checkPrice = 0.0;
            if (!$ride->isCashpayment()) {
                $getRefundEntryPrice = (float) Transaction::where('parent_id', $transaction->id)->sum('price');

                if (isset($transaction->coffee_from_wall) && (int) $transaction->coffee_from_wall === 1) {
                    $getRefundEntryPrice += (float) $transaction->booking_fee;
                }

                $checkPrice = (float) $transaction->price;
            } else {
                $getRefundEntryPrice = (float) Transaction::where('parent_id', $transaction->id)->sum('booking_fee');
                $checkPrice = (float) $transaction->booking_fee;
            }

            if (!isset($getRefundEntryPrice) || is_null($getRefundEntryPrice) || $getRefundEntryPrice == $checkPrice) {
                // nothing to refund, but still honour coffee wallet if needed
            } else {
                $transactionAmt = $checkPrice - $getRefundEntryPrice;

                if (isset($transaction->coffee_from_wall) && (int) $transaction->coffee_from_wall === 1) {
                    $transactionAmt -= (float) $transaction->booking_fee;
                }

                if ($transaction->pay_by_account == 0) {
                    if ($transaction->paypal_id) {
                        $paypal = new PayPalClient;
                        $paypal->setApiCredentials(config('paypal'));
                        $token = $paypal->getAccessToken();
                        $paypal->setAccessToken($token);
                        $response = $paypal->refundCapturedPayment(
                            $transaction->paypal_id,
                            'Invoice-' . $transaction->paypal_id,
                            $transactionAmt,
                            'Refund issued.'
                        );
                        $refundId = isset($response['id']) ? $response['id'] : '';
                    } elseif ($transaction->stripe_id) {
                        Stripe::setApiKey(config('stripe.secret'));

                        try {
                            $refund = Refund::create([
                                'payment_intent' => $transaction->stripe_id,
                                'amount' => (int) round($transactionAmt * 100),
                            ]);

                            $refundId = $refund->id;
                        } catch (\Stripe\Exception\ApiErrorException $e) {
                            Log::info($e->getMessage());

                            return [
                                'ok' => false,
                                'error' => $e->getMessage(),
                            ];
                        }
                    }
                } else {
                    TopUpBalance::create([
                        'booking_id' => $transaction->booking_id,
                        'user_id' => $booking->user_id,
                        'dr_amount' => $transactionAmt,
                        'added_date' => date('Y-m-d'),
                    ]);
                }

                if (isset($transaction->coffee_from_wall) && (int) $transaction->coffee_from_wall === 1) {
                    CoffeeWallet::create([
                        'booking_id' => $booking->id,
                        'ride_id' => $ride->id,
                        'user_id' => $booking->user_id,
                        'dr_amount' => $transaction->booking_fee,
                    ]);
                }

                if (isset($transaction->coffee_from_wall) && (int) $transaction->coffee_from_wall === 1) {
                    Transaction::create([
                        'booking_id' => $transaction->booking_id,
                        'ride_id' => $booking->ride_id,
                        'parent_id' => $transaction->id,
                        'type' => '3',
                        'price' => !$ride->isCashpayment() ? $transactionAmt : 0,
                        'booking_fee' => $ride->isCashpayment() ? $transactionAmt : $transaction->booking_fee,
                        'paypal_id' => isset($transaction->paypal_id) ? $refundId : null,
                        'stripe_id' => isset($transaction->stripe_id) ? $refundId : null,
                    ]);
                } else {
                    Transaction::create([
                        'booking_id' => $transaction->booking_id,
                        'ride_id' => $booking->ride_id,
                        'parent_id' => $transaction->id,
                        'type' => '3',
                        'price' => !$ride->isCashpayment() ? $transactionAmt : 0,
                        'booking_fee' => $ride->isCashpayment() ? $transactionAmt : 0,
                        'paypal_id' => isset($transaction->paypal_id) ? $refundId : null,
                        'stripe_id' => isset($transaction->stripe_id) ? $refundId : null,
                    ]);
                }
            }
        }

        $booking->update([
            'status' => 4,
            'remove_type' => $removeType ?: null,
            'removed_permanently' => $removedPermanently,
            'block_days' => $blockDay ?: null,
            'block_date_time' => $blockDateTime,
        ]);

        $seatDetails = SeatDetail::where('booking_id', $booking->id)->get();
        foreach ($seatDetails as $seatDetail) {
            $seatDetail->status = 'pending';
            $seatDetail->booking_id = null;
            $seatDetail->user_id = null;
            $seatDetail->save();
        }

        CancellationHistory::create([
            'ride_id' => $booking->ride_id,
            'booking_id' => $booking->id,
            'user_id' => $ride->added_by,
        ]);

        User::where('id', $ride->added_by)
            ->whereIn('folks_ride', ['1', ''])
            ->update(['folks_ride' => '0']);

        return [
            'ok' => true,
            'ride' => $ride->fresh(['driver', 'bookings.passenger']),
            'booking' => $booking->fresh('passenger'),
        ];
    }
}

