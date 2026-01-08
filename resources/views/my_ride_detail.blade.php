@extends('layouts.template')

@section('content')

    @if (session('message'))
        <div class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="relative flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full w-full">
                    <div
                        class="relative animate__animated animate__fadeIn z-20 transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                        <button onclick="closeModal('message-modal')"
                            class="absolute top-2 right-2 p-1 rounded-full hover:bg-gray-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start justify-center">
                                <!-- <div
                                class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full sm:mx-0 sm:h-10 sm:w-10 bg-red-500">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-lg text-white w-8 h-8" viewBox="0 0 16 16">
                                    <path d="M7.005 3.1a1 1 0 1 1 1.99 0l-.388 6.35a.61.61 0 0 1-1.214 0zM7 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0"/>
                                </svg>
                            </div> -->
                            </div>
                            <div class="text-center w-full">
                                <p class="can-exp-p text-center">{!! session('message') !!}</p>
                            </div>
                        </div>
                        <div class="px-4 pb-6 pt-4  sm:flex sm:flex-row-reverse sm:px-6 justify-center">
                            <a href=""
                                class="inline-flex w-full justify-center rounded bg-red-500 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-red-400 sm:ml-3 sm:w-24">{{ $messages->popup_close_btn_text ?? 'Close' }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if (session('success'))
        <div id="my-modal" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="relative flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeModal()"></div>
                    <div
                        class="relative animate__animated animate__fadeIn z-20 transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                         <button onclick="closeModal('success-modal')" class="absolute top-2 right-2 p-1 rounded-full hover:bg-gray-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start justify-center">
                                <!-- <div
                                class="mx-auto h-16 w-16">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="4" stroke="currentColor" class="w-12 h-12 text-greenXS">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                            </div> -->
                            </div>
                            <div class="w-full">
                                <p class="can-exp-p text-center">{!! session('success') !!}</p>
                            </div>
                        </div>
                        <div class="px-4 pb-6 pt-4  sm:flex sm:flex-row-reverse sm:px-6 justify-center">
                            <a href=""
                                class="inline-flex w-full justify-center rounded bg-greenXS px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-greenXS sm:ml-3 sm:w-24">{{ $messages->popup_close_btn_text ?? 'Close' }}</a>
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
                        class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg w-full">
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
                                class="whitespace-nowrap inline-flex w-full justify-center rounded bg-red-500 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-red-400 sm:ml-3 sm:w-24">Close</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <div class="container mx-auto my-10 xl:my-14 px-4 xl:px-0">
        @if (
            $ride->seats -
                $ride->bookings()->where('status', '<>', 3)->where('status', '<>', 4)->whereHas('passenger', function ($query) {
                        $query->whereNull('deleted_at');
                    })->sum('seats') ==
                0)
            <div class="mt-4 rounded-lg px-6 py-3 bg-blue-100 text-gray-600" role="alert">
                {{ $rideDetailPage->all_seats_booked_label ?? 'All seats are booked' }}
            </div>
        @endif
        @if ($ride->status == '2')
            <div class="mt-4 rounded-lg px-6 py-3 bg-red-100 text-gray-600" role="alert">
                {{ $rideDetailPage->ride_canceller_by_driver ?? 'This ride was cancelled by the driver' }}
            </div>
        @endif
        
        @if ($ride->status == '3')
            <div class="mt-4 rounded-lg px-6 py-3 bg-blue-100 text-gray-600" role="alert">
                {{ $rideDetailPage->ride_completed_text ?? 'This ride was completed' }}
            </div>
        @endif
        <h1>{{ $rideDetailPage->main_heading ?? 'My ride detail' }}</h1>
        <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-y-4 md:gap-4">
            <div class="col-span-2">
                @if (strtotime($ride->date) > strtotime('today') || (strtotime($ride->date) == strtotime('today') && strtotime($ride->time) > strtotime('now')))
                        @if (count($ride->bookings->where('status', 0)) > 0)
                        <div class=" bg-white rounded-lg overflow-hidden shadow-3xl mb-6">
                            <h3 class="bg-primary text-white py-2 px-4 text-2xl xl:text-3xl">
                                {{ $rideDetailPage->booking_request_main_heading ?? 'You have the following booking requests' }}
                            </h3>
                            <div class="bg-white p-2 py-4 md:p-4 space-y-3">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @foreach ($ride->bookings->where('status', 0) as $booking)
                                        @if ($booking->passenger)
                                            <div class="border rounded">
                                                <div class="border-b border-slate-300 px-4 py-2 font-FuturaMdCnBT text-2xl">
                                                    {{ $rideDetailPage->booking_request_heading ?? 'Booking request' }}</div>
                                                <div class="p-4">
                                                    <div class="flex items-center">
                                                        <img class="w-12 2xl:w-16 h-12 2xl:h-16 rounded-full object-cover mr-2 2xl:mr-3"
                                                            src="{{ $booking->passenger->profile_image }}" alt="">
                                                        <div>
                                                            <p class="mb-0 font-medium">{{ $booking->passenger->first_name }}
                                                                {{ $booking->passenger->last_name }}</p>
                                                            <div class="flex items-center 2xl:space-x-1">
                                                                @for ($i = 1; $i <= $booking->seats; $i++)
                                                                    <div
                                                                        class="relative w-8 xl:w-9 2xl:w-10 h-8 xl:h-9 2xl:h-10 mt-0.5">
                                                                        <img src="{{ asset('assets/seat-hover-1.png') }}"
                                                                            class="w-8 xl:w-9 2xl:w-10 h-8 xl:h-9 2xl:h-10 mt-0.5 cursor-pointer"
                                                                            alt="">
                                                                        <span
                                                                            class="absolute left-3 xl:left-3.5 2xl:left-4 top-3 xl:top-3.5 2xl:top-3 text-sm 2xl:text-base text-green-300">{{ $i }}</span>
                                                                    </div>
                                                                @endfor
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="flex items-center justify-center mt-4 gap-2">
                                                        <a href="{{ route('reject_booking_request', ['lang' => $selectedLanguage->abbreviation, 'id' => $booking->id, 'email' => auth()->user()->email]) }}"
                                                            class="button-exp-fill w-36">
                                                            {{ $rideDetailPage->request_reject_label ?? 'Reject' }}
                                                        </a>
                                                        <a href="{{ route('accept_booking_request', ['lang' => $selectedLanguage->abbreviation, 'id' => $booking->id, 'email' => auth()->user()->email]) }}"
                                                            class="bg-greenXS hover:bg-greenXS button-exp-fill w-36">
                                                            {{ $rideDetailPage->request_accept_label ?? 'Accept' }}
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                @endif
                
                @if (strtotime($ride->date) > strtotime('today') ||
                        (strtotime($ride->date) == strtotime('today') && strtotime($ride->time) > strtotime('now')))
                    @if (count($ride->bookings->where('status', 1)->where('secured_cash', 1)) > 0)
                        <div class="bg-white rounded-lg overflow-hidden shadow-3xl mb-6">
                            <h3 class="bg-primary text-white py-2 px-4 text-2xl xl:text-3xl">
                                {{ $rideDetailPage->secured_cash_heading ?? 'Secured-cash payments' }}</h3>
                            <div class="space-y-4 p-4">
                                @foreach ($ride->bookings->where('status', 1)->where('secured_cash', 1) as $booking)
                                    @php
                                        $dateTimeString = '' . $ride->date . ' ' . $ride->time . '';
                                        $targetTimestamp = strtotime($dateTimeString);
                                        $currentTimestamp = time();
                                        $diffInSeconds = $targetTimestamp - $currentTimestamp;
                                        $diffInMinutes = $diffInSeconds / 60;
                                    @endphp

                                    @if ($booking->passenger)
                                        <div class="flex items-center justify-between w-full no-scrollbar overflow-x-auto">
                                            <div class="flex items-center space-x-2">
                                                <div class="w-12 h-12 rounded-full">
                                                    <img class="w-full h-full rounded-full object-cover"
                                                        src="{{ $booking->passenger->profile_image }}" alt="">
                                                </div>
                                                <div
                                                    class="text-center flex flex-row md:flex-col items-center md:items-start space-x-2 md:space-x-0">
                                                    <p class="font-semibold leading-4 text-base mb-0 whitespace-nowrap">
                                                        {{ $booking->passenger->first_name }}</p>
                                                    <div class="flex items-center space-x-2">
                                                        @php
                                                            // Calculate the age based on the driver's date of birth
                                                            $dob = \Carbon\Carbon::parse($booking->passenger->dob);
                                                            $age = $dob->diffInYears(\Carbon\Carbon::now());
                                                        @endphp
                                                        <p
                                                            class="text-gray-700 leading-4 md:mt-2 text-base whitespace-nowrap">
                                                            {{ $rideDetailPage->driver_age_label ?? 'Age' }}:
                                                            <span>{{ $age }}</span></p>
                                                        <p
                                                            class="text-gray-700 leading-4 md:mt-2 text-base whitespace-nowrap">
                                                            {{ $rideDetailPage->web_gender_label ?? 'Gender' }}:
                                                            <span>{{ $booking->passenger->gender }}</span></p>
                                                        @php
                                                            $user_id = $booking->passenger->id;

                                                            // Assuming $ratings is a collection
                                                            $filteredRatings = $ratings
                                                                ->where('status', 1)
                                                                ->where('type', '2')
                                                                ->filter(function ($rating) use ($user_id) {
                                                                    return $rating->booking->user_id === $user_id;
                                                                });

                                                            $totalAverage =
                                                                $filteredRatings->avg('average_rating') ?? 0;
                                                        @endphp
                                                        <p
                                                            class="text-gray-700 leading-4 md:mt-2 text-base whitespace-nowrap">
                                                            {{ $rideDetailPage->review_label ?? 'Review' }}:
                                                            <span>{{ number_format($totalAverage, 1) }}</span></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="ml-20 md:ml-0">
                                                @if ($diffInMinutes <= 30)
                                                    <button
                                                        class="bg-greenXS hover:bg-greenXS text-white text-base md:text-lg rounded font-FuturaMdCnBT hover:font-FuturaMdCnBT px-5 py-2 border border-greenXS hover:border-greenXS hover:text-white text-center focus:bg-greenXS focus:text-white active:text-white active:bg-greenXS send_code"
                                                        data-booking-id="{{ $booking->id }}" data-booking-secured-cash-attempt="{{ $booking->secured_cash_attempt_count }}" data-setting-secured-cash-attempt="{{ $siteSetting->secured_cash_attempt }}">
                                                        {{ $rideDetailPage->enter_code_label ?? 'Enter payment code' }}
                                                    </button>
                                                @endif

                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endif
                <div class="bg-white rounded-lg shadow-3xl">
                    <div class="flex flex-col md:flex-row justify-between px-4">
                        <div class="w-full md:w-2/3 order-2 md:order-1">
                            @foreach ($ride->rideDetail as $detail)
                                @if ($detail->departure && $detail->destination)
                                    <div class="relative mt-5 text-left">
                                        <div class="flex items-center relative">
                                            <div
                                                class="border-r-2 border-black border-solid absolute h-full left-3 md:left-[26px] top-2 z-10">
                                                <span
                                                    class="bg-primary rounded-full w-7 h-7 -top-[2px] -ml-[13px] absolute flex justify-center items-center">
                                                    <img class="w-5 h-5 object-contain"
                                                        src="{{ asset('./images/new-21-search-bar-from.png') }}" alt="">
                                                </span>
                                            </div>
                                            <div class="ml-12 md:ml-20">
                                                <div class="font-bold text-xl text-black">
                                                    {{ $rideDetailPage->from_label ?? 'From' }}</div>
                                                <div class="text-primary md:mb-4">{{ $detail->departure }}, <br
                                                        class="md:hidden"> {{ $ride->pickup }}</div>
                                            </div>
                                        </div>
                                        <div class="flex items-center relative">
                                            <div
                                                class="border-r-2 border-black border-solid absolute h-0 left-3 md:left-5 top-2 z-10">
                                                <span
                                                    class="bg-gray-200 rounded-full w-7 h-7 -top-[6px] -ml-[12px] md:-ml-[8px] absolute flex justify-center items-center">
                                                    <img class="w-5 h-5 object-contain"
                                                        src="{{ asset('./images/new-21-search-bar-to.png') }}" alt="">
                                                </span>
                                            </div>
                                            <div class="ml-12 md:ml-20">
                                                <div class="font-bold text-xl text-black">{{ $rideDetailPage->to_label ?? 'To' }}
                                                </div>
                                                <div class="text-primary md:mb-4">{{ $detail->destination }}, <br
                                                        class="md:hidden"> {{ $ride->dropoff }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                        <div class="mt-4 order-1 md:order-2">
                            <p class="whitespace-nowrap font-semibold">
                                {{ \Carbon\Carbon::parse($ride->date)->format('F d, Y') }} at
                                {{ \Carbon\Carbon::parse($ride->time)->format('h:i A') == '12:00 PM' ? '12 noon' : (\Carbon\Carbon::parse($ride->time)->format('h:i A') == '12:00 AM' ? '12 midnight' : \Carbon\Carbon::parse($ride->time)->format('h:i A')) }}
                            </p>
                        </div>
                    </div>
                    <div class="border-t border-gray-300 grid grid-cols-2 divide-x divide-gray-300">
                        <div class="p-4">
                            <p class="text-left font-semibold">
                                {{ intval($ride->seats) -intval($ride->bookings()->where('status', '<>', 3)->where('status', '<>', 4)->whereHas('passenger', function ($query) {$query->whereNull('deleted_at');})->sum('seats')) }}
                                {{ $rideDetailPage->seats_left_label ?? 'seats left' }}
                            </p>
                        </div>
                        <div class="p-4">
                            <p class="font-semibold text-left text-primary">${{ $ride->rideDetail[0]->price }}
                                {{ $rideDetailPage->per_seat_label ?? 'per seat' }}</p>
                        </div>
                    </div>
                    <div
                        class="border-t border-gray-300 grid sm:grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-gray-300">
                        <div class="p-4">
                            <p class="font-medium text-left text-black">
                                {{ $rideDetailPage->payment_method_label ?? 'Payment method' }}: <span
                                    class="text-black font-normal">{{ $ride->payment_method }}</span></p>
                        </div>
                        <div class="p-4">
                            {{-- <p class="font-medium text-left text-black">Luggage: <span class="text-black font-normal">{{ $ride->luggage }}</span></p> --}}
                            <button
                                class="bg-greenXS hover:bg-greenXS text-white text-base md:text-lg rounded font-FuturaMdCnBT hover:font-FuturaMdCnBT px-5 py-2 border border-greenXS hover:border-greenXS hover:text-white text-center focus:bg-greenXS focus:text-white active:text-white active:bg-greenXS">{{ $ride->booking_method }}</button>
                        </div>
                    </div>
                    <div
                        class="border-t border-gray-300 grid sm:grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-gray-300">
                        <div class="p-4">
                            <p class="font-medium text-left text-black">
                                {{ $rideDetailPage->booked_on_column_label ?? 'Booked' }}: <span
                                    class="text-black font-normal">{{ $ride->bookings()->where('status', '<>', 3)->where('status', '<>', 4)->whereHas('passenger', function ($query) {$query->whereNull('deleted_at');})->sum('seats') }}
                                    {{ $rideDetailPage->seat_on_column_label ?? 'seats' }}</span></p>
                        </div>
                        <div class="p-4">
                        <div class="flex items-center justify-between">
                                <p class="font-semibold">{{ $rideDetailPage->mobile_seat_fare_label ?? 'Fare' }}: </p>
                                <p class="">
                                    ${{ number_format(floatval($ride->bookings()->where('status', '<>', 3)->where('status', '<>', 4)->whereHas('passenger', function ($query) {$query->whereNull('deleted_at');})->sum('seats') * floatval($ride->rideDetail[0]->price)),2) }}
                                </p>
                            </div>
                            <div class="flex items-center justify-between">
                                <p class="font-semibold">
                                    {{ $rideDetailPage->mobile_seat_booking_fee_label ?? 'Booking fee' }}: </p>
                                <p class="">
                                    ${{ number_format(floatval($ride->bookings->where('status', '<>', 3)->where('status', '<>', 4)->sum('booking_credit')), 2) }}
                                </p>
                            </div>
                            <div class="flex items-center justify-between">
                                <p class="font-semibold">
                                    {{ $rideDetailPage->mobile_seat_total_amount_label ?? 'Total amount' }}: </p>
                                <p class="">
                                    ${{ number_format(floatval($ride->bookings()->where('status', '<>', 3)->where('status', '<>', 4)->whereHas('passenger', function ($query) {$query->whereNull('deleted_at');})->sum('seats') *floatval($ride->rideDetail[0]->price) +$ride->bookings->where('status', '<>', 3)->where('status', '<>', 4)->sum('booking_credit')),2) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg overflow-hidden shadow-3xl mt-4">
                    <h3 class="bg-primary text-white py-2 px-4 text-2xl xl:text-3xl">
                        {{ $rideDetailPage->ride_features_label ?? 'Ride features' }}</h3>
                    <div class="bg-white p-4 space-y-3">
                        <div class="flex items-center space-x-2">
                            @if ($ride->smoke == $postRidePage->smoking_option1->features_setting_id)
                                @isset($postRidePage->smoking_option1->icon)
                                    <img class="w-7 h-7"
                                        src="{{ asset('home_page_icons/' . $postRidePage->smoking_option1->icon) }}"
                                        alt="">
                                @endisset
                                <p>{{ $rideDetailPage->smoking_label }} {{ $postRidePage->smoking_option1->name }}</p>
                            @elseif ($ride->smoke == $postRidePage->smoking_option2->features_setting_id)
                                @isset($postRidePage->smoking_option2->icon)
                                    <img class="w-7 h-7"
                                        src="{{ asset('home_page_icons/' . $postRidePage->smoking_option2->icon) }}"
                                        alt="">
                                @endisset
                                <p>{{ $rideDetailPage->smoking_label }} {{ $postRidePage->smoking_option2->name }}</p>
                            @endif
                        </div>
                        @isset($ride->animal_friendly->features_setting_id)
                            <div class="flex items-center space-x-2">
                                <img class="w-7 h-7"
                                    @if ($ride->animal_friendly->features_setting_id === $postRidePage->animals_option1->features_setting_id) src="{{ asset('home_page_icons/' . $postRidePage->animals_option1->icon) }}"
                                @elseif ($ride->animal_friendly->features_setting_id === $postRidePage->animals_option2->features_setting_id) src="{{ asset('home_page_icons/' . $postRidePage->animals_option2->icon) }}"
                                @elseif ($ride->animal_friendly->features_setting_id === $postRidePage->animals_option3->features_setting_id)
                                    src="{{ asset('home_page_icons/' . $postRidePage->animals_option3->icon) }}" @endif
                                    alt="">
                                <p>{{ $rideDetailPage->pets_label }} {{ $ride->animal_friendly->name }}</p>
                            </div>
                        @endisset
                        @isset($ride->luggage->features_setting_id)
                            <div class="flex items-center space-x-2">
                                <img class="w-7 h-7"
                                    src="{{ asset('home_page_icons/' . $ride->luggage->icon) }}"
                                    alt="">
                                <p>{{ $rideDetailPage->luggage_label }} {{ $ride->luggage->name }}</p>
                            </div>
                        @endisset
                        @php
                            $features = !empty($ride->features) ? explode('=', $ride->features) : [];
                        @endphp
                        @foreach ($features as $feature)
                            <div class="flex items-start space-x-2">
                                @if ($feature === $postRidePage->features_option11->name)
                                    <img class="w-7 h-7"
                                        src="{{ asset('home_page_icons/' . $postRidePage->features_option11->icon) }}"
                                        alt="">
                                @elseif ($feature === $postRidePage->features_option1->name)
                                    <img class="w-7 h-7"
                                        src="{{ asset('home_page_icons/' . $postRidePage->features_option1->icon) }}"
                                        alt="">
                                @elseif ($feature === $postRidePage->features_option2->name)
                                    <img class="w-7 h-7"
                                        src="{{ asset('home_page_icons/' . $postRidePage->features_option2->icon) }}"
                                        alt="">
                                @elseif ($feature === $postRidePage->features_option9->name)
                                    <img class="w-7 h-7"
                                        src="{{ asset('home_page_icons/' . $postRidePage->features_option9->icon) }}"
                                        alt="">
                                @elseif ($feature === $postRidePage->features_option8->name)
                                    <img class="w-7 h-7"
                                        src="{{ asset('home_page_icons/' . $postRidePage->features_option8->icon) }}"
                                        alt="">
                                @elseif ($feature === $postRidePage->features_option10->name)
                                    <img class="w-7 h-7"
                                        src="{{ asset('home_page_icons/' . $postRidePage->features_option10->icon) }}"
                                        alt="">
                                @elseif ($feature === $postRidePage->features_option3->name)
                                    <img class="w-7 h-7"
                                        src="{{ asset('home_page_icons/' . $postRidePage->features_option3->icon) }}"
                                        alt="">
                                @elseif ($feature === $postRidePage->features_option12->name)
                                    <img class="w-7 h-7"
                                        src="{{ asset('home_page_icons/' . $postRidePage->features_option12->icon) }}"
                                        alt="">
                                @elseif ($feature === $postRidePage->features_option4->name)
                                    <img class="w-7 h-7"
                                        src="{{ asset('home_page_icons/' . $postRidePage->features_option4->icon) }}"
                                        alt="">
                                @elseif ($feature === $postRidePage->features_option5->name)
                                    <img class="w-7 h-7"
                                        src="{{ asset('home_page_icons/' . $postRidePage->features_option5->icon) }}"
                                        alt="">
                                @elseif ($feature === $postRidePage->features_option6->name)
                                    <img class="w-7 h-7"
                                        src="{{ asset('home_page_icons/' . $postRidePage->features_option6->icon) }}"
                                        alt="">
                                @elseif ($feature === $postRidePage->features_option7->name)
                                    <img class="w-7 h-7"
                                        src="{{ asset('home_page_icons/' . $postRidePage->features_option7->icon) }}"
                                        alt="">
                                @elseif ($feature === $postRidePage->features_option13->name)
                                    <img class="w-7 h-7"
                                        src="{{ asset('home_page_icons/' . $postRidePage->features_option13->icon) }}"
                                        alt="">
                                @elseif ($feature === $postRidePage->features_option14->name)
                                    <img class="w-7 h-7"
                                        src="{{ asset('home_page_icons/' . $postRidePage->features_option14->icon) }}"
                                        alt="">
                                @elseif ($feature === $postRidePage->features_option15->name)
                                    <img class="w-7 h-7"
                                        src="{{ asset('home_page_icons/' . $postRidePage->features_option15->icon) }}"
                                        alt="">
                                @elseif ($feature === $postRidePage->features_option16->name)
                                    <img class="w-7 h-7"
                                        src="{{ asset('home_page_icons/' . $postRidePage->features_option16->icon) }}"
                                        alt="">
                                @else
                                    <input id="wi-fi" type="checkbox" name="features[]" value="" checked
                                        disabled
                                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                @endif
                                <p>{{ $feature }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
                {{-- <div class="mt-4 mb-4 rounded-lg px-6 py-3 bg-blue-100 text-gray-600" role="alert">
                <p class="text-gray-800">Important note from the driver: <span class="text-gray-500">{{ $ride->notes }}</span></p>
            </div> --}}
            </div>
            <div class="col-span-1">
                <div class="space-y-4">
                    @if (count($ride->bookings->where('status', 1)) > 0)
                        <div class="bg-white rounded-lg overflow-hidden shadow-3xl">
                            <h3 class="bg-primary text-white py-2 px-4 text-2xl xl:text-3xl">
                                @if ($ride_cancelled)
                                    
                                    {{ $rideDetailPage->review_passanger_label ?? 'Review my passengers' }}
                                @else
                                    {{ $rideDetailPage->ride_co_passenger_heading ?? 'My passengers' }}
                                @endif
                            </h3>
                            <a
                                href="{{ route('my_passengers', ['lang' => $selectedLanguage->abbreviation, 'departure' => $ride->rideDetail[0]->departure, 'destination' => $ride->rideDetail[0]->destination, 'id' => $ride->id]) }}">
                                <div class="space-y-4 p-4">
                                    @foreach ($ride->bookings->where('status', 1) as $booking)
                                        @if ($booking->passenger)
                                            <div class="flex items-center space-x-2 w-full no-scrollbar overflow-x-auto">
                                                <div class="w-12 h-12 rounded-full flex-shrink-0">
                                                    {{-- <img class="w-full h-full rounded-full object-cover"
                                                        src="{{ $booking->passenger->profile_image }}" alt=""> --}}
                                                        
                                                    {{-- @if (count($ride->bookings->where('status', '<>', 3)->where('status', '<>', 4)) > 0)
                                                        <a href="{{ route('my_passengers', ['lang' => $selectedLanguage->abbreviation, 'departure' => $ride->rideDetail[0]->departure, 'destination' => $ride->rideDetail[0]->destination, 'id' => $ride->id]) }}">Review passenger</a>
                                                    @endif --}}
                                                    @if (auth()->user())
                                                        @php
                                                            $uuid = $booking
                                                                ->where('user_id', auth()->user()->id)
                                                                // ->where('status', 3)
                                                                ->pluck('uuid')
                                                                ->first();
                                                        @endphp
                                                    @endif
                                                    @isset($uuid)
                                                        {{-- {{ dd($booking->passenger->profile_image) }} --}}
                                                        @if($ride->status == '3')
                                                            <a href="{{ route('review_passenger', ['lang' => $selectedLanguage->abbreviation, 'id' => $uuid]) }}">
                                                                @isset($booking->passenger->profile_image)
                                                                    <img class="w-full h-full object-cover"
                                                                        src="{{ $booking->passenger->profile_image }}" alt="">
                                                                @endisset
                                                            </a>
                                                        @else
                                                            <img class="w-full h-full object-cover"
                                                                src="{{ $booking->passenger->profile_image }}" alt="">
                                                        @endif    
                                                    @endisset
                                                </div>
                                                <div
                                                    class="text-center flex-auto flex flex-row md:flex-col items-center md:items-start space-x-2 md:space-x-0">
                                                    <p class="font-semibold leading-4 text-base mb-0 whitespace-nowrap">
                                                        {{ $booking->passenger->first_name }} </p>
                                                    <div class="flex items-center space-x-2">
                                                        @php
                                                            // Calculate the age based on the driver's date of birth
                                                            $dob = \Carbon\Carbon::parse($booking->passenger->dob);
                                                            $age = $dob->diffInYears(\Carbon\Carbon::now());
                                                        @endphp
                                                        <p
                                                            class="text-gray-700 leading-4 md:mt-2 text-base whitespace-nowrap">
                                                            {{ $rideDetailPage->passenger_age_label ?? 'Age' }}:
                                                            <span>{{ $age }}</span></p>
                                                        <p
                                                            class="text-gray-700 leading-4 md:mt-2 text-base whitespace-nowrap">
                                                            {{ $rideDetailPage->passenger_gender_label ?? 'Gender' }}:
                                                            <span>{{ $booking->passenger->gender }}</span></p>
                                                        @php
                                                            $user_id = $booking->passenger->id;

                                                            // Assuming $ratings is a collection
                                                            $filteredRatings = $ratings
                                                                ->where('status', 1)
                                                                ->where('type', '2')
                                                                ->filter(function ($rating) use ($user_id) {
                                                                    return $rating->booking->user_id === $user_id;
                                                                });

                                                            $totalAverage =
                                                                $filteredRatings->avg('average_rating') ?? 0;
                                                        @endphp
                                                        <p
                                                            class="text-gray-700 leading-4 md:mt-2 text-base whitespace-nowrap">
                                                            {{ $rideDetailPage->review_label ?? 'Review' }}:
                                                            <span>{{ number_format($totalAverage, 1) }}</span></p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </a>
                        </div>
                    @endif
                    <div class="bg-white rounded-lg overflow-hidden shadow-3xl">
                        <h3 class="bg-primary text-white py-2 px-4 text-2xl xl:text-3xl">
                            {{ $rideDetailPage->vehicle_info_label ?? 'Vehicle info' }}</h3>
                        <div class="flex items-start space-x-2 p-4 w-full">
                            <div class="w-20 h-20 rounded-full overflow-hidden">
                                <img class="w-full h-full object-cover rounded-full" src="{{ $ride->car_image }}"
                                    alt="">
                            </div>
                            <div class="text-center">
                                <div class="flex items-center flex-wrap gap-x-2 text-sm text-black">
                                    @if ($ride->year)
                                        <p class="text-sm">{{ $ride->year }}</p>
                                    @endif
                                    <span>/</span>
                                    <p class="text-sm">{{ $ride->model }}</p>
                                    <span>/</span>
                                    @if ($ride->color)
                                        <p class="text-sm">{{ $ride->color }}</p>
                                    @endif
                                </div>
                                <p class="font-semibold text-lg text-black text-start">{{ $ride->license_no }}</p>
                                @if ($ride->vehicle_type)
                                    <p class="text-sm">{{ $ride->vehicle_type }} </p>
                                @endif
                            </div>
                        </div>
                    </div>
                    {{-- <div class="bg-white rounded-lg overflow-hidden shadow-3xl">
                    <h3 class="bg-primary text-white py-2 px-4 text-2xl xl:text-3xl">Booking type</h3>
                    <div class="flex items-center space-x-2 p-4 w-full">
                        <div class="flex items-center justify-between w-full">
                            <label for="booking-method" class="font-normal text-gray-900 flex space-x-1">
                                <span class="text-lg">
                                    {{ $ride->booking_method }}
                                </span>
                            </label>
                            <input id="booking-method" checked type="checkbox" value="" class="w-4 h-4 ml-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                        </div>
                    </div>
                </div> --}}
                    <div class="bg-white rounded-lg overflow-hidden shadow-3xl">
                        <h3 class="bg-primary text-white py-2 px-4 text-2xl xl:text-3xl">
                            {{ $rideDetailPage->cancellation_policy_label ?? 'Cancellation policy' }}</h3>
                        <div class="flex items-center space-x-2 p-4 w-full">
                            <div class="flex items-center justify-between w-full">
                                <label for="booking-method" class="font-normal text-gray-900 flex space-x-1">
                                    <span class="text-lg">
                                        {{ $ride->booking_type }}
                                    </span>
                                </label>
                                <input id="booking-method" checked type="checkbox" value=""
                                    class="w-4 h-4 ml-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                            </div>
                        </div>
                    </div>
                    @if (strtotime($ride->date) > strtotime('today') ||
                            (strtotime($ride->date) == strtotime('today') && strtotime($ride->time) > strtotime('now')))
                        @if ($ride->status !== '2')
                            <div class="flex justify-center gap-4">
                                <a href="{{ route('edit_ride', ['lang' => $selectedLanguage->abbreviation, 'id' => $ride->id]) }}"
                                    class="button-exp-fill w-36">
                                    {{ $rideDetailPage->edit_ride_btn_label ?? 'Edit ride' }}
                                </a>
                                <a id="cancelRideBtn" href="#"
                                    class="inline-flex justify-center rounded bg-red-500 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-red-400 w-36">
                                    {{ $rideDetailPage->cancel_ride_btn_label ?? 'Cancel ride' }}
                                </a>

                                {{-- <a href="{{ route('ride.cancel', ['lang' => $selectedLanguage->abbreviation, 'id' => $ride->id]) }}" class="inline-flex justify-center rounded bg-red-500 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-red-400 w-36">
                                {{ $rideDetailPage->cancel_ride_btn_label ?? "Cancel ride"}}
                            </a> --}}
                                {{-- @php
                                if ($cancelSetting) {
                                    // Calculate the cancellation deadline
                                    $cancellationDeadline = strtotime('+' . $cancelSetting->driver_cancel_hours . ' hours', strtotime($ride->added_on));
                                }
                            @endphp
                            @if (isset($cancelSetting) && $cancelSetting && isset($cancellationDeadline))
                                @if (strtotime('now') < $cancellationDeadline)
                                    <a href="{{ route('ride.cancel', ['lang' => $selectedLanguage->abbreviation, 'id' => $ride->id]) }}" class="inline-flex justify-center rounded bg-red-500 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-red-400 w-36">
                                        {{ $rideDetailPage->cancel_ride_btn_label ?? "Cancel ride"}}
                                    </a>
                                @endif
                            @endif --}}
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div id="bookingModal" class="{{ $errors->any() ? '' : 'hidden' }} fixed z-50 inset-0 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 transition-opacity">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>​
            <div
                class="inline-block animate__animated animate__fadeIn align-bottom bg-white rounded-md text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form method="POST" action="{{ route('secured_cash_code') }}">
                    @csrf
                    @method('PUT')
                    <div class="bg-white px-4 py-6 sm:p-6">
                        <div class="sm:flex sm:items-start w-full">
                            {{-- <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M13 7H7v6h6V7z" />
                            </svg>
                        </div> --}}
                            <div class=" text-center sm:mt-0 sm:text-left w-full md:w-3/4">
                                <h3 class="text-primary font-FuturaMdCnBT" id="modal-title">
                                    {{ $rideDetailPage->enter_code_label ?? 'Enter the Secured-cash payment code' }}
                                </h3>
                                <div class="mt-2">
                                    <input type="hidden" name="booking_id" id="booking-id"
                                        value="{{ old('booking_id') }}">
                                    <input type="text" name="code" placeholder="Enter secured-cash payment code"
                                        class="mt-2 p-2 border border-gray-300 rounded w-full"
                                        value="{{ old('code') }}">
                                    @error('code')
                                        <div class="relative tooltip -bottom-4 group-hover:flex">
                                            <div role="tooltip"
                                                class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                                                <p class="text-white leading-none text-sm lg:text-base">{{ $message }}
                                                </p>
                                            </div>
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="px-4 mt-4 sm:px-6 flex items-center justify-center gap-2">
                            <button type="button" class="button-exp-fill w-28" id="closeModal">
                                {{ $message->popup_close_btn_text ?? 'Close' }}
                            </button>
                            <button type="submit" class="button-exp-fill w-28">
                                {{ $message->popup_submit_btn_text ?? 'Submit' }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="securedCashAttemptError" class="hidden fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="relative flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeModal()"></div>
            <div
                class="relative animate__animated animate__fadeIn z-20 transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                 <button onclick="closeModal()" class="absolute top-2 right-2 p-1 rounded-full hover:bg-gray-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="w-full">
                        <p class="can-exp-p text-center">{{ $successMessage->too_many_secured_cash_attempt_message }}</p>
                    </div>
                </div>
                <div class="px-4 pb-6 pt-4  sm:flex sm:flex-row-reverse sm:px-6 justify-center">
                    <a href=""
                        class="inline-flex w-full justify-center rounded bg-greenXS px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-greenXS sm:ml-3 sm:w-24">{{ $messages->popup_close_btn_text ?? 'Close' }}</a>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script>
        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.style.display = 'none';
            }
        }

        // Rest of your existing JavaScript...
        document.addEventListener('DOMContentLoaded', function() {
            const sendCodeButtons = document.querySelectorAll('.send_code');
            const modal = document.getElementById('bookingModal');
            const attemptModal = document.getElementById('securedCashAttemptError');
            const bookingIdField = document.getElementById('booking-id');
            const closeModal = document.getElementById('closeModal');
            const hasErrors = {{ $errors->any() ? 'true' : 'false' }};
            const oldBookingId = "{{ old('booking_id') }}";

            if (hasErrors && oldBookingId) {
                bookingIdField.value = oldBookingId;
                modal.classList.remove('hidden');
            }

            sendCodeButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const bookingId = this.getAttribute('data-booking-id');
                    const bookingSecuredCashAttempt = this.getAttribute('data-booking-secured-cash-attempt');
                    const settingSecuredCashAttempt = this.getAttribute('data-setting-secured-cash-attempt');
                    if (bookingSecuredCashAttempt == settingSecuredCashAttempt) {
                        attemptModal.classList.remove('hidden');
                    } else {
                        bookingIdField.value = bookingId;
                        modal.classList.remove('hidden');
                    }
                });
            });

            closeModal.addEventListener('click', function() {
                modal.classList.add('hidden');
            });

            window.addEventListener('click', function(event) {
                if (event.target == modal) {
                    modal.classList.add('hidden');
                }
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        const cancelRideBtn = document.getElementById('cancelRideBtn');
        cancelRideBtn.addEventListener('click', function(event) {
            event.preventDefault();
            const bookedSeats =
                {{ $ride->bookings()->where('status', '<>', 3)->where('status', '<>', 4)->whereHas('passenger', function ($query) {$query->whereNull('deleted_at');})->sum('seats') }};

            if (bookedSeats === 0) {
                swal.fire({
                    title: '{{ $rideDetailPage->cancel_ride_confirmation ?? "Are you sure you want to cancel this ride?" }}',
                    text: 'This action is irreversible!',
                    // icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#f87171',
                    cancelButtonColor: '#106BC7',
                    confirmButtonText: '{{ $rideDetailPage->cancel_ride_yes_btn ?? "Yes, cancel it!" }}',
                    cancelButtonText: '{{ $rideDetailPage->cancel_ride_no_btn ?? "No, take me back" }}',
                    showCloseButton: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch("{{ route('update_cancel_ride', $ride->id) }}", {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Content-Type': 'application/json',
                                    'X-HTTP-Method-Override': 'PUT'
                                },
                                body: JSON.stringify({})
                            })
                            .then(response => response.json())
                            .then(data => {
                                console.log(data);
                                if (data.success) {
                                    swal.fire({
                                        title: 'This ride has been cancelled',
                                        // icon: 'success',
                                        // showCancelButton: true,
                                        showConfirmButton: true,
                                        confirmButtonText: 'Close',
                                        confirmButtonColor: '#f87171'
                                    }).then(() => {
                                        window.location.href =
                                            "{{ route('my_rides', ['lang' => $selectedLanguage->abbreviation]) }}";
                                    });
                                } else if (data.error) {
                                    swal.fire({
                                        title: data.message,
                                        // icon: 'warning',
                                        showCancelButton: true,
                                        showConfirmButton: true,
                                        confirmButtonText: 'Close',
                                        confirmButtonColor: '#f87171'
                                    });
                                } else {
                                    swal.fire({
                                        title: 'Error',
                                        text: 'Failed to cancel the ride.',
                                        // icon: 'error',
                                        showCancelButton: true,
                                        confirmButtonText: 'Close',
                                        confirmButtonColor: '#f87171'
                                    });
                                }
                            })
                            .catch(error => {
                                swal.fire({
                                    title: 'Error',
                                    text: 'An error occurred while cancelling the ride.',
                                    // icon: 'error',
                                    showCancelButton: true,
                                    confirmButtonText: 'Close',
                                    confirmButtonColor: '#f87171'
                                });
                            });
                    }
                });
            } else {
                window.location.href =
                    "{{ route('ride.cancel', ['lang' => $selectedLanguage->abbreviation, 'id' => $ride->id]) }}";
            }
        });
    </script>
    <style>
        .swal2-confirm {
            background-color: #f87171 !important;
            /* Red background for "Yes, cancel it!" and "Close" */
            border-color: #f87171 !important;
            /* Red border */
        }

        .swal2-cancel {
            background-color: #106BC7 !important;
            /* Blue background for "No, take me back" */
            border-color: #106BC7 !important;
            /* Blue border */
        }
    </style>
@endsection
