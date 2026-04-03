<?php

namespace App\Jobs;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CompleteBookingNotificationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $bookingId,
        public array $payload = []
    ) {
    }

    public function handle(): void
    {
        $booking = Booking::with([
            'passenger.primaryPhone',
            'ride.driver.primaryPhone',
        ])->find($this->bookingId);

        if (!$booking || !$booking->ride || !$booking->passenger || !$booking->ride->driver) {
            return;
        }

        $notifications = app(\App\Http\Controllers\BookingWebNotificationController::class);
        $notifications->completeBookingUnifiedFlowNotificationsSync($booking, $this->payload);
    }
}
