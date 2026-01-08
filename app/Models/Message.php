<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;
    protected $fillable = ['ride_id','receiver','sender','message','is_read', 'status', 'redirect', 'ride_detail_id'];
    
    function user(){
        return $this->belongsTo(User::class, 'sender');
    }

    function receiver(){
        return $this->belongsTo(User::class, 'receiver');
    }

    function ride(){
        return $this->belongsTo(Ride::class, 'ride_id');
    }

    function rideDetail(){
        return $this->belongsTo(RideDetail::class, 'ride_detail_id', 'id');
    }
}
