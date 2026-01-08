<?php

namespace App\Models;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoffeeWallet extends Model
{
    use HasFactory;
    public $table = "coffee_wallets";

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
    public function card()
    {
        return $this->hasOne(Card::class, 'user_id', 'user_id');
    }


    protected static function booted()
    {
        parent::booted();

        static::saved(function ($coffeeWallet) {
            if (!isset($coffeeWallet->random_id)) {
                $randomStr = strtoupper(Str::random(4)); // 4 random letters (A-Z)
                $randomId = strtoupper(Str::random(2)); // 2 random letters (A-Z)
                $coffeeWallet->random_id = $randomStr . '-' . $randomId;
                $coffeeWallet->save();
            }
        });
    }
}
