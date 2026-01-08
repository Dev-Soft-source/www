@extends('layouts.template')

@section('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endsection

@section('content')

<div class="grid grid-cols-12 gap-4 md:container md:mx-auto  my-6 md:my-10 xl:my-14 px-4 xl:px-0">
    @include('layouts.inc.profile_sidebar')

    <div class="bg-white rounded pt-0 lg:px-4 w-full col-span-12 lg:col-span-9">
        <div class="flex flex-wrap" id="tabs-id">
            <div class="w-full">
                <ul class="flex mb-0 list-none flex-wrap pb-4 flex-row">
                    <li class="-mb-px mr-2 last:mr-0 flex-auto text-center">
                        <a href="{{ route('my_trips', ['lang' => $selectedLanguage->abbreviation]) }}" class="text-2xl font-FuturaMdCnBT font-medium px-5 py-2 shadow-lg rounded block border-blue-600 border leading-normal text-white bg-blue-600 cursor-pointer">
                            @isset($tripsPage->passenger_trips_heading)
                                {{ $tripsPage->passenger_trips_heading }}
                            @endisset
                        </a>
                    </li>
                    <li class="-mb-px mr-2 last:mr-0 flex-auto text-center">
                        <a href="{{ route('my_rides', ['lang' => $selectedLanguage->abbreviation]) }}" class="text-2xl font-FuturaMdCnBT font-medium px-5 py-2 shadow-lg rounded block border-gray-100 border leading-normal text-blue-600 bg-white cursor-pointer">
                            @isset($tripsPage->driver_rides_heading)
                            {{ $tripsPage->driver_rides_heading }}
                        @endisset
                        </a>
                    </li>
                </ul>
                <ul class="flex mb-0 list-none flex-wrap pt-3 pb-4 flex-row">
                    <li class="-mb-px mr-2 last:mr-0 flex-auto text-center">
                        <a href="{{ route('my_trips', ['lang' => $selectedLanguage->abbreviation]) }}" class="text-lg font-FuturaMdCnBT font-medium px-5 py-2 shadow-lg rounded block border-gray-100 border leading-normal text-blue-600 bg-white cursor-pointer">
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
                        <a href="{{ route('cancelled_trips', ['lang' => $selectedLanguage->abbreviation]) }}" class="text-lg font-FuturaMdCnBT font-medium px-5 py-2 shadow-lg rounded block border-blue-600 border leading-normal text-white bg-blue-600 cursor-pointer">
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
                                            @endphp
                                            <div class="relative even:bg-gray-100 odd:bg-white">
                                                <a href="{{ route('ride_detail', ['lang' => $selectedLanguage->abbreviation, 'departure' => $booking->departure, 'destination' => $booking->destination, 'id' => $booking->ride->id]) }}">
                                                    <div style="cursor:pointer;" onclick="window.location=''">
                                                    <div class="rounded-lg shadow-3xl border-[3px] border-solid  border-gray-100 " id="ride-29">
                                                        <div class="flex items-center justify-between pb-0 p-4">
                                                            <p class="flex items-center space-x-2 font-semibold">
                                                                {{ \Carbon\Carbon::parse($booking->ride->date)->format('F d, Y') }}
                                                                @isset($rideDetailPage->card_section_at_label)
                                                                    {{ $rideDetailPage->card_section_at_label }}
                                                                @endisset
                                                                {{ \Carbon\Carbon::parse($booking->ride->time)->format('h:i A') == '12:00 PM' ? '12 noon' : (\Carbon\Carbon::parse($booking->ride->time)->format('h:i A') == '12:00 AM' ? '12 midnight' : \Carbon\Carbon::parse($booking->ride->time)->format('h:i A')) }}
                                                            </p>
                                                        </div>
                                                        <div class="flex flex-col md:flex-row justify-between px-4 pb-4 md:pb-0">
                                                            <div class="w-full md:w-2/3 order-2 md:order-1">
                                                                <div class="relative mt-3 md:mt-5 text-left">
                                                                    <div class="flex items-center relative">
                                                                        <div class="border-r-2 border-black border-solid absolute h-full left-2 md:left-6 top-2 z-10">
                                                                            <span
                                                                            class="bg-primary rounded-full w-7 h-7 -top-[2px] -ml-[13px] absolute flex justify-center items-center">
                                                                                <img class="w-5 h-5 object-contain" src="{{ asset('./images/new-21-search-bar-from.png')}}" alt=""> 
                                                                            </span>
                                                                        </div>
                                                                        <div class="ml-10 md:ml-20">
                                                                            <div class="font-bold text-black">
                                                                                @isset($rideDetailPage->card_section_from_label)
                                                                                {{ $rideDetailPage->card_section_from_label }}
                                                                            @endisset
                                                                            </div>
                                                                            <div class="text-primary md:mb-4">{{ $from }} {{ $booking->ride->pickup }}</div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="flex items-center relative">
                                                                        <div class="border-r-2 border-black border-solid absolute h-0 left-2 md:left-5 top-2 z-10">
                                                                            <span class="bg-gray-200 rounded-full w-7 h-7 -top-[6px] -ml-[13px] md:-ml-[9px] absolute flex justify-center items-center">
                                                                                <img class="w-5 h-5 object-contain" src="{{ asset('./images/new-21-search-bar-to.png')}}" alt="">
                                                                            </span>
                                                                        </div>
                                                                        <div class="ml-10 md:ml-20">
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
                                                                <p class="text-lg md:text-xl font-semibold text-primary">$ {{ number_format(floatval($booking->price), 2) }} <small>
                                                                    @isset($rideDetailPage->card_section_per_seat)
                                                                    {{ $rideDetailPage->card_section_per_seat }}
                                                                @endisset
                                                                </small></p>
                                                            </div>
                                                        </div>
                                                        <div class="border-t border-gray-300 grid grid-cols-2 divide-x divide-gray-300">
                                                            <div class="flex items-center justify-center p-2 md:p-4">
                                                                <p class="">
                                                                    {{ $booking->seats }}
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
                                                        <div class="border-t border-gray-300 flex items-center justify-between p-4 w-full">
                                                                <div class="flex items-center space-x-2">
                                                                    <div class=" flex-initial">
                                                                    <div class="w-12 h-12 rounded-full overflow-hidden">
                                                                        <img class="w-full h-full object-cover" src="{{ $booking->ride->driver->profile_image }}" alt="">
                                                                    </div>
                                                                    </div>
                                                                    <div class=" flex-auto">
                                                                    <div class="text-center flex flex-wrap md:block">
                                                                        <p class="font-semibold">
                                                                            @if ($booking->ride->driver->type === '2')
                                                                                {{ $booking->ride->driver->last_name }}
                                                                            @elseif ($booking->ride->driver->type === '3')
                                                                                {{ $booking->ride->driver->first_name }} {{ $booking->ride->driver->last_name }}
                                                                            @else
                                                                                {{ $booking->ride->driver->first_name }}
                                                                            @endif
                                                                        </p>
                                                                        @php
                                                                            // Calculate the age based on the driver's date of birth
                                                                            $dob = \Carbon\Carbon::parse($booking->ride->driver->dob);
                                                                            $age = $dob->diffInYears(\Carbon\Carbon::now());
                                                                        @endphp
                                                                        <div class="flex items-center flex-wrap gap-2">
                                                                            <p class="mb-0 text-sm font-medium border-r border-gray-600 pr-2">
                                                                                @isset($rideDetailPage->card_section_age)
                                                                                    {{ $rideDetailPage->card_section_age }}
                                                                                @endisset
                                                                                : {{ $age ?? 0 }}
                                                                            </p>
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
                                                                                    return $rating->ride->added_by === $booking->ride->added_by;
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
                                                            </div>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                        @endforeach
                                        {{ $bookings->links() }}
                                    @else
                                        <p>You have no cancelled trips</p>
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
