@extends('layouts.template')

@section('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        .features_tooltiptext::after {
            content: "";
            border-width: 10px;
            border-style: solid;
            border-color: #3b82f6 transparent transparent transparent;
            position: absolute;
            bottom: -20px;
            /* left: 4rem; */
        }

        .luggage_tooltiptext::after {
            content: "";
            border-width: 10px;
            border-style: solid;
            border-color: #3b82f6 transparent transparent transparent;
            position: absolute;
            bottom: -20px;
            /* left: 4rem; */
        }

        .payment_tooltiptext::after {
            content: "";
            border-width: 10px;
            border-style: solid;
            border-color: #3b82f6 transparent transparent transparent;
            position: absolute;
            bottom: -20px;
            /* left: 4rem; */
        }

        /* Extra small devices */
        @media only screen and (max-width: 375px) {
            .tooltip_width {
                width: 16.5rem;
            }

            .tooltip_position {
                right: 13rem;
                top: -7.5rem;
            }

            .luggage_tooltiptext::after {
                right: 3.3rem;
            }

            .payment_tooltiptext_position {
                top: -6.3rem;
            }
        }

        @media only screen and (min-width:376px) and (max-width: 639px) {
            .tooltip_width {
                width: 20rem;
            }

            .tooltip_position {
                right: 16.5rem;
                top: -6.5rem;
            }

            .luggage_tooltiptext::after {
                right: 3.3rem;
            }
        }

        @media only screen and (max-width: 767px) {
            .features_tooltiptext::after {
                content: "";
                border-width: 10px;
                border-style: solid;
                border-color: transparent transparent #3b82f6 transparent;
                position: absolute;
                top: -20px;
                bottom: auto;
                left: 5.8rem;
            }
        }

        /* Add more spots – collapsible header and smooth slide panel */
        .add-more-spots-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            text-align: left;
            cursor: pointer;
            border: none;
            transition: opacity 0.2s ease, background-color 0.2s ease;
        }

        .add-more-spots-header:hover {
            opacity: 0.95;
        }

        .add-more-spots-chevron {
            flex-shrink: 0;
            width: 1.5rem;
            height: 1.5rem;
            margin-left: 0.5rem;
            transition: transform 0.35s ease-out;
        }

        .add-more-spots-header[aria-expanded="false"] .add-more-spots-chevron {
            transform: rotate(-90deg);
        }

        .add-more-spots-panel {
            overflow: hidden;
            transition: height 0.35s ease-out;
        }
    </style>
@endsection

