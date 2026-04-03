<?php

namespace App\Jobs;

use App\Http\Controllers\BookingWebNotificationController;
use App\Models\Booking;
use App\Models\Ride;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NotifyDriverCancelledRidePassengersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @param  list<int>  $bookingIds
     */
    public function __construct(
        public int $rideId,
        public int $driverUserId,
        public array $bookingIds,
        public string $cancellationMessage,
        public string $channel = 'web'
    ) {
    }

    public function handle(): void
    {
        $ride = Ride::with('driver')->find($this->rideId);
        $driver = User::find($this->driverUserId);

        if (!$ride || !$driver || $this->bookingIds === []) {
            return;
        }

        $notifications = app(BookingWebNotificationController::class);

        foreach ($this->bookingIds as $bookingId) {
            $booking = Booking::with(['passenger', 'ride.driver'])->find($bookingId);
            if (!$booking || !$booking->passenger) {
                continue;
            }

            $notifications->notifyDriverCancelledRidePassengerWebFlowSync(
                $booking,
                $ride,
                $driver,
                $this->cancellationMessage,
                $this->channel
            );
        }
    }
}
