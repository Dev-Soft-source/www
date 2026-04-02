@extends('layouts.template')

@section('style')
    <style>
        /* Match ride_detail: body text in Roboto, sizes from Tailwind (text-xl, text-sm) */
    </style>

    <!-- Scripts -->
    <script>
        window.authUserId = {{ Auth::id() ?? 'null' }};
        window.ride = @json($ride->id); // Pass $ride to JavaScript
        window.passenger = @json($ride->added_by); // Pass $ride to JavaScript
    </script>
    <script src="{{ asset('js/web.js') }}" defer></script>
@endsection

@section('content')
    <div class="">
        @if (session('message'))
            <div id="my-modal" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                    <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                        <div
                            class="relative relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
                            <button onclick="closeModal()"
                                class="absolute top-2 right-2 p-1 rounded-full hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                            <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                                <div class="sm:flex sm:items-start justify-center">
                                    <!-- <div class="mx-auto flex h-16 w-16 flex-shrink-0 items-center justify-center rounded-full bg-red-500 p-1">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-12 text-white">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                                                        </svg>
                                                    </div> -->
                                </div>
                                <div class="mt-3 text-center">
                                    <div class="mt-2">
                                        <h3 class="text-3xl text-center font-FuturaMdCnBT font-medium text-gray-900 mb-4"
                                            id="modal-title">{!! session('heading') !!}</h3>
                                    </div>
                                    <div class="mt-2 w-full">
                                        <p class="text-center can-exp-p">{!! session('message') !!}</p>
                                    </div>
                                </div>
                            </div>
                            {{-- <div class="px-4 pb-6 pt-4  sm:flex sm:flex-row-reverse sm:px-6 justify-center">
                        <a href=""
                            class="inline-flex w-full justify-center rounded bg-red-500 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-red-400 sm:ml-3 sm:w-24">{{ $siteText['close_btn_text'] ?? 'Close' }}</a>
                    </div> --}}
                            <div class="px-4 pb-6 pt-4 sm:flex sm:flex-row-reverse sm:px-6 justify-center gap-2">
                                <a href="{{ route('login', ['lang' => app()->getLocale()]) }}"
                                    class="inline-flex justify-center rounded bg-blue-500 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-blue-400">
                                    {{ $siteText['login_btn_text'] ?? 'Log in' }}
                                </a>
                                <a href="{{ route('signup', ['lang' => app()->getLocale()]) }}"
                                    class="inline-flex justify-center rounded bg-greenXS px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-greenXS">
                                    {{ $siteText['signup_btn_text'] ?? 'Sign up' }}
                                </a>
                                <button onclick="closeModal()"
                                    class="inline-flex justify-center rounded bg-red-500 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-red-400">
                                    {{ $siteText['close_btn_text'] ?? 'Close' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if (session('success'))
            <div id="my-modal" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                    <div
                        class="relative flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeModal()">
                        </div>
                        <div
                            class="relative animate__animated animate__fadeIn z-20 transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
                            <button onclick="closeModal()"
                                class="absolute top-2 right-2 p-1 rounded-full hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                            <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                                <div class="sm:flex sm:items-start justify-center">
                                    <!-- <div class="mx-auto h-16 w-16">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                            stroke-width="4" stroke="currentColor" class="w-12 h-12 text-greenXS">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                                        </svg>
                                                    </div> -->
                                </div>
                                <div class="w-full">
                                    <p class="text-center can-exp-p">{!! session('success') !!}</p>
                                </div>
                            </div>
                            <div class="px-4 pb-6 pt-4  sm:flex sm:flex-row-reverse sm:px-6 justify-center">
                                <a href=""
                                    class="inline-flex w-full justify-center rounded bg-greenXS px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-greenXS sm:ml-3 sm:w-24">{{ $siteText['close_btn_text'] ?? 'Close' }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if (session('failure'))
            <div class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                    <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                        <div
                            class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg w-full modal-border">
                            <button onclick="closeModal()"
                                class="absolute top-2 right-2 p-1 rounded-full hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                            <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                                <div class="sm:flex sm:items-start justify-center">
                                    <!-- <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full sm:mx-0 sm:h-10 sm:w-10 bg-red-500">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-lg text-white w-8 h-8" viewBox="0 0 16 16">
                                                    <path d="M7.005 3.1a1 1 0 1 1 1.99 0l-.388 6.35a.61.61 0 0 1-1.214 0zM7 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0"/>
                                                </svg>
                                            </div> -->
                                </div>
                                <div class="text-center">

                                    <div class="w-full">
                                        <p class="text-center can-exp-p">{!! session('failure') !!}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="px-4 pb-6 pt-4 sm:flex sm:flex-row-reverse sm:px-6 justify-center">
                                <a href=""
                                    class="whitespace-nowrap inline-flex w-full justify-center rounded bg-red-500 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-red-400 sm:ml-3 sm:w-24">{{ $siteText['close_btn_text'] ?? 'Close' }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <div id="my-chat-pop-modal" class="hidden relative z-50" aria-labelledby="modal-title" role="dialog"
            aria-modal="true">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                    <div
                        class="relative relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
                        <button onclick="closePopupModal()"
                            class="absolute top-2 right-2 p-1 rounded-full hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start justify-center">

                            </div>
                            <div class="text-center">
                                <div class="w-full">
                                    <h3 class="text-3xl text-center font-FuturaMdCnBT font-medium text-gray-900 mb-4"
                                        id="modal-title">{!! session('heading') !!}</h3>
                                </div>
                                <div class="mt-2 w-full">
                                    <p class="can-exp-p text-center">
                                        @isset($rideDetailPage->chat_error_message)
                                            {{ $rideDetailPage->chat_error_message }}
                                        @endisset
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="px-4 pb-6 pt-4 sm:flex sm:flex-row-reverse sm:px-6 justify-center gap-2">
                            <a href="{{ route('login', ['lang' => app()->getLocale()]) }}"
                                class="inline-flex w-full justify-center rounded bg-blue-500 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-blue-400 sm:w-24">
                                {{ $siteText['login_btn_text'] }}
                            </a>
                            <a href="{{ route('signup', ['lang' => app()->getLocale()]) }}"
                                class="inline-flex w-full justify-center rounded bg-greenXS px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-greenXS sm:w-24">
                                {{ $siteText['signup_btn_text'] }}

                            </a>
                            <button onclick="closePopupModal()"
                                class="inline-flex w-full justify-center rounded bg-red-500 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-red-400 sm:w-24">
                                {{ $siteText['close_btn_text'] }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Phone Number Required (any phone) for Regular Ride Booking -->
        <div id="phoneOnFileRequiredModal" class="hidden fixed z-50 inset-0 overflow-y-auto"
            aria-labelledby="phone-on-file-required-modal-title" role="dialog" aria-modal="true">
            <div onclick="closePhoneOnFileRequiredModal()"
                class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0">
                    <div class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border"
                        onclick="event.stopPropagation()">
                        <button type="button" onclick="closePhoneOnFileRequiredModal()"
                            class="absolute top-4 right-4 text-gray-400 hover:text-gray-500 z-50">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                        <div class="bg-white px-4 mt-10 sm:mt-1 pb-4 pt-16 sm:p-6 sm:pb-4 sm:pt-16">
                            <div class="text-center sm:ml-4 sm:mt-0 sm:text-left">
                                <div class="">

                                </div>
                                <div class="mt-2 w-full">
                                    <p class="can-exp-p text-center">
                                        {{ $siteText['phone_on_file_required_text'] ?? 'To book a ride, you must have a phone number on file. Add it in Dashboard → My Phone Number.' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="px-4 pb-6 pt-4 sm:flex sm:flex-row-reverse sm:px-6 justify-center gap-3">
                            <button type="button" onclick="goToPhoneNumberSettings()"
                                class="inline-flex justify-center rounded bg-primary px-6 py-2 font-FuturaMdCnBT text-lg text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-blue-600">
                                {{ $siteText['go_to_my_phone_number_btn_text'] ?? 'Go to My Phone Number' }}
                            </button>
                            <button type="button" onclick="closePhoneOnFileRequiredModal()"
                                class="inline-flex justify-center rounded bg-gray-300 px-6 py-2 font-FuturaMdCnBT text-lg text-gray-700 hover:text-gray-800 hover:shadow-lg shadow-sm hover:bg-gray-400">
                                {{ $siteText['close_btn_text'] ?? 'Close' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="verified_email_phone" class="hidden relative z-50" aria-labelledby="modal-title" role="dialog"
            aria-modal="true">
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div
                    class="relative flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeVerifyModal()">
                    </div>
                    <div
                        class="relative animate__animated animate__fadeIn z-20 transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
                        <button onclick="closeVerifyModal()"
                            class="absolute top-2 right-2 p-1 rounded-full hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start justify-center">

                            </div>
                            <div class="w-full">
                                <p class="text-center can-exp-p" id="verify-popup-text"></p>
                            </div>
                        </div>
                        <div class="px-4 pb-6 pt-4  sm:flex sm:flex-row-reverse sm:px-6 justify-center">
                            <a href="#" onclick="closeVerifyModal()"
                                class="inline-flex w-full justify-center rounded bg-greenXS px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-greenXS sm:ml-3 sm:w-24">{{ $siteText['close_btn_text'] }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container mx-auto my-10 xl:my-14 px-4 xl:px-0">
            @if (
                $ride->seats -
                    $ride->bookings()->where('status', '<>', 3)->where('status', '<>', 4)->whereHas('passenger', function ($query) {
                            $query->whereNull('deleted_at');
                        })->sum('seats') ===
                    0)
                <div class="rounded-lg px-6 py-3 bg-blue-100 text-gray-600" role="alert">
                    @isset($rideDetailPage->all_seats_booked_label)
                        {{ $rideDetailPage->all_seats_booked_label }}
                    @endisset
                </div>
            @endif
            <div class=" gap-3 border-gray-400 pb-2">
                <h1 class="-mb-1">
                    @isset($rideDetailPage->main_heading)
                        {{ $rideDetailPage->main_heading }}
                    @endisset
                </h1>

                @php
                    $user = auth()->user();
                    $isAuthenticated = (bool) $user;

                    $isPinkRide = $ride->isPinkRide();
                    $isExtraCareRide = $ride->isExtraCareRide();
                    $requiresStrictVerification = $isPinkRide || $isExtraCareRide;

                    $isShortDistanceRide = $ride->isShortDistanceRide();

                    $hasAnyPhoneNumber = $user?->hasPhone() ?? false;
                    $hasVerifiedPhone = $user?->hasVerifiedPhone() ?? false;

                    $needsPhoneVerification = $isAuthenticated && $hasAnyPhoneNumber && !$hasVerifiedPhone;
                    $needsPhoneOnFileForRegularRide =
                        $isAuthenticated && !$hasAnyPhoneNumber && !$requiresStrictVerification;
                    $needsVerifiedPhoneForPinkExtra =
                        $isAuthenticated && $requiresStrictVerification && !$hasVerifiedPhone;

                    $hasGovernmentId = !empty($user?->government_issued_id);
                    $showPhotoIdRequiredForBooking =
                        $isAuthenticated && $requiresStrictVerification && !$hasGovernmentId;
                @endphp
                @if ($isShortDistanceRide)
                    <div class="col-span-3 w-full">
                        <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-800 px-4 py-2 rounded flex items-center"
                            role="alert">
                            <svg class="w-6 h-6 mr-2 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"
                                    fill="none" />
                                <path d="M12 8v4m0 4h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                            <span
                                class="text-lg">{{ $siteText['proximalocal_ride_description'] ?? 'This is a Short-Distance Ride, and ProximaRide does not apply any Booking Fee.' }}</span>
                        </div>
                    </div>
                @else
                    @if ($isPinkRide && $isExtraCareRide)
                        <div class="col-span-3 w-full">
                            <div class="bg-orange-100 border-l-4 border-orange-500 text-orange-800 px-4 py-2 rounded flex items-center"
                                role="alert">
                                <svg class="w-6 h-6 mr-2 text-orange-500 flex-shrink-0" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"
                                        fill="none" />
                                    <path d="M12 8v4m0 4h.01" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <span
                                    class="text-lg">{{ $siteText['pink_extra_ride_description'] ?? 'This is a Pink Ride and an Extra+ Ride.' }}</span>
                            </div>
                        </div>
                    @else
                        @if ($isExtraCareRide)
                            <div class="col-span-3 w-full">
                                <div class="bg-green-100 border-l-4 border-green-500 text-green-800 px-4 py-2 rounded flex items-center"
                                    role="alert">
                                    <svg class="w-6 h-6 mr-2 text-green-500 flex-shrink-0" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="2" fill="none" />
                                        <path d="M12 8v4m0 4h.01" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <span
                                        class="text-lg">{{ $siteText['extra_ride_description'] ?? 'This is a Extra+ Ride.' }}</span>
                                </div>
                            </div>
                        @elseif($isPinkRide)
                            <div class="col-span-3 w-full">
                                <div class="bg-pink-100 border-l-4 border-pink-500 text-pink-800 px-4 py-2 rounded flex items-center"
                                    role="alert">
                                    <svg class="w-6 h-6 mr-2 text-pink-500 flex-shrink-0" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="2" fill="none" />
                                        <path d="M12 8v4m0 4h.01" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <span
                                        class="text-lg">{{ $siteText['pink_ride_description'] ?? 'This is a Pink Ride.' }}</span>
                                </div>
                            </div>
                        @endif
                    @endif
                @endif

            </div>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-y-4 lg:gap-4">
                <div class="col-span-2">
                    <x-px.ride-detail-info :ride="$ride" :rideDetailPage="$rideDetailPage" />
                </div>
                <div class="col-span-1">
                    <div class="space-y-4">
                        <div class="bg-white rounded-lg overflow-hidden shadow-3xl">
                            <h3 class="bg-primary text-white py-2 px-4 text-2xl xl:text-3xl">
                                @isset($rideDetailPage->vehicle_info_label)
                                    {{ $rideDetailPage->vehicle_info_label }}
                                @endisset
                            </h3>
                            <div class="flex items-start space-x-4 p-4 w-full">
                                @if (auth()->user() && $ride->bookings && $ride->hasNonRejectedBookingForUser(auth()->user()))
                                    <div>
                                        <div class="w-20 h-20 rounded-full overflow-hidden">
                                            <img class="w-full h-full object-cover" src="{{ $ride->car_image }}"
                                                alt="">
                                        </div>
                                    </div>
                                @endif
                                @php
                                    $vehicleYear = $ride->year ?: optional($ride->vehicle)->year;
                                    $vehicleMake = $ride->make ?: optional($ride->vehicle)->make;
                                    $vehicleModel = $ride->model ?: optional($ride->vehicle)->model;
                                    $vehicleColor = ucfirst($ride->color ?: optional($ride->vehicle)->color);
                                @endphp
                                <div class="text-left">
                                    @if ($vehicleYear || $vehicleMake || $vehicleModel || $vehicleColor)
                                        <div class="flex flex-row items-center justify-center gap-x-1 text-md text-black">
                                            @if ($vehicleYear)
                                                <p class="text-md font-semibold">{{ $vehicleYear }}</p>
                                                @if ($vehicleMake || $vehicleModel || $vehicleColor)
                                                    <span>|</span>
                                                @endif
                                            @endif
                                            @if ($vehicleMake)
                                                <p class="text-md font-semibold">{{ $vehicleMake }}</p>
                                                @if ($vehicleModel || $vehicleColor)
                                                    <span>|</span>
                                                @endif
                                            @endif
                                            @if ($vehicleModel)
                                                <p class="text-md font-semibold">{{ $vehicleModel }}</p>
                                                @if ($vehicleColor)
                                                    <span>|</span>
                                                @endif
                                            @endif
                                            @if ($vehicleColor)
                                                <p class="text-md font-semibold">{{ $vehicleColor }}</p>
                                            @endif
                                        </div>
                                    @endif
                                    @if ($ride->bookings()->where('status', '<>', 3)->where('status', '<>', 4)->exists())
                                        <label class="text-xl text-left text-black">{{ $ride->license_no }}</label>
                                    @endif
                                    @if ($ride->vehicle_type_label)
                                        <p class="text-md">{{ $ride->vehicle_type_label }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="bg-white rounded-lg overflow-hidden shadow-3xl">
                            @php
                                $passengerBookingUuid = null;
                                if (auth()->user() && $ride->bookings) {
                                    $userBooking = $ride->bookings
                                        ->where('user_id', auth()->user()->id)
                                        ->where('status', '<>', '3')
                                        ->where('status', '<>', '4')
                                        ->first();
                                    if ($userBooking && $userBooking->id) {
                                        $passengerBookingUuid = $userBooking->id;
                                    }
                                }
                                $rideIsPast = strtotime($ride->date . ' ' . $ride->time) < strtotime('now');
                                $alreadyReviewedDriver =
                                    auth()->user() &&
                                    \App\Models\Rating::where('ride_id', $ride->id)
                                        ->where('type', '1')
                                        ->where('posted_by', auth()->user()->id)
                                        ->exists();
                                $showReviewLink = $passengerBookingUuid && $rideIsPast && !$alreadyReviewedDriver;

                                $isAvailableShowInfo =
                                    auth()->user() && $ride->hasNonRejectedBookingForUser(auth()->user());
                            @endphp
                            <h3 class="bg-primary text-white py-2 px-4 text-2xl xl:text-3xl">
                                @if ($showReviewLink && $passengerBookingUuid && $isAvailableShowInfo)
                                    @isset($rideDetailPage->review_driver_info_label)
                                        {{ $rideDetailPage->review_driver_info_label }}
                                    @endisset
                                @elseif ($ride->added_by && $isAvailableShowInfo)
                                    @isset($rideDetailPage->driver_info_label)
                                        {{ $rideDetailPage->driver_info_label }}
                                    @endisset
                                @else
                                    @if ($ride_cancelled)
                                        @isset($rideDetailPage->review_driver_info_label)
                                            {{ $rideDetailPage->review_driver_info_label }}
                                        @endisset
                                    @else
                                        @isset($rideDetailPage->driver_info_label)
                                            {{ $rideDetailPage->driver_info_label }}
                                        @endisset
                                    @endif
                                @endif
                            </h3>
                            <div class="flex items-center justify-between p-4 w-full">
                                <div class="flex items-center space-x-4">
                                    @if (auth()->user() && $ride->bookings && $ride->hasNonRejectedBookingForUser(auth()->user()))
                                        <div class="w-20 h-20 rounded-full overflow-hidden">
                                            @php
                                                $hasBookedRide = $ride->bookings
                                                    ->where('user_id', auth()->user()->id)
                                                    ->where('status', 1)
                                                    ->isNotEmpty();

                                                $driverImage = !$hasBookedRide
                                                    ? asset('home_page_icons/1746188912-new-5-driver-female.png')
                                                    : $ride->driver?->profile_image;
                                            @endphp

                                            @if ($driverImage)
                                                <img class="w-full h-full object-cover" src="{{ $driverImage }}"
                                                    alt="">
                                            @endif
                                        </div>
                                    @endif
                                    <div class="text-center">
                                        @if (!$isAvailableShowInfo)
                                            <div class="mb-3 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                                <p class="text-yellow-800 text-sm w-full">
                                                    {{ $rideDetailPage->driver_info_show_label ?? 'Driver details are shared after booking confirmation.' }}
                                                </p>
                                            </div>
                                        @endif
                                        <div class="flex items-baseline gap-2">
                                            <label class="text-xl">
                                                {{ $rideDetailPage->driver_label ?? 'Verified Driver' }}:
                                            </label>
                                            <span class="text-primary">
                                                {{ $ride->driver?->getDisplayName() }}
                                            </span>
                                        </div>

                                        <div class="flex items-baseline gap-2">
                                            <label class="text-xl">
                                                {{ $rideDetailPage->passengers_driven_label ?? 'Passengers Driven' }}:
                                            </label>
                                            <span class="text-primary">
                                                @php
                                                    $drivenNum = $ride->driver
                                                        ?->rides()
                                                        ->where('status', '!=', 2)
                                                        ->where(function ($query) {
                                                            $query
                                                                ->whereDate('rides.date', '<', now()->toDateString())
                                                                ->orWhere(function ($query) {
                                                                    $query
                                                                        ->whereDate(
                                                                            'rides.date',
                                                                            '=',
                                                                            now()->toDateString(),
                                                                        )
                                                                        ->whereTime(
                                                                            'rides.time',
                                                                            '<=',
                                                                            now()->toTimeString(),
                                                                        );
                                                                });
                                                        })
                                                        ->get()
                                                        ->flatMap(function ($ride) {
                                                            return $ride->bookings()->pluck('seats');
                                                        })
                                                        ->sum();
                                                @endphp
                                                {{ $drivenNum > 100 ? '99+' : $drivenNum }}
                                            </span>
                                        </div>

                                        <div class="flex items-center gap-4 w-full">
                                            <div class="flex items-center gap-1 w-auto">
                                                @php
                                                    $filteredRatings = $ratings
                                                        ->where('status', 1)
                                                        ->where('type', '1')
                                                        ->filter(function ($rating) use ($ride) {
                                                            return $rating->ride &&
                                                                $rating->ride->added_by === $ride->added_by;
                                                        });

                                                    $totalAverage = $filteredRatings->avg('average_rating') ?? 0;
                                                    $driverHasReviews = $filteredRatings->isNotEmpty();
                                                @endphp
                                                @if ($driverHasReviews)
                                                    <p class="font-medium text-black">
                                                        {{ number_format($totalAverage, 1) }}</p>
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                        fill="currentColor" class="w-6 h-6 text-yellow-500">
                                                        <path fill-rule="evenodd"
                                                            d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z"
                                                            clip-rule="evenodd"></path>
                                                    </svg>
                                                @else
                                                    <p class="text-sm text-black">
                                                        {{ $rideDetailPage->no_reviews_label ?? ($siteText['no_reviews_label'] ?? 'No Reviews') }}
                                                    </p>
                                                @endif
                                            </div>
                                            @php
                                                $hasVerifiedPhone = App\Models\PhoneNumber::where(
                                                    'user_id',
                                                    $ride->driver?->id,
                                                )
                                                    ->where('verified', 1)
                                                    ->exists();
                                            @endphp

                                            <div class="flex items-center gap-2 w-auto">
                                                @if ($ride->driver?->email_verified == '1')
                                                    <span>|</span>
                                                    <span class="inline-block"
                                                        data-tippy-content="{{ $rideDetailPage->verified_email_tooltip ?? 'Email Verified' }}">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                            class="h-5">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                                                        </svg>
                                                    </span>
                                                @endif

                                                @if ($hasVerifiedPhone)
                                                    <span>|</span>
                                                    <span class="inline-block"
                                                        data-tippy-content="{{ $rideDetailPage->verified_phone_tooltip ?? 'Phone Number Verified' }}">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                            class="h-5 ">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
                                                        </svg>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @if (strtotime($ride->date) < strtotime('today') ||
                                        (strtotime($ride->date) == strtotime('today') && strtotime($ride->time) < strtotime('now')))
                                    @if (auth()->user() &&
                                            $ride->bookings &&
                                            $ride->bookings->where('user_id', auth()->user()->id)->where('status', 1)->isNotEmpty())
                                        @php
                                            // Calculate the difference in days between today and the ride's date
$rideDateTime = new DateTime($ride->date . ' ' . $ride->time);
// Add the leave review days to the ride's DateTime
                                            $reviewDateTime = clone $rideDateTime;
                                            $reviewDateTime->add(
                                                new DateInterval('P' . $setting->leave_review_days . 'D'),
                                            );

                                            // Get current DateTime
                                            $now = new DateTime();

                                            // Check if the current DateTime is before the review DateTime
                                            $reviewButtonVisible = $now < $reviewDateTime;
                                        @endphp
                                        @php
                                            $reviewed = false; // Flag to track if any rating meets the conditions
                                        @endphp
                                        <!-- Loop through ratings associated with this booking -->
                                        @foreach ($ride->ratings as $rating)
                                            @if ($rating->posted_by === auth()->user()->id && $rating->type === '1' && $rating->ride_id === $ride->id)
                                                @php
                                                    $reviewed = true; // Set the flag to true if a matching rating is found
                                                    $review = $rating;
                                                @endphp
                                                <!-- If at least one matching rating is found, break out of the loop -->
                                                @break
                                            @endif
                                        @endforeach

                                        <!-- Display button based on the flag value -->
                                        @if ($reviewed)
                                            @php
                                                // Format average rating with one decimal place
                                                $formattedAverageRating = $review->average_rating ?? 0;
                                            @endphp
                                            <div class="">
                                                <p class="mr-1">
                                                    @isset($rideDetailPage->i_reviewed_label)
                                                        {{ $rideDetailPage->i_reviewed_label }}
                                                    @endisset
                                                </p>
                                                <div class="flex">
                                                    <img src="{{ asset('assets/11-review-full-star.png') }}"
                                                        class="w-4 h-4 mt-1" alt="">
                                                    <p class="ml-1">{{ $formattedAverageRating }}</p>
                                                </div>
                                            </div>
                                        @elseif ($reviewButtonVisible)
                                            @php
                                                $uuid = $ride->bookings
                                                    ->where('user_id', auth()->user()->id)
                                                    ->where('status', 1)
                                                    ->pluck('uuid')
                                                    ->first();
                                            @endphp

                                            @isset($uuid)
                                                <!-- Show 'Review' button if no matching rating is found -->
                                                <a href="{{ route('review_driver', ['lang' => app()->getLocale(), 'id' => $uuid]) }}"
                                                    class="button-exp-fill me-1">
                                                    @isset($rideDetailPage->review_button_label)
                                                        {{ $rideDetailPage->review_button_label }}
                                                    @endisset
                                                </a>
                                            @endisset
                                        @endif
                                    @endif
                                @endif
                            </div>
                        </div>
                        @if (strtotime($ride->date) > strtotime('today') ||
                                (strtotime($ride->date) == strtotime('today') && strtotime($ride->time) > strtotime('now')))
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
                                                <a href="{{ route('chat', ['lang' => app()->getLocale(), 'departure' => $ride->detail->departure ?? 'unknown', 'destination' => $ride->detail->destination ?? 'unknown', 'id' => $ride->id, 'passenger' => $ride->driver->id, 'redirectUrl' => url()->full()]) }}"
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

                        <div
                            class="bg-white rounded-lg shadow-3xl overflow-hidden {{ isset($ride->booking_type->name) && $ride->booking_type->name == 'Firm cancellation' ? 'border-4 border-red-500' : '' }}">
                            <h3 class="bg-primary text-white py-2 px-4 text-2xl xl:text-3xl relative">
                                {{ $rideDetailPage->cancellation_policy ?? 'Cancellation Policy' }}
                            </h3>
                            <div class=" p-4 w-full">
                                <p class="text-left text-md font-semibold">
                                    @php
                                        $route = null;

                                        if ($ride->isStandardCancellation()) {
                                            $route = route('cancellation_policy', [
                                                'lang' => $selectedLanguage->abbreviation,
                                                'type' => 'standard',
                                            ]);
                                            $ride_cancellation_type_label =
                                                $rideFeatureOptions['cancellation']['standard']->name;
                                        } elseif ($ride->isFirmCancellation()) {
                                            $route = route('firm_cancellation_policy', [
                                                'lang' => $selectedLanguage->abbreviation,
                                                'type' => 'firm',
                                            ]);
                                            $ride_cancellation_type_label =
                                                $rideFeatureOptions['cancellation']['firm']->name;
                                        }

                                        $tooltip =
                                            $rideDetailPage->view_cancellation_tooltip ??
                                            'View our full Cancellation Policy';
                                    @endphp

                                    @if ($route)
                                        <a href="{{ $route }}"
                                            class="font-bold text-black no-underline hover:no-underline" target="_blank">
                                            {{ $ride_cancellation_type_label }}
                                        </a>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            fill="currentColor"
                                            class="bi bi-exclamation-circle-fill text-black cursor-help inline-block"
                                            data-tippy-content="{{ $tooltip }}" viewBox="0 0 16 16">
                                            <path
                                                d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 4zm.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2z" />
                                        </svg>
                                    @else
                                        {{ $ride_cancellation_type_label ?? 'N/A' }}
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 w-full justify-center lg:justify-center">
                            @if (auth()->user() && $ride->bookings && $ride->hasNonRejectedBookingForUser(auth()->user()))
                                @php
                                    $userBookingForEdit = $ride->bookings
                                        ->where('status', '<>', 3)
                                        ->where('status', '<>', 4)
                                        ->where('user_id', auth()->user()->id)
                                        ->first();
                                @endphp
                                @if ($userBookingForEdit && $userBookingForEdit->status !== '3')
                                    @if (strtotime($ride->date) > strtotime('today') ||
                                            (strtotime($ride->date) == strtotime('today') && strtotime($ride->time) > strtotime('now')))
                                        @if (
                                            $ride->seats -
                                                $ride->bookings()->where('status', '<>', 3)->where('status', '<>', 4)->whereHas('passenger', function ($query) {
                                                        $query->whereNull('deleted_at');
                                                    })->sum('seats') !=
                                                0)
                                            <div class="flex items-center justify-end">
                                                <a href="{{ route('booking', ['lang' => $selectedLanguage->abbreviation, 'id' => $ride->id, 'from_stop_id' => $from_stop_id, 'to_stop_id' => $to_stop_id]) }}"
                                                    class="button-exp-fill whitespace-nowrap me-1 text-xl">
                                                    @isset($rideDetailPage->edit_button_actions_label)
                                                        {{ $rideDetailPage->edit_button_actions_label }}
                                                    @endisset
                                                </a>
                                            </div>
                                        @endif
                                    @endif
                                @endif
                                @php
                                    $userBooking = $ride->bookings
                                        ->where('status', '<>', 3)
                                        ->where('status', '<>', 4)
                                        ->where('user_id', auth()->user()->id)
                                        ->first();
                                @endphp
                                @if ($userBooking && $userBooking->status !== '3')
                                    @if (strtotime($ride->date) > strtotime('today') ||
                                            (strtotime($ride->date) == strtotime('today') && strtotime($ride->time) > strtotime('now')))
                                        <div class="flex justify-end">
                                            <a @if ($ride->isFirmCancellation()) href="javascript:void(0);" 
                                                onclick="toggleModalCard('card-modal', '{{ $userBooking->id }}', '{{ $selectedLanguage->abbreviation }}')"
                                            @else
                                                href="{{ route('booking.cancel', ['lang' => $selectedLanguage->abbreviation, 'id' => $userBooking->id]) }}" @endif
                                                class="button-exp-fill text-xl">

                                                @if ((string) $userBooking->status === '0' || (int) $userBooking->status === 0)
                                                    {{ $rideDetailPage->cancel_booking_request_btn_label }}
                                                @else
                                                    {{ $rideDetailPage->cancel_booking_btn_label ?? 'Cancel booking' }}
                                                @endif
                                            </a>
                                        </div>
                                    @endif
                                @endif
                            @elseif (
                                $ride->seats -
                                    $ride->bookings()->where('status', '<>', 3)->where('status', '<>', 4)->whereHas('passenger', function ($query) {
                                            $query->whereNull('deleted_at');
                                        })->sum('seats') !=
                                    0)
                                @if ($ride->status !== '2')
                                    <div class="flex justify-end">
                                        @php
                                            $isInstant = $ride->isInstantBooking();

                                            $booking_methods = $rideFeatureOptions['booking_method'];

                                            $icon = $isInstant
                                                ? asset('home_page_icons/' . $booking_methods['instant']->icon)
                                                : asset('home_page_icons/' . $booking_methods['manual']->icon);

                                            $label = $isInstant
                                                ? $rideDetailPage->instant_booking_btn_label ?? 'Instant Book'
                                                : $rideDetailPage->request_booing_btn_label ?? 'Request to Book';

                                            $action = null;

                                            if ($needsPhoneOnFileForRegularRide) {
                                                $action = 'showPhoneOnFileRequiredModal()';
                                            } elseif ($needsVerifiedPhoneForPinkExtra) {
                                                $action = 'showVerifiedPhoneForPinkExtraModal()';
                                            } elseif ($needsPhoneVerification) {
                                                $action = 'showPhoneVerificationModal()';
                                            } elseif ($showPhotoIdRequiredForBooking) {
                                                $action = 'showPhotoIdRequiredModal()';
                                            } elseif (!$isAuthenticated) {
                                                $action = 'togglePopupModal1()';
                                            }

                                        @endphp

                                        @if ($action)
                                            <button type="button" onclick="{{ $action }}"
                                                class="inline-flex items-center justify-center space-x-3 w-fit pr-8 button-exp-fill rounded cursor-pointer">
                                                <img class="w-10 h-10 rounded-full" src="{{ $icon }}"
                                                    alt="">
                                                <span class="font-medium text-xl">{{ $label }}</span>
                                            </button>
                                        @else
                                            <a
                                                href="{{ route('booking', [
                                                    'lang' => $selectedLanguage->abbreviation,
                                                    'id' => $ride->id,
                                                    'from_stop_id' => $from_stop_id,
                                                    'to_stop_id' => $to_stop_id,
                                                ]) }}">
                                                <label
                                                    class="inline-flex items-center justify-center space-x-3 w-fit pr-8 button-exp-fill rounded cursor-pointer hover:border-2 hover:border-blue-500">
                                                    <img class="w-10 h-10 rounded-full" src="{{ $icon }}"
                                                        alt="">
                                                    <span class="font-medium text-xl">{{ $label }}</span>
                                                </label>
                                            </a>
                                        @endif

                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- <div class="mt-4 mb-4 rounded-lg px-6 py-3 bg-blue-100 text-gray-600" role="alert">
            <p class="text-gray-800">
                @isset($rideDetailPage->driver_note_label)
                    {{ $rideDetailPage->driver_note_label }}
                @endisset
                <span class="text-gray-500">{{ $ride->notes }}</span>
            </p>
        </div> --}}
        </div>

        <!-- Phone Verification Required Modal (same as search_ride) -->
        <div id="phoneVerificationModal" class="hidden fixed z-50 inset-0 overflow-y-auto"
            aria-labelledby="phone-verification-modal-title" role="dialog" aria-modal="true">
            <div onclick="closePhoneVerificationModal()"
                class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0">
                    <div class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border"
                        onclick="event.stopPropagation()">
                        <button type="button" onclick="closePhoneVerificationModal()"
                            class="absolute top-4 right-4 text-gray-400 hover:text-gray-500 z-50">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                        <div class="bg-white px-4 mt-10 sm:mt-1 pb-4 pt-16 sm:p-6 sm:pb-4 sm:pt-16">
                            <div class="text-center sm:ml-4 sm:mt-0 sm:text-left">
                                <div class="">
                                    <h3 class="text-center font-FuturaMdCnBT text-gray-800 mb-4"
                                        id="phone-verification-modal-title">
                                        {{ $siteText['phone_verification_required_text'] ?? 'Phone Verification Required' }}
                                    </h3>
                                </div>
                                <div class="mt-2 w-full">
                                    <p class="can-exp-p text-center">
                                        {{ $siteText['phone_verification_description_text'] ?? 'To maintain a safe and reliable community, you must have a verified phone number before booking or posting a ride.' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="px-4 pb-6 pt-4 sm:flex sm:flex-row-reverse sm:px-6 justify-center gap-3">
                            <button type="button" onclick="goToPhoneVerification()"
                                class="inline-flex justify-center rounded bg-primary px-6 py-2 font-FuturaMdCnBT text-lg text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-blue-600">
                                {{ $siteText['verify_my_number_btn_text'] ?? 'Verify My Number' }}
                            </button>
                            <button type="button" onclick="closePhoneVerificationModal()"
                                class="inline-flex justify-center rounded bg-gray-300 px-6 py-2 font-FuturaMdCnBT text-lg text-gray-700 hover:text-gray-800 hover:shadow-lg shadow-sm hover:bg-gray-400">
                                {{ $siteText['close_btn_text'] ?? 'Close' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Verified phone required for Pink/Extra+ Ride Booking -->
        <div id="verifiedPhoneForPinkExtraModal" class="hidden fixed z-50 inset-0 overflow-y-auto"
            aria-labelledby="verified-phone-pink-extra-modal-title" role="dialog" aria-modal="true">
            <div onclick="closeVerifiedPhoneForPinkExtraModal()"
                class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0">
                    <div class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border"
                        onclick="event.stopPropagation()">
                        <button type="button" onclick="closeVerifiedPhoneForPinkExtraModal()"
                            class="absolute top-4 right-4 text-gray-400 hover:text-gray-500 z-50">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                        <div class="bg-white px-4 mt-10 sm:mt-1 pb-4 pt-16 sm:p-6 sm:pb-4 sm:pt-16">
                            <div class="text-center sm:ml-4 sm:mt-0 sm:text-left">
                                <div class="">
                                    <h3 class="text-center font-FuturaMdCnBT text-gray-800 mb-4"
                                        id="verified-phone-pink-extra-modal-title">
                                        {{ $siteText['action_required_label'] ?? 'Action Required' }}</h3>
                                </div>
                                <div class="mt-2 w-full">
                                    <p class="can-exp-p text-center">
                                        {{ $siteText['verified_phone_required_for_pink_extra_text'] ?? 'You must verify your phone number to book Pink or Extra+ Rides. Please do this in Dashboard → My Phone Number.' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="px-4 pb-6 pt-4 sm:flex sm:flex-row-reverse sm:px-6 justify-center gap-3">
                            <button type="button" onclick="goToPhoneVerification()"
                                class="inline-flex justify-center rounded bg-primary px-6 py-2 font-FuturaMdCnBT text-lg text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-blue-600">
                                {{ $siteText['go_to_my_phone_number_btn_text'] ?? 'My Phone Number' }}
                            </button>
                            <button type="button" onclick="closeVerifiedPhoneForPinkExtraModal()"
                                class="inline-flex justify-center rounded bg-gray-300 px-6 py-2 font-FuturaMdCnBT text-lg text-gray-700 hover:text-gray-800 hover:shadow-lg shadow-sm hover:bg-gray-400">
                                {{ $siteText['close_btn_text'] ?? 'Close' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Photo ID Required for Pink/Extra+ Ride Booking -->
        <div id="photoIdRequiredModal" class="hidden fixed z-50 inset-0 overflow-y-auto"
            aria-labelledby="photo-id-required-modal-title" role="dialog" aria-modal="true">
            <div onclick="closePhotoIdRequiredModal()" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity">
            </div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0">
                    <div class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border1"
                        onclick="event.stopPropagation()">
                        <button type="button" onclick="closePhotoIdRequiredModal()"
                            class="absolute top-4 right-4 text-gray-400 hover:text-gray-500 z-50">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                        <div class="bg-white px-4 mt-10 sm:mt-1 pb-4 pt-16 sm:p-6 sm:pb-4 sm:pt-16">
                            <div class="text-center sm:ml-4 sm:mt-0 sm:text-left">
                                <div class="sm:flex sm:items-start justify-center">
                                    <div class="text-3xl text-center font-FuturaMdCnBT text-black">
                                        <svg width="64px" height="64px" viewBox="0 0 24 24" fill="none"
                                            xmlns="http://www.w3.org/2000/svg" stroke="#ff0000">
                                            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round">
                                            </g>
                                            <g id="SVGRepo_iconCarrier">
                                                <path d="M12 10V13" stroke="#db0000" stroke-width="2"
                                                    stroke-linecap="round"></path>
                                                <path d="M12 16V15.9888" stroke="#db0000" stroke-width="2"
                                                    stroke-linecap="round"></path>
                                                <path
                                                    d="M10.2518 5.147L3.6508 17.0287C2.91021 18.3618 3.87415 20 5.39912 20H18.6011C20.126 20 21.09 18.3618 20.3494 17.0287L13.7484 5.147C12.9864 3.77538 11.0138 3.77538 10.2518 5.147Z"
                                                    stroke="#db0000" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round"></path>
                                            </g>
                                        </svg>
                                    </div>
                                </div>
                                <div class="mt-4 w-full">
                                    <p class="can-exp-p text-center">
                                        {{ $postRidePage->alert_need_government_photo_label ?? 'To book a Pink or Extra+ Ride, you must have a government-issued photo ID on file. Please add it in Dashboard → Edit Profile.' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="px-4 pb-6 pt-4 sm:flex sm:flex-row-reverse sm:px-6 justify-center gap-3">
                            <button type="button" onclick="goToEditProfile()"
                                class="inline-flex justify-center rounded bg-primary px-6 py-2 font-FuturaMdCnBT text-lg text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-blue-600">
                                {{ $siteText['edit_profile_btn_text'] ?? 'Edit Profile' }}
                            </button>
                            <button type="button" onclick="closePhotoIdRequiredModal()"
                                class="inline-flex justify-center rounded bg-gray-300 px-6 py-2 font-FuturaMdCnBT text-lg text-gray-700 hover:text-gray-800 hover:shadow-lg shadow-sm hover:bg-gray-400">
                                {{ $siteText['close_btn_text'] ?? 'Close' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="hidden overflow-x-hidden overflow-y-auto fixed inset-0 z-50 outline-none focus:outline-none justify-center items-center"
            id="chat-modal">
            <div class="relative w-auto my-6 mx-auto max-w-2xl">
                <!--content-->
                <div
                    class="relative rounded-lg shadow border-0 flex flex-col w-full bg-white outline-none focus:outline-none">
                    <!--header-->
                    <div class="flex items-center justify-between p-4 border-b rounded-t">
                        <h3 class="can-edu-h3 mb-0">Select website language</h3>
                        <div>
                            <button type="button"
                                class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-full border border-primary text-sm p-1 ml-auto inline-flex items-center"
                                data-modal-hide="defaultModal" onclick="toggleModal('chat-modal')">
                                <svg aria-hidden="true" class="w-5 h-5 text-primary" fill="currentColor"
                                    viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <span class="sr-only">{{ $siteText['close_btn_text'] }}</span>
                            </button>
                        </div>
                    </div>
                    <!-- Modal body -->
                    <div id="ridesharing_app" class="relative z-50" aria-labelledby="modal-title" role="dialog"
                        aria-modal="true">
                        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                            <div
                                class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                                <div
                                    class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-7xl">
                                    <div class="bg-white w-full">
                                        <div
                                            class="flex items-center justify-between border-b pb-2 bg-secondary px-4 py-2">
                                            <h1 class="mb-0 text-white" id="modal-title">
                                                {{ $rideDetailPage->driver_chat_with ?? 'Chat with' }}
                                                {{ $ride->driver?->first_name }}</h1>
                                            <div>
                                                <button type="button"
                                                    class="text-gray-100 bg-transparent rounded-full border border-white text-sm p-1 ml-auto inline-flex items-center"
                                                    data-modal-hide="defaultModal" onclick="toggleModal('chat-modal')">
                                                    <svg aria-hidden="true" class="w-3 h-3 text-gray-100"
                                                        fill="currentColor" viewBox="0 0 20 20"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd"
                                                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                            clip-rule="evenodd"></path>
                                                    </svg>
                                                    <span class="sr-only">{{ $siteText['close_btn_text'] }}</span>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                                            <div class="py-3 text-center sm:text-left">
                                                <div class="mt-2">
                                                    <div class="panel-body">
                                                        {{-- <div style="font-weight:bold;color:#2563eb;margin-bottom:4px;">Ride Details</div> --}}
                                                        <chat-messages logged_in_user_id="{{ Auth::user()->id ?? null }}"
                                                            :messages="chats"
                                                            empty_chat_placeholder="{{ $rideDetailPage->chat_error_message }}"></chat-messages>
                                                    </div>

                                                </div>
                                            </div>

                                            @php
                                                $allow_chat = false;
                                                $currentDateTime = now();
                                                $rideDateTime = \Carbon\Carbon::parse($ride->date . ' ' . $ride->time);
                                                $hoursDifference = $currentDateTime->diffInHours($rideDateTime);
                                                $allow_chat = false;
                                                if (auth()->user()) {
                                                    $user_id = auth()->user()->id;
                                                    $user = \App\Models\User::whereId($user_id)->first();
                                                    $contact_limit = \App\Models\SiteSetting::value(
                                                        'user_per_day_limit',
                                                    );
                                                    $contact_count = \App\Models\UserMessageCount::where(
                                                        'user_id',
                                                        $user->id,
                                                    )
                                                        ->whereBetween('created_at', [
                                                            \Carbon\Carbon::today(),
                                                            \Carbon\Carbon::tomorrow(),
                                                        ])
                                                        ->first();

                                                    if (
                                                        is_null($contact_count) ||
                                                        $contact_count->user_inbox_count < $contact_limit
                                                    ) {
                                                        $allow_chat = true;
                                                    } elseif (
                                                        in_array(
                                                            $ride->driver?->id,
                                                            explode(',', $contact_count->contact_user_id),
                                                        )
                                                    ) {
                                                        $allow_chat = true;
                                                    }
                                                }

                                            @endphp
                                            @if ($rideDateTime < $currentDateTime)
                                                @if ($hoursDifference <= 48)
                                                    <div class="panel-footer">
                                                        <chat-form v-on:message-sent-event="addMessage"
                                                            allow_chat="{{ $allow_chat }}"
                                                            :ride_id="{{ $ride->id }}"
                                                            :user="{{ auth()->user() }}"></chat-form>
                                                    </div>
                                                @endif
                                            @elseif ($rideDateTime >= $currentDateTime)
                                                <div class="panel-footer">
                                                    <chat-form v-on:message-sent-event="addMessage"
                                                        allow_chat="{{ $allow_chat }}" :ride_id="{{ $ride->id }}"
                                                        :user="{{ auth()->user() }}"
                                                        type_message_placeholder="Please avoid sharing any contact details such as phone numbers, email addresses, or website links. Do not offer or agree to communicate or arrange payments outside the ProximaRide platform."></chat-form>
                                                </div>
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
        <div class="hidden opacity-25 fixed inset-0 z-40 bg-black" id="chat-modal-backdrop"></div>
        <div class="hidden overflow-x-hidden overflow-y-auto fixed inset-0 z-50 outline-none focus:outline-none justify-center items-center"
            id="modal-id3">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                    <!--content-->
                    <div
                        class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg w-full">
                        <button type="button" onclick="toggleModal1('modal-id3')"
                            class="absolute top-3 right-3 text-gray-400 hover:text-gray-500">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                        <!--body-->
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <div class="mt-4 text-center">
                                <div class="mt-2 w-full">
                                    <p class="can-exp-p text-center modal-message"></p>
                                </div>
                            </div>
                        </div>
                        <!--footer-->
                        <div class="px-4 pb-6 pt-4  sm:flex sm:flex-row-reverse sm:px-6 justify-center">
                            <button
                                class="inline-flex w-full justify-center rounded bg-red-500 px-3 py-2 text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-red-400 sm:ml-3 sm:w-24"
                                type="button" onclick="toggleModal1('modal-id3')">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="hidden overflow-x-hidden overflow-y-auto fixed inset-0 z-50 outline-none focus:outline-none justify-center items-center"
            id="card-modal">
            <div class="relative h-screen my-6 mx-auto flex items-center justify-center w-full">
                <!--content-->
                <div
                    class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                    <button type="button" onclick="toggleModalCard('card-modal')"
                        class="absolute top-3 right-3 text-gray-400 hover:text-gray-500">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </button>
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start justify-center">
                            <!-- <div
                                                class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full sm:mx-0 sm:h-10 sm:w-10 bg-red-500">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                                    class="bi bi-exclamation-lg text-white w-8 h-8" viewBox="0 0 16 16">
                                                    <path
                                                        d="M7.005 3.1a1 1 0 1 1 1.99 0l-.388 6.35a.61.61 0 0 1-1.214 0zM7 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0" />
                                                </svg>
                                            </div> -->
                        </div>
                        <div class="text-center sm:ml-4 sm:mt-0 sm:text-left">
                            <div class="">
                                <h3 class="text-3xl text-center font-FuturaMdCnBT font-medium text-gray-900 mb-4"
                                    id="modal-title">
                                    {{ $rideDetailPage->firm_cancellation_confirm_poup_heading ?? ($siteText['heading_text'] ?? 'Heading') }}
                                </h3>
                            </div>
                            <div class="mt-2 w-full">
                                <p class="can-exp-p text-center">
                                    {{ $rideDetailPage->firm_cancellation_confirm_poup_text ?? 'This ride has the Firm cancellation policy. While you can cancel your booking, you will not get any refunds. Are you sure you want to cancel? This is irreversible' }}
                                    <br>
                                    {{ $rideDetailPage->firm_cancellation_confirm_poup_sub_text ?? '' }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="px-4 pb-6 pt-4 flex items-center space-x-2 sm:space-x-4 sm:px-6 justify-center">
                        <a id="delete-card-link" href="#"
                            class="inline-flex justify-center rounded bg-red-500 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white whitespace-nowrap hover:text-white hover:shadow-lg shadow-sm hover:bg-red-400 sm:ml-3 w-auto">{{ $rideDetailPage->firm_cancellation_confirm_poup_yes_label ?? ($siteText['yes_btn_text'] ?? 'Yes') }}</a>
                        <button type="button" onclick="toggleModalCard('card-modal')"
                            class="button-exp-fill sm:w-42">{{ $rideDetailPage->firm_cancellation_confirm_poup_no_label ?? ($siteText['no_btn_text'] ?? 'No') }}</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="hidden opacity-25 fixed inset-0 z-40 bg-black" id="card-modal-backdrop"></div>
    </div>
@endsection

@section('script')
    <script>
        function closeModal() {
            document.getElementById('my-modal').style.display = 'none';
        }

        function closeVerifyModal() {
            document.getElementById('verified_email_phone').style.display = 'none';
        }

        function openVerifyModal(value) {
            document.getElementById('verified_email_phone').style.display = 'block';
            document.getElementById('verify-popup-text').innerText = value;

        }

        function closePopupModal() {
            document.getElementById('my-chat-pop-modal').style.display = 'none';
        }

        function showPhoneVerificationModal() {
            var modal = document.getElementById('phoneVerificationModal');
            if (modal) modal.classList.remove('hidden');
        }

        function closePhoneVerificationModal() {
            var modal = document.getElementById('phoneVerificationModal');
            if (modal) modal.classList.add('hidden');
        }

        function goToPhoneVerification() {
            window.location.href = '{{ route('phone', ['lang' => optional($selectedLanguage)->abbreviation ?? 'en']) }}';
        }

        function showVerifiedPhoneForPinkExtraModal() {
            var modal = document.getElementById('verifiedPhoneForPinkExtraModal');
            if (modal) modal.classList.remove('hidden');
        }

        function closeVerifiedPhoneForPinkExtraModal() {
            var modal = document.getElementById('verifiedPhoneForPinkExtraModal');
            if (modal) modal.classList.add('hidden');
        }

        function showPhoneOnFileRequiredModal() {
            var modal = document.getElementById('phoneOnFileRequiredModal');
            if (modal) modal.classList.remove('hidden');
        }

        function closePhoneOnFileRequiredModal() {
            var modal = document.getElementById('phoneOnFileRequiredModal');
            if (modal) modal.classList.add('hidden');
        }

        function goToPhoneNumberSettings() {
            window.location.href = '{{ route('phone', ['lang' => optional($selectedLanguage)->abbreviation ?? 'en']) }}';
        }

        function showPhotoIdRequiredModal() {
            var modal = document.getElementById('photoIdRequiredModal');
            if (modal) modal.classList.remove('hidden');
        }

        function closePhotoIdRequiredModal() {
            var modal = document.getElementById('photoIdRequiredModal');
            if (modal) modal.classList.add('hidden');
        }

        function goToEditProfile() {
            window.location.href =
                '{{ route('profile.edit', ['lang' => optional($selectedLanguage)->abbreviation ?? 'en']) }}';
        }

        function toggleModal1(modalID, message) {
            var modalElement = document.getElementById(modalID);
            var messageElement = modalElement.querySelector(".modal-message");

            // Set the message
            messageElement.innerText = message;

            // Toggle visibility
            modalElement.classList.toggle("hidden");
            modalElement.classList.toggle("flex");
        }

        function togglePopupModal1() {
            document.getElementById('my-chat-pop-modal').style.display = 'flex';

        }
    </script>


    <script>
        function toggleModalCard(modalId, cardId = null, lang = null) {
            let modal = document.getElementById(modalId);
            let backdrop = document.getElementById(modalId + "-backdrop");
            console.log('cardId', cardId);
            if (modal.classList.contains("hidden")) {
                modal.classList.remove("hidden");
                backdrop.classList.remove("hidden");

                // Update the delete link if cardId is provided
                if (cardId) {
                    let deleteLink = document.getElementById("delete-card-link");
                    var url = "{{ route('booking.cancel', ['lang' => ':lang', 'id' => ':bookingId']) }}";

                    url = url.replace(":lang", lang);
                    url = url.replace(":bookingId", cardId);

                    deleteLink.href = url;
                }
            } else {
                modal.classList.add("hidden");
                backdrop.classList.add("hidden");
            }
        }
    </script>
@endsection
