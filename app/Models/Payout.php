<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Payout extends Model
{
    use HasFactory;

    public $table = "payouts";
    protected $guarded = [];

    function ride(){
        return $this->belongsTo(Ride::class, 'ride_id');
    }

    function driver(){
        return $this->belongsTo(User::class, 'user_id');
    }
    function bookings(){
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    protected static function booted()
    {
        parent::booted();

        static::saved(function ($payout) {
            if (!isset($payout->random_id)) {
                $randomStr = strtoupper(Str::random(4)); // 4 random letters (A-Z)
                $payout->random_id = $randomStr . '-' . $payout->id;
                $payout->save();
            }
        });
    }
}
