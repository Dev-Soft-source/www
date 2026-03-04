<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PxTransaction extends Model
{
    protected $table = 'px_transactions';

    protected $fillable = [
        'booking_id',
        'ride_id',
        'user_id',
        'amount_minor',
        'currency',
        'provider',
        'type',
        'status',
        'stripe_payment_intent_id',
        'stripe_payment_method_id',
        'provider_payload',
        'processed_at',
    ];

    protected $casts = [
        'provider_payload' => 'array',
        'processed_at' => 'datetime',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(PxBooking::class, 'booking_id');
    }

    public function ride(): BelongsTo
    {
        return $this->belongsTo(PxRide::class, 'ride_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

