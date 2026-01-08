<?php

namespace App\Console\Commands;

use App\Mail\HolidaySeasonMail;
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

class HolidaySeasonCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'holiday-season:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send holidays season (Christmas and New Year) greetings to users';

    /**
     * Execute the console command.
     *
     * @return int
     */



    public function handle()
    {
        $today = Carbon::today();

        // Only run on December 20th
        if ($today->month !== 12 || $today->day !== 20) {
            Log::info("Holiday season cron skipped - not December 20");
            return 0;
        }

        // Send to all active users with email notifications enabled
        User::whereNull('deleted_at')
            ->where('email_notification', 1)
            ->chunk(100, function ($users) {
                foreach ($users as $user) {
                    try {
                        if ($user->email_notification == 1) {
                        Mail::to($user->email)->queue(
                            new HolidaySeasonMail(['first_name' => $user->first_name])
                        );
                        Log::info("Holiday email sent to: {$user->email}");
                    }
                    } catch (\Exception $e) {
                        Log::error("Failed to send holiday email to {$user->email}: " . $e->getMessage());
                    }
                }
            });
            
        User::whereNull('deleted_at')
            ->chunk(100, function ($users) {
                foreach ($users as $user) {
                    try {
                        $notification = Notification::create([
                            'category' => 'system',
                            'type' => null,
                            'receiver_id' => $user->id,
                            'posted_by' => $user->id,
                            'message' =>  'Merry Christmas and Happy New Year!',
                            'status' => 'christmas',
                            'notification_type' => 'christmas',
                        ]);
            
                        $fcmToken = $user->mobile_fcm_token;
                        $body = $notification->message;
                        $fcmService = new FCMService();
            
                        if ($fcmToken) {
                            // Send the booking notification
                            $fcmService->sendNotification($fcmToken, $body);
                        }
            
                        $fcm_tokens = FCMToken::where('user_id', $user->id)->get();
            
                        foreach ($fcm_tokens as $fcm_token) {
                            try {
                                $fcmService->sendNotification($fcm_token->token, $body);
                            } catch (\Exception $e) {
                                Log::error("FCM Notification failed for token: $fcm_token, Error: " . $e->getMessage());
                            }
                        }
                    } catch (\Exception $e) {
                        Log::error("Failed to send holiday email to {$user->email}: " . $e->getMessage());
                    }
                }
            });

        Log::info("Holiday season email campaign completed");
        return 0;
    }
}
