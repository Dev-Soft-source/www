<?php

namespace App\Http\Controllers;

use App\Jobs\PostRideNotificationsJob;
use App\Mail\ExtraCareRideMail;
use App\Mail\PinkExtraCareRideMail;
use App\Mail\PinkRideMail;
use App\Mail\RidePostedMail;
use App\Models\Notification;
use App\Models\Ride;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

/**
 * Queued ride-post side effects: optional confirmation email, in-app notification, FCM.
 *
 * {@see PostRideNotificationsJob} runs after the HTTP request transaction commits.
 */
class RideWebNotificationController extends Controller
{
    /**
     * Queue driver-facing post-ride notifications (new rides only).
     *
     * @param  int  $rideId  Primary ride row id.
     * @param  int  $userId  Driver who posted the ride.
     * @param  array<string, mixed>  $payload  Keys: my_rides_lang_abbr, posted_date, posted_time, seats, price_minor.
     */
    public function dispatchPostRidePostedNotifications(int $rideId, int $userId, array $payload): void
    {
        PostRideNotificationsJob::dispatch($rideId, $userId, $payload)->afterCommit();
    }

    /**
     * Synchronous worker: email (if enabled), DB notification row, FCM to the driver.
     *
     * @param  array<string, mixed>  $payload  Same shape as {@see dispatchPostRidePostedNotifications()}.
     */
    public function postRidePostedNotificationsSync(Ride $ride, User $user, array $payload): void
    {
        $ride->loadMissing('detail');
        if (!$ride->detail) {
            return;
        }

        $langAbbr = (string) ($payload['my_rides_lang_abbr'] ?? 'en');
        $postedDate = $payload['posted_date'] ?? $ride->date;
        $postedTime = $payload['posted_time'] ?? $ride->time;
        $seats = $payload['seats'] ?? $ride->seats;
        $priceMinor = (int) ($payload['price_minor'] ?? $ride->price ?? 0);

        if (isset($user->email_notification) && (int) $user->email_notification === 1) {
            $mailData = [
                'username' => $user->first_name,
                'from' => $ride->detail->departure,
                'to' => $ride->detail->destination,
                'on' => $postedDate,
                'at' => $postedTime,
                'seats' => $seats,
                'price' => number_format($priceMinor / 100, 2, '.', ''),
                'redirect' => route('my_rides', ['lang' => $langAbbr]),
            ];

            if ($ride->isPinkRide() && $ride->isExtraCareRide()) {
                Mail::to($user->email)->send(new PinkExtraCareRideMail($mailData));
            } elseif ($ride->isPinkRide()) {
                Mail::to($user->email)->send(new PinkRideMail($mailData));
            } elseif ($ride->isExtraCareRide()) {
                Mail::to($user->email)->send(new ExtraCareRideMail($mailData));
            } else {
                Mail::to($user->email)->send(new RidePostedMail($mailData));
            }
        }

        if ($ride->isPinkRide() && $ride->isExtraCareRide()) {
            $type = 'pink_extra_care';
        } elseif ($ride->isPinkRide()) {
            $type = 'pink';
        } elseif ($ride->isExtraCareRide()) {
            $type = 'extra_care';
        } else {
            $type = 'standard';
        }

        $messageConfig = [
            'standard' => 'ride_live_standard',
            'pink' => 'ride_live_pink',
            'extra_care' => 'ride_live_extra_care',
            'pink_extra_care' => 'ride_live_pink_extra_care',
        ];

        $hasVehicle = !empty($ride->vehicle_id);
        $slug = $hasVehicle ? $messageConfig[$type] : 'ride_live_requires_vehicle';
        $notificationBody = getNotificationMessageText($slug, $user, [], 'Add your vehicle to make your ride live');

        Notification::create([
            'ride_id' => $ride->id,
            'posted_by' => $user->id,
            'message' => $notificationBody,
            'status' => 'upcoming',
            'notification_type' => 'upcoming',
            'ride_detail_id' => $ride->detail->id,
            'departure' => $ride->detail->departure,
            'destination' => $ride->detail->destination,
        ]);

        $this->sendFCM($notificationBody, $user);
    }
}
