<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\CancellationHistory;
use App\Models\City;
use App\Models\CoffeeWallet;
use App\Models\Payout;
use App\Models\Ride;
use App\Models\SeatDetail;
use App\Models\TopUpBalance;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Stripe\Refund;
use Stripe\Stripe;

class BookingCancellationService
{
    /**
     * Returns a map of parent transaction id => sum(child column).
     *
     * @param  Collection<int, Transaction>  $transactions
     */
    public function refundedChildSums(Collection $transactions, string $column = 'price'): Collection
    {
        $parentIds = $transactions->pluck('id')->filter()->values();
        if ($parentIds->isEmpty()) {
            return collect();
        }

        return Transaction::whereIn('parent_id', $parentIds)
            ->selectRaw("parent_id, sum({$column}) as total")
            ->groupBy('parent_id')
            ->pluck('total', 'parent_id');
    }

    /**
     * Create or update the pending driver payout for a passenger cancellation.
     * Applies tax deduction when configured (`deduct_from_driver`) and returns the final amount.
     */
    public function upsertPendingDriverCancellationPayout(Booking $booking, Ride $ride, $setting, float $grossPayoutAmt): float
    {
        $payout = Payout::where('ride_id', $booking->ride_id)
            ->where('booking_id', $booking->id)
            ->first() ?? new Payout();

        $deduct_tax = $tax_type = "";
        $tax = 0;
        $taxAmt = 0;

        if (isset($setting) && !empty($setting) && isset($setting->deduct_tax) && $setting->deduct_tax === "deduct_from_driver") {
            $deduct_tax = $setting->deduct_tax;
            $tax_type = $setting->tax_type;

            if (isset($setting->tax_type) && $setting->tax_type === "state_wise_tax") {
                $locationBeforeComma = explode(',', (string) $booking->departure);
                $getFromState = City::with('state:id,tax')
                    ->where('status', '1')
                    ->whereRaw('LOWER(`name`) LIKE ? ', ['%' . ($locationBeforeComma[0] ?? '') . '%'])
                    ->first();

                if (!empty($getFromState)) {
                    $tax = (float) ($getFromState->state->tax ?? 0);
                }
            } else {
                $tax = (float) ($setting->tax ?? 0);
            }

            $taxAmt = round((($grossPayoutAmt * $tax) / 100), 2);
            $grossPayoutAmt = $grossPayoutAmt - $taxAmt;
        }

        if (isset($payout->amount)) {
            $grossPayoutAmt = (float) $payout->amount + $grossPayoutAmt;
        }

        $rideDateTime = Carbon::parse($ride->completed_date . ' ' . $ride->completed_time);

        $payout->ride_id = $booking->ride_id;
        $payout->booking_id = $booking->id;
        $payout->user_id = $ride->added_by;
        $payout->amount = $grossPayoutAmt;
        $payout->available_date = $rideDateTime;
        $payout->status = "pending";
        $payout->tax_amount = $taxAmt;
        $payout->tax_percentage = $tax != 0 ? $tax : 0;
        $payout->tax_type = ($tax_type !== "") ? $tax_type : null;
        $payout->deduct_type = ($deduct_tax !== "") ? $deduct_tax : null;
        $payout->save();

        return $grossPayoutAmt;
    }

    /**
     * Creates refund Transactions for "firm" cancellations.
     *
     * This is the same algorithm used in:
     * - web: when `$ride->isFirmCancellation()`
     * - api: when `$booking->type == 37`
     */
    public function createRefundTransactionsFirmCancellation(
        Booking $booking,
        Ride $ride,
        Collection $transactions,
        Collection $refundEntrySums,
        float $refundAmount
    ): void {
        $remainingRefundAmount = $refundAmount;

        foreach ($transactions as $transaction) {
            $existingChildSum = $refundEntrySums->has($transaction->id)
                ? (float) $refundEntrySums->get($transaction->id)
                : null;

            $expectedChildSum = (float) $transaction->price - (float) $transaction->booking_fee;

            // Skip if we already refunded this parent completely
            if (
                isset($existingChildSum)
                && !is_null($existingChildSum)
                && $existingChildSum == $expectedChildSum
            ) {
                continue;
            }

            $transactionAmount = (float) $transaction->price - (float) $transaction->booking_fee;

            if ($remainingRefundAmount <= 0) {
                break;
            }

            if ($transactionAmount >= $remainingRefundAmount) {
                Transaction::create([
                    'booking_id' => $transaction->booking_id,
                    'ride_id' => $booking->ride_id,
                    'parent_id' => $transaction->id,
                    'type' => '3',
                    'price' => $remainingRefundAmount,
                ]);

                $remainingRefundAmount = 0;
                break;
            }

            Transaction::create([
                'booking_id' => $transaction->booking_id,
                'ride_id' => $booking->ride_id,
                'parent_id' => $transaction->id,
                'type' => '3',
                'price' => $transactionAmount,
            ]);

            $remainingRefundAmount -= $transactionAmount;
        }
    }

