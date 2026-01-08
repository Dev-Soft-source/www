<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;
    public $timestamps = false;
    
    protected $fillable = ['ride_id','review','type','posted_to','posted_by','timeliness','vehicle_condition','safety','conscious','comfort','communication','attitude','respect','hygiene','average_rating','feature','status','reply_deadline','live_limit'];

    function ride(){
        return $this->belongsTo(Ride::class, 'ride_id');
    }
    
    function from(){
        return $this->belongsTo(User::class, 'posted_by');
    }

    function booking(){
        return $this->belongsTo(Booking::class, 'posted_to');
    }

    function replies(){
        return $this->hasMany(ReviewReply::class, 'rating_id');
    }
}
