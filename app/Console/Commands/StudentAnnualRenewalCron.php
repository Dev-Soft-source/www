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

        // Find students whose student card upload anniversary is today
        // Check if today matches the month and day of when they first uploaded their student card
        // This sends emails every year starting from the first upload date, regardless of expiry
        User::where(function ($query) {
                $query->where('student', 1)
                      ->orWhere('student', 2); // Include both verified (2) and pending (1) students
            })
            ->whereNotNull('student_card_upload')
            ->where('student_card_upload', '!=', '')
            ->whereRaw('MONTH(student_card_upload) = ?', [$today->month])
            ->whereRaw('DAY(student_card_upload) = ?', [$today->day])
            ->whereRaw('YEAR(student_card_upload) < ?', [$today->year]) // Only users who uploaded at least a year ago
            ->each(function ($user) use ($today) {
                // Check if we already sent an annual renewal email this year
                $lastRenewalNotification = Notification::where('receiver_id', $user->id)
                    ->where('notification_type', 'student_renewal')
                    ->whereYear('created_at', $today->year)
                    ->first();

                // Only send if we haven't sent one this year
                if (!$lastRenewalNotification) {
                    $this->sendAnnualRenewalEmail($user, false);
                }
            });

        // Send reminders to students who haven't responded
        // Check for students who received annual renewal emails but haven't updated their card
        $this->sendReminders($today);

        Log::info("Student annual renewal notifications processed");
    }

    /**
     * Send annual renewal email to a student
     */
    protected function sendAnnualRenewalEmail($user, $isReminder = false)
    {
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
            'message' => $isReminder 
                ? 'Reminder: Please confirm your student status' 
                : 'Annual reminder: Please confirm your student status',
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

        $emailType = $isReminder ? 'reminder' : 'annual renewal';
        Log::info("Student {$emailType} email sent to: {$user->email}");
    }

    /**
     * Send reminders to students who haven't responded to annual renewal emails
     */
    protected function sendReminders($today)
    {
        // Find students who received annual renewal notifications but haven't updated their card since
        // Send reminders 30 days, 60 days, and 90 days after the initial email
        $reminderDays = [30, 60, 90];

        foreach ($reminderDays as $days) {
            $reminderDate = $today->copy()->subDays($days);
            
            // Find students who received annual renewal email on the reminder date
            $notifications = Notification::where('notification_type', 'student_renewal')
                ->whereDate('created_at', $reminderDate->toDateString())
                ->where('message', 'NOT LIKE', '%Reminder%') // Only original emails, not reminders
                ->get();

            foreach ($notifications as $notification) {
                $user = User::find($notification->receiver_id);
                
                if (!$user) continue;

                // Check if user has updated their student card since the notification was sent
                $cardUpdatedAfterNotification = false;
                if ($user->student_card_upload) {
                    $cardUploadDate = Carbon::parse($user->student_card_upload);
                    $notificationDate = Carbon::parse($notification->created_at);
                    // If card was updated after the notification, they've responded
                    $cardUpdatedAfterNotification = $cardUploadDate->greaterThan($notificationDate);
                }

                // Check if we already sent a reminder for this notification
                $reminderSent = Notification::where('receiver_id', $user->id)
                    ->where('notification_type', 'student_renewal')
                    ->where('message', 'LIKE', '%Reminder%')
                    ->whereDate('created_at', $today->toDateString())
                    ->exists();

                // Send reminder if:
                // 1. User hasn't updated their card since the notification
                // 2. We haven't sent a reminder today
                // 3. User is still a student
                if (!$cardUpdatedAfterNotification && !$reminderSent && ($user->student == 1 || $user->student == 2)) {
                    $this->sendAnnualRenewalEmail($user, true);
                }
            }
        }
    }
}
