<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NoShowHistory extends Model
{
    use HasFactory;

    public $table = "no_show_history";

    protected $guarded = [];

    function ride(){
        return $this->belongsTo(Ride::class, 'ride_id');
    }

    function booking(){
        return $this->belongsTo(Booking::class, 'booking_id');
    }
    
    function user(){
        return $this->belongsTo(User::class, 'user_id');
    }
}
