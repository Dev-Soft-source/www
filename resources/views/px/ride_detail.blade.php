@extends('layouts.template')

@section('content')
    <div id="my-chat-pop-modal" class="hidden relative z-50" aria-labelledby="px-login-modal-title" role="dialog"
        aria-modal="true">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                <div
                    class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
                    <button onclick="closePopupModal()"
                        class="absolute top-2 right-2 p-1 rounded-full hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="text-center">
                            <div class="w-full">
                                <h3 class="text-3xl text-center font-FuturaMdCnBT font-medium text-gray-900 mb-4"
                                    id="px-login-modal-title">
                                    {{ $siteText['heading_text'] ?? 'Login required' }}
                                </h3>
                            </div>
                            <div class="mt-2 w-full">
                                <p class="can-exp-p text-center" id="px-login-modal-message">
                                    {{ $rideDetailPage->chat_error_message ?? 'Please log in or sign up to continue.' }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="px-4 pb-6 pt-4 sm:flex sm:flex-row-reverse sm:px-6 justify-center gap-2">
                        <a href="{{ route('login', ['lang' => optional($selectedLanguage)->abbreviation ?? app()->getLocale()]) }}"
                            class="inline-flex w-full justify-center rounded bg-blue-500 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-blue-400 sm:w-24">
                            {{ $siteText['login_btn_text'] ?? 'Log in' }}
                        </a>
                        <a href="{{ route('signup', ['lang' => optional($selectedLanguage)->abbreviation ?? app()->getLocale()]) }}"
                            class="inline-flex w-full justify-center rounded bg-greenXS px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-greenXS sm:w-24">
                            {{ $siteText['signup_btn_text'] ?? 'Sign up' }}
                        </a>
                        <button onclick="closePopupModal()"
                            class="inline-flex w-full justify-center rounded bg-red-500 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-red-400 sm:w-24">
                            {{ $siteText['close_btn_text'] ?? 'Close' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto my-10 xl:my-14 px-4 xl:px-0">
        @if (($displaySeatsAvailable ?? $ride->seats_available) == 0)
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
            $isPinkRide = $ride->women_only === true || $ride->women_only === 1;
            $isExtraCareRide = $ride->extra_care === true || $ride->extra_care === 1;
            $rideTypeAlert = null;

            if ($isPinkRide && $isExtraCareRide) {
                $rideTypeAlert = [
                    'wrapper' => 'bg-purple-100 border-purple-500 text-purple-800',
                    'icon' => 'text-purple-500',
                    'message' => 'This is a Pink Ride and Extra+ Ride',
                ];
            } elseif ($isExtraCareRide) {
                $rideTypeAlert = [
                    'wrapper' => 'bg-green-100 border-green-500 text-green-800',
                    'icon' => 'text-green-500',
                    'message' => 'This is a Extra+ Ride',
                ];
            } elseif ($isPinkRide) {
                $rideTypeAlert = [
                    'wrapper' => 'bg-pink-100 border-pink-500 text-pink-800',
                    'icon' => 'text-pink-500',
                    'message' => 'This is a Pink Ride',
                ];
            }
        @endphp
        @if ($rideTypeAlert)
            <div class="col-span-3 w-full">
                <div class="{{ $rideTypeAlert['wrapper'] }} border-l-4 px-4 py-2 rounded flex items-center" role="alert">
                    <svg class="w-6 h-6 mr-2 {{ $rideTypeAlert['icon'] }} flex-shrink-0" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"
                            fill="none" />
                        <path d="M12 8v4m0 4h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                    <span class="text-lg">{{ $rideTypeAlert['message'] }}</span>
                </div>
            </div>
        @endif

        <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-y-4 md:gap-4 ride-detail-page">
            <div class="col-span-2">
                <x-px.ride-details :ride="$ride" :rideDetailPage="$rideDetailPage" :parentOrigin="$rideDetailsData['parentOrigin']" :parentDestination="$rideDetailsData['parentDestination']" :origin="$rideDetailsData['origin']"
                    :destination="$rideDetailsData['destination']" :pickupLocation="$rideDetailsData['pickupLocation']" :dropoffLocation="$rideDetailsData['dropoffLocation']" :originDepartureAt="$rideDetailsData['originDepartureAt']" :pricePerSeatMinor="$rideDetailsData['pricePerSeatMinor']"
                    :currency="$rideDetailsData['currency']" :segmentStops="$rideDetailsData['segmentStops']" :segmentMode="$rideDetailsData['segmentMode']" :bookingModeLabel="$bookingModeLabel ?? null" :bookingMethodLabel="$bookingMethodLabel ?? null"
                    :bookingModeCode="$bookingModeCode ?? null" :bookingMethodCode="$bookingMethodCode ?? null" :postRidePage="$postRidePage ?? null" />
            </div>

            <div class="col-span-1">
                <div class="space-y-4">
                    <div class="bg-white rounded-lg overflow-hidden shadow-3xl">
                        <h3 class="bg-primary text-white py-2 px-4 text-2xl xl:text-3xl">
                            {{ $rideDetailPage->vehicle_info_label ?? 'Vehicle info' }}
                        </h3>
                        <div class="p-4">
                            <div class="flex items-center gap-4">
                                @if ($existingBooking && !empty($ride->car_image))
                                    <div class="w-20 h-20 rounded-full overflow-hidden bg-gray-100 shrink-0">
                                        <img class="w-full h-full object-cover" src="{{ $ride->car_image }}"
                                            alt="">
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    @if ($ride->vehicle)
                                        <div class="flex items-center flex-wrap gap-x-2 text-black">
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
                                        @if ($existingBooking && !empty($ride->vehicle->license_no))
                                            <p class="font-semibold text-lg text-black text-start mt-3">
                                                {{ $ride->vehicle->license_no }}
                                            </p>
                                        @endif
                                    @else
                                        <p class="text-gray-500">No vehicle information available</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg overflow-hidden shadow-3xl">
                        <h3 class="bg-primary text-white py-2 px-4 text-2xl xl:text-3xl">
                            {{ $rideDetailPage->driver_info_label ?? 'Driver info' }}
                        </h3>
                        <div class="p-4">
                            <div class="flex items-center gap-4">
                                <div class="w-20 h-20 rounded-full overflow-hidden bg-gray-100 shrink-0">
                                    @if (!empty($ride->driver?->profile_image))
                                        <img class="w-full h-full object-cover" src="{{ $ride->driver->profile_image }}"
                                            alt="{{ $driverDisplayName ?? 'Driver' }}">
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <a href="{{ route('driver_info', ['lang' => $selectedLanguage->abbreviation, 'id' => $ride->driver?->id]) }}"
                                        class="text-xl font-semibold hover:underline">
                                        {{ $driverDisplayName ?? 'N/A' }}
                                    </a>
                                    <p class="text-gray-700 mt-1">
                                        {{ $rideDetailPage->passengers_driven_label ?? 'Passengers driven' }}
                                        <span class="font-semibold text-black">{{ $driverPassengersDriven ?? 0 }}</span>
                                    </p>
                                    <div class="flex items-center gap-3 mt-2 text-black">
                                        <div class="flex items-center gap-1">
                                            <span
                                                class="font-medium">{{ number_format((float) ($driverAverageRating ?? 0), 1) }}</span>
                                            <span class="inline-flex cursor-pointer w-6 h-6">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                    fill="currentColor" class="w-full h-full text-yellow-500">
                                                    <path fill-rule="evenodd"
                                                        d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z"
                                                        clip-rule="evenodd"></path>
                                                </svg>
                                            </span>
                                        </div>
                                        @if (!empty($driverHasVerifiedEmail))
                                            <span class="inline-flex cursor-pointer w-6 h-6"
                                                data-tippy-content="{{ $rideDetailPage->verified_email_tooltip ?? '' }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                    class="w-full h-full text-green-500">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                                                </svg>
                                            </span>
                                        @endif
                                        @if (!empty($driverHasVerifiedPhone))
                                            <span class="inline-flex cursor-pointer w-6 h-6"
                                                data-tippy-content="{{ $rideDetailPage->verified_phone_tooltip ?? '' }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                    class="w-full h-full text-green-500">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
                                                </svg>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if (strtotime($ride->departure_at) > strtotime('now') && $ride->driver?->id !== Auth::id())
                        <div class="bg-white rounded-lg overflow-hidden shadow-3xl">
                            <h3 class="bg-primary text-white py-2 px-4 text-2xl xl:text-3xl">
                                @isset($rideDetailPage->driver_chat_heading)
                                    {{ $rideDetailPage->driver_chat_heading }}
                                @endisset
                            </h3>
                            <div class="p-4 w-full">
                                <p>
                                    @isset($rideDetailPage->driver_chat_label)
                                        {{ $rideDetailPage->driver_chat_label }}
                                    @endisset
                                </p>
                                <div class="flex justify-center mt-4">
                                    @php
                                        $lang = optional($selectedLanguage)->abbreviation ?? app()->getLocale();
                                        $chatUrl = route('px.chat', [
                                            'lang' => $lang,
                                            'id' => $ride->id,
                                            'from_stop_id' => $selectedFromStopId,
                                            'to_stop_id' => $selectedToStopId,
                                        ]);
                                    @endphp
                                    @if (Auth::check() && $ride->driver?->id)
                                        <a href="{{ $chatUrl }}"
                                            class="inline-block bg-greenXS hover:bg-greenXS text-white text-base md:text-lg rounded font-FuturaMdCnBT px-5 py-2 border border-greenXS hover:border-greenXS hover:text-white text-center focus:bg-greenXS focus:text-white active:text-white active:bg-greenXS w-36">
                                            {{ $rideDetailPage->driver_chat_button_label ?? '' }}
                                        </a>
                                    @elseif (!Auth::check())
                                        <button type="button"
                                            class="inline-block bg-greenXS hover:bg-greenXS text-white text-base md:text-lg rounded font-FuturaMdCnBT px-5 py-2 border border-greenXS hover:border-greenXS hover:text-white text-center focus:bg-greenXS focus:text-white active:text-white active:bg-greenXS w-36"
                                            onclick="openLoginAlert('{{ addslashes(($rideDetailPage->chat_error_message ?? 'Please log in or sign up to chat with the driver.') . ' ' . ($ride->driver?->first_name ?? '')) }}')">
                                            {{ $rideDetailPage->driver_chat_button_label ?? '' }}
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
                        @if (!empty($existingBooking))
                            <div class="flex justify-center mt-4 gap-3">
                                <a href="{{ route('px.booking', ['lang' => optional($selectedLanguage)->abbreviation, 'from_stop_id' => $selectedFromStopId, 'to_stop_id' => $selectedToStopId]) }}"
                                    class="group flex items-center button-exp-fill rounded cursor-pointer justify-center py-2 px-4 text-lg font-FuturaMdCnBT">
                                    <span
                                        class="font-medium text-xl">{{ $rideDetailPage->edit_button_actions_label ?? 'Update Booking' }}</span>
                                </a>
                                <a href="{{ route('px.booking.cancel_page', ['lang' => optional($selectedLanguage)->abbreviation, 'id' => $existingBooking->id]) }}"
                                    class="group flex items-center button-exp-no-fill rounded cursor-pointer justify-center py-2 px-4 text-lg font-FuturaMdCnBT">
                                    <span
                                        class="font-medium text-xl">{{ $rideDetailPage->cancel_booking_btn_label ?? 'Cancel Booking' }}</span>
                                </a>
                            </div>
                        @elseif (
                            (int) ($displaySeatsAvailable ?? $ride->seats_available) > 0 &&
                                $ride->status !== 'cancelled' &&
                                strtotime($ride->departure_at) > strtotime('now'))
                            <div class="flex justify-center mt-4">
                                @if (Auth::check())
                                    <a href="{{ route('px.booking', ['lang' => optional($selectedLanguage)->abbreviation, 'from_stop_id' => $selectedFromStopId, 'to_stop_id' => $selectedToStopId]) }}"
                                        class="group flex items-center button-exp-fill rounded cursor-pointer justify-center py-1 px-4 text-lg font-FuturaMdCnBT">
                                        @if ($bookingModeCode === 'manual')
                                            <img class="w-10 h-10 rounded-full"
                                                src="{{ asset('home_page_icons/' . $postRidePage->booking_option2->icon) }}"
                                                alt="">
                                        @elseif ($bookingModeCode === 'instant')
                                            <img class="w-10 h-10 rounded-full"
                                                src="{{ asset('home_page_icons/' . $postRidePage->booking_option1->icon) }}"
                                                alt="">
                                        @endif
                                        <span class="font-medium text-xl">
                                            Book Your Seats
                                        </span>
                                    </a>
                                @else
                                    <button type="button"
                                        class="group flex items-center button-exp-fill rounded cursor-pointer justify-center py-1 px-4 text-lg font-FuturaMdCnBT"
                                        onclick="openLoginAlert('Please log in or sign up to continue with your booking.')">
                                        @if ($bookingModeCode === 'manual')
                                            <img class="w-10 h-10 rounded-full"
                                                src="{{ asset('home_page_icons/' . $postRidePage->booking_option2->icon) }}"
                                                alt="">
                                        @elseif ($bookingModeCode === 'instant')
                                            <img class="w-10 h-10 rounded-full"
                                                src="{{ asset('home_page_icons/' . $postRidePage->booking_option1->icon) }}"
                                                alt="">
                                        @endif
                                        <span class="font-medium text-xl">
                                            Book Your Seats
                                        </span>
                                    </button>
                                @endif
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        function closePopupModal() {
            document.getElementById('my-chat-pop-modal').style.display = 'none';
        }

        function openLoginAlert(message) {
            var messageElement = document.getElementById('px-login-modal-message');

            if (messageElement && message) {
                messageElement.innerText = message;
            }

            document.getElementById('my-chat-pop-modal').style.display = 'flex';
        }
    </script>
@endsection

@section('style')
    <style>
        .ride-detail-page p {
            font-family: 'Nunito', sans-serif;
        }
    </style>
@endsection
