<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationMessageDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'notification_message_id',
        'language_id',
        'message',
    ];

    public function notificationMessage()
    {
        return $this->belongsTo(NotificationMessage::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}