    /**
     * Creates refund Transactions for non-firm cancellations where the ride is > 48 hours away.
     *
     * Canonical implementation is the web controller logic.
     */
    public function createRefundTransactionsNonFirmOver48Web(
        Booking $booking,
        Ride $ride,
        Collection $transactions,
        Collection $refundEntrySums,
        int $cancelSeats,
        float $refundAmount,
        float $seatBookingPrice
    ): void {
        $remainingRefundAmount = $refundAmount;

        foreach ($transactions as $transaction) {
            $existingChildSum = $refundEntrySums->has($transaction->id)
                ? (float) $refundEntrySums->get($transaction->id)
                : null;

            $checkPrice = (float) $transaction->price;
            if (isset($existingChildSum) && !is_null($existingChildSum) && $existingChildSum == $checkPrice) {
                continue;
            }

            $transactionAmount = (float) $transaction->price;

            if ($remainingRefundAmount <= 0) {
                break;
            }

            $refundId = "";
            $totalBookingFee = $seatBookingPrice * $cancelSeats;

            // In web flow, booking fee is added to the refund amount at this point.
            $remainingRefundAmount = $remainingRefundAmount + $totalBookingFee;

            // Coffee Wall handling reduces the amount actually refunded online.
            if (isset($transaction->coffee_from_wall) && (int) $transaction->coffee_from_wall === 1) {
                if ((float) $transaction->booking_fee >= $totalBookingFee) {
                    CoffeeWallet::create([
                        'booking_id' => $booking->id,
                        'ride_id' => $ride->id,
                        'user_id' => $booking->user_id,
                        'dr_amount' => $totalBookingFee,
                    ]);
                    $remainingRefundAmount = $remainingRefundAmount - $totalBookingFee;
                } else {
                    CoffeeWallet::create([
                        'booking_id' => $booking->id,
                        'ride_id' => $booking->ride_id,
                        'user_id' => $booking->user_id,
                        'dr_amount' => (float) $transaction->booking_fee,
                    ]);
                    $remainingRefundAmount = $remainingRefundAmount - (float) $transaction->booking_fee;
                }
            }

            $refundThisTxn = $transactionAmount >= $remainingRefundAmount
                ? $remainingRefundAmount
                : $transactionAmount;

            if ((int) $transaction->pay_by_account === 0) {
                if ($transaction->paypal_id) {
                    try {
                        $paypal = new PayPalClient;
                        $paypal->setApiCredentials(config('paypal'));
                        $token = $paypal->getAccessToken();
                        $paypal->setAccessToken($token);
                        $response = $paypal->refundCapturedPayment(
                            $transaction->paypal_id,
                            'Invoice-' . $transaction->paypal_id,
                            $refundThisTxn,
                            'Refund issued.'
                        );

                        $refundId = isset($response['id']) ? $response['id'] : "";
                    } catch (\PayPal\Exception\PayPalConnectionException $e) {
                        $errorData = json_decode($e->getData(), true);
                        Log::error("PayPal error: " . ($errorData['message'] ?? $e->getMessage()));
                    }
                } elseif ($transaction->stripe_id) {
                    Stripe::setApiKey(config('stripe.secret'));
                    try {
                        $refund = Refund::create([
                            'payment_intent' => $transaction->stripe_id,
                            'amount' => $refundThisTxn * 100,
                        ]);
                        $refundId = $refund->id;
                    } catch (\Stripe\Exception\ApiErrorException $e) {
                        // keep legacy silent behavior
                    }
                }
            } else {
                TopUpBalance::create([
                    'booking_id' => $transaction->booking_id,
                    'user_id' => $booking->user_id,
                    'dr_amount' => $refundThisTxn,
                    'added_date' => date('Y-m-d'),
                ]);
            }

            Transaction::create([
                'booking_id' => $transaction->booking_id,
                'ride_id' => $booking->ride_id,
                'parent_id' => $transaction->id,
                'type' => '3',
                'price' => $refundThisTxn,
                'paypal_id' => isset($transaction->paypal_id) ? $refundId : NULL,
                'stripe_id' => isset($transaction->stripe_id) ? $refundId : NULL,
            ]);

            $remainingRefundAmount -= $refundThisTxn;

            if ($remainingRefundAmount <= 0) {
                break;
            }
        }
    }

