<?php

namespace App\Jobs;

use App\Http\Controllers\BookingWebNotificationController;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NotifyBookingRequestRejectedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $bookingId;

    public int $driverId;

    /**
     * @var string  'web' | 'api'
     */
    public string $channel;

    public function __construct(int $bookingId, int $driverId, string $channel = 'web')
    {
        $this->bookingId = $bookingId;
        $this->driverId = $driverId;
        $this->channel = $channel;
    }

    public function handle(): void
    {
        $booking = Booking::with(['passenger', 'ride.driver'])->find($this->bookingId);
        $driver = User::find($this->driverId);

        if (!$booking || !$driver) {
            return;
        }

        app(BookingWebNotificationController::class)
            ->notifyBookingRequestRejectedWebFlowSync($booking, $driver, $this->channel);
    }
}
