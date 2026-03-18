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
                    <x-px.route-info
                        :ride="$ride"
                        :findRidePage="$findRidePage"
                        :postRidePage="$postRidePage"
                        :rideDetailPage="$rideDetailPage ?? null"
                        :selectedLanguage="$selectedLanguage ?? null"
                    />
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
                        @if ($ride->isInstantBooking() )
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
