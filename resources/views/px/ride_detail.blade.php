@extends('layouts.template')

@section('content')
    <div class="container mx-auto my-10 xl:my-14 px-4 xl:px-0">
        @if ($ride->seats_available == 0)
            <div class="mt-4 rounded-lg px-6 py-3 bg-blue-100 text-gray-600" role="alert">
                {{ $rideDetailPage->all_seats_booked_label ?? 'All seats are booked' }}
            </div>
        @endif

        @if ($ride->status === 'cancelled')
            <div class="mt-4 rounded-lg px-6 py-3 bg-red-100 text-gray-600" role="alert">
                {{ $rideDetailPage->ride_canceller_by_driver ?? 'This ride was cancelled by the driver' }}
            </div>
        @endif

        <h1>{{ $rideDetailPage->main_heading ?? 'Ride detail' }}</h1>

        @php
            $parentOrigin = $ride->route->origin_label ?? 'N/A';
            $parentDestination = $ride->route->destination_label ?? 'N/A';
            $origin = $displayOrigin ?? ($ride->route->origin_label ?? 'N/A');
            $destination = $displayDestination ?? ($ride->route->destination_label ?? 'N/A');
            $pickupLocation = $ride->meta['pickup_location'] ?? null;
            $dropoffLocation = $ride->meta['dropoff_location'] ?? null;
            $pricePerSeatMinor = (int) ($displayPriceMinor ?? $ride->price_minor);
            $currencyCode = strtoupper((string) ($ride->currency ?? ($selectedCurrency ?? 'USD')));
            $currencyMap = ['USD' => '$', 'CAD' => 'C$'];
            $currency = $currencyMap[$currencyCode] ?? ($currencyCode . ' ');
            $segmentStops = $displaySegmentStops ?? collect();
            $segmentMode = (bool) ($isSegmentView ?? false);
        @endphp

        <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-y-4 md:gap-4">
            <div class="col-span-2">
                <div class="bg-white rounded-lg shadow-3xl">
                    <div class="flex flex-col md:flex-row justify-between px-4">
                        <div class="w-full md:w-2/3 order-2 md:order-1">
                            <div class="relative mt-5 text-left rounded-lg bg-white p-4">
                                <div class="space-y-0">
                                    @if ($segmentMode)
                                        <div class="flex items-center relative">
                                            <div class="ml-12 md:ml-20 py-1">
                                                <p class="text-sm text-gray-600">
                                                    <strong>Original route: </strong>
                                                    <span class="text-primary font-medium">{{ $parentOrigin }} →
                                                        {{ $parentDestination }}</span>
                                                </p>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="flex items-center relative">
                                        <div
                                            class="border-r-2 border-black border-solid absolute h-full left-3 md:left-6 top-2 z-10">
                                            <span
                                                class="bg-primary rounded-full w-7 h-7 -top-[2px] -ml-[13px] absolute flex justify-center items-center">
                                                <img class="w-5 h-5 object-contain"
                                                    src="{{ asset('./images/new-21-search-bar-from.png') }}" alt="">
                                            </span>
                                        </div>
                                        <div class="items-center ml-12 md:ml-20">
                                            <p class="font-bold text-xl text-black">
                                                {{ $rideDetailPage->from_label ?? 'From' }}</p>
                                            <div class="flex gap-2 items-baseline">
                                                <h3 class="text-primary font-FuturaMdCnBT text-xl md:text-2xl md:mb-4">
                                                    {{ $origin }}.
                                                </h3>
                                                @if ($pickupLocation && !$segmentMode)
                                                    <p class="text-gray-600 text-sm ml-2"><strong>Pick-up at:</strong>
                                                        {{ $pickupLocation }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>



                                    @if (($segmentMode && $segmentStops->isNotEmpty()) || (!$segmentMode && $ride->stops->isNotEmpty()))
                                        <div class="flex items-center relative">
                                            <div
                                                class="border-r-2 border-black border-solid absolute h-full left-3 md:left-6 top-2 z-10">
                                            </div>
                                            <div class="ml-12 md:ml-20 flex">
                                                <p class="font-bold text-xl text-black mb-2">
                                                    {{ $rideDetailPage->stops_label ?? 'Stops on the way' }}</p>
                                                <ul class="flex flex-col gap-2 text-sm ml-4 mt-1 mb-4">
                                                    @foreach ($segmentMode ? $segmentStops : $ride->stops->where('is_pickup', true)->where('is_dropoff', true) as $stop)
                                                        <li
                                                            class="flex items-center px-2 py-0.5 rounded border border-gray-300 bg-gray-50 text-gray-700">
                                                            <span class="h-4 w-4 inline-flex mr-2 ">
                                                                <svg viewBox="0 0 512 512"
                                                                    xmlns="http://www.w3.org/2000/svg" fill="#000000">
                                                                    <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                                                        stroke-linejoin="round"></g>
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
                                        </div>
                                    @endif

                                    <div class="flex items-center relative">
                                        <div
                                            class="border-r-2 border-black border-solid absolute h-0 left-3 md:left-5 top-2 z-10">
                                            <span
                                                class="bg-gray-200 rounded-full w-7 h-7 -top-[6px] -ml-[12px] md:-ml-[9px] absolute flex justify-center items-center">
                                                <img class="w-5 h-5 object-contain"
                                                    src="{{ asset('./images/new-21-search-bar-to.png') }}" alt="">
                                            </span>
                                        </div>
                                        <div class="items-center ml-12 md:ml-20">
                                            <p class="font-bold text-xl text-black">
                                                {{ $rideDetailPage->to_label ?? 'To' }}</p>
                                            <div class="flex gap-2 items-baseline">
                                                <h3 class="text-primary font-FuturaMdCnBT text-xl md:text-2xl md:mb-4">
                                                    {{ $destination }}.
                                                </h3>
                                                @if ($dropoffLocation && !$segmentMode)
                                                    <p class="text-gray-600 text-sm ml-2"><strong>Drop-off at:</strong>
                                                        {{ $dropoffLocation }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 order-1 md:order-2">
                            <p class="whitespace-nowrap font-semibold">
                                {{ $ride->departure_at ? \Carbon\Carbon::parse($ride->departure_at)->format('F d, Y') : 'N/A' }}
                                at
                                @if ($ride->departure_at)
                                    @php $departureTime = \Carbon\Carbon::parse($ride->departure_at)->format('h:i A'); @endphp
                                    {{ $departureTime === '12:00 PM' ? '12 noon' : ($departureTime === '12:00 AM' ? '12 midnight' : $departureTime) }}
                                @else
                                    N/A
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="border-t border-gray-300 grid grid-cols-2 divide-x divide-gray-300">
                        <div class="flex items-baseline p-4">
                            <h4 class="font-medium text-xl xl:text-2xl text-left text-black font-FuturaMdCnBT">
                                {{ $rideDetailPage->seats_left_label ?? 'Seats left' }}:
                            </h4>
                            <p class="text-xl text-primary font-normal ml-2" style="font-family: 'Roboto', sans-serif;">
                                {{ $ride->seats_available }}</p>
                        </div>
                        <div class="flex flex-wrap items-center gap-3 p-4 items-baseline">
                            <h4 class="text-black text-xl xl:text-2xl">Booking Price:</h4>
                            <p class="text-lg text-primary font-normal" style="font-family: 'Roboto', sans-serif;">
                                {{ $currency }}{{ number_format($pricePerSeatMinor / 100, 2) }}
                                {{ $rideDetailPage->per_seat_label ?? 'per seat' }}
                            </p>
                        </div>
                    </div>

                    <div
                        class="border-t border-gray-300 grid sm:grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-gray-300">
                        <div class="p-4 items-baseline">
                            <h4 class="font-medium text-xl xl:text-2xl text-left text-black font-FuturaMdCnBT">
                                {{ $rideDetailPage->payment_method_label ?? 'Payment method' }}:
                                <span class="text-primary font-normal text-lg"
                                    style="font-family: 'Roboto', sans-serif;">{{ $bookingModeLabel ?? 'N/A' }}</span>
                            </h4>
                        </div>
                        <div class="p-4 items-baseline">
                            <h4 class="font-medium text-xl xl:text-2xl text-left text-black font-FuturaMdCnBT">
                                {{ $rideDetailPage->booking_method_label ?? 'Booking method' }}:
                                <span class="text-primary font-normal text-lg"
                                    style="font-family: 'Roboto', sans-serif;">{{ $bookingMethodLabel ?? 'N/A' }}</span>
                            </h4>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg overflow-hidden shadow-3xl mt-4">
                    <h3 class="bg-primary text-white py-2 px-4 text-2xl xl:text-3xl">
                        {{ $rideDetailPage->ride_features_label ?? 'Ride features' }}
                    </h3>
                    <div class="bg-white p-4 space-y-3">
                        @if ($ride->options->isNotEmpty())
                            @foreach ($ride->options as $option)
                                <div class="flex items-center space-x-2">
                                    @if ($option->icon)
                                        <img class="w-7 h-7" src="{{ asset('home_page_icons/' . $option->icon) }}"
                                            alt="{{ $option->display_label }}">
                                    @endif
                                    <p>{{ $option->display_label }}</p>
                                    <span class="inline-flex cursor-help w-4 h-4"
                                        data-tippy-content="{{ $option->display_description }}">
                                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M22 12C22 17.5228 17.5228 22 12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12ZM12 17.75C12.4142 17.75 12.75 17.4142 12.75 17V11C12.75 10.5858 12.4142 10.25 12 10.25C11.5858 10.25 11.25 10.5858 11.25 11V17C11.25 17.4142 11.5858 17.75 12 17.75ZM12 7C12.5523 7 13 7.44772 13 8C13 8.55228 12.5523 9 12 9C11.4477 9 11 8.55228 11 8C11 7.44772 11.4477 7 12 7Z"
                                                fill="#666666"></path>
                                        </svg>
                                    </span>
                                </div>
                            @endforeach
                        @else
                            <p class="text-gray-500">No ride features selected</p>
                        @endif
                    </div>
                </div>

                @if ($ride->notes)
                    <div class="mt-4 mb-4 rounded-lg px-6 py-3 bg-blue-100 text-gray-600" role="alert">
                        <p class="text-gray-800">Important note from the driver: <span
                                class="text-gray-500">{{ $ride->notes }}</span></p>
                    </div>
                @endif
            </div>

            <div class="col-span-1">
                <div class="space-y-4">
                    <div class="bg-white rounded-lg overflow-hidden shadow-3xl">
                        <h3 class="bg-primary text-white py-2 px-4 text-2xl xl:text-3xl">Driver</h3>
                        <div class="p-4">
                            <p class="text-black text-lg font-semibold">
                                {{ trim(($ride->driver->first_name ?? '') . ' ' . ($ride->driver->last_name ?? '')) ?: $ride->driver->name ?? 'N/A' }}
                            </p>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg overflow-hidden shadow-3xl">
                        <h3 class="bg-primary text-white py-2 px-4 text-2xl xl:text-3xl">
                            {{ $rideDetailPage->vehicle_info_label ?? 'Vehicle info' }}
                        </h3>
                        <div class="p-4">
                            @if ($ride->vehicle)
                                <div class="flex items-center flex-wrap gap-x-2 text-sm text-black">
                                    @if ($ride->vehicle->year)
                                        <p class="text-md">{{ $ride->vehicle->year }}</p>
                                    @endif
                                    <span>|</span>
                                    <p class="text-md">{{ $ride->vehicle->make }}</p>
                                    <span>|</span>
                                    <p class="text-md">{{ $ride->vehicle->model }}</p>
                                    @if ($ride->vehicle->color)
                                        <span>|</span>
                                        <p class="text-md">{{ $ride->vehicle->color }}</p>
                                    @endif
                                </div>
                                <p class="font-semibold text-lg text-black text-start">{{ $ride->vehicle->liscense_no }}
                                </p>
                            @else
                                <p class="text-gray-500">No vehicle information available</p>
                            @endif
                        </div>
                    </div>

                    @if (strtotime($ride->departure_at) > strtotime('now') && $ride->driver?->id !== Auth::id())
                        <div class="bg-white rounded-lg overflow-hidden shadow-3xl">
                            <h3 class="bg-primary text-white py-2 px-4 text-2xl xl:text-3xl">
                                @isset($rideDetailPage->driver_chat_heading)
                                    {{ $rideDetailPage->driver_chat_heading }}
                                @endisset
                            </h3>
                            <div class=" p-4 w-full">
                                <p>
                                    @isset($rideDetailPage->driver_chat_label)
                                        {{ $rideDetailPage->driver_chat_label }}
                                    @endisset
                                </p>
                                <div class="flex justify-center mt-4">
                                    @if (Auth::check())
                                        @if ($ride->driver?->id)
                                            <a href="{{ route('chat', ['lang' => app()->getLocale(), 'departure' => $ride->rideDetail[0]->departure ?? 'unknown', 'destination' => $ride->rideDetail[0]->destination ?? 'unknown', 'id' => $ride->id, 'passenger' => $ride->driver->id]) }}"
                                                class="bg-greenXS hover:bg-greenXS text-white text-base md:text-lg rounded font-FuturaMdCnBT hover:font-FuturaMdCnBT px-5 py-2 border border-greenXS hover:border-greenXS hover:text-white text-center focus:bg-greenXS focus:text-white active:text-white active:bg-greenXS w-36">
                                                @isset($rideDetailPage->driver_chat_button_label)
                                                    {{ $rideDetailPage->driver_chat_button_label }}
                                                @endisset
                                            </a>
                                        @endif
                                    @else
                                        <button type="button"
                                            class="bg-greenXS hover:bg-greenXS text-white text-base md:text-lg rounded font-FuturaMdCnBT hover:font-FuturaMdCnBT px-5 py-2 border border-greenXS hover:border-greenXS hover:text-white text-center focus:bg-greenXS focus:text-white active:text-white active:bg-greenXS w-36"
                                            onclick="togglePopupModal1()">
                                            @isset($rideDetailPage->driver_chat_button_label)
                                                {{ $rideDetailPage->driver_chat_button_label }}
                                            @endisset
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="bg-white rounded-lg overflow-hidden shadow-3xl">
                        <h3 class="bg-primary text-white py-2 px-4 text-2xl xl:text-3xl">
                            {{ $rideDetailPage->cancellation_policy_label ?? 'Cancellation policy' }}
                        </h3>
                        <div class="p-4">
                            <p class="text-lg">{{ $cancelationPolicyLabel ?? 'Standard' }}</p>
                        </div>
                    </div>

                    @if ($ride->driver?->id !== Auth::id())
                    <div class="flex justify-center mt-4">
                        <a href="{{ route('px.booking', ['lang' => optional($selectedLanguage)->abbreviation, 'from_stop_id' => $selectedFromStopId, 'to_stop_id' => $selectedToStopId]) }}"
                            class="group flex items-center button-exp-fill rounded cursor-pointer justify-center py-2 px-4 text-lg font-FuturaMdCnBT">
                            @if ($bookingModeCode === 'manual')
                                <img class="w-8 h-8 rounded-full"
                                    src="{{ asset('home_page_icons/' . $postRidePage->booking_option2->icon) }}"
                                    alt="">
                            @elseif ($bookingModeCode === 'instant')
                                <img class="w-8 h-8 rounded-full"
                                    src="{{ asset('home_page_icons/' . $postRidePage->booking_option1->icon) }}"
                                    alt="">
                            @endif
                            <span class="font-medium text-xl">
                                Book Your Seats
                            </span>
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
