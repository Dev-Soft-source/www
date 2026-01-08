<?php

namespace App\Http\Middleware;

use App\Mail\BookingExpiredMail;
use Twilio\Rest\Client;
use App\Mail\RejectBookingRequestMail;
use App\Models\Booking;
use App\Models\FCMToken;
use App\Models\Notification;
use App\Models\PhoneNumber;
use App\Models\Rating;
use App\Models\User;
use App\Services\FCMService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ExpiredBookingsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Booking::where('expires_at', '<', now())->delete();
        $bookings = Booking::where('expires_at', '<', now())->get();
        foreach ($bookings as $booking) {
            $notification = Notification::create([
                'type' => 2,
                'ride_id' => $booking->ride_id,
                'posted_to' => $booking->id,
                'posted_by' => $booking->ride->added_by,
                'message' =>  'Booking request expired',
                'status' => 'reject',
                'notification_type' => 'upcoming',
                'ride_detail_id' => $booking->ride_detail_id,
                'departure' => $booking->departure,
                'destination' => $booking->destination
            ]);
    
            $user = User::whereId($booking->user_id)->first();
            // Assuming $user and $fcmToken are defined
            $fcmToken = $user->mobile_fcm_token;
            $body = $notification->message;
    
            $fcmService = new FCMService();
            if ($fcmToken) {
                // Send the booking notification
                $fcmService->sendNotification($fcmToken, $body);
            }

            // Assuming $user and $fcmToken are defined
            $fcm_tokens = FCMToken::where('user_id', $booking->user_id)->get();
            $body = $notification->message;

            foreach ($fcm_tokens as $fcm_token) {
                try {
                    $fcmService->sendNotification($fcm_token->token, $body);
                } catch (\Exception $e) {
                    Log::error("FCM Notification failed for token: $fcm_token, Error: " . $e->getMessage());
                }
            }
            
            if (isset($booking->passenger->email_notification) && $booking->passenger->email_notification == 1) {
                $data = ['first_name' => $booking->passenger->first_name, 'seats' => $booking->seats, 'price' => $booking->fare, 'from' => $booking->departure, 'to' => $booking->destination, 'date' => $booking->ride->date, 'time' => $booking->ride->time];
                Mail::to($booking->passenger->email)->queue(new BookingExpiredMail($data));
                $booking->delete();
            }

            $phoneNumber = PhoneNumber::where('user_id', $booking->user_id)->where('verified', '1')->where('default', '1')->first();

            if (!$phoneNumber) {
                $phoneNumber = PhoneNumber::where('user_id', $booking->user_id)->where('verified', '1')->first();
            }

            if ($phoneNumber && env('APP_ENV') != 'local' && isset($booking->passenger->sms_notification) && $booking->passenger->sms_notification == 1) {
                $sid = env('TWILIO_ACCOUNT_SID');
                $token = env('TWILIO_AUTH_TOKEN');
                $from = env('TWILIO_PHONE_NUMBER');

                $title = "";
                $currentHour = date('H');
                if ($currentHour >= 0 && $currentHour < 12) {
                    $title = "Good morning " . $booking->passenger->first_name . ",";
                } elseif ($currentHour >= 12 && $currentHour < 17) {
                    $title = "Good afternoon " . $booking->passenger->first_name . ",";
                } else {
                    $title = "Good evening " . $booking->passenger->first_name . ",";
                }

                $twilio = new Client($sid, $token);
                $to = $phoneNumber->phone;

                $departureTime = date('H:i:s', strtotime($booking->ride->time));
                $departureDate = date('d F, Y', strtotime($booking->ride->date));

                $message = $title . "\n" . "From ProximaRide: We are sorry the driver did not respond to your booking request and it has now expired.\nRide from " . $booking->departure . " to " . $booking->destination . " on " . $departureDate . " at " . $departureTime . "\nAll payments that you have made to book on this ride will be refunded to you immediately";

                try {
                    $res = $twilio->messages->create(
                        $to,
                        [
                            'from' => $from,
                            'body' => $message,
                        ]
                    );
                } catch (\Exception  $e) {
                    Log::info('Cannot send text to ' . $to . ' and message is ' . $message . ' because ' . $e->getMessage());
                }
            }       
        }
        Rating::where('live_limit', '<', now())->update([
            'status' => 1,
            'live_limit' => null,
        ]);
        Rating::where('reply_deadline', '<', now())->update([
            'reply_deadline' => null,
        ]);
        return $next($request);
    }
}
