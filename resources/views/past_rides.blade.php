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

                                                        $orderedStops = ($ride->rideStops ?? collect())->sortBy('stop_order')->values();
                                                        $matchedFromStopIndex =
                                                            isset($ride->matched_from_stop_index) &&
                                                            $ride->matched_from_stop_index !== null
                                                                ? (int) $ride->matched_from_stop_index
                                                                : null;
                                                        $segmentFromIndex = 0;
                                                        if (
                                                            $matchedFromStopIndex !== null &&
                                                            $orderedStops->has($matchedFromStopIndex)
                                                        ) {
                                                            $segmentFromIndex = $matchedFromStopIndex;
                                                        }
                                                        $originIsMiddleOfParentRoute =
                                                            $orderedStops->count() >= 2 && $segmentFromIndex > 0;
                                                    @endphp
                                                    @if ($defaultDetail)
                                                    <div class="relative even:bg-gray-200 odd:bg-white">
                                                        <a class=""
                                                            href="{{ route('my_ride_detail', ['lang' => $selectedLanguage->abbreviation, 'departure' => $from, 'destination' => $to, 'id' => $ride->id]) }}">
                                                            <div class="rounded-lg shadow-3xl border-[3px] border-solid border-gray-100 "
                                                                id="ride-29">
                                                                <div class="grid grid-cols-5 gap-4 p-4 items-start">
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
                                                                    <div class="col-span-3">
                                                                        <div class="flex flex-row items-center flex-wrap gap-2">
                                                                            <p class="flex items-center space-x-2 font-semibold">
                                                                                {{ $departureDateLabel }}
                                                                                {{ $rideDetailPage->at_label }}
                                                                                {{ $departureTimeLabel ?? 'N/A' }}
                                                                            </p>
                                                                            @if($ride->isPinkRide())
                                                                                <img class="w-12 h-12 ml-2" src="{{ asset('home_page_icons/' . $postRidePage->features_option1->icon) }}" alt="">
                                                                            @endif
                                                                            @if($ride->isExtraCareRide())
                                                                                <img class="w-12 h-12 ml-2" src="{{ asset('home_page_icons/' . $postRidePage->features_option2->icon) }}" alt="">
                                                                            @endif
                                                                        </div>
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
                                                                                    <h4
                                                                                        class="flex gap-2 items-baseline text-xl text-black">
                                                                                        @isset($rideDetailPage->card_section_from_label)
                                                                                            {{ $rideDetailPage->card_section_from_label }}
                                                                                        @endisset
                                                                                        @if ($originIsMiddleOfParentRoute)
                                                                                            <span class="w-4 h-4 ml-2" data-tippy-content="{{ optional($findRidePage ?? null)->depends_on_other_stops_tooltip ?? 'This location depends on other stops' }}">
                                                                                                <svg width="20px" height="20px" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                                    <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                                                                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                                                                                    <g id="SVGRepo_iconCarrier">
                                                                                                        <path d="M1.5 0C0.671573 0 0 0.671573 0 1.5C0 2.32843 0.671573 3 1.5 3C2.15311 3 2.70873 2.5826 2.91465 2H4.5C5.88071 2 7 3.11929 7 4.5V10.5C7 12.433 8.567 14 10.5 14H12.0854C12.2913 14.5826 12.8469 15 13.5 15C14.3284 15 15 14.3284 15 13.5C15 12.6716 14.3284 12 13.5 12C12.8469 12 12.2913 12.4174 12.0854 13H10.5C9.11929 13 8 11.8807 8 10.5V4.5C8 2.567 6.433 1 4.5 1H2.91465C2.70873 0.417404 2.15311 0 1.5 0Z" fill="#0066eb"></path>
                                                                                                    </g>
                                                                                                </svg>
                                                                                            </span>
                                                                                        @endif
                                                                                    </h4>
                                                                                    <div class="flex gap-2 items-baseline">
                                                                                        <h3
                                                                                            class="text-primary font-FuturaMdCnBT text-xl md:text-2xl md:mb-4">
                                                                                            {{ $from }}.
                                                                                        </h3>
                                                                                        <label class="text-black">
                                                                                            {{ $rideDetailPage->pickup_at_label ?? 'Pick-up at' }}:
                                                                                        </label>
                                                                                        <p class="">
                                                                                            {{ $ride->pickup }}
                                                                                        </p>
                                                                                    </div>
                                                                                </div>
                                                                                @if ($ride->rideStops->isNotEmpty() && $ride->rideStops->count() > 2)
                                                                                    <div class="ml-12 md:ml-20 flex">
                                                                                        <label class="text-xl text-black">Stops on the way</label>
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
                                                                                    <h4 class="flex gap-2 items-baseline text-xl text-black">
                                                                                        @isset($rideDetailPage->card_section_to_label)
                                                                                            {{ $rideDetailPage->card_section_to_label }}
                                                                                        @endisset
                                                                                    </h4>
                                                                                    
                                                                                    <div class="flex gap-2 items-baseline">
                                                                                        <h3 class="text-primary font-FuturaMdCnBT text-xl md:text-2xl md:mb-4">
                                                                                            {{ $to }}.
                                                                                        </h3>
                                                                                        <label class="text-black">
                                                                                            {{ $rideDetailPage->dropoff_at_label ?? 'Drop-off at' }}:
                                                                                        </label>
                                                                                        <p class="">
                                                                                            {{ $ride->dropoff }}
                                                                                        </p>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-span-2 px-4">
                                                                        <div class="grid justify-end mt-4">
                                                                            <div class="flex items-center justify-end gap-2 mb-2">
                                                                                <div
                                                                                    class="w-fit px-2 py-1 rounded bg-green-100 text-sm text-green-600">
                                                                                    Completed
                                                                                </div>
                                                                            </div>
                                                                            <div class="pr-8">
                                                                                <p class="font-medium">
                                                                                    {{ str_replace(':count', $ride->seats, $rideDetailPage->total_seats_label ?? 'Total :count seats') }}
                                                                                </p>
                                                                            </div>
                                                                            <p class="text-xl font-semibold text-primary">
                                                                                ${{ number_format(floatval($defaultDetail->price / 100), 2) }}
                                                                                <small>
                                                                                    @isset($rideDetailPage->card_section_per_seat)
                                                                                        {{ $rideDetailPage->card_section_per_seat }}
                                                                                    @endisset
                                                                                </small>
                                                                            </p>
                                                                            @php
                                                                                $pendingBookingRequests = $ride->bookings->where(
                                                                                    'status',
                                                                                    0,
                                                                                );
                                                                            @endphp
                                                                            @if ($pendingBookingRequests->isNotEmpty())
                                                                                <div class="mt-2 rounded-lg border-2 border-red-400 bg-red-50 px-3 py-2.5 shadow-md animate__animated animate__fadeInDown booking-request-alert">
                                                                                    <div class="flex items-center gap-2">
                                                                                        <svg class="h-5 w-5 flex-shrink-0 text-red-600"
                                                                                            xmlns="http://www.w3.org/2000/svg"
                                                                                            fill="none" viewBox="0 0 24 24"
                                                                                            stroke-width="2"
                                                                                            stroke="currentColor">
                                                                                            <path stroke-linecap="round"
                                                                                                stroke-linejoin="round"
                                                                                                d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75v-.7V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0M3.124 7.5A8.969 8.969 0 0 1 5.292 3m13.416 0a8.969 8.969 0 0 1 2.168 4.5" />
                                                                                        </svg>
                                                                                        <p class="font-semibold text-red-700">
                                                                                            {{ str_replace(':count', $pendingBookingRequests->count(), $rideDetailPage->request_booking_label ?? 'You have :count booking request(s).') }}
                                                                                        </p>
                                                                                    </div>
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div
                                                                    class="border-t border-gray-300 grid sm:grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-gray-300">
                                                                    <div class="flex items-center justify-start p-4">
                                                                        <label class="text-black">
                                                                            @isset($rideDetailPage->card_section_booked)
                                                                                {{ $rideDetailPage->card_section_booked }}:
                                                                            @endisset
                                                                        </label>
                                                                        <p class="text-primary font-semibold ml-2">
                                                                            {{ $ride->bookings()->where('status', '<>', 3)->where('status', '<>', 4)->whereHas('passenger', function ($query) {
                                                                                    $query->whereNull('deleted_at');
                                                                                })->sum('seats') }}
                                                                            @isset($rideDetailPage->card_section_seats)
                                                                                {{ $rideDetailPage->card_section_seats }}
                                                                            @endisset
                                                                        </p>
                                                                    </div>
                                                                    <div class="p-4">
                                                                        <div class="flex items-center justify-between">
                                                                            <label class="text-black">
                                                                                @isset($rideDetailPage->card_section_seats_fee)
                                                                                    {{ $rideDetailPage->card_section_seats_fee }}
                                                                                @endisset
                                                                                :
                                                                            </label>
                                                                            <p class="text-primary font-semibold">

                                                                                ${{ number_format(floatval($ride->bookings()->where('status', '<>', 3)->where('status', '<>', 4)->whereHas('passenger', function ($query) {$query->whereNull('deleted_at');})->sum('seats') * floatval($defaultDetail->price) / 100),2) }}
                                                                            </p>
                                                                        </div>

                                                                        <div class="flex items-center justify-between">
                                                                            <label class="text-black">
                                                                                @isset($rideDetailPage->card_section_booking_fee)
                                                                                    {{ $rideDetailPage->card_section_booking_fee }}
                                                                                @endisset
                                                                                :
                                                                            </label>
                                                                            <p class="text-primary font-semibold">
                                                                                ${{ number_format(floatval($ride->bookings->where('status', '<>', 3)->where('status', '<>', 4)->sum('booking_credit')), 2) }}
                                                                            </p>
                                                                        </div>

                                                                        <div class="flex items-center justify-between">
                                                                            <label class="text-black">
                                                                                @isset($rideDetailPage->card_section_amount)
                                                                                    {{ $rideDetailPage->card_section_amount }}
                                                                                @endisset
                                                                                : </label>
                                                                            <p class="text-primary font-semibold">
                                                                                ${{ number_format(floatval($ride->bookings()->where('status', '<>', 3)->where('status', '<>', 4)->whereHas('passenger', function ($query) {$query->whereNull('deleted_at');})->sum('seats') *floatval($defaultDetail->price / 100) + $ride->bookings->where('status', '<>', 3)->where('status', '<>', 4)->sum('booking_credit')),2) }}
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
