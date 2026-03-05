@props([
    'ride',
    'lang' => null,
    'detailRoute' => 'px.my_ride_detail',
    'wrapperClass' => 'relative even:bg-gray-200 odd:bg-white',
    'showStatus' => false,
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

    $departureDate = $ride->departure_at ? \Carbon\Carbon::parse($ride->departure_at)->format('F d, Y') : 'N/A';
    $departureTime = $ride->departure_at ? \Carbon\Carbon::parse($ride->departure_at)->format('h:i A') : null;
    if ($departureTime === '12:00 PM') {
        $departureTime = '12 noon';
    } elseif ($departureTime === '12:00 AM') {
        $departureTime = '12 midnight';
    }

    $seats = $ride->seats_available ?? ($ride->seats ?? 0);
    $priceMinorSource = (float) (!is_null($priceMinor)
        ? $priceMinor
        : $ride->price_per_seat_minor ?? ($ride->price_minor ?? 0));
    $normalizedPrice = !is_null($priceMajor) ? (float) $priceMajor : $priceMinorSource / 100;
    $rideCurrencyCode = strtoupper((string) ($ride->currency ?? ($selectedCurrency ?? 'USD')));
    $currencySymbolMap = [
        'USD' => '$',
        'CAD' => 'C$',
    ];
    $resolvedCurrency = !is_null($currency)
        ? (string) $currency
        : $currencySymbolMap[$rideCurrencyCode] ?? $rideCurrencyCode . ' ';

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

    $showParentRouteHint = $orderedStops->count() >= 2 && ($segmentFromIndex > 0 || $segmentToIndex < $lastStopIndex);

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

<div class="{{ $wrapperClass }}">
    @php
        $detailParams = array_merge(['lang' => $lang, 'id' => $ride->id], is_array($detailQuery) ? $detailQuery : []);
    @endphp
    <a href="{{ route($detailRoute, $detailParams) }}" class="block">
        <div class="rounded-lg shadow-3xl border-[3px] border-solid border-gray-100"
            @if ($cardId) id="{{ $cardId }}" @endif>
            {{-- @if ($showStatus)
                @if ($ride->status === 'draft')
                    <span class="bg-yellow-100 text-yellow-800 text-sm font-medium ml-3 px-2.5 py-0.5 rounded">Draft</span>
                @elseif ($ride->status === 'published')
                    <span class="bg-green-100 text-green-800 text-sm font-medium ml-3 px-2.5 py-0.5 rounded">Published</span>
                @endif
            @endif --}}

            <div class="flex items-center justify-between pb-0 p-4">
                <p class="flex items-center space-x-2 font-semibold">
                    {{ $departureDate }}
                    at
                    {{ $departureTime ?? 'N/A' }}
                </p>
                <div>
                    <p class="font-semibold">
                        Total {{ $ride->seats_total }} seats
                    </p>
                </div>
            </div>

            <div class="flex flex-col md:flex-row justify-between px-4">
                <div class="w-full md:w-2/3 order-2 md:order-1">
                    @if ($showParentRouteHint)
                        <p class="text-sm mt-2 text-gray-600">
                            Parent route: {{ $parentOrigin }} -> {{ $parentDestination }}
                        </p>
                    @endif
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
                                <p class="font-bold text-xl text-black">From</p>
                                <div class="flex gap-2 items-baseline">
                                    <h3 class="text-primary font-FuturaMdCnBT text-xl md:text-2xl md:mb-4">
                                        {{ $origin }}.
                                    </h3>
                                    @if ($pickupLocation)
                                        <p class="text-sm mt-2">
                                            Pick-up at: {{ $pickupLocation }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                            @if ($orderedStops->isNotEmpty())
                                <div class="ml-12 md:ml-20 flex">
                                    <p class="font-bold text-xl text-black">Stops on the way</p>
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
                                                </span>{{ $stop->label }}</li>
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
                                <p class="font-bold text-xl text-black">To</p>
                                <div class="flex gap-2">
                                    <h3 class="text-primary font-FuturaMdCnBT text-xl md:text-2xl md:mb-4">
                                        {{ $destination }}.
                                    </h3>

                                    @if ($dropoffLocation)
                                        <p class="text-sm mt-2">
                                            Drop-off at: {{ $dropoffLocation }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>


                    </div>
                </div>

                <div class="mt-4 justify-items-end order-1 md:order-2">
                    <p class="text-xl text-right font-semibold text-primary">
                        {{ $resolvedCurrency }}{{ number_format($normalizedPrice, 2) }}
                        <small>per seat</small>
                    </p>
                    @if (!empty($resolvedRightInfo))
                        <p class="text-sm text-right text-gray-600 mt-1">
                            {{ $resolvedRightInfo }}
                        </p>
                    @endif
                    @if ($waitingBookingRequestsCount > 0)
                        <div class="mt-2 rounded-lg border-2 border-red-400 bg-red-50 px-3 py-2.5 shadow-md animate__animated animate__fadeInDown booking-request-alert">
                            <div class="flex items-center gap-2">
                                <svg class="h-5 w-5 flex-shrink-0 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75v-.7V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0M3.124 7.5A8.969 8.969 0 0 1 5.292 3m13.416 0a8.969 8.969 0 0 1 2.168 4.5" />
                                </svg>
                                <p class="font-semibold text-red-700">
                                    You have {{ $waitingBookingRequestsCount }} booking request(s).
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            @php
                $bookedSeats = max(0, (int) (($ride->seats_total ?? 0) - ($ride->seats_available ?? 0)));
                $bookingPriceMinorTotal = $ride->relationLoaded('bookings')
                    ? (int) $ride->bookings->where('status', '!=', 'cancelled')->sum('segment_price_minor')
                    : (int) $ride->bookings()->where('status', '!=', 'cancelled')->sum('segment_price_minor');
                $bookingPriceTotal = (float) ($bookingPriceMinorTotal / 100);
                $bookingFeeTotal = (float) ($ride->booking_fee ?? 0);
                $totalAmount = (float) ($ride->total_amount ?? ($bookingPriceTotal + $bookingFeeTotal));
            @endphp
            <div
                class="border-t border-gray-300 grid sm:grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-gray-300">
                <div class="flex items-center justify-between p-4">
                    <p class="font-semibold">
                        Booked:
                    </p>
                    <p class="">
                        {{ $bookedSeats }} {{ $bookedSeats === 1 ? 'seat' : 'seats' }}
                    </p>
                </div>
                <div class="p-4">
                    <div class="flex items-center justify-between">
                        <p class="font-semibold">Booking Price (total):</p>
                        <p class="">
                            {{ $resolvedCurrency }}{{ number_format($bookingPriceTotal, 2) }}
                        </p>
                    </div>

                    <div class="flex items-center justify-between">
                        <p class="font-semibold">Booking Fee (total):</p>
                        <p class="">
                            {{ $resolvedCurrency }}{{ number_format($bookingFeeTotal, 2) }}
                        </p>
                    </div>

                    <div class="flex items-center justify-between">
                        <p class="font-semibold">Total Amount:</p>
                        <p class="">
                            {{ $resolvedCurrency }}{{ number_format($totalAmount, 2) }}
                        </p>
                    </div>
                </div>
            </div>

            @if ($showOptions && $ride->options && $ride->options->isNotEmpty())
                <div class="border-t border-gray-300 p-3">
                    <div class="flex flex-wrap items-center gap-2">
                        @php
                            $sortedRideOptions = $ride->options
                                ->loadMissing('group')
                                ->sort(function ($a, $b) {
                                    $groupSortA = $a->group->sort_order ?? PHP_INT_MAX;
                                    $groupSortB = $b->group->sort_order ?? PHP_INT_MAX;
                                    $optionSortA = $a->sort_order ?? PHP_INT_MAX;
                                    $optionSortB = $b->sort_order ?? PHP_INT_MAX;

                                    return [$groupSortB, $optionSortA, $a->id] <=> [$groupSortA, $optionSortB, $b->id];
                                });
                        @endphp
                        @foreach ($sortedRideOptions as $option)
                            <img src="{{ asset('home_page_icons/' . $option->icon) }}"
                                alt="{{ $option->display_label ?? $option->code }}"
                                data-tippy-content="{{ $option->display_description ?? '' }}"
                                class="w-8 h-8 rounded-full object-contain">
                        @endforeach
                    </div>
                </div>
            @endif

            @if (isset($slot) && !$slot->isEmpty())
                <div class="border-t border-gray-300 pt-0 p-3">
                    {{ $slot }}
                </div>
            @endif
        </div>
    </a>
</div>
