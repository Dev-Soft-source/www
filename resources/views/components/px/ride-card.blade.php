@props([
    'ride',
    'lang' => null,
    'detailRoute' => 'px.my_ride_detail',
    'wrapperClass' => 'relative even:bg-gray-200 odd:bg-white',
    'showStatus' => false,
    'showOptions' => true,
    'priceMinor' => null,
    'priceMajor' => null,
    'currency' => '$',
    'rightInfo' => null,
    'cardId' => null,
    'detailQuery' => [],
])

@php
    $origin = $ride->route->origin_label ?? 'N/A';
    $destination = $ride->route->destination_label ?? 'N/A';
    $pickupLocation = $ride->meta['pickup_location'] ?? null;
    $dropoffLocation = $ride->meta['dropoff_location'] ?? null;

    $departureDate = $ride->departure_at ? \Carbon\Carbon::parse($ride->departure_at)->format('F d, Y') : 'N/A';
    $departureTime = $ride->departure_at ? \Carbon\Carbon::parse($ride->departure_at)->format('h:i A') : null;
    if ($departureTime === '12:00 PM') {
        $departureTime = '12 noon';
    } elseif ($departureTime === '12:00 AM') {
        $departureTime = '12 midnight';
    }

    $seats = $ride->seats_available ?? $ride->seats ?? 0;
    $normalizedPrice = !is_null($priceMajor)
        ? (float) $priceMajor
        : ((float) (!is_null($priceMinor) ? $priceMinor : ($ride->price_per_seat_minor ?? $ride->price_minor ?? 0)) / 100);

    $resolvedRightInfo = $rightInfo;
    if (is_null($resolvedRightInfo)) {
        if ($ride->vehicle) {
            $resolvedRightInfo = trim(($ride->vehicle->make ?? '') . ' | ' . ($ride->vehicle->model ?? '') . ' | ' . ($ride->vehicle->year ?? ''), ' |');
        } elseif ($ride->driver) {
            $resolvedRightInfo = 'Driver: ' . ($ride->driver->name ?? 'N/A');
        }
    }

    $orderedStops = ($ride->stops ?? collect())->sortBy('stop_order')->values();
    $matchedFromStopIndex = isset($ride->matched_from_stop_index) && $ride->matched_from_stop_index !== null
        ? (int) $ride->matched_from_stop_index
        : null;
    $matchedToStopIndex = isset($ride->matched_to_stop_index) && $ride->matched_to_stop_index !== null
        ? (int) $ride->matched_to_stop_index
        : null;
@endphp

