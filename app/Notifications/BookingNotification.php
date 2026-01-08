<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;

class BookingNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $message;
    protected $fcmToken;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($message, $fcmToken)
    {
        $this->message = $message;
        $this->fcmToken = $fcmToken;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [FcmChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toFcm($notifiable)
    {
        return FcmMessage::create()
            ->setData(['message' => $this->message])
            ->setNotification(\NotificationChannels\Fcm\Resources\Notification::create()
                ->setTitle('Booking Notification')
                ->setBody($this->message))
            ->setToken($this->fcmToken);
    }
}
