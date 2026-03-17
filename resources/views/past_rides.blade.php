@extends('layouts.template')

@section('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        @keyframes booking-request-pulse {

            0%,
            100% {
                opacity: 1;
                transform: scale(1);
                box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            }

            50% {
                opacity: 0.95;
                transform: scale(1.08);
                box-shadow: 0 10px 15px -3px rgb(35 168 168 / 0.2);
            }
        }

        .booking-request-alert {
            animation: booking-request-pulse 1.5s ease-in-out 5;
        }
    </style>
@endsection
@section('content')

    <div class="grid grid-cols-12 gap-4 md:container md:mx-auto  my-6 md:my-10 xl:my-14 px-4 xl:px-0">
        @include('layouts.inc.profile_sidebar')

        <div class="bg-white rounded pt-0 lg:px-4 w-full col-span-12 lg:col-span-9">
            <div class="flex flex-wrap" id="tabs-id">
                <div class="w-full">
                    @include('layouts.inc.trips_tabs')
                    <div class="relative flex flex-col min-w-0 break-words bg-white w-full py-5 shadow-lg rounded">
                        <div class="">
                            <div class="px-4 flex-auto">
                                <div class="tab-content tab-space">
                                    <div class="block" id="tab-profile">
                                        <div class="space-y-4">
                                            @if (!empty($pastRides) && count($pastRides) > 0)
                                                @foreach ($pastRides as $ride)
                                                    @php
                                                        $defaultDetail = $ride->rideDetail->first();
                                                        $from = optional($defaultDetail)->departure;
                                                        $to = optional($defaultDetail)->destination;
                                                    @endphp
                                                    @if ($defaultDetail)
                                                    <div class="relative even:bg-gray-200 odd:bg-white">
                                                        <a class=""
                                                            href="{{ route('my_ride_detail', ['lang' => $selectedLanguage->abbreviation, 'departure' => $from, 'destination' => $to, 'id' => $ride->id]) }}">
                                                            <div class="rounded-lg shadow-3xl border-[3px] border-solid border-gray-100 "
                                                                id="ride-29">
                                                                <div
                                                                    class="flex flex-col md:flex-row gap-2 items-start justify-between pb-0 p-4">
                                                                    @php
                                                                        $displayDt = ($defaultDetail->date ?? $ride->date) . ' ' . ($defaultDetail->time ?? $ride->time ?? '00:00');
                                                                        $departureDateTime = formatDepartureDateTime(
                                                                            $displayDt,
                                                                            $selectedLanguage ?? null,
                                                                            $rideDetailPage ?? null,
                                                                        );
                                                                        $departureDateLabel =
                                                                            $departureDateTime['dateLabel'];
                                                                        $departureTimeLabel =
                                                                            $departureDateTime['timeLabel'];
                                                                    @endphp
                                                                    <p
                                                                        class="flex items-center space-x-2 w-full font-semibold text-left">
                                                                        {{ $departureDateLabel }}
                                                                        {{ $rideDetailPage->at_label }}
                                                                        {{ $departureTimeLabel ?? 'N/A' }}
                                                                    </p>
                                                                    <div class="flex items-center justify-end w-full gap-2">
                                                                        <div
                                                                            class="w-fit px-2 py-1 rounded bg-green-100 text-sm text-green-600">
                                                                            Completed
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div
                                                                    class="flex flex-col md:flex-row justify-between px-4 pb-4 md:pb-0">
                                                                    <div class="w-full md:w-2/3 order-2 md:order-1">
                                                                        <div class="relative mt-5 text-left">
                                                                            <div class="items-center relative">
                                                                                <div
                                                                                    class="border-r-2 border-black border-solid absolute h-full left-3 md:left-6 top-2 z-10">
                                                                                    <span
                                                                                        class="bg-primary rounded-full w-7 h-7 -top-[2px] -ml-[13px] absolute flex justify-center items-center">
                                                                                        <img class="w-5 h-5 object-contain"
                                                                                            src="{{ asset('./images/new-21-search-bar-from.png') }}"
                                                                                            alt="">
                                                                                    </span>
                                                                                </div>
                                                                                <div class="ml-12 md:ml-20">
                                                                                    <p class="font-bold text-xl text-black">
                                                                                        @isset($rideDetailPage->card_section_from_label)
                                                                                            {{ $rideDetailPage->card_section_from_label }}
                                                                                        @endisset
                                                                                    </p>
                                                                                    <div class="flex gap-2 items-baseline">
                                                                                        <h3
                                                                                            class="text-primary font-FuturaMdCnBT text-xl md:text-2xl md:mb-4">
                                                                                            {{ $from }}.
                                                                                        </h3>
                                                                                        <p class="text-sm mt-2">
                                                                                            {{ $rideDetailPage->pickup_at_label ?? 'Pick-up at' }}: {{ $ride->pickup }}
                                                                                        </p>
                                                                                    </div>
                                                                                </div>
                                                                                @if ($ride->rideStops->isNotEmpty() && $ride->rideStops->count() > 2)
                                                                                    <div class="ml-12 md:ml-20 flex">
                                                                                        <p class="font-bold text-xl text-black">Stops on the way</p>
                                                                                        <ul class="flex flex-col gap-2 text-sm ml-4 mt-1 mb-4">
                                                                                            @foreach ($ride->rideStops as $stop)
                                                                                                @continue($loop->first || $loop->last)
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
                                                                                <div
                                                                                    class="border-r-2 border-black border-solid absolute h-0 left-3 md:left-5 top-2 z-10">
                                                                                    <span
                                                                                        class="bg-gray-200 rounded-full w-7 h-7 -top-[6px] -ml-[12px] md:-ml-[9px] absolute flex justify-center items-center">
                                                                                        <img class="w-5 h-5 object-contain"
                                                                                            src="{{ asset('./images/new-21-search-bar-to.png') }}"
                                                                                            alt="">
                                                                                    </span>
                                                                                </div>
                                                                                <div class="ml-12 md:ml-20 items-baseline">
                                                                                    <p class="font-bold text-xl text-black">
                                                                                        @isset($rideDetailPage->card_section_to_label)
                                                                                            {{ $rideDetailPage->card_section_to_label }}
                                                                                        @endisset
                                                                                    </p>
                                                                                    <div class="flex gap-2">
                                                                                        <h3
                                                                                            class="text-primary font-FuturaMdCnBT text-xl md:text-2xl md:mb-4">
                                                                                            {{ $to }}.
                                                                                        </h3>
                                                                                        <p class="text-sm mt-2">
                                                                                            {{ $rideDetailPage->dropoff_at_label ?? 'Drop-off at' }}:
                                                                                            {{ $ride->dropoff }}
                                                                                        </p>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="mt-4 order-1 md:order-2">
                                                                        <div class="pr-8">
                                                                            <p class="font-medium">
                                                                                {{ str_replace(':count', $ride->seats, $rideDetailPage->total_seats_label ?? 'Total :count seats') }}
                                                                            </p>
                                                                        </div>
                                                                        <p
                                                                            class="text-lg md:text-xl font-semibold text-primary">
                                                                            ${{ number_format(floatval($defaultDetail->price), 2) }}
                                                                            <small>
                                                                                @isset($rideDetailPage->card_section_per_seat)
                                                                                    {{ $rideDetailPage->card_section_per_seat }}
                                                                                @endisset
                                                                            </small>
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                                <div
                                                                    class="border-t border-gray-300 grid sm:grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-gray-300">
                                                                    <div
                                                                        class="flex items-center justify-between p-2 md:p-4">
                                                                        <p class="font-semibold">
                                                                            @isset($rideDetailPage->card_section_booked)
                                                                                {{ $rideDetailPage->card_section_booked }}
                                                                            @endisset
                                                                        </p>
                                                                        <p class="">
                                                                            {{ $ride->bookings()->where('status', '<>', 3)->where('status', '<>', 4)->whereHas('passenger', function ($query) {$query->whereNull('deleted_at');})->sum('seats') }}
                                                                            @isset($rideDetailPage->card_section_seats)
                                                                                {{ $rideDetailPage->card_section_seats }}
                                                                            @endisset
                                                                        </p>
                                                                    </div>
                                                                    <div class="p-2 md:p-4">
                                                                        <div class="flex items-center justify-between">
                                                                            <p class="font-semibold">
                                                                                @isset($rideDetailPage->card_section_seats_fee)
                                                                                    {{ $rideDetailPage->card_section_seats_fee }}
                                                                                @endisset
                                                                                : </p>
                                                                            <p class="">
                                                                                ${{ number_format(floatval($ride->bookings()->where('status', '<>', 3)->where('status', '<>', 4)->whereHas('passenger', function ($query) {$query->whereNull('deleted_at');})->sum('seats') * floatval($defaultDetail->price)),2) }}
                                                                            </p>
                                                                        </div>

                                                                        <div class="flex items-center justify-between">
                                                                            <p class="font-semibold">
                                                                                @isset($rideDetailPage->card_section_booking_fee)
                                                                                    {{ $rideDetailPage->card_section_booking_fee }}
                                                                                @endisset
                                                                                : </p>
                                                                            <p class="">
                                                                                ${{ number_format(floatval($ride->bookings()->where('status', '<>', 3)->where('status', '<>', 4)->whereHas('passenger', function ($query) {$query->whereNull('deleted_at');})->sum('booking_credit')),2) }}
                                                                            </p>
                                                                        </div>

                                                                        <div class="flex items-center justify-between">
                                                                            <p class="font-semibold">
                                                                                @isset($rideDetailPage->card_section_amount)
                                                                                    {{ $rideDetailPage->card_section_amount }}
                                                                                @endisset
                                                                                : </p>
                                                                            <p class="">
                                                                                ${{ number_format(floatval($ride->bookings()->where('status', '<>', 3)->where('status', '<>', 4)->whereHas('passenger', function ($query) {$query->whereNull('deleted_at');})->sum('seats') * $defaultDetail->price + $ride->bookings->where('status', '<>', 3)->where('status', '<>', 4)->sum('booking_credit')),2) }}
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div
                                                                    class="border-t border-gray-300 no-scrollbar overflow-x-auto flex items-center space-x-2 p-4">
                                                                    @include('partials.ride_preference_icons', [
                                                                        'ride' => $ride,
                                                                        'searchOptionGroups' => $searchOptionGroups,
                                                                    ])
                                                                    @include('partials.ride_feature_icons', [
                                                                        'rideFeatures' => $ride->features,
                                                                    ])
                                                                </div>
                                                                <div class="border-t border-gray-300 p-4">
                                                                    <div class="pb-4">
                                                                        @if (count($ride->bookings->where('status', '<>', 3)->where('status', '<>', 4)) > 0)
                                                                            <a
                                                                                href="{{ route('my_passengers', ['lang' => $selectedLanguage->abbreviation, 'departure' => $from, 'destination' => $to, 'id' => $ride->id]) }}">Review
                                                                                passenger</a>
                                                                        @endif
                                                                    </div>
                                                                    @if (count($ride->bookings->where('status', '<>', 3)->where('status', '<>', 4)) > 0)
                                                                        <div
                                                                            class="flex no-scrollbar overflow-x-auto items-center space-x-2">
                                                                            @foreach ($ride->bookings->where('status', '<>', 3)->where('status', '<>', 4) as $booking)
                                                                                @for ($i = 0; $i < $booking->seats; $i++)
                                                                                    @if ($booking->passenger)
                                                                                        @if ($booking->passenger->profile_image)
                                                                                            <img class="w-10 h-10 rounded-full"
                                                                                                src="{{ $booking->passenger->profile_image }}"
                                                                                                alt="">
                                                                                        @else
                                                                                            <img class="w-10 h-10 rounded-full"
                                                                                                src="{{ asset('images/59-booked-seat.png') }}"
                                                                                                alt="">
                                                                                        @endif
                                                                                    @endif
                                                                                @endfor
                                                                            @endforeach
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </a>

                                                    </div>
                                                    @endif
                                                @endforeach
                                                {{ $pastRides->links() }}
                                            @else
                                                <p>{{ $tripsPage->no_completed_rides_label ?? 'You have no completed rides' }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
