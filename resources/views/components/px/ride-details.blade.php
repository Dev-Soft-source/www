@props([
    'ride',
    'rideDetailPage',
    'parentOrigin',
    'parentDestination',
    'origin',
    'destination',
    'pickupLocation',
    'dropoffLocation',
    'originDepartureAt',
    'pricePerSeatMinor',
    'currency',
    'segmentStops',
    'segmentMode',
    'bookingModeLabel' => null,
    'bookingMethodLabel' => null,
    'postRidePage' => null,
    'type' => 'ride_detail',
])

<div class="bg-white rounded-lg shadow-3xl">
    <div class="flex flex-col md:flex-row justify-between px-4">
        <div class="w-full md:w-2/3 order-2 md:order-1">
            <div class="relative mt-5 text-left rounded-lg bg-white p-4">
                <div class="space-y-0">
                    @if ($segmentMode)
                        <div class="flex items-center relative">
                            <div class="ml-12 md:ml-20 py-1">
                                <a href="{{ route('px.ride_detail', ['lang' => optional($selectedLanguage)->abbreviation, 'id' => $ride->id]) }}"
                                    class="text-gray-600 hover:text-primary transition-colors duration-200">
                                    <p class="text-xl text-gray-600">
                                        <strong>Original route: </strong>
                                        <span class="text-primary font-FuturaMdCnBT">{{ $parentOrigin }} →
                                            {{ $parentDestination }}</span>
                                    </p>
                                </a>
                            </div>
                        </div>
                    @endif

                    <div class="flex items-center relative">
                        <div class="border-r-2 border-black border-solid absolute h-full left-3 md:left-6 top-2 z-10">
                            <span
                                class="bg-primary rounded-full w-7 h-7 -top-[2px] -ml-[13px] absolute flex justify-center items-center">
                                <img class="w-5 h-5 object-contain"
                                    src="{{ asset('./images/new-21-search-bar-from.png') }}" alt="">
                            </span>
                        </div>
                        <div class="items-center ml-12 md:ml-20">
                            <p class="font-bold text-xl text-black">
                                {{ $rideDetailPage->from_label ?? 'From' }}</p>
                            <div class="flex flex-col gap-1">
                                <div class="flex gap-2 items-baseline">
                                    <h3 class="text-primary font-FuturaMdCnBT text-xl md:text-2xl md:mb-4">
                                        {{ $origin }}.
                                    </h3>
                                    @if ($pickupLocation)
                                        <p class="text-gray-600 text-sm"><strong>Pick up:</strong>
                                            {{ $pickupLocation }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    @if (($segmentMode && $segmentStops->isNotEmpty()) || (!$segmentMode && $ride->stops->count() > 2))
                        <div class="flex items-center relative">
                            <div
                                class="border-r-2 border-black border-solid absolute h-full left-3 md:left-6 top-2 z-10">
                            </div>
                            <div class="ml-12 md:ml-20 flex">
                                <p class="font-bold text-xl text-black mb-2">
                                    {{ $rideDetailPage->stops_label ?? 'Stops on the way' }}</p>
                                <ul class="flex flex-col gap-1 text-sm ml-4 mb-4">
                                    @foreach ($segmentMode ? $segmentStops : $ride->stops->where('is_pickup', true)->where('is_dropoff', true) as $stop)
                                        <li
                                            class="flex flex-col px-2 py-0.5 rounded border border-gray-300 bg-gray-50 text-gray-700">
                                            <div class="flex items-center">
                                                <span class="h-5 w-5 inline-flex mr-2 flex-shrink-0">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                        <path fill="#888888" fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                                    </svg>
                                                </span>
                                                <span
                                                    class="text-primary font-FuturaMdCnBT text-xl md:text-lg ">{{ $stop->label }}</span>
                                                @if ($stop->pickup_dropoff_location)
                                                    <div class="ml-6 text-xs text-gray-600">
                                                        <strong>Pick up/Drop off:</strong>
                                                        {{ $stop->pickup_dropoff_location }}
                                                    </div>
                                                @endif
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    <div class="flex items-center relative">
                        <div class="border-r-2 border-black border-solid absolute h-0 left-3 md:left-5 top-2 z-10">
                            <span
                                class="bg-gray-200 rounded-full w-7 h-7 -top-[6px] -ml-[12px] md:-ml-[9px] absolute flex justify-center items-center">
                                <img class="w-5 h-5 object-contain"
                                    src="{{ asset('./images/new-21-search-bar-to.png') }}" alt="">
                            </span>
                        </div>
                        <div class="items-center ml-12 md:ml-20">
                            <p class="font-bold text-xl text-black">
                                {{ $rideDetailPage->to_label ?? 'To' }}</p>
                            <div class="flex flex-col gap-1">
                                <div class="flex gap-2 items-baseline">
                                    <h3 class="text-primary font-FuturaMdCnBT text-xl md:text-2xl md:mb-4">
                                        {{ $destination }}.
                                    </h3>
                                    @if ($dropoffLocation)
                                        <p class="text-gray-600 text-sm"><strong>Drop off:</strong>
                                            {{ $dropoffLocation }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-4 order-1 md:order-2">
            <p class="whitespace-nowrap font-semibold">
                {{ $originDepartureAt ? \Carbon\Carbon::parse($originDepartureAt)->format('F d, Y') : 'N/A' }}
                at
                @if ($originDepartureAt)
                    @php $departureTime = \Carbon\Carbon::parse($originDepartureAt)->format('h:i A'); @endphp
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


    @if ($type !== 'my_ride_detail')
        <div class="border-t border-gray-300 flex flex-col md:flex-row md:items-center justify-start md:space-x-2 p-4">
            <div>
                <h4 class="font-medium text-xl xl:text-2xl md:text-center text-black mr-4 font-FuturaMdCnBT">
                    {{ $rideDetailPage->co_passenger_label ?? 'Co-passengers' }} :
                </h4>
            </div>
            <div class="flex items-center space-x-2 no-scrollbar overflow-x-auto mt-2 md:mt-0">
                @foreach ($ride->bookings->where('status', '<>', 3)->where('status', '<>', 4) as $booking)
                    @for ($i = 0; $i < $booking->seats; $i++)
                        @if ($booking->passenger)
                            @if ($booking->passenger->profile_image)
                                <img class="w-10 h-10 rounded-full" src="{{ $booking->passenger->profile_image }}"
                                    alt="">
                            @else
                                <img class="w-10 h-10 rounded-full" src="{{ asset('images/59-booked-seat.png') }}"
                                    alt="">
                            @endif
                        @endif
                    @endfor
                @endforeach
            </div>
        </div>
    @else
        <div
            class="border-t border-gray-300 grid sm:grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-gray-300">
            <div class="p-4 flex items-baseline">
                <h4 class="text-black text-xl xl:text-2xl font-FuturaMdCnBT">
                    {{ $rideDetailPage->booked_on_column_label ?? 'Booked' }}:
                </h4>
                <h4 class="text-primary font-normal text-lg ml-2" style="font-family: 'Roboto', sans-serif;">
                    {{ $ride->seats_total - $ride->seats_available }}
                    {{ $ride->seats_total - $ride->seats_available == 1 ? $rideDetailPage->seat_on_column_label ?? 'seat' : $rideDetailPage->ride_seat_label ?? 'seats' }}
                </h4>
            </div>
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <h4 class="text-black text-xl xl:text-2xl font-FuturaMdCnBT">
                        {{ $rideDetailPage->mobile_seat_fare_label ?? 'Fare' }}:
                    </h4>
                    <p>
                        {{ $currency }}{{ number_format(($pricePerSeatMinor * ($ride->seats_total - $ride->seats_available)) / 100, 2) }}
                    </p>
                </div>
                <div class="flex items-center justify-between">
                    <h4 class="text-black text-xl xl:text-2xl font-FuturaMdCnBT">
                        {{ $rideDetailPage->booking_fee_label ?? 'Booking Fee' }}:
                    </h4>
                    <p>{{ $currency }}</p>
                </div>
                <div class="flex items-center justify-between">
                    <h4 class="text-black text-xl xl:text-2xl font-FuturaMdCnBT">
                        {{ $rideDetailPage->total_amount_label ?? 'Total Amount' }}:
                    </h4>
                    <p>{{ $currency }}</p>
                </div>
            </div>
        </div>
    @endif
</div>

<div class="bg-white rounded-lg overflow-hidden shadow-3xl mt-4">
    <h3 class="bg-primary text-white py-2 px-4 text-2xl xl:text-3xl">
        {{ $rideDetailPage->ride_features_label ?? 'Ride features' }}
    </h3>
    <div class="bg-white p-4 space-y-3">
        @php
            $allowedGroups = ['preference', 'pets_allowed', 'smoking_allowed', 'luggage_size'];
            $preferenceOptions = $ride->options->filter(
                fn($option) => in_array(optional($option->group)->code, $allowedGroups),
            );
        @endphp
        @if ($preferenceOptions->isNotEmpty())
            @foreach ($preferenceOptions as $option)
                <div class="flex items-center space-x-2">
                    {{-- @if ($option->icon) --}}
                    <img class="w-7 h-7 rounded-full" src="{{ asset('home_page_icons/' . $option->icon) }}"
                        alt="{{ $option->display_label }}">
                    {{-- @endif --}}
                    <p>{{ $option->display_label }}</p>
                    @if(!empty($option->display_description))
                    <span class="inline-flex cursor-help w-4 h-4"
                        data-tippy-content="{{ $option->display_description }}">
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M22 12C22 17.5228 17.5228 22 12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12ZM12 17.75C12.4142 17.75 12.75 17.4142 12.75 17V11C12.75 10.5858 12.4142 10.25 12 10.25C11.5858 10.25 11.25 10.5858 11.25 11V17C11.25 17.4142 11.5858 17.75 12 17.75ZM12 7C12.5523 7 13 7.44772 13 8C13 8.55228 12.5523 9 12 9C11.4477 9 11 8.55228 11 8C11 7.44772 11.4477 7 12 7Z"
                                fill="#666666"></path>
                        </svg>
                    </span>
                    @endif
                </div>
            @endforeach
        @else
            <p class="text-gray-500">No ride features selected</p>
        @endif
    </div>
</div>
