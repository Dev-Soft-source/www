<?php

namespace App\Console\Commands;

use App\Mail\PassengerListMail;
use App\Models\Booking;
use Twilio\Rest\Client;
use App\Models\FCMToken;
use App\Models\Notification;
use App\Models\PhoneNumber;
use App\Models\Ride;
use App\Services\FCMService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendPassengerList extends Command
{
    protected $signature = 'send-passenger-list:cron';
    protected $description = 'Send passenger list to drivers 1 hour before rides';

    public function handle()
    {
        $now = Carbon::now();
        $current_date = $now->format('Y-m-d');
        $current_time = $now->timestamp;

        $rides = Ride::with(['driver', 'bookings'])
            ->where('date', $current_date)
            // Consider rides for the current date
            ->get();

        foreach ($rides as $ride) {
            $current_time = time();
            $ride_time = strtotime($ride->time);
            $time_left = $ride_time - $current_time;
            
            $now = Carbon::now(); // current time
            $rideDateTime = Carbon::parse($ride->date . ' ' . $ride->time); // full ride datetime
            $oneHourBefore = $rideDateTime->copy()->subHour();
            
            // Check if the ride is within the next hour (less than or equal to 3600 seconds)

            if ($now->between($oneHourBefore, $oneHourBefore->copy()->addMinute())) {
                $getBookings = Booking::with('passenger')
                    ->where('ride_id', $ride->id)
                    ->whereNotIn('status', ['3', '0', '4']) // Exclude cancelled, pending, and rejected bookings
                    ->get();

                if ($getBookings->isNotEmpty()) {
                    $passengers = [];
                    foreach ($getBookings as $booking) {
                        $passengers[] = [
                            'first_name' => $booking->passenger->first_name,
                            'seats' => $booking->seats,
                        ];
                    }

                    $data = [
                        'driver_name' => $ride->driver->first_name,
                        'from' => $booking->departure,
                        'to' => $booking->destination,
                        'date' => $ride->date,
                        'time' => $ride->time,
                        'passengers' => $passengers,
                    ];

                    if ($ride->driver->email_notification == 1) {

                        Mail::to($ride->driver->email)
                            ->send(new PassengerListMail($data));
                    }

                    $notification = Notification::create([
                        'ride_id' => $ride->id,
                        'posted_by' => $ride->added_by,
                        'message' => 'Your passenger list ',
                        'status' => 'upcoming',
                        'notification_type' => 'upcoming',
                        'ride_detail_id' => $ride->rideDetail->first()->id ?? null, // Assuming RideDetail has a first() method
                        'departure' => $ride->departure,
                        'destination' => $ride->destination,
                    ]);

                    $fcmService = new FCMService();
                    $fcmToken = $ride->driver->mobile_fcm_token;
                    $body = $notification->message;

                    if ($fcmToken) {
                        $fcmService->sendNotification($fcmToken, $body);
                    }

                    $fcm_tokens = FCMToken::where('user_id', $ride->added_by)->get();
                    foreach ($fcm_tokens as $fcm_token) {
                        try {
                            $fcmService->sendNotification($fcm_token->token, $body);
                        } catch (\Exception $e) {
                            Log::error("FCM Notification failed for token: $fcm_token->token, Error: " . $e->getMessage());
                        }
                    }

                      // Send SMS if enabled
                      if (env('APP_ENV') != 'local' && isset($ride->driver->sms_notification) && $ride->driver->sms_notification == 1) {
                        $phoneNumber = PhoneNumber::where('user_id', $ride->added_by)
                            ->where('verified', '1')
                            ->orderBy('default', 'desc')
                            ->first();

                        if ($phoneNumber) {
                            $sid = env('TWILIO_ACCOUNT_SID');
                            $token = env('TWILIO_AUTH_TOKEN');
                            $from = env('TWILIO_PHONE_NUMBER');

                            $twilio = new Client($sid, $token);
                            $to = $phoneNumber->phone;

                            // Create greeting based on time of day
                            $currentHour = date('H');
                            if ($currentHour >= 0 && $currentHour < 12) {
                                $title = "Good morning " . $ride->driver->first_name . ",";
                            } elseif ($currentHour >= 12 && $currentHour < 17) {
                                $title = "Good afternoon " . $ride->driver->first_name . ",";
                            } else {
                                $title = "Good evening " . $ride->driver->first_name . ",";
                            }

                            $departureTime = date('H:i:s', strtotime($ride->time));
                            $departureDate = date('d F, Y', strtotime($ride->date));

                            // Build passenger list
                            $passengerList = "";
                            $counter = 1;

                            foreach ($getBookings as $booking) {
                                $passengerPhone = $booking->passenger->phone;
                                $formattedPhone = preg_replace("/^(\d{3})(\d{3})(\d{4})$/", "($1)$2-$3", $passengerPhone);
                                
                                $seatText = $booking->seats == 1 ? 'seat' : 'seats';
                                
                                $passengerList .= $counter . "- " . $booking->passenger->first_name . 
                                                 ". Phone " . $formattedPhone . 
                                                 ". Booked: " . $booking->seats . " " . $seatText . "\n";
                                $counter++;
                            }

                            $message = $title . "\n" . "From ProximaRide: Here is your passenger list for your ride from " . 
                            $ride->rideDetail[0]->departure . " to " . $ride->rideDetail[0]->destination . 
                            " on " . $departureDate . " at " . $departureTime . "\n" . 
                            $passengerList . "Drive safe!";

                            try {
                                $res = $twilio->messages->create(
                                    $to,
                                    [
                                        'from' => $from,
                                        'body' => $message,
                                    ]
                                );
                                Log::info('SMS sent to ' . $to . ' for ride ' . $ride->id);
                            } catch (\Exception $e) {
                                Log::error('Cannot send text to ' . $to . ' for ride ' . $ride->id . '. Error: ' . $e->getMessage());
                            }
                        }
                    }
                }
            }
        }

    }

}