@section('content')

    {{-- Early function definitions to prevent "not defined" errors on browser autocomplete --}}
    <script>
        function fromInput(index) {
            if (typeof $ !== 'undefined' && typeof debounce !== 'undefined') {
                debounce(function() {
                    let searchTerm = $('#from_spot_' + index).val();
                    if (searchTerm.length >= 2) {
                        let searchData = $('#to_spot_' + index).val();
                        if (typeof fetchCities !== 'undefined') {
                            fetchCities(searchTerm, searchData, 'from_spot', index);
                        }
                    }
                }, 500)();
            }
        }

        function toInput(index) {
            if (typeof $ !== 'undefined' && typeof debounce !== 'undefined') {
                debounce(function() {
                    let searchTerm = $('#to_spot_' + index).val();
                    if (searchTerm.length >= 2) {
                        let searchData = $('#from_spot_' + index).val();
                        if (typeof fetchCities !== 'undefined') {
                            fetchCities(searchTerm, searchData, 'to_spot', index);
                        }
                    }
                }, 500)();
            }
        }
    </script>

    <div class="container px-4 mx-auto my-14">
        @if (session('error'))
            <div class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                    <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                        <div
                            class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg w-full modal-border">
                            <button type="button" onclick="closeModal()"
                                class="absolute top-3 right-3 text-gray-400 hover:text-gray-500">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                            <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                                <div class="sm:flex sm:items-start justify-center">
                                    <!-- <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full sm:mx-0 sm:h-10 sm:w-10 bg-red-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-lg text-white w-8 h-8" viewBox="0 0 16 16">
                                            <path d="M7.005 3.1a1 1 0 1 1 1.99 0l-.388 6.35a.61.61 0 0 1-1.214 0zM7 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0"/>
                                        </svg>
                                    </div> -->
                                </div>
                                <div class="text-center sm:ml-4 sm:mt-0 sm:text-left">
                                    <div class="">
                                        <h3 class="text-3xl text-center font-FuturaMdCnBT font-medium text-gray-900 mb-4"
                                            id="modal-title">{!! session('heading') !!}</h3>
                                    </div>
                                    <div class="mt-2 w-full">
                                        <p class="text-lg text-center text-black">{!! session('error') !!}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="px-4 pb-6 pt-4  sm:flex sm:flex-row-reverse sm:px-6 justify-center">
                                <a href=""
                                    class="inline-flex w-full justinline-flex justify-center rounded bg-red-500 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-red-400 sm:ml-3 sm:w-24">{{ $siteText['close_btn_text'] ?? 'Close' }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        {{-- Saved/info message popup disabled on edit ride page --}}
        @if (false && session('message'))
            <div id="myModal" class="relative z-50" id="delete_message_confirmation" aria-labelledby="modal-title"
                role="dialog" aria-modal="true">
                <div onclick="closeModal()" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                    <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                        <div
                            class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
                            <button type="button" onclick="closeModal()"
                                class="absolute top-3 right-3 text-gray-400 hover:text-gray-500">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                            <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                                <div class="sm:flex sm:items-start justify-center"></div>
                                <div class="text-center sm:ml-4 sm:mt-0 sm:text-left">
                                    <div class="mt-2 w-full">
                                        <p class="can-exp-p text-center">{{ session('message') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="px-4 pb-6 pt-4 flex items-center space-x-2 sm:space-x-4 sm:px-6 justify-center">
                                <input type="hidden" id="notificationId" value="3094">
                                <a href="#" onclick="closeModal()"
                                    class="button-exp-fill">{{ $siteText['close_btn_text'] }} </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <div class="flex justify-between items-center">
            <h1>
                Edit Ride
            </h1>
        </div>
        <form method="POST"
            action="{{ route('update_ride', ['lang' => $selectedLanguage->abbreviation, 'ride_id' => $ride->id]) }}"
            enctype="multipart/form-data" id="edit-ride-form">
            @csrf
            @method('PUT')
            <div class=" grid grid-cols-1 lg:grid-cols-3 gap-4">
                <div class="col-span-3">
                    <div class="bg-white rounded-lg overflow-hidden shadow-3xl">
                        <h3 class="bg-primary text-white py-2 px-4">
                            @isset($postRidePage->ride_info_heading)
                                {{ $postRidePage->ride_info_heading }}
                            @endisset
                        </h3>

                        <input type="hidden" value="{{ $ride->defaultRideDetail[0]->id }}" name="default_ride_detail_id">
                        <div class="bg-white p-4">
                            <div class="flex flex-col md:flex-row justify-between items-start">
                                <div class="w-full md:w-[45%] mb-4 relative">
                                    <div>
                                        <label for="from_spot_0" class="block mb-2 text-gray-900">
                                            @isset($postRidePage->from_label)
                                                {{ $postRidePage->from_label }}
                                            @endisset
                                            <span class="text-red-500">*</span>
                                        </label>
                                        <div class="relative mt-2">
                                            <div
                                                class="absolute inset-y-0 start-0 flex items-center pl-2 pointer-events-none">
                                                <img src="{{ asset('assets/search-bar-from.png') }}" class="w-auto h-6"
                                                    alt="">
                                            </div>

                                            @php
                                                $departure =
                                                    isset($ride->defaultRideDetail) &&
                                                    isset($ride->defaultRideDetail[0])
                                                        ? $ride->defaultRideDetail[0]->departure
                                                        : '';
                                                $destination =
                                                    isset($ride->defaultRideDetail) &&
                                                    isset($ride->defaultRideDetail[0])
                                                        ? $ride->defaultRideDetail[0]->destination
                                                        : '';
                                            @endphp

                                            <input type="text" id="from_spot_0" name="from"
                                                value="{{ old('from', $departure) }}" autocomplete="off"
                                                class="bg-gray-100 border border-gray-200 pl-7 text-gray-900 text-base lg:text-lg rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 block w-full p-2.5 mt-2"
                                                @isset($postRidePage->from_placeholder)
                                                placeholder="{{ $postRidePage->from_placeholder }}"
                                            @endisset>
                                            <div class="absolute hidden mt-1 z-10 left-0 top-full" id="fromInputError">
                                                <div
                                                    class="tooltip-error shadow-lg rounded p-2 bg-red-500 text-white text-sm lg:text-base">
                                                </div>
                                            </div>
                                        </div>
                                        @error('from')
                                            <div class="relative tooltip -bottom-4 flex mt-1" role="alert">
                                                <div
                                                    class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                                                    <p class="text-white leading-none text-sm lg:text-base">
                                                        {{ $message }}
                                                        <a class="text-white leading-none text-sm lg:text-base"
                                                            href="{{ route('contact_us', ['lang' => app()->getLocale()]) }}">
                                                            {{ optional($postRideSubDetailPage)->city_not_fount_contact_text ?? '' }}
                                                        </a>
                                                    </p>
                                                </div>
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="w-full md:w-[10%] md:mt-10 flex justify-center items-start">
                                    <button type="button" onclick="swapLocations()">
                                        <img src="{{ asset('assets/arrow.png') }}" class="w-10 h-10 mx-auto"
                                            alt="">
                                    </button>
                                </div>
                                <div class="w-full md:w-[45%] mb-4 relative">
                                    <div>
                                        <label for="to_spot_0" class="block mb-2 text-gray-900">
                                            @isset($postRidePage->to_label)
                                                {{ $postRidePage->to_label }}
                                            @endisset
                                            <span class="text-red-500">*</span>
                                        </label>
                                        <div class="relative mt-2">
                                            <div
                                                class="absolute inset-y-0 start-0 flex items-center pl-2 pointer-events-none">
                                                <img src="{{ asset('images/new-21-search-bar-to.png') }}"
                                                    class="w-auto h-6" alt="">
                                            </div>
                                            <input type="text" id="to_spot_0" name="to"
                                                value="{{ old('to', $destination) }}" autocomplete="off"
                                                class="bg-gray-100 border pl-7 border-gray-200 text-base lg:text-lg text-gray-900 rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 mt-2 block w-full p-2.5"
                                                @isset($postRidePage->to_placeholder)
                                                placeholder="{{ $postRidePage->to_placeholder }}"
                                            @endisset>
                                            <div class="absolute hidden mt-1 z-10 left-0 top-full" id="toInputError">
                                                <div
                                                    class="tooltip-error shadow-lg rounded p-2 bg-red-500 text-white text-sm lg:text-base">
                                                </div>
                                            </div>
                                        </div>
                                        @error('to')
                                            <div class="relative tooltip -bottom-4 flex mt-1" role="alert">
                                                <div
                                                    class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                                                    <p class="text-white leading-none text-sm lg:text-base">
                                                        {{ $message }}
                                                        <a class="text-white leading-none text-sm lg:text-base"
                                                            href="{{ route('contact_us', ['lang' => app()->getLocale()]) }}">
                                                            {{ optional($postRideSubDetailPage)->city_not_fount_contact_text ?? '' }}
                                                        </a>
                                                    </p>
                                                </div>
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-end flex-col md:flex-row justify-between">
                                <div class="w-full md:w-[45%] mb-4">
                                    <label for="pickup_location" class="block mb-2 text-gray-900">
                                        @isset($postRidePage->pick_up_label)
                                            {{ $postRidePage->pick_up_label }}
                                        @endisset
                                    </label>
                                    <textarea id="pickup_location" rows="5" name="pickup"
                                        class="block p-2.5 w-full text-gray-900 bg-gray-100 rounded border border-gray-200 text-base lg:text-lg focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 mt-2"
                                        @isset($postRidePage->pick_up_placeholder)
                                    placeholder="{{ $postRidePage->pick_up_placeholder }}"
                                @endisset>{{ old('pickup', $ride->pickup) }}</textarea>
                                    @error('pickup')
                                        <div class="relative tooltip -bottom-4 group-hover:flex">
                                            <div role="tooltip"
                                                class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                                                <p class="text-white leading-none text-sm lg:text-base">{{ $message }}
                                                </p>
                                            </div>
                                        </div>
                                    @enderror
                                </div>
                                <div class="w-full md:w-[45%] mb-4">
                                    <label for="dropoff_location"class="block mb-2 text-gray-900">
                                        @isset($postRidePage->drop_off_label)
                                            {{ $postRidePage->drop_off_label }}
                                        @endisset
                                    </label>
                                    <textarea id="dropoff_location" rows="5" name="dropoff"
                                        class="block p-2.5 w-full text-gray-900 bg-gray-100 rounded border border-gray-200 text-base lg:text-lg focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 mt-2"
                                        @isset($postRidePage->drop_off_placeholder)
                                    placeholder="{{ $postRidePage->drop_off_placeholder }}"
                                @endisset>{{ old('dropoff', $ride->dropoff) }}</textarea>
                                    @error('dropoff')
                                        <div class="relative tooltip -bottom-4 group-hover:flex">
                                            <div role="tooltip"
                                                class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                                                <p class="text-white leading-none text-sm lg:text-base">{{ $message }}
                                                </p>
                                            </div>
                                        </div>
                                    @enderror
                                </div>
                                <div class="map-container w-full h-64 block md:hidden">
                                    <iframe
                                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3806.452697041917!2d78.39076592375736!3d17.43803374982052!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3bcb9144cdba8c47%3A0x937fe346f411a645!2sTutorials%20Point%20(India)%20Ltd.!5e0!3m2!1sen!2sin!4v1673629212535!5m2!1sen!2sin"
                                        width="100%" height="100%" style="border:0;" allowfullscreen=""
                                        loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                                    </iframe>
                                </div>
                            </div>

                            <div>
                                <label for="date_time" class="block text-gray-900">
                                    @isset($postRidePage->date_time_label)
                                        {{ $postRidePage->date_time_label }}
                                    @endisset
                                </label>
                                <div class="flex items-start flex-row mb-4 justify-between">
                                    <div class="w-[45%] mb-4">
                                        <div class="relative mt-2">
                                            <div
                                                class="absolute inset-y-0 start-0 flex items-center pl-2 pointer-events-none">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" class="bi bi-calendar-event" viewBox="0 0 16 16">
                                                    <path
                                                        d="M11 6.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1z" />
                                                    <path
                                                        d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z" />
                                                </svg>
                                            </div>
                                            <input type="text" id="dateInput" name="date"
                                                class="bg-gray-100 border pl-7 border-gray-200 text-base lg:text-lg text-gray-900  rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 mt-2 block w-full p-2.5"
                                                placeholder="">
                                        </div>
                                        @error('date')
                                            <div class="relative tooltip -bottom-4 group-hover:flex">
                                                <div role="tooltip"
                                                    class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                                                    <p class="text-white leading-none text-sm lg:text-base">
                                                        {{ $message }}</p>
                                                </div>
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="w-[10%] mt-4 text-center">
                                        <span class="text-center text-base lg:text-lg ">
                                            @isset($postRidePage->at_label)
                                                {{ $postRidePage->at_label }}
                                            @endisset
                                        </span>
                                    </div>
                                    <div class="w-[45%] mb-4">
                                        <div class="relative mt-2">
                                            <div
                                                class="absolute inset-y-0 start-0 flex items-center pl-2 pointer-events-none">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                    class="w-6 h-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </div>
                                            @php
                                                $timeValue = old('time');
                                                if ($timeValue === null && $ride && !empty($ride->time)) {
                                                    try {
                                                        $timeValue = \Carbon\Carbon::parse($ride->time)->format('H:i');
                                                    } catch (\Exception $e) {
                                                        $timeValue = is_string($ride->time)
                                                            ? substr($ride->time, 0, 5)
                                                            : '';
                                                    }
                                                }
                                                $timeValue = $timeValue ?? '';
                                            @endphp
                                            <input type="text" id="timeInput" name="time"
                                                value="{{ $timeValue ?? '' }}"
                                                class="bg-gray-100 border pl-10 border-gray-200 text-gray-900 text-base lg:text-lg rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 mt-2 block w-full p-2.5"
                                                placeholder="">
                                        </div>
                                        @error('time')
                                            <div class="relative tooltip -bottom-4 group-hover:flex">
                                                <div role="tooltip"
                                                    class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                                                    <p class="text-white leading-none text-sm lg:text-base">
                                                        {{ $message }}</p>
                                                </div>
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            @php
                                $originText =
                                    isset($ride->defaultRideDetail) && isset($ride->defaultRideDetail[0])
                                        ? $ride->defaultRideDetail[0]->departure
                                        : '';
                                $destinationText =
                                    isset($ride->defaultRideDetail) && isset($ride->defaultRideDetail[0])
                                        ? $ride->defaultRideDetail[0]->destination
                                        : '';
                                $stopsForDisplay = [];
                                $pricesForDisplay = [];
                                $segmentIdsForStops = [];
                                $chainSegments = [];
                                if (null !== old('stop_spot_display') && is_array(old('stop_spot_display'))) {
                                    $stopsForDisplay = old('stop_spot_display');
                                    $pricesForDisplay =
                                        null !== old('price_spot_display') && is_array(old('price_spot_display'))
                                            ? old('price_spot_display')
                                            : array_fill(0, count($stopsForDisplay), '');
                                } elseif (
                                    null !== old('to_spot') &&
                                    is_array(old('to_spot')) &&
                                    count(old('to_spot')) > 0
                                ) {
                                    $toSpots = old('to_spot');
                                    $n = count($toSpots) - 1;
                                    for ($i = 0; $i < $n; $i++) {
                                        $stopsForDisplay[] = $toSpots[$i];
                                    }
                                    $pricesForDisplay =
                                        null !== old('price_spot') && is_array(old('price_spot'))
                                            ? array_slice(old('price_spot'), 0, $n)
                                            : array_fill(0, count($stopsForDisplay), '');
                                } elseif (!empty($ride->moreRideDetail) && count($ride->moreRideDetail) > 0) {
                                    // Build ordered chain from origin to destination using only the actual route segments.
                                    $details = $ride->moreRideDetail->sortBy('id')->values();
                                    $orderedPoints = collect([$originText]);
                                    $current = $originText;
                                    $remaining = $details;
                                    while ($current !== $destinationText && $remaining->isNotEmpty()) {
                                        $nextSegment = $remaining->first(function ($d) use ($current) {
                                            return (string) $d->departure === (string) $current;
                                        });
                                        if (!$nextSegment) {
                                            break;
                                        }
                                        $chainSegments[] = $nextSegment;
                                        $orderedPoints->push($nextSegment->destination);
                                        $current = $nextSegment->destination;
                                        $remaining = $remaining->filter(function ($d) use ($nextSegment) {
                                            return $d->id != $nextSegment->id;
                                        });
                                    }
                                    $segmentIdsForStops = collect($chainSegments)->pluck('id')->values()->all();
                                    $chainStops =
                                        $orderedPoints->count() > 2
                                            ? $orderedPoints->slice(1, $orderedPoints->count() - 2)->values()
                                            : collect();
                                    foreach ($chainStops as $index => $stop) {
                                        $stopsForDisplay[] = $stop;
                                        if (isset($chainSegments[$index])) {
                                            $pricesForDisplay[] = $chainSegments[$index]->price ?? '';
                                        } else {
                                            $pricesForDisplay[] = '';
                                        }
                                    }
                                }
                                $stopPickupDropoffForDisplay = [];
                                if (null !== old('stop_pickup_dropoff') && is_array(old('stop_pickup_dropoff'))) {
                                    $stopPickupDropoffForDisplay = old('stop_pickup_dropoff');
                                } elseif (
                                    !empty($ride->moreRideDetail) &&
                                    count($ride->moreRideDetail) > 0 &&
                                    !empty($chainSegments)
                                ) {
                                    foreach ($chainSegments as $index => $segment) {
                                        if ($index >= count($stopsForDisplay)) {
                                            break;
                                        }
                                        $stopPickupDropoffForDisplay[] = $segment->dropoff ?? '';
                                    }
                                }
                                if (empty($stopsForDisplay)) {
                                    $stopsForDisplay = [''];
                                    $pricesForDisplay = [''];
                                }
                                if (count($pricesForDisplay) !== count($stopsForDisplay)) {
                                    $pricesForDisplay = array_pad($pricesForDisplay, count($stopsForDisplay), '');
                                }
                                if (count($stopPickupDropoffForDisplay) !== count($stopsForDisplay)) {
                                    $stopPickupDropoffForDisplay = array_pad(
                                        $stopPickupDropoffForDisplay,
                                        count($stopsForDisplay),
                                        '',
                                    );
                                }
                                $segmentsForPrice = [];
                                $realStops = array_values(
                                    array_filter($stopsForDisplay, function ($s) {
                                        return trim((string) $s) !== '';
                                    }),
                                );
                                if (count($realStops) > 0) {
                                    // Always show only the consecutive segments (origin → stop1, stop1 → stop2, ..., lastStop → destination)
                                    if (!empty($chainSegments)) {
                                        $n = count($chainSegments);
                                        for ($i = 0; $i < $n; $i++) {
                                            $from = $chainSegments[$i]->departure ?? '';
                                            $to = $chainSegments[$i]->destination ?? '';
                                            $segmentsForPrice[] = [
                                                'from' => $from,
                                                'to' => $to,
                                                'price' => $chainSegments[$i]->price ?? '',
                                            ];
                                        }
                                    } elseif (
                                        null !== old('from_spot') &&
                                        is_array(old('from_spot')) &&
                                        null !== old('to_spot') &&
                                        is_array(old('to_spot')) &&
                                        count(old('from_spot')) > 0
                                    ) {
                                        $fromSpot = old('from_spot');
                                        $toSpot = old('to_spot');
                                        $prices =
                                            null !== old('price_spot') && is_array(old('price_spot'))
                                                ? old('price_spot')
                                                : (null !== old('price_spot_display') &&
                                                is_array(old('price_spot_display'))
                                                    ? old('price_spot_display')
                                                    : []);
                                        for ($i = 0; $i < count($fromSpot); $i++) {
                                            $segmentsForPrice[] = [
                                                'from' => $fromSpot[$i] ?? '',
                                                'to' => $toSpot[$i] ?? '',
                                                'price' => $prices[$i] ?? '',
                                            ];
                                        }
                                    } else {
                                        $n = count($realStops);
                                        $pricesFromOld =
                                            null !== old('price_spot_display') && is_array(old('price_spot_display'))
                                                ? old('price_spot_display')
                                                : [];
                                        for ($i = 0; $i <= $n; $i++) {
                                            $from = $i === 0 ? $originText : $realStops[$i - 1];
                                            $to = $i === $n ? $destinationText : $realStops[$i];
                                            $segmentsForPrice[] = [
                                                'from' => $from,
                                                'to' => $to,
                                                'price' => isset($pricesFromOld[$i]) ? $pricesFromOld[$i] : '',
                                            ];
                                        }
                                    }
                                }
                            @endphp
                            <div class="bg-white rounded-lg overflow-hidden shadow-3xl" id="stops-section-wrapper"
                                data-segment-ids="{{ json_encode($segmentIdsForStops) }}">
                                @php $hasStops = count($realStops) > 0; @endphp
                                <button type="button" id="add-more-spots-toggle"
                                    class="add-more-spots-header text-2xl bg-primary text-white py-2 px-4"
                                    aria-expanded="{{ $hasStops ? 'true' : 'false' }}"
                                    aria-controls="add-more-spots-panel" onclick="toggleAddMoreSpots(this)">
                                    <h3 class="text-2xl">
                                        {{ $postRidePage->add_more_from_to ?? 'Stops Along the Way (Optional)' }}</h3>
                                    <svg class="add-more-spots-chevron text-white" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <div id="add-more-spots-panel" class="add-more-spots-panel" role="region"
                                    aria-labelledby="add-more-spots-toggle"
                                    style="{{ $hasStops ? 'height: auto;' : 'height: 0;' }}">
                                    <div class="add-more-spots-panel-inner bg-white p-4">
                                        <div class="flex items-center gap-2 mb-3">
                                            <h4 class="text-gray-900 text-xl font-medium ">From: </h4>
                                            <p class="text-gray-900 text-primary lg:text-lg ">{{ $originText }}</p>
                                        </div>
                                        <h4 class="text-xl font-medium text-gray-900 mt-4 mb-3">Stops Along the Way:<span
                                                class="text-red-500">*</span></h4>
                                        <div class="space-y-3 mb-4" id="stops-rows-container">
                                            @if ($hasStops)
                                                @foreach ($stopsForDisplay as $idx => $stopValue)
                                                    @php $renderIndex = $idx + 1; @endphp
                                                    <div class="flex items-center gap-3 stop-row"
                                                        data-stop-index="{{ $renderIndex }}">
                                                        <div class="flex flex-row gap-2 items-stretch flex-1 min-w-0">
                                                            <div class="relative flex-1 min-w-0">
                                                                <div
                                                                    class="absolute inset-y-0 start-0 flex items-center pl-2 pointer-events-none">
                                                                    <img src="{{ asset('assets/search-bar-from.png') }}"
                                                                        class="w-auto h-6" alt="">
                                                                </div>
                                                                <input type="text" name="stop_spot_display[]"
                                                                    data-stop-index="{{ $renderIndex }}"
                                                                    id="stop_spot_{{ $renderIndex }}"
                                                                    value="{{ $stopValue }}" autocomplete="off"
                                                                    class="bg-gray-100 border border-gray-200 pl-7 text-gray-900 text-base lg:text-lg rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 block w-full p-2.5"
                                                                    placeholder="">
                                                                <div class="absolute hidden mt-1 z-10 left-0 top-full"
                                                                    id="stopInputError_{{ $renderIndex }}">
                                                                    <div
                                                                        class="tooltip-error shadow-lg rounded p-2 bg-red-500 text-white text-sm lg:text-base">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <textarea name="stop_pickup_dropoff[]" data-stop-index="{{ $renderIndex }}"
                                                                id="stop_pickup_dropoff_{{ $renderIndex }}" rows="1" placeholder="pick up / drop off"
                                                                class="flex-1 min-w-0 bg-gray-100 border border-gray-200 text-gray-900 text-base lg:text-lg rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 block w-full p-2.5 resize-none">{{ old('stop_pickup_dropoff.' . $idx, $stopPickupDropoffForDisplay[$idx] ?? '') }}</textarea>
                                                        </div>
                                                        <button type="button"
                                                            class="stop-delete-btn flex-shrink-0 p-2 text-red-600 hover:text-red-700 hover:bg-red-50 rounded focus:outline-none focus:ring-2 focus:ring-red-400"
                                                            onclick="confirmDeleteStop(this)" title="Delete stop"
                                                            aria-label="Delete stop">
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                viewBox="0 0 24 24" stroke-width="1.5"
                                                                stroke="currentColor" class="w-6 h-6">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M6 18L18 6M6 6l12 12" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                        <button type="button" onclick="addStopRow();"
                                            class="button-exp-fill flex-shrink-0 whitespace-nowrap mb-4">+ Add
                                            Stop</button>
                                        <div class="flex items-center gap-2 mb-3">
                                            <h4 class="text-gray-900 text-xl font-medium ">To: </h4>
                                            <p class="text-gray-900 text-primary lg:text-lg ">{{ $destinationText }}</p>
                                        </div>
                                        <div id="stops-segments-hidden" class="hidden"></div>
                                    </div>
                                </div>
                            </div>

                            {{-- Delete Stop confirmation modal --}}
                            <div id="delete-stop-modal" class="relative z-50 hidden"
                                aria-labelledby="delete-stop-modal-title" role="dialog" aria-modal="true">
                                <div id="delete-stop-modal-backdrop" onclick="closeDeleteStopModal()"
                                    class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                                <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                                    <div
                                        class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                                        <div
                                            class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border1">
                                            <button type="button" onclick="closeDeleteStopModal()"
                                                class="absolute top-3 right-3 text-gray-400 hover:text-gray-500">
                                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                            <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                                                <div class="sm:flex sm:items-start justify-center"></div>
                                                <div class="text-center sm:ml-4 sm:mt-0 sm:text-left">
                                                    <div class="mt-2 w-full">
                                                        <p id="delete-stop-modal-title"
                                                            class="can-exp-p text-center text-xl">Delete Stop?</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div
                                                class="px-4 pb-6 pt-4 flex items-center space-x-2 sm:space-x-4 sm:px-6 justify-center">
                                                <button type="button" id="delete-stop-no"
                                                    class="w-24 bg-blue-600 p-2 rounded-md text-white hover:bg-blue-700">No</button>
                                                <button type="button" id="delete-stop-yes"
                                                    class="w-24 bg-red-600 p-2 rounded-md text-white hover:bg-red-700">Yes</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Modal for Price Error (Exceeds $0.72/km per seat) - same as post_ride --}}
                            <div id="priceErrorModal" class="hidden fixed inset-0 z-50"
                                aria-labelledby="price-error-modal-title" role="dialog" aria-modal="true">
                                <div onclick="closePriceErrorModal()"
                                    class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                                <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                                    <div
                                        class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                                        <div
                                            class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
                                            <button type="button" onclick="closePriceErrorModal()"
                                                class="absolute top-4 right-4 text-gray-400 hover:text-gray-500 z-50">
                                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                            <div class="bg-white px-4 mt-10 sm:mt-1 pb-4 pt-16 sm:p-6 sm:pb-4 sm:pt-16">
                                                <div class="text-center sm:ml-4 sm:mt-0 sm:text-left">
                                                    <div class="">
                                                        <h3 class="text-3xl text-center font-FuturaMdCnBT text-gray-900 mb-4"
                                                            id="priceErrorHeading">Price Limit Exceeded</h3>
                                                    </div>
                                                    <div class="mt-2 w-full">
                                                        <p class="can-exp-p text-center mb-3" id="priceErrorParagraph1">
                                                        </p>
                                                        <p class="can-exp-p text-center mb-3" id="priceErrorParagraph2">
                                                        </p>
                                                        <p class="can-exp-p text-center" id="priceErrorParagraph3"></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div
                                                class="px-4 pb-6 pt-4 flex items-center space-x-2 sm:space-x-4 sm:px-6 justify-center">
                                                <button type="button" id="priceErrorAdjustBtn"
                                                    onclick="adjustPriceFromError()" class="button-exp-fill">Adjust
                                                    Price</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Modal for Price Warning (Exceeds $0.66/km per seat but <= $0.72/km per seat) - same as post_ride --}}
                            <div id="priceWarningModal" class="hidden fixed inset-0 z-50"
                                aria-labelledby="price-warning-modal-title" role="dialog" aria-modal="true">
                                <div onclick="closePriceWarningModal()"
                                    class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                                <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                                    <div
                                        class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                                        <div
                                            class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
                                            <button type="button" onclick="closePriceWarningModal()"
                                                class="absolute top-4 right-4 text-gray-400 hover:text-gray-500 z-50">
                                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                            <div class="bg-white px-4 mt-10 sm:mt-1 pb-4 pt-16 sm:p-6 sm:pb-4 sm:pt-16">
                                                <div class="text-center sm:ml-4 sm:mt-0 sm:text-left">
                                                    <div class="">
                                                        <h3
                                                            class="text-3xl text-center font-FuturaMdCnBT text-gray-900 mb-4">
                                                            Recommended Contribution Limit</h3>
                                                    </div>
                                                    <div class="mt-2 w-full">
                                                        <p class="can-exp-p text-center mb-3" id="priceWarningParagraph1">
                                                        </p>
                                                        <p class="can-exp-p text-center" id="priceWarningParagraph2"></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div
                                                class="px-4 pb-6 pt-4 flex items-center space-x-2 sm:space-x-4 sm:px-6 justify-center">
                                                <button type="button" id="priceWarningAdjustBtn"
                                                    onclick="adjustPriceFromWarning(); return false;"
                                                    class="button-exp-fill">Adjust Price</button>
                                                <button type="button" id="priceWarningContinue"
                                                    class="button-exp-fill">Keep Current Price</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6">
                                <div class="flex items-center mb-4">
                                    <input id="recurring_trip" type="checkbox" name="recurring" value="1"
                                        {{ old('recurring') === '1' ? 'checked' : '' }}
                                        class="w-4 h-4 text-blue-600 cursor-pointer bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                                    <label for="recurring_trip" class="ml-2 text-gray-900">
                                        @isset($postRidePage->recurring_label)
                                            {{ $postRidePage->recurring_label }}
                                        @endisset
                                    </label>
                                </div>

                                <div id="recurringtripDetails">
                                    <div class="flex items-start flex-col md:flex-row mb-4 justify-between">
                                        <div class="w-full md:w-[45%] mb-4">
                                            <label for="recurring_type" class="block mb-2 text-gray-900">
                                                Recurring type
                                            </label>
                                            <div class="relative mt-2">
                                                <select id="type" name="recurring_type"
                                                    class="bg-gray-100 border border-gray-200 text-gray-900 text-base lg:text-lg rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 mt-2 block w-full p-2.5">
                                                    <option value=""
                                                        {{ old('recurring_type') === '' ? 'selected' : '' }}>
                                                        Select
                                                    </option>
                                                    <option value="Daily"
                                                        {{ old('recurring_type') === 'Daily' ? 'selected' : '' }}>
                                                        Daily
                                                    </option>
                                                    <option value="Weekly"
                                                        {{ old('recurring_type') === 'Weekly' ? 'selected' : '' }}>
                                                        Weekly
                                                    </option>
                                                </select>
                                            </div>
                                            @error('recurring_type')
                                                <div class="relative tooltip -bottom-4 group-hover:flex">
                                                    <div role="tooltip"
                                                        class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                                                        <p class="text-white leading-none text-sm lg:text-base">
                                                            {{ $message }}</p>
                                                    </div>
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="w-full md:w-[10%] hidden md:block mt-12 text-center">
                                            <span class="text-center text-base lg:text-lg ">
                                                or
                                            </span>
                                        </div>
                                        <div class="w-full md:w-[45%] mb-4">
                                            <label for="recurring_trips" class="block mb-2 text-gray-900">
                                                Recurring trips
                                            </label>
                                            <div class="relative mt-2">
                                                <input type="number" min="1" name="recurring_trips"
                                                    value="{{ old('recurring_trips') }}"
                                                    class="bg-gray-100 border border-gray-200 text-gray-900 text-base lg:text-lg rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 mt-2 block w-full p-2.5">
                                            </div>
                                            @error('recurring_trips')
                                                <div class="relative tooltip -bottom-4 group-hover:flex">
                                                    <div role="tooltip"
                                                        class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                                                        <p class="text-white leading-none text-sm lg:text-base">
                                                            {{ $message }}</p>
                                                    </div>
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6">
                        <div class="bg-white rounded-lg overflow-hidden shadow-3xl">
                            <h3 class="bg-primary text-white py-2 px-4">
                                @isset($postRidePage->meeting_drop_off_description_label)
                                    {{ $postRidePage->meeting_drop_off_description_label }}
                                @endisset
                            </h3>
                            <div class="bg-white p-4 space-y-3">
                                <label for="meeting" class="block mb-2 font-medium text-gray-900">
                                    @isset($postRidePage->meeting_drop_off_description_label)
                                        {{ $postRidePage->meeting_drop_off_description_label }}
                                    @endisset
                                </label>
                                <textarea id="meeting" rows="5" name="details"
                                    class="block p-2.5 w-full text-gray-900 bg-gray-100 rounded border border-gray-200 text-base lg:text-lg focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 mt-2"
                                    @isset($postRidePage->meeting_drop_off_description_placeholder)
                                    placeholder="{{ $postRidePage->meeting_drop_off_description_placeholder }}"
                                @endisset>{{ old('details', $ride->details) }}</textarea>
                                @error('details')
                                    <div class="relative tooltip -bottom-1 group-hover:flex">
                                        <div role="tooltip"
                                            class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                                            <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                        </div>
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mt-6">
                        <div class="bg-white rounded-lg overflow-hidden shadow-3xl">
                            <div class="bg-primary text-white py-2 px-4">
                                <label for="no_of_seats" class="font-medium text-lg lg:text-3xl font-FuturaMdCnBT mb-2">
                                    <h3>
                                        @isset($postRidePage->seats_label)
                                            {{ $postRidePage->seats_label }}
                                        @endisset
                                    </h3>
                                </label>
                            </div>
                            <div class="bg-white p-4">
                                <div class="flex items-center flex-wrap gap-2 mt-2">
                                    @for ($i = 1; $i <= 7; $i++)
                                        <div class="relative">
                                            <label for="number-of-seat-{{ $i }}">
                                                <input id="number-of-seat-{{ $i }}" name="seats"
                                                    type="radio" value="{{ $i }}" class="hidden"
                                                    {{ old('seats', $ride->seats) == $i ? 'checked' : '' }}
                                                    onchange="seat_selected(this)" data-parsley-required="true"
                                                    data-parsley-trigger="blur focusout change"
                                                    data-parsley-required-message="Please select the available seats."
                                                    data-parsley-errors-container="#parsley-seats-error">
                                                <img src="{{ old('seats', $ride->seats) >= $i ? asset('assets/seat-hover-1.png') : asset('assets/seat.png') }}"
                                                    class="w-10 h-10 mt-0.5 cursor-pointer seat-image seat-unselect-{{ $i }}"
                                                    alt="">
                                                <span
                                                    class="absolute left-4 top-3 seat-number seat-number-{{ $i }} {{ old('seats', $ride->seats) >= $i ? 'text-green-300' : '' }}">{{ $i }}</span>
                                            </label>
                                        </div>
                                    @endfor
                                </div>
                                @error('seats')
                                    <div class="relative tooltip -bottom-4 group-hover:flex">
                                        <div role="tooltip"
                                            class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                                            <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                        </div>
                                    </div>
                                @enderror
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 mt-6 gap-4">
                                    <div>
                                        <label for="pickup_location" class="text-gray-900 mb-2">
                                            @isset($postRidePage->seats_middle_label)
                                                {{ $postRidePage->seats_middle_label }}
                                            @endisset
                                        </label>
                                        <ul class="grid gap-2 grid-cols-2 mt-2">
                                            <li>
                                                <input type="radio" id="2-seats" name="middle_seats" value="2"
                                                    class="hidden peer"
                                                    {{ old('middle_seats', $ride->middle_seats) == '2' ? 'checked' : '' }}>
                                                <label for="2-seats"
                                                    class="inline-flex items-center justify-center w-full p-3 text-gray-800 bg-white border-2 border-gray-100 rounded cursor-pointer peer-checked:border-green-500 peer-checked:border-2 peer-checked:text-green-500 hover:border-2 hover:border-green-500">
                                                    <span class="font-medium text-base">
                                                        2 seats
                                                    </span>
                                                </label>
                                            </li>
                                            <li>
                                                <input type="radio" id="3-seats" name="middle_seats" value="3"
                                                    class="hidden peer"
                                                    {{ old('middle_seats', $ride->middle_seats) == '3' ? 'checked' : '' }}>
                                                <label for="3-seats"
                                                    class="inline-flex items-center justify-center w-full p-3 text-gray-800 bg-white border-2 border-gray-100 rounded cursor-pointer peer-checked:border-green-500 peer-checked:border-2 peer-checked:text-green-500 hover:border-2 hover:border-green-500">
                                                    <span class="font-medium text-base">3 seats</span>
                                                </label>
                                            </li>
                                        </ul>
                                        @error('middle_seats')
                                            <div class="relative tooltip -bottom-4 group-hover:flex">
                                                <div role="tooltip"
                                                    class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full rounded">
                                                    <p class="text-white leading-none text-sm lg:text-base">
                                                        {{ $message }}</p>
                                                </div>
                                            </div>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="pickup_location" class="text-gray-900 mb-2">
                                            @isset($postRidePage->seats_back_label)
                                                {{ $postRidePage->seats_back_label }}
                                            @endisset
                                        </label>
                                        <ul class="grid gap-2 grid-cols-2 mt-2">
                                            <li>
                                                <input type="radio" id="2-back_seats" name="back_seats" value="2"
                                                    class="hidden peer"
                                                    {{ old('back_seats', $ride->back_seats) == '2' ? 'checked' : '' }}>
                                                <label for="2-back_seats"
                                                    class="inline-flex items-center justify-center w-full p-3 text-gray-800 bg-white border-2 border-gray-100 rounded cursor-pointer peer-checked:border-green-500 peer-checked:border-2 peer-checked:text-green-500 hover:border-2 hover:border-green-500">
                                                    <span class="font-medium text-base">
                                                        2 seats
                                                    </span>
                                                </label>
                                            </li>
                                            <li>
                                                <input type="radio" id="3-back_seats" name="back_seats" value="3"
                                                    class="hidden peer"
                                                    {{ old('back_seats', $ride->back_seats) == '3' ? 'checked' : '' }}>
                                                <label for="3-back_seats"
                                                    class="inline-flex items-center justify-center w-full p-3 text-gray-800 bg-white border-2 border-gray-100 rounded cursor-pointer peer-checked:border-green-500 peer-checked:border-2 peer-checked:text-green-500 hover:border-2 hover:border-green-500">
                                                    <span class="font-medium text-base">3 seats</span>
                                                </label>
                                            </li>
                                        </ul>
                                        @error('back_seats')
                                            <div class="relative tooltip -bottom-4 group-hover:flex">
                                                <div role="tooltip"
                                                    class="relative tooltiptext after:left-6 -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full rounded">
                                                    <p class="text-white leading-none text-sm lg:text-base">
                                                        {{ $message }}</p>
                                                </div>
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6">
                        <div class="bg-white rounded-lg overflow-visible shadow-3xl">
                            <div class="bg-primary text-white py-2 px-4 rounded-t-lg">
                                <h3>
                                    @isset($postRidePage->price_payment_heading)
                                        {{ $postRidePage->price_payment_heading }}
                                    @endisset
                                </h3>
                            </div>
                            <div id="edit-ride-price-section" class="bg-white p-4 rounded-b-lg">
                                @if (empty($segmentsForPrice))
                                    <div id="single-price-block">
                                        <div>
                                            <label for="" class=" text-gray-700 font-medium">
                                                @isset($postRidePage->price_per_seat_label)
                                                    {{ $postRidePage->price_per_seat_label }}
                                                @endisset
                                            </label>
                                            <div class="relative mt-2">
                                                <span
                                                    class="absolute inset-y-0 start-0 flex items-center pl-2 pointer-events-none">
                                                    <svg fill="currentColor" width="800px" height="800px"
                                                        viewBox="0 0 32 32" class="w-5 h-5 text-gray-500"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M 15 3 L 15 5.09375 C 12.164063 5.570313 10 8.050781 10 11 C 10 12.777344 10.832031 14.148438 11.9375 15.03125 C 13.042969 15.914063 14.375 16.40625 15.625 16.90625 C 16.875 17.40625 18.042969 17.914063 18.8125 18.53125 C 19.582031 19.148438 20 19.773438 20 21 C 20 23.15625 18.207031 25 16 25 C 13.78125 25 12 23.21875 12 21 L 12 20 L 10 20 L 10 21 C 10 23.964844 12.164063 26.429688 15 26.90625 L 15 29 L 17 29 L 17 26.90625 C 19.84375 26.425781 22 23.925781 22 21 C 22 19.21875 21.167969 17.855469 20.0625 16.96875 C 18.957031 16.082031 17.625 15.5625 16.375 15.0625 C 15.125 14.5625 13.957031 14.082031 13.1875 13.46875 C 12.417969 12.855469 12 12.21875 12 11 C 12 8.808594 13.785156 7 16 7 C 18.21875 7 20 8.78125 20 11 L 20 12 L 22 12 L 22 11 C 22 8.035156 19.835938 5.570313 17 5.09375 L 17 3 Z" />
                                                    </svg>
                                                </span>
                                                <input type="number" step="any" name="price" id="priceData0"
                                                    placeholder=""
                                                    value="{{ old('price', $ride->defaultRideDetail[0]->price) }}"
                                                    class="bg-gray-100 border border-gray-200 pl-7 text-gray-900 text-base lg:text-lg rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 block w-full p-2.5 mt-2 " />
                                            </div>
                                            @error('price')
                                                <div class="relative tooltip -bottom-4 group-hover:flex">
                                                    <div role="tooltip"
                                                        class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                                                        <p class="text-white leading-none text-sm lg:text-base">
                                                            {{ $message }}</p>
                                                    </div>
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div id="stops-segment-prices-dynamic" style="display: none;"
                                        data-bookings-readonly="0">
                                        <p class="text-gray-700 font-medium mt-2 mb-1">Full route price</p>
                                        <div class="relative">
                                            <div class="relative mt-2 mb-2">
                                                <span
                                                    class="absolute inset-y-0 start-0 flex items-center pl-2 pointer-events-none">
                                                    <svg fill="currentColor" width="800px" height="800px"
                                                        viewBox="0 0 32 32" class="w-5 h-5 text-gray-500"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M 15 3 L 15 5.09375 C 12.164063 5.570313 10 8.050781 10 11 C 10 12.777344 10.832031 14.148438 11.9375 15.03125 C 13.042969 15.914063 14.375 16.40625 15.625 16.90625 C 16.875 17.40625 18.042969 17.914063 18.8125 18.53125 C 19.582031 19.148438 20 19.773438 20 21 C 20 23.15625 18.207031 25 16 25 C 13.78125 25 12 23.21875 12 21 L 12 20 L 10 20 L 10 21 C 10 23.964844 12.164063 26.429688 15 26.90625 L 15 29 L 17 29 L 17 26.90625 C 19.84375 26.425781 22 23.925781 22 21 C 22 19.21875 21.167969 17.855469 20.0625 16.96875 C 18.957031 16.082031 17.625 15.5625 16.375 15.0625 C 15.125 14.5625 13.957031 14.082031 13.1875 13.46875 C 12.417969 12.855469 12 12.21875 12 11 C 12 8.808594 13.785156 7 16 7 C 18.21875 7 20 8.78125 20 11 L 20 12 L 22 12 L 22 11 C 22 8.035156 19.835938 5.570313 17 5.09375 L 17 3 Z" />
                                                    </svg>
                                                </span>
                                                <input type="number" step="any" id="priceData0DynamicInput"
                                                    placeholder=""
                                                    value="{{ old('price', $ride->defaultRideDetail[0]->price) }}"
                                                    class="full-route-price-input bg-gray-100 border border-gray-200 pl-7 text-gray-900 text-base lg:text-lg rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 block w-full p-2.5 mt-2 " />
                                            </div>
                                            <div id="full-route-tooltip-container-dynamic"
                                                class="absolute hidden top-full left-1/2 -translate-x-1/2 mt-1 z-10">
                                                <div class="tooltip-error">
                                                    The full-route price can't be higher than the total of all route
                                                    sections.<br>
                                                    You can lower the full-route price or adjust section prices.
                                                </div>
                                            </div>
                                        </div>
                                        <p class="text-gray-700 font-medium mt-2 mb-1">Total price (all sections)</p>
                                        <div class="relative mt-2 mb-4">
                                            <span
                                                class="absolute inset-y-0 start-0 flex items-center pl-2 pointer-events-none text-gray-500 font-medium">
                                                <svg fill="currentColor" width="800px" height="800px"
                                                    viewBox="0 0 32 32" class="w-5 h-5 text-gray-500"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M 15 3 L 15 5.09375 C 12.164063 5.570313 10 8.050781 10 11 C 10 12.777344 10.832031 14.148438 11.9375 15.03125 C 13.042969 15.914063 14.375 16.40625 15.625 16.90625 C 16.875 17.40625 18.042969 17.914063 18.8125 18.53125 C 19.582031 19.148438 20 19.773438 20 21 C 20 23.15625 18.207031 25 16 25 C 13.78125 25 12 23.21875 12 21 L 12 20 L 10 20 L 10 21 C 10 23.964844 12.164063 26.429688 15 26.90625 L 15 29 L 17 29 L 17 26.90625 C 19.84375 26.425781 22 23.925781 22 21 C 22 19.21875 21.167969 17.855469 20.0625 16.96875 C 18.957031 16.082031 17.625 15.5625 16.375 15.0625 C 15.125 14.5625 13.957031 14.082031 13.1875 13.46875 C 12.417969 12.855469 12 12.21875 12 11 C 12 8.808594 13.785156 7 16 7 C 18.21875 7 20 8.78125 20 11 L 20 12 L 22 12 L 22 11 C 22 8.035156 19.835938 5.570313 17 5.09375 L 17 3 Z" />
                                                </svg>
                                            </span>
                                            <input type="text" id="segment-total-price-input-dynamic" readonly
                                                placeholder="0.00" value="0.00"
                                                class="bg-gray-200 border border-gray-300 pl-7 text-gray-700 text-base lg:text-lg rounded block w-full p-2.5 mt-2 cursor-default" />
                                        </div>
                                        <div id="segment-price-rows-dynamic"></div>
                                    </div>
                                @else
                                    <div id="stops-segment-prices-container" data-bookings-readonly="0">
                                        <label for="" class="text-gray-700 font-medium">
                                            @isset($postRidePage->price_per_seat_label)
                                                {{ $postRidePage->price_per_seat_label }} (by Route Section)
                                            @else
                                                Price per Seat (by Route Section)
                                            @endisset
                                        </label>
                                        @php
                                            $totalSegmentPrice = collect($segmentsForPrice)->sum(function ($s) {
                                                return is_numeric($s['price']) ? (float) $s['price'] : 0;
                                            });
                                            $fullRoutePrice =
                                                count($segmentsForPrice) > 0
                                                    ? min(
                                                        (float) ($segmentsForPrice[0]['price'] ?? 0),
                                                        $totalSegmentPrice,
                                                    )
                                                    : $totalSegmentPrice;
                                            if ($totalSegmentPrice <= 0) {
                                                $fullRoutePrice =
                                                    count($segmentsForPrice) > 0
                                                        ? (float) ($segmentsForPrice[0]['price'] ?? 0)
                                                        : 0;
                                            }
                                        @endphp
                                        <p class="text-gray-700 font-medium mt-2 mb-1">Full route price</p>
                                        <div class="relative">
                                            <div class="relative mt-2 mb-2">
                                                <span
                                                    class="absolute inset-y-0 start-0 flex items-center pl-2 pointer-events-none">
                                                    <svg fill="currentColor" width="800px" height="800px"
                                                        viewBox="0 0 32 32" class="w-5 h-5 text-gray-500"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M 15 3 L 15 5.09375 C 12.164063 5.570313 10 8.050781 10 11 C 10 12.777344 10.832031 14.148438 11.9375 15.03125 C 13.042969 15.914063 14.375 16.40625 15.625 16.90625 C 16.875 17.40625 18.042969 17.914063 18.8125 18.53125 C 19.582031 19.148438 20 19.773438 20 21 C 20 23.15625 18.207031 25 16 25 C 13.78125 25 12 23.21875 12 21 L 12 20 L 10 20 L 10 21 C 10 23.964844 12.164063 26.429688 15 26.90625 L 15 29 L 17 29 L 17 26.90625 C 19.84375 26.425781 22 23.925781 22 21 C 22 19.21875 21.167969 17.855469 20.0625 16.96875 C 18.957031 16.082031 17.625 15.5625 16.375 15.0625 C 15.125 14.5625 13.957031 14.082031 13.1875 13.46875 C 12.417969 12.855469 12 12.21875 12 11 C 12 8.808594 13.785156 7 16 7 C 18.21875 7 20 8.78125 20 11 L 20 12 L 22 12 L 22 11 C 22 8.035156 19.835938 5.570313 17 5.09375 L 17 3 Z" />
                                                    </svg>
                                                </span>
                                                <input type="number" step="any" name="price" id="priceData0"
                                                    placeholder="" value="{{ $fullRoutePrice }}"
                                                    class="full-route-price-input bg-gray-100 border border-gray-200 pl-7 text-gray-900 text-base lg:text-lg rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 block w-full p-2.5 mt-2 " />
                                            </div>
                                            <div id="full-route-tooltip-container"
                                                class="absolute hidden top-full left-1/2 -translate-x-1/2 mt-1 z-10">
                                                <div class="tooltip-error">
                                                    The full-route price can't be higher than the total of all route
                                                    sections.<br>
                                                    You can lower the full-route price or adjust section prices.
                                                </div>
                                            </div>
                                        </div>
                                        <p class="text-gray-700 font-medium mt-2 mb-1">Total price (all sections)</p>
                                        <div class="relative mt-2 mb-4">
                                            <span
                                                class="absolute inset-y-0 start-0 flex items-center pl-2 pointer-events-none text-gray-500 font-medium">
                                                <svg fill="currentColor" width="800px" height="800px"
                                                    viewBox="0 0 32 32" class="w-5 h-5 text-gray-500"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M 15 3 L 15 5.09375 C 12.164063 5.570313 10 8.050781 10 11 C 10 12.777344 10.832031 14.148438 11.9375 15.03125 C 13.042969 15.914063 14.375 16.40625 15.625 16.90625 C 16.875 17.40625 18.042969 17.914063 18.8125 18.53125 C 19.582031 19.148438 20 19.773438 20 21 C 20 23.15625 18.207031 25 16 25 C 13.78125 25 12 23.21875 12 21 L 12 20 L 10 20 L 10 21 C 10 23.964844 12.164063 26.429688 15 26.90625 L 15 29 L 17 29 L 17 26.90625 C 19.84375 26.425781 22 23.925781 22 21 C 22 19.21875 21.167969 17.855469 20.0625 16.96875 C 18.957031 16.082031 17.625 15.5625 16.375 15.0625 C 15.125 14.5625 13.957031 14.082031 13.1875 13.46875 C 12.417969 12.855469 12 12.21875 12 11 C 12 8.808594 13.785156 7 16 7 C 18.21875 7 20 8.78125 20 11 L 20 12 L 22 12 L 22 11 C 22 8.035156 19.835938 5.570313 17 5.09375 L 17 3 Z" />
                                                </svg>
                                            </span>
                                            <input type="text" id="segment-total-price-input" readonly
                                                placeholder="0.00" value="{{ number_format($totalSegmentPrice, 2) }}"
                                                class="bg-gray-200 border border-gray-300 pl-7 text-gray-700 text-base lg:text-lg rounded block w-full p-2.5 mt-2 cursor-default" />
                                        </div>
                                        @foreach ($segmentsForPrice as $segIdx => $seg)
                                            <div class="mt-4 segment-price-row">
                                                <p class="text-gray-700 font-medium mb-1 segment-label">
                                                    {{ $seg['from'] }} → {{ $seg['to'] }}</p>
                                                <div class="relative mt-2">
                                                    <span
                                                        class="absolute inset-y-0 start-0 flex items-center pl-2 pointer-events-none">
                                                        <svg fill="currentColor" width="800px" height="800px"
                                                            viewBox="0 0 32 32" class="w-5 h-5 text-gray-500"
                                                            xmlns="http://www.w3.org/2000/svg">
                                                            <path
                                                                d="M 15 3 L 15 5.09375 C 12.164063 5.570313 10 8.050781 10 11 C 10 12.777344 10.832031 14.148438 11.9375 15.03125 C 13.042969 15.914063 14.375 16.40625 15.625 16.90625 C 16.875 17.40625 18.042969 17.914063 18.8125 18.53125 C 19.582031 19.148438 20 19.773438 20 21 C 20 23.15625 18.207031 25 16 25 C 13.78125 25 12 23.21875 12 21 L 12 20 L 10 20 L 10 21 C 10 23.964844 12.164063 26.429688 15 26.90625 L 15 29 L 17 29 L 17 26.90625 C 19.84375 26.425781 22 23.925781 22 21 C 22 19.21875 21.167969 17.855469 20.0625 16.96875 C 18.957031 16.082031 17.625 15.5625 16.375 15.0625 C 15.125 14.5625 13.957031 14.082031 13.1875 13.46875 C 12.417969 12.855469 12 12.21875 12 11 C 12 8.808594 13.785156 7 16 7 C 18.21875 7 20 8.78125 20 11 L 20 12 L 22 12 L 22 11 C 22 8.035156 19.835938 5.570313 17 5.09375 L 17 3 Z" />
                                                        </svg>
                                                    </span>
                                                    <input type="number" step="any" name="price_spot_display[]"
                                                        placeholder="" value="{{ $seg['price'] }}"
                                                        class="bg-gray-100 border border-gray-200 pl-7 text-gray-900 text-base lg:text-lg rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 block w-full p-2.5 mt-2 " />
                                                </div>
                                                @error('price_spot_display.' . $segIdx)
                                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                                <div class="mt-6">
                                    <label for="" class="block mb-2 font-medium text-gray-900">
                                        @isset($postRidePage->payment_methods_label)
                                            {{ $postRidePage->payment_methods_label }}
                                        @endisset
                                    </label>
                                    <div class="space-y-2 mt-2">
                                        @isset($postRidePage->payment_methods_option1->features_setting_id)
                                            <div class="flex items-center space-x-1 md:space-x-2 mb-2 mr-2 lg:mr-2">
                                                <input id="cash" name="payment_method" type="radio"
                                                    value="{{ $postRidePage->payment_methods_option1->features_setting_id }}"
                                                    {{ old('payment_method', $ride->payment_method) == $postRidePage->payment_methods_option1->features_setting_id ? 'checked' : '' }}
                                                    class="h-5 w-5 rounded bg-white border border-gray-200 cursor-pointer text-indigo-600 focus:ring-indigo-600">
                                                <label for="cash"
                                                    class="ml-3 font-normal text-gray-900 flex items-center space-x-1">

                                                    <span class="">
                                                        {{ $postRidePage->payment_methods_option1->name }}
                                                    </span>
                                                    <span class="inline-flex cursor-help payment-method-tooltip"
                                                        data-tippy-content="{{ $postRidePage->payment_methods_option1_tooltip ?? '' }}">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                            fill="currentColor" class="bi bi-info-circle-fill text-black"
                                                            viewBox="0 0 16 16">
                                                            <path
                                                                d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
                                                        </svg>
                                                    </span>
                                                </label>
                                            </div>
                                        @endisset
                                        @isset($postRidePage->payment_methods_option2->features_setting_id)
                                            <div class="flex items-center space-x-1 md:space-x-2 mb-2 mr-2 lg:mr-2">
                                                <input id="online" name="payment_method" type="radio"
                                                    value="{{ $postRidePage->payment_methods_option2->features_setting_id }}"
                                                    {{ old('payment_method', $ride->payment_method) == $postRidePage->payment_methods_option2->features_setting_id ? 'checked' : '' }}
                                                    class="h-5 w-5 rounded bg-white border border-gray-200 cursor-pointer text-indigo-600 focus:ring-indigo-600">
                                                <label for="online"
                                                    class="ml-3 font-normal text-gray-900 flex items-center space-x-1">

                                                    <span class="">
                                                        {{ $postRidePage->payment_methods_option2->name }}
                                                    </span>
                                                    <span class="inline-flex cursor-help payment-method-tooltip"
                                                        data-tippy-content="{{ $postRidePage->payment_methods_option2_tooltip ?? '' }}">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                            fill="currentColor" class="bi bi-info-circle-fill text-black"
                                                            viewBox="0 0 16 16">
                                                            <path
                                                                d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
                                                        </svg>
                                                    </span>
                                                </label>
                                            </div>
                                        @endisset
                                        @isset($postRidePage->payment_methods_option3->features_setting_id)
                                            <div class="flex items-center space-x-1 md:space-x-2 mb-2 mr-2 lg:mr-2">
                                                <input id="secured" name="payment_method" type="radio"
                                                    value="{{ $postRidePage->payment_methods_option3->features_setting_id }}"
                                                    {{ old('payment_method', $ride->payment_method) == $postRidePage->payment_methods_option3->features_setting_id ? 'checked' : '' }}
                                                    class="h-5 w-5 rounded border border-gray-200 bg-white cursor-pointer text-indigo-600 focus:ring-indigo-600">
                                                <label for="secured"
                                                    class="ml-3 font-normal text-gray-900 flex items-center space-x-1">
                                                    <span class="">
                                                        {{ $postRidePage->payment_methods_option3->name }}
                                                    </span>
                                                    <span class="inline-flex cursor-help payment-method-tooltip"
                                                        data-tippy-content="{{ $postRidePage->payment_methods_option3_tooltip ?? '' }}">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                            fill="currentColor" class="bi bi-info-circle-fill text-black"
                                                            viewBox="0 0 16 16">
                                                            <path
                                                                d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
                                                        </svg>
                                                    </span>
                                                </label>
                                            </div>
                                        @endisset
                                    </div>
                                    @error('payment_method')
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
                    </div>

                    <div class="mt-6">
                        <div class="bg-white rounded-lg overflow-hidden shadow-3xl">
                            <div class="bg-primary text-white py-2 px-4">
                                <h3>
                                    @isset($postRidePage->booking_label)
                                        {{ $postRidePage->booking_label }}
                                    @endisset
                                </h3>
                            </div>
                            <div class="bg-white p-4">
                                <ul class="grid w-full gap-6 md:grid-cols-2">
                                    @isset($postRidePage->booking_option1->features_setting_id)
                                        <li>
                                            <input type="radio" id="instant-booking" name="booking_method"
                                                value="{{ $postRidePage->booking_option1->features_setting_id }}"
                                                {{ old('booking_method', $ride->booking_method) == $postRidePage->booking_option1->features_setting_id ? 'checked' : '' }}
                                                class="hidden peer">
                                            <label for="instant-booking"
                                                class="inline-flex items-center space-x-3 w-full p-4 text-gray-800 bg-white border-2 border-gray-100 rounded cursor-pointer peer-checked:border-green-500 peer-checked:border-2 peer-checked:text-green-500 hover:border-2 hover:border-green-500">
                                                <img class="w-12 h-12" src="{{ asset('assets/instant.png') }}"
                                                    alt="">
                                                <span class="font-medium text-xl">
                                                    {{ $postRidePage->booking_option1->name }}
                                                </span>
                                            </label>
                                        </li>
                                    @endisset
                                    @isset($postRidePage->booking_option2->features_setting_id)
                                        <li>
                                            <input type="radio" id="manual-approval" name="booking_method"
                                                value="{{ $postRidePage->booking_option2->features_setting_id }}"
                                                {{ old('booking_method', $ride->booking_method) == $postRidePage->booking_option2->features_setting_id ? 'checked' : '' }}
                                                class="hidden peer">
                                            <label for="manual-approval"
                                                class="inline-flex items-center space-x-3 w-full p-4 text-gray-800 bg-white border-2 border-gray-100 rounded cursor-pointer peer-checked:border-green-500 peer-checked:border-2 peer-checked:text-green-500 hover:border-2 hover:border-green-500">
                                                <img class="w-12 h-12" src="{{ asset('assets/manual.png') }}"
                                                    alt="">
                                                <span class="font-medium text-xl">
                                                    {{ $postRidePage->booking_option2->name }}
                                                </span>
                                            </label>
                                        </li>
                                    @endisset
                                </ul>
                                @error('booking_method')
                                    <div class="relative tooltip -bottom-4 group-hover:flex">
                                        <div role="tooltip"
                                            class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                                            <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                        </div>
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!--Vehicle label-->
                    <div class="mt-6">
                        <div class="bg-white rounded-lg overflow-hidden shadow-3xl">
                            <div class="bg-primary text-white py-2 px-4">
                                <h3>
                                    @isset($postRidePage->vehicle_label)
                                        {{ $postRidePage->vehicle_label }}
                                    @endisset
                                </h3>
                            </div>
                            <div class="bg-white p-4">
                                <div class="flex flex-col sm:flex-col md:flex-row justify-between mb-4">
                                    <div>
                                        <input id="skip" type="checkbox" name="skip_vehicle" value="1"
                                            {{ old('skip_vehicle', $ride->skip_vehicle) == '1' ? 'checked' : '' }}
                                            class="w-4 h-4 text-blue-600 cursor-pointer bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                                        <label for="skip" class="ml-2  text-gray-900">
                                            @isset($postRidePage->skip_label)
                                                {{ $postRidePage->skip_label }}
                                            @endisset
                                        </label>
                                    </div>
                                    <div>
                                        <input id="add" type="checkbox" name="add_vehicle" value="1"
                                            {{ old('add_vehicle', $ride->add_vehicle) == '1' ? 'checked' : '' }}
                                            class="w-4 h-4 text-blue-600 cursor-pointer bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                                        <label for="add" class="ml-2  text-gray-900">
                                            @isset($postRidePage->add_vehicle_label)
                                                {{ $postRidePage->add_vehicle_label }}
                                            @endisset
                                        </label>
                                    </div>
                                    @php
                                        // Check "Existing" when ride was saved with an existing vehicle (added_vehicle=1 or vehicle_id set)
                                        $savedAsExisting =
                                            !empty($ride->vehicle_id) || (string) ($ride->added_vehicle ?? '') === '1';
                                        $savedAsAddNew = (string) ($ride->add_vehicle ?? '') === '1';
                                        $defaultAddedVehicle = $savedAsExisting
                                            ? '1'
                                            : ($savedAsAddNew
                                                ? '0'
                                                : ($vehicles->firstWhere('primary_vehicle', '1')
                                                    ? '1'
                                                    : '0'));
                                    @endphp
                                    <div class="{{ $vehicles->count() > 0 ? '' : 'hidden' }}">
                                        <input id="added" type="checkbox" name="added_vehicle" value="1"
                                            {{ old('added_vehicle', $defaultAddedVehicle) === '1' ? 'checked' : '' }}
                                            class="w-4 h-4 text-blue-600 cursor-pointer bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                                        <label for="added" class="ml-2  text-gray-900">
                                            Existing
                                        </label>
                                    </div>
                                </div>
                                @error('vehicle_selection')
                                    <div class="relative tooltip bottom-0 group-hover:flex">
                                        <div role="tooltip"
                                            class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                                            <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                        </div>
                                    </div>
                                @enderror
                                <div id="skipVehicle">
                                    <div class="mt-6 grid grid-cols-1 gap-6 md:grid-cols-4">
                                        <div class="md:col-span-2">
                                            <label for="make" class="text-gray-900 mb-2">
                                                @isset($postRidePage->make_label)
                                                    {{ $postRidePage->make_label }}
                                                @endisset
                                            </label>
                                            <div class="mt-2">
                                                <input type="text" name="make" id=""
                                                    @if ($errors->count() > 0) value="{{ old('make', $ride->make) }}"
                                                @else
                                                    value="{{ $ride->make }}" @endif
                                                    class="bg-gray-100 border border-gray-200 text-gray-900 text-base lg:text-lg rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 mt-2 block w-full p-2.5">
                                            </div>
                                            @error('make')
                                                <div class="relative tooltip -bottom-4 group-hover:flex">
                                                    <div role="tooltip"
                                                        class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                                                        <p class="text-white leading-none text-sm lg:text-base">
                                                            {{ $message }}</p>
                                                    </div>
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="md:col-span-2">
                                            <label for="modal" class="text-gray-900 mb-2">
                                                @isset($postRidePage->model_label)
                                                    {{ $postRidePage->model_label }}
                                                @endisset
                                            </label>
                                            <div class="mt-2">
                                                <input type="text" name="model" id=""
                                                    @if ($errors->count() > 0) value="{{ old('model', $ride->model) }}"
                                                @else
                                                    value="{{ $ride->model }}" @endif
                                                    class="bg-gray-100 border border-gray-200 text-gray-900 text-base lg:text-lg rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 mt-2 block w-full p-2.5">
                                            </div>
                                            @error('model')
                                                <div class="relative tooltip -bottom-4 group-hover:flex">
                                                    <div role="tooltip"
                                                        class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                                                        <p class="text-white leading-none text-sm lg:text-base">
                                                            {{ $message }}</p>
                                                    </div>
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="md:col-span-2">
                                            <label for="type" class="text-gray-900 mb-2">
                                                @isset($postRidePage->type_label)
                                                    {{ $postRidePage->type_label }}
                                                @endisset
                                            </label>
                                            <div class="mt-2">
                                                <select id="type" name="vehicle_type"
                                                    class="bg-gray-100 border border-gray-200 text-gray-900 text-base lg:text-lg rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 mt-2 block w-full p-2.5">
                                                    <option
                                                        {{ old('vehicle_type', $ride->vehicle_type) == '' ? 'selected' : '' }}
                                                        value="">
                                                        @isset($postRidePage->vehicle_type_placeholder)
                                                            {{ $postRidePage->vehicle_type_placeholder }}
                                                        @endisset
                                                    </option>

                                                    <option
                                                        value="{{ $postRidePage->vehicle_type_convertible_value ?? 'Convertable' }}"
                                                        {{ old('vehicle_type', $ride->vehicle_type) === ($postRidePage->vehicle_type_convertible_value ?? 'Convertable') ? 'selected' : '' }}>
                                                        {{ $postRidePage->vehicle_type_convertible_text ?? 'Convertable' }}
                                                    </option>
                                                    <option
                                                        value="{{ $postRidePage->vehicle_type_coupe_value ?? 'Coupe' }}"
                                                        {{ old('vehicle_type', $ride->vehicle_type) === ($postRidePage->vehicle_type_coupe_value ?? 'Coupe') ? 'selected' : '' }}>
                                                        {{ $postRidePage->vehicle_type_coupe_text ?? 'Coupe' }}
                                                    </option>
                                                    <option
                                                        value="{{ $postRidePage->vehicle_type_hatchback_value ?? 'Hatchback' }}"
                                                        {{ old('vehicle_type', $ride->vehicle_type) === ($postRidePage->vehicle_type_hatchback_value ?? 'Hatchback') ? 'selected' : '' }}>
                                                        {{ $postRidePage->vehicle_type_hatchback_text ?? 'Hatchback' }}
                                                    </option>
                                                    <option
                                                        value="{{ $postRidePage->vehicle_type_minivan_value ?? 'Minivan' }}"
                                                        {{ old('vehicle_type', $ride->vehicle_type) === ($postRidePage->vehicle_type_minivan_value ?? 'Minivan') ? 'selected' : '' }}>
                                                        {{ $postRidePage->vehicle_type_minivan_text ?? 'Minivan' }}
                                                    </option>
                                                    <option
                                                        value="{{ $postRidePage->vehicle_type_sedan_value ?? 'Sedan' }}"
                                                        {{ old('vehicle_type', $ride->vehicle_type) === ($postRidePage->vehicle_type_sedan_value ?? 'Sedan') ? 'selected' : '' }}>
                                                        {{ $postRidePage->vehicle_type_sedan_text ?? 'Sedan' }}
                                                    </option>
                                                    <option value="{{ $postRidePage->vehicle_type_station_wagon_value }}"
                                                        {{ old('vehicle_type', $ride->vehicle_type) === ($postRidePage->vehicle_type_station_wagon_value ?? 'Station wagon') ? 'selected' : '' }}>
                                                        {{ $postRidePage->vehicle_type_station_wagon_text ?? 'Station wagon' }}
                                                    </option>
                                                    <option value="{{ $postRidePage->vehicle_type_suv_value ?? 'SUV' }}"
                                                        {{ old('vehicle_type', $ride->vehicle_type) === ($postRidePage->vehicle_type_suv_value ?? 'SUV') ? 'selected' : '' }}>
                                                        {{ $postRidePage->vehicle_type_suv_text ?? 'SUV' }}
                                                    </option>
                                                    <option
                                                        value="{{ $postRidePage->vehicle_type_truck_value ?? 'Truck' }}"
                                                        {{ old('vehicle_type', $ride->vehicle_type) === ($postRidePage->vehicle_type_truck_value ?? 'Truck') ? 'selected' : '' }}>
                                                        {{ $postRidePage->vehicle_type_truck_text ?? 'Truck' }}
                                                    </option>
                                                    <option value="{{ $postRidePage->vehicle_type_van_value ?? 'Van' }}"
                                                        {{ old('vehicle_type', $ride->vehicle_type) === ($postRidePage->vehicle_type_van_value ?? 'Van') ? 'selected' : '' }}>
                                                        {{ $postRidePage->vehicle_type_van_text ?? 'Van' }}
                                                    </option>
                                                </select>
                                            </div>
                                            @error('vehicle_type')
                                                <div class="relative tooltip -bottom-4 group-hover:flex">
                                                    <div role="tooltip"
                                                        class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                                                        <p class="text-white leading-none text-sm lg:text-base">
                                                            {{ $message }}</p>
                                                    </div>
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="">
                                            <label for="type" class="text-gray-900 mb-2">
                                                @isset($postRidePage->year_label)
                                                    {{ $postRidePage->year_label }}
                                                @endisset
                                            </label>
                                            <div class="mt-2">
                                                <input type="text" name="year" id="" placeholder=""
                                                    @if ($errors->count() > 0) value="{{ old('year', $ride->year) }}"
                                                @else
                                                    value="{{ $ride->year }}" @endif
                                                    class="bg-gray-100 border border-gray-200 text-gray-900 text-base lg:text-lg rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 mt-2 block w-full p-2.5">
                                            </div>
                                            @error('year')
                                                <div class="relative tooltip -bottom-4 group-hover:flex">
                                                    <div role="tooltip"
                                                        class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full rounded">
                                                        <p class="text-white leading-none text-sm lg:text-base">
                                                            {{ $message }}</p>
                                                    </div>
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="">
                                            <label for="modal" class="text-gray-900 mb-2">
                                                @isset($postRidePage->color_label)
                                                    {{ $postRidePage->color_label }}
                                                @endisset
                                            </label>
                                            <div class="mt-2">
                                                <input type="text" name="color" id="" placeholder=""
                                                    @if ($errors->count() > 0) value="{{ old('color', $ride->color) }}"
                                                @else
                                                    value="{{ $ride->color }}" @endif
                                                    class="bg-gray-100 border border-gray-200 text-gray-900 text-base lg:text-lg rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 mt-2 block w-full p-2.5">
                                            </div>
                                            @error('color')
                                                <div class="relative tooltip -bottom-4 group-hover:flex">
                                                    <div role="tooltip"
                                                        class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full rounded">
                                                        <p class="text-white leading-none text-sm lg:text-base">
                                                            {{ $message }}</p>
                                                    </div>
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="md:col-span-2">
                                            <label for="modal" class="text-gray-900 mb-2">
                                                @isset($postRidePage->liscense_label)
                                                    {{ $postRidePage->liscense_label }}
                                                @endisset
                                            </label>
                                            <div class="mt-2">
                                                <input type="text" name="license_no" id="" placeholder=""
                                                    @if ($errors->count() > 0) value="{{ old('license_no', $ride->license_no) }}"
                                                @else
                                                    value="{{ $ride->license_no }}" @endif
                                                    class="bg-gray-100 border border-gray-200 text-gray-900 text-base lg:text-lg rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 mt-2 block w-full p-2.5">
                                            </div>
                                            @error('license_no')
                                                <div class="relative tooltip -bottom-4 group-hover:flex">
                                                    <div role="tooltip"
                                                        class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                                                        <p class="text-white leading-none text-sm lg:text-base">
                                                            {{ $message }}</p>
                                                    </div>
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="md:col-span-4">
                                            <label for="modal" class="text-gray-900 mb-2">Fuel</label>
                                            <div class=" flex items-center">
                                                @isset($postRidePage->electric_car_label)
                                                    <div class="flex items-center space-x-1.5 lg:space-x-3 mb-2 mr-2 lg:mr-2">
                                                        <input id="" name="car_type" type="radio"
                                                            value="{{ $postRidePage->electric_car_label }}"
                                                            {{ old('car_type', $ride->car_type) == $postRidePage->electric_car_label ? 'checked' : '' }}
                                                            class="h-5 w-5 border-gray-300 bg-gray-200 cursor-pointer text-indigo-600 focus:ring-indigo-600">
                                                        <label for="" class="block text-gray-900">
                                                            {{ $postRidePage->electric_car_label }}
                                                        </label>
                                                    </div>
                                                @endisset
                                                @isset($postRidePage->hybrid_car_label)
                                                    <div class="flex items-center space-x-1.5 lg:space-x-3 mb-2 mr-2 lg:mr-2">
                                                        <input id="" name="car_type" type="radio"
                                                            value="{{ $postRidePage->hybrid_car_label }}"
                                                            {{ old('car_type', $ride->car_type) == $postRidePage->hybrid_car_label ? 'checked' : '' }}
                                                            class="h-5 w-5 border-gray-300 bg-gray-200 cursor-pointer text-indigo-600 focus:ring-indigo-600">
                                                        <label for="" class="block text-gray-900">
                                                            {{ $postRidePage->hybrid_car_label }}
                                                        </label>
                                                    </div>
                                                @endisset
                                                @isset($postRidePage->gas_car_label)
                                                    <div class="flex items-center space-x-1.5 lg:space-x-3 mb-2 mr-2 lg:mr-2">
                                                        <input id="" name="car_type" type="radio"
                                                            value="{{ $postRidePage->gas_car_label }}"
                                                            {{ old('car_type', $ride->car_type) == $postRidePage->gas_car_label ? 'checked' : '' }}
                                                            class="h-5 w-5 border-gray-300 bg-gray-200 cursor-pointer text-indigo-600 focus:ring-indigo-600">
                                                        <label for="" class="block text-gray-900">
                                                            {{ $postRidePage->gas_car_label }}
                                                        </label>
                                                    </div>
                                                @endisset
                                            </div>
                                            @error('car_type')
                                                <div class="relative tooltip -bottom-4 group-hover:flex">
                                                    <div role="tooltip"
                                                        class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                                                        <p class="text-white leading-none text-sm lg:text-base">
                                                            {{ $message }}</p>
                                                    </div>
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="md:col-span-4">
                                            <div id="">
                                                <label for="car-photo" class="text-gray-900 mb-2">
                                                    Car Photo
                                                </label>
                                                <div class="md:col-span-2 mt-2">
                                                    <label for="dropzone-file"
                                                        class="flex flex-col items-center justify-center w-full h-auto border-2 border-gray-300 border-dashed rounded cursor-pointer bg-gray-100 hover:bg-gray-100">
                                                        <div
                                                            class="flex flex-col items-center justify-center pt-5 pb-6 p-4">
                                                            @if (session('uploaded_image'))
                                                                <img id="profile-image"
                                                                    class="w-40 h-40 object-contain mb-4 cursor-pointer"
                                                                    src="{{ asset('car_images/' . session('uploaded_image')) }}"
                                                                    alt="Uploaded Image">
                                                            @elseif ($ride->car_image)
                                                                <img id="profile-image"
                                                                    class="w-40 h-40 object-contain mb-4 cursor-pointer"
                                                                    src="{{ $ride->car_image }}">
                                                            @else
                                                                <img id="profile-image"
                                                                    class="w-12 h-12 object-contain mb-4 cursor-pointer"
                                                                    src="{{ asset('assets/image-placeholder.png') }}">
                                                            @endif
                                                            <p class="text-sm lg:text-lg text-gray-900"> Upload car photo.
                                                                <span class="font-semibold text-primary"> Choose
                                                                    file</span>
                                                            </p>
                                                            <p class="text-sm lg:text-base text-gray-900 font-normal">
                                                                Allowed formats: JPG, JPEG. PNG, and GIF. 10MB max.
                                                            </p>
                                                        </div>
                                                        <input id="dropzone-file" name="image" type="file"
                                                            onchange="previewImage(this)" class="hidden" />
                                                        @if (session('uploaded_image'))
                                                            <input type="hidden" name="existing_image"
                                                                value="{{ session('uploaded_image') }}">
                                                        @elseif ($ride->car_image)
                                                            @php
                                                                $imageName = basename($ride->car_image);
                                                            @endphp
                                                            <input type="hidden" name="existing_image"
                                                                value="{{ $imageName }}">
                                                        @endif
                                                        @error('image')
                                                            @if ($message !== 'The image is not uploaded yet')
                                                                <p class="text-red-500 text-base">{{ $message }}</p>
                                                            @endif
                                                        @enderror
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="showVehicles" class="md:col-span-2 group">
                                    <label for="vehicle_id_select" class="text-gray-900 mb-2">
                                        Select vehicle <span class="text-red-500">*</span>
                                    </label>
                                    <div class="mt-2">
                                        @php
                                            $selectedVehicleId = old('vehicle_id', $ride->vehicle_id);
                                            // Treat empty string, null, and 0 as "no selection"
                                            if (
                                                $selectedVehicleId === '' ||
                                                $selectedVehicleId === null ||
                                                $selectedVehicleId === 0
                                            ) {
                                                $primaryVehicle = $vehicles->firstWhere('primary_vehicle', '1');
                                                $selectedVehicleId = $primaryVehicle ? $primaryVehicle->id : null;
                                            }
                                            // Normalize to string for reliable option comparison (int/string from DB)
                                            $selectedVehicleIdStr =
                                                $selectedVehicleId !== null && $selectedVehicleId !== ''
                                                    ? (string) $selectedVehicleId
                                                    : null;
                                        @endphp
                                        <select id="vehicle_id_select" name="vehicle_id"
                                            class="bg-white border border-gray-300 text-gray-900 text-base lg:text-lg rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 mt-2 block w-full p-2.5">
                                            <option value=""
                                                {{ $selectedVehicleIdStr === null ? 'selected' : '' }}>
                                                Select
                                            </option>
                                            @foreach ($vehicles as $vehicle)
                                                <option value="{{ $vehicle->id }}"
                                                    {{ $selectedVehicleIdStr !== null && (string) $vehicle->id === $selectedVehicleIdStr ? 'selected' : '' }}>
                                                    {{ $vehicle->make }} / {{ $vehicle->model }} /
                                                    {{ $vehicle->year }}@if ($vehicle->vehicle_type)
                                                        / {{ $vehicle->vehicle_type }}
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('vehicle_id')
                                            <div class="relative tooltip -bottom-4 group-hover:flex">
                                                <div role="tooltip"
                                                    class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                                                    <p class="text-white leading-none text-sm lg:text-base">
                                                        {{ $message }}</p>
                                                </div>
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg overflow-hidden shadow-3xl mt-6">
                        <div class="bg-primary text-white py-2 px-4">
                            <h3>
                                @isset($postRidePage->luggage_label)
                                    {{ $postRidePage->luggage_label }}
                                @endisset
                            </h3>
                        </div>
                        <div class="bg-white p-4">
                            <div class="border rounded-md divide-y">
                                @isset($postRidePage->luggage_option1)
                                    <div class="flex items-center justify-start p-3">
                                        <input type="radio" id="no-luggage" name="luggage" value="0"
                                            {{ old('luggage', $ride->luggage) == 0 ? 'checked' : '' }}
                                            class="w-4 h-4 ml-2 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                        <label for="no-luggage"
                                            class="font-normal text-gray-900 flex items-center space-x-1 ml-4">
                                            <img class="w-10 h-10" src="{{ asset('assets/noluggage.png') }}"
                                                alt="">
                                            <span>
                                                {{ $postRidePage->luggage_option1->name }}
                                            </span>
                                        </label>
                                    </div>
                                @endisset
                                @isset($postRidePage->luggage_option2)
                                    <div class="flex items-center justify-start p-3">
                                        <input type="radio" id="small" name="luggage"
                                            value="{{ $postRidePage->luggage_option2->features_setting_id }}"
                                            {{ old('luggage', $ride->luggage) == $postRidePage->luggage_option2->features_setting_id ? 'checked' : '' }}
                                            class="w-4 h-4 ml-2 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                        <label for="small"
                                            class="font-normal text-gray-900 flex items-center space-x-1 ml-4">
                                            <img class="w-10 h-10" src="{{ asset('assets/luggage.png') }}" alt="">
                                            <span>
                                                {{ $postRidePage->luggage_option2->name }}
                                            </span>
                                        </label>
                                    </div>
                                @endisset
                                @isset($postRidePage->luggage_option3)
                                    <div class="flex items-center justify-start p-3">
                                        <input type="radio" id="medium" name="luggage"
                                            value="{{ $postRidePage->luggage_option3->features_setting_id }}"
                                            {{ old('luggage', $ride->luggage) == $postRidePage->luggage_option3->features_setting_id ? 'checked' : '' }}
                                            class="w-4 h-4 ml-2 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                        <label for="medium"
                                            class="font-normal text-gray-900 flex items-center space-x-1 ml-4">
                                            <img class="w-10 h-10" src="{{ asset('assets/mediumluggage.png') }}"
                                                alt="">
                                            <span>
                                                {{ $postRidePage->luggage_option3->name }}
                                            </span>
                                        </label>
                                    </div>
                                @endisset
                                @isset($postRidePage->luggage_option4)
                                    <div class="flex items-center justify-start p-3">
                                        <input type="radio" id="large" name="luggage"
                                            value="{{ $postRidePage->luggage_option4->features_setting_id }}"
                                            {{ old('luggage', $ride->luggage) == $postRidePage->luggage_option4->features_setting_id ? 'checked' : '' }}
                                            class="w-4 h-4 ml-2 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                        <label for="large"
                                            class="font-normal text-gray-900 flex items-center space-x-1 ml-4">
                                            <img class="w-10 h-10" src="{{ asset('assets/largeluggage.png') }}"
                                                alt="">
                                            <span>
                                                {{ $postRidePage->luggage_option4->name }}
                                            </span>
                                        </label>
                                    </div>
                                @endisset
                                @isset($postRidePage->luggage_option5)
                                    <div class="flex items-center justify-start p-3">
                                        <input type="radio" id="xl-multiple" name="luggage"
                                            value="{{ $postRidePage->luggage_option5->features_setting_id }}"
                                            {{ old('luggage', $ride->luggage) == $postRidePage->luggage_option5->features_setting_id ? 'checked' : '' }}
                                            class="w-4 h-4 ml-2 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                        <label for="xl-multiple"
                                            class="font-normal text-gray-900 flex items-center space-x-1 ml-4">
                                            <img class="w-10 h-10" src="{{ asset('assets/extralargeluggage.png') }}"
                                                alt="">
                                            <div>
                                                <p class="leading-normal mt-2">
                                                    {{ $postRidePage->luggage_option5->name }}
                                                </p>
                                                <div
                                                    class="font-normal text-gray-900 flex lg:block items-center space-x-0.5 2xl:pr-8">
                                                    <small>{{ $postRidePage->luggage_option5_label }} <sup
                                                            class="text-red-500">*</sup></small>
                                                    <span class="inline-flex cursor-help items-center"
                                                        data-tippy-content="{{ $postRidePage->luggage_option5_tooltip ?? '' }}">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                            height="16" fill="currentColor"
                                                            class="bi bi-info-circle-fill text-gray-800"
                                                            viewBox="0 0 16 16">
                                                            <path
                                                                d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
                                                        </svg>
                                                    </span>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                @endisset
                            </div>
                            @error('luggage')
                                <div class="relative tooltip -bottom-4 group-hover:flex">
                                    <div role="tooltip"
                                        class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                                        <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                    </div>
                                </div>
                            @enderror
                            <div class="mt-6 space-y-2">
                                <div class="flex items-start">
                                    <input id="heating" type="checkbox" name="accept_more_luggage" value="1"
                                        {{ old('accept_more_luggage', $ride->accept_more_luggage) == '1' ? 'checked' : '' }}
                                        class="w-4 h-4 mt-1 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                    <label for="heating" class="ml-2 font-normal text-gray-900 flex space-x-1">
                                        <span class="">
                                            @isset($postRidePage->luggage_checkbox_label1)
                                                {{ $postRidePage->luggage_checkbox_label1 }}
                                            @endisset
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg overflow-hidden shadow-3xl mt-6">
                        <div class="bg-primary text-white py-2 px-4">
                            <h3>
                                @isset($postRidePage->smoking_label)
                                    {{ $postRidePage->smoking_label }}
                                @endisset
                            </h3>
                        </div>
                        <div class="bg-white p-4">
                            <div class="border rounded-md overflow-hidden divide-y">
                                @isset($postRidePage->smoking_option1->features_setting_id)
                                    <div class="flex items-center justify-start p-3">
                                        <input id="smoke-1" name="smoke" type="radio"
                                            value="{{ $postRidePage->smoking_option1->features_setting_id }}"
                                            {{ old('smoke', $ride->smoke) == $postRidePage->smoking_option1->features_setting_id ? 'checked' : '' }}
                                            class="w-4 h-4 ml-2 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                        <label for="smoke-1" class="font-normal text-gray-900 flex space-x-1 ml-4">
                                            <span>
                                                {{ $postRidePage->smoking_option1->name }}
                                            </span>
                                        </label>
                                    </div>
                                @endisset
                                @isset($postRidePage->smoking_option2->features_setting_id)
                                    <div class="flex items-center justify-start p-3">
                                        <input id="smoke-2" name="smoke" type="radio"
                                            value="{{ $postRidePage->smoking_option2->features_setting_id }}"
                                            {{ old('smoke', $ride->smoke) == $postRidePage->smoking_option2->features_setting_id ? 'checked' : '' }}
                                            class="w-4 h-4 ml-2 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                        <label for="smoke-2" class="font-normal text-gray-900 flex space-x-1 ml-4">
                                            <span>
                                                {{ $postRidePage->smoking_option2->name }}
                                            </span>
                                        </label>
                                    </div>
                                @endisset
                            </div>
                            @error('smoke')
                                <div class="relative tooltip -bottom-4 group-hover:flex">
                                    <div role="tooltip"
                                        class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                                        <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                    </div>
                                </div>
                            @enderror
                        </div>
                    </div>

                    <div class="bg-white rounded-lg overflow-hidden shadow-3xl mt-6">
                        <div class="bg-primary text-white py-2 px-4">
                            <h3>
                                @isset($postRidePage->animals_label)
                                    {{ $postRidePage->animals_label }}
                                @endisset
                            </h3>
                        </div>
                        <div class="bg-white p-4">
                            <div class="border rounded-md overflow-hidden divide-y">
                                @isset($postRidePage->animals_option1->features_setting_id)
                                    <div class="flex items-center justify-start p-3">
                                        <input id="animal-1" name="animal_friendly" type="radio"
                                            value="{{ $postRidePage->animals_option1->features_setting_id }}"
                                            {{ old('animal_friendly', $ride->animal_friendly) == $postRidePage->animals_option1->features_setting_id ? 'checked' : '' }}
                                            class="w-4 h-4 ml-2 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                        <label for="animal-1" class="font-normal text-gray-900 flex space-x-1 ml-4">
                                            <span>
                                                {{ $postRidePage->animals_option1->name }}
                                            </span>
                                        </label>
                                    </div>
                                @endisset
                                @isset($postRidePage->animals_option2->features_setting_id)
                                    <div class="flex items-center justify-start p-3">
                                        <input id="animal-2" name="animal_friendly" type="radio"
                                            value="{{ $postRidePage->animals_option2->features_setting_id }}"
                                            {{ old('animal_friendly', $ride->animal_friendly) == $postRidePage->animals_option2->features_setting_id ? 'checked' : '' }}
                                            class="w-4 h-4 ml-2 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                        <label for="animal-2" class="font-normal text-gray-900 flex space-x-1 ml-4">
                                            <span>
                                                {{ $postRidePage->animals_option2->name }}
                                            </span>
                                        </label>
                                    </div>
                                @endisset
                                @isset($postRidePage->animals_option3->features_setting_id)
                                    <div class="flex items-center justify-start p-3">
                                        <input id="animal-3" name="animal_friendly" type="radio"
                                            value="{{ $postRidePage->animals_option3->features_setting_id }}"
                                            {{ old('animal_friendly', $ride->animal_friendly) == $postRidePage->animals_option3->features_setting_id ? 'checked' : '' }}
                                            class="w-4 h-4 ml-2 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                        <label for="animal-3" class="font-normal text-gray-900 flex space-x-1 ml-4">
                                            <span>
                                                {{ $postRidePage->animals_option3->name }}
                                            </span>
                                        </label>
                                    </div>
                                @endisset
                            </div>
                            @error('animal_friendly')
                                <div class="relative tooltip -bottom-4 group-hover:flex">
                                    <div role="tooltip"
                                        class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                                        <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                    </div>
                                </div>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6">
                        <div class="bg-white rounded-lg overflow-hidden shadow-3xl">
                            <div class="bg-primary text-white py-2 px-4">
                                <h3>
                                    @isset($postRidePage->preferences_label)
                                        {{ $postRidePage->preferences_label }}
                                    @endisset
                                </h3>
                            </div>
                            <div class="bg-white p-4">
                                <div class="space-y-2">
                                    @isset($postRidePage->features_option1)
                                        <div class="flex items-center">
                                            <input id="pink-ride" type="checkbox" name="features[]"
                                                @php $disabled = false; @endphp
                                                @if ($user->pink_ride == '0') @php $disabled = true; @endphp
                                            @elseif ($user->pink_ride == '')
                                                @if ($pinkRideSetting)
                                                    @if ($pinkRideSetting->female === '1' && $user->gender !== 'female')
                                                        @php $disabled = true; @endphp
                                                    @elseif ($pinkRideSetting->verfiy_phone === '1' && $user->phone_verified !== '1')
                                                        @php $disabled = true; @endphp
                                                    @elseif ($pinkRideSetting->verify_email === '1' && $user->email_verified !== '1')
                                                        @php $disabled = true; @endphp
                                                    @elseif ($pinkRideSetting->driver_license === '1' && $user->driver !== '1')
                                                        @php $disabled = true; @endphp @endif
                                                @endif
                                            @endif
                                            @if ($disabled) {{ 'disabled' }} @endif
                                            value="{{ $postRidePage->features_option1->features_setting_id }}"
                                            {{ in_array($postRidePage->features_option1->features_setting_id, explode('=', $ride->features)) ? 'checked' : '' }}
                                            class="w-4 h-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                            <label for="pink-ride" class="ml-2 text-gray-900 flex space-x-1">
                                                <span
                                                    class="text-pink-500 font-medium
                                                @php $disabled = false; @endphp
                                                @if ($user->pink_ride == '0') @php $disabled = true; @endphp
                                                @elseif ($user->pink_ride == '')
                                                    @if ($pinkRideSetting)
                                                        @if ($pinkRideSetting->female === '1' && $user->gender !== 'female')
                                                            @php $disabled = true; @endphp
                                                        @elseif ($pinkRideSetting->verfiy_phone === '1' && $user->phone_verified !== '1')
                                                            @php $disabled = true; @endphp
                                                        @elseif ($pinkRideSetting->verify_email === '1' && $user->email_verified !== '1')
                                                            @php $disabled = true; @endphp
                                                        @elseif ($pinkRideSetting->driver_license === '1' && $user->driver !== '1')
                                                            @php $disabled = true; @endphp @endif
                                                    @endif
                                                @endif
                                                @if ($disabled) {{ 'line-through' }} @endif">
                                                    {{ $postRidePage->features_option1->name }}
                                                </span>
                                                @php
                                                    $pinkRideTooltipText = '';
                                                    if ($user->pink_ride == '0') {
                                                        $pinkRideTooltipText =
                                                            $postRidePage->pink_ride_tooltip_admin_disable_text ?? '';
                                                    } elseif ($user->pink_ride == '1') {
                                                        $pinkRideTooltipText =
                                                            $postRidePage->pink_ride_tooltip_admin_enable_text ?? '';
                                                    } elseif ($pinkRideSetting) {
                                                        $pinkRideTooltipText =
                                                            ($postRidePage->pink_ride_tooltip_only_text ?? '') .
                                                            ' ' .
                                                            ($postRidePage->pink_ride_tooltip_female_text ?? '') .
                                                            ' ' .
                                                            ($postRidePage->pink_ride_tooltip_driver_text ?? '');
                                                        if (
                                                            $pinkRideSetting->verfiy_phone === '1' ||
                                                            $pinkRideSetting->verify_email === '1' ||
                                                            $pinkRideSetting->driver_license === '1' ||
                                                            $pinkRideSetting->profile_complete === '1'
                                                        ) {
                                                            $pinkRideTooltipText .=
                                                                ' ' .
                                                                ($postRidePage->pink_ride_tooltip_with_text ?? '');
                                                            if ($pinkRideSetting->profile_complete === '1') {
                                                                $pinkRideTooltipText .=
                                                                    ' ' .
                                                                    ($postRidePage->pink_ride_tooltip_complete_profile_text ??
                                                                        '');
                                                            }
                                                            if (
                                                                $pinkRideSetting->verfiy_phone === '1' ||
                                                                $pinkRideSetting->verify_email === '1' ||
                                                                $pinkRideSetting->driver_license === '1'
                                                            ) {
                                                                if ($pinkRideSetting->verfiy_phone === '1') {
                                                                    $pinkRideTooltipText .=
                                                                        ' ' .
                                                                        ($postRidePage->pink_ride_tooltip_phone_number_text ??
                                                                            '');
                                                                }
                                                                if ($pinkRideSetting->verify_email === '1') {
                                                                    $pinkRideTooltipText .=
                                                                        ' ' .
                                                                        ($postRidePage->pink_ride_tooltip_email_text ??
                                                                            '');
                                                                }
                                                                if ($pinkRideSetting->driver_license === '1') {
                                                                    $pinkRideTooltipText .=
                                                                        ' ' .
                                                                        ($postRidePage->pink_ride_tooltip_driver_license_text ??
                                                                            '');
                                                                }
                                                                $pinkRideTooltipText .=
                                                                    ' ' .
                                                                    ($postRidePage->pink_ride_tooltip_verified_text ??
                                                                        '');
                                                            }
                                                        }
                                                        $pinkRideTooltipText .=
                                                            ' ' .
                                                            ($postRidePage->pink_ride_tooltip_select_this_ride_text ??
                                                                '');
                                                    }
                                                @endphp
                                                <span class="inline-flex cursor-help"
                                                    data-tippy-content="{{ e($pinkRideTooltipText) }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                        fill="currentColor" class="bi bi-info-circle-fill text-gray-800"
                                                        viewBox="0 0 16 16">
                                                        <path
                                                            d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
                                                    </svg>
                                                </span>
                                            </label>
                                        </div>
                                    @endisset
                                    @isset($postRidePage->features_option2)
                                        @php
                                            // Calculate the age based on the driver's date of birth
                                            $dob = \Carbon\Carbon::parse($user->dob);
                                            $age = $dob->diffInYears(\Carbon\Carbon::now());
                                        @endphp
                                        <div class="flex items-center">
                                            <input id="Extra+" type="checkbox" name="features[]"
                                                @php $disabled = false; @endphp
                                                @if ($user->folks_ride == '0') @php $disabled = true; @endphp
                                            @elseif ($user->folks_ride == '')
                                                @if ($setting)
                                                    @if ($setting->verfiy_phone === '1' && $user->phone_verified !== '1')
                                                        @php $disabled = true; @endphp
                                                    @elseif ($setting->verify_email === '1' && $user->email_verified !== '1')
                                                        @php $disabled = true; @endphp
                                                    @elseif ($setting->driver_license === '1' && $user->driver !== '1')
                                                        @php $disabled = true; @endphp
                                                    @elseif ($overallRating < $setting->average_rating || $age < $setting->driver_age)
                                                        @php $disabled = true; @endphp @endif
                                                @endif
                                            @endif
                                            @if ($disabled) {{ 'disabled' }} @endif
                                            value="{{ $postRidePage->features_option2->features_setting_id }}"
                                            {{ in_array($postRidePage->features_option2->features_setting_id, explode('=', $ride->features)) ? 'checked' : '' }}
                                            class="w-4 h-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                            <label for="Extra+" class="ml-2 text-gray-900 flex space-x-1">
                                                <span
                                                    class="text-green-500 font-medium
                                                @php $disabled = false; @endphp
                                                @if ($user->folks_ride == '0') @php $disabled = true; @endphp
                                                @elseif ($user->folks_ride == '')
                                                    @if ($setting)
                                                        @if ($setting->verfiy_phone === '1' && $user->phone_verified !== '1')
                                                            @php $disabled = true; @endphp
                                                        @elseif ($setting->verify_email === '1' && $user->email_verified !== '1')
                                                            @php $disabled = true; @endphp
                                                        @elseif ($setting->driver_license === '1' && $user->driver !== '1')
                                                            @php $disabled = true; @endphp
                                                        @elseif ($overallRating < $setting->average_rating || $age < $setting->driver_age)
                                                            @php $disabled = true; @endphp @endif
                                                    @endif
                                                @endif
                                                @if ($disabled) {{ 'line-through' }} @endif
                                                ">
                                                    {{ $postRidePage->features_option2->name }}
                                                </span>
                                                @php
                                                    $extraCareTooltipText = '';
                                                    if ($user->folks_ride == '0') {
                                                        $extraCareTooltipText =
                                                            $postRidePage->extra_care_tooltip_admin_disable_text ?? '';
                                                    } elseif ($user->folks_ride == '1') {
                                                        $extraCareTooltipText =
                                                            $postRidePage->extra_care_tooltip_admin_enable_text ?? '';
                                                    } else {
                                                        $extraCareTooltipText =
                                                            ($postRidePage->extra_care_tooltip_driver_review_text ??
                                                                '') .
                                                            ' ' .
                                                            ($setting ? $setting->average_rating : '0') .
                                                            ' ' .
                                                            ($postRidePage->extra_care_tooltip_greater_age_text ?? '') .
                                                            ' ' .
                                                            ($setting ? $setting->driver_age : '0') .
                                                            ' ' .
                                                            ($postRidePage->extra_care_tooltip_greater_text ?? '');
                                                        if (
                                                            $setting &&
                                                            ($setting->verfiy_phone === '1' ||
                                                                $setting->verify_email === '1' ||
                                                                $setting->driver_license === '1' ||
                                                                $setting->profile_complete === '1')
                                                        ) {
                                                            if ($setting->profile_complete === '1') {
                                                                $extraCareTooltipText .=
                                                                    ' ' .
                                                                    ($postRidePage->extra_care_tooltip_complete_profile_text ??
                                                                        '');
                                                            }
                                                            $extraCareTooltipText .=
                                                                ' ' .
                                                                ($postRidePage->extra_care_tooltip_and_his_text ?? '');
                                                            if ($setting->verfiy_phone === '1') {
                                                                $extraCareTooltipText .=
                                                                    ' ' .
                                                                    ($postRidePage->extra_care_tooltip_phone_number_text ??
                                                                        '');
                                                            }
                                                            if ($setting->verify_email === '1') {
                                                                $extraCareTooltipText .=
                                                                    ' ' .
                                                                    ($postRidePage->extra_care_tooltip_email_text ??
                                                                        '');
                                                            }
                                                            if ($setting->driver_license === '1') {
                                                                $extraCareTooltipText .=
                                                                    ' ' .
                                                                    ($postRidePage->extra_care_tooltip_driver_license_text ??
                                                                        '');
                                                            }
                                                            $extraCareTooltipText .=
                                                                ' ' .
                                                                ($postRidePage->extra_care_tooltip_verified_text ?? '');
                                                        }
                                                        $extraCareTooltipText .=
                                                            ' ' .
                                                            ($postRidePage->extra_care_tooltip_eligible_text ?? '');
                                                    }
                                                @endphp
                                                <span class="inline-flex cursor-help"
                                                    data-tippy-content="{{ e($extraCareTooltipText) }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                        fill="currentColor" class="bi bi-info-circle-fill text-gray-800"
                                                        viewBox="0 0 16 16">
                                                        <path
                                                            d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
                                                    </svg>
                                                </span>
                                            </label>
                                        </div>
                                    @endisset
                                    @isset($postRidePage->features_option3)
                                        <div class="flex items-center">
                                            <input id="wi-fi" type="checkbox" name="features[]"
                                                value="{{ $postRidePage->features_option3->features_setting_id }}"
                                                {{ in_array($postRidePage->features_option3->features_setting_id, explode('=', $ride->features)) ? 'checked' : '' }}
                                                class="w-4 h-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                            <label for="wi-fi" class="ml-2 font-normal text-gray-900 flex space-x-1">
                                                <span>
                                                    {{ $postRidePage->features_option3->name }}
                                                </span>
                                            </label>
                                        </div>
                                    @endisset
                                    @isset($postRidePage->features_option4)
                                        <div class="flex items-center">
                                            <input id="rating-passengers" type="checkbox" name="features[]"
                                                value="{{ $postRidePage->features_option4->features_setting_id }}"
                                                {{ in_array($postRidePage->features_option4->features_setting_id, explode('=', $ride->features)) ? 'checked' : '' }}
                                                class="w-4 h-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                            <label for="rating-passengers"
                                                class="ml-2 font-normal text-gray-900 flex space-x-1">
                                                <span>
                                                    {{ $postRidePage->features_option4->name }}
                                                </span>
                                            </label>
                                        </div>
                                    @endisset
                                    @isset($postRidePage->features_option5)
                                        <div class="flex items-center">
                                            <input id="provide-babyseats" type="checkbox" name="features[]"
                                                value="{{ $postRidePage->features_option5->features_setting_id }}"
                                                {{ in_array($postRidePage->features_option5->features_setting_id, explode('=', $ride->features)) ? 'checked' : '' }}
                                                class="w-4 h-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                            <label for="provide-babyseats"
                                                class="ml-2 font-normal text-gray-900 flex space-x-1">
                                                <span class="">
                                                    {{ $postRidePage->features_option5->name }}
                                                </span>
                                            </label>
                                        </div>
                                    @endisset
                                    @isset($postRidePage->features_option6)
                                        <div class="flex items-center">
                                            <input id="passenger-provide" type="checkbox" name="features[]"
                                                value="{{ $postRidePage->features_option6->features_setting_id }}"
                                                {{ in_array($postRidePage->features_option6->features_setting_id, explode('=', $ride->features)) ? 'checked' : '' }}
                                                class="w-4 h-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                            <label for="passenger-provide"
                                                class="ml-2 font-normal text-gray-900 flex space-x-1">
                                                <span class="">
                                                    {{ $postRidePage->features_option6->name }}
                                                </span>
                                            </label>
                                        </div>
                                    @endisset
                                    @isset($postRidePage->features_option7)
                                        <div class="flex items-center">
                                            <input id="take-children" type="checkbox" name="features[]"
                                                value="{{ $postRidePage->features_option7->features_setting_id }}"
                                                {{ in_array($postRidePage->features_option7->features_setting_id, explode('=', $ride->features)) ? 'checked' : '' }}
                                                class="w-4 h-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                            <label for="take-children"
                                                class="ml-2 font-normal text-gray-900 flex space-x-1">
                                                <span class="">
                                                    {{ $postRidePage->features_option7->name }}
                                                </span>
                                            </label>
                                        </div>
                                    @endisset
                                    @isset($postRidePage->features_option8)
                                        <div class="flex items-center">
                                            <input id="passenger-provide1" type="checkbox" name="features[]"
                                                value="{{ $postRidePage->features_option8->features_setting_id }}"
                                                {{ in_array($postRidePage->features_option8->features_setting_id, explode('=', $ride->features)) ? 'checked' : '' }}
                                                class="w-4 h-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                            <label for="passenger-provide1"
                                                class="ml-2 font-normal text-gray-900 flex space-x-1">
                                                <span class="">
                                                    {{ $postRidePage->features_option8->name }}
                                                </span>
                                            </label>
                                        </div>
                                    @endisset
                                    @isset($postRidePage->features_option9)
                                        <div class="flex items-center">
                                            <input id="bike-rack" type="checkbox" name="features[]"
                                                value="{{ $postRidePage->features_option9->features_setting_id }}"
                                                {{ in_array($postRidePage->features_option9->features_setting_id, explode('=', $ride->features)) ? 'checked' : '' }}
                                                class="w-4 h-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                            <label for="bike-rack" class="ml-2 font-normal text-gray-900 flex space-x-1">
                                                <span class="">
                                                    {{ $postRidePage->features_option9->name }}
                                                </span>
                                            </label>
                                        </div>
                                    @endisset
                                    @isset($postRidePage->features_option10)
                                        <div class="flex items-center">
                                            <input id="ski-rack" type="checkbox" name="features[]"
                                                value="{{ $postRidePage->features_option10->features_setting_id }}"
                                                {{ in_array($postRidePage->features_option10->features_setting_id, explode('=', $ride->features)) ? 'checked' : '' }}
                                                class="w-4 h-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                            <label for="ski-rack" class="ml-2 font-normal text-gray-900 flex space-x-1">
                                                <span class="">
                                                    {{ $postRidePage->features_option10->name }}
                                                </span>
                                            </label>
                                        </div>
                                    @endisset
                                    @isset($postRidePage->features_option11)
                                        <div class="flex items-center">
                                            <input id="winter-tires" type="checkbox" name="features[]"
                                                value="{{ $postRidePage->features_option11->features_setting_id }}"
                                                {{ in_array($postRidePage->features_option11->features_setting_id, explode('=', $ride->features)) ? 'checked' : '' }}
                                                class="w-4 h-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                            <label for="winter-tires" class="ml-2 font-normal text-gray-900 flex space-x-1">
                                                <span class="">
                                                    {{ $postRidePage->features_option11->name }}
                                                </span>
                                            </label>
                                        </div>
                                    @endisset
                                    @isset($postRidePage->features_option12)
                                        <div class="flex items-center">
                                            <input id="air-conditioning" type="checkbox" name="features[]"
                                                value="{{ $postRidePage->features_option12->features_setting_id }}"
                                                {{ in_array($postRidePage->features_option12->features_setting_id, explode('=', $ride->features)) ? 'checked' : '' }}
                                                class="w-4 h-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                            <label for="air-conditioning"
                                                class="ml-2 font-normal text-gray-900 flex space-x-1">
                                                <span class="">
                                                    {{ $postRidePage->features_option12->name }}
                                                </span>
                                            </label>
                                        </div>
                                    @endisset
                                    @isset($postRidePage->features_option13)
                                        <div class="flex items-center">
                                            <input id="heating" type="checkbox" name="features[]"
                                                value="{{ $postRidePage->features_option13->features_setting_id }}"
                                                {{ in_array($postRidePage->features_option13->features_setting_id, explode('=', $ride->features)) ? 'checked' : '' }}
                                                class="w-4 h-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                            <label for="heating" class="ml-2 font-normal text-gray-900 flex space-x-1">
                                                <span class="">
                                                    {{ $postRidePage->features_option13->name }}
                                                </span>
                                            </label>
                                        </div>
                                    @endisset
                                    @isset($postRidePage->features_option14)
                                        <div class="flex items-center">
                                            <input id="heating" type="checkbox" name="features[]"
                                                value="{{ $postRidePage->features_option14->features_setting_id }}"
                                                {{ in_array($postRidePage->features_option14->features_setting_id, explode('=', $ride->features)) ? 'checked' : '' }}
                                                class="w-4 h-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                            <label for="heating" class="ml-2 font-normal text-gray-900 flex space-x-1">
                                                <span class="">
                                                    {{ $postRidePage->features_option14->name }}
                                                </span>
                                            </label>
                                        </div>
                                    @endisset
                                    @isset($postRidePage->features_option15)
                                        <div class="flex items-center">
                                            <input id="heating" type="checkbox" name="features[]"
                                                value="{{ $postRidePage->features_option15->features_setting_id }}"
                                                {{ in_array($postRidePage->features_option15->features_setting_id, explode('=', $ride->features)) ? 'checked' : '' }}
                                                class="w-4 h-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                            <label for="heating" class="ml-2 font-normal text-gray-900 flex space-x-1">
                                                <span class="">
                                                    {{ $postRidePage->features_option15->name }}
                                                </span>
                                            </label>
                                        </div>
                                    @endisset
                                    @isset($postRidePage->features_option16)
                                        <div class="flex items-center">
                                            <input id="heating" type="checkbox" name="features[]"
                                                value="{{ $postRidePage->features_option16->features_setting_id }}"
                                                {{ in_array($postRidePage->features_option16->features_setting_id, explode('=', $ride->features)) ? 'checked' : '' }}
                                                class="w-4 h-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                            <label for="heating" class="ml-2 font-normal text-gray-900 flex space-x-1">
                                                <span class="">
                                                    {{ $postRidePage->features_option16->name }}
                                                </span>
                                            </label>
                                        </div>
                                    @endisset
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg overflow-hidden shadow-3xl mt-6">
                        <div class="bg-primary text-white py-2 px-4">
                            <h3>
                                Cancellation policy
                            </h3>
                        </div>
                        <div class="bg-white p-4">
                            <div>
                                <div class="space-y-2 mt-2">
                                    @isset($postRidePage->cancellation_policy_label1->features_setting_id)
                                        <div class="flex items-center space-x-1 md:space-x-2 mb-2 mr-2 lg:mr-2">
                                            <input id="standard" name="booking_type" type="radio"
                                                value="{{ $postRidePage->cancellation_policy_label1->features_setting_id }}"
                                                {{ old('booking_type', $ride->booking_type) == $postRidePage->cancellation_policy_label1->features_setting_id ? 'checked' : '' }}
                                                class="h-5 w-5 rounded bg-white border border-gray-200 cursor-pointer text-indigo-600 focus:ring-indigo-600">
                                            <label for="standard"
                                                class="ml-3 font-normal text-gray-900 flex items-center space-x-1">
                                                <span class="">
                                                    {{ $postRidePage->cancellation_policy_label1->name }}
                                                </span>
                                            </label>
                                        </div>
                                    @endisset
                                    @isset($postRidePage->cancellation_policy_label2->features_setting_id)
                                        <div class="flex items-center space-x-1 md:space-x-2 mb-2 mr-2 lg:mr-2">
                                            <input id="firm" name="booking_type" type="radio"
                                                value="{{ $postRidePage->cancellation_policy_label2->features_setting_id }}"
                                                {{ old('booking_type', $ride->booking_type) == $postRidePage->cancellation_policy_label2->features_setting_id ? 'checked' : '' }}
                                                class="h-5 w-5 rounded bg-white border border-gray-200 cursor-pointer text-indigo-600 focus:ring-indigo-600">
                                            <label for="firm"
                                                class="ml-3 font-normal text-gray-900 flex items-center space-x-1">
                                                <span class="">
                                                    {{ $postRidePage->cancellation_policy_label2->name }}
                                                </span>
                                            </label>
                                        </div>
                                    @endisset
                                </div>
                                @error('booking_type')
                                    <div class="relative tooltip -bottom-4 group-hover:flex">
                                        <div role="tooltip"
                                            class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-primary text-gray-600 w-full md:w-1/2 rounded">
                                            <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                        </div>
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class=" mt-6">
                        <div class="bg-white rounded-lg overflow-hidden shadow-3xl">
                            <div class="bg-primary text-white py-2 px-4">
                                <label for="more" class="">
                                    <h3>
                                        @isset($postRidePage->anything_to_add_label)
                                            {{ $postRidePage->anything_to_add_label }}
                                        @endisset
                                    </h3>
                                </label>
                            </div>
                            <div class="bg-white p-4">
                                <textarea id="more" rows="5" name="notes"
                                    class="block p-2.5 w-full mt-2 text-gray-900 bg-gray-100 text-base lg:text-lg rounded border border-gray-200 focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500"
                                    @isset($postRidePage->anything_to_add_placeholder)
                                    placeholder="{{ $postRidePage->anything_to_add_placeholder }}"
                                @endisset>{{ old('notes', $ride->notes) }}</textarea>
                                @error('notes')
                                    <div class="relative tooltip -bottom-4 group-hover:flex">
                                        <div role="tooltip"
                                            class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                                            <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                        </div>
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class=" mt-6">
                        <div class="bg-white rounded-lg overflow-hidden shadow-3xl">
                            <div class="bg-primary text-white py-2 px-4">
                                <h3>
                                    @isset($postRidePage->disclaimers_label)
                                        {{ $postRidePage->disclaimers_label }}
                                    @endisset
                                </h3>
                            </div>
                            <div class="bg-white p-4">
                                @isset($postRidePage->disclaimers_description)
                                    {!! str_replace(
                                        '<ol>',
                                        '<ol class="list-decimal list-inside">',
                                        str_replace(
                                            '<li>',
                                            '<li class="border-b border-gray-300 text-base lg:text-lg last:border-b-0 py-3">',
                                            $postRidePage->disclaimers_description,
                                        ),
                                    ) !!}
                                @endisset
                            </div>
                        </div>
                    </div>

                    <div class="flex items-start my-4">
                        <input id="agree_checkbox" type="checkbox" name="agree_terms" value="1" checked
                            class="w-4 h-4 text-blue-600 cursor-pointer bg-white mt-1 border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                        <label for="agree_checkbox" class="ml-2 font-normal text-gray-900">
                            @isset($postRidePage->agree_terms_label)
                                {!! $postRidePage->agree_terms_label !!}
                            @endisset
                        </label>
                    </div>
                    @error('agree_terms')
                        <div class="relative tooltip -bottom-4 group-hover:flex">
                            <div role="tooltip"
                                class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                                <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                            </div>
                        </div>
                    @enderror
                    <div class="hidden lg:flex justify-center items-center mt-8">
                        <button
                            class="edit-ride-submit-btn bg-greenXS hover:bg-greenXS text-white text-base md:text-lg rounded font-FuturaMdCnBT hover:font-FuturaMdCnBT px-5 py-2 border border-greenXS hover:border-greenXS hover:text-white text-center focus:bg-greenXS focus:text-white active:text-white active:bg-greenXS disabled:opacity-70 disabled:cursor-not-allowed"
                            type="submit">
                            @isset($postRidePage->submit_button_label)
                                {{ $postRidePage->submit_button_label }}
                            @endisset
                        </button>
                    </div>
                </div>

            </div>
            <div class="flex lg:hidden justify-center items-center mt-8">
                <button
                    class="edit-ride-submit-btn bg-greenXS hover:bg-greenXS text-white text-base md:text-lg rounded font-FuturaMdCnBT hover:font-FuturaMdCnBT px-5 py-2 border border-greenXS hover:border-greenXS hover:text-white text-center focus:bg-greenXS focus:text-white active:text-white active:bg-greenXS disabled:opacity-70 disabled:cursor-not-allowed"
                    type="submit">
                    @isset($postRidePage->submit_button_label)
                        {{ $postRidePage->submit_button_label }}
                    @endisset
                </button>
            </div>
        </form>
    </div>

