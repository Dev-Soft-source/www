@props([
    'ride',
    'rideDetailPage' => null,
    'selectedLanguage' => null,
])

@php
    $rideDetailPage = $rideDetailPage ?? (object) [
        'from_label' => 'From',
        'to_label' => 'To',
        'pickup_at_label' => 'Pick-up at',
        'dropoff_at_label' => 'Drop-off at',
        'stops_along_the_way_label' => 'Stops along the way',
        'depends_on_other_stops_tooltip' => 'This location depends on other stops',
        'departure_time_approximate_tooltip' => 'Departure time is approximate from :departure',
    ];

    $parentOrigin = $ride->route->origin_label ?? 'N/A';
    $parentDestination = $ride->route->destination_label ?? 'N/A';
    $origin = $parentOrigin;
    $destination = $parentDestination;
    $pickupLocation = $ride->meta['pickup_location'] ?? null;
    $dropoffLocation = $ride->meta['dropoff_location'] ?? null;

    $orderedStops = ($ride->stops ?? collect())->sortBy('stop_order')->values();
    $matchedFromStopIndex = isset($ride->matched_from_stop_index) && $ride->matched_from_stop_index !== null
        ? (int) $ride->matched_from_stop_index
        : null;
    $matchedToStopIndex = isset($ride->matched_to_stop_index) && $ride->matched_to_stop_index !== null
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

    $departureDateTime = $ride->departure_at;

    if ($segmentFromIndex > 0 && $orderedStops->has($segmentFromIndex)) {
        $firstStop = $orderedStops[$segmentFromIndex];
        if ($firstStop && !empty($firstStop->departure_at)) {
            $departureDateTime = $firstStop->departure_at;
        }
    }

    $hasRouteStops = $orderedStops->count() >= 2;
    $showParentRouteHint = $hasRouteStops && ($segmentFromIndex > 0 || $segmentToIndex < $lastStopIndex);
    $originIsMiddleOfParentRoute = $hasRouteStops && $segmentFromIndex > 0;
    $departureIsMiddleOfParentRoute = $hasRouteStops && $segmentToIndex < $lastStopIndex;

    if ($hasRouteStops && $segmentToIndex > $segmentFromIndex) {
        $orderedStops = $orderedStops->slice($segmentFromIndex + 1, $segmentToIndex - $segmentFromIndex - 1)->values();
    } else {
        $orderedStops = collect();
    }

    $formattedDeparture = null;
    if ($departureDateTime) {
        $formattedDeparture = formatDepartureDateTime($departureDateTime, $selectedLanguage, $rideDetailPage);
    }
    
@endphp

<div class="flex flex-row items-center">
    <p class="flex items-center space-x-2 font-semibold">
        {{ $formattedDeparture['dateTimeLabel'] ?? 'N/A' }}
        @if ($showParentRouteHint && $parentOrigin)
            <span class="inline-block cursor-help ml-2" 
                data-tippy-content="{{ str_replace(':departure', $parentOrigin, $rideDetailPage->departure_time_approximate_tooltip) }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-circle-fill text-black" viewBox="0 0 16 16">
                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 4zm.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2z" />
                </svg>
            </span>
        @endif
    </p>
    @if($ride->isPinkRide())
        <img class="w-12 h-12 ml-2" src="{{ asset('home_page_icons/' . $rideFeatureOptions['features']['pink_rides']->icon) }}" alt=""
        data-tippy-content="{{ $rideFeatureOptions['features']['pink_rides']->tooltip }}">
    @endif
    @if($ride->isExtraCareRide())
        <img class="w-12 h-12 ml-2" src="{{ asset('home_page_icons/' . $rideFeatureOptions['features']['extra_care_rides']->icon) }}" alt=""
        data-tippy-content="{{ $rideFeatureOptions['features']['extra_care_rides']->tooltip }}">
    @endif
</div>

