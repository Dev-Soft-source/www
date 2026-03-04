<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PxBooking extends Model
{
    protected $table = 'px_bookings';

    protected $fillable = [
        'ride_id',
        'passenger_id',
        'driver_id',
        'from_stop_id',
        'to_stop_id',
        'card_id',
        'seats',
        'segment_price_minor',
        'total_price_minor',
        'currency',
        'status',
        'booked_at',
        'meta',
    ];

    protected $casts = [
        'booked_at' => 'datetime',
        'meta' => 'array',
    ];

    public function ride(): BelongsTo
    {
        return $this->belongsTo(PxRide::class, 'ride_id');
    }

    public function passenger(): BelongsTo
    {
        return $this->belongsTo(User::class, 'passenger_id');
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function fromStop(): BelongsTo
    {
        return $this->belongsTo(PxRideStop::class, 'from_stop_id');
    }

    public function toStop(): BelongsTo
    {
        return $this->belongsTo(PxRideStop::class, 'to_stop_id');
    }

    public function card(): BelongsTo
    {
        return $this->belongsTo(Card::class, 'card_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(PxTransaction::class, 'booking_id');
    }
}

