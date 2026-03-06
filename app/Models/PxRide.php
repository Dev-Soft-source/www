<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

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

    public function resolveStopIndexes(int $fromStopId, int $toStopId): array
    {
        $orderedStops = $this->relationLoaded('stops')
            ? $this->stops->sortBy('stop_order')->values()
            : $this->stops()->orderBy('stop_order')->get()->values();

        $fromIndex = null;
        $toIndex = null;

        foreach ($orderedStops as $idx => $stop) {
            $stopId = (int) ($stop->id ?? 0);
            if ($stopId === $fromStopId) {
                $fromIndex = $idx;
            }
            if ($stopId === $toStopId) {
                $toIndex = $idx;
            }
        }

        return [$fromIndex, $toIndex];
    }

    public function resolveSegmentAvailableSeats(int $fromStopId, int $toStopId): int
    {
        $orderedStops = $this->relationLoaded('stops')
            ? $this->stops->sortBy('stop_order')->values()
            : $this->stops()->orderBy('stop_order')->get()->values();

        [$fromIndex, $toIndex] = $this->resolveStopIndexes($fromStopId, $toStopId);
        if ($fromIndex === null || $toIndex === null || $fromIndex >= $toIndex) {
            return 0;
        }

        $segmentLegStops = $orderedStops->slice($fromIndex + 1, $toIndex - $fromIndex);
        if ($segmentLegStops->isEmpty()) {
            return 0;
        }

        return max(0, (int) $segmentLegStops->min(function ($stop) {
            return (int) ($stop->seats_available ?? $this->seats_available ?? $this->seats_total ?? 0);
        }));
    }

    public function resolveConfiguredSegmentPriceMinor(int $fromIndex, int $toIndex): ?int
    {
        $segmentPrices = $this->meta['segment_prices'] ?? null;
        if (!is_array($segmentPrices)) {
            return null;
        }

        foreach ($segmentPrices as $segmentPrice) {
            if (!is_array($segmentPrice)) {
                continue;
            }

            if (
                (int) ($segmentPrice['from_index'] ?? -1) === $fromIndex
                && (int) ($segmentPrice['to_index'] ?? -1) === $toIndex
            ) {
                return max(0, (int) ($segmentPrice['price_minor'] ?? 0));
            }
        }

        return null;
    }

    public function adjustSegmentSeatAvailability(int $fromStopId, int $toStopId, int $seatDelta): void
    {
        $this->load('stops');
        $orderedStops = $this->stops->sortBy('stop_order')->values();
        [$fromIndex, $toIndex] = $this->resolveStopIndexes($fromStopId, $toStopId);

        if ($fromIndex === null || $toIndex === null || $fromIndex >= $toIndex) {
            throw new \RuntimeException('Invalid route section for booking.');
        }

        $maxSeats = max(0, (int) ($this->seats_total ?? 0));

        for ($idx = $fromIndex + 1; $idx <= $toIndex; $idx++) {
            $stop = $orderedStops->get($idx);
            if (!$stop) {
                continue;
            }

            $currentSeats = (int) ($stop->seats_available ?? $maxSeats);
            $updatedSeats = $currentSeats - $seatDelta;

            if ($seatDelta > 0 && $updatedSeats < 0) {
                throw new \RuntimeException('Not enough available seats for this route section.');
            }

            if ($maxSeats > 0) {
                $updatedSeats = min($maxSeats, $updatedSeats);
            }

            $stop->seats_available = max(0, $updatedSeats);
            $stop->save();
        }

        $this->refreshSeatsAvailableFromStops();
    }

    public function refreshSeatsAvailableFromStops(): void
    {
        $this->load('stops');
        $orderedStops = $this->stops->sortBy('stop_order')->values();
        $segmentLegStops = $orderedStops->slice(1);

        $seatsAvailable = $segmentLegStops->isEmpty()
            ? (int) ($this->seats_total ?? 0)
            : max(0, (int) $segmentLegStops->min(function ($stop) {
                return (int) ($stop->seats_available ?? $this->seats_total ?? 0);
            }));

        if ((int) $this->seats_available !== $seatsAvailable) {
            $this->seats_available = $seatsAvailable;
            $this->save();
        }
    }
}
