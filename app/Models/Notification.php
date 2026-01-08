<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    public $timestamps = false;
    
    protected $fillable = ['type','ride_id','posted_to','posted_by','receiver_id','message','status','notification_type','is_delete','is_read', 'ride_detail_id', 'departure', 'destination', 'category'];

    function ride(){
        return $this->belongsTo(Ride::class, 'ride_id');
    }

    function rideDetail(){
        return $this->belongsTo(RideDetail::class, 'ride_detail_id');
    }
    
    function from(){
        return $this->belongsTo(User::class, 'posted_by');
    }

    function booking(){
        return $this->belongsTo(Booking::class, 'posted_to');
    }

    function receiver(){
        return $this->belongsTo(User::class, 'receiver_id');
    }
}