<div class="{{ $wrapperClass }}">
    @php
        $detailParams = array_merge(['lang' => $lang, 'id' => $ride->id], is_array($detailQuery) ? $detailQuery : []);
    @endphp
    <a href="{{ route($detailRoute, $detailParams) }}" class="block">
        <div class="rounded-lg shadow-3xl border-[3px] border-solid border-gray-100" @if($cardId) id="{{ $cardId }}" @endif>
            @if ($showStatus)
                @if ($ride->status === 'draft')
                    <span class="bg-yellow-100 text-yellow-800 text-sm font-medium ml-3 px-2.5 py-0.5 rounded">Draft</span>
                @elseif ($ride->status === 'published')
                    <span class="bg-green-100 text-green-800 text-sm font-medium ml-3 px-2.5 py-0.5 rounded">Published</span>
                @endif
            @endif

            <div class="flex items-center justify-between pb-0 p-4">
                <p class="flex items-center space-x-2 font-semibold">
                    {{ $departureDate }}
                    at
                    {{ $departureTime ?? 'N/A' }}
                </p>
                <div>
                    <p class="font-semibold">
                        Total {{ $seats }} seats
                    </p>
                </div>
            </div>

            <div class="flex flex-col md:flex-row justify-between px-4">
                <div class="w-full md:w-2/3 order-2 md:order-1">
                    <div class="relative mt-5 text-left">
                        <div class=" items-center relative">
                            <div class="border-r-2 border-black border-solid absolute h-full left-3 md:left-6 top-2 z-10">
                                <span class="bg-primary rounded-full w-7 h-7 -top-[2px] -ml-[13px] absolute flex justify-center items-center">
                                    <img class="w-5 h-5 object-contain" src="{{ asset('./images/new-21-search-bar-from.png') }}" alt="">
                                </span>
                            </div>
                            <div class="ml-12 md:ml-20">
                                <p class="font-bold text-xl text-black">From</p>
                                <div class="flex gap-2 items-baseline">
                                    <h3 class="text-primary font-FuturaMdCnBT text-xl md:text-2xl md:mb-4">
                                        {{ $origin }}.
                                    </h3>
                                    @if($pickupLocation)
                                        <p class="text-sm mt-2">
                                            Pick-up at: {{ $pickupLocation }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                            @if($orderedStops->isNotEmpty())
                                <div class="ml-12 md:ml-20 flex">
                                    <p class="font-bold text-xl text-black">Stops on the way</p>
                                    <ul class="flex flex-col gap-2 text-sm ml-4 mt-1 mb-4">
                                    @foreach($orderedStops as $index => $stop)
                                        @php
                                            $isMatchedFrom = $matchedFromStopIndex !== null && $index === $matchedFromStopIndex;
                                            $isMatchedTo = $matchedToStopIndex !== null && $index === $matchedToStopIndex;
                                            $pointClass = 'px-2 py-0.5 rounded border border-gray-300 bg-gray-50 text-gray-700';
                                            if ($isMatchedFrom) {
                                                $pointClass = 'px-2 py-0.5 rounded border border-green-300 bg-green-50 text-green-700 font-semibold';
                                            } elseif ($isMatchedTo) {
                                                $pointClass = 'px-2 py-0.5 rounded border border-blue-300 bg-blue-50 text-blue-700 font-semibold';
                                            }
                                        @endphp
                                        <li class="flex items-center {{ $pointClass }}"><span class="h-4 w-4 inline-flex mr-2">
                                            <svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg" fill="#000000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><path fill="#666666" d="M256 17.108c-75.73 0-137.122 61.392-137.122 137.122.055 23.25 6.022 46.107 11.58 56.262L256 494.892l119.982-274.244h-.063c11.27-20.324 17.188-43.18 17.202-66.418C393.122 78.5 331.73 17.108 256 17.108zm0 68.56a68.56 68.56 0 0 1 68.56 68.562A68.56 68.56 0 0 1 256 222.79a68.56 68.56 0 0 1-68.56-68.56A68.56 68.56 0 0 1 256 85.67z"></path></g></svg>
                                            </span>{{ $stop->label }}</li>
                                    @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                        <div class="flex items-center relative">
                            <div class="border-r-2 border-black border-solid absolute h-0 left-3 md:left-5 top-2 z-10">
                                <span class="bg-gray-200 rounded-full w-7 h-7 -top-[6px] -ml-[12px] md:-ml-[9px] absolute flex justify-center items-center">
                                    <img class="w-5 h-5 object-contain" src="{{ asset('./images/new-21-search-bar-to.png') }}" alt="">
                                </span>
                            </div>
                            <div class="ml-12 md:ml-20 items-baseline">
                                <p class="font-bold text-xl text-black">To</p>
                                <div class="flex gap-2">
                                    <h3 class="text-primary font-FuturaMdCnBT text-xl md:text-2xl md:mb-4">
                                        {{ $destination }}.
                                    </h3>
                                    @if($dropoffLocation)
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
                        {{ $currency }}{{ number_format($normalizedPrice, 2) }}
                        <small>per seat</small>
                    </p>
                    @if(!empty($resolvedRightInfo))
                        <p class="text-sm text-right text-gray-600 mt-1">
                            {{ $resolvedRightInfo }}
                        </p>
                    @endif
                </div>
            </div>

            @if($showOptions && $ride->options && $ride->options->isNotEmpty())
                <div class="border-t border-gray-300 p-3">
                    <div class="flex flex-wrap items-center gap-2">
                        @foreach($ride->options as $option)
                            <img src="{{ asset('home_page_icons/' . $option->icon) }}"
                                alt="{{ $option->display_label ?? $option->code }}"
                                data-tippy-content="{{ $option->display_description ?? '' }}"
                                class="w-8 h-8 object-contain">
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
