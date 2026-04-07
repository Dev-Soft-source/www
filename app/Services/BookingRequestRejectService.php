<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\CoffeeWallet;
use App\Models\FeaturesSetting;
use App\Models\SeatDetail;
use App\Models\TopUpBalance;
use App\Models\Transaction;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Stripe\Refund;
use Stripe\Stripe;

class BookingRequestRejectService
{
    public function rejectWeb(Booking $booking): array
    {
        $booking->update([
            'status' => '3',
            'expires_at' => null,
        ]);

        $this->releaseSeatsForRejectedBooking($booking);

        return $this->processWebRefunds($booking);
    }

    public function rejectApi(Booking $booking): array
    {
        $booking->update([
            'status' => '3',
            'expires_at' => null,
        ]);

        $this->releaseSeatsForRejectedBooking($booking);

        $cashPaymentMethodId = FeaturesSetting::where('slug', 'cash')->value('id');

        return $this->processApiRefunds($booking, $cashPaymentMethodId);
    }

    protected function releaseSeatsForRejectedBooking(Booking $booking): void
    {
        $getSeatDetails = SeatDetail::where('booking_id', $booking->id)->get();
        if ($getSeatDetails->isNotEmpty()) {
            foreach ($getSeatDetails as $getSeatDetail) {
                $getSeatDetail->status = 'pending';
                $getSeatDetail->booking_id = null;
                $getSeatDetail->user_id = null;
                $getSeatDetail->save();
            }
        }

        $orphanedHoldSeats = SeatDetail::where('ride_id', $booking->ride_id)
            ->where('user_id', $booking->user_id)
            ->where('status', 'hold')
            ->get();
        if ($orphanedHoldSeats->isNotEmpty()) {
            foreach ($orphanedHoldSeats as $seat) {
                $seat->status = 'pending';
                $seat->booking_id = null;
                $seat->user_id = null;
                $seat->save();
            }
        }
    }

