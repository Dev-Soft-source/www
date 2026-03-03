<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Http\Requests\Px\PxSearchRidesRequest;
use App\Http\Requests\Px\PxStoreRideRequest;
use App\Models\Language;
use App\Models\PxOptionGroup;
use App\Models\PxRide;
use App\Services\PxRideService;
use App\Traits\StatusResponser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PxRideController extends Controller
{
    use StatusResponser;

    public function rideOptions(Request $request): JsonResponse
    {
        $selectedLanguage = Language::resolveLanguage($request->input('lang'));
        $defaultLanguage = Language::where('is_default', 1)->first();
        $selectedLangId = optional($selectedLanguage)->id;
        $defaultLangId = optional($defaultLanguage)->id;

        $groups = PxOptionGroup::query()
            ->with(['options' => function ($q) use ($selectedLangId, $defaultLangId) {
                $q->where('is_active', true)
                    ->orderBy('sort_order')
                    ->with(['translations' => function ($tq) use ($selectedLangId, $defaultLangId) {
                        $tq->whereIn('language_id', array_filter([$selectedLangId, $defaultLangId]));
                    }]);
            }])
            ->orderBy('sort_order')
            ->get()
            ->map(function ($group) use ($selectedLangId, $defaultLangId) {
                return [
                    'id' => $group->id,
                    'code' => $group->code,
                    'options' => $group->options->map(function ($option) use ($selectedLangId, $defaultLangId) {
                        $selected = $option->translations->firstWhere('language_id', $selectedLangId);
                        $fallback = $option->translations->firstWhere('language_id', $defaultLangId);
                        return [
                            'id' => $option->id,
                            'code' => $option->code,
                            'label' => optional($selected)->label ?: optional($fallback)->label ?: $option->code,
                            'description' => optional($selected)->description ?: optional($fallback)->description,
                            'meta' => $option->meta,
                        ];
                    })->values(),
                ];
            })->values();

        return response()->json([
            'status' => 'Success',
            'message' => 'PX ride options fetched successfully.',
            'data' => $groups,
        ]);
    }

    public function search(PxSearchRidesRequest $request, PxRideService $service): JsonResponse
    {
        $paginator = $service->searchRides($request->validated(), $request->user());

        return response()->json([
            'status' => 'Success',
            'message' => 'PX rides fetched successfully.',
            'data' => [
                'items' => collect($paginator->items())->map(fn (PxRide $ride) => $this->transformRide($ride))->values(),
                'pagination' => [
                    'total' => $paginator->total(),
                    'per_page' => $paginator->perPage(),
                    'current_page' => $paginator->currentPage(),
                    'last_page' => $paginator->lastPage(),
                ],
            ],
        ]);
    }

    public function store(PxStoreRideRequest $request, PxRideService $service): JsonResponse
    {
        $ride = $service->createRide($request->validated(), $request->user());

        return response()->json([
            'status' => 'Success',
            'message' => 'PX ride posted successfully.',
            'data' => $this->transformRide($ride),
        ], 201);
    }

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $selectedLanguage = Language::resolveLanguage($request->input('lang'));
        $defaultLanguage = Language::where('is_default', 1)->first();
        $selectedLangId = optional($selectedLanguage)->id;
        $defaultLangId = optional($defaultLanguage)->id;

        // Get tab filter from query parameter (default to 'upcoming')
        $tab = $request->query('tab', 'upcoming');

        // Build query based on tab
        $query = PxRide::where('driver_id', $user->id)
            ->with(['route', 'vehicle', 'stops', 'options.translations']);

        switch ($tab) {
            case 'completed':
                // include past rides even if they are not marked as completed, as long as their departure time has passed
                $query->where(function ($query) {
                    $query->where('status', 'completed')
                        ->orWhere(function ($query) {
                            $query->where('status', '!=', 'completed')
                                ->where('departure_at', '<', now());
                        });
                })
                    ->orderBy('departure_at', 'desc');
                break;
            case 'cancelled':
                $query->where('status', 'cancelled')
                    ->orderBy('departure_at', 'desc');
                break;
            case 'upcoming':
            default:
                $query->whereIn('status', ['draft', 'published', 'started'])
                    ->where('departure_at', '>=', now())
                    ->orderBy('departure_at', 'asc');
                break;
        }

        $perPage = (int) $request->query('per_page', 10);
        $rides = $query->paginate($perPage);

        // Calculate counts for each tab
        $upcomingCount = PxRide::where('driver_id', $user->id)
            ->whereIn('status', ['draft', 'published', 'started'])
            ->where('departure_at', '>=', now())
            ->count();

        $completedCount = PxRide::where('driver_id', $user->id)
            ->where(function ($query) {
                $query->where('status', 'completed')
                    ->orWhere(function ($query) {
                        $query->where('status', '!=', 'completed')
                            ->where('departure_at', '<', now());
                    });
            })
            ->count();

        $cancelledCount = PxRide::where('driver_id', $user->id)
            ->where('status', 'cancelled')
            ->count();

        return response()->json([
            'status' => 'Success',
            'message' => 'PX rides fetched successfully.',
            'data' => [
                'items' => collect($rides->items())->map(fn (PxRide $ride) => $this->transformRide($ride))->values(),
                'pagination' => [
                    'total' => $rides->total(),
                    'per_page' => $rides->perPage(),
                    'current_page' => $rides->currentPage(),
                    'last_page' => $rides->lastPage(),
                ],
                'counts' => [
                    'upcoming' => $upcomingCount,
                    'completed' => $completedCount,
                    'cancelled' => $cancelledCount,
                ],
                'active_tab' => $tab,
            ],
        ]);
    }

    public function show(Request $request, $id): JsonResponse
    {
        $user = $request->user();
        $selectedLanguage = Language::resolveLanguage($request->input('lang'));
        $defaultLanguage = Language::where('is_default', 1)->first();
        $selectedLangId = optional($selectedLanguage)->id;
        $defaultLangId = optional($defaultLanguage)->id;

        // Get the PX ride and verify ownership
        $ride = PxRide::where('id', $id)
            ->where('driver_id', $user->id)
            ->with(['route', 'vehicle', 'stops', 'options.translations', 'driver'])
            ->first();

        if (!$ride) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Ride not found or you do not have permission to view it.',
            ], 404);
        }

        return response()->json([
            'status' => 'Success',
            'message' => 'PX ride fetched successfully.',
            'data' => $this->transformRide($ride),
        ]);
    }

    public function update(PxStoreRideRequest $request, PxRideService $service, $id): JsonResponse
    {
        $user = $request->user();

        // Get the PX ride and verify ownership
        $ride = PxRide::where('id', $id)
            ->where('driver_id', $user->id)
            ->first();

        if (!$ride) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Ride not found or you do not have permission to update it.',
            ], 404);
        }

        // Check if ride can be edited (upcoming and not booked)
        $isUpcoming = $ride->departure_at > now();
        $isUpcomingStatus = in_array($ride->status, ['draft', 'published', 'started']);
        $isNotBooked = $ride->seats_available == $ride->seats_total;

        if (!$isUpcoming || !$isUpcomingStatus || !$isNotBooked) {
            return response()->json([
                'status' => 'Error',
                'message' => 'This ride cannot be updated. Only upcoming rides without bookings can be updated.',
            ], 422);
        }

        $payload = $request->validated();
        $updatedRide = $service->updateRide($ride, $payload, $user);

        return response()->json([
            'status' => 'Success',
            'message' => 'PX ride updated successfully.',
            'data' => $this->transformRide($updatedRide),
        ]);
    }

    protected function transformRide(PxRide $ride): array
    {
        return [
            'id' => $ride->id,
            'status' => $ride->status,
            'visibility' => $ride->visibility,
            'booking_mode' => (int) $ride->booking_mode,
            'booking_method' => (int) $ride->booking_method,
            'driver_id' => $ride->driver_id,
            'vehicle_id' => $ride->vehicle_id,
            'departure_at' => optional($ride->departure_at)->toIso8601String(),
            'arrival_estimated_at' => optional($ride->arrival_estimated_at)->toIso8601String(),
            'seats_total' => $ride->seats_total,
            'seats_available' => $ride->seats_available,
            'middle_seats' => (int) data_get($ride->meta, 'seat_layout.middle_seats', 0),
            'back_seats' => (int) data_get($ride->meta, 'seat_layout.back_seats', 0),
            'price_minor' => $ride->price_minor,
            'currency' => $ride->currency,
            'flags' => [
                'allow_detour' => (bool) $ride->allow_detour,
                'women_only' => (bool) $ride->women_only,
                'extra_care' => (bool) $ride->extra_care,
                'smoking_allowed' => (int) $ride->smoking_allowed,
                'pets_allowed' => (int) $ride->pets_allowed,
            ],
            'luggage_size' => (int) $ride->luggage_size,
            'accept_more_luggage' => (bool) data_get($ride->meta, 'accept_more_luggage', false),
            'cancelation_policy' => (int) $ride->cancelation_policy,
            'recurring' => [
                'enabled' => (bool) data_get($ride->meta, 'recurring.enabled', false),
                'frequency' => data_get($ride->meta, 'recurring.frequency'),
                'trips' => data_get($ride->meta, 'recurring.trips'),
            ],
            'notes' => $ride->notes,
            'route' => $ride->route ? [
                'id' => $ride->route->id,
                'origin_city_id' => $ride->route->origin_city_id,
                'destination_city_id' => $ride->route->destination_city_id,
                'origin_label' => $ride->route->origin_label,
                'destination_label' => $ride->route->destination_label,
                'origin_lat' => $ride->route->origin_lat,
                'origin_lng' => $ride->route->origin_lng,
                'destination_lat' => $ride->route->destination_lat,
                'destination_lng' => $ride->route->destination_lng,
                'distance_meters' => $ride->route->distance_meters,
                'duration_seconds' => $ride->route->duration_seconds,
                'timezone' => $ride->route->timezone,
            ] : null,
            'stops' => $ride->stops->map(fn ($stop) => [
                'id' => $stop->id,
                'order' => $stop->stop_order,
                'city_id' => $stop->city_id,
                'label' => $stop->label,
                'lat' => $stop->lat,
                'lng' => $stop->lng,
                'eta_at' => optional($stop->eta_at)->toIso8601String(),
                'price_delta_minor' => $stop->price_delta_minor,
                'seats_available' => $stop->seats_available,
                'is_pickup' => (bool) $stop->is_pickup,
                'is_dropoff' => (bool) $stop->is_dropoff,
            ])->values(),
            'ride_options' => $ride->options->map(function ($option) {
                $label = optional($option->translations->first())->label ?? $option->code;
                return [
                    'id' => $option->id,
                    'code' => $option->code,
                    'label' => $label,
                    'meta' => $option->meta,
                ];
            })->values(),
            'created_at' => optional($ride->created_at)->toIso8601String(),
        ];
    }
}
