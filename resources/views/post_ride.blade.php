@extends('layouts.template')

@section('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        #px-segment-distance-loader {
            display: none;
        }

        #px-segment-distance-loader.is-active {
            display: block;
        }

        .px-top-progress-track {
            height: 4px;
            width: 100%;
            overflow: hidden;
            background: rgba(17, 24, 39, 0.12);
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.12);
        }

        .px-top-progress-bar {
            height: 100%;
            width: 35%;
            background: #10b981;
            animation: pxTopProgressSlide 1.1s ease-in-out infinite;
            will-change: transform;
        }

        @keyframes pxTopProgressSlide {
            0% {
                transform: translateX(-120%);
            }

            100% {
                transform: translateX(320%);
            }
        }

        /* Shared style for all checkbox & radio inputs */
        .form-check-input {
            margin-top: 0; /* mt-2 */
            width: 1.25rem;       /* w-4 */
            height: 1.25rem;      /* h-4 */
            cursor: pointer;
            background-color: #ffffff; /* bg-white */
            border-width: 1px;
            border-color: #d1d5db;     /* border-gray-300 */
            border-radius: 0.25rem;    /* rounded */
        }

        .form-check-input:focus {
            outline: 2px solid transparent;
            outline-offset: 2px;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5); /* approx focus:ring-blue-500 focus:ring-2 */
        }
    </style>
@endsection

