<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RideStop extends Model
{
    use HasFactory;

    protected $table = 'ride_stops';

    protected $fillable = [
        'ride_id',
        'stop_order',
        'city_id',
        'label',
        'lat',
        'lng',
        'departure_at',
        'pickup_dropoff_location',
        'eta_at',
        'price_delta_minor',
        'seats_available',
        'is_pickup',
        'is_dropoff',
    ];

    protected $casts = [
        'lat' => 'decimal:7',
        'lng' => 'decimal:7',
        'departure_at' => 'datetime',
        'eta_at' => 'datetime',
        'is_pickup' => 'boolean',
        'is_dropoff' => 'boolean',
    ];

    public function ride()
    {
        return $this->belongsTo(Ride::class, 'ride_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function outgoingSegments()
    {
        return $this->hasMany(RideStopSegment::class, 'from_stop_id');
    }

    public function incomingSegments()
    {
        return $this->hasMany(RideStopSegment::class, 'to_stop_id');
    }
}
