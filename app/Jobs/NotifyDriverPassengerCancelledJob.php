<?php

namespace App\Jobs;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NotifyDriverPassengerCancelledJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $bookingId,
        public int $actorUserId,
        public string $cancellationMessage,
        public int $originalSeats,
        public int $cancelSeats,
        public float $payoutAmt
    ) {
    }

    public function handle(): void
    {
        $booking = Booking::with(['passenger', 'ride.driver'])->find($this->bookingId);
        $actor = User::find($this->actorUserId);

        if (!$booking || !$booking->ride || !$booking->passenger || !$actor) {
            return;
        }

        $ride = $booking->ride;
        if (!$ride->driver) {
            return;
        }

        $notifications = app(\App\Http\Controllers\BookingWebNotificationController::class);
        $notifications->notifyDriverPassengerCancelledWebFlowSync(
            $booking,
            $ride,
            $actor,
            $this->cancellationMessage,
            $this->originalSeats,
            $this->cancelSeats,
            $this->payoutAmt
        );
    }
}
