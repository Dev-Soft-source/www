@extends('layouts.template')

@section('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endsection

@section('content')
    @if (session('success'))
    <div id="my-modal" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="relative flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeModal()"></div>
                <div
                    class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg z-20 modal-border">
                    <button onclick="closeModal()" class="absolute top-2 right-2 p-1 rounded-full hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-400">
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
                        <div class="mt-2 w-full">
                          <p class="can-exp-p text-center">{!! session('success') !!}</p>
                        </div>
                    </div>
                        <div class="px-4 pb-6 pt-4 sm:flex sm:flex-row-reverse sm:px-6 justify-center">
                            <a href=""
                                class="inline-flex w-full justify-center rounded bg-greenXS px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-greenXS sm:ml-3 sm:w-24">Close</a>
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
                                <p class="can-exp-p text-center">{!! session('failure') !!}</p>
                            </div>
                        </div>
                    </div>
                    <div class="px-4 pb-6 pt-4 sm:flex sm:flex-row-reverse sm:px-6 justify-center">
                        <a href=""
                            class="inline-flex w-full justify-center rounded bg-red-500 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-red-400 sm:ml-3 sm:w-24">Close</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="container mx-auto my-14 px-4 md:px-8 xl:px-0">
        <div class="border-b border-gray-400">
            <div class="flex gap-2">
                <div class="bg-white rounded-md p-1 h-16 w-16 flex justify-center items-center">
                    <img class="" src="{{asset('/images/heart.png')}}" alt="">
                </div>
                <h1 class="">
                    Search for Extra-Care Rides
                </h1>
            </div>
        </div>
        <!-- <p class="mt-1">
            @isset($findRidePage->extra_care_ride_page_label)
                {{ $findRidePage->extra_care_ride_page_label }}
            @endisset
        </p> -->
        <div class="mt-4 p-4 bg-[#d4f3d4] border-l-4 border-green-500 rounded">
            <p class="text-gray-900 font-medium">
                I understand that Extra-Care Rides are for our most respectful drivers and passengers. I promise to be courteous, polite, and considerate in order to maintain the high standard of these rides.
            </p>
        </div>
        <div class="mt-6 grid grid-cols-1 lg:grid-cols-4 gap-x-0 lg:gap-x-4 gap-4">
            <div>
            <div class="search-filter-container flex flex-col relative">
                <button id="search-filter-toggle"
                    class="search-filter-toggle button-exp-fill flex items-center justify-center ml-auto gap-1 w-40 shadow lg:hidden">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" />
                    </svg>
                    <span class="text-xl">
                    Search filters
                    </span>
                </button>

                <div id="search-filter-overlay" class="fixed inset-0 bg-black bg-opacity-50 hidden z-40 lg:hidden"></div>
                    <div id="search-filter"
                        class="search-filter fixed top-0 right-0 h-full overflow-y-auto bg-white w-11/12 sm:w-96 lg:w-full transform translate-x-full lg:translate-x-0 lg:static lg:shadow-3xl lg:h-auto transition-transform duration-300 z-40">
                        <button id="search-filter-close"
                            class="search-filter-close border w-6 h-6 overflow-hidden flex items-center justify-center border-gray-500 rounded-full text-gray-500 text-3xl absolute top-3 right-4 hover:text-red-500 lg:hidden">
                            &times;
                        </button>
                        <div
                            class="search-filter-menu bg-white border lg:border-none rounded pt-12 p-4 lg:p-0 border-gray-200 w-full shadow">

                        <div class="">
                        <div class="bg-white rounded-lg overflow-hidden shadow-3xl">
                    <div class="bg-primary text-white font-medium text-xl flex items-center justify-center gap-2 p-4">
                        <div class="flex items-center justify-center h-8 w-8 rounded-full bg-white">
                            <img class="w-6 h-6 rounded-full" src="{{ asset('assets/filter.png') }}" alt="">
                        </div>
                        @isset($findRidePage->filter_section_heading)
                         {{ $findRidePage->filter_section_heading }}
                        @endisset
                    </div>
                    <div class="bg-white p-4 ">

                        <div class="divide-y mb-2">
                                @php
                                    $features_check = isset($_GET['features']) ? explode(';', $_GET['features']) : [];
                                @endphp
                                @isset($findRidePage->ride_features_option1->features_setting_id)
                                <div class="flex items-start justify-between p-3">
                                    <label for="pink-ride" class="text-gray-900 flex space-x-1">
                                        <span class="text-pink-500 text-lg">
                                            {{ $findRidePage->ride_features_option1->name }}
                                        </span>
                                        <!-- <div class="sups relative">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                fill="currentColor" class="bi bi-info-circle-fill text-gray-900 peer"
                                                viewBox="0 0 16 16">
                                                <path
                                                    d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
                                            </svg>
                                            <div
                                                class="absolute right-32 tooltip -top-12 group-hover:flex hidden peer-hover:flex">
                                                <div role="tooltip"
                                                    class="absolute tooltiptext_icon after:right-1/2 -left-1/2 -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-blue-500  border border-blue-500 text-gray-600 rounded w-64 px-4">
                                                    <p class="text-white font-semibold leading-none text-sm lg:text-base">
                                                        tooltip text
                                                    </p>
                                                </div>
                                            </div>
                                        </div> -->
                                    </label>
                                    <input id="pink-ride" type="checkbox" value="{{ $findRidePage->ride_features_option1->features_setting_id }}"
                                        {{ in_array($findRidePage->ride_features_option1->features_setting_id, $features_check) ? 'checked' : '' }}
                                        class="ride-preferences w-4 h-4 ml-4 mt-1 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                </div>
                                @endisset
                                @isset($findRidePage->ride_features_option2->features_setting_id)
                                <div class="flex items-start justify-between p-3">
                                    <label for="extra-care" class="text-gray-900 flex space-x-1">
                                        <span class="text-green-500 text-lg">
                                            {{ $findRidePage->ride_features_option2->name }}
                                        </span>
                                        <!-- <div class="sups">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                fill="currentColor" class="bi bi-info-circle-fill text-gray-900"
                                                viewBox="0 0 16 16">
                                                <path
                                                    d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
                                            </svg>
                                        </div> -->
                                    </label>
                                    <input id="extra-care" type="checkbox"
                                        value="{{ $findRidePage->ride_features_option2->features_setting_id }}"
                                        checked disabled
                                        class="ride-preferences w-4 h-4 ml-4 mt-1 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                </div>
                                @endisset
                        </div>

                        <div class="space-y-4 mb-4">
                            <h3 for="keyword" class="text-primary text-2xl xl:text-3xl">
                                @isset($findRidePage->search_section_keyword_label)
                                    {{ $findRidePage->search_section_keyword_label }}
                                @endisset
                            </h3>
                            <textarea id="keyword"
                            class="bg-gray-100 border-0 text-black text-base md:text-lg rounded italic focus:outline-none focus:ring-1 focus:ring-sky-500 block w-full p-2.5 resize-none overflow-hidden"
                                @isset($findRidePage->search_section_keyword_placeholder)
                                    placeholder="{{ $findRidePage->search_section_keyword_placeholder }}"
                                @endisset></textarea>
                        </div>

                        <div class="space-y-4 mb-4">
                            <h3 class="text-primary">
                                @isset($findRidePage->filter1_driver_heading)
                                    {{ $findRidePage->filter1_driver_heading }}
                                @endisset
                            </h3>
                         <div>
                            <label for="type" class="block mb-2 font-medium text-gray-900">
                                @isset($findRidePage->driver_age_label)
                                    {{ $findRidePage->driver_age_label }}
                                @endisset
                            </label>
                            <div class="mt-2">
                                <select id="driverAge" name=""
                                    class="bg-gray-100 border-0 text-gray-500 rounded text-lg focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 block w-full p-2.5"
                                    {{-- onchange="navigateToSearchRoute()" --}}
                                    >
                                    <option value="0" {{ $request->driver_age == 0 ? 'selected' : '' }}>
                                        @isset($findRidePage->driver_age_placeholder)
                                            {{ $findRidePage->driver_age_placeholder }}
                                        @endisset
                                    </option>
                                    <option {{ $request->driver_age == 20 ? 'selected' : '' }}>20</option>
                                    <option {{ $request->driver_age == 30 ? 'selected' : '' }}>30</option>
                                    <option {{ $request->driver_age == 40 ? 'selected' : '' }}>40</option>
                                    <option {{ $request->driver_age == 50 ? 'selected' : '' }}>50</option>
                                    <option {{ $request->driver_age == 60 ? 'selected' : '' }}>60</option>
                                </select>
                            </div>
                         </div>
                         <div>
                            <label for="type" class="block mb-2 font-medium text-gray-900">
                                @isset($findRidePage->driver_rating_label)
                                    {{ $findRidePage->driver_rating_label }}
                                @endisset
                            </label>
                            <div class="mt-2">
                                <select id="driverRating" name=""
                                    class="bg-gray-100 border-0 text-gray-500 text-lg rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 block w-full p-2.5"
                                    {{-- onchange="navigateToSearchRoute()" --}}
                                    >
                                    <option value="0" {{ $request->driver_rating == 0 ? 'selected' : '' }}>
                                        @isset($findRidePage->driver_rating_placeholder)
                                            {{ $findRidePage->driver_rating_placeholder }}
                                        @endisset
                                    </option>
                                    <option value="4.5" {{ $request->driver_rating == 4.5 ? 'selected' : '' }}>5</option>
                                    <option value="4" {{ $request->driver_rating == 4 ? 'selected' : '' }}>4</option>
                                    <option value="3" {{ $request->driver_rating == 3 ? 'selected' : '' }}>3</option>
                                    <option value="2" {{ $request->driver_rating == 2 ? 'selected' : '' }}>2</option>
                                    <option value="1" {{ $request->driver_rating == 1 ? 'selected' : '' }}>1</option>
                                </select>
                            </div>
                         </div>
                         <div class="flex items-center space-x-2 mb-2 mr-2 lg:mr-2">
                            <input id="driverPhone" name="" type="checkbox"
                                {{ $request->driver_phone == 1 ? 'checked' : '' }}
                                class="h-4 w-4 border-gray-300 bg-white cursor-pointer text-indigo-600 focus:ring-indigo-600"
                                {{-- onchange="navigateToSearchRoute()" --}}
                                >
                            <label for="" class="block font-normal text-sm text-gray-900">
                                @isset($findRidePage->driver_phone_access_label)
                                    {{ $findRidePage->driver_phone_access_label }}
                                @endisset
                            </label>
                         </div>
                         <div>
                            <label for="driverName" class="block mb-2 font-medium text-gray-900">
                                @isset($findRidePage->driver_know_label)
                                    {{ $findRidePage->driver_know_label }}
                                @endisset
                            </label>
                            <input type="text" id="driverName" value="{{ $request->driver_name }}"
                                class=" italic rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 block w-full p-2.5"
                                @isset($findRidePage->driver_know_placeholder)
                                placeholder={{ $findRidePage->driver_know_placeholder }}
                            @endisset>
                         </div>
                        </div>

                        <div class="space-y-4 mb-4">
                                <h3 class="text-primary">
                                    @isset($findRidePage->filter2_passengers_heading)
                                        {{ $findRidePage->filter2_passengers_heading }}
                                    @endisset
                                </h3>
                            <div>
                                <label for="type" class="block mb-2 font-medium text-gray-900">
                                    @isset($findRidePage->passengers_rating_label)
                                        {{ $findRidePage->passengers_rating_label }}
                                    @endisset
                                </label>
                                <div class="mt-2">
                                    <select id="passengerRating" name=""
                                        class="bg-gray-100 border-0 text-gray-500 text-lg rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 block w-full p-2.5"
                                        {{-- onchange="navigateToSearchRoute()" --}}
                                        >
                                        <option value="" {{ $request->passenger_rating == '' ? 'selected' : '' }}>
                                            @isset($findRidePage->passengers_rating_placeholder)
                                                {{ $findRidePage->passengers_rating_placeholder }}
                                            @endisset
                                        </option>
                                        @isset($findRidePage->ride_features_option13->features_setting_id)
                                            <?php
                                            $dynamicText = str_replace('passengers', 'co-passengers', $findRidePage->ride_features_option13->name);
                                            ?>
                                            <option value="{{ $findRidePage->ride_features_option13->features_setting_id }}"
                                                {{ $request->passenger_rating == $findRidePage->ride_features_option13->features_setting_id ? 'selected' : '' }}>
                                                {{ $dynamicText }}
                                            </option>
                                        @endisset
                                        @isset($findRidePage->ride_features_option14->features_setting_id)
                                            <?php
                                            $dynamicText = str_replace('passengers', 'co-passengers', $findRidePage->ride_features_option14->name);
                                            ?>
                                            <option value="{{ $findRidePage->ride_features_option14->features_setting_id }}"
                                                {{ $request->passenger_rating == $findRidePage->ride_features_option14->features_setting_id ? 'selected' : '' }}>
                                                {{ $dynamicText }}
                                            </option>
                                        @endisset
                                        @isset($findRidePage->ride_features_option15->features_setting_id)
                                            <?php
                                            $dynamicText = str_replace('passengers', 'co-passengers', $findRidePage->ride_features_option15->name);
                                            ?>
                                            <option value="{{ $findRidePage->ride_features_option15->features_setting_id }}"
                                                {{ $request->passenger_rating == $findRidePage->ride_features_option15->features_setting_id ? 'selected' : '' }}>
                                                {{ $dynamicText }}
                                            </option>
                                        @endisset
                                        @isset($findRidePage->ride_features_option16->features_setting_id)
                                            <?php
                                            $dynamicText = str_replace('passengers', 'co-passengers', $findRidePage->ride_features_option16->name);
                                            ?>
                                            <option value="{{ $findRidePage->ride_features_option16->features_setting_id }}"
                                                {{ $request->passenger_rating == $findRidePage->ride_features_option16->features_setting_id ? 'selected' : '' }}>
                                                {{ $dynamicText }}
                                            </option>
                                        @endisset
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4 mb-4">
                            <h3 class="text-primary">
                                @isset($findRidePage->filter3_payment_methods_heading)
                                    {{ $findRidePage->filter3_payment_methods_heading }}
                                @endisset
                            </h3>
                            <div>
                                <label for="payment-method" class="block mb-2 font-medium text-gray-900">
                                    @isset($findRidePage->payment_methods_label)
                                        {{ $findRidePage->payment_methods_label }}
                                    @endisset
                                </label>

                                <div class="mt-2">
                                    <select id="payment-method" name=""
                                        class="bg-gray-100 border-0 text-gray-500 text-lg rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 block w-full p-2.5"
                                        {{-- onchange="navigateToSearchRoute()" --}}
                                        >
                                        @isset($findRidePage->payment_methods_option1)
                                            <option value=""
                                                {{ $request->payment_method == '' ? 'selected' : '' }}>
                                                {{ ($findRidePage->payment_methods_option1) }}
                                            </option>
                                        @endisset
                                        @isset($findRidePage->payment_methods_option2)
                                            <option value="{{ $findRidePage->payment_methods_option2 }}"
                                                {{ $request->payment_method == $findRidePage->payment_methods_option2 ? 'selected' : '' }}>
                                                {{ $findRidePage->payment_methods_option2->name }}
                                            </option>
                                        @endisset
                                        @isset($findRidePage->payment_methods_option3)
                                            <option value="{{ $findRidePage->payment_methods_option3 }}"
                                                {{ $request->payment_method == $findRidePage->payment_methods_option3 ? 'selected' : '' }}>
                                                {{ $findRidePage->payment_methods_option3->name }}
                                            </option>
                                        @endisset
                                        @isset($findRidePage->payment_methods_option4)
                                            <option value="{{ $findRidePage->payment_methods_option4 }}"
                                                {{ $request->payment_method == $findRidePage->payment_methods_option4 ? 'selected' : '' }}>
                                                {{ $findRidePage->payment_methods_option4->name }}
                                            </option>
                                        @endisset
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="space-y-4 mb-4">
                            <h3 class="text-primary">
                                @isset($findRidePage->filter4_vehicle_heading)
                                    {{ $findRidePage->filter4_vehicle_heading }}
                                @endisset
                            </h3>
                            <div>
                               <label for="type" class="block mb-2 font-medium text-gray-900">
                                    @isset($findRidePage->vehicle_type_label)
                                        {{ $findRidePage->vehicle_type_label }}
                                    @endisset
                                </label>
                                <div class="mt-2">
                                    <select id="VehicleType" name=""
                                      class="bg-gray-100 border-0 text-gray-500 text-lg rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 block w-full p-2.5"
                                      {{-- onchange="navigateToSearchRoute()" --}}
                                      >
                                        <option {{ $request->vehicle_type == '' ? 'selected' : '' }} value="">
                                            @isset($findRidePage->vehicle_type_placeholder)
                                                {{ $findRidePage->vehicle_type_placeholder }}
                                            @endisset
                                        </option>
                                        <option {{ $request->vehicle_type == 'Convertable' ? 'selected' : '' }}>Convertable
                                        </option>
                                        <option {{ $request->vehicle_type == 'Hatchback' ? 'selected' : '' }}>Hatchback
                                        </option>
                                        <option {{ $request->vehicle_type == 'Coupe' ? 'selected' : '' }}>Coupe</option>
                                        <option {{ $request->vehicle_type == 'Minivan' ? 'selected' : '' }}>Minivan</option>
                                        <option {{ $request->vehicle_type == 'Sedan' ? 'selected' : '' }}>Sedan</option>
                                        <option {{ $request->vehicle_type == 'Station Wagon' ? 'selected' : '' }}>Station Wagon
                                        </option>
                                        <option {{ $request->vehicle_type == 'SUV' ? 'selected' : '' }}>SUV</option>
                                        <option {{ $request->vehicle_type == 'Truck' ? 'selected' : '' }}>Truck</option>
                                        <option {{ $request->vehicle_type == 'Van' ? 'selected' : '' }}>Van</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="space-y-4 mb-4">
                            <h3 class="text-primary">
                                @isset($findRidePage->luggage_placeholder)
                                    {{ $findRidePage->luggage_placeholder }}
                                @endisset
                            </h3>
                            <div class="border rounded-md overflow-hidden divide-y">
                                @php
                                    $features_check = isset($_GET['features']) ? explode(';', $_GET['features']) : [];
                                @endphp

                                @isset($findRidePage->ride_features_option3->features_setting_id)
                                <div class="flex items-start justify-between p-3">
                                    <label for="wi-fi" class="font-normal text-gray-900 flex space-x-1">
                                        <span class="text-lg">
                                            {{ $findRidePage->ride_features_option3->name }}
                                        </span>
                                    </label>
                                    <input id="wi-fi" type="checkbox" value="{{ $findRidePage->ride_features_option3->features_setting_id }}"
                                        {{ in_array($findRidePage->ride_features_option3->features_setting_id, $features_check) ? 'checked' : '' }}
                                        class="ride-preferences w-4 h-4 ml-4 mt-1 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                </div>
                                @endisset
                                @isset($findRidePage->ride_features_option4->features_setting_id)
                                <div class="flex items-start justify-between p-3">
                                    <label for="rating-passengers" class="font-normal text-gray-900 flex space-x-1">
                                        <span class="text-lg">
                                            {{ $findRidePage->ride_features_option4->name }}
                                        </span>
                                    </label>
                                    <input id="rating-passengers" type="checkbox"
                                        value="{{ $postRidePage->features_option4->features_setting_id }}"
                                        {{ in_array($postRidePage->features_option4->features_setting_id, $features_check) ? 'checked' : '' }}
                                        class="ride-preferences w-4 h-4 ml-4 mt-1 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                </div>
                                @endisset
                                @isset($findRidePage->ride_features_option5->features_setting_id)
                                <div class="flex items-start justify-between p-3">
                                    <label for="provide-babyseats" class="font-normal text-gray-900 flex space-x-1">
                                        <span class="text-lg">
                                            {{ $findRidePage->ride_features_option5->name }}
                                        </span>
                                    </label>
                                    <input id="provide-babyseats" type="checkbox"
                                        value="{{ $postRidePage->features_option5->features_setting_id }}"
                                        {{ in_array($postRidePage->features_option5->features_setting_id, $features_check) ? 'checked' : '' }}
                                        class="ride-preferences w-4 h-4 ml-4 mt-1 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                </div>
                                @endisset
                                @isset($findRidePage->ride_features_option6->features_setting_id)
                                <div class="flex items-start justify-between p-3">
                                    <label for="passenger-provide" class="font-normal text-gray-900 flex space-x-1">
                                        <span class="text-lg">
                                            {{ $findRidePage->ride_features_option6->name }}
                                        </span>
                                    </label>
                                    <input id="passenger-provide" type="checkbox"
                                        value="{{ $postRidePage->features_option6->features_setting_id }}"
                                        {{ in_array($postRidePage->features_option6->features_setting_id, $features_check) ? 'checked' : '' }}
                                        class="ride-preferences w-4 h-4 ml-4 mt-1 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                </div>
                                @endisset
                                @isset($findRidePage->ride_features_option7->features_setting_id)
                                <div class="flex items-start justify-between p-3">
                                    <label for="take-children" class="font-normal text-gray-900 flex space-x-1">
                                        <span class="text-lg">
                                            {{ $findRidePage->ride_features_option7->name }}
                                        </span>
                                    </label>
                                    <input id="take-children" type="checkbox" value="{{ $postRidePage->features_option7->features_setting_id }}"
                                        {{ in_array($postRidePage->features_option7->features_setting_id, $features_check) ? 'checked' : '' }}
                                        class="ride-preferences w-4 h-4 ml-4 mt-1 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                </div>
                                @endisset
                                @isset($findRidePage->ride_features_option8->features_setting_id)
                                <div class="flex items-start justify-between p-3">
                                    <label for="passenger-provide1" class="font-normal text-gray-900 flex space-x-1">
                                        <span class="text-lg">
                                            {{ $findRidePage->ride_features_option8->name }}
                                        </span>
                                    </label>
                                    <input id="passenger-provide1" type="checkbox"
                                        value="{{ $findRidePage->ride_features_option8->features_setting_id }}"
                                        {{ in_array($findRidePage->ride_features_option8->features_setting_id, $features_check) ? 'checked' : '' }}
                                        class="ride-preferences w-4 h-4 ml-4 mt-1 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                </div>
                                @endisset
                                @isset($findRidePage->ride_features_option9->features_setting_id)
                                <div class="flex items-start justify-between p-3">
                                     <label for="bike-rack" class="font-normal text-gray-900 flex space-x-1">
                                        <span class="text-lg">
                                            {{ $findRidePage->ride_features_option9->name }}
                                        </span>
                                    </label>
                                    <input id="bike-rack" type="checkbox" value="{{ $findRidePage->ride_features_option9->features_setting_id }}"
                                        {{ in_array($findRidePage->ride_features_option9->features_setting_id, $features_check) ? 'checked' : '' }}
                                        class="ride-preferences w-4 h-4 ml-4 mt-1 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                </div>
                                @endisset
                                @isset($findRidePage->ride_features_option10->features_setting_id)
                                <div class="flex items-start justify-between p-3">
                                    <label for="ski-rack" class="font-normal text-gray-900 flex space-x-1">
                                        <span class="text-lg">
                                            {{ $findRidePage->ride_features_option10->name }}
                                        </span>
                                    </label>
                                    <input id="ski-rack" type="checkbox"
                                        value="{{ $findRidePage->ride_features_option10->features_setting_id }}"
                                        {{ in_array($findRidePage->ride_features_option10->features_setting_id, $features_check) ? 'checked' : '' }}
                                        class="ride-preferences w-4 h-4 ml-4 mt-1 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                </div>
                                @endisset
                                @isset($findRidePage->ride_features_option11->features_setting_id)
                                <div class="flex items-start justify-between p-3">
                                    <label for="winter-tires" class="font-normal text-gray-900 flex space-x-1">
                                        <span class="text-lg">
                                            {{ $findRidePage->ride_features_option11->name }}
                                        </span>
                                    </label>
                                    <input id="winter-tires" type="checkbox"
                                        value="{{ $findRidePage->ride_features_option11->features_setting_id }}"
                                        {{ in_array($findRidePage->ride_features_option11->features_setting_id, $features_check) ? 'checked' : '' }}
                                        class="ride-preferences w-4 h-4 ml-4 mt-1 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                </div>
                                @endisset
                                @isset($findRidePage->ride_features_option12->features_setting_id)
                                <div class="flex items-start justify-between p-3">
                                    <label for="air-conditioning" class="font-normal text-gray-900 flex space-x-1">
                                        <span class="text-lg">
                                            {{ $findRidePage->ride_features_option12->name }}
                                        </span>
                                    </label>
                                    <input id="air-conditioning" type="checkbox"
                                        value="{{ $findRidePage->ride_features_option12->features_setting_id }}"
                                        {{ in_array($findRidePage->ride_features_option12->features_setting_id, $features_check) ? 'checked' : '' }}
                                        class="ride-preferences w-4 h-4 ml-4 mt-1 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                </div>
                                @endisset
                            </div>
                        </div>
                        <div class="space-y-4 mb-4">
                            <h3 class="text-primary">
                                @isset($findRidePage->luggage_label)
                                    {{ $findRidePage->luggage_label }}
                                @endisset
                            </h3>
                            <div class="border rounded-md overflow-hidden divide-y">
                                @php
                                    $luggages_check = isset($_GET['luggage']) ? explode(';', $_GET['luggage']) : [];
                                @endphp
                                @isset($findRidePage->luggage_option1->features_setting_id)
                                    <div class="flex items-center justify-between p-3">
                                        <label for="small-luggage" class="font-normal text-gray-900 flex space-x-1">
                                            <span class="text-lg">
                                                {{ $findRidePage->luggage_option1->name }}
                                            </span>
                                        </label>
                                        <input id="small-luggage" type="checkbox"
                                            value="{{ $findRidePage->luggage_option1->features_setting_id }}"
                                            {{ in_array($findRidePage->luggage_option1->features_setting_id, $luggages_check) ? 'checked' : '' }}
                                            class="luggage w-4 h-4 ml-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                    </div>
                                @endisset
                                @isset($findRidePage->luggage_option2->features_setting_id)
                                    <div class="flex items-center justify-between p-3">
                                        <label for="Medium-luggage" class="font-normal text-gray-900 flex space-x-1">
                                            <span class="text-lg">
                                                {{ $findRidePage->luggage_option2->name }}
                                            </span>
                                        </label>
                                        <input id="Medium-luggage" type="checkbox"
                                            value="{{ $findRidePage->luggage_option2->features_setting_id }}"
                                            {{ in_array($findRidePage->luggage_option2->features_setting_id, $luggages_check) ? 'checked' : '' }}
                                            class="luggage w-4 h-4 ml-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                    </div>
                                @endisset
                                @isset($findRidePage->luggage_option3->features_setting_id)
                                    <div class="flex items-center justify-between p-3">
                                        <label for="Large-luggage" class="font-normal text-gray-900 flex space-x-1">
                                            <span class="text-lg">
                                                {{ $findRidePage->luggage_option3->name }}
                                            </span>
                                        </label>
                                        <input id="Large-luggage" type="checkbox"
                                            value="{{ $findRidePage->luggage_option3->features_setting_id }}"
                                            {{ in_array($findRidePage->luggage_option3->features_setting_id, $luggages_check) ? 'checked' : '' }}
                                            class="luggage w-4 h-4 ml-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                    </div>
                                @endisset
                                @isset($findRidePage->luggage_option4->features_setting_id)
                                    <div class="flex items-center justify-between p-3">
                                        <label for="multiple-luggage" class="font-normal text-gray-900 flex space-x-1">
                                            <span class="text-lg">
                                                {{ $findRidePage->luggage_option4->name }}
                                            </span>
                                        </label>
                                        <input id="multiple-luggage" type="checkbox"
                                            value="{{ $findRidePage->luggage_option4->features_setting_id }}"
                                            {{ in_array($findRidePage->luggage_option4->features_setting_id, $luggages_check) ? 'checked' : '' }}
                                            class="luggage w-4 h-4 ml-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                    </div>
                                @endisset
                                @isset($findRidePage->luggage_option5->features_setting_id)
                                    <div class="flex items-center justify-between p-3">
                                        <label for="no-luggage" class="font-normal text-gray-900 flex space-x-1">
                                            <span class="text-lg">
                                                {{ $findRidePage->luggage_option5->name }}
                                            </span>
                                        </label>
                                        <input id="no-luggage" type="checkbox"
                                            value="{{ $findRidePage->luggage_option5->features_setting_id }}"
                                            {{ in_array($findRidePage->luggage_option5->features_setting_id, $luggages_check) ? 'checked' : '' }}
                                            class="luggage w-4 h-4 ml-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                    </div>
                                @endisset
                            </div>
                        </div>
                        <div class="space-y-4 mb-4">
                            <h3 class="text-primary">
                                @isset($findRidePage->smoking_label)
                                    {{ $findRidePage->smoking_label }}
                                @endisset
                            </h3>
                            <div class="border rounded-md overflow-hidden divide-y">
                                @php
                                    $smoking_check = isset($_GET['smoking']) ? explode(';', $_GET['smoking']) : [];
                                @endphp
                                @isset($findRidePage->smoking_option1->features_setting_id)
                                    <div class="flex items-center justify-between p-3">
                                        <label for="indifferent" class="font-normal text-gray-900 flex space-x-1">
                                            <span class="text-lg">
                                                {{ $findRidePage->smoking_option1->name }}
                                            </span>
                                        </label>
                                        <input id="indifferent" type="checkbox"
                                            value="{{ $findRidePage->smoking_option1->features_setting_id }}"
                                            {{ in_array($findRidePage->smoking_option1->features_setting_id, $smoking_check) ? 'checked' : '' }}
                                            class="smoking w-4 h-4 ml-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                    </div>
                                @endisset
                                @isset($findRidePage->smoking_option2->features_setting_id)
                                    <div class="flex items-center justify-between p-3">
                                        <label for="no-smoking" class="font-normal text-gray-900 flex space-x-1">
                                            <span class="text-lg">
                                                {{ $findRidePage->smoking_option2->name }}
                                            </span>
                                        </label>
                                        <input id="no-smoking" type="checkbox"
                                            value="{{ $findRidePage->smoking_option2->features_setting_id }}"
                                            {{ in_array($findRidePage->smoking_option2->features_setting_id, $smoking_check) ? 'checked' : '' }}
                                            class="smoking w-4 h-4 ml-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                    </div>
                                @endisset
                            </div>
                        </div>
                        <div class="space-y-4 mb-4">
                            <h3 class="text-primary">
                                @isset($findRidePage->pets_allowed_label)
                                    {{ $findRidePage->pets_allowed_label }}
                                @endisset
                            </h3>
                            <div class="border rounded-md overflow-hidden divide-y">
                                @php
                                    $pets_check = isset($_GET['pets']) ? explode(';', $_GET['pets']) : [];
                                @endphp
                                @isset($findRidePage->pets_allowed_option1->features_setting_id)
                                    <div class="flex items-center justify-between p-3">
                                        <label for="indifferent-pet" class="font-normal text-gray-900 flex space-x-1">
                                            <span class="text-lg">
                                                {{ $findRidePage->pets_allowed_option1->name }}
                                            </span>
                                        </label>
                                        <input id="indifferent-pet" type="checkbox"
                                            value="{{ $findRidePage->pets_allowed_option1->features_setting_id }}"
                                            {{ in_array($findRidePage->pets_allowed_option1->features_setting_id, $pets_check) ? 'checked' : '' }}
                                            class="pet w-4 h-4 ml-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                    </div>
                                @endisset
                                @isset($findRidePage->pets_allowed_option2->features_setting_id)
                                    <div class="flex items-center justify-between p-3">
                                        <label for="no-pet" class="font-normal text-gray-900 flex space-x-1">
                                            <span class="text-lg">
                                                {{ $findRidePage->pets_allowed_option2->name }}
                                            </span>
                                        </label>
                                        <input id="no-pet" type="checkbox"
                                            value="{{ $findRidePage->pets_allowed_option2->features_setting_id }}"
                                            {{ in_array($findRidePage->pets_allowed_option2->features_setting_id, $pets_check) ? 'checked' : '' }}
                                            class="pet w-4 h-4 ml-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                    </div>
                                @endisset
                                @isset($findRidePage->pets_allowed_option3->features_setting_id)
                                    <div class="flex items-center justify-between p-3">
                                        <label for="caged" class="font-normal text-gray-900 flex space-x-1">
                                            <span class="text-lg">
                                                {{ $findRidePage->pets_allowed_option3->name }}
                                            </span>
                                        </label>
                                        <input id="caged" type="checkbox"
                                            value="{{ $findRidePage->pets_allowed_option3->features_setting_id }}"
                                            {{ in_array($findRidePage->pets_allowed_option3->features_setting_id, $pets_check) ? 'checked' : '' }}
                                            class="pet w-4 h-4 ml-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                    </div>
                                @endisset
                            </div>
                        <button class="w-28 text-white text-lg font-FuturaMdCnBT px-4 py-2 bg-blue-600 rounded" onclick="navigateToSearchRoute()">{{ $findRidePage->filter_search_btn_label }}</button>
                        <button class="w-28 text-white text-lg font-FuturaMdCnBT px-4 py-2 bg-blue-600 rounded" onclick="resetFilters()">{{ $findRidePage->filter_close_btn_label }}</button>
                        </div>
                    </div>
                </div>
                        </div>
                    </div>
                </div>
            </div>


            </div>
            <div class="col-span-3">
                <div class="bg-gray-100 rounded-md p-4 py-6">
                    <div
                        class="flex items-end flex-col md:flex-row justify-between gap-4 md:gap-0 rounded-lg overflow-hidden">
                        <div class="w-full md:w-[30%]">
                            <div class="relative">
                                <div class="absolute inset-y-0 start-0 flex items-center pl-2 pointer-events-none">
                                    <img src="{{ asset('assets/search-bar-from.png') }}" class="w-auto h-6"
                                        alt="">
                                </div>
                                <input type="text" id="fromInput" value="{{ $request->from }}"
                                    class="bg-white rounded-md md:rounded-none italic pl-7 border-0 text-gray-900 focus:outline-none text-lg focus:border-sky-500 focus:ring-1 focus:ring-sky-500 block w-full p-2.5"
                                    @isset($findRidePage->search_section_from_placeholder)
                                        placeholder="{{ $findRidePage->search_section_from_placeholder }}"
                                    @endisset>
                            </div>
                            <p id="fromError" class="text-sm hidden text-red-500 absolute mt-1"></p>
                        </div>
                        <div class="w-full md:w-[5%] md:bg-gray-200 md:h-12 flex items-center justify-center">
                            <button onclick="swapLocations()">
                                <img src="{{ asset('assets/arrow.png') }}" class="w-8 h-8 mx-auto" alt="">
                            </button>
                        </div>
                        <div class="w-full md:w-[30%]">
                            <div class="relative">
                                <div class="absolute inset-y-0 start-0 flex items-center pl-2 pointer-events-none">
                                    <img src="{{ asset('images/new-21-search-bar-to.png') }}" class="w-4 h-6" alt="">
                                </div>
                                <input type="text" id="toInput" value="{{ $request->to }}"
                                    class="bg-white rounded-md md:rounded-none pl-7 italic border-0 text-gray-900 focus:outline-none text-lg focus:border-sky-500 focus:ring-1 focus:ring-sky-500 block w-full p-2.5"
                                    @isset($findRidePage->search_section_to_placeholder)
                                        placeholder="{{ $findRidePage->search_section_to_placeholder }}"
                                    @endisset>
                            </div>
                            <p id="toError" class="text-sm hidden text-red-500 absolute mt-1"></p>
                        </div>
                        <div class="w-52 mx-auto md:mx-0 md:w-[30%]">
                            <div class="relative">
                                <div class="absolute inset-y-0 start-0 flex items-center pl-2 pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-gray-500">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0 2.25h.008v.008H12v-.008zM9.75 15h.008v.008H9.75V15zm0 2.25h.008v.008H9.75v-.008zM7.5 15h.008v.008H7.5V15zm0 2.25h.008v.008H7.5v-.008zm6.75-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V15zm0 2.25h.008v.008h-.008v-.008zm2.25-4.5h.008v.008H16.5v-.008zm0 2.25h.008v.008H16.5V15z" />
                                    </svg>
                                </div>
                                <input type="text" id="dateInput" value="{{ $request->date }}"
                                    class="bg-white rounded-md md:rounded-none px-7 italic md:border-l border-gray-300 border-0 text-gray-900 text-lg focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 block w-full p-2.5"
                                    @isset($findRidePage->search_section_date_placeholder)
                                        placeholder="{{ $findRidePage->search_section_date_placeholder }}"
                                    @endisset>
                                <div class="absolute inset-y-0 end-0 flex items-center pr-2 cursor-pointer"
                                    onclick="clearDateInput()">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" data-slot="icon"
                                        class="w-6 h-6 text-gray-400">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="w-24 mx-auto md:w-[5%] h-12 flex items-center justify-center">
                            <button onclick="navigateToSearchRoute()"
                                class="bg-blue-500 w-full h-full flex items-center justify-center text-white rounded-lg" style="border-radius:6px;">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="mt-6 space-y-4">
                    @if (!$paginatedRides && $recentSearches->count() > 0)
                        <div class="font-medium text-lg">
                            @isset($findRidePage->search_section_recent_searches)
                                {{ $findRidePage->search_section_recent_searches }}
                            @endisset
                        </div>
                        @foreach ($recentSearches as $recentSearch)
                            <div class="bg-white rounded-lg shadow-3xl border border-solid border-gray-100 cursor-pointer" onclick="SearchRoute('{{ $recentSearch->from }}', '{{ $recentSearch->to }}')">
                                <div class="flex justify-between px-4">
                                    <div class="md:w-2/3">
                                        <div class="relative mt-5 text-left">
                                            <div class="flex items-center relative">
                                                    <div class="border-r-2 border-black border-solid absolute h-full left-3 md:left-6 top-2 z-10">
                                                        <span class="bg-primary rounded-full w-7 h-7 -top-[2px] -ml-[13px] absolute flex justify-center items-center">
                                                            <img class="w-5 h-5 object-contain" src="{{ asset('./images/new-21-search-bar-from.png')}}" alt="">
                                                        </span>
                                                    </div>
                                                <div class="ml-20">
                                                    <div class="font-bold text-black">
                                                        @isset($findRidePage->search_section_from_placeholder)
                                                            {{ $findRidePage->search_section_from_placeholder }}
                                                        @endisset
                                                    </div>
                                                    <div class="text-primary md:mb-4">{{ $recentSearch->from }}</div>
                                                </div>
                                            </div>

                                            <div class="flex items-center relative">
                                                <div class="border-r-2 border-black border-solid absolute h-0 left-3 md:left-5 top-2 z-10">
                                                    <span class="bg-gray-200 rounded-full w-7 h-7 -top-[6px] -ml-[12px] md:-ml-[9px] absolute flex justify-center items-center">
                                                        <img class="w-5 h-5 object-contain" src="{{ asset('./images/new-21-search-bar-to.png')}}" alt="">
                                                    </span>
                                                </div>
                                                <div class="ml-20">
                                                    <div class="font-bold text-black">
                                                        @isset($findRidePage->search_section_to_placeholder)
                                                            {{ $findRidePage->search_section_to_placeholder }}
                                                        @endisset
                                                    </div>
                                                    <div class="text-primary md:mb-4">{{ $recentSearch->to }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @elseif ($paginatedRides && $paginatedRides->count() > 0)
                        @if ($paginatedRides->filter(fn($ride) => $ride->type === 'ride')->count() > 0)
                            <h3 class="text-primary">{{ $findRidePage->search_results_extra_care_ride_label ?? 'Search results for Extra care ride' }}</h3>
                            @foreach ($paginatedRides->filter(fn($ride) => $ride->type === 'ride') as $ride)
                                @php
                                    $from = $ride->rideDetail[0]->departure;
                                    $to = $ride->rideDetail[0]->destination;
                                @endphp
                                <div class="relative">
                                    <div class="absolute right-4 top-8">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                            class="w-6 h-6 -mt-4 cursor-pointer ride-remove-btn"
                                            data-ride-id="{{ $ride->id }}">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    @if (auth()->user())
                                        @php
                                            $user_id = auth()->user()->id;

                                            // Assuming $ratings is a collection
                                            $filteredRatings = $ratings->where('status', 1)->where('type', '2')->filter(function ($rating) use ($user_id) {
                                                // Check if booking exists and is not null before accessing user_id
                                                return $rating->booking && $rating->booking->user_id === $user_id;
                                            });

                                            $totalAverage = $filteredRatings->avg('average_rating') ?? 0;
                                        @endphp
                                    @endif
                                    <a
                                        @if ($ride->status === '2') href=""
                                        @elseif (auth()->user() &&
                                            in_array($findRidePage->ride_features_option1->features_setting_id ?? null, explode('=', $ride->features)) &&
                                            auth()->user()->gender != 'female' && auth()->user()->address != '')
                                            href="javascript:void(0);" onclick="toggleModal1('modal-id1', 'Only females can select this ride')"
                                        @elseif (auth()->user() &&
                                            in_array($findRidePage->ride_features_option2->features_setting_id ?? null, explode('=', $ride->features)))
                                            @if ($filteredRatings->count() === 0 && auth()->user()->address == '')
                                                href="javascript:void(0);" onclick="toggleModal1('modal-id1', 'Only passengers with complete address can select this ride')"
                                            @elseif ($filteredRatings->count() === 0 && auth()->user()->address !== '')
                                                href="{{ route('ride_detail', ['lang' => $selectedLanguage->abbreviation, 'departure' => $ride->rideDetail[0]->departure, 'destination' => $ride->rideDetail[0]->destination, 'id' => $ride->id]) }}"
                                            @elseif ($totalAverage < 4.5 && auth()->user()->address !== '')
                                                href="javascript:void(0);" onclick="toggleModal1('modal-id1', 'Only 5 star passengers can select this ride')"
                                            @elseif ($totalAverage < 4.5 && auth()->user()->address == '')
                                                href="javascript:void(0);" onclick="toggleModal1('modal-id1', 'Only 5 star passengers with complete address can select this ride')"
                                            @else
                                                href="{{ route('ride_detail', ['lang' => $selectedLanguage->abbreviation, 'departure' => $ride->rideDetail[0]->departure, 'destination' => $ride->rideDetail[0]->destination, 'id' => $ride->id]) }}"
                                            @endif
                                        @elseif (auth()->user() &&
                                            in_array('I only want passengers with reviews', explode('=', $ride->features)))
                                            @if ($filteredRatings->count() === 0)
                                                href="javascript:void(0);" onclick="toggleModal1('modal-id1', 'Driver only want passengers with reviews')"
                                            @else
                                                href="{{ route('ride_detail', ['lang' => $selectedLanguage->abbreviation, 'departure' => $ride->rideDetail[0]->departure, 'destination' => $ride->rideDetail[0]->destination, 'id' => $ride->id]) }}"
                                            @endif
                                        @elseif (auth()->user() &&
                                            in_array($findRidePage->ride_features_option15->features_setting_id ?? null, explode('=', $ride->features)))
                                            @if ($totalAverage < 3)
                                                href="javascript:void(0);" onclick="toggleModal1('modal-id1', 'Driver want only passengers with-3 star reviews above')"
                                            @else
                                                href="{{ route('ride_detail', ['lang' => $selectedLanguage->abbreviation, 'departure' => $ride->rideDetail[0]->departure, 'destination' => $ride->rideDetail[0]->destination, 'id' => $ride->id]) }}"
                                            @endif
                                        @elseif (auth()->user() &&
                                            in_array($findRidePage->ride_features_option14->features_setting_id ?? null, explode('=', $ride->features)))
                                            @if ($totalAverage < 4)
                                                href="javascript:void(0);" onclick="toggleModal1('modal-id1', 'Driver want only passengers with-4 star reviews above')"
                                            @else
                                                href="{{ route('ride_detail', ['lang' => $selectedLanguage->abbreviation, 'departure' => $ride->rideDetail[0]->departure, 'destination' => $ride->rideDetail[0]->destination, 'id' => $ride->id]) }}"
                                            @endif
                                        @elseif (auth()->user() &&
                                            in_array($findRidePage->ride_features_option13->features_setting_id ?? null, explode('=', $ride->features)))
                                            @if ($totalAverage < 4.5)
                                                href="javascript:void(0);" onclick="toggleModal1('modal-id1', 'Driver want only passengers with-5 star reviews above')"
                                            @else
                                                href="{{ route('ride_detail', ['lang' => $selectedLanguage->abbreviation, 'departure' => $ride->rideDetail[0]->departure, 'destination' => $ride->rideDetail[0]->destination, 'id' => $ride->id]) }}"
                                            @endif
                                        @else
                                            href="{{ route('ride_detail', ['lang' => $selectedLanguage->abbreviation, 'departure' => $ride->rideDetail[0]->departure, 'destination' => $ride->rideDetail[0]->destination, 'id' => $ride->id]) }}" @endif>
                                        <div class="bg-white rounded-lg shadow-3xl border-[3px] border-solid @if ($ride->status === '2') border-red-500 @elseif(isset($findRidePage->ride_features_option1->features_setting_id) &&
                                                in_array($findRidePage->ride_features_option1->features_setting_id ?? null, explode('=', $ride->features))) border-pink-500 @elseif(isset($findRidePage->ride_features_option2->features_setting_id) &&
                                                in_array($findRidePage->ride_features_option2->features_setting_id ?? null, explode('=', $ride->features))) border-green-500 @else border-gray-100 @endif"
                                            id="ride-{{ $ride->id }}">
                                            <div class="flex items-center justify-between pb-0 p-4">
                                                <div class="flex items-center gap-2">
                                                    <p class="flex items-center space-x-2 font-semibold">
                                                        {{ \Carbon\Carbon::parse($ride->date)->format('F d, Y') }}
                                                        @isset($findRidePage->card_section_at_label)
                                                            {{ $findRidePage->card_section_at_label }}
                                                        @endisset
                                                        {{ \Carbon\Carbon::parse($ride->time)->format('h:i A') == '12:00 PM' ? '12 noon' : (\Carbon\Carbon::parse($ride->time)->format('h:i A') == '12:00 AM' ? '12 midnight' : \Carbon\Carbon::parse($ride->time)->format('h:i A')) }}
                                                    </p>
                                                    @if (in_array($findRidePage->ride_features_option1->features_setting_id ?? null, explode('=', $ride->features)))
                                                        <button type="button" onclick="toggleModal1('modal-id2', '{{ $postRidePage->features_option1_tooltip }}', '{{ $findRidePage->ride_features_option1->name ?? $findRidePage->ride_features_option1->label }}')">
                                                            <img class="w-8 h-8" src="{{asset('home_page_icons/' . $findRidePage->ride_features_option1->icon)}}" alt="">
                                                        </button>
                                                    @endif
                                                </div>
                                                <div class="pr-8">
                                                    <p class="font-medium">
                                                        {{ intval($ride->seats) - intval($ride->bookings()->where('status', '<>', 3)->where('status', '<>', 4)->whereHas('passenger', function($query) { $query->whereNull('deleted_at'); })->sum('seats')) }}
                                                        @isset($findRidePage->card_section_seats_left)
                                                            {{ $findRidePage->card_section_seats_left }}
                                                        @endisset
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="flex justify-between px-4">
                                                <div class="md:w-2/3">
                                                    <div class="relative mt-5 text-left">
                                                        <div class="flex items-center relative">
                                                            <div class="border-r-2 border-black border-solid absolute h-full left-3 md:left-6 top-2 z-10">
                                                                <span
                                                                    class="bg-primary rounded-full w-7 h-7 -top-[2px] -ml-[13px] absolute flex justify-center items-center">
                                                                    <img class="w-5 h-5 object-contain" src="{{ asset('./images/new-21-search-bar-from.png')}}" alt="">
                                                                </span>
                                                            </div>
                                                            <div class="ml-20">
                                                                <div class="font-bold text-black">From</div>
                                                                <div class="text-primary md:mb-4">{{ $ride->pickup }},
                                                                    {{ $from }}</div>
                                                            </div>
                                                        </div>

                                                        <div class="flex items-center relative">
                                                            <div class="border-r-2 border-black border-solid absolute h-0 left-3 md:left-5 top-2 z-10">
                                                                <span
                                                                    class="bg-gray-200 rounded-full w-7 h-7 -top-[6px] -ml-[12px] md:-ml-[9px] absolute flex justify-center items-center">
                                                                    <img class="w-5 h-5 object-contain" src="{{ asset('./images/new-21-search-bar-to.png')}}" alt="">
                                                                </span>
                                                            </div>
                                                            <div class="ml-20">
                                                                <div class="font-bold text-black">To</div>
                                                                <div class="text-primary md:mb-4">{{ $ride->dropoff }},
                                                                    {{ $to }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mt-4">
                                                    <p class="text-xl font-semibold text-primary">
                                                        <div class="flex items-center gap-2">
                                                            @if (isset($firm_cancellation_discount) && $firm_cancellation_discount!='' && $ride->booking_type == $postRidePage->cancellation_policy_label2->features_setting_id)
                                                                <span class="line-through">
                                                                    ${{ number_format(floatval($ride->rideDetail[0]->price), 2) }}
                                                                    </span>
                                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
                                                                    </svg>

                                                                    <span>

                                                                        ${{ $ride->rideDetail[0]->price - ($ride->rideDetail[0]->price * $firm_cancellation_discount) / 100 }}
                                                                    </span>

                                                                @else
                                                                    ${{ number_format(floatval($ride->rideDetail[0]->price), 2) }}
                                                                @endif

                                                                <small>
                                                                    @isset($findRidePage->card_section_per_seat)
                                                                        {{ $findRidePage->card_section_per_seat }}
                                                                    @endisset
                                                                </small>

                                                                <div class="sups inline-flex relative">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle-fill text-black peer" viewBox="0 0 16 16">
                                                                        <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                                                                    </svg>
                                                                    <div
                                                                      class="absolute tooltip payment_tooltiptext_position top-8 group-hover:flex hidden peer-hover:flex bg-blue-500 px-4 py-2 rounded right-0 w-60 z-10"
                                                                    >
                                                                        <p class="text-white font-semibold text-start text-sm lg:text-base">
                                                                            {!! nl2br($findRidePage->firm_cancellation_tooltip) ?? 'This ride has the Firm cancellation policy, so its booking price is reduced by 10%' !!}
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                        </div>
                                                    </p>
                                                    {{-- <p class="text-xl font-semibold text-primary">${{ number_format(floatval($ride->rideDetail[0]->price), 2) }}
                                                        <small>
                                                            @isset($findRidePage->card_section_per_seat)
                                                                {{ $findRidePage->card_section_per_seat }}
                                                            @endisset
                                                        </small>
                                                    </p> --}}
                                                </div>
                                            </div>
                                            <div class="border-t border-gray-300 grid grid-cols-4 divide-x divide-gray-300">
                                                {{-- <div class="flex items-center justify-between p-4">
                                                    <p class="font-semibold">
                                                        @isset($findRidePage->card_section_booked)
                                                            {{ $findRidePage->card_section_booked }}
                                                        @endisset
                                                    </p>
                                                    <p class="">
                                                        @if (auth()->user())
                                                            {{ $ride->bookings()->where('user_id', auth()->user()->id)->where('status', '<>', 3)->where('status', '<>', 4)->whereHas('passenger', function($query) { $query->whereNull('deleted_at'); })->sum('seats') }}
                                                        @else
                                                            0
                                                        @endif
                                                        @isset($findRidePage->card_section_seats)
                                                            {{ $findRidePage->card_section_seats }}
                                                        @endisset
                                                    </p>
                                                </div> --}}
                                                <div class="col-span-4 p-4 flex justify-start items-center no-scrollbar overflow-x-auto space-x-2 md:space-x-4">
                                                    @unless(old('skip_vehicle', $ride->skip_vehicle) == '0')
                                                        @if ($ride->remove_car_image == 0)
                                                            <div class="flex-none w-12 h-12 bg-gray-100 border rounded-full">
                                                                <img class="w-full h-full object-cover rounded-full"
                                                                    src="{{ $ride->car_image }}"
                                                                    alt="">
                                                            </div>
                                                        @endif
                                                    @endunless

                                                    <div class="flex items-center space-x-1">
                                                        @if ($ride->payment_method == ($findRidePage->payment_methods_option2->features_setting_id ?? null))
                                                            <a href="javascript:void(0);"
                                                            onmouseover="toggleModal1('modal-id2', '{{ $postRidePage->payment_methods_option1_tooltip }}', '{{ $findRidePage->payment_methods_option2->name ?? $findRidePage->payment_methods_option2->label }}')"
                                                            onclick="toggleModal1('modal-id2', '{{ $postRidePage->payment_methods_option1_tooltip }}', '{{ $findRidePage->payment_methods_option2->name ?? $findRidePage->payment_methods_option2->label }}')">
                                                                <img class="w-8 h-8" src="{{asset('home_page_icons/' . $findRidePage->payment_methods_option2->icon)}}" alt="">
                                                            </a>
                                                        @elseif ($ride->payment_method == ($findRidePage->payment_methods_option3->features_setting_id ?? null))
                                                            <a href="javascript:void(0);" 
                                                            onmouseover="toggleModal1('modal-id2', '{{ $postRidePage->payment_methods_option2_tooltip }}', '{{ $findRidePage->payment_methods_option3->name ?? $findRidePage->payment_methods_option3->label }}')"
                                                            onclick="toggleModal1('modal-id2', '{{ $postRidePage->payment_methods_option2_tooltip }}', '{{ $findRidePage->payment_methods_option3->name ?? $findRidePage->payment_methods_option3->label }}')">
                                                                <img class="w-8 h-8" src="{{asset('home_page_icons/' . $findRidePage->payment_methods_option3->icon)}}" alt="">
                                                            </a>
                                                        @elseif ($ride->payment_method == ($findRidePage->payment_methods_option4->features_setting_id ?? null))
                                                            <a href="javascript:void(0);"
                                                            onmouseover="toggleModal1('modal-id2', '{{ $postRidePage->payment_methods_option3_tooltip }}', '{{ $findRidePage->payment_methods_option4->name ?? $findRidePage->payment_methods_option4->label }}')"
                                                            onclick="toggleModal1('modal-id2', '{{ $postRidePage->payment_methods_option3_tooltip }}', '{{ $findRidePage->payment_methods_option4->name ?? $findRidePage->payment_methods_option4->label }}')">
                                                                <img class="w-8 h-8" src="{{asset('home_page_icons/' . $findRidePage->payment_methods_option4->icon)}}" alt="">
                                                            </a>
                                                        @endif
                                                        @if ($ride->smoke == ($findRidePage->smoking_option2->features_setting_id ?? null))
                                                            <a href="javascript:void(0);" 
                                                            onmouseover="toggleModal1('modal-id2', '{{ $postRidePage->smoking_option2_tooltip }}','{{ $findRidePage->smoking_label }}')"
                                                            onclick="toggleModal1('modal-id2', '{{ $postRidePage->smoking_option2_tooltip }}','{{ $findRidePage->smoking_label }}')"><img class="w-8 h-8" src="{{asset('home_page_icons/' . $findRidePage->smoking_option2->icon)}}" alt=""></a>
                                                        @endif
                                                        @if ($ride->animal_friendly == ($findRidePage->pets_allowed_option2->features_setting_id ?? null))
                                                            <a href="javascript:void(0);"
                                                            onmouseover="toggleModal1('modal-id2', '{{ $postRidePage->animals_option2_tooltip }}','{{ $findRidePage->pets_allowed_label }}')"
                                                            onclick="toggleModal1('modal-id2', '{{ $postRidePage->animals_option2_tooltip }}','{{ $findRidePage->pets_allowed_label }}')"><img class="w-8 h-8"
                                                                src="{{asset('home_page_icons/' . $findRidePage->pets_allowed_option2->icon)}}"
                                                                alt=""></a>
                                                        @elseif ($ride->animal_friendly == ($findRidePage->pets_allowed_option3->features_setting_id ?? null))
                                                            <a href="javascript:void(0);" 
                                                            onmouseover="toggleModal1('modal-id2', '{{ $postRidePage->animals_option3_tooltip }}','{{ $findRidePage->pets_allowed_label }}')"
                                                            onclick="toggleModal1('modal-id2', '{{ $postRidePage->animals_option3_tooltip }}','{{ $findRidePage->pets_allowed_label }}')"><img class="w-8 h-8"
                                                                src="{{asset('home_page_icons/' . $findRidePage->pets_allowed_option3->icon)}}"
                                                                alt=""></a>
                                                        @endif
                                                        @if ($ride->luggage == ($findRidePage->luggage_option1->features_setting_id ?? null))
                                                            <a href="javascript:void(0);" 
                                                            onmouseover="toggleModal1('modal-id2', '{{ $postRidePage->luggage_option1_tooltip }}','{{ $findRidePage->luggage_label }}')"
                                                            onclick="toggleModal1('modal-id2', '{{ $postRidePage->luggage_option1_tooltip }}','{{ $findRidePage->luggage_label }}')"><img class="w-8 h-8"
                                                                src="{{asset('home_page_icons/' . $findRidePage->luggage_option1->icon)}}"
                                                                alt=""></a>
                                                        @elseif ($ride->luggage == ($findRidePage->luggage_option2->features_setting_id ?? null))
                                                            <a href="javascript:void(0);"
                                                            onmouseover="toggleModal1('modal-id2', '{{ $postRidePage->luggage_option2_tooltip }}','{{ $findRidePage->luggage_label }}')"
                                                            onclick="toggleModal1('modal-id2', '{{ $postRidePage->luggage_option2_tooltip }}','{{ $findRidePage->luggage_label }}')"><img class="w-8 h-8"
                                                                src="{{asset('home_page_icons/' . $findRidePage->luggage_option2->icon)}}"
                                                                alt=""></a>
                                                        @elseif ($ride->luggage == ($findRidePage->luggage_option3->features_setting_id ?? null))
                                                            <a href="javascript:void(0);"
                                                            onmouseover="toggleModal1('modal-id2', '{{ $postRidePage->luggage_option3_tooltip }}','{{ $findRidePage->luggage_label }}')"
                                                            onclick="toggleModal1('modal-id2', '{{ $postRidePage->luggage_option3_tooltip }}','{{ $findRidePage->luggage_label }}')"><img class="w-8 h-8"
                                                                src="{{asset('home_page_icons/' . $findRidePage->luggage_option3->icon)}}"
                                                                alt=""></a>
                                                        @elseif ($ride->luggage == ($findRidePage->luggage_option4->features_setting_id ?? null))
                                                            <a href="javascript:void(0);"
                                                            onmouseover="toggleModal1('modal-id2', '{{ $postRidePage->luggage_option4_tooltip }}','{{ $findRidePage->luggage_label }}')"
                                                            onclick="toggleModal1('modal-id2', '{{ $postRidePage->luggage_option4_tooltip }}','{{ $findRidePage->luggage_label }}')"><img class="w-8 h-8"
                                                                src="{{asset('home_page_icons/' . $findRidePage->luggage_option4->icon)}}"
                                                                alt=""></a>
                                                        @elseif ($ride->luggage == ($findRidePage->luggage_option5->features_setting_id ?? null))
                                                            <a href="javascript:void(0);"
                                                            onmouseover="toggleModal1('modal-id2', '{{ $postRidePage->luggage_option5_tooltip }}','{{ $findRidePage->luggage_label }}')"
                                                            onclick="toggleModal1('modal-id2', '{{ $postRidePage->luggage_option5_tooltip }}','{{ $findRidePage->luggage_label }}')"><img class="w-8 h-8"
                                                                src="{{asset('home_page_icons/' . $findRidePage->luggage_option5->icon)}}"
                                                                alt=""></a>
                                                        @endif
                                                        @if (in_array($findRidePage->ride_features_option2->features_setting_id ?? null, explode('=', $ride->features)))
                                                            <a href="javascript:void(0);"
                                                            onmouseover="toggleModal1('modal-id2', '{{ $postRidePage->features_option2_tooltip }}', '{{ $findRidePage->ride_features_option2->name ?? $findRidePage->ride_features_option2->label }}')"
                                                            onclick="toggleModal1('modal-id2', '{{ $postRidePage->features_option2_tooltip }}', '{{ $findRidePage->ride_features_option2->name ?? $findRidePage->ride_features_option2->label }}')"><img class="w-8 h-8" src="{{asset('home_page_icons/' . $findRidePage->ride_features_option2->icon)}}" alt=""></a>
                                                        @endif
                                                        @if (in_array($findRidePage->ride_features_option3->features_setting_id ?? null, explode('=', $ride->features)))
                                                            <a href="javascript:void(0);" 
                                                            onmouseover="toggleModal1('modal-id2', '{{ $postRidePage->features_option3_tooltip }}', '{{ $findRidePage->ride_features_option3->name ?? $findRidePage->ride_features_option3->label }}')"
                                                            onclick="toggleModal1('modal-id2', '{{ $postRidePage->features_option3_tooltip }}', '{{ $findRidePage->ride_features_option3->name ?? $findRidePage->ride_features_option3->label }}')"><img class="w-8 h-8" src="{{asset('home_page_icons/' . $findRidePage->ride_features_option3->icon)}}" alt=""></a>
                                                        @endif
                                                        @if (in_array($findRidePage->ride_features_option8->features_setting_id ?? null, explode('=', $ride->features)))
                                                            <a href="javascript:void(0);" 
                                                            onmouseover="toggleModal1('modal-id2', '{{ $postRidePage->features_option8_tooltip }}', '{{ $findRidePage->ride_features_option8->name ?? $findRidePage->ride_features_option8->label }}')"
                                                            onclick="toggleModal1('modal-id2', '{{ $postRidePage->features_option8_tooltip }}', '{{ $findRidePage->ride_features_option8->name ?? $findRidePage->ride_features_option8->label }}')"><img class="w-8 h-8" src="{{asset('home_page_icons/' . $findRidePage->ride_features_option8->icon)}}" alt=""></a>
                                                        @endif
                                                        @if (in_array($findRidePage->ride_features_option9->features_setting_id ?? null, explode('=', $ride->features)))
                                                            <a href="javascript:void(0);" 
                                                            onmouseover="toggleModal1('modal-id2', '{{ $postRidePage->features_option9_tooltip }}', '{{ $findRidePage->ride_features_option9->name ?? $findRidePage->ride_features_option9->label }}')"
                                                            onclick="toggleModal1('modal-id2', '{{ $postRidePage->features_option9_tooltip }}', '{{ $findRidePage->ride_features_option9->name ?? $findRidePage->ride_features_option9->label }}')"><img class="w-8 h-8" src="{{asset('home_page_icons/' . $findRidePage->ride_features_option9->icon)}}" alt=""></a>
                                                        @endif
                                                        @if (in_array($findRidePage->ride_features_option10->features_setting_id ?? null, explode('=', $ride->features)))
                                                            <a href="javascript:void(0);" 
                                                            onmouseover="toggleModal1('modal-id2', '{{ $postRidePage->features_option10_tooltip }}', '{{ $findRidePage->ride_features_option10->name ?? $findRidePage->ride_features_option10->label }}')"
                                                            onclick="toggleModal1('modal-id2', '{{ $postRidePage->features_option10_tooltip }}', '{{ $findRidePage->ride_features_option10->name ?? $findRidePage->ride_features_option10->label }}')"><img class="w-8 h-8" src="{{asset('home_page_icons/' . $findRidePage->ride_features_option10->icon)}}" alt=""></a>
                                                        @endif
                                                        @if (in_array($findRidePage->ride_features_option11->features_setting_id ?? null, explode('=', $ride->features)))
                                                            <a href="javascript:void(0);" 
                                                            onmouseover="toggleModal1('modal-id2', '{{ $postRidePage->features_option11_tooltip }}', '{{ $findRidePage->ride_features_option11->name ?? $findRidePage->ride_features_option11->label }}')"
                                                            onclick="toggleModal1('modal-id2', '{{ $postRidePage->features_option11_tooltip }}', '{{ $findRidePage->ride_features_option11->name ?? $findRidePage->ride_features_option11->label }}')"><img class="w-8 h-8" src="{{asset('home_page_icons/' . $findRidePage->ride_features_option11->icon)}}" alt=""></a>
                                                        @endif
                                                        @if (in_array($findRidePage->ride_features_option12->features_setting_id ?? null, explode('=', $ride->features)))
                                                            <a href="javascript:void(0);" 
                                                            onmouseover="toggleModal1('modal-id2', '{{ $postRidePage->features_option12_tooltip }}', '{{ $findRidePage->ride_features_option12->name ?? $findRidePage->ride_features_option12->label }}')"
                                                            onclick="toggleModal1('modal-id2', '{{ $postRidePage->features_option12_tooltip }}', '{{ $findRidePage->ride_features_option12->name ?? $findRidePage->ride_features_option12->label }}')"><img class="w-8 h-8" src="{{asset('home_page_icons/' . $findRidePage->ride_features_option12->icon)}}" alt=""></a>
                                                        @endif
                                                        @if (in_array($findRidePage->ride_features_option13->features_setting_id ?? null, explode('=', $ride->features)))
                                                            <a href="javascript:void(0);" 
                                                            onmouseover="toggleModal1('modal-id2', '{{ $postRidePage->features_option13_tooltip }}', '{{ $findRidePage->ride_features_option13->name ?? $findRidePage->ride_features_option13->label }}')"
                                                            onclick="toggleModal1('modal-id2', '{{ $postRidePage->features_option13_tooltip }}', '{{ $findRidePage->ride_features_option13->name ?? $findRidePage->ride_features_option13->label }}')"><img class="w-8 h-8" src="{{asset('home_page_icons/' . $findRidePage->ride_features_option13->icon)}}" alt=""></a>
                                                        @endif
                                                        @if (in_array($findRidePage->ride_features_option14->features_setting_id ?? null, explode('=', $ride->features)))
                                                            <a href="javascript:void(0);" 
                                                            onmouseover="toggleModal1('modal-id2', '{{ $postRidePage->features_option14_tooltip }}', '{{ $findRidePage->ride_features_option14->name ?? $findRidePage->ride_features_option14->label }}')"
                                                            onclick="toggleModal1('modal-id2', '{{ $postRidePage->features_option14_tooltip }}', '{{ $findRidePage->ride_features_option14->name ?? $findRidePage->ride_features_option14->label }}')"><img class="w-8 h-8" src="{{asset('home_page_icons/' . $findRidePage->ride_features_option14->icon)}}" alt=""></a>
                                                        @endif
                                                        @if (in_array($findRidePage->ride_features_option15->features_setting_id ?? null, explode('=', $ride->features)))
                                                            <a href="javascript:void(0);" 
                                                            onmouseover="toggleModal1('modal-id2', '{{ $postRidePage->features_option15_tooltip }}', '{{ $findRidePage->ride_features_option15->name ?? $findRidePage->ride_features_option15->label }}')"
                                                            onclick="toggleModal1('modal-id2', '{{ $postRidePage->features_option15_tooltip }}', '{{ $findRidePage->ride_features_option15->name ?? $findRidePage->ride_features_option15->label }}')"><img class="w-8 h-8" src="{{asset('home_page_icons/' . $findRidePage->ride_features_option15->icon)}}" alt=""></a>
                                                        @endif
                                                        @if (in_array($findRidePage->ride_features_option16->features_setting_id ?? null, explode('=', $ride->features)))
                                                            <a href="javascript:void(0);" 
                                                            onmouseover="toggleModal1('modal-id2', '{{ $postRidePage->features_option16_tooltip }}', '{{ $findRidePage->ride_features_option16->name ?? $findRidePage->ride_features_option16->label }}')"
                                                            onclick="toggleModal1('modal-id2', '{{ $postRidePage->features_option16_tooltip }}', '{{ $findRidePage->ride_features_option16->name ?? $findRidePage->ride_features_option16->label }}')"><img class="w-8 h-8" src="{{asset('home_page_icons/' . $findRidePage->ride_features_option16->icon)}}" alt=""></a>
                                                        @endif
                                                        @if (in_array($postRidePage->features_option4->features_setting_id ?? null, explode('=', $ride->features)))
                                                            <a href="javascript:void(0);" 
                                                            onmouseover="toggleModal1('modal-id2', '{{ $postRidePage->features_option4_tooltip }}'), '{{ $findRidePage->ride_features_option4->name ?? $findRidePage->ride_features_option4->label }}'"
                                                            onclick="toggleModal1('modal-id2', '{{ $postRidePage->features_option4_tooltip }}'), '{{ $findRidePage->ride_features_option4->name ?? $findRidePage->ride_features_option4->label }}'"><img class="w-8 h-8" src="{{asset('home_page_icons/' . $postRidePage->features_option4->icon)}}" alt=""></a>
                                                        @endif
                                                        @if (in_array($postRidePage->features_option5->features_setting_id ?? null, explode('=', $ride->features)))
                                                            <a href="javascript:void(0);"
                                                            onmouseover="toggleModal1('modal-id2', '{{ $postRidePage->features_option5_tooltip }}', '{{ $findRidePage->ride_features_option5->name ?? $findRidePage->ride_features_option5->label }}')"
                                                            onclick="toggleModal1('modal-id2', '{{ $postRidePage->features_option5_tooltip }}', '{{ $findRidePage->ride_features_option5->name ?? $findRidePage->ride_features_option5->label }}')"><img class="w-8 h-8" src="{{asset('home_page_icons/' . $postRidePage->features_option5->icon)}}" alt=""></a>
                                                        @endif
                                                        @if (in_array($postRidePage->features_option6->features_setting_id ?? null, explode('=', $ride->features)))
                                                            <a href="javascript:void(0);" 
                                                            onmouseover="toggleModal1('modal-id2', '{{ $postRidePage->features_option6_tooltip }}', '{{ $findRidePage->ride_features_option6->name ?? $findRidePage->ride_features_option6->label }}')"
                                                            onclick="toggleModal1('modal-id2', '{{ $postRidePage->features_option6_tooltip }}', '{{ $findRidePage->ride_features_option6->name ?? $findRidePage->ride_features_option6->label }}')"><img class="w-8 h-8" src="{{asset('home_page_icons/' . $postRidePage->features_option6->icon)}}" alt=""></a>
                                                        @endif
                                                        @if (in_array($postRidePage->features_option7->features_setting_id ?? null, explode('=', $ride->features)))
                                                            <a href="javascript:void(0);" 
                                                            onmouseover="toggleModal1('modal-id2', '{{ $postRidePage->features_option7_tooltip }}', '{{ $findRidePage->ride_features_option7->name ?? $findRidePage->ride_features_option7->label }}')"
                                                            onclick="toggleModal1('modal-id2', '{{ $postRidePage->features_option7_tooltip }}', '{{ $findRidePage->ride_features_option7->name ?? $findRidePage->ride_features_option7->label }}')"><img class="w-8 h-8" src="{{asset('home_page_icons/' . $postRidePage->features_option7->icon)}}" alt=""></a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="border-t border-gray-300 grid grid-cols-1 divide-x divide-gray-300">
                                                <div class="flex items-center justify-between p-4 w-full">
                                                    <div class="flex items-center space-x-2">
                                                        {{-- <div class="w-12 h-12 rounded-full overflow-hidden">
                                                            <img class="w-full h-full object-contain"
                                                                src="{{ $ride->driver?->profile_image }}" alt="">
                                                        </div> --}}
                                                        <div class="text-center">
                                                            <p class="font-semibold">
                                                                {{-- @isset($findRidePage->card_section_driver)
                                                                    {{ $findRidePage->card_section_driver }}
                                                                @endisset --}}
                                                                <span>
                                                                    @if ($ride->driver?->type === '2')
                                                                        {{ $ride->driver?->last_name }}
                                                                    @elseif ($ride->driver?->type === '3')
                                                                        {{ $ride->driver?->first_name }} {{ $ride->driver?->last_name }}
                                                                    @else
                                                                        {{ $ride->driver?->first_name }}
                                                                    @endif
                                                                    {{-- @if($ride->driver?->gender && $ride->driver?->gender !== 'Prefer not to say')
                                                                    ({{ strtoupper(substr($ride->driver?->gender, 0, 1)) }})
                                                                @endif --}}
                                                                </span></p>
                                                            @php
                                                                // Calculate the age based on the driver's date of birth
                                                                $dob = \Carbon\Carbon::parse($ride->driver?->dob);
                                                                $age = $dob->diffInYears(\Carbon\Carbon::now());
                                                            @endphp
                                                            <p class="mb-0 text-sm">
                                                                @isset($findRidePage->card_section_age)
                                                                    {{ $findRidePage->card_section_age }}
                                                                @endisset
                                                                {{ $age }}</p>
                                                            <p class="mb-0 text-sm">
                                                                {{  $ride->driver?->rides()
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
                                                                @isset($findRidePage->card_section_driven)
                                                                    {{ $findRidePage->card_section_driven }}
                                                                @endisset
                                                            </p>
                                                            @php
                                                                $filteredRatings = $ratings->where('status', 1)->where('type', '1')->filter(function ($rating) use ($ride) {
                                                                    return $rating->ride && $rating->ride->added_by === $ride->added_by;
                                                                });

                                                                $totalAverage = $filteredRatings->avg('average_rating') ?? 0;
                                                            @endphp
                                                        </div>
                                                    </div>
                                                    <div class="">
                                                        <span
                                                            class="font-semibold text-gray-800">{{ number_format($totalAverage, 1) }}</span>
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                            fill="currentColor"
                                                            class="w-6 h-6 text-yellow-500 stroke-gray-600">
                                                            <path fill-rule="evenodd"
                                                                d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z"
                                                                clip-rule="evenodd"></path>
                                                        </svg>
                                                    </div>
                                                </div>
                                                {{-- <div>
                                                    <div class="flex items-center justify-between px-4">
                                                        <p class="font-semibold">
                                                            @isset($findRidePage->card_section_booking_fee)
                                                                {{ $findRidePage->card_section_booking_fee }}
                                                            @endisset
                                                        </p>
                                                        <p class="">
                                                            @if (auth()->user())
                                                                ${{ number_format(floatval($ride->bookings->where('user_id', auth()->user()->id)->where('status', '<>', 3)->where('status', '<>', 4)->sum('booking_credit')), 2) }}
                                                            @else
                                                                $0.00
                                                            @endif
                                                        </p>
                                                    </div>
                                                    <div class="flex items-center justify-between px-4">
                                                        <p class="font-semibold">
                                                            @isset($findRidePage->card_section_seats_fee)
                                                                {{ $findRidePage->card_section_seats_fee }}
                                                            @endisset
                                                        </p>
                                                        <p class="">
                                                            @if (auth()->user())
                                                                ${{ number_format(floatval(($ride->bookings()->where('user_id', auth()->user()->id)->where('status', '<>', 3)->where('status', '<>', 4)->whereHas('passenger', function($query) { $query->whereNull('deleted_at'); })->sum('seats') * floatval($ride->rideDetail[0]->price))), 2) }}
                                                            @else
                                                                $0.00
                                                            @endif
                                                        </p>
                                                    </div>
                                                    <div class="flex items-center justify-between px-4">
                                                        <p class="font-semibold">
                                                            @isset($findRidePage->card_section_amount)
                                                                {{ $findRidePage->card_section_amount }}
                                                            @endisset
                                                        </p>
                                                        <p class="">
                                                            @if (auth()->user())
                                                                ${{ number_format(floatval(($ride->bookings()->where('user_id', auth()->user()->id)->where('status', '<>', 3)->where('status', '<>', 4)->whereHas('passenger', function($query) { $query->whereNull('deleted_at'); })->sum('seats') * floatval($ride->rideDetail[0]->price)) + $ride->bookings->where('user_id', auth()->user()->id)->where('status', '<>', 3)->where('status', '<>', 4)->sum('booking_credit')), 2) }}
                                                            @else
                                                                $0.00
                                                            @endif
                                                        </p>
                                                    </div>
                                                </div> --}}
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        @endif
                        @if ($paginatedRides->filter(fn($ride) => $ride->type === 'otherRide')->count() > 0)
                            <h3 class="text-primary">{{ $findRidePage->more_rides_pink_ride_label ?? 'More rides from' }} {{ $request->from }} {{ $findRidePage->to_pink_ride_label ?? 'to' }} {{ $request->to }}. {{ $findRidePage->imp_extra_care_ride_label ?? 'Important: these are NOT Extra-Care ride' }}</h3>
                            @foreach ($paginatedRides->filter(fn($ride) => $ride->type === 'otherRide') as $ride)
                                @php
                                    $from = $ride->rideDetail[0]->departure;
                                    $to = $ride->rideDetail[0]->destination;
                                @endphp
                                <div class="relative">
                                    <div class="absolute right-4 top-8">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                            class="w-6 h-6 -mt-4 cursor-pointer ride-remove-btn"
                                            data-ride-id="{{ $ride->id }}">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    @if (auth()->user())
                                        @php
                                            $user_id = auth()->user()->id;

                                            // Assuming $ratings is a collection
                                            $filteredRatings = $ratings->where('status', 1)->where('type', '2')->filter(function ($rating) use ($user_id) {
                                                // Check if booking exists and is not null before accessing user_id
                                                return $rating->booking && $rating->booking->user_id === $user_id;
                                            });

                                            $totalAverage = $filteredRatings->avg('average_rating') ?? 0;
                                        @endphp
                                    @endif
                                    <a
                                        @if ($ride->status === '2') href=""
                                        @elseif (auth()->user() &&
                                            in_array($findRidePage->ride_features_option1->features_setting_id ?? null, explode('=', $ride->features)) &&
                                            auth()->user()->gender != 'female' && auth()->user()->address != '')
                                            href="javascript:void(0);" onclick="toggleModal1('modal-id1', 'Only females can select this ride')"
                                        @elseif (auth()->user() &&
                                            in_array($findRidePage->ride_features_option2->features_setting_id ?? null, explode('=', $ride->features)))
                                            @if ($filteredRatings->count() === 0 && auth()->user()->address == '')
                                                href="javascript:void(0);" onclick="toggleModal1('modal-id1', 'Only passengers with complete address can select this ride')"
                                            @elseif ($filteredRatings->count() === 0 && auth()->user()->address !== '')
                                                href="{{ route('ride_detail', ['lang' => $selectedLanguage->abbreviation, 'departure' => $ride->rideDetail[0]->departure, 'destination' => $ride->rideDetail[0]->destination, 'id' => $ride->id]) }}"
                                            @elseif ($totalAverage < 4.5 && auth()->user()->address !== '')
                                                href="javascript:void(0);" onclick="toggleModal1('modal-id1', 'Only 5 star passengers can select this ride')"
                                            @elseif ($totalAverage < 4.5 && auth()->user()->address == '')
                                                href="javascript:void(0);" onclick="toggleModal1('modal-id1', 'Only 5 star passengers with complete address can select this ride')"
                                            @else
                                                href="{{ route('ride_detail', ['lang' => $selectedLanguage->abbreviation, 'departure' => $ride->rideDetail[0]->departure, 'destination' => $ride->rideDetail[0]->destination, 'id' => $ride->id]) }}"
                                            @endif
                                        @elseif (auth()->user() &&
                                            in_array('I only want passengers with reviews', explode('=', $ride->features)))
                                            @if ($filteredRatings->count() === 0)
                                                href="javascript:void(0);" onclick="toggleModal1('modal-id1', 'Driver only want passengers with reviews')"
                                            @else
                                                href="{{ route('ride_detail', ['lang' => $selectedLanguage->abbreviation, 'departure' => $ride->rideDetail[0]->departure, 'destination' => $ride->rideDetail[0]->destination, 'id' => $ride->id]) }}"
                                            @endif
                                        @elseif (auth()->user() &&
                                            in_array($findRidePage->ride_features_option15->features_setting_id ?? null, explode('=', $ride->features)))
                                            @if ($totalAverage < 3)
                                                href="javascript:void(0);" onclick="toggleModal1('modal-id1', 'Driver want only passengers with-3 star reviews above')"
                                            @else
                                                href="{{ route('ride_detail', ['lang' => $selectedLanguage->abbreviation, 'departure' => $ride->rideDetail[0]->departure, 'destination' => $ride->rideDetail[0]->destination, 'id' => $ride->id]) }}"
                                            @endif
                                        @elseif (auth()->user() &&
                                            in_array($findRidePage->ride_features_option14->features_setting_id ?? null, explode('=', $ride->features)))
                                            @if ($totalAverage < 4)
                                                href="javascript:void(0);" onclick="toggleModal1('modal-id1', 'Driver want only passengers with-4 star reviews above')"
                                            @else
                                                href="{{ route('ride_detail', ['lang' => $selectedLanguage->abbreviation, 'departure' => $ride->rideDetail[0]->departure, 'destination' => $ride->rideDetail[0]->destination, 'id' => $ride->id]) }}"
                                            @endif
                                        @elseif (auth()->user() &&
                                            in_array($findRidePage->ride_features_option13->features_setting_id ?? null, explode('=', $ride->features)))
                                            @if ($totalAverage < 4.5)
                                                href="javascript:void(0);" onclick="toggleModal1('modal-id1', 'Driver want only passengers with-5 star reviews above')"
                                            @else
                                                href="{{ route('ride_detail', ['lang' => $selectedLanguage->abbreviation, 'departure' => $ride->rideDetail[0]->departure, 'destination' => $ride->rideDetail[0]->destination, 'id' => $ride->id]) }}"
                                            @endif
                                        @else
                                            href="{{ route('ride_detail', ['lang' => $selectedLanguage->abbreviation, 'departure' => $ride->rideDetail[0]->departure, 'destination' => $ride->rideDetail[0]->destination, 'id' => $ride->id]) }}" @endif>
                                        <div class="bg-white rounded-lg shadow-3xl border-[3px] border-solid @if ($ride->status === '2') border-red-500 @elseif(isset($findRidePage->ride_features_option1->features_setting_id) &&
                                                in_array($findRidePage->ride_features_option1->features_setting_id ?? null, explode('=', $ride->features))) border-pink-500 @elseif(isset($findRidePage->ride_features_option2->features_setting_id) &&
                                                in_array($findRidePage->ride_features_option2->features_setting_id ?? null, explode('=', $ride->features))) border-green-500 @else border-gray-100 @endif"
                                            id="ride-{{ $ride->id }}">
                                            <div class="flex items-center justify-between pb-0 p-4">
                                                <div class="flex items-center gap-2">
                                                    <p class="flex items-center space-x-2 font-semibold">
                                                        {{ \Carbon\Carbon::parse($ride->date)->format('F d, Y') }}
                                                        @isset($findRidePage->card_section_at_label)
                                                            {{ $findRidePage->card_section_at_label }}
                                                        @endisset
                                                        {{ \Carbon\Carbon::parse($ride->time)->format('h:i A') == '12:00 PM' ? '12 noon' : (\Carbon\Carbon::parse($ride->time)->format('h:i A') == '12:00 AM' ? '12 midnight' : \Carbon\Carbon::parse($ride->time)->format('h:i A')) }}
                                                    </p>
                                                    @if (in_array($findRidePage->ride_features_option1->features_setting_id ?? null, explode('=', $ride->features)))
                                                        <button type="button" onclick="toggleModal1('modal-id2', '{{ $postRidePage->features_option1_tooltip }}', '{{ $findRidePage->ride_features_option1->name ?? $findRidePage->ride_features_option1->label }}')">
                                                            <img class="w-8 h-8" src="{{asset('home_page_icons/' . $findRidePage->ride_features_option1->icon)}}" alt="">
                                                        </button>
                                                    @endif
                                                </div>
                                                <div class="pr-8">
                                                    <p class="font-medium">
                                                        {{ intval($ride->seats) - intval($ride->bookings()->where('status', '<>', 3)->where('status', '<>', 4)->whereHas('passenger', function($query) { $query->whereNull('deleted_at'); })->sum('seats')) }}
                                                        @isset($findRidePage->card_section_seats_left)
                                                            {{ $findRidePage->card_section_seats_left }}
                                                        @endisset
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="flex justify-between px-4">
                                                <div class="md:w-2/3">
                                                    <div class="relative mt-5 text-left">
                                                        <div class="flex items-center relative">
                                                            <div class="border-r-2 border-black border-solid absolute h-full left-3 md:left-6 top-2 z-10">
                                                                <span
                                                                    class="bg-primary rounded-full w-7 h-7 -top-[2px] -ml-[13px] absolute flex justify-center items-center">
                                                                    <img class="w-5 h-5 object-contain" src="{{ asset('./images/new-21-search-bar-from.png')}}" alt="">
                                                                </span>
                                                            </div>
                                                            <div class="ml-20">
                                                                <div class="font-bold text-black">From</div>
                                                                <div class="text-primary md:mb-4">{{ $ride->pickup }},
                                                                    {{ $from }}</div>
                                                            </div>
                                                        </div>

                                                        <div class="flex items-center relative">
                                                            <div class="border-r-2 border-black border-solid absolute h-0 left-3 md:left-5 top-2 z-10">
                                                                <span
                                                                    class="bg-gray-200 rounded-full w-7 h-7 -top-[6px] -ml-[12px] md:-ml-[9px] absolute flex justify-center items-center">
                                                                    <img class="w-5 h-5 object-contain" src="{{ asset('./images/new-21-search-bar-to.png')}}" alt="">
                                                                </span>
                                                            </div>
                                                            <div class="ml-20">
                                                                <div class="font-bold text-black">To</div>
                                                                <div class="text-primary md:mb-4">{{ $ride->dropoff }},
                                                                    {{ $to }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mt-4">
                                                    <p class="text-xl font-semibold text-primary">
                                                        <div class="flex items-center gap-2">
                                                            @if (isset($firm_cancellation_discount) && $firm_cancellation_discount!='' && $ride->booking_type == $postRidePage->cancellation_policy_label2->features_setting_id)
                                                                <span class="line-through">
                                                                    ${{ number_format(floatval($ride->rideDetail[0]->price), 2) }}
                                                                    </span>
                                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
                                                                    </svg>

                                                                    <span>

                                                                        ${{ $ride->rideDetail[0]->price - ($ride->rideDetail[0]->price * $firm_cancellation_discount) / 100 }}
                                                                    </span>

                                                                @else
                                                                    ${{ number_format(floatval($ride->rideDetail[0]->price), 2) }}
                                                                @endif

                                                                <small>
                                                                    @isset($findRidePage->card_section_per_seat)
                                                                        {{ $findRidePage->card_section_per_seat }}
                                                                    @endisset
                                                                </small>

                                                                <div class="sups inline-flex relative">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle-fill text-black peer" viewBox="0 0 16 16">
                                                                        <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                                                                    </svg>
                                                                    <div
                                                                      class="absolute tooltip payment_tooltiptext_position top-8 group-hover:flex hidden peer-hover:flex bg-blue-500 px-4 py-2 rounded right-0 w-60 z-10"
                                                                    >
                                                                        <p class="text-white font-semibold text-start text-sm lg:text-base">
                                                                            {!! nl2br($findRidePage->firm_cancellation_tooltip) ?? 'This ride has the Firm cancellation policy, so its booking price is reduced by 10%' !!}
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                        </div>
                                                    </p>
                                                    {{-- <p class="text-xl font-semibold text-primary">${{ number_format(floatval($ride->rideDetail[0]->price), 2) }}
                                                        <small>
                                                            @isset($findRidePage->card_section_per_seat)
                                                                {{ $findRidePage->card_section_per_seat }}
                                                            @endisset
                                                        </small>
                                                    </p> --}}
                                                </div>
                                            </div>
                                            <div class="border-t border-gray-300 grid grid-cols-4 divide-x divide-gray-300">
                                                {{-- <div class="flex items-center justify-between p-4">
                                                    <p class="font-semibold">
                                                        @isset($findRidePage->card_section_booked)
                                                            {{ $findRidePage->card_section_booked }}
                                                        @endisset
                                                    </p>
                                                    <p class="">
                                                        @if (auth()->user())
                                                            {{ $ride->bookings()->where('user_id', auth()->user()->id)->where('status', '<>', 3)->where('status', '<>', 4)->whereHas('passenger', function($query) { $query->whereNull('deleted_at'); })->sum('seats') }}
                                                        @else
                                                            0
                                                        @endif
                                                        @isset($findRidePage->card_section_seats)
                                                            {{ $findRidePage->card_section_seats }}
                                                        @endisset
                                                    </p>
                                                </div> --}}
                                                <div class="col-span-4 p-4 flex justify-start items-center no-scrollbar overflow-x-auto space-x-2 md:space-x-4">
                                                    @unless(old('skip_vehicle', $ride->skip_vehicle) == '0')
                                                        @if ($ride->remove_car_image == 0)
                                                            <div class="flex-none w-12 h-12 bg-gray-100 border rounded-full">
                                                                <img class="w-full h-full object-cover rounded-full"
                                                                    src="{{ $ride->car_image }}"
                                                                    alt="">
                                                            </div>
                                                        @endif
                                                    @endunless

                                                    <div class="flex items-center space-x-1">
                                                        @if ($ride->payment_method == ($findRidePage->payment_methods_option2->features_setting_id ?? null))
                                                            <a href="javascript:void(0);"
                                                            onmouseover="toggleModal1('modal-id2', '{{ $postRidePage->payment_methods_option1_tooltip }}', '{{ $findRidePage->payment_methods_option2->name ?? $findRidePage->payment_methods_option2->label }}')"
                                                            onclick="toggleModal1('modal-id2', '{{ $postRidePage->payment_methods_option1_tooltip }}', '{{ $findRidePage->payment_methods_option2->name ?? $findRidePage->payment_methods_option2->label }}')">
                                                                <img class="w-8 h-8" src="{{asset('home_page_icons/' . $findRidePage->payment_methods_option2->icon)}}" alt="">
                                                            </a>
                                                        @elseif ($ride->payment_method == ($findRidePage->payment_methods_option3->features_setting_id ?? null))
                                                            <a href="javascript:void(0);" 
                                                            onmouseover="toggleModal1('modal-id2', '{{ $postRidePage->payment_methods_option2_tooltip }}', '{{ $findRidePage->payment_methods_option3->name ?? $findRidePage->payment_methods_option3->label }}')"
                                                            onclick="toggleModal1('modal-id2', '{{ $postRidePage->payment_methods_option2_tooltip }}', '{{ $findRidePage->payment_methods_option3->name ?? $findRidePage->payment_methods_option3->label }}')">
                                                                <img class="w-8 h-8" src="{{asset('home_page_icons/' . $findRidePage->payment_methods_option3->icon)}}" alt="">
                                                            </a>
                                                        @elseif ($ride->payment_method == ($findRidePage->payment_methods_option4->features_setting_id ?? null))
                                                            <a href="javascript:void(0);"
                                                            onmouseover="toggleModal1('modal-id2', '{{ $postRidePage->payment_methods_option3_tooltip }}', '{{ $findRidePage->payment_methods_option4->name ?? $findRidePage->payment_methods_option4->label }}')"
                                                            onclick="toggleModal1('modal-id2', '{{ $postRidePage->payment_methods_option3_tooltip }}', '{{ $findRidePage->payment_methods_option4->name ?? $findRidePage->payment_methods_option4->label }}')">
                                                                <img class="w-8 h-8" src="{{asset('home_page_icons/' . $findRidePage->payment_methods_option4->icon)}}" alt="">
                                                            </a>
                                                        @endif
                                                        @if ($ride->smoke == ($findRidePage->smoking_option2->features_setting_id ?? null))
                                                            <a href="javascript:void(0);" 
                                                            onmouseover="toggleModal1('modal-id2', '{{ $postRidePage->smoking_option2_tooltip }}','{{ $findRidePage->smoking_label }}')"
                                                            onclick="toggleModal1('modal-id2', '{{ $postRidePage->smoking_option2_tooltip }}','{{ $findRidePage->smoking_label }}')"><img class="w-8 h-8" src="{{asset('home_page_icons/' . $findRidePage->smoking_option2->icon)}}" alt=""></a>
                                                        @endif
                                                        @if ($ride->animal_friendly == ($findRidePage->pets_allowed_option2->features_setting_id ?? null))
                                                            <a href="javascript:void(0);"
                                                            onmouseover="toggleModal1('modal-id2', '{{ $postRidePage->animals_option2_tooltip }}','{{ $findRidePage->pets_allowed_label }}')"
                                                            onclick="toggleModal1('modal-id2', '{{ $postRidePage->animals_option2_tooltip }}','{{ $findRidePage->pets_allowed_label }}')"><img class="w-8 h-8"
                                                                src="{{asset('home_page_icons/' . $findRidePage->pets_allowed_option2->icon)}}"
                                                                alt=""></a>
                                                        @elseif ($ride->animal_friendly == ($findRidePage->pets_allowed_option3->features_setting_id ?? null))
                                                            <a href="javascript:void(0);" 
                                                            onmouseover="toggleModal1('modal-id2', '{{ $postRidePage->animals_option3_tooltip }}','{{ $findRidePage->pets_allowed_label }}')"
                                                            onclick="toggleModal1('modal-id2', '{{ $postRidePage->animals_option3_tooltip }}','{{ $findRidePage->pets_allowed_label }}')"><img class="w-8 h-8"
                                                                src="{{asset('home_page_icons/' . $findRidePage->pets_allowed_option3->icon)}}"
                                                                alt=""></a>
                                                        @endif
                                                        @if ($ride->luggage == ($findRidePage->luggage_option1->features_setting_id ?? null))
                                                            <a href="javascript:void(0);" 
                                                            onmouseover="toggleModal1('modal-id2', '{{ $postRidePage->luggage_option1_tooltip }}','{{ $findRidePage->luggage_label }}')"
                                                            onclick="toggleModal1('modal-id2', '{{ $postRidePage->luggage_option1_tooltip }}','{{ $findRidePage->luggage_label }}')"><img class="w-8 h-8"
                                                                src="{{asset('home_page_icons/' . $findRidePage->luggage_option1->icon)}}"
                                                                alt=""></a>
                                                        @elseif ($ride->luggage == ($findRidePage->luggage_option2->features_setting_id ?? null))
                                                            <a href="javascript:void(0);"
                                                            onmouseover="toggleModal1('modal-id2', '{{ $postRidePage->luggage_option2_tooltip }}','{{ $findRidePage->luggage_label }}')"
                                                            onclick="toggleModal1('modal-id2', '{{ $postRidePage->luggage_option2_tooltip }}','{{ $findRidePage->luggage_label }}')"><img class="w-8 h-8"
                                                                src="{{asset('home_page_icons/' . $findRidePage->luggage_option2->icon)}}"
                                                                alt=""></a>
                                                        @elseif ($ride->luggage == ($findRidePage->luggage_option3->features_setting_id ?? null))
                                                            <a href="javascript:void(0);"
                                                            onmouseover="toggleModal1('modal-id2', '{{ $postRidePage->luggage_option3_tooltip }}','{{ $findRidePage->luggage_label }}')"
                                                            onclick="toggleModal1('modal-id2', '{{ $postRidePage->luggage_option3_tooltip }}','{{ $findRidePage->luggage_label }}')"><img class="w-8 h-8"
                                                                src="{{asset('home_page_icons/' . $findRidePage->luggage_option3->icon)}}"
                                                                alt=""></a>
                                                        @elseif ($ride->luggage == ($findRidePage->luggage_option4->features_setting_id ?? null))
                                                            <a href="javascript:void(0);"
                                                            onmouseover="toggleModal1('modal-id2', '{{ $postRidePage->luggage_option4_tooltip }}','{{ $findRidePage->luggage_label }}')"
                                                            onclick="toggleModal1('modal-id2', '{{ $postRidePage->luggage_option4_tooltip }}','{{ $findRidePage->luggage_label }}')"><img class="w-8 h-8"
                                                                src="{{asset('home_page_icons/' . $findRidePage->luggage_option4->icon)}}"
                                                                alt=""></a>
                                                        @elseif ($ride->luggage == ($findRidePage->luggage_option5->features_setting_id ?? null))
                                                            <a href="javascript:void(0);"
                                                            onmouseover="toggleModal1('modal-id2', '{{ $postRidePage->luggage_option5_tooltip }}','{{ $findRidePage->luggage_label }}')"
                                                            onclick="toggleModal1('modal-id2', '{{ $postRidePage->luggage_option5_tooltip }}','{{ $findRidePage->luggage_label }}')"><img class="w-8 h-8"
                                                                src="{{asset('home_page_icons/' . $findRidePage->luggage_option5->icon)}}"
                                                                alt=""></a>
                                                        @endif
                                                        @if (in_array($findRidePage->ride_features_option2->features_setting_id ?? null, explode('=', $ride->features)))
                                                            <a href="javascript:void(0);"
                                                            onmouseover="toggleModal1('modal-id2', '{{ $postRidePage->features_option2_tooltip }}', '{{ $findRidePage->ride_features_option2->name ?? $findRidePage->ride_features_option2->label }}')"
                                                            onclick="toggleModal1('modal-id2', '{{ $postRidePage->features_option2_tooltip }}', '{{ $findRidePage->ride_features_option2->name ?? $findRidePage->ride_features_option2->label }}')"><img class="w-8 h-8" src="{{asset('home_page_icons/' . $findRidePage->ride_features_option2->icon)}}" alt=""></a>
                                                        @endif
                                                        @if (in_array($findRidePage->ride_features_option3->features_setting_id ?? null, explode('=', $ride->features)))
                                                            <a href="javascript:void(0);" 
                                                            onmouseover="toggleModal1('modal-id2', '{{ $postRidePage->features_option3_tooltip }}', '{{ $findRidePage->ride_features_option3->name ?? $findRidePage->ride_features_option3->label }}')"
                                                            onclick="toggleModal1('modal-id2', '{{ $postRidePage->features_option3_tooltip }}', '{{ $findRidePage->ride_features_option3->name ?? $findRidePage->ride_features_option3->label }}')"><img class="w-8 h-8" src="{{asset('home_page_icons/' . $findRidePage->ride_features_option3->icon)}}" alt=""></a>
                                                        @endif
                                                        @if (in_array($findRidePage->ride_features_option8->features_setting_id ?? null, explode('=', $ride->features)))
                                                            <a href="javascript:void(0);" 
                                                            onmouseover="toggleModal1('modal-id2', '{{ $postRidePage->features_option8_tooltip }}', '{{ $findRidePage->ride_features_option8->name ?? $findRidePage->ride_features_option8->label }}')"
                                                            onclick="toggleModal1('modal-id2', '{{ $postRidePage->features_option8_tooltip }}', '{{ $findRidePage->ride_features_option8->name ?? $findRidePage->ride_features_option8->label }}')"><img class="w-8 h-8" src="{{asset('home_page_icons/' . $findRidePage->ride_features_option8->icon)}}" alt=""></a>
                                                        @endif
                                                        @if (in_array($findRidePage->ride_features_option9->features_setting_id ?? null, explode('=', $ride->features)))
                                                            <a href="javascript:void(0);" 
                                                            onmouseover="toggleModal1('modal-id2', '{{ $postRidePage->features_option9_tooltip }}', '{{ $findRidePage->ride_features_option9->name ?? $findRidePage->ride_features_option9->label }}')"
                                                            onclick="toggleModal1('modal-id2', '{{ $postRidePage->features_option9_tooltip }}', '{{ $findRidePage->ride_features_option9->name ?? $findRidePage->ride_features_option9->label }}')"><img class="w-8 h-8" src="{{asset('home_page_icons/' . $findRidePage->ride_features_option9->icon)}}" alt=""></a>
                                                        @endif
                                                        @if (in_array($findRidePage->ride_features_option10->features_setting_id ?? null, explode('=', $ride->features)))
                                                            <a href="javascript:void(0);" 
                                                            onmouseover="toggleModal1('modal-id2', '{{ $postRidePage->features_option10_tooltip }}', '{{ $findRidePage->ride_features_option10->name ?? $findRidePage->ride_features_option10->label }}')"
                                                            onclick="toggleModal1('modal-id2', '{{ $postRidePage->features_option10_tooltip }}', '{{ $findRidePage->ride_features_option10->name ?? $findRidePage->ride_features_option10->label }}')"><img class="w-8 h-8" src="{{asset('home_page_icons/' . $findRidePage->ride_features_option10->icon)}}" alt=""></a>
                                                        @endif
                                                        @if (in_array($findRidePage->ride_features_option11->features_setting_id ?? null, explode('=', $ride->features)))
                                                            <a href="javascript:void(0);" 
                                                            onmouseover="toggleModal1('modal-id2', '{{ $postRidePage->features_option11_tooltip }}', '{{ $findRidePage->ride_features_option11->name ?? $findRidePage->ride_features_option11->label }}')"
                                                            onclick="toggleModal1('modal-id2', '{{ $postRidePage->features_option11_tooltip }}', '{{ $findRidePage->ride_features_option11->name ?? $findRidePage->ride_features_option11->label }}')"><img class="w-8 h-8" src="{{asset('home_page_icons/' . $findRidePage->ride_features_option11->icon)}}" alt=""></a>
                                                        @endif
                                                        @if (in_array($findRidePage->ride_features_option12->features_setting_id ?? null, explode('=', $ride->features)))
                                                            <a href="javascript:void(0);" 
                                                            onmouseover="toggleModal1('modal-id2', '{{ $postRidePage->features_option12_tooltip }}', '{{ $findRidePage->ride_features_option12->name ?? $findRidePage->ride_features_option12->label }}')"
                                                            onclick="toggleModal1('modal-id2', '{{ $postRidePage->features_option12_tooltip }}', '{{ $findRidePage->ride_features_option12->name ?? $findRidePage->ride_features_option12->label }}')"><img class="w-8 h-8" src="{{asset('home_page_icons/' . $findRidePage->ride_features_option12->icon)}}" alt=""></a>
                                                        @endif
                                                        @if (in_array($findRidePage->ride_features_option13->features_setting_id ?? null, explode('=', $ride->features)))
                                                            <a href="javascript:void(0);" 
                                                            onmouseover="toggleModal1('modal-id2', '{{ $postRidePage->features_option13_tooltip }}', '{{ $findRidePage->ride_features_option13->name ?? $findRidePage->ride_features_option13->label }}')"
                                                            onclick="toggleModal1('modal-id2', '{{ $postRidePage->features_option13_tooltip }}', '{{ $findRidePage->ride_features_option13->name ?? $findRidePage->ride_features_option13->label }}')"><img class="w-8 h-8" src="{{asset('home_page_icons/' . $findRidePage->ride_features_option13->icon)}}" alt=""></a>
                                                        @endif
                                                        @if (in_array($findRidePage->ride_features_option14->features_setting_id ?? null, explode('=', $ride->features)))
                                                            <a href="javascript:void(0);" 
                                                            onmouseover="toggleModal1('modal-id2', '{{ $postRidePage->features_option14_tooltip }}', '{{ $findRidePage->ride_features_option14->name ?? $findRidePage->ride_features_option14->label }}')"
                                                            onclick="toggleModal1('modal-id2', '{{ $postRidePage->features_option14_tooltip }}', '{{ $findRidePage->ride_features_option14->name ?? $findRidePage->ride_features_option14->label }}')"><img class="w-8 h-8" src="{{asset('home_page_icons/' . $findRidePage->ride_features_option14->icon)}}" alt=""></a>
                                                        @endif
                                                        @if (in_array($findRidePage->ride_features_option15->features_setting_id ?? null, explode('=', $ride->features)))
                                                            <a href="javascript:void(0);" 
                                                            onmouseover="toggleModal1('modal-id2', '{{ $postRidePage->features_option15_tooltip }}', '{{ $findRidePage->ride_features_option15->name ?? $findRidePage->ride_features_option15->label }}')"
                                                            onclick="toggleModal1('modal-id2', '{{ $postRidePage->features_option15_tooltip }}', '{{ $findRidePage->ride_features_option15->name ?? $findRidePage->ride_features_option15->label }}')"><img class="w-8 h-8" src="{{asset('home_page_icons/' . $findRidePage->ride_features_option15->icon)}}" alt=""></a>
                                                        @endif
                                                        @if (in_array($findRidePage->ride_features_option16->features_setting_id ?? null, explode('=', $ride->features)))
                                                            <a href="javascript:void(0);" 
                                                            onmouseover="toggleModal1('modal-id2', '{{ $postRidePage->features_option16_tooltip }}', '{{ $findRidePage->ride_features_option16->name ?? $findRidePage->ride_features_option16->label }}')"
                                                            onclick="toggleModal1('modal-id2', '{{ $postRidePage->features_option16_tooltip }}', '{{ $findRidePage->ride_features_option16->name ?? $findRidePage->ride_features_option16->label }}')"><img class="w-8 h-8" src="{{asset('home_page_icons/' . $findRidePage->ride_features_option16->icon)}}" alt=""></a>
                                                        @endif
                                                        @if (in_array($postRidePage->features_option4->features_setting_id ?? null, explode('=', $ride->features)))
                                                            <a href="javascript:void(0);" 
                                                            onmouseover="toggleModal1('modal-id2', '{{ $postRidePage->features_option4_tooltip }}'), '{{ $findRidePage->ride_features_option4->name ?? $findRidePage->ride_features_option4->label }}'"
                                                            onclick="toggleModal1('modal-id2', '{{ $postRidePage->features_option4_tooltip }}'), '{{ $findRidePage->ride_features_option4->name ?? $findRidePage->ride_features_option4->label }}'"><img class="w-8 h-8" src="{{asset('home_page_icons/' . $postRidePage->features_option4->icon)}}" alt=""></a>
                                                        @endif
                                                        @if (in_array($postRidePage->features_option5->features_setting_id ?? null, explode('=', $ride->features)))
                                                            <a href="javascript:void(0);"
                                                            onmouseover="toggleModal1('modal-id2', '{{ $postRidePage->features_option5_tooltip }}', '{{ $findRidePage->ride_features_option5->name ?? $findRidePage->ride_features_option5->label }}')"
                                                            onclick="toggleModal1('modal-id2', '{{ $postRidePage->features_option5_tooltip }}', '{{ $findRidePage->ride_features_option5->name ?? $findRidePage->ride_features_option5->label }}')"><img class="w-8 h-8" src="{{asset('home_page_icons/' . $postRidePage->features_option5->icon)}}" alt=""></a>
                                                        @endif
                                                        @if (in_array($postRidePage->features_option6->features_setting_id ?? null, explode('=', $ride->features)))
                                                            <a href="javascript:void(0);" 
                                                            onmouseover="toggleModal1('modal-id2', '{{ $postRidePage->features_option6_tooltip }}', '{{ $findRidePage->ride_features_option6->name ?? $findRidePage->ride_features_option6->label }}')"
                                                            onclick="toggleModal1('modal-id2', '{{ $postRidePage->features_option6_tooltip }}', '{{ $findRidePage->ride_features_option6->name ?? $findRidePage->ride_features_option6->label }}')"><img class="w-8 h-8" src="{{asset('home_page_icons/' . $postRidePage->features_option6->icon)}}" alt=""></a>
                                                        @endif
                                                        @if (in_array($postRidePage->features_option7->features_setting_id ?? null, explode('=', $ride->features)))
                                                            <a href="javascript:void(0);" 
                                                            onmouseover="toggleModal1('modal-id2', '{{ $postRidePage->features_option7_tooltip }}', '{{ $findRidePage->ride_features_option7->name ?? $findRidePage->ride_features_option7->label }}')"
                                                            onclick="toggleModal1('modal-id2', '{{ $postRidePage->features_option7_tooltip }}', '{{ $findRidePage->ride_features_option7->name ?? $findRidePage->ride_features_option7->label }}')"><img class="w-8 h-8" src="{{asset('home_page_icons/' . $postRidePage->features_option7->icon)}}" alt=""></a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="border-t border-gray-300 grid grid-cols-1 divide-x divide-gray-300">
                                                <div class="flex items-center justify-between p-4 w-full">
                                                    <div class="flex items-center space-x-2">
                                                        {{-- <div class="w-12 h-12 rounded-full overflow-hidden">
                                                            <img class="w-full h-full object-contain"
                                                                src="{{ $ride->driver?->profile_image }}" alt="">
                                                        </div> --}}
                                                        <div class="text-center">
                                                            <p class="font-semibold">
                                                                {{-- @isset($findRidePage->card_section_driver)
                                                                    {{ $findRidePage->card_section_driver }}
                                                                @endisset --}}
                                                                <span>
                                                                    @if ($ride->driver?->type === '2')
                                                                        {{ $ride->driver?->last_name }}
                                                                    @elseif ($ride->driver?->type === '3')
                                                                        {{ $ride->driver?->first_name }} {{ $ride->driver?->last_name }}
                                                                    @else
                                                                        {{ $ride->driver?->first_name }}
                                                                    @endif
                                                                    {{-- @if($ride->driver?->gender && $ride->driver?->gender !== 'Prefer not to say')
                                                                    ({{ strtoupper(substr($ride->driver?->gender, 0, 1)) }})
                                                                @endif --}}
                                                                </span></p>
                                                            @php
                                                                // Calculate the age based on the driver's date of birth
                                                                $dob = \Carbon\Carbon::parse($ride->driver?->dob);
                                                                $age = $dob->diffInYears(\Carbon\Carbon::now());
                                                            @endphp
                                                            <p class="mb-0 text-sm">
                                                                @isset($findRidePage->card_section_age)
                                                                    {{ $findRidePage->card_section_age }}
                                                                @endisset
                                                                {{ $age }}</p>
                                                            <p class="mb-0 text-sm">
                                                                {{  $ride->driver?->rides()
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
                                                                @isset($findRidePage->card_section_driven)
                                                                    {{ $findRidePage->card_section_driven }}
                                                                @endisset
                                                            </p>
                                                            @php
                                                                $filteredRatings = $ratings->where('status', 1)->where('type', '1')->filter(function ($rating) use ($ride) {
                                                                    return $rating->ride && $rating->ride->added_by === $ride->added_by;
                                                                });

                                                                $totalAverage = $filteredRatings->avg('average_rating') ?? 0;
                                                            @endphp
                                                        </div>
                                                    </div>
                                                    <div class="">
                                                        <span
                                                            class="font-semibold text-gray-800">{{ number_format($totalAverage, 1) }}</span>
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                            fill="currentColor"
                                                            class="w-6 h-6 text-yellow-500 stroke-gray-600">
                                                            <path fill-rule="evenodd"
                                                                d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z"
                                                                clip-rule="evenodd"></path>
                                                        </svg>
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="flex items-center justify-between px-4">
                                                        <p class="font-semibold">
                                                            @isset($findRidePage->card_section_booking_fee)
                                                                {{ $findRidePage->card_section_booking_fee }}
                                                            @endisset
                                                        </p>
                                                        <p class="">
                                                            @if (auth()->user())
                                                                ${{ number_format(floatval($ride->bookings->where('user_id', auth()->user()->id)->where('status', '<>', 3)->where('status', '<>', 4)->sum('booking_credit')), 2) }}
                                                            @else
                                                                $0.00
                                                            @endif
                                                        </p>
                                                    </div>
                                                    <div class="flex items-center justify-between px-4">
                                                        <p class="font-semibold">
                                                            @isset($findRidePage->card_section_seats_fee)
                                                                {{ $findRidePage->card_section_seats_fee }}
                                                            @endisset
                                                        </p>
                                                        <p class="">
                                                            @if (auth()->user())
                                                                ${{ number_format(floatval(($ride->bookings()->where('user_id', auth()->user()->id)->where('status', '<>', 3)->where('status', '<>', 4)->whereHas('passenger', function($query) { $query->whereNull('deleted_at'); })->sum('seats') * floatval($ride->rideDetail[0]->price))), 2) }}
                                                            @else
                                                                $0.00
                                                            @endif
                                                        </p>
                                                    </div>
                                                    <div class="flex items-center justify-between px-4">
                                                        <p class="font-semibold">
                                                            @isset($findRidePage->card_section_amount)
                                                                {{ $findRidePage->card_section_amount }}
                                                            @endisset
                                                        </p>
                                                        <p class="">
                                                            @if (auth()->user())
                                                                ${{ number_format(floatval(($ride->bookings()->where('user_id', auth()->user()->id)->where('status', '<>', 3)->where('status', '<>', 4)->whereHas('passenger', function($query) { $query->whereNull('deleted_at'); })->sum('seats') * floatval($ride->rideDetail[0]->price)) + $ride->bookings->where('user_id', auth()->user()->id)->where('status', '<>', 3)->where('status', '<>', 4)->sum('booking_credit')), 2) }}
                                                            @else
                                                                $0.00
                                                            @endif
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        @endif
                        {{ $paginatedRides->appends(request()->query())->links() }}
                    @elseif ($paginatedRides && $paginatedRides->count() == 0)
                        <div class="flex items-center space-x-2">
                            <img class="w-8 h-8" src="{{ asset('assets/exclamation-mark.png') }}">
                            <p>Sorry, no rides found for your search.</p>
                        </div>
                    @endif
                </div>
                <!-- Confirmation Modal for Hiding Rides -->
        <div class="hidden overflow-x-hidden overflow-y-auto fixed inset-0 z-50 outline-none focus:outline-none justify-center items-center" id="hide-ride-confirm-modal">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto flex items-center justify-center">
                <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md">
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <!-- <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-blue-600">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                                </svg>
                            </div> -->
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                <h3 class="text-base font-semibold leading-6 text-gray-900" id="modal-title">Confirm Hide Ride</h3>
                                <div class="mt-2 w-full">
                                    <p class="can-exp-p text-center">Do you want this ride to be hidden from your search results? You will not be able to see it anymore</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                        <button type="button" id="confirm-hide-ride" class="inline-flex w-full justify-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 sm:ml-3 sm:w-auto">Yes, hide it</button>
                        <button type="button" onclick="closeHideRideModal()" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">No, take me back</button>
                    </div>
                </div>
            </div>
        </div>
                <div class="hidden overflow-x-hidden overflow-y-auto fixed inset-0 z-50 outline-none focus:outline-none justify-center items-center"
                    id="modal-id1">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                        <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                        <!--content-->
                        <div
                            class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg w-full modal-border">
                            <!--body-->
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
                                        <p class="text-center can-exp-p modal-message"></p>
                                    </div>
                                </div>
                            </div>
                            <!--footer-->
                            <div
                                class="px-4 pb-6 pt-4  sm:flex sm:flex-row-reverse sm:px-6 justify-center">
                                <button
                                    class="inline-flex w-full justinline-flex justify-center rounded bg-red-500 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-red-400 sm:ml-3 sm:w-24"
                                    type="button" onclick="toggleModal1('modal-id1')">
                                    Close
                                </button>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>
                <div class="mt-6 grid grid-cols-1 lg:grid-cols-1 gap-x-0 lg:gap-x-4 gap-4">
                    <div>
                        <div class="bg-white rounded-lg overflow-hidden shadow-3xl">
                            <div class="text-2xl bg-primary text-white py-2 px-4 font-FuturaMdCnBT text-center">
                                FAQs on the Extra-Care Rides
                            </div>
                            <div class="bg-white p-4 ">
                                @foreach ($extraCareFaqs as $extraCareFaq)
                                    <div>
                                        <button class="faq-question block w-full text-left mb-3 font-medium text-gray-900 focus:outline-none rounded px-4 py-2.5 shadow text-base md:text-lg bg-gray-100 hover:bg-gray-200 transition-colors font-FuturaMdCnBT" onclick="toggleAnswer(this)">
                                            {{ $extraCareFaq->question }}
                                        </button>
                                        <div class="faq-answer hidden bg-white p-4 rounded text-base md:text-lg text-gray-900">
                                            {!! $extraCareFaq->answer !!}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        function closeModal() {
            document.getElementById('my-modal').style.display = 'none';
        }

        let removedRideIds = [];
        let currentRideIdToHide = null;

        // Function to close the hide ride modal
        function closeHideRideModal() {
            document.getElementById('hide-ride-confirm-modal').classList.add('hidden');
            currentRideIdToHide = null;
        }

        // Function to actually hide the ride
        function hideCurrentRide() {
            if (currentRideIdToHide) {
                var rideElement = document.getElementById('ride-' + currentRideIdToHide);

                if (rideElement) {
                    rideElement.style.display = 'none';

                    // Add the removed ride ID to the array
                    if (!removedRideIds.includes(currentRideIdToHide)) {
                        removedRideIds.push(currentRideIdToHide);
                        localStorage.setItem('removedRideIds', JSON.stringify(removedRideIds));
                    }

                    // Check if the total number of visible rides on the current page is less than the desired count
                    var visibleRides = document.querySelectorAll('.p-4.bg-white:not([style*="display: none"])');
                    var ridesPerPage = 8; // Update this with your actual per-page count

                    if (visibleRides.length < ridesPerPage) {
                        console.log(visibleRides.length);
                    }
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize removedRideIds from localStorage
            try {
                const storedIds = localStorage.getItem('removedRideIds');
                removedRideIds = storedIds ? JSON.parse(storedIds) : [];

                // Hide previously removed rides
                removedRideIds.forEach(function(rideId) {
                    const rideElement = document.getElementById('ride-' + rideId);
                    if (rideElement) {
                        rideElement.style.display = 'none';
                    }
                });
            } catch (error) {
                console.error("Error parsing removedRideIds:", error);
                removedRideIds = [];
            }

            // Attach click event to remove buttons - shows confirmation modal
            document.querySelectorAll('.ride-remove-btn').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    currentRideIdToHide = this.getAttribute('data-ride-id');
                    document.getElementById('hide-ride-confirm-modal').classList.remove('hidden');
                });
            });

            // Confirm hide ride button handler
            document.getElementById('confirm-hide-ride').addEventListener('click', function() {
                hideCurrentRide();
                closeHideRideModal();
            });
        });

        function toggleAnswer(element) {
            let answer = element.nextElementSibling;
            answer.classList.toggle("hidden");
            // Change the background color of the button based on the answer's visibility
            if (!answer.classList.contains("hidden")) {
                element.classList.remove("bg-gray-100", "hover:bg-gray-200");
                element.classList.add("bg-greenXS", "text-white");
            } else {
                element.classList.remove("bg-greenXS", "text-white");
                element.classList.add("bg-gray-100", "hover:bg-gray-200");
            }
        }

        function toggleModal1(modalID, message, title = '') {
            var modalElement = document.getElementById(modalID);
            if (message && title) {
                var messageElement = modalElement.querySelector(".modal-message");
                var titleElement = modalElement.querySelector(".modal-title");

                if (titleElement) titleElement.innerText = title;
                if (messageElement) messageElement.innerText = message;
            }

            modalElement.classList.toggle("hidden");
            modalElement.classList.toggle("flex");
        }

        function clearDateInput() {
            document.getElementById('dateInput').value = null;
        }
        function swapLocations() {
            // Get the values of the "From" and "To" input fields
            const fromValue = document.getElementById('fromInput').value;
            const toValue = document.getElementById('toInput').value;

            // Swap the values
            document.getElementById('fromInput').value = toValue;
            document.getElementById('toInput').value = fromValue;
            navigateToSearchRoute();
        }

        const dateInput = document.getElementById('dateInput');

        // Initialize the date picker
        flatpickr(dateInput, {
            dateFormat: 'F d, Y', // Display format (e.g., "January 15, 2024")
            altInput: true,
            altFormat: 'F d, Y',
            minDate: 'today', // Restrict to future dates only
            disableMobile: true, // Disable mobile-friendly mode for consistent experience
            allowInput: true, // Allow manual input
            clickOpens: true, // Open calendar on click
            theme: 'default' // Use default theme
        });

        // Add an event listener to the input fields
        document.getElementById('fromInput').addEventListener('keypress', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                navigateToSearchRoute();
            }
        });

        document.getElementById('toInput').addEventListener('keypress', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                navigateToSearchRoute();
            }
        });

        document.getElementById('dateInput').addEventListener('keypress', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                navigateToSearchRoute();
            }
        })

        document.getElementById('driverName').addEventListener('keypress', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                navigateToSearchRoute();
            }
        })

        // Initialize an array to store selected features
        let getFeatures = '{{ isset($_GET['features']) ? $_GET['features'] : '' }}';
        getFeatures = getFeatures ? getFeatures.split(";").filter(f => f.trim() !== "") : [];
        let selectedFeatures = getFeatures;
        selectedFeatures.push('{{ $findRidePage->ride_features_option2->features_setting_id ?? "" }}');
        console.log(selectedFeatures);

        // Initialize an array to store selected luggages
        let getLuggages = '{{ isset($_GET['luggage']) ? $_GET['luggage'] : '' }}';
        getLuggages = getLuggages.split(";");
        let selectedLuggages = getLuggages;

        // Initialize an array to store selected smoking options
        let getSmoking = '{{ isset($_GET['smoking']) ? $_GET['smoking'] : '' }}';
        getSmoking = getSmoking.split(";");
        let selectedSmoking = getSmoking;

        // Initialize an array to store selected pets
        let getPets = '{{ isset($_GET['pets']) ? $_GET['pets'] : '' }}';
        getPets = getPets.split(";");
        let selectedPets = getPets;

        // Add an event listener to each checkbox
        document.querySelectorAll('input[type="checkbox"].ride-preferences').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                handleCheckboxChange(checkbox);
            });
        });

        // Add an event listener to each checkbox
        document.querySelectorAll('input[type="checkbox"].luggage').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                handleLuggageCheckboxChange(checkbox);
            });
        });

        // Add an event listener to each checkbox
        document.querySelectorAll('input[type="checkbox"].smoking').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                handleSmokingCheckboxChange(checkbox);
            });
        });

        // Add an event listener to each checkbox
        document.querySelectorAll('input[type="checkbox"].pet').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                handlePetCheckboxChange(checkbox);
            });
        });

        function handleCheckboxChange(checkbox) {
            const featureValue = checkbox.value;

            // Check if the checkbox is checked
            if (checkbox.checked) {
                selectedFeatures.push(featureValue);
            } else {
                selectedFeatures = selectedFeatures.filter(item => item !== featureValue);
            }

            // Call a function to update the URL with the selected features
            // navigateToSearchRoute();
        }

        function handleLuggageCheckboxChange(checkbox) {
            const featureValue = checkbox.value;

            // Check if the checkbox is checked
            if (checkbox.checked) {
                selectedLuggages.push(featureValue);
            } else {
                selectedLuggages = selectedLuggages.filter(item => item !== featureValue);
            }

            // Call a function to update the URL with the selected features
            // navigateToSearchRoute();
        }

        function handleSmokingCheckboxChange(checkbox) {
            const featureValue = checkbox.value;

            // Check if the checkbox is checked
            if (checkbox.checked) {
                selectedSmoking.push(featureValue);
            } else {
                selectedSmoking = selectedSmoking.filter(item => item !== featureValue);
            }

            // Call a function to update the URL with the selected features
            // navigateToSearchRoute();
        }

        function handlePetCheckboxChange(checkbox) {
            const featureValue = checkbox.value;

            // Check if the checkbox is checked
            if (checkbox.checked) {
                selectedPets.push(featureValue);
            } else {
                selectedPets = selectedPets.filter(item => item !== featureValue);
            }

            // Call a function to update the URL with the selected features
            // navigateToSearchRoute();
        }

        function SearchRoute(SearchfromValue, SearchtoValue) {
            const featuresParam = selectedFeatures.length > 0 ? selectedFeatures.join(';') : '';
            // Construct the URL with query parameters
            let searchUrl =
                `{{ route('folk_ride', ['lang' => $selectedLanguage->abbreviation]) }}?from=${SearchfromValue}&to=${SearchtoValue}&date=&driver_age=&driver_rating=&driver_phone=&driver_name=&passenger_rating=&payment_method=&vehicle_type=&features=${featuresParam}&luggage=&smoking=&pets=`;

            // Navigate to the constructed URL
            window.location.href = searchUrl;
        }

        function navigateToSearchRoute() {
            localStorage.setItem('removedRideIds', JSON.stringify([]));

            // Get the values of the input fields
            const fromValue = document.getElementById('fromInput').value;
            const toValue = document.getElementById('toInput').value;
            const dateValue = document.getElementById('dateInput').value;
            const driverAge = document.getElementById('driverAge').value;
            const passengerRating = document.getElementById('passengerRating').value;
            const paymentMethod = document.getElementById('payment-method').value;
            const driverRating = document.getElementById('driverRating').value;
            const driverPhone = document.getElementById('driverPhone').checked ? 1 : 0;
            const driverName = document.getElementById('driverName').value;
            const VehicleType = document.getElementById('VehicleType').value;

            // Construct the URL with the selected features as a comma-separated list
            const featuresParam = selectedFeatures.length > 0 ? selectedFeatures.join(';') : '';
            // return;
            const luggage = selectedLuggages.join(';');

            const smokingValue = selectedSmoking.join(';');

            const petsValue = selectedPets.join(';');

            // Get the error message elements
            const fromError = document.getElementById('fromError');
            const toError = document.getElementById('toError');

            // Check if "From" and "To" fields are empty
            if (fromValue.trim() === '') {
                @isset($findRidePage->search_section_required_error)
                    fromError.textContent = '{{ $findRidePage->search_section_required_error }}';
                @endisset
                fromError.classList.remove('hidden');
                toError.classList.add('hidden');
                return;
            } else if (toValue.trim() === '') {
                @isset($findRidePage->search_section_required_error)
                    toError.textContent = '{{ $findRidePage->search_section_required_error }}';
                @endisset
                toError.classList.remove('hidden');
                fromError.classList.add('hidden');
                return;
            } else {
                // Both fields are filled, hide error messages
                fromError.classList.add('hidden');
                toError.classList.add('hidden');
            }

            // Construct the URL with query parameters
            let searchUrl =
                `{{ route('folk_ride', ['lang' => $selectedLanguage->abbreviation]) }}?from=${fromValue}&to=${toValue}&date=${dateValue}&driver_age=${driverAge}&driver_rating=${driverRating}&driver_phone=${driverPhone}&driver_name=${driverName}&passenger_rating=${passengerRating}&payment_method=${paymentMethod}&vehicle_type=${VehicleType}&features=${featuresParam}&luggage=${luggage}&smoking=${smokingValue}&pets=${petsValue}`;

            // Navigate to the constructed URL
            window.location.href = searchUrl;
        }
        function resetFilters() {
    // Reset checkboxes
    document.querySelectorAll('.ride-preferences, .luggage, .smoking, .pet').forEach(checkbox => {
        checkbox.checked = false;
    });

    // Reset selects
    document.getElementById('driverAge').value = '0';
    document.getElementById('passengerRating').value = '';
    document.getElementById('payment-method').value = '';
    document.getElementById('driverRating').value = '0';
    document.getElementById('VehicleType').value = '';

    // Reset other inputs
    document.getElementById('driverPhone').checked = false;
    document.getElementById('driverName').value = '';
    document.getElementById('keyword').value = '';

    // Clear any stored selections
    selectedFeatures = [];
    selectedLuggages = [];
    selectedSmoking = [];
    selectedPets = [];

    // Optionally refresh the page or navigate back
    window.location.href = "{{ route('search_ride', ['lang' => $selectedLanguage->abbreviation]) }}";
}
    </script>
    <script>

        document.addEventListener('DOMContentLoaded', () => {

        const autoGrow = (textarea) => {
            textarea.style.height = 'auto';
            textarea.style.height = textarea.scrollHeight + 'px';
        };

        document.querySelectorAll('textarea').forEach(textarea => {
            // Base styles (safe, non-breaking)
            textarea.style.resize = 'none';
            textarea.style.overflowY = 'hidden';

            // Grow on typing
            textarea.addEventListener('input', () => autoGrow(textarea));

            // Grow to fit placeholder on load
            if (!textarea.value && textarea.placeholder) {
                textarea.value = textarea.placeholder;
                autoGrow(textarea);
                textarea.value = '';
            } else {
                autoGrow(textarea);
            }
        });

        });

        document.addEventListener('DOMContentLoaded', () => {
            const toggle = document.getElementById('search-filter-toggle');
            const close = document.getElementById('search-filter-close');
            const searchFilters = document.getElementById('search-filter');
            const overlay = document.getElementById('search-filter-overlay');

            const toggleSearchFilters = (show) => {
                searchFilters.classList.toggle('translate-x-full', !show);
                overlay.classList.toggle('hidden', !show);
            };

            toggle.addEventListener('click', () => toggleSearchFilters(true));
            close.addEventListener('click', () => toggleSearchFilters(false));
            overlay.addEventListener('click', () => toggleSearchFilters(false));
        });
    </script>
@endsection
