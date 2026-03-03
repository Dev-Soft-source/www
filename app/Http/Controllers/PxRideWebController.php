<?php

namespace App\Http\Controllers;

use App\Http\Requests\Px\PxStoreRideRequest;
use App\Models\PxOptionGroup;
use App\Models\PxRide;
use App\Models\Vehicle;
use App\Models\TripsPageSettingDetail;
use App\Models\RideDetailPageSettingDetail;
use App\Services\PxRideService;
use Illuminate\Http\Request;

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
        $rides->getCollection()->transform(function ($ride) use ($selectedLangId, $defaultLangId) {
            $ride->options->transform(function ($option) use ($selectedLangId, $defaultLangId) {
                $selected = $option->translations->firstWhere('language_id', $selectedLangId);
                $fallback = $option->translations->firstWhere('language_id', $defaultLangId);
                $option->display_label = optional($selected)->label ?: optional($fallback)->label ?: $option->code;
                $option->display_description = optional($selected)->description ?: optional($fallback)->description;
                return $option;
            });
            return $ride;
        });

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

    public function show($lang = null, $id)
    {
        $user_id = auth()->user()->id;
        $selectedLangId = optional($this->selectedLanguage)->id;
        $defaultLangId = optional($this->defaultLang)->id;

        // Get the PX ride and verify ownership
        $ride = PxRide::where('id', $id)
            ->where('driver_id', $user_id)
            ->with(['route', 'vehicle', 'stops', 'options.translations', 'driver'])
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
            'bookingMethodLabel' => $bookingMethodLabel,
            'cancelationPolicyLabel' => $cancelationPolicyLabel,
        ]);
    }

    public function edit($lang = null, $id)
    {
        $user_id = auth()->user()->id;
        $selectedLangId = optional($this->selectedLanguage)->id;
        $defaultLangId = optional($this->defaultLang)->id;

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
                return [
                    'label' => $stop->label,
                    'city_id' => $stop->city_id,
                    'lat' => $stop->lat,
                    'lng' => $stop->lng,
                    'is_pickup' => $stop->is_pickup,
                    'is_dropoff' => $stop->is_dropoff,
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
        
        $rides = null;
        $hasSearch = false;
        
        // Check if there are search parameters
        $originLabel = $request->input('origin.label');
        $destinationLabel = $request->input('destination.label');
        $departureDate = $request->input('departure_date');
        
        if (!empty($originLabel) && !empty($destinationLabel) && !empty($departureDate)) {
            $hasSearch = true;
            
            // Validate request if search parameters are present
            $validated = $request->validate([
                'origin.label' => ['required', 'string', 'max:160'],
                'destination.label' => ['required', 'string', 'max:160'],
                'departure_date' => ['required', 'date'],
                'origin.city_id' => ['nullable', 'integer', 'exists:cities,id'],
                'destination.city_id' => ['nullable', 'integer', 'exists:cities,id'],
            ]);
            
            // Prepare filters for search
            $filters = [
                'origin_city_id' => $request->input('origin.city_id'),
                'destination_city_id' => $request->input('destination.city_id'),
                'origin_label' => $originLabel,
                'destination_label' => $destinationLabel,
                'departure_date' => $departureDate,
                'per_page' => 20,
                'sort' => 'soonest',
            ];
            
            // Perform search
            $rides = $service->searchRides($filters, auth()->user());
            
            // Add translated labels to ride options
            $rides->getCollection()->transform(function ($ride) use ($selectedLangId, $defaultLangId) {
                $ride->options->transform(function ($option) use ($selectedLangId, $defaultLangId) {
                    $selected = $option->translations->firstWhere('language_id', $selectedLangId);
                    $fallback = $option->translations->firstWhere('language_id', $defaultLangId);
                    $option->display_label = optional($selected)->label ?: optional($fallback)->label ?: $option->code;
                    $option->display_description = optional($selected)->description ?: optional($fallback)->description;
                    return $option;
                });
                return $ride;
            });
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
}