    /**
     * Creates refund Transactions for non-firm cancellations where 12 <= hours <= 48.
     *
     * Canonical implementation is the web controller logic:
     * - refund half to passenger (payment gateway or top-up)
     * - create a matching driver Transaction entry
     * - decrease remaining by the full transactionAmount
     */
    public function createRefundTransactionsNonFirmBetween12And48Web(
        Booking $booking,
        Ride $ride,
        Collection $transactions,
        Collection $refundEntrySums,
        float $refundAmount
    ): void {
        $remainingRefundAmount = $refundAmount;
        $passengerRefundAmt = $refundAmount * 0.5;

        foreach ($transactions as $transaction) {
            $existingChildSum = $refundEntrySums->has($transaction->id)
                ? (float) $refundEntrySums->get($transaction->id)
                : null;

            $expectedChildSum = (float) $transaction->price - (float) $transaction->booking_fee;
            if (isset($existingChildSum) && !is_null($existingChildSum) && $existingChildSum == $expectedChildSum) {
                continue;
            }

            $transactionAmount = (float) $transaction->price - (float) $transaction->booking_fee;

            if ($remainingRefundAmount <= 0) {
                break;
            }

            $refundId = "";
            $refundThisPassenger = $transactionAmount >= $remainingRefundAmount
                ? $passengerRefundAmt
                : ($transactionAmount * 0.5);

            if ((int) $transaction->pay_by_account === 0) {
                if ($transaction->paypal_id) {
                    try {
                        $paypal = new PayPalClient;
                        $paypal->setApiCredentials(config('paypal'));
                        $token = $paypal->getAccessToken();
                        $paypal->setAccessToken($token);
                        $response = $paypal->refundCapturedPayment(
                            $transaction->paypal_id,
                            'Invoice-' . $transaction->paypal_id,
                            $refundThisPassenger,
                            'Refund issued.'
                        );

                        $refundId = isset($response['id']) ? $response['id'] : "";
                    } catch (\PayPal\Exception\PayPalConnectionException $e) {
                        $errorData = json_decode($e->getData(), true);
                        Log::error("PayPal error: " . ($errorData['message'] ?? $e->getMessage()));
                    }
                } elseif ($transaction->stripe_id) {
                    Stripe::setApiKey(config('stripe.secret'));
                    try {
                        $refund = Refund::create([
                            'payment_intent' => $transaction->stripe_id,
                            'amount' => $refundThisPassenger * 100,
                        ]);
                        $refundId = $refund->id;
                    } catch (\Stripe\Exception\ApiErrorException $e) {
                        // keep legacy silent behavior
                    }
                }
            } else {
                TopUpBalance::create([
                    'booking_id' => $transaction->booking_id,
                    'user_id' => $booking->user_id,
                    'dr_amount' => $refundThisPassenger,
                    'added_date' => date('Y-m-d'),
                ]);
            }

            // Passenger entry
            Transaction::create([
                'booking_id' => $transaction->booking_id,
                'ride_id' => $booking->ride_id,
                'parent_id' => $transaction->id,
                'type' => '3',
                'price' => $refundThisPassenger,
                'paypal_id' => isset($transaction->paypal_id) ? $refundId : NULL,
                'stripe_id' => isset($transaction->stripe_id) ? $refundId : NULL,
            ]);

            // Driver entry
            Transaction::create([
                'booking_id' => $transaction->booking_id,
                'ride_id' => $booking->ride_id,
                'parent_id' => $transaction->id,
                'type' => '3',
                'price' => $refundThisPassenger,
            ]);

            // Match legacy behavior: reduce by full transactionAmount.
            $remainingRefundAmount -= $transactionAmount;

            if ($transactionAmount >= $remainingRefundAmount) {
                break;
            }
        }
    }

