<?php

namespace App\Jobs;

use App\Mail\NewVehicleAddedMail;
use App\Models\FCMToken;
use App\Models\Notification;
use App\Models\User;
use App\Services\FCMService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotifyVehicleAddedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $userId)
    {
    }

    public function handle(): void
    {
        $user = User::find($this->userId);

        if (!$user) {
            return;
        }

        if ((int) $user->email_notification === 1) {
            $emailData = [
                'first_name' => $user->first_name,
            ];

            Mail::to($user->email)->queue(new NewVehicleAddedMail($emailData));
        }

        $notification = Notification::create([
            'type' => null,
            'category' => 'system',
            'receiver_id' => $user->id,
            'posted_by' => $user->id,
            'message' => getNotificationMessageText(
                'vehicle_added_to_profile',
                $user,
                [],
                'A new vehicle added to your profile'
            ),
            'status' => 'completed',
            'notification_type' => 'vehicle',
        ]);

        $body = $notification->message;
        $fcmService = new FCMService();
        $fcmTokens = FCMToken::where('user_id', $user->id)->get();

        if ($user->mobile_fcm_token) {
            try {
                $fcmService->sendNotification($user->mobile_fcm_token, $body);
            } catch (\Exception $e) {
                Log::error("FCM Notification failed for mobile_fcm_token: {$user->mobile_fcm_token}, Error: {$e->getMessage()}");
            }
        }

        foreach ($fcmTokens as $fcmToken) {
            try {
                $fcmService->sendNotification($fcmToken->token, $body);
            } catch (\Exception $e) {
                Log::error("FCM Notification failed for token: {$fcmToken->token}, Error: {$e->getMessage()}");
            }
        }
    }
}
