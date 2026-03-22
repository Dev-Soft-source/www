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
                @include('layouts.inc.trips_tabs')
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
                                                <div class="absolute right-4 top-32 md:top-28">
                                                    @php
                                                        // Calculate the difference in days between today and the ride's date
                                                        $rideDateTime = new DateTime($booking->ride->date . ' ' . $booking->ride->time);
                                                        // Add the leave review days to the ride's DateTime
                                                        $reviewDateTime = clone $rideDateTime;
                                                        $reviewDateTime->add(new DateInterval('P' . $setting->leave_review_days . 'D'));

                                                        // Get current DateTime
                                                        $now = new DateTime();

                                                        // Check if the current DateTime is before the review DateTime
                                                        $reviewButtonVisible = $now < $reviewDateTime;
                                                    @endphp
                                                    @php
                                                        $reviewed = false; // Flag to track if any rating meets the conditions
                                                    @endphp
                                                    <!-- Loop through ratings associated with this booking -->
                                                    @foreach($booking->ride->ratings as $rating)
                                                        @if ($rating->posted_by === auth()->user()->id && $rating->type === '1' && $rating->ride_id === $booking->ride->id)
                                                            @php
                                                                $reviewed = true; // Set the flag to true if a matching rating is found
                                                                $review = $rating
                                                            @endphp
                                                            <!-- If at least one matching rating is found, break out of the loop -->
                                                            @break
                                                        @endif
                                                    @endforeach

                                                    <!-- Display button based on the flag value -->
                                                    @if ($reviewed)
                                                        <div>
                                                            <a href="{{ route('review_passenger.index', ['lang' => $selectedLanguage->abbreviation, 'id' => $rating->id]) }}" class="button-exp-fill me-1">
                                                                @isset($rideDetailPage->card_section_review)
                                                                {{ $rideDetailPage->card_section_review }}
                                                                @endisset
                                                            </a>
                                                        </div>
                                                    @elseif ($reviewButtonVisible)
                                                        @isset($booking->uuid)
                                                            <!-- Show 'Review' button if no matching rating is found -->
                                                            <a href="{{ route('review_driver', ['lang' => $selectedLanguage->abbreviation, 'id' => $booking->uuid]) }}" class="button-exp-fill me-1">
                                                                {{--  Review your driver  --}}
                                                                @isset($rideDetailPage->trips_card_section_review_driver)
                                                                    {{ $rideDetailPage->trips_card_section_review_driver }}
                                                                @endisset
                                                            </a>
                                                        @endisset
                                                    @endif
                                                </div>
                                                <a href="{{ route('ride_detail', ['lang' => $selectedLanguage->abbreviation, 'departure' => $booking->departure, 'destination' => $booking->destination, 'id' => $booking->ride->id]) }}">
                                                    <div style="cursor:pointer;" onclick="window.location=''" style="cursor:pointer;">
                                                    <div class=" rounded-lg shadow-3xl border-[3px] border-solid  border-gray-100 " id="ride-29">
                                                        <div class="grid grid-cols-5 gap-4 p-4 items-start">
                                                            @php
                                                                $bookingSegment = $booking->ride_detail_id
                                                                    ? $booking->ride->rideDetail->firstWhere('id', $booking->ride_detail_id)
                                                                    : $booking->ride->rideDetail->first(fn($d) => (string) $d->departure === (string) $booking->departure);
                                                                $displayDt = $bookingSegment
                                                                    ? (($bookingSegment->date ?? $booking->ride->date) . ' ' . ($bookingSegment->time ?? $booking->ride->time ?? '00:00'))
                                                                    : ($booking->ride->date . ' ' . ($booking->ride->time ?? '00:00'));
                                                                $departureDateTime = formatDepartureDateTime($displayDt, $selectedLanguage ?? null, $rideDetailPage ?? null);
                                                                $departureDateLabel = $departureDateTime['dateLabel'];
                                                                $departureTimeLabel = $departureDateTime['timeLabel'];

                                                                $rideForRoute = $booking->ride;
                                                                $orderedStops = ($rideForRoute->rideStops ?? collect())->sortBy('stop_order')->values();
                                                                $matchedFromStopIndex =
                                                                    isset($rideForRoute->matched_from_stop_index) &&
                                                                    $rideForRoute->matched_from_stop_index !== null
                                                                        ? (int) $rideForRoute->matched_from_stop_index
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
                                                            <div class="col-span-3">
                                                                <div class="flex flex-row items-center flex-wrap gap-2">
                                                                    <p class="flex items-center space-x-2 font-semibold">
                                                                        {{ $departureDateLabel }}
                                                                        {{ $rideDetailPage->card_section_at_label ?? $rideDetailPage->at_label ?? 'at' }}
                                                                        {{ $departureTimeLabel ?? 'N/A' }}
                                                                    </p>
                                                                    @if ($booking->ride->isPinkRide())
                                                                        <img class="w-12 h-12 ml-2" src="{{ asset('home_page_icons/' . $postRidePage->features_option1->icon) }}" alt="">
                                                                    @endif
                                                                    @if ($booking->ride->isExtraCareRide())
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
                                                                            <h4 class="flex gap-2 items-baseline text-xl text-black">
                                                                                {{ $rideDetailPage->card_section_from_label ?? 'From' }}
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
                                                                                    {{ $booking->ride->pickup }}
                                                                                </p>
                                                                            </div>
                                                                        </div>
                                                                        @if ($booking->ride->rideStops->isNotEmpty() && $booking->ride->rideStops->count() > 2)
                                                                            <div class="ml-12 md:ml-20 flex">
                                                                                <label class="text-xl text-black">Stops on the way</label>
                                                                                <ul class="flex flex-col gap-2 text-sm ml-4 mt-1 mb-4">
                                                                                    @foreach ($booking->ride->rideStops as $stop)
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
                                                                                {{ $rideDetailPage->card_section_to_label ?? 'To' }}
                                                                            </h4>
                                                                            <div class="flex gap-2 items-baseline">
                                                                                <h3
                                                                                    class="text-primary font-FuturaMdCnBT text-xl md:text-2xl md:mb-4">
                                                                                    {{ $to }}.
                                                                                </h3>
                                                                                <label class="text-black">
                                                                                    {{ $rideDetailPage->dropoff_at_label ?? 'Drop-off at' }}:
                                                                                </label>
                                                                                <p class="">
                                                                                    {{ $booking->ride->dropoff }}
                                                                                </p>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="col-span-2 px-4">
                                                                <div class="grid justify-end mt-4">
                                                                    <div class="flex items-center justify-end gap-2 mb-2">
                                                                        <span class="text-green-600 p-1 px-2 rounded text-sm bg-green-100">
                                                                            Completed
                                                                        </span>
                                                                    </div>
                                                                    <div class="pr-8">
                                                                        <p class="font-medium">
                                                                            {{ str_replace(':count', $booking->ride->seats, $rideDetailPage->total_seats_label ?? 'Total :count seats') }}
                                                                        </p>
                                                                    </div>
                                                                    <p class="text-xl font-semibold text-primary">
                                                                        ${{ number_format(floatval($booking->price / 100), 2) }}
                                                                        <small>
                                                                            {{ $rideDetailPage->card_section_per_seat ?? '' }}
                                                                        </small>
                                                                    </p>
                                                                </div>
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
                                                            @include('partials.ride_feature_icons', [
                                                                'rideFeatures' => $booking->ride->features,
                                                                'postRidePage' => $postRidePage,
                                                            ])
                                                        </div>
                                                        @if ($booking->ride->driver)
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
                                                                        <div class="flex flex-wrap items-center gap-2">
                                                                            <p class="mb-0 text-sm font-medium border-r border-gray-600 pr-2">
                                                                                @isset($rideDetailPage->card_section_age)
                                                                                {{ $rideDetailPage->card_section_age }}
                                                                            @endisset
                                                                            : {{ $age }}</p>
                                                                            <p class="mb-0 text-sm font-medium border-r border-gray-600 pr-2">{{ ucfirst($booking->ride->driver?->gender) }}</p>
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
                                                                                {{ $rideDetailPage->card_section_review }}
                                                                            @endisset
                                                                            : {{ number_format($totalAverage, 1) }}</p>
                                                                        </div>
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
                                        <p>{{ $tripsPage->no_completed_trips_label ?? 'You have no completed trips' }}</p>
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