{{-- Route info --}}
<div class="relative {{ $formattedDeparture ? 'mt-5' : '' }} text-left">
    <div class="items-center relative">
        <div class="border-r-2 border-black border-solid absolute h-full left-3 md:left-6 top-2 z-10">
            <span class="bg-primary rounded-full w-7 h-7 -top-[2px] -ml-[13px] absolute flex justify-center items-center">
                <img class="w-5 h-5 object-contain"
                    src="{{ asset('./images/new-21-search-bar-from.png') }}" alt="">
            </span>
        </div>
        <div class="ml-12 md:ml-20">
            <p class="flex gap-2 items-baseline font-bold text-xl text-black">
                {{ $rideDetailPage->from_label }}
                @if ($originIsMiddleOfParentRoute)
                    <span class="w-4 h-4 ml-2" data-tippy-content="{{ $rideDetailPage->depends_on_other_stops_tooltip }}">
                        <svg width="20px" height="20px" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                            <g id="SVGRepo_iconCarrier">
                                <path d="M1.5 0C0.671573 0 0 0.671573 0 1.5C0 2.32843 0.671573 3 1.5 3C2.15311 3 2.70873 2.5826 2.91465 2H4.5C5.88071 2 7 3.11929 7 4.5V10.5C7 12.433 8.567 14 10.5 14H12.0854C12.2913 14.5826 12.8469 15 13.5 15C14.3284 15 15 14.3284 15 13.5C15 12.6716 14.3284 12 13.5 12C12.8469 12 12.2913 12.4174 12.0854 13H10.5C9.11929 13 8 11.8807 8 10.5V4.5C8 2.567 6.433 1 4.5 1H2.91465C2.70873 0.417404 2.15311 0 1.5 0Z" fill="#0066eb"></path>
                            </g>
                        </svg>
                    </span>
                @endif
            </p>
            <div class="flex gap-2 items-baseline">
                <h3 class="text-primary font-FuturaMdCnBT text-xl md:text-2xl md:mb-4">
                    {{ $origin }}.
                </h3>
                @if ($pickupLocation)
                    <p class="text-sm mt-2">
                        {{ $rideDetailPage->pickup_at_label }}: {{ $pickupLocation }}
                    </p>
                @endif
            </div>
        </div>
        @if ($orderedStops->isNotEmpty())
            <div class="ml-12 md:ml-20 flex">
                <p class="font-bold text-xl text-black">{{ $rideDetailPage->stops_along_the_way_label }}</p>
                <ul class="flex flex-col gap-2 text-sm ml-4 mt-1 mb-4">
                    @foreach ($orderedStops as $stop)
                        <li class="flex items-center px-2 py-0.5 rounded border border-gray-300 bg-gray-50 text-gray-700">
                            <span class="h-4 w-4 inline-flex mr-2">
                                <svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg" fill="#000000">
                                    <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                    <g id="SVGRepo_iconCarrier">
                                        <path fill="#666666"
                                            d="M256 17.108c-75.73 0-137.122 61.392-137.122 137.122.055 23.25 6.022 46.107 11.58 56.262L256 494.892l119.982-274.244h-.063c11.27-20.324 17.188-43.18 17.202-66.418C393.122 78.5 331.73 17.108 256 17.108zm0 68.56a68.56 68.56 0 0 1 68.56 68.562A68.56 68.56 0 0 1 256 222.79a68.56 68.56 0 0 1-68.56-68.56A68.56 68.56 0 0 1 256 85.67z">
                                        </path>
                                    </g>
                                </svg>
                            </span>
                            {{ $stop->label }}
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
    <div class="flex items-center relative">
        <div class="border-r-2 border-black border-solid absolute h-0 left-3 md:left-5 top-2 z-10">
            <span class="bg-gray-200 rounded-full w-7 h-7 -top-[6px] -ml-[12px] md:-ml-[9px] absolute flex justify-center items-center">
                <img class="w-5 h-5 object-contain"
                    src="{{ asset('./images/new-21-search-bar-to.png') }}" alt="">
            </span>
        </div>
        <div class="ml-12 md:ml-20 items-baseline">
            <p class="flex gap-2 items-baseline font-bold text-xl text-black">
                {{ $rideDetailPage->to_label }}
                @if ($departureIsMiddleOfParentRoute)
                    <span class="w-4 h-4 ml-2" data-tippy-content="{{ $rideDetailPage->depends_on_other_stops_tooltip }}">
                        <svg width="20px" height="20px" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                            <g id="SVGRepo_iconCarrier">
                                <path d="M1.5 0C0.671573 0 0 0.671573 0 1.5C0 2.32843 0.671573 3 1.5 3C2.15311 3 2.70873 2.5826 2.91465 2H4.5C5.88071 2 7 3.11929 7 4.5V10.5C7 12.433 8.567 14 10.5 14H12.0854C12.2913 14.5826 12.8469 15 13.5 15C14.3284 15 15 14.3284 15 13.5C15 12.6716 14.3284 12 13.5 12C12.8469 12 12.2913 12.4174 12.0854 13H10.5C9.11929 13 8 11.8807 8 10.5V4.5C8 2.567 6.433 1 4.5 1H2.91465C2.70873 0.417404 2.15311 0 1.5 0Z" fill="#0066eb"></path>
                            </g>
                        </svg>
                    </span>
                @endif
            </p>
            <div class="flex gap-2">
                <h3 class="text-primary font-FuturaMdCnBT text-xl md:text-2xl md:mb-4">
                    {{ $destination }}.
                </h3>
                @if ($dropoffLocation)
                    <p class="text-sm mt-2">
                        {{ $rideDetailPage->dropoff_at_label }}: {{ $dropoffLocation }}
                    </p>
                @endif
            </div>
        </div>
    </div>
</div>