    /**
     * Creates refund Transactions for non-firm cancellations where hours < 12.
     *
     * Canonical implementation is the web controller logic:
     * create Transaction(type=3) entries against parent transactions without gateway refunds.
     */
    public function createRefundTransactionsNonFirmUnder12Web(
        Booking $booking,
        Ride $ride,
        Collection $transactions,
        Collection $refundEntrySums,
        float $refundAmount
    ): void {
        $remainingRefundAmount = $refundAmount;

        foreach ($transactions as $transaction) {
            $existingChildSum = $refundEntrySums->has($transaction->id)
                ? (float) $refundEntrySums->get($transaction->id)
                : null;

            $expectedChildSum = (float) $transaction->price - (float) $transaction->booking_fee;
            if (isset($existingChildSum) && !is_null($existingChildSum) && $existingChildSum == $expectedChildSum) {
                continue;
            }

            $transactionAmount = (float) $transaction->price - (float) $transaction->booking_fee;

            if ($remainingRefundAmount <= 0) {
                break;
            }

            $refundThisTxn = $transactionAmount >= $remainingRefundAmount
                ? $remainingRefundAmount
                : $transactionAmount;

            Transaction::create([
                'booking_id' => $transaction->booking_id,
                'ride_id' => $booking->ride_id,
                'parent_id' => $transaction->id,
                'type' => '3',
                'price' => $refundThisTxn,
            ]);

            $remainingRefundAmount -= $refundThisTxn;

            if ($remainingRefundAmount <= 0) {
                break;
            }
        }
    }

