<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\CancellationHistory;
use App\Models\CoffeeWallet;
use App\Models\FeaturesSetting;
use App\Models\Ride;
use App\Models\TopUpBalance;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Stripe\Refund;
use Stripe\Stripe;

class DriverRideCancellationService
{
    /**
     * Ride-level cancel when there are no passenger bookings to refund (web: also revokes Extra Care).
     */
    public function markRideCancelledEmpty(Ride $ride, bool $revokeExtraCareEligibility): void
    {
        $ride->update(['status' => '2']);

        CancellationHistory::create([
            'ride_id' => $ride->id,
            'user_id' => $ride->added_by,
            'type' => 'driver',
        ]);

        if ($revokeExtraCareEligibility) {
            User::where('id', $ride->added_by)->whereIn('folks_ride', ['1', ''])->update(['folks_ride' => '0']);
        }
    }

    /**
     * Web flow: cancel ride first, then refund and mark each booking cancelled (legacy order).
     *
     * @param  Collection<int, Booking>  $bookings
     * @return list<int>  Booking ids for queued passenger notifications
     */
    public function cancelByDriverWeb(Ride $ride, Collection $bookings): array
    {
        $ride->update(['status' => '2']);

        CancellationHistory::create([
            'ride_id' => $ride->id,
            'user_id' => $ride->added_by,
            'type' => 'driver',
        ]);

        User::where('id', $ride->added_by)->whereIn('folks_ride', ['1', ''])->update(['folks_ride' => '0']);

        $ids = [];
        foreach ($bookings as $booking) {
            $this->refundTypeOneTransactionsWeb($ride, $booking);
            $booking->update(['status' => '4']);
            $ids[] = (int) $booking->id;
        }

        return $ids;
    }

    /**
     * API flow: refund each booking, per-booking history, then mark ride cancelled (legacy order).
     *
     * @param  Collection<int, Booking>  $bookings
     * @return array{ok: true, booking_ids: list<int>}|array{ok: false, error: string}
     */
    public function cancelByDriverApi(Ride $ride, Collection $bookings): array
    {
        $cashPaymentMethodId = FeaturesSetting::where('slug', 'cash')->value('id');

        $ids = [];
        foreach ($bookings as $booking) {
            $refundResult = $this->refundTypeOneTransactionsApi($ride, $booking, $cashPaymentMethodId);
            if (!$refundResult['ok']) {
                return ['ok' => false, 'error' => $refundResult['error']];
            }

            $booking->update(['status' => '4']);

            CancellationHistory::create([
                'ride_id' => $booking->ride_id,
                'booking_id' => $booking->id,
                'user_id' => $ride->added_by,
            ]);

            $ids[] = (int) $booking->id;
        }

        $ride->update(['status' => '2']);

        return ['ok' => true, 'booking_ids' => $ids];
    }

    protected function refundTypeOneTransactionsWeb(Ride $ride, Booking $booking): void
    {
        $transactions = Transaction::where('booking_id', $booking->id)->where('type', '1')->get();

        foreach ($transactions as $transaction) {
            if (!$transaction) {
                continue;
            }

            $refundId = '';

            $checkPrice = 0.0;
            $getRefundEntryPrice = Transaction::where('parent_id', $transaction->id)->sum('price');
            if (isset($transaction->coffee_from_wall) && $transaction->coffee_from_wall == 1) {
                $getRefundEntryPrice = (float) $getRefundEntryPrice + (float) $transaction->booking_fee;
            }
            $checkPrice = (float) $transaction->price;

            if (isset($getRefundEntryPrice) && !is_null($getRefundEntryPrice) && (float) $getRefundEntryPrice == $checkPrice) {
                if (isset($transaction->coffee_from_wall) && $transaction->coffee_from_wall == 1) {
                    CoffeeWallet::create([
                        'booking_id' => $booking->id,
                        'ride_id' => $ride->id,
                        'user_id' => $booking->user_id,
                        'dr_amount' => $transaction->booking_fee,
                    ]);

                    Transaction::create([
                        'booking_id' => $transaction->booking_id,
                        'ride_id' => $booking->ride_id,
                        'parent_id' => $transaction->id,
                        'type' => '3',
                        'price' => $transaction->booking_fee,
                        'paypal_id' => null,
                        'stripe_id' => null,
                    ]);
                }
            } else {
                $transactionAmt = $checkPrice - (float) $getRefundEntryPrice;

                if (isset($transaction->coffee_from_wall) && $transaction->coffee_from_wall == 1) {
                    $transactionAmt = $transactionAmt - (float) $transaction->booking_fee;
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
                                'amount' => $transactionAmt * 100,
                            ]);

                            $refundId = $refund->id;
                        } catch (\Stripe\Exception\ApiErrorException $e) {
                            Log::info($e->getMessage());
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
                    CoffeeWallet::create([
                        'booking_id' => $booking->id,
                        'ride_id' => $ride->id,
                        'user_id' => $booking->user_id,
                        'dr_amount' => $transaction->booking_fee,
                    ]);
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
        }
    }

