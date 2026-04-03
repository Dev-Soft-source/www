<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Booking;
use App\Models\User;

class NotifyBookingRequestApprovedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $bookingId;
    public int $driverId;
    public bool $statusAlreadyBooked;

    public function __construct(int $bookingId, int $driverId, bool $statusAlreadyBooked = false)
    {
        $this->bookingId = $bookingId;
        $this->driverId = $driverId;
        $this->statusAlreadyBooked = $statusAlreadyBooked;
    }

    public function handle()
    {
        $booking = Booking::with(['passenger.primaryPhone', 'ride.driver.primaryPhone'])->find($this->bookingId);
        $driver = User::find($this->driverId);

        if (!$booking || !$driver) {
            return;
        }

        $notifications = app(\App\Http\Controllers\BookingWebNotificationController::class);
        $notifications->notifyBookingRequestApprovedWebFlowSync($booking, $driver, $this->statusAlreadyBooked);
    }
}
