<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'gateway',
        'payment_method_id',
        'type',
        'brand',
        'last4',
        'email',
        'is_default',
        'name',
        'code',
        'is_active',
        'config'
    ];

    protected $casts = [
        'config' => 'array',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
    ];
}
