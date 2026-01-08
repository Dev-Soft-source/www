<?php

namespace App\Console\Commands;

use App\Mail\StudentAnnualRenewalMail;
use App\Models\FCMToken;
use App\Models\Notification;
use Illuminate\Console\Command;
use App\Models\User;
use App\Services\FCMService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class StudentAnnualRenewalCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'student-annual-renewal:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send annual reminder to students to confirm their student status';

    /**
     * Execute the console command.
     *
     * @return int
     */

    public function handle()
    {
        $today = Carbon::today();

        // Find students whose registration anniversary is today
        // Check if today matches the month and day of when they became a student
        User::where('student', 1)
            ->whereNotNull('student_card_exp_date')
            ->where('student_card_exp_date', '!=', '')
            ->whereRaw('MONTH(created_at) = ?', [$today->month])
            ->whereRaw('DAY(created_at) = ?', [$today->day])
            ->whereRaw('YEAR(created_at) < ?', [$today->year]) // Only users who registered at least a year ago
            ->each(function ($user) {
                // Send email notification
                if (isset($user->email_notification) && $user->email_notification == 1) {
                    Mail::to($user->email)->queue(
                        new StudentAnnualRenewalMail(['first_name' => $user->first_name])
                    );
                }

                // Create database notification
                $notification = Notification::create([
                    'category' => 'system',
                    'type' => null,
                    'receiver_id' => $user->id,
                    'posted_by' => $user->id,
                    'message' => 'Annual reminder: Please confirm your student status',
                    'status' => 'completed',
                    'notification_type' => 'student_renewal'
                ]);

                // Send push notification
                $fcmService = new FCMService();
                $fcm_tokens = FCMToken::where('user_id', $user->id)->get();
                $body = $notification->message;

                $fcmToken = $user->mobile_fcm_token;
                if ($fcmToken) {
                    try {
                        $fcmService->sendNotification($fcmToken, $body);
                    } catch (\Exception $e) {
                        Log::error("FCM Notification failed for mobile token, Error: " . $e->getMessage());
                    }
                }

                foreach ($fcm_tokens as $fcm_token) {
                    try {
                        $fcmService->sendNotification($fcm_token->token, $body);
                    } catch (\Exception $e) {
                        Log::error("FCM Notification failed for token: {$fcm_token->token}, Error: " . $e->getMessage());
                    }
                }

                Log::info("Annual renewal reminder sent to student: {$user->email}");
            });

        Log::info("Student annual renewal notifications processed");
    }
}
