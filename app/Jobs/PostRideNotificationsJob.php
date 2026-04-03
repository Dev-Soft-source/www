<?php

namespace App\Jobs;

use App\Http\Controllers\RideWebNotificationController;
use App\Models\Ride;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PostRideNotificationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @param  array<string, mixed>  $payload
     */
    public function __construct(
        public int $rideId,
        public int $userId,
        public array $payload = []
    ) {
    }

    public function handle(): void
    {
        $ride = Ride::with('detail')->find($this->rideId);
        $user = User::find($this->userId);

        if (!$ride || !$user) {
            return;
        }

        app(RideWebNotificationController::class)->postRidePostedNotificationsSync($ride, $user, $this->payload);
    }
}
