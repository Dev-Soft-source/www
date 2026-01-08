<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Transaction extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = ['booking_id','type','price', 'paypal_id', 'stripe_id', 'parent_id', 'booking_fee','pay_by_account','coffee_from_wall', 'tax_percentage', 'deduct_type', 'tax_type', 'tax_amount'];

    function booking(){
        return $this->belongsTo(Booking::class, 'booking_id');
    }
    protected static function booted()
    {
        parent::booted();

        static::saved(function ($transaction) {
            if (!isset($transaction->random_id)) {
                $randomStr = strtoupper(Str::random(4)); // 4 random letters (A-Z)
                $transaction->random_id = $randomStr . '-' . $transaction->id;
                $transaction->save();
            }
        });
    }
}
