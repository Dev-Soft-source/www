<?php

namespace App\Models;
use Illuminate\Support\Str;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TopUpBalance extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $guarded = [];

    function booking(){
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    protected static function booted()
    {
        parent::booted();

        static::saved(function ($topUpBalance) {
            if (!isset($topUpBalance->random_id)) {
                $randomStr = strtoupper(Str::random(4)); // 4 random letters (A-Z)
                $topUpBalance->random_id = $randomStr . '-' . $topUpBalance->id;
                $topUpBalance->save();
            }
        });
    }
}
