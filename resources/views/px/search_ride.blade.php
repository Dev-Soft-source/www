@extends('layouts.template')

@section('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endsection

@section('content')
    @php
        $searchRoute = route('px.search_ride', ['lang' => optional($selectedLanguage)->abbreviation]);
        $bookingMethodGroup = $searchOptionGroups->get('booking_method');
        $luggageGroup = $searchOptionGroups->get('luggage_size');
        $preferenceGroup = $searchOptionGroups->get('preference');
        $smokingGroup = $searchOptionGroups->get('smoking_allowed');
        $petsGroup = $searchOptionGroups->get('pets_allowed');
        $coPassengerOptionCodes = ['min_rating_5', 'min_rating_4', 'min_rating_3', 'existing_reviews_only'];
        $extraOptionCodes = ['pink_rides', 'extra_plus_rides'];
        $vehicleTypes = [
            'Convertable',
            'Coupe',
            'Hatchback',
            'Minivan',
            'Sedan',
            'Station wagon',
            'SUV',
            'Truck',
            'Van',
        ];
        $showSearchResultsHeading = $hasSearch || $hasActiveFilters;
        $defaultResultsHeading = auth()->check() ? 'Recent Added Rides' : 'Upcoming Rides';
    @endphp

    <div class="container mx-auto my-6 md:px-8 xl:px-0">


        <form method="GET" action="{{ $searchRoute }}" id="px-search-form">
            <div class="mt-6 grid grid-cols-1 lg:grid-cols-4 gap-4">
                <div class="search-filter-container flex flex-col relative">
                    <button id="search-filter-toggle"
                        class="search-filter-toggle button-exp-fill flex items-center justify-center ml-auto gap-1 w-40 shadow lg:hidden">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" />
                        </svg>
                        <span class="text-xl">{{ $siteText['search_filters_btn_text'] ?? 'Search filters' }}</span>
                    </button>

                    <div id="search-filter-overlay" class="fixed inset-0 bg-black bg-opacity-50 hidden z-40 lg:hidden">
                    </div>
                    <div id="search-filter"
                        class="search-filter fixed top-0 right-0 h-full overflow-y-auto bg-white w-11/12 sm:w-96 lg:w-full transform translate-x-full lg:translate-x-0 lg:static lg:shadow-3xl lg:h-auto transition-transform duration-300 z-40">
                        <button id="search-filter-close"
                            class="search-filter-close border w-6 h-6 overflow-hidden flex items-center justify-center border-gray-500 rounded-full text-gray-500 text-3xl absolute top-3 right-4 hover:text-red-500 lg:hidden">
                            &times;
                        </button>

                        <input type="hidden" name="origin[label]" value="{{ $oldOriginLabel ?? '' }}">
                        <input type="hidden" name="origin[city_id]" value="{{ $oldOriginCityId ?? '' }}">
                        <input type="hidden" name="destination[label]" value="{{ $oldDestinationLabel ?? '' }}">
                        <input type="hidden" name="destination[city_id]" value="{{ $oldDestinationCityId ?? '' }}">
                        <input type="hidden" name="departure_date" value="{{ $oldDepartureDate }}">
                        <input type="hidden" name="keyword" value="{{ $oldKeyword ?? '' }}">
                        <input type="hidden" name="search"
                            value="{{ !empty($oldOriginLabel) || !empty($oldDestinationLabel) || !empty($oldKeyword) ? 1 : 0 }}">

                        <div
                            class="rounded-t-lg bg-primary text-white font-medium text-xl flex items-center justify-center space-x-2 p-4">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" />
                            </svg>
                            <span>{{ $findRidePage->filter_section_heading ?? ($siteText['search_filters_text'] ?? 'Search Filters') }}</span>
                        </div>

                        <div class="bg-white p-4 space-y-4">
                            <div class="divide-y">

                                @php
                                    $pinkRideLabel = $findRidePage->search_section_pink_ride_label ?? 'Pink Ride';
                                    $extraRideLabel = $findRidePage->search_section_extra_care_label ?? 'Extra+ Ride';

                                    $pinkRideLabel =
                                        $preferenceGroup->options->firstWhere('code', 'pink_rides')->display_label ??
                                        $pinkRideLabel;
                                    $extraRideLabel =
                                        $preferenceGroup->options->firstWhere('code', 'extra_plus_rides')
                                            ->display_label ?? $extraRideLabel;
                                @endphp


                                <label class="flex items-center justify-between p-3">
                                    <span class="text-pink-500 text-base md:text-lg">{{ $pinkRideLabel }}</span>
                                    <input type="checkbox" name="women_only" value="1" class="w-4 h-4"
                                        @checked(!empty($searchFilters['women_only']))>
                                </label>
                                <label class="flex items-center justify-between p-3">
                                    <span class="text-green-600 text-base md:text-lg">{{ $extraRideLabel }}</span>
                                    <input type="checkbox" name="extra_care" value="1" class="w-4 h-4"
                                        @checked(!empty($searchFilters['extra_care']))>
                                </label>
                                <label class="flex items-center justify-between p-3">
                                    <span class="text-base md:text-lg text-gray-900">Hide Full Rides</span>
                                    <input type="checkbox" name="hide_full_rides" value="1" class="w-4 h-4"
                                        @checked(!empty($searchFilters['hide_full_rides']))>
                                </label>
                            </div>



                            <div class="space-y-3">
                                <h3 class="text-primary text-2xl xl:text-3xl">
                                    {{ $findRidePage->filter1_driver_heading ?? 'Driver' }}</h3>
                                <div>
                                    <label for="driverAge"
                                        class="block mb-2 font-medium text-gray-900">{{ $findRidePage->driver_age_label ?? 'Driver age' }}</label>
                                    <select id="driverAge" name="driver_age"
                                        class="bg-gray-100 text-base md:text-lg border-0 text-black rounded block w-full p-2.5">
                                        <option value="">{{ $findRidePage->driver_age_placeholder ?? 'Any age' }}
                                        </option>
                                        @foreach ([20, 30, 40, 50, 60] as $age)
                                            <option value="{{ $age }}" @selected((string) ($searchFilters['driver_age'] ?? '') === (string) $age)>
                                                {{ $age }}+</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="driverRating"
                                        class="block mb-2 font-medium text-gray-900">{{ $findRidePage->driver_rating_label ?? 'Driver rating' }}</label>
                                    <select id="driverRating" name="driver_rating"
                                        class="bg-gray-100 text-base md:text-lg border-0 text-black rounded block w-full p-2.5">
                                        <option value="">
                                            {{ $findRidePage->driver_rating_placeholder ?? 'Any rating' }}</option>
                                        @foreach ([3, 4, 4.5] as $rating)
                                            <option value="{{ $rating }}" @selected((string) ($searchFilters['driver_rating'] ?? '') === (string) $rating)>
                                                {{ $rating }}+</option>
                                        @endforeach
                                    </select>
                                </div>
                                <label class="flex items-center space-x-2">
                                    <input id="driverPhone" type="checkbox" name="driver_phone" value="1"
                                        class="w-4 h-4" @checked(!empty($searchFilters['driver_phone']))>
                                    <span>{{ $findRidePage->driver_phone_access_label ?? "Access to Driver's Phone Number" }}</span>
                                </label>
                                <div>
                                    <label for="driverName"
                                        class="block mb-2 font-medium text-gray-900">{{ $findRidePage->driver_know_label ?? 'Driver You Know' }}</label>
                                    <input id="driverName" type="text" name="driver_name"
                                        value="{{ $searchFilters['driver_name'] ?? '' }}"
                                        class="bg-gray-100 text-base md:text-lg border-0 text-black rounded block w-full p-2.5 italic"
                                        placeholder="{{ $findRidePage->driver_know_placeholder ?? "Enter Driver's Name" }}">
                                </div>
                            </div>

                            @if ($preferenceGroup && $preferenceGroup->options->isNotEmpty())
                                @php
                                    $coPassengerOptions = $preferenceGroup->options
                                        ->filter(function ($option) use ($coPassengerOptionCodes) {
                                            return in_array($option->code, $coPassengerOptionCodes, true);
                                        })
                                        ->values();
                                    $selectedCoPassengerOption =
                                        (string) (collect((array) ($searchFilters['ride_option_ids'] ?? []))->first() ??
                                            '');
                                @endphp
                                @if ($coPassengerOptions->isNotEmpty())
                                    <div class="space-y-3">
                                        <h3 class="text-primary text-2xl xl:text-3xl">
                                            {{ $findRidePage->filter2_passengers_heading ?? 'Co-passengers' }}</h3>
                                        <div>
                                            <label for="coPassengerRating"
                                                class="block mb-2 font-medium text-gray-900">{{ $findRidePage->passengers_rating_label ?? 'Co-passengers Rating' }}</label>
                                            <select id="coPassengerRating" name="ride_option_ids[]"
                                                class="bg-gray-100 text-base md:text-lg border-0 text-black rounded block w-full p-2.5">
                                                <option value="">
                                                    {{ $findRidePage->passengers_rating_placeholder ?? 'All' }}</option>
                                                @foreach ($coPassengerOptions as $option)
                                                    <option value="{{ $option->id }}" @selected($selectedCoPassengerOption === (string) $option->id)>
                                                        {{ str_replace('passengers', 'co-passengers', $option->display_label) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                @endif
                            @endif

                            @if ($bookingMethodGroup && $bookingMethodGroup->options->isNotEmpty())
                                <div class="space-y-3">
                                    <h3 class="text-primary text-2xl xl:text-3xl">
                                        {{ $findRidePage->filter3_payment_methods_heading ?? 'Payment methods' }}</h3>
                                    <div>
                                        <select id="bookingMethod" name="booking_method"
                                            class="bg-gray-100 text-base md:text-lg border-0 text-black rounded block w-full p-2.5">
                                            <option value="">
                                                {{ $findRidePage->payment_methods_label ?? 'Any payment method' }}</option>
                                            @foreach ($bookingMethodGroup->options as $option)
                                                <option value="{{ $option->id }}" @selected((string) ($searchFilters['booking_method'] ?? '') === (string) $option->id)>
                                                    {{ $option->display_label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @endif

                            <div class="space-y-3">
                                <h3 class="text-primary text-2xl xl:text-3xl">
                                    {{ $findRidePage->filter4_vehicle_heading ?? 'Vehicle' }}</h3>
                                <div>
                                    <select id="vehicleType" name="vehicle_type"
                                        class="bg-gray-100 text-base md:text-lg border-0 text-black rounded block w-full p-2.5">
                                        <option value="">
                                            {{ $findRidePage->vehicle_type_placeholder ?? 'Any vehicle type' }}</option>
                                        @foreach ($vehicleTypes as $type)
                                            <option value="{{ $type }}" @selected(($searchFilters['vehicle_type'] ?? '') === $type)>
                                                {{ $type }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            @if ($preferenceGroup && $preferenceGroup->options->isNotEmpty())
                                <div class="space-y-3">
                                    <h3 class="text-primary text-2xl xl:text-3xl">
                                        {{ $findRidePage->preference_label ?? 'Preference' }}</h3>
                                    <div class="border rounded-md overflow-hidden divide-y">
                                        @foreach ($preferenceGroup->options as $option)
                                            @continue(in_array($option->code, $coPassengerOptionCodes, true))
                                            @continue(in_array($option->code, $extraOptionCodes, true))
                                            <div class="flex items-start justify-between p-3">
                                                <label class="flex gap-2 text-base md:text-lg">
                                                    <input type="checkbox" name="ride_option_ids[]"
                                                        value="{{ $option->id }}" class="w-4 h-4 mt-1"
                                                        @checked(in_array($option->id, (array) ($searchFilters['ride_option_ids'] ?? [])))>
                                                    <span>{{ $option->display_label }}</span>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if ($luggageGroup && $luggageGroup->options->isNotEmpty())
                                <div class="space-y-3">
                                    <h3 class="text-primary text-2xl xl:text-3xl">
                                        {{ $findRidePage->luggage_label ?? 'Luggage' }}</h3>
                                    <div class="border rounded-md overflow-hidden divide-y">
                                        @foreach ($luggageGroup->options as $option)
                                            <div class="flex items-start justify-between p-3">
                                                <label class="flex items-center gap-2 text-base md:text-lg">
                                                    <input type="radio" name="luggage_size"
                                                        value="{{ $option->id }}" class="w-4 h-4"
                                                        @checked((string) ($searchFilters['luggage_size'] ?? '') === (string) $option->id)>
                                                    <span>{{ $option->display_label }}</span>
                                                </label>
                                            </div>
                                        @endforeach
                                        <div class="flex items-start justify-between p-3">
                                            <label class="flex items-center gap-2 text-base md:text-lg">
                                                <input type="radio" name="luggage_size" value="" class="w-4 h-4"
                                                    @checked(empty($searchFilters['luggage_size']))>
                                                <span>Any</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if ($smokingGroup && $smokingGroup->options->isNotEmpty())
                                <div class="space-y-3">
                                    <h3 class="text-primary text-2xl xl:text-3xl">
                                        {{ $findRidePage->smoking_label ?? 'Smoking' }}</h3>
                                    <div class="border rounded-md overflow-hidden divide-y">
                                        @foreach ($smokingGroup->options as $option)
                                            <div class="flex items-start justify-between p-3">
                                                <label class="flex items-center gap-2 text-base md:text-lg">
                                                    <input type="radio" name="smoking_allowed"
                                                        value="{{ $option->id }}" class="w-4 h-4"
                                                        @checked((string) ($searchFilters['smoking_allowed'] ?? '') === (string) $option->id)>
                                                    <span>{{ $option->display_label }}</span>
                                                </label>
                                            </div>
                                        @endforeach
                                        <div class="flex items-start justify-between p-3">
                                            <label class="flex items-center gap-2 text-base md:text-lg">
                                                <input type="radio" name="smoking_allowed" value=""
                                                    class="w-4 h-4" @checked(empty($searchFilters['smoking_allowed']))>
                                                <span>Any</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if ($petsGroup && $petsGroup->options->isNotEmpty())
                                <div class="space-y-3">
                                    <h3 class="text-primary text-2xl xl:text-3xl">
                                        {{ $findRidePage->pets_allowed_label ?? 'Pets' }}</h3>
                                    <div class="border rounded-md overflow-hidden divide-y">
                                        @foreach ($petsGroup->options as $option)
                                            <div class="flex items-start justify-between p-3">
                                                <label class="flex items-center gap-2 text-base md:text-lg">
                                                    <input type="radio" name="pets_allowed"
                                                        value="{{ $option->id }}" class="w-4 h-4"
                                                        @checked((string) ($searchFilters['pets_allowed'] ?? '') === (string) $option->id)>
                                                    <span>{{ $option->display_label }}</span>
                                                </label>
                                            </div>
                                        @endforeach
                                        <div class="flex items-start justify-between p-3">
                                            <label class="flex items-center gap-2 text-base md:text-lg">
                                                <input type="radio" name="pets_allowed" value="" class="w-4 h-4"
                                                    @checked(empty($searchFilters['pets_allowed']))>
                                                <span>Any</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="flex gap-3 pt-2">
                                <a href="{{ route('px.search_ride', ['lang' => optional($selectedLanguage)->abbreviation]) }}"
                                    class="button-exp-fill gap-2 w-full flex justify-center items-center">
                                    <span class="inline-flex items-center justify-center w-6 h-6 text-white">
                                        <svg fill="#ffffff" width="64px" height="64px" viewBox="0 0 32 32"
                                            id="icon" xmlns="http://www.w3.org/2000/svg" stroke="#000000"
                                            stroke-width="0.00032">
                                            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"
                                                stroke="#CCCCCC" stroke-width="0.384"></g>
                                            <g id="SVGRepo_iconCarrier">
                                                <defs>
                                                    <style>
                                                        .cls-1 {
                                                            fill: none;
                                                        }
                                                    </style>
                                                </defs>
                                                <polygon
                                                    points="30 11.414 28.586 10 24 14.586 19.414 10 18 11.414 22.586 16 18 20.585 19.415 22 24 17.414 28.587 22 30 20.587 25.414 16 30 11.414">
                                                </polygon>
                                                <path
                                                    d="M4,4A2,2,0,0,0,2,6V9.1709a2,2,0,0,0,.5859,1.4145L10,18v8a2,2,0,0,0,2,2h4a2,2,0,0,0,2-2V24H16v2H12V17.1709l-.5859-.5855L4,9.1709V6H24V8h2V6a2,2,0,0,0-2-2Z">
                                                </path>
                                                <rect id="_Transparent_Rectangle_"
                                                    data-name="&lt;Transparent Rectangle&gt;" class="cls-1"
                                                    width="32" height="32"></rect>
                                            </g>
                                        </svg>
                                    </span>
                                    <span class="text-xl">Clear</span>
                                </a>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="lg:col-span-3">
                    <div class="bg-gray-100 rounded-md p-4 py-6 lg:shadow-3xl">
                        <div class="text-center mb-4">
                            <h1 class="font-FuturaMdCnBT">
                                {{ $findRidePage->main_heading ?? 'Search PX Rides' }}
                            </h1>
                        </div>

                        <div class="px-search-shell flex flex-col md:flex-row md:items-stretch">
                            <div class="w-full md:w-[26.5%] px-search-segment px-search-divider">
                                <div class="h-full">
                                    @livewire(
                                        'px.city-autocomplete',
                                        [
                                            'field' => 'origin',
                                            'placeholder' => $findRidePage->search_section_from_placeholder ?? 'Origin',
                                            'initialLabel' => $oldOriginLabel,
                                            'initialCityId' => $oldOriginCityId,
                                            'invalidErrorMessage' => $siteText['invalid_city_error_text'] ?? 'Please select a valid city from the dropdown',
                                            'class' => 'h-full w-full border-0 bg-transparent pl-10 pr-4 font-semibold text-slate-900 placeholder-slate-900 focus:ring-0',
                                        ],
                                        key('px-search-origin')
                                    )
                                </div>
                            </div>

                            <div
                                class="w-full md:w-[4%] px-search-segment px-search-divider flex items-center justify-center bg-white">
                                <button type="button" onclick="swapLocations()"
                                    class="flex h-10 w-10 items-center justify-center rounded-full text-[#1677e6] transition hover:bg-blue-50">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M8.28001 11.7193C8.13939 11.5789 7.94876 11.5 7.75001 11.5C7.55126 11.5 7.36064 11.5789 7.22001 11.7193L3.22001 15.7193C3.07956 15.86 3.00067 16.0506 3.00067 16.2493C3.00067 16.4481 3.07956 16.6387 3.22001 16.7793L7.22001 20.7793C7.28867 20.853 7.37147 20.9121 7.46347 20.9531C7.55547 20.9941 7.65479 21.0162 7.75549 21.0179C7.85619 21.0197 7.95622 21.0012 8.04961 20.9635C8.143 20.9257 8.22783 20.8696 8.29905 20.7984C8.37027 20.7272 8.42641 20.6423 8.46413 20.5489C8.50186 20.4555 8.52038 20.3555 8.5186 20.2548C8.51683 20.1541 8.49479 20.0548 8.45379 19.9628C8.4128 19.8708 8.3537 19.788 8.28001 19.7193L5.56001 16.9993L17 16.9993C17.1989 16.9993 17.3897 16.9203 17.5303 16.7797C17.671 16.639 17.75 16.4483 17.75 16.2493C17.75 16.0504 17.671 15.8597 17.5303 15.719C17.3897 15.5784 17.1989 15.4993 17 15.4993L5.56001 15.4993L8.28001 12.7793C8.42046 12.6387 8.49935 12.4481 8.49935 12.2493C8.49935 12.0506 8.42046 11.86 8.28001 11.7193Z"
                                            fill="currentColor"></path>
                                        <path
                                            d="M15.77 12.2777C15.9106 12.4182 16.1012 12.4971 16.3 12.4971C16.4987 12.4971 16.6894 12.4182 16.83 12.2777L20.83 8.27773C20.9704 8.1371 21.0493 7.94648 21.0493 7.74773C21.0493 7.54898 20.9704 7.35835 20.83 7.21773L16.83 3.21773C16.7613 3.14404 16.6785 3.08494 16.5865 3.04395C16.4945 3.00296 16.3952 2.98092 16.2945 2.97914C16.1938 2.97736 16.0938 2.99589 16.0004 3.03361C15.907 3.07133 15.8222 3.12747 15.7509 3.19869C15.6797 3.26991 15.6236 3.35475 15.5859 3.44813C15.5481 3.54152 15.5296 3.64155 15.5314 3.74225C15.5332 3.84295 15.5552 3.94227 15.5962 4.03427C15.6372 4.12627 15.6963 4.20907 15.77 4.27773L18.49 6.99773L7.04998 6.99773C6.85106 6.99773 6.6603 7.07675 6.51965 7.2174C6.37899 7.35805 6.29998 7.54882 6.29998 7.74773C6.29998 7.94664 6.37899 8.13741 6.51965 8.27806C6.6603 8.41871 6.85106 8.49773 7.04998 8.49773L18.49 8.49773L15.77 11.2177C15.6295 11.3584 15.5506 11.549 15.5506 11.7477C15.5506 11.9465 15.6295 12.1371 15.77 12.2777Z"
                                            fill="currentColor"></path>
                                    </svg>
                                </button>
                            </div>

                            <div class="w-full md:w-[26.5%] px-search-segment px-search-divider">
                                <div class="h-full">
                                    @livewire(
                                        'px.city-autocomplete',
                                        [
                                            'field' => 'destination',
                                            'placeholder' => $findRidePage->search_section_to_placeholder ?? 'Destination',
                                            'initialLabel' => $oldDestinationLabel,
                                            'initialCityId' => $oldDestinationCityId,
                                            'invalidErrorMessage' => $siteText['invalid_city_error_text'] ?? 'Please select a valid city from the dropdown',
                                            'class' => 'h-full w-full border-0 bg-transparent pl-10 pr-4 font-semibold text-slate-900 placeholder-slate-900 focus:ring-0',
                                        ],
                                        key('px-search-destination')
                                    )
                                </div>
                            </div>

                            <div class="w-full md:w-[23%] px-search-segment px-search-divider bg-white">
                                <div class="relative h-full">
                                    <div class="absolute inset-y-0 start-0 flex items-center pl-4 pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                            fill="currentColor" aria-hidden="true">
                                            <path fill="#888888" fill-rule="evenodd"
                                                d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <input id="departure_date" name="departure_date" value="{{ $oldDepartureDate }}"
                                        type="text" readonly
                                        class="h-full w-full border-0 bg-transparent pl-10 pr-4 font-semibold text-slate-900 placeholder-slate-900 focus:ring-0"
                                        placeholder="{{ $findRidePage->search_section_date_placeholder ?? 'Select date' }}"
                                        autocomplete="off">
                                </div>
                                @error('departure_date')
                                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="w-full md:w-[20%]">
                                <button type="submit" name="search" value="1"
                                    class="px-search-submit w-full h-full flex items-center justify-center text-base font-semibold text-white transition-colors">
                                    Search
                                </button>
                            </div>
                        </div>
                        <div class="">
                            <div class="flex items-center gap-4 py-4">
                                <div class="h-px flex-1 bg-gray-300"></div>
                                <span class="text-sm font-semibold uppercase tracking-[0.2em] text-gray-500">or</span>
                                <div class="h-px flex-1 bg-gray-300"></div>
                            </div>
                            <div class="relative">
                                <input name="keyword" type="text"
                                    class="px-search-shell text-base md:text-lg w-full p-4 pr-12 font-semibold text-slate-900"
                                    placeholder="{{ $findRidePage->search_section_keyword_placeholder ?? 'Search notes, route, or stops' }}"
                                    value="{{ $oldKeyword ?? '' }}">
                                <button type="button" id="keyword-clear-button"
                                    class="absolute right-4 top-1/2 hidden -translate-y-1/2 text-gray-400 transition hover:text-gray-700"
                                    aria-label="Clear keyword">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                        fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd"
                                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                    </div>
                    <div class="my-0">
                        @if ($rides && $rides->count() > 0)
                            <h1 class="can-exp-h1 text-center font-FuturaMdCnBT text-primary mb-4 mt-4">
                                @if ($showSearchResultsHeading)
                                    {{ $findRidePage->heading_ride_card_section ?? 'Available Rides' }}
                                @else
                                    {{ $defaultResultsHeading }}
                                @endif
                                {{-- ({{ $rides->total() }}) --}}
                            </h1>

                            <div class="space-y-4">
                                @foreach ($rides as $ride)
                                    <x-px.ride-card :ride="$ride" :lang="optional($selectedLanguage)->abbreviation" detail-route="px.ride_detail"
                                        :show-status="false" :show-booking-info="false" :show-options="true" :price-minor="$ride->matched_segment_price_minor ?? $ride->price_minor" />
                                @endforeach
                            </div>
                            <div class="mt-6">
                                {{ $rides->appends(request()->query())->links() }}
                            </div>
                        @else
                            <div class="text-center py-12 bg-white rounded-lg shadow-md">
                                <p class="text-xl text-gray-600">
                                    @if ($showSearchResultsHeading)
                                        {{ $findRidePage->search_result_no_found_message ?? ($findRidePage->no_rides_found_message ?? 'No rides found matching your search criteria.') }}
                                    @else
                                        {{ auth()->check() ? 'No recent rides available.' : 'No upcoming rides available.' }}
                                    @endif
                                </p>
                            </div>
                        @endif

                        @if (!empty($recentSearches) && $recentSearches->count() > 0)
                            <div class="mt-10">
                                <h2 class="font-FuturaMdCnBT text-primary text-2xl md:text-3xl">
                                    {{ $findRidePage->search_section_recent_searches ?? 'Recent Searches' }}
                                </h2>

                                <div class="space-y-4 mt-4">
                                    @foreach ($recentSearches as $index => $recentSearch)
                                        @php
                                            $colors = [
                                                'bg-blue-50',
                                                'bg-green-50',
                                                'bg-yellow-50',
                                                'bg-pink-50',
                                                'bg-cyan-50',
                                            ];
                                            $colorClass = $colors[$index % count($colors)];
                                        @endphp

                                        <a href="{{ $recentSearch->search_url }}"
                                            class="{{ $colorClass }} block rounded-lg border border-gray-100 px-4 py-5 shadow-3xl transition-shadow duration-200 hover:shadow-xl">
                                            <div class="space-y-4">
                                                <div class="flex items-start gap-3">
                                                    <span
                                                        class="mt-1 inline-flex h-7 w-7 items-center justify-center rounded-full bg-primary text-white">A</span>
                                                    <div>
                                                        <div class="text-sm font-semibold text-gray-700">
                                                            {{ $findRidePage->search_section_from_placeholder ?? 'Origin' }}
                                                        </div>
                                                        <div class="text-primary">{{ $recentSearch->origin_label }}</div>
                                                    </div>
                                                </div>

                                                <div class="flex items-start gap-3">
                                                    <span
                                                        class="mt-1 inline-flex h-7 w-7 items-center justify-center rounded-full bg-gray-300 text-gray-800">B</span>
                                                    <div>
                                                        <div class="text-sm font-semibold text-gray-700">
                                                            {{ $findRidePage->search_section_to_placeholder ?? 'Destination' }}
                                                        </div>
                                                        <div class="text-primary">{{ $recentSearch->destination_label }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        flatpickr("#departure_date", {
            dateFormat: "Y-m-d",
            minDate: "today",
            enableTime: false,
        });

        document.addEventListener('DOMContentLoaded', function() {
            const getLocationInput = (fieldName) => document.querySelector(
                `input[name="${fieldName}"]:not([type="hidden"])`
            );

            const originInput = getLocationInput('origin[label]');
            const destinationInput = getLocationInput('destination[label]');
            const originCityIdInput = document.querySelector('input[name="origin[city_id]"]');
            const destinationCityIdInput = document.querySelector('input[name="destination[city_id]"]');
            const departureDateInput = document.querySelector('#departure_date');
            const keywordInputs = document.querySelectorAll('input[name="keyword"]');
            const keywordClearButton = document.getElementById('keyword-clear-button');
            const originComponent = originInput?.closest('[wire\\:id]');
            const destinationComponent = destinationInput?.closest('[wire\\:id]');
            const toggle = document.getElementById('search-filter-toggle');
            const close = document.getElementById('search-filter-close');
            const searchFilters = document.getElementById('search-filter');
            const overlay = document.getElementById('search-filter-overlay');
            let suppressKeywordClear = false;

            const getLivewireComponent = (element) => {
                const wireId = element?.getAttribute('wire:id');
                return wireId && window.Livewire ? window.Livewire.find(wireId) : null;
            };

            const getComponentHiddenCityId = (input) => {
                return input?.closest('[wire\\:id]')?.querySelector('input[type="hidden"][name$="[city_id]"]') ?? null;
            };

            const resetAutocomplete = (input, cityIdInput, component) => {
                const livewireComponent = getLivewireComponent(component);

                if (livewireComponent) {
                    livewireComponent.set('query', '');
                    livewireComponent.set('cityId', null);
                    livewireComponent.set('suggestions', []);
                    livewireComponent.set('errorMessage', null);
                } else if (input) {
                    input.value = '';
                }

                if (cityIdInput) {
                    cityIdInput.value = '';
                }
            };

            const focusField = (field) => {
                
                if (!field) {
                    return;
                }

                field.focus();
                if (typeof field.select === 'function') {
                    field.select();
                }
            };

            const focusNextFieldWhenSelected = (currentInput, nextFieldSelector, attempts = 6) => {
                window.setTimeout(() => {
                    const cityIdField = getComponentHiddenCityId(currentInput);
                    const nextField = document.querySelector(nextFieldSelector);

                    if (cityIdField?.value && nextField) {
                        focusField(nextField);
                        return;
                    }

                    if (attempts > 1) {
                        focusNextFieldWhenSelected(currentInput, nextFieldSelector, attempts - 1);
                    }
                }, 120);
            };

            const clearKeywordInputs = () => {
                if (suppressKeywordClear) {
                    return;
                }

                keywordInputs.forEach((input) => {
                    input.value = '';
                });
                syncKeywordClearButton();
            };

            const syncKeywordClearButton = () => {
                if (!keywordClearButton) {
                    return;
                }

                const hasKeywordValue = Array.from(keywordInputs).some((input) => input.value.trim() !== '');
                keywordClearButton.classList.toggle('hidden', !hasKeywordValue);
            };

            const clearLocationInputs = () => {
                suppressKeywordClear = true;
                resetAutocomplete(originInput, originCityIdInput, originComponent);
                resetAutocomplete(destinationInput, destinationCityIdInput, destinationComponent);

                suppressKeywordClear = false;
            };

            if (toggle && close && searchFilters && overlay) {
                const toggleSearchFilters = (show) => {
                    searchFilters.classList.toggle('translate-x-full', !show);
                    overlay.classList.toggle('hidden', !show);
                };

                toggle.addEventListener('click', () => toggleSearchFilters(true));
                close.addEventListener('click', () => toggleSearchFilters(false));
                overlay.addEventListener('click', () => toggleSearchFilters(false));
            }

            if (originInput) {
                originInput.addEventListener('input', clearKeywordInputs);
            }

            if (destinationInput) {
                destinationInput.addEventListener('input', clearKeywordInputs);
            }

            document.addEventListener('keydown', function(event) {
                if (event.key !== 'Enter') {
                    return;
                }

                const target = event.target;

                if (target?.matches('input[name="origin[label]"]:not([type="hidden"])')) {
                    focusNextFieldWhenSelected(
                        target,
                        'input[name="destination[label]"]:not([type="hidden"])'
                    );
                }

                if (target?.matches('input[name="destination[label]"]:not([type="hidden"])')) {
                    focusNextFieldWhenSelected(
                        target,
                        '#departure_date'
                    );
                }
            });

            keywordInputs.forEach((input) => {
                input.addEventListener('input', function() {
                    clearLocationInputs();
                    syncKeywordClearButton();
                });
            });

            if (keywordClearButton) {
                keywordClearButton.addEventListener('click', function() {
                    const searchForm = document.getElementById('px-search-form');

                    keywordInputs.forEach((input) => {
                        input.value = '';
                        input.dispatchEvent(new Event('input', {
                            bubbles: true
                        }));
                    });

                    syncKeywordClearButton();

                    if (searchForm) {
                        if (typeof searchForm.requestSubmit === 'function') {
                            searchForm.requestSubmit();
                        } else {
                            searchForm.submit();
                        }
                        return;
                    }

                    keywordInputs[0]?.focus();
                });
            }

            syncKeywordClearButton();
        });

        function swapLocations() {
            const originInput = document.querySelector('input[name="origin[label]"]:not([type="hidden"])');
            const destinationInput = document.querySelector('input[name="destination[label]"]:not([type="hidden"])');
            const originComponent = originInput?.closest('[wire\\:id]');
            const destinationComponent = destinationInput?.closest('[wire\\:id]');
            const originCityIdInput = originComponent?.querySelector('input[name="origin[city_id]"]');
            const destinationCityIdInput = destinationComponent?.querySelector('input[name="destination[city_id]"]');
            const originCityId = originCityIdInput?.value ? parseInt(originCityIdInput.value, 10) : null;
            const destinationCityId = destinationCityIdInput?.value ? parseInt(destinationCityIdInput.value, 10) : null;
            const originLabel = originInput?.value ?? '';
            const destinationLabel = destinationInput?.value ?? '';

            if (window.Livewire && originComponent && destinationComponent) {
                const originWireId = originComponent.getAttribute('wire:id');
                const destinationWireId = destinationComponent.getAttribute('wire:id');

                if (originWireId && destinationWireId) {
                    try {
                        const originLivewire = window.Livewire.find(originWireId);
                        const destinationLivewire = window.Livewire.find(destinationWireId);

                        if (originLivewire && destinationLivewire) {
                            if (destinationCityId) {
                                originLivewire.call('selectCity', destinationCityId);
                            } else {
                                originLivewire.set('query', destinationLabel);
                                originLivewire.set('cityId', null);
                                originLivewire.set('suggestions', []);
                                originLivewire.set('errorMessage', null);
                            }

                            if (originCityId) {
                                destinationLivewire.call('selectCity', originCityId);
                            } else {
                                destinationLivewire.set('query', originLabel);
                                destinationLivewire.set('cityId', null);
                                destinationLivewire.set('suggestions', []);
                                destinationLivewire.set('errorMessage', null);
                            }
                        }
                    } catch (e) {
                        if (originInput && destinationInput) {
                            originInput.value = destinationLabel;
                            destinationInput.value = originLabel;

                            if (originCityIdInput) {
                                originCityIdInput.value = destinationCityId ?? '';
                            }
                            if (destinationCityIdInput) {
                                destinationCityIdInput.value = originCityId ?? '';
                            }

                            originInput.dispatchEvent(new Event('input', {
                                bubbles: true
                            }));
                            destinationInput.dispatchEvent(new Event('input', {
                                bubbles: true
                            }));
                        }
                    }
                }
            } else {
                if (originInput && destinationInput) {
                    originInput.value = destinationLabel;
                    destinationInput.value = originLabel;
                    originInput.dispatchEvent(new Event('input', {
                        bubbles: true
                    }));
                    destinationInput.dispatchEvent(new Event('input', {
                        bubbles: true
                    }));
                }
            }
        }
    </script>
@endsection
