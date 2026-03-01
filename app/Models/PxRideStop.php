<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PxRideStop extends Model
{
    protected $table = 'px_ride_stops';

    protected $fillable = [
        'ride_id',
        'stop_order',
        'city_id',
        'label',
        'lat',
        'lng',
        'eta_at',
        'price_delta_minor',
        'seats_available',
        'is_pickup',
        'is_dropoff',
    ];

    protected $casts = [
        'eta_at' => 'datetime',
        'is_pickup' => 'boolean',
        'is_dropoff' => 'boolean',
    ];

    public function ride(): BelongsTo
    {
        return $this->belongsTo(PxRide::class, 'ride_id');
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city_id');
    }
}

