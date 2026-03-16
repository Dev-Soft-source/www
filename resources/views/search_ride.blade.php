@extends('layouts.template')

@section('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endsection

@section('content')
    @if (session('success'))
        <div id="my-modal" class="relative z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <!-- Backdrop with transition -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-0 transition-opacity duration-300 ease-in-out z-10"
                id="modal-backdrop"></div>

            <!-- Modal container with transition -->
            <div class="fixed inset-0 flex items-center justify-center p-4 z-20 opacity-0 scale-95 transition-all duration-300 ease-in-out"
                id="modal-container">
                <div
                    class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full w-full">
                    <!-- Modal content with transition -->
                    <div
                        class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 w-full sm:max-w-xl modal-border">
                        <button type="button" onclick="closeModal()"
                            class="absolute top-3 right-3 text-gray-400 hover:text-gray-500">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
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
                                class="inline-flex w-full justify-center rounded bg-greenXS px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-greenXS sm:ml-3 sm:w-24 transition-colors duration-200">{{ $siteText['close_btn_text'] ?? 'Close' }}</button>
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
            @if (session('success'))
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
                                                class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full sm:mx-0 sm:h-10 sm:w-10 bg-[#c75b5b]">
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
                                class="inline-flex w-full justify-center rounded bg-red-600 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-red-400 sm:ml-3 sm:w-24">{{ $siteText['close_btn_text'] ?? 'Close' }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if (session('message'))
        <div class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full justify-center p-4 text-center items-center sm:p-0">
                    <div
                        class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start justify-center">
                                <!-- <div
                                                    class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full sm:mx-0 sm:h-10 sm:w-10 bg-[#c75b5b]">
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
                                class="inline-flex w-full justify-center rounded bg-red-600 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-red-400 sm:ml-3 sm:w-24">{{ $siteText['close_btn_text'] ?? 'Close' }}</a>
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
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" />
                    </svg>
                    <span class="text-xl">
                        {{ $siteText['search_filters_btn_text'] ?? 'Search filters' }}
                    </span>
                </button>

                <div id="search-filter-overlay" class="fixed inset-0 bg-black bg-opacity-50 hidden z-40 lg:hidden"></div>
                <div id="search-filter"
                    class="search-filter fixed top-0 right-0 h-full bg-white w-11/12 sm:w-96 lg:w-full transform translate-x-full lg:translate-x-0 lg:static lg:h-auto transition-transform duration-300 z-40">
                    <button id="search-filter-close"
                        class="search-filter-close border w-6 h-6 overflow-hidden flex items-center justify-center border-gray-500 rounded-full text-gray-500 text-3xl absolute top-3 right-4 hover:text-red-500 lg:hidden">
                        &times;
                    </button>
                    <div
                        class="search-filter-menu bg-white border lg:border-none rounded-t-lg rounded-b-lg pt-12 p-4 lg:p-0 border-gray-200 w-full shadow">
                        <div class="">
                            <div class="bg-white rounded-t-lg shadow-3xl">
                                <div
                                    class="rounded-t-lg bg-primary text-white font-medium text-xl flex items-center justify-center space-x-2 p-4">
                                    <div
                                        class="w-9 h-9 mr-2 p-1 flex items-center justify-center bg-white rounded-full border-2 border-[#1F4174]">
                                        <img class="w-5 h-5 object-contain" src="{{ asset('assets/filter.png') }}"
                                            alt="">
                                    </div>
                                    @isset($findRidePage->filter_section_heading)
                                        {{ $findRidePage->filter_section_heading }}
                                    @endisset
                                </div>

                                <div class="bg-white p-4 ">
                                    <div class="divide-y mb-2">
                                        @php
                                            $features_check = isset($_GET['features'])
                                                ? explode(';', $_GET['features'])
                                                : [];
                                        @endphp
                                        @isset($findRidePage->ride_features_option1->features_setting_id)
                                            <div class="flex items-start justify-between p-3">
                                                <label for="pink-ride" class="text-gray-900 flex space-x-1">
                                                    <span class="text-pink-600 text-base md:text-lg">
                                                        {{ $findRidePage->ride_features_option1->name }}
                                                    </span>

                                                </label>
                                                <input id="pink-ride" type="checkbox"
                                                    value="{{ $findRidePage->ride_features_option1->features_setting_id }}"
                                                    {{ in_array($findRidePage->ride_features_option1->features_setting_id, $features_check) ? 'checked' : '' }}
                                                    class="ride-preferences w-4 h-4 ml-4 mt-1 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                            </div>
                                        @endisset
                                        @isset($findRidePage->ride_features_option2->features_setting_id)
                                            <div class="flex items-start justify-between p-3">
                                                <label for="Extra+" class="text-gray-900 flex space-x-1">
                                                    <span class="text-indigo-600 text-base md:text-lg">
                                                        {{ $findRidePage->ride_features_option2->name }}
                                                    </span>
                                                    
                                                </label>
                                                <input id="Extra+" type="checkbox"
                                                    value="{{ $findRidePage->ride_features_option2->features_setting_id }}"
                                                    {{ in_array($findRidePage->ride_features_option2->features_setting_id, $features_check) ? 'checked' : '' }}
                                                    class="ride-preferences w-4 h-4 ml-4 mt-1 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
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
                                                @php
                                                    $driverAgeOptions = [20, 30, 40, 50, 60];
                                                @endphp
                                                <select id="driverAge" name=""
                                                    class="bg-gray-100 text-base md:text-lg border-0 text-black rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 block w-full p-2.5"
                                                    {{-- onchange="navigateToSearchRoute()" --}}>
                                                    <option value="0"
                                                        {{ $request->driver_age == 0 ? 'selected' : '' }}>
                                                        @isset($findRidePage->driver_age_placeholder)
                                                            {{ $findRidePage->driver_age_placeholder }}
                                                        @endisset
                                                    </option>
                                                    @foreach ($driverAgeOptions as $age)
                                                        <option value="{{ $age }}"
                                                            {{ $request->driver_age == $age ? 'selected' : '' }}>
                                                            +{{ $age }}
                                                        </option>
                                                    @endforeach
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
                                                @php
                                                    $driverRatingOptions = [
                                                        4.5 => '5',
                                                        4 => '4 and above',
                                                        3 => '3 and above',
                                                        2 => '2 and above',
                                                        1 => '1 and above',
                                                    ];
                                                @endphp
                                                <select id="driverRating" name=""
                                                    class="bg-gray-100 border-0 placeholder:text-gray-900 text-black text-base md:text-lg rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 block w-full p-2.5"
                                                    {{-- onchange="navigateToSearchRoute()" --}}>
                                                    <option value="0"
                                                        {{ $request->driver_rating == 0 ? 'selected' : '' }}>
                                                        @isset($findRidePage->driver_rating_placeholder)
                                                            {{ $findRidePage->driver_rating_placeholder }}
                                                        @endisset
                                                    </option>
                                                    @foreach ($driverRatingOptions as $value => $text)
                                                        <option value="{{ $value }}"
                                                            {{ $request->driver_rating == $value ? 'selected' : '' }}>
                                                            {{ $text }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-2 mb-2 mr-2 lg:mr-2">
                                            <input id="driverPhone" name="" type="checkbox"
                                                {{ $request->driver_phone == 1 ? 'checked' : '' }}
                                                class="w-4 h-4 ml-4 mt-1 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2"
                                                {{-- onchange="navigateToSearchRoute()" --}}>
                                            <label for="driverPhone" class="block font-normal text-gray-900">
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
                                                @php
                                                    $passengerRatingOptions = [13, 14, 15, 16];
                                                @endphp
                                                <select id="passengerRating" name=""
                                                    class="bg-gray-100 border-0 placeholder:text-gray-900 text-black text-base md:text-lg rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 block w-full p-2.5 whitespace-pre-line pr-8"
                                                    {{-- onchange="navigateToSearchRoute()" --}}>
                                                    <option value=""
                                                        {{ $request->passenger_rating == '' ? 'selected' : '' }}>
                                                        @isset($findRidePage->passengers_rating_placeholder)
                                                            {{ $findRidePage->passengers_rating_placeholder }}
                                                        @endisset
                                                    </option>
                                                    @foreach ($passengerRatingOptions as $optionNum)
                                                        @php
                                                            $optionProperty = 'ride_features_option' . $optionNum;
                                                            $option = $findRidePage->$optionProperty ?? null;
                                                            $isSelected =
                                                                $option &&
                                                                $request->passenger_rating ==
                                                                    $option->features_setting_id;
                                                            $dynamicText = $option
                                                                ? str_replace(
                                                                    'passengers',
                                                                    'co-passengers',
                                                                    $option->name,
                                                                )
                                                                : '';
                                                        @endphp
                                                        @if ($option && isset($option->features_setting_id))
                                                            <option value="{{ $option->features_setting_id }}"
                                                                {{ $isSelected ? 'selected' : '' }}>
                                                                {{ $dynamicText }}
                                                            </option>
                                                        @endif
                                                    @endforeach
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
                                                @php
                                                    $paymentMethodOptions = [
                                                        2 => 'payment_methods_option2',
                                                        3 => 'payment_methods_option3',
                                                        4 => 'payment_methods_option4',
                                                    ];
                                                @endphp
                                                <select id="payment-method" name=""
                                                    class="bg-gray-100 border-0 placeholder:text-gray-900 text-black text-base md:text-lg rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 block w-full p-2.5"
                                                    {{-- onchange="navigateToSearchRoute()" --}}>
                                                    @isset($findRidePage->payment_methods_option1)
                                                        <option value=""
                                                            {{ $request->payment_method == '' ? 'selected' : '' }}>
                                                            {{ $findRidePage->payment_methods_option1 }}
                                                        </option>
                                                    @endisset
                                                    @foreach ($paymentMethodOptions as $optionNum => $optionProperty)
                                                        @php
                                                            $option = $findRidePage->$optionProperty ?? null;
                                                            $isSelected =
                                                                $option &&
                                                                $request->payment_method ==
                                                                    $option->features_setting_id;
                                                        @endphp
                                                        @if ($option && isset($option->features_setting_id))
                                                            <option value="{{ $option->features_setting_id }}"
                                                                {{ $isSelected ? 'selected' : '' }}>
                                                                {{ $option->name }}
                                                            </option>
                                                        @endif
                                                    @endforeach
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
                                                @php
                                                    $vehicleTypes = [
                                                        'convertible' => 'Convertable',
                                                        'coupe' => 'Coupe',
                                                        'hatchback' => 'Hatchback',
                                                        'minivan' => 'Minivan',
                                                        'sedan' => 'Sedan',
                                                        'station_wagon' => 'Station wagon',
                                                        'suv' => 'SUV',
                                                        'truck' => 'Truck',
                                                        'van' => 'Van',
                                                    ];
                                                @endphp
                                                <select id="VehicleType" name=""
                                                    class="bg-gray-100 border-0 placeholder:text-gray-900 text-black text-base md:text-lg rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 block w-full p-2.5"
                                                    {{-- onchange="navigateToSearchRoute()" --}}>
                                                    <option value=""
                                                        {{ $request->vehicle_type == '' ? 'selected' : '' }}>
                                                        @isset($findRidePage->vehicle_type_placeholder)
                                                            {{ $findRidePage->vehicle_type_placeholder }}
                                                        @endisset
                                                    </option>
                                                    @foreach ($vehicleTypes as $key => $default)
                                                        @php
                                                            $valueProperty = 'vehicle_type_' . $key . '_value';
                                                            $textProperty = 'vehicle_type_' . $key . '_text';
                                                            $value = $findRidePage->$valueProperty ?? $default;
                                                            $text = $findRidePage->$textProperty ?? $default;
                                                            $isSelected = (string) $request->vehicle_type === (string) $value;
                                                        @endphp
                                                        <option value="{{ $value }}"
                                                            {{ $isSelected ? 'selected' : '' }}>
                                                            {{ $text }}
                                                        </option>
                                                    @endforeach
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
                                                $features_check = isset($_GET['features'])
                                                    ? explode(';', $_GET['features'])
                                                    : [];

                                                
                                            @endphp
                                            @foreach ($featureOptions as $featureOption)
                                                @php
                                                    $isChecked = in_array($featureOption['id'], $features_check);
                                                @endphp
                                                <div class="flex items-start justify-between p-3">
                                                    <label for="{{ $featureOption['slug'] }}"
                                                        class="font-normal text-gray-900 flex space-x-1">
                                                        <span class="text-base md:text-lg">{{ $featureOption['label'] }}</span>
                                                    </label>
                                                    <input id="{{ $featureOption['slug'] }}" type="checkbox"
                                                        value="{{ $featureOption['id'] }}"
                                                        {{ $isChecked ? 'checked' : '' }}
                                                        class="ride-preferences w-4 h-4 ml-4 mt-1 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                                                </div>
                                            @endforeach
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
                                                $luggages_check = isset($_GET['luggage'])
                                                    ? explode(';', $_GET['luggage'])
                                                    : [];
                                            @endphp
                                            @isset($findRidePage->luggage_option1)
                                                <div class="flex items-center justify-between p-3">
                                                    <label for="small-luggage"
                                                        class="font-normal text-gray-900 flex space-x-1">
                                                        <span class="text-base md:text-lg">
                                                            {{ $findRidePage->luggage_option1->name }}
                                                        </span>
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                            fill="currentColor" class="bi bi-info-circle-fill text-black"
                                                            viewBox="0 0 16 16"
                                                            data-tippy-content="{{ $postRidePage->luggage_option1_tooltip }}">
                                                            <path
                                                                d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
                                                        </svg>
                                                    </label>
                                                    <input id="small-luggage" type="checkbox"
                                                        value="{{ $findRidePage->luggage_option1->features_setting_id }}"
                                                        {{ in_array($findRidePage->luggage_option1->features_setting_id, $luggages_check) ? 'checked' : '' }}
                                                        class="luggage w-4 h-4 ml-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                                                </div>
                                            @endisset
                                            @isset($findRidePage->luggage_option2)
                                                <div class="flex items-center justify-between p-3">
                                                    <label for="Medium-luggage"
                                                        class="font-normal text-gray-900 flex space-x-1">
                                                        <span class="text-base md:text-lg">
                                                            {{ $findRidePage->luggage_option2->name }}
                                                        </span>
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                            fill="currentColor" class="bi bi-info-circle-fill text-black"
                                                            viewBox="0 0 16 16"
                                                            data-tippy-content="{{ $postRidePage->luggage_option2_tooltip }}">
                                                            <path
                                                                d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
                                                        </svg>
                                                    </label>
                                                    <input id="Medium-luggage" type="checkbox"
                                                        value="{{ $findRidePage->luggage_option2->features_setting_id }}"
                                                        {{ in_array($findRidePage->luggage_option2->features_setting_id, $luggages_check) ? 'checked' : '' }}
                                                        class="luggage w-4 h-4 ml-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                                                </div>
                                            @endisset
                                            @isset($findRidePage->luggage_option3)
                                                <div class="flex items-center justify-between p-3">
                                                    <label for="Large-luggage"
                                                        class="font-normal text-gray-900 flex space-x-1">
                                                        <span class="text-base md:text-lg">
                                                            {{ $findRidePage->luggage_option3->name }}
                                                        </span>
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                            fill="currentColor" class="bi bi-info-circle-fill text-black"
                                                            viewBox="0 0 16 16"
                                                            data-tippy-content="{{ $postRidePage->luggage_option3_tooltip }}">
                                                            <path
                                                                d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
                                                        </svg>
                                                    </label>
                                                    <input id="Large-luggage" type="checkbox"
                                                        value="{{ $findRidePage->luggage_option3->features_setting_id }}"
                                                        {{ in_array($findRidePage->luggage_option3->features_setting_id, $luggages_check) ? 'checked' : '' }}
                                                        class="luggage w-4 h-4 ml-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                                                </div>
                                            @endisset
                                            @isset($findRidePage->luggage_option4)
                                                <div class="flex items-center justify-between p-3">
                                                    <label for="multiple-luggage"
                                                        class="font-normal text-gray-900 flex space-x-1">
                                                        <span class="text-base md:text-lg">
                                                            {{ $findRidePage->luggage_option4->name }}
                                                        </span>
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                            fill="currentColor" class="bi bi-info-circle-fill text-black"
                                                            viewBox="0 0 16 16"
                                                            data-tippy-content="{{ $postRidePage->luggage_option4_tooltip }}">
                                                            <path
                                                                d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
                                                        </svg>



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
                                                        <label for="no-luggage"
                                                            class="font-normal text-gray-900 flex space-x-1">
                                                            <span class="text-base md:text-lg">
                                                                {{ $findRidePage->luggage_option5->name }}
                                                            </span>
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                                height="16" fill="currentColor"
                                                                class="bi bi-info-circle-fill text-black" viewBox="0 0 16 16"
                                                                data-tippy-content="{{ $postRidePage->luggage_option5_tooltip }}">
                                                                <path
                                                                    d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
                                                            </svg>
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
                                                $smoking_check = isset($_GET['smoking'])
                                                    ? explode(';', $_GET['smoking'])
                                                    : [];
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
                                                    <label for="indifferent-pet"
                                                        class="font-normal text-gray-900 flex space-x-1">
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

                                    <div class="flex items-center justify-between p-3 mt-2 mb-2">
                                        <label for="hide-full-rides"
                                            class="flex items-center gap-2 cursor-pointer select-none font-normal text-gray-900">
                                            <input type="checkbox" id="hide-full-rides"
                                                class="hide-full-rides w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500 focus:ring-2 cursor-pointer"
                                                {{ request('hide_full_rides') ? 'checked' : '' }}>
                                            <span
                                                class="text-base font-medium">{{ $siteText['hide_full_ride_text'] ?? 'Hide Full Rides' }}</span>
                                        </label>
                                    </div>

                                    <button class="w-auto text-white text-lg font-FuturaMdCnBT px-4 py-2 bg-blue-600 rounded"
                                        onclick="navigateToSearchRoute()">{{ $findRidePage->filter_search_btn_label }}</button>
                                    <button class="w-auto text-white text-lg font-FuturaMdCnBT px-4 py-2 bg-blue-600 rounded"
                                        onclick="resetFilters()">{{ $findRidePage->filter_close_btn_label }}</button>
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
                    <div class="flex items-end flex-col md:flex-row justify-between gap-4 md:gap-0 rounded-lg">
                        <div class="w-full md:w-[30%] relative">
                            <div class="relative">
                                <div class="absolute inset-y-0 start-0 flex items-center pl-2 pointer-events-none">
                                    @isset($findRidePage->from_field_icon)
                                        <img src="{{ asset('home_page_icons/' . $findRidePage->from_field_icon) }}"
                                            class="w-auto h-6" alt="">
                                    @else
                                        <img src="{{ asset('assets/search-bar-from.png') }}" class="w-auto h-6"
                                            alt="">
                                    @endisset
                                </div>
                                <input type="text" id="from_spot_0" value="{{ $request->from }}" autocomplete="off"
                                    class="bg-white rounded-md md:rounded-none pl-7 border-0 italic text-gray-900 focus:outline-none text-lg focus:border-sky-500 focus:ring-1 focus:ring-sky-500 block w-full p-2.5"
                                    @isset($findRidePage->search_section_from_placeholder)
                                        placeholder="{{ $findRidePage->search_section_from_placeholder }}"
                                    @endisset>
                            </div>
                            <div class="absolute hidden mt-1 z-10" id="fromInputError">
                                <div
                                    class="tooltip-error shadow-lg rounded p-2 bg-red-500 text-white text-sm lg:text-base">
                                </div>
                            </div>
                        </div>
                        <div class="w-full md:w-[5%] md:bg-gray-200 md:h-12 flex items-center justify-center">
                            <button type="button" onclick="swapLocations()">
                                @isset($findRidePage->swap_field_icon)
                                    <img src="{{ asset('home_page_icons/' . $findRidePage->swap_field_icon) }}"
                                        class="w-8 h-8 mx-auto" alt="">
                                @else
                                    <img src="{{ asset('assets/arrow.png') }}" class="w-8 h-8 mx-auto" alt="">
                                @endisset
                            </button>
                        </div>
                        <div class="w-full md:w-[30%] relative">
                            <div class="relative">
                                <div class="absolute inset-y-0 start-0 flex items-center pl-2 pointer-events-none">
                                    @isset($findRidePage->to_field_icon)
                                        <img src="{{ asset('home_page_icons/' . $findRidePage->to_field_icon) }}"
                                            class="w-4 h-6" alt="">
                                    @else
                                        <img src="{{ asset('images/new-21-search-bar-to.png') }}" class="w-4 h-6"
                                            alt="">
                                    @endisset
                                </div>
                                <input type="text" id="to_spot_0" value="{{ $request->to }}" autocomplete="off"
                                    class="bg-white pl-7 rounded-md md:rounded-none md:border-0 italic text-gray-900 focus:outline-none text-lg focus:border-sky-500 focus:ring-1 focus:ring-sky-500 block w-full p-2.5 border-x-0 border-t-0 border-gray-300"
                                    @isset($findRidePage->search_section_to_placeholder)
                                        placeholder="{{ $findRidePage->search_section_to_placeholder }}"
                                    @endisset>
                            </div>
                            <div class="absolute hidden mt-1 z-10" id="toInputError">
                                <div
                                    class="tooltip-error shadow-lg rounded p-2 bg-red-500 text-white text-sm lg:text-base">
                                </div>
                            </div>
                        </div>
                        <div class="w-48 mx-auto md:mx-0 md:w-[30%]">
                            <div class="relative">
                                <div class="absolute inset-y-0 start-0 flex items-center pl-2 pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-gray-500">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0 2.25h.008v.008H12v-.008zM9.75 15h.008v.008H9.75V15zm0 2.25h.008v.008H9.75v-.008zM7.5 15h.008v.008H7.5V15zm0 2.25h.008v.008H7.5v-.008zm6.75-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V15zm0 2.25h.008v.008h-.008v-.008zm2.25-4.5h.008v.008H16.5v-.008zm0 2.25h.008v.008H16.5V15z" />
                                    </svg>
                                </div>
                                <input type="text" id="dateInput" value="{{ $request->date }}" readonly
                                    class="bg-white rounded-md md:rounded-none px-7 sm:border-l italic border-gray-300 border-0 text-gray-900 text-lg focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 block w-full p-2.5 cursor-pointer"
                                    @isset($findRidePage->search_section_date_placeholder)
                                        placeholder="{{ $findRidePage->search_section_date_placeholder }}"
                                    @endisset>
                            </div>
                        </div>
                        <div class="w-24 mx-auto md:w-[5%] h-12 flex items-center justify-center">
                            <button type="button" onclick="navigateToSearchRoute()"
                                class="bg-blue-500 w-full h-full flex items-center justify-center text-white rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                                </svg>
                            </button>
                        </div>
                        {{-- error tooltip (kept for existing JS validation) --}}
                        <div id="fromToError" class="absolute hidden top-full left-1/2 -translate-x-1/2 mt-1 z-10">
                            <div class="tooltip-error">
                                {{ $findRidePage->search_section_required_error }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="my-6">

                    <div class="mt-6 rounded-lg">
                        @if ($rides && $rides->count() > 0)
                            <h1 class="text-center font-FuturaMdCnBT text-primary mb-4">
                                @isset($findRidePage->heading_ride_card_section)
                                    {{ $findRidePage->heading_ride_card_section }}
                                @endisset
                            </h1>
                        @endif
                        @if ($rides && $rides->count() > 0)
                            @foreach ($rides as $ride)
                                @php
                                    $bookedSeats = intval(
                                        $ride
                                            ->bookings()
                                            ->where('status', '<>', 3)
                                            ->where('status', '<>', 4)
                                            ->whereHas('passenger', function ($q) {
                                                $q->whereNull('deleted_at');
                                            })
                                            ->sum('seats'),
                                    );
                                    $seatsLeft = intval($ride->seats) - $bookedSeats;
                                    $isFull = $seatsLeft <= 0;
                                @endphp
                                <div class="ride-card-item relative even:bg-white odd:bg-gray-200 space-y-4 rounded-lg"
                                    data-ride-full="{{ $isFull ? '1' : '0' }}">
                                    <div class="absolute right-4 top-8">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor"
                                            class="w-6 h-6 -mt-4 cursor-pointer ride-remove-btn"
                                            data-ride-id="{{ $ride->id }}">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    @if (auth()->user())
                                        @php
                                            $user_id = auth()->user()->id;
                                            $totalAverage = $ride->getAverageRating(1, 2, $user_id);
                                        @endphp
                                    @endif
                                    @php
                                        $rideDetail = $ride->rideDetail->first();
                                    @endphp
                                    @if ($rideDetail)
                                        <a @if ($ride->status === '2') href=""
                                        @elseif (auth()->user() &&
                                                in_array($findRidePage->ride_features_option1->features_setting_id ?? null, explode('=', $ride->features)) &&
                                                (auth()->user()->gender !== 'female' ||
                                                    ($pinkRideSetting->verfiy_phone_passenger == 1
                                                        ? !auth()->user()->phoneNumbers->contains('verified', 1)
                                                        : false)))
                                            href="javascript:void(0);" onclick="toggleModal1('modal-id1', 'Only female passengers @if ($pinkRideSetting->verfiy_phone_passenger == 1) with verified number @endif
                                        can select this ride')" @elseif (isset($findRidePage->ride_features_option18) &&
                                                auth()->user() &&
                                                in_array($findRidePage->ride_features_option18->features_setting_id, explode('=', $ride->features)) &&
                                                !auth()->user()->phoneNumbers->contains('verified', 1))
                                            href="javascript:void(0);"
                                            onclick="toggleModal1('modal-id1', 'This driver accepts only phone-verified passengers.')"
                                        @elseif (auth()->user() &&
                                                in_array($findRidePage->ride_features_option16->features_setting_id ?? null, explode('=', $ride->features)))
                                            @if ($totalAverage < 1) href="javascript:void(0);" onclick="toggleModal1('modal-id1', 'Driver only want passengers with reviews')"
                                            @else
                                                href="{{ route('ride_detail', ['lang' => $selectedLanguage->abbreviation, 'departure' => $rideDetail->departure, 'destination' => $rideDetail->destination, 'id' => $ride->id]) }}" @endif
                                        @elseif (auth()->user() &&
                                                in_array($findRidePage->ride_features_option15->features_setting_id ?? null, explode('=', $ride->features)))
                                            @if ($totalAverage < 3) href="javascript:void(0);" onclick="toggleModal1('modal-id1', 'Driver want only passengers with-3 star reviews above')"
                                            @else
                                                href="{{ route('ride_detail', ['lang' => $selectedLanguage->abbreviation, 'departure' => $rideDetail->departure, 'destination' => $rideDetail->destination, 'id' => $ride->id]) }}" @endif
                                        @elseif (auth()->user() &&
                                                in_array($findRidePage->ride_features_option14->features_setting_id ?? null, explode('=', $ride->features)))
                                            @if ($totalAverage < 4) href="javascript:void(0);" onclick="toggleModal1('modal-id1', 'Driver want only passengers with-4 star reviews above')"
                                            @else
                                                href="{{ route('ride_detail', ['lang' => $selectedLanguage->abbreviation, 'departure' => $rideDetail->departure, 'destination' => $rideDetail->destination, 'id' => $ride->id]) }}" @endif
                                        @elseif (auth()->user() &&
                                                in_array($findRidePage->ride_features_option13->features_setting_id ?? null, explode('=', $ride->features)))
                                            @if ($totalAverage < 4.5) href="javascript:void(0);" onclick="toggleModal1('modal-id1', 'Driver want only passengers with-5 star reviews above')"
                                            @else
                                                href="{{ route('ride_detail', ['lang' => $selectedLanguage->abbreviation, 'departure' => $rideDetail->departure, 'destination' => $rideDetail->destination, 'id' => $ride->id]) }}" @endif
                                        @else
                                            href="{{ route('ride_detail', ['lang' => $selectedLanguage->abbreviation, 'departure' => $rideDetail->departure, 'destination' => $rideDetail->destination, 'id' => $ride->id]) }}"
                                            @endif>
                                            @php
                                                $isPink =
                                                    isset($findRidePage->ride_features_option1) &&
                                                    in_array(
                                                        $findRidePage->ride_features_option1->features_setting_id,
                                                        explode('=', $ride->features),
                                                    );
                                                $isExtraCare =
                                                    isset($findRidePage->ride_features_option2) &&
                                                    in_array(
                                                        $findRidePage->ride_features_option2->features_setting_id,
                                                        explode('=', $ride->features),
                                                    );
                                                $isPinkAndExtraCare = $isPink && $isExtraCare;
                                            @endphp
                                            @if ($isPinkAndExtraCare)
                                                {{-- Double frame: green outside, pink inside, ~1–2mm gap --}}
                                                <div
                                                    class="rounded-lg border-[3px] border-solid border-green-500 p-[2px] shadow-3xl">
                                                    <div class="rounded-[6px] border-[3px] border-solid border-pink-500"
                                                        id="ride-{{ $ride->id }}">
                                                    @else
                                                        <div class="rounded-lg shadow-3xl border-[3px] border-solid @if ($ride->status === '2') border-red-500 @elseif($isPink) border-pink-500 @elseif($isExtraCare) border-green-500 @else border-gray-100 @endif"
                                                            id="ride-{{ $ride->id }}">
                                            @endif
                                            <div
                                                class="flex flex-col md:flex-row items-start md:items-center justify-between pb-0 p-4">
                                                <div class="flex items-center gap-2">
                                                    @php
                                                        $displayDt = ($rideDetail->date ?? $ride->date) . ' ' . ($rideDetail->time ?? $ride->time ?? '00:00');
                                                        $departureDateTime = formatDepartureDateTime(
                                                            $displayDt,
                                                            $selectedLanguage ?? null,
                                                            $rideDetailPage ?? null,
                                                        );
                                                        $departureDateLabel = $departureDateTime['dateLabel'];
                                                        $departureTimeLabel = $departureDateTime['timeLabel'];
                                                    @endphp
                                                    <p class="flex items-center space-x-2 font-semibold">
                                                        {{ $departureDateLabel }}
                                                        {{ $rideDetailPage->at_label }}
                                                        {{ $departureTimeLabel ?? 'N/A' }}
                                                    </p>

                                                    @if (in_array($findRidePage->ride_features_option1->features_setting_id ?? null, explode('=', $ride->features)))
                                                        <span class="ml-2 inline-block cursor-help"
                                                            data-tippy-content="{{ $postRidePage->features_option1_tooltip ?? '' }}">
                                                            <img class="w-12 h-12"
                                                                src="{{ asset('home_page_icons/' . ($findRidePage->ride_features_option1->icon ?? '')) }}"
                                                                alt="">
                                                        </span>
                                                    @endif
                                                    @if (in_array($findRidePage->ride_features_option2->features_setting_id ?? null, explode('=', $ride->features)))
                                                        <span class="ml-2 inline-block cursor-help"
                                                            data-tippy-content="{{ $postRidePage->features_option2_tooltip ?? '' }}">
                                                            <img class="w-12 h-12"
                                                                src="{{ asset('home_page_icons/' . ($findRidePage->ride_features_option2->icon ?? '')) }}"
                                                                alt="">
                                                        </span>
                                                    @endif
                                                </div>

                                                <div class="pr-8">
                                                    <div class="pr-8">
                                                        <p class="font-medium">
                                                            {{ str_replace(':count', $ride->seats, $findRidePage->total_seats_label ?? 'Total :count seats') }}
                                                        </p>
                                                    </div>
                                                    {{-- {{ dd($postRidePage->cancellation_policy_label1->features_setting_id,$ride->booking_type) }} --}}
                                                    <p class="text-xl font-semibold text-primary">
                                                    <div class="flex items-center gap-2">
                                                        @if (isset($firm_cancellation_discount) &&
                                                                $firm_cancellation_discount != '' &&
                                                                $ride->booking_type == $postRidePage->cancellation_policy_label2?->features_setting_id)
                                                            <span class="line-through">
                                                                ${{ number_format(floatval($rideDetail->price), 2) }}
                                                            </span>
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                viewBox="0 0 24 24" stroke-width="1.5"
                                                                stroke="currentColor" class="h-6">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
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
                                                        @if (isset($firm_cancellation_discount) &&
                                                                $firm_cancellation_discount != '' &&
                                                                $ride->booking_type == $postRidePage->cancellation_policy_label2?->features_setting_id)

                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                                height="16" fill="currentColor"
                                                                class="bi bi-info-circle-fill text-black"
                                                                viewBox="0 0 16 16"
                                                                data-tippy-content="{!! nl2br($findRidePage->firm_cancellation_tooltip) ??
                                                                    'This ride has the Firm cancellation policy, so its booking price is reduced by 10%' !!}">
                                                                <path
                                                                    d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
                                                            </svg>
                                                        @endif

                                                    </div>
                                                    </p>
                                                </div>
                                                {{-- <div class="pr-8">
                                                    <div class="pr-8">
                                                        <p class="font-medium">
                                                            {{ str_replace(':count', $ride->seats, $findRidePage->total_seats_label ?? 'Total :count seats') }}</p>
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
                                                                <p class="font-bold text-xl text-black">
                                                                    @isset($findRidePage->card_section_from_label)
                                                                        {{ $findRidePage->card_section_from_label }}
                                                                    @endisset
                                                                </p>
                                                                <div class="flex gap-2">
                                                                    <h3
                                                                        class="text-primary font-FuturaMdCnBT text-xl md:text-2xl md:mb-4">
                                                                        {{ $rideDetail->departure }}.
                                                                    </h3>
                                                                    @php $segmentPickup = $rideDetail->pickup ?? $ride->pickup; @endphp
                                                                    @if (!empty($segmentPickup))
                                                                        <p class="text-sm mt-2">
                                                                            {{ $findRidePage->pickup_at_label ?? 'Pick-up at' }}:
                                                                            {{ $segmentPickup }}
                                                                        </p>
                                                                    @endif
                                                                </div>
                                                            </div>
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
                                                            <div class="ml-12 md:ml-20">
                                                                <p class="font-bold text-xl text-black">
                                                                    @isset($findRidePage->card_section_to_label)
                                                                        {{ $findRidePage->card_section_to_label }}
                                                                    @endisset
                                                                </p>
                                                                <div class="flex gap-2">
                                                                    <h3
                                                                        class="text-primary font-FuturaMdCnBT text-xl md:text-2xl md:mb-4">
                                                                        {{ $rideDetail->destination }}.
                                                                    </h3>
                                                                    @php $segmentDropoff = $rideDetail->dropoff ?? $ride->dropoff; @endphp
                                                                    @if (!empty($segmentDropoff))
                                                                        <p class="text-sm mt-2">
                                                                            {{ $findRidePage->dropoff_at_label ?? 'Drop-off at' }}:
                                                                            {{ $segmentDropoff }}
                                                                        </p>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mt-4">
                                                    <p class="text-sm md:text-base whitespace-nowrap font-medium">
                                                        {{ intval($ride->seats) -intval($ride->bookings()->where('status', '<>', 3)->where('status', '<>', 4)->whereHas('passenger', function ($query) {$query->whereNull('deleted_at');})->sum('seats')) }}
                                                        @isset($findRidePage->card_section_seats_left)
                                                            {{ $findRidePage->card_section_seats_left }}
                                                        @endisset
                                                    </p>
                                                    <div class="my-4">
                                                        @if ($ride->booking_method == ($postRidePage->booking_option1->features_setting_id ?? null))
                                                            <a href="{{ route('ride_detail', ['lang' => $selectedLanguage->abbreviation, 'departure' => $rideDetail->departure, 'destination' => $rideDetail->destination, 'id' => $ride->id]) }}"
                                                                class="bg-greenXS hover:bg-greenXS text-white text-base md:text-lg rounded font-FuturaMdCnBT hover:font-FuturaMdCnBT px-5 py-2 border border-greenXS hover:border-greenXS hover:text-white text-center focus:bg-greenXS focus:text-white active:text-white active:bg-greenXS flex items-center gap-2"
                                                                data-tippy-content="{{ $postRidePage->booking_option1_tooltip }}"><img
                                                                    class="w-8 h-8"
                                                                    src="{{ asset('home_page_icons/' . $postRidePage->booking_option1->icon) }}"
                                                                    alt="">
                                                                {{ $siteText['instant_booking_btn_text'] ?? 'Instant booking' }}</a>
                                                        @elseif ($ride->booking_method == ($postRidePage->booking_option2->features_setting_id ?? null))
                                                            <a href="{{ route('ride_detail', ['lang' => $selectedLanguage->abbreviation, 'departure' => $rideDetail->departure, 'destination' => $rideDetail->destination, 'id' => $ride->id]) }}"
                                                                class="bg-greenXS hover:bg-greenXS text-white text-base md:text-lg rounded font-FuturaMdCnBT hover:font-FuturaMdCnBT px-5 py-2 border border-greenXS hover:border-greenXS hover:text-white text-center focus:bg-greenXS focus:text-white active:text-white active:bg-greenXS flex items-center gap-2"
                                                                data-tippy-content="{{ $postRidePage->booking_option2_tooltip }}"><img
                                                                    class="w-8 h-8"
                                                                    src="{{ asset('home_page_icons/' . $postRidePage->booking_option2->icon) }}"
                                                                    alt="">{{ $siteText['request_to_book_btn_text'] ?? 'Request to book' }}</a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div
                                                class="border-t border-gray-300 grid grid-cols-4 divide-x divide-gray-300">
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
                                                <div
                                                    class="col-span-4 p-4 flex justify-start items-center no-scrollbar space-x-2 md:space-x-4">
                                                    @unless (old('skip_vehicle', $ride->skip_vehicle) == '0')
                                                        @if ($ride->remove_car_image == 0)
                                                            <div class="flex-none w-12 h-12 bg-gray-100 border rounded-full">
                                                                <img class="w-full h-full object-cover rounded-full"
                                                                    src="{{ $ride->car_image }}" alt="">
                                                            </div>
                                                        @endif
                                                    @endunless

                                                    <div class="flex flex-nowrap items-center gap-1 md:gap-2">
                                                        @if ($ride->payment_method == ($findRidePage->payment_methods_option2->features_setting_id ?? null))
                                                            <img class="w-8 h-8"
                                                                src="{{ asset('home_page_icons/' . $findRidePage->payment_methods_option2->icon) }}"
                                                                alt=""
                                                                data-tippy-content="{{ $postRidePage->payment_methods_option1_tooltip }}">
                                                        @elseif ($ride->payment_method == ($findRidePage->payment_methods_option3->features_setting_id ?? null))
                                                            <img class="w-8 h-8"
                                                                src="{{ asset('home_page_icons/' . $findRidePage->payment_methods_option3->icon) }}"
                                                                alt=""
                                                                data-tippy-content="{{ $postRidePage->payment_methods_option2_tooltip }}">
                                                        @elseif ($ride->payment_method == ($findRidePage->payment_methods_option4->features_setting_id ?? null))
                                                            <img class="w-8 h-8"
                                                                src="{{ asset('home_page_icons/' . $findRidePage->payment_methods_option4->icon) }}"
                                                                alt=""
                                                                data-tippy-content="{{ $postRidePage->payment_methods_option3_tooltip }}">
                                                        @endif
                                                        @if ($ride->smoke == ($findRidePage->smoking_option2->features_setting_id ?? null))
                                                            <img class="w-8 h-8"
                                                                src="{{ asset('home_page_icons/' . $findRidePage->smoking_option2->icon) }}"
                                                                alt=""
                                                                data-tippy-content="{{ $postRidePage->smoking_option2_tooltip }}">
                                                        @endif
                                                        @if ($ride->animal_friendly == ($findRidePage->pets_allowed_option2->features_setting_id ?? null))
                                                            <img class="w-8 h-8"
                                                                src="{{ asset('home_page_icons/' . $findRidePage->pets_allowed_option2->icon) }}"
                                                                alt=""
                                                                data-tippy-content="{{ $postRidePage->animals_option2_tooltip }}">
                                                        @elseif ($ride->animal_friendly == ($findRidePage->pets_allowed_option3->features_setting_id ?? null))
                                                            <img class="w-8 h-8"
                                                                src="{{ asset('home_page_icons/' . $findRidePage->pets_allowed_option3->icon) }}"
                                                                alt=""
                                                                data-tippy-content="{{ $postRidePage->animals_option3_tooltip }}">
                                                        @endif
                                                        @if ($ride->luggage == ($findRidePage->luggage_option1->features_setting_id ?? null))
                                                            <img class="w-8 h-8"
                                                                src="{{ asset('home_page_icons/' . $findRidePage->luggage_option1->icon) }}"
                                                                alt=""
                                                                data-tippy-content="{{ $postRidePage->luggage_option1_tooltip }}">
                                                        @elseif ($ride->luggage == ($findRidePage->luggage_option2->features_setting_id ?? null))
                                                            <img class="w-8 h-8"
                                                                src="{{ asset('home_page_icons/' . $findRidePage->luggage_option2->icon) }}"
                                                                alt=""
                                                                data-tippy-content="{{ $postRidePage->luggage_option2_tooltip }}">
                                                        @elseif ($ride->luggage == ($findRidePage->luggage_option3->features_setting_id ?? null))
                                                            <img class="w-8 h-8"
                                                                src="{{ asset('home_page_icons/' . $findRidePage->luggage_option3->icon) }}"
                                                                alt=""
                                                                data-tippy-content="{{ $postRidePage->luggage_option3_tooltip }}">
                                                        @elseif ($ride->luggage == ($findRidePage->luggage_option4->features_setting_id ?? null))
                                                            <img class="w-8 h-8"
                                                                src="{{ asset('home_page_icons/' . $findRidePage->luggage_option4->icon) }}"
                                                                alt=""
                                                                data-tippy-content="{{ $postRidePage->luggage_option4_tooltip }}">
                                                        @elseif ($ride->luggage == ($findRidePage->luggage_option5->features_setting_id ?? null))
                                                            <img class="w-8 h-8"
                                                                src="{{ asset('home_page_icons/' . $findRidePage->luggage_option5->icon) }}"
                                                                alt=""
                                                                data-tippy-content="{{ $postRidePage->luggage_option5_tooltip }}">
                                                        @endif
                                                        {{-- @php
                                                            dd($ride->features);
                                                        @endphp --}}
                                                        @include('partials.ride_feature_icons', [
                                                            'rideFeatures' => $ride->features,
                                                            'iconClass' => 'w-8 h-8 cursor-help',
                                                        ])
                                                    </div>
                                                </div>
                                            </div>
                                            <div
                                                class="border-t border-gray-300 grid grid-cols-1 divide-x divide-gray-300">
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
                                                                        {{ $ride->driver?->first_name }}
                                                                        {{ $ride->driver?->last_name }}
                                                                    @else
                                                                        {{ $ride->driver?->first_name }}
                                                                    @endif
                                                                    {{-- @if ($ride->driver?->gender && $ride->driver?->gender !== 'Prefer not to say')
                                                                        ({{ strtoupper(substr($ride->driver?->gender, 0, 1)) }})
                                                                    @endif --}}
                                                                </span>
                                                            </p>
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
                                                                {{ $ride->driver
                                                                    ?->rides()->where('status', '!=', 2)->where(function ($query) {
                                                                        $query->whereDate('rides.date', '<', now()->toDateString())->orWhere(function ($query) {
                                                                            $query->whereDate('rides.date', '=', now()->toDateString())->whereTime('rides.time', '<=', now()->toTimeString());
                                                                        });
                                                                    })->get()->flatMap(function ($ride) {
                                                                        return $ride->bookings()->pluck('seats');
                                                                    })->sum() }}
                                                                @isset($findRidePage->card_section_driven)
                                                                    {{ $findRidePage->card_section_driven }}
                                                                @endisset
                                                            </p>

                                                        </div>
                                                    </div>
                                                    <div class="flex items-center gap-2">
                                                        <span class="font-semibold text-gray-800">
                                                            @if ($ride->getDriverHasRatings())
                                                                {{ number_format($ride->getDriverAverageRating(), 1) }}
                                                            @else
                                                                {{ $rideDetailPage->no_reviews_label ?? 'No Reviews' }}
                                                            @endif
                                                        </span>

                                                        @if ($ride->getDriverHasRatings())
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

                                            </div>
                                </div>
                                @if ($isPinkAndExtraCare)
                    </div>
                    @endif
                    </a>
                    @endif
                </div>
                @endforeach
                {{ $rides->appends(request()->except('page'))->links() }}
            @elseif ($rides && $rides->count() == 0 && $request->from && $request->to)
                <div class="text-center">
                    <h1 class="font-FuturaMdCnBT">
                        @isset($findRidePage->search_result_no_found_message)
                            {{ $findRidePage->search_result_no_found_message }}
                        @else
                            {{ $siteText['no_ride_for_location_text'] ?? 'No ride for this location exist' }}
                        @endisset
                    </h1>
                </div>
                @endif
            </div>
            @if ($recentSearches->count() > 0)
                <h1 class="font-FuturaMdCnBT text-primary text-3xl mt-12">
                    @isset($findRidePage->search_section_recent_searches)
                        {{ $findRidePage->search_section_recent_searches }}
                    @endisset
                </h1>
                <div class="space-y-4 mt-4">
                    @foreach ($recentSearches as $index => $recentSearch)
                        @php
                            $colors = [
                                'bg-blue-50',
                                'bg-green-50',
                                'bg-yellow-50',
                                'bg-purple-50',
                                'bg-pink-50',
                                'bg-indigo-50',
                                'bg-cyan-50',
                                'bg-teal-50',
                            ];
                            $colorClass = $colors[$index % count($colors)];
                        @endphp
                        <div class="{{ $colorClass }} rounded-lg shadow-3xl border border-solid border-gray-100 cursor-pointer hover:shadow-xl transition-shadow duration-200"
                            onclick="SearchRoute('{{ $recentSearch->from }}', '{{ $recentSearch->to }}')">
                            <div class="flex flex-col sm:flex-col md:flex-row justify-between px-4">
                                <div class="w-full">
                                    <div class="relative mt-5 text-left">
                                        <div class="flex items-center relative">
                                            <div
                                                class="border-r-2 border-black border-solid absolute h-full left-3 md:left-6 top-2 z-10">
                                                <span
                                                    class="bg-primary rounded-full w-7 h-7 -top-[2px] -ml-[13px] absolute flex justify-center items-center">
                                                    <img class="w-5 h-5 object-contain"
                                                        src="{{ asset('./images/new-21-search-bar-from.png') }}"
                                                        alt="">
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
                                            <div
                                                class="border-r-2 border-black border-solid absolute h-0 left-3 md:left-5 top-2 z-10">
                                                <span
                                                    class="bg-gray-200 rounded-full w-7 h-7 -top-[6px] -ml-[12px] md:-ml-[9px] absolute flex justify-center items-center">
                                                    <img class="w-5 h-5 object-contain"
                                                        src="{{ asset('./images/new-21-search-bar-to.png') }}"
                                                        alt="">
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
        <div class="hidden relative z-50" id="hide-ride-confirm-modal" aria-labelledby="modal-title" role="dialog"
            aria-modal="true">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity modal-backdrop"></div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full justify-center p-4 text-center items-center sm:p-0">
                    <div
                        class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border1">
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <div class="flex justify-end">
                                <button type="button" onclick="closeHideRideModal()"
                                    class="p-1 rounded-full hover:bg-gray-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                            <div class="text-center mt-2">
                                <h3 class="text-2xl font-FuturaMdCnBT leading-6 text-gray-900" id="modal-title">
                                    {{ $findRidePage->hide_ride_popup_heading ?? 'Confirm Hide Ride' }}</h3>
                                <div class="w-full mt-4">
                                    <p class="can-exp-p text-center text-black">
                                        {{ $findRidePage->hide_ride_popup_text ?? 'Do you want this ride to be hidden from your search results? You will not be able to see it anymore.' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="px-4 pb-6 pt-4 flex items-center space-x-2 sm:space-x-4 sm:px-6 justify-center">
                            <button type="button" onclick="closeHideRideModal()"
                                class="inline-flex justify-center w-42 rounded bg-red-500 px-3 py-2 font-FuturaMdCnBT text-lg text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-red-400 sm:ml-3">{{ $findRidePage->hide_ride_popup_take_me_back_button ?? 'No, take me back' }}</button>
                            <button type="button" id="confirm-hide-ride"
                                class="inline-flex justify-center w-auto rounded bg-primary px-3 py-2 font-FuturaMdCnBT text-lg text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-primary/80 sm:ml-3">{{ $findRidePage->hide_ride_popup_confirm_button ?? 'Yes, hide it' }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="hidden overflow-x-hidden overflow-y-auto fixed inset-0 z-50 outline-none focus:outline-none justify-center items-center"
            id="modal-id1">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="toggleModal1('modal-id1')">
            </div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto  flex items-center justify-center">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                    <!--content-->
                    <div
                        class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg w-full modal-border">
                        <button type="button" onclick="toggleModal1('modal-id1')"
                            class="absolute top-3 right-3 text-gray-400 hover:text-gray-500">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                        <!--body-->
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start justify-center">
                                <!-- <div
                                                            class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full sm:mx-0 sm:h-10 sm:w-10 bg-[#c75b5b]">
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
                        <div class="px-4 pb-6 pt-4  sm:flex sm:flex-row-reverse sm:px-6 justify-center">
                            <button
                                class="inline-flex w-full justinline-flex justify-center rounded bg-red-600 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-red-400 sm:ml-3 sm:w-24"
                                type="button" onclick="toggleModal1('modal-id1')">
                                {{ $siteText['close_btn_text'] ?? 'Close' }}
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
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                    onclick="toggleModal1('modal-id2')"></div>
                <!--content-->
                <div
                    class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl modal-border">
                    <button type="button" onclick="toggleModal1('modal-id2')"
                        class="absolute top-3 right-3 text-gray-400 hover:text-gray-500">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
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
                    <div class="px-4 pb-6 pt-4  sm:flex sm:flex-row-reverse sm:px-6 justify-center">
                        <button
                            class="bg-greenXS hover:bg-greenXS text-white text-base md:text-lg rounded font-FuturaMdCnBT hover:font-FuturaMdCnBT px-5 py-2 border border-greenXS hover:border-greenXS hover:text-white text-center focus:bg-greenXS focus:text-white active:text-white active:bg-greenXS flex items-center gap-2"
                            type="button" onclick="toggleModal1('modal-id2')">
                            {{ $siteText['close_btn_text'] ?? 'Close' }}
                        </button>
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
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <script>
        // Global variables
        let removedRideIds = [];
        let currentRideIdToHide = null;

        // Google Places (from/to) – used when API loads
        let selectedFromPlace = null;
        let selectedToPlace = null;
        let geocoder = null;
        let fromAutocomplete = null;
        let toAutocomplete = null;
        let isSettingPlaceValue = false;
        let isSelectingFromDropdown = false;
        var errorFromRequired = @json(__('validation.custom.from.required'));
        var errorToRequired = @json(__('validation.custom.to.required'));
        var errorCityMissing = @json(__('validation.custom.city_not_in_record.message'));

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
                        const visibleRides = document.querySelectorAll(
                            '.p-4.bg-white:not([style*="display: none"])');
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
            const fromValue = document.getElementById('from_spot_0').value;
            const toValue = document.getElementById('to_spot_0').value;
            document.getElementById('from_spot_0').value = toValue;
            document.getElementById('to_spot_0').value = fromValue;
            const tempPlace = selectedFromPlace;
            selectedFromPlace = selectedToPlace;
            selectedToPlace = tempPlace;
        }

        function debounce(func, delay) {
            let timer;
            return function() {
                const args = arguments;
                clearTimeout(timer);
                timer = setTimeout(() => {
                    func.apply(this, args);
                }, delay);
            };
        }

        function fetchCities(searchTerm, fieldId, fieldIndex) {
            const container = document.getElementById(fieldId + '_suggestions' + fieldIndex);
            if (!container) return;
            if (searchTerm.length < 2) {
                container.innerHTML = '';
                return;
            }
            const body = new URLSearchParams({
                search: searchTerm,
                _token: '{{ csrf_token() }}'
            });
            fetch('{{ url('get-cities-by-state') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: body.toString()
                })
                .then(r => {
                    if (!r.ok) throw new Error('Request failed: ' + r.status);
                    return r.json();
                })
                .then(result => {
                    container.innerHTML = '';
                    const cities = result.cities != null ? (Array.isArray(result.cities) ? result.cities : Object
                        .values(result.cities)) : [];
                    cities.forEach(value => {
                        const stateAbrv = value.state && value.state.abrv ? value.state.abrv : '';
                        const countryName = value.state && value.state.country && value.state.country.name ?
                            value.state.country.name : '';
                        const displayText = [value.name, stateAbrv, countryName].filter(Boolean).join(', ');
                        const div = document.createElement('div');
                        div.className = 'suggestion-item p-2 hover:bg-gray-200 cursor-pointer';
                        div.textContent = displayText;
                        div.addEventListener('click', function() {
                            const input = document.getElementById(fieldId + '_' + fieldIndex);
                            if (input) input.value = displayText;
                            container.innerHTML = '';
                        });
                        container.appendChild(div);
                    });
                })
                .catch(err => console.error('fetchCities error', err));
        }

        const debouncedFromFetch = debounce(function() {
            const searchTerm = (document.getElementById('from_spot_0') || {}).value || '';
            fetchCities(searchTerm, 'from_spot', '0');
        }, 500);
        const debouncedToFetch = debounce(function() {
            const searchTerm = (document.getElementById('to_spot_0') || {}).value || '';
            fetchCities(searchTerm, 'to_spot', '0');
        }, 500);

        function fromInput(index) {
            const el = document.getElementById('from_spot_' + index);
            if (el && el.value.length < 2) {
                const container = document.getElementById('from_spot_suggestions' + index);
                if (container) container.innerHTML = '';
            }
            debouncedFromFetch();
        }

        function toInput(index) {
            const el = document.getElementById('to_spot_' + index);
            if (el && el.value.length < 2) {
                const container = document.getElementById('to_spot_suggestions' + index);
                if (container) container.innerHTML = '';
            }
            debouncedToFetch();
        }

        document.addEventListener('click', function(e) {
            const fromSuggest = document.getElementById('from_spot_suggestions0');
            const toSuggest = document.getElementById('to_spot_suggestions0');
            if (fromSuggest && !fromSuggest.contains(e.target) && e.target.id !== 'from_spot_0') fromSuggest.innerHTML = '';
            if (toSuggest && !toSuggest.contains(e.target) && e.target.id !== 'to_spot_0') toSuggest.innerHTML = '';
        });

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



        // Handle Enter key on input fields
        ['from_spot_0', 'to_spot_0', 'dateInput', 'driverName', 'keyword'].forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                element.addEventListener('keypress', function(event) {
                    if (event.key === 'Enter') {
                        event.preventDefault();
                        navigateToSearchRoute();
                    }
                });
            }
        });

        // Initialize selected arrays from URL parameters
        @php
            $getFeatures = isset($_GET['features']) ? $_GET['features'] : '';
            $getLuggages = isset($_GET['luggage']) ? $_GET['luggage'] : '';
            $getSmoking = isset($_GET['smoking']) ? $_GET['smoking'] : '';
            $getPets = isset($_GET['pets']) ? $_GET['pets'] : '';
        @endphp

        const parseSelectedArray = (value, filterEmpty = false) => {
            if (!value) return [];
            const items = value.split(";");
            return filterEmpty ? items.filter(f => f.trim() !== "") : items;
        };

        let selectedFeatures = parseSelectedArray('{{ $getFeatures }}', true);
        let selectedLuggages = parseSelectedArray('{{ $getLuggages }}');
        let selectedSmoking = parseSelectedArray('{{ $getSmoking }}');
        let selectedPets = parseSelectedArray('{{ $getPets }}');

        // Generic checkbox handler
        const handleCheckboxChange = (checkbox, selectedArray, allowDuplicates = false) => {
            const value = checkbox.value;
            if (checkbox.checked) {
                if (allowDuplicates || !selectedArray.includes(value)) {
                    selectedArray.push(value);
                }
            } else {
                const index = selectedArray.indexOf(value);
                if (index > -1) {
                    selectedArray.splice(index, 1);
                }
            }
        };

        // Attach event listeners to all checkbox types
        const checkboxConfigs = [{
                selector: '.ride-preferences',
                array: selectedFeatures,
                allowDuplicates: false
            },
            {
                selector: '.luggage',
                array: selectedLuggages,
                allowDuplicates: true
            },
            {
                selector: '.smoking',
                array: selectedSmoking,
                allowDuplicates: true
            },
            {
                selector: '.pet',
                array: selectedPets,
                allowDuplicates: true
            }
        ];

        checkboxConfigs.forEach(config => {
            document.querySelectorAll(`input[type="checkbox"]${config.selector}`).forEach(checkbox => {
                checkbox.addEventListener('change', () => {
                    handleCheckboxChange(checkbox, config.array, config.allowDuplicates);
                });
            });
        });

        function SearchRoute(SearchfromValue, SearchtoValue) {
            const hideFullRides = document.getElementById('hide-full-rides')?.checked ? '1' : '';
            // Construct the URL with query parameters
            let searchUrl =
                `{{ route('search_ride', ['lang' => $selectedLanguage->abbreviation]) }}?from=${SearchfromValue}&to=${SearchtoValue}&date=&driver_age=&driver_rating=&driver_phone=&driver_name=&keyword=&passenger_rating=&payment_method=&vehicle_type=&features=&luggage=&smoking=&pets=&hide_full_rides=${hideFullRides}`;

            // Navigate to the constructed URL
            window.location.href = searchUrl;
        }

        function navigateToSearchRoute() {
            localStorage.setItem('removedRideIds', JSON.stringify([]));

            const fromValue = document.getElementById('from_spot_0').value.trim();
            const toValue = document.getElementById('to_spot_0').value.trim();
            const fromInputError = document.getElementById('fromInputError');
            const toInputError = document.getElementById('toInputError');

            let isValid = true;
            if (fromValue === '' || !selectedFromPlace || fromValue !== selectedFromPlace.value) {
                if (fromInputError) {
                    const tooltipError = fromInputError.querySelector('.tooltip-error');
                    if (tooltipError) tooltipError.textContent = fromValue === '' ? errorFromRequired : errorCityMissing;
                    fromInputError.classList.remove('hidden');
                }
                isValid = false;
            }
            if (toValue === '' || !selectedToPlace || toValue !== selectedToPlace.value) {
                if (toInputError) {
                    const tooltipError = toInputError.querySelector('.tooltip-error');
                    if (tooltipError) tooltipError.textContent = toValue === '' ? errorToRequired : errorCityMissing;
                    toInputError.classList.remove('hidden');
                }
                isValid = false;
            }
            if (!isValid) return;

            if (fromInputError) {
                fromInputError.classList.add('hidden');
                const tooltipError = fromInputError.querySelector('.tooltip-error');
                if (tooltipError) tooltipError.textContent = '';
            }
            if (toInputError) {
                toInputError.classList.add('hidden');
                const tooltipError = toInputError.querySelector('.tooltip-error');
                if (tooltipError) tooltipError.textContent = '';
            }

            const formData = {
                from: fromValue,
                to: toValue,
                date: document.getElementById('dateInput').value,
                driver_age: document.getElementById('driverAge').value,
                driver_rating: document.getElementById('driverRating').value,
                driver_phone: document.getElementById('driverPhone').checked ? 1 : 0,
                driver_name: document.getElementById('driverName').value,
                keyword: document.getElementById('keyword').value,
                passenger_rating: document.getElementById('passengerRating').value,
                payment_method: document.getElementById('payment-method').value,
                vehicle_type: document.getElementById('VehicleType').value,
                features: selectedFeatures.join(';'),
                luggage: selectedLuggages.join(';'),
                smoking: selectedSmoking.join(';'),
                pets: selectedPets.join(';'),
                hide_full_rides: document.getElementById('hide-full-rides')?.checked ? '1' : ''
            };

            const baseUrl = '{{ route('search_ride', ['lang' => $selectedLanguage->abbreviation]) }}';
            const queryParams = Object.entries(formData)
                .map(([key, value]) => `${key}=${encodeURIComponent(value)}`)
                .join('&');

            window.location.href = `${baseUrl}?${queryParams}`;
        }

        function navigateToSearchRoute1() {
            localStorage.setItem('removedRideIds', JSON.stringify([]));

            const fromValue = document.getElementById('from_spot_0').value.trim();
            const toValue = document.getElementById('to_spot_0').value.trim();
            const fromInputError = document.getElementById('fromInputError');
            const toInputError = document.getElementById('toInputError');

            let isValid = true;
            if (fromValue === '' || !selectedFromPlace || fromValue !== selectedFromPlace.value) {
                isValid = false;
            }
            if (toValue === '' || !selectedToPlace || toValue !== selectedToPlace.value) {
                isValid = false;
            }
            if (!isValid) return;

            if (fromInputError) {
                fromInputError.classList.add('hidden');
                const tooltipError = fromInputError.querySelector('.tooltip-error');
                if (tooltipError) tooltipError.textContent = '';
            }
            if (toInputError) {
                toInputError.classList.add('hidden');
                const tooltipError = toInputError.querySelector('.tooltip-error');
                if (tooltipError) tooltipError.textContent = '';
            }

            const formData = {
                from: fromValue,
                to: toValue,
                date: document.getElementById('dateInput').value,
                driver_age: document.getElementById('driverAge').value,
                driver_rating: document.getElementById('driverRating').value,
                driver_phone: document.getElementById('driverPhone').checked ? 1 : 0,
                driver_name: document.getElementById('driverName').value,
                keyword: document.getElementById('keyword').value,
                passenger_rating: document.getElementById('passengerRating').value,
                payment_method: document.getElementById('payment-method').value,
                vehicle_type: document.getElementById('VehicleType').value,
                features: selectedFeatures.join(';'),
                luggage: selectedLuggages.join(';'),
                smoking: selectedSmoking.join(';'),
                pets: selectedPets.join(';'),
                hide_full_rides: document.getElementById('hide-full-rides')?.checked ? '1' : ''
            };

            const baseUrl = '{{ route('search_ride', ['lang' => $selectedLanguage->abbreviation]) }}';
            const queryParams = Object.entries(formData)
                .map(([key, value]) => `${key}=${encodeURIComponent(value)}`)
                .join('&');

            window.location.href = `${baseUrl}?${queryParams}`;
        }

        function resetFilters() {
            // Reset checkboxes
            document.querySelectorAll('.ride-preferences, .luggage, .smoking, .pet').forEach(checkbox => {
                checkbox.checked = false;
            });

            // Reset form fields
            const resetFields = {
                'driverAge': '0',
                'passengerRating': '',
                'payment-method': '',
                'driverRating': '0',
                'VehicleType': '',
                'driverName': '',
                'keyword': ''
            };

            Object.entries(resetFields).forEach(([id, value]) => {
                const element = document.getElementById(id);
                if (element) element.value = value;
            });

            document.getElementById('driverPhone').checked = false;

            const hideFullRidesCheckbox = document.getElementById('hide-full-rides');
            if (hideFullRidesCheckbox) hideFullRidesCheckbox.checked = false;

            // Clear stored selections
            selectedFeatures = [];
            selectedLuggages = [];
            selectedSmoking = [];
            selectedPets = [];

            window.location.href = "{{ route('search_ride', ['lang' => $selectedLanguage->abbreviation]) }}";
        }
        // Google Places init (from/to) – same behaviour as index.blade.php
        window.initGooglePlaces = function() {
            geocoder = new google.maps.Geocoder();
            const fromInput = document.getElementById('from_spot_0');
            const toInput = document.getElementById('to_spot_0');
            if (!fromInput || !toInput) return;

            fromAutocomplete = new google.maps.places.Autocomplete(fromInput, {
                componentRestrictions: {
                    country: 'ca'
                },
                types: ['(cities)'],
                fields: ['address_components', 'formatted_address', 'name', 'place_id']
            });
            toAutocomplete = new google.maps.places.Autocomplete(toInput, {
                componentRestrictions: {
                    country: 'ca'
                },
                types: ['(cities)'],
                fields: ['address_components', 'formatted_address', 'name', 'place_id']
            });

            fromAutocomplete.addListener('place_changed', function() {
                const place = fromAutocomplete.getPlace();
                if (place.address_components && place.place_id) {
                    isSettingPlaceValue = true;
                    isSelectingFromDropdown = true;
                    const formattedAddress = formatPlaceAddressSearchRide(place);
                    selectedFromPlace = {
                        place_id: place.place_id,
                        formatted_address: formattedAddress,
                        value: formattedAddress
                    };
                    fromInput.value = formattedAddress;
                    const err = document.getElementById('fromInputError');
                    if (err) err.classList.add('hidden');
                    setTimeout(function() {
                        isSettingPlaceValue = false;
                        isSelectingFromDropdown = false;
                    }, 100);
                }
            });
            toAutocomplete.addListener('place_changed', function() {
                const place = toAutocomplete.getPlace();
                if (place.address_components && place.place_id) {
                    isSettingPlaceValue = true;
                    isSelectingFromDropdown = true;
                    const formattedAddress = formatPlaceAddressSearchRide(place);
                    selectedToPlace = {
                        place_id: place.place_id,
                        formatted_address: formattedAddress,
                        value: formattedAddress
                    };
                    toInput.value = formattedAddress;
                    const err = document.getElementById('toInputError');
                    if (err) err.classList.add('hidden');
                    setTimeout(function() {
                        isSettingPlaceValue = false;
                        isSelectingFromDropdown = false;
                    }, 100);
                }
            });

            fromInput.addEventListener('input', function() {
                if (isSettingPlaceValue) return;
                if (selectedFromPlace && this.value.trim() !== selectedFromPlace.value) selectedFromPlace =
                    null;
            });
            toInput.addEventListener('input', function() {
                if (isSettingPlaceValue) return;
                if (selectedToPlace && this.value.trim() !== selectedToPlace.value) selectedToPlace = null;
            });

            fromInput.addEventListener('keydown', function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    resolveTypedCityValueSearchRide(this.value, 'from').then(function() {
                        navigateToSearchRoute1();
                    });
                }
            });
            toInput.addEventListener('keydown', function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    resolveTypedCityValueSearchRide(this.value, 'to').then(function() {
                        navigateToSearchRoute1();
                    });
                }
            });

            document.addEventListener('mousedown', function(e) {
                if (e.target.closest('.pac-container')) isSelectingFromDropdown = true;
                else setTimeout(function() {
                    isSelectingFromDropdown = false;
                }, 50);
            });

            fromInput.addEventListener('blur', function() {
                if (isSettingPlaceValue || isSelectingFromDropdown) return;
                var self = this;
                setTimeout(async function() {
                    if (isSettingPlaceValue || isSelectingFromDropdown) return;
                    var currentValue = self.value.trim();
                    var fromInputError = document.getElementById('fromInputError');

                    if (currentValue !== '' && (!selectedFromPlace || currentValue !==
                            selectedFromPlace.value)) {
                        await resolveTypedCityValueSearchRide(currentValue, 'from');
                        currentValue = self.value.trim();
                    }

                    // Validate: check if input has value but no valid place is selected
                    if (currentValue === '' || !selectedFromPlace || currentValue !==
                        selectedFromPlace.value) {
                        selectedFromPlace = null;

                        // Show error tooltip: required when empty, city not found when invalid text
                        if (currentValue !== '' && fromInputError) {
                            var tooltipError = fromInputError.querySelector('.tooltip-error');
                            if (tooltipError) {
                                tooltipError.textContent = currentValue === '' ? errorFromRequired :
                                    errorCityMissing;
                            }
                            fromInputError.classList.remove('hidden');
                        }
                    } else {
                        // Valid place selected, hide error if showing
                        if (currentValue !== '' && fromInputError) {
                            fromInputError.classList.add('hidden');
                        }
                    }
                }, 200);
            });

            toInput.addEventListener('blur', function() {
                if (isSettingPlaceValue || isSelectingFromDropdown) return;
                var self = this;
                setTimeout(async function() {
                    if (isSettingPlaceValue || isSelectingFromDropdown) return;
                    var currentValue = self.value.trim();
                    var toInputError = document.getElementById('toInputError');

                    if (currentValue !== '' && (!selectedToPlace || currentValue !== selectedToPlace
                            .value)) {
                        await resolveTypedCityValueSearchRide(currentValue, 'to');
                        currentValue = self.value.trim();
                    }

                    // Validate: check if input has value but no valid place is selected
                    if (currentValue === '' || !selectedToPlace || currentValue !== selectedToPlace
                        .value) {
                        selectedToPlace = null;

                        // Show error tooltip: required when empty, city not found when invalid text
                        if (currentValue !== '' && toInputError) {
                            var tooltipError = toInputError.querySelector('.tooltip-error');
                            if (tooltipError) {
                                tooltipError.textContent = currentValue === '' ? errorToRequired :
                                    errorCityMissing;
                            }
                            toInputError.classList.remove('hidden');
                        }
                    } else {
                        // Valid place selected, hide error if showing
                        if (currentValue !== '' && toInputError) {
                            toInputError.classList.add('hidden');
                        }
                    }
                }, 200);
            });

            fromInput.addEventListener('focus', function() {
                var fromInputError = document.getElementById('fromInputError');
                if (fromInputError) fromInputError.classList.add('hidden');
            });
            toInput.addEventListener('focus', function() {
                var toInputError = document.getElementById('toInputError');
                if (toInputError) toInputError.classList.add('hidden');
            });

            // Initialize place state from pre-filled values (e.g. from request) so swap + search works without tooltip errors
            (function initPlaceStateFromInputs() {
                var fromVal = (fromInput.value || '').trim();
                var toVal = (toInput.value || '').trim();
                if (fromVal) resolveTypedCityValueSearchRide(fromVal, 'from');
                if (toVal) resolveTypedCityValueSearchRide(toVal, 'to');
            })();
        };

        function formatPlaceAddressSearchRide(place) {
            var city = '',
                province = '',
                country = 'Canada';
            if (!place.address_components) return place.name || place.formatted_address || '';
            for (var i = 0; i < place.address_components.length; i++) {
                var c = place.address_components[i],
                    t = c.types;
                if (!city && (t.indexOf('locality') !== -1 || t.indexOf('administrative_area_level_2') !== -1)) city = c
                    .long_name;
                if (!province && t.indexOf('administrative_area_level_1') !== -1) province = c.short_name;
                if (t.indexOf('country') !== -1) country = c.long_name;
            }
            if (!city && place.name) {
                var p = place.name.split(',').map(function(s) {
                    return s.trim();
                });
                if (p[0]) city = p[0];
                if (p[1] && p[1].length <= 3 && !province) province = p[1].toUpperCase();
            }
            if (!city && place.formatted_address) {
                var a = place.formatted_address.split(',').map(function(s) {
                    return s.trim();
                });
                if (a[0]) city = a[0];
            }
            var out = city || '';
            if (province) out += (out ? ', ' : '') + province;
            if (country && out) out += ', ' + country;
            return out || place.name || place.formatted_address || '';
        }

        function resolveTypedCityValueSearchRide(rawValue, target) {
            var value = (rawValue || '').trim();
            if (!value || !geocoder) return Promise.resolve(false);
            var inputId = target === 'from' ? 'from_spot_0' : 'to_spot_0';
            var input = document.getElementById(inputId);
            return new Promise(function(resolve) {
                geocoder.geocode({
                    address: value,
                    componentRestrictions: {
                        country: 'CA'
                    }
                }, function(response, status) {
                    if (status !== 'OK' || !response || !response.length) {
                        resolve(false);
                        return;
                    }
                    var result = null;
                    for (var i = 0; i < response.length; i++) {
                        var item = response[i];
                        if (item.address_components && item.address_components.some(function(comp) {
                                return comp.types.indexOf('locality') !== -1 || comp.types.indexOf(
                                    'administrative_area_level_2') !== -1;
                            })) {
                            result = item;
                            break;
                        }
                    }
                    if (!result) {
                        resolve(false);
                        return;
                    }
                    var formattedAddress = formatPlaceAddressSearchRide(result);
                    if (!formattedAddress) {
                        resolve(false);
                        return;
                    }
                    isSettingPlaceValue = true;
                    var selectedPlace = {
                        place_id: result.place_id,
                        formatted_address: formattedAddress,
                        value: formattedAddress
                    };
                    if (target === 'from') selectedFromPlace = selectedPlace;
                    else selectedToPlace = selectedPlace;
                    if (input) input.value = formattedAddress;
                    var errorEl = document.getElementById(target === 'from' ? 'fromInputError' :
                        'toInputError');
                    if (errorEl) errorEl.classList.add('hidden');
                    setTimeout(function() {
                        isSettingPlaceValue = false;
                    }, 100);
                    resolve(true);
                });
            });
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Auto-grow textareas
            const autoGrow = (textarea) => {
                textarea.style.height = 'auto';
                textarea.style.height = textarea.scrollHeight + 'px';
            };

            document.querySelectorAll('textarea').forEach(textarea => {
                textarea.style.resize = 'none';
                textarea.style.overflowY = 'hidden';
                textarea.addEventListener('input', () => autoGrow(textarea));

                if (!textarea.value && textarea.placeholder) {
                    textarea.value = textarea.placeholder;
                    autoGrow(textarea);
                    textarea.value = '';
                } else {
                    autoGrow(textarea);
                }
            });

            // Search filter toggle
            const toggle = document.getElementById('search-filter-toggle');
            const close = document.getElementById('search-filter-close');
            const searchFilters = document.getElementById('search-filter');
            const overlay = document.getElementById('search-filter-overlay');

            if (toggle && close && searchFilters && overlay) {
                const toggleSearchFilters = (show) => {
                    searchFilters.classList.toggle('translate-x-full', !show);
                    overlay.classList.toggle('hidden', !show);
                };

                toggle.addEventListener('click', () => toggleSearchFilters(true));
                close.addEventListener('click', () => toggleSearchFilters(false));
                overlay.addEventListener('click', () => toggleSearchFilters(false));
            }
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
        @if (session('success'))
            document.addEventListener('DOMContentLoaded', showModal);
        @endif

        // Hide Full Rides: filter is applied server-side via hide_full_rides query param
    </script>
    <!-- Google Places Autocomplete API -->
    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_API_KEY') }}&libraries=places&callback=initGooglePlaces"
        async defer></script>
@endsection
