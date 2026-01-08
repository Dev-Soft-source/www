<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeatDetail extends Model
{
    use HasFactory;

    public $table = "seat_details";
    protected $guarded = [];

    function userInfo(){
        return $this->belongTo(User::class, 'user_id');
    }

    function rideDetail(){
        return $this->belongTo(Ride::class, 'ride_id');
    }

    function bookingDetail(){
        return $this->belongTo(Booking::class, 'booking_id');
    }
}
