@extends('layouts.template')

@section('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endsection

@section('content')
@if(session('success'))
<div id="myModal" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div onclick="closeModal()" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center:p-0">
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
                    <div class="text-center sm:ml-4 sm:mt-0 w-full">
                        <div class="mt-2">
                            <h3 class="text-3xl text-center font-FuturaMdCnBT font-medium text-gray-900 mb-4" id="modal-title">{!! session('heading') !!}</h3>
                        </div>
                        <div class="mt-2 w-full">
                            <p class="can-exp-p text-center">{!! session('success') !!}</p>
                        </div>
                    </div>
                </div>
                <div class="px-4 pb-6 pt-4 flex items-center space-x-2 sm:space-x-4 sm:px-6 justify-center">
                    @if(session('id'))
                        <a href="{{ route('repost_ride', ['lang' => $selectedLanguage->abbreviation, 'id' => session('id')]) }}"
                            class="inline-flex w-full justify-center rounded bg-greenXS px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white whitespace-nowrap hover:text-white hover:shadow-lg shadow-sm hover:bg-greenXS sm:ml-3 sm:w-fit">Repost ride</a>
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
                        <a href="{{ route('my_trips', ['lang' => $selectedLanguage->abbreviation]) }}" class="text-2xl font-FuturaMdCnBT font-medium px-5 py-2 shadow-lg rounded block border-blue-600 border leading-normal text-white bg-blue-600 cursor-pointer">
                            {{--  Passenger trips  --}}
                            @isset($tripsPage->passenger_trips_heading)
                                {{ $tripsPage->passenger_trips_heading }}
                            @endisset

                        </a>
                    </li>
                    <li class="-mb-px mr-2 last:mr-0 flex-auto text-center">
                        <a href="{{ route('my_rides', ['lang' => $selectedLanguage->abbreviation]) }}" class="text-2xl font-FuturaMdCnBT font-medium px-5 py-2 shadow-lg rounded block border-gray-100 border leading-normal text-blue-600 bg-white cursor-pointer">
                            {{--  Driver rides   --}}
                            @isset($tripsPage->driver_rides_heading)
                            {{ $tripsPage->driver_rides_heading }}
                        @endisset
                        </a>
                    </li>
                </ul>
                <ul class="flex mb-0 list-none flex-wrap pt-3 pb-4 flex-row">
                    <li class="-mb-px mr-2 last:mr-0 flex-auto text-center">
                        <a href="{{ route('my_trips', ['lang' => $selectedLanguage->abbreviation]) }}" class="text-lg font-FuturaMdCnBT font-medium px-5 py-2 shadow-lg rounded block border-blue-600 border leading-normal text-white bg-blue-600 cursor-pointer">
                            @isset($tripsPage->upcoming_label)
                            {{ $tripsPage->upcoming_label }}
                        @endisset
                        </a>
                    </li>
                    <li class="-mb-px mr-2 last:mr-0 flex-auto text-center">
                        <a href="{{ route('past_trips', ['lang' => $selectedLanguage->abbreviation]) }}" class="text-lg font-FuturaMdCnBT font-medium px-5 py-2 shadow-lg rounded block border-gray-100 border leading-normal text-blue-600 bg-white cursor-pointer">
                            @isset($tripsPage->completed_label)
                            {{ $tripsPage->completed_label }}
                        @endisset
                        </a>
                    </li>
                    <li class="-mb-px mr-2 last:mr-0 flex-auto text-center">
                        <a href="{{ route('cancelled_trips', ['lang' => $selectedLanguage->abbreviation]) }}" class="text-lg font-FuturaMdCnBT font-medium px-5 py-2 shadow-lg rounded block border-gray-100 border leading-normal text-blue-600 bg-white cursor-pointer">
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
                                    @if (!empty($bookings) && count($bookings) > 0)
                                        @foreach ($bookings as $booking)
                                            @php
                                                $from = $booking->departure; 
                                                $to = $booking->destination;
                                                $userBookingId = $booking->ride->bookings()
                                                    ->where('user_id', auth()->user()->id)
                                                    ->where('status', '<>', 3)
                                                    ->where('status', '<>', 4)
                                                    ->whereHas('passenger', function($query) {
                                                        $query->whereNull('deleted_at');
                                                    })
                                                    ->pluck('id')->first();
                                                $exist = \App\Models\NoShowHistory::where('ride_id', $booking->ride_id)->where('booking_id', $userBookingId)->where('user_id', $booking->ride->added_by)->where('type', 'driver')->first();
                                                // dd($userBookingId);

                                            @endphp
                                            <div class="relative even:bg-gray-100 odd:bg-white">
                                            
                                                @if (strtotime($booking->ride->date) < strtotime('today') || (strtotime($booking->ride->date) == strtotime('today') && strtotime($booking->ride->time) < strtotime('now')))
                                                @if(!isset($exist))
                                                    <div class="absolute right-4 top-32 md:top-28">
                                                        <a href="javascript:void(0)" id="noShowDriverButton" data-booking-id="{{ $booking->ride->bookings()->where('user_id', auth()->user()->id)->where('status', '<>', 3)->where('status', '<>', 4)->whereHas('passenger', function($query) { $query->whereNull('deleted_at'); })->pluck('id')->first() }}" class="button-exp-fill me-1">
                            {{$messages->no_show_driver_button ?? "No show driver"}}
                            
                                                        </a>
                                                    </div>
                                                    @else
                                                    <div class="absolute right-4 top-32 md:top-28">
                                                       <a href="javascript:void(0)" id="revertNoShowDriverButton" data-booking-id="{{ $booking->ride->bookings()->where('user_id', auth()->user()->id)->where('status', '<>', 3)->where('status', '<>', 4)->whereHas('passenger', function($query) { $query->whereNull('deleted_at'); })->pluck('id')->first() }}" class="button-exp-fill me-1">
                                                        {{$messages->revert_arbitration_button ?? "Revert"}}

                                                       </a>
                                                   </div>

                                                   @endif
                                                @endif
                                                <a href="{{ route('ride_detail', ['lang' => $selectedLanguage->abbreviation, 'departure' => $booking->departure, 'destination' => $booking->destination, 'id' => $booking->ride->id]) }}">
                                                    <div style="cursor:pointer;" onclick="window.location=''" style="cursor:pointer;">
                                                    <div class="rounded-lg shadow-3xl border-[3px] border-solid  border-gray-100 " id="ride-29">
                                                        <div class="flex items-center justify-between pb-0 p-4">
                                                            <p class="flex items-center space-x-2 font-semibold">
                                                                {{ \Carbon\Carbon::parse($booking->ride->date)->format('F d, Y') }}
                                                                @isset($rideDetailPage->card_section_at_label)
                                                                    {{ $rideDetailPage->card_section_at_label }}
                                                                @endisset
                                                                {{ \Carbon\Carbon::parse($booking->ride->time)->format('h:i A') == '12:00 PM' ? '12 noon' : (\Carbon\Carbon::parse($booking->ride->time)->format('h:i A') == '12:00 AM' ? '12 midnight' : \Carbon\Carbon::parse($booking->ride->time)->format('h:i A')) }}
                                                            </p>
                                                            {{-- <div class="pr-8">
                                                                <p class="font-medium">
                                                                    {{ $booking->ride->seats - $booking->ride->bookings()->where('status', '<>', 3)->where('status', '<>', 4)->whereHas('passenger', function($query) { $query->whereNull('deleted_at'); })->sum('seats') }} seats left
                                                            </div> --}}
                                                        </div>
                                                        <div class="flex flex-col md:flex-row justify-between px-4 pb-4 md:pb-0">
                                                            <div class="w-full md:w-2/3 order-2 md:order-1">
                                                                <div class="relative mt-3 md:mt-5 text-left">
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
                                                                            <div class="text-primary md:mb-4">{{ $from }} {{ $booking->ride->pickup }}</div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="flex items-center relative">
                                                                    <div class="border-r-2 border-black border-solid absolute h-0 left-3 md:left-5 top-2 z-10">
                                                                            <span
                                                                                class="bg-gray-200 rounded-full w-7 h-7 -top-[6px] -ml-[13px] md:-ml-[9px] absolute flex justify-center items-center">
                                                                                <img class="w-5 h-5 object-contain" src="{{ asset('./images/new-21-search-bar-to.png')}}" alt="">
                                                                            </span>
                                                                        </div>
                                                                        <div class="ml-20">
                                                                            <div class="font-bold text-black">
                                                                                @isset($rideDetailPage->card_section_to_label)
                                                                                {{ $rideDetailPage->card_section_to_label }}
                                                                            @endisset
                                                                            </div>
                                                                            <div class="text-primary md:mb-4">{{ $to }} {{ $booking->ride->dropoff }}</div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="mt-4 order-1 md:order-2">
                                                                <p class="text-xl font-semibold text-primary">${{ number_format(floatval($booking->price), 2) }} <small>
                                                                    @isset($rideDetailPage->card_section_per_seat)
                                                                    {{ $rideDetailPage->card_section_per_seat }}
                                                                @endisset
                                                                </small></p>
                                                            </div>
                                                        </div>
                                                        <div class="border-t border-gray-300 grid grid-cols-2 divide-x divide-gray-300">
                                                            <div class="flex items-center justify-center p-2 md:p-4">
                                                                <p class="">
                                                                    {{ $booking->ride->bookings()->where('user_id', auth()->user()->id)->where('status', '<>', 3)->where('status', '<>', 4)->whereHas('passenger', function($query) { $query->whereNull('deleted_at'); })->sum('seats') }}
                                                                    @isset($rideDetailPage->trips_card_section_seat_booked)
                                                                    {{ $rideDetailPage->trips_card_section_seat_booked }}
                                                                @endisset
                                                                </p>
                                                            </div>
                                                            <div class="flex items-center justify-center p-2 md:p-4">
                                                                <p class="">
                                                                    {{ intval($booking->ride->seats) - intval($booking->ride->bookings()->where('status', '<>', 3)->where('status', '<>', 4)->whereHas('passenger', function($query) { $query->whereNull('deleted_at'); })->sum('seats')) }}
                                                                    @isset($rideDetailPage->trips_card_section_seat_available)
                                                                    {{ $rideDetailPage->trips_card_section_seat_available }}
                                                                @endisset
                                                                </p>
                                                            </div>
                                                        </div>
                                                        <div class="border-t border-gray-300 no-scrollbar overflow-x-auto flex items-center space-x-2 p-4">
                                                            @if ($booking->ride->booking_method == $postRidePage->booking_option1->features_setting_id)
                                                                <img class="w-8 h-8"
                                                                    src="{{asset('home_page_icons/' . $postRidePage->booking_option1->icon)}}"
                                                                    alt="">
                                                            @elseif ($booking->ride->booking_method == $postRidePage->booking_option2->features_setting_id)
                                                                <img class="w-8 h-8"
                                                                    src="{{asset('home_page_icons/' . $postRidePage->booking_option2->icon)}}"
                                                                    alt="">
                                                            @endif
                                                            @if ($booking->ride->payment_method == $postRidePage->payment_methods_option1->features_setting_id)
                                                                <img class="w-8 h-8" src="{{asset('home_page_icons/' . $postRidePage->payment_methods_option1->icon)}}" alt="">
                                                            @elseif ($booking->ride->payment_method == $postRidePage->payment_methods_option2->features_setting_id)
                                                                <img class="w-8 h-8" src="{{asset('home_page_icons/' . $postRidePage->payment_methods_option2->icon)}}" alt="">
                                                            @elseif ($booking->ride->payment_method == $postRidePage->payment_methods_option3->features_setting_id)
                                                                <img class="w-8 h-8" src="{{asset('home_page_icons/' . $postRidePage->payment_methods_option3->icon)}}" alt="">
                                                            @endif
                                                            @if ($booking->ride->smoke == $postRidePage->smoking_option1->features_setting_id)
                                                                <img class="w-8 h-8" src="{{asset('home_page_icons/' . $postRidePage->smoking_option1->icon)}}" alt="">
                                                            @elseif ($booking->ride->smoke == $postRidePage->smoking_option2->features_setting_id)
                                                                <img class="w-8 h-8" src="{{asset('home_page_icons/' . $postRidePage->smoking_option2->icon)}}" alt="">
                                                            @endif
                                                            @if ($booking->ride->animal_friendly == $postRidePage->animals_option1->features_setting_id)
                                                                <img class="w-8 h-8"
                                                                    src="{{asset('home_page_icons/' . $postRidePage->animals_option1->icon)}}"
                                                                    alt="">
                                                            @elseif ($booking->ride->animal_friendly == $postRidePage->animals_option2->features_setting_id)
                                                                <img class="w-8 h-8"
                                                                    src="{{asset('home_page_icons/' . $postRidePage->animals_option2->icon)}}"
                                                                    alt="">
                                                            @elseif ($booking->ride->animal_friendly == $postRidePage->animals_option3->features_setting_id)
                                                                <img class="w-8 h-8"
                                                                    src="{{asset('home_page_icons/' . $postRidePage->animals_option3->icon)}}"
                                                                    alt="">
                                                            @endif
                                                            @if ($booking->ride->luggage == $postRidePage->luggage_option1->features_setting_id)
                                                                <img class="w-8 h-8"
                                                                    src="{{asset('home_page_icons/' . $postRidePage->luggage_option1->icon)}}"
                                                                    alt="">
                                                            @elseif ($booking->ride->luggage == $postRidePage->luggage_option2->features_setting_id)
                                                                <img class="w-8 h-8"
                                                                    src="{{asset('home_page_icons/' . $postRidePage->luggage_option2->icon)}}"
                                                                    alt="">
                                                            @elseif ($booking->ride->luggage == $postRidePage->luggage_option3->features_setting_id)
                                                                <img class="w-8 h-8"
                                                                    src="{{asset('home_page_icons/' . $postRidePage->luggage_option3->icon)}}"
                                                                    alt="">
                                                            @elseif ($booking->ride->luggage == $postRidePage->luggage_option4->features_setting_id)
                                                                <img class="w-8 h-8"
                                                                    src="{{asset('home_page_icons/' . $postRidePage->luggage_option4->icon)}}"
                                                                    alt="">
                                                            @elseif ($booking->ride->luggage == $postRidePage->luggage_option5->features_setting_id)
                                                                <img class="w-8 h-8"
                                                                    src="{{asset('home_page_icons/' . $postRidePage->luggage_option5->icon)}}"
                                                                    alt="">
                                                            @endif
                                                            @if (in_array($postRidePage->features_option1->features_setting_id, explode('=', $booking->ride->features)))
                                                                <img class="w-8 h-8" src="{{asset('home_page_icons/' . $postRidePage->features_option1->icon)}}" alt="">
                                                            @endif
                                                            @if (in_array($postRidePage->features_option2->features_setting_id, explode('=', $booking->ride->features)))
                                                                <img class="w-8 h-8" src="{{asset('home_page_icons/' . $postRidePage->features_option2->icon)}}" alt="">
                                                            @endif
                                                            @if (in_array($postRidePage->features_option3->features_setting_id, explode('=', $booking->ride->features)))
                                                                <img class="w-8 h-8" src="{{asset('home_page_icons/' . $postRidePage->features_option3->icon)}}" alt="">
                                                            @endif
                                                            @if (in_array($postRidePage->features_option8->features_setting_id, explode('=', $booking->ride->features)))
                                                                <img class="w-8 h-8" src="{{asset('home_page_icons/' . $postRidePage->features_option8->icon)}}" alt="">
                                                            @endif
                                                            @if (in_array($postRidePage->features_option9->features_setting_id, explode('=', $booking->ride->features)))
                                                                <img class="w-8 h-8" src="{{asset('home_page_icons/' . $postRidePage->features_option9->icon)}}" alt="">
                                                            @endif
                                                            @if (in_array($postRidePage->features_option10->features_setting_id, explode('=', $booking->ride->features)))
                                                                <img class="w-8 h-8" src="{{asset('home_page_icons/' . $postRidePage->features_option10->icon)}}" alt="">
                                                            @endif
                                                            @if (in_array($postRidePage->features_option11->features_setting_id, explode('=', $booking->ride->features)))
                                                                <img class="w-8 h-8" src="{{asset('home_page_icons/' . $postRidePage->features_option11->icon)}}" alt="">
                                                            @endif
                                                            @if (in_array($postRidePage->features_option12->features_setting_id, explode('=', $booking->ride->features)))
                                                                <img class="w-8 h-8" src="{{asset('home_page_icons/' . $postRidePage->features_option12->icon)}}" alt="">
                                                            @endif
                                                            @if (in_array($postRidePage->features_option13->features_setting_id, explode('=', $booking->ride->features)))
                                                                <img class="w-8 h-8" src="{{asset('home_page_icons/' . $postRidePage->features_option13->icon)}}" alt="">
                                                            @endif
                                                            @if (in_array($postRidePage->features_option14->features_setting_id, explode('=', $booking->ride->features)))
                                                                <img class="w-8 h-8" src="{{asset('home_page_icons/' . $postRidePage->features_option14->icon)}}" alt="">
                                                            @endif
                                                            @if (in_array($postRidePage->features_option15->features_setting_id, explode('=', $booking->ride->features)))
                                                                <img class="w-8 h-8" src="{{asset('home_page_icons/' . $postRidePage->features_option15->icon)}}" alt="">
                                                            @endif
                                                            @if (in_array($postRidePage->features_option16->features_setting_id, explode('=', $booking->ride->features)))
                                                                <img class="w-8 h-8" src="{{asset('home_page_icons/' . $postRidePage->features_option16->icon)}}" alt="">
                                                            @endif
                                                            @if (in_array($postRidePage->features_option4->features_setting_id, explode('=', $booking->ride->features)))
                                                                <img class="w-8 h-8" src="{{asset('home_page_icons/' . $postRidePage->features_option4->icon)}}" alt="">
                                                            @endif
                                                            @if (in_array($postRidePage->features_option5->features_setting_id, explode('=', $booking->ride->features)))
                                                                <img class="w-8 h-8" src="{{asset('home_page_icons/' . $postRidePage->features_option5->icon)}}" alt="">
                                                            @endif
                                                            @if (in_array($postRidePage->features_option6->features_setting_id, explode('=', $booking->ride->features)))
                                                                <img class="w-8 h-8" src="{{asset('home_page_icons/' . $postRidePage->features_option6->icon)}}" alt="">
                                                            @endif
                                                            @if (in_array($postRidePage->features_option7->features_setting_id, explode('=', $booking->ride->features)))
                                                                <img class="w-8 h-8" src="{{asset('home_page_icons/' . $postRidePage->features_option7->icon)}}" alt="">
                                                            @endif
                                                        </div>
                                                        @if ($booking->ride->driver)
                                                            <div class="border-t border-gray-300 flex items-center justify-between p-4 w-full">
                                                                <div class="flex items-center space-x-2">
                                                                    <div class="w-12 h-12 rounded-full overflow-hidden">
                                                                        <img class="w-full h-full object-cover" src="{{ $booking->ride->driver->profile_image }}" alt="">
                                                                    </div>
                                                                    <div class="text-center">
                                                                        <p class="font-semibold">
                                                                            <span>
                                                                                @if ($booking->ride->driver->type === '2')
                                                                                    {{ $booking->ride->driver->last_name }}
                                                                                @elseif ($booking->ride->driver->type === '3')
                                                                                    {{ $booking->ride->driver->first_name }} {{ $booking->ride->driver->last_name }}
                                                                                @else
                                                                                    {{ $booking->ride->driver->first_name }}
                                                                                @endif
                                                                            </span>
                                                                        </p>
                                                                        @php
                                                                            // Calculate the age based on the driver's date of birth
                                                                            $dob = \Carbon\Carbon::parse($booking->ride->driver->dob);
                                                                            $age = $dob->diffInYears(\Carbon\Carbon::now());
                                                                        @endphp
                                                                        <div class="flex items-center gap-2 flex-wrap">
                                                                            <p class="mb-0 text-sm font-medium border-r border-gray-600 pr-2">
                                                                                @isset($rideDetailPage->card_section_age)
                                                                                    {{ $rideDetailPage->card_section_age }}
                                                                                @endisset
                                                                                : {{ $age }}</p>
                                                                            <p class="mb-0 text-sm font-medium border-r border-gray-600 pr-2">{{ $booking->ride->driver->gender }}</p>
                                                                            <p class="mb-0 text-sm font-medium border-r border-gray-600 pr-2">
                                                                                @isset($rideDetailPage->card_section_driven)
                                                                                {{ $rideDetailPage->card_section_driven }}
                                                                            @endisset
                                                                            :
                                                                                {{  $booking->ride->driver->rides()
                                                                                        ->where('status', '!=', 2)
                                                                                        ->where(function ($query) {
                                                                                            $query->whereDate('rides.date', '<', now()->toDateString())
                                                                                                ->orWhere(function ($query) {
                                                                                                    $query->whereDate('rides.date', '=', now()->toDateString())
                                                                                                        ->whereTime('rides.time', '<=', now()->toTimeString());
                                                                                                });
                                                                                        })
                                                                                        ->get()
                                                                                        ->flatMap(function ($ride) {
                                                                                            return $ride->bookings()->pluck('seats');
                                                                                        })
                                                                                        ->sum()
                                                                                }}
                                                                            </p>
                                                                            @php
                                                                                $filteredRatings = $ratings->where('status', 1)->where('type', '1')->filter(function ($rating) use ($booking) {
                                                                                    return $rating->ride && $booking->ride && $rating->ride->added_by === $booking->ride->added_by;
                                                                                });

                                                                                $totalAverage = $filteredRatings->avg('average_rating') ?? 0;
                                                                            @endphp
                                                                            <p class="mb-0 text-sm font-medium">
                                                                                @isset($rideDetailPage->card_section_review)
                                                                                {{ $rideDetailPage->card_section_review}}
                                                                            @endisset
                                                                                : {{ number_format($totalAverage, 1) }}</p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    </div>
                                                </a>
                                            </div>
                                        @endforeach
                                        {{ $bookings->links() }}
                                    @else
                                        <p>You have no booked trips</p>
                                        {{-- <p>No booked ride. You can search for a ride <a href="{{ route('search_ride', ['lang' => $selectedLanguage->abbreviation]) }}">here</a></p> --}}
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

<div id="bookingModal" class="hidden fixed z-50 inset-0 overflow-y-auto">
    <div class="relative z-50">
        <div id="close-modal" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full iteitems-end justify-center p-4 text-center sm:items-center w-full">
                <div
                    class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-xl w-full">
                    <button type="button" id="close-modal" class="absolute top-3 right-3 text-gray-400 hover:text-gray-500">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start justify-center">
                            <!-- <div
                                class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full sm:mx-0 sm:h-10 sm:w-10 bg-blue-500">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-lg text-white w-8 h-8" viewBox="0 0 16 16">
                                    <path d="M7.005 3.1a1 1 0 1 1 1.99 0l-.388 6.35a.61.61 0 0 1-1.214 0zM7 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0"/>
                                </svg>
                            </div> -->
                        </div>
                        <div class="text-center sm:ml-4 sm:mt-0 sm:text-left">
                            <div class="">
                                <h3 class="text-3xl text-center font-FuturaMdCnBT font-medium text-gray-900 mb-4 modal-message mt-3" id="modal-title">{!! session('heading') !!}</h3>
                            </div>
                            <div class="mt-2 w-full">
                                <p class="text-center can-exp-p"></p>
                            </div>
                        </div>
                    </div>
                    <div class="px-4 pb-6 pt-4  sm:flex sm:flex-row-reverse sm:px-6 justify-center">
                        <button type="button" id="close-modal" class="inline-flex w-full justify-center rounded bg-blue-500 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-blue-400 sm:ml-3 sm:w-24">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="revertModal" class="hidden fixed z-50 inset-0 overflow-y-auto">
    <div class="relative z-50">
        <div id="take-me-back-modal" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center">
                <div class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg w-full">
                <button type="button" id="take-me-back-modal" class="absolute top-3 right-3 text-gray-400 hover:text-gray-500">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start justify-center">
                            <!-- <div
                                class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full sm:mx-0 sm:h-10 sm:w-10 bg-blue-500">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-lg text-white w-8 h-8" viewBox="0 0 16 16">
                                    <path d="M7.005 3.1a1 1 0 1 1 1.99 0l-.388 6.35a.61.61 0 0 1-1.214 0zM7 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0"/>
                                </svg>
                            </div> -->
                        </div>
                        <div class="text-center sm:ml-4 sm:mt-0 sm:text-left">
                            <div class="">
                                <h3 class="text-3xl text-center font-FuturaMdCnBT font-medium text-gray-900 mb-4" id="modal-title">{{ $messages->cancel_noshow_are_you_sure??'are you sure' }}</h3>
                            </div>
                            <div class="mt-2">
                                <p class="can-exp-p"></p>
                            </div>
                        </div>
                    </div>
                    <div class="px-4 pb-6 pt-4 sm:flex sm:flex-row-reverse sm:px-6 justify-center">
                        <button type="button" id="take-me-back-modal" class="inline-flex w-full justify-center rounded bg-blue-500 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-blue-400 sm:ml-3 sm:w-24">
                            {{$messages->cancel_noshow_take_me_back ?? "No take me back"}}
                        </button>
                        <button type="button" id="close-revert-modal" class="inline-flex w-full justify-center rounded bg-blue-500 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-blue-400 sm:ml-3 sm:w-24">
                            {{$messages->confirm_cancel_noshow ?? "Yes"}}
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Attach click event to all buttons with ID 'noShowDriverButton'
        document.querySelectorAll('#noShowDriverButton').forEach(button => {
            button.addEventListener('click', function () {
                // Get the booking ID from the data attribute
                const booking_id = this.getAttribute('data-booking-id');
                console.log('Booking ID:', booking_id);

                $.ajax({
                    url: '{{ route("no_show_driver") }}', // Laravel route for the no_show_driver
                    type: 'POST',
                    data: {
                        booking_id: booking_id,
                        _token: '{{ csrf_token() }}' // CSRF token for security
                    },
                    success: function(response) {
                        console.log('Seats on hold:', response);
                        
                        // Update the modal content with the response message
                        const modalMessageElement = document.querySelector('.modal-message');
                        if (modalMessageElement) {
                            modalMessageElement.textContent = response.message; // Assuming 'message' is part of the response
                        }
                        const modal = document.getElementById('bookingModal');
                        modal.classList.remove('hidden');
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                        // Handle error response
                    }
                });
            });
        });
    });
    
    // document.addEventListener('DOMContentLoaded', function () {
    //     // Attach click event to all buttons with ID 'noShowDriverButton'
    //     document.querySelectorAll('#revertNoShowDriverButton').forEach(button => {
    //         button.addEventListener('click', function () {
    //             // Get the booking ID from the data attribute
    //             const booking_id = this.getAttribute('data-booking-id');
    //             console.log('Booking ID:', booking_id);

    //             $.ajax({
    //                 url: '{{ route("revert_no_show_driver") }}', // Laravel route for the no_show_driver
    //                 type: 'POST',
    //                 data: {
    //                     booking_id: booking_id,
    //                     _token: '{{ csrf_token() }}' // CSRF token for security
    //                 },
    //                 success: function(response) {
    //                     console.log('Seats on hold:', response);
                        
    //                     // Update the modal content with the response message
    //                     const modalMessageElement = document.querySelector('#bookingModal .text-sm.text-gray-500');
    //                     if (modalMessageElement) {
    //                         modalMessageElement.textContent = response.message; // Assuming 'message' is part of the response
    //                     }
    //                     const modal = document.getElementById('bookingModal');
    //                     modal.classList.remove('hidden');
    //                 },
    //                 error: function(xhr, status, error) {
    //                     console.error('Error:', error);
    //                     // Handle error response
    //                 }
    //             });
    //         });
    //     });
    // });
    document.addEventListener('DOMContentLoaded', function () {
    let selectedBookingId = null;

    // Step 1: Listen for clicks on the revert button
    document.querySelectorAll('#revertNoShowDriverButton').forEach(button => {
        button.addEventListener('click', function () {
            selectedBookingId = this.getAttribute('data-booking-id');
            console.log('Selected Booking ID:', selectedBookingId);

            // Show the modal
            const modal = document.getElementById('revertModal');
            modal.classList.remove('hidden');
        });
    });

    // Step 2: Handle the click on the "Yes" button in the modal
    const confirmButton = document.getElementById('close-revert-modal');
    confirmButton.addEventListener('click', function () {
        if (!selectedBookingId) return;

        $.ajax({
            url: '{{ route("revert_no_show_driver") }}',
            type: 'POST',
            data: {
                booking_id: selectedBookingId,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                console.log('Seats on hold:', response);

                // Update the modal message
                const modalMessageElement = document.querySelector('#bookingModal .text-sm.text-gray-500');
                if (modalMessageElement) {
                    modalMessageElement.textContent = response.message || 'Action completed.';
                }

                // Optionally hide the modal
                const modal = document.getElementById('revertModal');
                modal.classList.add('hidden');
                window.location.reload();

                // Clear stored ID
                selectedBookingId = null;
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    });
});
    document.getElementById('close-modal').addEventListener('click', function () {
        const modal = document.getElementById('bookingModal');
        window.location.reload();
        modal.classList.add('hidden');
    });
    document.getElementById('take-me-back-modal').addEventListener('click', function () {
        const modal = document.getElementById('revertModal');
        modal.classList.add('hidden');
    });
    function closeModal() {
    const modal = document.getElementById('myModal');
        if (modal) {
            modal.classList.add('hidden');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Get the modal and close buttons
        const modal = document.getElementById('bookingModal');
        const closeButtons = document.querySelectorAll('#close-modal');
        
        // Add click event to all close buttons
        closeButtons.forEach(button => {
            button.addEventListener('click', function() {
                modal.classList.add('hidden');
            });
        });
        
        // Optional: Close when clicking outside the modal content
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.classList.add('hidden');
            }
        });
    });
    
</script>

@endsection