    /**
     * @return array{ok: true}|array{ok: false, error: string}
     */
    protected function refundTypeOneTransactionsApi(Ride $ride, Booking $booking, $cashPaymentMethodId): array
    {
        $transactions = Transaction::where('booking_id', $booking->id)->where('type', '1')->get();

        foreach ($transactions as $transaction) {
            if (!$transaction) {
                continue;
            }

            $refundId = '';

            $checkPrice = 0.0;
            if ($ride->payment_method != $cashPaymentMethodId) {
                $getRefundEntryPrice = Transaction::where('parent_id', $transaction->id)->sum('price');
                if (isset($transaction->coffee_from_wall) && $transaction->coffee_from_wall == 1) {
                    $getRefundEntryPrice = (float) $getRefundEntryPrice + (float) $transaction->booking_fee;
                }
                $checkPrice = (float) $transaction->price;
            } else {
                $getRefundEntryPrice = Transaction::where('parent_id', $transaction->id)->sum('booking_fee');
                $checkPrice = (float) $transaction->booking_fee;
            }

            if (isset($getRefundEntryPrice) && !is_null($getRefundEntryPrice) && (float) $getRefundEntryPrice == $checkPrice) {
                continue;
            }

            $transactionAmt = $checkPrice - (float) $getRefundEntryPrice;

            if (isset($transaction->coffee_from_wall) && $transaction->coffee_from_wall == 1) {
                $transactionAmt = $transactionAmt - (float) $transaction->booking_fee;
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
                            'amount' => $transactionAmt * 100,
                        ]);

                        $refundId = $refund->id;
                    } catch (\Stripe\Exception\ApiErrorException $e) {
                        return ['ok' => false, 'error' => $e->getMessage()];
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
                CoffeeWallet::create([
                    'booking_id' => $booking->id,
                    'ride_id' => $ride->id,
                    'user_id' => $booking->user_id,
                    'dr_amount' => $transaction->booking_fee,
                ]);
            }

            if (isset($transaction->coffee_from_wall) && $transaction->coffee_from_wall == 1) {
                Transaction::create([
                    'booking_id' => $transaction->booking_id,
                    'ride_id' => $booking->ride_id,
                    'parent_id' => $transaction->id,
                    'type' => '3',
                    'price' => $ride->payment_method != $cashPaymentMethodId ? $transactionAmt : 0,
                    'booking_fee' => $ride->payment_method == $cashPaymentMethodId ? $transactionAmt : $transaction->booking_fee,
                    'paypal_id' => isset($transaction->paypal_id) ? $refundId : null,
                    'stripe_id' => isset($transaction->stripe_id) ? $refundId : null,
                ]);
            } else {
                Transaction::create([
                    'booking_id' => $transaction->booking_id,
                    'ride_id' => $booking->ride_id,
                    'parent_id' => $transaction->id,
                    'type' => '3',
                    'price' => $ride->payment_method != $cashPaymentMethodId ? $transactionAmt : 0,
                    'booking_fee' => $ride->payment_method == $cashPaymentMethodId ? $transactionAmt : 0,
                    'paypal_id' => isset($transaction->paypal_id) ? $refundId : null,
                    'stripe_id' => isset($transaction->stripe_id) ? $refundId : null,
                ]);
            }
        }

        return ['ok' => true];
    }

    public function countBookedSeatsExcludingInactive(Ride $ride): float|int
    {
        return (int) $ride->bookings()
            ->where('status', '!=', '0')
            ->where('status', '!=', '3')
            ->where('status', '!=', '4')
            ->sum('seats');
    }

    /**
     * Bookings included in the API cancel-ride passenger loop (legacy query).
     *
     * @return Collection<int, Booking>
     */
    public function bookingsForApiCancel(Ride $ride): Collection
    {
        return Booking::where('ride_id', $ride->id)
            ->where('status', '!=', '0')
            ->where('status', '!=', '3')
            ->where('status', '!=', '4')
            ->with('passenger')
            ->get();
    }

    /**
     * Bookings included in the web cancel-ride passenger loop (booked or completed).
     *
     * @return Collection<int, Booking>
     */
    public function bookingsForWebCancel(int $rideId): Collection
    {
        return Booking::where('ride_id', $rideId)
            ->bookedOrCompleted()
            ->with('passenger')
            ->get();
    }
}
