<?php

namespace App\Services;

use App\Jobs\UpdateSeatOnHold;
use App\Models\Ride;
use App\Models\SeatDetail;
use App\Models\User;

class SeatHoldService
{
    public const OUTCOME_HELD = 'held';

    public const OUTCOME_RELEASED = 'released';

    public const OUTCOME_BOOKED = 'booked';

    public const OUTCOME_NOT_FOUND = 'not_found';

    public const OUTCOME_HELD_BY_OTHER = 'held_by_other';

    /**
     * @return array{outcome: string, seat: ?SeatDetail}
     */
    public function process(?SeatDetail $seat, User $user): array
    {
        if (!$seat) {
            return ['outcome' => self::OUTCOME_NOT_FOUND, 'seat' => null];
        }

        if ($seat->status === 'pending') {
            $seat->user_id = $user->id;
            $seat->status = 'hold';
            $seat->save();

            $this->scheduleReleaseJobIfRideInFuture($seat);

            return ['outcome' => self::OUTCOME_HELD, 'seat' => $seat->fresh()];
        }

        if ($seat->status === 'booked') {
            return ['outcome' => self::OUTCOME_BOOKED, 'seat' => $seat];
        }

        if ($seat->status === 'hold') {
            if ((int) $seat->user_id !== (int) $user->id) {
                return ['outcome' => self::OUTCOME_HELD_BY_OTHER, 'seat' => $seat];
            }

            $seat->user_id = null;
            $seat->status = 'pending';
            $seat->save();

            return ['outcome' => self::OUTCOME_RELEASED, 'seat' => $seat->fresh()];
        }

        return ['outcome' => self::OUTCOME_NOT_FOUND, 'seat' => $seat];
    }

    protected function scheduleReleaseJobIfRideInFuture(SeatDetail $seat): void
    {
        $ride = Ride::query()->where('id', $seat->ride_id)->first();
        if (!$ride) {
            return;
        }

        $rideTimestamp = strtotime($ride->date . ' ' . $ride->time);
        $now = time();
        if ($rideTimestamp <= $now) {
            return;
        }

        $hoursUntilRide = ($rideTimestamp - $now) / 3600;
        $delayMinutes = $hoursUntilRide <= 1 ? 5 : 10;

        UpdateSeatOnHold::dispatch($seat->id)->delay(now()->addMinutes((int) $delayMinutes));
    }
}
