<?php

namespace App\Events;

use App\Models\Message;
use App\Models\Ride;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSentEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $ride;
    public $user;
    public $message;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Ride $ride, User $user,Message $message)
    {
        $this->ride = $ride;
        $this->user = $user;
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('chat.' . $this->message->receiver);
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'message.event';
    }

    /**
     * The data to broadcast with the event.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'ride' => [
                'id' => $this->ride->id,
            ],
            'user' => [
                'id' => $this->user->id,
                'first_name' => $this->user->first_name,
                'last_name' => $this->user->last_name,
                'profile_image' => $this->user->profile_image,
            ],
            'message' => [
                'id' => $this->message->id,
                'message' => $this->message->message ?? null,
                'sender' => $this->message->sender,
                'receiver' => $this->message->receiver,
                'ride_id' => $this->message->ride_id,
                'created_at' => optional($this->message->created_at)->toISOString(),
            ],
            // Also include flat structure for easier access
            'ride_id' => $this->ride->id,
            'user_id' => $this->user->id,
            'message_id' => $this->message->id,
            'receiver_id' => $this->message->receiver,
        ];
    }
}
