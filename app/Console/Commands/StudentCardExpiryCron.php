<?php

namespace App\Console\Commands;

use App\Mail\StudentCardExpiredMail;
use App\Mail\StudentCardExpiringMail;
use App\Mail\StudentCardExpiringReminderMail;
use App\Models\FCMToken;
use App\Models\Notification;
use Illuminate\Console\Command;
use App\Models\User;
use App\Services\FCMService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class StudentCardExpiryCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'student-card-expiry:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'student-card-expiry';

    /**
     * Execute the console command.
     *
     * @return int
     */
    
    public function handle()
    {
        $today = Carbon::today();
        
        // About to expire (1st of month)
        if ($today->day === 1) {
            $this->sendNotifications(
                $today->month,
                $today->year,
                StudentCardExpiringMail::class,
                'Your student card is about to expire'
            );
        }

        // Reminder (23rd of month)
        if ($today->day === 23) {
            $this->sendNotifications(
                $today->month,
                $today->year,
                StudentCardExpiringReminderMail::class,
                'Reminder - Your student card is about to expire'
            );
        }

        // Expired (last day of month)
        if ($today->isLastOfMonth()) {
            $this->sendNotifications(
                $today->month,
                $today->year,
                StudentCardExpiredMail::class,
                'Your student card has expired'
            );
        }

        Log::info("Student card expiry notifications processed");
    }

    protected function sendNotifications($month, $year, $mailClass, $notificationMessage)
    {

            User::where('student', 1)
            ->whereMonth('student_card_exp_date', $month)
            ->whereYear('student_card_exp_date', $year)
            ->each(function ($user) use ($mailClass, $notificationMessage) {
                if (isset($user->email_notification) && $user->email_notification == 1) {
                Mail::to($user->email)->queue(
                    new $mailClass(['first_name' => $user->first_name])
                );
            }
                 // Create database notification
                $notification = Notification::create([
                    'category' => 'system',
                    'type' => null, 
                    'receiver_id' => $user->id,
                    'posted_by' => $user->id, 
                    'message' => $notificationMessage,
                    'status' => 'completed',
                    'notification_type' => 'student_card'
                ]);
                
                // Send push notification
                $fcmService = new FCMService();
                $fcm_tokens = FCMToken::where('user_id', $user->id)->get();
                $body = $notification->message;

                $fcmToken = $user->mobile_fcm_token;
                if ($fcmToken) {
                    $fcmService->sendNotification($fcmToken, $body);
                }

                foreach ($fcm_tokens as $fcm_token) {
                    try {
                        $fcmService->sendNotification($fcm_token->token, $body);
                    } catch (\Exception $e) {
                        Log::error("FCM Notification failed for token: {$fcm_token->token}, Error: " . $e->getMessage());
                    }
                }
            });
    }
}
