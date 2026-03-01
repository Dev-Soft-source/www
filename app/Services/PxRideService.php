<?php

namespace App\Services;

use App\Models\PxRide;
use App\Models\PxRideSearchLog;
use App\Models\PxRoute;
use App\Models\PxOption;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class PxRideService
{
    public function createRide(array $payload, User $driver): PxRide
    {
        return DB::transaction(function () use ($payload, $driver) {
            $origin = Arr::get($payload, 'origin', []);
            $destination = Arr::get($payload, 'destination', []);

            $fingerprint = $this->routeFingerprint(
                Arr::get($origin, 'city_id'),
                Arr::get($destination, 'city_id'),
                Arr::get($origin, 'label'),
                Arr::get($destination, 'label'),
                Arr::get($origin, 'lat'),
                Arr::get($origin, 'lng'),
                Arr::get($destination, 'lat'),
                Arr::get($destination, 'lng')
            );

            $route = PxRoute::query()->firstOrCreate(
                ['fingerprint' => $fingerprint],
                [
                    'origin_city_id' => Arr::get($origin, 'city_id'),
                    'destination_city_id' => Arr::get($destination, 'city_id'),
                    'origin_label' => Arr::get($origin, 'label'),
                    'destination_label' => Arr::get($destination, 'label'),
                    'origin_lat' => Arr::get($origin, 'lat'),
                    'origin_lng' => Arr::get($origin, 'lng'),
                    'destination_lat' => Arr::get($destination, 'lat'),
                    'destination_lng' => Arr::get($destination, 'lng'),
                    'distance_meters' => Arr::get($payload, 'distance_meters'),
                    'duration_seconds' => Arr::get($payload, 'duration_seconds'),
                    'timezone' => Arr::get($payload, 'timezone', 'UTC'),
                    'polyline' => Arr::get($payload, 'polyline'),
                ]
            );

            $seatsTotal = (int) Arr::get($payload, 'seats_total');
            $status = Arr::get($payload, 'status', 'published');
            // Accept both payload keys:
            // - ride_option_ids: API/normalized
            // - preference: legacy/UI checkbox alias
            $selectedRideOptionIds = collect(Arr::get($payload, 'ride_option_ids', Arr::get($payload, 'preference', [])))
                ->filter()
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values();

            $meta = Arr::get($payload, 'meta', []);
            if (!is_array($meta)) {
                $meta = [];
            }
            $pickupLocation = trim((string) Arr::get($origin, 'pickup_location', ''));
            $dropoffLocation = trim((string) Arr::get($destination, 'dropoff_location', ''));
            if ($pickupLocation !== '') {
                $meta['pickup_location'] = $pickupLocation;
            }
            if ($dropoffLocation !== '') {
                $meta['dropoff_location'] = $dropoffLocation;
            }

            $ride = PxRide::query()->create([
                'route_id' => $route->id,
                'driver_id' => $driver->id,
                'vehicle_id' => Arr::get($payload, 'vehicle_id'),
                'departure_at' => Arr::get($payload, 'departure_at'),
                'arrival_estimated_at' => Arr::get($payload, 'arrival_estimated_at'),
                'boarding_window_minutes' => Arr::get($payload, 'boarding_window_minutes', 15),
                'seats_total' => $seatsTotal,
                'seats_available' => $seatsTotal,
                'price_minor' => (int) Arr::get($payload, 'price_minor'),
                'currency' => strtoupper((string) Arr::get($payload, 'currency', 'USD')),
                'status' => $status,
                'visibility' => Arr::get($payload, 'visibility', 'public'),
                'booking_mode' => (int) Arr::get($payload, 'booking_mode', 0),
                'booking_method' => (int) Arr::get($payload, 'booking_method', 0),
                'allow_detour' => (bool) Arr::get($payload, 'allow_detour', false),
                'women_only' => (bool) Arr::get($payload, 'women_only', false),
                'extra_care' => (bool) Arr::get($payload, 'extra_care', false),
                'smoking_allowed' => (int) Arr::get($payload, 'smoking_allowed', 0),
                'pets_allowed' => (int) Arr::get($payload, 'pets_allowed', 0),
                'luggage_size' => (int) Arr::get($payload, 'luggage_size', 0),
                'cancelation_policy' => (int) Arr::get($payload, 'cancelation_policy', 0),
                'notes' => Arr::get($payload, 'notes'),
                'meta' => empty($meta) ? null : $meta,
                'published_at' => $status === 'published' ? now() : null,
            ]);

            $stops = Arr::get($payload, 'stops', []);
            $orderedStops = $this->buildOrderedStops($origin, $destination, $stops);

            foreach ($orderedStops as $index => $stop) {
                $ride->stops()->create([
                    'stop_order' => $index + 1,
                    'city_id' => Arr::get($stop, 'city_id'),
                    'label' => Arr::get($stop, 'label'),
                    'lat' => Arr::get($stop, 'lat'),
                    'lng' => Arr::get($stop, 'lng'),
                    'eta_at' => Arr::get($stop, 'eta_at'),
                    'price_delta_minor' => (int) Arr::get($stop, 'price_delta_minor', 0),
                    'seats_available' => Arr::get($stop, 'seats_available'),
                    'is_pickup' => (bool) Arr::get($stop, 'is_pickup', true),
                    'is_dropoff' => (bool) Arr::get($stop, 'is_dropoff', true),
                ]);
            }

            if ($selectedRideOptionIds->isNotEmpty()) {
                $validOptionIds = PxOption::query()
                    ->whereIn('id', $selectedRideOptionIds)
                    ->where('is_active', true)
                    ->pluck('id')
                    ->all();
                $ride->options()->sync($validOptionIds);
            }

            return $ride->load(['route', 'stops', 'driver', 'vehicle', 'options.translations']);
        });
    }

    public function searchRides(array $filters, ?User $user = null): LengthAwarePaginator
    {
        $query = PxRide::query()
            ->with(['route', 'stops', 'driver', 'vehicle', 'options.translations'])
            ->published();

        $this->applyOrderedStopFilters($query, $filters);
        $this->applyGeoFilters($query, $filters);
        $this->applyDepartureFilters($query, $filters);
        $this->applyRideFilters($query, $filters);
        $this->applySorting($query, Arr::get($filters, 'sort', 'soonest'));

        $perPage = (int) Arr::get($filters, 'per_page', 20);
        $result = $query->paginate($perPage);

        if ((bool) Arr::get($filters, 'log_search', true)) {
            $this->logSearch($filters, $result->total(), $user);
        }

        return $result;
    }

    protected function applyOrderedStopFilters(Builder $query, array $filters): void
    {
        $fromCityId = Arr::get($filters, 'origin_city_id');
        $toCityId = Arr::get($filters, 'destination_city_id');
        $fromLabel = trim((string) Arr::get($filters, 'origin_label', ''));
        $toLabel = trim((string) Arr::get($filters, 'destination_label', ''));

        $hasFrom = !empty($fromCityId) || $fromLabel !== '';
        $hasTo = !empty($toCityId) || $toLabel !== '';

        // Most important rule for ride matching:
        // departure stop must appear before destination stop in same ride.
        if ($hasFrom && $hasTo) {
            $query->whereExists(function ($sub) use ($fromCityId, $toCityId, $fromLabel, $toLabel) {
                $sub->select(DB::raw(1))
                    ->from('px_ride_stops as s_from')
                    ->join('px_ride_stops as s_to', function ($join) {
                        $join->on('s_to.ride_id', '=', 's_from.ride_id')
                            ->whereColumn('s_from.stop_order', '<', 's_to.stop_order');
                    })
                    ->whereColumn('s_from.ride_id', 'px_rides.id');

                if (!empty($fromCityId)) {
                    $sub->where('s_from.city_id', $fromCityId);
                } else {
                    $sub->where('s_from.label', 'like', '%' . $fromLabel . '%');
                }

                if (!empty($toCityId)) {
                    $sub->where('s_to.city_id', $toCityId);
                } else {
                    $sub->where('s_to.label', 'like', '%' . $toLabel . '%');
                }
            });
            return;
        }

        // One-sided queries (only origin or only destination) match any stop.
        if ($hasFrom) {
            $query->whereHas('stops', function (Builder $stopQuery) use ($fromCityId, $fromLabel) {
                if (!empty($fromCityId)) {
                    $stopQuery->where('city_id', $fromCityId);
                } else {
                    $stopQuery->where('label', 'like', '%' . $fromLabel . '%');
                }
            });
        }

        if ($hasTo) {
            $query->whereHas('stops', function (Builder $stopQuery) use ($toCityId, $toLabel) {
                if (!empty($toCityId)) {
                    $stopQuery->where('city_id', $toCityId);
                } else {
                    $stopQuery->where('label', 'like', '%' . $toLabel . '%');
                }
            });
        }
    }

    protected function applyGeoFilters(Builder $query, array $filters): void
    {
        $radius = (float) Arr::get($filters, 'radius_km', 20);
        $radiusMeters = $radius * 1000;

        if (Arr::has($filters, ['origin_lat', 'origin_lng'])) {
            $lat = (float) Arr::get($filters, 'origin_lat');
            $lng = (float) Arr::get($filters, 'origin_lng');
            $query->whereHas('route', function (Builder $routeQuery) use ($lat, $lng, $radiusMeters) {
                $routeQuery->whereRaw(
                    '(6371000 * ACOS(COS(RADIANS(?)) * COS(RADIANS(origin_lat)) * COS(RADIANS(origin_lng) - RADIANS(?)) + SIN(RADIANS(?)) * SIN(RADIANS(origin_lat)))) <= ?',
                    [$lat, $lng, $lat, $radiusMeters]
                );
            });
        }

        if (Arr::has($filters, ['destination_lat', 'destination_lng'])) {
            $lat = (float) Arr::get($filters, 'destination_lat');
            $lng = (float) Arr::get($filters, 'destination_lng');
            $query->whereHas('route', function (Builder $routeQuery) use ($lat, $lng, $radiusMeters) {
                $routeQuery->whereRaw(
                    '(6371000 * ACOS(COS(RADIANS(?)) * COS(RADIANS(destination_lat)) * COS(RADIANS(destination_lng) - RADIANS(?)) + SIN(RADIANS(?)) * SIN(RADIANS(destination_lat)))) <= ?',
                    [$lat, $lng, $lat, $radiusMeters]
                );
            });
        }
    }

    protected function applyDepartureFilters(Builder $query, array $filters): void
    {
        if (Arr::has($filters, 'departure_date')) {
            $query->whereDate('departure_at', Arr::get($filters, 'departure_date'));
            return;
        }

        if (Arr::has($filters, 'departure_from')) {
            $query->where('departure_at', '>=', Arr::get($filters, 'departure_from'));
        }
        if (Arr::has($filters, 'departure_to')) {
            $query->where('departure_at', '<=', Arr::get($filters, 'departure_to'));
        }
    }

    protected function applyRideFilters(Builder $query, array $filters): void
    {
        $seatsRequired = (int) Arr::get($filters, 'seats_required', 1);
        $query->where('seats_available', '>=', max(1, $seatsRequired));

        if (Arr::has($filters, 'price_minor_min')) {
            $query->where('price_minor', '>=', (int) Arr::get($filters, 'price_minor_min'));
        }
        if (Arr::has($filters, 'price_minor_max')) {
            $query->where('price_minor', '<=', (int) Arr::get($filters, 'price_minor_max'));
        }

        foreach (['booking_mode', 'luggage_size', 'smoking_allowed', 'pets_allowed'] as $field) {
            if (Arr::has($filters, $field)) {
                $query->where($field, (int) Arr::get($filters, $field));
            }
        }

        foreach (['women_only', 'extra_care'] as $field) {
            if (Arr::has($filters, $field)) {
                $query->where($field, (bool) Arr::get($filters, $field));
            }
        }
    }

    protected function applySorting(Builder $query, string $sort): void
    {
        if ($sort === 'price_asc') {
            $query->orderBy('price_minor', 'asc')->orderBy('departure_at', 'asc');
            return;
        }

        if ($sort === 'price_desc') {
            $query->orderBy('price_minor', 'desc')->orderBy('departure_at', 'asc');
            return;
        }

        $query->orderBy('departure_at', 'asc');
    }

    protected function logSearch(array $filters, int $total, ?User $user): void
    {
        PxRideSearchLog::query()->create([
            'user_id' => $user?->id,
            'origin_city_id' => Arr::get($filters, 'origin_city_id'),
            'destination_city_id' => Arr::get($filters, 'destination_city_id'),
            'origin_label' => Arr::get($filters, 'origin_label'),
            'destination_label' => Arr::get($filters, 'destination_label'),
            'departure_date' => Arr::get($filters, 'departure_date'),
            'seats_required' => Arr::get($filters, 'seats_required'),
            'results_count' => $total,
            'filters' => $filters,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'searched_at' => now(),
        ]);
    }

    protected function routeFingerprint(
        $originCityId,
        $destinationCityId,
        $originLabel,
        $destinationLabel,
        $originLat,
        $originLng,
        $destinationLat,
        $destinationLng
    ): string {
        return hash('sha256', implode('|', [
            $originCityId ?: 'null',
            $destinationCityId ?: 'null',
            mb_strtolower(trim((string) $originLabel)),
            mb_strtolower(trim((string) $destinationLabel)),
            $originLat ?: 'null',
            $originLng ?: 'null',
            $destinationLat ?: 'null',
            $destinationLng ?: 'null',
        ]));
    }

    protected function buildOrderedStops(array $origin, array $destination, array $intermediateStops): array
    {
        $stops = [];

        $stops[] = [
            'city_id' => Arr::get($origin, 'city_id'),
            'label' => Arr::get($origin, 'label'),
            'lat' => Arr::get($origin, 'lat'),
            'lng' => Arr::get($origin, 'lng'),
            'is_pickup' => true,
            'is_dropoff' => false,
        ];

        foreach ($intermediateStops as $stop) {
            $label = trim((string) Arr::get($stop, 'label', ''));
            if ($label === '') {
                continue;
            }
            $stops[] = [
                'city_id' => Arr::get($stop, 'city_id'),
                'label' => $label,
                'lat' => Arr::get($stop, 'lat'),
                'lng' => Arr::get($stop, 'lng'),
                'eta_at' => Arr::get($stop, 'eta_at'),
                'price_delta_minor' => Arr::get($stop, 'price_delta_minor', 0),
                'seats_available' => Arr::get($stop, 'seats_available'),
                'is_pickup' => (bool) Arr::get($stop, 'is_pickup', true),
                'is_dropoff' => (bool) Arr::get($stop, 'is_dropoff', true),
            ];
        }

        $stops[] = [
            'city_id' => Arr::get($destination, 'city_id'),
            'label' => Arr::get($destination, 'label'),
            'lat' => Arr::get($destination, 'lat'),
            'lng' => Arr::get($destination, 'lng'),
            'is_pickup' => false,
            'is_dropoff' => true,
        ];

        return $stops;
    }
}
