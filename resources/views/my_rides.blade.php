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
    @if (session('error'))
        <div id="errorModal" class="relative z-50" aria-labelledby="error-modal-title" role="dialog" aria-modal="true">
            <div onclick="closeErrorModal()" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                    <div
                        class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
                        <button type="button" onclick="closeErrorModal()"
                            class="absolute top-3 right-3 text-gray-400 hover:text-gray-500">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start justify-center">
                                <div class="mx-auto h-16 w-16 flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="2" stroke="currentColor" class="w-12 h-12 text-red-500">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="text-center sm:ml-4 sm:mt-0 sm:text-left">
                                <h3 class="text-3xl text-center font-FuturaMdCnBT font-medium text-gray-900 mb-4"
                                    id="error-modal-title">Notice</h3>
                                <div class="mt-2 w-full">
                                    <p class="can-exp-p text-center text-gray-700">{!! session('error') !!}</p>
                                </div>
                            </div>
                        </div>
                        <div class="px-4 pb-6 pt-4 flex items-center justify-center sm:px-6">
                            <button type="button" onclick="closeErrorModal()"
                                class="button-exp-fill">{{ $siteText['close_btn_text'] }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
            function closeErrorModal() {
                const modal = document.getElementById('errorModal');
                if (modal) modal.style.display = 'none';
            }
        </script>
    @endif
    @if (session('message'))
        <div id="myModal" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div onclick="closeModal()" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                    <div
                        class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
                        <button type="button" onclick="closeModal()"
                            class="absolute top-3 right-3 text-gray-400 hover:text-gray-500">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start justify-center">
                                <div class="mx-auto h-16 w-16 flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="4" stroke="currentColor" class="w-12 h-12 text-greenXS">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                    </svg>
                                </div>

                                <!-- <div
                                class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full sm:mx-0 sm:h-10 sm:w-10 bg-green-500">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-lg text-white w-8 h-8" viewBox="0 0 16 16">
                                    <path d="M7.005 3.1a1 1 0 1 1 1.99 0l-.388 6.35a.61.61 0 0 1-1.214 0zM7 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0"/>
                                </svg>
                            </div> -->
                            </div>
                            <div class="text-center sm:ml-4 sm:mt-0 sm:text-left">
                                <div class="">
                                    <h3 class="text-3xl text-center font-FuturaMdCnBT font-medium text-gray-900 mb-4"
                                        id="modal-title">{!! session('heading') !!}</h3>
                                </div>
                                <div class="mt-2 w-full">
                                    <p class="can-exp-p text-center">{!! session('message') !!}</p>
                                </div>
                            </div>
                        </div>
                        <div class="px-4 pb-6 pt-4 flex items-center space-x-2 sm:space-x-4 sm:px-6 justify-center">
                            @if (session('id'))
                                <a href="{{ route('repost_ride', ['lang' => $selectedLanguage->abbreviation, 'id' => session('id')]) }}"
                                    class="inline-flex w-full justify-center rounded bg-greenXS px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white whitespace-nowrap hover:text-white hover:shadow-lg shadow-sm hover:bg-greenXS sm:ml-3 sm:w-fit">Post
                                    a Return Ride</a>
                            @endif
                            <a href="" class="button-exp-fill">{{ $siteText['close_btn_text'] }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    @if (session('price_warning'))
        <!-- Modal for Price Warning (Exceeds $0.66/km per seat but <= $0.72/km per seat) -->
        <div id="priceWarningModal" class="hidden fixed inset-0 z-50" aria-labelledby="price-warning-modal-title"
            role="dialog" aria-modal="true">
            <div onclick="closePriceWarningModal()" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity">
            </div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                    <div
                        class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
                        <button type="button" onclick="closePriceWarningModal()"
                            class="absolute top-4 right-4 text-gray-400 hover:text-gray-500 z-50">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                        <div class="bg-white px-4 mt-10 sm:mt-1 pb-4 pt-16 sm:p-6 sm:pb-4 sm:pt-16">
                            <div class="text-center sm:ml-4 sm:mt-0 sm:text-left">
                                <div class="">
                                    <h3 class="text-3xl text-center font-FuturaMdCnBT text-gray-900 mb-4">Recommended
                                        Contribution Limit</h3>
                                </div>
                                <div class="mt-2 w-full">
                                    <p class="can-exp-p text-center mb-3" id="priceWarningParagraph1">The price you
                                        entered is above the standard reimbursement rate recommended by the CRA and Revenu
                                        Québec.</p>
                                    <p class="can-exp-p text-center" id="priceWarningParagraph2">While you can proceed, we
                                        suggest reducing the price per seat. This ensures your ride remains a standard
                                        carpool even if you drive long distances this year.</p>
                                </div>
                            </div>
                        </div>
                        <div class="px-4 pb-6 pt-4 flex items-center space-x-2 sm:space-x-4 sm:px-6 justify-center">
                            <button type="button" onclick="closePriceWarningModal()" class="button-exp-fill">Got
                                it</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const modal = document.getElementById('priceWarningModal');
                if (modal) {
                    modal.classList.remove('hidden');
                    modal.style.display = 'block';
                    modal.style.visibility = 'visible';
                    modal.style.opacity = '1';
                    modal.style.zIndex = '50';
                }
            });

            function closePriceWarningModal() {
                const modal = document.getElementById('priceWarningModal');
                if (modal) {
                    modal.classList.add('hidden');
                    modal.style.display = 'none';
                }
            }
        </script>
    @endif
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
                                            @if (!empty($rides) && count($rides) > 0)
                                                @foreach ($rides as $ride)
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
                                                                @if ($ride->make === '')
                                                                    <span
                                                                        class="bg-red-100 text-red-800 text-sm font-medium ml-3 px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">Not
                                                                        live</span>
                                                                @endif
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
                                                                        <div class="flex flex-row items-center">
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
                                                                                    <p
                                                                                        class="font-bold text-xl text-black">
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
                                                                                            {{ $rideDetailPage->pickup_at_label ?? 'Pick-up at' }}:
                                                                                            {{ $ride->pickup }}
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
                                                                                        <h3 class="text-primary font-FuturaMdCnBT text-xl md:text-2xl md:mb-4">
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
                                                                    
                                                                    <div class="col-span-2 px-4">
                                                                        <div class="grid justify-end mt-4">
                                                                            <div class="pr-8">
                                                                                <p class="font-medium">
                                                                                    {{ str_replace(':count', $ride->seats, $rideDetailPage->total_seats_label ?? 'Total :count seats') }}
                                                                                </p>
                                                                            </div>
                                                                            <p class="text-xl font-semibold text-primary">
                                                                                ${{ number_format(floatval($ride->detail->price/100), 2) }}
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
                                                                        <p class="font-semibold">
                                                                            @isset($findrideDetailPage->card_section_booked)
                                                                                {{ $findrideDetailPage->card_section_booked }}:
                                                                            @endisset
                                                                        </p>
                                                                        <p class="text-primary font-semibold ml-2">
                                                                            {{ $ride->bookings()->where('status', '<>', 3)->where('status', '<>', 4)->whereHas('passenger', function ($query) {
                                                                                    $query->whereNull('deleted_at'); // Exclude soft deleted users
                                                                                })->sum('seats') }}
                                                                            @isset($findrideDetailPage->card_section_seats)
                                                                                {{ $findrideDetailPage->card_section_seats }}
                                                                            @endisset
                                                                        </p>
                                                                    </div>
                                                                    <div class="p-4">
                                                                        <div class="flex items-center justify-between">
                                                                            <p class="font-semibold">
                                                                                @isset($findrideDetailPage->card_section_seats_fee)
                                                                                    {{ $findrideDetailPage->card_section_seats_fee }}
                                                                                @endisset
                                                                                : </p>
                                                                            <p class="text-primary font-semibold">

                                                                                ${{ number_format(floatval($ride->bookings()->where('status', '<>', 3)->where('status', '<>', 4)->whereHas('passenger', function ($query) {$query->whereNull('deleted_at');})->sum('seats') * floatval($ride->detail->price) / 100),2) }}
                                                                            </p>
                                                                        </div>

                                                                        <div class="flex items-center justify-between">
                                                                            <p class="font-semibold">
                                                                                @isset($findrideDetailPage->card_section_booking_fee)
                                                                                    {{ $findrideDetailPage->card_section_booking_fee }}
                                                                                @endisset
                                                                                : </p>
                                                                            <p class="text-primary font-semibold">
                                                                                ${{ number_format(floatval($ride->bookings->where('status', '<>', 3)->where('status', '<>', 4)->sum('booking_credit')), 2) }}
                                                                            </p>
                                                                        </div>

                                                                        <div class="flex items-center justify-between">
                                                                            <p class="font-semibold">
                                                                                @isset($findrideDetailPage->card_section_amount)
                                                                                    {{ $findrideDetailPage->card_section_amount }}
                                                                                @endisset
                                                                                : </p>
                                                                            <p class="text-primary font-semibold">
                                                                                ${{ number_format(floatval($ride->bookings()->where('status', '<>', 3)->where('status', '<>', 4)->whereHas('passenger', function ($query) {$query->whereNull('deleted_at');})->sum('seats') *floatval($ride->detail->price / 100) + $ride->bookings->where('status', '<>', 3)->where('status', '<>', 4)->sum('booking_credit')),2) }}
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
                                                                @php
                                                                    $bookings = $ride->bookings->whereNotIn('status', [3, 4]);
                                                                @endphp

                                                                @if ($bookings->isNotEmpty())
                                                                    <div class="border-t border-gray-300 flex no-scrollbar overflow-x-auto items-center space-x-2 p-4">
                                                                        
                                                                        @foreach ($bookings as $booking)
                                                                            @php
                                                                                $image = $booking->passenger?->profile_image
                                                                                    ?? asset('images/59-booked-seat.png');
                                                                            @endphp

                                                                            @for ($i = 0; $i < $booking->seats; $i++)
                                                                                <img class="w-10 h-10 rounded-full" src="{{ $image }}" alt="">
                                                                            @endfor
                                                                        @endforeach

                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </a>
                                                    </div>
                                                    @endif
                                                @endforeach
                                                {{ $rides->links() }}
                                            @else
                                                <p>{{ $tripsPage->no_upcoming_rides_label ?? 'You have no upcoming rides scheduled.' }}
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
@section('script')
    <script>
        function closeModal() {
            // Hide all modals
            document.querySelectorAll('.relative.z-50').forEach(modal => {
                modal.style.display = 'none';
            });

            // Also remove any session messages from the URL
            if (window.history.replaceState) {
                const cleanUrl = window.location.href.split('?')[0];
                window.history.replaceState({}, document.title, cleanUrl);
            }
        }

        function closeModal() {
            const modal = document.getElementById('myModal');
            if (modal) {
                modal.classList.add('hidden');
            }
        }
    </script>
@endsection
