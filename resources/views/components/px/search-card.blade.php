@props([
    'ride',
    'lang' => null,
    'detailRoute' => 'my_ride_detail',
    'wrapperClass' => 'relative even:bg-gray-200 odd:bg-white',
    'showStatus' => false,
    'showBookingInfo' => true,
    'showOptions' => true,
    'priceMinor' => null,
    'priceMajor' => null,
    'currency' => null,
    'rightInfo' => null,
    'cardId' => null,
    'detailQuery' => [],
])

@php
    
    $parentOrigin = $ride->route->origin_label ?? 'N/A';
    $parentDestination = $ride->route->destination_label ?? 'N/A';
    $origin = $parentOrigin;
    $destination = $parentDestination;
    $pickupLocation = $ride->meta['pickup_location'] ?? null;
    $dropoffLocation = $ride->meta['dropoff_location'] ?? null;

    $seats = $ride->seats_available ?? ($ride->seats ?? 0);
    $priceMinorSource = (float) (!is_null($priceMinor)
        ? $priceMinor
        : $ride->price_per_seat_minor ?? ($ride->price_minor ?? 0));
    $normalizedPrice = !is_null($priceMajor) ? (float) $priceMajor : $priceMinorSource / 100;
    $resolvedCurrency = !is_null($currency) ? (string) $currency : '';

    $resolvedRightInfo = $rightInfo;
    if (is_null($resolvedRightInfo)) {
        if ($ride->vehicle) {
            $resolvedRightInfo = trim(
                ($ride->vehicle->make ?? '') .
                    ' | ' .
                    ($ride->vehicle->model ?? '') .
                    ' | ' .
                    ($ride->vehicle->year ?? ''),
                ' |',
            );
        } elseif ($ride->driver) {
            $resolvedRightInfo = 'Driver: ' . ($ride->driver->name ?? 'N/A');
        }
    }

    $orderedStops = ($ride->stops ?? collect())->sortBy('stop_order')->values();
    $matchedFromStopIndex =
        isset($ride->matched_from_stop_index) && $ride->matched_from_stop_index !== null
            ? (int) $ride->matched_from_stop_index
            : null;
    $matchedToStopIndex =
        isset($ride->matched_to_stop_index) && $ride->matched_to_stop_index !== null
            ? (int) $ride->matched_to_stop_index
            : null;
    $lastStopIndex = $orderedStops->count() - 1;
    $segmentFromIndex = 0;
    $segmentToIndex = $lastStopIndex;

    if ($matchedFromStopIndex !== null && $orderedStops->has($matchedFromStopIndex)) {
        $segmentFromIndex = $matchedFromStopIndex;
        $origin = (string) ($orderedStops[$matchedFromStopIndex]->label ?? $origin);
    }
    if ($matchedToStopIndex !== null && $orderedStops->has($matchedToStopIndex)) {
        $segmentToIndex = $matchedToStopIndex;
        $destination = (string) ($orderedStops[$matchedToStopIndex]->label ?? $destination);
    }

    if ($segmentFromIndex > $segmentToIndex) {
        $segmentFromIndex = 0;
        $segmentToIndex = $lastStopIndex;
        $origin = $parentOrigin;
        $destination = $parentDestination;
    }

    // Use first stop's datetime when middle route is selected
    $departureDateTime = $ride->departure_at;
    if ($segmentFromIndex > 0 && $orderedStops->has($segmentFromIndex)) {
        $firstStop = $orderedStops[$segmentFromIndex];
        if ($firstStop && !empty($firstStop->departure_at)) {
            $departureDateTime = $firstStop->departure_at;
        }
    }
    
    $departureDate = $departureDateTime ? \Carbon\Carbon::parse($departureDateTime)->format('F d, Y') : 'N/A';
    $departureTime = $departureDateTime ? \Carbon\Carbon::parse($departureDateTime)->format('h:i A') : null;
    if ($departureTime === '12:00 PM') {
        $departureTime = '12 noon';
    } elseif ($departureTime === '12:00 AM') {
        $departureTime = '12 midnight';
    }

    $detailFromStopId =
        isset($ride->matched_from_stop_id) && $ride->matched_from_stop_id !== null
            ? (int) $ride->matched_from_stop_id
            : (int) ($orderedStops[$segmentFromIndex]->id ?? 0);
    $detailToStopId =
        isset($ride->matched_to_stop_id) && $ride->matched_to_stop_id !== null
            ? (int) $ride->matched_to_stop_id
            : (int) ($orderedStops[$segmentToIndex]->id ?? 0);

    $showParentRouteHint = $orderedStops->count() >= 2 && ($segmentFromIndex > 0 || $segmentToIndex < $lastStopIndex);
    $originIsMiddleOfParentRoute = $orderedStops->count() >= 2 && $segmentFromIndex > 0;
    $departureIsMiddleOfParentRoute = $orderedStops->count() >= 2 && $segmentToIndex < $lastStopIndex;

    if ($orderedStops->count() >= 2 && $segmentToIndex > $segmentFromIndex) {
        // Only keep middle stops between displayed from/to.
        $orderedStops = $orderedStops->slice($segmentFromIndex + 1, $segmentToIndex - $segmentFromIndex - 1)->values();
    } else {
        $orderedStops = collect();
    }

    $waitingBookingRequestsCount = $ride->relationLoaded('bookings')
        ? (int) $ride->bookings->where('status', 'waiting')->count()
        : (int) $ride->bookings()->where('status', 'waiting')->count();
