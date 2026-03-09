<?php

namespace App\Http\Controllers;

use App\Http\Requests\Px\PxStoreRideRequest;
use App\Models\PxBooking;
use App\Models\PxOption;
use App\Models\PxOptionGroup;
use App\Models\PxRide;
use App\Models\Vehicle;
use App\Models\TripsPageSettingDetail;
use App\Models\RideDetailPageSettingDetail;
use App\Models\SuccessMessagesSettingDetail;
use App\Models\PinkRideSetting;
use App\Models\FolkRideSetting;
use App\Models\PostRidePageSettingDetail;
use App\Models\User;
use App\Models\NoShowHistory;
use App\Models\CancellationHistory;
use App\Models\Rating;
use App\Models\PhoneNumber;
use App\Models\City;
use App\Models\State;
use App\Models\SeatDetail;
use App\Models\PxRideStop;
use App\Models\RecentSearch;
use App\Models\ExtraCareFaq;
use App\Models\ExtraCareFaqDetail;
use App\Models\PinkRideFaqDetail;
use App\Models\ProfilePageSettingDetail;
use App\Models\ProfileSettingDetail;
use App\Models\MyReviewSettingDetail;
use App\Services\PxRideService;
use App\Services\FCMService;
use App\Models\Notification;
use App\Models\FCMToken;
use App\Mail\RidePostedMail;
use App\Mail\PinkRideMail;
use App\Mail\ExtraCareRideMail;
use App\Mail\PinkExtraCareRideMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class PxRideWebController extends Controller
{
    /**
     * Cached success messages for validation
     *
     * @var \App\Models\SuccessMessagesSettingDetail|null
     */
    protected $successMessages = null;

    public function postRideAgainUpcoming($lang = null)
    {
        return $this->renderPostRideAgainPage('upcoming');
    }

    public function postRideAgainCompleted($lang = null)
    {
        return $this->renderPostRideAgainPage('completed');
    }

    public function postRideAgainCancelled($lang = null)
    {
        return $this->renderPostRideAgainPage('cancelled');
    }

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

        $ProfilePage = ProfilePageSettingDetail::where('language_id', $this->selectedLanguage->id)->first();
        $ProfileSetting = ProfileSettingDetail::where('language_id', $this->selectedLanguage->id)->first();
        $reviewSetting = MyReviewSettingDetail::where('language_id', $this->selectedLanguage->id)->select('review_left_label', 'review_received_label')->first();


        return view('px.my_rides', [
            'rides' => $rides,
            'postRidePage' => $postRidePage,
            'tripsPage' => $tripsPage,
            'activeTab' => $tab,
            'upcomingCount' => $upcomingCount,
            'completedCount' => $completedCount,
            'cancelledCount' => $cancelledCount,
            'reviewSetting' => $reviewSetting,
            'ProfilePage' => $ProfilePage,
            'ProfileSetting' => $ProfileSetting,
        ]);
    }

    protected function renderPostRideAgainPage(string $tab)
    {
        $userId = auth()->id();
        $selectedLangId = optional($this->selectedLanguage)->id;
        $defaultLangId = optional($this->defaultLang)->id;

        $query = PxRide::query()
            ->where('driver_id', $userId)
            ->with(['route', 'vehicle', 'stops']);

        switch ($tab) {
            case 'completed':
                $query->where(function ($query) {
                    $query->where('status', 'completed')
                        ->orWhere(function ($query) {
                            $query->where('status', '!=', 'completed')
                                ->where('departure_at', '<', now());
                        });
                })->orderBy('departure_at', 'desc');
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
        $postRidePage = $this->getPostRidePageWithSettingDetail();
        $tripsPage = TripsPageSettingDetail::getByLanguageWithFallback($selectedLangId, $defaultLangId);

        return view('px.post_ride_again', [
            'rides' => $rides,
            'postRidePage' => $postRidePage,
            'tripsPage' => $tripsPage,
            'activeTab' => $tab,
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

    public function copy($lang = null, $id = null)
    {
        $userId = auth()->id();
        $selectedLangId = optional($this->selectedLanguage)->id;
        $defaultLangId = optional($this->defaultLang)->id;

        $isPinkRideDisabled = auth()->user()->isPinkRideDisabled();
        $isExtraRideDisabled = auth()->user()->isFolkRideDisabled();

        $ride = PxRide::query()
            ->where('id', $id)
            ->where('driver_id', $userId)
            ->with(['route', 'vehicle', 'stops', 'options.translations'])
            ->first();

        if (!$ride) {
            return redirect()
                ->route('px.post_ride_again', ['lang' => optional($this->selectedLanguage)->abbreviation])
                ->with('error', 'Ride not found or you do not have permission to copy it.');
        }

        $originLabel = $ride->route->origin_label ?? '';
        $destinationLabel = $ride->route->destination_label ?? '';

        $intermediateStops = $ride->stops
            ->filter(function ($stop) use ($originLabel, $destinationLabel) {
                $stopLabel = trim($stop->label ?? '');
                return $stopLabel !== ''
                    && strcasecmp($stopLabel, $originLabel) !== 0
                    && strcasecmp($stopLabel, $destinationLabel) !== 0;
            })
            ->map(function ($stop) {
                $departureAt = $stop->departure_at ?? $stop->eta_at ?? null;
                $departureAtFormatted = '';

                if ($departureAt) {
                    try {
                        $dt = \Illuminate\Support\Carbon::parse($departureAt);
                        $departureAtFormatted = $dt->format('Y-m-d H:i');
                    } catch (\Throwable $e) {
                    }
                }

                $pickupDropoffLocation = $stop->pickup_dropoff_location ?? '';
                if (empty($pickupDropoffLocation)) {
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

        $ride->intermediate_stops = $intermediateStops;
        $ride->departure_at = null;

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
            'isExtraRideDisabled' => $isExtraRideDisabled,
            'isPinkRideDisabled' => $isPinkRideDisabled,
            'optionGroups' => $optionGroups,
            'postRidePage' => $postRidePage,
            'isCopyMode' => true,
        ]);
    }

    public function store(PxStoreRideRequest $request, PxRideService $service, $lang = null)
    {
        $validationResponse = $this->validatePostRidePermissions($request->user());
        if ($validationResponse) {
            return $validationResponse;
        }

        $featureValidationResponse = $this->validateFeatureEligibility($request, $request->user());
        if ($featureValidationResponse) {
            return $featureValidationResponse;
        }
        $cityValidationResponse = $this->validateCities($request);
        if ($cityValidationResponse) {
            return $cityValidationResponse;
        }

        $stateLimitResponse = $this->validateStateRideLimit($request, $request->user());
        if ($stateLimitResponse) {
            return $stateLimitResponse;
        }

        $duplicateResponse = $this->validateDuplicateDateTime($request, $request->user());
        if ($duplicateResponse) {
            return $duplicateResponse;
        }

        $siteText = $this->siteText;

        $payload = $request->validated();


        // Calculate distance and duration from Google Distance Matrix API
        // $originLabel = $payload['origin']['label'] ?? '';
        // $destinationLabel = $payload['destination']['label'] ?? '';

        // if (!empty($originLabel) && !empty($destinationLabel)) {
        //     $googleApiData = $this->getDataFromGoogleApi($originLabel, $destinationLabel);
        //     if (isset($googleApiData) && !empty($googleApiData)) {
        //         // Check element status first before accessing distance/duration
        //         $elementStatus = isset($googleApiData['rows']) &&
        //             isset($googleApiData['rows'][0]) &&
        //             isset($googleApiData['rows'][0]['elements']) &&
        //             isset($googleApiData['rows'][0]['elements'][0]) &&
        //             isset($googleApiData['rows'][0]['elements'][0]['status'])
        //             ? $googleApiData['rows'][0]['elements'][0]['status']
        //             : null;

        //         if ($elementStatus === 'OK') {
        //             $distanceMeters = isset($googleApiData['rows'][0]['elements'][0]['distance']['value'])
        //                 ? (int) $googleApiData['rows'][0]['elements'][0]['distance']['value']
        //                 : 0;
        //             $durationSeconds = isset($googleApiData['rows'][0]['elements'][0]['duration']['value'])
        //                 ? (int) $googleApiData['rows'][0]['elements'][0]['duration']['value']
        //                 : 0;

        //             $payload['distance_meters'] = $distanceMeters;
        //             $payload['duration_seconds'] = $durationSeconds;

        //             Log::info('Google API data added to payload (PX Ride)', [
        //                 'user_id' => auth()->id(),
        //                 'distance_meters' => $distanceMeters,
        //                 'duration_seconds' => $durationSeconds,
        //             ]);
        //         } else {
        //             // Log error if API returns an error status
        //             Log::error('Google API returned error status (PX Ride)', [
        //                 'user_id' => auth()->id(),
        //                 'status' => $elementStatus,
        //             ]);
        //         }
        //     }
        // }
        // // Cost-sharing cap validation: Price per seat validation
        // $costSharingValidationResponse = $this->validateCostSharingCap($request, $payload);
        // if ($costSharingValidationResponse) {
        //     return $costSharingValidationResponse;
        // }

        $this->processVehicleMode($request, $payload);
        $this->processStops($request, $payload);

        $vehicleValidationError = $this->validateVehicleOwnership($payload, $siteText);
        if ($vehicleValidationError) {
            return $vehicleValidationError;
        }

        $ride = $service->createRide($payload, $request->user());

        // Create in-app notification and send FCM push notifications
        $this->createRideNotificationAndPush($ride, $request->user());

        // Queue email notification if enabled
        $this->queueRideEmailNotification($ride, $request->user());

        // Handle recurring rides if enabled
        if (!empty($payload['is_recurring']) && $payload['is_recurring']) {
            $this->createRecurringRides($ride, $payload, $request->user());
        }

        // Determine language for redirect - use route parameter, then selectedLanguage, then default
        $redirectLang = $lang ?? optional($this->selectedLanguage)->abbreviation ?? optional($this->defaultLang)->abbreviation;

        return redirect()
            ->route('px.my_rides', ['lang' => $redirectLang])
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
     * Process vehicle mode and handle vehicle creation if needed
     *
     * @param \Illuminate\Http\Request $request
     * @param array &$payload Reference to payload array to modify
     * @return void
     */
    protected function processVehicleMode(Request $request, array &$payload): void
    {
        $vehicleMode = (string) ($payload['vehicle_mode'] ?? '');

        if ($vehicleMode === 'skip') {
            $payload['vehicle_id'] = null;
            return;
        }

        if ($vehicleMode === 'add_new') {
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
    }

    /**
     * Process stops from request and add to payload
     *
     * @param \Illuminate\Http\Request $request
     * @param array &$payload Reference to payload array to modify
     * @return void
     */
    protected function processStops(Request $request, array &$payload): void
    {
        $payload['stops'] = $this->parseStopsRows((array) $request->input('stops', []));
        if (empty($payload['stops'])) {
            $payload['stops'] = $this->parseStopsText((string) $request->input('stops_text', ''));
        }
    }

    public function segmentDistanceEstimates(Request $request, $lang = null)
    {
        $validator = Validator::make($request->all(), [
            'point_labels' => ['required', 'array', 'min:2', 'max:22'],
            'point_labels.*' => ['required', 'string', 'max:160'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid route points.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $pointLabels = array_values(array_map(
            fn($label) => trim((string) $label),
            (array) $request->input('point_labels', [])
        ));

        $legDistancesMeters = [];
        $segmentDistancesMeters = [];
        $resolvedLegs = 0;
        $resolvedSegments = 0;
        $pointCount = count($pointLabels);

        for ($fromIndex = 0; $fromIndex < $pointCount - 1; $fromIndex++) {
            for ($toIndex = $fromIndex + 1; $toIndex < $pointCount; $toIndex++) {
                $from = $pointLabels[$fromIndex] ?? '';
                $to = $pointLabels[$toIndex] ?? '';
                $distanceMeters = 0;

                if ($from !== '' && $to !== '') {
                    $googleApiData = $this->getDataFromGoogleApi($from, $to);
                    $elementStatus = isset($googleApiData['rows'][0]['elements'][0]['status'])
                        ? $googleApiData['rows'][0]['elements'][0]['status']
                        : null;

                    if ($elementStatus === 'OK') {
                        $distanceMeters = isset($googleApiData['rows'][0]['elements'][0]['distance']['value'])
                            ? (int) $googleApiData['rows'][0]['elements'][0]['distance']['value']
                            : 0;
                    }
                }

                if ($toIndex === $fromIndex + 1) {
                    if ($distanceMeters > 0) {
                        $resolvedLegs++;
                    }
                    $legDistancesMeters[] = $distanceMeters;
                }

                if ($distanceMeters > 0) {
                    $resolvedSegments++;
                }

                $segmentDistancesMeters["{$fromIndex}:{$toIndex}"] = $distanceMeters;
            }
        }

        return response()->json([
            'leg_distances_meters' => $legDistancesMeters,
            'total_distance_meters' => array_sum($legDistancesMeters),
            'resolved_legs' => $resolvedLegs,
            'segment_distances_meters' => $segmentDistancesMeters,
            'resolved_segments' => $resolvedSegments,
        ]);
    }

    /**
     * Validate that the user owns the selected vehicle
     *
     * @param array $payload
     * @param array $siteText
     * @return \Illuminate\Http\RedirectResponse|null Returns redirect response if validation fails, null otherwise
     */
    protected function validateVehicleOwnership(array $payload, array $siteText)
    {
        if (empty($payload['vehicle_id'])) {
            return null;
        }

        $ownsVehicle = Vehicle::query()
            ->where('id', $payload['vehicle_id'])
            ->where('user_id', auth()->id())
            ->exists();

        if (!$ownsVehicle) {
            return back()
                ->withInput()
                ->withErrors(['vehicle_id' => ($siteText['required_field_error_text'] ?? 'This field is required.')]);
        }

        return null;
    }

    /**
     * Create recurring rides based on the initial ride
     *
     * @param \App\Models\PxRide $initialRide
     * @param array $payload
     * @param \App\Models\User $driver
     * @return void
     */
    protected function createRecurringRides(PxRide $initialRide, array $payload, User $driver): void
    {
        $recurringFrequency = $payload['recurring_frequency'] ?? 'daily';
        $recurringTrips = (int) ($payload['recurring_trips'] ?? 0);

        if ($recurringTrips <= 0) {
            return;
        }

        // Wrap in transaction for data integrity
        DB::transaction(function () use ($initialRide, $payload, $driver, $recurringFrequency, $recurringTrips) {
            // Get the initial departure date
            $initialDepartureAt = Carbon::parse($initialRide->departure_at);
            $initialArrivalAt = $initialRide->arrival_estimated_at ? Carbon::parse($initialRide->arrival_estimated_at) : null;

            // Get original stops to copy
            $originalStops = $initialRide->stops()->orderBy('stop_order')->get();

            // Get original options to copy
            $originalOptionIds = $initialRide->options()->pluck('id')->toArray();

            // Create recurring rides
            for ($i = 1; $i <= $recurringTrips; $i++) {
                // Calculate next departure date (multiply interval by iteration number)
                $daysToAdd = $recurringFrequency === 'weekly' ? $i * 7 : $i;
                $nextDepartureAt = (clone $initialDepartureAt)->addDays($daysToAdd);
                $nextArrivalAt = $initialArrivalAt ? (clone $initialArrivalAt)->addDays($daysToAdd) : null;

                // Create recurring ride payload
                $recurringPayload = [
                    'route_id' => $initialRide->route_id,
                    'driver_id' => $driver->id,
                    'vehicle_id' => $initialRide->vehicle_id,
                    'departure_at' => $nextDepartureAt->format('Y-m-d H:i'),
                    'arrival_estimated_at' => $nextArrivalAt ? $nextArrivalAt->format('Y-m-d H:i') : null,
                    'boarding_window_minutes' => $initialRide->boarding_window_minutes,
                    'seats_total' => $initialRide->seats_total,
                    'seats_available' => $initialRide->seats_total,
                    'price_minor' => $initialRide->price_minor,
                    'currency' => $initialRide->currency,
                    'status' => $initialRide->status,
                    'visibility' => $initialRide->visibility,
                    'booking_mode' => $initialRide->booking_mode,
                    'booking_method' => $initialRide->booking_method,
                    'allow_detour' => $initialRide->allow_detour,
                    'women_only' => $initialRide->women_only,
                    'extra_care' => $initialRide->extra_care,
                    'smoking_allowed' => $initialRide->smoking_allowed,
                    'pets_allowed' => $initialRide->pets_allowed,
                    'luggage_size' => $initialRide->luggage_size,
                    'cancelation_policy' => $initialRide->cancelation_policy,
                    'notes' => $initialRide->notes,
                    'meta' => array_merge($initialRide->meta ?? [], ['recurring_id' => $initialRide->id]),
                ];

                // Create the recurring ride
                $recurringRide = PxRide::query()->create($recurringPayload);

                // Copy stops with adjusted times
                foreach ($originalStops as $originalStop) {
                    $stopEtaAt = $originalStop->eta_at ? Carbon::parse($originalStop->eta_at) : null;
                    $nextStopEtaAt = $stopEtaAt ? (clone $stopEtaAt)->addDays($daysToAdd) : null;

                    $recurringRide->stops()->create([
                        'stop_order' => $originalStop->stop_order,
                        'city_id' => $originalStop->city_id,
                        'label' => $originalStop->label,
                        'lat' => $originalStop->lat,
                        'lng' => $originalStop->lng,
                        'eta_at' => $nextStopEtaAt,
                        'price_delta_minor' => $originalStop->price_delta_minor,
                        'seats_available' => $originalStop->seats_available,
                        'is_pickup' => $originalStop->is_pickup,
                        'is_dropoff' => $originalStop->is_dropoff,
                        'pickup_dropoff_location' => $originalStop->pickup_dropoff_location,
                    ]);
                }

                // Create seat details for recurring ride
                for ($seatNum = 1; $seatNum <= $recurringRide->seats_total; $seatNum++) {
                    SeatDetail::create([
                        'ride_id' => $recurringRide->id,
                        'seat_number' => $seatNum,
                        'status' => 'pending',
                    ]);
                }

                // Copy options
                if (!empty($originalOptionIds)) {
                    $recurringRide->options()->sync($originalOptionIds);
                }

                // Create in-app notification and send FCM push notifications for recurring ride
                $this->createRideNotificationAndPush($recurringRide, $driver);

                // Queue email notification for recurring ride
                $this->queueRideEmailNotification($recurringRide, $driver);
            }
        });
    }

    /**
     * Create in-app notification and send FCM push notifications
     *
     * @param \App\Models\PxRide $ride
     * @param \App\Models\User $user
     * @return void
     */
    protected function createRideNotificationAndPush(PxRide $ride, User $user): void
    {
        // Reload ride with relationships
        $ride->load(['route', 'vehicle']);

        // Check if user has a vehicle
        $hasVehicle = !empty($ride->vehicle_id);

        // Determine ride type and message
        $isPinkRide = $ride->women_only === true || $ride->women_only === 1;
        $isExtraCareRide = $ride->extra_care === true || $ride->extra_care === 1;

        // Build notification message based on ride type
        if ($isPinkRide && $isExtraCareRide) {
            $message = $hasVehicle ? 'Your Pink and Extra+ PX ride is now live on ProximaRide' : 'Add your vehicle to make your Pink and Extra+ PX ride live';
        } elseif ($isPinkRide) {
            $message = $hasVehicle ? 'Your Pink PX ride is now live on ProximaRide' : 'Add your vehicle to make your Pink PX ride live';
        } elseif ($isExtraCareRide) {
            $message = $hasVehicle ? 'Your Extra+ PX ride is now live on ProximaRide' : 'Add your vehicle to make your Extra+ PX ride live';
        } else {
            $message = $hasVehicle ? 'Your PX ride is now live on ProximaRide' : 'Add your vehicle to make your PX ride live';
        }

        // Get origin and destination from route
        $origin = $ride->route->origin_label ?? 'N/A';
        $destination = $ride->route->destination_label ?? 'N/A';

        // Create in-app notification
        $notification = Notification::create([
            'type' => null,
            'ride_id' => null, // PX rides don't use regular ride_id
            'posted_by' => $user->id,
            'receiver_id' => $user->id,
            'message' => $message,
            'status' => 'upcoming',
            'notification_type' => 'px_ride_posted',
            'category' => 'px_ride',
            'departure' => $origin,
            'destination' => $destination,
        ]);

        // Send FCM push notifications
        $fcmService = new FCMService();
        $body = $notification->message;

        // Send to user's mobile FCM token
        $fcmToken = $user->mobile_fcm_token;
        if ($fcmToken) {
            try {
                $fcmService->sendNotification($fcmToken, $body, 'notify', null, 'PX Ride Posted');
            } catch (\Exception $e) {
                Log::error('FCM Notification failed for mobile_fcm_token (PX Ride)', [
                    'user_id' => $user->id,
                    'ride_id' => $ride->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Send to all saved FCM tokens for the user
        $fcmTokens = FCMToken::where('user_id', $user->id)->get();
        foreach ($fcmTokens as $fcmTokenRecord) {
            try {
                $fcmService->sendNotification($fcmTokenRecord->token, $body, 'notify', null, 'PX Ride Posted');
            } catch (\Exception $e) {
                Log::error('FCM Notification failed for saved token (PX Ride)', [
                    'user_id' => $user->id,
                    'ride_id' => $ride->id,
                    'token_id' => $fcmTokenRecord->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Queue email notification for ride creation
     *
     * @param \App\Models\PxRide $ride
     * @param \App\Models\User $user
     * @return void
     */
    protected function queueRideEmailNotification(PxRide $ride, User $user): void
    {
        // Check if user has email notifications enabled
        if (!isset($user->email_notification) || $user->email_notification != 1) {
            return;
        }

        // Reload ride with relationships
        $ride->load(['route', 'stops']);

        // Get origin and destination from route
        $origin = $ride->route->origin_label ?? 'N/A';
        $destination = $ride->route->destination_label ?? 'N/A';

        // Format departure date and time
        $departureAt = Carbon::parse($ride->departure_at);
        $departureDate = $departureAt->format('Y-m-d');
        $departureTime = $departureAt->format('H:i');

        // Format price (convert from minor units to major units)
        $price = $ride->price_minor ? number_format($ride->price_minor / 100, 2) : '0.00';

        // Build email data
        $data = [
            'username' => $user->first_name,
            'from' => $origin,
            'to' => $destination,
            'on' => $departureDate,
            'at' => $departureTime,
            'seats' => $ride->seats_total,
            'price' => $price,
            'redirect' => env('APP_URL') . '/' . optional($this->selectedLanguage)->abbreviation . '/my-rides',
        ];

        // Determine ride type and queue appropriate email
        $isPinkRide = $ride->women_only === true || $ride->women_only === 1;
        $isExtraCareRide = $ride->extra_care === true || $ride->extra_care === 1;

        if ($isPinkRide && $isExtraCareRide) {
            // Both Pink and Extra Care
            Mail::to($user->email)->queue(new PinkExtraCareRideMail($data));
        } elseif ($isPinkRide) {
            // Only Pink Ride
            Mail::to($user->email)->queue(new PinkRideMail($data));
        } elseif ($isExtraCareRide) {
            // Only Extra Care Ride
            Mail::to($user->email)->queue(new ExtraCareRideMail($data));
        } else {
            // Regular ride
            Mail::to($user->email)->queue(new RidePostedMail($data));
        }
    }

    /**
     * Get distance and duration data from Google Distance Matrix API
     *
     * @param string $from Origin address
     * @param string $to Destination address
     * @return array|null API response data or null on error
     */
    protected function getDataFromGoogleApi($from, $to)
    {
        $apiKey = env('GOOGLE_API_KEY');
        $ch = curl_init();

        // URL encode the addresses to properly handle spaces and special characters
        // This ensures city names like "Montreal, QC" and "Ottawa, ON" work correctly
        $fromEncoded = urlencode($from);
        $toEncoded = urlencode($to);

        $apiUrl = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=" . $fromEncoded . "&destinations=" . $toEncoded . "&units=imperial&key=" . $apiKey . "";

        Log::info('Google Maps API Request (PX Ride)', [
            'from' => $from,
            'to' => $to,
            'from_encoded' => $fromEncoded,
            'to_encoded' => $toEncoded,
            'url' => str_replace($apiKey, 'HIDDEN_KEY', $apiUrl)
        ]);

        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            Log::error('Google Maps API cURL Error (PX Ride): ' . curl_error($ch), [
                'from' => $from,
                'to' => $to,
                'curl_error' => curl_error($ch),
                'curl_errno' => curl_errno($ch)
            ]);
            curl_close($ch);
            return null;
        }

        curl_close($ch);

        $data = json_decode($response, true);

        // Log API response
        if (isset($data['status']) && $data['status'] === 'OK') {
            $distance = isset($data['rows'][0]['elements'][0]['distance']['value']) ? $data['rows'][0]['elements'][0]['distance']['value'] : 0;
            $distanceText = isset($data['rows'][0]['elements'][0]['distance']['text']) ? $data['rows'][0]['elements'][0]['distance']['text'] : 'N/A';
            $duration = isset($data['rows'][0]['elements'][0]['duration']['value']) ? $data['rows'][0]['elements'][0]['duration']['value'] : 0;
            $durationText = isset($data['rows'][0]['elements'][0]['duration']['text']) ? $data['rows'][0]['elements'][0]['duration']['text'] : 'N/A';

            Log::info('Google Maps API Success (PX Ride)', [
                'from' => $from,
                'to' => $to,
                'distance_meters' => $distance,
                'distance_text' => $distanceText,
                'duration_seconds' => $duration,
                'duration_text' => $durationText,
            ]);
        } else {
            Log::warning('Google Maps API Error (PX Ride)', [
                'from' => $from,
                'to' => $to,
                'status' => $data['status'] ?? 'unknown',
                'error_message' => $data['error_message'] ?? 'No error message',
            ]);
        }

        return $data;
    }

    /**
     * Validate cost-sharing cap: Price per seat validation
     * Formula: (Distance × Cap) ÷ Seats = Max price per seat
     * Skip validation if user explicitly chose to bypass (after seeing warning)
     *
     * @param \Illuminate\Http\Request $request
     * @param array $payload
     * @return \Illuminate\Http\RedirectResponse|null Returns redirect response if validation fails, null otherwise
     */
    protected function validateCostSharingCap(Request $request, array $payload)
    {
        // Skip validation if user explicitly chose to bypass (after seeing warning)
        $bypassValidation = $request->has('bypass_price_validation') && $request->bypass_price_validation == '1';

        if ($bypassValidation) {
            Log::info('Cost-sharing cap validation bypassed by user', [
                'user_id' => auth()->id(),
            ]);
            return null;
        }

        // Get required values from payload
        $distanceMeters = $payload['distance_meters'] ?? 0;
        $priceMinor = $payload['price_minor'] ?? 0;
        $seatsTotal = $payload['seats_total'] ?? 0;

        // Skip validation if required data is missing or invalid
        if ($distanceMeters <= 0 || $priceMinor <= 0 || $seatsTotal <= 0) {
            return null;
        }

        // Convert distance from meters to kilometers
        $distanceKm = $distanceMeters / 1000;

        // Convert price from minor units (cents) to major units (dollars)
        $pricePerSeat = $priceMinor / 100;

        // Cost-sharing cap constants
        $errorCap = 0.72; // $0.72 per km - BLOCK if exceeded
        $warningCap = 0.66; // $0.66 per km - WARN but ALLOW

        // Calculate max allowed price per seat using Error-Triggering Cap: $0.72/km
        $maxPricePerSeat = ($distanceKm * $errorCap) / $seatsTotal;

        // Calculate soft warning price per seat: $0.66/km
        $softWarningPricePerSeat = ($distanceKm * $warningCap) / $seatsTotal;

        Log::info('Cost-sharing cap validation (PX Ride)', [
            'user_id' => auth()->id(),
            'price_per_seat' => $pricePerSeat,
            'distance_km' => round($distanceKm, 2),
            'seats_total' => $seatsTotal,
            'max_price_per_seat' => round($maxPricePerSeat, 2),
            'soft_warning_price_per_seat' => round($softWarningPricePerSeat, 2),
            'error_cap' => $errorCap,
            'warning_cap' => $warningCap,
        ]);

        // Error-Triggering Cap: $0.72 per km - BLOCK if exceeded
        if ($pricePerSeat > $maxPricePerSeat) {
            Log::warning('Price per seat exceeds error-triggering cap (PX Ride)', [
                'user_id' => auth()->id(),
                'price_per_seat' => $pricePerSeat,
                'max_allowed' => round($maxPricePerSeat, 2),
                'cap' => $errorCap,
            ]);

            return back()
                ->with('error', 'The price per seat ($' . number_format($pricePerSeat, 2) . ') exceeds the maximum allowed for cost-sharing rides ($' . number_format($maxPricePerSeat, 2) . ' per seat). Please adjust your price.')
                ->with('heading', 'Price Limit Exceeded')
                ->with('max_price_per_seat', round($maxPricePerSeat, 2))
                ->withInput();
        }

        // Soft warning is handled client-side on the post-ride form.
        if ($pricePerSeat > $softWarningPricePerSeat) {
            Log::info('Price per seat exceeds soft warning cap but within error cap (PX Ride)', [
                'user_id' => auth()->id(),
                'price_per_seat' => $pricePerSeat,
                'soft_warning_price' => round($softWarningPricePerSeat, 2),
                'warning_cap' => $warningCap,
            ]);
        }

        return null;
    }

    /**
     * Get success messages with caching
     *
     * @return \App\Models\SuccessMessagesSettingDetail
     */
    protected function getSuccessMessages()
    {
        if ($this->successMessages === null) {
            $selectedLangId = optional($this->selectedLanguage)->id;
            $defaultLangId = optional($this->defaultLang)->id;
            $this->successMessages = SuccessMessagesSettingDetail::getByLanguageWithFallback($selectedLangId, $defaultLangId);
        }

        return $this->successMessages;
    }

    /**
     * Validate user permissions for posting/updating rides
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Http\RedirectResponse|null Returns redirect response if validation fails, null otherwise
     */
    protected function validatePostRidePermissions($user)
    {
        $message = $this->getSuccessMessages();

        if ($user->block_post_ride == '1') {
            return back()->with('message', $message->block_post_ride_message)
                ->with('validation_error', $message->block_post_ride_message)
                ->with('validation_heading', 'Post Ride Permissions');
        }

        if (!isset($user->profile_image) || $user->profile_image == '' || in_array(basename($user->profile_image), ['male.png', 'female.png', 'neutral.png'])) {
            $errorMsg = $message->profile_photo_required_message ?? 'For posting a ride profile photo is required';
            return back()->with('message', $errorMsg)
                ->with('validation_error', $errorMsg)
                ->with('validation_heading', 'Post Ride Permissions');
        }

        // Check if user has suspanded
        if ($user->suspand === '1') {
            $errorMsg = 'Your account has been suspended by the admin';
            return back()->with('message', $errorMsg)
                ->with('validation_error', $errorMsg)
                ->with('validation_heading', 'Post Ride Permissions');
        }

        return null;
    }

    /**
     * Validate feature eligibility for Pink Ride and Extra Care Ride
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\User $user
     * @return \Illuminate\Http\RedirectResponse|null Returns redirect response if validation fails, null otherwise
     */
    protected function validateFeatureEligibility(Request $request, User $user)
    {
        // Feature gatekeeping logic for Pink Ride and Extra Care Ride
        $selectedFeatureCodes = $this->resolveRideOptionCodes($request);

        $pinkRideSetting = PinkRideSetting::first();

        if (in_array('pink_rides', $selectedFeatureCodes, true)) {
            // GENDER VALIDATION: Only female users can post Pink Rides
            if ($pinkRideSetting && $pinkRideSetting->female === '1') {
                // Check if user has admin override (pink_ride = '1')
                if ($user->pink_ride !== '1') {
                    // If user is explicitly disabled (pink_ride = '0'), block them
                    if ($user->pink_ride === '0') {
                        $errorMsg = 'You are not allowed to post Pink Rides. Please contact support if you believe this is an error.';
                        return back()->with('message', $errorMsg)
                            ->with('validation_error', $errorMsg)
                            ->with('validation_heading', 'Feature Eligibility');
                    }
                    // If pink_ride is empty/null, check gender restriction
                    if ($user->gender !== 'female') {
                        $errorMsg = 'Only female drivers can post Pink Rides.';
                        return back()->with('message', $errorMsg)
                            ->with('validation_error', $errorMsg)
                            ->with('validation_heading', 'Feature Eligibility');
                    }
                }
            }

            // Check if driver's license is required and uploaded
            if ($pinkRideSetting && $pinkRideSetting->driver_license === '1') {
                if (empty($user->driver_license_upload)) {
                    $errorMsg = 'A government-issued photo ID (driver\'s license) is required to post Pink Rides. Please upload your driver\'s license in your profile.';
                    return back()->with('message', $errorMsg)
                        ->with('validation_error', $errorMsg)
                        ->with('validation_heading', 'Feature Eligibility');
                }
            }
        }

        if (in_array('extra_plus_rides', $selectedFeatureCodes, true)) {
            $extraCareError = $this->validateExtraCareEligibility($user);
            if ($extraCareError) {
                return back()->with('message', $extraCareError)
                    ->with('validation_error', $extraCareError)
                    ->with('validation_heading', 'Feature Eligibility');
            }
        }

        return null;
    }

    protected function resolveRideOptionCodes(Request $request): array
    {
        $rideOptionIds = $request->input('ride_option_ids', $request->input('preference', []));

        if (!is_array($rideOptionIds)) {
            $rideOptionIds = is_string($rideOptionIds) ? explode(',', $rideOptionIds) : [$rideOptionIds];
        }

        $rideOptionIds = array_values(array_filter(array_map('intval', $rideOptionIds)));

        if (empty($rideOptionIds)) {
            return [];
        }

        return PxOption::query()
            ->whereIn('id', $rideOptionIds)
            ->pluck('code')
            ->map(fn($code) => (string) $code)
            ->all();
    }

    /**
     * Validate that all city IDs in origin, destination, and stops exist in the cities table
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|null Returns redirect response if validation fails, null otherwise
     */
    protected function validateCities(Request $request)
    {
        $siteText = $this->siteText;
        $errorMessage = $siteText['required_field_error_text'] ?? 'Invalid city selected.';

        // Collect all city IDs to validate
        $cityIds = [];

        // Get origin city_id
        $originCityId = $request->input('origin.city_id');
        if (!empty($originCityId)) {
            $cityIds[] = (int) $originCityId;
        }

        // Get destination city_id
        $destinationCityId = $request->input('destination.city_id');
        if (!empty($destinationCityId)) {
            $cityIds[] = (int) $destinationCityId;
        }

        // Get stops city_ids
        $stops = $request->input('stops', []);
        if (!empty($stops) && is_array($stops)) {
            foreach ($stops as $stop) {
                if (isset($stop['city_id']) && !empty($stop['city_id'])) {
                    $cityIds[] = (int) $stop['city_id'];
                }
            }
        }

        // If no city IDs to validate, return null
        if (empty($cityIds)) {
            return null;
        }

        // Remove duplicates
        $cityIds = array_unique($cityIds);

        // Check all cities in a single query
        $existingCityIds = City::whereIn('id', $cityIds)->pluck('id')->toArray();
        $missingCityIds = array_diff($cityIds, $existingCityIds);

        // If any cities are missing, return error
        if (!empty($missingCityIds)) {
            // Find which field has the invalid city
            if (in_array((int) $originCityId, $missingCityIds)) {
                return back()
                    ->withInput()
                    ->withErrors(['origin.city_id' => $errorMessage])
                    ->with('validation_error', $errorMessage)
                    ->with('validation_heading', 'Cities Validation');
            }

            if (in_array((int) $destinationCityId, $missingCityIds)) {
                return back()
                    ->withInput()
                    ->withErrors(['destination.city_id' => $errorMessage])
                    ->with('validation_error', $errorMessage)
                    ->with('validation_heading', 'Cities Validation');
            }

            // Check stops
            foreach ($stops as $index => $stop) {
                if (isset($stop['city_id']) && !empty($stop['city_id']) && in_array((int) $stop['city_id'], $missingCityIds)) {
                    return back()
                        ->withInput()
                        ->withErrors(["stops.$index.city_id" => $errorMessage])
                        ->with('validation_error', $errorMessage)
                        ->with('validation_heading', 'Cities Validation');
                }
            }
        }

        return null;
    }

    /**
     * Validate per-state daily post limit
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\User $user
     * @param int|null $excludeRideId Optional ride ID to exclude from count (for updates)
     * @return \Illuminate\Http\RedirectResponse|null Returns redirect response if validation fails, null otherwise
     */
    protected function validateStateRideLimit(Request $request, User $user, $excludeRideId = null)
    {
        $message = $this->getSuccessMessages();
        $errorMessage = $message->not_allowed_post_ride_state_wise_message ?? 'You have reached the daily ride posting limit for this state.';

        // Get origin city_id from request
        $originCityId = $request->input('origin.city_id');
        if (empty($originCityId)) {
            // If no city_id, try to get from origin label (fallback)
            $originLabel = $request->input('origin.label');
            if (!empty($originLabel)) {
                $locationBeforeComma = explode(',', $originLabel);
                $originCity = City::where('status', '1')
                    ->whereRaw('LOWER(`name`) LIKE ?', ['%' . strtolower(trim($locationBeforeComma[0])) . '%'])
                    ->first();

                if ($originCity) {
                    $originCityId = $originCity->id;
                }
            }
        }

        if (empty($originCityId)) {
            // Cannot validate without origin city
            return null;
        }

        // Get the origin city and its state
        $originCity = City::with('state:id,abrv,ride_limit')
            ->where('id', $originCityId)
            ->where('status', '1')
            ->first();

        if (!$originCity || !$originCity->state) {
            // No state found, skip validation
            return null;
        }

        $state = $originCity->state;

        // If no ride limit is set, skip validation
        if (empty($state->ride_limit)) {
            return null;
        }

        // Get today's date
        $nowDate = Carbon::now()->toDateString();

        // Count PX rides posted today from the same state
        // Count rides where the route's origin city is in the same state
        $query = PxRide::where('driver_id', $user->id)
            ->whereDate('created_at', $nowDate)
            ->whereHas('route', function ($q) use ($state) {
                $q->whereHas('originCity', function ($cityQuery) use ($state) {
                    $cityQuery->where('state_id', $state->id);
                });
            });

        // Exclude current ride if updating
        if ($excludeRideId) {
            $query->where('id', '!=', $excludeRideId);
        }

        $getRideCount = $query->count();

        $getRideCount = $getRideCount ?? 0;

        // Check if user has reached the limit
        if ($getRideCount >= $state->ride_limit) {
            return back()
                ->with('message', $errorMessage)
                ->with('validation_error', $errorMessage)
                ->with('validation_heading', 'State Ride Limit')
                ->withInput();
        }

        return null;
    }


    /**
     * Validate that no existing ride has the same date and time
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\User $user
     * @param int|null $excludeRideId Optional ride ID to exclude from check (for updates)
     * @return \Illuminate\Http\RedirectResponse|null Returns redirect response if validation fails, null otherwise
     */
    protected function validateDuplicateDateTime(Request $request, User $user, $excludeRideId = null)
    {
        $message = $this->getSuccessMessages();
        $errorMessage = $message->ride_schedule_message ?? 'A ride with the same date and time already exists.';

        // Get departure_at from request
        $departureAt = $request->input('departure_at');

        if (empty($departureAt)) {
            // Try to get from departure_date and departure_time (backward compatibility)
            $departureDate = $request->input('departure_date');
            $departureTime = $request->input('departure_time');
            if ($departureDate && $departureTime) {
                $departureAt = trim($departureDate . ' ' . $departureTime);
            }
        }

        if (empty($departureAt)) {
            // Cannot validate without departure time
            return null;
        }

        // Parse departure_at to Carbon instance
        try {
            $requestDepartureAt = Carbon::createFromFormat('Y-m-d H:i', $departureAt);
            if ($requestDepartureAt === false) {
                $requestDepartureAt = Carbon::parse($departureAt);
            }
        } catch (\Throwable $e) {
            // Invalid date format, skip validation
            return null;
        }

        // Get all existing rides for this user
        $query = PxRide::where('driver_id', $user->id);

        // Exclude current ride if updating
        if ($excludeRideId) {
            $query->where('id', '!=', $excludeRideId);
        }

        $rides = $query->get();

        // Check if any existing ride has the same date and time
        foreach ($rides as $existingRide) {
            if ($existingRide->departure_at) {
                $existingDepartureAt = Carbon::parse($existingRide->departure_at);

                // Compare date and time (to the minute)
                if (
                    $existingDepartureAt->format('Y-m-d') === $requestDepartureAt->format('Y-m-d') &&
                    $existingDepartureAt->format('H:i') === $requestDepartureAt->format('H:i')
                ) {
                    return back()
                        ->with('message', $errorMessage)
                        ->with('error', $errorMessage)
                        ->with('heading', 'Ride already scheduled')
                        ->with('validation_error', $errorMessage)
                        ->with('validation_heading', 'Duplicate Date/Time')
                        ->withInput();
                }
            }
        }

        return null;
    }

    /**
     * Validate that the user is eligible to post Extra Care Rides (rating, age, completed rides, no-shows, cancellations, verification).
     * Returns an error message string if ineligible, or null if eligible.
     *
     * @param \App\Models\User $user
     * @return string|null
     */
    protected function validateExtraCareEligibility(User $user)
    {
        $setting = FolkRideSetting::first();
        if (!$setting) {
            return null;
        }

        if ($user->folks_ride === '0') {
            return 'You are not allowed to post Extra Care Rides. Please contact support if you believe this is an error.';
        }

        if ($user->folks_ride !== '1') {
            if ($setting->verfiy_phone === '1') {
                $hasVerifiedPhone = $user->relationLoaded('phone_numbers')
                    ? $user->phone_numbers->contains('verified', 1)
                    : $user->phoneNumbers()->where('verified', 1)->exists();
                if (!$hasVerifiedPhone) {
                    return 'A verified phone number is required to post Extra Care Rides.';
                }
            }
            if ($setting->verify_email === '1' && $user->email_verified !== '1') {
                return 'A verified email is required to post Extra Care Rides.';
            }
            if ($setting->driver_license === '1') {
                if ($user->driver !== '1') {
                    return 'Driver verification is required to post Extra Care Rides.';
                }
                if (empty($user->driver_license_upload)) {
                    return 'A government-issued photo ID (driver\'s license) is required to post Extra Care Rides. Please upload your driver\'s license in your profile.';
                }
            }
            if (empty($user->government_issued_id ?? $user->government_id ?? null) || empty($user->address ?? '')) {
                return 'A complete address and government-issued ID are required to post Extra Care Rides.';
            }

            $noShowsCount = NoShowHistory::where('user_id', $user->id)->where('type', 'driver')
                ->whereBetween('created_at', [Carbon::now()->subMonths(3), Carbon::now()])->count();
            if ($noShowsCount > 0) {
                return 'Drivers with recent no-shows cannot post Extra Care Rides.';
            }
            $cancellationCount = CancellationHistory::where('user_id', $user->id)->where('type', 'driver')
                ->whereBetween('created_at', [Carbon::now()->subMonths(3), Carbon::now()])->whereNotNull('booking_id')->count();
            if ($cancellationCount > 0) {
                return 'Drivers with recent cancellations cannot post Extra Care Rides.';
            }

            // Check ratings from regular rides where user is the driver
            // Note: PX rides may not have ratings yet, so we check regular rides
            $ratings = Rating::where('type', 1)->where('status', 1)
                ->whereHas('ride', fn($q) => $q->where('added_by', $user->id))->get();
            $overallRating = $ratings->isEmpty() ? 0 : $ratings->avg('average_rating');
            $minRating = (float) ($setting->average_rating ?? 0);
            if ($overallRating < $minRating) {
                return 'Extra Care Rides require a minimum driver rating of ' . $minRating . ' stars. Your current rating is ' . number_format((float) $overallRating, 1) . '.';
            }

            $age = $user->dob ? Carbon::parse($user->dob)->diffInYears(Carbon::now()) : 0;
            $minAge = (int) ($setting->driver_age ?? 0);
            if ($minAge > 0 && $age < $minAge) {
                return 'Extra Care Rides require drivers to be at least ' . $minAge . ' years old.';
            }

            $rideLimit = (int) ($setting->extra_rides_trip_limit ?? 0);
            if ($rideLimit > 0) {
                // For PX rides, check completed PxRides
                $totalCompleted = PxRide::where('driver_id', $user->id)
                    ->where('status', 'completed')
                    ->where('departure_at', '<', now())
                    ->count();
                if ($totalCompleted < $rideLimit) {
                    return 'Extra Care Rides require at least ' . $rideLimit . ' completed rides.';
                }
            }
        }

        return null;
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
        $bookingMethodCode = $this->getOptionCode($optionGroups->get('booking_method'), $ride->booking_method, '');
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
            'bookingMethodCode' => $bookingMethodCode,
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

                $rideForUpdate->adjustSegmentSeatAvailability(
                    (int) $booking->from_stop_id,
                    (int) $booking->to_stop_id,
                    -(int) $booking->seats
                );
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

    public function rideDetail($lang = null, $id = null, $from_stop_id = null, $to_stop_id = null)
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

        if (auth()->check() && (int) $ride->driver_id === (int) auth()->id()) {
            return redirect()
                ->route('px.my_ride_detail', ['lang' => optional($this->selectedLanguage)->abbreviation, 'id' => $ride->id]);
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
        $bookingMethodCode = $this->getOptionCode($optionGroups->get('booking_method'), $ride->booking_method, '');
        $cancelationPolicyLabel = $this->getOptionLabel($optionGroups->get('cancelation_policy'), $ride->cancelation_policy, $selectedLangId, $defaultLangId, 'Standard');
        $rideDetailPage = RideDetailPageSettingDetail::getByLanguageWithFallback($selectedLangId, $defaultLangId);
        $orderedStops = $ride->stops ? $ride->stops->sortBy('stop_order')->values()->all() : [];

        $fromStopId = (int) ($from_stop_id ?? request()->query('from_stop_id', 0));
        $toStopId = (int) ($to_stop_id ?? request()->query('to_stop_id', 0));
        $hasSegmentContext = ($fromStopId > 0 && $toStopId > 0);

        $displayOrigin = $ride->route->origin_label ?? 'N/A';
        $displayDestination = $ride->route->destination_label ?? 'N/A';
        $displayPriceMinor = (int) ($ride->price_minor ?? 0);
        $displaySeatsAvailable = (int) ($ride->seats_available ?? 0);
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
                $displaySeatsAvailable = $ride->resolveSegmentAvailableSeats(
                    (int) ($orderedStops[$matchedFromIndex]->id ?? 0),
                    (int) ($orderedStops[$matchedToIndex]->id ?? 0)
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

        if (!$from_stop_id && !$to_stop_id && $selectedFromStopId && $selectedToStopId) {
            return redirect()->route('px.ride_detail', [
                'lang' => optional($this->selectedLanguage)->abbreviation,
                'id' => $ride->id,
                'from_stop_id' => $selectedFromStopId,
                'to_stop_id' => $selectedToStopId,
            ]);
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

        $driver = $ride->driver;
        $driverDisplayName = 'N/A';
        $driverPassengersDriven = 0;
        $driverAverageRating = 0;
        $driverHasVerifiedPhone = false;
        $driverHasVerifiedEmail = false;

        if ($driver) {
            $driverDisplayName = trim(match ((string) ($driver->type ?? '')) {
                '2' => (string) ($driver->last_name ?? ''),
                '3' => trim((string) ($driver->first_name ?? '') . ' ' . (string) ($driver->last_name ?? '')),
                default => (string) ($driver->first_name ?? ''),
            });

            if ($driverDisplayName === '') {
                $driverDisplayName = $driver->name ?? 'N/A';
            }

            $driverPassengersDriven = (int) PxBooking::query()
                ->where('driver_id', (int) $driver->id)
                ->whereNotIn('status', ['cancelled', 'refunded', 'failed'])
                ->whereHas('ride', function ($query) use ($driver) {
                    $query->where('driver_id', (int) $driver->id)
                        ->where('status', '!=', 'cancelled')
                        ->where('departure_at', '<=', now());
                })
                ->sum('seats');

            $driverAverageRating = (float) (Rating::query()
                ->where('status', 1)
                ->where('type', 1)
                ->whereHas('ride', fn($query) => $query->where('added_by', (int) $driver->id))
                ->avg('average_rating') ?? 0);

            $driverHasVerifiedPhone = PhoneNumber::query()
                ->where('user_id', (int) $driver->id)
                ->where('verified', 1)
                ->exists();

            $driverHasVerifiedEmail = (string) ($driver->email_verified ?? '0') === '1'
                || !empty($driver->email_verified_at);
        }

        $postRidePage = $this->getPostRidePageWithSettingDetail();

        return view('px.ride_detail', [
            'ride' => $ride,
            'rideDetailPage' => $rideDetailPage,
            'postRidePage' => $postRidePage,
            'bookingModeLabel' => $bookingModeLabel,
            'bookingModeCode' => $bookingModeCode,
            'bookingMethodLabel' => $bookingMethodLabel,
            'bookingMethodCode' => $bookingMethodCode,
            'cancelationPolicyLabel' => $cancelationPolicyLabel,
            'displayOrigin' => $displayOrigin,
            'displayDestination' => $displayDestination,
            'displayPriceMinor' => $displayPriceMinor,
            'displaySeatsAvailable' => $displaySeatsAvailable,
            'displaySegmentStops' => $displaySegmentStops,
            'isSegmentView' => $isSegmentView,
            'selectedFromStopId' => $selectedFromStopId,
            'selectedToStopId' => $selectedToStopId,
            'existingBooking' => $existingBooking,
            'driverDisplayName' => $driverDisplayName,
            'driverPassengersDriven' => $driverPassengersDriven,
            'driverAverageRating' => $driverAverageRating,
            'driverHasVerifiedPhone' => $driverHasVerifiedPhone,
            'driverHasVerifiedEmail' => $driverHasVerifiedEmail,
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

        $ProfilePage = ProfilePageSettingDetail::where('language_id', $this->selectedLanguage->id)->first();
        $ProfileSetting = ProfileSettingDetail::where('language_id', $this->selectedLanguage->id)->first();
        $reviewSetting = MyReviewSettingDetail::where('language_id', $this->selectedLanguage->id)->select('review_left_label', 'review_received_label')->first();


        return view('px.my_trips', [
            'bookings' => $bookings,
            'tripsPage' => $tripsPage,
            'activeTab' => $tab,
            'upcomingCount' => $upcomingCount,
            'completedCount' => $completedCount,
            'cancelledCount' => $cancelledCount,
            'ProfilePage' => $ProfilePage,
            'ProfileSetting' => $ProfileSetting,
            'reviewSetting' => $reviewSetting,
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
        $validationResponse = $this->validatePostRidePermissions($request->user());
        if ($validationResponse) {
            return $validationResponse;
        }

        $featureValidationResponse = $this->validateFeatureEligibility($request, $request->user());
        if ($featureValidationResponse) {
            return $featureValidationResponse;
        }

        $cityValidationResponse = $this->validateCities($request);
        if ($cityValidationResponse) {
            return $cityValidationResponse;
        }

        $stateLimitResponse = $this->validateStateRideLimit($request, $request->user(), $id);
        if ($stateLimitResponse) {
            return $stateLimitResponse;
        }

        $duplicateResponse = $this->validateDuplicateDateTime($request, $request->user(), $id);
        if ($duplicateResponse) {
            return $duplicateResponse;
        }

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

        // Calculate distance and duration from Google Distance Matrix API
        $originLabel = $payload['origin']['label'] ?? '';
        $destinationLabel = $payload['destination']['label'] ?? '';

        if (!empty($originLabel) && !empty($destinationLabel)) {
            $googleApiData = $this->getDataFromGoogleApi($originLabel, $destinationLabel);

            if (isset($googleApiData) && !empty($googleApiData)) {
                // Check element status first before accessing distance/duration
                $elementStatus = isset($googleApiData['rows']) &&
                    isset($googleApiData['rows'][0]) &&
                    isset($googleApiData['rows'][0]['elements']) &&
                    isset($googleApiData['rows'][0]['elements'][0]) &&
                    isset($googleApiData['rows'][0]['elements'][0]['status'])
                    ? $googleApiData['rows'][0]['elements'][0]['status']
                    : null;

                if ($elementStatus === 'OK') {
                    $distanceMeters = isset($googleApiData['rows'][0]['elements'][0]['distance']['value'])
                        ? (int) $googleApiData['rows'][0]['elements'][0]['distance']['value']
                        : 0;
                    $durationSeconds = isset($googleApiData['rows'][0]['elements'][0]['duration']['value'])
                        ? (int) $googleApiData['rows'][0]['elements'][0]['duration']['value']
                        : 0;

                    $payload['distance_meters'] = $distanceMeters;
                    $payload['duration_seconds'] = $durationSeconds;

                    Log::info('Google Distance Matrix API - Distance and duration calculated for PX ride update', [
                        'ride_id' => $id,
                        'origin' => $originLabel,
                        'destination' => $destinationLabel,
                        'distance_meters' => $distanceMeters,
                        'duration_seconds' => $durationSeconds,
                    ]);
                } else {
                    Log::warning('Google Maps API element status is not OK for PX ride update', [
                        'ride_id' => $id,
                        'origin' => $originLabel,
                        'destination' => $destinationLabel,
                        'element_status' => $elementStatus,
                        'api_status' => $googleApiData['status'] ?? 'unknown',
                        'error_message' => $googleApiData['error_message'] ?? 'No error message',
                    ]);
                }
            }
        }

        // Cost-sharing cap validation: Price per seat validation
        $costSharingValidationResponse = $this->validateCostSharingCap($request, $payload);
        if ($costSharingValidationResponse) {
            return $costSharingValidationResponse;
        }

        $this->processVehicleMode($request, $payload);
        $this->processStops($request, $payload);

        $vehicleValidationError = $this->validateVehicleOwnership($payload, $siteText);
        if ($vehicleValidationError) {
            return $vehicleValidationError;
        }

        $updatedRide = $service->updateRide($ride, $payload, $request->user());

        return redirect()
            ->route('px.my_ride_detail', ['lang' => optional($this->selectedLanguage)->abbreviation, 'id' => $updatedRide->id])
            ->with('success', 'PX ride updated successfully.');
    }

    public function folk_ride_search(Request $request, PxRideService $service, $lang = null)
    {
        $extraCareFaqs = ExtraCareFaqDetail::where('language_id', $this->selectedLanguage->id)->get();
        view()->share('extraCareFaqs', $extraCareFaqs);

        return $this->search($request, $service, $lang, 'px.search_folk_ride');
    }

    public function pink_ride_search(Request $request, PxRideService $service, $lang = null)
    {
        $pinkRideFaqs = PinkRideFaqDetail::where('language_id', $this->selectedLanguage->id)->get();
        view()->share('pinkRideFaqs', $pinkRideFaqs);

        return $this->search($request, $service, $lang, 'px.search_pink_ride');
    }

    public function proximalocal_ride_search(Request $request, PxRideService $service, $lang = null)
    {
        $proximaLocalRideFaqs = [];
        view()->share('proximaLocalRideFaqs', $proximaLocalRideFaqs);

        return $this->search($request, $service, $lang, 'px.search_proximalocal_ride');
    }

    public function search(Request $request, PxRideService $service, $lang = null, $view = 'px.search_ride')
    {
        $selectedLangId = optional($this->selectedLanguage)->id;
        $defaultLangId = optional($this->defaultLang)->id;
        $user = auth()->user();
        $isGuest = !$user;
        $per_page = 20;
        $excludedDriverIds = $user ? $this->getTemporarilyBlockedDriverIds($user->id) : [];

        $findRidePage = $this->getFindRidePageWithSettingDetail();

        $searchOptionGroups = $this->getPxSearchOptionGroups($selectedLangId, $defaultLangId);
        $searchFilters = $this->getPxSearchFilters($request);

        $searchFilters['proximalocal'] = 0;
        $page_type = 'px_ride';
        $action_route = 'px.search_ride';
        if ($view === 'px.search_folk_ride') {
            $searchFilters['extra_care'] = 1;
            $page_type = 'px_folk_ride';
            $action_route = 'folk_ride';
        } elseif ($view === 'px.search_pink_ride') {
            $searchFilters['women_only'] = 1;
            $page_type = 'px_pink_ride';
            $action_route = 'pink_ride';
        } elseif ($view === 'px.search_proximalocal_ride') {
            $searchFilters['proximalocal'] = 1;
            $page_type = 'px_proximalocal_ride';
            $action_route = 'proximalocal_ride';
        }

        $keyword = trim((string) $request->input('keyword'));
        $hasActiveFilters = collect($searchFilters)->contains(function ($value) {
            return $value !== null && $value !== '' && $value !== false;
        });

        // Check if there are search parameters
        $originLabel = trim((string) $request->input('origin.label'));
        $destinationLabel = trim((string) $request->input('destination.label'));
        $originCityId = $request->input('origin.city_id');
        $destinationCityId = $request->input('destination.city_id');
        $departureDate = $request->input('departure_date');
        $isSearchSubmission = $request->boolean('search');
        $hasSearch = false;
        $hasOriginSearch = $originLabel !== '' || !empty($originCityId);
        $hasDestinationSearch = $destinationLabel !== '' || !empty($destinationCityId);
        $hasLocationSearch = $hasOriginSearch || $hasDestinationSearch;
        $hasKeywordSearch = $keyword !== '';
        $hasPrimarySearch = $hasLocationSearch || $hasKeywordSearch;
        $shouldRunFilteredSearch = $hasPrimarySearch || $hasActiveFilters;

        $filters = array_merge($searchFilters, [
            'keyword' => $keyword,
            'per_page' => $per_page,
            'sort' => $isGuest && !$isSearchSubmission ? 'soonest' : 'latest_added',
            'excluded_driver_ids' => $excludedDriverIds,
            'require_vehicle' => true,
            'exclude_admin_deactive' => true,
        ]);

        if ($hasOriginSearch) {
            $filters['origin_city_id'] = $originCityId;
            $filters['origin_label'] = $originLabel;
        }

        if ($hasDestinationSearch) {
            $filters['destination_city_id'] = $destinationCityId;
            $filters['destination_label'] = $destinationLabel;
        }

        if (!empty($departureDate)) {
            $filters['departure_date'] = $departureDate;
        }

        $rides = $service->searchRides(
            $shouldRunFilteredSearch ? $filters : array_diff_key($filters, array_flip([
                'origin_city_id',
                'origin_label',
                'destination_city_id',
                'destination_label',
                'departure_date',
            ])),
            $user
        );

        if ($isSearchSubmission) {
            $validator = Validator::make($request->all(), [
                'origin.label' => ['nullable', 'string', 'max:160'],
                'destination.label' => ['nullable', 'string', 'max:160'],
                'departure_date' => ['nullable', 'date'],
                'origin.city_id' => ['nullable', 'integer'],
                'destination.city_id' => ['nullable', 'integer'],
            ]);

            $invalidCityMessage = $this->siteText['invalid_city_error_text']
                ?? 'Please select a valid city from the dropdown.';
            $requiredFieldMessage = $this->siteText['required_field_error_text']
                ?? 'This field is required.';

            $validator->after(function ($validator) use ($request, $invalidCityMessage, $requiredFieldMessage, $hasActiveFilters) {
                $originLabel = trim((string) $request->input('origin.label'));
                $destinationLabel = trim((string) $request->input('destination.label'));
                $keyword = trim((string) $request->input('keyword'));
                $originCityId = $request->input('origin.city_id');
                $destinationCityId = $request->input('destination.city_id');
                $hasValidOrigin = $originLabel !== '' && !empty($originCityId);
                $hasValidDestination = $destinationLabel !== '' && !empty($destinationCityId);

                if ($keyword === '' && $originLabel === '' && $destinationLabel === '' && !$hasActiveFilters) {
                    $validator->errors()->add('keyword', 'Enter a keyword, select at least one valid city from the dropdown, or choose a filter.');
                }

                if ($keyword === '' && ($originLabel !== '' || $destinationLabel !== '')) {
                    if ($hasValidOrigin && $destinationLabel === '') {
                        $validator->errors()->add('destination.label', $requiredFieldMessage);
                    }

                    if ($hasValidDestination && $originLabel === '') {
                        $validator->errors()->add('origin.label', $requiredFieldMessage);
                    }
                }

                if ($originLabel !== '' && empty($originCityId)) {
                    $validator->errors()->add('origin.label', $invalidCityMessage);
                }

                if ($destinationLabel !== '' && empty($destinationCityId)) {
                    $validator->errors()->add('destination.label', $invalidCityMessage);
                }
            });

            $validator->validate();
        }

        if ($shouldRunFilteredSearch) {
            $hasSearch = true;

            if ($user && $hasPrimarySearch) {
                if ($user->suspand === '1') {
                    return redirect()
                        ->route('home', ['lang' => optional($this->selectedLanguage)->abbreviation])
                        ->with(['message' => 'Your account has been suspended by the admin']);
                }

                if ($keyword === '') {
                    $existingSearch = RecentSearch::query()
                        ->where('user_id', $user->id)
                        ->where('page_type', $page_type)
                        ->where('from', 'like', '%' . $originLabel . '%')
                        ->where('to', 'like', '%' . $destinationLabel . '%')
                        ->first();

                    if ($existingSearch) {
                        $existingSearch->touch();
                    } else {
                        RecentSearch::create([
                            'from' => $originLabel,
                            'to' => $destinationLabel,
                            'user_id' => $user->id,
                            'page_type' => $page_type,
                        ]);
                    }
                }
            }
        }

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

            if ($hasLocationSearch) {
                [$matchedFromIndex, $matchedToIndex] = $this->findMatchingSegmentIndices(
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
            $ride->matched_seats_available = ($ride->matched_from_stop_id && $ride->matched_to_stop_id)
                ? $ride->resolveSegmentAvailableSeats(
                    (int) $ride->matched_from_stop_id,
                    (int) $ride->matched_to_stop_id
                )
                : (int) ($ride->seats_available ?? 0);
        }

        $recentSearches = collect();
        if ($user) {
            $recentSearches = RecentSearch::query()
                ->where('user_id', $user->id)
                ->where('page_type', $page_type)
                ->where('from', '!=', '')
                ->where('to', '!=', '')
                ->orderByDesc('updated_at')
                ->limit(3)
                ->get()
                ->map(function (RecentSearch $recentSearch) use ($lang, $view) {
                    $originLabel = trim((string) $recentSearch->from);
                    $destinationLabel = trim((string) $recentSearch->to);
                    $originCityId = $this->resolveRecentSearchCityId($originLabel);
                    $destinationCityId = $this->resolveRecentSearchCityId($destinationLabel);

                    $recentSearch->origin_label = $originLabel;
                    $recentSearch->destination_label = $destinationLabel;
                    $recentSearch->origin_city_id = $originCityId;
                    $recentSearch->destination_city_id = $destinationCityId;
                    $recentSearch->search_url = ($originCityId && $destinationCityId)
                        ? route($view, [
                            'lang' => $lang ?? optional($this->selectedLanguage)->abbreviation,
                            'origin' => [
                                'label' => $originLabel,
                                'city_id' => $originCityId,
                            ],
                            'destination' => [
                                'label' => $destinationLabel,
                                'city_id' => $destinationCityId,
                            ],
                            'search' => 1,
                        ])
                        : null;

                    return $recentSearch;
                })
                ->filter(fn(RecentSearch $recentSearch) => !empty($recentSearch->search_url))
                ->values();
        }

        return view($view, [
            'action_route' => $action_route,
            'findRidePage' => $findRidePage,
            'searchOptionGroups' => $searchOptionGroups,
            'rides' => $rides,
            'recentSearches' => $recentSearches,
            'hasSearch' => $hasSearch,
            'hasActiveFilters' => $hasActiveFilters,
            'searchFilters' => $searchFilters,
            'oldOriginLabel' => old('origin.label', $originLabel),
            'oldOriginCityId' => old('origin.city_id', $request->input('origin.city_id')),
            'oldDestinationLabel' => old('destination.label', $destinationLabel),
            'oldDestinationCityId' => old('destination.city_id', $request->input('destination.city_id')),
            'oldDepartureDate' => old('departure_date', $departureDate),
            'oldKeyword' => old('keyword', $request->input('keyword')),
        ]);
    }

    protected function getPxSearchOptionGroups($selectedLangId, $defaultLangId)
    {
        return PxOptionGroup::query()
            ->whereIn('code', ['booking_method', 'preference', 'luggage_size', 'smoking_allowed', 'pets_allowed'])
            ->with(['options' => function ($query) use ($selectedLangId, $defaultLangId) {
                $query->where('is_active', true)
                    ->orderBy('sort_order')
                    ->with(['translations' => function ($translationQuery) use ($selectedLangId, $defaultLangId) {
                        $translationQuery->whereIn('language_id', array_filter([$selectedLangId, $defaultLangId]));
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
            })
            ->keyBy('code');
    }

    protected function getPxSearchFilters(Request $request): array
    {
        $filters = [
            'driver_age' => $request->input('driver_age'),
            'driver_rating' => $request->input('driver_rating'),
            'driver_phone' => $request->boolean('driver_phone') ? 1 : null,
            'driver_name' => trim((string) $request->input('driver_name')),
            'booking_method' => $request->input('booking_method'),
            'vehicle_type' => trim((string) $request->input('vehicle_type')),
            'luggage_size' => $request->input('luggage_size'),
            'smoking_allowed' => $request->input('smoking_allowed'),
            'pets_allowed' => $request->input('pets_allowed'),
            'women_only' => $request->boolean('women_only') ? 1 : null,
            'extra_care' => $request->boolean('extra_care') ? 1 : null,
            'hide_full_rides' => $request->boolean('hide_full_rides') ? 1 : null,
            'ride_option_ids' => array_values(array_filter(array_map('intval', (array) $request->input('ride_option_ids', [])))),
        ];

        return collect($filters)
            ->map(function ($value) {
                return $value === '0' ? null : $value;
            })
            ->all();
    }

    protected function resolveRecentSearchCityId(?string $label): ?int
    {
        $label = trim((string) $label);
        if ($label === '') {
            return null;
        }

        $parts = array_values(array_filter(array_map('trim', explode(',', $label)), fn($part) => $part !== ''));
        $cityName = $parts[0] ?? '';
        $stateAbbreviation = $parts[1] ?? null;
        $countryName = $parts[2] ?? null;

        if ($cityName === '') {
            return null;
        }

        $query = City::query()->where('status', '1')->where('name', $cityName);

        if ($stateAbbreviation) {
            $query->whereHas('state', function ($stateQuery) use ($stateAbbreviation) {
                $stateQuery->where('abrv', $stateAbbreviation);
            });
        }

        if ($countryName) {
            $query->whereHas('state.country', function ($countryQuery) use ($countryName) {
                $countryQuery->where('name', $countryName);
            });
        }

        $city = $query->first();

        if (!$city) {
            $city = City::query()
                ->where('status', '1')
                ->where('name', $cityName)
                ->first();
        }

        return $city ? (int) $city->id : null;
    }

    protected function getTemporarilyBlockedDriverIds(int $userId): array
    {
        // Future: move this legacy booking-block lookup into a shared search filter service.
        return PxBooking::query()
            ->where('passenger_id', $userId)
            ->where('removed_permanently', 1)
            ->where('block_date_time', '>', now())
            ->get()
            ->pluck('driver_id')
            ->filter()
            ->unique()
            ->map(fn($driverId) => (int) $driverId)
            ->values()
            ->all();
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
            [$fromIndex, $toIndex] = $this->findMatchingSegmentIndices($stops, $fromCityId, $toCityId, $fromLabel, $toLabel);
        }

        if ($fromIndex === null || $toIndex === null || $fromIndex >= $toIndex) {
            return (int) ($ride->price_minor ?? 0);
        }

        $configuredSegmentPriceMinor = $ride->resolveConfiguredSegmentPriceMinor((int) $fromIndex, (int) $toIndex);
        if ($configuredSegmentPriceMinor !== null) {
            return $configuredSegmentPriceMinor;
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

    protected function findMatchingSegmentIndices(array $stops, $fromCityId, $toCityId, string $fromLabel, string $toLabel): array
    {
        [$fromIndex, $toIndex] = $this->findMatchingStopPair($stops, $fromCityId, $toCityId, $fromLabel, $toLabel);

        if ($fromIndex !== null && $toIndex !== null) {
            return [$fromIndex, $toIndex];
        }

        $lastIndex = count($stops) - 1;
        if ($lastIndex < 1) {
            return [null, null];
        }

        if ($fromIndex === null && (!empty($fromCityId) || trim($fromLabel) !== '')) {
            foreach ($stops as $idx => $stop) {
                if ($this->stopMatches($stop, $fromCityId, $fromLabel) && $idx < $lastIndex) {
                    return [$idx, $lastIndex];
                }
            }
        }

        if ($toIndex === null && (!empty($toCityId) || trim($toLabel) !== '')) {
            foreach ($stops as $idx => $stop) {
                if ($this->stopMatches($stop, $toCityId, $toLabel) && $idx > 0) {
                    return [0, $idx];
                }
            }
        }

        return [null, null];
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
