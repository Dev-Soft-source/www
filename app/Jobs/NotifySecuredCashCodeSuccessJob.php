<?php

namespace App\Jobs;

use App\Mail\SecuredCashDriverMail;
use App\Mail\SecuredCashPassengerMail;
use App\Models\Booking;
use App\Models\FCMToken;
use App\Models\Notification;
use App\Models\PhoneNumber;
use App\Services\FCMService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Twilio\Rest\Client;

/**
 * Queued side effects after a successful secured-cash code (driver + passenger notifications).
 *
 * In-app notifications, FCM (mobile + stored tokens), email, and SMS (when enabled).
 */
class NotifySecuredCashCodeSuccessJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $bookingId
    ) {
    }

    public function handle(): void
    {
        $booking = Booking::with(['ride.driver', 'passenger'])->find($this->bookingId);

        if (!$booking || !$booking->ride || !$booking->ride->driver || !$booking->passenger) {
            return;
        }

        $ride = $booking->ride;
        $driver = $ride->driver;
        $passenger = $booking->passenger;

        $driverPhoneNumber = PhoneNumber::where('user_id', $driver->id)
            ->where('default', '1')
            ->first();
        $driverPhoneToUse = $driverPhoneNumber ? $driverPhoneNumber->phone : $driver->phone;

        $passengerPhoneNumberDefault = PhoneNumber::where('user_id', $passenger->id)
            ->where('default', '1')
            ->first();
        $passengerPhoneToUse = $passengerPhoneNumberDefault ? $passengerPhoneNumberDefault->phone : $passenger->phone;

        $passengerData = [
            'passenger_first_name' => $passenger->first_name,
            'seats_booked' => $booking->seats,
            'booking_price' => $booking->price,
            'from' => $booking->departure,
            'to' => $booking->destination,
            'on' => $ride->date,
            'at' => $ride->time,
            'driver_first_name' => $driver->first_name,
            'driver_phone' => $driverPhoneToUse,
            'passenger_email' => $driver->email,
        ];

        if (isset($passenger->email_notification) && (int) $passenger->email_notification === 1) {
            Mail::to($passenger->email)->send(new SecuredCashPassengerMail($passengerData));
        }

        $driverData = [
            'driver_first_name' => $driver->first_name,
            'seats_booked' => $booking->seats,
            'booking_price' => $booking->price,
            'from' => $booking->departure,
            'to' => $booking->destination,
            'on' => $ride->date,
            'at' => $ride->time,
            'passenger_first_name' => $passenger->first_name,
            'passenger_phone' => $passengerPhoneToUse,
            'passenger_email' => $passenger->email,
        ];

        if (isset($driver->email_notification) && (int) $driver->email_notification === 1) {
            Mail::to($driver->email)->send(new SecuredCashDriverMail($driverData));
        }

        $this->sendPassengerSms($booking, $ride, $driver, $passenger, $driverPhoneToUse);
        $this->sendDriverSms($booking, $ride, $driver, $passenger, $passengerPhoneToUse);

        $driverMessage = getNotificationMessageText(
            'secured_cash_payment_code_successful',
            $driver,
            [],
            'Secured-cash payment code successful'
        );

        $notificationDriver = Notification::create([
            'ride_id' => $booking->ride_id,
            'posted_by' => $booking->user_id,
            'message' => $driverMessage,
            'status' => 'upcoming',
            'notification_type' => 'upcoming',
            'ride_detail_id' => $booking->ride_detail_id,
            'departure' => $booking->departure,
            'destination' => $booking->destination,
        ]);

        $fcmService = new FCMService();
        $bodyDriver = $notificationDriver->message;

        if ($driver->mobile_fcm_token) {
            try {
                $fcmService->sendNotification($driver->mobile_fcm_token, $bodyDriver);
            } catch (\Exception $e) {
                Log::error('FCM secured cash driver mobile failed: ' . $e->getMessage());
            }
        }

        foreach (FCMToken::where('user_id', $ride->added_by)->get() as $fcmToken) {
            try {
                $fcmService->sendNotification($fcmToken->token, $bodyDriver);
            } catch (\Exception $e) {
                Log::error('FCM secured cash driver token failed: ' . $e->getMessage());
            }
        }

        $passengerMessage = getNotificationMessageText(
            'secured_cash_payment_code_successful',
            $passenger,
            [],
            'Secured-cash payment code successful'
        );

        $notificationPassenger = Notification::create([
            'type' => 2,
            'ride_id' => $booking->ride_id,
            'posted_to' => $booking->id,
            'posted_by' => $ride->added_by,
            'message' => $passengerMessage,
            'status' => 'upcoming',
            'notification_type' => 'upcoming',
            'ride_detail_id' => $booking->ride_detail_id,
            'departure' => $booking->departure,
            'destination' => $booking->destination,
        ]);

        $bodyPassenger = $notificationPassenger->message;

        if ($passenger->mobile_fcm_token) {
            try {
                $fcmService->sendNotification($passenger->mobile_fcm_token, $bodyPassenger);
            } catch (\Exception $e) {
                Log::error('FCM secured cash passenger mobile failed: ' . $e->getMessage());
            }
        }

        foreach (FCMToken::where('user_id', $booking->user_id)->get() as $fcmToken) {
            try {
                $fcmService->sendNotification($fcmToken->token, $bodyPassenger);
            } catch (\Exception $e) {
                Log::error('FCM secured cash passenger token failed: ' . $e->getMessage());
            }
        }
    }

    private function sendPassengerSms(
        Booking $booking,
        $ride,
        $driver,
        $passenger,
        string $driverPhoneToUse
    ): void {
        $passengerPhoneNumber = PhoneNumber::where('user_id', $passenger->id)
            ->where('verified', '1')
            ->where('default', '1')
            ->first();

        if (!$passengerPhoneNumber) {
            $passengerPhoneNumber = PhoneNumber::where('user_id', $passenger->id)
                ->where('verified', '1')
                ->first();
        }

        if (
            !$passengerPhoneNumber
            || env('APP_ENV') === 'local'
            || !isset($passenger->sms_notification)
            || (int) $passenger->sms_notification !== 1
        ) {
            return;
        }

        $sid = env('TWILIO_ACCOUNT_SID');
        $token = env('TWILIO_AUTH_TOKEN');
        $from = env('TWILIO_PHONE_NUMBER');
        $twilio = new Client($sid, $token);
        $to = $passengerPhoneNumber->phone;

        $title = '';
        $currentHour = (int) date('H');
        if ($currentHour >= 0 && $currentHour < 12) {
            $title = 'Good morning ' . $passenger->first_name . ',';
        } elseif ($currentHour >= 12 && $currentHour < 17) {
            $title = 'Good afternoon ' . $passenger->first_name . ',';
        } else {
            $title = 'Good evening ' . $passenger->first_name . ',';
        }

        $driverPhone = preg_replace("/^(\d{3})(\d{3})(\d{4})$/", '($1)$2-$3', $driverPhoneToUse);
        $departureTime = date('H:i:s', strtotime($ride->time));
        $departureDate = date('d F, Y', strtotime($ride->date));
        $seatText = $booking->seats == 1 ? 'seat' : 'seats';
        $totalAmount = ($booking->seats * $booking->price) + $booking->booking_credit;
        $formattedAmountForPassengerToPay = number_format($totalAmount, 2);

        $smsBody = $title . "\n" . 'From ProximaRide: Secured-cash payment code was successful. Your booking price has been refunded to you. Now, please pay your driver in cash. Pay the booking price only, not the booking fee.' . "\n" .
            'Ride from ' . $booking->departure . ' to ' . $booking->destination .
            ' on ' . $departureDate . ' at ' . $departureTime . "\n" .
            'Driver name is (' . $driver->first_name . '). Phone ' . $driverPhone . "\n" .
            'You booked: ' . $booking->seats . ' ' . $seatText . "\n" .
            'Amount to pay to the driver: $' . $formattedAmountForPassengerToPay;

        try {
            $twilio->messages->create($to, [
                'from' => $from,
                'body' => $smsBody,
            ]);
        } catch (\Exception $e) {
            Log::error('Cannot send secured cash success SMS to ' . $to . '. Error: ' . $e->getMessage());
        }
    }

    private function sendDriverSms(
        Booking $booking,
        $ride,
        $driver,
        $passenger,
        string $passengerPhoneToUse
    ): void {
        $driverPhoneNumber = PhoneNumber::where('user_id', $driver->id)
            ->where('verified', '1')
            ->where('default', '1')
            ->first();

        if (!$driverPhoneNumber) {
            $driverPhoneNumber = PhoneNumber::where('user_id', $driver->id)
                ->where('verified', '1')
                ->first();
        }

        if (
            !$driverPhoneNumber
            || env('APP_ENV') === 'local'
            || !isset($driver->sms_notification)
            || (int) $driver->sms_notification !== 1
        ) {
            return;
        }

        $sid = env('TWILIO_ACCOUNT_SID');
        $token = env('TWILIO_AUTH_TOKEN');
        $from = env('TWILIO_PHONE_NUMBER');
        $twilio = new Client($sid, $token);
        $to = $driverPhoneNumber->phone;

        $title = '';
        $currentHour = (int) date('H');
        if ($currentHour >= 0 && $currentHour < 12) {
            $title = 'Good morning ' . $driver->first_name . ',';
        } elseif ($currentHour >= 12 && $currentHour < 17) {
            $title = 'Good afternoon ' . $driver->first_name . ',';
        } else {
            $title = 'Good evening ' . $driver->first_name . ',';
        }

        $passengerPhone = preg_replace("/^(\d{3})(\d{3})(\d{4})$/", '($1)$2-$3', $passengerPhoneToUse);
        $departureTime = date('H:i:s', strtotime($ride->time));
        $departureDate = date('d F, Y', strtotime($ride->date));
        $totalAmount = ($booking->seats * $booking->price) + $booking->booking_credit;
        $formattedAmount = number_format($totalAmount, 2);

        $smsBody = $title . "\n" . 'From ProximaRide: Secured-cash payment code was successful. Now, take your payment from the passenger in cash.' . "\n" .
            'Passenger name is (' . $passenger->first_name . '). Phone ' . $passengerPhone . "\n" .
            'Ride from ' . $booking->departure . ' to ' . $booking->destination .
            ' on ' . $departureDate . ' at ' . $departureTime . "\n" .
            'Seats booked: ' . $booking->seats . "\n" .
            'Amount due to you: $' . $formattedAmount;

        try {
            $twilio->messages->create($to, [
                'from' => $from,
                'body' => $smsBody,
            ]);
        } catch (\Exception $e) {
            Log::error('Cannot send secured cash success SMS to driver ' . $to . '. Error: ' . $e->getMessage());
        }
    }
}