@endphp

@php

    $classes = 'rounded-lg shadow-3xl border-[3px] border-solid';
    $wrapperStart = '';
    $wrapperEnd = '';

    if ($ride->isPinkExtraCareRide()) {
        $wrapperStart = '<div class="rounded-lg border-[3px] border-solid border-green-500 p-[2px] shadow-3xl">';
        $wrapperEnd = '</div>';
        $classes .= ' border-pink-500';
    } elseif ($ride->isPinkRide()) {
        $classes .= ' border-pink-500';
    } elseif ($ride->isExtraCareRide()) {
        $classes .= ' border-green-500';
    } elseif ($ride->isShortDistanceRide()) {
        $classes .= ' border-blue-500';
    }
@endphp
{!! $wrapperStart !!}
<div class="{{ $wrapperClass }} {{ $classes }}" id="ride-{{ $ride->id }}">
    @php
        $detailParams = array_merge(
            [
                'lang' => $lang,
                'id' => $ride->id,
                'from_stop_id' => $detailFromStopId ?: null,
                'to_stop_id' => $detailToStopId ?: null,
            ],
            is_array($detailQuery) ? $detailQuery : [],
        );
    @endphp
    
        <div class="rounded-lg shadow-3xl border-[3px] border-solid border-gray-100"
            @if ($cardId) id="{{ $cardId }}" @endif>

            <div class="grid grid-cols-4 gap-4 p-4">
                
                <div class="col-span-3">
                    @php
                        
                        $departureDateTime = formatDepartureDateTime($departureDateTime, $selectedLanguage ?? null, $rideDetailPage ?? null);
                        $departureDateLabel = $departureDateTime['dateLabel'];
                        $departureTimeLabel = $departureDateTime['timeLabel'];
                    @endphp
                    <p class="flex items-center space-x-2 font-semibold">
                        
                        {{ $departureDateLabel }}
                        {{ $rideDetailPage->at_label }}
                        {{ $departureTimeLabel ?? 'N/A' }}

                        @if ($showParentRouteHint)
                        <span class="inline-block cursor-help ml-2" 
                        data-tippy-content="{{ str_replace(':departure', $parentOrigin, $findRidePage->departure_time_approximate_tooltip) }} ">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-circle-fill text-black" viewBox="0 0 16 16">
                                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 4zm.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2z" />
                            </svg>
                        </span>
                        @endif
                    </p>
                    
                    <div class="relative mt-5 text-left">
                        <div class="items-center relative">
                            <div
                                class="border-r-2 border-black border-solid absolute h-full left-3 md:left-6 top-2 z-10">
                                <span
                                    class="bg-primary rounded-full w-7 h-7 -top-[2px] -ml-[13px] absolute flex justify-center items-center">
                                    <img class="w-5 h-5 object-contain"
                                        src="{{ asset('./images/new-21-search-bar-from.png') }}" alt="">
                                </span>
                            </div>
                            <div class="ml-12 md:ml-20">
                                <p class="flex gap-2 items-baseline font-bold text-xl text-black">{{ $findRidePage->card_section_from_label }}
                                    @if ($originIsMiddleOfParentRoute)
                                        <span class="w-4 h-4 ml-2" data-tippy-content="{{ $findRidePage->depends_on_other_stops_tooltip }}">
                                            <svg width="20px" height="20px" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M1.5 0C0.671573 0 0 0.671573 0 1.5C0 2.32843 0.671573 3 1.5 3C2.15311 3 2.70873 2.5826 2.91465 2H4.5C5.88071 2 7 3.11929 7 4.5V10.5C7 12.433 8.567 14 10.5 14H12.0854C12.2913 14.5826 12.8469 15 13.5 15C14.3284 15 15 14.3284 15 13.5C15 12.6716 14.3284 12 13.5 12C12.8469 12 12.2913 12.4174 12.0854 13H10.5C9.11929 13 8 11.8807 8 10.5V4.5C8 2.567 6.433 1 4.5 1H2.91465C2.70873 0.417404 2.15311 0 1.5 0Z" fill="#0066eb"></path> </g></svg>
                                        </span>
                                    @endif
                                </p>
                                <div class="flex gap-2 items-baseline">
                                    <h3 class="text-primary font-FuturaMdCnBT text-xl md:text-2xl md:mb-4">
                                        {{ $origin }}.
                                    </h3>
                                    @if ($pickupLocation)
                                        <p class="text-sm mt-2">
                                            {{ $findRidePage->pickup_at_label ?? 'Pick-up at' }}: {{ $pickupLocation }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                            @if ($orderedStops->isNotEmpty())
                                <div class="ml-12 md:ml-20 flex">
                                    <p class="font-bold text-xl text-black">{{ $postRidePage->stops_along_the_way_label }}</p>
                                    <ul class="flex flex-col gap-2 text-sm ml-4 mt-1 mb-4">
                                        @foreach ($orderedStops as $stop)
                                            <li
                                                class="flex items-center px-2 py-0.5 rounded border border-gray-300 bg-gray-50 text-gray-700">
                                                <span class="h-4 w-4 inline-flex mr-2">
                                                    <svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"
                                                        fill="#000000">
                                                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                                            stroke-linejoin="round"></g>
                                                        <g id="SVGRepo_iconCarrier">
                                                            <path fill="#666666"
                                                                d="M256 17.108c-75.73 0-137.122 61.392-137.122 137.122.055 23.25 6.022 46.107 11.58 56.262L256 494.892l119.982-274.244h-.063c11.27-20.324 17.188-43.18 17.202-66.418C393.122 78.5 331.73 17.108 256 17.108zm0 68.56a68.56 68.56 0 0 1 68.56 68.562A68.56 68.56 0 0 1 256 222.79a68.56 68.56 0 0 1-68.56-68.56A68.56 68.56 0 0 1 256 85.67z">
                                                            </path>
                                                        </g>
                                                    </svg>
                                                </span>{{ $stop->label }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                        <div class="flex items-center relative">
                            <div class="border-r-2 border-black border-solid absolute h-0 left-3 md:left-5 top-2 z-10">
                                <span
                                    class="bg-gray-200 rounded-full w-7 h-7 -top-[6px] -ml-[12px] md:-ml-[9px] absolute flex justify-center items-center">
                                    <img class="w-5 h-5 object-contain"
                                        src="{{ asset('./images/new-21-search-bar-to.png') }}" alt="">
                                </span>
                            </div>
                            <div class="ml-12 md:ml-20 items-baseline">
                                <p class="flex gap-2 items-baseline font-bold text-xl text-black">{{ $findRidePage->card_section_to_label }}
                                    @if ($departureIsMiddleOfParentRoute)
                                        <span class="w-4 h-4 ml-2" data-tippy-content="{{ $findRidePage->depends_on_other_stops_tooltip }}">
                                            <svg width="20px" height="20px" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M1.5 0C0.671573 0 0 0.671573 0 1.5C0 2.32843 0.671573 3 1.5 3C2.15311 3 2.70873 2.5826 2.91465 2H4.5C5.88071 2 7 3.11929 7 4.5V10.5C7 12.433 8.567 14 10.5 14H12.0854C12.2913 14.5826 12.8469 15 13.5 15C14.3284 15 15 14.3284 15 13.5C15 12.6716 14.3284 12 13.5 12C12.8469 12 12.2913 12.4174 12.0854 13H10.5C9.11929 13 8 11.8807 8 10.5V4.5C8 2.567 6.433 1 4.5 1H2.91465C2.70873 0.417404 2.15311 0 1.5 0Z" fill="#0066eb"></path> </g></svg>
                                        </span>
                                    @endif
                                </p>
                                <div class="flex gap-2">
                                    <h3 class="text-primary font-FuturaMdCnBT text-xl md:text-2xl md:mb-4">
                                        {{ $destination }}.
                                    </h3>

                                    @if ($dropoffLocation)
                                        <p class="text-sm mt-2">
                                            {{ $findRidePage->dropoff_at_label ?? 'Drop-off at' }}: {{ $dropoffLocation }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="grid justify-items-end">
                    <div class="pr-2">
                        <p class="font-medium text-2xl text-right">
                            {{ str_replace(':count', $ride->seats, $findRidePage->total_seats_label ?? 'Total :count seats') }}
                        </p>
                        <div class="flex items-center gap-2 text-primary justify-end">
                            @php
                                $seat_price = $ride->detail->price / 100;
                            @endphp
                            @if (isset($firm_cancellation_discount) &&
                                    $firm_cancellation_discount != '' &&
                                    $ride->booking_type == $postRidePage->cancellation_policy_label2?->features_setting_id)
                                <span class="line-through">
                                    ${{ number_format(floatval($seat_price), 2) }}
                                </span>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
                                </svg>
    
                                <span>
    
                                    ${{ $seat_price - ($seat_price * $firm_cancellation_discount) / 100 }}
                                </span>
                            @else
                                ${{ number_format(floatval($seat_price), 2) }}
                            @endif
    
                            <small>
                                @isset($findRidePage->card_section_per_seat)
                                    {{ $findRidePage->card_section_per_seat }}
                                @endisset
                            </small>
                            @if (isset($firm_cancellation_discount) &&
                                    $firm_cancellation_discount != '' &&
                                    $ride->booking_type == $postRidePage->cancellation_policy_label2?->features_setting_id)
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                    class="bi bi-info-circle-fill text-black" viewBox="0 0 16 16"
                                    data-tippy-content="{!! nl2br($findRidePage->firm_cancellation_tooltip) ??
                                        'This ride has the Firm cancellation policy, so its booking price is reduced by 10%' !!}">
                                    <path
                                        d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
                                </svg>
                            @endif
    
                        </div>
                        <p class="text-primary text-right">
                            {{ intval($ride->seats) -intval($ride->bookings()->where('status', '<>', 3)->where('status', '<>', 4)->whereHas('passenger', function ($query) {$query->whereNull('deleted_at');})->sum('seats')) }}
                            {{ $findRidePage->card_section_seats_left ?? 'seats available' }}
                        </p>
                    </div>

                    <div class="my-4">
                        @if ($ride->bookingMethod() == 'instant' )
                            <a href="{{ route('ride_detail', ['lang' => app()->getLocale(), 'id' => $ride->id]) }}"
                                class="button-exp-green-fill flex justify-center w-full" data-tippy-content="{{ $postRidePage->booking_option1_tooltip }}">
                                <img class="w-8 h-8" src="{{ asset('home_page_icons/' . $postRidePage->booking_option1->icon) }}" />
                                {{ $siteText['instant_booking_btn_text'] ?? 'Instant booking' }}
                            </a>
                        @else
                            <a href="{{ route('ride_detail', ['lang' => app()->getLocale(), 'id' => $ride->id]) }}"
                                class="button-exp-sky-fill flex justify-center w-full" data-tippy-content="{{ $postRidePage->booking_option2_tooltip }}">
                                <img class="w-8 h-8" src="{{ asset('home_page_icons/' . $postRidePage->booking_option2->icon) }}" />
                                {{ $siteText['request_to_book_btn_text'] ?? 'Request to book' }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>


            @if ($showDriverInfo)

                <div
                    class="border-t border-gray-300">
                    <div class="flex items-center justify-between p-4 w-full">
                        <div class="flex items-center space-x-2">
                            <div>
                                <p class="font-semibold">
                                    {{ $ride->driver?->first_name }}
                                </p>
                                <p class="text-sm">
                                    {{ $findRidePage->card_section_age }} {{ $ride->driver?->getAge() }}
                                </p>
                                <p class="text-sm">
                                    {{ $ride->driver?->getCompletedPassengerBookingsCount() }} {{ $findRidePage->card_section_driven }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="flex items-center justify-end">
                                <span class="font-semibold text-gray-800">
                                    @if ($ride->getDriverHasRatings())
                                        {{ number_format($ride->getDriverAverageRating(), 1) }}
                                    @else
                                        {{ $rideDetailPage->no_reviews_label ?? 'No Reviews' }}
                                    @endif
                                </span>

                                @if ($ride->getDriverHasRatings())
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                        fill="currentColor"
                                        class="w-6 h-6 text-yellow-500 stroke-gray-600">
                                        <path fill-rule="evenodd"
                                            d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                @endif
                            </div>

                        </div>
                    </div>
                </div>
            @endif


            @if ($showOptions && $ride->features)
                <div class="border-t border-gray-300 p-3">
                    <div class="flex flex-wrap items-center gap-2">
                        @include('partials.ride_feature_icons', [
                            'rideFeatures' => $ride->features,
                        ])
                    </div>
                </div>
            @endif

            @if (isset($slot) && !$slot->isEmpty())
                <div class="border-t border-gray-300 pt-0 p-3">
                    {{ $slot }}
                </div>
            @endif
        </div>

</div>
{!! $wrapperEnd !!}
