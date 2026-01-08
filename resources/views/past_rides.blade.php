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
                        <a href="{{ route('my_trips', ['lang' => $selectedLanguage->abbreviation]) }}" class="text-2xl font-FuturaMdCnBT px-5 py-2 shadow-lg rounded block border-gray-100 border leading-normal text-blue-600 bg-white cursor-pointer">
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
                        <a href="{{ route('my_rides', ['lang' => $selectedLanguage->abbreviation]) }}" class="text-lg font-FuturaMdCnBT font-medium px-5 py-2 shadow-lg rounded block border-gray-100 border leading-normal text-blue-600 bg-white cursor-pointer">
                            @isset($tripsPage->upcoming_label)
                            {{ $tripsPage->upcoming_label }}
                        @endisset
                        </a>
                    </li>
                    <li class="-mb-px mr-2 last:mr-0 flex-auto text-center">
                        <a href="{{ route('past_rides', ['lang' => $selectedLanguage->abbreviation]) }}" class="text-lg font-FuturaMdCnBT font-medium px-5 py-2 shadow-lg rounded block border-blue-600 border leading-normal text-white bg-blue-600 cursor-pointer">
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
                            <div class="block" id="tab-options">
                                <div class="space-y-4">
                                    @if (!empty($pastRides) && count($pastRides) > 0)
                                        @foreach ($pastRides as $ride)
                                            @php
                                                 $from = $ride->rideDetail[0]->departure;
                                                 $to = $ride->rideDetail[0]->destination;
                                            @endphp
                                            <div class="relative even:bg-gray-100 odd:bg-white">
                                                <div class="">
                                                    {{-- <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 -mt-4 cursor-pointer ride-remove-btn" data-ride-id="29">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg> --}}

                                                </div>
                                                <a href="{{ route('my_ride_detail', ['lang' => $selectedLanguage->abbreviation, 'departure' => $ride->rideDetail[0]->departure, 'destination' => $ride->rideDetail[0]->destination, 'id' => $ride->id]) }}">
                                                    <div class="rounded-lg shadow-3xl border-[3px] border-solid border-gray-100 " id="ride-29">
                                                        <div class="flex flex-col md:flex-row gap-2 items-start justify-between pb-0 p-4">
                                                            <p class="flex items-center space-x-2 w-full font-semibold text-left">
                                                                {{ \Carbon\Carbon::parse($ride->date)->format('F d, Y') }}
                                                                @isset($rideDetailPage->card_section_at_label)
                                                                    {{ $rideDetailPage->card_section_at_label }}
                                                                @endisset
                                                                {{ \Carbon\Carbon::parse($ride->time)->format('h:i A') == '12:00 PM' ? '12 noon' : (\Carbon\Carbon::parse($ride->time)->format('h:i A') == '12:00 AM' ? '12 midnight' : \Carbon\Carbon::parse($ride->time)->format('h:i A')) }}
                                                            </p>
                                                            <div class="flex items-center justify-end w-full gap-2">

                                                                {{-- <p class="font-medium">
                                                                    {{ intval($ride->seats) - intval($ride->bookings()->where('status', '<>', 3)->where('status', '<>', 4)->whereHas('passenger', function($query) { $query->whereNull('deleted_at'); })->sum('seats')) }} seats left</p> --}}
                                                                    <div class="w-fit px-2 py-1 rounded bg-green-100 text-sm text-green-600">
                                                                        Completed
                                                                    </div>
                                                            </div>
                                                        </div>
                                                        <div class="flex flex-col md:flex-row justify-between px-4 pb-4 md:pb-0">
                                                            <div class="w-full md:w-2/3 order-2 md:order-1">
                                                                <div class="relative mt-3 md:mt-5 text-left">
                                                                    <div class="flex items-center relative">
                                                                        <div class="border-r-2 border-black border-solid absolute h-full left-2 md:left-6 top-2 z-10">
                                                                            <span class="bg-primary rounded-full w-7 h-7 -top-[2px] -ml-[13px] absolute flex justify-center items-center">
                                                                                <img class="w-5 h-5 object-contain" src="{{ asset('./images/new-21-search-bar-from.png')}}" alt=""> 
                                                                            </span>
                                                                        </div>
                                                                        <div class="ml-10 md:ml-20">
                                                                            <div class="font-bold text-black">
                                                                                @isset($rideDetailPage->card_section_from_label)
                                                                                {{ $rideDetailPage->card_section_from_label }}
                                                                            @endisset
                                                                            </div>
                                                                            <div class="text-primary md:mb-4">{{ $from }} {{ $ride->pickup }}</div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="flex items-center relative">
                                                                        <div class="border-r-2 border-black border-solid absolute h-0 left-2 md:left-6 top-2 z-10">
                                                                            <span class="bg-gray-200 rounded-full w-7 h-7 -top-[6px] -ml-[13px] absolute flex justify-center items-center">
                                                                                <img class="w-5 h-5 object-contain" src="{{ asset('./images/new-21-search-bar-to.png')}}" alt="">
                                                                            </span>
                                                                        </div>
                                                                        <div class="ml-10 md:ml-20">
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
                                                            <div class="mt-1 md:mt-4 order-1 md:order-2">
                                                                <div class="pr-8">
                                                                    <p class="font-medium">
                                                                        Total {{ $ride->seats }} seats</p>
                                                                </div>
                                                                <p class="text-lg md:text-xl font-semibold text-primary">${{ number_format(floatval($ride->rideDetail[0]->price), 2) }} <small>
                                                                    @isset($rideDetailPage->card_section_per_seat)
                                                                    {{ $rideDetailPage->card_section_per_seat }}
                                                                @endisset
                                                                </small></p>
                                                            </div>
                                                        </div>
                                                        <div class="border-t border-gray-300 grid sm:grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-gray-300">
                                                            <div class="flex items-center justify-between p-2 md:p-4">
                                                                <p class="font-semibold">
                                                                    @isset($rideDetailPage->card_section_booked)
                                                                    {{ $rideDetailPage->card_section_booked }}
                                                                @endisset
                                                                : </p>
                                                                <p class="">
                                                                    {{ $ride->bookings()->where('status', '<>', 3)->where('status', '<>', 4)->whereHas('passenger', function($query) { $query->whereNull('deleted_at'); })->sum('seats') }}
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
                                                                        ${{ number_format(floatval($ride->bookings()->where('status', '<>', 3)->where('status', '<>', 4)->whereHas('passenger', function($query) { $query->whereNull('deleted_at'); })->sum('booking_credit')), 2) }}
                                                                    </p>
                                                                </div>

                                                                <div class="flex items-center justify-between">
                                                                    <p class="font-semibold">
                                                                        @isset($rideDetailPage->card_section_amount)
                                                                        {{ $rideDetailPage->card_section_amount }}
                                                                    @endisset
                                                                        : </p>
                                                                    <p class="">
                                                                        ${{ number_format(floatval(($ride->bookings()->where('status', '<>', 3)->where('status', '<>', 4)->whereHas('passenger', function($query) { $query->whereNull('deleted_at'); })->sum('seats') * $ride->rideDetail[0]->price) + $ride->bookings->where('status', '<>', 3)->where('status', '<>', 4)->sum('booking_credit')), 2) }}
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
                                                        <div class="border-t border-gray-300 p-4">
                                                            <div class="pb-4">
                                                                @if (count($ride->bookings->where('status', '<>', 3)->where('status', '<>', 4)) > 0)
                                                                    <a href="{{ route('my_passengers', ['lang' => $selectedLanguage->abbreviation, 'departure' => $ride->rideDetail[0]->departure, 'destination' => $ride->rideDetail[0]->destination, 'id' => $ride->id]) }}">Review passenger</a>
                                                                @endif
                                                            </div>
                                                            @if (count($ride->bookings->where('status', '<>', 3)->where('status', '<>', 4)) > 0)
                                                                <div class="flex no-scrollbar overflow-x-auto items-center space-x-2">
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
                                                            @endif
                                                        </div>
                                                    </div>
                                                </a>

                                            </div>
                                        @endforeach
                                        {{ $pastRides->links() }}
                                    @else
                                        <p>You have no completed rides</p>
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
