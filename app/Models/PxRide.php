<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use phpseclib3\Crypt\Hash;

class PxRide extends Model
{
    use SoftDeletes;

    protected $table = 'px_rides';

    protected $fillable = [
        'route_id',
        'driver_id',
        'vehicle_id',
        'departure_at',
        'arrival_estimated_at',
        'boarding_window_minutes',
        'seats_total',
        'seats_available',
        'price_minor',
        'currency',
        'status',
        'visibility',
        'booking_mode',
        'booking_method',
        'allow_detour',
        'women_only',
        'extra_care',
        'smoking_allowed',
        'pets_allowed',
        'luggage_size',
        'cancelation_policy',
        'notes',
        'meta',
        'published_at',
        'cancelled_at',
        'cancel_reason',
    ];

    protected $casts = [
        'departure_at' => 'datetime',
        'arrival_estimated_at' => 'datetime',
        'published_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'meta' => 'array',
        'booking_mode' => 'integer',
        'booking_method' => 'integer',
        'smoking_allowed' => 'integer',
        'pets_allowed' => 'integer',
        'luggage_size' => 'integer',
        'cancelation_policy' => 'integer',
        'allow_detour' => 'boolean',
        'women_only' => 'boolean',
        'extra_care' => 'boolean',
    ];

    public function route(): BelongsTo
    {
        return $this->belongsTo(PxRoute::class, 'route_id');
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    public function stops(): HasMany
    {
        return $this->hasMany(PxRideStop::class, 'ride_id')->orderBy('stop_order');
    }

    public function options(): BelongsToMany
    {
        return $this->belongsToMany(PxOption::class, 'px_ride_option_assignments', 'ride_id', 'option_id')
            ->withPivot('value')
            ->withTimestamps();
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(PxBooking::class, 'ride_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(PxTransaction::class, 'ride_id');
    }

    public function seatDetail(): HasMany
    {
        return $this->hasMany(SeatDetail::class, 'ride_id')->orderBy('seat_number');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('status', 'published')
            ->where('visibility', 'public')
            ->where('departure_at', '>=', now());
    }
}