@section('content')

    @php

        $isEditMode = isset($ride) && isset($isEditMode) && $isEditMode;
        $isCopyMode = isset($ride) && isset($isCopyMode) && $isCopyMode;
        $isRepostMode = isset($ride) && isset($isRepostMode) && $isRepostMode;
        $prefillRide = $isEditMode || $isCopyMode || $isRepostMode ? $ride : null;

        if ($prefillRide) {

            $rideDetail = $prefillRide->detail;
    
            // Populate origin data
            if (!old('origin.label') && $rideDetail) {
                $oldOriginLabel = $rideDetail->departure;
                $oldOriginCityId = $rideDetail->origin_city_id;
            } else {
                $oldOriginLabel = old('origin.label');
                $oldOriginCityId = old('origin.city_id');
            }

            // Populate destination data
            if (!old('destination.label') && $rideDetail) {
                $oldDestinationLabel = $rideDetail->destination;
                $oldDestinationCityId = $rideDetail->destination_city_id;
            } else {
                $oldDestinationLabel = old('destination.label');
                $oldDestinationCityId = old('destination.city_id');
            }

            // Populate pickup/dropoff locations
            $oldPickupLocation = old('origin.pickup_location', $rideDetail->pickup ?? '');
            $oldDropoffLocation = old('destination.dropoff_location', $rideDetail->dropoff ?? '');

            // Populate departure_at
            if($isCopyMode){
                $oldDepartureDate = old('date', '');
                $oldDepartureTime = old('time', '');
            }else{
                $oldDepartureDate = old('date', \Carbon\Carbon::parse($rideDetail->date)->format('F j, Y'));
                $oldDepartureTime = old('time', $rideDetail->time);
            }

            // Populate stops (excluding origin and destination - already filtered in controller)
            $oldStops = old('stops', $prefillRide->intermediate_stops);

            $oldDestinationPriceDeltaMinor = old('price_minor');
            if (
                $oldDestinationPriceDeltaMinor === null &&
                isset($prefillRide->rideStops) &&
                $prefillRide->rideStops->isNotEmpty()
            ) {
                $destinationStop = $prefillRide->rideStops->sortBy('stop_order')->last();
                $oldDestinationPriceDeltaMinor = $destinationStop ? $destinationStop->price_delta_minor ?? 0 : 0;
            }


            // Populate other fields
            $oldSeatsTotal = old('seats_total', $prefillRide->seats);
            $oldPriceMinor = old('price_minor', $rideDetail->price);
            $oldVehicleId = old('vehicle_id', $prefillRide->vehicle_id);
            $oldNotes = old('notes', $prefillRide->notes);
            $oldStatus = old('status', $isCopyMode ? 'published' : $prefillRide->status);
            $oldVisibility = old('visibility', $prefillRide->visibility);
            $oldPaymentMethod = old('payment_method', $prefillRide->payment_method);
            $oldBookingMethod = old('booking_method', $prefillRide->booking_method);
            $oldSmokingAllowed = old('smoke', $prefillRide->smoking_allowed);
            $oldPetsAllowed = old('animal_friendly', $prefillRide->pets_allowed);
            $oldLuggageSize = old('luggage', $prefillRide->luggage);
            $oldAcceptMoreLuggage = old('accept_more_luggage', $prefillRide->meta['accept_more_luggage'] ?? false);
            $oldIsRecurring = old('recurring', $prefillRide->recurring);
            $oldRecurringType = old('recurring_type', $prefillRide->recurring_type);
            $oldRecurringTrips = old('recurring_trips', $prefillRide->recurring_trips);
            $oldSelectedFeatures = old('features', explode('=', $prefillRide->features ?? ''));

            // Determine vehicle mode
            if ($prefillRide->vehicle_id) {
                $oldVehicleMode = old('vehicle_mode', 'existing');
            } else {
                $oldVehicleMode = old('vehicle_mode', 'skip');
            }

            $isAvailablePinkRide = $ride->isPinkRide();
            $isAvailableExtraCareRide = $ride->isExtraCareRide();

        } else {
            // Create mode - use old() values
            $oldOriginLabel = old('origin.label');
            $oldOriginCityId = old('origin.city_id');
            $oldDestinationLabel = old('destination.label');
            $oldDestinationCityId = old('destination.city_id');
            $oldPickupLocation = old('origin.pickup_location');
            $oldDropoffLocation = old('destination.dropoff_location');
            $oldDepartureDate = old('date', '');
            $oldDepartureTime = old('time', '');
            $oldStops = old('stops', []);
            $oldDestinationPriceDeltaMinor = old('price_minor');
            $oldSeatsTotal = old('seats_total');
            $oldPriceMinor = old('price_minor');
            $oldVehicleId = old('vehicle_id');
            $oldNotes = old('notes');
            $oldStatus = old('status', 'published');
            $oldVisibility = old('visibility', 'public');
            $oldPaymentMethod = old('payment_method');
            $oldBookingMethod = old('booking_method');
            $oldSmokingAllowed = old('smoke');
            $oldPetsAllowed = old('animal_friendly');
            $oldLuggageSize = old('luggage');
            $oldAcceptMoreLuggage = old('accept_more_luggage', false);
            $oldIsRecurring = old('recurring', false);
            $oldRecurringType = old('recurring_type', '');
            $oldRecurringTrips = old('recurring_trips', 0);
            $oldSelectedFeatures = old('features', []);
            $oldVehicleMode = old('vehicle_mode', 'existing');

            $isAvailablePinkRide = false;
            $isAvailableExtraCareRide = false;
        }

        $oldStops = collect($oldStops ?? [])
            ->map(function ($stop) {
                if (!is_array($stop)) {
                    $stop = (array) $stop;
                }

                $stop['pickup_dropoff_location'] = $stop['pickup_dropoff_location']
                    ?? $stop['pickup_location']
                    ?? $stop['dropoff_location']
                    ?? '';

                return $stop;
            })
            ->values()
            ->all();

        $stopsExpanded = !empty($oldStops);
        if (!$stopsExpanded && $errors->any()) {
            foreach ($errors->keys() as $errorKey) {
                if (str_starts_with($errorKey, 'stops')) {
                    $stopsExpanded = true;
                    break;
                }
            }
        }

        $oldDepartureDateDisplay = old('date');
        $oldDepartureTimeDisplay = old('time');

        if (
            ($oldDepartureDateDisplay === null || $oldDepartureDateDisplay === '') &&
            !empty($oldDepartureAtFormatted)
        ) {
            try {
                $dt = \Illuminate\Support\Carbon::parse($oldDepartureAtFormatted);
                $oldDepartureDateDisplay = $dt->format('F j, Y');
                $oldDepartureTimeDisplay = $dt->format('h:i A');
            } catch (\Throwable $e) {
                // Keep existing fallback values if parsing fails.
            }
        }

        if (!empty($oldDepartureDateDisplay)) {
            try {
                $oldDepartureDateDisplay = \Illuminate\Support\Carbon::parse($oldDepartureDateDisplay)->format(
                    'F j, Y',
                );
            } catch (\Throwable $e) {
                // Leave user-entered value as-is if it cannot be parsed.
            }
        }

        if (!empty($oldDepartureTimeDisplay)) {
            try {
                $oldDepartureTimeDisplay = \Illuminate\Support\Carbon::parse($oldDepartureTimeDisplay)->format('h:i A');
            } catch (\Throwable $e) {
                // Leave user-entered value as-is if it cannot be parsed.
            }
        }

        $oldPriceMajorDisplay =
            $oldPriceMinor !== null && $oldPriceMinor !== ''
                ? number_format(((int) $oldPriceMinor) / 100, 2, '.', '')
                : '';

        $oldSegmentPrices = old('meta.segment_prices', $prefillRide->meta['segment_prices'] ?? []);
        if (!is_array($oldSegmentPrices)) {
            $oldSegmentPrices = [];
        }

        if (empty($oldSegmentPrices)) {
            $oldStopFrom = old('stop_from', []);
            $oldStopTo = old('stop_to', []);
            $oldStopPriceMinor = old('stop_price_minor', []);
            if (is_array($oldStopFrom) && is_array($oldStopTo) && is_array($oldStopPriceMinor)) {
                $routePointLabels = collect([
                    ['label' => $oldOriginLabel ?? ''],
                    ...collect($oldStops ?? [])->map(function ($stop) {
                        return ['label' => $stop['label'] ?? ''];
                    })->all(),
                    ['label' => $oldDestinationLabel ?? ''],
                ])
                    ->map(fn($point) => trim((string) ($point['label'] ?? '')))
                    ->values()
                    ->all();

                $derivedSegmentPrices = [];
                foreach ($oldStopFrom as $segmentIndex => $fromLabel) {
                    $toLabel = $oldStopTo[$segmentIndex] ?? null;
                    $priceMinor = $oldStopPriceMinor[$segmentIndex] ?? null;
                    
                    if ($fromLabel === null || $toLabel === null || $priceMinor === null) {
                        continue;
                    }

                    $normalizedFromLabel = trim((string) $fromLabel);
                    $normalizedToLabel = trim((string) $toLabel);
                    $resolvedFromIndex = null;
                    $resolvedToIndex = null;

                    foreach ($routePointLabels as $pointIndex => $pointLabel) {
                        if ($resolvedFromIndex === null && strcasecmp($pointLabel, $normalizedFromLabel) === 0) {
                            $resolvedFromIndex = $pointIndex;
                            continue;
                        }

                        if (
                            $resolvedFromIndex !== null &&
                            $pointIndex > $resolvedFromIndex &&
                            strcasecmp($pointLabel, $normalizedToLabel) === 0
                        ) {
                            $resolvedToIndex = $pointIndex;
                            break;
                        }
                    }
                        
                    $derivedSegmentPrices[] = [
                        'from_index' => $resolvedFromIndex,
                        'to_index' => $resolvedToIndex,
                        'from_label' => (string) $fromLabel,
                        'to_label' => (string) $toLabel,
                        'price_minor' => (int) $priceMinor,
                    ];
                }

                $oldSegmentPrices = $derivedSegmentPrices;
            }
        }

        if (
            empty($oldSegmentPrices) &&
            // $isEditMode &&
            isset($prefillRide->rideStopSegments) &&
            $prefillRide->rideStopSegments->isNotEmpty()
        ) {
            $oldSegmentPrices = $prefillRide->rideStopSegments
                ->filter(function ($segment) {
                    return $segment->fromStop && $segment->toStop;
                })
                ->sortBy(function ($segment) {
                    return [
                        (int) ($segment->fromStop->stop_order ?? 0),
                        (int) ($segment->toStop->stop_order ?? 0),
                    ];
                })
                ->values()
                ->map(function ($segment) {
                    return [
                        'from_index' => max(0, (int) ($segment->fromStop->stop_order ?? 1) - 1),
                        'to_index' => max(0, (int) ($segment->toStop->stop_order ?? 1) - 1),
                        'from_label' => (string) ($segment->fromStop->label ?? ''),
                        'to_label' => (string) ($segment->toStop->label ?? ''),
                        'price_minor' => (int) ($segment->price_minor ?? 0),
                    ];
                })
                ->all();
        }

        $hasInitialValidStops = collect($oldStops)->contains(function ($stop) {
            return !empty(trim((string) ($stop['label'] ?? ''))) && !empty($stop['city_id']);
        });
        $showSegmentPriceMode = $hasInitialValidStops || !empty($oldSegmentPrices);


        // Check if pink_rides and extra_care_rides are initially checked
        $pinkRideChecked = false;
        $extraCareRideChecked = false;

    @endphp

    <div class="container px-4 mx-auto my-14 page-post_a_ride">

        @if (session('error'))
            <div id="errorModal" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div onclick="closeModal()" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                    <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                        <div
                            class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg w-full modal-border1">
                            <button type="button" onclick="closeModal()"
                                class="absolute top-4 right-4 text-gray-400 hover:text-gray-500 z-50">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                            <div class="bg-white px-4 mt-10 sm:mt-1 pb-4 pt-10 sm:pb-4">
                                <div class="sm:flex sm:items-start justify-center">
                                    <svg width="64px" height="64px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#ff0000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M12 10V13" stroke="#db0000" stroke-width="2" stroke-linecap="round"></path> <path d="M12 16V15.9888" stroke="#db0000" stroke-width="2" stroke-linecap="round"></path> <path d="M10.2518 5.147L3.6508 17.0287C2.91021 18.3618 3.87415 20 5.39912 20H18.6011C20.126 20 21.09 18.3618 20.3494 17.0287L13.7484 5.147C12.9864 3.77538 11.0138 3.77538 10.2518 5.147Z" stroke="#db0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
                                </div>
                                <div class="text-center">
                                    <div class="">
                                        <h3 class="text-3xl text-center font-FuturaMdCnBT text-gray-900 mb-4"
                                            id="modal-title">{!! session('heading') !!}</h3>
                                    </div>
                                    <div class="mt-2 w-full">
                                        <p class="can-exp-p text-center">{!! session('error') !!}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="px-4 pb-6 pt-4  sm:flex sm:flex-row-reverse sm:px-6 justify-center">
                                <button type="button" onclick="closeModal()"
                                    class="button-exp-fill">{{ $siteText['close_btn_text'] }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if (($routeType ?? '') !== 'edit')
            <div class="flex justify-end md:items-center">
                <a href="{{ $isNewForm && ($routeType ?? '') === 'create' ? route('post_ride_again_completed', ['lang' => optional($selectedLanguage)->abbreviation]) : route('post_ride_again', ['lang' => optional($selectedLanguage)->abbreviation]) }}"
                    class="button-exp-green-fill">
                    {{ $postRidePage->post_arrived_again_label ?? 'Repost a Previous Ride' }}
                </a>
            </div>
        @endif
        <div class="flex flex-col sm:flex-col md:flex-row lg:flex-row justify-between md:items-center">
            <h1>
                @if(($routeType) == 'edit')
                {{ $postRidePage->main_heading_update ?? 'Edit Ride' }}
                @elseif($routeType == 'copy')
                {{ $postRidePage->main_heading ?? 'Post a Ride' }} <span class="text-gray-400 text-xl">COPY RIDE</span>
                @elseif($routeType == 'repost')
                Repost a Ride
                @else
                {{ $postRidePage->main_heading ?? 'Post a Ride' }}
                @endif
            </h1>
            <p>
                <span class="text-red-500">* {{ $postRidePage->indicates_required_field_text ?? 'Indicates required fields' }} </span>
            </p>
        </div>
        <form method="POST"
            action="{{ ($routeType ?? '') === 'edit' ? route('update_ride', ['lang' => optional($selectedLanguage)->abbreviation, 'ride_id' => $ride->id]) : route('post_ride.store') }}"
            enctype="multipart/form-data" id="post-ride-form">
            @csrf
            @if (($routeType ?? '') === 'edit')
                @method('PUT')
            @endif
            
                <div class="bg-white rounded-lg overflow-hidden shadow-3xl">
                    <h3 class="text-2xl bg-primary text-white py-2 px-4">
                        {{ $postRidePage->ride_info_heading ?? 'Ride Info' }}
                    </h3>
                    <div class="bg-white p-4 space-y-3">
                        <div class="flex flex-col md:flex-row justify-between items-start">
                            <div class="w-full md:w-[45%] ">
                                <label class="block text-sm mb-4 required">{{ $postRidePage->from_label }}</label>
                                @livewire(
                                    'px.city-autocomplete',
                                    [
                                        'field' => 'origin',
                                        'placeholder' => $postRidePage->from_placeholder,
                                        'initialLabel' => $oldOriginLabel ?? old('origin.label'),
                                        'initialCityId' => $oldOriginCityId ?? old('origin.city_id'),
                                    ],
                                    key('px-origin-city-autocomplete')
                                )
                                @error('origin.label')
                                    <div class="tooltip-error shadow-lg">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="w-full md:w-[10%] md:mt-10 flex justify-center items-start">
                                <button type="button" onclick="swapLocations()">
                                    <img src="{{ asset('assets/arrow.png') }}" class="w-10 h-10 mx-auto" alt="">
                                </button>
                            </div>
                            <div class="w-full md:w-[45%] ">
                                <label class="block text-sm mb-4 required">{{ $postRidePage->to_label }}</label>
                                @livewire(
                                    'px.city-autocomplete',
                                    [
                                        'field' => 'destination',
                                        'placeholder' => $postRidePage->to_placeholder,
                                        'initialLabel' => $oldDestinationLabel ?? old('destination.label'),
                                        'initialCityId' => $oldDestinationCityId ?? old('destination.city_id'),
                                    ],
                                    key('px-destination-city-autocomplete')
                                )
                                @error('destination.label')
                                    <div class="tooltip-error shadow-lg">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="flex flex-col md:flex-row justify-between mt-4">
                            <div class="w-full md:w-[45%]">
                                <label class="block text-sm mb-4 required">{{ $postRidePage->pick_up_label }}</label>
                                <textarea name="pickup" rows="4" class="w-full rounded border-gray-300" autocomplete="off"
                                    placeholder="{{ $postRidePage->pick_up_placeholder }}">{{ $oldPickupLocation ?? old('pickup') }}</textarea>
                                @error('pickup')
                                    <div class="tooltip-error shadow-lg">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="w-full md:w-[45%]">
                                <label class="block text-sm mb-4 required">{{ $postRidePage->drop_off_label }}</label>
                                <textarea name="dropoff" rows="4" class="w-full rounded border-gray-300" autocomplete="off"
                                    placeholder="{{ $postRidePage->drop_off_placeholder }}">{{ $oldDropoffLocation ?? old('dropoff') }}</textarea>
                                @error('dropoff')
                                    <div class="tooltip-error shadow-lg">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="date_time" class="block text-gray-900 required">
                                {{ $postRidePage->date_time_label ?? 'Date and Time' }}
                            </label>
                            <div class="flex flex-col sm:flex-col md:flex-row lg:flow-row items-start mb-4 justify-between">
                                <div class="w-full md:w-[45%] mb-4">
                                    <div class="relative mt-2">
                                        <div class="absolute inset-y-0 start-0 flex items-center pl-2 pointer-events-none">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                                fill="currentColor" aria-hidden="true">
                                                <path fill="#888888" fill-rule="evenodd"
                                                    d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <input id="departure-at-date" name="date" value="{{ $oldDepartureDate }}"
                                            type="text"
                                            class="bg-gray-100 border pl-10 border-gray-200 text-gray-900 text-base lg:text-lg rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 mt-2 block w-full p-2.5"
                                            placeholder="" autocomplete="off">
                                    </div>
                                    @error('date')
                                        <div class="tooltip-error shadow-lg">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="w-full md:w-[10%] md:mt-4 text-center">
                                    <span class="text-center text-base lg:text-lg ">
                                        @isset($postRidePage->at_label)
                                            {{ $postRidePage->at_label }}
                                        @endisset
                                    </span>
                                </div>
                                <div class="w-full md:w-[45%] mb-4">
                                    <div class="relative mt-2">
                                        <div class="absolute inset-y-0 start-0 flex items-center pl-2 pointer-events-none">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <input type="text" id="departure-at-time" name="time"
                                            value="{{ $oldDepartureTime }}"
                                            class="bg-gray-100 border pl-10 border-gray-200 text-gray-900 text-base lg:text-lg rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 mt-2 block w-full p-2.5"
                                            placeholder="" autocomplete="off">
                                    </div>
                                    @error('time')
                                        <div class="tooltip-error shadow-lg">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="bg-white rounded-lg shadow-3xl mt-6">
                            <button type="button" id="px-stops-toggle"
                                class="bg-primary rounded-lg text-white w-full flex items-center justify-between text-left px-4 py-2 disabled:opacity-60 disabled:cursor-not-allowed"
                                {{ empty($oldOriginCityId) || empty($oldDestinationCityId) ? 'disabled' : '' }}
                                aria-expanded="{{ $stopsExpanded ? 'true' : 'false' }}" aria-controls="stops-content">
                                <h3 class="text-2xl">
                                    {{ $postRidePage->stop_along_the_way_label ?? 'Stops Along the Way' }}
                                </h3>
                                <svg id="px-stops-chevron"
                                    class="w-5 h-5 text-white transition-transform {{ $stopsExpanded ? 'rotate-180' : '' }}"
                                    viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.51a.75.75 0 01-1.08 0l-4.25-4.51a.75.75 0 01.02-1.06z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>

                            <div id="px-stops-content" class="px-4 py-4 {{ $stopsExpanded ? '' : 'hidden' }}">
                                @livewire(
                                    'px.stops-repeater',
                                    [
                                        'initialStops' => $oldStops,
                                        'originLabel' => $oldOriginLabel ?? old('from', ''),
                                        'destinationLabel' => $oldDestinationLabel ?? old('to', ''),
                                        'addStopBtnLabel' => $postRidePage->add_stop_btn_label ?? 'Add Stop',
                                        'stopAlongTheWayLabel' => $postRidePage->stops_along_the_way_label ?? 'Stop Along the Way',
                                        'stopsDeleteConfirmText' => $postRidePage->stops_remove_confirm_text ?? 'Are you sure you want to delete this stop row?',
                                        'removeBtnText' => $siteText['remove_btn_text'],
                                        'cancelBtnText' => $siteText['cancel_btn_text'],
                                    ],
                                    key('px-stops-repeater')
                                )
                            </div>
                        </div>

                        <div class="border my-4"></div>

                        <div class="flex items-center mb-4">
                            <input id="px-is-recurring" type="checkbox" name="recurring" value="1"
                                {{ (string) ($oldIsRecurring ? '1' : '0') === '1' ? 'checked' : '' }}
                                class="form-check-input">
                            <label for="px-is-recurring" class="ml-2 text-gray-900">
                                @isset($postRidePage->recurring_label)
                                    {{ $postRidePage->recurring_label }}
                                @endisset
                            </label>
                        </div>

                        <div id="px-recurring-fields">
                            <div class="flex items-start flex-col md:flex-row mb-4 justify-between">
                                <div class="w-full md:w-[45%] mb-4">
                                    <label for="recurring_type" class="block mb-2 text-gray-900">
                                        @isset($postRidePage->recurring_type_label)
                                            {{ $postRidePage->recurring_type_label }}
                                        @endisset
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative mt-2">
                                        <select id="type" name="recurring_type"
                                            class="bg-gray-100 border border-gray-200 text-gray-900 text-base lg:text-lg rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 mt-2 block w-full p-2.5">
                                            <option value="" {{ ($oldRecurringType ?? '') === '' ? 'selected' : '' }}>
                                                Select
                                            </option>
                                            <option value="Daily"
                                                {{ ($oldRecurringType ?? '') === 'Daily' ? 'selected' : '' }}>
                                                Daily
                                            </option>
                                            <option value="Weekly"
                                                {{ ($oldRecurringType ?? '') === 'Weekly' ? 'selected' : '' }}>
                                                Weekly
                                            </option>
                                        </select>
                                    </div>
                                    @error('recurring_type')
                                        <div class="tooltip-error shadow-lg">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="w-full md:w-[10%] hidden md:block mt-12 text-center">
                                    <span class="text-center text-base lg:text-lg ">
                                        
                                    </span>
                                </div>
                                <div class="w-full md:w-[45%] mb-4">
                                    <label for="recurring_trips" class="block mb-2 text-gray-900">
                                        @isset($postRidePage->recurring_trips_label)
                                            {{ $postRidePage->recurring_trips_label }}
                                        @endisset
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative mt-2">
                                        <input type="number" min="0" max="10" name="recurring_trips"
                                            value="{{ $oldRecurringTrips }}"
                                            @isset($postRidePage->recurring_trips_placeholder)
                                                placeholder="{{ $postRidePage->recurring_trips_placeholder }}"
                                            @endisset
                                            class="bg-gray-100 border border-gray-200 text-gray-900 text-base lg:text-lg rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 mt-2 block w-full p-2.5">
                                    </div>
                                    @error('recurring_trips')
                                        <div class="tooltip-error shadow-lg">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-6">
                    <div class="bg-white rounded-lg overflow-hidden shadow-3xl">
                        <h3 class="text-2xl bg-primary text-white py-2 px-4">
                            @isset($postRidePage->meeting_drop_off_description_label)
                                {{ $postRidePage->meeting_drop_off_description_label }}
                            @endisset
                            <span class="text-white">*</span>
                        </h3>
                        <div class="bg-white p-4 space-y-3">
                            <textarea id="meeting" rows="5" name="details"
                                class="block p-2.5 w-full text-gray-900 bg-gray-100 rounded border border-gray-200 text-base lg:text-lg focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 mt-2"
                                @isset($postRidePage->meeting_drop_off_description_placeholder)
                                    placeholder="{{ $postRidePage->meeting_drop_off_description_placeholder }}"
                                @endisset>{{ old('details', optional($ride)->details) }}</textarea>
                            @error('details')
                                <div class="tooltip-error shadow-lg">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mt-6">
                    <div class="bg-white rounded-lg overflow-hidden shadow-3xl">
                        <div class="text-2xl bg-primary text-white py-2 px-4">
                            <h3 class="text-2xl">
                                @isset($postRidePage->seats_label)
                                    {{ $postRidePage->seats_label }}
                                @endisset
                                <span class="text-white">*</span>
                            </h3>
                        </div>
                        <div class="bg-white p-4">
                            <div class="relative">
                                <div class="flex items-center flex-wrap gap-2 mt-2">
                                    @for ($i = 1; $i <= 7; $i++)
                                        <div class="relative">
                                            <label class="cursor-pointer inline-block"
                                                for="number-of-seat-{{ $i }}">
                                                <input id="number-of-seat-{{ $i }}" name="seats_total"
                                                    type="radio" value="{{ $i }}" class="hidden"
                                                    @checked((string) ($oldSeatsTotal ?? old('seats_total', '1')) === (string) $i) onchange="seat_selected(this)"
                                                    data-parsley-required="true"
                                                    data-parsley-trigger="blur focusout change"
                                                    data-parsley-required-message="Please select the available seats."
                                                    data-parsley-errors-container="#parsley-seats-error">
                                                <span class="relative inline-block w-6 h-6 md:w-8 md:h-8">
                                                    <img src="{{ (int) ($oldSeatsTotal ?? old('seats_total', 0)) >= $i ? asset('assets/seat-hover-1.png') : asset('assets/seat.png') }}"
                                                        class="w-8 h-8 object-cover cursor-pointer seat-image seat-unselect-{{ $i }}"
                                                        alt="">
                                                    <span
                                                        class="absolute mt-2 inset-0 flex items-center justify-center text-sm seat-number seat-number-{{ $i }} {{ (int) ($oldSeatsTotal ?? old('seats_total', 0)) >= $i ? 'text-green-300' : '' }}">{{ $i }}</span>
                                                </span>
                                            </label>
                                        </div>
                                    @endfor
                                </div>
                                @error('seats')
                                    <div class="tooltip-error shadow-lg">{{ $message }}</div>
                                @enderror
                                @error('seats_total')
                                    <div class="tooltip-error shadow-lg">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 mt-6 gap-4">
                                <div>
                                    <label for="" class="text-gray-900 mb-2">
                                        @isset($postRidePage->seats_middle_label)
                                            {{ $postRidePage->seats_middle_label }}
                                        @endisset
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <ul class="grid gap-2 grid-cols-2 mt-2">
                                        <li>
                                            <input type="radio" id="2-seats" name="middle_seats" value="2"
                                                class="hidden peer"
                                                {{ old('middle_seats', $ride->middle_seats) == '2' ? 'checked' : '' }}>
                                            <label for="2-seats"
                                                class="inline-flex items-center justify-center w-full p-1 text-gray-800 bg-white border-2 border-gray-100 rounded cursor-pointer peer-checked:border-green-500 peer-checked:border-2 peer-checked:text-green-500 hover:border-2 hover:border-green-500">
                                                <span class="font-medium text-md">
                                                    2 {{ $postRidePage->seats_text ?? 'seats' }}
                                                </span>
                                            </label>
                                        </li>
                                        <li>
                                            <input type="radio" id="3-seats" name="middle_seats" value="3"
                                                class="hidden peer"
                                                {{ old('middle_seats', $ride->middle_seats) == '3' ? 'checked' : '' }}>
                                            <label for="3-seats"
                                                class="inline-flex items-center justify-center w-full p-1 text-gray-800 bg-white border-2 border-gray-100 rounded cursor-pointer peer-checked:border-green-500 peer-checked:border-2 peer-checked:text-green-500 hover:border-2 hover:border-green-500">
                                                <span class="font-medium text-md">3
                                                    {{ $postRidePage->seats_text ?? 'seats' }}</span>
                                            </label>
                                        </li>
                                    </ul>
                                    @error('middle_seats')
                                        <div class="tooltip-error shadow-lg">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div>
                                    <label for="back_seats" class="text-gray-900 mb-2">
                                        @isset($postRidePage->seats_back_label)
                                            {{ $postRidePage->seats_back_label }}
                                        @endisset
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <ul class="grid gap-2 grid-cols-2 mt-2">
                                        <li>
                                            <input type="radio" id="2-back_seats" name="back_seats" value="2"
                                                class="hidden peer"
                                                {{ old('back_seats', $ride->back_seats) == '2' ? 'checked' : '' }}>
                                            <label for="2-back_seats"
                                                class="inline-flex items-center justify-center w-full p-1 text-gray-800 bg-white border-2 border-gray-100 rounded cursor-pointer peer-checked:border-green-500 peer-checked:border-2 peer-checked:text-green-500 hover:border-2 hover:border-green-500">
                                                <span class="font-medium text-md">
                                                    2 {{ $postRidePage->seats_text ?? 'seats' }}
                                                </span>
                                            </label>
                                        </li>
                                        <li>
                                            <input type="radio" id="3-back_seats" name="back_seats" value="3"
                                                class="hidden peer"
                                                {{ old('back_seats', $ride->back_seats) == '3' ? 'checked' : '' }}>
                                            <label for="3-back_seats"
                                                class="inline-flex items-center justify-center w-full p-1 text-gray-800 bg-white border-2 border-gray-100 rounded cursor-pointer peer-checked:border-green-500 peer-checked:border-2 peer-checked:text-green-500 hover:border-2 hover:border-green-500">
                                                <span class="font-medium text-md">3
                                                    {{ $postRidePage->seats_text ?? 'seats' }}</span>
                                            </label>
                                        </li>
                                    </ul>
                                    @error('back_seats')
                                        <div class="tooltip-error shadow-lg">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="duration" id="px-duration-input" value="{{ old('duration', 0) }}" />

                <div id="px-segment-distance-loader" class="fixed top-0 left-0 right-0 z-50 pointer-events-none" aria-hidden="true">
                    <div class="px-top-progress-track">
                        <div class="px-top-progress-bar"></div>
                    </div>
                </div>

                <div class="mt-6 bg-white rounded-lg overflow-visible shadow-3xl">
                    <div class="text-2xl bg-primary text-white py-2 px-4 rounded-t-lg">
                        <h3 class="text-2xl">
                            {{ $postRidePage->price_payment_heading ?? 'Price and Payment Method' }}
                            <span class="text-white">*</span>
                        </h3>
                    </div>
                    <div class="bg-white p-4 rounded-b-lg">
                        <label id="px-price-label" class=" text-gray-700 font-medium required">
                            {{ $postRidePage->price_per_seat_label ?? 'Price per Seat' }}
                        </label>
                        <div id="px-price-single-wrap" class="{{ $showSegmentPriceMode ? 'hidden' : '' }}">
                            <input id="px-price-minor-input" name="{{ $showSegmentPriceMode ? '' : 'price_minor' }}" value="{{ $oldPriceMajorDisplay }}"
                                type="number" min="0" step="0.01" class="w-full rounded border-gray-300"
                                {{ $showSegmentPriceMode ? 'disabled' : '' }}
                                placeholder="e.g. 25.00">
                            <p id="px-price-single-expected" class="mt-2 text-xs text-gray-500 hidden"></p>
                            @error('price_minor')
                                <div class="tooltip-error shadow-lg">{{ $message }}</div>
                            @enderror
                        </div>
                        <div id="px-price-segments-wrap" class="{{ $showSegmentPriceMode ? '' : 'hidden' }} space-y-3">
                            <div id="px-price-segments-list" class="space-y-2"></div>
                            <div
                                class="flex items-center justify-between rounded-md bg-gray-50 border border-gray-200 px-3 py-2">
                                <span class="text-gray-700">Parent route price per seat</span>
                                <span id="px-price-segments-total" class="text-gray-900">0.00</span>
                            </div>
                            <input type="hidden" id="px-price-minor-hidden"
                                name="{{ $showSegmentPriceMode ? 'price_minor' : '' }}"
                                value="{{ (int) ($oldPriceMinor ?? old('price_minor', 0)) }}">
                            <input type="hidden" id="px-destination-price-delta-initial"
                                value="{{ $oldDestinationPriceDeltaMinor ?? old('destination.price_delta_minor', 0) }}">
                            <script type="application/json" id="px-initial-segment-prices-json">{!! json_encode(array_values($oldSegmentPrices), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>
                            <input type="hidden" name="distance_meters" id="px-distance-meters-input"
                                value="{{ old('distance_meters', '') }}">
                            @error('price_minor')
                                <div class="tooltip-error shadow-lg">{{ $message }}</div>
                            @enderror
                        </div>
                        @error('stops.*.price_delta_minor')
                            <div class="tooltip-error shadow-lg">{{ $message }}</div>
                        @enderror

                        <div class="mt-6">
                            <label for="" class="block mb-2 font-medium text-gray-900">
                                @isset($postRidePage->payment_methods_label)
                                    {{ $postRidePage->payment_methods_label }}
                                @endisset
                                <span class="text-red-500">*</span>
                            </label>
                            <div class="space-y-2 mt-2">
                                @foreach ($rideFeatureOptions['payment_method'] as $slug=>$payment_method)
                                    <div class="flex items-center space-x-1 md:space-x-2 mb-2 mr-2 lg:mr-2">
                                        <input id="{{$slug}}" name="payment_method" type="radio"
                                            value="{{ $payment_method->id }}"
                                            {{ $oldPaymentMethod == $payment_method->id ? 'checked' : '' }}
                                            class="form-check-input">
                                        <label for="{{$slug}}"
                                            class="ml-3 font-normal text-gray-900 flex items-center space-x-1">
                                            @isset($payment_method->icon)
                                                <div class="w-8 h-6">
                                                    <img src="{{ asset('home_page_icons/' . $payment_method->icon) }}"
                                                        class="mx-auto w-full h-full object-contain" alt="">
                                                </div>
                                            @endisset
                                            <span class="">
                                                {{ $payment_method->name }}
                                            </span>
                                            <span class="inline-flex cursor-help payment-method-tooltip"
                                                data-tippy-content="{{ $payment_method->tooltip ?? '' }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" class="bi bi-info-circle-fill text-black"
                                                    viewBox="0 0 16 16">
                                                    <path
                                                        d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
                                                </svg>
                                            </span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            @error('payment_method')
                                <div class="tooltip-error shadow-lg">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mt-6">
                    <div class="bg-white rounded-lg shadow-3xl">
                        <div class="text-2xl bg-primary rounded-t-lg text-white py-2 px-4">
                            <h3 class="text-2xl">
                                @isset($postRidePage->booking_label)
                                    {{ $postRidePage->booking_label }}
                                @endisset
                                <span class="text-white">*</span>
                            </h3>
                        </div>
                        <div class="bg-white p-4">
                            <ul class="grid w-full gap-6 md:grid-cols-2">
                                @foreach ($rideFeatureOptions['booking_method'] as $slug=>$booking_method)
                                    <li>
                                        <input type="radio" id="{{$slug}}_booking" name="booking_method"
                                            value="{{ $booking_method->id }}"
                                            {{ $oldBookingMethod == $booking_method->id ? 'checked' : '' }}
                                            class="hidden peer">
                                        <label for="{{$slug}}_booking"
                                            class="inline-flex items-center space-x-3 w-full p-4 text-gray-800 bg-white border-2 border-gray-100 rounded cursor-pointer peer-checked:border-green-500 peer-checked:border-2 peer-checked:text-green-500 hover:border-2 hover:border-green-500">
                                            <img class="w-12 h-12"
                                                src="{{ asset('home_page_icons/' . $booking_method->icon) }}"
                                                alt="">
                                            <span class="font-medium text-xl">
                                                {{ $booking_method->name }}
                                            </span>
                                            <span class="inline-flex cursor-help"
                                                data-tippy-content="{{ $booking_method->tooltip ?? '' }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" class="bi bi-info-circle-fill text-black"
                                                    viewBox="0 0 16 16">
                                                    <path
                                                        d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
                                                </svg>
                                            </span>
                                        </label>
                                    </li>
                                @endforeach
                            </ul>
                            @error('booking_method')
                                <div class="tooltip-error shadow-lg">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>


                <div class="bg-white rounded-lg overflow-hidden shadow-3xl mt-6">
                    <div class="text-2xl bg-primary text-white py-2 px-4">
                        <h3 class="text-2xl">
                            @isset($postRidePage->cancellation_policy_label)
                                {{ $postRidePage->cancellation_policy_label }}
                            @endisset
                            <span class="text-white">*</span>
                        </h3>
                    </div>
                    <div class="bg-white p-4">
                        <div>
                            <div class="space-y-2 mt-2">
                                @foreach ($rideFeatureOptions['cancellation'] as $slug=>$cancellation)
                                    <div class="flex items-center space-x-1 md:space-x-2 mb-2 mr-2 lg:mr-2">
                                        <input id="{{$slug}}" name="booking_type" type="radio"
                                            value="{{ $cancellation->id }}"
                                            {{ old('booking_type', $ride->booking_type) == $cancellation->id || empty(old('booking_type')) ? 'checked' : '' }}
                                            class="form-check-input">
                                        <label for="{{$slug}}"
                                            class="ml-3 font-normal text-gray-900 flex items-center space-x-1">
                                            <span class="">
                                                {{ $cancellation->name }}
                                            </span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            @error('booking_type')
                                <div class="tooltip-error shadow-lg">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mt-6">
                    <div class="bg-white rounded-lg overflow-hidden shadow-3xl">
                        <div class="text-2xl bg-primary text-white py-2 px-4">
                            <h3 class="text-2xl">
                                @isset($postRidePage->vehicle_label)
                                    {{ $postRidePage->vehicle_label }}
                                @endisset
                                <span class="text-white">*</span>
                            </h3>
                        </div>
                        <div class="bg-white p-4">
                            <div class="flex flex-col sm:flex-col md:flex-row justify-between mb-4">
                                @php
                                    $oldVehicleMode = old('vehicle_mode') ?? 'existing';
                                @endphp
                                <div class="flex flex-wrap items-center gap-12">
                                    <label class="inline-flex items-center gap-2 text-sm">
                                        <input type="radio" name="vehicle_mode" value="existing"
                                            class="form-check-input" @checked($oldVehicleMode === 'existing')>
                                        {{ $postRidePage->existing_label ?? 'Existing' }}
                                    </label>
                                </div>
                                <div class="flex flex-wrap items-center gap-12">
                                    <label class="inline-flex items-center gap-2 text-sm">
                                        <input type="radio" name="vehicle_mode" value="add_new"
                                        class="form-check-input" @checked($oldVehicleMode === 'add_new')>
                                        {{ $postRidePage->add_vehicle_label ?? 'Add New Vehicle' }}
                                    </label>
                                </div>
                                <div class="flex flex-wrap items-center gap-12">
                                    <label class="inline-flex items-center gap-2 text-sm">
                                        <input type="radio" name="vehicle_mode" value="skip"
                                            class="form-check-input" @checked($oldVehicleMode === 'skip')>
                                        {{ $postRidePage->skip_label ?? 'Skip This Time' }}
                                    </label>
                                </div>
                            </div>

                            <div id="px-vehicle-existing-fields"
                                class="md:col-span-2 mt-8 {{ $oldVehicleMode !== 'existing' ? 'hidden' : '' }}">
                                <label
                                    class="block text-sm mb-4 required">{{ $vehiclePage->vehicle_type_label ?? 'Select vehicle' }}</label>
                                <select name="vehicle_id" class="w-full rounded border-gray-300">
                                    @foreach ($vehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}" @selected((string) old('vehicle_id', $oldVehicleId) === (string) $vehicle->id)>
                                            {{ $vehicle->make }} / {{ $vehicle->model }} / {{ $vehicle->year }}
                                            @if ($vehicle->type_label)
                                                / {{ $vehicle->type_label }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('vehicle_id')
                                    <div class="tooltip-error shadow-lg">{{ $message }}</div>
                                @enderror
                            </div>

                            <div id="px-vehicle-new-fields"
                                class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4 mt-8 {{ $oldVehicleMode !== 'add_new' ? 'hidden' : '' }}">
                                <div>
                                    <label class="block text-sm mb-2 required">{{ $vehiclePage->make_label }}</label>
                                    <input name="make" value="{{ old('make') }}"
                                        placeholder="{{ $vehiclePage->make_placeholder }}" type="text"
                                        class="w-full rounded border-gray-300">
                                    @error('make')
                                        <div class="tooltip-error shadow-lg">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm mb-2 required">{{ $vehiclePage->model_label }}</label>
                                    <input name="model" value="{{ old('model') }}"
                                        placeholder="{{ $vehiclePage->model_placeholder }}" type="text"
                                        class="w-full rounded border-gray-300">
                                    @error('model')
                                        <div class="tooltip-error shadow-lg">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div>
                                    <label
                                        class="block text-sm mb-2 required">{{ $vehiclePage->vehicle_type_label }}</label>
                                    <select name="vehicle_type" class="w-full rounded border-gray-300">
                                        <option value="">{{ $vehiclePage->vehicle_type_placeholder }}</option>
                                        @foreach ($vehicleTypes ?? collect() as $vehicleType)
                                            <option value="{{ $vehicleType['id'] }}" @selected(old('vehicle_type') == $vehicleType['id'])>
                                                {{ $vehicleType['label'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('vehicle_type')
                                        <div class="tooltip-error shadow-lg">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm mb-2 required">{{ $vehiclePage->year_label }}</label>
                                    <input name="year" value="{{ old('year') }}" placeholder="2026"
                                        maxlength="4" type="text" class="w-full rounded border-gray-300">
                                    @error('year')
                                        <div class="tooltip-error shadow-lg">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm mb-2 required">{{ $vehiclePage->color_label }}</label>
                                    <input name="color" value="{{ old('color') }}" maxlength="15" type="text"
                                        class="w-full rounded border-gray-300">
                                    @error('color')
                                        <div class="tooltip-error shadow-lg">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div>
                                    <label
                                        class="block text-sm mb-2 required">{{ $vehiclePage->license_plate_number_label }}</label>
                                    <input name="license_no" value="{{ old('license_no') }}" maxlength="8"
                                        type="text" class="w-full rounded border-gray-300">
                                    @error('license_no')
                                        <div class="tooltip-error shadow-lg">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm mb-2 required">{{ $vehiclePage->fuel_label }}</label>
                                    <div class="flex flex-wrap items-center gap-4 mt-2">
                                        <div class="flex items-center space-x-1.5 lg:space-x-3 mb-2 mr-2 lg:mr-2">
                                            <input id="" name="power_type" type="radio"
                                                value="{{ $vehiclePage->electric_checkbox_label }}"
                                                {{ old('power_type', $ride->power_type) == $vehiclePage->electric_checkbox_label ? 'checked' : '' }}
                                                class="form-check-input">
                                            <label for="" class="block text-gray-900">
                                                {{ $vehiclePage->electric_checkbox_label }}
                                            </label>
                                        </div>
                                        <div class="flex items-center space-x-1.5 lg:space-x-3 mb-2 mr-2 lg:mr-2">
                                            <input id="" name="power_type" type="radio"
                                                value="{{ $vehiclePage->hybrid_checkbox_label }}"
                                                {{ old('power_type', $ride->power_type) == $vehiclePage->hybrid_checkbox_label ? 'checked' : '' }}
                                                class="form-check-input">
                                            <label for="" class="block text-gray-900">
                                                {{ $vehiclePage->hybrid_checkbox_label }}
                                            </label>
                                        </div>
                                        <div class="flex items-center space-x-1.5 lg:space-x-3 mb-2 mr-2 lg:mr-2">
                                            <input id="" name="power_type" type="radio"
                                                value="{{ $vehiclePage->gas_checkbox_label }}"
                                                {{ old('power_type', $ride->power_type) == $vehiclePage->gas_checkbox_label || empty(old('power_type')) ? 'checked' : '' }}
                                                class="form-check-input">
                                            <label for="" class="block text-gray-900">
                                                {{ $vehiclePage->gas_checkbox_label }}
                                            </label>
                                        </div>
                                    </div>
                                    @error('power_type')
                                        <div class="tooltip-error shadow-lg">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm mb-2 required"></label>
                                    <label for="dropzone-file"
                                        class="flex flex-col items-center justify-center w-full h-auto border-2 border-gray-300 border-dashed rounded cursor-pointer bg-gray-100 hover:bg-gray-100">
                                        <div class="flex flex-col items-center justify-center pt-5 pb-6 p-4">
                                            @if ($ride->car_image)
                                                <img id="vehicle-image"
                                                    class="w-40 h-40 object-contain mb-4 cursor-pointer"
                                                    src="{{ $ride->car_image }}">
                                            @else
                                                <img id="vehicle-image"
                                                    class="w-12 h-12 object-contain mb-4 cursor-pointer"
                                                    src="{{ asset('assets/image-placeholder.png') }}">
                                            @endif
                                            <p class="text-sm lg:text-lg text-gray-900">
                                                {{ $vehiclePage->image_description_label ?? 'Upload car photo' }}
                                            </p>
                                            <p class="text-sm lg:text-base text-gray-900 font-normal">
                                                {{ $vehiclePage->images_option_placeholder ?? 'JPEG, JPG, PNG, GIF - 10MB max.' }}
                                            </p>
                                        </div>
                                        <input id="dropzone-file" name="vehicle_image" type="file" accept="image/*"
                                            class="hidden" />
                                    </label>
                                    @error('vehicle_image')
                                        <div class="tooltip-error shadow-lg">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 bg-white rounded-lg shadow-3xl">
                    <div class="text-2xl bg-primary rounded-t-lg text-white py-2 px-4">
                        <h3 class="text-2xl">
                            @isset($postRidePage->luggage_label)
                                {{ $postRidePage->luggage_label }}
                            @endisset
                            <span class="text-white">*</span>
                        </h3>
                    </div>
                    <div class="bg-white p-4">
                        <div class="border rounded-md divide-y">
                            @foreach ($rideFeatureOptions['luggage_size'] as $luggageOption)
                                <div class="flex items-center gap-4 p-3">
                                    <label for="{{ $luggageOption->slug }}"
                                        class="font-normal text-gray-900 flex space-x-1 flex items-center gap-2 w-full">
                                        <input id="{{ $luggageOption->slug }}"
                                            name="luggage" type="radio"
                                            value="{{ $luggageOption->id }}"
                                            @checked($oldLuggageSize
                                                ? $oldLuggageSize == $luggageOption->id
                                                : $loop->first)
                                            class="form-check-input">
                                            @isset($luggageOption->icon)
                                            <img class="w-10 h-10"
                                                src="{{ asset('home_page_icons/' . $luggageOption->icon) }}"
                                                alt="">
                                              @endisset  
                                        {{ $luggageOption->name }}
                                        <span class="inline-flex cursor-help"
                                            data-tippy-content="{{ $luggageOption->tooltip ?? '' }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                fill="currentColor" class="bi bi-info-circle-fill text-black"
                                                viewBox="0 0 16 16">
                                                <path
                                                    d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
                                            </svg>
                                        </span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        @error('luggage')
                            <div class="tooltip-error shadow-lg">{{ $message }}</div>
                        @enderror
                        <div class="mt-6 space-y-2">
                            <div class="flex items-center">
                                <input id="heating" type="checkbox" name="accept_more_luggage" value="1"
                                    {{ old('accept_more_luggage', $ride->accept_more_luggage) == '1' ? 'checked' : '' }}
                                    class="form-check-input mt-1">
                                <label for="heating" class="ml-2 font-normal text-gray-900 flex space-x-1">
                                    <span class="">
                                        @isset($postRidePage->luggage_checkbox_label1)
                                            {{ $postRidePage->luggage_checkbox_label1 }}
                                        @endisset
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 bg-white rounded-lg overflow-hidden shadow-3xl">
                    <div class="text-2xl bg-primary text-white py-2 px-4">
                        <h3 class="text-2xl">
                            @isset($postRidePage->smoking_label)
                                {{ $postRidePage->smoking_label }}
                            @endisset
                            <span class="text-white">*</span>
                        </h3>
                    </div>
                    <div class="bg-white p-4">
                        <div class="border rounded-md overflow-hidden divide-y">
                            @foreach ($rideFeatureOptions['smoking_allowed'] as $smokingOption)
                                <div class="flex items-center gap-4 p-3">
                                    <label for="{{ $smokingOption->slug }}"
                                        class="font-normal text-gray-900 flex space-x-1 flex items-center gap-2 w-full">
                                        <input id="{{ $smokingOption->slug }}"
                                            name="smoke" type="radio"
                                            value="{{ $smokingOption->id }}"
                                            @checked($oldSmokingAllowed
                                                ? $oldSmokingAllowed == $smokingOption->id
                                                : $loop->first)
                                            class="form-check-input">
                                        {{ $smokingOption->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        @error('smoke')
                            <div class="tooltip-error shadow-lg">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mt-6 bg-white rounded-lg overflow-hidden shadow-3xl">
                    <div class="text-2xl bg-primary text-white py-2 px-4">
                        <h3 class="text-2xl">
                            @isset($postRidePage->animals_label)
                                {{ $postRidePage->animals_label }}
                            @endisset
                            <span class="text-white">*</span>
                        </h3>
                    </div>
                    <div class="bg-white p-4">
                        <div class="border rounded-md overflow-hidden divide-y">
                            @foreach ($rideFeatureOptions['pets_allowed'] as $animalOption)
                                <div class="flex items-center gap-4 p-3">
                                    <label for="{{ $animalOption->slug }}"
                                        class="font-normal text-gray-900 flex space-x-1 flex items-center gap-2 w-full">
                                        <input id="{{ $animalOption->slug }}"
                                            name="animal_friendly" type="radio"
                                            value="{{ $animalOption->id }}"
                                            @checked($oldPetsAllowed
                                                ? $oldPetsAllowed == $animalOption->id
                                                : $loop->first)
                                            class="form-check-input">
                                        {{ $animalOption->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        @error('animal_friendly')
                            <div class="tooltip-error shadow-lg">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mt-6">
                    <div class="bg-white rounded-lg shadow-3xl">
                        <div class="text-2xl bg-primary rounded-t-lg text-white py-2 px-4">
                            <h3 class="text-2xl">
                                @isset($postRidePage->preferences_label)
                                    {{ $postRidePage->preferences_label }}
                                @endisset
                            </h3>
                        </div>
                        <div class="bg-white p-4">
                            <div class="space-y-2">
                                @foreach ($rideFeatureOptions['features'] as $featureOption)
                                    @php
                                    
                                        $disabled = false;
                                        $tooltipText = $featureOption->tooltip;
                                        $data_ride_option_code = '';

                                        if ($featureOption->slug === 'pink_rides') {
                                            $disabled = !$user->canUsePinkRide();
                                            $tooltipText = $user->pinkRideTooltip($postRidePage);
                                            $data_ride_option_code = 'pink_rides';
                                            $pinkRideChecked = in_array($featureOption->id, $oldSelectedFeatures);
                                        }

                                        if ($featureOption->slug === 'extra_care_rides') {
                                            $disabled = !$user->canUseExtraRide();
                                            $tooltipText = $user->extraRideTooltip();
                                            $extraCareRideChecked = in_array($featureOption->id, $oldSelectedFeatures);
                                            $data_ride_option_code = 'extra_care_rides';
                                        }
                                    @endphp

                                    <div class="flex items-center">

                                        <label for="{{ $featureOption->slug }}"
                                            class="font-normal text-gray-900 flex space-x-1 flex items-center gap-2 w-full">
                                            
                                            <input id="{{ $featureOption->slug }}" type="checkbox" name="features[]"
                                                value="{{ $featureOption->id }}" @checked(in_array($featureOption->id, $oldSelectedFeatures))
                                                @disabled($disabled)
                                                data-ride-option-code="{{$data_ride_option_code}}"
                                                class="form-check-input">
                                            <span @class([
                                                'text-pink-500 font-medium' => $featureOption->slug === 'pink_rides',
                                                'text-green-500 font-medium' =>
                                                    $featureOption->slug === 'extra_care_rides',
                                                'line-through' => $disabled,
                                            ])>
                                                {{ $featureOption->name }}
                                            </span>
                                            @if ($tooltipText)
                                                <span class="inline-flex cursor-help"
                                                    data-tippy-content="{{ $tooltipText }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                        fill="currentColor" class="bi bi-info-circle-fill text-black"
                                                        viewBox="0 0 16 16">
                                                        <path
                                                            d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
                                                    </svg>
                                                </span>
                                            @endif
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="">
                    <div class="mt-6">

                        <div class=" mt-6">
                            <div class="bg-white rounded-lg overflow-hidden shadow-3xl">
                                <div class="text-2xl bg-primary text-white py-2 px-4">
                                    <label for="more" class="">
                                        <h3 class="text-2xl">
                                            @isset($postRidePage->anything_to_add_label)
                                                {{ $postRidePage->anything_to_add_label }}
                                            @endisset
                                        </h3>
                                    </label>
                                </div>
                                <div class="bg-white p-4">
                                    <textarea id="more" rows="5" name="notes"
                                        class="block p-2.5 w-full mt-2 text-gray-900 bg-gray-100 text-base lg:text-lg rounded border border-gray-200 focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500"
                                        @isset($postRidePage->anything_to_add_placeholder)
                                    placeholder="{{ $postRidePage->anything_to_add_placeholder }}"
                                @endisset>{{ old('notes', $ride->notes) }}</textarea>
                                    @error('notes')
                                        <div class="tooltip-error shadow-lg">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mt-6">
                            <div class="bg-white rounded-lg overflow-hidden shadow-3xl">
                                <div class="text-2xl bg-primary text-white py-2 px-4">
                                    <h3 class="text-2xl">
                                        @isset($postRidePage->disclaimers_label)
                                            {{ $postRidePage->disclaimers_label }}
                                        @endisset
                                        <span class="text-white">*</span>
                                    </h3>
                                </div>
                                <div class="bg-white p-4">
                                    @isset($postRidePage->disclaimers_description)
                                        {!! str_replace(
                                            '<ol>',
                                            '<ol class="list-decimal list-inside">',
                                            str_replace(
                                                '<li>',
                                                '<li class="border-b border-gray-300 text-base lg:text-lg last:border-b-0 py-3">',
                                                $postRidePage->disclaimers_description,
                                            ),
                                        ) !!}
                                    @endisset
                                </div>
                                
                                <div id="pink-ride-disclaimer"
                                    class="bg-white px-4 border-t border-gray-200 {{ $isAvailablePinkRide ? '' : 'hidden' }}">
                                    <p class="border-gray-300 text-base lg:text-lg py-3 text-gray-900">
                                        <span>5. </span>
                                        {{ $postRidePage->pink_ride_disclaimer_text }}
                                    </p>
                                </div>
                                <div id="extra-care-ride-disclaimer"
                                    class="bg-white px-4 border-t border-gray-200 {{ $isAvailableExtraCareRide ? '' : 'hidden' }}">
                                    <p class="border-gray-300 text-base lg:text-lg py-3 text-gray-900">
                                        <!-- {{ $postRidePage->extra_care_ride_disclaimer_text ?? 'I understand that this is an Extra+ Ride, exclusive to members with highest review score. I will adhere to its standards' }} -->
                                        <span
                                            id="extra-care-disclaimer-number">{{ $isAvailablePinkRide ? '6.' : '5.' }}</span>
                                        {{ $postRidePage->extra_care_ride_disclaimer_text }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">

                            <div class="flex items-center my-4">
                                <input type="hidden" name="agree_terms" value="0">
                                <input id="agree_terms" type="checkbox" name="agree_terms" value="1"
                                    {{ old('agree_terms') == '1' ? 'checked' : '' }}
                                    class="form-check-input mt-3">
                                <label for="agree_terms"
                                    class="ml-2 font-normal text-gray-900 flex text-md items-center space-x-0.5">
                                    @isset($postRidePage->agree_terms_label)
                                        {!! $postRidePage->agree_terms_label !!}
                                    @endisset
                                    <span class="text-red-500">*</span>
                                </label>
                            </div>
                            @error('agree_terms')
                                <div class="tooltip-error shadow-lg">{{ $message }}</div>
                            @enderror
                            <div class="hidden lg:flex justify-center items-center mt-8">
                                <button
                                    class="post-ride-submit-btn bg-greenXS hover:bg-greenXS text-white text-base md:text-lg rounded font-FuturaMdCnBT hover:font-FuturaMdCnBT px-5 py-2 border border-greenXS hover:border-greenXS hover:text-white text-center focus:bg-greenXS focus:text-white active:text-white active:bg-greenXS disabled:opacity-70 disabled:cursor-not-allowed"
                                    type="submit">
                                    @if (in_array(($routeType ?? ''), ['repost', 'edit'], true))
                                        {{ $postRidePage->update_ride_label ?? 'Update Ride' }}
                                    @else
                                        @isset($postRidePage->submit_button_label)
                                            {{ $postRidePage->submit_button_label }}
                                        @endisset
                                    @endif
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="flex lg:hidden justify-center items-center mt-8">
                        <button
                            class="post-ride-submit-btn bg-greenXS hover:bg-greenXS text-white text-base md:text-lg rounded font-FuturaMdCnBT hover:font-FuturaMdCnBT px-5 py-2 border border-greenXS hover:border-greenXS hover:text-white text-center focus:bg-greenXS focus:text-white active:text-white active:bg-greenXS disabled:opacity-70 disabled:cursor-not-allowed"
                            type="submit">
                            @if (in_array(($routeType ?? ''), ['repost', 'edit'], true))
                                {{ $postRidePage->update_ride_label ?? 'Update Ride' }}
                            @else
                                @isset($postRidePage->submit_button_label)
                                    {{ $postRidePage->submit_button_label }}
                                @endisset
                            @endif
                        </button>
                    </div>
                </div>
        </form>

    </div>

    @php
        $projectTimezone = config('app.timezone');
        if(empty($projectToday)){
            // in case ofnot repost
            $projectNow = \Carbon\Carbon::now($projectTimezone);
            $projectToday = $projectNow->format('Y-m-d');
        }
    @endphp

    <!-- Modal for 5+ seats warning -->
    <div id="pxSeatsWarningModal" class="hidden fixed inset-0 z-50" aria-labelledby="px-seats-modal-title"
        role="dialog" aria-modal="true">
        <div onclick="closePxSeatsWarningModal()" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity">
        </div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                <div
                    class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
                    <button type="button" onclick="closePxSeatsWarningModal()"
                        class="absolute top-4 right-4 text-gray-400 hover:text-gray-500 z-50">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    <div class="bg-white px-4 mt-10 sm:mt-1 pb-4 pt-16 sm:p-6 sm:pb-4 sm:pt-16">
                        <div class="text-center sm:ml-4 sm:mt-0 sm:text-left">
                            <div class="">
                                <h3 class="text-3xl text-center font-FuturaMdCnBT text-gray-900 mb-4"
                                    id="px-seats-modal-title">{{ $postRidePage->seats_warning_modal_heading ?? 'Heads up for 5+ seats' }}</h3>
                            </div>
                            <div class="mt-2 w-full">
                                <p class="can-exp-p text-center">{{ $postRidePage->seats_warning_modal_paragraph ?? 'Please note that for large vehicles, your total trip collection must stay within non-commercial limits. To keep this a standard carpool, we suggest a lower price per seat. By law, total contributions cannot exceed the standard reimbursement limit ($0.72/km).' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="px-4 pb-6 pt-4 flex items-center space-x-2 justify-center">
                        <button type="button" onclick="closePxSeatsWarningModal()" class="button-exp-fill">{{ $postRidePage->seats_warning_modal_got_it_btn ?? 'Got it' }}</button>
                        <button type="button"
                            onclick="window.location.href='{{ route('cost_sharing_policy', ['lang' => optional($selectedLanguage)->abbreviation ?? 'en']) }}'"
                            class="button-exp-no-fill inline-block text-center">{{ $postRidePage->seats_warning_modal_learn_more_btn ?? 'Learn more about limits' }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Price Error (Exceeds $0.72/km per seat) -->
    <div id="pxPriceErrorModal" class="hidden fixed inset-0 z-50" aria-labelledby="px-price-error-modal-title"
        role="dialog" aria-modal="true">
        <div onclick="adjustPxPriceFromError()" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                <div
                    class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
                    <button type="button" onclick="adjustPxPriceFromError()"
                        class="absolute top-4 right-4 text-gray-400 hover:text-gray-500 z-50">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    <div class="bg-white px-4 mt-10 sm:mt-1 pb-4 pt-16 sm:p-6 sm:pb-4 sm:pt-16">
                        <div class="text-center sm:ml-4 sm:mt-0 sm:text-left">
                            <div class="">
                                <h3 class="text-3xl text-center font-FuturaMdCnBT text-gray-900 mb-4"
                                    id="pxPriceErrorHeading">{{ $postRidePage->price_error_heading ?? 'Price Limit Exceeded' }}</h3>
                            </div>
                            <div class="mt-2 w-full">
                                <p class="can-exp-p text-center mb-3" id="pxPriceErrorParagraph1"></p>
                                <p class="can-exp-p text-center mb-3" id="pxPriceErrorParagraph2"></p>
                                <p class="can-exp-p text-center" id="pxPriceErrorParagraph3"></p>
                            </div>
                        </div>
                    </div>
                    <div class="px-4 pb-6 pt-4 flex items-center space-x-2 sm:space-x-4 sm:px-6 justify-center">
                        <button type="button" id="pxPriceErrorAdjustBtn" onclick="adjustPxPriceFromError()"
                            class="button-exp-fill">{{ $postRidePage->price_error_adjust_btn_label ?? 'Adjust Price' }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Validation Error -->
    <div id="pxValidationErrorModal" class="hidden fixed inset-0 z-50" aria-labelledby="px-validation-error-modal-title"
        role="dialog" aria-modal="true">
        <div onclick="closePxValidationErrorModal()" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity">
        </div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                <div
                    class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
                    <button type="button" onclick="closePxValidationErrorModal()"
                        class="absolute top-4 right-4 text-gray-400 hover:text-gray-500 z-50">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    <div class="bg-white px-4 mt-10 sm:mt-1 pb-4 pt-16 sm:p-6 sm:pb-4 sm:pt-16">
                        <div class="text-center sm:ml-4 sm:mt-0 sm:text-left">
                            <div class="mx-auto h-16 w-16 flex items-center justify-center mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="2" stroke="currentColor" class="w-12 h-12 text-red-500">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                </svg>
                            </div>
                            <h3 class="text-3xl text-center font-FuturaMdCnBT text-gray-900 mb-4"
                                id="pxValidationErrorHeading">Validation Failed</h3>
                            <div class="mt-2 w-full">
                                <p class="can-exp-p text-center text-gray-700" id="pxValidationErrorParagraph"></p>
                            </div>
                        </div>
                    </div>
                    <div class="px-4 pb-6 pt-4 flex items-center justify-center sm:px-6">
                        <button type="button" onclick="closePxValidationErrorModal()"
                            class="button-exp-fill">{{ $siteText['close_btn_text'] ?? 'Close' }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Price Warning (Exceeds $0.66/km per seat but <= $0.72/km per seat) -->
    <div id="pxPriceWarningModal" class="hidden fixed inset-0 z-50" aria-labelledby="px-price-warning-modal-title"
        role="dialog" aria-modal="true">
        <div onclick="closePxPriceWarningModal()" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity">
        </div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                <div
                    class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
                    <button type="button" onclick="closePxPriceWarningModal()"
                        class="absolute top-4 right-4 text-gray-400 hover:text-gray-500 z-50">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    <div class="bg-white px-4 mt-10 sm:mt-1 pb-4 pt-16 sm:p-6 sm:pb-4 sm:pt-16">
                        <div class="text-center sm:ml-4 sm:mt-0 sm:text-left">
                            <div class="">
                                <h3 class="text-3xl text-center font-FuturaMdCnBT text-gray-900 mb-4">{{ $postRidePage->price_warning_heading ??  'Recommended Contribution Limit'}}</h3>
                            </div>
                            <div class="mt-2 w-full">
                                <p class="can-exp-p text-center mb-3" id="pxPriceWarningParagraph1"></p>
                                <p class="can-exp-p text-center" id="pxPriceWarningParagraph2"></p>
                            </div>
                        </div>
                    </div>
                    <div class="px-4 pb-6 pt-4 flex items-center space-x-2 sm:space-x-4 sm:px-6 justify-center">
                        <button type="button" id="pxPriceWarningAdjustBtn"
                            onclick="adjustPxPriceFromWarning(); return false;" class="button-exp-fill">{{ $postRidePage->price_warning_adjust_btn_label ?? 'Adjust Price' }}</button>
                        <button type="button" id="pxPriceWarningContinue" class="button-exp-fill">{{ $postRidePage->price_warning_keep_current_btn_label ?? 'Keep Current Price' }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection

@section('script')

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        // Swap origin and destination values
        function swapLocations() {
            // Find Livewire components by their wire:id
            const originComponent = document.querySelector('input[name="origin[label]"]')?.closest('[wire\\:id]');
            const destinationComponent = document.querySelector('input[name="destination[label]"]')?.closest('[wire\\:id]');

            // Get origin and destination city_id hidden inputs
            const originCityIdInput = document.querySelector('input[name="origin[city_id]"]');
            const destinationCityIdInput = document.querySelector('input[name="destination[city_id]"]');

            // Get origin pickup_location and destination dropoff_location textareas
            const originPickupTextarea = document.querySelector('textarea[name="origin[pickup_location]"]');
            const destinationDropoffTextarea = document.querySelector('textarea[name="destination[dropoff_location]"]');

            // Get current city IDs
            const originCityId = originCityIdInput ? parseInt(originCityIdInput.value) : null;
            const destinationCityId = destinationCityIdInput ? parseInt(destinationCityIdInput.value) : null;

            // Swap city IDs using Livewire's selectCity method
            if (window.Livewire && originComponent && destinationComponent) {
                const originWireId = originComponent.getAttribute('wire:id');
                const destinationWireId = destinationComponent.getAttribute('wire:id');

                if (originWireId && destinationWireId) {
                    try {
                        const originLivewire = window.Livewire.find(originWireId);
                        const destinationLivewire = window.Livewire.find(destinationWireId);

                        // Swap city selections
                        if (destinationCityId && originLivewire) {
                            originLivewire.call('selectCity', destinationCityId);
                        }
                        if (originCityId && destinationLivewire) {
                            destinationLivewire.call('selectCity', originCityId);
                        }

                        // If one is null, clear the other
                        if (!destinationCityId && originLivewire) {
                            originLivewire.set('query', '');
                            originLivewire.set('cityId', null);
                        }
                        if (!originCityId && destinationLivewire) {
                            destinationLivewire.set('query', '');
                            destinationLivewire.set('cityId', null);
                        }
                    } catch (e) {
                        
                        // Fallback: try direct input manipulation
                        const originInput = originComponent.querySelector('input[name="origin[label]"]');
                        const destinationInput = destinationComponent.querySelector('input[name="destination[label]"]');
                        if (originInput && destinationInput) {
                            const temp = originInput.value;
                            originInput.value = destinationInput.value;
                            destinationInput.value = temp;
                            originInput.dispatchEvent(new Event('input', {
                                bubbles: true
                            }));
                            destinationInput.dispatchEvent(new Event('input', {
                                bubbles: true
                            }));
                        }
                    }
                }
            } else {
                // Fallback: direct input manipulation if Livewire is not available
                const originInput = document.querySelector('input[name="origin[label]"]');
                const destinationInput = document.querySelector('input[name="destination[label]"]');
                if (originInput && destinationInput) {
                    const temp = originInput.value;
                    originInput.value = destinationInput.value;
                    destinationInput.value = temp;
                    originInput.dispatchEvent(new Event('input', {
                        bubbles: true
                    }));
                    destinationInput.dispatchEvent(new Event('input', {
                        bubbles: true
                    }));
                }
            }

            // Swap pickup/dropoff location values
            if (originPickupTextarea && destinationDropoffTextarea) {
                const tempLocation = originPickupTextarea.value;
                originPickupTextarea.value = destinationDropoffTextarea.value;
                destinationDropoffTextarea.value = tempLocation;

                // Trigger input event
                originPickupTextarea.dispatchEvent(new Event('input', {
                    bubbles: true,
                    cancelable: true
                }));
                destinationDropoffTextarea.dispatchEvent(new Event('input', {
                    bubbles: true,
                    cancelable: true
                }));
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            
            // Toggle vehicle form sections based on selected vehicle mode.
            const vehicleModeInputs = document.querySelectorAll('input[name="vehicle_mode"]');
            const existingVehicleFields = document.getElementById('px-vehicle-existing-fields');
            const newVehicleFields = document.getElementById('px-vehicle-new-fields');

            function setSectionEnabled(container, enabled) {
                if (!container) return;
                container.classList.toggle('hidden', !enabled);
                const fields = container.querySelectorAll('input, select, textarea');
                fields.forEach((field) => {
                    field.disabled = !enabled;
                });
            }

            function syncVehicleMode() {
                const selected = document.querySelector('input[name="vehicle_mode"]:checked')?.value || 'skip';
                setSectionEnabled(existingVehicleFields, selected === 'existing');
                setSectionEnabled(newVehicleFields, selected === 'add_new');
            }

            vehicleModeInputs.forEach((input) => {
                input.addEventListener('change', syncVehicleMode);
            });
            syncVehicleMode();

            // Expand/collapse ordered intermediate stops panel.
            const stopsToggle = document.getElementById('px-stops-toggle');
            const stopsContent = document.getElementById('px-stops-content');
            const stopsChevron = document.getElementById('px-stops-chevron');

            function syncStopsToggleState() {
                if (!stopsToggle || !stopsContent || !stopsChevron) {
                    return;
                }

                const canOpenStops = hasValidCityId('origin[city_id]') && hasValidCityId('destination[city_id]');
                stopsToggle.disabled = !canOpenStops;
                stopsToggle.setAttribute('aria-disabled', canOpenStops ? 'false' : 'true');

                if (!canOpenStops) {
                    stopsToggle.setAttribute('aria-expanded', 'false');
                    stopsContent.classList.add('hidden');
                    stopsChevron.classList.remove('rotate-180');
                }
            }

            if (stopsToggle && stopsContent && stopsChevron) {
                stopsToggle.addEventListener('click', function() {
                    if (stopsToggle.disabled) {
                        return;
                    }

                    const expanded = stopsToggle.getAttribute('aria-expanded') === 'true';
                    const nextExpanded = !expanded;
                    stopsToggle.setAttribute('aria-expanded', nextExpanded ? 'true' : 'false');
                    stopsContent.classList.toggle('hidden', !nextExpanded);
                    stopsChevron.classList.toggle('rotate-180', nextExpanded);
                });

                syncStopsToggleState();
            }

            // Enable recurring inputs only when recurring trip is checked.
            const recurringToggle = document.getElementById('px-is-recurring');
            const recurringFields = document.getElementById('px-recurring-fields');
            const recurringInputs = recurringFields ? recurringFields.querySelectorAll('select, input, textarea') :
                [];

            function syncRecurringState() {
                if (!recurringToggle || !recurringFields) return;
                const enabled = recurringToggle.checked;

                // Show/hide the recurring fields block
                recurringFields.classList.toggle('hidden', !enabled);
                recurringFields.classList.toggle('opacity-60', !enabled);

                // Enable/disable inner inputs so they don't submit when hidden
                recurringInputs.forEach((el) => {
                    el.disabled = !enabled;
                    // If you ever want hard required on these, uncomment:
                    // if (el.name === 'recurring_frequency' || el.name === 'recurring_trips') {
                    //     el.required = enabled;
                    // }
                });
            }

            if (recurringToggle) {
                recurringToggle.addEventListener('change', syncRecurringState);
                syncRecurringState();
            }

            // Seat visual selector: highlight all seats up to selected total.
            window.showPxSeatsWarningModal = function() {
                openModalById('pxSeatsWarningModal');
            };

            window.closePxSeatsWarningModal = function() {
                closeModalById('pxSeatsWarningModal');
            };

            window.seat_selected = function(th, showWarning = true) {
                const seat = parseInt(th?.value || '0', 10);
                const maxSeats = 7;

                for (let i = 1; i <= maxSeats; i++) {
                    const image = document.querySelector('.seat-image.seat-unselect-' + i);
                    const number = document.querySelector('.seat-number.seat-number-' + i);
                    const selected = i <= seat;

                    if (image) {
                        image.src = selected ? '{{ asset('assets/seat-hover-1.png') }}' :
                            '{{ asset('assets/seat.png') }}';
                    }
                    if (number) {
                        number.classList.toggle('text-green-300', selected);
                    }
                }

                if (showWarning && seat >= 5) {
                    window.showPxSeatsWarningModal();
                }
            };
            window.seat_selected(document.querySelector('input[name="seats_total"]:checked'), false);

            const postRideForm = document.querySelector('form[action*="px.post_ride.store"]') || document
                .querySelector('form[action*="px.post_ride.update"]') || document.querySelector('form');
            const priceLabel = document.getElementById('px-price-label');
            const priceSingleWrap = document.getElementById('px-price-single-wrap');
            const priceSegmentsWrap = document.getElementById('px-price-segments-wrap');
            const priceSegmentsList = document.getElementById('px-price-segments-list');
            const priceSegmentsTotal = document.getElementById('px-price-segments-total');
            const priceMinorInput = document.getElementById('px-price-minor-input');
            const priceSingleExpected = document.getElementById('px-price-single-expected');
            const priceMinorHiddenInput = document.getElementById('px-price-minor-hidden');
            const destinationPriceDeltaInitialInput = document.getElementById('px-destination-price-delta-initial');
            const initialSegmentPricesJson = document.getElementById('px-initial-segment-prices-json');
            const segmentDistanceLoader = document.getElementById('px-segment-distance-loader');
            const distanceMetersInput = document.getElementById('px-distance-meters-input');
            const durationInput = document.getElementById('px-duration-input');
            const segmentDistanceEstimateUrl = @json(route('post_ride.segment_distance_estimates', ['lang' => optional($selectedLanguage)->abbreviation]));
            let lastPxPriceValidationSignature = null;
            const initialSegmentPrices = (() => {
                if (!initialSegmentPricesJson) return [];
                try {
                    const parsed = JSON.parse(initialSegmentPricesJson.textContent || '[]');
                    return Array.isArray(parsed) ? parsed : [];
                } catch (error) {
                    return [];
                }
            })();
            const initialDistanceMeters = Number.parseInt(distanceMetersInput?.value || '0', 10) || 0;
            const initialDurationSeconds = Number.parseInt(durationInput?.value || '0', 10) || 0;
            const shouldPreferInitialSegmentMode = initialSegmentPrices.length > 0;
            let segmentDistanceState = {
                key: '',
                pendingKey: '',
                legDistancesMeters: [],
                legDurationsSeconds: [],
                segmentDistancesMeters: {},
                segmentDurationsSeconds: {},
                totalDistanceMeters: initialDistanceMeters,
                totalDurationSeconds: initialDurationSeconds,
            };
            let segmentDistanceDebounceTimer = null;

            function getSelectedCurrencySymbol() {
                return '$';
            }

            function setSegmentPriceInputsDisabled(isDisabled) {
                if (!priceSegmentsList) {
                    return;
                }

                priceSegmentsList.querySelectorAll('.px-segment-price-input').forEach((input) => {
                    input.disabled = isDisabled;
                });
            }

            function setSegmentDistanceLoading(isLoading) {
                setSegmentPriceInputsDisabled(isLoading);

                if (segmentDistanceLoader) {
                    segmentDistanceLoader.classList.toggle('is-active', isLoading);
                    segmentDistanceLoader.setAttribute('aria-hidden', isLoading ? 'false' : 'true');
                }
            }

            function toMinorInt(value) {
                const parsed = parseInt((value ?? '').toString().trim(), 10);
                return Number.isNaN(parsed) || parsed < 0 ? 0 : parsed;
            }

            function toMinorFromMajor(value) {
                const normalized = (value ?? '').toString().trim().replace(',', '.');
                const parsed = parseFloat(normalized);
                if (!Number.isFinite(parsed) || parsed < 0) {
                    return 0;
                }
                return Math.round(parsed * 100);
            }

            function toMajorFromMinor(minorValue) {
                if(minorValue == 0) return '';
                return (toMinorInt(minorValue) / 100).toFixed(2);
            }

            function getFirstInputValueByName(name) {
                const direct = document.querySelector(`input[name="${name}"]`);
                if (direct) {
                    return direct.value.trim();
                }
                const anyInput = Array.from(document.querySelectorAll('input[type="text"],input[type="hidden"]'))
                    .find((input) => input.name && input.name.includes(name));
                return anyInput ? anyInput.value.trim() : '';
            }

            function getStopsData() {
                const stopLabelInputs = document.querySelectorAll('input[name^="stops["][name$="[label]"]');
                const stopCityIdInputs = document.querySelectorAll('input[name^="stops["][name$="[city_id]"]');
                const stopIsPickupInputs = document.querySelectorAll('input[name^="stops["][name$="[is_pickup]"]');
                const stopIsDropoffInputs = document.querySelectorAll(
                    'input[name^="stops["][name$="[is_dropoff]"]');
                const stopPriceDeltaInputs = document.querySelectorAll(
                    'input[name^="stops["][name$="[price_delta_minor]"]');
                const stopsData = new Map();

                stopLabelInputs.forEach(function(input) {
                    const match = input.name.match(/^stops\[(\d+)\]\[label\]$/);
                    if (!match) return;
                    const index = parseInt(match[1], 10);
                    if (!stopsData.has(index)) {
                        stopsData.set(index, {});
                    }
                    stopsData.get(index).label = input.value.trim();
                });

                stopCityIdInputs.forEach(function(input) {
                    const match = input.name.match(/^stops\[(\d+)\]\[city_id\]$/);
                    if (!match) return;
                    const index = parseInt(match[1], 10);
                    if (!stopsData.has(index)) {
                        stopsData.set(index, {});
                    }
                    stopsData.get(index).cityId = input.value.trim();
                });

                stopIsPickupInputs.forEach(function(input) {
                    const match = input.name.match(/^stops\[(\d+)\]\[is_pickup\]$/);
                    if (!match) return;
                    const index = parseInt(match[1], 10);
                    if (!stopsData.has(index)) {
                        stopsData.set(index, {});
                    }
                    stopsData.get(index).isPickup = input.value;
                });

                stopIsDropoffInputs.forEach(function(input) {
                    const match = input.name.match(/^stops\[(\d+)\]\[is_dropoff\]$/);
                    if (!match) return;
                    const index = parseInt(match[1], 10);
                    if (!stopsData.has(index)) {
                        stopsData.set(index, {});
                    }
                    stopsData.get(index).isDropoff = input.value;
                });

                stopPriceDeltaInputs.forEach(function(input) {
                    const match = input.name.match(/^stops\[(\d+)\]\[price_delta_minor\]$/);
                    if (!match) return;
                    const index = parseInt(match[1], 10);
                    if (!stopsData.has(index)) {
                        stopsData.set(index, {});
                    }
                    stopsData.get(index).priceDeltaMinor = toMinorInt(input.value);
                });

                return Array.from(stopsData.keys())
                    .sort((a, b) => a - b)
                    .map((index) => ({
                        index,
                        ...stopsData.get(index)
                    }));
            }

            function getValidStopsData() {
                return getStopsData().filter((stop) => stop.label && stop.cityId);
            }

            function hasValidCityId(fieldName) {
                const cityIdValue = getFirstInputValueByName(fieldName);
                if (cityIdValue === '') {
                    return false;
                }

                const parsed = Number.parseInt(cityIdValue, 10);
                return !Number.isNaN(parsed) && parsed > 0;
            }

            function canRequestDistanceEstimates(validStops) {
                if (!hasValidCityId('origin[city_id]') || !hasValidCityId('destination[city_id]')) {
                    return false;
                }

                return validStops.every((stop) => {
                    const parsed = Number.parseInt(stop.cityId || '0', 10);
                    return !Number.isNaN(parsed) && parsed > 0;
                });
            }

            function getSegmentPriceKey(fromIndex, toIndex) {
                return `${fromIndex}:${toIndex}`;
            }

            function getInitialSegmentPriceMap() {
                const priceMap = new Map();
                initialSegmentPrices.forEach((segmentPrice) => {
                    const fromIndex = Number.parseInt(segmentPrice?.from_index ?? '', 10);
                    const toIndex = Number.parseInt(segmentPrice?.to_index ?? '', 10);
                    const priceMinor = toMinorInt(segmentPrice?.price_minor ?? 0);
                    if (!Number.isNaN(fromIndex) && !Number.isNaN(toIndex) && toIndex > fromIndex) {
                        priceMap.set(getSegmentPriceKey(fromIndex, toIndex), priceMinor);
                    }
                });
                return priceMap;
            }

            function getInitialSegmentPriceMinorByLabels(fromLabel, toLabel) {
                const normalizedFrom = (fromLabel ?? '').toString().trim().toLowerCase();
                const normalizedTo = (toLabel ?? '').toString().trim().toLowerCase();
                if (!normalizedFrom || !normalizedTo) {
                    return null;
                }

                const match = initialSegmentPrices.find((segmentPrice) => {
                    const itemFrom = (segmentPrice?.from_label ?? '').toString().trim().toLowerCase();
                    const itemTo = (segmentPrice?.to_label ?? '').toString().trim().toLowerCase();
                    return itemFrom === normalizedFrom && itemTo === normalizedTo;
                });

                if (!match) {
                    return null;
                }

                return toMinorInt(match?.price_minor ?? 0);
            }

            function getAllRoutePoints(validStops) {
                const originLabel = getFirstInputValueByName('origin[label]') || 'Origin';
                const destinationLabel = getFirstInputValueByName('destination[label]') || 'Destination';

                return [{
                        index: 0,
                        label: originLabel
                    },
                    ...validStops.map((stop, idx) => ({
                        index: idx + 1,
                        label: stop.label || `Stop ${idx + 1}`,
                        stopIndex: stop.index
                    })),
                    {
                        index: validStops.length + 1,
                        label: destinationLabel
                    }
                ];
            }

            function getPointLabelsForDistance(points) {
                return points
                    .map((point) => (point?.label ?? '').toString().trim())
                    .filter((label) => label !== '');
            }

            function getDistanceRequestKey(points) {
                return getPointLabelsForDistance(points).join('||');
            }

            function buildLegacyAdjacentPriceMap(validStops, points) {
                const adjacentPriceMap = new Map();
                validStops.forEach((stop, idx) => {
                    adjacentPriceMap.set(getSegmentPriceKey(idx, idx + 1), toMinorInt(stop
                    .priceDeltaMinor));
                });

                const destinationDeltaMinor = toMinorInt(destinationPriceDeltaInitialInput?.value ?? 0);
                if (points.length >= 2) {
                    adjacentPriceMap.set(
                        getSegmentPriceKey(points.length - 2, points.length - 1),
                        destinationDeltaMinor
                    );
                }

                return adjacentPriceMap;
            }

            function resolveDefaultSegmentPriceMinor(fromIndex, toIndex, configuredPrices, adjacentPrices,
                parentPriceMinor) {
                const configuredPrice = configuredPrices.get(getSegmentPriceKey(fromIndex, toIndex));
                if (configuredPrice !== undefined) {
                    return configuredPrice;
                }

                let adjacentSum = 0;
                let hasAllAdjacentPrices = true;
                for (let idx = fromIndex; idx < toIndex; idx++) {
                    const adjacentPrice = adjacentPrices.get(getSegmentPriceKey(idx, idx + 1));
                    if (adjacentPrice === undefined) {
                        hasAllAdjacentPrices = false;
                        break;
                    }
                    adjacentSum += adjacentPrice;
                }

                if (hasAllAdjacentPrices && adjacentSum > 0) {
                    return adjacentSum;
                }

                if (fromIndex === 0 && parentPriceMinor > 0) {
                    return parentPriceMinor;
                }

                return 0;
            }

            function getSelectedSeatsTotal() {
                const selectedSeatsInput = document.querySelector('input[name="seats_total"]:checked');
                const selectedSeats = selectedSeatsInput ? Number.parseInt(selectedSeatsInput.value || '0', 10) : 0;
                return Number.isNaN(selectedSeats) || selectedSeats <= 0 ? 0 : selectedSeats;
            }

            function calculateExpectedSegmentPriceMinor(distanceMeters, seatsTotal) {
                const normalizedDistanceMeters = Math.max(0, Number.parseInt(distanceMeters || '0', 10) || 0);
                const normalizedSeatsTotal = Math.max(0, Number.parseInt(seatsTotal || '0', 10) || 0);

                if (normalizedDistanceMeters <= 0 || normalizedSeatsTotal <= 0) {
                    return {
                        suggestedMinor: 0,
                        maxMinor: 0,
                    };
                }

                const distanceKm = normalizedDistanceMeters / 1000;
                const suggestedMajor = (distanceKm * SOFT_WARNING_CAP) / normalizedSeatsTotal;
                const maxMajor = (distanceKm * ERROR_TRIGGERING_CAP) / normalizedSeatsTotal;

                return {
                    suggestedMinor: Math.round(suggestedMajor * 100),
                    maxMinor: Math.round(maxMajor * 100),
                };
            }

            function getSegmentDistanceMeters(fromIndex, toIndex) {
                const segmentDistancesMeters = segmentDistanceState &&
                    segmentDistanceState.segmentDistancesMeters &&
                    typeof segmentDistanceState.segmentDistancesMeters === 'object' ?
                    segmentDistanceState.segmentDistancesMeters :
                    {};

                return Number.parseInt(segmentDistancesMeters[`${fromIndex}:${toIndex}`] || '0', 10) || 0;
            }

            function refreshExpectedSegmentPriceHints() {
                if (!priceSegmentsList) {
                    return;
                }

                const allSegmentInputs = Array.from(priceSegmentsList.querySelectorAll('.px-segment-price-input'));
                if (allSegmentInputs.length === 0) {
                    return;
                }

                const seatsTotal = getSelectedSeatsTotal();

                allSegmentInputs.forEach((input) => {
                    const fromIndex = Number.parseInt(input.getAttribute('data-from-index') || '-1', 10);
                    const toIndex = Number.parseInt(input.getAttribute('data-to-index') || '-1', 10);
                    const hint = input.parentElement?.querySelector('.px-segment-price-expected');
                    if (Number.isNaN(fromIndex) || Number.isNaN(toIndex) || !hint) {
                        return;
                    }

                    let suggestedMinor = 0;
                    let maxMinor = 0;
                    let distanceSuffix = 'distance unavailable';

                    const segmentDistanceMeters = getSegmentDistanceMeters(fromIndex, toIndex);
                    input.setAttribute('data-distance-meters', String(segmentDistanceMeters));
                    if (segmentDistanceMeters > 0) {
                        const priceEstimate = calculateExpectedSegmentPriceMinor(segmentDistanceMeters,
                            seatsTotal);
                        suggestedMinor = priceEstimate.suggestedMinor;
                        maxMinor = priceEstimate.maxMinor;
                        distanceSuffix = `${(segmentDistanceMeters / 1000).toFixed(1)} km`;
                    }

                    const suggestedMajor = toMajorFromMinor(suggestedMinor);
                    const maxMajor = toMajorFromMinor(maxMinor);
                    const canEditSegmentPrice = suggestedMinor > 0 && !segmentDistanceState.pendingKey;
                    input.disabled = !canEditSegmentPrice;
                    hint.textContent =
                        `Suggested: ${getSelectedCurrencySymbol()}${suggestedMajor} | Max: ${getSelectedCurrencySymbol()}${maxMajor} (${distanceSuffix})`;
                });
            }

            function refreshSingleRouteExpectedPriceHint() {
                if (!priceSingleExpected) {
                    return;
                }

                const originLabel = getFirstInputValueByName('origin[label]');
                const destinationLabel = getFirstInputValueByName('destination[label]');
                const seatsTotal = getSelectedSeatsTotal();
                const totalDistanceMeters = toMinorInt(segmentDistanceState.totalDistanceMeters);

                if (!originLabel || !destinationLabel || seatsTotal <= 0) {
                    priceSingleExpected.classList.add('hidden');
                    priceSingleExpected.textContent = '';
                    return;
                }

                if (totalDistanceMeters <= 0) {
                    priceSingleExpected.classList.remove('hidden');
                    priceSingleExpected.textContent = 'Suggested/Max price will appear when distance is available.';
                    return;
                }

                const priceEstimate = calculateExpectedSegmentPriceMinor(totalDistanceMeters, seatsTotal);
                const suggestedMajor = toMajorFromMinor(priceEstimate.suggestedMinor);
                const maxMajor = toMajorFromMinor(priceEstimate.maxMinor);
                const distanceKm = (totalDistanceMeters / 1000).toFixed(1);

                priceSingleExpected.classList.remove('hidden');
                priceSingleExpected.textContent =
                    `Suggested: ${getSelectedCurrencySymbol()}${suggestedMajor} | Max: ${getSelectedCurrencySymbol()}${maxMajor} (${distanceKm} km)`;
            }

            async function requestSegmentDistanceEstimates(points) {
                const pointLabels = getPointLabelsForDistance(points);
                const requestKey = pointLabels.join('||');

                if (pointLabels.length < 2) {
                    segmentDistanceState = {
                        key: '',
                        pendingKey: '',
                        legDistancesMeters: [],
                        legDurationsSeconds: [],
                        segmentDistancesMeters: {},
                        segmentDurationsSeconds: {},
                        totalDistanceMeters: 0,
                        totalDurationSeconds: 0,
                    };
                    window.pxRideDistanceKm = null;
                    const distanceMetersInput = document.getElementById('px-distance-meters-input');
                    const durationInput = document.getElementById('px-duration-input');
                    if (distanceMetersInput) {
                        distanceMetersInput.value = '';
                    }
                    if (durationInput) {
                        durationInput.value = '0';
                    }
                    refreshExpectedSegmentPriceHints();
                    refreshSingleRouteExpectedPriceHint();
                    return;
                }

                if (segmentDistanceState.key === requestKey || segmentDistanceState.pendingKey === requestKey) {
                    refreshExpectedSegmentPriceHints();
                    return;
                }

                segmentDistanceState.pendingKey = requestKey;
                setSegmentDistanceLoading(true);

                const csrfToken = postRideForm?.querySelector('input[name="_token"]')?.value || '';

                try {
                    const response = await fetch(segmentDistanceEstimateUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify({
                            point_labels: pointLabels,
                        }),
                    });

                    if (!response.ok) {
                        throw new Error(`Distance estimate request failed: ${response.status}`);
                    }

                    const payload = await response.json();
                    if (segmentDistanceState.pendingKey !== requestKey) {
                        return;
                    }

                    segmentDistanceState = {
                        key: requestKey,
                        pendingKey: '',
                        legDistancesMeters: Array.isArray(payload.leg_distances_meters) ? payload
                            .leg_distances_meters : [],
                        legDurationsSeconds: Array.isArray(payload.leg_durations_seconds) ? payload
                            .leg_durations_seconds : [],
                        segmentDistancesMeters: payload.segment_distances_meters && typeof payload
                            .segment_distances_meters === 'object' ?
                            payload.segment_distances_meters :
                            {},
                        segmentDurationsSeconds: payload.segment_durations_seconds && typeof payload
                            .segment_durations_seconds === 'object' ?
                            payload.segment_durations_seconds :
                            {},
                        totalDistanceMeters: Number.parseInt(payload.total_distance_meters || '0', 10) || 0,
                        totalDurationSeconds: Number.parseInt(payload.total_duration_seconds || '0', 10) || 0,
                    };

                    if (segmentDistanceState.totalDistanceMeters > 0) {
                        window.pxRideDistanceKm = segmentDistanceState.totalDistanceMeters / 1000;
                        const distanceMetersInput = document.getElementById('px-distance-meters-input');
                        if (distanceMetersInput) {
                            distanceMetersInput.value = String(segmentDistanceState.totalDistanceMeters);
                        }
                    }
                    const durationInput = document.getElementById('px-duration-input');
                    if (durationInput) {
                        durationInput.value = String(segmentDistanceState.totalDurationSeconds);
                    }
                } catch (error) {
                    if (segmentDistanceState.pendingKey === requestKey) {
                        segmentDistanceState.pendingKey = '';
                    }
                    console.warn('PX segment distance estimates failed', error);
                } finally {
                    setSegmentDistanceLoading(false);
                    refreshExpectedSegmentPriceHints();
                    refreshSingleRouteExpectedPriceHint();
                }
            }

            function scheduleSegmentDistanceEstimates(points) {
                if (segmentDistanceDebounceTimer) {
                    clearTimeout(segmentDistanceDebounceTimer);
                }

                segmentDistanceDebounceTimer = setTimeout(() => {
                    requestSegmentDistanceEstimates(points);
                }, 350);
            }

            function syncSegmentPriceTotal() {
                if (!priceSegmentsList || !priceMinorHiddenInput) return;

                const parentSegmentInput = priceSegmentsList.querySelector(
                    '.px-segment-price-input[data-parent-segment="1"]'
                );
                const parentPriceMinor = parentSegmentInput ? toMinorFromMajor(parentSegmentInput.value) : 0;
                priceMinorHiddenInput.value = String(parentPriceMinor);
                if (priceSegmentsTotal) {
                    priceSegmentsTotal.textContent = toMajorFromMinor(parentPriceMinor);
                }
                refreshExpectedSegmentPriceHints();
                refreshSingleRouteExpectedPriceHint();
                maybeShowPxLivePriceAlert();
            }

            function syncStopPriceDeltaInputsFromSegmentRows() {
                if (!priceSegmentsList) return;
                const segmentInputs = priceSegmentsList.querySelectorAll(
                    '.px-segment-price-input[data-adjacent-stop-index]');
                segmentInputs.forEach((input) => {
                    const stopIndex = input.getAttribute('data-adjacent-stop-index');
                    if (stopIndex === null || stopIndex === '') {
                        return;
                    }
                    const hiddenStopPrice = document.querySelector(
                        `input[name="stops[${stopIndex}][price_delta_minor]"]`);
                    if (hiddenStopPrice) {
                        hiddenStopPrice.value = String(toMinorFromMajor(input.value));
                    }
                });
            }

            function syncPriceInputMode() {
                if (!priceLabel || !priceSingleWrap || !priceSegmentsWrap || !priceMinorInput || !
                    priceMinorHiddenInput || !priceSegmentsList) {
                    return;
                }

                const validStops = getValidStopsData();
                const hasValidStops = validStops.length > 0;
                const canEstimateDistance = canRequestDistanceEstimates(validStops);
                const hasRestoredSegmentState = shouldPreferInitialSegmentMode && initialSegmentPrices.length > 0;

                if (!hasValidStops && !hasRestoredSegmentState) {
                    const points = getAllRoutePoints(validStops);
                    if (canEstimateDistance) {
                        scheduleSegmentDistanceEstimates(points);
                    } else {
                        segmentDistanceState = {
                            key: '',
                            pendingKey: '',
                            legDistancesMeters: [],
                            legDurationsSeconds: [],
                            segmentDistancesMeters: {},
                            segmentDurationsSeconds: {},
                            totalDistanceMeters: 0,
                            totalDurationSeconds: 0,
                        };
                        window.pxRideDistanceKm = null;
                        if (distanceMetersInput) {
                            distanceMetersInput.value = '';
                        }
                        if (durationInput) {
                            durationInput.value = '0';
                        }
                    }
                    // priceLabel.textContent = 'Price per Seat';
                    priceSingleWrap.classList.remove('hidden');
                    priceSegmentsWrap.classList.add('hidden');
                    priceSegmentsList.innerHTML = '';
                    priceMinorInput.disabled = false;
                    priceMinorInput.name = 'price_minor';
                    priceMinorHiddenInput.name = '';
                    refreshSingleRouteExpectedPriceHint();
                    return;
                }

                const points = getAllRoutePoints(validStops);
                if (canEstimateDistance) {
                    scheduleSegmentDistanceEstimates(points);
                } else if (!hasRestoredSegmentState) {
                    segmentDistanceState = {
                        key: '',
                        pendingKey: '',
                        legDistancesMeters: [],
                        legDurationsSeconds: [],
                        segmentDistancesMeters: {},
                        segmentDurationsSeconds: {},
                        totalDistanceMeters: 0,
                        totalDurationSeconds: 0,
                    };
                    window.pxRideDistanceKm = null;
                    if (distanceMetersInput) {
                        distanceMetersInput.value = '';
                    }
                    if (durationInput) {
                        durationInput.value = '0';
                    }
                }

                // priceLabel.textContent = 'Price per Seat (all route sections)';
                priceSingleWrap.classList.add('hidden');
                priceSegmentsWrap.classList.remove('hidden');
                priceMinorInput.disabled = true;
                priceMinorInput.name = '';
                priceMinorHiddenInput.name = 'price_minor';

                const previousValues = new Map();
                Array.from(priceSegmentsList.querySelectorAll('.px-segment-price-input')).forEach((input) => {
                    const fromIndex = input.getAttribute('data-from-index');
                    const toIndex = input.getAttribute('data-to-index');
                    if (fromIndex === null || toIndex === null) {
                        return;
                    }
                    previousValues.set(
                        getSegmentPriceKey(Number.parseInt(fromIndex, 10), Number.parseInt(toIndex,
                        10)),
                        toMinorFromMajor(input.value)
                    );
                });

                const configuredPrices = getInitialSegmentPriceMap();
                const adjacentPrices = buildLegacyAdjacentPriceMap(validStops, points);
                const baseTotalMinor = priceMinorHiddenInput.value ?
                    toMinorInt(priceMinorHiddenInput.value) :
                    toMinorFromMajor(priceMinorInput.value);
                priceSegmentsList.innerHTML = '';

                for (let fromIndex = 0; fromIndex < points.length - 1; fromIndex++) {
                    const group = document.createElement('div');
                    group.className = 'rounded-md border border-gray-200 bg-gray-50 p-3 space-y-2';

                    // const groupTitle = document.createElement('div');
                    // groupTitle.className = 'text-sm font-semibold text-gray-700';
                    // groupTitle.textContent = `From ${points[fromIndex].label}`;
                    // group.appendChild(groupTitle);

                    for (let toIndex = fromIndex + 1; toIndex < points.length; toIndex++) {
                        const from = points[fromIndex].label || 'Point A';
                        const to = points[toIndex].label || 'Point B';
                        const previousKey = getSegmentPriceKey(fromIndex, toIndex);
                        let initialMinor = previousValues.has(previousKey) ?
                            previousValues.get(previousKey) :
                            resolveDefaultSegmentPriceMinor(fromIndex, toIndex, configuredPrices, adjacentPrices,
                                baseTotalMinor);

                        if (!previousValues.has(previousKey)) {
                            const initialMinorByLabels = getInitialSegmentPriceMinorByLabels(from, to);
                            if (initialMinorByLabels !== null) {
                                initialMinor = initialMinorByLabels;
                            }
                        }

                        if (fromIndex === 0 && toIndex === points.length - 1 && initialMinor <= 0 &&
                            baseTotalMinor > 0) {
                            initialMinor = baseTotalMinor;
                        }

                        const row = document.createElement('div');
                        row.className = 'grid grid-cols-1 md:grid-cols-2 gap-3 items-center';

                        const routeLabelWrap = document.createElement('div');
                        const routeLabel = document.createElement('label');
                        routeLabel.className = 'block text-primary text-gray-700';
                        routeLabel.textContent = `${from} \u2192 ${to}`;
                        routeLabelWrap.appendChild(routeLabel);

                        const expectedHint = document.createElement('p');
                        expectedHint.className = 'px-segment-price-expected text-xs text-gray-500 mt-1';
                        expectedHint.textContent = `Expected: ${getSelectedCurrencySymbol()}0.00`;
                        routeLabelWrap.appendChild(expectedHint);

                        const priceInput = document.createElement('input');
                        
                        
                        priceInput.type = 'number';
                        priceInput.min = '0';
                        priceInput.step = '0.01';
                        priceInput.value = toMajorFromMinor(initialMinor);
                        priceInput.className = 'px-segment-price-input w-full rounded border-gray-300';
                        priceInput.placeholder = `e.g. ${getSelectedCurrencySymbol()}12.00`;
                        priceInput.disabled = true;
                        priceInput.setAttribute('data-from-index', String(fromIndex));
                        priceInput.setAttribute('data-to-index', String(toIndex));
                        priceInput.setAttribute('data-distance-meters', '0');

                        if (toIndex === fromIndex + 1 && toIndex <= validStops.length) {
                            priceInput.setAttribute('data-adjacent-stop-index', String(validStops[toIndex - 1]
                                .index));
                        }

                        if (fromIndex === 0 && toIndex === points.length - 1) {
                            priceInput.setAttribute('data-parent-segment', '1');
                        }

                        const stopFromInput = document.createElement('input');
                        stopFromInput.type = 'hidden';
                        stopFromInput.name = 'stop_from[]';
                        stopFromInput.value = from;

                        const stopToInput = document.createElement('input');
                        stopToInput.type = 'hidden';
                        stopToInput.name = 'stop_to[]';
                        stopToInput.value = to;

                        const stopPriceInput = document.createElement('input');
                        stopPriceInput.type = 'hidden';
                        stopPriceInput.name = 'stop_price_minor[]';
                        stopPriceInput.value = String(initialMinor);

                        priceInput.addEventListener('input', function() {
                            stopPriceInput.value = String(toMinorFromMajor(priceInput.value));
                            syncStopPriceDeltaInputsFromSegmentRows();
                            syncSegmentPriceTotal();
                            maybeShowPxLivePriceAlert(false, priceInput);
                        });

                        priceInput.addEventListener('blur', function() {
                            maybeShowPxLivePriceAlert(true, priceInput);
                        });

                        row.appendChild(routeLabelWrap);
                        row.appendChild(priceInput);
                        row.appendChild(stopFromInput);
                        row.appendChild(stopToInput);
                        row.appendChild(stopPriceInput);
                        group.appendChild(row);
                    }

                    priceSegmentsList.appendChild(group);
                }

                syncStopPriceDeltaInputsFromSegmentRows();
                syncSegmentPriceTotal();
                refreshExpectedSegmentPriceHints();
                setSegmentPriceInputsDisabled(!!segmentDistanceState.pendingKey);
            }

            if (priceMinorInput) {
                priceMinorInput.addEventListener('input', function() {
                    if (priceMinorHiddenInput) {
                        priceMinorHiddenInput.value = String(toMinorFromMajor(priceMinorInput.value));
                    }
                    
                    maybeShowPxLivePriceAlert();
                });
                priceMinorInput.addEventListener('blur', function() {
                    maybeShowPxLivePriceAlert(true);
                });
            }

            document.addEventListener('input', function(event) {
                const target = event.target;
                if (!(target instanceof HTMLInputElement)) {
                    return;
                }
                if (target.name && (
                        target.name.includes('origin[label]') ||
                        target.name.includes('origin[city_id]') ||
                        target.name.includes('destination[label]') ||
                        target.name.includes('destination[city_id]') ||
                        target.name.match(/^stops\[\d+\]\[(label|city_id|price_delta_minor)\]$/)
                    )) {
                    syncStopsToggleState();
                    syncPriceInputMode();
                }
            });

            document.addEventListener('change', function(event) {
                const target = event.target;
                if (!(target instanceof HTMLInputElement)) {
                    return;
                }

                if (target.name === 'seats_total') {
                    refreshExpectedSegmentPriceHints();
                    refreshSingleRouteExpectedPriceHint();
                    maybeShowPxLivePriceAlert();
                }
            });

            if (window.Livewire && typeof window.Livewire.hook === 'function') {
                window.Livewire.hook('message.processed', function() {
                    setTimeout(syncStopsToggleState, 60);
                    setTimeout(syncPriceInputMode, 60);
                });
            }

            syncStopsToggleState();
            syncPriceInputMode();
            setTimeout(syncPriceInputMode, 150);
            setTimeout(syncPriceInputMode, 400);

            // Filter out empty stops before form submission
            if (postRideForm) {
                postRideForm.addEventListener('submit', function(event) {
                    // Check if bypass flag is already set (user already saw warning and chose to continue)
                    const bypassInput = postRideForm.querySelector('input[name="bypass_price_validation"]');
                    if (bypassInput && bypassInput.value === '1') {
                        // Continue with normal form submission
                    } else {
                        
                        
                        const submitValidation = getPxSubmitValidationResult();

                        if (submitValidation.type === 'error') {
                            event.preventDefault();
                            event.stopPropagation();
                            event.stopImmediatePropagation();
                            showPxPriceErrorModal(
                                submitValidation.maxPricePerSeat,
                                submitValidation.routeLabel
                            );
                            return false;
                        }

                        if (submitValidation.type === 'warning') {
                            event.preventDefault();
                            event.stopPropagation();
                            event.stopImmediatePropagation();
                            showPxPriceWarningModal(function() {
                                const bypassInput = document.createElement('input');
                                bypassInput.type = 'hidden';
                                bypassInput.name = 'bypass_price_validation';
                                bypassInput.value = '1';
                                postRideForm.appendChild(bypassInput);
                                const newForm = postRideForm.cloneNode(true);
                                postRideForm.parentNode.replaceChild(newForm, postRideForm);
                                newForm.submit();
                            }, submitValidation.routeLabel, submitValidation.softWarningPrice);
                            return false;
                        }
                    }

                    if (priceMinorInput && !priceMinorInput.disabled) {
                        priceMinorInput.value = String(toMinorFromMajor(priceMinorInput.value));
                    }
                    const durationInput = document.getElementById('px-duration-input');
                    if (durationInput) {
                        durationInput.value = String(Number.parseInt(segmentDistanceState.totalDurationSeconds || '0', 10) || 0);
                    }
                    syncStopPriceDeltaInputsFromSegmentRows();
                    syncSegmentPriceTotal();

                    const stopLabelInputs = document.querySelectorAll(
                        'input[name^="stops["][name$="[label]"]');
                    const stopCityIdInputs = document.querySelectorAll(
                        'input[name^="stops["][name$="[city_id]"]');
                    const stopIsPickupInputs = document.querySelectorAll(
                        'input[name^="stops["][name$="[is_pickup]"]');
                    const stopIsDropoffInputs = document.querySelectorAll(
                        'input[name^="stops["][name$="[is_dropoff]"]');
                    const stopPriceDeltaInputs = document.querySelectorAll(
                        'input[name^="stops["][name$="[price_delta_minor]"]');
                    const existingDestinationPriceDeltaInput = postRideForm.querySelector(
                        'input[name="destination[price_delta_minor]"][data-generated="1"]');
                    if (existingDestinationPriceDeltaInput) {
                        existingDestinationPriceDeltaInput.remove();
                    }
                    postRideForm.querySelectorAll('input[data-generated-segment-price="1"]').forEach((
                        input) => {
                            input.remove();
                        });

                    const validStops = getValidStopsData().map((stop) => ({
                        label: stop.label,
                        cityId: stop.cityId,
                        isPickup: stop.isPickup || '1',
                        isDropoff: stop.isDropoff || '1',
                        priceDeltaMinor: toMinorInt(stop.priceDeltaMinor),
                    }));

                    // Source-of-truth for stop leg prices: visible segment rows.
                    // Map first N segment rows to N intermediate stops (in route order).
                    if (priceSegmentsList && validStops.length > 0) {
                        const adjacentSegmentInputs = Array.from(
                            priceSegmentsList.querySelectorAll(
                                '.px-segment-price-input[data-adjacent-stop-index]')
                        );
                        adjacentSegmentInputs.forEach((input) => {
                            const stopIndex = input.getAttribute('data-adjacent-stop-index');
                            if (stopIndex === null || stopIndex === '') {
                                return;
                            }
                            const matchingStop = validStops.find((stop) => String(stop.index) ===
                                String(stopIndex));
                            if (matchingStop) {
                                matchingStop.priceDeltaMinor = toMinorFromMajor(input.value);
                            }
                        });

                        const allSegmentInputs = Array.from(priceSegmentsList.querySelectorAll(
                            '.px-segment-price-input'));
                        const lastAdjacentSegmentInput = allSegmentInputs.find((input) => {
                            const fromIndex = Number.parseInt(input.getAttribute(
                                'data-from-index') || '-1', 10);
                            const toIndex = Number.parseInt(input.getAttribute('data-to-index') ||
                                '-1', 10);
                            const pointCount = validStops.length + 2;
                            return fromIndex === pointCount - 2 && toIndex === pointCount - 1;
                        });

                        if (lastAdjacentSegmentInput) {
                            const destinationPriceDeltaInput = document.createElement('input');
                            destinationPriceDeltaInput.type = 'hidden';
                            destinationPriceDeltaInput.name = 'destination[price_delta_minor]';
                            destinationPriceDeltaInput.value = String(toMinorFromMajor(
                                lastAdjacentSegmentInput
                                .value));
                            destinationPriceDeltaInput.setAttribute('data-generated', '1');
                            postRideForm.appendChild(destinationPriceDeltaInput);
                        }

                        allSegmentInputs.forEach((input, index) => {
                            const fromIndex = input.getAttribute('data-from-index');
                            const toIndex = input.getAttribute('data-to-index');
                            if (fromIndex === null || toIndex === null) {
                                return;
                            }

                            const priceMinor = toMinorFromMajor(input.value);
                            [
                                ['from_index', fromIndex],
                                ['to_index', toIndex],
                                ['price_minor', String(priceMinor)],
                            ].forEach(([field, value]) => {
                                const hiddenInput = document.createElement('input');
                                hiddenInput.type = 'hidden';
                                hiddenInput.name =
                                    `meta[segment_prices][${index}][${field}]`;
                                hiddenInput.value = value;
                                hiddenInput.setAttribute('data-generated-segment-price',
                                    '1');
                                postRideForm.appendChild(hiddenInput);
                            });
                        });
                    }

                    // Remove all existing stop inputs
                    stopLabelInputs.forEach(function(input) {
                        input.remove();
                    });
                    stopCityIdInputs.forEach(function(input) {
                        input.remove();
                    });
                    stopIsPickupInputs.forEach(function(input) {
                        input.remove();
                    });
                    stopIsDropoffInputs.forEach(function(input) {
                        input.remove();
                    });
                    stopPriceDeltaInputs.forEach(function(input) {
                        input.remove();
                    });

                    // Add back only valid stops with sequential indices
                    validStops.forEach(function(stop, newIndex) {
                        const labelInput = document.createElement('input');
                        labelInput.type = 'text';
                        labelInput.name = `stops[${newIndex}][label]`;
                        labelInput.value = stop.label;
                        labelInput.style.display = 'none';
                        postRideForm.appendChild(labelInput);

                        const cityIdInput = document.createElement('input');
                        cityIdInput.type = 'hidden';
                        cityIdInput.name = `stops[${newIndex}][city_id]`;
                        cityIdInput.value = stop.cityId;
                        postRideForm.appendChild(cityIdInput);

                        const isPickupInput = document.createElement('input');
                        isPickupInput.type = 'hidden';
                        isPickupInput.name = `stops[${newIndex}][is_pickup]`;
                        isPickupInput.value = stop.isPickup;
                        postRideForm.appendChild(isPickupInput);

                        const isDropoffInput = document.createElement('input');
                        isDropoffInput.type = 'hidden';
                        isDropoffInput.name = `stops[${newIndex}][is_dropoff]`;
                        isDropoffInput.value = stop.isDropoff;
                        postRideForm.appendChild(isDropoffInput);

                        const priceDeltaInput = document.createElement('input');
                        priceDeltaInput.type = 'hidden';
                        priceDeltaInput.name = `stops[${newIndex}][price_delta_minor]`;
                        priceDeltaInput.value = String(stop.priceDeltaMinor);
                        postRideForm.appendChild(priceDeltaInput);
                    });
                });
            }

            // Hide field tooltip error when user clicks/focuses inside its parent container.
            function hideTooltipInParent(eventTarget) {
                if (!(eventTarget instanceof HTMLElement) || !postRideForm) return;
                let node = eventTarget.closest('div, section, label');

                // Walk up until form root and remove tooltips that belong to the current field
                while (node && node !== postRideForm) {
                    // Check for tooltip as a direct child
                    const tooltipInChildren = Array.from(node.children).find((child) =>
                        child instanceof HTMLElement && child.classList.contains('tooltip-error')
                    );
                    if (tooltipInChildren) {
                        tooltipInChildren.remove();
                        return;
                    }

                    // Check for tooltip as a sibling (for cases like terms checkbox where error is sibling of label)
                    if (node.parentElement) {
                        const tooltipSibling = Array.from(node.parentElement.children).find((sibling) =>
                            sibling instanceof HTMLElement &&
                            sibling.classList.contains('tooltip-error') &&
                            sibling !== node
                        );
                        if (tooltipSibling) {
                            tooltipSibling.remove();
                            return;
                        }
                    }
                    node = node.parentElement?.closest('div, section') || null;
                }
            }

            if (postRideForm) {
                postRideForm.addEventListener('click', function(event) {
                    hideTooltipInParent(event.target);
                });
                postRideForm.addEventListener('focusin', function(event) {
                    hideTooltipInParent(event.target);
                });
            }

            // Initialize departure date/time pickers. according to 'America/New_York'
            const departureDateInput = document.getElementById('departure-at-date');
            const departureTimeInput = document.getElementById('departure-at-time');
            const projectTimezone = @json($projectTimezone);
            const projectToday = flatpickr.parseDate(@json($projectToday), 'Y-m-d');
            
            if (typeof flatpickr !== 'undefined') {
                if (departureDateInput) {
                    flatpickr(departureDateInput, {
                        dateFormat: 'F j, Y',
                        minDate: projectToday,
                        disableMobile: true,
                    });
                }

                if (departureTimeInput) {
                    const departureTimePicker = flatpickr(departureTimeInput, {
                        enableTime: true,
                        noCalendar: true,
                        dateFormat: 'H:i', // 24-hour format for backend (stored value)
                        altInput: true,
                        altFormat: 'H:i', // Display 24-hour in the time area
                        time_24hr: false, // Show 24-hour in picker to match display
                        disableMobile: true,
                        clickOpens: false, // We handle open/close so second click on time area closes
                    });

                    // show/hide timepicker when click on here
                    var timeWrapper = departureTimeInput.closest('.relative') || departureTimeInput.parentElement;
                    if (timeWrapper) {
                        timeWrapper.addEventListener('click', function(e) {
                            if (e.target.closest('.flatpickr-calendar')) return;
                            if (!departureTimeInput._flatpickr) return;
                            if (departureTimePicker.isOpen) {
                                departureTimePicker.close();
                                e.stopPropagation();
                                e.preventDefault();
                                e.stopImmediatePropagation();
                                return;
                            }
                        }, true);
                        timeWrapper.addEventListener('click', function(e) {
                            if (e.target.closest('.flatpickr-calendar')) return;
                            if (!departureTimeInput._flatpickr) return;
                            if (!departureTimeInput._flatpickr.input.value) {
                                const projectTime = getCurrentProjectTime();
                                const [hours, minutes] = projectTime.split(':');
                                const date = new Date();
                                date.setHours(parseInt(hours), parseInt(minutes), 0, 0);
                                departureTimeInput._flatpickr.setDate(date, true);
                            }
                            departureTimePicker.open();
                        }, false);
                    } else {
                        departureTimeInput.addEventListener('click', function() {
                            if (!departureTimeInput._flatpickr) return;
                            if (departureTimePicker.isOpen) { departureTimePicker.close(); return; }
                            if (!departureTimeInput._flatpickr.input.value) {
                                const projectTime = getCurrentProjectTime();
                                const [hours, minutes] = projectTime.split(':');
                                const date = new Date();
                                date.setHours(parseInt(hours), parseInt(minutes), 0, 0);
                                departureTimeInput._flatpickr.setDate(date, true);
                            }
                            departureTimePicker.open();
                        });
                    }
                }

            }

            function getCurrentProjectTime() {
                const formatter = new Intl.DateTimeFormat('en-GB', {
                    timeZone: projectTimezone,
                    hour: '2-digit',
                    minute: '2-digit',
                    hourCycle: 'h23',
                });
                const parts = formatter.formatToParts(new Date());
                const hours = parts.find((part) => part.type === 'hour')?.value ?? '00';
                const minutes = parts.find((part) => part.type === 'minute')?.value ?? '00';

                return `${hours}:${minutes}`;
            }

            // Client-side image preview for "add new vehicle" upload.
            const vehicleImageInput = document.getElementById('dropzone-file');
            const vehicleImagePreview = document.getElementById('vehicle-image');
            if (vehicleImageInput && vehicleImagePreview) {
                vehicleImageInput.addEventListener('change', function(event) {
                    
                    const file = event.target.files && event.target.files[0];
                    if (!file) {
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        vehicleImagePreview.src = e.target?.result || '';
                        vehicleImagePreview.classList.remove('w-12', 'h-12');
                        vehicleImagePreview.classList.add('w-40', 'h-40');
                    };
                    reader.readAsDataURL(file);
                });
            }

            // Toggle Pink Ride and Extra+ Ride disclaimers based on checkbox state
            function updateRideDisclaimers() {
                const pinkRideCheckbox = document.querySelector('input[data-ride-option-code="pink_rides"]');
                const extraCareCheckbox = document.querySelector('input[data-ride-option-code="extra_care_rides"]');
                const pinkRideDisclaimer = document.getElementById('pink-ride-disclaimer');
                const extraCareDisclaimer = document.getElementById('extra-care-ride-disclaimer');
                const extraCareNumber = document.getElementById('extra-care-disclaimer-number');


                if (!pinkRideCheckbox || !extraCareCheckbox || !pinkRideDisclaimer || !extraCareDisclaimer || !
                    extraCareNumber) {
                    return;
                }

                const isPinkRideChecked = pinkRideCheckbox.checked;
                const isExtraCareChecked = extraCareCheckbox.checked;

                toggleElementHidden(pinkRideDisclaimer, !isPinkRideChecked);
                toggleElementHidden(extraCareDisclaimer, !isExtraCareChecked);

                // Update numbering: if pink ride is checked, extra+ is 6, otherwise 5
                if (isExtraCareChecked) {
                    extraCareNumber.textContent = isPinkRideChecked ? '6.' : '5.';
                }
            }

            
            // Add event listeners to feature option checkboxes
            document.addEventListener('change', function(event) {
                const target = event.target;
                if (target && target.hasAttribute('data-ride-option-code')) {
                    const optionCode = target.getAttribute('data-ride-option-code');
                    
                    if (optionCode === 'pink_rides' || optionCode === 'extra_care_rides') {
                        updateRideDisclaimers();
                    }
                }
            });

            // Initialize disclaimers on page load
            updateRideDisclaimers();

            // Handle server-side validation error (from validation redirect)
            @if (session('validation_error') && session('validation_heading'))
                const validationError = {
                    message: @json(session('validation_error')),
                    heading: @json(session('validation_heading', 'Validation Failed'))
                };
                if (validationError.message && validationError.heading) {
                    showPxValidationErrorModal(validationError.heading, validationError.message);
                }
            @endif

            // Handle server-side price error (from validation redirect)
            @if (session('error') && session('max_price_per_seat'))
                const serverError = {
                    message: @json(session('error')),
                    heading: @json(session('heading', 'Price Limit Exceeded')),
                    maxPricePerSeat: parseFloat(@json(session('max_price_per_seat')))
                };
                if (serverError.message && serverError.maxPricePerSeat) {
                    // Show the error modal first (this sets default content)
                    showPxPriceErrorModal(serverError.maxPricePerSeat);

                    // Then update with server-side message and heading
                    const errorHeading = document.getElementById('pxPriceErrorHeading');
                    if (errorHeading && serverError.heading) {
                        errorHeading.textContent = serverError.heading;
                    }
                    const errorPara1 = document.getElementById('pxPriceErrorParagraph1');
                    if (errorPara1 && serverError.message) {
                        errorPara1.textContent = serverError.message;
                    }
                }
            @endif

            // Handle server-side price warning (from validation redirect)
            @if (session('price_warning'))
                const priceWarning = @json(session('price_warning'));
                if (priceWarning && priceWarning.message) {
                    showPxPriceWarningModal(function() {
                        // User clicked "Keep Current Price" - submit the form with bypass flag
                        const bypassInput = document.createElement('input');
                        bypassInput.type = 'hidden';
                        bypassInput.name = 'bypass_price_validation';
                        bypassInput.value = '1';
                        postRideForm.appendChild(bypassInput);

                        // Remove the event listener to prevent re-validation
                        const newForm = postRideForm.cloneNode(true);
                        postRideForm.parentNode.replaceChild(newForm, postRideForm);
                        // Submit the form
                        newForm.submit();
                    });
                }
            @endif

        });

        // Cost-sharing cap validation constants
        const ERROR_TRIGGERING_CAP = 0.72; // $0.72 per km - BLOCK if exceeded
        const SOFT_WARNING_CAP = 0.66; // $0.66 per km - WARN but ALLOW

        // Store distance globally when available (from Google API calculation)
        window.pxRideDistanceKm = null;
        let lastPxPriceValidationInput = null;
        const acknowledgedPxWarningSignatures = new Set();

        function setModalVisibility(modalId, isVisible) {
            const modal = document.getElementById(modalId);
            if (!modal) {
                return null;
            }

            modal.classList.toggle('hidden', !isVisible);
            modal.style.display = isVisible ? 'block' : 'none';
            return modal;
        }

        function openModalById(modalId) {
            return setModalVisibility(modalId, true);
        }

        function closeModalById(modalId) {
            return setModalVisibility(modalId, false);
        }

        function setElementText(elementId, value) {
            const element = document.getElementById(elementId);
            if (element) {
                element.textContent = value;
            }
        }

        function toggleElementHidden(element, isHidden) {
            if (element) {
                element.classList.toggle('hidden', isHidden);
            }
        }

        function focusPxPriceInput(targetInput = null) {
            const priceInput = targetInput instanceof HTMLElement ? targetInput : document.getElementById(
                'px-price-minor-input');
            if (!priceInput) {
                return;
            }

            priceInput.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
            setTimeout(() => {
                priceInput.focus();
                priceInput.select();
            }, 300);
        }

        // Function to validate price per seat
        // Formula: (Distance × Cap) ÷ Seats = Max price per seat
        function validatePxPricePerSeat(priceMinor, distanceKm, seats) {
            if (!priceMinor || !distanceKm || !seats || distanceKm <= 0 || priceMinor <= 0 || seats <= 0) {
                console.log('PX Price validation skipped - missing data:', {
                    priceMinor,
                    distanceKm,
                    seats
                });
                return {
                    valid: true,
                    type: null
                };
            }

            // Convert price from minor units (cents) to major units (dollars)
            const pricePerSeat = parseFloat(priceMinor) / 100;
            const distance = parseFloat(distanceKm);
            const numSeats = parseInt(seats);

            // Calculate max allowed price per seat using Error-Triggering Cap: $0.72/km
            const maxPricePerSeat = (distance * ERROR_TRIGGERING_CAP) / numSeats;

            // Calculate soft warning price per seat: $0.66/km
            const softWarningPricePerSeat = (distance * SOFT_WARNING_CAP) / numSeats;

            console.log('PX Price validation calculations:', {
                pricePerSeat: pricePerSeat,
                distanceKm: distance,
                numSeats: numSeats,
                maxPricePerSeat: maxPricePerSeat,
                softWarningPricePerSeat: softWarningPricePerSeat,
                exceedsMax: pricePerSeat > maxPricePerSeat,
                exceedsSoftWarning: pricePerSeat > softWarningPricePerSeat
            });

            // Error-Triggering Cap: $0.72 per km - BLOCK if exceeded
            if (pricePerSeat > maxPricePerSeat) {
                console.log('PX ERROR CAP TRIGGERED');
                return {
                    valid: false,
                    type: 'error',
                    maxPricePerSeat: maxPricePerSeat.toFixed(2)
                };
            }

            // Soft Warning Cap: $0.66 per km - WARN but ALLOW
            if (pricePerSeat > softWarningPricePerSeat) {
                console.log('PX SOFT WARNING CAP TRIGGERED');
                return {
                    valid: true,
                    type: 'warning',
                    softWarningPrice: softWarningPricePerSeat.toFixed(2)
                };
            }

            console.log('PX No warning or error - price is within limits');
            return {
                valid: true,
                type: null
            };
        }

        function buildPxPriceValidationContext(priceMinor, seatsTotal, distanceMeters, routeLabel) {
            const parsedDistanceMeters = Number.parseInt(distanceMeters || '0', 10) || 0;

            return {
                priceMinor: Number.isNaN(priceMinor) ? 0 : priceMinor,
                seatsTotal: Number.isNaN(seatsTotal) ? 0 : seatsTotal,
                distanceKm: parsedDistanceMeters > 0 ? parsedDistanceMeters / 1000 : null,
                routeLabel,
            };
        }

        function getCurrentPxPriceValidationContext(sourceInput = null) {
            const selectedSeatsInput = document.querySelector('input[name="seats_total"]:checked');
            const seatsTotal = selectedSeatsInput ? parseInt(selectedSeatsInput.value || '0', 10) : 0;

            if (sourceInput instanceof HTMLInputElement && sourceInput.classList.contains('px-segment-price-input')) {
                const distanceMeters = Number.parseInt(sourceInput.getAttribute('data-distance-meters') || '0', 10) || 0;
                const routeLabel = sourceInput.parentElement?.querySelector('label')?.textContent?.trim() || 'this segment';
                const normalizedPrice = (sourceInput.value ?? '').toString().trim().replace(',', '.');
                const parsedPrice = parseFloat(normalizedPrice);
                const priceMinor = Number.isFinite(parsedPrice) && parsedPrice >= 0 ? Math.round(parsedPrice * 100) : 0;

                return buildPxPriceValidationContext(
                    priceMinor,
                    seatsTotal,
                    distanceMeters,
                    routeLabel
                );
            }

            const priceMinorInput = document.getElementById('px-price-minor-input');
            const priceMinorHiddenInput = document.getElementById('px-price-minor-hidden');
            const distanceMetersInput = document.getElementById('px-distance-meters-input');
            const priceMinor = priceMinorHiddenInput && priceMinorHiddenInput.name === 'price_minor' ?
                parseInt(priceMinorHiddenInput.value || '0', 10) :
                (priceMinorInput ? Math.round((parseFloat(priceMinorInput.value || '0') || 0) * 100) : 0);

            let distanceMeters = 0;
            if (distanceMetersInput && distanceMetersInput.value) {
                distanceMeters = parseInt(distanceMetersInput.value || '0', 10) || 0;
            } else if (window.pxRideDistanceKm) {
                distanceMeters = Math.round((parseFloat(window.pxRideDistanceKm) || 0) * 1000);
            }

            return buildPxPriceValidationContext(priceMinor, seatsTotal, distanceMeters, 'this trip');
        }

        function maybeShowPxLivePriceAlert(force = false, sourceInput = null) {
            const validationInput = sourceInput instanceof HTMLElement ? sourceInput : document.getElementById(
                'px-price-minor-input');
            const {
                priceMinor,
                seatsTotal,
                distanceKm,
                routeLabel
            } = getCurrentPxPriceValidationContext(sourceInput);

            if (!priceMinor || !seatsTotal || !distanceKm || distanceKm <= 0) {
                lastPxPriceValidationSignature = null;
                return;
            }

            const validation = validatePxPricePerSeat(priceMinor, distanceKm, seatsTotal);

            if (!validation.type) {
                lastPxPriceValidationSignature = null;
                return;
            }

            const signature = JSON.stringify({
                type: validation.type,
                routeLabel,
                priceMinor,
                seatsTotal,
                distanceKm: Number(distanceKm).toFixed(2),
                maxPricePerSeat: validation.maxPricePerSeat ?? null,
                softWarningPrice: validation.softWarningPrice ?? null,
            });

            if (!force && lastPxPriceValidationSignature === signature) {
                return;
            }

            if (validation.type === 'warning' && acknowledgedPxWarningSignatures.has(signature)) {
                lastPxPriceValidationSignature = signature;
                return;
            }

            lastPxPriceValidationSignature = signature;

            if (!force) {
                return;
            }

            if (validation.type === 'error') {
                lastPxPriceValidationInput = validationInput;
                showPxPriceErrorModal(validation.maxPricePerSeat, routeLabel);
                return;
            }

            if (validation.type === 'warning') {
                lastPxPriceValidationInput = validationInput;
                showPxPriceWarningModal(function() {
                    closeModalById('pxPriceWarningModal');
                }, routeLabel, validation.softWarningPrice);
            }
        }

        function getPxSubmitValidationResult() {
            const segmentInputs = Array.from(document.querySelectorAll('.px-segment-price-input'));
            const contexts = [];

            if (segmentInputs.length > 0 && priceSegmentsWrap && !priceSegmentsWrap.classList.contains('hidden')) {
                segmentInputs.forEach((input) => {
                    contexts.push(getCurrentPxPriceValidationContext(input));
                });
            } else {
                contexts.push(getCurrentPxPriceValidationContext());
            }

            let firstWarning = null;

            for (const context of contexts) {
                if (!context.priceMinor || !context.seatsTotal || !context.distanceKm || context.distanceKm <= 0) {
                    continue;
                }

                const validation = validatePxPricePerSeat(
                    context.priceMinor,
                    context.distanceKm,
                    context.seatsTotal
                );

                if (!validation.valid) {
                    return {
                        type: 'error',
                        routeLabel: context.routeLabel,
                        maxPricePerSeat: validation.maxPricePerSeat,
                    };
                }

                if (validation.type === 'warning' && firstWarning === null) {
                    firstWarning = {
                        type: 'warning',
                        routeLabel: context.routeLabel,
                        softWarningPrice: validation.softWarningPrice,
                    };
                }
            }

            return firstWarning ?? {
                type: null,
            };
        }

        // Function to show error modal (Price Limit Exceeded)
        function showPxPriceErrorModal(maxPricePerSeat, routeLabel = 'this trip') {
            setElementText('pxPriceErrorParagraph1',
                priceErrorParagraph1
            );
            setElementText('pxPriceErrorParagraph2',
                (priceErrorParagraph2Template || '').replace(/:max_per_seat/g, maxPricePerSeat)
            );
            setElementText('pxPriceErrorParagraph3',
                priceErrorParagraph3
            );
            openModalById('pxPriceErrorModal');
        }

        // Function to show warning modal (Recommended Contribution Limit)
        function showPxPriceWarningModal(callback, routeLabel = 'this trip', softWarningPrice = null) {
            const modal = document.getElementById('pxPriceWarningModal');
            if (!modal) {
                console.error('PX Price warning modal not found!');
                return;
            }

            const para1 = document.getElementById('pxPriceWarningParagraph1');
            const para2 = document.getElementById('pxPriceWarningParagraph2');

            if (para1) {
                para1.textContent = priceWarningParagraph1;
            }
            if (para2) {
                para2.textContent = priceWarningParagraph2;
            }

            modal.classList.remove('hidden');
            modal.style.display = 'block';

            // Set up continue button callback
            const continueBtn = document.getElementById('pxPriceWarningContinue');
            if (continueBtn) {
                continueBtn.onclick = function() {
                    if (lastPxPriceValidationSignature) {
                        acknowledgedPxWarningSignatures.add(lastPxPriceValidationSignature);
                    }
                    const activePostRideForm = document.querySelector('form[action*="px.post_ride.store"]') ||
                        document.querySelector('form[action*="px.post_ride.update"]') ||
                        document.querySelector('form');
                    if (!activePostRideForm || !activePostRideForm.querySelector(
                            'input[name="bypass_price_validation"]')) {
                        focusPxPriceInput(lastPxPriceValidationInput);
                    }
                    if (callback) callback();
                };
            }
        }

        // Function to adjust price from error (focus on price input field)
        function adjustPxPriceFromError() {
            closeModalById('pxPriceErrorModal');
            focusPxPriceInput(lastPxPriceValidationInput);
        }

        // Function to adjust price from warning (focus on price input field)
        function adjustPxPriceFromWarning() {
            console.log('Adjust PX Price clicked - closing modal and focusing on price field');
            closeModalById('pxPriceWarningModal');
            focusPxPriceInput(lastPxPriceValidationInput);
            return false;
        }

        function showPxValidationErrorModal(heading, message) {
            setElementText('pxValidationErrorHeading', heading);
            setElementText('pxValidationErrorParagraph', message);
            openModalById('pxValidationErrorModal');
        }

        function closePxPriceErrorModal() {
            closeModalById('pxPriceErrorModal');
        }

        function closePxPriceWarningModal() {
            closeModalById('pxPriceWarningModal');
        }

        function closePxValidationErrorModal() {
            closeModalById('pxValidationErrorModal');
        }

        function closeModal(){
            const modal = document.getElementById('errorModal');
            if (modal) {
                modal.classList.add('hidden');
            }
        }

        var priceErrorParagraph1 = @json(optional($postRidePage)->carpool_regulation_limit_message ?? 'To comply with Canadian and Quebec carpooling regulations, the total amount collected for a trip cannot exceed the official 2026 reimbursement rate of $0.72/km.');
        var priceErrorParagraph2Template = @json(optional($postRidePage)->max_price_per_seat_message ?? 'The maximum allowed for this trip is $:max_per_seat per seat.');
        var priceErrorParagraph3 = @json(optional($postRidePage)->non_commercial_carpool_requirement_message ?? 'This limit is mandatory to ensure your ride is classified as a non-commercial carpool, protecting your insurance coverage and maintaining the cost-sharing status of your contributions.');

        var priceWarningParagraph1 = @json(optional($postRidePage)->price_above_reimbursement_warning ?? 'The price you entered is above the standard reimbursement rate recommended by the CRA and Revenu Québec.');
        var priceWarningParagraph2 = @json(optional($postRidePage)->price_reduction_suggestion_message ?? 'While you can proceed, we suggest reducing the price per seat. This ensures your ride remains a standard carpool even if you drive long distances this year.');

        document.addEventListener('keydown', function(event) {
            if (event.key !== 'Enter') {
                return;
            }

            const target = event.target;
            if (target instanceof HTMLInputElement && target.classList.contains('px-segment-price-input')) {
                event.preventDefault();
            }
        });

    </script>

