<?php

namespace App\Http\Controllers;

use App\Http\Requests\Px\PxStoreRideRequest;
use App\Models\Card;
use App\Models\PxBooking;
use App\Models\PxOptionGroup;
use App\Models\PxRide;
use App\Models\PxRideStop;
use App\Models\PxTransaction;
use App\Models\Vehicle;
use App\Models\TripsPageSettingDetail;
use App\Models\RideDetailPageSettingDetail;
use App\Services\PxRideService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Stripe\PaymentIntent;
use Stripe\PaymentMethod as StripePaymentMethod;
use Stripe\Stripe;

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

            if ($matchedFromIndex !== null && $matchedToIndex !== null && $matchedFromIndex < $matchedToIndex) {
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
        ]);
    }

    public function booking($lang = null, $from_stop_id = null, $to_stop_id = null)
    {
        $fromStop = PxRideStop::query()->find($from_stop_id);
        $toStop = PxRideStop::query()->find($to_stop_id);

        if (!$fromStop || !$toStop || (int) $fromStop->ride_id !== (int) $toStop->ride_id) {
            return redirect()
                ->route('px.search_ride', ['lang' => optional($this->selectedLanguage)->abbreviation])
                ->with('error', 'Invalid booking segment.');
        }

        $ride = PxRide::query()
            ->with(['route', 'stops', 'driver', 'vehicle'])
            ->published()
            ->where('id', $fromStop->ride_id)
            ->first();

        if (!$ride) {
            return redirect()
                ->route('px.search_ride', ['lang' => optional($this->selectedLanguage)->abbreviation])
                ->with('error', 'Ride not found or unavailable.');
        }

        $orderedStops = $ride->stops->sortBy('stop_order')->values()->all();
        $fromIndex = null;
        $toIndex = null;
        foreach ($orderedStops as $idx => $stop) {
            $stopId = (int) ($stop->id ?? 0);
            if ($stopId === (int) $from_stop_id) {
                $fromIndex = $idx;
            }
            if ($stopId === (int) $to_stop_id) {
                $toIndex = $idx;
            }
        }

        if ($fromIndex === null || $toIndex === null || $fromIndex >= $toIndex) {
            return redirect()
                ->route('px.ride_detail', ['lang' => optional($this->selectedLanguage)->abbreviation, 'id' => $ride->id])
                ->with('error', 'Invalid route section for booking.');
        }

        $selectedLangId = optional($this->selectedLanguage)->id;
        $defaultLangId = optional($this->defaultLang)->id;
        $optionGroups = PxOptionGroup::whereIn('code', ['booking_mode', 'booking_method'])
            ->with(['options' => function ($q) use ($selectedLangId, $defaultLangId) {
                $q->where('is_active', true)
                    ->with(['translations' => function ($tq) use ($selectedLangId, $defaultLangId) {
                        $tq->whereIn('language_id', array_filter([$selectedLangId, $defaultLangId]));
                    }]);
            }])
            ->get()
            ->keyBy('code');

        $bookingModeCode = $this->getOptionCode($optionGroups->get('booking_mode'), $ride->booking_mode, '');
        $bookingMethodLabel = $this->getOptionLabel($optionGroups->get('booking_method'), $ride->booking_method, $selectedLangId, $defaultLangId, 'N/A');
        $segmentPriceMinor = $this->resolveMatchedSegmentPriceMinor($ride, null, null, '', '', $fromIndex, $toIndex);

        $cards = Card::query()
            ->where('user_id', auth()->id())
            ->orderByDesc('primary_card')
            ->orderByDesc('id')
            ->get();

        return view('px.booking', [
            'ride' => $ride,
            'fromStop' => $orderedStops[$fromIndex],
            'toStop' => $orderedStops[$toIndex],
            'segmentStops' => collect($orderedStops)->slice($fromIndex + 1, max(0, $toIndex - $fromIndex - 1))->values(),
            'segmentPriceMinor' => $segmentPriceMinor,
            'bookingModeCode' => $bookingModeCode,
            'bookingMethodLabel' => $bookingMethodLabel,
            'cards' => $cards,
        ]);
    }

    public function payBooking(Request $request, $lang = null)
    {
        $validated = $request->validate([
            'from_stop_id' => ['required', 'integer', 'exists:px_ride_stops,id'],
            'to_stop_id' => ['required', 'integer', 'exists:px_ride_stops,id'],
            'card_id' => ['required', 'integer', 'exists:cards,id'],
            'seats' => ['required', 'integer', 'min:1', 'max:8'],
        ]);

        $fromStop = PxRideStop::query()->find((int) $validated['from_stop_id']);
        $toStop = PxRideStop::query()->find((int) $validated['to_stop_id']);
        if (!$fromStop || !$toStop || (int) $fromStop->ride_id !== (int) $toStop->ride_id) {
            return response()->json(['message' => 'Invalid booking segment.'], 422);
        }

        $ride = PxRide::query()
            ->with('stops')
            ->published()
            ->where('id', $fromStop->ride_id)
            ->first();
        if (!$ride) {
            return response()->json(['message' => 'Ride not found or unavailable.'], 404);
        }

        $orderedStops = $ride->stops->sortBy('stop_order')->values()->all();
        $fromIndex = null;
        $toIndex = null;
        foreach ($orderedStops as $idx => $stop) {
            $stopId = (int) ($stop->id ?? 0);
            if ($stopId === (int) $validated['from_stop_id']) {
                $fromIndex = $idx;
            }
            if ($stopId === (int) $validated['to_stop_id']) {
                $toIndex = $idx;
            }
        }
        if ($fromIndex === null || $toIndex === null || $fromIndex >= $toIndex) {
            return response()->json(['message' => 'Invalid route section for booking.'], 422);
        }

        $segmentPriceMinor = $this->resolveMatchedSegmentPriceMinor($ride, null, null, '', '', $fromIndex, $toIndex);
        $amountMinor = (int) $segmentPriceMinor * (int) $validated['seats'];
        
        if ($amountMinor <= 0) {
            return response()->json(['message' => 'Invalid payment amount.'], 422);
        }

        $card = Card::query()
            ->where('id', (int) $validated['card_id'])
            ->where('user_id', auth()->id())
            ->first();
        if (!$card || empty($card->stripe_payment_method_id)) {
            return response()->json(['message' => 'Selected card is invalid.'], 422);
        }

        $user = auth()->user();
        if (empty($user->stripe_customer_id)) {
            return response()->json(['message' => 'No Stripe customer found for this user.'], 422);
        }

        $seatsRequested = (int) $validated['seats'];
        if ($seatsRequested > (int) $ride->seats_available) {
            return response()->json(['message' => 'Not enough available seats for this route section.'], 422);
        }

        try {
            Stripe::setApiKey(env('STRIPE_SECRET'));
            $paymentMethod = StripePaymentMethod::retrieve($card->stripe_payment_method_id);
            try {
                $paymentMethod->attach(['customer' => $user->stripe_customer_id]);
            } catch (\Throwable $e) {
                // Ignore if already attached; Stripe will validate on create.
            }

            $paymentIntent = PaymentIntent::create([
                'amount' => $amountMinor,
                'currency' => 'cad',
                'payment_method' => $paymentMethod->id,
                'customer' => $user->stripe_customer_id,
                'confirmation_method' => 'automatic',
                'confirm' => true,
                'off_session' => true,
                'description' => 'PX booking payment for ride ' . $ride->id,
                'metadata' => [
                    'px_ride_id' => (string) $ride->id,
                    'from_stop_id' => (string) $validated['from_stop_id'],
                    'to_stop_id' => (string) $validated['to_stop_id'],
                    'seats' => (string) $validated['seats'],
                ],
            ]);

            $paymentIntentId = (string) ($paymentIntent->id ?? '');
            if ($paymentIntentId === '') {
                return response()->json(['message' => 'Payment provider did not return a payment intent ID.'], 422);
            }

            $existingTransaction = PxTransaction::query()
                ->with('booking')
                ->where('stripe_payment_intent_id', $paymentIntentId)
                ->first();
            if ($existingTransaction) {
                return response()->json([
                    'status' => 'succeeded',
                    'payment_intent_id' => $paymentIntentId,
                    'amount_minor' => (int) $existingTransaction->amount_minor,
                    'booking_id' => (int) $existingTransaction->booking_id,
                    'transaction_id' => (int) $existingTransaction->id,
                    'idempotent' => true,
                ]);
            }

            $booking = null;
            $transaction = null;
            DB::transaction(function () use (
                &$booking,
                &$transaction,
                $ride,
                $fromStop,
                $toStop,
                $validated,
                $seatsRequested,
                $segmentPriceMinor,
                $amountMinor,
                $card,
                $paymentIntent,
                $paymentIntentId,
                $user
            ) {
                $rideForUpdate = PxRide::query()
                    ->where('id', $ride->id)
                    ->lockForUpdate()
                    ->first();

                if (!$rideForUpdate || (int) $rideForUpdate->seats_available < $seatsRequested) {
                    throw new \RuntimeException('Not enough available seats for this route section.');
                }

                $booking = PxBooking::query()->create([
                    'ride_id' => (int) $ride->id,
                    'passenger_id' => (int) $user->id,
                    'driver_id' => (int) $ride->driver_id,
                    'from_stop_id' => (int) $fromStop->id,
                    'to_stop_id' => (int) $toStop->id,
                    'card_id' => (int) $card->id,
                    'seats' => $seatsRequested,
                    'segment_price_minor' => (int) $segmentPriceMinor,
                    'total_price_minor' => (int) $amountMinor,
                    'currency' => strtoupper((string) ($ride->currency ?: 'CAD')),
                    'status' => 'paid',
                    'booked_at' => now(),
                    'meta' => [
                        'booking_source' => 'px_web',
                        'payment_provider' => 'stripe',
                        'booking_mode' => $ride->booking_mode,
                        'booking_method' => $ride->booking_method,
                        'from_stop_label' => (string) ($fromStop->label ?? ''),
                        'to_stop_label' => (string) ($toStop->label ?? ''),
                        'seats' => $seatsRequested,
                    ],
                ]);

                $transaction = PxTransaction::query()->create([
                    'booking_id' => (int) $booking->id,
                    'ride_id' => (int) $ride->id,
                    'user_id' => (int) $user->id,
                    'amount_minor' => (int) $amountMinor,
                    'currency' => strtoupper((string) ($ride->currency ?: 'CAD')),
                    'provider' => 'stripe',
                    'type' => 'charge',
                    'status' => (string) ($paymentIntent->status ?: 'succeeded'),
                    'stripe_payment_intent_id' => $paymentIntentId,
                    'stripe_payment_method_id' => (string) ($paymentIntent->payment_method ?: $card->stripe_payment_method_id),
                    'provider_payload' => method_exists($paymentIntent, 'toArray')
                        ? $paymentIntent->toArray()
                        : ['id' => $paymentIntentId],
                    'processed_at' => now(),
                ]);

                $rideForUpdate->seats_available = max(0, (int) $rideForUpdate->seats_available - $seatsRequested);
                $rideForUpdate->save();
            });

            return response()->json([
                'status' => 'succeeded',
                'payment_intent_id' => $paymentIntentId,
                'amount_minor' => $amountMinor,
                'booking_id' => (int) ($booking->id ?? 0),
                'transaction_id' => (int) ($transaction->id ?? 0),
            ]);
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        } catch (\Throwable $e) {
            $maybePaymentIntentId = isset($paymentIntent) ? (string) ($paymentIntent->id ?? '') : '';
            if ($maybePaymentIntentId !== '') {
                $existingTransaction = PxTransaction::query()
                    ->with('booking')
                    ->where('stripe_payment_intent_id', $maybePaymentIntentId)
                    ->first();
                if ($existingTransaction) {
                    return response()->json([
                        'status' => 'succeeded',
                        'payment_intent_id' => $maybePaymentIntentId,
                        'amount_minor' => (int) $existingTransaction->amount_minor,
                        'booking_id' => (int) $existingTransaction->booking_id,
                        'transaction_id' => (int) $existingTransaction->id,
                        'idempotent' => true,
                    ]);
                }
            }
            return response()->json(['message' => $e->getMessage()], 422);
        }
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
                    'price_delta_minor' => $stop->price_delta_minor,
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
            $originCityId = $request->input('origin.city_id');
            $destinationCityId = $request->input('destination.city_id');
            
            // Add translated labels to ride options
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
                [$matchedFromIndex, $matchedToIndex] = $this->findMatchingStopPair(
                    $orderedStops,
                    $originCityId,
                    $destinationCityId,
                    (string) $originLabel,
                    (string) $destinationLabel
                );

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
