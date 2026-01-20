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
        .tooltip_width{
            width: 16.5rem;
        }
        .tooltip_position{
            right: 13rem;
            top: -7.5rem;
        }
        .luggage_tooltiptext::after{
            right: 3.3rem;
        }
        .payment_tooltiptext_position{
            top: -6.3rem;
        }
    }
    @media only screen and (min-width:376px) and (max-width: 639px) {
        .tooltip_width{
            width: 20rem;
        }
        .tooltip_position{
            right: 16.5rem;
            top: -6.5rem;
        }
        .luggage_tooltiptext::after{
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
</style>
@endsection

@section('content')

{{-- Early function definitions to prevent "not defined" errors on page load --}}
<script>
    // Placeholder functions that will be properly initialized after jQuery loads
    function fromInput(index) {
        // Will be overwritten when full script loads
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
        // Will be overwritten when full script loads
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

<div class="container px-4 mx-auto my-14 page-post_a_ride">
    @if(session('error'))
        <div id="myModal" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div onclick="closeModal()" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                    <div
                        class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg w-full modal-border">
                        <button type="button" onclick="closeModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-500 z-50">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                        <div class="bg-white px-4 mt-10 sm:mt-1 pb-4 pt-16 sm:p-6 sm:pb-4 sm:pt-16">
                            <div class="sm:flex sm:items-start justify-center">
                                <!-- <div
                                    class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full sm:mx-0 sm:h-10 sm:w-10 bg-red-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-lg text-white w-8 h-8" viewBox="0 0 16 16">
                                        <path d="M7.005 3.1a1 1 0 1 1 1.99 0l-.388 6.35a.61.61 0 0 1-1.214 0zM7 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0"/>
                                    </svg>
                                </div> -->
                            </div>
                            <div class="text-center sm:ml-4 sm:mt-0 sm:text-left">
                                <div class="">
                                    <h3 class="text-3xl text-center font-FuturaMdCnBT text-gray-900 mb-4" id="modal-title">{!! session('heading') !!}</h3>
                                </div>
                                <div class="mt-2 w-full">
                                    <p class="can-exp-p text-center">{!! session('error') !!}</p>
                                </div>
                            </div>
                        </div>
                        <div class="px-4 pb-6 pt-4  sm:flex sm:flex-row-reverse sm:px-6 justify-center">
                            <button type="button" onclick="closeModal()" class="inline-flex justify-center rounded bg-red-500 px-3 py-2 font-FuturaMdCnBT text-lg text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-red-400 sm:ml-3 w-28">
                                Close
                            </button>
                            {{-- <a href=""
                                class="inline-flex w-full justinline-flex justify-center rounded bg-red-500 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-red-400 sm:ml-3 sm:w-24">Close</a> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    @if(session('message'))
        <div id="myModal" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div onclick="closeModal()" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                    <div
                        class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
                        <button type="button" onclick="closeModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-500 z-50">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                        <div class="bg-white px-4 mt-10 sm:mt-1 pb-4 pt-16 sm:p-6 sm:pb-4 sm:pt-16">
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
                                    <h3 class="text-3xl text-center font-FuturaMdCnBT text-gray-900 mb-4" id="modal-title">{!! session('heading') !!}</h3>
                                </div>
                                <div class="mt-2 w-full">
                                    <p class="can-exp-p text-center">{!! session('message') !!}</p>
                                </div>
                            </div>
                        </div>
                        <div class="px-4 pb-6 pt-4 flex items-center space-x-2 sm:space-x-4 sm:px-6 justify-center">
                            @if(session('id'))
                                <a href="{{ route('repost_ride', ['lang' => $selectedLanguage->abbreviation, 'id' => session('id')]) }}"
                                    class="inline-flex w-full justify-center rounded bg-greenXS px-3 py-2 font-FuturaMdCnBT text-lg text-white whitespace-nowrap hover:text-white hover:shadow-lg shadow-sm hover:bg-greenXS sm:ml-3 sm:w-fit">Post a Return Ride</a>
                            @endif
                            <a href=""
                                class="button-exp-fill">Close</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <div id="myModal" class="hidden relative z-50" id="extra-care-ride-modal" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div onclick="closeModal()" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                <div
                    class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
                    <button type="button" onclick="closeExtraCareRideModal()" class="absolute top-3 right-3 text-gray-400 hover:text-gray-500">
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
                                <h3 class="text-3xl text-center font-FuturaMdCnBT text-gray-900 mb-4" id="modal-title">{!! session('heading') !!}</h3>
                            </div>
                            <div class="mt-2 w-full">
                                {{-- <p class="can-exp-p text-center">{!! session('message') !!}</p> --}}
                                <p class="can-exp-p text-center">{{ $postRideSubDetailPage->extra_care_popup_eligible_text??'' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="px-4 pb-6 pt-4 flex items-center space-x-2 sm:space-x-4 sm:px-6 justify-center">
                        @if(session('id'))
                            <a href="{{ route('repost_ride', ['lang' => $selectedLanguage->abbreviation, 'id' => session('id')]) }}"
                                class="inline-flex w-full justify-center rounded bg-greenXS px-3 py-2 font-FuturaMdCnBT text-lg text-white whitespace-nowrap hover:text-white hover:shadow-lg shadow-sm hover:bg-greenXS sm:ml-3 sm:w-fit">Repost ride</a>
                        @endif
                        <button type="button" onclick="closeExtraCareRideModal()"
                            class="button-exp-fill">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="flex flex-col sm:flex-col md:flex-row lg:flex-row justify-between md:items-center">
        <h1>
            @isset($postRidePage->main_heading)
                {{ $postRidePage->main_heading }}
            @endisset

        </h1>
        <a href="{{ route('post_ride_again', ['lang' => optional($selectedLanguage)->abbreviation]) }}" class="bg-greenXS hover:bg-greenXS text-white text-base md:text-lg rounded font-FuturaMdCnBT hover:font-FuturaMdCnBT px-5 py-2 border border-greenXS hover:border-greenXS hover:text-white text-center focus:bg-greenXS focus:text-white active:text-white active:bg-greenXS">
            @isset($postRidePage->post_arrived_again_label)
                {{ $postRidePage->post_arrived_again_label }}
            @endisset
        </a>
    </div>
    <div class="pt-1 flex mt-2 md:mt-0">
        <p>
            <span class="text-red-500">* {{ $postRideSubDetailPage->feilds_required_text??'Indicates required fields' }} </span>
        </p>
    </div>
    <form method="POST" action="{{ route('post_ride.store') }}" enctype="multipart/form-data" id="post-ride-form">
        @csrf
            <div class="">
                <div class="bg-white rounded-lg overflow-hidden shadow-3xl">
                    <h3 class="text-2xl bg-primary text-white py-2 px-4">
                        @isset($postRidePage->ride_info_heading)
                            {{ $postRidePage->ride_info_heading }}
                        @endisset
                    </h3>
                    <div class="bg-white p-4 space-y-3">
                        <div class="flex flex-col md:flex-row justify-between items-start">
                            <div class="w-full md:w-[45%] mb-4">
                            <div>
                                <label for="from"
                                    class="block mb-2 text-gray-900">
                                    @isset($postRidePage->from_label)
                                        {{ $postRidePage->from_label }}
                                    @endisset
                                    <span class="text-red-500">*</span>
                                </label>
                                <div class="relative mt-2">
                                    <div class="absolute inset-y-0 start-0 flex items-center pl-2 pointer-events-none">
                                        <img src="{{ asset('assets/search-bar-from.png') }}" class="w-auto h-6" alt="">
                                    </div>

                                    @php
                                    $departure = $destination = "";
                                    if($routeType == "repost"){

                                        $departure = isset($ride->defaultRideDetail) && isset($ride->defaultRideDetail[0]) ? $ride->defaultRideDetail[0]->destination : "";

                                        $destination = isset($ride->defaultRideDetail) && isset($ride->defaultRideDetail[0]) ? $ride->defaultRideDetail[0]->departure : "";
                                    }else{


                                        $departure = isset($ride->defaultRideDetail) && isset($ride->defaultRideDetail[0]) ? $ride->defaultRideDetail[0]->departure : "";

                                        $destination = isset($ride->defaultRideDetail) && isset($ride->defaultRideDetail[0]) ? $ride->defaultRideDetail[0]->destination : "";
                                    }

                                    @endphp

                                    <input type="text" id="from_spot_0" name="from" value="{{ old('from', $departure) }}" oninput="fromInput('0')"
                                        class="bg-gray-100 border border-gray-200 pl-7 text-gray-900 text-base lg:text-lg rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 block w-full p-2.5 mt-2"
                                        @isset($postRidePage->from_placeholder)
                                            placeholder="{{ $postRidePage->from_placeholder }}"
                                        @endisset>

                                    <!-- Suggestions Container for 'from' field -->
                                    <div id="from_spot_suggestions0" class="absolute left-0 right-0 bg-white shadow-lg mt-1 max-h-60 overflow-y-auto z-50"></div>
                                </div>
                                @error('from')
                                  <div class="relative tooltip -bottom-4 group-hover:flex">
                                    <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                        <p class="text-white leading-none text-sm lg:text-base">{{ $message }} <a class="text-white leading-none text-sm lg:text-base" href="{{ route('contact_us', ['lang' => app()->getLocale()]) }}">
                                            {{ $postRideSubDetailPage->city_not_fount_contact_text ?? '' }}
                                        </a> </p>
                                    </div>
                                  </div>
                                @enderror
                            </div>
                            </div>
                            <div class="w-full md:w-[10%] md:mt-10 flex justify-center items-start">
                                <button type="button" onclick="swapLocations()">
                                    <img src="{{ asset('assets/arrow.png') }}" class="w-10 h-10 mx-auto" alt="">
                                </button>
                            </div>
                            <div class="w-full md:w-[45%] mb-4">
                                <div>
                                    <label for="to"
                                        class="block mb-2 text-gray-900">
                                        @isset($postRidePage->to_label)
                                            {{ $postRidePage->to_label }}
                                        @endisset
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative mt-2">
                                        <div class="absolute inset-y-0 start-0 flex items-center pl-2 pointer-events-none">
                                            <img src="{{ asset('images/new-21-search-bar-to.png') }}" class="w-auto h-6" alt="">
                                        </div>
                                        <input type="text" id="to_spot_0" name="to" value="{{ old('to', $destination) }}" oninput="toInput('0')"
                                            class="bg-gray-100 border pl-7 border-gray-200 text-base lg:text-lg text-gray-900  rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 mt-2 block w-full p-2.5"
                                            @isset($postRidePage->to_placeholder)
                                                placeholder="{{ $postRidePage->to_placeholder }}"
                                            @endisset>

                                        <!-- Suggestions Container for 'to' field -->
                                        <div id="to_spot_suggestions0" class="absolute left-0 right-0 bg-white shadow-lg mt-1 max-h-60 overflow-y-auto z-50"></div>
                                    </div>
                                    @error('to')
                                    <div class="relative tooltip -bottom-4 group-hover:flex">
                                        <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                            <p class="text-white leading-none text-sm lg:text-base">{{ $message }}

                                                <a class="text-white leading-none text-sm lg:text-base" href="{{ route('contact_us', ['lang' => app()->getLocale()]) }}">
                                                    {{ $postRideSubDetailPage->city_not_fount_contact_text ?? '' }}
                                                </a>
                                            </p>
                                        </div>
                                    </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="flex items-end flex-col md:flex-row justify-between mt-4">
                            <div class="w-full md:w-[45%] mb-4">
                                <label for="pickup_location" class="block mb-2 text-gray-900">
                                    @isset($postRidePage->pick_up_label)
                                        {{ $postRidePage->pick_up_label }}
                                    @endisset
                                    <span class="text-red-500">*</span>
                                </label>
                                <textarea id="pickup_location" rows="5" name="pickup"
                                class="block p-2.5 w-full text-gray-900 bg-gray-100 rounded border border-gray-200 text-base lg:text-lg focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 mt-2"
                                @isset($postRidePage->pick_up_placeholder)
                                    placeholder="{{ $postRidePage->pick_up_placeholder }}"
                                @endisset
                                >{{ old('pickup', $ride->pickup) }}</textarea>
                                @error('pickup')
                                <div class="relative tooltip -bottom-4 group-hover:flex">
                                    <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                        <p class="text-white leading-none text-sm lg:text-base">{{ $message }}
                                            <a class="text-white leading-none text-sm lg:text-base" href="{{ route('contact_us', ['lang' => app()->getLocale()]) }}">
                                                {{ $postRideSubDetailPage->city_not_fount_contact_text ?? '' }}
                                            </a>
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
                                    <span class="text-red-500">*</span>
                                </label>
                                <textarea id="dropoff_location" rows="5" name="dropoff"
                                class="block p-2.5 w-full text-gray-900 bg-gray-100 rounded border border-gray-200 text-base lg:text-lg focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 mt-2"
                                @isset($postRidePage->drop_off_placeholder)
                                    placeholder="{{ $postRidePage->drop_off_placeholder }}"
                                @endisset
                                >{{ old('dropoff', $ride->dropoff) }}</textarea>
                                @error('dropoff')
                                <div class="relative tooltip -bottom-4 group-hover:flex">
                                    <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                        <p class="text-white leading-none text-sm lg:text-base">{{ $message }}
                                            <a class="text-white leading-none text-sm lg:text-base" href="{{ route('contact_us', ['lang' => app()->getLocale()]) }}">
                                                {{ $postRideSubDetailPage->city_not_fount_contact_text ?? '' }}
                                            </a>
                                        </p>
                                    </div>
                                </div>
                                @enderror
                            </div>
                            {{-- <div class="map-container w-full h-64 block md:hidden">
                                <iframe
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3806.452697041917!2d78.39076592375736!3d17.43803374982052!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3bcb9144cdba8c47%3A0x937fe346f411a645!2sTutorials%20Point%20(India)%20Ltd.!5e0!3m2!1sen!2sin!4v1673629212535!5m2!1sen!2sin"
                                width="100%"
                                height="100%"
                                style="border:0;"
                                allowfullscreen="" loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade">
                                </iframe>
                            </div> --}}
                        </div>
                        <div>
                            <label for="date_time" class="block text-gray-900">
                                @isset($postRidePage->date_time_label)
                                    {{ $postRidePage->date_time_label }}
                                @endisset
                                <span class="text-red-500">*</span>
                            </label>
                            <div class="flex flex-col sm:flex-col md:flex-row lg:flow-row items-start mb-4 justify-between">
                                <div class="w-full md:w-[45%] mb-4">
                                    <div class="relative mt-2">
                                        <div class="absolute inset-y-0 start-0 flex items-center pl-2 pointer-events-none">
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
                                        <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                            <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                        </div>
                                    </div>
                                    @enderror
                                </div>
                                <div class="w-full md:w-[10%] md:mt-4 text-center">
                                    <span class="text-center text-base lg:text-lg ">
                                        @isset($postRidePage->at_label)
                                            {{ $postRidePage->at_label }}
                                        @endisset
                                    </span>
                                </div>
                                <div class="w-full md:w-[45%] mb-4">
                                    <div class="relative mt-2">
                                        <div class="absolute inset-y-0 start-0 flex items-center pl-2 pointer-events-none">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <input type="text" id="timeInput" name="time"
                                            class="bg-gray-100 border pl-10 border-gray-200 text-gray-900 text-base lg:text-lg rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 mt-2 block w-full p-2.5"
                                            placeholder="">
                                    </div>
                                    @error('time')
                                    <div class="relative tooltip -bottom-4 group-hover:flex">
                                        <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                            <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                        </div>
                                    </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center mb-4">
                            <input id="recurring_trip" type="checkbox" name="recurring" value="1" {{ old('recurring') === '1' ? 'checked' : '' }}
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
                                    @isset($postRidePage->recurring_type_label)
                                        {{ $postRidePage->recurring_type_label }}
                                    @endisset
                                    <span class="text-red-500">*</span>
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
                                    <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                        <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
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
                                    @isset($postRidePage->recurring_trips_label)
                                        {{ $postRidePage->recurring_trips_label }}
                                    @endisset
                                    <span class="text-red-500">*</span>
                                </label>
                                <div class="relative mt-2">
                                    <input type="number" min="1" max="10" name="recurring_trips" value="{{ old('recurring_trips') }}"
                                        @isset($postRidePage->recurring_trips_placeholder)
                                            placeholder="{{ $postRidePage->recurring_trips_placeholder }}"
                                        @endisset
                                        class="bg-gray-100 border border-gray-200 text-gray-900 text-base lg:text-lg rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 mt-2 block w-full p-2.5">
                                </div>
                                @error('recurring_trips')
                                  <div class="relative tooltip -bottom-4 group-hover:flex">
                                    <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                        <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                    </div>
                                  </div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
                <div class="mt-6">
                    <div class="bg-white rounded-lg overflow-hidden shadow-3xl">
                        <h3 class="text-2xl bg-primary text-white py-2 px-4">
                            @isset($postRidePage->meeting_drop_off_description_label)
                                {{ $postRidePage->meeting_drop_off_description_label }}
                            @endisset
                            <span class="text-white">*</span>
                        </h3>
                        <div class="bg-white p-4 space-y-3">
                            <textarea id="meeting" rows="5" name="details"
                              class="block p-2.5 w-full text-gray-900 bg-gray-100 rounded border border-gray-200 text-base lg:text-lg focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 mt-2"
                                @isset($postRidePage->meeting_drop_off_description_placeholder)
                                    placeholder="{{ $postRidePage->meeting_drop_off_description_placeholder }}"
                                @endisset>{{ old('details', $ride->details) }}</textarea>
                            @error('details')
                              <div class="relative tooltip -bottom-1 group-hover:flex">
                                <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                    <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                </div>
                              </div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="mt-6">
                    <div class="bg-white rounded-lg overflow-hidden shadow-3xl">
                        <div class="text-2xl bg-primary text-white py-2 px-4">
                            <label for="no_of_seats" class="text-lg lg:text-3xl font-FuturaMdCnBT mb-2">
                                <h3 class="text-2xl">
                                    @isset($postRidePage->seats_label)
                                        {{ $postRidePage->seats_label }}
                                    @endisset
                                    <span class="text-white">*</span>
                                </h3>
                            </label>
                        </div>
                        <div class="bg-white p-4">
                            <div class="flex items-center flex-wrap gap-2 mt-2">
                                @for ($i = 1; $i <= 7; $i++)
                                <div class="relative">
                                    <label class="cursor-pointer" for="number-of-seat-{{ $i }}">
                                        <input id="number-of-seat-{{ $i }}" name="seats" type="radio" value="{{ $i }}" class="hidden" {{ old('seats', $ride->seats) == $i ? 'checked' : '' }} onchange="seat_selected(this)" data-parsley-required="true" data-parsley-trigger="blur focusout change" data-parsley-required-message="Please select the available seats." data-parsley-errors-container="#parsley-seats-error">
                                        <img src="{{ old('seats', $ride->seats) >= $i ? asset('assets/seat-hover-1.png') : asset('assets/seat.png') }}" class="w-8 h-8 md:w-10 md:h-10 mt-0.5 cursor-pointer seat-image seat-unselect-{{ $i }}" alt="">
                                        <span class="absolute left-3 top-2 md:left-4 md:top-3 seat-number seat-number-{{ $i }} {{ old('seats', $ride->seats) >= $i ? 'text-green-300' : '' }}">{{ $i }}</span>
                                    </label>
                                </div>
                                @endfor
                                @error('seats')
                                <div class="relative tooltip -bottom-4 group-hover:flex">
                                    <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                        <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                    </div>
                                </div>
                                @enderror
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 mt-6 gap-4">
                                <div>
                                    <label for="pickup_location" class="text-gray-900 mb-2">
                                        @isset($postRidePage->seats_middle_label)
                                            {{ $postRidePage->seats_middle_label }}
                                        @endisset
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <ul class="grid gap-2 grid-cols-2 mt-2">
                                        <li>
                                            <input type="radio" id="2-seats" name="middle_seats" value="2" class="hidden peer"
                                                {{ old('middle_seats', $ride->middle_seats) == '2' ? 'checked' : '' }}>
                                            <label for="2-seats" class="inline-flex items-center justify-center w-full p-3 text-gray-800 bg-white border-2 border-gray-100 rounded cursor-pointer peer-checked:border-green-500 peer-checked:border-2 peer-checked:text-green-500 hover:border-2 hover:border-green-500">
                                                <span class="font-medium text-base">
                                                    2 seats
                                                </span>
                                            </label>
                                        </li>
                                        <li>
                                            <input type="radio" id="3-seats" name="middle_seats" value="3" class="hidden peer"
                                                {{ old('middle_seats', $ride->middle_seats) == '3' ? 'checked' : '' }}>
                                            <label for="3-seats" class="inline-flex items-center justify-center w-full p-3 text-gray-800 bg-white border-2 border-gray-100 rounded cursor-pointer peer-checked:border-green-500 peer-checked:border-2 peer-checked:text-green-500 hover:border-2 hover:border-green-500">
                                                <span class="font-medium text-base">3 seats</span>
                                            </label>
                                        </li>
                                    </ul>
                                    @error('middle_seats')
                                      <div class="relative tooltip -bottom-4 group-hover:flex">
                                        <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full rounded" >
                                            <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                        </div>
                                      </div>
                                    @enderror
                                </div>
                                <div>
                                    <label for="back_seats" class="text-gray-900 mb-2">
                                        @isset($postRidePage->seats_back_label)
                                            {{ $postRidePage->seats_back_label }}
                                        @endisset
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <ul class="grid gap-2 grid-cols-2 mt-2">
                                        <li>
                                            <input type="radio" id="2-back_seats" name="back_seats" value="2" class="hidden peer"
                                                {{ old('back_seats', $ride->back_seats) == '2' ? 'checked' : '' }}>
                                            <label for="2-back_seats" class="inline-flex items-center justify-center w-full p-3 text-gray-800 bg-white border-2 border-gray-100 rounded cursor-pointer peer-checked:border-green-500 peer-checked:border-2 peer-checked:text-green-500 hover:border-2 hover:border-green-500">
                                                <span class="font-medium text-base">
                                                    2 seats
                                                </span>
                                            </label>
                                        </li>
                                        <li>
                                            <input type="radio" id="3-back_seats" name="back_seats" value="3" class="hidden peer"
                                                {{ old('back_seats', $ride->back_seats) == '3' ? 'checked' : '' }}>
                                            <label for="3-back_seats" class="inline-flex items-center justify-center w-full p-3 text-gray-800 bg-white border-2 border-gray-100 rounded cursor-pointer peer-checked:border-green-500 peer-checked:border-2 peer-checked:text-green-500 hover:border-2 hover:border-green-500">
                                                <span class="font-medium text-base">3 seats</span>
                                            </label>
                                        </li>
                                    </ul>
                                    @error('back_seats')
                                      <div class="relative tooltip -bottom-4 group-hover:flex">
                                        <div role="tooltip" class="relative tooltiptext after:left-6 -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full rounded" >
                                            <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                        </div>
                                      </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-6 bg-white rounded-lg shadow-3xl">
                        <div class="text-2xl bg-primary text-white py-2 px-4 rounded-t-lg">
                            <h3 class="text-2xl">
                                @isset($postRidePage->price_payment_heading)
                                    {{ $postRidePage->price_payment_heading }}
                                @endisset
                                <span class="text-white">*</span>
                            </h3>
                        </div>
                        <div class="bg-white p-4">
                            <div>
                                <label for="" class=" text-gray-700 font-medium">
                                    @isset($postRidePage->price_per_seat_label)
                                        {{ $postRidePage->price_per_seat_label }}
                                    @endisset
                                    <span class="text-red-500">*</span>
                                </label>
                                <div class="relative mt-2">
                                    <span class="absolute inset-y-0 start-0 flex items-center pl-2 pointer-events-none">
                                        <svg fill="currentColor" width="800px" height="800px" viewBox="0 0 32 32" class="w-5 h-5 text-gray-500" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M 15 3 L 15 5.09375 C 12.164063 5.570313 10 8.050781 10 11 C 10 12.777344 10.832031 14.148438 11.9375 15.03125 C 13.042969 15.914063 14.375 16.40625 15.625 16.90625 C 16.875 17.40625 18.042969 17.914063 18.8125 18.53125 C 19.582031 19.148438 20 19.773438 20 21 C 20 23.15625 18.207031 25 16 25 C 13.78125 25 12 23.21875 12 21 L 12 20 L 10 20 L 10 21 C 10 23.964844 12.164063 26.429688 15 26.90625 L 15 29 L 17 29 L 17 26.90625 C 19.84375 26.425781 22 23.925781 22 21 C 22 19.21875 21.167969 17.855469 20.0625 16.96875 C 18.957031 16.082031 17.625 15.5625 16.375 15.0625 C 15.125 14.5625 13.957031 14.082031 13.1875 13.46875 C 12.417969 12.855469 12 12.21875 12 11 C 12 8.808594 13.785156 7 16 7 C 18.21875 7 20 8.78125 20 11 L 20 12 L 22 12 L 22 11 C 22 8.035156 19.835938 5.570313 17 5.09375 L 17 3 Z"/>
                                        </svg>
                                    </span>
                                    @php
                                        $defaultPrice = isset($ride->defaultRideDetail) && isset($ride->defaultRideDetail[0]) ? $ride->defaultRideDetail[0]->price : "";;
                                    @endphp
                                    <input type="number" step="any" name="price" id="priceData0" placeholder=""
                                        value="{{ old('price', $defaultPrice) }}"
                                        class="bg-gray-100 border border-gray-200 pl-7 text-gray-900 text-base lg:text-lg rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 block w-full p-2.5 mt-2"/>
                                </div>
                                @error('price')
                                  <div class="relative tooltip -bottom-4 group-hover:flex">
                                    <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                        <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                    </div>
                                  </div>
                                @enderror
                            </div>
                            <div class="mt-6">
                                <label for="" class="block mb-2 font-medium text-gray-900">
                                    @isset($postRidePage->payment_methods_label)
                                        {{ $postRidePage->payment_methods_label }}
                                    @endisset
                                    <span class="text-red-500">*</span>
                                </label>
                                <div class="space-y-2 mt-2">
                                    @isset($postRidePage->payment_methods_option1->features_setting_id)
                                        <div class="flex items-center space-x-1 md:space-x-2 mb-2 mr-2 lg:mr-2">
                                            <input id="cash" name="payment_method" type="radio" value="{{ $postRidePage->payment_methods_option1->features_setting_id }}"
                                                {{ old('payment_method', $ride->payment_method) == $postRidePage->payment_methods_option1->features_setting_id ? 'checked' : '' }}
                                                class="h-5 w-5 rounded bg-white border border-gray-200 cursor-pointer text-indigo-600 focus:ring-indigo-600">
                                            <label for="cash"
                                                class="ml-3 font-normal text-gray-900 flex items-center space-x-1">
                                                @isset($postRidePage->payment_methods_option1->icon)
                                                    <div class="w-8 h-6">
                                                            <img src="{{asset('home_page_icons/' . $postRidePage->payment_methods_option1->icon)}}" class="mx-auto w-full h-full object-contain" alt="">
                                                    </div>
                                                @endisset
                                                <span class="">
                                                    {{ $postRidePage->payment_methods_option1->name }}
                                                </span>
                                                <div class="sups relative inline-flex">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle-fill text-black peer" viewBox="0 0 16 16">
                                                        <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                                                    </svg>
                                                    <div
                                                      class="absolute tooltip payment_tooltiptext_position right-32 -top-12 group-hover:flex hidden peer-hover:flex"
                                                    >
                                                        <div
                                                            role="tooltip"
                                                            class="absolute after:left-[6.8rem] md:after:left-[6.8rem] payment_tooltiptext -left-1/2 -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-blue-500  border border-blue-500 text-gray-600 rounded tooltip_width sm:w-[25rem] md:w-[30rem] lg:w-72 xl:w-[23rem] 2xl:w-[25rem] px-4"
                                                        >
                                                            <p class="text-white font-normal text-start text-sm lg:text-base">
                                                                {{ $postRidePage->payment_methods_option1_tooltip }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    @endisset
                                    @isset($postRidePage->payment_methods_option2->features_setting_id)
                                        <div class="flex items-center space-x-1 md:space-x-2 mb-2 mr-2 lg:mr-2">
                                            <input id="online" name="payment_method" type="radio" value="{{ $postRidePage->payment_methods_option2->features_setting_id }}"
                                                {{ old('payment_method', $ride->payment_method) == $postRidePage->payment_methods_option2->features_setting_id ? 'checked' : '' }}
                                                class="h-5 w-5 rounded bg-white border border-gray-200 cursor-pointer text-indigo-600 focus:ring-indigo-600">
                                            <label for="online"
                                                class="ml-3 font-normal text-gray-900 flex items-center space-x-1">
                                                @isset($postRidePage->payment_methods_option2->icon)
                                                    <div class="w-8 h-6">
                                                            <img src="{{asset('home_page_icons/' . $postRidePage->payment_methods_option2->icon)}}" class="h-full w-full mx-auto object-contain" alt="">
                                                    </div>
                                                @endisset
                                                <span class="">
                                                    {{ $postRidePage->payment_methods_option2->name }}
                                                </span>
                                                <div class="sups relative inline-flex">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle-fill text-black peer" viewBox="0 0 16 16">
                                                        <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                                                    </svg>
                                                    <div
                                                      class="absolute tooltip right-48 -top-20 group-hover:flex hidden peer-hover:flex"
                                                    >
                                                        <div
                                                            role="tooltip"
                                                            class="absolute after:left-[10.8rem] lg:after:left-[10.8rem] payment_tooltiptext -left-1/2 -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-blue-500  border border-blue-500 text-gray-600 rounded tooltip_width sm:w-[25rem] md:w-[30rem] lg:w-72 xl:w-[23rem] 2xl:w-[25rem] px-4"
                                                        >
                                                            <p class="text-white font-normal text-start text-sm lg:text-base">
                                                                {{ $postRidePage->payment_methods_option2_tooltip }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    @endisset
                                    @isset($postRidePage->payment_methods_option3->features_setting_id)
                                        <div class="flex items-center space-x-1 md:space-x-2 mb-2 mr-2 lg:mr-2">
                                            <input id="secured" name="payment_method" type="radio" value="{{ $postRidePage->payment_methods_option3->features_setting_id }}"
                                                {{ old('payment_method', $ride->payment_method) == $postRidePage->payment_methods_option3->features_setting_id ? 'checked' : '' }}
                                                class="h-5 w-5 rounded border border-gray-200 bg-white cursor-pointer text-indigo-600 focus:ring-indigo-600">
                                            <label for="secured"
                                                class="ml-3 font-normal text-gray-900 flex items-center space-x-1">
                                                @isset($postRidePage->payment_methods_option3->icon)
                                                    <div class="w-8 h-6">
                                                        <img src="{{asset('home_page_icons/' . $postRidePage->payment_methods_option3->icon)}}" class="mx-auto h-full w-full object-contain" alt="">
                                                    </div>
                                                @endisset
                                                <span class="">
                                                    {{ $postRidePage->payment_methods_option3->name }}
                                                </span>
                                                <div class="sups relative inline-flex">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle-fill text-black peer" viewBox="0 0 16 16">
                                                        <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                                                    </svg>
                                                    <div
                                                      class="absolute tooltip right-44 -top-14 group-hover:flex hidden peer-hover:flex"
                                                    >
                                                        <div
                                                            role="tooltip"
                                                            class="absolute after:left-[9.8rem] md:after:left-[9.8rem] payment_tooltiptext -left-1/2 -top-20 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-blue-500  border border-blue-500 text-gray-600 rounded tooltip_width sm:w-[25rem] md:w-[30rem] lg:w-72 xl:w-[23rem] 2xl:w-[25rem] px-4"
                                                        >
                                                            <p class="text-white font-normal text-start text-sm lg:text-base">
                                                                {{ $postRidePage->payment_methods_option3_tooltip }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    @endisset
                                </div>
                                @error('payment_method')
                                  <div class="relative tooltip -bottom-4 group-hover:flex">
                                    <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                        <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                    </div>
                                  </div>
                                @enderror
                            </div>
                        </div>
                </div>

                <div class="mt-6 ">
                    <div class=" mt-6">
                        <div class="bg-white rounded-lg shadow-3xl">
                            <div class="text-2xl bg-primary rounded-t-lg text-white py-2 px-4">
                                <h3 class="text-2xl">
                                    @isset($postRidePage->booking_label)
                                        {{ $postRidePage->booking_label }}
                                    @endisset
                                    <span class="text-white">*</span>
                                </h3>
                            </div>
                            <div class="bg-white p-4">
                                <ul class="grid w-full gap-6 md:grid-cols-2">
                                    @isset($postRidePage->booking_option1->features_setting_id)
                                        <li>
                                            <input type="radio" id="instant-booking" name="booking_method" value="{{ $postRidePage->booking_option1->features_setting_id }}"
                                                {{ old('booking_method', $ride->booking_method) == $postRidePage->booking_option1->features_setting_id ? 'checked' : '' }} class="hidden peer">
                                            <label for="instant-booking" class="inline-flex items-center space-x-3 w-full p-4 text-gray-800 bg-white border-2 border-gray-100 rounded cursor-pointer peer-checked:border-green-500 peer-checked:border-2 peer-checked:text-green-500 hover:border-2 hover:border-green-500">
                                                <img class="w-12 h-12" src="{{asset('home_page_icons/' . $postRidePage->booking_option1->icon)}}" alt="">
                                                <span class="font-medium text-xl">
                                                    {{ $postRidePage->booking_option1->name }}
                                                </span>
                                                <div class="sups relative inline-flex">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle-fill text-black peer" viewBox="0 0 16 16">
                                                        <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                                                    </svg>
                                                    <div
                                                      class="absolute tooltip right-48 -top-20 group-hover:flex hidden peer-hover:flex"
                                                    >
                                                        <div
                                                            role="tooltip"
                                                            class="absolute after:left-[10.8rem] lg:after:left-[10.8rem] payment_tooltiptext -left-1/2 -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-blue-500  border border-blue-500 text-gray-600 rounded tooltip_width sm:w-[25rem] md:w-[30rem] lg:w-72 xl:w-[23rem] 2xl:w-[25rem] px-4"
                                                        >
                                                            <p class="text-white font-normal text-start text-sm lg:text-base">
                                                                {{ $postRidePage->booking_option1_tooltip }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </label>
                                        </li>
                                    @endisset
                                    @isset($postRidePage->booking_option2->features_setting_id)
                                        <li>
                                            <input type="radio" id="manual-approval" name="booking_method" value="{{ $postRidePage->booking_option2->features_setting_id }}"
                                                {{ old('booking_method', $ride->booking_method) == $postRidePage->booking_option2->features_setting_id ? 'checked' : '' }} class="hidden peer">
                                            <label for="manual-approval" class="inline-flex items-center space-x-3 w-full p-4 text-gray-800 bg-white border-2 border-gray-100 rounded cursor-pointer peer-checked:border-green-500 peer-checked:border-2 peer-checked:text-green-500 hover:border-2 hover:border-green-500">
                                                <img class="w-12 h-12" src="{{asset('home_page_icons/' . $postRidePage->booking_option2->icon)}}" alt="">
                                                <span class="font-medium text-xl">
                                                    {{ $postRidePage->booking_option2->name }}
                                                </span>
                                                <div class="sups relative inline-flex">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle-fill text-black peer" viewBox="0 0 16 16">
                                                        <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                                                    </svg>
                                                    <div
                                                      class="absolute tooltip right-48 -top-20 group-hover:flex hidden peer-hover:flex"
                                                    >
                                                        <div
                                                            role="tooltip"
                                                            class="absolute after:left-[10.8rem] lg:after:left-[10.8rem] payment_tooltiptext -left-1/2 -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-blue-500  border border-blue-500 text-gray-600 rounded tooltip_width sm:w-[25rem] md:w-[30rem] lg:w-72 xl:w-[23rem] 2xl:w-[25rem] px-4"
                                                        >
                                                            <p class="text-white font-normal text-start text-sm lg:text-base">
                                                                {{ $postRidePage->booking_option2_tooltip }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </label>
                                        </li>
                                    @endisset
                                </ul>
                                @error('booking_method')
                                  <div class="relative tooltip -bottom-4 group-hover:flex">
                                    <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                        <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                    </div>
                                  </div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6">
                    <div class="bg-white rounded-lg overflow-hidden shadow-3xl">
                        <div class="text-2xl bg-primary text-white py-2 px-4">
                            <h3 class="text-2xl">
                                @isset($postRidePage->vehicle_label)
                                    {{ $postRidePage->vehicle_label }}
                                @endisset
                                <span class="text-white">*</span>
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
                                        {{ old('add_vehicle') == '1' ? 'checked' : '' }}
                                        class="w-4 h-4 text-blue-600 cursor-pointer bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                                    <label for="add" class="ml-2  text-gray-900">
                                        @isset($postRidePage->add_vehicle_label)
                                            {{ $postRidePage->add_vehicle_label }}
                                        @endisset
                                    </label>
                                </div>
                                <div class="{{ $vehicles->count() > '0' ? '' : 'hidden' }}">
                                    <input id="added" type="checkbox" name="added_vehicle" value="1"
                                        {{ old('added_vehicle', $ride->added_vehicle, $ride->add_vehicle) == '1' || collect($vehicles)->contains('primary_vehicle', 1) ? 'checked' : '' }}
                                        class="w-4 h-4 text-blue-600 cursor-pointer bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                                    <label for="added" class="ml-2  text-gray-900">
                                        @isset($postRidePage->existing_label)
                                            {{ $postRidePage->existing_label }}
                                        @endisset
                                    </label>
                                </div>
                            </div>
                            @error('vehicle_selection')
                                <div class="relative tooltip bottom-0 group-hover:flex">
                                    <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                        <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                    </div>
                                </div>
                            @enderror
                            <div id="skipVehicle">
                                <div class="mt-6 grid grid-cols-1 gap-6 md:grid-cols-4">
                                    <div class="md:col-span-2">
                                        <label for="make"
                                            class="text-gray-900 mb-2">
                                            @isset($postRidePage->make_label)
                                                {{ $postRidePage->make_label }}
                                            @endisset
                                            <span class="text-red-500">*</span>
                                        </label>
                                        <div class="mt-2">
                                            <input type="text" name="make" id=""
                                                @if ($errors->count() > 0)
                                                    value="{{ old('make', $ride->make) }}"
                                                @else
                                                    value="{{ $ride->make }}"
                                                @endif
                                                @isset($postRidePage->make_placeholder)
                                                    placeholder="{{ $postRidePage->make_placeholder }}"
                                                @endisset
                                                class="bg-gray-100 border border-gray-200 text-gray-900 text-base lg:text-lg rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 mt-2 block w-full p-2.5">
                                        </div>
                                        @error('make')
                                          <div class="relative tooltip -bottom-4 group-hover:flex">
                                            <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                                <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                            </div>
                                          </div>
                                        @enderror
                                    </div>
                                    <div class="md:col-span-2">
                                        <label for="modal"
                                            class="text-gray-900 mb-2">
                                            @isset($postRidePage->model_label)
                                                {{ $postRidePage->model_label }}
                                            @endisset
                                            <span class="text-red-500">*</span>
                                        </label>
                                        <div class="mt-2">
                                            <input type="text" name="model" id=""
                                                @if ($errors->count() > 0)
                                                    value="{{ old('model', $ride->model) }}"
                                                @else
                                                    value="{{ $ride->model }}"
                                                @endif
                                                @isset($postRidePage->model_placeholder)
                                                    placeholder="{{ $postRidePage->model_placeholder }}"
                                                @endisset
                                                class="bg-gray-100 border border-gray-200 text-gray-900 text-base lg:text-lg rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 mt-2 block w-full p-2.5">
                                        </div>
                                        @error('model')
                                          <div class="relative tooltip -bottom-4 group-hover:flex">
                                            <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                                <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                            </div>
                                          </div>
                                        @enderror
                                    </div>
                                    <div class="md:col-span-2">
                                        <label for="type" class="text-gray-900 mb-2">
                                            @isset($postRidePage->type_label)
                                                {{ $postRidePage->type_label }}
                                            @endisset
                                            <span class="text-red-500">*</span>
                                        </label>
                                        <div class="mt-2">
                                            <select id="type" name="vehicle_type"
                                                class="bg-gray-100 border border-gray-200 text-gray-900 text-base lg:text-lg rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 mt-2 block w-full p-2.5">

                                                <option {{ old('vehicle_type', $ride->vehicle_type) == '' ? 'selected' : '' }} value="">
                                                    @isset($postRidePage->vehicle_type_placeholder)
                                                        {{ $postRidePage->vehicle_type_placeholder }}
                                                    @endisset
                                                </option>

                                                <option value="{{ $postRidePage->vehicle_type_convertible_value ?? 'Convertable' }}"
                                                    {{ old('vehicle_type', $ride->vehicle_type) === ($postRidePage->vehicle_type_convertible_value ?? 'Convertable') ? 'selected' : '' }}>
                                                    {{ $postRidePage->vehicle_type_convertible_text ?? "Convertable"}}
                                                </option>
                                                <option value="{{ $postRidePage->vehicle_type_coupe_value ?? 'Coupe' }}"
                                                    {{ old('vehicle_type', $ride->vehicle_type) === ($postRidePage->vehicle_type_coupe_value ??'Coupe') ? 'selected' : '' }}>
                                                    {{ $postRidePage->vehicle_type_coupe_text ?? "Coupe"}}
                                                </option>
                                                <option value="{{ $postRidePage->vehicle_type_hatchback_value ??'Hatchback' }}"
                                                    {{ old('vehicle_type', $ride->vehicle_type) === ($postRidePage->vehicle_type_hatchback_value ??'Hatchback') ? 'selected' : '' }}>
                                                    {{ $postRidePage->vehicle_type_hatchback_text ?? "Hatchback"}}
                                                </option>
                                                <option value="{{ $postRidePage->vehicle_type_minivan_value ??'Minivan' }}"
                                                    {{ old('vehicle_type', $ride->vehicle_type) === ($postRidePage->vehicle_type_minivan_value ??'Minivan') ? 'selected' : '' }}>
                                                    {{ $postRidePage->vehicle_type_minivan_text ?? "Minivan"}}
                                                </option>
                                                <option value="{{ $postRidePage->vehicle_type_sedan_value ??'Sedan' }}"
                                                    {{ old('vehicle_type', $ride->vehicle_type) === ($postRidePage->vehicle_type_sedan_value ??'Sedan') ? 'selected' : '' }}>
                                                    {{ $postRidePage->vehicle_type_sedan_text ?? "Sedan"}}
                                                </option>
                                                <option value="{{ $postRidePage->vehicle_type_station_wagon_value }}"
                                                    {{ old('vehicle_type', $ride->vehicle_type) === ($postRidePage->vehicle_type_station_wagon_value ??'Station wagon') ? 'selected' : '' }}>
                                                    {{ $postRidePage->vehicle_type_station_wagon_text ?? "Station wagon"}}
                                                </option>
                                                <option value="{{ $postRidePage->vehicle_type_suv_value ??'SUV' }}"
                                                    {{ old('vehicle_type', $ride->vehicle_type) === ($postRidePage->vehicle_type_suv_value ??'SUV') ? 'selected' : '' }}>
                                                    {{ $postRidePage->vehicle_type_suv_text ?? "SUV"}}
                                                </option>
                                                <option value="{{ $postRidePage->vehicle_type_truck_value ??'Truck' }}"
                                                    {{ old('vehicle_type', $ride->vehicle_type) === ($postRidePage->vehicle_type_truck_value ??'Truck') ? 'selected' : '' }}>
                                                    {{ $postRidePage->vehicle_type_truck_text ?? "Truck"}}
                                                </option>
                                                <option value="{{ $postRidePage->vehicle_type_van_value ??'Van' }}"
                                                    {{ old('vehicle_type', $ride->vehicle_type) === ($postRidePage->vehicle_type_van_value ??'Van') ? 'selected' : '' }}>
                                                    {{ $postRidePage->vehicle_type_van_text ?? "Van"}}
                                                </option>
                                            </select>
                                        </div>
                                        @error('vehicle_type')
                                          <div class="relative tooltip -bottom-4 group-hover:flex">
                                            <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                                <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                            </div>
                                          </div>
                                        @enderror
                                    </div>
                                    <div class="">
                                        <label for="type" class="text-gray-900 mb-2">
                                            @isset($postRidePage->year_label)
                                                {{ $postRidePage->year_label }}
                                            @endisset
                                            <span class="text-red-500">*</span>
                                        </label>
                                        <div class="mt-2">
                                            <input type="text" name="year" id="" placeholder=""
                                                @if ($errors->count() > 0)
                                                    value="{{ old('year', $ride->year) }}"
                                                @else
                                                    value="{{ $ride->year }}"
                                                @endif
                                                class="bg-gray-100 border border-gray-200 text-gray-900 text-base lg:text-lg rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 mt-2 block w-full p-2.5">
                                        </div>
                                        @error('year')
                                          <div class="relative tooltip -bottom-4 group-hover:flex">
                                            <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full rounded" >
                                                <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                            </div>
                                          </div>
                                        @enderror
                                    </div>
                                    <div class="">
                                        <label for="color"
                                            class="text-gray-900 mb-2">
                                            @isset($postRidePage->color_label)
                                                {{ $postRidePage->color_label }}
                                            @endisset
                                            <span class="text-red-500">*</span>
                                        </label>
                                        <div class="mt-2">
                                            <input type="text" name="color" id="" placeholder=""
                                                @if ($errors->count() > 0)
                                                    value="{{ old('color', $ride->color) }}"
                                                @else
                                                    value="{{ $ride->color }}"
                                                @endif
                                                class="bg-gray-100 border border-gray-200 text-gray-900 text-base lg:text-lg rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 mt-2 block w-full p-2.5">
                                        </div>
                                        @error('color')
                                          <div class="relative tooltip -bottom-4 group-hover:flex">
                                            <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full rounded" >
                                                <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                            </div>
                                          </div>
                                        @enderror
                                    </div>
                                    <div class="md:col-span-2">
                                        <label for="modal" class="text-gray-900 mb-2">
                                            @isset($postRidePage->liscense_label)
                                                {{ $postRidePage->liscense_label }}
                                            @endisset
                                            <span class="text-red-500">*</span>
                                        </label>
                                        <div class="mt-2">
                                            <input type="text" name="license_no" id="" placeholder=""
                                                @if ($errors->count() > 0)
                                                    value="{{ old('license_no', $ride->license_no) }}"
                                                @else
                                                    value="{{ $ride->license_no }}"
                                                @endif
                                                class="bg-gray-100 border border-gray-200 text-gray-900 text-base lg:text-lg rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 mt-2 block w-full p-2.5">
                                        </div>
                                        @error('license_no')
                                          <div class="relative tooltip -bottom-4 group-hover:flex">
                                            <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                                <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                            </div>
                                          </div>
                                        @enderror
                                    </div>
                                    <div class="md:col-span-4">
                                        <label for="modal" class="text-gray-900 mb-2">
                                            @isset($postRidePage->car_type_label)
                                                {{ $postRidePage->car_type_label }}
                                            @endisset
                                            <span class="text-red-500">*</span>
                                        </label>
                                        <div class=" flex items-center">
                                            @isset($postRidePage->electric_car_label)
                                                <div class="flex items-center space-x-1.5 lg:space-x-3 mb-2 mr-2 lg:mr-2">
                                                    <input id="" name="car_type" type="radio" value="{{ $postRidePage->electric_car_label }}"
                                                        {{ old('car_type', $ride->car_type) == $postRidePage->electric_car_label ? 'checked' : '' }}
                                                        class="h-5 w-5 border-gray-300 bg-gray-200 cursor-pointer text-indigo-600 focus:ring-indigo-600">
                                                    <label for="" class="block text-gray-900">
                                                        {{ $postRidePage->electric_car_label }}
                                                    </label>
                                                </div>
                                            @endisset
                                            @isset($postRidePage->hybrid_car_label)
                                                <div class="flex items-center space-x-1.5 lg:space-x-3 mb-2 mr-2 lg:mr-2">
                                                    <input id="" name="car_type" type="radio" value="{{  $postRidePage->hybrid_car_label }}"
                                                    {{ old('car_type', $ride->car_type) == $postRidePage->hybrid_car_label ? 'checked' : '' }}
                                                        class="h-5 w-5 border-gray-300 bg-gray-200 cursor-pointer text-indigo-600 focus:ring-indigo-600">
                                                    <label for="" class="block text-gray-900">
                                                        {{ $postRidePage->hybrid_car_label }}
                                                    </label>
                                                </div>
                                            @endisset
                                            @isset($postRidePage->gas_car_label)
                                                <div class="flex items-center space-x-1.5 lg:space-x-3 mb-2 mr-2 lg:mr-2">
                                                    <input id="" name="car_type" type="radio" value="{{ $postRidePage->gas_car_label }}"
                                                        {{ old('car_type', $ride->car_type) == $postRidePage->gas_car_label || ( empty(old('car_type'))) ? 'checked' : '' }}
                                                        class="h-5 w-5 border-gray-300 bg-gray-200 cursor-pointer text-indigo-600 focus:ring-indigo-600">
                                                    <label for="" class="block text-gray-900">
                                                        {{ $postRidePage->gas_car_label }}
                                                    </label>
                                                </div>
                                            @endisset
                                        </div>
                                        @error('car_type')
                                            <div class="relative tooltip -bottom-4 group-hover:flex">
                                                <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                                    <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
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
                                                    <div class="flex flex-col items-center justify-center pt-5 pb-6 p-4">
                                                        @if (session('uploaded_image'))
                                                            <img id="profile-image" class="w-40 h-40 object-contain mb-4 cursor-pointer" src="{{ asset('car_images/' . session('uploaded_image')) }}" alt="Uploaded Image">
                                                        @elseif ($ride->car_image)
                                                            <img id="profile-image" class="w-40 h-40 object-contain mb-4 cursor-pointer" src="{{ $ride->car_image }}">
                                                        @else
                                                            <img id="profile-image" class="w-12 h-12 object-contain mb-4 cursor-pointer" src="{{ asset('assets/image-placeholder.png')}}">
                                                        @endif
                                                        <p class="text-sm lg:text-lg text-gray-900">Upload car photo.
                                                            <!-- <span class="font-semibold text-primary">Choose file</span> -->
                                                        </p>
                                                        <p class="text-sm lg:text-base text-gray-900 font-normal">
                                                        JPEG, JPG, PNG, GIF - 10MB max.
                                                        </p>
                                                    </div>
                                                    <input id="dropzone-file" name="image" type="file" onchange="previewImage(this)" class="hidden" />
                                                    @if (session('uploaded_image'))
                                                        <input type="hidden" name="existing_image" value="{{ session('uploaded_image') }}">
                                                    @elseif ($ride->car_image)
                                                        @php
                                                            $imageName = basename($ride->car_image);
                                                        @endphp
                                                        <input type="hidden" name="existing_image" value="{{ $imageName }}">
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
                            <div id="showVehicles" class="md:col-span-2">
                                <label for="type" class="text-gray-900 mb-2">
                                    Select vehicle <span class="text-red-500">*</span>
                                </label>
                                {{ old('vehicle_id') }}
                                <div class="mt-2">
                                    <select id="type" name="vehicle_id"
                                        class="bg-white border border-gray-300 text-gray-900 text-base lg:text-lg rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 mt-2 block w-full p-2.5">
                                        <option value=""
                                            {{ old('vehicle_id', $ride->vehicle_id) === '' ? 'selected' : '' }}>
                                            Select
                                        </option>
                                        @foreach ($vehicles as $vehicle)
                                            <option value="{{ $vehicle->id }}"
                                                {{ (int) old('vehicle_id', $ride->vehicle_id) === (int) $vehicle->id || $vehicle->primary_vehicle === '1' ? 'selected' : '' }}>
                                                {{ $vehicle->year }} / {{ $vehicle->model }} / {{ $vehicle->type }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('vehicle_id')
                                        <div class="relative tooltip -bottom-4 group-hover:flex">
                                            <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                                <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                            </div>
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                    <div class="mt-6 bg-white rounded-lg shadow-3xl">
                        <div class="text-2xl bg-primary rounded-t-lg text-white py-2 px-4">
                            <h3 class="text-2xl">
                                @isset($postRidePage->luggage_label)
                                    {{ $postRidePage->luggage_label }}
                                @endisset
                                <span class="text-white">*</span>
                            </h3>
                        </div>
                        <div class="bg-white p-4">
                            <div class="border rounded-md divide-y">
                                @isset($postRidePage->luggage_option1->features_setting_id)
                                    <div class="flex items-center gap-4 p-3">
                                        <label for="{{ $postRidePage->luggage_option1->features_setting_id }}" class="font-normal text-gray-900 flex items-center space-x-1 gap-2 w-full">
                                            <input id="{{ $postRidePage->luggage_option1->features_setting_id }}" type="radio" name="luggage" value="{{ $postRidePage->luggage_option1->features_setting_id }}"
                                              {{ old('luggage', $ride->luggage) == $postRidePage->luggage_option1->features_setting_id ? 'checked' : '' }} class="w-4 h-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                            @isset($postRidePage->luggage_option1->icon)
                                                <img class="w-10 h-10" src="{{asset('home_page_icons/' . $postRidePage->luggage_option1->icon)}}" alt="">
                                            @endisset
                                            <span>
                                                {{ $postRidePage->luggage_option1->name }}
                                            </span>
                                            <div class="sups relative inline-flex">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle-fill text-black peer" viewBox="0 0 16 16">
                                                    <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                                                </svg>
                                                <div
                                                  class="absolute tooltip payment_tooltiptext_position -top-12 right-32 group-hover:flex hidden peer-hover:flex"
                                                >
                                                    <div
                                                        role="tooltip"
                                                        class="absolute after:left-[6.8rem] md:after:left-[6.8rem] payment_tooltiptext -left-1/2 -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-blue-500  border border-blue-500 text-gray-600 rounded tooltip_width sm:w-[25rem] md:w-[30rem] lg:w-72 xl:w-[23rem] 2xl:w-[25rem] px-4"
                                                    >
                                                        <p class="text-white font-normal text-start text-sm lg:text-base">
                                                            {{ $postRidePage->luggage_option1_tooltip }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </label>
                                        </div>
                                @endisset
                                @isset($postRidePage->luggage_option2->features_setting_id)
                                    <div class="flex items-center gap-4 p-3">
                                        <label for="{{ $postRidePage->luggage_option2->features_setting_id }}" class="font-normal text-gray-900 flex items-center space-x-1 gap-2 w-full">
                                            <input type="radio" id="{{ $postRidePage->luggage_option2->features_setting_id }}" name="luggage" value="{{ $postRidePage->luggage_option2->features_setting_id }}"
                                              {{ old('luggage', $ride->luggage) == $postRidePage->luggage_option2->features_setting_id ? 'checked' : '' }} class="w-4 h-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                            @isset($postRidePage->luggage_option2->icon)
                                                <img class="w-10 h-10" src="{{asset('home_page_icons/' . $postRidePage->luggage_option2->icon)}}" alt="">
                                            @endisset
                                            <span class="">
                                                {{ $postRidePage->luggage_option2->name }}
                                            </span>
                                            <div class="sups relative inline-flex">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle-fill text-black peer" viewBox="0 0 16 16">
                                                    <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                                                </svg>
                                                <div
                                                  class="absolute tooltip payment_tooltiptext_position -top-20 right-32 group-hover:flex hidden peer-hover:flex"
                                                >
                                                    <div
                                                        role="tooltip"
                                                        class="absolute after:left-[6.8rem] md:after:left-[6.8rem] payment_tooltiptext -left-1/2 -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-blue-500  border border-blue-500 text-gray-600 rounded tooltip_width sm:w-[25rem] md:w-[30rem] lg:w-72 xl:w-[23rem] 2xl:w-[25rem] px-4"
                                                    >
                                                        <p class="text-white font-normal text-start text-sm lg:text-base">
                                                            {{ $postRidePage->luggage_option2_tooltip }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </label>
                                        </div>
                                @endisset
                                @isset($postRidePage->luggage_option3->features_setting_id)
                                <div class="flex items-center gap-4 p-3">
                                    <label for="{{ $postRidePage->luggage_option3->features_setting_id }}" class="font-normal text-gray-900 flex items-center space-x-1 gap-2 w-full">
                                        <input type="radio" id="{{ $postRidePage->luggage_option3->features_setting_id }}" name="luggage" value="{{ $postRidePage->luggage_option3->features_setting_id }}"
                                         {{ old('luggage', $ride->luggage) == $postRidePage->luggage_option3->features_setting_id ? 'checked' : '' }} class="w-4 h-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                            @isset($postRidePage->luggage_option3->icon)
                                                <img class="w-10 h-10" src="{{asset('home_page_icons/' . $postRidePage->luggage_option3->icon)}}" alt="">
                                            @endisset
                                            <span>
                                                {{ $postRidePage->luggage_option3->name }}
                                            </span>
                                            <div class="sups relative inline-flex">
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
                                                        <p class="text-white font-normal text-start text-sm lg:text-base">
                                                            {{ $postRidePage->luggage_option3_tooltip }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                @endisset
                                @isset($postRidePage->luggage_option4->features_setting_id)
                                    <div class="flex items-center gap-4 p-3">
                                        <label for="{{ $postRidePage->luggage_option4->features_setting_id }}" class="font-normal text-gray-900 flex items-center space-x-1 gap-2 w-full">
                                            <input type="radio" id="{{ $postRidePage->luggage_option4->features_setting_id }}" name="luggage" value="{{ $postRidePage->luggage_option4->features_setting_id }}"
                                              {{ old('luggage', $ride->luggage) == $postRidePage->luggage_option4->features_setting_id ? 'checked' : '' }} class="w-4 h-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                            @isset($postRidePage->luggage_option4->icon)
                                                <img class="w-10 h-10" src="{{asset('home_page_icons/' . $postRidePage->luggage_option4->icon)}}" alt="">
                                            @endisset
                                            <span>
                                                {{ $postRidePage->luggage_option4->name }}
                                            </span>
                                            <div class="sups relative inline-flex">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle-fill text-black peer" viewBox="0 0 16 16">
                                                    <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                                                </svg>
                                                <div
                                                  class="absolute tooltip payment_tooltiptext_position -top-12 right-32 group-hover:flex hidden peer-hover:flex"
                                                >
                                                    <div
                                                        role="tooltip"
                                                        class="absolute after:left-[6.8rem] md:after:left-[6.8rem] payment_tooltiptext -left-1/2 -top-16 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-blue-500  border border-blue-500 text-gray-600 rounded tooltip_width sm:w-[25rem] md:w-[30rem] lg:w-72 xl:w-[23rem] 2xl:w-[25rem] px-4"
                                                    >
                                                        <p class="text-white font-normal text-start text-sm lg:text-base">
                                                            {{ $postRidePage->luggage_option4_tooltip }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                @endisset
                                @isset($postRidePage->luggage_option5->features_setting_id)
                                    <div class="flex items-center gap-4 p-3">
                                        <label for="{{ $postRidePage->luggage_option5->features_setting_id }}" class="font-normal text-gray-900 flex items-start space-x-1 gap-2 w-full">
                                            <input type="radio" id="{{ $postRidePage->luggage_option5->features_setting_id }}" name="luggage" value="{{ $postRidePage->luggage_option5->features_setting_id }}"
                                              {{ old('luggage', $ride->luggage) == $postRidePage->luggage_option5->features_setting_id ? 'checked' : '' }} class="w-4 h-4 mt-2 text-blue-600 cursor-pointer bg-white border-gray-500 rounded focus:ring-blue-500  focus:ring-2">
                                            @isset($postRidePage->luggage_option5->icon)
                                                <img class="w-10 h-10" src="{{asset('home_page_icons/' . $postRidePage->luggage_option5->icon)}}" alt="">
                                            @endisset
                                            <div>
                                                <p class="leading-normal mt-2">
                                                    {{ $postRidePage->luggage_option5->name }}
                                                </p>
                                                <div class="font-normal text-gray-900 flex lg:block items-center space-x-0.5 2xl:pr-8">
                                                    <small>{{ $postRidePage->luggage_option5_label }}</small>
                                                    <div class="sups relative inline-flex items-center">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle-fill text-black peer" viewBox="0 0 16 16">
                                                            <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                                                        </svg>
                                                        <div
                                                          class="absolute tooltip tooltip_position md:right-64 lg:right-52 xl:right-36 -top-14 group-hover:flex hidden peer-hover:flex"
                                                        >
                                                            <div
                                                                role="tooltip"
                                                                class="absolute sm:after:left-1/3 xl:after:left-1/3 2xl:after:left-1/3 luggage_tooltiptext -left-1/2 -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-blue-500  border border-blue-500 text-gray-600 rounded tooltip_width sm:w-[25rem] md:w-[30rem] lg:w-72 xl:w-[23rem] 2xl:w-[25rem] px-4"
                                                            >
                                                                <p class="text-white font-normal text-start text-sm lg:text-base">
                                                                    {{ $postRidePage->luggage_option5_tooltip }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                @endisset
                            </div>
                            @error('luggage')
                              <div class="relative tooltip -bottom-4 group-hover:flex">
                                <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                    <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                </div>
                              </div>
                            @enderror
                            <div class="mt-6 space-y-2">
                                <div class="flex items-start">
                                    <input id="heating" type="checkbox" name="accept_more_luggage" value="1"
                                        {{ old('accept_more_luggage', $ride->accept_more_luggage) == '1' ? 'checked' : '' }}
                                        class="w-4 h-4 mt-1 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                    <label for="heating"
                                        class="ml-2 font-normal text-gray-900 flex space-x-1">
                                        <span class="">
                                            @isset($postRidePage->luggage_checkbox_label1)
                                                {{ $postRidePage->luggage_checkbox_label1 }}
                                            @endisset
                                        </span>
                                    </label>
                                </div>
                                {{-- <div class="flex items-start">
                                    <input id="heating" type="checkbox" name="open_customized" value="1"
                                        {{ old('open_customized', $ride->open_customized) == '1' ? 'checked' : '' }}
                                        class="w-4 h-4 mt-1 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                    <label for="heating"
                                        class="ml-2 font-normal text-gray-900 flex space-x-1">
                                        <span class="">
                                            @isset($postRidePage->luggage_checkbox_label2)
                                                {{ $postRidePage->luggage_checkbox_label2 }}
                                            @endisset
                                            <sup class="text-red-500">*</sup>
                                        </span>
                                    </label>
                                </div> --}}
                            </div>

                        </div>
                    </div>

                    <div class="mt-6 bg-white rounded-lg overflow-hidden shadow-3xl">
                        <div class="text-2xl bg-primary text-white py-2 px-4">
                            <h3 class="text-2xl">
                                @isset($postRidePage->smoking_label)
                                    {{ $postRidePage->smoking_label }}
                                @endisset
                                <span class="text-white">*</span>
                            </h3>
                        </div>
                        <div class="bg-white p-4">
                            <div class="border rounded-md overflow-hidden divide-y">
                                @isset($postRidePage->smoking_option1->features_setting_id)
                                    <div class="flex items-center gap-4 p-3 w-full">
                                        <label for="{{ $postRidePage->smoking_option1->features_setting_id }}" class="font-normal text-gray-900 flex space-x-1 flex items-center gap-4 w-full">
                                            <input id="{{ $postRidePage->smoking_option1->features_setting_id }}" name="smoke" type="radio" value="{{ $postRidePage->smoking_option1->features_setting_id }}"
                                            {{ $isNewForm ? (old('smoke', $user->smoke) == $postRidePage->smoking_option1->features_setting_id ? 'checked' : ( 21 == $postRidePage->smoking_option1->features_setting_id ? 'checked' : "")) : (old('smoke', $ride->smoke) == $postRidePage->smoking_option1->features_setting_id ? 'checked' : '') }}
                                            class="h-4 w-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">

                                            <span class="">
                                                {{ $postRidePage->smoking_option1->name }}
                                            </span>
                                        </label>

                                    </div>
                                @endisset
                                @isset($postRidePage->smoking_option2->features_setting_id)
                                    <div class="flex items-center gap-4 p-3">
                                        <label for="{{ $postRidePage->smoking_option2->features_setting_id }}" class="font-normal text-gray-900 flex space-x-1 flex items-center gap-4 w-full">
                                            <input id="{{ $postRidePage->smoking_option2->features_setting_id }}" name="smoke" type="radio" value="{{ $postRidePage->smoking_option2->features_setting_id }}"
                                            {{ $isNewForm ? (old('smoke', $user->smoke) == $postRidePage->smoking_option2->features_setting_id ? 'checked' : '') : (old('smoke', $ride->smoke) == $postRidePage->smoking_option2->features_setting_id ? 'checked' : '') }}
                                            class="h-4 w-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                            {{ $postRidePage->smoking_option2->name }}
                                        </label>
                                    </div>
                                @endisset
                            </div>
                            @error('smoke')
                              <div class="relative tooltip -bottom-4 group-hover:flex">
                                <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                    <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                </div>
                              </div>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6 bg-white rounded-lg overflow-hidden shadow-3xl">
                        <div class="text-2xl bg-primary text-white py-2 px-4">
                            <h3 class="text-2xl">
                                @isset($postRidePage->animals_label)
                                    {{ $postRidePage->animals_label }}
                                @endisset
                                <span class="text-white">*</span>
                            </h3>
                        </div>
                        <div class="bg-white p-4">
                            <div class="border rounded-md overflow-hidden divide-y">
                                @isset($postRidePage->animals_option1->features_setting_id)
                                    <div class="flex items-center gap-4 p-3">
                                        <label for="{{ $postRidePage->animals_option1->features_setting_id }}" class="font-normal text-gray-900 flex space-x-1 flex items-center gap-4 w-full">
                                            <input id="{{ $postRidePage->animals_option1->features_setting_id }}" name="animal_friendly" type="radio" value="{{ $postRidePage->animals_option1->features_setting_id }}"
                                            {{ $isNewForm ? (old('animal_friendly') == $postRidePage->animals_option1->features_setting_id ? 'checked' : (23 == $postRidePage->animals_option1->features_setting_id ? 'checked' : '')) : (old('animal_friendly', $ride->animal_friendly) == $postRidePage->animals_option1->features_setting_id ? 'checked' : '') }}
                                            class="w-4 h-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                            {{ $postRidePage->animals_option1->name }}
                                        </label>
                                    </div>
                                @endisset
                                @isset($postRidePage->animals_option2->features_setting_id)
                                    <div class="flex items-center gap-4 p-3">
                                        <label for="{{ $postRidePage->animals_option2->features_setting_id }}" class="font-normal text-gray-900 flex space-x-1 flex items-center gap-4 w-full">
                                            <input id="{{ $postRidePage->animals_option2->features_setting_id }}" name="animal_friendly" type="radio" value="{{ $postRidePage->animals_option2->features_setting_id }}"
                                            {{ $isNewForm ? (old('animal_friendly') == $postRidePage->animals_option2->features_setting_id ? 'checked' : '') : (old('animal_friendly', $ride->animal_friendly) == $postRidePage->animals_option2->features_setting_id ? 'checked' : '') }}
                                            class="w-4 h-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                            {{ $postRidePage->animals_option2->name }}
                                        </label>
                                    </div>
                                @endisset
                                @isset($postRidePage->animals_option3->features_setting_id)
                                    <div class="flex items-center gap-4 p-3">
                                        <label for="{{ $postRidePage->animals_option3->features_setting_id }}" class="font-normal text-gray-900 flex space-x-1 flex items-center gap-4 w-full">
                                            <input id="{{ $postRidePage->animals_option3->features_setting_id }}" name="animal_friendly" type="radio" value="{{ $postRidePage->animals_option3->features_setting_id }}"
                                            {{ $isNewForm ? (old('animal_friendly') == $postRidePage->animals_option3->features_setting_id ? 'checked' : '') : (old('animal_friendly', $ride->animal_friendly) == $postRidePage->animals_option3->features_setting_id ? 'checked' : '') }}
                                            class="w-4 h-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                            {{ $postRidePage->animals_option3->name }}
                                        </label>
                                    </div>
                                @endisset
                            </div>
                            @error('animal_friendly')
                              <div class="relative tooltip -bottom-4 group-hover:flex">
                                <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                    <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                </div>
                              </div>
                            @enderror
                        </div>
                    </div>

                <div class="mt-6">
                    <div class="bg-white rounded-lg shadow-3xl">
                        <div class="text-2xl bg-primary rounded-t-lg text-white py-2 px-4">
                            <h3 class="text-2xl">
                                @isset($postRidePage->preferences_label)
                                    {{ $postRidePage->preferences_label }}
                                @endisset
                            </h3>
                        </div>
                        <div class="bg-white p-4">
                            <div class="space-y-2">
                                @isset($postRidePage->features_option1->features_setting_id)
                                    <div class="flex items-center">
                                        <input id="pink-ride" type="checkbox" name="features[]"
                                            @php $disabled = false; @endphp
                                            @if ($user->pink_ride == '0')
                                                @php $disabled = true; @endphp
                                            @elseif ($user->pink_ride == '')
                                                @if ($pinkRideSetting)
                                                @if (($user->gender == 'female') && (empty($user->government_issued_id) || empty($user->address)))
                                                @php $disabled = true; @endphp
                                                 @elseif ($user->gender !== 'female')
                                                        @php $disabled = true; @endphp
                                                    @elseif ($pinkRideSetting->verfiy_phone === '1' && $user->phone_verified !== '1')
                                                        @php $disabled = true; @endphp
                                                    @elseif ($pinkRideSetting->verify_email === '1' && $user->email_verified !== '1')
                                                        @php $disabled = true; @endphp
                                                    @elseif ($pinkRideSetting->driver_license === '1' && $user->driver !== '1')
                                                        @php $disabled = true; @endphp
                                                    @endif
                                                @endif
                                            @endif
                                            @if ($disabled)
                                                {{ 'disabled' }}
                                            @endif
                                            value="{{ $postRidePage->features_option1->features_setting_id }}"
                                            {{ $isNewForm
                                                ? (in_array($postRidePage->features_option1->features_setting_id, old('features', []))
                                                    ? 'checked'
                                                    : '')
                                                : (old('features')
                                                    ? (in_array($postRidePage->features_option1->features_setting_id, old('features', []))
                                                        ? 'checked'
                                                        : '')
                                                    : (in_array($postRidePage->features_option1->features_setting_id, explode('=', $ride->features))
                                                        ? 'checked'
                                                        : '')) }}
                                            class="w-4 h-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                        <label for="pink-ride"
                                            class="ml-2 text-gray-900 flex space-x-1">
                                            <span class="text-pink-500 font-medium
                                                @php $disabled = false; @endphp
                                                @if ($user->pink_ride == '0')
                                                    @php $disabled = true; @endphp
                                                @elseif ($user->pink_ride == '')
                                                    @if ($pinkRideSetting)

                                                        @if (($user->gender == 'female') && (empty($user->government_issued_id) || empty($user->address)))
                                                         @php $disabled = true; @endphp
                                                        @elseif ($user->gender !== 'female')
                                                            @php $disabled = true; @endphp
                                                        @elseif ($pinkRideSetting->verfiy_phone === '1' && $user->phone_verified !== '1')
                                                            @php $disabled = true; @endphp
                                                        @elseif ($pinkRideSetting->verify_email === '1' && $user->email_verified !== '1')
                                                            @php $disabled = true; @endphp
                                                        @elseif ($pinkRideSetting->driver_license === '1' && $user->driver !== '1')
                                                            @php $disabled = true; @endphp
                                                        @endif
                                                    @endif
                                                @endif
                                                @if ($disabled)
                                                    {{ 'line-through' }}
                                                @endif">
                                                {{ $postRidePage->features_option1->name }}
                                            </span>
                                            <div class="sups relative">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle-fill text-text-black peer" viewBox="0 0 16 16">
                                                    <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                                                </svg>
                                                <div
                                                  class="absolute right-28 md:right-24 tooltip -top-16 group-hover:flex hidden peer-hover:flex"
                                                >
                                                    <div
                                                        role="tooltip"
                                                        class="absolute after:left-[4.8rem] features_tooltiptext -left-1/2 -top-10 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-blue-500  border border-blue-500 text-gray-600 rounded tooltip_width sm:w-[25rem] md:w-[30rem] px-4"
                                                    >
                                                        <p class="text-white font-normal text-start text-sm lg:text-base">
                                                            @if ($user->pink_ride == '0')
                                                                {{ $postRidePage->pink_ride_tooltip_admin_disable_text }}
                                                            @elseif ($user->pink_ride == '1')
                                                                {{ $postRidePage->pink_ride_tooltip_admin_enable_text }}
                                                            @else
                                                                @if ($pinkRideSetting)
                                                                    {{ $postRidePage->pink_ride_tooltip_only_text }} {{ $postRidePage->pink_ride_tooltip_female_text }} {{ $postRidePage->pink_ride_tooltip_driver_text }}
                                                                    @if ($pinkRideSetting->verfiy_phone === '1' || $pinkRideSetting->verify_email === '1' || $pinkRideSetting->driver_license === '1' || $pinkRideSetting->profile_complete === '1')
                                                                        {{ $postRidePage->pink_ride_tooltip_with_text }}
                                                                        @if ($pinkRideSetting->profile_complete === '1')
                                                                            {{ $postRidePage->pink_ride_tooltip_complete_profile_text }}
                                                                        @endif
                                                                        @if ($pinkRideSetting->verfiy_phone === '1' || $pinkRideSetting->verify_email === '1' || $pinkRideSetting->driver_license === '1')
                                                                            @if ($pinkRideSetting->verfiy_phone === '1')
                                                                                {{ $postRidePage->pink_ride_tooltip_phone_number_text }}
                                                                            @endif
                                                                            @if ($pinkRideSetting->verify_email === '1')
                                                                                {{ $postRidePage->pink_ride_tooltip_email_text }}
                                                                            @endif
                                                                            @if ($pinkRideSetting->driver_license === '1')
                                                                                {{ $postRidePage->pink_ride_tooltip_driver_license_text }}
                                                                            @endif
                                                                            {{ $postRidePage->pink_ride_tooltip_verified_text }}
                                                                        @endif
                                                                    @endif
                                                                    {{ $postRidePage->pink_ride_tooltip_select_this_ride_text }}
                                                                @endif
                                                            @endif
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                @endisset
                                @isset($postRidePage->features_option2->features_setting_id)
                                    @php
                                        // Calculate the age based on the driver's date of birth
                                        $dob = \Carbon\Carbon::parse($user->dob);
                                        $age = $dob->diffInYears(\Carbon\Carbon::now());
                                        $totalRidesCount=0;
                                        $ride_limit=0;
                                        if(isset($totalRides) && !empty($totalRides)){
                                            $totalRidesCount=$totalRides;
                                        };
                                        
                                        if(isset($setting->extra_rides_trip_limit) && !is_null($setting->extra_rides_trip_limit)){
                                            $ride_limit=$setting->extra_rides_trip_limit;
                                        };
                                    @endphp
                                    <div class="flex items-center">
                                        <input id="extra-care" type="checkbox" name="features[]"
                                            @php $disabled = false; @endphp
                                            @if ($user->folks_ride == '0')
                                                @php $disabled = true; @endphp
                                            @elseif ($user->folks_ride == '')
                                                @if ($setting)
                                                    @if ($setting->verfiy_phone === '1' && !$user->phone_numbers->contains('verified', 1))
                                                        @php $disabled = true; @endphp
                                                    @elseif ($setting->verify_email === '1' && $user->email_verified !== '1')
                                                        @php $disabled = true; @endphp
                                                    @elseif ($setting->driver_license === '1' && $user->driver !== '1')
                                                        @php $disabled = true; @endphp
                                                    @elseif (($overallRating < $setting->average_rating || $age < $setting->driver_age) || ($totalRidesCount < $ride_limit) || ($noShowsCount > 0) || ($cancellationCount > 0))
                                                        @php $disabled = true; @endphp
                                                    @elseif ($noshows > 0)
                                                        @php $disabled = true; @endphp
                                                        
                                                    @elseif (empty($user->government_issued_id) || empty($user->address))
                                                        @php $disabled = true; @endphp
                                                    @endif
                                                @endif
                                            @endif
                                            @if ($disabled)
                                                {{ 'disabled' }}
                                            @endif
                                        value="{{ $postRidePage->features_option2->features_setting_id }}"
                                        {{ $isNewForm
                                            ? (in_array($postRidePage->features_option2->features_setting_id, old('features', []))
                                                ? 'checked'
                                                : '')
                                            : (old('features')
                                                ? (in_array($postRidePage->features_option2->features_setting_id, old('features', []))
                                                    ? 'checked'
                                                    : '')
                                                : (in_array($postRidePage->features_option2->features_setting_id, explode('=', $ride->features))
                                                    ? 'checked'
                                                    : '')) }}
                                            class="w-4 h-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                        <label for="extra-care"
                                            class="ml-2 text-gray-900 flex space-x-1">
                                            <span class="text-green-500 font-medium
                                                @php $disabled = false; @endphp
                                                
                                            @if ($user->folks_ride == '0')
                                                    @php $disabled = true; @endphp
                                                @elseif ($user->folks_ride == '')
                                                    @if ($setting)
                                                        @if ($setting->verfiy_phone === '1' && !$user->phone_numbers->contains('verified', 1))
                                                            @php $disabled = true; @endphp
                                                        @elseif ($setting->verify_email === '1' && $user->email_verified !== '1')
                                                            @php $disabled = true; @endphp
                                                        @elseif ($setting->driver_license === '1' && $user->driver !== '1')
                                                            @php $disabled = true; @endphp
                                                        @elseif (($overallRating < $setting->average_rating || $age < $setting->driver_age) || ($totalRidesCount < $ride_limit) || ($noShowsCount > 0) || ($cancellationCount > 0))
                                                            @php $disabled = true; @endphp
                                                        @elseif ($noshows > 0)
                                                            @php $disabled = true; @endphp
                                                        @elseif (empty($user->government_issued_id) || empty($user->address))
                                                        @php $disabled = true; @endphp
                                                        @endif
                                                    @endif
                                                @endif
                                                @if ($disabled)
                                                    {{ 'line-through' }}
                                                    @endif
                                                    "
                                                    @if ($disabled)
                                                    onclick="extraCareRideModal()"

                                                    @endif
                                                    >
                                                {{ $postRidePage->features_option2->name }}
                                            </span>
                                            <div class="sups relative">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle-fill text-black peer" viewBox="0 0 16 16">
                                                    <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                                                </svg>
                                                <div
                                                    class="absolute right-28 md:right-24 tooltip -bottom-3 md:-top-14 lg:-top-16 group-hover:flex hidden peer-hover:flex"
                                                >
                                                    <div
                                                        role="tooltip"
                                                        class="absolute after:left-[4.8rem] features_tooltiptext -left-1/2 -top-10 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-blue-500  border border-blue-500 text-gray-600 rounded tooltip_width sm:w-[25rem] md:w-[30rem] px-4"
                                                    >
                                                        <p class="text-white font-normal text-start text-sm lg:text-base">
                                                            @if ($user->folks_ride == '0')
                                                                {{ $postRidePage->extra_care_tooltip_admin_disable_text }}
                                                            @elseif ($user->folks_ride == '1')
                                                                {{ $postRidePage->extra_care_tooltip_admin_enable_text }}
                                                            @else
                                                                {{ $postRidePage->extra_care_tooltip_driver_review_text }}
                                                                @if ($setting)
                                                                    {{ $setting->average_rating }}
                                                                @else
                                                                    0
                                                                @endif
                                                                {{ $postRidePage->extra_care_tooltip_greater_age_text }}
                                                                @if ($setting)
                                                                    {{ $setting->driver_age }}
                                                                @else
                                                                    0
                                                                @endif
                                                                {{ $postRidePage->extra_care_tooltip_greater_text }}
                                                                @if ($setting)
                                                                    @if ($setting->verfiy_phone === '1' || $setting->verify_email === '1' || $setting->driver_license === '1' || $setting->profile_complete === '1')
                                                                        @if ($pinkRideSetting->profile_complete === '1')
                                                                            {{ $postRidePage->extra_care_tooltip_complete_profile_text }}
                                                                        @endif
                                                                        {{ $postRidePage->extra_care_tooltip_and_his_text }}
                                                                        @if ($setting->verfiy_phone === '1')
                                                                            {{ $postRidePage->extra_care_tooltip_phone_number_text }}
                                                                        @endif
                                                                        @if ($setting->verify_email === '1')
                                                                            {{ $postRidePage->extra_care_tooltip_email_text }}
                                                                        @endif
                                                                        @if ($setting->driver_license === '1')
                                                                            {{ $postRidePage->extra_care_tooltip_driver_license_text }}
                                                                        @endif
                                                                        {{ $postRidePage->extra_care_tooltip_verified_text }}
                                                                    @endif
                                                                @endif {{ $postRidePage->extra_care_tooltip_eligible_text }}
                                                            @endif
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                @endisset
                                @isset($postRidePage->features_option3->features_setting_id)
                                    <div class="flex items-start">
                                        <input id="wi-fi" type="checkbox" name="features[]" value="{{ $postRidePage->features_option3->features_setting_id }}"
                                        {{ $isNewForm
                                            ? (in_array($postRidePage->features_option3->features_setting_id, old('features', []))
                                                ? 'checked'
                                                : '')
                                            : (old('features')
                                                ? (in_array($postRidePage->features_option3->features_setting_id, old('features', []))
                                                    ? 'checked'
                                                    : '')
                                                : (in_array($postRidePage->features_option3->features_setting_id, explode('=', $ride->features))
                                                    ? 'checked'
                                                    : '')) }}
                                            class="mt-2 w-4 h-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                        <label for="wi-fi"
                                            class="ml-2 font-normal text-gray-900 flex space-x-1">
                                            <span>
                                                {{ $postRidePage->features_option3->name }}
                                            </span>
                                        </label>
                                    </div>
                                @endisset
                                @isset($postRidePage->features_option4->features_setting_id)
                                    <div class="flex items-start">
                                        <input id="rating-passengers" type="checkbox" name="features[]" value="{{ $postRidePage->features_option4->features_setting_id }}"
                                        {{ $isNewForm
                                            ? (in_array($postRidePage->features_option4->features_setting_id, old('features', []))
                                                ? 'checked'
                                                : '')
                                            : (old('features')
                                                ? (in_array($postRidePage->features_option4->features_setting_id, old('features', []))
                                                    ? 'checked'
                                                    : '')
                                                : (in_array($postRidePage->features_option4->features_setting_id, explode('=', $ride->features))
                                                    ? 'checked'
                                                    : '')) }}
                                            class="mt-2 w-4 h-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                        <label for="rating-passengers"
                                            class="ml-2 font-normal text-gray-900 flex space-x-1">
                                            <span>
                                                {{ $postRidePage->features_option4->name }}
                                            </span>
                                        </label>
                                    </div>
                                @endisset
                                @isset($postRidePage->features_option5->features_setting_id)
                                    <div class="flex items-start">
                                        <input id="provide-babyseats" type="checkbox" name="features[]" value="{{ $postRidePage->features_option5->features_setting_id }}"
                                        {{ $isNewForm
                                            ? (in_array($postRidePage->features_option5->features_setting_id, old('features', []))
                                                ? 'checked'
                                                : '')
                                            : (old('features')
                                                ? (in_array($postRidePage->features_option5->features_setting_id, old('features', []))
                                                    ? 'checked'
                                                    : '')
                                                : (in_array($postRidePage->features_option5->features_setting_id, explode('=', $ride->features))
                                                    ? 'checked'
                                                    : '')) }}
                                            class="mt-2 w-4 h-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                        <label for="provide-babyseats"
                                            class="ml-2 font-normal text-gray-900 flex space-x-1">
                                            <span class="">
                                                {{ $postRidePage->features_option5->name }}
                                            </span>
                                        </label>
                                    </div>
                                @endisset
                                @isset($postRidePage->features_option6->features_setting_id)
                                    <div class="flex items-start">
                                        <input id="passenger-provide" type="checkbox" name="features[]" value="{{ $postRidePage->features_option6->features_setting_id }}"
                                        {{ $isNewForm
                                            ? (in_array($postRidePage->features_option6->features_setting_id, old('features', []))
                                                ? 'checked'
                                                : '')
                                            : (old('features')
                                                ? (in_array($postRidePage->features_option6->features_setting_id, old('features', []))
                                                    ? 'checked'
                                                    : '')
                                                : (in_array($postRidePage->features_option6->features_setting_id, explode('=', $ride->features))
                                                    ? 'checked'
                                                    : '')) }}
                                            class="mt-2 w-4 h-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                        <label for="passenger-provide"
                                            class="ml-2 font-normal text-gray-900 flex space-x-1">
                                            <span class="">
                                                {{ $postRidePage->features_option6->name }}
                                            </span>
                                        </label>
                                    </div>
                                @endisset
                                @isset($postRidePage->features_option7->features_setting_id)
                                    <div class="flex items-start">
                                        <input id="take-children" type="checkbox" name="features[]" value="{{ $postRidePage->features_option7->features_setting_id }}"
                                        {{ $isNewForm
                                            ? (in_array($postRidePage->features_option7->features_setting_id, old('features', []))
                                                ? 'checked'
                                                : '')
                                            : (old('features')
                                                ? (in_array($postRidePage->features_option7->features_setting_id, old('features', []))
                                                    ? 'checked'
                                                    : '')
                                                : (in_array($postRidePage->features_option7->features_setting_id, explode('=', $ride->features))
                                                    ? 'checked'
                                                    : '')) }}
                                            class="mt-2 w-4 h-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                        <label for="take-children"
                                            class="ml-2 font-normal text-gray-900 flex space-x-1">
                                            <span class="">
                                                {{ $postRidePage->features_option7->name }}
                                            </span>
                                        </label>
                                    </div>
                                @endisset
                                @isset($postRidePage->features_option8->features_setting_id)
                                    <div class="flex items-start">
                                        <input id="passenger-provide1" type="checkbox" name="features[]" value="{{ $postRidePage->features_option8->features_setting_id }}"
                                        {{ $isNewForm
                                            ? (in_array($postRidePage->features_option8->features_setting_id, old('features', []))
                                                ? 'checked'
                                                : '')
                                            : (old('features')
                                                ? (in_array($postRidePage->features_option8->features_setting_id, old('features', []))
                                                    ? 'checked'
                                                    : '')
                                                : (in_array($postRidePage->features_option8->features_setting_id, explode('=', $ride->features))
                                                    ? 'checked'
                                                    : '')) }}
                                            class="mt-2 w-4 h-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                        <label for="passenger-provide1"
                                            class="ml-2 font-normal text-gray-900 flex space-x-1">
                                            <span class="">
                                                {{ $postRidePage->features_option8->name }}
                                            </span>
                                        </label>
                                    </div>
                                @endisset
                                @isset($postRidePage->features_option9->features_setting_id)
                                    <div class="flex items-start">
                                        <input id="bike-rack" type="checkbox" name="features[]" value="{{ $postRidePage->features_option9->features_setting_id }}"
                                        {{ $isNewForm
                                            ? (in_array($postRidePage->features_option9->features_setting_id, old('features', []))
                                                ? 'checked'
                                                : '')
                                            : (old('features')
                                                ? (in_array($postRidePage->features_option9->features_setting_id, old('features', []))
                                                    ? 'checked'
                                                    : '')
                                                : (in_array($postRidePage->features_option9->features_setting_id, explode('=', $ride->features))
                                                    ? 'checked'
                                                    : '')) }}
                                            class="mt-2 w-4 h-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                        <label for="bike-rack"
                                            class="ml-2 font-normal text-gray-900 flex space-x-1">
                                            <span class="">
                                                {{ $postRidePage->features_option9->name }}
                                            </span>
                                        </label>
                                    </div>
                                @endisset
                                @isset($postRidePage->features_option10->features_setting_id)
                                    <div class="flex items-start">
                                        <input id="ski-rack" type="checkbox" name="features[]" value="{{ $postRidePage->features_option10->features_setting_id }}"
                                        {{ $isNewForm
                                            ? (in_array($postRidePage->features_option10->features_setting_id, old('features', []))
                                                ? 'checked'
                                                : '')
                                            : (old('features')
                                                ? (in_array($postRidePage->features_option10->features_setting_id, old('features', []))
                                                    ? 'checked'
                                                    : '')
                                                : (in_array($postRidePage->features_option10->features_setting_id, explode('=', $ride->features))
                                                    ? 'checked'
                                                    : '')) }}
                                            class="mt-2 w-4 h-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                        <label for="ski-rack"
                                            class="ml-2 font-normal text-gray-900 flex space-x-1">
                                            <span class="">
                                                {{ $postRidePage->features_option10->name }}
                                            </span>
                                        </label>
                                    </div>
                                @endisset
                                @isset($postRidePage->features_option11->features_setting_id)
                                    <div class="flex items-start">
                                        <input id="winter-tires" type="checkbox" name="features[]" value="{{ $postRidePage->features_option11->features_setting_id }}"
                                        {{ $isNewForm
                                            ? (in_array($postRidePage->features_option11->features_setting_id, old('features', []))
                                                ? 'checked'
                                                : '')
                                            : (old('features')
                                                ? (in_array($postRidePage->features_option11->features_setting_id, old('features', []))
                                                    ? 'checked'
                                                    : '')
                                                : (in_array($postRidePage->features_option11->features_setting_id, explode('=', $ride->features))
                                                    ? 'checked'
                                                    : '')) }}
                                            class="mt-2 w-4 h-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                        <label for="winter-tires"
                                            class="ml-2 font-normal text-gray-900 flex space-x-1">
                                            <span class="">
                                                {{ $postRidePage->features_option11->name }}
                                            </span>
                                        </label>
                                    </div>
                                @endisset
                                @isset($postRidePage->features_option12->features_setting_id)
                                    <div class="flex items-start">
                                        <input id="air-conditioning" type="checkbox" name="features[]" value="{{ $postRidePage->features_option12->features_setting_id }}"
                                        {{ $isNewForm
                                            ? (in_array($postRidePage->features_option12->features_setting_id, old('features', []))
                                                ? 'checked'
                                                : '')
                                            : (old('features')
                                                ? (in_array($postRidePage->features_option12->features_setting_id, old('features', []))
                                                    ? 'checked'
                                                    : '')
                                                : (in_array($postRidePage->features_option12->features_setting_id, explode('=', $ride->features))
                                                    ? 'checked'
                                                    : '')) }}
                                            class="mt-2 w-4 h-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                        <label for="air-conditioning"
                                            class="ml-2 font-normal text-gray-900 flex space-x-1">
                                            <span class="">
                                                {{ $postRidePage->features_option12->name }}
                                            </span>
                                        </label>
                                    </div>
                                @endisset
                                @isset($postRidePage->features_option13->features_setting_id)
                                    <div class="flex items-start">
                                        <input id="heating" type="checkbox" name="features[]" value="{{ $postRidePage->features_option13->features_setting_id }}"
                                        {{ $isNewForm
                                            ? (in_array($postRidePage->features_option13->features_setting_id, old('features', []))
                                                ? 'checked'
                                                : '')
                                            : (old('features')
                                                ? (in_array($postRidePage->features_option13->features_setting_id, old('features', []))
                                                    ? 'checked'
                                                    : '')
                                                : (in_array($postRidePage->features_option13->features_setting_id, explode('=', $ride->features))
                                                    ? 'checked'
                                                    : '')) }}
                                            class="mt-2 w-4 h-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                        <label for="heating"
                                            class="ml-2 font-normal text-gray-900 flex space-x-1">
                                            <span class="">
                                                {{ $postRidePage->features_option13->name }}
                                            </span>
                                        </label>
                                    </div>
                                @endisset
                                @isset($postRidePage->features_option14->features_setting_id)
                                    <div class="flex items-start">
                                        <input id="heating" type="checkbox" name="features[]" value="{{ $postRidePage->features_option14->features_setting_id }}"
                                        {{ $isNewForm
                                            ? (in_array($postRidePage->features_option14->features_setting_id, old('features', []))
                                                ? 'checked'
                                                : '')
                                            : (old('features')
                                                ? (in_array($postRidePage->features_option14->features_setting_id, old('features', []))
                                                    ? 'checked'
                                                    : '')
                                                : (in_array($postRidePage->features_option14->features_setting_id, explode('=', $ride->features))
                                                    ? 'checked'
                                                    : '')) }}
                                            class="mt-2 w-4 h-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                        <label for="heating"
                                            class="ml-2 font-normal text-gray-900 flex space-x-1">
                                            <span class="">
                                                {{ $postRidePage->features_option14->name }}
                                            </span>
                                        </label>
                                    </div>
                                @endisset
                                @isset($postRidePage->features_option15->features_setting_id)
                                    <div class="flex items-start">
                                        <input id="heating" type="checkbox" name="features[]" value="{{ $postRidePage->features_option15->features_setting_id }}"
                                        {{ $isNewForm
                                            ? (in_array($postRidePage->features_option15->features_setting_id, old('features', []))
                                                ? 'checked'
                                                : '')
                                            : (old('features')
                                                ? (in_array($postRidePage->features_option15->features_setting_id, old('features', []))
                                                    ? 'checked'
                                                    : '')
                                                : (in_array($postRidePage->features_option15->features_setting_id, explode('=', $ride->features))
                                                    ? 'checked'
                                                    : '')) }}
                                            class="mt-2 w-4 h-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                        <label for="heating"
                                            class="ml-2 font-normal text-gray-900 flex space-x-1">
                                            <span class="">
                                                {{ $postRidePage->features_option15->name }}
                                            </span>
                                        </label>
                                    </div>
                                @endisset
                                @isset($postRidePage->features_option16->features_setting_id)
                                    <div class="flex items-start">
                                        <input id="heating" type="checkbox" name="features[]" value="{{ $postRidePage->features_option16->features_setting_id }}"
                                        {{ $isNewForm
                                            ? (in_array($postRidePage->features_option16->features_setting_id, old('features', []))
                                                ? 'checked'
                                                : '')
                                            : (old('features')
                                                ? (in_array($postRidePage->features_option16->features_setting_id, old('features', []))
                                                    ? 'checked'
                                                    : '')
                                                : (in_array($postRidePage->features_option16->features_setting_id, explode('=', $ride->features))
                                                    ? 'checked'
                                                    : '')) }}
                                            class="mt-2 w-4 h-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                        <label for="heating"
                                            class="ml-2 font-normal text-gray-900 flex space-x-1">
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

            </div>
            <div class="">
                <div class="mt-6">

                <div class="bg-white rounded-lg overflow-hidden shadow-3xl">
                        <div class="text-2xl bg-primary text-white py-2 px-4">
                            <h3 class="text-2xl">
                                @isset($postRidePage->cancellation_policy_label)
                                    {{ $postRidePage->cancellation_policy_label }}
                                @endisset
                                <span class="text-white">*</span>
                            </h3>
                        </div>
                        <div class="bg-white p-4">
                            <div>
                                <div class="space-y-2 mt-2">
                                    @isset($postRidePage->cancellation_policy_label1->features_setting_id)
                                        <div class="flex items-center space-x-1 md:space-x-2 mb-2 mr-2 lg:mr-2">
                                            <input id="standard" name="booking_type" type="radio" value="{{ $postRidePage->cancellation_policy_label1->features_setting_id }}"
                                                {{ old('booking_type', $ride->booking_type) == $postRidePage->cancellation_policy_label1->features_setting_id || (empty(old('booking_type'))) ? 'checked' : '' }}
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
                                            <input id="firm" name="booking_type" type="radio" value="{{ $postRidePage->cancellation_policy_label2->features_setting_id }}"
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
                                    <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                        <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                    </div>
                                  </div>
                                @enderror
                            </div>
                        </div>
                </div>

                <div class=" mt-6">
                    <div class="bg-white rounded-lg overflow-hidden shadow-3xl">
                        <div class="text-2xl bg-primary text-white py-2 px-4">
                          <label for="more" class="">
                            <h3 class="text-2xl">
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
                                <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                    <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                </div>
                              </div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mt-6">
                    <div class="bg-white rounded-lg overflow-hidden shadow-3xl">
                        <div class="text-2xl bg-primary text-white py-2 px-4">
                            <h3 class="text-2xl">
                                @isset($postRidePage->disclaimers_label)
                                    {{ $postRidePage->disclaimers_label }}
                                @endisset
                                <span class="text-white">*</span>
                            </h3>
                        </div>
                        <div class="bg-white p-4">
                            @isset($postRidePage->disclaimers_description)
                                {!! str_replace('<ol>', '<ol class="list-decimal list-inside">', str_replace('<li>', '<li class="border-b border-gray-300 text-base lg:text-lg last:border-b-0 py-3">', $postRidePage->disclaimers_description)) !!}
                            @endisset
                        </div>
                    </div>
                </div>

                <div class="mt-4">

                <div class="flex items-start my-4">
                    <input id="agree_terms" type="checkbox" name="agree_terms" value="1"
                        {{ old('agree_terms') == '1' ? 'checked' : '' }}
                        class="w-4 h-4 mt-2 text-blue-600 cursor-pointer bg-white border-gray-500 rounded focus:ring-blue-500  focus:ring-2">
                        <label for="agree_terms" class="ml-2 font-normal text-gray-900 flex items-center space-x-0.5">
                            @isset($postRidePage->agree_terms_label)
                                {!! $postRidePage->agree_terms_label !!}
                            @endisset
                            <span class="text-red-500">*</span>
                        </label>
                    </div>
                    @error('agree_terms')
                    <div class="relative tooltip -bottom-4 group-hover:flex">
                        <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                            <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                        </div>
                    </div>
                    @enderror
                    <div class="hidden lg:flex justify-center items-center mt-8">
                        <button class="bg-greenXS hover:bg-greenXS text-white text-base md:text-lg rounded font-FuturaMdCnBT hover:font-FuturaMdCnBT px-5 py-2 border border-greenXS hover:border-greenXS hover:text-white text-center focus:bg-greenXS focus:text-white active:text-white active:bg-greenXS" type="submit">
                            @isset($postRidePage->submit_button_label)
                                {{ $postRidePage->submit_button_label }}
                            @endisset
                        </button>
                    </div>
                </div>
            </div>
        <div class="flex lg:hidden justify-center items-center mt-8">
            <button class="bg-greenXS hover:bg-greenXS text-white text-base md:text-lg rounded font-FuturaMdCnBT hover:font-FuturaMdCnBT px-5 py-2 border border-greenXS hover:border-greenXS hover:text-white text-center focus:bg-greenXS focus:text-white active:text-white active:bg-greenXS" type="submit">
                @isset($postRidePage->submit_button_label)
                    {{ $postRidePage->submit_button_label }}
                @endisset
            </button>
        </div>
    </form>
</div>

@php
    $projectTimezone = config('app.timezone');
    $projectOffset = \Carbon\Carbon::now($projectTimezone)->offsetHours;
@endphp

@endsection

@section('script')

{{-- <script async
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAadtOhXUj_mb2QWOD1mCPYPRujBiQO4nE&libraries=places&callback=initMap">
</script> --}}

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<script>
    // let autocomplete;
    // function initMap() {
    //     autocomplete = new google.maps.places.Autocomplete(
    //         document.getElementById('from'),
    //         {
    //             types: ['establishment'],
    //             componentRestrictions: {'country' : ['AU']},
    //             fields: ['place_id', 'geometry', 'name']
    //         }
    //     );
    //     autocomplete = new google.maps.places.Autocomplete(
    //         document.getElementById('from'),
    //         {
    //             types: ['establishment'],
    //             componentRestrictions: {'country' : ['AU']},
    //             fields: ['place_id', 'geometry', 'name']
    //         }
    //     );
    // }


    // Define the handler function
    function extraCareRideModal(parms) {
        document.getElementById('extra-care-ride-modal').classList.remove('hidden');
    }
    function closeExtraCareRideModal(parms) {
        document.getElementById('extra-care-ride-modal').classList.add('hidden');
    }
    
    
    function hideTooltip(parms) {
        if ($(this).parent().find('.tooltip').length > 0 && parms != 'label') {
            $(this).parent().find('.tooltip').addClass('hidden');
        }
        else if ($(this).parent().parent().find('.tooltip').length > 0 && parms != 'label') {
            $(this).parent().parent().find('.tooltip').addClass('hidden');
        }
        else if ($(this).parent().parent().parent().find('.tooltip').length > 0) {
            $(this).parent().parent().parent().find('.tooltip').addClass('hidden');
        }
    }

    const inputs = document.querySelectorAll('input, textarea');
    inputs.forEach(input => {
        input.addEventListener('input', hideTooltip); // no parameter on input typing
    });

    const labels = document.querySelectorAll('label');
    labels.forEach(input => {
        input.addEventListener('click', function (e) {
            hideTooltip.call(this, 'label'); // pass 'testing' on label click
        });
    });
    
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
function scrollToFirstError() {
    // Find all error elements
    const errorElements = document.querySelectorAll('.tooltip, .tooltiptext');

    // Find the first visible error element
    let firstVisibleError = null;

    errorElements.forEach(element => {
        // Check if element is visible (basic check - you might need more robust visibility detection)
        if (!firstVisibleError &&
            element.offsetParent !== null &&
            element.getBoundingClientRect().width > 0 &&
            element.getBoundingClientRect().height > 0) {
            firstVisibleError = element;
        }
    });

    if (firstVisibleError) {
        // Scroll to the error with smooth behavior
        firstVisibleError.scrollIntoView({
            behavior: 'smooth',
            block: 'center'
        });

        // Also focus on the related input field if possible
        const relatedInput = firstVisibleError.closest('.relative')?.previousElementSibling?.querySelector('input, select, textarea');
        if (relatedInput) {
            relatedInput.focus();
        }
    }
}

// Add a small delay to ensure dynamic content is loaded
document.addEventListener('DOMContentLoaded', function() {
    @if($errors->any() || session('error'))
        setTimeout(scrollToFirstError, 300);
    @endif
});

    // Handle form submission errors
    document.querySelector('form').addEventListener('submit', function(e) {
        // First check HTML5 validation
        const firstInvalid = this.querySelector(':invalid');
        if (firstInvalid) {
            e.preventDefault();
            firstInvalid.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
            firstInvalid.focus();
            return;
        }

    });
    function swapLocations() {
        // Get the values of the "From" and "To" input fields
        const fromValue = document.getElementById('from_spot_0').value;
        const toValue = document.getElementById('to_spot_0').value;

        // Swap the values
        document.getElementById('from_spot_0').value = toValue;
        document.getElementById('to_spot_0').value = fromValue;
    }

    const dateInput = document.getElementById('dateInput');
    const timeInput = document.getElementById('timeInput');

    const projectOffset = {{ $projectOffset }};

    function getCurrentProjectTime() {
        const now = new Date();

        // System ka UTC offset (minutes me)
        const localOffset = now.getTimezoneOffset();

        // Laravel se aane wala offset hours me hota hai, isko minutes me convert karein
        const laravelOffsetMinutes = projectOffset * 60;

        // Adjust UTC time Laravel ke time zone ke mutabiq
        now.setMinutes(now.getMinutes() + localOffset + laravelOffsetMinutes);

        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');

        return `${hours}:${minutes}`;
    }

    // Retrieve old values from Laravel's old() function

    var rideTime = "";
    if('{{$routeType}}' == "repost"){
        rideTime = '{{ $ride->time }}';
    }
    const oldDate = '{{ old('date') }}';
    const oldTime = rideTime == "" ? '{{ old('time') }}' : rideTime;

    // Initialize the date picker
    flatpickr(dateInput, {
        dateFormat: 'F d, Y',
        minDate: 'today',   // Restrict to future dates only
        defaultDate: oldDate || 'today', // Set default date to today
        disableMobile: true,
        onChange: function(selectedDates, dateStr, instance) {
            // Update minTime based on whether the selected date is today or a future date
            const isToday = instance.latestSelectedDateObj ? instance.latestSelectedDateObj.toDateString() === new Date().toDateString() : false;

            const minTime = isToday ? getCurrentProjectTime() : '00:00';

            // Update minTime dynamically without destroying the entire instance
            timeInput._flatpickr.set('minTime', minTime);

            // If the date is today, set the time input value to the current time
            if (isToday) {
                const utcTime = getCurrentProjectTime();
                timeInput._flatpickr.setDate(utcTime, true, 'H:i');
            }
        },
    });

    // Initialize the date picker for time input
    flatpickr(timeInput, {
        enableTime: true,
        noCalendar: true,
        dateFormat: 'H:i',
        disableMobile: true,
        minTime: getCurrentProjectTime(), // Set min time to current time
        defaultDate: oldTime || '',
        minuteIncrement: 1, // Set minute increment to 1
    });

    // Add a click event listener to the time input field
    timeInput.addEventListener('click', function() {
        // Check if the time input field is empty before setting the default time
        if (!timeInput._flatpickr.input.value) {
            // Set the default time to the current time when the field is clicked
            const projectTime = getCurrentProjectTime();
            timeInput._flatpickr.setDate(projectTime, true, 'H:i');
        }
    });

    function seat_selected(th) {
        var seat = $(th).val();

        for (i = 1; i <= seat; i++) {
            // Change the image source for selected seats
            $(".seat-image.seat-unselect-" + i).attr('src', '{{ asset("assets/seat-hover-1.png") }}');
            $(".seat-number.seat-number-" + i).addClass('text-green-300');
            $("#number-of-seat-cross-" + i).hide();
        }

        for (i = parseInt(seat) + 1; i <= 7; i++) {
            if (seat == 7) continue;
            // Change the image source back to unselected for remaining seats
            $(".seat-image.seat-unselect-" + i).attr('src', '{{ asset("assets/seat.png") }}');
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

    document.addEventListener('DOMContentLoaded', function () {
        // Get the checkbox element
        var skipCheckbox = document.getElementById('skip');
        var addCheckbox = document.getElementById('add');
        var addedCheckbox = document.getElementById('added');
        var recurringTripCheckbox = document.getElementById('recurring_trip');

        // Get the disclaimer div
        var skipVehicle = document.getElementById('skipVehicle');
        var showVehicles = document.getElementById('showVehicles');
        var recurringtripDetails = document.getElementById('recurringtripDetails');

        // Array of all checkboxes for mutual exclusivity
        var checkboxes = [skipCheckbox, addCheckbox, addedCheckbox];

        // Function to uncheck other checkboxes when one is checked
        function handleCheckboxChange(checkedCheckbox) {
            checkboxes.forEach(function (checkbox) {
                if (checkbox !== checkedCheckbox) {
                    checkbox.checked = false; // Uncheck all other checkboxes
                }
            });
        }

        // Add an event listener to the checkbox
        skipCheckbox.addEventListener('change', function () {
            handleCheckboxChange(skipCheckbox);
            // If the checkbox is checked, show the disclaimer; otherwise, hide it
            skipVehicle.style.display = 'none';
            showVehicles.style.display = 'none';
        });
        addCheckbox.addEventListener('change', function () {
            handleCheckboxChange(addCheckbox);
            // If the checkbox is checked, show the disclaimer; otherwise, hide it
            skipVehicle.style.display = this.checked ? 'block' : 'none';
            showVehicles.style.display = 'none';
        });
        addedCheckbox.addEventListener('change', function () {
            handleCheckboxChange(addedCheckbox);
            // If the checkbox is checked, show the disclaimer; otherwise, hide it
            showVehicles.style.display = this.checked ? 'block' : 'none';
            skipVehicle.style.display = 'none';
        });
        recurringTripCheckbox.addEventListener('change', function () {
            // If the checkbox is checked, show the recurring details; otherwise, hide it
            recurringtripDetails.style.display = this.checked ? 'block' : 'none';
        });

        // Initial check when the page loads
        skipVehicle.style.display = addCheckbox.checked ? 'block' : 'none';
        showVehicles.style.display = addedCheckbox.checked ? 'block' : 'none';
        recurringtripDetails.style.display = recurringTripCheckbox.checked ? 'block' : 'none';
    });

    function debounce(func, delay) {
        let timer;
        return function(...args) {
            clearTimeout(timer);
            timer = setTimeout(() => {
                func.apply(this, args);
            }, delay);
        };
    }

    // Function to fetch cities based on search input
    function fetchCities(searchTerm, searchData, fieldId, fieldIndex) {
        // Get the state_id (if required) or set it to null or default
        let stateId = 0;  // You can adjust this if you need to pass state_id
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
                let suggestionsContainer = $('#' + fieldId + '_suggestions'+fieldIndex+'');
                suggestionsContainer.empty();  // Clear previous suggestions

                $.each(result.cities, function(key, value) {
                    // Create a list item for each city
                    let displayText = `${value.name}, ${value.state.abrv}, ${value.state.country.name}`;

                    let suggestionItem = $('<div class="suggestion-item p-2 hover:bg-gray-200 cursor-pointer"></div>')
                        .text(displayText)
                        .on('click', function() {
                            $('#'+fieldId+'_'+fieldIndex+'').val(displayText);
                            fromToInputChange(fieldIndex); // Set the selected city in the input field
                            suggestionsContainer.empty();  // Clear the suggestions
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
                debugger;
                $("#priceData"+index+"").val(result.pricePerKm);
                // Store distance for price validation
                if (result.distance) {
                    $("#priceData"+index+"").data('distance', result.distance);
                }
            }
        });
    }

    document.getElementById('close-modal').addEventListener('click', function () {
        const modal = document.querySelector('.relative.z-50');
        if (modal) {
            modal.style.display = 'none'; // Hide the modal
        }
    });

    function fromInput(index){
        debounce(function() {
            let searchTerm = $('#from_spot_' + index).val();
            if (searchTerm.length >= 2) {
                let searchData = $('#to_spot_' + index).val();
                fetchCities(searchTerm, searchData, 'from_spot', index);
            }
        }, 500)();
    }

    function toInput(index){
        debounce(function() {
            let searchTerm = $('#to_spot_' + index).val();
            if (searchTerm.length >= 2) {
                let searchData = $('#from_spot_' + index).val();
                fetchCities(searchTerm, searchData, 'to_spot', index);
            }
        }, 500)();
    }

    function fromToInputChange(index){
        let searchTerm = $('#to_spot_'+index+'').val();
        let searchData = $('#from_spot_'+index+'').val();
        if (searchTerm != "" && searchData != "") {
            fetchRecommendedPrice(searchTerm, searchData, index);
        }
    }


    function addNewRow() {
        var oldIndex = parseInt($("#rowCount").val());
        if($("#from_spot_"+oldIndex+"").val() == ""){
            alert("Please select from spot");
            return;
        }else if($("#to_spot_"+oldIndex+"").val() == ""){
            alert("Please select to spot");
            return;
        }
        // else if($("#priceData"+oldIndex+"").val() == ""){
        //     alert("Please select price spot");
        //     return;
        // }
        var from_city=$("#from_spot_"+oldIndex+"").val()
        var to_city=$("#to_spot_"+oldIndex+"").val()
        var price=$("#price_"+oldIndex+"").val()
        var index = parseInt($("#rowCount").val() + 1);
        $.ajax({
            url: "{{ url('add-new-spots') }}",
            type: "POST",
            data: {
                from_spot:from_city,
                to_spot:to_city,
                price:price,
                index: index,
                _token: '{{ csrf_token() }}'
            },
            dataType: 'json',
            success: function(result) {
                console.log(result);
                if (result.status === 'error') {
                    console.log(result);
                    if (result.errors.from_spot) {
                        console.log(result);
                        // $('#from_spot_' + index + '_error').text(result.errors.from_spot[0]).show();
                    $('.to_spot_error_'+oldIndex).removeClass('hidden');
                    $('.to_spot_error_message').text(result.errors.to_spot[0]);

                }
                if (result.errors.to_spot) {
                    // Display error for to_spot
                    $('.from_spot_error_'+oldIndex).removeClass('hidden');
                    $('.from_spot_error_message').text(result.errors.from_spot[0]);
                }
                
                if (result.errors.price) {
                    console.log(result.errors.price[0]);
                    // Display error for to_spot
                    $('.price_'+oldIndex).removeClass('hidden');
                    $('.price_message').text(result.errors.price[0]);
                }
            }else{
                $('.from_spot_error_'+oldIndex).addClass('hidden');
                $('.to_spot_error_'+oldIndex).addClass('hidden');

                $(".appendNewRow").append(result.spotHtml);
                $("#rowCount").val(index);
            }

            }
        });
    }

    function removeRow(index, rideDetailId) {
        if(index != 1){
            $(".remove-row"+index+"").remove();
        }

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
    }

    // Initialize tooltip clear listeners
    document.addEventListener('DOMContentLoaded', setupTooltipClearOnInput);

    // Form validation before submission
    function validatePostRideForm() {
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
        const agreeTermsCheckbox = document.getElementById('agree_terms');
        if (agreeTermsCheckbox && !agreeTermsCheckbox.checked) {
            isValid = false;
            // Just highlight the checkbox, no tooltip
            agreeTermsCheckbox.classList.add('validation-error-border', 'ring-2', 'ring-red-500');
            if (!firstErrorField) firstErrorField = agreeTermsCheckbox;
        }

        // Scroll to first error field
        if (!isValid && firstErrorField) {
            firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
            if (firstErrorField.type !== 'checkbox') {
                firstErrorField.focus();
            }
        }

        return isValid;
    }

    // Helper function to show tooltip on checkbox
    function showCheckboxTooltip(checkbox, message) {
        if (!checkbox) return;

        // Remove existing tooltip if any
        removeCheckboxTooltip(checkbox);

        // Add error styling to checkbox
        checkbox.classList.add('validation-error-border', 'ring-2', 'ring-red-500');

        // Create tooltip element
        const tooltip = document.createElement('div');
        tooltip.className = 'validation-tooltip checkbox-tooltip';
        tooltip.innerHTML = `
            <div class="validation-tooltip-arrow"></div>
            <div class="validation-tooltip-content">${message}</div>
        `;

        // Insert tooltip after the checkbox's parent container
        const container = checkbox.closest('.flex') || checkbox.parentNode;
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

    // Add focus/change listener to agree_terms checkbox
    document.addEventListener('DOMContentLoaded', function() {
        const agreeTermsCheckbox = document.getElementById('agree_terms');
        if (agreeTermsCheckbox) {
            agreeTermsCheckbox.addEventListener('change', function() {
                // Remove highlight when checkbox is checked
                this.classList.remove('validation-error-border', 'ring-2', 'ring-red-500');
            });
        }
    });
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
<style>
    .flatpickr-time input {
        font-size:18px !important;
    }
</style>
