<?php

namespace App\Jobs;

use App\Mail\CancelPassengerAdminMail;
use App\Mail\CancelPassengerMail;
use App\Models\Admin;
use App\Models\Booking;
use App\Models\FCMToken;
use App\Models\Notification;
use App\Models\PhoneNumber;
use App\Models\Ride;
use App\Models\User;
use App\Services\FCMService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Twilio\Rest\Client;

/**
 * Queued side effects for "driver removed passenger" (booking cancelled by driver for one passenger).
 *
 * Handles:
 * - In-app notification + thread message.
 * - Passenger + admin emails.
 * - Passenger FCM + SMS.
 * - Optional same-day passenger-list SMS to the driver (within 1 hour).
 */
class NotifyPassengerRemovedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $bookingId,
        public int $driverUserId,
        public string $adminMessage,
        public string $passengerMessage
    ) {
    }

    public function handle(): void
    {
        $booking = Booking::with(['ride.driver', 'passenger'])->find($this->bookingId);
        $driver = User::find($this->driverUserId);

        if (!$booking || !$booking->passenger || !$booking->ride || !$booking->ride->driver || !$driver) {
            return;
        }

        $ride = $booking->ride;
        $passenger = $booking->passenger;

        // In-app notification
        $notification = Notification::create([
            'type' => 2,
            'ride_id' => $booking->ride_id,
            'posted_to' => $booking->id,
            'posted_by' => $ride->added_by,
            'message' => getNotificationMessageText(
                'driver_cancelled_your_booking',
                $passenger,
                [],
                'Driver cancelled your booking'
            ),
            'status' => 'cancelled',
            'notification_type' => 'upcoming',
            'ride_detail_id' => $booking->ride_detail_id,
            'departure' => $booking->departure,
            'destination' => $booking->destination,
        ]);

        // Thread message to passenger from driver
        \App\Models\Message::create([
            'ride_id' => $booking->ride_id,
            'receiver' => $booking->user_id,
            'sender' => $ride->added_by,
            'message' => $this->adminMessage,
            'ride_detail_id' => $booking->ride_detail_id ?: null,
        ]);

        $body = $notification->message;

        // FCM to passenger
        $user = User::find($booking->user_id);
        if ($user && $user->mobile_fcm_token) {
            $fcmService = new FCMService();
            $fcmService->sendNotification($user->mobile_fcm_token, $body);
        }

        // Email to passenger
        if (isset($passenger->email_notification) && (int) $passenger->email_notification === 1) {
            $data = [
                'passenger_name' => $passenger->first_name,
                'driver_name' => $ride->driver->first_name,
                'message' => $this->passengerMessage,
                'from' => $booking->departure,
                'to' => $booking->destination,
                'date' => Carbon::parse($ride->date)->format('F d, Y'),
                'time' => $ride->time,
                'seats' => $booking->seats,
                'total_price' => $booking->fare,
            ];
            Mail::to($passenger->email)->queue(new CancelPassengerMail($data));
        }

        // Email to admin
        $admin = Admin::first();
        if ($admin) {
            $data = [
                'admin_username' => $admin->username,
                'driver_name' => $ride->driver->first_name,
                'passenger_name' => $passenger->first_name,
                'departure' => $booking->departure,
                'destination' => $booking->destination,
                'date' => $ride->date,
                'message' => $this->adminMessage,
            ];
            Mail::to($admin->admin_email)->queue(new CancelPassengerAdminMail($data));
        }

        // SMS to passenger
        $phoneNumber = PhoneNumber::where('user_id', $booking->user_id)
            ->where('verified', '1')
            ->where('default', '1')
            ->first();

        if (!$phoneNumber) {
            $phoneNumber = PhoneNumber::where('user_id', $booking->user_id)
                ->where('verified', '1')
                ->first();
        }

        if ($phoneNumber && env('APP_ENV') !== 'local' && isset($passenger->sms_notification) && (int) $passenger->sms_notification === 1) {
            $sid = env('TWILIO_ACCOUNT_SID');
            $token = env('TWILIO_AUTH_TOKEN');
            $from = env('TWILIO_PHONE_NUMBER');

            $twilio = new Client($sid, $token);
            $to = $phoneNumber->phone;

            $title = '';
            $currentHour = (int) date('H');
            if ($currentHour >= 0 && $currentHour < 12) {
                $title = "Good morning " . $passenger->first_name . "";
            } elseif ($currentHour >= 12 && $currentHour < 17) {
                $title = "Good afternoon " . $passenger->first_name . "";
            } else {
                $title = "Good evening " . $passenger->first_name . "";
            }

            $depatureDate = date('d F, Y H:i:s', strtotime($ride->date . ' ' . $ride->time));

            $sms = $title . "\nDriver remove your seat from this ride\nTrip detail\nOrigin: " . $booking->departure .
                "\nDestination: " . $booking->destination .
                "\nDeparture date: " . $depatureDate .
                "\nDriver name: " . $ride->driver->first_name .
                "\nDriver phone number: " . $ride->driver->phone;

            try {
                $twilio->messages->create($to, [
                    'from' => $from,
                    'body' => $sms,
                ]);
            } catch (\Exception $e) {
                Log::info('can not send text to ' . $to . ' and message is ' . $sms . ' because ' . $e->getMessage());
            }
        }

        // Optional: same-day passenger list SMS to driver when within 1 hour
        $ride_time = strtotime($ride->time);
        $current_time = time();
        $current_date = date('Y-m-d');
        $time_left = $ride_time - $current_time;

        if ($current_date === date('Y-m-d', strtotime($ride->date)) && $time_left <= 3600) {
            $getBookings = Booking::with('passenger')
                ->where('ride_id', $ride->id)
                ->whereNotIn('status', [0, 3, 4])
                ->get();

            if ($getBookings->isNotEmpty()) {
                $messageContent = '';
                foreach ($getBookings as $b) {
                    if ($messageContent === '') {
                        $messageContent = $b->passenger->first_name . '(' . $b->passenger->phone . ')';
                    } else {
                        $messageContent .= "\n" . $b->passenger->first_name . '(' . $b->passenger->phone . ')';
                    }
                }

                $phoneNumber = PhoneNumber::where('user_id', $ride->added_by)
                    ->where('verified', '1')
                    ->where('default', '1')
                    ->first();

                if (!$phoneNumber) {
                    $phoneNumber = PhoneNumber::where('user_id', $ride->added_by)
                        ->where('verified', '1')
                        ->first();
                }

                if ($phoneNumber && env('APP_ENV') !== 'local') {
                    $sid = env('TWILIO_ACCOUNT_SID');
                    $token = env('TWILIO_AUTH_TOKEN');
                    $from = env('TWILIO_PHONE_NUMBER');

                    $twilio = new Client($sid, $token);
                    $to = $phoneNumber->phone;

                    $title = '';
                    $currentHour = (int) date('H');
                    if ($currentHour >= 0 && $currentHour < 12) {
                        $title = "Good morning " . $ride->driver->first_name . "";
                    } elseif ($currentHour >= 12 && $currentHour < 17) {
                        $title = "Good afternoon " . $ride->driver->first_name . "";
                    } else {
                        $title = "Good evening " . $ride->driver->first_name . "";
                    }

                    $depatureDate = date('d F, Y H:i:s', strtotime($ride->date . ' ' . $ride->time));

                    $sms = $title . "\nTrip detail\nOrigin: " . $booking->departure .
                        "\nDestination: " . $booking->destination .
                        "\nDeparture date: " . $depatureDate .
                        "\nHere is your passengers’ list\n" . $messageContent;

                    try {
                        $twilio->messages->create($to, [
                            'from' => $from,
                            'body' => $sms,
                        ]);
                    } catch (\Exception $e) {
                        Log::info('can not send text to ' . $to . ' and message is ' . $sms . ' because ' . $e->getMessage());
                    }
                }
            }
        }
    }
}