    /**
     * Web-canonical cancellation flow (refunds + payout + seat cancellation).
     *
     * Returns:
     * - booking: refreshed Booking (after seat cancellation)
     * - payoutAmt: final payout amount saved (after tax + accumulation)
     * - originalSeats: seats booked before cancellation
     * - cancelSeats: seats cancelled
     */
    public function cancelPassengerBookingWebFlow(Booking $booking, Ride $ride, int $cancelSeats, int $actorUserId, $siteSetting): array
    {
        $originalSeats = (int) $booking->seats;
        if ($cancelSeats <= 0 || $cancelSeats > $originalSeats) {
            throw new \InvalidArgumentException('Invalid number of seats to cancel.');
        }

        $rideDateTime = Carbon::parse($ride->date . ' ' . $ride->time);
        $bookingDateTime = Carbon::parse($booking->booked_on);
        $hoursDifference = $rideDateTime->diffInHours($bookingDateTime);

        $transactions = Transaction::where('booking_id', $booking->id)
            ->where('type', '1')
            ->get();

        $refundEntrySumsPrice = $this->refundedChildSums($transactions, 'price');

        $seatFarePrice = (float) $booking->fare / max(1, (int) $booking->seats);
        $seatBookingFee = (float) $booking->booking_credit / max(1, (int) $booking->seats);

        $refundAmount = $cancelSeats * $seatFarePrice;
        $refundTotalAmount = $refundAmount;
        $refundTotalBookingFee = $cancelSeats * $seatBookingFee;

        $payoutAmt = 0.0;

        if ($ride->isFirmCancellation()) {
            $this->createRefundTransactionsFirmCancellation(
                $booking,
                $ride,
                $transactions,
                $refundEntrySumsPrice,
                (float) $refundAmount
            );

            $payoutGrossAmt = (isset($siteSetting->booking_fee_give_to_driver) && (int) $siteSetting->booking_fee_give_to_driver === 1)
                ? $refundTotalAmount + $refundTotalBookingFee
                : $refundTotalAmount;

            $payoutAmt = $this->upsertPendingDriverCancellationPayout($booking, $ride, $siteSetting, (float) $payoutGrossAmt);
        } else {
            if ($hoursDifference > 48) {
                $this->createRefundTransactionsNonFirmOver48Web(
                    $booking,
                    $ride,
                    $transactions,
                    $refundEntrySumsPrice,
                    $cancelSeats,
                    (float) $refundAmount,
                    (float) $seatBookingFee
                );
            } elseif ($hoursDifference >= 12 && $hoursDifference <= 48) {
                $this->createRefundTransactionsNonFirmBetween12And48Web(
                    $booking,
                    $ride,
                    $transactions,
                    $refundEntrySumsPrice,
                    (float) $refundAmount
                );

                $passengerAndDriverRefundAmt = $refundAmount * 0.5;
                $passengerAndDriverRefundBookingFee = $refundTotalBookingFee * 0.5;

                $payoutGrossAmt = (isset($siteSetting->booking_fee_give_to_driver) && (int) $siteSetting->booking_fee_give_to_driver === 1)
                    ? $passengerAndDriverRefundAmt + $passengerAndDriverRefundBookingFee
                    : $passengerAndDriverRefundAmt;

                $payoutAmt = $this->upsertPendingDriverCancellationPayout($booking, $ride, $siteSetting, (float) $payoutGrossAmt);
            } elseif ($hoursDifference < 12) {
                $this->createRefundTransactionsNonFirmUnder12Web(
                    $booking,
                    $ride,
                    $transactions,
                    $refundEntrySumsPrice,
                    (float) $refundAmount
                );
            }

            if ($hoursDifference < 12 || $hoursDifference > 48) {
                $payoutGrossAmt = (isset($siteSetting->booking_fee_give_to_driver) && (int) $siteSetting->booking_fee_give_to_driver === 1)
                    ? $refundTotalAmount + $refundTotalBookingFee
                    : $refundTotalAmount;

                $payoutAmt = $this->upsertPendingDriverCancellationPayout($booking, $ride, $siteSetting, (float) $payoutGrossAmt);
            }
        }

        $booking = $this->applySeatCancellation($booking, $cancelSeats, $actorUserId);

        return [
            'booking' => $booking,
            'payoutAmt' => $payoutAmt,
            'originalSeats' => $originalSeats,
            'cancelSeats' => $cancelSeats,
        ];
    }

    /**
     * Apply a seat cancellation to the booking:
     * - updates booking seats/credit/fare or marks cancelled
     * - frees up SeatDetail rows for the cancelled seats
     * - creates CancellationHistory row
     *
     * Returns the refreshed Booking instance.
     */
    public function applySeatCancellation(Booking $booking, int $cancelSeats, int $actorUserId): Booking
    {
        $originalSeats = (int) $booking->seats;

        if ($cancelSeats <= 0 || $cancelSeats > $originalSeats) {
            throw new \InvalidArgumentException('Invalid number of seats to cancel.');
        }

        $updatedSeats = $originalSeats - $cancelSeats;

        $perSeatBookingCredit = $originalSeats > 0 ? ((float) $booking->booking_credit / $originalSeats) : 0.0;
        $perSeatFare = $originalSeats > 0 ? ((float) $booking->fare / $originalSeats) : 0.0;

        if ($cancelSeats < $originalSeats) {
            $booking->update([
                'seats' => $updatedSeats,
                'booking_credit' => $perSeatBookingCredit * $updatedSeats,
                'fare' => $perSeatFare * $updatedSeats,
            ]);
        } else {
            $booking->update([
                'status' => '4',
            ]);
        }

        SeatDetail::where('booking_id', $booking->id)
            ->orderBy('id')
            ->limit($cancelSeats)
            ->update([
                'status' => 'pending',
                'booking_id' => null,
                'user_id' => null,
            ]);

        CancellationHistory::create([
            'ride_id' => $booking->ride_id,
            'booking_id' => $booking->id,
            'user_id' => $actorUserId,
            'type' => 'passenger',
        ]);

        return $booking->refresh();
    }
}

