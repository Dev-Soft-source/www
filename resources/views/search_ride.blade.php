@extends('layouts.template')

@section('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endsection

@section('content')
    @if (session('success'))
    <div id="my-modal" class="relative z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!-- Backdrop with transition -->
    <div class="fixed inset-0 bg-gray-500 bg-opacity-0 transition-opacity duration-300 ease-in-out z-10" id="modal-backdrop"></div>

    <!-- Modal container with transition -->
    <div class="fixed inset-0 flex items-center justify-center p-4 z-20 opacity-0 scale-95 transition-all duration-300 ease-in-out" id="modal-container">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full w-full"> 
            <!-- Modal content with transition -->
            <div class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 w-full sm:max-w-xl modal-border">
                <button type="button" onclick="closeModal()" class="absolute top-3 right-3 text-gray-400 hover:text-gray-500">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                </button>
                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start justify-center">
                        <!-- <div class="mx-auto h-16 w-16 flex-shrink-0 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="4" stroke="currentColor" class="w-12 h-12 text-greenXS">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                        </div> -->
                    </div>
                    <div class="mt-4 w-full">
                        <p class="can-exp-p text-center">{!! session('success') !!}</p>
                    </div>
                </div>
                <div class="px-4 pb-6 pt-4 sm:flex sm:flex-row-reverse sm:px-6 justify-center">
                    <button onclick="closeModal()"
                        class="inline-flex w-full justify-center rounded bg-greenXS px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-greenXS sm:ml-3 sm:w-24 transition-colors duration-200">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Function to show modal with transitions
    function showModal() {
        const modal = document.getElementById('my-modal');
        const backdrop = document.getElementById('modal-backdrop');
        const container = document.getElementById('modal-container');

        modal.classList.remove('hidden');

        // Trigger reflow to enable transitions
        void modal.offsetWidth;

        backdrop.classList.remove('bg-opacity-0');
        backdrop.classList.add('bg-opacity-75');

        container.classList.remove('opacity-0', 'scale-95');
        container.classList.add('opacity-100', 'scale-100');
    }

    // Function to close modal with transitions
    function closeModal() {
        const backdrop = document.getElementById('modal-backdrop');
        const container = document.getElementById('modal-container');

        backdrop.classList.remove('bg-opacity-75');
        backdrop.classList.add('bg-opacity-0');

        container.classList.remove('opacity-100', 'scale-100');
        container.classList.add('opacity-0', 'scale-95');

        // Wait for transition to complete before hiding
        setTimeout(() => {
            document.getElementById('my-modal').classList.add('hidden');
        }, 300);
    }

    // Auto-show modal if there's a success message
    @if(session('success'))
    document.addEventListener('DOMContentLoaded', showModal);
    @endif
</script>
    @endif
    @if (session('failure'))
    <div class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                <div
                    class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg w-full modal-border">
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start justify-center">
                            <!-- <div
                                class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full sm:mx-0 sm:h-10 sm:w-10 bg-red-500">
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

    @if(session('message'))
        <div class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full justify-center p-4 text-center items-center sm:p-0">
                    <div
                        class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start justify-center">
                                <!-- <div
                                    class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full sm:mx-0 sm:h-10 sm:w-10 bg-red-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-lg text-white w-8 h-8" viewBox="0 0 16 16">
                                        <path d="M7.005 3.1a1 1 0 1 1 1.99 0l-.388 6.35a.61.61 0 0 1-1.214 0zM7 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0"/>
                                    </svg>
                                </div> -->
                            </div>
                            <div class="text-center">
                                <div class="w-full">
                                    <p class="can-exp-p text-center">{!! session('message') !!}</p>
                                </div>
                            </div>
                        </div>
                        <div class="px-4 pb-6 pt-4 flex items-center space-x-2 sm:space-x-4 sm:px-6 justify-center">

                            <a href=""
                                class="inline-flex w-full justify-center rounded bg-red-500 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-red-400 sm:ml-3 sm:w-24">Close</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="container mx-auto my-6 md:px-8 xl:px-0">

        <div class="mt-6 grid grid-cols-1 lg:grid-cols-4 gap-x-0 lg:gap-x-4 gap-4">
            <!---->
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
                <div class="search-filter-menu bg-white border lg:border-none rounded pt-12 p-4 lg:p-0 border-gray-200 w-full shadow">
                    <div class="">
                        <div class="bg-white rounded-lg overflow-hidden shadow-3xl">
                            <div class="bg-primary text-white font-medium text-xl flex items-center justify-center space-x-2 p-4">
                                <div class="w-9 h-9 mr-2 p-1 flex items-center justify-center bg-white rounded-full border-2 border-[#1F4174]">
                                    <img class="w-5 h-5 object-contain" src="{{ asset('assets/filter.png') }}" alt="">
                                </div>
                                @isset($findRidePage->filter_section_heading)
                                {{ $findRidePage->filter_section_heading }}
                                @endisset
                            </div>
                            
                            <div class="bg-white p-4 ">
                                <div class="divide-y mb-2">
                                    @php  $features_check = isset($_GET['features']) ? explode(';', $_GET['features']) : [];
                                        @endphp
                                        @isset($findRidePage->ride_features_option1->features_setting_id)
                                        <div class="flex items-start justify-between p-3">
                                            <label for="pink-ride" class="text-gray-900 flex space-x-1">
                                                <span class="text-pink-500 text-base md:text-lg">
                                                    {{ $findRidePage->ride_features_option1->name }}
                                                </span>
                                                <!-- <div class="sups relative">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                        fill="currentColor" class="bi bi-info-circle-fill text-black peer"
                                                        viewBox="0 0 16 16">
                                                        <path
                                                            d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
                                                    </svg>
                                                    <div
                                                        class="absolute right-20 tooltip -top-10 group-hover:flex hidden peer-hover:flex">
                                                        <div role="tooltip"
                                                            class="absolute tooltiptext_icon after:right-1/2 after:-left-1/2 -top-1 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-blue-500  border border-blue-500 text-gray-600 rounded w-64 px-4">
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
                                                <span class="text-green-500 text-base md:text-lg">
                                                    {{ $findRidePage->ride_features_option2->name }}
                                                </span>
                                                <!-- <div class="sups">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                        fill="currentColor" class="bi bi-info-circle-fill text-black"
                                                        viewBox="0 0 16 16">
                                                        <path
                                                            d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
                                                    </svg>
                                                </div> -->
                                            </label>
                                            <input id="extra-care" type="checkbox"
                                                value="{{ $findRidePage->ride_features_option2->features_setting_id }}"
                                                {{ in_array($findRidePage->ride_features_option2->features_setting_id, $features_check) ? 'checked' : '' }}
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
                                        @endisset>{{ old('keyword', $request->keyword) }}</textarea>
                                    <!-- <textarea type="text" id="keyword" value="{{ $request->keyword }}"
                                        class="bg-gray-100 border-0 placeholder:text-gray-900 text-black text-base md:text-lg rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 block w-full p-2.5 line-clamp-2"
                                        @isset($findRidePage->search_section_keyword_placeholder)
                                            placeholder="{{ $findRidePage->search_section_keyword_placeholder }}"
                                        @endisset>
                                    </textarea> -->
                                </div>
                                
                                <div class="space-y-4 mb-4">
                                    <h3 class="text-primary text-2xl xl:text-3xl">
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
                                            class="bg-gray-100 text-base md:text-lg border-0 text-black rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 block w-full p-2.5"
                                            {{-- onchange="navigateToSearchRoute()" --}}
                                            >
                                            <option value="0" {{ $request->driver_age == 0 ? 'selected' : '' }}>
                                                @isset($findRidePage->driver_age_placeholder)
                                                    {{ $findRidePage->driver_age_placeholder }}
                                                @endisset
                                            </option>
                                            <option {{ $request->driver_age == 20 ? 'selected' : '' }}>+20</option>
                                            <option {{ $request->driver_age == 30 ? 'selected' : '' }}>+30</option>
                                            <option {{ $request->driver_age == 40 ? 'selected' : '' }}>+40</option>
                                            <option {{ $request->driver_age == 50 ? 'selected' : '' }}>+50</option>
                                            <option {{ $request->driver_age == 60 ? 'selected' : '' }}>+60</option>
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
                                            class="bg-gray-100 border-0 placeholder:text-gray-900 text-black text-base md:text-lg rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 block w-full p-2.5"
                                            {{-- onchange="navigateToSearchRoute()" --}}
                                            >
                                            <option value="0" {{ $request->driver_rating == 0 ? 'selected' : '' }}>
                                                @isset($findRidePage->driver_rating_placeholder)
                                                    {{ $findRidePage->driver_rating_placeholder }}
                                                @endisset
                                            </option>
                                            <option value="4.5" {{ $request->driver_rating == 4.5 ? 'selected' : '' }}>5</option>
                                            <option value="4" {{ $request->driver_rating == 4 ? 'selected' : '' }}>4 and above</option>
                                            <option value="3" {{ $request->driver_rating == 3 ? 'selected' : '' }}>3 and above</option>
                                            <option value="2" {{ $request->driver_rating == 2 ? 'selected' : '' }}>2 and above</option>
                                            <option value="1" {{ $request->driver_rating == 1 ? 'selected' : '' }}>1 and above</option>
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
                                        class="bg-gray-100 border-0 placeholder:text-gray-900 text-black text-base md:text-lg italic rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 block w-full p-2.5"
                                        @isset($findRidePage->driver_know_placeholder)
                                            placeholder="{{ $findRidePage->driver_know_placeholder }}"
                                        @endisset>
                                </div>
                                </div>

                                <div class="space-y-4 mb-4">
                                        <h3 class="text-primary text-2xl xl:text-3xl">
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
                                                class="bg-gray-100 border-0 placeholder:text-gray-900 text-black text-base md:text-lg rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 block w-full p-2.5 whitespace-pre-line pr-8"
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
                                    <h3 class="text-primary text-2xl xl:text-3xl">
                                        @isset($findRidePage->filter3_payment_methods_heading)
                                            {{ $findRidePage->filter3_payment_methods_heading }}
                                        @endisset
                                    </h3>
                                    <div>
                                        {{-- <label for="payment-method" class="block mb-2 font-medium text-gray-900">
                                            @isset($findRidePage->payment_methods_label)
                                                {{ $findRidePage->payment_methods_label }}
                                            @endisset
                                        </label> --}}
                                        <div class="">
                                            <select id="payment-method" name=""
                                                class="bg-gray-100 border-0 placeholder:text-gray-900 text-black text-base md:text-lg rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 block w-full p-2.5"
                                                {{-- onchange="navigateToSearchRoute()" --}}
                                                >
                                                @isset($findRidePage->payment_methods_option1)
                                                    <option value=""
                                                        {{ $request->payment_method == '' ? 'selected' : '' }}>
                                                        {{ $findRidePage->payment_methods_option1 }}
                                                    </option>
                                                @endisset
                                                @isset($findRidePage->payment_methods_option2->features_setting_id)
                                                    <option value="{{ $findRidePage->payment_methods_option2->features_setting_id }}"
                                                        {{ $request->payment_method == $findRidePage->payment_methods_option2->features_setting_id ? 'selected' : '' }}>
                                                        {{ $findRidePage->payment_methods_option2->name }}
                                                    </option>
                                                @endisset
                                                @isset($findRidePage->payment_methods_option3->features_setting_id)
                                                    <option value="{{ $findRidePage->payment_methods_option3->features_setting_id }}"
                                                        {{ $request->payment_method == $findRidePage->payment_methods_option3->features_setting_id ? 'selected' : '' }}>
                                                        {{ $findRidePage->payment_methods_option3->name }}
                                                    </option>
                                                @endisset
                                                @isset($findRidePage->payment_methods_option4->features_setting_id)
                                                    <option value="{{ $findRidePage->payment_methods_option4->features_setting_id }}"
                                                        {{ $request->payment_method == $findRidePage->payment_methods_option4->features_setting_id ? 'selected' : '' }}>
                                                        {{ $findRidePage->payment_methods_option4->name }}
                                                    </option>
                                                @endisset
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="space-y-4 mb-4">
                                    <h3 class="text-primary text-2xl xl:text-3xl">
                                        @isset($findRidePage->filter4_vehicle_heading)
                                            {{ $findRidePage->filter4_vehicle_heading }}
                                        @endisset
                                    </h3>
                                    <div>
                                    {{-- <label for="type" class="block mb-2 font-medium text-gray-900">
                                            @isset($findRidePage->vehicle_type_label)
                                                {{ $findRidePage->vehicle_type_label }}
                                            @endisset
                                        </label> --}}
                                        <div class="">
                                            <select id="VehicleType" name=""
                                            class="bg-gray-100 border-0 placeholder:text-gray-900 text-black text-base md:text-lg rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 block w-full p-2.5"
                                            {{-- onchange="navigateToSearchRoute()" --}}
                                            >
                                                <option {{ $request->vehicle_type == '' ? 'selected' : '' }} value="">
                                                    @isset($findRidePage->vehicle_type_placeholder)
                                                        {{ $findRidePage->vehicle_type_placeholder }}
                                                    @endisset
                                                </option>

                                                <option value="{{ $findRidePage->vehicle_type_convertible_value ?? 'Convertable' }}"
                                                    {{ $request->vehicle_type === ($findRidePage->vehicle_type_convertible_value ?? 'Convertable') ? 'selected' : '' }}>
                                                    {{ $findRidePage->vehicle_type_convertible_text ?? "Convertable"}}
                                                </option>
                                                <option value="{{ $findRidePage->vehicle_type_coupe_value ?? 'Coupe' }}"
                                                    {{ $request->vehicle_type === ($findRidePage->vehicle_type_coupe_value ??'Coupe') ? 'selected' : '' }}>
                                                    {{ $findRidePage->vehicle_type_coupe_text ?? "Coupe"}}
                                                </option>
                                                <option value="{{ $findRidePage->vehicle_type_hatchback_value ??'Hatchback' }}"
                                                    {{ $request->vehicle_type === ($findRidePage->vehicle_type_hatchback_value ??'Hatchback') ? 'selected' : '' }}>
                                                    {{ $findRidePage->vehicle_type_hatchback_text ?? "Hatchback"}}
                                                </option>
                                                <option value="{{ $findRidePage->vehicle_type_minivan_value ??'Minivan' }}"
                                                    {{ $request->vehicle_type === ($findRidePage->vehicle_type_minivan_value ??'Minivan') ? 'selected' : '' }}>
                                                    {{ $findRidePage->vehicle_type_minivan_text ?? "Minivan"}}
                                                </option>
                                                <option value="{{ $findRidePage->vehicle_type_sedan_value ??'Sedan' }}"
                                                    {{ $request->vehicle_type === ($findRidePage->vehicle_type_sedan_value ??'Sedan') ? 'selected' : '' }}>
                                                    {{ $findRidePage->vehicle_type_sedan_text ?? "Sedan"}}
                                                </option>
                                                <option value="{{ $findRidePage->vehicle_type_station_wagon_value }}"
                                                    {{ $request->vehicle_type === ($findRidePage->vehicle_type_station_wagon_value ??'Station wagon') ? 'selected' : '' }}>
                                                    {{ $findRidePage->vehicle_type_station_wagon_text ?? "Station wagon"}}
                                                </option>
                                                <option value="{{ $findRidePage->vehicle_type_suv_value ??'SUV' }}"
                                                    {{ $request->vehicle_type === ($findRidePage->vehicle_type_suv_value ??'SUV') ? 'selected' : '' }}>
                                                    {{ $findRidePage->vehicle_type_suv_text ?? "SUV"}}
                                                </option>
                                                <option value="{{ $findRidePage->vehicle_type_truck_value ??'Truck' }}"
                                                    {{ $request->vehicle_type === ($findRidePage->vehicle_type_truck_value ??'Truck') ? 'selected' : '' }}>
                                                    {{ $findRidePage->vehicle_type_truck_text ?? "Truck"}}
                                                </option>
                                                <option value="{{ $findRidePage->vehicle_type_van_value ??'Van' }}"
                                                    {{ $request->vehicle_type === ($findRidePage->vehicle_type_van_value ??'Van') ? 'selected' : '' }}>
                                                    {{ $findRidePage->vehicle_type_van_text ?? "Van"}}
                                                </option>

                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="space-y-4 mb-4">
                                    <h3 class="text-primary text-2xl xl:text-3xl">
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
                                                <span class="text-base md:text-lg">
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
                                                <span class="text-base md:text-lg">
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
                                                <span class="text-base md:text-lg">
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
                                                <span class="text-base md:text-lg">
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
                                                <span class="text-base md:text-lg">
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
                                                <span class="text-base md:text-lg">
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
                                                <span class="text-base md:text-lg">
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
                                                <span class="text-base md:text-lg">
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
                                                <span class="text-base md:text-lg">
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
                                                <span class="text-base md:text-lg">
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
                                    <h3 class="text-primary text-2xl xl:text-3xl">
                                        @isset($findRidePage->luggage_label)
                                            {{ $findRidePage->luggage_label }}
                                        @endisset
                                    </h3>
                                    <div class="border rounded-md divide-y">
                                        @php
                                            $luggages_check = isset($_GET['luggage']) ? explode(';', $_GET['luggage']) : [];
                                        @endphp
                                        @isset($findRidePage->luggage_option1)
                                            <div class="flex items-center justify-between p-3">
                                                <label for="small-luggage" class="font-normal text-gray-900 flex space-x-1">
                                                    <span class="text-base md:text-lg">
                                                        {{ $findRidePage->luggage_option1->name }}
                                                    </span>
                                                    <div class="sups relative inline-flex">
                                                        <!-- Info Icon -->
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle-fill text-black peer" viewBox="0 0 16 16">
                                                            <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                                                        </svg>
        
                                                        <!-- Tooltip Content -->
                                                        <div class="absolute -left-32 -top-20 z-50 hidden peer-hover:block hover:block transition-opacity duration-300 ease-in-out opacity-0 peer-hover:opacity-100 hover:opacity-100">
                                                            <div class="relative w-64 sm:w-72 md:w-80 lg:w-96 px-4 mt-2">
                                                            <!-- Tooltip Arrow -->
                                                            <div class="absolute -bottom-1 left-36 transform -translate-x-1/2 w-4 h-4 bg-primary rotate-45"></div>
                                                            
                                                            <!-- Tooltip Body -->
                                                            <div class="bg-primary text-white rounded-lg shadow-xl p-4">
                                                                <p class="text-white">
                                                                {{ $postRidePage->luggage_option1_tooltip }}
                                                                </p>
                                                            </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </label>
                                                <input id="small-luggage" type="checkbox"
                                                    value="{{ $findRidePage->luggage_option1->features_setting_id }}"
                                                    {{ in_array($findRidePage->luggage_option1->features_setting_id, $luggages_check) ? 'checked' : '' }}
                                                    class="luggage w-4 h-4 ml-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                                            </div>
                                        @endisset
                                        @isset($findRidePage->luggage_option2)
                                            <div class="flex items-center justify-between p-3">
                                                <label for="Medium-luggage" class="font-normal text-gray-900 flex space-x-1">
                                                    <span class="text-base md:text-lg">
                                                        {{ $findRidePage->luggage_option2->name }}
                                                    </span>
                                                    <!-- <div class="sups relative inline-flex">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle-fill text-black peer" viewBox="0 0 16 16">
                                                            <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                                                        </svg>
                                                        <div
                                                        class="absolute tooltip payment_tooltiptext_position -top-20 sm:-top-16 right-32 lg:-top-28 xl:right-32 xl:-top-24 2xl:-top-24 group-hover:flex hidden peer-hover:flex"
                                                        >
                                                            <div
                                                                role="tooltip"
                                                                class="absolute after:left-[6.8rem] md:after:left-[6.8rem] payment_tooltiptext -left-1/2 -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-blue-500  border border-blue-500 text-gray-600 rounded tooltip_width sm:w-[25rem] md:w-[30rem] lg:w-72 xl:w-[23rem] 2xl:w-[25rem] px-4"
                                                            >
                                                                <p class="text-white font-semibold text-start text-sm lg:text-base">
                                                                    {{ $postRidePage->luggage_option2_tooltip }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div> -->
                                                    <div class="sups relative inline-flex">
                                                        <!-- Info Icon -->
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle-fill text-black peer" viewBox="0 0 16 16">
                                                            <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                                                        </svg>
        
                                                        <!-- Tooltip Content -->
                                                        <div class="absolute -left-20 -top-28 z-50 hidden peer-hover:block hover:block transition-opacity duration-300 ease-in-out opacity-0 peer-hover:opacity-100 hover:opacity-100">
                                                            <div class="relative w-64 sm:w-72 md:w-80 lg:w-96 px-4 mt-2">
                                                            <!-- Tooltip Arrow -->
                                                            <div class="absolute -bottom-1 left-24 transform -translate-x-1/2 w-4 h-4 bg-primary rotate-45"></div>
                                                            
                                                            <!-- Tooltip Body -->
                                                            <div class="bg-primary text-white rounded-lg shadow-xl p-4">
                                                                <p class="text-white">
                                                                {{ $postRidePage->luggage_option2_tooltip }}
                                                                </p>
                                                            </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </label>
                                                <input id="Medium-luggage" type="checkbox"
                                                    value="{{ $findRidePage->luggage_option2->features_setting_id }}"
                                                    {{ in_array($findRidePage->luggage_option2->features_setting_id, $luggages_check) ? 'checked' : '' }}
                                                    class="luggage w-4 h-4 ml-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                                            </div>
                                        @endisset
                                        @isset($findRidePage->luggage_option3)
                                            <div class="flex items-center justify-between p-3">
                                                <label for="Large-luggage" class="font-normal text-gray-900 flex space-x-1">
                                                    <span class="text-base md:text-lg">
                                                        {{ $findRidePage->luggage_option3->name }}
                                                    </span>
                                                <div class="sups relative inline-flex">
                                                        <!-- Info Icon -->
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle-fill text-black peer" viewBox="0 0 16 16">
                                                            <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                                                        </svg>
        
                                                        <!-- Tooltip Content -->
                                                        <div class="absolute -left-28 -top-36 z-50 hidden peer-hover:block hover:block transition-opacity duration-300 ease-in-out opacity-0 peer-hover:opacity-100 hover:opacity-100">
                                                            <div class="relative w-64 sm:w-72 md:w-80 lg:w-96 px-4 mt-2">
                                                            <!-- Tooltip Arrow -->
                                                            <div class="absolute -bottom-1 left-36 transform -translate-x-1/2 w-4 h-4 bg-primary rotate-45"></div>
                                                            
                                                            <!-- Tooltip Body -->
                                                            <div class="bg-primary text-white rounded-lg shadow-xl p-4">
                                                                <p class="text-white">
                                                                {{ $postRidePage->luggage_option3_tooltip }}
                                                                </p>
                                                            </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </label>
                                                <input id="Large-luggage" type="checkbox"
                                                    value="{{ $findRidePage->luggage_option3->features_setting_id }}"
                                                    {{ in_array($findRidePage->luggage_option3->features_setting_id, $luggages_check) ? 'checked' : '' }}
                                                    class="luggage w-4 h-4 ml-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                                            </div>
                                        @endisset
                                        @isset($findRidePage->luggage_option4)
                                            <div class="flex items-center justify-between p-3">
                                                <label for="multiple-luggage" class="font-normal text-gray-900 flex space-x-1">
                                                    <span class="text-base md:text-lg">
                                                        {{ $findRidePage->luggage_option4->name }}
                                                    </span>
                                                    <div class="sups relative inline-flex">
                                                        <!-- Info Icon -->
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle-fill text-black peer" viewBox="0 0 16 16">
                                                            <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                                                        </svg>
        
                                                        <!-- Tooltip Content -->
                                                        <div class="absolute -left-20 -top-28 z-50 hidden peer-hover:block hover:block transition-opacity duration-300 ease-in-out opacity-0 peer-hover:opacity-100 hover:opacity-100">
                                                            <div class="relative w-64 sm:w-72 md:w-80 lg:w-96 px-4 mt-2">
                                                            <!-- Tooltip Arrow -->
                                                            <div class="absolute -bottom-1 left-24 transform -translate-x-1/2 w-4 h-4 bg-primary rotate-45"></div>
                                                            
                                                            <!-- Tooltip Body -->
                                                            <div class="bg-primary text-white rounded-lg shadow-xl p-4">
                                                                <p class="text-white">
                                                                {{ $postRidePage->luggage_option4_tooltip }}
                                                                </p>
                                                            </div>
                                                            </div>
                                                        </div>
                                                    </div>


                                                
                                                </label>
                                                <input id="multiple-luggage" type="checkbox"
                                                    value="{{ $findRidePage->luggage_option4->features_setting_id }}"
                                                    {{ in_array($findRidePage->luggage_option4->features_setting_id, $luggages_check) ? 'checked' : '' }}
                                                    class="luggage w-4 h-4 ml-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                                            </div>
                                        @endisset
                                        @isset($findRidePage->luggage_option5)
                                            <div class="p-3">
                                                <div class="flex items-center justify-between">
                                                    <label for="no-luggage" class="font-normal text-gray-900 flex space-x-1">
                                                        <span class="text-base md:text-lg">
                                                            {{ $findRidePage->luggage_option5->name }}
                                                        </span>
                                                        <div class="sups relative inline-flex">
                                                        <!-- Info Icon -->
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle-fill text-black peer" viewBox="0 0 16 16">
                                                            <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                                                        </svg>
        
                                                        <!-- Tooltip Content -->
                                                        <div class="absolute -left-40 -top-28 z-50 hidden peer-hover:block hover:block transition-opacity duration-300 ease-in-out opacity-0 peer-hover:opacity-100 hover:opacity-100">
                                                            <div class="relative w-64 sm:w-72 md:w-80 lg:w-96 px-4 mt-2">
                                                            <!-- Tooltip Arrow -->
                                                            <div class="absolute -bottom-1 left-44 transform -translate-x-1/2 w-4 h-4 bg-primary rotate-45"></div>
                                                            
                                                            <!-- Tooltip Body -->
                                                            <div class="bg-primary text-white rounded-lg shadow-xl p-4">
                                                                <p class="text-white">
                                                                {{ $postRidePage->luggage_option5_tooltip }}
                                                                </p>
                                                            </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    </label>
                                                    <input id="no-luggage" type="checkbox"
                                                        value="{{ $findRidePage->luggage_option5->features_setting_id }}"
                                                        {{ in_array($findRidePage->luggage_option5->features_setting_id, $luggages_check) ? 'checked' : '' }}
                                                        class="luggage w-4 h-4 ml-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                                                </div>
                                                <p class="text-xs">{{ $findRidePage->luggage_option5_label }}</p>
                                            </div>
                                        @endisset
                                    </div>
                                </div>
                                
                                <div class="space-y-4 mb-4">
                                    <h3 class="text-primary text-2xl xl:text-3xl">
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
                                                    <span class="text-base md:text-lg">
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
                                                    <span class="text-base md:text-lg">
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
                                    <h3 class="text-primary text-2xl xl:text-3xl">
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
                                                    <span class="text-base md:text-lg">
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
                                                    <span class="text-base md:text-lg">
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
                                                    <span class="text-base md:text-lg">
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
                                </div>
                                
                                <button class="w-28 text-white text-lg font-FuturaMdCnBT px-4 py-2 bg-blue-600 rounded" onclick="navigateToSearchRoute()">{{ $findRidePage->filter_search_btn_label }}</button>
                                <button class="w-28 text-white text-lg font-FuturaMdCnBT px-4 py-2 bg-blue-600 rounded" onclick="resetFilters()">{{ $findRidePage->filter_close_btn_label }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <!---->
            <div class="col-span-3">
                <div class="bg-gray-100 rounded-md p-4 py-6">
                    <div class="text-center">
                        <h1 class="font-FuturaMdCnBT">
                            @isset($findRidePage->main_heading)
                                {{ $findRidePage->main_heading }}
                            @endisset
                        </h1>
                    </div>
                    <div
                        class="flex items-start flex-col md:flex-row justify-between">
                        <div class="w-full md:w-[30%]">
                            <!-- <label for="" class="md:hidden mt-2 m-1 block font-medium text-gray-900">From</label> -->
                            <div class="relative">
                                <div class="absolute inset-y-0 start-0 flex items-center pl-2 pointer-events-none">
                                    <img src="{{asset('home_page_icons/' . $findRidePage->from_field_icon)}}" class="w-auto h-6"
                                        alt="">
                                </div>
                                <input type="text" id="fromInput" value="{{ $request->from }}"
                                    class="bg-white pl-7 rounded-md md:rounded-r-none italic md:border-r-0 border border-gray-200 placeholder:text-gray-900 focus:outline-none text-base md:text-lg focus:border-sky-500 focus:ring-1 focus:ring-sky-500 block w-full p-2.5"
                                    @isset($findRidePage->search_section_from_placeholder)
                                        placeholder="{{ $findRidePage->search_section_from_placeholder }}"
                                    @endisset>

                                <!-- Suggestions Container for 'from' field -->
                                <div id="fromInput-suggestions" class="absolute left-0 right-0 bg-white shadow-lg mt-1 max-h-60 overflow-y-auto z-50"></div>
                            </div>
                            {{-- <div class="relative tooltip -bottom-4 group-hover:flex">
                                <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-primary text-gray-600 w-full md:w-1/2 rounded">
                                    <p class="text-white leading-none text-sm lg:text-base">validation.required</p>
                                </div>
                              </div> --}}
                            <p id="fromError" class="text-sm hidden text-red-500 absolute mt-1"></p>
                        </div>
                        <div class="mt-3 md:mt-0 w-full md:w-[5%] md:bg-gray-200 md:border border-gray-200 md:h-[3.1rem] flex items-center justify-center p-0.5 xl:p-0 ">
                            <button onclick="swapLocations()">
                                @isset($findRidePage->swap_field_icon)
                                    <img src="{{asset('home_page_icons/' . $findRidePage->swap_field_icon)}}" class="w-8 md:w-full xl:w-8 h-8 md:h-full xl:h-8 mx-auto" alt="">
                                @endisset
                            </button>
                        </div>
                        <div class="w-full md:w-[30%] mt-2 md:mt-0">
                            <!-- <label for="" class="md:hidden block font-medium text-gray-900">To</label> -->
                            <div class="relative">
                                <div class="absolute inset-y-0 start-0 flex items-center pl-2 pointer-events-none">
                                    <img src="{{asset('home_page_icons/' . $findRidePage->to_field_icon)}}" class="w-auto h-6" alt="">
                                </div>
                                <input type="text" id="toInput" value="{{ $request->to }}"
                                    class="bg-white pl-7 border-x-0 placeholder:text-gray-900 italic focus:outline-none border border-gray-200 text-base md:text-lg focus:border-sky-500 focus:ring-1 focus:ring-sky-500 block w-full p-2.5 rounded-md md:rounded-none"
                                    @isset($findRidePage->search_section_to_placeholder)
                                        placeholder="{{ $findRidePage->search_section_to_placeholder }}"
                                    @endisset>

                                <!-- Suggestions Container for 'to' field -->
                                <div id="toInput-suggestions" class="absolute left-0 right-0 bg-white shadow-lg mt-1 max-h-60 overflow-y-auto z-50"></div>
                            </div>
                            {{-- <div class="relative tooltip -bottom-4 group-hover:flex">
                                <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-primary text-gray-600 w-full md:w-1/2 rounded ">
                                    <p class="text-white leading-none text-sm lg:text-base">validation.required</p>
                                </div>
                              </div> --}}
                            <p id="toError" class="text-sm hidden text-red-500 absolute mt-1"></p>
                        </div>
                        <div class="w-44 mx-auto md:mx-0 md:w-[35%] mt-4 md:mt-0">
                            <!-- <label for="" class="md:hidden m-1 mt-2 block font-medium text-gray-900">Date (Optional)</label> -->
                            <div class="flex flex-col sm:flex-col md:flex-row items-center">
                                <div class="w-full md:w-[85%]">
                                    <div class="relative">
                                        <div class="absolute inset-y-0 start-0 flex items-center pl-2 pointer-events-none">
                                            <img src="{{asset('home_page_icons/' . $findRidePage->date_field_icon)}}" class="w-4 h-6" alt="">
                                        </div>
                                        <input type="text" id="dateInput" value="{{ $request->date }}"
                                            class="bg-white px-7 border border-gray-200 italic border-r-0 placeholder:text-gray-900 text-base md:text-lg focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 block w-full p-2.5 rounded-md md:rounded-none"
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
                                <div class="mt-4 md:mt-0 w-1/2 md:w-[15%] h-[2.8rem] md:h-[3.1rem] flex items-center justify-center -mr-1">
                                    <button
                                    onclick="navigateToSearchRoute()"
                                        class="font-FuturaMdCnBT bg-blue-500 w-full h-full flex items-center justify-center text-white rounded-md">
                                        <span class="block md:hidden">Serach</span>
                                        <img src="{{asset('home_page_icons/' . $findRidePage->search_field_icon)}}" class="w-auto h-6" alt="">
                                    </button>
                                </div>
                            </div>
                        </div>
                </div>

                <div class="my-6">

                    <div class="mt-6 rounded-lg">
                        @if ($rides && $rides->count() > 0)
                        <h1 class="can-exp-h1 text-center font-FuturaMdCnBT text-primary">
                            @isset($findRidePage->heading_ride_card_section)
                                {{ $findRidePage->heading_ride_card_section }}
                            @endisset
                        </h1>
                        @endif
                        @if ($rides && $rides->count() > 0)
                            @foreach ($rides as $ride)
                                <div class="relative even:bg-white odd:bg-gray-100 space-y-4 rounded-lg">
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
                                    @php
                                        $rideDetail = $ride->rideDetail->first();
                                    @endphp
                                    @if($rideDetail)
                                    <a
                                        @if ($ride->status === '2')
                                            href=""
                                        @elseif (auth()->user() && in_array($findRidePage->ride_features_option1->features_setting_id ?? null, explode('=', $ride->features)) && (auth()->user()->gender !== 'female' || ($pinkRideSetting->verfiy_phone_passenger == 1 ? !auth()->user()->phone_numbers->contains('verified', 1) : false)))
                                            href="javascript:void(0);" onclick="toggleModal1('modal-id1', 'Only female passengers @if ($pinkRideSetting->verfiy_phone_passenger == 1) with verified number @endif can select this ride')"
                                        @elseif (auth()->user() && in_array($findRidePage->ride_features_option16->features_setting_id, explode('=', $ride->features)))
                                            @if ($totalAverage < 1)
                                                href="javascript:void(0);" onclick="toggleModal1('modal-id1', 'Driver only want passengers with reviews')"
                                            @else
                                                href="{{ route('ride_detail', ['lang' => $selectedLanguage->abbreviation, 'departure' => $rideDetail->departure, 'destination' => $rideDetail->destination, 'id' => $ride->id]) }}"
                                            @endif
                                        @elseif (auth()->user() && in_array($findRidePage->ride_features_option15->features_setting_id ?? null, explode('=', $ride->features)))
                                            @if ($totalAverage < 3)
                                                href="javascript:void(0);" onclick="toggleModal1('modal-id1', 'Driver want only passengers with-3 star reviews above')"
                                            @else
                                                href="{{ route('ride_detail', ['lang' => $selectedLanguage->abbreviation, 'departure' => $rideDetail->departure, 'destination' => $rideDetail->destination, 'id' => $ride->id]) }}"
                                            @endif
                                        @elseif (auth()->user() && in_array($findRidePage->ride_features_option14->features_setting_id ?? null, explode('=', $ride->features)))
                                            @if ($totalAverage < 4)
                                                href="javascript:void(0);" onclick="toggleModal1('modal-id1', 'Driver want only passengers with-4 star reviews above')"
                                            @else
                                                href="{{ route('ride_detail', ['lang' => $selectedLanguage->abbreviation, 'departure' => $rideDetail->departure, 'destination' => $rideDetail->destination, 'id' => $ride->id]) }}"
                                            @endif
                                        @elseif (auth()->user() && in_array($findRidePage->ride_features_option13->features_setting_id ?? null, explode('=', $ride->features)))
                                            @if ($totalAverage < 4.5)
                                                href="javascript:void(0);" onclick="toggleModal1('modal-id1', 'Driver want only passengers with-5 star reviews above')"
                                            @else
                                                href="{{ route('ride_detail', ['lang' => $selectedLanguage->abbreviation, 'departure' => $rideDetail->departure, 'destination' => $rideDetail->destination, 'id' => $ride->id]) }}"
                                            @endif
                                        @else
                                            href="{{ route('ride_detail', ['lang' => $selectedLanguage->abbreviation, 'departure' => $rideDetail->departure, 'destination' => $rideDetail->destination, 'id' => $ride->id]) }}"
                                        @endif>
                                        <div class="rounded-lg shadow-3xl border-[3px] border-solid @if ($ride->status === '2') border-red-500 @elseif(isset($findRidePage->ride_features_option1) &&
                                                in_array($findRidePage->ride_features_option1->features_setting_id, explode('=', $ride->features))) border-pink-500 @elseif(isset($findRidePage->ride_features_option2) &&
                                                in_array($findRidePage->ride_features_option2->features_setting_id, explode('=', $ride->features))) border-green-500 @else border-gray-100 @endif"
                                            id="ride-{{ $ride->id }}">
                                            <div class="flex flex-col md:flex-row items-start md:items-center justify-between pb-0 p-4">
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
                                                    <div class="pr-8">
                                                        <p class="font-medium">
                                                            Total {{ $ride->seats }} seats</p>
                                                    </div>
                                                    {{-- {{ dd($postRidePage->cancellation_policy_label1->features_setting_id,$ride->booking_type) }} --}}
                                                    <p class="text-xl font-semibold text-primary">
                                                        <div class="flex items-center gap-2">
                                                            @if (isset($firm_cancellation_discount) && $firm_cancellation_discount!='' && $ride->booking_type == $postRidePage->cancellation_policy_label2->features_setting_id)
                                                                <span class="line-through">
                                                                    ${{ number_format(floatval($rideDetail->price), 2) }}
                                                                    </span>
                                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
                                                                    </svg>

                                                                    <span>

                                                                        ${{ $rideDetail->price - ($rideDetail->price * $firm_cancellation_discount) / 100 }}
                                                                    </span>

                                                                @else
                                                                    ${{ number_format(floatval($rideDetail->price), 2) }}
                                                                @endif

                                                                <small>
                                                                    @isset($findRidePage->card_section_per_seat)
                                                                        {{ $findRidePage->card_section_per_seat }}
                                                                    @endisset
                                                                </small>
                                                                @if (isset($firm_cancellation_discount) && $firm_cancellation_discount!='' && $ride->booking_type == $postRidePage->cancellation_policy_label2->features_setting_id)

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
                                                                @endif

                                                        </div>
                                                    </p>
                                                </div>
                                                {{-- <div class="pr-8">
                                                    <div class="pr-8">
                                                        <p class="font-medium">
                                                            Total {{ $ride->seats }} seats</p>
                                                    </div>
                                                    <p class="text-xl font-semibold text-primary">${{ number_format(floatval($rideDetail->price), 2) }}
                                                        <small>
                                                            @isset($findRidePage->card_section_per_seat)
                                                                {{ $findRidePage->card_section_per_seat }}
                                                            @endisset
                                                        </small>
                                                    </p>
                                                </div> --}}
                                            </div>
                                            <div class="flex flex-col sm:flex-col md:flex-row justify-between px-4">
                                                <div class="w-full">
                                                    <div class="relative mt-5 text-left">
                                                        <div class="flex items-center relative">
                                                            <div class="border-r-2 border-black border-solid absolute h-full left-3 md:left-6 top-2 z-10">
                                                                <span
                                                                    class="bg-primary rounded-full w-7 h-7 -top-[2px] -ml-[13px] absolute flex justify-center items-center">
                                                                    <img class="w-5 h-5 object-contain" src="{{ asset('./images/new-21-search-bar-from.png')}}" alt="">
                                                                </span>
                                                            </div>
                                                            <div class="ml-12 md:ml-20">
                                                                <div class="font-bold text-black">From</div>
                                                                <div class="text-primary md:mb-4">{{$rideDetail->departure }} {{ $ride->pickup }}</div>
                                                            </div>
                                                        </div>

                                                        <div class="flex items-center relative">
                                                            <div class="border-r-2 border-black border-solid absolute h-0 left-3 md:left-5 top-2 z-10">
                                                                <span
                                                                    class="bg-gray-200 rounded-full w-7 h-7 -top-[6px] -ml-[12px] md:-ml-[9px] absolute flex justify-center items-center">
                                                                    <img class="w-5 h-5 object-contain" src="{{ asset('./images/new-21-search-bar-to.png')}}" alt="">
                                                                </span>
                                                            </div>
                                                            <div class="ml-12 md:ml-20">
                                                                <div class="font-bold text-black">To</div>
                                                                <div class="text-primary md:mb-4">{{$rideDetail->destination }} {{ $ride->dropoff }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mt-4">
                                                    <p class="text-sm md:text-base whitespace-nowrap font-medium">
                                                        {{ intval($ride->seats) - intval($ride->bookings()->where('status', '<>', 3)->where('status', '<>', 4)->whereHas('passenger', function($query) { $query->whereNull('deleted_at'); })->sum('seats')) }}
                                                        @isset($findRidePage->card_section_seats_left)
                                                            {{ $findRidePage->card_section_seats_left }}
                                                        @endisset
                                                    </p>
                                                    <div class="my-4">
                                                        @if ($ride->booking_method == ($postRidePage->booking_option1->features_setting_id ?? null))
                                                            <a href="javascript:void(0);" onclick="toggleModal1('modal-id2', '{{ $postRidePage->booking_option1_tooltip }}')" class="bg-greenXS hover:bg-greenXS text-white text-base md:text-lg rounded font-FuturaMdCnBT hover:font-FuturaMdCnBT px-5 py-2 border border-greenXS hover:border-greenXS hover:text-white text-center focus:bg-greenXS focus:text-white active:text-white active:bg-greenXS flex items-center gap-2"><img class="w-8 h-8"
                                                                src="{{asset('home_page_icons/' . $postRidePage->booking_option1->icon)}}"
                                                                alt="">
                                                                Instant booking</a>
                                                        @elseif ($ride->booking_method == ($postRidePage->booking_option2->features_setting_id ?? null))
                                                            <a href="javascript:void(0);" onclick="toggleModal1('modal-id2', '{{ $postRidePage->booking_option2_tooltip }}')" class="bg-greenXS hover:bg-greenXS text-white text-base md:text-lg rounded font-FuturaMdCnBT hover:font-FuturaMdCnBT px-5 py-2 border border-greenXS hover:border-greenXS hover:text-white text-center focus:bg-greenXS focus:text-white active:text-white active:bg-greenXS flex items-center gap-2"><img class="w-8 h-8"
                                                                src="{{asset('home_page_icons/' . $postRidePage->booking_option2->icon)}}"
                                                                alt="">Request to book</a>
                                                        @endif
                                                    </div>
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
                                                    {{-- <div class="flex-none w-12 h-12 bg-gray-100 border rounded-full">
                                                        <img class="w-full h-full object-cover rounded-full"
                                                            src="{{ $ride->car_image }}"
                                                            alt="">
                                                    </div> --}}
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

                                                        {{-- @if ($ride->payment_method == ($findRidePage->payment_methods_option2->features_setting_id ?? null))
                                                            <a href="javascript:void(0);" onclick="toggleModal1('modal-id2', '{{ $postRidePage->payment_methods_option1_tooltip }}')"><img class="w-8 h-8" src="{{asset('home_page_icons/' . $findRidePage->payment_methods_option2->icon)}}" alt=""></a>
                                                        @elseif ($ride->payment_method == ($findRidePage->payment_methods_option3->features_setting_id ?? null))
                                                            <a href="javascript:void(0);" onclick="toggleModal1('modal-id2', '{{ $postRidePage->payment_methods_option2_tooltip }}')"><img class="w-8 h-8" src="{{asset('home_page_icons/' . $findRidePage->payment_methods_option3->icon)}}" alt=""></a>
                                                        @elseif ($ride->payment_method == ($findRidePage->payment_methods_option4->features_setting_id ?? null))
                                                            <a href="javascript:void(0);" onclick="toggleModal1('modal-id2', '{{ $postRidePage->payment_methods_option3_tooltip }}')"><img class="w-8 h-8" src="{{asset('home_page_icons/' . $findRidePage->payment_methods_option4->icon)}}" alt=""></a>
                                                        @endif --}}
                                                        @if ($ride->payment_method == ($findRidePage->payment_methods_option2->features_setting_id ?? null))
                                                            <a href="javascript:void(0);"
                                                            onclick="toggleModal1('modal-id2', '{{ $postRidePage->payment_methods_option1_tooltip }}', '{{ $findRidePage->payment_methods_option2->name ?? $findRidePage->payment_methods_option2->label }}')">
                                                                <img class="w-8 h-8" src="{{asset('home_page_icons/' . $findRidePage->payment_methods_option2->icon)}}" alt="">
                                                            </a>
                                                        @elseif ($ride->payment_method == ($findRidePage->payment_methods_option3->features_setting_id ?? null))
                                                            <a href="javascript:void(0);" 
                                                            onclick="toggleModal1('modal-id2', '{{ $postRidePage->payment_methods_option2_tooltip }}', '{{ $findRidePage->payment_methods_option3->name ?? $findRidePage->payment_methods_option3->label }}')">
                                                                <img class="w-8 h-8" src="{{asset('home_page_icons/' . $findRidePage->payment_methods_option3->icon)}}" alt="">
                                                            </a>
                                                        @elseif ($ride->payment_method == ($findRidePage->payment_methods_option4->features_setting_id ?? null))
                                                            <a href="javascript:void(0);"
                                                            onclick="toggleModal1('modal-id2', '{{ $postRidePage->payment_methods_option3_tooltip }}', '{{ $findRidePage->payment_methods_option4->name ?? $findRidePage->payment_methods_option4->label }}')">
                                                                <img class="w-8 h-8" src="{{asset('home_page_icons/' . $findRidePage->payment_methods_option4->icon)}}" alt="">
                                                            </a>
                                                        @endif
                                                    {{-- @if ($ride->smoke == ($findRidePage->smoking_option1->features_setting_id ?? null))
                                                        <a href="javascript:void(0);" onclick="toggleModal1('modal-id2', '{{ $postRidePage->smoking_option1_tooltip }}','{{ $findRidePage->smoking_label }}')"><img class="w-8 h-8" src="{{asset('home_page_icons/' . $findRidePage->smoking_option1->icon)}}" alt=""></a> --}}
                                                        @if ($ride->smoke == ($findRidePage->smoking_option2->features_setting_id ?? null))
                                                            <a href="javascript:void(0);" 
                                                            onclick="toggleModal1('modal-id2', '{{ $postRidePage->smoking_option2_tooltip }}','{{ $findRidePage->smoking_label }}')"><img class="w-8 h-8" src="{{asset('home_page_icons/' . $findRidePage->smoking_option2->icon)}}" alt=""></a>
                                                        @endif
                                                        {{-- @if ($ride->animal_friendly == ($findRidePage->pets_allowed_option1->features_setting_id ?? null))
                                                            <a href="javascript:void(0);" onclick="toggleModal1('modal-id2', '{{ $postRidePage->animals_option1_tooltip }}','{{ $findRidePage->pets_allowed_label }}')"><img class="w-8 h-8"
                                                                src="{{asset('home_page_icons/' . $findRidePage->pets_allowed_option1->icon)}}"
                                                                alt=""></a> --}}
                                                        @if ($ride->animal_friendly == ($findRidePage->pets_allowed_option2->features_setting_id ?? null))
                                                            <a href="javascript:void(0);"
                                                            onclick="toggleModal1('modal-id2', '{{ $postRidePage->animals_option2_tooltip }}','{{ $findRidePage->pets_allowed_label }}')"><img class="w-8 h-8"
                                                                src="{{asset('home_page_icons/' . $findRidePage->pets_allowed_option2->icon)}}"
                                                                alt=""></a>
                                                        @elseif ($ride->animal_friendly == ($findRidePage->pets_allowed_option3->features_setting_id ?? null))
                                                            <a href="javascript:void(0);" 
                                                            onclick="toggleModal1('modal-id2', '{{ $postRidePage->animals_option3_tooltip }}','{{ $findRidePage->pets_allowed_label }}')"><img class="w-8 h-8"
                                                                src="{{asset('home_page_icons/' . $findRidePage->pets_allowed_option3->icon)}}"
                                                                alt=""></a>
                                                        @endif
                                                        @if ($ride->luggage == ($findRidePage->luggage_option1->features_setting_id ?? null))
                                                            <a href="javascript:void(0);" 
                                                            onclick="toggleModal1('modal-id2', '{{ $postRidePage->luggage_option1_tooltip }}','{{ $findRidePage->luggage_label }}')"><img class="w-8 h-8"
                                                                src="{{asset('home_page_icons/' . $findRidePage->luggage_option1->icon)}}"
                                                                alt=""></a>
                                                        @elseif ($ride->luggage == ($findRidePage->luggage_option2->features_setting_id ?? null))
                                                            <a href="javascript:void(0);"
                                                            onclick="toggleModal1('modal-id2', '{{ $postRidePage->luggage_option2_tooltip }}','{{ $findRidePage->luggage_label }}')"><img class="w-8 h-8"
                                                                src="{{asset('home_page_icons/' . $findRidePage->luggage_option2->icon)}}"
                                                                alt=""></a>
                                                        @elseif ($ride->luggage == ($findRidePage->luggage_option3->features_setting_id ?? null))
                                                            <a href="javascript:void(0);"
                                                            onclick="toggleModal1('modal-id2', '{{ $postRidePage->luggage_option3_tooltip }}','{{ $findRidePage->luggage_label }}')"><img class="w-8 h-8"
                                                                src="{{asset('home_page_icons/' . $findRidePage->luggage_option3->icon)}}"
                                                                alt=""></a>
                                                        @elseif ($ride->luggage == ($findRidePage->luggage_option4->features_setting_id ?? null))
                                                            <a href="javascript:void(0);"
                                                            onclick="toggleModal1('modal-id2', '{{ $postRidePage->luggage_option4_tooltip }}','{{ $findRidePage->luggage_label }}')"><img class="w-8 h-8"
                                                                src="{{asset('home_page_icons/' . $findRidePage->luggage_option4->icon)}}"
                                                                alt=""></a>
                                                        @elseif ($ride->luggage == ($findRidePage->luggage_option5->features_setting_id ?? null))
                                                            <a href="javascript:void(0);"
                                                            onclick="toggleModal1('modal-id2', '{{ $postRidePage->luggage_option5_tooltip }}','{{ $findRidePage->luggage_label }}')"><img class="w-8 h-8"
                                                                src="{{asset('home_page_icons/' . $findRidePage->luggage_option5->icon)}}"
                                                                alt=""></a>
                                                        @endif
                                                        {{-- @if (in_array($findRidePage->ride_features_option1->features_setting_id ?? null, explode('=', $ride->features)))
                                                            <a href="javascript:void(0);" onclick="toggleModal1('modal-id2', '{{ $postRidePage->features_option1_tooltip }}', '{{ $findRidePage->ride_features_option1->name ?? $findRidePage->ride_features_option1->label }}')"><img class="w-8 h-8" src="{{asset('home_page_icons/' . $findRidePage->ride_features_option1->icon)}}" alt=""></a>
                                                        @endif --}}
                                                        @if (in_array($findRidePage->ride_features_option2->features_setting_id ?? null, explode('=', $ride->features)))
                                                            <a href="javascript:void(0);"
                                                            onclick="toggleModal1('modal-id2', '{{ $postRidePage->features_option2_tooltip }}', '{{ $findRidePage->ride_features_option2->name ?? $findRidePage->ride_features_option2->label }}')"><img class="w-8 h-8" src="{{asset('home_page_icons/' . $findRidePage->ride_features_option2->icon)}}" alt=""></a>
                                                        @endif
                                                        @if (in_array($findRidePage->ride_features_option3->features_setting_id ?? null, explode('=', $ride->features)))
                                                            <a href="javascript:void(0);" 
                                                            onclick="toggleModal1('modal-id2', '{{ $postRidePage->features_option3_tooltip }}', '{{ $findRidePage->ride_features_option3->name ?? $findRidePage->ride_features_option3->label }}')"><img class="w-8 h-8" src="{{asset('home_page_icons/' . $findRidePage->ride_features_option3->icon)}}" alt=""></a>
                                                        @endif
                                                        @if (in_array($findRidePage->ride_features_option8->features_setting_id ?? null, explode('=', $ride->features)))
                                                            <a href="javascript:void(0);" 
                                                            onclick="toggleModal1('modal-id2', '{{ $postRidePage->features_option8_tooltip }}', '{{ $findRidePage->ride_features_option8->name ?? $findRidePage->ride_features_option8->label }}')"><img class="w-8 h-8" src="{{asset('home_page_icons/' . $findRidePage->ride_features_option8->icon)}}" alt=""></a>
                                                        @endif
                                                        @if (in_array($findRidePage->ride_features_option9->features_setting_id ?? null, explode('=', $ride->features)))
                                                            <a href="javascript:void(0);" 
                                                            onclick="toggleModal1('modal-id2', '{{ $postRidePage->features_option9_tooltip }}', '{{ $findRidePage->ride_features_option9->name ?? $findRidePage->ride_features_option9->label }}')"><img class="w-8 h-8" src="{{asset('home_page_icons/' . $findRidePage->ride_features_option9->icon)}}" alt=""></a>
                                                        @endif
                                                        @if (in_array($findRidePage->ride_features_option10->features_setting_id ?? null, explode('=', $ride->features)))
                                                            <a href="javascript:void(0);" 
                                                            onclick="toggleModal1('modal-id2', '{{ $postRidePage->features_option10_tooltip }}', '{{ $findRidePage->ride_features_option10->name ?? $findRidePage->ride_features_option10->label }}')"><img class="w-8 h-8" src="{{asset('home_page_icons/' . $findRidePage->ride_features_option10->icon)}}" alt=""></a>
                                                        @endif
                                                        @if (in_array($findRidePage->ride_features_option11->features_setting_id ?? null, explode('=', $ride->features)))
                                                            <a href="javascript:void(0);" 
                                                            onclick="toggleModal1('modal-id2', '{{ $postRidePage->features_option11_tooltip }}', '{{ $findRidePage->ride_features_option11->name ?? $findRidePage->ride_features_option11->label }}')"><img class="w-8 h-8" src="{{asset('home_page_icons/' . $findRidePage->ride_features_option11->icon)}}" alt=""></a>
                                                        @endif
                                                        @if (in_array($findRidePage->ride_features_option12->features_setting_id ?? null, explode('=', $ride->features)))
                                                            <a href="javascript:void(0);" 
                                                            onclick="toggleModal1('modal-id2', '{{ $postRidePage->features_option12_tooltip }}', '{{ $findRidePage->ride_features_option12->name ?? $findRidePage->ride_features_option12->label }}')"><img class="w-8 h-8" src="{{asset('home_page_icons/' . $findRidePage->ride_features_option12->icon)}}" alt=""></a>
                                                        @endif
                                                        @if (in_array($findRidePage->ride_features_option13->features_setting_id ?? null, explode('=', $ride->features)))
                                                            <a href="javascript:void(0);" 
                                                            onclick="toggleModal1('modal-id2', '{{ $postRidePage->features_option13_tooltip }}', '{{ $findRidePage->ride_features_option13->name ?? $findRidePage->ride_features_option13->label }}')"><img class="w-8 h-8" src="{{asset('home_page_icons/' . $findRidePage->ride_features_option13->icon)}}" alt=""></a>
                                                        @endif
                                                        @if (in_array($findRidePage->ride_features_option14->features_setting_id ?? null, explode('=', $ride->features)))
                                                            <a href="javascript:void(0);" 
                                                            onclick="toggleModal1('modal-id2', '{{ $postRidePage->features_option14_tooltip }}', '{{ $findRidePage->ride_features_option14->name ?? $findRidePage->ride_features_option14->label }}')"><img class="w-8 h-8" src="{{asset('home_page_icons/' . $findRidePage->ride_features_option14->icon)}}" alt=""></a>
                                                        @endif
                                                        @if (in_array($findRidePage->ride_features_option15->features_setting_id ?? null, explode('=', $ride->features)))
                                                            <a href="javascript:void(0);" 
                                                            onclick="toggleModal1('modal-id2', '{{ $postRidePage->features_option15_tooltip }}', '{{ $findRidePage->ride_features_option15->name ?? $findRidePage->ride_features_option15->label }}')"><img class="w-8 h-8" src="{{asset('home_page_icons/' . $findRidePage->ride_features_option15->icon)}}" alt=""></a>
                                                        @endif
                                                        @if (in_array($findRidePage->ride_features_option16->features_setting_id ?? null, explode('=', $ride->features)))
                                                            <a href="javascript:void(0);" 
                                                            onclick="toggleModal1('modal-id2', '{{ $postRidePage->features_option16_tooltip }}', '{{ $findRidePage->ride_features_option16->name ?? $findRidePage->ride_features_option16->label }}')"><img class="w-8 h-8" src="{{asset('home_page_icons/' . $findRidePage->ride_features_option16->icon)}}" alt=""></a>
                                                        @endif
                                                        @if (in_array($postRidePage->features_option4->features_setting_id ?? null, explode('=', $ride->features)))
                                                            <a href="javascript:void(0);" 
                                                            onclick="toggleModal1('modal-id2', '{{ $postRidePage->features_option4_tooltip }}'), '{{ $findRidePage->ride_features_option4->name ?? $findRidePage->ride_features_option4->label }}'"><img class="w-8 h-8" src="{{asset('home_page_icons/' . $postRidePage->features_option4->icon)}}" alt=""></a>
                                                        @endif
                                                        @if (in_array($postRidePage->features_option5->features_setting_id ?? null, explode('=', $ride->features)))
                                                            <a href="javascript:void(0);"
                                                            onclick="toggleModal1('modal-id2', '{{ $postRidePage->features_option5_tooltip }}', '{{ $findRidePage->ride_features_option5->name ?? $findRidePage->ride_features_option5->label }}')"><img class="w-8 h-8" src="{{asset('home_page_icons/' . $postRidePage->features_option5->icon)}}" alt=""></a>
                                                        @endif
                                                        @if (in_array($postRidePage->features_option6->features_setting_id ?? null, explode('=', $ride->features)))
                                                            <a href="javascript:void(0);" 
                                                            onclick="toggleModal1('modal-id2', '{{ $postRidePage->features_option6_tooltip }}', '{{ $findRidePage->ride_features_option6->name ?? $findRidePage->ride_features_option6->label }}')"><img class="w-8 h-8" src="{{asset('home_page_icons/' . $postRidePage->features_option6->icon)}}" alt=""></a>
                                                        @endif
                                                        @if (in_array($postRidePage->features_option7->features_setting_id ?? null, explode('=', $ride->features)))
                                                            <a href="javascript:void(0);" 
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
                                                                $hasReviews = $filteredRatings->count() > 0;
                                                            @endphp
                                                        </div>
                                                    </div>
                                                    <div class="">
                                                        <span class="font-semibold text-gray-800">@if($hasReviews){{ number_format($totalAverage, 1) }}@else No Reviews Yet @endif</span>
                                                        
                                                        @if($hasReviews)
                                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                                fill="currentColor"
                                                                class="w-6 h-6 text-yellow-500 stroke-gray-600">
                                                                <path fill-rule="evenodd"
                                                                    d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z"
                                                                    clip-rule="evenodd"></path>
                                                            </svg>
                                                        @endif
                                                        
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
                                                                ${{ number_format(floatval(($ride->bookings()->where('user_id', auth()->user()->id)->where('status', '<>', 3)->where('status', '<>', 4)->whereHas('passenger', function($query) { $query->whereNull('deleted_at'); })->sum('seats') * $ride->price)), 2) }}
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
                                                                ${{ number_format(floatval(($ride->bookings()->where('user_id', auth()->user()->id)->where('status', '<>', 3)->where('status', '<>', 4)->whereHas('passenger', function($query) { $query->whereNull('deleted_at'); })->sum('seats') * $ride->price) + $ride->bookings->where('user_id', auth()->user()->id)->where('status', '<>', 3)->where('status', '<>', 4)->sum('booking_credit')), 2) }}
                                                            @else
                                                                $0.00
                                                            @endif
                                                        </p>
                                                    </div>
                                                </div> --}}
                                            </div>
                                        </div>
                                    </a>
                                    @endif
                                </div>
                            @endforeach
                            {{ $rides->appends(request()->except('page'))->links(); }}

                        @elseif ($rides && $rides->count() == 0 && $request->from && $request->to)
                            <div class="text-center">
                                <h1 class="font-FuturaMdCnBT">
                                    @isset($findRidePage->search_result_no_found_message)
                                        {{ $findRidePage->search_result_no_found_message }}
                                    @else
                                        No ride for this location exist
                                    @endisset
                                </h1>
                            </div>
                        @endif
                    </div>
                    @if ($recentSearches->count() > 0)
                    <h1 class="font-FuturaMdCnBT text-primary text-2xl mt-8">
                        @isset($findRidePage->search_section_recent_searches)
                            {{ $findRidePage->search_section_recent_searches }}
                        @endisset
                    </h1>
                    <div class="space-y-4 mt-4">
                        @foreach ($recentSearches as $index => $recentSearch)
                            @php
                                $colors = ['bg-blue-50', 'bg-green-50', 'bg-yellow-50', 'bg-purple-50', 'bg-pink-50', 'bg-indigo-50', 'bg-cyan-50', 'bg-teal-50'];
                                $colorClass = $colors[$index % count($colors)];
                            @endphp
                            <div class="{{ $colorClass }} rounded-lg shadow-3xl border border-solid border-gray-100 cursor-pointer hover:shadow-xl transition-shadow duration-200" onclick="SearchRoute('{{ $recentSearch->from }}', '{{ $recentSearch->to }}')">
                                <div class="flex flex-col sm:flex-col md:flex-row justify-between px-4">
                                    <div class="w-full">
                                        <div class="relative mt-5 text-left">
                                            <div class="flex items-center relative">
                                                    <div class="border-r-2 border-black border-solid absolute h-full left-3 md:left-6 top-2 z-10">
                                                            <span
                                                                class="bg-primary rounded-full w-7 h-7 -top-[2px] -ml-[13px] absolute flex justify-center items-center">
                                                                <img class="w-5 h-5 object-contain" src="{{ asset('./images/new-21-search-bar-from.png')}}" alt="">
                                                            </span>
                                                        </div>
                                                <div class="ml-10 md:ml-20 mt-1 mb-8 md:mb-0 flex flex-row md:flex-col">
                                                    <div class="font-bold text-black">
                                                        @isset($findRidePage->search_section_from_placeholder)
                                                            {{ $findRidePage->search_section_from_placeholder }}
                                                        @endisset
                                                    </div>
                                                    <div class="font-bold text-black mx-1 md:hidden">:</div>
                                                    <div class="text-primary md:mb-4">{{ $recentSearch->from }}</div>
                                                </div>
                                            </div>

                                            <div class="flex items-center relative">
                                                        <div class="border-r-2 border-black border-solid absolute h-0 left-3 md:left-5 top-2 z-10">
                                                            <span
                                                                class="bg-gray-200 rounded-full w-7 h-7 -top-[6px] -ml-[12px] md:-ml-[9px] absolute flex justify-center items-center">
                                                                <img class="w-5 h-5 object-contain" src="{{ asset('./images/new-21-search-bar-to.png')}}" alt="">
                                                            </span>
                                                        </div>
                                                <div class="ml-10 md:ml-20 mt-1 mb-6 md:mb-0 flex flex-row md:flex-col">
                                                    <div class="font-bold text-black">
                                                        @isset($findRidePage->search_section_to_placeholder)
                                                            {{ $findRidePage->search_section_to_placeholder }}
                                                        @endisset
                                                    </div>
                                                    <div class="font-bold text-black mx-1 md:hidden">:</div>
                                                    <div class="text-primary md:mb-4">{{ $recentSearch->to }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
                </div>
                <!-- Confirmation Modal for Hiding Rides -->
                <div class="hidden overflow-x-hidden overflow-y-auto fixed inset-0 z-50 outline-none focus:outline-none justify-center items-center" id="hide-ride-confirm-modal">
                    <div onclick="closeHideRideModal()" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                    <div class="fixed inset-0 z-10 w-screen overflow-y-auto flex items-center justify-center">
                        <div class="relative animate__animated animate__fadeIn z-20 transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
                            <button onclick="closeHideRideModal()" class="absolute top-2 right-2 p-1 rounded-full hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                            <div class="bg-white p-4 sm:p-6">
                            <!-- <div class="flex h-20 w-20 items-center justify-center rounded-full bg-blue-100 mx-auto mb-4">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-16 w-16 text-primary">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                                        </svg>
                                    </div> -->
                                <div class="flex items-center gap-2">
                                    <h3 class="card-heading text-center w-full" id="modal-title"> {{ $findRidePage->hide_ride_popup_heading ??'Confirm Hide Ride' }} </h3>
                                </div>
                                <div class="sm:flex sm:items-start">
                                    <div class="">
                                        <div class="mt-3">
                                            <p class="can-exp-p text-center">
                                                {{ $findRidePage->hide_ride_popup_text ??'Do you want this ride to be hidden from your search results? You will not be able to see it anymore' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-6 sm:flex sm:px-6 gap-2 justify-center">
                                <button type="button" onclick="closeHideRideModal()" class="button-exp-no-fill">
                                    {{ $findRidePage->hide_ride_popup_take_me_back_button ??'No, take me back' }}    
                                </button>   
                                <button type="button" id="confirm-hide-ride" class="button-exp-fill">
                                    {{ $findRidePage->hide_ride_popup_confirm_button ??'Yes, hide it' }}

                                </button>
                            </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
                <div class="hidden overflow-x-hidden overflow-y-auto fixed inset-0 z-50 outline-none focus:outline-none justify-center items-center"
                    id="modal-id1">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="toggleModal1('modal-id1')"></div>
                    <div class="fixed inset-0 z-10 w-screen overflow-y-auto  flex items-center justify-center">
                        <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                        <!--content-->
                            <div
                                class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg w-full modal-border">
                                <button type="button" onclick="toggleModal1('modal-id1')" class="absolute top-3 right-3 text-gray-400 hover:text-gray-500">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                                <!--body-->
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
                                        <h3 class="card-heading"></h3>
                                        <div class="mt-2 text-center">
                                            <p class="can-exp-p" id="features-modal-text"></p>
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
                <div class="hidden overflow-x-hidden overflow-y-auto fixed inset-0 z-50 outline-none focus:outline-none justify-center items-center"
                    id="modal-id2">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                    <div class="fixed inset-0 z-10 w-screen overflow-y-auto flex items-center justify-center">
                        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="toggleModal1('modal-id2')"></div>
                            <!--content-->
                            <div
                                class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl modal-border">
                                <button type="button" onclick="toggleModal1('modal-id2')" class="absolute top-3 right-3 text-gray-400 hover:text-gray-500">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                                <!--body-->
                                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                                    <div class="mt-4 text-center w-full">
                                        <h3 class="card-heading"></h3>
                                        <div class="mt-2 w-full">
                                            <p class="can-exp-p text-center" id="features-modal-text"></p>
                                        </div>
                                    </div>
                                </div>
                                <!--footer-->
                                <div
                                    class="px-4 pb-6 pt-4  sm:flex sm:flex-row-reverse sm:px-6 justify-center">
                                    <button
                                        class="bg-greenXS hover:bg-greenXS text-white text-base md:text-lg rounded font-FuturaMdCnBT hover:font-FuturaMdCnBT px-5 py-2 border border-greenXS hover:border-greenXS hover:text-white text-center focus:bg-greenXS focus:text-white active:text-white active:bg-greenXS flex items-center gap-2"
                                        type="button" onclick="toggleModal1('modal-id2')">
                                        Close
                                    </button>
                                </div>
                            </div>
                            </div>
                        </div>
                    </div>
            </div>
        </div>
    </div>

<!-- Phone Verification Required Modal -->
<div id="phoneVerificationModal" class="hidden fixed z-50 inset-0 overflow-y-auto" aria-labelledby="phone-verification-modal-title" role="dialog" aria-modal="true">
    <div onclick="closePhoneVerificationModal()" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border" onclick="event.stopPropagation()">
                <button type="button" onclick="closePhoneVerificationModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-500 z-50">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <div class="bg-white px-4 mt-10 sm:mt-1 pb-4 pt-16 sm:p-6 sm:pb-4 sm:pt-16">
                    <div class="sm:flex sm:items-start justify-center">
                        <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full sm:mx-0 sm:h-10 sm:w-10 bg-yellow-100">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-yellow-600">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                            </svg>
                        </div>
                    </div>
                    <div class="text-center sm:ml-4 sm:mt-0 sm:text-left">
                        <div class="">
                            <h3 class="text-3xl text-center font-FuturaMdCnBT text-gray-900 mb-4" id="phone-verification-modal-title">Phone Verification Required</h3>
                        </div>
                        <div class="mt-2 w-full">
                            <p class="can-exp-p text-center">To maintain a safe and reliable community, you must have a verified phone number before booking or posting a ride.</p>
                        </div>
                    </div>
                </div>
                <div class="px-4 pb-6 pt-4 sm:flex sm:flex-row-reverse sm:px-6 justify-center gap-3">
                    <button type="button" onclick="goToPhoneVerification()" class="inline-flex justify-center rounded bg-primary px-6 py-2 font-FuturaMdCnBT text-lg text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-blue-600">
                        Verify My Number
                    </button>
                    <button type="button" onclick="closePhoneVerificationModal()" class="inline-flex justify-center rounded bg-gray-300 px-6 py-2 font-FuturaMdCnBT text-lg text-gray-700 hover:text-gray-800 hover:shadow-lg shadow-sm hover:bg-gray-400">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <script>
        // Global variables
        let removedRideIds = [];
        let currentRideIdToHide = null;

        // Function to close the hide ride modal
        function closeHideRideModal() {
            document.getElementById('hide-ride-confirm-modal').classList.add('hidden');
            currentRideIdToHide = null;
        }

        // Function to toggle other modals
        function toggleModal1(modalID, message, title = '') {
            console.log(modalID, message, title);
            var modalElement = document.getElementById(modalID);
            if (message) {
                if (title) {
                    var titleElement = modalElement.querySelector(".modal-title");
                    if (titleElement) titleElement.innerText = title;
                }

                var messageElement = modalElement.querySelector("#features-modal-text");
                if (messageElement) messageElement.innerText = message;
            }

            modalElement.classList.toggle("hidden");
            modalElement.classList.toggle("flex");
        }

        function clearDateInput() {
            document.getElementById('dateInput').value = null;
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
                if (currentRideIdToHide) {
                    const rideElement = document.getElementById('ride-' + currentRideIdToHide);

                    if (rideElement) {
                        rideElement.style.display = 'none';
                        removedRideIds.push(currentRideIdToHide);
                        localStorage.setItem('removedRideIds', JSON.stringify(removedRideIds));

                        // Check if we need to load more rides
                        const visibleRides = document.querySelectorAll('.p-4.bg-white:not([style*="display: none"])');
                        const ridesPerPage = 8;
                        if (visibleRides.length < ridesPerPage) {
                            console.log("Less than", ridesPerPage, "rides visible");
                        }
                    }

                    closeHideRideModal();
                }
            });

            // Rest of your existing code...
        });


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
            dateFormat: 'Y-m-d', // Customize date format if needed
            minDate: 'today', // Restrict to future dates only
            disableMobile: true, // Optional: Disable mobile-friendly mode
            onChange: function(selectedDates, dateStr, instance) {
                // Format the date in the desired format
                const formattedDate = flatpickr.formatDate(selectedDates[0], 'F d, Y');

                // Update the input value with the formatted date
                dateInput.value = formattedDate;
            }
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

        document.getElementById('keyword').addEventListener('keypress', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                navigateToSearchRoute();
            }
        })

        // Initialize an array to store selected features
        let getFeatures = '{{ isset($_GET['features']) ? $_GET['features'] : '' }}';
        getFeatures = getFeatures ? getFeatures.split(";").filter(f => f.trim() !== "") : [];
        let selectedFeatures = getFeatures;

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
                if (!selectedFeatures.includes(featureValue)) {
                    selectedFeatures.push(featureValue);
                }
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
            // Construct the URL with query parameters
            let searchUrl =
                `{{ route('search_ride', ['lang' => $selectedLanguage->abbreviation]) }}?from=${SearchfromValue}&to=${SearchtoValue}&date=&driver_age=&driver_rating=&driver_phone=&driver_name=&keyword=&passenger_rating=&payment_method=&vehicle_type=&features=&luggage=&smoking=&pets=`;

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
            const keyword = document.getElementById('keyword').value;
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
                `{{ route('search_ride', ['lang' => $selectedLanguage->abbreviation]) }}?from=${fromValue}&to=${toValue}&date=${dateValue}&driver_age=${driverAge}&driver_rating=${driverRating}&driver_phone=${driverPhone}&driver_name=${driverName}&keyword=${keyword}&passenger_rating=${passengerRating}&payment_method=${paymentMethod}&vehicle_type=${VehicleType}&features=${featuresParam}&luggage=${luggage}&smoking=${smokingValue}&pets=${petsValue}`;

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
        // Debounce function to limit the number of AJAX requests
        function debounce(func, delay) {
            let timer;
            return function() {
                clearTimeout(timer);
                timer = setTimeout(func, delay);
            };
        }

        // Function to fetch cities based on search input
        function fetchCities(searchTerm, fieldId) {
            // Get the state_id (if required) or set it to null or default
            let stateId = 0;  // You can adjust this if you need to pass state_id
            let url = '{{ url('get-cities-by-state') }}';
            let params = {
                state_id: stateId,
                search: searchTerm
            };

            $.ajax({
                url: "{{ url('get-cities-by-state') }}",
                type: "POST",
                data: {
                    search: searchTerm,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(result) {
                    let suggestionsContainer = $('#' + fieldId + '-suggestions');
                    suggestionsContainer.empty();  // Clear previous suggestions

                    $.each(result.cities, function(key, value) {
                        // Create a list item for each city
                        let displayText = `${value.name}, ${value.state.abrv}, ${value.state.country.name}`;

                        let suggestionItem = $('<div class="suggestion-item p-2 hover:bg-gray-200 cursor-pointer"></div>')
                            .text(displayText)
                            .on('click', function() {
                                $('#' + fieldId).val(displayText);  // Set the selected city in the input field
                                suggestionsContainer.empty();  // Clear the suggestions
                            });

                        suggestionsContainer.append(suggestionItem);
                    });
                }
            });
        }

        // Attach event listener to the input fields with debounce
        $(document).ready(function() {
            $('#fromInput').on('input', debounce(function() {
                let searchTerm = $('#fromInput').val();
                if (searchTerm.length >= 2) {  // Start searching after 3 characters are entered
                    fetchCities(searchTerm, 'fromInput');
                }
            }, 500));

            $('#toInput').on('input', debounce(function() {
                let searchTerm = $('#toInput').val();
                if (searchTerm.length >= 2) {  // Start searching after 3 characters are entered
                    fetchCities(searchTerm, 'toInput');
                }
            }, 500));
        });
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
         // Function to show modal with transitions
    function showModal() {
        const modal = document.getElementById('my-modal');
        const backdrop = document.getElementById('modal-backdrop');
        const container = document.getElementById('modal-container');

        modal.classList.remove('hidden');

        // Trigger reflow to enable transitions
        void modal.offsetWidth;

        backdrop.classList.remove('bg-opacity-0');
        backdrop.classList.add('bg-opacity-75');

        container.classList.remove('opacity-0', 'scale-95');
        container.classList.add('opacity-100', 'scale-100');
    }

    // Function to close modal with transitions
    function closeModal() {
        const backdrop = document.getElementById('modal-backdrop');
        const container = document.getElementById('modal-container');

        backdrop.classList.remove('bg-opacity-75');
        backdrop.classList.add('bg-opacity-0');

        container.classList.remove('opacity-100', 'scale-100');
        container.classList.add('opacity-0', 'scale-95');

        // Wait for transition to complete before hiding
        setTimeout(() => {
            document.getElementById('my-modal').classList.add('hidden');
        }, 300);
    }

    // Auto-show modal if there's a success message
    @if(session('success'))
    document.addEventListener('DOMContentLoaded', showModal);
    @endif

    // Phone Verification Modal Functions
    function showPhoneVerificationModal() {
        const modal = document.getElementById('phoneVerificationModal');
        if (!modal) {
            console.error('Phone verification modal not found');
            return;
        }
        modal.classList.remove('hidden');
        modal.style.setProperty('display', 'block', 'important');
        modal.style.setProperty('visibility', 'visible', 'important');
        modal.style.setProperty('opacity', '1', 'important');
        modal.style.setProperty('z-index', '50', 'important');
    }

    function closePhoneVerificationModal() {
        const modal = document.getElementById('phoneVerificationModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.style.removeProperty('display');
            modal.style.removeProperty('visibility');
            modal.style.removeProperty('opacity');
            modal.style.removeProperty('z-index');
        }
    }

    function goToPhoneVerification() {
        @php
            $lang = isset($selectedLanguage) && $selectedLanguage ? $selectedLanguage->abbreviation : (session('selectedLanguage') ?: 'en');
        @endphp
        window.location.href = '{{ route("step5to5", ["lang" => $lang]) }}';
    }

    // Make functions globally available
    window.showPhoneVerificationModal = showPhoneVerificationModal;
    window.closePhoneVerificationModal = closePhoneVerificationModal;
    window.goToPhoneVerification = goToPhoneVerification;

    // Check phone verification and intercept ride link clicks
    @php
        $needsPhoneVerification = false;
        if (auth()->user()) {
            $user = auth()->user();
            // Check if user has any phone numbers from phone_numbers table
            $phoneNumber = \App\Models\PhoneNumber::where('user_id', $user->id)->first();
            $hasPhoneNumber = !is_null($phoneNumber);
            // Check if user has a verified phone number from phone_numbers table
            $verifiedPhoneNumber = \App\Models\PhoneNumber::where('user_id', $user->id)->where('verified', '1')->first();
            $hasVerifiedPhone = !is_null($verifiedPhoneNumber);
            // User needs verification if they don't have a phone OR don't have a verified phone
            $needsPhoneVerification = !$hasPhoneNumber || !$hasVerifiedPhone;
        }
    @endphp
    @if($needsPhoneVerification)
    document.addEventListener('DOMContentLoaded', function() {
        // Use event delegation on the document to catch all clicks
        document.addEventListener('click', function(e) {
            // Find the closest anchor tag from the click target
            let link = e.target.closest('a');
            if (!link) return;
            
            const href = link.getAttribute('href');
            if (!href || href.trim() === '' || href === '#' || href.includes('javascript:void(0)')) {
                return;
            }
            
            // Check if this is a ride detail link - matches pattern: /lang/ride/departure/to/destination/id
            // The route generates URLs like: /en/ride/Ottawa/to/Montreal/123
            const rideDetailPattern = /\/ride\/[^\/]+\/to\/[^\/]+\/\d+/;
            if (rideDetailPattern.test(href)) {
                // Don't intercept if it already has an onclick handler (for other modals like pink ride, reviews, etc.)
                const onclickAttr = link.getAttribute('onclick');
                if (onclickAttr && onclickAttr.includes('toggleModal')) {
                    return;
                }
                
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                
                // Show the phone verification modal
                if (typeof window.showPhoneVerificationModal === 'function') {
                    window.showPhoneVerificationModal();
                } else if (typeof showPhoneVerificationModal === 'function') {
                    showPhoneVerificationModal();
                } else {
                    console.error('showPhoneVerificationModal function not found');
                }
                
                return false;
            }
        }, true); // Use capture phase to intercept early
    });
    @endif

    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closePhoneVerificationModal();
        }
    });
    </script>
@endsection