@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_API_KEY') }}&libraries=places&callback=initMapEditRide"
        async defer></script>

    <script>
        function debounce(func, delay) {
            let timer;
            return function(...args) {
                clearTimeout(timer);
                timer = setTimeout(() => {
                    func.apply(this, args);
                }, delay);
            };
        }

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
        document.addEventListener('click', function(event) {
            if (event.target.classList.contains('fixed') && event.target.classList.contains('inset-0')) {
                closeModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeModal();
            }
        });

        function toggleAddMoreSpots(button) {
            var panel = document.getElementById('add-more-spots-panel');
            if (!panel) return;
            var isOpen = button.getAttribute('aria-expanded') === 'true';
            if (isOpen) {
                panel.style.height = panel.scrollHeight + 'px';
                panel.offsetHeight;
                panel.style.height = '0';
                button.setAttribute('aria-expanded', 'false');
            } else {
                panel.style.height = panel.scrollHeight + 'px';
                button.setAttribute('aria-expanded', 'true');
                panel.addEventListener('transitionend', function onEnd() {
                    panel.removeEventListener('transitionend', onEnd);
                    if (button.getAttribute('aria-expanded') === 'true') {
                        panel.style.height = 'auto';
                    }
                }, {
                    once: true
                });
            }
        }

        function swapLocations() {
            const fromEl = document.getElementById('from_spot_0');
            const toEl = document.getElementById('to_spot_0');
            if (!fromEl || !toEl) return;
            const fromValue = fromEl.value;
            const toValue = toEl.value;
            fromEl.value = toValue;
            toEl.value = fromValue;
            var t = selectedFromPlaceEditRide;
            selectedFromPlaceEditRide = selectedToPlaceEditRide;
            selectedToPlaceEditRide = t;
        }

        var geocoderEditRide = null;
        var selectedFromPlaceEditRide = null;
        var selectedToPlaceEditRide = null;
        var isSettingPlaceValueEditRide = false;
        var isSelectingFromDropdownEditRide = false;
        var errorFromRequiredEditRide = 'The origin is required';
        var errorToRequiredEditRide = 'The destination is required';
        var errorCityMissingEditRide = 'We could not find this city name in our records, please double-check the spelling.';

        window.initMapEditRide = function initMapEditRide() {
            if (typeof google === 'undefined' || !google.maps || !google.maps.places) return;
            geocoderEditRide = new google.maps.Geocoder();
            var fromInput = document.getElementById('from_spot_0');
            var toInput = document.getElementById('to_spot_0');
            if (!fromInput || !toInput) return;
            var fromAutocomplete = new google.maps.places.Autocomplete(fromInput, {
                componentRestrictions: {
                    country: 'ca'
                },
                types: ['(cities)'],
                fields: ['address_components', 'formatted_address', 'name', 'place_id']
            });
            var toAutocomplete = new google.maps.places.Autocomplete(toInput, {
                componentRestrictions: {
                    country: 'ca'
                },
                types: ['(cities)'],
                fields: ['address_components', 'formatted_address', 'name', 'place_id']
            });
            fromAutocomplete.addListener('place_changed', function() {
                var place = fromAutocomplete.getPlace();
                if (place.address_components && place.place_id) {
                    isSettingPlaceValueEditRide = true;
                    isSelectingFromDropdownEditRide = true;
                    var formatted = formatPlaceAddressEditRide(place);
                    selectedFromPlaceEditRide = {
                        place_id: place.place_id,
                        formatted_address: formatted,
                        value: formatted
                    };
                    fromInput.value = formatted;
                    var err = document.getElementById('fromInputError');
                    if (err) err.classList.add('hidden');
                    if (typeof fetchDistance === 'function') setTimeout(function() {
                        fetchDistance(fromInput.value.trim(), toInput.value.trim()).then(function() {});
                    }, 300);
                    setTimeout(function() {
                        isSettingPlaceValueEditRide = false;
                        isSelectingFromDropdownEditRide = false;
                    }, 100);
                }
            });
            toAutocomplete.addListener('place_changed', function() {
                var place = toAutocomplete.getPlace();
                if (place.address_components && place.place_id) {
                    isSettingPlaceValueEditRide = true;
                    isSelectingFromDropdownEditRide = true;
                    var formatted = formatPlaceAddressEditRide(place);
                    selectedToPlaceEditRide = {
                        place_id: place.place_id,
                        formatted_address: formatted,
                        value: formatted
                    };
                    toInput.value = formatted;
                    var err = document.getElementById('toInputError');
                    if (err) err.classList.add('hidden');
                    if (typeof fetchDistance === 'function') setTimeout(function() {
                        fetchDistance(fromInput.value.trim(), toInput.value.trim()).then(function() {});
                    }, 300);
                    setTimeout(function() {
                        isSettingPlaceValueEditRide = false;
                        isSelectingFromDropdownEditRide = false;
                    }, 100);
                }
            });
            fromInput.addEventListener('focus', function() {
                var el = document.getElementById('fromInputError');
                if (el) el.classList.add('hidden');
            });
            toInput.addEventListener('focus', function() {
                var el = document.getElementById('toInputError');
                if (el) el.classList.add('hidden');
            });
            fromInput.addEventListener('blur', function() {
                if (isSettingPlaceValueEditRide || isSelectingFromDropdownEditRide) return;
                var self = this;
                setTimeout(function() {
                    if (isSettingPlaceValueEditRide || isSelectingFromDropdownEditRide) return;
                    var currentValue = self.value.trim();
                    var fromInputError = document.getElementById('fromInputError');
                    if (currentValue !== '' && (!selectedFromPlaceEditRide || currentValue !== (
                            selectedFromPlaceEditRide.value || '').trim())) {
                        resolveTypedCityValueEditRide(currentValue, 'from').then(function() {
                            currentValue = self.value.trim();
                            if (currentValue === '' || !selectedFromPlaceEditRide ||
                                currentValue !== (selectedFromPlaceEditRide.value || '').trim()
                                ) {
                                selectedFromPlaceEditRide = null;
                                if (currentValue !== '' && fromInputError) {
                                    var te = fromInputError.querySelector('.tooltip-error');
                                    if (te) te.textContent = errorCityMissingEditRide;
                                    fromInputError.classList.remove('hidden');
                                } else if (fromInputError) fromInputError.classList.add(
                                    'hidden');
                            } else if (fromInputError) fromInputError.classList.add('hidden');
                            if (typeof fetchDistance === 'function' && toInput && toInput.value)
                                fetchDistance(fromInput.value.trim(), toInput.value.trim())
                                .then(function() {});
                        });
                    } else {
                        if (currentValue === '' || !selectedFromPlaceEditRide || currentValue !== (
                                selectedFromPlaceEditRide.value || '').trim()) {
                            selectedFromPlaceEditRide = null;
                            if (currentValue !== '' && fromInputError) {
                                var te = fromInputError.querySelector('.tooltip-error');
                                if (te) te.textContent = currentValue === '' ?
                                    errorFromRequiredEditRide : errorCityMissingEditRide;
                                fromInputError.classList.remove('hidden');
                            }
                        } else if (fromInputError) fromInputError.classList.add('hidden');
                        if (typeof fetchDistance === 'function' && toInput && toInput.value)
                            fetchDistance(fromInput.value.trim(), toInput.value.trim()).then(
                        function() {});
                    }
                }, 200);
            });
            toInput.addEventListener('blur', function() {
                if (isSettingPlaceValueEditRide || isSelectingFromDropdownEditRide) return;
                var self = this;
                setTimeout(function() {
                    if (isSettingPlaceValueEditRide || isSelectingFromDropdownEditRide) return;
                    var currentValue = self.value.trim();
                    var toInputError = document.getElementById('toInputError');
                    if (currentValue !== '' && (!selectedToPlaceEditRide || currentValue !== (
                            selectedToPlaceEditRide.value || '').trim())) {
                        resolveTypedCityValueEditRide(currentValue, 'to').then(function() {
                            currentValue = self.value.trim();
                            if (currentValue === '' || !selectedToPlaceEditRide ||
                                currentValue !== (selectedToPlaceEditRide.value || '').trim()) {
                                selectedToPlaceEditRide = null;
                                if (currentValue !== '' && toInputError) {
                                    var te = toInputError.querySelector('.tooltip-error');
                                    if (te) te.textContent = errorCityMissingEditRide;
                                    toInputError.classList.remove('hidden');
                                } else if (toInputError) toInputError.classList.add('hidden');
                            } else if (toInputError) toInputError.classList.add('hidden');
                            if (typeof fetchDistance === 'function' && fromInput && fromInput
                                .value) fetchDistance(fromInput.value.trim(), toInput.value
                                .trim()).then(function() {});
                        });
                    } else {
                        if (currentValue === '' || !selectedToPlaceEditRide || currentValue !== (
                                selectedToPlaceEditRide.value || '').trim()) {
                            selectedToPlaceEditRide = null;
                            if (currentValue !== '' && toInputError) {
                                var te = toInputError.querySelector('.tooltip-error');
                                if (te) te.textContent = currentValue === '' ? errorToRequiredEditRide :
                                    errorCityMissingEditRide;
                                toInputError.classList.remove('hidden');
                            }
                        } else if (toInputError) toInputError.classList.add('hidden');
                        if (typeof fetchDistance === 'function' && fromInput && fromInput.value)
                            fetchDistance(fromInput.value.trim(), toInput.value.trim()).then(
                        function() {});
                    }
                }, 200);
            });
            document.addEventListener('mousedown', function(e) {
                if (e.target.closest('.pac-container')) isSelectingFromDropdownEditRide = true;
                else setTimeout(function() {
                    isSelectingFromDropdownEditRide = false;
                }, 50);
            });

            document.querySelectorAll('input[name="stop_spot_display[]"]').forEach(function(inp) {
                if (inp.id && inp.id.indexOf('stop_spot_') === 0 && !inp.getAttribute(
                        'data-autocomplete-attached')) {
                    attachStopAutocompleteEditRide(inp);
                }
            });
        }

        function formatPlaceAddressEditRide(place) {
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

        function resolveTypedCityValueEditRide(rawValue, target) {
            var value = (rawValue || '').trim();
            if (!value || !geocoderEditRide) return Promise.resolve(false);
            var inputId = target === 'from' ? 'from_spot_0' : 'to_spot_0';
            var input = document.getElementById(inputId);
            return new Promise(function(resolve) {
                geocoderEditRide.geocode({
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
                    var formatted = formatPlaceAddressEditRide(result);
                    if (!formatted) {
                        resolve(false);
                        return;
                    }
                    isSettingPlaceValueEditRide = true;
                    var sel = {
                        place_id: result.place_id,
                        formatted_address: formatted,
                        value: formatted
                    };
                    if (target === 'from') selectedFromPlaceEditRide = sel;
                    else selectedToPlaceEditRide = sel;
                    if (input) input.value = formatted;
                    var err = document.getElementById(target === 'from' ? 'fromInputError' :
                    'toInputError');
                    if (err) err.classList.add('hidden');
                    setTimeout(function() {
                        isSettingPlaceValueEditRide = false;
                    }, 100);
                    resolve(true);
                });
            });
        }

        function getStopErrorElementEditRide(inputEl) {
            if (!inputEl) return null;
            var id = inputEl.id || '';
            var dataIndex = inputEl.getAttribute('data-stop-index');
            var index = dataIndex || (id.indexOf('stop_spot_') === 0 ? id.replace('stop_spot_', '') : null);
            return index ? document.getElementById('stopInputError_' + index) : null;
        }

        function resolveTypedCityValueForStopEditRide(inputElement) {
            var value = (inputElement && inputElement.value) ? inputElement.value.trim() : '';
            var err = getStopErrorElementEditRide(inputElement);
            if (!value) {
                if (err) err.classList.add('hidden');
                return Promise.resolve(true);
            }
            if (!geocoderEditRide) {
                if (err) {
                    var te = err.querySelector('.tooltip-error');
                    if (te) te.textContent = errorCityMissingEditRide;
                    err.classList.remove('hidden');
                }
                return Promise.resolve(false);
            }
            return new Promise(function(resolve) {
                geocoderEditRide.geocode({
                    address: value,
                    componentRestrictions: {
                        country: 'CA'
                    }
                }, function(response, status) {
                    if (status !== 'OK' || !response || !response.length) {
                        if (err) {
                            var te = err.querySelector('.tooltip-error');
                            if (te) te.textContent = errorCityMissingEditRide;
                            err.classList.remove('hidden');
                        }
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
                        if (err) {
                            var te = err.querySelector('.tooltip-error');
                            if (te) te.textContent = errorCityMissingEditRide;
                            err.classList.remove('hidden');
                        }
                        resolve(false);
                        return;
                    }
                    var formatted = formatPlaceAddressEditRide(result);
                    if (!formatted) {
                        if (err) {
                            var te = err.querySelector('.tooltip-error');
                            if (te) te.textContent = errorCityMissingEditRide;
                            err.classList.remove('hidden');
                        }
                        resolve(false);
                        return;
                    }
                    isSettingPlaceValueEditRide = true;
                    inputElement.value = formatted;
                    if (err) err.classList.add('hidden');
                    if (typeof syncSegmentPricesUI === 'function') syncSegmentPricesUI();
                    setTimeout(function() {
                        isSettingPlaceValueEditRide = false;
                    }, 100);
                    resolve(true);
                });
            });
        }

        function attachStopAutocompleteEditRide(inputElement) {
            if (!inputElement || typeof google === 'undefined' || !google.maps || !google.maps.places) return;
            if (inputElement.getAttribute('data-autocomplete-attached')) return;
            inputElement.setAttribute('data-autocomplete-attached', '1');
            inputElement.setAttribute('autocomplete', 'off');
            var autocomplete = new google.maps.places.Autocomplete(inputElement, {
                componentRestrictions: {
                    country: 'ca'
                },
                types: ['(cities)'],
                fields: ['address_components', 'formatted_address', 'name', 'place_id']
            });
            autocomplete.addListener('place_changed', function() {
                var place = autocomplete.getPlace();
                if (place.address_components && place.place_id) {
                    var formatted = formatPlaceAddressEditRide(place);
                    if (formatted) inputElement.value = formatted;
                    var err = getStopErrorElementEditRide(inputElement);
                    if (err) err.classList.add('hidden');
                    if (typeof syncSegmentPricesUI === 'function') syncSegmentPricesUI();
                }
            });
            inputElement.addEventListener('focus', function() {
                var err = getStopErrorElementEditRide(inputElement);
                if (err) err.classList.add('hidden');
            });
            inputElement.addEventListener('blur', function() {
                if (isSettingPlaceValueEditRide || isSelectingFromDropdownEditRide) return;
                var self = this;
                setTimeout(function() {
                    if (isSettingPlaceValueEditRide || isSelectingFromDropdownEditRide) return;
                    if (!self.value || !self.value.trim()) {
                        var e = getStopErrorElementEditRide(self);
                        if (e) e.classList.add('hidden');
                        return;
                    }
                    if (typeof resolveTypedCityValueForStopEditRide === 'function')
                        resolveTypedCityValueForStopEditRide(self);
                }, 200);
            });
        }

        const dateInput = document.getElementById('dateInput');
        const timeInput = document.getElementById('timeInput');
        if (!dateInput || !timeInput) {
            console.warn('edit_ride: dateInput or timeInput not found');
        } else {

            @php
                $projectTimezone = config('app.timezone');
                $projectOffset = \Carbon\Carbon::now($projectTimezone)->offsetHours;
                $jsOldTime = old('time');
                if ($jsOldTime === null && $ride && !empty($ride->time)) {
                    try {
                        $jsOldTime = \Carbon\Carbon::parse($ride->time)->format('H:i');
                    } catch (\Exception $e) {
                        $jsOldTime = is_string($ride->time) ? substr($ride->time, 0, 5) : '';
                    }
                }
                $jsOldTime = $jsOldTime ?? '';
            @endphp
            const projectOffset = {{ $projectOffset }};

            function getCurrentProjectTime() {
                const now = new Date();
                const localOffset = now.getTimezoneOffset();
                const laravelOffsetMinutes = projectOffset * 60;
                now.setMinutes(now.getMinutes() + localOffset + laravelOffsetMinutes);
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                return `${hours}:${minutes}`;
            }

            const oldDate = '{{ old('date', $ride->date ? date('F d, Y', strtotime($ride->date)) : '') }}';
            const oldTime = '{{ $jsOldTime }}';

            function isDefaultDateToday() {
                if (!oldDate || oldDate === 'today') return true;
                try {
                    const d = new Date(oldDate);
                    return !isNaN(d.getTime()) && d.toDateString() === new Date().toDateString();
                } catch (e) {
                    return true;
                }
            }
            const initialMinTime = isDefaultDateToday() ? getCurrentProjectTime() : '00:00';

            flatpickr(dateInput, {
                dateFormat: 'F d, Y',
                minDate: 'today',
                defaultDate: oldDate || 'today',
                disableMobile: true,
                onChange: function(selectedDates, dateStr, instance) {
                    if (!timeInput._flatpickr) return;
                    const isToday = instance.latestSelectedDateObj ? instance.latestSelectedDateObj
                        .toDateString() === new Date().toDateString() : false;
                    const minTime = isToday ? getCurrentProjectTime() : '00:00';
                    timeInput._flatpickr.set('minTime', minTime);
                    if (isToday) {
                        const utcTime = getCurrentProjectTime();
                        const [hours, minutes] = utcTime.split(':');
                        const date = new Date();
                        date.setHours(parseInt(hours, 10), parseInt(minutes, 10), 0, 0);
                        timeInput._flatpickr.setDate(date, true);
                    }
                },
            });

            function formatTimeDisplay(date) {
                const hours = date.getHours();
                const minutes = date.getMinutes();
                const mins = minutes < 10 ? '0' + minutes : String(minutes);
                if (hours < 12) {
                    const h = hours === 0 ? 12 : hours;
                    return h + ':' + mins + ' am';
                } else {
                    const h = hours < 10 ? '0' + hours : String(hours);
                    return h + ':' + mins;
                }
            }
            const timePicker = flatpickr(timeInput, {
                enableTime: true,
                noCalendar: true,
                dateFormat: 'H:i',
                altInput: true,
                altFormat: 'H:i',
                time_24hr: false,
                disableMobile: true,
                minTime: initialMinTime,
                defaultDate: (oldTime && oldTime.length >= 4) ? oldTime : '',
                minuteIncrement: 1,
                onChange: function(selectedDates) {
                    if (selectedDates.length && timeInput._flatpickr.altInput) {
                        timeInput._flatpickr.altInput.value = formatTimeDisplay(selectedDates[0]);
                    }
                },
                onClose: function(selectedDates) {
                    if (selectedDates.length && timeInput._flatpickr.altInput) {
                        timeInput._flatpickr.altInput.value = formatTimeDisplay(selectedDates[0]);
                    }
                }
            });
            if (timePicker.selectedDates.length && timePicker.altInput) {
                timePicker.altInput.value = formatTimeDisplay(timePicker.selectedDates[0]);
            }

            timeInput.addEventListener('click', function() {
                if (!timeInput._flatpickr || !timeInput._flatpickr.input) return;
                if (!timeInput._flatpickr.input.value) {
                    const projectTime = getCurrentProjectTime();
                    const [hours, minutes] = projectTime.split(':');
                    const date = new Date();
                    date.setHours(parseInt(hours, 10), parseInt(minutes, 10), 0, 0);
                    timeInput._flatpickr.setDate(date, true);
                }
            });

        }

        function seat_selected(th) {
            var seat = $(th).val();

            for (i = 1; i <= seat; i++) {
                // Change the image source for selected seats
                $(".seat-image.seat-unselect-" + i).attr('src', '{{ asset('assets/seat-hover-1.png') }}');
                $(".seat-number.seat-number-" + i).addClass('text-green-300');
                $("#number-of-seat-cross-" + i).hide();
            }

            for (i = parseInt(seat) + 1; i <= 7; i++) {
                if (seat == 7) continue;
                // Change the image source back to unselected for remaining seats
                $(".seat-image.seat-unselect-" + i).attr('src', '{{ asset('assets/seat.png') }}');
                $(".seat-number.seat-number-" + i).removeClass('text-green-300');
                $("#number-of-seat-cross-" + i).show();
            }
        }

        const profileImage = document.getElementById('profile-image');

        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    profileImage.src = e.target.result;
                    $('#profile-image').addClass('w-40');
                    $('#profile-image').addClass('h-40');
                };

                reader.readAsDataURL(input.files[0]);
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Get the checkbox elements
            var skipCheckbox = document.getElementById('skip');
            var addCheckbox = document.getElementById('add');
            var addedCheckbox = document.getElementById('added');
            var recurringTripCheckbox = document.getElementById('recurring_trip');

            // Get the vehicle panel divs
            var skipVehicle = document.getElementById('skipVehicle');
            var showVehicles = document.getElementById('showVehicles');
            var recurringtripDetails = document.getElementById('recurringtripDetails');

            // Array of all checkboxes for mutual exclusivity (only include existing elements)
            var checkboxes = [skipCheckbox, addCheckbox, addedCheckbox].filter(Boolean);

            // Function to uncheck other checkboxes when one is checked
            function handleCheckboxChange(checkedCheckbox) {
                checkboxes.forEach(function(checkbox) {
                    if (checkbox && checkbox !== checkedCheckbox) {
                        checkbox.checked = false;
                    }
                });
            }

            if (skipCheckbox && skipVehicle) {
                skipCheckbox.addEventListener('change', function() {
                    handleCheckboxChange(skipCheckbox);
                    skipVehicle.style.display = 'none';
                    if (showVehicles) showVehicles.style.display = 'none';
                });
            }
            if (addCheckbox && skipVehicle) {
                addCheckbox.addEventListener('change', function() {
                    handleCheckboxChange(addCheckbox);
                    skipVehicle.style.display = this.checked ? 'block' : 'none';
                    if (showVehicles) showVehicles.style.display = 'none';
                    var vehicleFields = skipVehicle.querySelectorAll(
                        'input[name="make"], input[name="model"], select[name="vehicle_type"], input[name="year"], input[name="color"], input[name="license_no"], input[name="car_type"]'
                        );
                    vehicleFields.forEach(function(field) {
                        if (this.checked) {
                            field.removeAttribute('disabled');
                            field.setAttribute('required', 'required');
                        } else {
                            field.removeAttribute('required');
                        }
                    }.bind(this));
                });
            }
            if (addedCheckbox && showVehicles) {
                addedCheckbox.addEventListener('change', function() {
                    handleCheckboxChange(addedCheckbox);
                    showVehicles.style.display = this.checked ? 'block' : 'none';
                    if (skipVehicle) skipVehicle.style.display = 'none';
                    if (skipVehicle) {
                        var vehicleFields = skipVehicle.querySelectorAll(
                            'input[name="make"], input[name="model"], select[name="vehicle_type"], input[name="year"], input[name="color"], input[name="license_no"], input[name="car_type"]'
                            );
                        vehicleFields.forEach(function(field) {
                            field.removeAttribute('required');
                        });
                    }
                });
            }
            if (recurringTripCheckbox && recurringtripDetails) {
                recurringTripCheckbox.addEventListener('change', function() {
                    recurringtripDetails.style.display = this.checked ? 'block' : 'none';
                });
            }

            // Initial visibility when the page loads
            if (skipVehicle) skipVehicle.style.display = addCheckbox && addCheckbox.checked ? 'block' : 'none';
            if (showVehicles) showVehicles.style.display = addedCheckbox && addedCheckbox.checked ? 'block' :
            'none';
            if (recurringtripDetails) recurringtripDetails.style.display = recurringTripCheckbox &&
                recurringTripCheckbox.checked ? 'block' : 'none';

            // Set initial required state for vehicle fields (when "Add vehicle" is checked)
            if (skipVehicle && addCheckbox) {
                var vehicleFields = skipVehicle.querySelectorAll(
                    'input[name="make"], input[name="model"], select[name="vehicle_type"], input[name="year"], input[name="color"], input[name="license_no"], input[name="car_type"]'
                    );
                vehicleFields.forEach(function(field) {
                    if (addCheckbox.checked) {
                        field.removeAttribute('disabled');
                        field.setAttribute('required', 'required');
                    } else {
                        field.removeAttribute('required');
                    }
                });
            }
        });

        document.getElementById('delete-stop-yes').addEventListener('click', function() {
            deleteStopRowConfirmed();
        });
        document.getElementById('delete-stop-no').addEventListener('click', function() {
            closeDeleteStopModal();
        });
        var deleteStopBackdrop = document.getElementById('delete-stop-modal-backdrop');
        if (deleteStopBackdrop) deleteStopBackdrop.addEventListener('click', closeDeleteStopModal);

        function updateSegmentTotalPrice() {
            var container = document.getElementById('stops-segment-prices-container') || document.getElementById(
                'stops-segment-prices-dynamic');
            var totalInput = document.getElementById('segment-total-price-input') || document.getElementById(
                'segment-total-price-input-dynamic');
            if (!container || !totalInput) return;
            var inputs = container.querySelectorAll('input[name="price_spot_display[]"]');
            var sum = 0;
            inputs.forEach(function(inp) {
                var v = parseFloat(inp.value);
                if (!isNaN(v)) sum += v;
            });
            totalInput.value = sum.toFixed(2);
            checkFullRouteVsTotal();
        }

        function checkFullRouteVsTotal() {
            var container = document.getElementById('stops-segment-prices-container') || document.getElementById(
                'stops-segment-prices-dynamic');
            if (!container) return;
            var fullRouteInput = container.querySelector('input[name="price"]') || container.querySelector(
                '.full-route-price-input');
            var totalInput = container.querySelector('#segment-total-price-input') || container.querySelector(
                '#segment-total-price-input-dynamic');
            var tooltip = container.querySelector('#full-route-tooltip-container') || container.querySelector(
                '#full-route-tooltip-container-dynamic');
            if (!fullRouteInput || !totalInput || !tooltip) return;
            var fullVal = parseFloat(fullRouteInput.value);
            var totalVal = parseFloat(totalInput.value);
            if (isNaN(fullVal)) fullVal = 0;
            if (isNaN(totalVal)) totalVal = 0;
            if (fullVal > totalVal) {
                tooltip.classList.remove('hidden');
            } else {
                tooltip.classList.add('hidden');
            }
        }

        function syncContainerSegmentRows() {
            var segmentContainer = document.getElementById('stops-segment-prices-container');
            var stopsContainer = document.getElementById('stops-rows-container');
            if (!segmentContainer || !stopsContainer) return;
            var stopInputs = stopsContainer.querySelectorAll('input[name="stop_spot_display[]"]');
            var stops = [];
            stopInputs.forEach(function(inp) {
                var v = (inp.value && inp.value.trim) ? inp.value.trim() : '';
                if (v) stops.push(v);
            });
            var originEl = document.getElementById('from_spot_0') || document.querySelector('input[name="from"]');
            var destEl = document.getElementById('to_spot_0') || document.querySelector('input[name="to"]');
            var origin = originEl ? (originEl.value ? originEl.value.trim() : '') : '';
            var destination = destEl ? (destEl.value ? destEl.value.trim() : '') : '';
            var segments = [];
            for (var i = 0; i <= stops.length; i++) {
                segments.push({
                    from: i === 0 ? origin : stops[i - 1],
                    to: i === stops.length ? destination : stops[i]
                });
            }
            var readOnly = segmentContainer.getAttribute('data-bookings-readonly') === '1';
            var rowClass =
                'bg-gray-100 border border-gray-200 pl-7 text-gray-900 text-base lg:text-lg rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 block w-full p-2.5 mt-2' +
                (readOnly ? ' cursor-not-allowed opacity-60' : '');
            var rows = segmentContainer.querySelectorAll('.segment-price-row');
            var needed = segments.length;
            var i, row, labelP, from, to, newRow, frag;
            for (i = 0; i < rows.length; i++) {
                row = rows[i];
                labelP = row.querySelector('.segment-label');
                if (labelP && segments[i]) {
                    labelP.textContent = segments[i].from + ' \u2192 ' + segments[i].to;
                }
            }
            if (rows.length < needed) {
                var svgPath =
                    'M 15 3 L 15 5.09375 C 12.164063 5.570313 10 8.050781 10 11 C 10 12.777344 10.832031 14.148438 11.9375 15.03125 C 13.042969 15.914063 14.375 16.40625 15.625 16.90625 C 16.875 17.40625 18.042969 17.914063 18.8125 18.53125 C 19.582031 19.148438 20 19.773438 20 21 C 20 23.15625 18.207031 25 16 25 C 13.78125 25 12 23.21875 12 21 L 12 20 L 10 20 L 10 21 C 10 23.964844 12.164063 26.429688 15 26.90625 L 15 29 L 17 29 L 17 26.90625 C 19.84375 26.425781 22 23.925781 22 21 C 22 19.21875 21.167969 17.855469 20.0625 16.96875 C 18.957031 16.082031 17.625 15.5625 16.375 15.0625 C 15.125 14.5625 13.957031 14.082031 13.1875 13.46875 C 12.417969 12.855469 12 12.21875 12 11 C 12 8.808594 13.785156 7 16 7 C 18.21875 7 20 8.78125 20 11 L 20 12 L 22 12 L 22 11 C 22 8.035156 19.835938 5.570313 17 5.09375 L 17 3 Z';
                for (i = rows.length; i < needed; i++) {
                    from = segments[i].from;
                    to = segments[i].to;
                    newRow = document.createElement('div');
                    newRow.className = 'mt-4 segment-price-row';
                    newRow.innerHTML = '<p class="text-gray-700 font-medium mb-1 segment-label"></p>' +
                        '<div class="relative mt-2">' +
                        '<span class="absolute inset-y-0 start-0 flex items-center pl-2 pointer-events-none">' +
                        '<svg fill="currentColor" width="800px" height="800px" viewBox="0 0 32 32" class="w-5 h-5 text-gray-500" xmlns="http://www.w3.org/2000/svg"><path d="' +
                        svgPath + '"/></svg></span>' +
                        '<input type="number" step="any" name="price_spot_display[]" placeholder="" value="" ' + (readOnly ?
                            'readonly ' : '') + 'class="' + rowClass + '"/>' +
                        '</div>';
                    newRow.querySelector('.segment-label').textContent = from + ' \u2192 ' + to;
                    segmentContainer.appendChild(newRow);
                }
            } else if (rows.length > needed) {
                for (i = rows.length - 1; i >= needed; i--) {
                    rows[i].remove();
                }
            }
            updateSegmentTotalPrice();
            checkFullRouteVsTotal();
        }

        function syncSegmentPricesUI() {
            var singleBlock = document.getElementById('single-price-block');
            var dynamicBlock = document.getElementById('stops-segment-prices-dynamic');
            var segmentContainer = document.getElementById('stops-segment-prices-container');
            var stopsContainer = document.getElementById('stops-rows-container');
            if (!stopsContainer) return;
            if (!dynamicBlock && segmentContainer) {
                syncContainerSegmentRows();
                return;
            }
            if (!dynamicBlock) return;
            if (!stopsContainer) return;
            var stopInputs = stopsContainer.querySelectorAll('input[name="stop_spot_display[]"]');
            var stops = [];
            stopInputs.forEach(function(inp) {
                var v = (inp.value && inp.value.trim) ? inp.value.trim() : '';
                if (v) stops.push(v);
            });
            var origin = '';
            var destination = '';
            var originEl = document.getElementById('from_spot_0') || document.querySelector('input[name="from"]');
            var destEl = document.getElementById('to_spot_0') || document.querySelector('input[name="to"]');
            if (originEl) origin = originEl.value ? originEl.value.trim() : '';
            if (destEl) destination = destEl.value ? destEl.value.trim() : '';
            var mainPrice = '0';
            var singlePriceInput = document.getElementById('priceData0');
            if (singlePriceInput) mainPrice = singlePriceInput.value !== '' ? singlePriceInput.value : '0';
            if (stops.length === 0) {
                if (singleBlock) singleBlock.style.display = '';
                dynamicBlock.style.display = 'none';
                var dynFullRouteInput = document.getElementById('priceData0DynamicInput');
                if (dynFullRouteInput) {
                    dynFullRouteInput.removeAttribute('name');
                    dynFullRouteInput.id = 'priceData0DynamicInput';
                }
                if (singlePriceInput) {
                    singlePriceInput.setAttribute('name', 'price');
                    singlePriceInput.id = 'priceData0';
                }
                var rowsDyn = document.getElementById('segment-price-rows-dynamic');
                if (rowsDyn) rowsDyn.innerHTML = '';
                var tooltipDyn = document.getElementById('full-route-tooltip-container-dynamic');
                if (tooltipDyn) tooltipDyn.classList.add('hidden');
                return;
            }
            if (singleBlock) singleBlock.style.display = 'none';
            if (singlePriceInput) {
                singlePriceInput.removeAttribute('name');
                singlePriceInput.id = 'priceData0Backup';
            }
            var dynFullRouteInput = document.getElementById('priceData0DynamicInput');
            if (dynFullRouteInput) {
                dynFullRouteInput.setAttribute('name', 'price');
                dynFullRouteInput.value = mainPrice;
                dynFullRouteInput.id = 'priceData0';
            }
            dynamicBlock.style.display = '';
            var segments = [];
            for (var i = 0; i <= stops.length; i++) {
                segments.push({
                    from: i === 0 ? origin : stops[i - 1],
                    to: i === stops.length ? destination : stops[i]
                });
            }
            var rowsEl = document.getElementById('segment-price-rows-dynamic');
            if (!rowsEl) return;
            var existingInputs = rowsEl.querySelectorAll('input[name="price_spot_display[]"]');
            var existingValues = [];
            existingInputs.forEach(function(inp) {
                existingValues.push(inp.value);
            });
            rowsEl.innerHTML = '';
            var readOnly = dynamicBlock.getAttribute('data-bookings-readonly') === '1';
            for (var j = 0; j < segments.length; j++) {
                var seg = segments[j];
                var row = document.createElement('div');
                row.className = 'mt-4 segment-price-row-dynamic';
                row.innerHTML = '<p class="text-gray-700 font-medium mb-1 segment-label">' + (seg.from + ' \u2192 ' + seg
                        .to) + '</p>' +
                    '<div class="relative mt-2">' +
                    '<span class="absolute inset-y-0 start-0 flex items-center pl-2 pointer-events-none">' +
                    '<svg fill="currentColor" width="800px" height="800px" viewBox="0 0 32 32" class="w-5 h-5 text-gray-500" xmlns="http://www.w3.org/2000/svg">' +
                    '<path d="M 15 3 L 15 5.09375 C 12.164063 5.570313 10 8.050781 10 11 C 10 12.777344 10.832031 14.148438 11.9375 15.03125 C 13.042969 15.914063 14.375 16.40625 15.625 16.90625 C 16.875 17.40625 18.042969 17.914063 18.8125 18.53125 C 19.582031 19.148438 20 19.773438 20 21 C 20 23.15625 18.207031 25 16 25 C 13.78125 25 12 23.21875 12 21 L 12 20 L 10 20 L 10 21 C 10 23.964844 12.164063 26.429688 15 26.90625 L 15 29 L 17 29 L 17 26.90625 C 19.84375 26.425781 22 23.925781 22 21 C 22 19.21875 21.167969 17.855469 20.0625 16.96875 C 18.957031 16.082031 17.625 15.5625 16.375 15.0625 C 15.125 14.5625 13.957031 14.082031 13.1875 13.46875 C 12.417969 12.855469 12 12.21875 12 11 C 12 8.808594 13.785156 7 16 7 C 18.21875 7 20 8.78125 20 11 L 20 12 L 22 12 L 22 11 C 22 8.035156 19.835938 5.570313 17 5.09375 L 17 3 Z"/>' +
                    '</svg></span>' +
                    '<input type="number" step="any" name="price_spot_display[]" placeholder="" value="' + (existingValues[
                        j] !== undefined ? existingValues[j] : mainPrice) + '" ' +
                    (readOnly ? 'readonly ' : '') +
                    'class="bg-gray-100 border border-gray-200 pl-7 text-gray-900 text-base lg:text-lg rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 block w-full p-2.5 mt-2' +
                    (readOnly ? ' cursor-not-allowed opacity-60' : '') + '"/>' +
                    '</div>';
                rowsEl.appendChild(row);
            }
            updateSegmentTotalPrice();
            dynamicBlock.querySelectorAll('input[name="price_spot_display[]"]').forEach(function(inp) {
                inp.addEventListener('input', updateSegmentTotalPrice);
                inp.addEventListener('change', updateSegmentTotalPrice);
            });
            checkFullRouteVsTotal();
        }

        (function() {
            var container = document.getElementById('stops-segment-prices-container');
            if (container) {
                container.addEventListener('input', function(e) {
                    if (e.target && e.target.name === 'price_spot_display[]') updateSegmentTotalPrice();
                    if (e.target && (e.target.name === 'price' || e.target.classList.contains(
                            'full-route-price-input'))) checkFullRouteVsTotal();
                });
                container.addEventListener('change', function(e) {
                    if (e.target && e.target.name === 'price_spot_display[]') updateSegmentTotalPrice();
                    if (e.target && (e.target.name === 'price' || e.target.classList.contains(
                            'full-route-price-input'))) checkFullRouteVsTotal();
                });
                checkFullRouteVsTotal();
            }
            var dynamicBlock = document.getElementById('stops-segment-prices-dynamic');
            if (dynamicBlock) {
                dynamicBlock.addEventListener('input', function(e) {
                    if (e.target && (e.target.name === 'price' || e.target.id === 'priceData0DynamicInput' || e
                            .target.classList.contains('full-route-price-input'))) checkFullRouteVsTotal();
                });
                dynamicBlock.addEventListener('change', function(e) {
                    if (e.target && (e.target.name === 'price' || e.target.id === 'priceData0DynamicInput' || e
                            .target.classList.contains('full-route-price-input'))) checkFullRouteVsTotal();
                });
            }
            var stopsContainer = document.getElementById('stops-rows-container');
            if (stopsContainer) {
                stopsContainer.addEventListener('input', function(e) {
                    if (e.target && e.target.name === 'stop_spot_display[]') syncSegmentPricesUI();
                });
                stopsContainer.addEventListener('change', function(e) {
                    if (e.target && e.target.name === 'stop_spot_display[]') syncSegmentPricesUI();
                });
            }
        })();

        function buildStopsSegmentsForSubmit() {
            var form = document.getElementById('edit-ride-form');
            var container = document.getElementById('stops-rows-container');
            var hiddenContainer = document.getElementById('stops-segments-hidden');
            var wrapper = document.getElementById('stops-section-wrapper');
            if (!container || !hiddenContainer) return;
            var origin = (document.getElementById('from_spot_0') || document.querySelector('input[name="from"]')) ? (
                document.getElementById('from_spot_0') || document.querySelector('input[name="from"]')).value : '';
            var destination = (document.getElementById('to_spot_0') || document.querySelector('input[name="to"]')) ? (
                document.getElementById('to_spot_0') || document.querySelector('input[name="to"]')).value : '';
            var mainPrice = (document.getElementById('priceData0') || (form ? form.querySelector('input[name="price"]') :
                null)) ? (document.getElementById('priceData0') || (form ? form.querySelector('input[name="price"]') :
                null)).value : '0';
            var segmentPriceInputs = form ? form.querySelectorAll('input[name="price_spot_display[]"]') : [];
            var priceHidden = form ? form.querySelector('input[name="price"]') : null;
            if (segmentPriceInputs.length > 0 && priceHidden) {
                priceHidden.value = segmentPriceInputs[0].value !== '' ? segmentPriceInputs[0].value : mainPrice;
            }
            var stopInputs = container.querySelectorAll('input[name="stop_spot_display[]"]');
            var stops = [];
            stopInputs.forEach(function(inp) {
                var v = inp.value ? inp.value.trim() : '';
                if (v) stops.push(v);
            });
            var segmentIds = [];
            try {
                segmentIds = wrapper ? JSON.parse(wrapper.getAttribute('data-segment-ids') || '[]') : [];
            } catch (e) {}
            hiddenContainer.innerHTML = '';
            if (stops.length === 0) {
                return;
            }
            var n = stops.length;
            for (var i = 0; i <= n; i++) {
                var fromVal = (i === 0) ? origin : stops[i - 1];
                var toVal = (i === n) ? destination : stops[i];
                if (!fromVal || !toVal) continue;
                var segPrice = mainPrice;
                if (segmentPriceInputs.length > i && segmentPriceInputs[i].value !== '') {
                    segPrice = segmentPriceInputs[i].value;
                }
                var segId = (segmentIds[i] !== undefined && segmentIds[i] !== null) ? segmentIds[i] : '0';
                var inpFrom = document.createElement('input');
                inpFrom.type = 'hidden';
                inpFrom.name = 'from_spot[]';
                inpFrom.value = fromVal;
                var inpTo = document.createElement('input');
                inpTo.type = 'hidden';
                inpTo.name = 'to_spot[]';
                inpTo.value = toVal;
                var inpPrice = document.createElement('input');
                inpPrice.type = 'hidden';
                inpPrice.name = 'price_spot[]';
                inpPrice.value = segPrice;
                var inpId = document.createElement('input');
                inpId.type = 'hidden';
                inpId.name = 'ride_detail_ids[]';
                inpId.value = segId;
                hiddenContainer.appendChild(inpFrom);
                hiddenContainer.appendChild(inpTo);
                hiddenContainer.appendChild(inpPrice);
                hiddenContainer.appendChild(inpId);
            }
        }

        // Prevent form submit on Enter (allow Enter in textareas for new lines)
        var editRideForm = document.getElementById('edit-ride-form');
        if (editRideForm) {
            editRideForm.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
                    e.preventDefault();
                }
            });
        }

        // Ensure all form fields are submitted, especially disabled/readonly ones
        document.getElementById('edit-ride-form').addEventListener('submit', function(e) {
            var form = document.getElementById('edit-ride-form');
            if (form) form.querySelectorAll('.edit-ride-submit-btn').forEach(function(btn) {
                btn.disabled = true;
            });
            try {
                var fromVal = (document.getElementById('from_spot_0') || {}).value || '';
                var toVal = (document.getElementById('to_spot_0') || {}).value || '';
                var fromInputError = document.getElementById('fromInputError');
                var toInputError = document.getElementById('toInputError');
                var fromInvalid = !fromVal.trim();
                var toInvalid = !toVal.trim();
                if (fromInputError) fromInputError.classList.toggle('hidden', !fromInvalid);
                if (fromInvalid && fromInputError) {
                    var te = fromInputError.querySelector('.tooltip-error');
                    if (te) te.textContent = errorFromRequiredEditRide || 'The origin is required';
                }
                if (toInputError) toInputError.classList.toggle('hidden', !toInvalid);
                if (toInvalid && toInputError) {
                    var te2 = toInputError.querySelector('.tooltip-error');
                    if (te2) te2.textContent = errorToRequiredEditRide || 'The destination is required';
                }

                var stopsContainer = document.getElementById('stops-rows-container');
                var stopInputs = stopsContainer ? stopsContainer.querySelectorAll(
                    'input[name="stop_spot_display[]"]') : [];
                var firstInvalidStop = null;
                var stopInvalid = false;
                stopInputs.forEach(function(inp) {
                    var err = typeof getStopErrorElementEditRide === 'function' ?
                        getStopErrorElementEditRide(inp) : null;
                    if (err) err.classList.add('hidden');
                    if (!inp.value || !inp.value.trim()) {
                        stopInvalid = true;
                        if (err) {
                            var te = err.querySelector('.tooltip-error');
                            if (te) te.textContent = 'Please enter or select a city.';
                            err.classList.remove('hidden');
                        }
                        if (!firstInvalidStop) firstInvalidStop = inp;
                    }
                });

                if (fromInvalid || toInvalid || stopInvalid) {
                    e.preventDefault();
                    var scrollTarget = fromInvalid ? document.getElementById('from_spot_0') : (toInvalid ? document
                        .getElementById('to_spot_0') : firstInvalidStop);
                    if (scrollTarget) scrollTarget.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                    return;
                }
                if (fromInputError) fromInputError.classList.add('hidden');
                if (toInputError) toInputError.classList.add('hidden');

                buildStopsSegmentsForSubmit();
                // When segment prices are shown: full-route must be <= total; if not, scroll to price section and prevent submit
                var segmentContainer = document.getElementById('stops-segment-prices-container');
                var dynamicBlock = document.getElementById('stops-segment-prices-dynamic');
                var isSegmentMode = (segmentContainer && segmentContainer.offsetParent !== null) || (dynamicBlock &&
                    dynamicBlock.style.display !== 'none');
                if (isSegmentMode) {
                    var container = segmentContainer && segmentContainer.offsetParent !== null ? segmentContainer :
                        dynamicBlock;
                    var fullRouteInput = container ? (container.querySelector('input[name="price"]') || container
                        .querySelector('.full-route-price-input')) : null;
                    var totalInput = container ? (container.querySelector('#segment-total-price-input') || container
                        .querySelector('#segment-total-price-input-dynamic')) : null;
                    if (fullRouteInput && totalInput) {
                        var fullVal = parseFloat(fullRouteInput.value);
                        var totalVal = parseFloat(totalInput.value);
                        if (isNaN(fullVal)) fullVal = 0;
                        if (isNaN(totalVal)) totalVal = 0;
                        if (fullVal > totalVal) {
                            e.preventDefault();
                            if (typeof updateSegmentTotalPrice === 'function') updateSegmentTotalPrice();
                            if (typeof checkFullRouteVsTotal === 'function') checkFullRouteVsTotal();
                            var priceSection = document.getElementById('edit-ride-price-section');
                            if (priceSection) {
                                priceSection.scrollIntoView({
                                    behavior: 'smooth',
                                    block: 'start'
                                });
                            }
                            return;
                        }
                    }
                }

                // Price validation: same as post_ride - Google Maps distance × $0.72/km ÷ seats per section
                var bypassInput = this.querySelector('input[name="bypass_price_validation"]');
                if (bypassInput && bypassInput.value === '1') {
                    return;
                }
                var seatsInput = this.querySelector('input[name="seats"]:checked') || this.querySelector(
                    'input[name="seats"]');
                var numSeats = seatsInput ? parseInt(seatsInput.value, 10) : 0;
                if (!numSeats) numSeats = 0;
                var segmentContainer = document.getElementById('stops-segment-prices-container');
                var dynamicBlock = document.getElementById('stops-segment-prices-dynamic');
                var isSegmentPriceMode = (segmentContainer && segmentContainer.offsetParent !== null) || (
                    dynamicBlock && dynamicBlock.style.display !== 'none' && dynamicBlock.offsetParent !== null);

                if (!isSegmentPriceMode) {
                    var priceInput = document.getElementById('priceData0');
                    var price = priceInput ? parseFloat(priceInput.value) : 0;
                    if (price && price > 0 && numSeats > 0) {
                        var distance = (typeof $ !== 'undefined' && priceInput) ? $(priceInput).data('distance') :
                            null;
                        if (!distance) distance = window.rideDistance;
                        if (distance && distance > 0) {
                            var validation = validatePricePerSeat(price, distance, numSeats);
                            if (!validation.valid) {
                                e.preventDefault();
                                showPriceErrorModal(validation.maxPricePerSeat);
                                return;
                            }
                            if (validation.type === 'warning') {
                                e.preventDefault();
                                showPriceWarningModal(function() {
                                    var form = document.getElementById('edit-ride-form');
                                    if (form) {
                                        var bypass = document.createElement('input');
                                        bypass.type = 'hidden';
                                        bypass.name = 'bypass_price_validation';
                                        bypass.value = '1';
                                        form.appendChild(bypass);
                                        form.submit();
                                    }
                                });
                                return;
                            }
                        } else {
                            var fromInput = document.getElementById('from_spot_0') || this.querySelector(
                                'input[name="from"]');
                            var toInput = document.getElementById('to_spot_0') || this.querySelector(
                                'input[name="to"]');
                            if (fromInput && toInput && fromInput.value.trim() && toInput.value.trim()) {
                                e.preventDefault();
                                var self = this;
                                fetchDistance(fromInput.value.trim(), toInput.value.trim()).then(function(
                                    distanceKm) {
                                    if (!distanceKm || distanceKm <= 0) {
                                        document.getElementById('edit-ride-form').submit();
                                        return;
                                    }
                                    window.rideDistance = distanceKm;
                                    if (priceInput && typeof $ !== 'undefined') $(priceInput).data(
                                        'distance', distanceKm);
                                    var validation = validatePricePerSeat(price, distanceKm, numSeats);
                                    if (!validation.valid) {
                                        showPriceErrorModal(validation.maxPricePerSeat);
                                        return;
                                    }
                                    if (validation.type === 'warning') {
                                        showPriceWarningModal(function() {
                                            var form = document.getElementById('edit-ride-form');
                                            if (form) {
                                                var b = document.createElement('input');
                                                b.type = 'hidden';
                                                b.name = 'bypass_price_validation';
                                                b.value = '1';
                                                form.appendChild(b);
                                                form.submit();
                                            }
                                        });
                                        return;
                                    }
                                    document.getElementById('edit-ride-form').submit();
                                }).catch(function() {
                                    document.getElementById('edit-ride-form').submit();
                                });
                                return;
                            }
                        }
                    }
                } else if (numSeats > 0) {
                    var origin = ((document.getElementById('from_spot_0') || this.querySelector(
                        'input[name="from"]')) || {}).value;
                    var destination = ((document.getElementById('to_spot_0') || this.querySelector(
                        'input[name="to"]')) || {}).value;
                    origin = (origin && typeof origin === 'string') ? origin.trim() : '';
                    destination = (destination && typeof destination === 'string') ? destination.trim() : '';
                    var stopInputs = this.querySelectorAll('input[name="stop_spot_display[]"]');
                    var stops = [];
                    if (stopInputs && stopInputs.length)
                        for (var si = 0; si < stopInputs.length; si++) {
                            var v = (stopInputs[si].value || '').trim();
                            if (v) stops.push(v);
                        }
                    var segments = [];
                    for (var i = 0; i <= stops.length; i++) {
                        segments.push({
                            from: i === 0 ? origin : stops[i - 1],
                            to: i === stops.length ? destination : stops[i]
                        });
                    }
                    var priceInputs = (segmentContainer && segmentContainer.offsetParent !== null) ?
                        (segmentContainer.querySelectorAll('input[name="price_spot_display[]"]') || []) :
                        (dynamicBlock ? (dynamicBlock.querySelectorAll('input[name="price_spot_display[]"]') ||
                        []) : []);
                    if (segments.length > 0 && segments.length === priceInputs.length) {
                        e.preventDefault();
                        var fetchPromises = [];
                        for (var fi = 0; fi < segments.length; fi++) {
                            fetchPromises.push(fetchDistance(segments[fi].from, segments[fi].to));
                        }
                        Promise.all(fetchPromises).then(function(distances) {
                            var firstErrorMax = null;
                            var hasWarning = false;
                            for (var vi = 0; vi < segments.length; vi++) {
                                var dist = distances[vi];
                                var priceVal = parseFloat(priceInputs[vi].value);
                                if (!dist || dist <= 0 || !priceVal || priceVal <= 0) continue;
                                var v = validatePricePerSeat(priceVal, dist, numSeats);
                                if (!v.valid) {
                                    firstErrorMax = v.maxPricePerSeat;
                                    break;
                                }
                                if (v.type === 'warning') hasWarning = true;
                            }
                            if (firstErrorMax !== null) {
                                showPriceErrorModal(firstErrorMax);
                                return;
                            }
                            if (hasWarning) {
                                showPriceWarningModal(function() {
                                    var form = document.getElementById('edit-ride-form');
                                    if (form) {
                                        var b = document.createElement('input');
                                        b.type = 'hidden';
                                        b.name = 'bypass_price_validation';
                                        b.value = '1';
                                        form.appendChild(b);
                                        form.submit();
                                    }
                                });
                                return;
                            }
                            document.getElementById('edit-ride-form').submit();
                        }).catch(function() {
                            document.getElementById('edit-ride-form').submit();
                        });
                        return;
                    }
                }

                // Function to check if hidden input already exists for a field
                function hasHiddenInput(form, name) {
                    return form.querySelector(`input[type="hidden"][name="${name}"]`) !== null;
                }

                // Function to add hidden input only if it doesn't exist
                function ensureHiddenInput(form, name, value) {
                    if (value === null || value === undefined || value === '') return;

                    // Check if hidden input already exists (from server-side)
                    if (hasHiddenInput(form, name)) {
                        // Update existing hidden input value
                        const existing = form.querySelector(`input[type="hidden"][name="${name}"]`);
                        if (existing) {
                            existing.value = value;
                        }
                        return;
                    }

                    // Create new hidden input only if it doesn't exist
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = name;
                    hiddenInput.value = value;
                    form.appendChild(hiddenInput);
                }

                // Handle all disabled select fields
                const disabledSelects = this.querySelectorAll('select[disabled]');
                disabledSelects.forEach(select => {
                    if (select.disabled && select.value && select.name) {
                        ensureHiddenInput(this, select.name, select.value);
                    }
                });

                // Handle all disabled radio buttons (only submit checked ones)
                const disabledRadios = this.querySelectorAll('input[type="radio"][disabled]');
                const radioGroups = {};
                disabledRadios.forEach(radio => {
                    if (radio.disabled && radio.name) {
                        if (!radioGroups[radio.name]) {
                            radioGroups[radio.name] = [];
                        }
                        radioGroups[radio.name].push(radio);
                    }
                });

                // For each radio group, submit the checked value
                Object.keys(radioGroups).forEach(name => {
                    const checkedRadio = radioGroups[name].find(radio => radio.checked);
                    if (checkedRadio && checkedRadio.value) {
                        ensureHiddenInput(this, name, checkedRadio.value);
                    }
                });

                // Handle disabled checkboxes (only if they don't already have hidden inputs)
                const disabledCheckboxes = this.querySelectorAll('input[type="checkbox"][disabled]');
                const checkboxGroups = {};

                disabledCheckboxes.forEach(checkbox => {
                    if (checkbox.disabled && checkbox.name) {
                        // Group checkboxes by name (for array fields like features[])
                        if (!checkboxGroups[checkbox.name]) {
                            checkboxGroups[checkbox.name] = [];
                        }
                        checkboxGroups[checkbox.name].push(checkbox);
                    }
                });

                // Handle each checkbox group
                Object.keys(checkboxGroups).forEach(name => {
                    const checkboxes = checkboxGroups[name];
                    const checkedBoxes = checkboxes.filter(cb => cb.checked);

                    // For array fields (like features[]), submit all checked values
                    if (name.endsWith('[]')) {
                        // For array fields, we need to ensure all checked disabled checkboxes have hidden inputs
                        // Check which values already have hidden inputs
                        const existingHidden = Array.from(this.querySelectorAll(
                                `input[type="hidden"][name="${name}"]`))
                            .map(input => input.value);

                        // Add hidden inputs only for checked checkboxes that don't already have hidden inputs
                        checkedBoxes.forEach(checkbox => {
                            const value = checkbox.value || '1';
                            if (!existingHidden.includes(value)) {
                                ensureHiddenInput(this, name, value);
                            }
                        });
                    } else {
                        // For non-array checkboxes, check if hidden input already exists
                        if (!hasHiddenInput(this, name)) {
                            const checkedBox = checkboxes.find(cb => cb.checked);
                            if (checkedBox) {
                                ensureHiddenInput(this, name, checkedBox.value || '1');
                            } else {
                                // For unchecked disabled checkboxes, submit 0
                                ensureHiddenInput(this, name, '0');
                            }
                        }
                    }
                });

                // Ensure readonly text fields are not also disabled (readonly fields ARE submitted)
                const readonlyFields = this.querySelectorAll('input[readonly], textarea[readonly]');
                readonlyFields.forEach(field => {
                    if (field.hasAttribute('disabled')) {
                        field.removeAttribute('disabled');
                    }
                });

                // Handle vehicle fields specifically - ensure readonly fields are not disabled
                const skipVehicle = document.getElementById('skipVehicle');
                if (skipVehicle) {
                    const makeField = skipVehicle.querySelector('input[name="make"]');
                    const modelField = skipVehicle.querySelector('input[name="model"]');
                    const yearField = skipVehicle.querySelector('input[name="year"]');
                    const colorField = skipVehicle.querySelector('input[name="color"]');
                    const licenseNoField = skipVehicle.querySelector('input[name="license_no"]');

                    // Ensure readonly vehicle text fields are not disabled
                    [makeField, modelField, yearField, colorField, licenseNoField].forEach(field => {
                        if (field && field.hasAttribute('readonly') && field.hasAttribute('disabled')) {
                            field.removeAttribute('disabled');
                        }
                    });
                }
            } finally {
                if (e.defaultPrevented) {
                    var f = document.getElementById('edit-ride-form');
                    if (f) f.querySelectorAll('.edit-ride-submit-btn').forEach(function(btn) {
                        btn.disabled = false;
                    });
                }
            }
        });


        function fromInput(index) {
            debounce(function() {
                let searchTerm = $('#from_spot_' + index).val();
                if (searchTerm.length >= 2) {
                    let searchData = $('#to_spot_' + index).val();
                    fetchCities(searchTerm, searchData, 'from_spot', index);
                }
            }, 500)();
        }

        function toInput(index) {
            debounce(function() {
                let searchTerm = $('#to_spot_' + index).val();
                if (searchTerm.length >= 2) {
                    let searchData = $('#from_spot_' + index).val();
                    fetchCities(searchTerm, searchData, 'to_spot', index);
                }
            }, 500)();
        }

        function stopInput(index) {
            debounce(function() {
                var searchTerm = $('#stop_spot_' + index).val();
                if (searchTerm.length >= 2) {
                    if (typeof fetchCities !== 'undefined') {
                        fetchCities(searchTerm, '', 'stop_spot', index);
                    }
                }
            }, 500)();
        }

        function fromToInputChange(index) {
            let searchTerm = $('#to_spot_' + index + '').val();
            let searchData = $('#from_spot_' + index + '').val();
            if (searchTerm != "" && searchData != "") {
                fetchRecommendedPrice(searchTerm, searchData, index);
            }
        }


        var deleteStopTargetRow = null;

        function addStopRow() {
            var container = document.getElementById('stops-rows-container');
            if (!container) return;
            var rows = container.querySelectorAll('.stop-row');
            var nextIndex = 1;
            rows.forEach(function(r) {
                var idx = parseInt(r.getAttribute('data-stop-index'), 10);
                if (!isNaN(idx)) nextIndex = Math.max(nextIndex, idx + 1);
            });
            var row = document.createElement('div');
            row.className = 'flex items-center gap-3 stop-row';
            row.setAttribute('data-stop-index', nextIndex);
            row.innerHTML = '<div class="flex flex-row gap-2 items-stretch flex-1 min-w-0">' +
                '<div class="relative flex-1 min-w-0">' +
                '<div class="absolute inset-y-0 start-0 flex items-center pl-2 pointer-events-none"><img src="{{ asset('assets/search-bar-from.png') }}" class="w-auto h-6" alt=""></div>' +
                '<input type="text" name="stop_spot_display[]" data-stop-index="' + nextIndex + '" id="stop_spot_' +
                nextIndex +
                '" value="" autocomplete="off" class="bg-gray-100 border border-gray-200 pl-7 text-gray-900 text-base lg:text-lg rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 block w-full p-2.5" placeholder="">' +
                '<div class="absolute hidden mt-1 z-10 left-0 top-full" id="stopInputError_' + nextIndex +
                '"><div class="tooltip-error shadow-lg rounded p-2 bg-red-500 text-white text-sm lg:text-base"></div></div>' +
                '</div>' +
                '<textarea name="stop_pickup_dropoff[]" data-stop-index="' + nextIndex + '" id="stop_pickup_dropoff_' +
                nextIndex +
                '" rows="1" placeholder="pick up / drop off" class="flex-1 min-w-0 bg-gray-100 border border-gray-200 text-gray-900 text-base lg:text-lg rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 block w-full p-2.5 resize-none"></textarea>' +
                '</div>' +
                '<button type="button" class="stop-delete-btn flex-shrink-0 p-2 text-red-600 hover:text-red-700 hover:bg-red-50 rounded focus:outline-none focus:ring-2 focus:ring-red-400" onclick="confirmDeleteStop(this)" title="Delete stop" aria-label="Delete stop">' +
                '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>' +
                '</button>';
            container.appendChild(row);
            var newStopInput = document.getElementById('stop_spot_' + nextIndex);
            if (newStopInput && typeof attachStopAutocompleteEditRide === 'function') attachStopAutocompleteEditRide(
                newStopInput);
            var wrapper = document.getElementById('stops-section-wrapper');
            if (wrapper) {
                var segIds = [];
                try {
                    segIds = JSON.parse(wrapper.getAttribute('data-segment-ids') || '[]');
                } catch (e) {}
                var totalRows = container.querySelectorAll('.stop-row').length;
                var neededSegments = totalRows + 1;
                while (segIds.length < neededSegments) segIds.push(0);
                wrapper.setAttribute('data-segment-ids', JSON.stringify(segIds));
            }
            if (typeof syncSegmentPricesUI === 'function') syncSegmentPricesUI();
        }

        function confirmDeleteStop(btn) {
            var row = btn && btn.closest('.stop-row');
            if (!row) return;
            deleteStopTargetRow = row;
            var modal = document.getElementById('delete-stop-modal');
            if (modal) modal.classList.remove('hidden');
        }

        function closeDeleteStopModal() {
            deleteStopTargetRow = null;
            var modal = document.getElementById('delete-stop-modal');
            if (modal) modal.classList.add('hidden');
        }

        function deleteStopRowConfirmed() {
            if (!deleteStopTargetRow) {
                closeDeleteStopModal();
                return;
            }
            var row = deleteStopTargetRow;
            var container = row.parentNode;
            var stopIndex = parseInt(row.getAttribute('data-stop-index'), 10);
            if (isNaN(stopIndex)) stopIndex = 0;
            var wrapper = document.getElementById('stops-section-wrapper');
            var isFirst = container.querySelector('.stop-row') === row;
            row.remove();
            if (wrapper) {
                var segIds = [];
                try {
                    segIds = JSON.parse(wrapper.getAttribute('data-segment-ids') || '[]');
                } catch (e) {}
                var remainingRows = container.querySelectorAll('.stop-row').length;
                if (remainingRows === 0) {
                    wrapper.setAttribute('data-segment-ids', JSON.stringify([]));
                } else if (segIds.length > 0 && stopIndex >= 1 && stopIndex < segIds.length) {
                    var newIds = segIds.slice(0, stopIndex - 1).concat(0).concat(segIds.slice(stopIndex + 1));
                    wrapper.setAttribute('data-segment-ids', JSON.stringify(newIds));
                }
            }
            reindexStopRows();
            var remaining = document.getElementById('stops-rows-container').querySelectorAll('.stop-row').length;
            if (remaining === 0) {
                addStopRow();
            }
            if (typeof syncSegmentPricesUI === 'function') syncSegmentPricesUI();
            closeDeleteStopModal();
        }

        function reindexStopRows() {
            var container = document.getElementById('stops-rows-container');
            if (!container) return;
            var rows = container.querySelectorAll('.stop-row');
            rows.forEach(function(r, i) {
                var idx = i + 1;
                r.setAttribute('data-stop-index', idx);
                var input = r.querySelector('input[name="stop_spot_display[]"]');
                if (input) {
                    input.setAttribute('data-stop-index', idx);
                    input.id = 'stop_spot_' + idx;
                }
                var errDiv = r.querySelector('[id^="stopInputError_"]');
                if (errDiv) errDiv.id = 'stopInputError_' + idx;
                var textarea = r.querySelector('textarea[name="stop_pickup_dropoff[]"]');
                if (textarea) {
                    textarea.setAttribute('data-stop-index', idx);
                    textarea.id = 'stop_pickup_dropoff_' + idx;
                }
            });
        }

        function addNewRow() {
            var oldIndex = parseInt($("#rowCount").val(), 10);
            var fromVal = $("#from_spot_" + oldIndex).val();
            var toVal = $("#to_spot_" + oldIndex).val();
            var priceVal = $("#priceData" + oldIndex).val();
            if (!fromVal || fromVal.trim() === "") {
                alert("Please select from spot");
                return;
            }
            if (!toVal || toVal.trim() === "") {
                alert("Please select to spot");
                return;
            }
            if (priceVal === "" || priceVal == null) {
                alert("Please enter price");
                return;
            }
            var index = oldIndex + 1;
            $.ajax({
                url: "{{ url('add-new-spots') }}",
                type: "POST",
                data: {
                    from_spot: fromVal,
                    to_spot: toVal,
                    price: priceVal,
                    index: index,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(result) {
                    if (result.status === 'error' && result.errors) {
                        if (result.errors.from_spot) alert(result.errors.from_spot[0]);
                        else if (result.errors.to_spot) alert(result.errors.to_spot[0]);
                        else if (result.errors.price) alert(result.errors.price[0]);
                        return;
                    }
                    if (result.spotHtml) {
                        $(".appendNewRow").append(result.spotHtml);
                        $("#rowCount").val(index);
                    }
                },
                error: function(xhr) {
                    var msg = (xhr.responseJSON && xhr.responseJSON.errors) ? (xhr.responseJSON.errors
                        .from_spot || xhr.responseJSON.errors.to_spot || xhr.responseJSON.errors.price || []
                        )[0] : "Failed to add spot.";
                    if (msg) alert(msg);
                }
            });
        }

        function removeRow(index, rideDetailId) {
            if (index != 1) {
                if (rideDetailId != 0) {
                    $.ajax({
                        url: "{{ url('delete-spots') }}",
                        type: "POST",
                        data: {
                            rideDetailId: rideDetailId,
                            _token: '{{ csrf_token() }}'
                        },
                        dataType: 'json',
                        success: function(result) {
                            if (result.status == "error") {
                                alert(result.message);
                            }
                        }
                    });
                }

                $(".remove-row" + index + "").remove();
            }

        }

        // Function to fetch cities based on search input
        function fetchCities(searchTerm, searchData, fieldId, fieldIndex) {
            // Get the state_id (if required) or set it to null or default
            let stateId = 0; // You can adjust this if you need to pass state_id
            let url = '{{ url('get-cities-by-state') }}';
            let params = {
                state_id: stateId,
                search: searchTerm,
                searchData: searchData
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
                    let suggestionsContainer = $('#' + fieldId + '_suggestions' + fieldIndex + '');
                    suggestionsContainer.empty(); // Clear previous suggestions

                    $.each(result.cities, function(key, value) {
                        // Create a list item for each city
                        let displayText =
                            `${value.name}, ${value.state.abrv}, ${value.state.country.name}`;

                        let suggestionItem = $(
                                '<div class="suggestion-item p-2 hover:bg-gray-200 cursor-pointer"></div>'
                                )
                            .text(displayText)
                            .on('click', function() {
                                $('#' + fieldId + '_' + fieldIndex + '').val(displayText);
                                fromToInputChange(
                                fieldIndex); // Set the selected city in the input field
                                suggestionsContainer.empty(); // Clear the suggestions
                            });

                        suggestionsContainer.append(suggestionItem);
                    });
                }
            });
        }


        // Function to fetch recommended price based on search input
        function fetchRecommendedPrice(searchTerm, searchData, index) {
            let stateId = 0;
            let url = '{{ url('get-cities-distance') }}';
            let params = {
                search: searchTerm,
                searchData: searchData
            };

            $.ajax({
                url: "{{ url('get-cities-distance') }}",
                type: "POST",
                data: {
                    search: searchTerm,
                    searchData: searchData,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(result) {
                    $("#priceData" + index + "").val(result.pricePerKm);
                    // Store distance for price validation (same as post_ride)
                    const distanceValue = result.distance || (result.data && result.data.distance) || null;
                    if (distanceValue && parseFloat(distanceValue) > 0) {
                        const distanceKm = parseFloat(distanceValue);
                        $("#priceData" + index + "").data('distance', distanceKm);
                        window.rideDistance = distanceKm;
                    }
                }
            });
        }

        // Cost-sharing cap validation (same formula as post_ride: Google Maps distance × cap ÷ seats)
        const ERROR_TRIGGERING_CAP = 0.72;
        const SOFT_WARNING_CAP = 0.66;
        window.rideDistance = null;

        function fetchDistance(from, to) {
            return new Promise(function(resolve, reject) {
                if (!from || !to) {
                    resolve(null);
                    return;
                }
                $.ajax({
                    url: "{{ url('get-cities-distance') }}",
                    type: "POST",
                    data: {
                        search: from,
                        searchData: to,
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: 'json',
                    success: function(result) {
                        const d = result.distance || (result.data && result.data.distance) || null;
                        resolve(d != null ? parseFloat(d) : null);
                    },
                    error: function() {
                        reject();
                    }
                });
            });
        }

        function validatePricePerSeat(price, distance, seats) {
            if (!price || !distance || !seats || distance <= 0 || price <= 0 || seats <= 0) {
                return {
                    valid: true,
                    type: null
                };
            }
            const pricePerSeat = parseFloat(price);
            const distanceKm = parseFloat(distance);
            const numSeats = parseInt(seats, 10);
            const maxPricePerSeat = (distanceKm * ERROR_TRIGGERING_CAP) / numSeats;
            const softWarningPricePerSeat = (distanceKm * SOFT_WARNING_CAP) / numSeats;
            if (pricePerSeat > maxPricePerSeat) {
                return {
                    valid: false,
                    type: 'error',
                    maxPricePerSeat: maxPricePerSeat.toFixed(2)
                };
            }
            if (pricePerSeat > softWarningPricePerSeat) {
                return {
                    valid: true,
                    type: 'warning',
                    softWarningPrice: softWarningPricePerSeat.toFixed(2)
                };
            }
            return {
                valid: true,
                type: null
            };
        }

        function showPriceErrorModal(maxPricePerSeat) {
            const modal = document.getElementById('priceErrorModal');
            if (modal) {
                document.getElementById('priceErrorParagraph1').textContent =
                    'To comply with Canadian and Quebec carpooling regulations, the total amount collected for a trip cannot exceed the official 2026 reimbursement rate of $0.72/km.';
                document.getElementById('priceErrorParagraph2').textContent =
                    'The maximum allowed for this trip is $' + maxPricePerSeat + ' per seat.';
                document.getElementById('priceErrorParagraph3').textContent =
                    'This limit is mandatory to ensure your ride is classified as a non-commercial carpool, protecting your insurance coverage and maintaining the cost-sharing status of your contributions.';
                modal.classList.remove('hidden');
                modal.style.display = 'block';
            }
        }

        function showPriceWarningModal(callback) {
            const modal = document.getElementById('priceWarningModal');
            if (!modal) return;
            var para1 = document.getElementById('priceWarningParagraph1');
            var para2 = document.getElementById('priceWarningParagraph2');
            if (para1) para1.textContent =
                'The price you entered is above the standard reimbursement rate recommended by the CRA and Revenu Québec';
            if (para2) para2.textContent =
                'While you can proceed, we suggest reducing the price per seat. This ensures your ride remains a standard carpool even if you drive long distances this year.';
            modal.classList.remove('hidden');
            modal.style.setProperty('display', 'block', 'important');
            var continueBtn = document.getElementById('priceWarningContinue');
            if (continueBtn) {
                var newBtn = continueBtn.cloneNode(true);
                continueBtn.parentNode.replaceChild(newBtn, continueBtn);
                newBtn.onclick = function(e) {
                    e.preventDefault();
                    modal.classList.add('hidden');
                    modal.style.display = 'none';
                    if (callback) callback();
                };
            }
        }

        function closePriceErrorModal() {
            var m = document.getElementById('priceErrorModal');
            if (m) {
                m.classList.add('hidden');
                m.style.display = 'none';
            }
        }

        function closePriceWarningModal() {
            var m = document.getElementById('priceWarningModal');
            if (m) {
                m.classList.add('hidden');
                m.style.display = 'none';
            }
        }

        function focusEditRidePriceInput() {
            var section = document.getElementById('edit-ride-price-section');
            if (section) section.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
            var priceInput = document.getElementById('priceData0');
            if (priceInput) setTimeout(function() {
                priceInput.focus();
                priceInput.select();
            }, 300);
        }

        function adjustPriceFromError() {
            closePriceErrorModal();
            focusEditRidePriceInput();
        }

        function adjustPriceFromWarning() {
            closePriceWarningModal();
            focusEditRidePriceInput();
            return false;
        }

        function closeModal() {
            const modal = document.getElementById('myModal');
            if (modal) {
                modal.classList.add('hidden');
            }
        }

        // Helper function to show tooltip on a field
        function showFieldTooltip(field, message) {
            if (!field) return;

            // Remove existing tooltip if any
            removeFieldTooltip(field);

            // Add error styling
            field.classList.add('validation-error-border', 'border-red-500', 'ring-red-500');

            // Create tooltip element
            const tooltip = document.createElement('div');
            tooltip.className = 'validation-tooltip';
            tooltip.innerHTML = `
            <div class="validation-tooltip-arrow"></div>
            <div class="validation-tooltip-content">${message}</div>
        `;

            // Insert tooltip after the field
            field.parentNode.insertBefore(tooltip, field.nextSibling);
        }

        // Helper function to remove tooltip from a field
        function removeFieldTooltip(field) {
            if (!field) return;
            field.classList.remove('validation-error-border', 'border-red-500', 'ring-red-500');
            const existingTooltip = field.parentNode.querySelector('.validation-tooltip');
            if (existingTooltip) {
                existingTooltip.remove();
            }
        }

        // Helper function to show tooltip on checkbox
        function showCheckboxTooltip(checkbox, message) {
            if (!checkbox) return;

            // Remove existing tooltip if any
            removeCheckboxTooltip(checkbox);

            // Add error styling to checkbox
            checkbox.classList.add('validation-error-border', 'ring-2', 'ring-red-500');

            // Find the parent container (the div containing checkbox and label)
            const container = checkbox.closest('.flex') || checkbox.parentNode;
            if (!container) return;

            // Create tooltip element
            const tooltip = document.createElement('div');
            tooltip.className = 'validation-tooltip checkbox-tooltip';
            tooltip.innerHTML = `
            <div class="validation-tooltip-arrow"></div>
            <div class="validation-tooltip-content">${message}</div>
        `;

            // Insert tooltip after the container
            container.parentNode.insertBefore(tooltip, container.nextSibling);
        }

        // Helper function to remove tooltip from checkbox
        function removeCheckboxTooltip(checkbox) {
            if (!checkbox) return;
            checkbox.classList.remove('validation-error-border', 'ring-2', 'ring-red-500');
            const container = checkbox.closest('.flex') || checkbox.parentNode;
            if (!container) return;
            // Look for the tooltip as the next sibling of the container
            let nextEl = container.nextElementSibling;
            if (nextEl && nextEl.classList.contains('checkbox-tooltip')) {
                nextEl.remove();
            }
        }

        // Clear tooltip when user focuses, starts typing or changes value
        function setupTooltipClearOnInput() {
            document.querySelectorAll('input, select, textarea').forEach(field => {
                field.addEventListener('focus', function() {
                    removeFieldTooltip(this);
                });
                field.addEventListener('input', function() {
                    removeFieldTooltip(this);
                });
                field.addEventListener('change', function() {
                    removeFieldTooltip(this);
                });
            });

            // Add listener for Terms & Conditions checkbox
            const agreeCheckbox = document.getElementById('agree_checkbox');
            if (agreeCheckbox) {
                agreeCheckbox.addEventListener('change', function() {
                    // Remove highlight when checkbox is checked
                    this.classList.remove('validation-error-border', 'ring-2', 'ring-red-500');
                });
            }
        }

        // Initialize tooltip clear listeners
        document.addEventListener('DOMContentLoaded', setupTooltipClearOnInput);

        // Form validation before submission
        function validateEditRideForm() {
            let isValid = true;
            let firstErrorField = null;

            // Get required field values
            const fromSpot = document.getElementById('from_spot_0');
            const toSpot = document.getElementById('to_spot_0');
            const dateInput = document.querySelector('input[name="departure_date"]');
            const timeInput = document.querySelector('input[name="departure_time"]');
            const seatsInput = document.querySelector('select[name="max_passengers"], input[name="max_passengers"]');
            const priceInput = document.getElementById('priceData0');
            const makeInput = document.querySelector('input[name="make"]');
            const modelInput = document.querySelector('input[name="model"]');
            const typeInput = document.querySelector('select[name="vehicle_type"]');

            // Clear all previous tooltips
            document.querySelectorAll('.validation-tooltip').forEach(el => el.remove());
            document.querySelectorAll('.validation-error-border').forEach(el => {
                el.classList.remove('validation-error-border', 'border-red-500', 'ring-red-500');
            });

            // Validate From location
            if (!fromSpot || !fromSpot.value.trim()) {
                isValid = false;
                showFieldTooltip(fromSpot, 'From location is required');
                if (!firstErrorField) firstErrorField = fromSpot;
            }

            // Validate To location
            if (!toSpot || !toSpot.value.trim()) {
                isValid = false;
                showFieldTooltip(toSpot, 'To location is required');
                if (!firstErrorField) firstErrorField = toSpot;
            }

            // Validate Date
            if (!dateInput || !dateInput.value.trim()) {
                isValid = false;
                showFieldTooltip(dateInput, 'Departure date is required');
                if (!firstErrorField) firstErrorField = dateInput;
            }

            // Validate Time
            if (!timeInput || !timeInput.value.trim()) {
                isValid = false;
                showFieldTooltip(timeInput, 'Departure time is required');
                if (!firstErrorField) firstErrorField = timeInput;
            }

            // Validate Seats
            if (!seatsInput || !seatsInput.value.trim()) {
                isValid = false;
                showFieldTooltip(seatsInput, 'Number of seats is required');
                if (!firstErrorField) firstErrorField = seatsInput;
            }

            // Validate Price
            if (!priceInput || !priceInput.value.trim()) {
                isValid = false;
                showFieldTooltip(priceInput, 'Price is required');
                if (!firstErrorField) firstErrorField = priceInput;
            }

            // Validate Make
            if (!makeInput || !makeInput.value.trim()) {
                isValid = false;
                showFieldTooltip(makeInput, 'Vehicle make is required');
                if (!firstErrorField) firstErrorField = makeInput;
            }

            // Validate Model
            if (!modelInput || !modelInput.value.trim()) {
                isValid = false;
                showFieldTooltip(modelInput, 'Vehicle model is required');
                if (!firstErrorField) firstErrorField = modelInput;
            }

            // Validate Vehicle Type
            if (!typeInput || !typeInput.value.trim()) {
                isValid = false;
                showFieldTooltip(typeInput, 'Vehicle type is required');
                if (!firstErrorField) firstErrorField = typeInput;
            }

            // Validate Terms & Conditions checkbox
            const agreeTermsCheckbox = document.getElementById('agree_checkbox');
            if (agreeTermsCheckbox && !agreeTermsCheckbox.checked) {
                isValid = false;
                // Just highlight the checkbox, no tooltip
                agreeTermsCheckbox.classList.add('validation-error-border', 'ring-2', 'ring-red-500');
                if (!firstErrorField) firstErrorField = agreeTermsCheckbox;
            }

            // Scroll to first error field
            if (!isValid && firstErrorField) {
                firstErrorField.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
                firstErrorField.focus();
            }

            return isValid;
        }
    </script>

    <style>
        .validation-tooltip {
            position: relative;
            margin-top: 4px;
        }

        .validation-tooltip-arrow {
            width: 0;
            height: 0;
            border-left: 8px solid transparent;
            border-right: 8px solid transparent;
            border-bottom: 8px solid #ef4444;
            margin-left: 10px;
        }

        .validation-tooltip-content {
            background-color: #ef4444;
            color: white;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 14px;
            display: inline-block;
        }

        .validation-error-border {
            border-color: #ef4444 !important;
        }
    </style>
@endsection
