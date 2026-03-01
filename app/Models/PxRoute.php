<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PxRoute extends Model
{
    protected $table = 'px_routes';

    protected $fillable = [
        'origin_city_id',
        'destination_city_id',
        'origin_label',
        'destination_label',
        'origin_lat',
        'origin_lng',
        'destination_lat',
        'destination_lng',
        'origin_geohash',
        'destination_geohash',
        'distance_meters',
        'duration_seconds',
        'timezone',
        'polyline',
        'fingerprint',
    ];

    public function rides(): HasMany
    {
        return $this->hasMany(PxRide::class, 'route_id');
    }

    public function originCity(): BelongsTo
    {
        return $this->belongsTo(City::class, 'origin_city_id');
    }

    public function destinationCity(): BelongsTo
    {
        return $this->belongsTo(City::class, 'destination_city_id');
    }
}

