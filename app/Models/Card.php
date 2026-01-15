<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = ['user_id','stripe_payment_method_id','payment_method_type','paypal_email','paypal_payer_id','payment_method_details','name_on_card','card_number','card_type','exp_month','exp_year','address','primary_card','fingerprint','added_on'];
    
    protected $casts = [
        'payment_method_details' => 'array',
    ];
}
