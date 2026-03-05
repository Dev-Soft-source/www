<?php

namespace App\Http\Controllers;

use App\Http\Requests\Px\PxStoreRideRequest;
use App\Models\PxBooking;
use App\Models\PxOptionGroup;
use App\Models\PxRide;
use App\Models\Vehicle;
use App\Models\TripsPageSettingDetail;
use App\Models\RideDetailPageSettingDetail;
use App\Services\PxRideService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PxRideWebController extends Controller
{
    public function index($lang = null)
    {
        $user_id = auth()->user()->id;
        $selectedLangId = optional($this->selectedLanguage)->id;
        $defaultLangId = optional($this->defaultLang)->id;

        // Get tab filter from query parameter (default to 'upcoming')
        $tab = request()->query('tab', 'upcoming');

        // Build query based on tab
        $query = PxRide::where('driver_id', $user_id)
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

        $rides = $query->paginate(10);

        // Add translated labels and descriptions to each ride's options
        foreach ($rides as $ride) {
            $ride->options->transform(function ($option) use ($selectedLangId, $defaultLangId) {
                $selected = $option->translations->firstWhere('language_id', $selectedLangId);
                $fallback = $option->translations->firstWhere('language_id', $defaultLangId);
                $option->display_label = optional($selected)->label ?: optional($fallback)->label ?: $option->code;
                $option->display_description = optional($selected)->description ?: optional($fallback)->description;
                return $option;
            });
        }

        // Calculate counts for each tab
        $upcomingCount = PxRide::where('driver_id', $user_id)
            ->whereIn('status', ['draft', 'published', 'started'])
            ->where('departure_at', '>=', now())
            ->count();

        $completedCount = PxRide::where('driver_id', $user_id)
            ->where(function ($query) {
                $query->where('status', 'completed')
                    ->orWhere(function ($query) {
                        $query->where('status', '!=', 'completed')
                            ->where('departure_at', '<', now());
                    });
            })
            ->count();

        $cancelledCount = PxRide::where('driver_id', $user_id)
            ->where('status', 'cancelled')
            ->count();

        $postRidePage = $this->getPostRidePageWithSettingDetail();
        $tripsPage = TripsPageSettingDetail::getByLanguageWithFallback($selectedLangId, $defaultLangId);

        return view('px.my_rides', [
            'rides' => $rides,
            'postRidePage' => $postRidePage,
            'tripsPage' => $tripsPage,
            'activeTab' => $tab,
            'upcomingCount' => $upcomingCount,
            'completedCount' => $completedCount,
            'cancelledCount' => $cancelledCount,
        ]);
    }

    public function create($lang = null)
    {
        $selectedLangId = optional($this->selectedLanguage)->id;
        $defaultLangId = optional($this->defaultLang)->id;

        $isPinkRideDisabled = auth()->user()->isPinkRideDisabled();
        $isExtraRideDisabled = auth()->user()->isFolkRideDisabled();

        $vehicles = Vehicle::query()
            ->where('user_id', auth()->id())
            ->orderByDesc('primary_vehicle')
            ->orderByDesc('id')
            ->get();

        $optionGroups = PxOptionGroup::query()
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
                $group->options = $group->options->map(function ($option) use ($selectedLangId, $defaultLangId) {
                    $selected = $option->translations->firstWhere('language_id', $selectedLangId);
                    $fallback = $option->translations->firstWhere('language_id', $defaultLangId);
                    $option->display_label = optional($selected)->label ?: optional($fallback)->label ?: $option->code;
                    $option->display_description = optional($selected)->description ?: optional($fallback)->description;
                    return $option;
                });
                return $group;
            });

        $postRidePage = $this->getPostRidePageWithSettingDetail();

        return view('px.post_ride', [
            'vehicles' => $vehicles,
            'isExtraRideDisabled' => $isExtraRideDisabled,
            'isPinkRideDisabled' => $isPinkRideDisabled,
            'optionGroups' => $optionGroups,
            'postRidePage' => $postRidePage,
        ]);
    }

    public function store(PxStoreRideRequest $request, PxRideService $service, $lang = null)
    {
        $siteText = $this->siteText;

        $payload = $request->validated();
        $vehicleMode = (string) ($payload['vehicle_mode'] ?? '');

        if ($vehicleMode === 'skip') {
            $payload['vehicle_id'] = null;
        } elseif ($vehicleMode === 'add_new') {
            $newVehicle = (array) ($payload['new_vehicle'] ?? []);
            $primaryVehicle = (string) ($newVehicle['primary_vehicle'] ?? '0');
            $vehicleImageFilename = '';

            if ($request->hasFile('new_vehicle_image')) {
                $file = $request->file('new_vehicle_image');
                $vehicleImageFilename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('/car_images'), $vehicleImageFilename);
            }

            if ($primaryVehicle === '1') {
                Vehicle::query()
                    ->where('user_id', auth()->id())
                    ->update(['primary_vehicle' => 0]);
            }

            $createdVehicle = Vehicle::query()->create([
                'user_id' => auth()->id(),
                'make' => (string) ($newVehicle['make'] ?? ''),
                'model' => (string) ($newVehicle['model'] ?? ''),
                'type' => (string) ($newVehicle['type'] ?? ''),
                'liscense_no' => (string) ($newVehicle['liscense_no'] ?? ''),
                'color' => (string) ($newVehicle['color'] ?? ''),
                'year' => (string) ($newVehicle['year'] ?? ''),
                'car_type' => (string) ($newVehicle['car_type'] ?? ''),
                'primary_vehicle' => $primaryVehicle,
                'image' => $vehicleImageFilename,
                'original_image' => $vehicleImageFilename !== '' ? $vehicleImageFilename : null,
                'remove_image' => '0',
            ]);

            $payload['vehicle_id'] = $createdVehicle->id;
        }

        $payload['stops'] = $this->parseStopsRows((array) $request->input('stops', []));
        if (empty($payload['stops'])) {
            $payload['stops'] = $this->parseStopsText((string) $request->input('stops_text', ''));
        }

        if (!empty($payload['vehicle_id'])) {
            $ownsVehicle = Vehicle::query()
                ->where('id', $payload['vehicle_id'])
                ->where('user_id', auth()->id())
                ->exists();

            if (!$ownsVehicle) {
                return back()
                    ->withInput()
                    ->withErrors(['vehicle_id' => ($siteText['required_field_error_text'] ?? 'This field is required.')]);
            }
        }

        $ride = $service->createRide($payload, $request->user());

        return redirect()
            ->route('px.post_ride.create', ['lang' => optional($this->selectedLanguage)->abbreviation])
            ->with('message', 'PX ride posted successfully. Ride ID: ' . $ride->id);
    }

    protected function parseStopsText(string $stopsText): array
    {
        $lines = preg_split('/\r\n|\r|\n/', $stopsText) ?: [];
        $stops = [];
        foreach ($lines as $line) {
            $label = trim($line);
            if ($label === '') {
                continue;
            }
            $stops[] = [
                'label' => $label,
                'is_pickup' => true,
                'is_dropoff' => true,
            ];
        }
        return $stops;
    }

    protected function parseStopsRows(array $stops): array
    {
        $parsed = [];
        foreach ($stops as $stop) {
            $label = trim((string) ($stop['label'] ?? ''));
            if ($label === '') {
                continue;
            }

            $parsed[] = [
                'city_id' => !empty($stop['city_id']) ? (int) $stop['city_id'] : null,
                'label' => $label,
                'departure_at' => $stop['departure_at'] ?? null,
                'pickup_dropoff_location' => $stop['pickup_dropoff_location'] ?? null,
                'price_delta_minor' => isset($stop['price_delta_minor']) && is_numeric($stop['price_delta_minor']) ? (int) $stop['price_delta_minor'] : 0,
                'is_pickup' => isset($stop['is_pickup']) ? (bool) $stop['is_pickup'] : true,
                'is_dropoff' => isset($stop['is_dropoff']) ? (bool) $stop['is_dropoff'] : true,
            ];
        }

        return $parsed;
    }

    /**
     * Get translated label for an option from an option group
     *
     * @param \App\Models\PxOptionGroup|null $group
     * @param int|null $optionId
     * @param int|null $selectedLangId
     * @param int|null $defaultLangId
     * @param string $defaultLabel
     * @return string
     */
    protected function getOptionLabel($group, $optionId, $selectedLangId, $defaultLangId, $defaultLabel = 'N/A'): string
    {
        if (!$optionId || !$group) {
            return $defaultLabel;
        }

        $option = $group->options->firstWhere('id', $optionId);
        if (!$option) {
            return $defaultLabel;
        }

        $selected = $option->translations->firstWhere('language_id', $selectedLangId);
        $fallback = $option->translations->firstWhere('language_id', $defaultLangId);

        return optional($selected)->label ?: optional($fallback)->label ?: $option->code;
    }

    protected function getOptionCode($group, $optionId, $defaultCode = ''): string
    {
        if (!$optionId || !$group) {
            return (string) $defaultCode;
        }

        $option = $group->options->firstWhere('id', $optionId);
        if (!$option) {
            return (string) $defaultCode;
        }

        return (string) ($option->code ?? $defaultCode);
    }

    public function show($lang = null, $id)
    {
        $user_id = auth()->user()->id;
        $selectedLangId = optional($this->selectedLanguage)->id;
        $defaultLangId = optional($this->defaultLang)->id;

        // Get the PX ride and verify ownership
        $ride = PxRide::where('id', $id)
            ->where('driver_id', $user_id)
            ->with([
                'route',
                'vehicle',
                'stops',
                'options.translations',
                'driver',
                'bookings' => function ($query) {
                    $query->where('status', 'waiting')
                        ->with(['passenger', 'fromStop', 'toStop'])
                        ->latest('id');
                },
            ])
            ->first();

        if (!$ride) {
            return redirect()
                ->route('px.my_rides', ['lang' => optional($this->selectedLanguage)->abbreviation])
                ->with('error', 'Ride not found or you do not have permission to view it.');
        }

        // Add translated labels and descriptions to each ride's options
        $ride->options->transform(function ($option) use ($selectedLangId, $defaultLangId) {
            $selected = $option->translations->firstWhere('language_id', $selectedLangId);
            $fallback = $option->translations->firstWhere('language_id', $defaultLangId);
            $option->display_label = optional($selected)->label ?: optional($fallback)->label ?: $option->code;
            $option->display_description = optional($selected)->description ?: optional($fallback)->description;
            return $option;
        });

        // Load all required option groups in a single query
        $optionGroups = PxOptionGroup::whereIn('code', ['booking_mode', 'booking_method', 'cancelation_policy'])
            ->with(['options' => function ($q) use ($selectedLangId, $defaultLangId) {
                $q->where('is_active', true)
                    ->with(['translations' => function ($tq) use ($selectedLangId, $defaultLangId) {
                        $tq->whereIn('language_id', array_filter([$selectedLangId, $defaultLangId]));
                    }]);
            }])
            ->get()
            ->keyBy('code');

        // Get translated labels using helper method
        $bookingModeLabel = $this->getOptionLabel($optionGroups->get('booking_mode'), $ride->booking_mode, $selectedLangId, $defaultLangId, 'N/A');
        $bookingModeCode = $this->getOptionCode($optionGroups->get('booking_mode'), $ride->booking_mode, '');
        $bookingMethodLabel = $this->getOptionLabel($optionGroups->get('booking_method'), $ride->booking_method, $selectedLangId, $defaultLangId, 'N/A');
        $cancelationPolicyLabel = $this->getOptionLabel($optionGroups->get('cancelation_policy'), $ride->cancelation_policy, $selectedLangId, $defaultLangId, 'Standard');

        $postRidePage = $this->getPostRidePageWithSettingDetail();
        $tripsPage = TripsPageSettingDetail::getByLanguageWithFallback($selectedLangId, $defaultLangId);
        $rideDetailPage = RideDetailPageSettingDetail::getByLanguageWithFallback($selectedLangId, $defaultLangId);

        return view('px.my_ride_detail', [
            'ride' => $ride,
            'postRidePage' => $postRidePage,
            'tripsPage' => $tripsPage,
            'rideDetailPage' => $rideDetailPage,
            'bookingModeLabel' => $bookingModeLabel,
            'bookingModeCode' => $bookingModeCode,
            'bookingMethodLabel' => $bookingMethodLabel,
            'cancelationPolicyLabel' => $cancelationPolicyLabel,
        ]);
    }

    public function approveBookingRequest($lang = null, $id = null, $bookingId = null)
    {
        $driverId = (int) auth()->id();

        $ride = PxRide::query()
            ->where('id', (int) $id)
            ->where('driver_id', $driverId)
            ->first();

        if (!$ride) {
            return redirect()
                ->route('px.my_rides', ['lang' => optional($this->selectedLanguage)->abbreviation])
                ->with('error', 'Ride not found or you do not have permission to manage bookings.');
        }

        $booking = PxBooking::query()
            ->where('id', (int) $bookingId)
            ->where('ride_id', (int) $ride->id)
            ->where('driver_id', $driverId)
            ->first();

        if (!$booking || (string) $booking->status !== 'waiting') {
            return redirect()
                ->route('px.my_ride_detail', ['lang' => optional($this->selectedLanguage)->abbreviation, 'id' => $ride->id])
                ->with('error', 'Booking request is no longer available.');
        }

        $meta = is_array($booking->meta) ? $booking->meta : [];
        $meta['approved_at'] = now()->toDateTimeString();
        $meta['approved_by'] = 'driver_web';
        $booking->meta = $meta;
        $booking->status = 'approved';
        $booking->save();

        return redirect()
            ->route('px.my_ride_detail', ['lang' => optional($this->selectedLanguage)->abbreviation, 'id' => $ride->id])
            ->with('success', 'Booking request approved.');
    }

    public function declineBookingRequest($lang = null, $id = null, $bookingId = null)
    {
        $driverId = (int) auth()->id();

        $ride = PxRide::query()
            ->where('id', (int) $id)
            ->where('driver_id', $driverId)
            ->first();

        if (!$ride) {
            return redirect()
                ->route('px.my_rides', ['lang' => optional($this->selectedLanguage)->abbreviation])
                ->with('error', 'Ride not found or you do not have permission to manage bookings.');
        }

        try {
            DB::transaction(function () use ($ride, $bookingId, $driverId): void {
                $rideForUpdate = PxRide::query()
                    ->where('id', (int) $ride->id)
                    ->lockForUpdate()
                    ->first();

                $booking = PxBooking::query()
                    ->where('id', (int) $bookingId)
                    ->where('ride_id', (int) $ride->id)
                    ->where('driver_id', $driverId)
                    ->lockForUpdate()
                    ->first();

                if (!$rideForUpdate || !$booking || (string) $booking->status !== 'waiting') {
                    throw new \RuntimeException('Booking request is no longer available.');
                }

                $meta = is_array($booking->meta) ? $booking->meta : [];
                $meta['declined_at'] = now()->toDateTimeString();
                $meta['declined_by'] = 'driver_web';
                $booking->meta = $meta;
                $booking->status = 'cancelled';
                $booking->save();

                $newSeatsAvailable = (int) $rideForUpdate->seats_available + (int) $booking->seats;
                $maxSeats = (int) ($rideForUpdate->seats_total ?? 0);
                if ($maxSeats > 0) {
                    $newSeatsAvailable = min($maxSeats, $newSeatsAvailable);
                }
                $rideForUpdate->seats_available = max(0, $newSeatsAvailable);
                $rideForUpdate->save();
            });
        } catch (\RuntimeException $e) {
            return redirect()
                ->route('px.my_ride_detail', ['lang' => optional($this->selectedLanguage)->abbreviation, 'id' => $ride->id])
                ->with('error', $e->getMessage());
        }

        return redirect()
            ->route('px.my_ride_detail', ['lang' => optional($this->selectedLanguage)->abbreviation, 'id' => $ride->id])
            ->with('success', 'Booking request declined.');
    }

    public function rideDetail($lang = null, $id)
    {
        $selectedLangId = optional($this->selectedLanguage)->id;
        $defaultLangId = optional($this->defaultLang)->id;

        $ride = PxRide::query()
            ->published()
            ->where('id', $id)
            ->with(['route', 'vehicle', 'stops', 'options.translations', 'driver'])
            ->first();

        if (!$ride) {
            return redirect()
                ->route('px.search_ride', ['lang' => optional($this->selectedLanguage)->abbreviation])
                ->with('error', 'Ride not found or no longer available.');
        }

        $ride->options->transform(function ($option) use ($selectedLangId, $defaultLangId) {
            $selected = $option->translations->firstWhere('language_id', $selectedLangId);
            $fallback = $option->translations->firstWhere('language_id', $defaultLangId);
            $option->display_label = optional($selected)->label ?: optional($fallback)->label ?: $option->code;
            $option->display_description = optional($selected)->description ?: optional($fallback)->description;
            return $option;
        });

        $optionGroups = PxOptionGroup::whereIn('code', ['booking_mode', 'booking_method', 'cancelation_policy'])
            ->with(['options' => function ($q) use ($selectedLangId, $defaultLangId) {
                $q->where('is_active', true)
                    ->with(['translations' => function ($tq) use ($selectedLangId, $defaultLangId) {
                        $tq->whereIn('language_id', array_filter([$selectedLangId, $defaultLangId]));
                    }]);
            }])
            ->get()
            ->keyBy('code');

        $bookingModeLabel = $this->getOptionLabel($optionGroups->get('booking_mode'), $ride->booking_mode, $selectedLangId, $defaultLangId, 'N/A');
        $bookingModeCode = $this->getOptionCode($optionGroups->get('booking_mode'), $ride->booking_mode, '');
        $bookingMethodLabel = $this->getOptionLabel($optionGroups->get('booking_method'), $ride->booking_method, $selectedLangId, $defaultLangId, 'N/A');
        $cancelationPolicyLabel = $this->getOptionLabel($optionGroups->get('cancelation_policy'), $ride->cancelation_policy, $selectedLangId, $defaultLangId, 'Standard');
        $rideDetailPage = RideDetailPageSettingDetail::getByLanguageWithFallback($selectedLangId, $defaultLangId);
        $orderedStops = $ride->stops ? $ride->stops->sortBy('stop_order')->values()->all() : [];

        $fromStopId = (int) request()->query('from_stop_id', 0);
        $toStopId = (int) request()->query('to_stop_id', 0);
        $hasSegmentContext = ($fromStopId > 0 && $toStopId > 0);

        $displayOrigin = $ride->route->origin_label ?? 'N/A';
        $displayDestination = $ride->route->destination_label ?? 'N/A';
        $displayPriceMinor = (int) ($ride->price_minor ?? 0);
        $displaySegmentStops = collect();
        $isSegmentView = false;
        $selectedFromStopId = null;
        $selectedToStopId = null;

        if ($hasSegmentContext && count($orderedStops) >= 2) {
            $matchedFromIndex = null;
            $matchedToIndex = null;

            foreach ($orderedStops as $idx => $stop) {
                $stopId = (int) ($stop->id ?? 0);
                if ($stopId === $fromStopId) {
                    $matchedFromIndex = $idx;
                }
                if ($stopId === $toStopId) {
                    $matchedToIndex = $idx;
                }
            }
            
            if ($matchedFromIndex !== null && $matchedToIndex !== null && $matchedFromIndex < $matchedToIndex && !($matchedFromIndex === 0 && $matchedToIndex === count($orderedStops) - 1)) {
                $displayOrigin = (string) ($orderedStops[$matchedFromIndex]->label ?? $displayOrigin);
                $displayDestination = (string) ($orderedStops[$matchedToIndex]->label ?? $displayDestination);
                $displayPriceMinor = $this->resolveMatchedSegmentPriceMinor(
                    $ride,
                    null,
                    null,
                    '',
                    '',
                    $matchedFromIndex,
                    $matchedToIndex
                );
                $displaySegmentStops = collect($orderedStops)
                    ->slice($matchedFromIndex + 1, max(0, $matchedToIndex - $matchedFromIndex - 1))
                    ->values();
                $isSegmentView = true;
                $selectedFromStopId = (int) ($orderedStops[$matchedFromIndex]->id ?? 0);
                $selectedToStopId = (int) ($orderedStops[$matchedToIndex]->id ?? 0);
            }
        }

        if (($selectedFromStopId === null || $selectedToStopId === null) && count($orderedStops) >= 2) {
            $selectedFromStopId = (int) ($orderedStops[0]->id ?? 0);
            $selectedToStopId = (int) ($orderedStops[count($orderedStops) - 1]->id ?? 0);
        }

        $existingBooking = null;
        if (auth()->check() && $ride->driver_id !== auth()->id() && $selectedFromStopId && $selectedToStopId) {
            $existingBooking = PxBooking::query()
                ->where('ride_id', (int) $ride->id)
                ->where('passenger_id', (int) auth()->id())
                ->where('from_stop_id', (int) $selectedFromStopId)
                ->where('to_stop_id', (int) $selectedToStopId)
                ->whereNotIn('status', ['cancelled', 'refunded', 'failed'])
                ->latest('id')
                ->first();
        }

        $postRidePage = $this->getPostRidePageWithSettingDetail();

        return view('px.ride_detail', [
            'ride' => $ride,
            'rideDetailPage' => $rideDetailPage,
            'postRidePage' => $postRidePage,
            'bookingModeLabel' => $bookingModeLabel,
            'bookingModeCode' => $bookingModeCode,
            'bookingMethodLabel' => $bookingMethodLabel,
            'cancelationPolicyLabel' => $cancelationPolicyLabel,
            'displayOrigin' => $displayOrigin,
            'displayDestination' => $displayDestination,
            'displayPriceMinor' => $displayPriceMinor,
            'displaySegmentStops' => $displaySegmentStops,
            'isSegmentView' => $isSegmentView,
            'selectedFromStopId' => $selectedFromStopId,
            'selectedToStopId' => $selectedToStopId,
            'existingBooking' => $existingBooking,
        ]);
    }

    public function myTrips($lang = null)
    {
        $userId = auth()->id();
        $tab = request()->query('tab', 'upcoming');

        $baseQuery = PxBooking::query()
            ->where('passenger_id', $userId)
            ->with([
                'ride.route',
                'ride.vehicle',
                'ride.driver',
                'ride.stops',
                'ride.options.translations',
                'fromStop',
                'toStop',
            ]);

        $applyTabFilter = function ($query) use ($tab) {
            if ($tab === 'completed') {
                $query->whereNotIn('status', ['cancelled', 'refunded', 'failed'])
                    ->whereHas('ride', function ($rideQuery) {
                        $rideQuery->where('departure_at', '<', now())
                            ->where('status', '!=', 'cancelled');
                    });
                return;
            }

            if ($tab === 'cancelled') {
                $query->where(function ($cancelledQuery) {
                    $cancelledQuery
                        ->whereIn('status', ['cancelled', 'refunded', 'failed'])
                        ->orWhereHas('ride', function ($rideQuery) {
                            $rideQuery->where('status', 'cancelled');
                        });
                });
                return;
            }

            $query->whereNotIn('status', ['cancelled', 'refunded', 'failed'])
                ->whereHas('ride', function ($rideQuery) {
                    $rideQuery->where('departure_at', '>=', now())
                        ->where('status', '!=', 'cancelled');
                });
        };

        $bookingsQuery = (clone $baseQuery);
        $applyTabFilter($bookingsQuery);
        $bookings = $bookingsQuery
            ->orderByDesc('booked_at')
            ->paginate(10);

        foreach ($bookings as $booking) {
            if (!$booking->ride) {
                continue;
            }

            $orderedStops = $booking->ride->stops
                ? $booking->ride->stops->sortBy('stop_order')->values()->all()
                : [];
            $fromIndex = null;
            $toIndex = null;

            foreach ($orderedStops as $idx => $stop) {
                $stopId = (int) ($stop->id ?? 0);
                if ($stopId === (int) $booking->from_stop_id) {
                    $fromIndex = $idx;
                }
                if ($stopId === (int) $booking->to_stop_id) {
                    $toIndex = $idx;
                }
            }

            $booking->ride->matched_from_stop_index = $fromIndex;
            $booking->ride->matched_to_stop_index = $toIndex;
        }

        $upcomingCount = (clone $baseQuery)
            ->whereNotIn('status', ['cancelled', 'refunded', 'failed'])
            ->whereHas('ride', function ($rideQuery) {
                $rideQuery->where('departure_at', '>=', now())
                    ->where('status', '!=', 'cancelled');
            })
            ->count();

        $completedCount = (clone $baseQuery)
            ->whereNotIn('status', ['cancelled', 'refunded', 'failed'])
            ->whereHas('ride', function ($rideQuery) {
                $rideQuery->where('departure_at', '<', now())
                    ->where('status', '!=', 'cancelled');
            })
            ->count();

        $cancelledCount = (clone $baseQuery)
            ->where(function ($cancelledQuery) {
                $cancelledQuery
                    ->whereIn('status', ['cancelled', 'refunded', 'failed'])
                    ->orWhereHas('ride', function ($rideQuery) {
                        $rideQuery->where('status', 'cancelled');
                    });
            })
            ->count();

        $selectedLangId = optional($this->selectedLanguage)->id;
        $defaultLangId = optional($this->defaultLang)->id;

        foreach ($bookings as $booking) {
            if (!$booking->ride || !$booking->ride->relationLoaded('options')) {
                continue;
            }
            $booking->ride->options->transform(function ($option) use ($selectedLangId, $defaultLangId) {
                $selected = $option->translations->firstWhere('language_id', $selectedLangId);
                $fallback = $option->translations->firstWhere('language_id', $defaultLangId);
                $option->display_label = optional($selected)->label ?: optional($fallback)->label ?: $option->code;
                $option->display_description = optional($selected)->description ?: optional($fallback)->description;
                return $option;
            });
        }

        $tripsPage = TripsPageSettingDetail::getByLanguageWithFallback($selectedLangId, $defaultLangId);

        return view('px.my_trips', [
            'bookings' => $bookings,
            'tripsPage' => $tripsPage,
            'activeTab' => $tab,
            'upcomingCount' => $upcomingCount,
            'completedCount' => $completedCount,
            'cancelledCount' => $cancelledCount,
        ]);
    }

    public function edit($lang = null, $id)
    {
        $user_id = auth()->user()->id;
        $selectedLangId = optional($this->selectedLanguage)->id;
        $defaultLangId = optional($this->defaultLang)->id;

        $isPinkRideDisabled = auth()->user()->isPinkRideDisabled();
        $isExtraRideDisabled = auth()->user()->isFolkRideDisabled();
        

        // Get the PX ride and verify ownership
        $ride = PxRide::where('id', $id)
            ->where('driver_id', $user_id)
            ->with(['route', 'vehicle', 'stops', 'options.translations'])
            ->first();

        if (!$ride) {
            return redirect()
                ->route('px.my_rides', ['lang' => optional($this->selectedLanguage)->abbreviation])
                ->with('error', 'Ride not found or you do not have permission to edit it.');
        }

        // Check if ride can be edited (upcoming and not booked)
        $isUpcoming = $ride->departure_at > now();
        $isUpcomingStatus = in_array($ride->status, ['draft', 'published', 'started']);
        $isNotBooked = $ride->seats_available == $ride->seats_total;

        if (!$isUpcoming || !$isUpcomingStatus || !$isNotBooked) {
            return redirect()
                ->route('px.my_ride_detail', ['lang' => optional($this->selectedLanguage)->abbreviation, 'id' => $ride->id])
                ->with('error', 'This ride cannot be edited. Only upcoming rides without bookings can be edited.');
        }

        // Filter out origin and destination from stops (only intermediate stops should be shown)
        $originLabel = $ride->route->origin_label ?? '';
        $destinationLabel = $ride->route->destination_label ?? '';
        
        $intermediateStops = $ride->stops
            ->filter(function ($stop) use ($originLabel, $destinationLabel) {
                // Exclude stops that match origin or destination
                $stopLabel = trim($stop->label ?? '');
                return $stopLabel !== '' 
                    && strcasecmp($stopLabel, $originLabel) !== 0 
                    && strcasecmp($stopLabel, $destinationLabel) !== 0;
            })
            ->map(function ($stop) {
                $departureAt = $stop->departure_at ?? $stop->eta_at ?? null;
                
                // Format for display in input field (Y-m-d H:i)
                $departureAtFormatted = '';
                if ($departureAt) {
                    try {
                        $dt = \Illuminate\Support\Carbon::parse($departureAt);
                        $departureAtFormatted = $dt->format('Y-m-d H:i');
                    } catch (\Throwable $e) {
                        // Keep empty if parsing fails
                    }
                }
                
                // Use pickup_dropoff_location from database, with fallback for backward compatibility
                $pickupDropoffLocation = $stop->pickup_dropoff_location ?? '';
                if (empty($pickupDropoffLocation)) {
                    // Fallback: combine old separate fields if they exist
                    $pickupLocation = $stop->pickup_location ?? $stop->meta['pickup_location'] ?? '';
                    $dropoffLocation = $stop->dropoff_location ?? $stop->meta['dropoff_location'] ?? '';
                    if (!empty($pickupLocation) && !empty($dropoffLocation)) {
                        $pickupDropoffLocation = $pickupLocation . ' / ' . $dropoffLocation;
                    } else {
                        $pickupDropoffLocation = $pickupLocation ?: $dropoffLocation;
                    }
                }
                
                return [
                    'label' => $stop->label,
                    'city_id' => $stop->city_id,
                    'lat' => $stop->lat,
                    'lng' => $stop->lng,
                    'price_delta_minor' => $stop->price_delta_minor,
                    'is_pickup' => $stop->is_pickup,
                    'is_dropoff' => $stop->is_dropoff,
                    'departure_at' => $departureAtFormatted,
                    'pickup_dropoff_location' => $pickupDropoffLocation,
                ];
            })
            ->values()
            ->toArray();
        
        // Add filtered stops to ride as a property for easy access in view
        $ride->intermediate_stops = $intermediateStops;

        $vehicles = Vehicle::query()
            ->where('user_id', auth()->id())
            ->orderByDesc('primary_vehicle')
            ->orderByDesc('id')
            ->get();

        $optionGroups = PxOptionGroup::query()
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
                $group->options = $group->options->map(function ($option) use ($selectedLangId, $defaultLangId) {
                    $selected = $option->translations->firstWhere('language_id', $selectedLangId);
                    $fallback = $option->translations->firstWhere('language_id', $defaultLangId);
                    $option->display_label = optional($selected)->label ?: optional($fallback)->label ?: $option->code;
                    $option->display_description = optional($selected)->description ?: optional($fallback)->description;
                    return $option;
                });
                return $group;
            });

        $postRidePage = $this->getPostRidePageWithSettingDetail();

        return view('px.post_ride', [
            'ride' => $ride,
            'isPinkRideDisabled' => $isPinkRideDisabled,
            'isExtraRideDisabled' => $isExtraRideDisabled,
            'vehicles' => $vehicles,
            'optionGroups' => $optionGroups,
            'postRidePage' => $postRidePage,
            'isEditMode' => true,
        ]);
    }

    public function update(PxStoreRideRequest $request, PxRideService $service, $lang = null, $id)
    {
        $user_id = auth()->user()->id;
        $siteText = $this->siteText;

        // Get the PX ride and verify ownership
        $ride = PxRide::where('id', $id)
            ->where('driver_id', $user_id)
            ->first();

        if (!$ride) {
            return redirect()
                ->route('px.my_rides', ['lang' => optional($this->selectedLanguage)->abbreviation])
                ->with('error', 'Ride not found or you do not have permission to update it.');
        }

        // Check if ride can be edited (upcoming and not booked)
        $isUpcoming = $ride->departure_at > now();
        $isUpcomingStatus = in_array($ride->status, ['draft', 'published', 'started']);
        $isNotBooked = $ride->seats_available == $ride->seats_total;

        if (!$isUpcoming || !$isUpcomingStatus || !$isNotBooked) {
            return redirect()
                ->route('px.my_ride_detail', ['lang' => optional($this->selectedLanguage)->abbreviation, 'id' => $ride->id])
                ->with('error', 'This ride cannot be updated. Only upcoming rides without bookings can be updated.');
        }

        $payload = $request->validated();
        $vehicleMode = (string) ($payload['vehicle_mode'] ?? '');

        if ($vehicleMode === 'skip') {
            $payload['vehicle_id'] = null;
        } elseif ($vehicleMode === 'add_new') {
            $newVehicle = (array) ($payload['new_vehicle'] ?? []);
            $primaryVehicle = (string) ($newVehicle['primary_vehicle'] ?? '0');
            $vehicleImageFilename = '';

            if ($request->hasFile('new_vehicle_image')) {
                $file = $request->file('new_vehicle_image');
                $vehicleImageFilename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('/car_images'), $vehicleImageFilename);
            }

            if ($primaryVehicle === '1') {
                Vehicle::query()
                    ->where('user_id', auth()->id())
                    ->update(['primary_vehicle' => 0]);
            }

            $createdVehicle = Vehicle::query()->create([
                'user_id' => auth()->id(),
                'make' => (string) ($newVehicle['make'] ?? ''),
                'model' => (string) ($newVehicle['model'] ?? ''),
                'type' => (string) ($newVehicle['type'] ?? ''),
                'liscense_no' => (string) ($newVehicle['liscense_no'] ?? ''),
                'color' => (string) ($newVehicle['color'] ?? ''),
                'year' => (string) ($newVehicle['year'] ?? ''),
                'car_type' => (string) ($newVehicle['car_type'] ?? ''),
                'primary_vehicle' => $primaryVehicle,
                'image' => $vehicleImageFilename,
                'original_image' => $vehicleImageFilename !== '' ? $vehicleImageFilename : null,
                'remove_image' => '0',
            ]);

            $payload['vehicle_id'] = $createdVehicle->id;
        }

        $payload['stops'] = $this->parseStopsRows((array) $request->input('stops', []));
        if (empty($payload['stops'])) {
            $payload['stops'] = $this->parseStopsText((string) $request->input('stops_text', ''));
        }

        if (!empty($payload['vehicle_id'])) {
            $ownsVehicle = Vehicle::query()
                ->where('id', $payload['vehicle_id'])
                ->where('user_id', auth()->id())
                ->exists();

            if (!$ownsVehicle) {
                return back()
                    ->withInput()
                    ->withErrors(['vehicle_id' => ($siteText['required_field_error_text'] ?? 'This field is required.')]);
            }
        }

        $updatedRide = $service->updateRide($ride, $payload, $request->user());

        return redirect()
            ->route('px.my_ride_detail', ['lang' => optional($this->selectedLanguage)->abbreviation, 'id' => $updatedRide->id])
            ->with('success', 'PX ride updated successfully.');
    }

    public function search(Request $request, PxRideService $service, $lang = null)
    {
        $selectedLangId = optional($this->selectedLanguage)->id;
        $defaultLangId = optional($this->defaultLang)->id;
        
        $findRidePage = $this->getFindRidePageWithSettingDetail();
        $postRidePage = $this->getPostRidePageWithSettingDetail();
        
        $rides = $service->searchRides([
            'per_page' => 20,
            'sort' => 'latest_added',
        ], auth()->user());
        $hasSearch = false;
        
        // Check if there are search parameters
        $originLabel = $request->input('origin.label');
        $destinationLabel = $request->input('destination.label');
        $departureDate = $request->input('departure_date');
        
        if (!empty($originLabel) && !empty($destinationLabel)) {
            $hasSearch = true;
            
            // Validate request if search parameters are present
            $request->validate([
                'origin.label' => ['required', 'string', 'max:160'],
                'destination.label' => ['required', 'string', 'max:160'],
                'departure_date' => ['nullable', 'date'],
                'origin.city_id' => ['nullable', 'integer', 'exists:cities,id'],
                'destination.city_id' => ['nullable', 'integer', 'exists:cities,id'],
            ]);
            
            // Prepare filters for search
            $filters = [
                'origin_city_id' => $request->input('origin.city_id'),
                'destination_city_id' => $request->input('destination.city_id'),
                'origin_label' => $originLabel,
                'destination_label' => $destinationLabel,
                'per_page' => 20,
                'sort' => 'latest_added',
            ];

            if (!empty($departureDate)) {
                $filters['departure_date'] = $departureDate;
            }
            
            // Perform filtered search
            $rides = $service->searchRides($filters, auth()->user());
        }

        $originCityId = $request->input('origin.city_id');
        $destinationCityId = $request->input('destination.city_id');

        foreach ($rides as $ride) {
            $ride->options->transform(function ($option) use ($selectedLangId, $defaultLangId) {
                $selected = $option->translations->firstWhere('language_id', $selectedLangId);
                $fallback = $option->translations->firstWhere('language_id', $defaultLangId);
                $option->display_label = optional($selected)->label ?: optional($fallback)->label ?: $option->code;
                $option->display_description = optional($selected)->description ?: optional($fallback)->description;
                return $option;
            });

            $orderedStops = $ride->stops
                ? $ride->stops->sortBy('stop_order')->values()->all()
                : [];

            if ($hasSearch) {
                [$matchedFromIndex, $matchedToIndex] = $this->findMatchingStopPair(
                    $orderedStops,
                    $originCityId,
                    $destinationCityId,
                    (string) $originLabel,
                    (string) $destinationLabel
                );
            } else {
                $matchedFromIndex = count($orderedStops) >= 2 ? 0 : null;
                $matchedToIndex = count($orderedStops) >= 2 ? count($orderedStops) - 1 : null;
            }

            $ride->matched_from_stop_index = $matchedFromIndex;
            $ride->matched_to_stop_index = $matchedToIndex;
            $ride->matched_from_stop_id = ($matchedFromIndex !== null && isset($orderedStops[$matchedFromIndex]))
                ? (int) ($orderedStops[$matchedFromIndex]->id ?? 0)
                : null;
            $ride->matched_to_stop_id = ($matchedToIndex !== null && isset($orderedStops[$matchedToIndex]))
                ? (int) ($orderedStops[$matchedToIndex]->id ?? 0)
                : null;
            $ride->matched_segment_price_minor = $this->resolveMatchedSegmentPriceMinor(
                $ride,
                $originCityId,
                $destinationCityId,
                (string) $originLabel,
                (string) $destinationLabel,
                $matchedFromIndex,
                $matchedToIndex
            );
        }
        
        return view('px.search_ride', [
            'findRidePage' => $findRidePage,
            'postRidePage' => $postRidePage,
            'rides' => $rides,
            'hasSearch' => $hasSearch,
            'oldOriginLabel' => old('origin.label', $originLabel),
            'oldOriginCityId' => old('origin.city_id', $request->input('origin.city_id')),
            'oldDestinationLabel' => old('destination.label', $destinationLabel),
            'oldDestinationCityId' => old('destination.city_id', $request->input('destination.city_id')),
            'oldDepartureDate' => old('departure_date', $departureDate),
        ]);
    }

    protected function resolveMatchedSegmentPriceMinor(PxRide $ride, $fromCityId, $toCityId, string $fromLabel, string $toLabel, $fromIndex = null, $toIndex = null): int
    {
        $stops = $ride->stops
            ? $ride->stops->sortBy('stop_order')->values()->all()
            : [];

        if (count($stops) < 2) {
            return (int) ($ride->price_minor ?? 0);
        }

        if ($fromIndex === null || $toIndex === null) {
            [$fromIndex, $toIndex] = $this->findMatchingStopPair($stops, $fromCityId, $toCityId, $fromLabel, $toLabel);
        }

        if ($fromIndex === null || $toIndex === null || $fromIndex >= $toIndex) {
            return (int) ($ride->price_minor ?? 0);
        }

        $lastIndex = count($stops) - 1;
        $totalPriceMinor = (int) ($ride->price_minor ?? 0);
        $intermediateLegsSum = 0;

        foreach ($stops as $idx => $stop) {
            if ($idx === 0 || $idx === $lastIndex) {
                continue;
            }
            $intermediateLegsSum += (int) ($stop->price_delta_minor ?? 0);
        }

        $storedFinalLegPrice = (int) ($stops[$lastIndex]->price_delta_minor ?? 0);
        $finalLegPrice = $storedFinalLegPrice > 0
            ? $storedFinalLegPrice
            : max(0, $totalPriceMinor - $intermediateLegsSum);
        $segmentPriceMinor = 0;

        for ($i = $fromIndex; $i < $toIndex; $i++) {
            $destIdx = $i + 1;
            $segmentPriceMinor += ($destIdx === $lastIndex)
                ? $finalLegPrice
                : (int) ($stops[$destIdx]->price_delta_minor ?? 0);
        }

        return max(0, $segmentPriceMinor);
    }

    protected function findMatchingStopPair(array $stops, $fromCityId, $toCityId, string $fromLabel, string $toLabel): array
    {
        $fromCandidates = [];
        $toCandidates = [];

        foreach ($stops as $idx => $stop) {
            if ($this->stopMatches($stop, $fromCityId, $fromLabel)) {
                $fromCandidates[] = $idx;
            }
            if ($this->stopMatches($stop, $toCityId, $toLabel)) {
                $toCandidates[] = $idx;
            }
        }

        foreach ($fromCandidates as $fromIdx) {
            foreach ($toCandidates as $toIdx) {
                if ($toIdx > $fromIdx) {
                    return [$fromIdx, $toIdx];
                }
            }
        }

        return [null, null];
    }

    protected function stopMatches($stop, $cityId, string $label): bool
    {
        if (!empty($cityId)) {
            return (int) ($stop->city_id ?? 0) === (int) $cityId;
        }

        $needle = mb_strtolower(trim($label));
        if ($needle === '') {
            return false;
        }

        $haystack = mb_strtolower((string) ($stop->label ?? ''));
        return str_contains($haystack, $needle);
    }
}
