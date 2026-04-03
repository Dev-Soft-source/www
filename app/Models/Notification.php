<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    public $timestamps = false;

    // Notification `type` constants
    // '1' => my-ride (private ride details for owner)
    // '2' => public ride detail (ride detail page)
    // null => chat/message (no ride link; opens chat/inbox)
    public const TYPE_MY_RIDE = '1';
    public const TYPE_RIDE_DETAIL = '2';
    public const TYPE_CHAT = null;

    protected $fillable = ['type', 'ride_id', 'posted_to', 'posted_by', 'receiver_id', 'message', 'status', 'notification_type', 'is_delete', 'is_read', 'ride_detail_id', 'departure', 'destination', 'from_stop_id', 'to_stop_id', 'category'];

    function ride()
    {
        return $this->belongsTo(Ride::class, 'ride_id');
    }

    function rideDetail()
    {
        return $this->belongsTo(RideDetail::class, 'ride_detail_id');
    }

    function from()
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    function booking()
    {
        return $this->belongsTo(Booking::class, 'posted_to');
    }

    function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}
