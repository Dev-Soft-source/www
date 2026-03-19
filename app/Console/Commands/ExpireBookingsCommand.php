<?php

namespace App\Console\Commands;

use App\Mail\BookingExpiredMail;
use App\Models\Booking;
use App\Models\FCMToken;
use App\Models\Notification;
use App\Models\PhoneNumber;
use App\Models\Rating;
use App\Models\User;
use App\Services\FCMService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Twilio\Rest\Client;

class ExpireBookingsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process expired bookings and send notifications';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting expired bookings processing...');

        try {
            // Get expired bookings
            $bookings = Booking::where('expires_at', '<', now())
                ->with(['passenger', 'ride'])
                ->get();

            $processedCount = 0;
            $errorCount = 0;

            foreach ($bookings as $booking) {
                try {
                    $this->processExpiredBooking($booking);
                    $processedCount++;
                } catch (\Exception $e) {
                    $errorCount++;
                    Log::error("Failed to process expired booking ID {$booking->id}: " . $e->getMessage(), [
                        'booking_id' => $booking->id,
                        'exception' => $e,
                    ]);
                    $this->warn("Failed to process booking ID {$booking->id}: " . $e->getMessage());
                }
            }

            // Process rating updates
            $this->processRatingUpdates();

            $this->info("Processed {$processedCount} expired bookings. Errors: {$errorCount}");

            return Command::SUCCESS;
        } catch (\Exception $e) {
            Log::error('ExpireBookingsCommand failed: ' . $e->getMessage(), [
                'exception' => $e,
            ]);
            $this->error('Command failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Process a single expired booking
     *
     * @param Booking $booking
     * @return void
     */
    protected function processExpiredBooking(Booking $booking)
    {
        $user = User::find($booking->user_id);
        if (!$user) {
            Log::warning("User not found for booking ID {$booking->id}");
            return;
        }

        // Create notification
        $notification = Notification::create([
            'type' => 2,
            'ride_id' => $booking->ride_id,
            'posted_to' => $booking->id,
            'posted_by' => $booking->ride->added_by ?? null,
            'message' => getNotificationMessageText(
                'booking_request_expired',
                $user,
                [],
                'Booking request expired'
            ),
            'status' => 'reject',
            'notification_type' => 'upcoming',
            'ride_detail_id' => $booking->ride_detail_id,
            'departure' => $booking->departure,
            'destination' => $booking->destination
        ]);

        // Send FCM notifications
        $fcmService = new FCMService();
        $body = $notification->message;

        // Send to mobile FCM token
        if ($user->mobile_fcm_token) {
            try {
                $fcmService->sendNotification($user->mobile_fcm_token, $body);
            } catch (\Exception $e) {
                Log::error("FCM Notification failed for mobile token (booking {$booking->id}): " . $e->getMessage());
            }
        }

        // Send to all FCM tokens for this user
        $fcm_tokens = FCMToken::where('user_id', $booking->user_id)->get();
        foreach ($fcm_tokens as $fcm_token) {
            try {
                $fcmService->sendNotification($fcm_token->token, $body);
            } catch (\Exception $e) {
                Log::error("FCM Notification failed for token (booking {$booking->id}): " . $e->getMessage());
            }
        }

        // Send email notification if enabled
        if (isset($booking->passenger->email_notification) && $booking->passenger->email_notification == 1) {
            try {
                $data = [
                    'first_name' => $booking->passenger->first_name,
                    'seats' => $booking->seats,
                    'price' => $booking->fare,
                    'from' => $booking->departure,
                    'to' => $booking->destination,
                    'date' => $booking->ride->date ?? '',
                    'time' => $booking->ride->time ?? ''
                ];
                Mail::to($booking->passenger->email)->queue(new BookingExpiredMail($data));
                $booking->delete();
            } catch (\Exception $e) {
                Log::error("Failed to send email for expired booking ID {$booking->id}: " . $e->getMessage());
            }
        }

        // Send SMS notification if enabled
        if (isset($booking->passenger->sms_notification) && $booking->passenger->sms_notification == 1) {
            $this->sendSmsNotification($booking, $user);
        }
    }

    /**
     * Send SMS notification for expired booking
     *
     * @param Booking $booking
     * @param User $user
     * @return void
     */
    protected function sendSmsNotification(Booking $booking, User $user)
    {
        if (env('APP_ENV') == 'local') {
            return; // Skip SMS in local environment
        }

        $phoneNumber = PhoneNumber::where('user_id', $booking->user_id)
            ->where('verified', '1')
            ->where('default', '1')
            ->first();

        if (!$phoneNumber) {
            $phoneNumber = PhoneNumber::where('user_id', $booking->user_id)
                ->where('verified', '1')
                ->first();
        }

        if (!$phoneNumber) {
            return;
        }

        try {
            $sid = env('TWILIO_ACCOUNT_SID');
            $token = env('TWILIO_AUTH_TOKEN');
            $from = env('TWILIO_PHONE_NUMBER');

            if (!$sid || !$token || !$from) {
                Log::warning('Twilio credentials not configured');
                return;
            }

            // Determine greeting based on time of day
            $currentHour = (int) date('H');
            if ($currentHour >= 0 && $currentHour < 12) {
                $title = "Good morning " . $booking->passenger->first_name . ",";
            } elseif ($currentHour >= 12 && $currentHour < 17) {
                $title = "Good afternoon " . $booking->passenger->first_name . ",";
            } else {
                $title = "Good evening " . $booking->passenger->first_name . ",";
            }

            $twilio = new Client($sid, $token);
            $to = $phoneNumber->phone;

            $departureTime = $booking->ride->time ? date('H:i:s', strtotime($booking->ride->time)) : '';
            $departureDate = $booking->ride->date ? date('d F, Y', strtotime($booking->ride->date)) : '';

            $message = $title . "\n" . "From ProximaRide: We are sorry the driver did not respond to your booking request and it has now expired.\nRide from " . $booking->departure . " to " . $booking->destination . " on " . $departureDate . " at " . $departureTime . "\nAll payments that you have made to book on this ride will be refunded to you immediately";

            $twilio->messages->create(
                $to,
                [
                    'from' => $from,
                    'body' => $message,
                ]
            );
        } catch (\Exception $e) {
            Log::info('Cannot send text to ' . ($phoneNumber->phone ?? 'unknown') . ' for booking ' . $booking->id . ' because ' . $e->getMessage());
        }
    }

    /**
     * Process rating updates for expired deadlines
     *
     * @return void
     */
    protected function processRatingUpdates()
    {
        try {
            // Update ratings with expired live_limit
            Rating::where('live_limit', '<', now())
                ->whereNotNull('live_limit')
                ->update([
                    'status' => 1,
                    'live_limit' => null,
                ]);

            // Update ratings with expired reply_deadline
            Rating::where('reply_deadline', '<', now())
                ->whereNotNull('reply_deadline')
                ->update([
                    'reply_deadline' => null,
                ]);
        } catch (\Exception $e) {
            Log::error('Failed to process rating updates: ' . $e->getMessage());
        }
    }
}