    /**
     * @return array{ok: true}|array{ok: false, stripe_error: string}
     */
    protected function processWebRefunds(Booking $booking): array
    {
        $transactions = Transaction::where('booking_id', $booking->id)->where('type', '1')->get();
        foreach ($transactions as $transaction) {
            if (!$transaction) {
                continue;
            }

            $transactionAmt = 0.0;
            if (isset($transaction->coffee_from_wall) && $transaction->coffee_from_wall == 1) {
                $transactionAmt = (float) $transaction->price - (float) $transaction->booking_fee;
            } else {
                $transactionAmt = (float) $transaction->price;
            }

            $getRefundEntryPrice = Transaction::where('parent_id', $transaction->id)->sum('price');
            if (isset($getRefundEntryPrice) && !is_null($getRefundEntryPrice)) {
                $transactionAmt = $transactionAmt - $getRefundEntryPrice;
            }

            if ($transactionAmt <= 0) {
                continue;
            }

            $refundId = '';
            if ($transaction->pay_by_account == 0) {
                if ($transaction->paypal_id) {
                    $uniqueId = strtotime(date('Y-m-d H:i:s'));
                    $paypal = new PayPalClient;
                    $paypal->setApiCredentials(config('paypal'));
                    $token = $paypal->getAccessToken();
                    $paypal->setAccessToken($token);
                    $response = $paypal->refundCapturedPayment(
                        $transaction->paypal_id,
                        'Invoice-' . $uniqueId,
                        $transactionAmt,
                        'Refund issued.'
                    );
                    $refundId = isset($response['id']) ? $response['id'] : '';
                } elseif ($transaction->stripe_id) {
                    Stripe::setApiKey(config('stripe.secret'));

                    try {
                        $refund = Refund::create([
                            'payment_intent' => $transaction->stripe_id,
                            'amount' => $transactionAmt * 100,
                        ]);
                        $refundId = $refund->id;
                    } catch (\Stripe\Exception\ApiErrorException $e) {
                        return ['ok' => false, 'stripe_error' => $e->getMessage()];
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

            if (isset($transaction->coffee_from_wall) && $transaction->coffee_from_wall == 1) {
                $bookingAmt = $transaction->booking_fee;

                $getRefundBookingPrice = CoffeeWallet::where('booking_id', $booking->id)
                    ->where('ride_id', $booking->ride_id)
                    ->where('user_id', $booking->user_id)
                    ->sum('dr_amount');
                if (isset($getRefundBookingPrice) && !is_null($getRefundBookingPrice)) {
                    $bookingAmt = $transaction->booking_fee - $getRefundBookingPrice;
                }

                if ($bookingAmt > 0) {
                    CoffeeWallet::create([
                        'booking_id' => $booking->id,
                        'ride_id' => $booking->ride_id,
                        'user_id' => $booking->user_id,
                        'dr_amount' => $bookingAmt,
                    ]);
                }
            }

            Transaction::create([
                'booking_id' => $transaction->booking_id,
                'ride_id' => $booking->ride_id,
                'parent_id' => $transaction->id,
                'type' => '3',
                'price' => $transactionAmt,
                'paypal_id' => isset($transaction->paypal_id) ? $refundId : null,
                'stripe_id' => isset($transaction->stripe_id) ? $refundId : null,
            ]);
        }

        return ['ok' => true];
    }

    /**
     * @return array{ok: true}|array{ok: false, api_error: string}
     */
    protected function processApiRefunds(Booking $booking, $cashPaymentMethodId): array
    {
        $booking->loadMissing('ride');

        $getTranscationsSum = Transaction::where('booking_id', $booking->id)->where('type', '3')->sum('price');
        $getTranscationsSum = $getTranscationsSum == null ? 0 : $getTranscationsSum;

        $transactions = Transaction::where('booking_id', $booking->id)->where('type', '1')->get();
        foreach ($transactions as $transaction) {
            if (!$transaction) {
                continue;
            }

            $transactionAmt = 0.0;
            if (isset($transaction->coffee_from_wall) && $transaction->coffee_from_wall == 1) {
                $transactionAmt = ((float) $transaction->price - $getTranscationsSum) - (float) $transaction->booking_fee;
            } else {
                $transactionAmt = (float) $transaction->price - $getTranscationsSum;
            }

            $refundId = '';
            if ($transaction->pay_by_account == 0) {
                if ($transaction->paypal_id) {
                    $uniqueId = strtotime(date('Y-m-d H:i:s'));
                    $paypal = new PayPalClient;
                    $paypal->setApiCredentials(config('paypal'));
                    $token = $paypal->getAccessToken();
                    $paypal->setAccessToken($token);
                    $response = $paypal->refundCapturedPayment(
                        $transaction->paypal_id,
                        'Invoice-' . $uniqueId,
                        $booking->ride->payment_method != $cashPaymentMethodId ? $transactionAmt : $transaction->booking_fee,
                        'Refund issued.'
                    );
                    $refundId = isset($response['id']) ? $response['id'] : '';
                } elseif ($transaction->stripe_id) {
                    Stripe::setApiKey(config('stripe.secret'));

                    try {
                        $refund = Refund::create([
                            'payment_intent' => $transaction->stripe_id,
                            'amount' => $booking->ride->payment_method != $cashPaymentMethodId
                                ? $transactionAmt * 100
                                : $transaction->booking_fee * 100,
                        ]);
                        $refundId = $refund->id;
                    } catch (\Stripe\Exception\ApiErrorException $e) {
                        return ['ok' => false, 'api_error' => $e->getMessage()];
                    }
                }
            } else {
                TopUpBalance::create([
                    'booking_id' => $transaction->booking_id,
                    'user_id' => $booking->user_id,
                    'dr_amount' => $booking->ride->payment_method != $cashPaymentMethodId ? $transactionAmt : $transaction->booking_fee,
                    'added_date' => date('Y-m-d'),
                ]);
            }

            if (isset($transaction->coffee_from_wall) && $transaction->coffee_from_wall == 1) {
                CoffeeWallet::create([
                    'booking_id' => $booking->id,
                    'ride_id' => $booking->ride_id,
                    'user_id' => $booking->user_id,
                    'dr_amount' => $transaction->booking_fee,
                ]);
            }

            Transaction::create([
                'booking_id' => $transaction->booking_id,
                'ride_id' => $booking->ride_id,
                'parent_id' => $transaction->id,
                'type' => '3',
                'price' => $booking->ride->payment_method != $cashPaymentMethodId ? $transaction->price : 0,
                'booking_fee' => $booking->ride->payment_method == $cashPaymentMethodId ? $transaction->booking_fee : 0,
                'paypal_id' => isset($transaction->paypal_id) ? $refundId : null,
                'stripe_id' => isset($transaction->stripe_id) ? $refundId : null,
            ]);
        }

        return ['ok' => true];
    }
}
