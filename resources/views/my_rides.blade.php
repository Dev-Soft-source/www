@extends('layouts.template')

@section('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endsection

@section('content')
@if(session('message'))
<div id="myModal" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div onclick="closeModal()" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
            <div
                class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
                <button type="button" onclick="closeModal()" class="absolute top-3 right-3 text-gray-400 hover:text-gray-500">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start justify-center">
                        <!-- <div
                            class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full sm:mx-0 sm:h-10 sm:w-10 bg-green-500">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-lg text-white w-8 h-8" viewBox="0 0 16 16">
                                <path d="M7.005 3.1a1 1 0 1 1 1.99 0l-.388 6.35a.61.61 0 0 1-1.214 0zM7 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0"/>
                            </svg>
                        </div> -->
                    </div>
                    <div class="text-center sm:ml-4 sm:mt-0 sm:text-left">
                        <div class="">
                            <h3 class="text-3xl text-center font-FuturaMdCnBT font-medium text-gray-900 mb-4" id="modal-title">{!! session('heading') !!}</h3>
                        </div>
                        <div class="mt-2 w-full">
                            <p class="can-exp-p text-center">{!! session('message') !!}</p>
                        </div>
                    </div>
                </div>
                <div class="px-4 pb-6 pt-4 flex items-center space-x-2 sm:space-x-4 sm:px-6 justify-center">
                    @if(session('id'))
                        <a href="{{ route('repost_ride', ['lang' => $selectedLanguage->abbreviation, 'id' => session('id')]) }}"
                            class="inline-flex w-full justify-center rounded bg-greenXS px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white whitespace-nowrap hover:text-white hover:shadow-lg shadow-sm hover:bg-greenXS sm:ml-3 sm:w-fit">Post a Return Ride</a>
                    @endif
                    <a href=""
                        class="button-exp-fill">Close</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
<div class="grid grid-cols-12 gap-4 md:container md:mx-auto  my-6 md:my-10 xl:my-14 px-4 xl:px-0">
    @include('layouts.inc.profile_sidebar')

    <div class="bg-white rounded pt-0 lg:px-4 w-full col-span-12 lg:col-span-9">
        <div class="flex flex-wrap" id="tabs-id">
            <div class="w-full">
                <ul class="flex mb-0 list-none flex-wrap pb-4 flex-row">
                    <li class="-mb-px mr-2 last:mr-0 flex-auto text-center">
                        <a href="{{ route('my_trips', ['lang' => $selectedLanguage->abbreviation]) }}" class="text-2xl font-FuturaMdCnBT px-5 py-2 shadow-lg rounded block border-gray-100 border leading-normal text-blue-600 bg-white cursor-pointer">
                            {{--  Passenger trips  --}}
                            @isset($tripsPage->passenger_trips_heading)
                            {{ $tripsPage->passenger_trips_heading }}
                        @endisset
                        </a>
                    </li>
                    <li class="-mb-px mr-2 last:mr-0 flex-auto text-center">
                        <a href="{{ route('my_rides', ['lang' => $selectedLanguage->abbreviation]) }}" class="text-2xl font-FuturaMdCnBT px-5 py-2 shadow-lg rounded block border-blue-600 border leading-normal text-white bg-blue-600 cursor-pointer">
                            @isset($tripsPage->driver_rides_heading)
                            {{ $tripsPage->driver_rides_heading }}
                        @endisset
                        </a>
                    </li>
                </ul>
                <ul class="flex mb-0 list-none flex-wrap pt-3 pb-4 flex-row">
                    <li class="-mb-px mr-2 last:mr-0 flex-auto text-center">
                        <a href="{{ route('my_rides', ['lang' => $selectedLanguage->abbreviation]) }}" class="text-lg font-FuturaMdCnBT font-medium px-5 py-2 shadow-lg rounded block border-blue-600 border leading-normal text-white bg-blue-600 cursor-pointer">
                            @isset($tripsPage->upcoming_label)
                            {{ $tripsPage->upcoming_label }}
                        @endisset
                        </a>
                    </li>
                    <li class="-mb-px mr-2 last:mr-0 flex-auto text-center">
                        <a href="{{ route('past_rides', ['lang' => $selectedLanguage->abbreviation]) }}" class="text-lg font-FuturaMdCnBT font-medium px-5 py-2 shadow-lg rounded block border-gray-100 border leading-normal text-blue-600 bg-white cursor-pointer">
                            @isset($tripsPage->completed_label)
                            {{ $tripsPage->completed_label }}
                        @endisset
                        </a>
                    </li>
                    <li class="-mb-px mr-2 last:mr-0 flex-auto text-center">
                        <a href="{{ route('cancelled_rides', ['lang' => $selectedLanguage->abbreviation]) }}" class="text-lg font-FuturaMdCnBT font-medium px-5 py-2 shadow-lg rounded block border-gray-100 border leading-normal text-blue-600 bg-white cursor-pointer">
                            @isset($tripsPage->cancelled_label)
                            {{ $tripsPage->cancelled_label }}
                        @endisset
                        </a>
                    </li>
                </ul>
                <div class="relative flex flex-col min-w-0 break-words bg-white w-full py-5 shadow-lg rounded">
                    <div class="">
                    <div class="px-4 flex-auto">
                        <div class="tab-content tab-space">
                            <div class="block" id="tab-profile">
                                <div class="space-y-4">
                                    @if (!empty($rides) && count($rides) > 0)
                                        @foreach ($rides as $ride)
                                            @php
                                                 $from = $ride->rideDetail[0]->departure;
                                                 $to = $ride->rideDetail[0]->destination;
                                            @endphp
                                            <div class="relative even:bg-gray-200 odd:bg-white">
                                                {{-- <div class="absolute right-4 top-8">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 -mt-4 cursor-pointer ride-remove-btn" data-ride-id="29">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                </div> --}}
                                                <a class="" href="{{ route('my_ride_detail', ['lang' => $selectedLanguage->abbreviation, 'departure' => $ride->rideDetail[0]->departure, 'destination' => $ride->rideDetail[0]->destination, 'id' => $ride->id]) }}">
                                                    <div class="rounded-lg shadow-3xl border-[3px] border-solid border-gray-100 " id="ride-29">
                                                        @if ($ride->make === '')
                                                            <span class="bg-red-100 text-red-800 text-sm font-medium ml-3 px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">Not live</span>
                                                        @endif
                                                        <div class="flex items-center justify-between pb-0 p-4">
                                                            <p class="flex items-center space-x-2 font-semibold">
                                                                {{ \Carbon\Carbon::parse($ride->date)->format('F d, Y') }}
                                                                @isset($rideDetailPage->card_section_at_label)
                                                                    {{ $rideDetailPage->card_section_at_label }}
                                                                @endisset
                                                                {{ \Carbon\Carbon::parse($ride->time)->format('h:i A') == '12:00 PM' ? '12 noon' : (\Carbon\Carbon::parse($ride->time)->format('h:i A') == '12:00 AM' ? '12 midnight' : \Carbon\Carbon::parse($ride->time)->format('h:i A')) }}
                                                            </p>
                                                            <div class="pr-8">
                                                                <p class="font-medium">
                                                                    Total {{ $ride->seats }} seats</p>
                                                            </div>
                                                            {{-- <div class="pr-8">
                                                                <p class="font-medium">
                                                                    {{ intval($ride->seats) - intval($ride->bookings()->where('status', '<>', 3)->where('status', '<>', 4)->whereHas('passenger', function($query) { $query->whereNull('deleted_at'); })->sum('seats')) }} seats left</p>
                                                            </div> --}}
                                                        </div>
                                                        <div class="flex flex-col md:flex-row justify-between px-4">
                                                            <div class="w-full md:w-2/3 order-2 md:order-1">
                                                                <div class="relative mt-5 text-left">
                                                                    <div class="flex items-center relative">
                                                                        <div class="border-r-2 border-black border-solid absolute h-full left-3 md:left-6 top-2 z-10">
                                                                            <span
                                                                                class="bg-primary rounded-full w-7 h-7 -top-[2px] -ml-[13px] absolute flex justify-center items-center">
                                                                                <img class="w-5 h-5 object-contain" src="{{ asset('./images/new-21-search-bar-from.png')}}" alt="">
                                                                            </span>
                                                                        </div>
                                                                        <div class="ml-20">
                                                                            <div class="font-bold text-black">
                                                                                @isset($rideDetailPage->card_section_from_label)
                                                                                {{ $rideDetailPage->card_section_from_label }}
                                                                            @endisset
                                                                            </div>
                                                                            <div class="text-primary md:mb-4">{{ $from }} {{ $ride->pickup }}</div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="flex items-center relative">
                                                                        <div class="border-r-2 border-black border-solid absolute h-0 left-3 md:left-5 top-2 z-10">
                                                                            <span
                                                                                class="bg-gray-200 rounded-full w-7 h-7 -top-[6px] -ml-[13px] md:-ml-[10px] absolute flex justify-center items-center">
                                                                                <img class="w-5 h-5 object-contain" src="{{ asset('./images/new-21-search-bar-to.png')}}" alt="">
                                                                            </span>
                                                                        </div>
                                                                        <div class="ml-20">
                                                                            <div class="font-bold text-black">
                                                                                @isset($rideDetailPage->card_section_to_label)
                                                                                {{ $rideDetailPage->card_section_to_label }}
                                                                            @endisset
                                                                            </div>
                                                                            <div class="text-primary md:mb-4">{{ $to }} {{ $ride->dropoff }}</div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="mt-4 order-1 md:order-2">
                                                                <p class="text-xl font-semibold text-primary">${{ number_format(floatval($ride->rideDetail[0]->price), 2) }}
                                                                <small>
                                                                    @isset($rideDetailPage->card_section_per_seat)
                                                                        {{ $rideDetailPage->card_section_per_seat }}
                                                                    @endisset
                                                                </small>
                                                            </p>
                                                            @if (count($ride->bookings->where('status', 0)) > 0)
                                                                <div class="">
                                                                    <p class="font-medium text-red-600">
                                                                        {{ $ride->bookings->where('status', 0)->count() }} booking request</p>
                                                                </div>
                                                            @endif
                                                            </div>
                                                        </div>
                                                        <div class="border-t border-gray-300 grid sm:grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-gray-300">
                                                            <div class="flex items-center justify-between p-4">
                                                                <p class="font-semibold">
                                                                    @isset($rideDetailPage->card_section_booked)
                                                                    {{ $rideDetailPage->card_section_booked }}
                                                                @endisset
                                                                    : </p>
                                                                <p class="">
                                                                    {{
                                                                        $ride->bookings()
                                                                            ->where('status', '<>', 3)
                                                                            ->where('status', '<>', 4)
                                                                            ->whereHas('passenger', function($query) {
                                                                                $query->whereNull('deleted_at'); // Exclude soft deleted users
                                                                            })
                                                                            ->sum('seats')
                                                                    }}  @isset($rideDetailPage->card_section_seats)
                                                                    {{ $rideDetailPage->card_section_seats }}
                                                                @endisset
                                                                </p>
                                                            </div>
                                                            <div class="p-4">
                                                                <div class="flex items-center justify-between">
                                                                    <p class="font-semibold">
                                                                        @isset($rideDetailPage->card_section_seats_fee)
                                                                        {{ $rideDetailPage->card_section_seats_fee }}
                                                                    @endisset
                                                                    : </p>
                                                                    <p class="">

                                                                        ${{ number_format(floatval(($ride->bookings()->where('status', '<>', 3)->where('status', '<>', 4)->whereHas('passenger', function($query) { $query->whereNull('deleted_at'); })->sum('seats') * floatval($ride->rideDetail[0]->price))), 2) }}
                                                                    </p>
                                                                </div>

                                                                <div class="flex items-center justify-between">
                                                                    <p class="font-semibold">
                                                                        @isset($rideDetailPage->card_section_booking_fee)
                                                                        {{ $rideDetailPage->card_section_booking_fee }}
                                                                    @endisset
                                                                        : </p>
                                                                    <p class="">
                                                                        ${{ number_format(floatval($ride->bookings->where('status', '<>', 3)->where('status', '<>', 4)->sum('booking_credit')), 2) }}
                                                                    </p>
                                                                </div>

                                                                <div class="flex items-center justify-between">
                                                                    <p class="font-semibold">
                                                                        @isset($rideDetailPage->card_section_amount)
                                                                        {{ $rideDetailPage->card_section_amount }}
                                                                    @endisset
                                                                        : </p>
                                                                    <p class="">
                                                                        ${{ number_format(floatval(($ride->bookings()->where('status', '<>', 3)->where('status', '<>', 4)->whereHas('passenger', function($query) { $query->whereNull('deleted_at'); })->sum('seats') * floatval($ride->rideDetail[0]->price)) + $ride->bookings->where('status', '<>', 3)->where('status', '<>', 4)->sum('booking_credit')), 2) }}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="border-t border-gray-300 no-scrollbar overflow-x-auto flex items-center space-x-2 p-4">
                                                            @if ($ride->booking_method == $postRidePage->booking_option1->features_setting_id)
                                                                <img class="w-8 h-8"
                                                                    src="{{asset('home_page_icons/' . $postRidePage->booking_option1->icon)}}"
                                                                    alt="">
                                                            @elseif ($ride->booking_method == $postRidePage->booking_option2->features_setting_id)
                                                                <img class="w-8 h-8"
                                                                    src="{{asset('home_page_icons/' . $postRidePage->booking_option2->icon)}}"
                                                                    alt="">
                                                            @endif
                                                            @if ($ride->payment_method == $postRidePage->payment_methods_option1->features_setting_id)
                                                                <img class="w-8 h-8" src="{{asset('home_page_icons/' . $postRidePage->payment_methods_option1->icon)}}" alt="">
                                                            @elseif ($ride->payment_method == $postRidePage->payment_methods_option2->features_setting_id)
                                                                <img class="w-8 h-8" src="{{asset('home_page_icons/' . $postRidePage->payment_methods_option2->icon)}}" alt="">
                                                            @elseif ($ride->payment_method == $postRidePage->payment_methods_option3->features_setting_id)
                                                                <img class="w-8 h-8" src="{{asset('home_page_icons/' . $postRidePage->payment_methods_option3->icon)}}" alt="">
                                                            @endif
                                                            @if ($ride->smoke == $postRidePage->smoking_option1->features_setting_id)
                                                                <img class="w-8 h-8" src="{{asset('home_page_icons/' . $postRidePage->smoking_option1->icon)}}" alt="">
                                                            @elseif ($ride->smoke == $postRidePage->smoking_option2->features_setting_id)
                                                                <img class="w-8 h-8" src="{{asset('home_page_icons/' . $postRidePage->smoking_option2->icon)}}" alt="">
                                                            @endif
                                                            @if ($ride->animal_friendly == $postRidePage->animals_option1->features_setting_id)
                                                                <img class="w-8 h-8"
                                                                    src="{{asset('home_page_icons/' . $postRidePage->animals_option1->icon)}}"
                                                                    alt="">
                                                            @elseif ($ride->animal_friendly == $postRidePage->animals_option2->features_setting_id)
                                                                <img class="w-8 h-8"
                                                                    src="{{asset('home_page_icons/' . $postRidePage->animals_option2->icon)}}"
                                                                    alt="">
                                                            @elseif ($ride->animal_friendly == $postRidePage->animals_option3->features_setting_id)
                                                                <img class="w-8 h-8"
                                                                    src="{{asset('home_page_icons/' . $postRidePage->animals_option3->icon)}}"
                                                                    alt="">
                                                            @endif
                                                            @if ($ride->luggage == $postRidePage->luggage_option1->features_setting_id)
                                                                <img class="w-8 h-8"
                                                                    src="{{asset('home_page_icons/' . $postRidePage->luggage_option1->icon)}}"
                                                                    alt="">
                                                            @elseif ($ride->luggage == $postRidePage->luggage_option2->features_setting_id)
                                                                <img class="w-8 h-8"
                                                                    src="{{asset('home_page_icons/' . $postRidePage->luggage_option2->icon)}}"
                                                                    alt="">
                                                            @elseif ($ride->luggage == $postRidePage->luggage_option3->features_setting_id)
                                                                <img class="w-8 h-8"
                                                                    src="{{asset('home_page_icons/' . $postRidePage->luggage_option3->icon)}}"
                                                                    alt="">
                                                            @elseif ($ride->luggage == $postRidePage->luggage_option4->features_setting_id)
                                                                <img class="w-8 h-8"
                                                                    src="{{asset('home_page_icons/' . $postRidePage->luggage_option4->icon)}}"
                                                                    alt="">
                                                            @elseif ($ride->luggage == $postRidePage->luggage_option5->features_setting_id)
                                                                <img class="w-8 h-8"
                                                                    src="{{asset('home_page_icons/' . $postRidePage->luggage_option5->icon)}}"
                                                                    alt="">
                                                            @endif
                                                            @if (in_array($postRidePage->features_option1->features_setting_id, explode('=', $ride->features)))
                                                                <img class="w-8 h-8" src="{{asset('home_page_icons/' . $postRidePage->features_option1->icon)}}" alt="">
                                                            @endif
                                                            @if (in_array($postRidePage->features_option2->features_setting_id, explode('=', $ride->features)))
                                                                <img class="w-8 h-8" src="{{asset('home_page_icons/' . $postRidePage->features_option2->icon)}}" alt="">
                                                            @endif
                                                            @if (in_array($postRidePage->features_option3->features_setting_id, explode('=', $ride->features)))
                                                                <img class="w-8 h-8" src="{{asset('home_page_icons/' . $postRidePage->features_option3->icon)}}" alt="">
                                                            @endif
                                                            @if (in_array($postRidePage->features_option8->features_setting_id, explode('=', $ride->features)))
                                                                <img class="w-8 h-8" src="{{asset('home_page_icons/' . $postRidePage->features_option8->icon)}}" alt="">
                                                            @endif
                                                            @if (in_array($postRidePage->features_option9->features_setting_id, explode('=', $ride->features)))
                                                                <img class="w-8 h-8" src="{{asset('home_page_icons/' . $postRidePage->features_option9->icon)}}" alt="">
                                                            @endif
                                                            @if (in_array($postRidePage->features_option10->features_setting_id, explode('=', $ride->features)))
                                                                <img class="w-8 h-8" src="{{asset('home_page_icons/' . $postRidePage->features_option10->icon)}}" alt="">
                                                            @endif
                                                            @if (in_array($postRidePage->features_option11->features_setting_id, explode('=', $ride->features)))
                                                                <img class="w-8 h-8" src="{{asset('home_page_icons/' . $postRidePage->features_option11->icon)}}" alt="">
                                                            @endif
                                                            @if (in_array($postRidePage->features_option12->features_setting_id, explode('=', $ride->features)))
                                                                <img class="w-8 h-8" src="{{asset('home_page_icons/' . $postRidePage->features_option12->icon)}}" alt="">
                                                            @endif
                                                            @if (in_array($postRidePage->features_option13->features_setting_id, explode('=', $ride->features)))
                                                                <img class="w-8 h-8" src="{{asset('home_page_icons/' . $postRidePage->features_option13->icon)}}" alt="">
                                                            @endif
                                                            @if (in_array($postRidePage->features_option14->features_setting_id, explode('=', $ride->features)))
                                                                <img class="w-8 h-8" src="{{asset('home_page_icons/' . $postRidePage->features_option14->icon)}}" alt="">
                                                            @endif
                                                            @if (in_array($postRidePage->features_option15->features_setting_id, explode('=', $ride->features)))
                                                                <img class="w-8 h-8" src="{{asset('home_page_icons/' . $postRidePage->features_option15->icon)}}" alt="">
                                                            @endif
                                                            @if (in_array($postRidePage->features_option16->features_setting_id, explode('=', $ride->features)))
                                                                <img class="w-8 h-8" src="{{asset('home_page_icons/' . $postRidePage->features_option16->icon)}}" alt="">
                                                            @endif
                                                            @if (in_array($postRidePage->features_option4->features_setting_id, explode('=', $ride->features)))
                                                                <img class="w-8 h-8" src="{{asset('home_page_icons/' . $postRidePage->features_option4->icon)}}" alt="">
                                                            @endif
                                                            @if (in_array($postRidePage->features_option5->features_setting_id, explode('=', $ride->features)))
                                                                <img class="w-8 h-8" src="{{asset('home_page_icons/' . $postRidePage->features_option5->icon)}}" alt="">
                                                            @endif
                                                            @if (in_array($postRidePage->features_option6->features_setting_id, explode('=', $ride->features)))
                                                                <img class="w-8 h-8" src="{{asset('home_page_icons/' . $postRidePage->features_option6->icon)}}" alt="">
                                                            @endif
                                                            @if (in_array($postRidePage->features_option7->features_setting_id, explode('=', $ride->features)))
                                                                <img class="w-8 h-8" src="{{asset('home_page_icons/' . $postRidePage->features_option7->icon)}}" alt="">
                                                            @endif
                                                        </div>
                                                        <div class="border-t border-gray-300 flex no-scrollbar overflow-x-auto items-center space-x-2 p-4">
                                                            @foreach ($ride->bookings->where('status', '<>', 3)->where('status', '<>', 4) as $booking)
                                                                @for ($i = 0; $i < $booking->seats; $i++)
                                                                    @if ($booking->passenger)
                                                                            @if ($booking->passenger->profile_image)
                                                                                <img class="w-10 h-10 rounded-full"
                                                                                    src="{{ $booking->passenger->profile_image }}"
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
                                                </a>
                                            </div>
                                        @endforeach
                                        {{ $rides->links() }}
                                    @else
                                        <p>You have no upcoming rides </p>
                                        {{-- <p>No rides posted. You can post a ride <a href="{{ route('post_ride', ['lang' => $selectedLanguage->abbreviation]) }}">here</a></p> --}}
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