<?php

namespace App\Console\Commands;

use App\Mail\BirthdayWishMail;
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

class UserBirthdayWishCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user-birthday-wish:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'user-birthday-wish';

    /**
     * Execute the console command.
     *
     * @return int
     */


  
    public function handle()
    {
        $today = Carbon::today();

        User::whereNull('deleted_at')
            ->where('email_notification', 1)
            ->chunk(100, function ($users) use ($today) {
                foreach ($users as $user) {
                    try {
                        if (empty($user->dob)) {
                            continue;
                        }

                        $dob = Carbon::parse($user->dob);

                        if ($dob->month == $today->month && $dob->day == $today->day) {
                            Mail::to($user->email)->queue(
                                new BirthdayWishMail(['first_name' => $user->first_name])
                            );
                        }
                    } catch (\Exception $e) {
                        Log::error("Failed to send birthday email to {$user->email}: " . $e->getMessage());
                    }
                }
            });

        User::whereNull('deleted_at')
            ->chunk(100, function ($users) use ($today) {
                foreach ($users as $user) {
                    try {
                        if (empty($user->dob)) {
                            continue;
                        }

                        $dob = Carbon::parse($user->dob);

                        if ($dob->month == $today->month && $dob->day == $today->day) {
                            $notification = Notification::create([
                                'category' => 'system',
                                'type' => null,
                                'receiver_id' => $user->id,
                                'posted_by' => $user->id,
                                'message' =>  'Happy Birthday to our BEST member!',
                                'status' => 'birthday',
                                'notification_type' => 'birthday',
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
                        }
                    } catch (\Exception $e) {
                        Log::error("Failed to send birthday email to {$user->email}: " . $e->getMessage());
                    }
                }
            });

        return 0;
    }
}
