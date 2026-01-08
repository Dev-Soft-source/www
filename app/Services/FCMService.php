<?php

namespace App\Services;

use App\Models\FCMToken;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Illuminate\Support\Facades\Log;
use Exception;

class FCMService
{
    protected $messaging;

    public function __construct()
    {
        try {
            $factory = (new Factory)->withServiceAccount(config('firebase.credentials'));
            $this->messaging = $factory->createMessaging();
        } catch (Exception $e) {
            Log::error('Firebase initialization failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Send notification using FCM HTTP v1 API
     *
     * @param string $userFcmToken Device FCM token
     * @param string $body Notification body text
     * @param string|null $type Notification type (chat, notify, etc.)
     * @param mixed $chatId Chat ID if type is chat
     * @param string $title Notification title
     * @return array Response from FCM
     */
    public function sendNotification($userFcmToken, $body, $type = null, $chatId = null, $title = '', array $extraData = [])
    {
        try {
            // Prepare notification data
            $data = [
                'type' => $type ?? 'notify',
            ];

            // Add chat-specific data if applicable
            if ($type === 'chat' && $chatId !== null) {
                $data['chatId'] = (string) $chatId;
                $data['notification_type'] = 'chat received';
            }

            // Merge any extra application-specific data
            foreach ($extraData as $key => $value) {
                // FCM expects strings in data payload
                $data[$key] = is_bool($value) ? ($value ? '1' : '0') : (string) $value;
            }

            // Build the message
            $messageBuilder = CloudMessage::withTarget('token', $userFcmToken);

            // Add notification payload (for display notifications)
            if (!empty($body) || !empty($title)) {
                $messageBuilder = $messageBuilder->withNotification(
                    Notification::create($title, $body)
                );
            }

            // Add data payload (for app logic)
            $messageBuilder = $messageBuilder->withData($data);

            $message = $messageBuilder;

            // Log the notification details
            Log::info('FCM Notification Sending', [
                'token' => substr($userFcmToken, 0, 20) . '...',
                'title' => $title,
                'body' => $body,
                'data' => $data
            ]);

            // Send the message
            $response = $this->messaging->send($message);

            Log::info('FCM Notification Sent Successfully', ['response' => $response]);

            return [
                'success' => true,
                'message_id' => $response
            ];
        } catch (Exception $e) {
            Log::error('FCM Notification Failed', [
                'error' => $e->getMessage(),
                'token' => substr($userFcmToken, 0, 20) . '...',
                'body' => $body
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Send notification to multiple tokens
     *
     * @param array $tokens Array of FCM tokens
     * @param string $body Notification body
     * @param string|null $type Notification type
     * @param mixed $chatId Chat ID if applicable
     * @param string $title Notification title
     * @return array Results for each token
     */
    public function sendMultipleNotifications($tokens, $body, $type = null, $chatId = null, $title = '', array $extraData = [])
    {
        $results = [];
        
        foreach ($tokens as $token) {
            $results[] = $this->sendNotification($token, $body, $type, $chatId, $title, $extraData);
        }

        return $results;
    }
}
