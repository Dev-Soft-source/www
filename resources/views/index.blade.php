@extends('layouts.template')

@section('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <style>
        .city-autocomplete-input {
            background-color: white;
            border: 1px solid gray;
            border-radius: 4px;
            padding-bottom: 12px;
            padding-top: 12px;
        }
    </style>
@endsection

@section('content')
    <div class="md:h-96 w-full bg-cover relative z-10"
        style="background-image:url('home_page_icons/{{ $homePage->slider_image }}');">

        @if (session('message'))
            <div class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                    <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                        <div
                            class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-xl transition-all sm:my-12 sm:w-full sm:max-w-lg w-full modal-border">
                            <button onclick="closeModal('message-moda12')"
                                class="absolute top-2 right-2 p-1 rounded-full hover:bg-gray-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
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
                                    <p class="can-exp-p text-center">{!! session('message') !!}</p>
                                </div>
                            </div>
                            <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 justify-center">
                                <a href=""
                                    class="inline-flex w-full justify-center rounded bg-red-500 px-3 py-2 font-FuturaMdCnBT text-lg text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-red-400 sm:ml-3 sm:w-24">{{ $siteText['close_btn_text'] ?? 'Close' }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <!-- <div class="pr-111112"> -->
        @if (session('success'))
            <div id="my-modal" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                    <div class="relative flex min-h-full items-center justify-center p-4  sm:items-center sm:p-0 w-full">
                        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeModal()">
                        </div>
                        <div
                            class="relative animate__animated animate__fadeIn z-20 transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
                            <button onclick="closeModal('success-modal')"
                                class="absolute top-2 right-2 p-1 rounded-full hover:bg-gray-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                            <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                                <div class="sm:flex sm:items-start justify-center">
                                    <!-- <div class="mx-auto h-16 w-16">
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
                            <div class="px-4 pb-6 pt-4  sm:flex sm:flex-row-reverse sm:px-6 justify-center">
                                <a href=""
                                    class="inline-flex w-full justify-center rounded bg-greenXS px-3 py-2 font-FuturaMdCnBT text-lg text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-greenXS sm:ml-3 sm:w-24">{{ $siteText['close_btn_text'] ?? 'Close' }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if (session('success1'))
            <div id="my-modal" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                    <div
                        class="relative flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeModal()">
                        </div>
                        <div
                            class="relative animate__animated animate__fadeIn z-20 transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
                            <div class="bg-white px-4 mt-10 sm:mt-1 pb-4 pt-16 sm:p-6 sm:pb-4 sm:pt-16">
                                <button onclick="closeModal()"
                                    class="absolute top-4 right-4 p-1 rounded-full hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-400 z-50">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                                <div class="sm:flex sm:items-start justify-center">
                                    <div class="mx-auto h-16 w-16">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="4" stroke="currentColor" class="w-12 h-12 text-greenXS">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M4.5 12.75l6 6 9-13.5" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="text-center sm:ml-4 sm:mt-0">
                                    <div class="w-full">
                                        <p class="can-exp-p text-center font-FuturaMdCnBT">{!! session('success1') !!}</p>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="px-4 pb-6 pt-4 items-center space-x-2 sm:space-x-4 sm:px-6 justify-center hidden md:flex">
                                <a href="{{ route('step1to5', ['lang' => $selectedLanguage->abbreviation]) }}"
                                    class="inline-flex w-full justify-center rounded bg-greenXS px-3 py-2 font-FuturaMdCnBT text-lg text-white whitespace-nowrap hover:text-white hover:shadow-lg shadow-sm hover:bg-greenXS sm:ml-3 sm:w-fit">{!! session('create_my_profile_btn') !!}</a>
                                <a href="#" class="button-exp-fill"
                                    onclick="handleSuccessModalClose(event)">{{ $siteText['close_btn_text'] ?? 'Close' }}</a>
                            </div>
                            @if (auth()->check() && isset($token))
                                <div
                                    class="px-4 pb-6 pt-4 flex items-center space-x-2 sm:space-x-4 sm:px-6 justify-center md:hidden">
                                    <a href="{{ route('login_with_app', ['lang' => $selectedLanguage->abbreviation, 'token' => $token]) }}"
                                        class="inline-flex w-full justify-center rounded bg-greenXS px-3 py-2 font-FuturaMdCnBT text-lg text-white whitespace-nowrap hover:text-white hover:shadow-lg shadow-sm hover:bg-greenXS sm:ml-3 sm:w-fit">{!! session('continue_with_app_btn') !!}</a>
                                    <a href="#" class="button-exp-fill"
                                        onclick="handleSuccessModalClose(event)">{{ $siteText['close_btn_text'] ?? 'Close' }}</a>
                                </div>
                            @else
                                <div
                                    class="px-4 pb-6 pt-4 flex items-center space-x-2 sm:space-x-4 sm:px-6 justify-center md:hidden">
                                    <a href="{{ route('login_with_app', ['lang' => $selectedLanguage->abbreviation]) }}"
                                        class="inline-flex w-full justify-center rounded bg-greenXS px-3 py-2 font-FuturaMdCnBT text-lg text-white whitespace-nowrap hover:text-white hover:shadow-lg shadow-sm hover:bg-greenXS sm:ml-3 sm:w-fit">{!! session('continue_with_app_btn') !!}</a>
                                    <a href="#" class="button-exp-fill"
                                        onclick="handleSuccessModalClose(event)">{{ $siteText['close_btn_text'] ?? 'Close' }}</a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="bg-black bg-opacity-50 h-full relative top-0 z-30">
            <section class="pt-14 pb-14 flex flex-col justify-center items-center h-full space-y-8">
                <div>
                    <div class="text-center text-white text-lg font-FuturaMdCnBT px-3 py-2 bg-blue-600 rounded">
                        @isset($homePage->slider_heading)
                            {{ $homePage->slider_heading }}
                        @endisset
                    </div>
                </div>
                @php
                    $oldOriginLabel = old('origin.label');
                    $oldOriginCityId = old('origin.city_id');
                    $oldDestinationLabel = old('destination.label');
                    $oldDestinationCityId = old('destination.city_id');
                    $oldDepartureDate = old('departure_date');
                @endphp
                    <form method="POST" action="{{ route('search_ride.validate', ['lang' => optional($selectedLanguage)->abbreviation]) }}" id="home-search-form">
                <div class="flex flex-col md:ml-10 sm:flex-col md:flex-row lg:flex-row gap-4 px-4 md:px-8 xl:px-0">
                        @csrf
                        <div class="flex flex-col sm:flex-col md:flex-row lg:flex-row md:items-center gap-1 relative">
                            <div class="w-54 relative">
                                @livewire(
                                    'px.city-autocomplete',
                                    [
                                        'field' => 'origin',
                                        'placeholder' => $findRidePage->search_section_from_placeholder ?? 'Origin',
                                        'initialLabel' => $oldOriginLabel,
                                        'initialCityId' => $oldOriginCityId,
                                        'invalidErrorMessage' => __('validation.custom.city_not_in_record.message') ?? 'Please select a valid city from the dropdown',
                                        'class' => 'h-full w-full border-0 bg-transparent pl-10 pr-4 text-slate-900 placeholder-slate-900 focus:ring-0',
                                    ],
                                    key('px-search-origin')
                                )
                                @error('origin.label')
                                    <div class="tooltip-error shadow-lg mt-1">
                                        <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                                            <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                        </div>
                                    </div>
                                @enderror
                            </div>
                            <div class="relative">
                                <div class="flex justify-center items-center">
                                    <button onclick="swapLocations()">
                                        <div class="w-8 h-8">
                                            @isset($homePage->swap_field_icon)
                                                <img class="w-full h-full object-contain"
                                                    src="{{ asset('home_page_icons/' . $homePage->swap_field_icon) }}"
                                                    alt="">
                                            @endisset
                                        </div>
                                    </button>
                                </div>
                            </div>
                            <div class="w-54 relative">
                                @livewire(
                                    'px.city-autocomplete',
                                    [
                                        'field' => 'destination',
                                        'placeholder' => $findRidePage->search_section_to_placeholder ?? 'Destination',
                                        'initialLabel' => $oldDestinationLabel,
                                        'initialCityId' => $oldDestinationCityId,
                                        'invalidErrorMessage' => __('validation.custom.city_not_in_record.message') ?? 'Please select a valid city from the dropdown',
                                        'class' => 'h-full w-full border-0 bg-transparent pl-10 pr-4 text-slate-900 placeholder-slate-900 focus:ring-0',
                                    ],
                                    key('px-search-destination')
                                )
                                @error('destination.label')
                                    <div class="tooltip-error shadow-lg mt-1">
                                        <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                                            <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                        </div>
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="mx-auto md:mx-0 md:w-auto flex flex-col sm:flex-col md:flex-row items-center gap-2">
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
                                    class="city-autocomplete-input h-full border-0 bg-transparent pl-10 text-slate-900 placeholder-slate-900 focus:ring-0"
                                    placeholder="{{ $findRidePage->search_section_date_placeholder ?? 'Select date' }}"
                                    autocomplete="off">
                                <button type="button" id="departure-date-clear-button"
                                    class="absolute right-4 top-1/2 hidden -translate-y-1/2 flex h-6 w-6 items-center justify-center rounded-full bg-gray-200 text-gray-500 transition hover:bg-gray-300 hover:text-gray-700"
                                    aria-label="Clear date">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                                        fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd"
                                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>
                                @error('departure_date')
                                    <div class="tooltip-error shadow-lg mt-1">
                                        <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                                            <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                        </div>
                                    </div>
                                @enderror
                            </div>
                            <div class="flex justify-center items-center">
                                <button type="submit" class="bg-primary py-2 px-3 rounded button-exp-fill">
                                    <span class="block md:hidden">{{ $siteText['search_btn_text'] ?? 'Search' }}</span>
                                    <div class="w-auto h-6 hidden md:block">
                                        @isset($homePage->search_field_icon)
                                            <img class="w-full h-full object-contain"
                                                src="{{ asset('home_page_icons/' . $homePage->search_field_icon) }}"
                                                alt="">
                                        @endisset
                                    </div>
                                </button>
                            </div>
                        </div>
                        <input type="hidden" name="search" value="1">
                    </div>
                </form>
            </section>
        </div>
    </div>

    <div class="container mx-auto px-4 md:px-8 xl:px-0 relative">
        <section class="py-14">
            <h1 class="text-center">
                @isset($homePage->section1_main_heading)
                    {{ $homePage->section1_main_heading }}
                @endisset
            </h1>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-8 gap-4 xl:gap-8 mt-8 md:mt-12">
                <div class="relative">
                    <div class="h-full">
                        <a href="{{ route('pink_ride', ['lang' => $selectedLanguage->abbreviation]) }}">

                            <div class="w-full h-full py-8 px-4 border border-gray-100 shadow rounded-md">
                                <div class="h-20 w-20 mx-auto flex justify-center items-center">
                                    <img class=""
                                        src="{{ asset('home_page_icons/' . $homePage->section1_pink_rides_image) }}"
                                        alt="">
                                </div>
                                <div>
                                    <h3 class="my-4 text-center text-pink-700 font-FuturaMdCnBT hover:text-black">
                                        @isset($homePage->section1_pink_rides_label)
                                            {{ $homePage->section1_pink_rides_label }}
                                        @endisset
                                    </h3>

                                    <p
                                        class="text-black text-justify font-semibold mt-4 lg:text-lg md:text-base text-base">
                                        @isset($homePage->section1_pink_rides_description)
                                            {!! $homePage->section1_pink_rides_description !!}
                                        @endisset
                                    </p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>

                <div class="relative">
                    <div class="h-full">
                        <a href="{{ route('folk_ride', ['lang' => $selectedLanguage->abbreviation]) }}">

                            <div class="w-full h-full py-8 px-4 border border-gray-100 shadow rounded-md">
                                <div class="h-20 w-20 mx-auto flex justify-center items-center">
                                    <img class=""
                                        src="{{ asset('home_page_icons/' . $homePage->section1_folks_rides_image) }}"
                                        alt="">
                                </div>
                                <div>
                                    <h3 class="my-4 text-center text-indigo-600 hover:text-black">
                                        @isset($homePage->section1_folks_rides_label)
                                            {{ $homePage->section1_folks_rides_label }}
                                        @endisset
                                    </h3>

                                    @isset($homePage->section1_folks_rides_description)
                                        {!! $homePage->section1_folks_rides_description !!}
                                    @endisset
                                </div>
                            </div>
                        </a>
                    </div>
                </div>

                <div class="relative">
                    <div class="h-full">
                        <a href="{{ route('proximalocal_ride', ['lang' => $selectedLanguage->abbreviation]) }}">
                            <div class="w-full h-full py-8 px-4 border border-gray-100 shadow rounded-md">
                                <div class="h-20 w-20 mx-auto flex justify-center items-center">
                                    <img class=""
                                        src="{{ asset('home_page_icons/' . $homePage->section1_customize_image) }}"
                                        alt="">
                                </div>
                                <div>
                                    <h3 class="mt-4 text-center text-green-700 hover:text-black">
                                        @isset($homePage->section1_customize_label)
                                            {{ $homePage->section1_customize_label }}
                                        @endisset
                                    </h3>
                                    <p class="text-black text-justify lg:text-lg md:text-base text-base mt-4">
                                        @isset($homePage->section1_customize_description)
                                            {!! $homePage->section1_customize_description !!}
                                        @endisset
                                    </p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <section class="bg-blue-600 w-full px-4 md:px-8 xl:px-0 py-14">
        <div class="container mx-auto">
            <h1 class="text-white text-center">
                @isset($homePage->section2_main_heading)
                    {{ $homePage->section2_main_heading }}
                @endisset
            </h1>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-y-8 gap-4 xl:gap-8 mt-8 md:mt-12">
                <div class="bg-white p-4 rounded-md shadow relative">

                    <div class="pb-8">
                        <div class="h-16 w-16 flex justify-center items-center">
                            <img src="{{ asset('home_page_icons/' . $homePage->section2_profile_verification_image) }}"
                                alt="">
                        </div>
                        <h3 class="mt-3">
                            @isset($homePage->section2_profile_verification_label)
                                {{ $homePage->section2_profile_verification_label }}
                            @endisset
                        </h3>
                        <p class="text-justify mt-1 lg:text-lg md:text-base text-base">
                            @isset($homePage->section2_profile_verification_description)
                                {!! $homePage->section2_profile_verification_description !!}
                            @endisset
                        </p>
                    </div>
                </div>

                <div class="bg-white p-4 rounded-md shadow relative">

                    <div class="pb-8">
                        <div class="h-16 w-16 flex justify-center items-center">
                            <img src="{{ asset('home_page_icons/' . $homePage->section2_policies_image) }}"
                                alt="">
                        </div>
                        <h3 class="mt-3">
                            @isset($homePage->section2_policies_label)
                                {{ $homePage->section2_policies_label }}
                            @endisset
                        </h3>
                        <p class="text-justify mt-1 lg:text-lg md:text-base text-base">
                            @isset($homePage->section2_policies_description)
                                {!! $homePage->section2_policies_description !!}
                            @endisset
                        </p>
                    </div>
                </div>

                <div class="bg-white p-4 rounded-md shadow relative">

                    <div class="pb-8">
                        <div class="h-16 w-16 flex justify-center items-center">
                            <img src="{{ asset('home_page_icons/' . $homePage->section2_car_insurance_image) }}"
                                alt="">
                        </div>
                        <h3 class="mt-3">
                            @isset($homePage->section2_car_insurance_label)
                                {{ $homePage->section2_car_insurance_label }}
                            @endisset
                        </h3>
                        <p class="text-justify mt-1 lg:text-lg md:text-base text-base">
                            @isset($homePage->section2_car_insurance_description)
                                {!! $homePage->section2_car_insurance_description !!}
                            @endisset
                        </p>
                    </div>
                </div>

                <div class="bg-white p-4 rounded-md shadow relative">

                    <div class="pb-8">
                        <div class="h-16 w-16 flex justify-center items-center">
                            <img src="{{ asset('home_page_icons/' . $homePage->section2_help_image) }}" alt="">
                        </div>
                        <h3 class="mt-3">
                            @isset($homePage->section2_help_label)
                                {{ $homePage->section2_help_label }}
                            @endisset
                        </h3>
                        <p class="text-justify mt-1 lg:text-lg md:text-base text-base">
                            @isset($homePage->section2_help_description)
                                {!! $homePage->section2_help_description !!}
                            @endisset
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <section class="container mx-auto">

        <div class="py-14 px-4 md:px-8 xl:px-0">
            <h1 class="text-center">
                @isset($homePage->section3_main_heading)
                    {{ $homePage->section3_main_heading }}
                @endisset
            </h1>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-8 gap-4 xl:gap-8 mt-8 md:mt-12">
                <div class="relative">
                    <div class="h-full">

                        <div class="w-full h-full py-6 px-4 border border-gray-100 shadow rounded-md">
                            <div>
                                <div class="h-16 w-16 flex justify-center items-center">
                                    <img class=""
                                        src="{{ asset('home_page_icons/' . $homePage->section3_safe_image) }}"
                                        alt="">
                                </div>
                                <h3 class="mt-1">
                                    @isset($homePage->section3_safe_label)
                                        {{ $homePage->section3_safe_label }}
                                    @endisset
                                </h3>
                                <p class="text-justify mt-2 lg:text-lg md:text-base text-base">
                                    @isset($homePage->section3_safe_description)
                                        {!! $homePage->section3_safe_description !!}
                                    @endisset
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative">
                    <div class="h-full">

                        <div class="w-full h-full py-6 px-4 border border-gray-100 shadow rounded-md">
                            <div>
                                <div class="h-16 w-16 flex justify-center items-center">
                                    <img class=""
                                        src="{{ asset('home_page_icons/' . $homePage->section3_affordable_image) }}"
                                        alt="">
                                </div>
                                <h3 class="mt-1">
                                    @isset($homePage->section3_affordable_label)
                                        {{ $homePage->section3_affordable_label }}
                                    @endisset
                                </h3>
                                <p class="text-justify mt-2 lg:text-lg md:text-base text-base">
                                    @isset($homePage->section3_affordable_description)
                                        {!! $homePage->section3_affordable_description !!}
                                    @endisset
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative h-full">
                    <div class="h-full">

                        <div class="w-full py-6 px-4 border border-gray-100 shadow rounded-md h-full">
                            <div>
                                <div class="h-16 w-16 flex justify-center items-center">
                                    <img class=""
                                        src="{{ asset('home_page_icons/' . $homePage->section3_reliable_image) }}"
                                        alt="">
                                </div>
                                <h3 class="mt-1">
                                    @isset($homePage->section3_reliable_label)
                                        {{ $homePage->section3_reliable_label }}
                                    @endisset
                                </h3>
                                <p class="text-justify mt-2 lg:text-lg md:text-base text-base">
                                    @isset($homePage->section3_reliable_description)
                                        {!! $homePage->section3_reliable_description !!}
                                    @endisset
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>

    <section class="bg-blue-600 py-14 px-4 md:px-8 xl:px-0">
        <div class="container mx-auto">
            <h1 class="text-white text-center mb-0">
                @isset($homePage->section4_main_heading)
                    {{ $homePage->section4_main_heading }}
                @endisset
            </h1>
            <div class="grid sm:grid-cols-1 md:grid-cols-1 lg:grid-cols-2 mt-4 md:mt-10 gap-6 items-center">
                <div class="flex flex-col space-y-4">
                    @if (!empty($rides))
                        @foreach ($rides as $ride)
                        <a href="{{ route('ride_detail', ['lang'=>app()->getLocale(), 'id'=>$ride->id]) }}" >
                            <x-px.search-card 
                                :ride="$ride" 
                                :lang="optional($selectedLanguage)->abbreviation" 
                                :detail-route="$ride->detail_route ?? 'px.ride_detail'" 
                                :detail-query="$ride->detail_query ?? []"
                                :show-status="true" 
                                :show-driver-info="true" 
                                :show-options="true" 
                                :show-booking-button="false" 
                                :show-kind-border="false" 
                                :price-minor="$ride->matched_segment_price_minor ?? $ride->price_minor" />
                        </a>
                        @endforeach
                    @endif
                </div>
                <div>
                    @if ($video)
                        @php
                            // $video->link contains the YouTube video URL
                            $youtubeUrl = $video->link;
                            // Extract the video ID from the URL
                            parse_str(parse_url($youtubeUrl, PHP_URL_QUERY), $query);
                            $videoId = $query['v'] ?? '';
                        @endphp

                        @if (!empty($videoId))
                            {{-- Embed the YouTube video using an iframe --}}
                            <iframe class="mx-auto rounded-md h-full w-full md:h-[700px]"
                                src="https://www.youtube.com/embed/{{ $videoId }}">
                            </iframe>
                        @else
                            <p>Invalid YouTube video URL</p>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </section>

    <section class="py-14 px-4 md:px-8 xl:px-0">
        <div class="container mx-auto">
            <h1 class="text-center mb-0">
                @isset($homePage->work_section_main_heading)
                    {{ $homePage->work_section_main_heading }}
                @endisset
            </h1>
            <p class="lg:text-lg md:text-base text-base text-center">
                @isset($homePage->work_section_main_text)
                    {{ $homePage->work_section_main_text }}
                @endisset
            </p>

            <div class="mt-4 md:mt-6 xl:mt-10 grid lg:grid-cols-2 md:grid-cols-2 sm:grid-cols-1 gap-4 xl:gap-6">
                <div class="rounded-md border shadow flex flex-col">
                    <div class="bg-blue-600 p-3 shadow border border-blue-600 flex-1 rounded-t-md">
                        <h3 class="text-white text-center">
                            @isset($homePage->work_section_passenger_label)
                                {{ $homePage->work_section_passenger_label }}
                            @endisset
                        </h3>
                        <p class="text-white text-center lg:text-lg md:text-base text-base">
                            @isset($homePage->work_section_passenger_description)
                                {{ $homePage->work_section_passenger_description }}
                            @endisset
                        </p>
                    </div>
                    <div class="p-2 md:p-4 space-y-4">
                        <div class="bg-white rounded shadow p-3 flex items-start border gap-4">
                            <div>
                                <div class="h-14 w-14 rounded-full mt-0.5">
                                    @isset($homePage->work_section_passenger_point1_image)
                                        <img class="w-full h-full object-contain"
                                            src="{{ asset('home_page_icons/' . $homePage->work_section_passenger_point1_image) }}"
                                            alt="">
                                    @endisset
                                </div>
                            </div>
                            <div>
                                <p class="font-FuturaMdCnBT lg:text-[22px] text-xl">
                                    @isset($homePage->work_section_passenger_point1_label)
                                        {{ $homePage->work_section_passenger_point1_label }}
                                    @endisset
                                </p>
                                <p class="text-justify lg:text-lg md:text-base text-base">
                                    @isset($homePage->work_section_passenger_point1_description)
                                        {{ $homePage->work_section_passenger_point1_description }}
                                    @endisset
                                </p>
                            </div>
                        </div>

                        <div class="bg-white rounded shadow p-3 flex items-start border gap-4">
                            <div>
                                <div class="h-14 w-14 rounded-full mt-0.5">
                                    @isset($homePage->work_section_passenger_point2_image)
                                        <img class="w-full h-full object-contian"
                                            src="{{ asset('home_page_icons/' . $homePage->work_section_passenger_point2_image) }}"
                                            alt="">
                                    @endisset
                                </div>
                            </div>
                            <div>
                                <p class="font-FuturaMdCnBT lg:text-[22px] text-xl">
                                    @isset($homePage->work_section_passenger_point2_label)
                                        {{ $homePage->work_section_passenger_point2_label }}
                                    @endisset
                                </p>
                                <p class="text-justify lg:text-lg md:text-base text-base">
                                    @isset($homePage->work_section_passenger_point2_description)
                                        {{ $homePage->work_section_passenger_point2_description }}
                                    @endisset
                                </p>
                            </div>
                        </div>

                        <div class="bg-white rounded shadow p-3 flex items-start border gap-4">
                            <div>
                                <div class="h-14 w-14 rounded-full mt-0.5">
                                    <img class=""
                                        src="{{ asset('home_page_icons/' . $homePage->work_section_passenger_point3_image) }}"
                                        alt="">
                                </div>
                            </div>
                            <div>
                                <p class="font-FuturaMdCnBT lg:text-[22px] text-xl">
                                    @isset($homePage->work_section_passenger_point3_label)
                                        {{ $homePage->work_section_passenger_point3_label }}
                                    @endisset
                                </p>
                                <p class="text-justify lg:text-lg md:text-base text-base">
                                    @isset($homePage->work_section_passenger_point3_description)
                                        {{ $homePage->work_section_passenger_point3_description }}
                                    @endisset
                                </p>
                            </div>
                        </div>

                        <div class="bg-white rounded shadow p-3 flex items-start border gap-4">
                            <div>
                                <div class="h-14 w-14 rounded-full mt-0.5">
                                    <img class=""
                                        src="{{ asset('home_page_icons/' . $homePage->work_section_passenger_point4_image) }}"
                                        alt="">
                                </div>
                            </div>
                            <div>
                                <p class="font-FuturaMdCnBT lg:text-[22px] text-xl">
                                    @isset($homePage->work_section_passenger_point4_label)
                                        {{ $homePage->work_section_passenger_point4_label }}
                                    @endisset
                                </p>
                                <p class="text-justify lg:text-lg md:text-base text-base">
                                    @isset($homePage->work_section_passenger_point4_description)
                                        {{ $homePage->work_section_passenger_point4_description }}
                                    @endisset
                                </p>
                            </div>
                        </div>

                        <div class="bg-white rounded shadow p-3 flex items-start border gap-4">
                            <div>
                                <div class="h-14 w-14 rounded-full mt-0.5">
                                    <img class=""
                                        src="{{ asset('home_page_icons/' . $homePage->work_section_passenger_point5_image) }}"
                                        alt="">
                                </div>
                            </div>
                            <div>
                                <p class="font-FuturaMdCnBT lg:text-[22px] text-xl">
                                    @isset($homePage->work_section_passenger_point5_label)
                                        {{ $homePage->work_section_passenger_point5_label }}
                                    @endisset
                                </p>
                                <p class="text-justify lg:text-lg md:text-base text-base">
                                    @isset($homePage->work_section_passenger_point5_description)
                                        {{ $homePage->work_section_passenger_point5_description }}
                                    @endisset
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-md border shadow flex flex-col">
                    <div class="bg-blue-600 p-3 shadow border border-blue-600 flex-1 rounded-t-md">
                        <h3 class="text-white text-center">
                            @isset($homePage->work_section_driver_label)
                                {{ $homePage->work_section_driver_label }}
                            @endisset
                        </h3>
                        <p class="text-white text-center lg:text-lg md:text-base text-base">
                            @isset($homePage->work_section_driver_description)
                                {{ $homePage->work_section_driver_description }}
                            @endisset
                        </p>
                    </div>
                    <div class="p-2 md:p-4 space-y-4">
                        <div class="bg-white rounded shadow p-3 flex items-start border gap-4">
                            <div>
                                <div class="h-14 w-14 rounded-full mt-0.5">
                                    @isset($homePage->work_section_driver_point1_image)
                                        <img class="w-full h-full object-contain"
                                            src="{{ asset('home_page_icons/' . $homePage->work_section_driver_point1_image) }}"
                                            alt="">
                                    @endisset
                                </div>
                            </div>
                            <div>
                                <p class="font-FuturaMdCnBT lg:text-[22px] text-xl">
                                    @isset($homePage->work_section_driver_point1_label)
                                        {{ $homePage->work_section_driver_point1_label }}
                                    @endisset
                                </p>
                                <p class="text-justify lg:text-lg md:text-base text-base">
                                    @isset($homePage->work_section_driver_point1_description)
                                        {{ $homePage->work_section_driver_point1_description }}
                                    @endisset
                                </p>
                            </div>
                        </div>

                        <div class="bg-white rounded shadow p-3 flex items-start border gap-4">
                            <div>
                                <div class="h-14 w-14 rounded-full mt-0.5">
                                    @isset($homePage->work_section_driver_point2_image)
                                        <img class="w-full h-full object-contain"
                                            src="{{ asset('home_page_icons/' . $homePage->work_section_driver_point2_image) }}"
                                            alt="">
                                    @endisset
                                </div>
                            </div>
                            <div>
                                <p class="font-FuturaMdCnBT lg:text-[22px] text-xl">
                                    @isset($homePage->work_section_driver_point2_label)
                                        {{ $homePage->work_section_driver_point2_label }}
                                    @endisset
                                </p>
                                <p class="text-justify lg:text-lg md:text-base text-base">
                                    @isset($homePage->work_section_driver_point2_description)
                                        {{ $homePage->work_section_driver_point2_description }}
                                    @endisset
                                </p>
                            </div>
                        </div>

                        <div class="bg-white rounded shadow p-3 flex items-start border gap-4">
                            <div>
                                <div class="h-14 w-14 rounded-full mt-0.5">
                                    @isset($homePage->work_section_driver_point3_image)
                                        <img class="w-full h-full object-contain"
                                            src="{{ asset('home_page_icons/' . $homePage->work_section_driver_point3_image) }}"
                                            alt="">
                                    @endisset
                                </div>
                            </div>
                            <div>
                                <p class="font-FuturaMdCnBT lg:text-[22px] text-xl">
                                    @isset($homePage->work_section_driver_point3_label)
                                        {{ $homePage->work_section_driver_point3_label }}
                                    @endisset
                                </p>
                                <p class="text-justify lg:text-lg md:text-base text-base">
                                    @isset($homePage->work_section_driver_point3_description)
                                        {{ $homePage->work_section_driver_point3_description }}
                                    @endisset
                                </p>
                            </div>
                        </div>

                        <div class="bg-white rounded shadow p-3 flex items-start border gap-4">
                            <div>
                                <div class="h-14 w-14 rounded-full mt-0.5">
                                    @isset($homePage->work_section_driver_point4_image)
                                        <img class="w-full h-full object-contain"
                                            src="{{ asset('home_page_icons/' . $homePage->work_section_driver_point4_image) }}"
                                            alt="">
                                    @endisset
                                </div>
                            </div>
                            <div>
                                <p class="font-FuturaMdCnBT lg:text-[22px] text-xl">
                                    @isset($homePage->work_section_driver_point4_label)
                                        {{ $homePage->work_section_driver_point4_label }}
                                    @endisset
                                </p>
                                <p class="text-justify lg:text-lg md:text-base text-base">
                                    @isset($homePage->work_section_driver_point4_description)
                                        {{ $homePage->work_section_driver_point4_description }}
                                    @endisset
                                </p>
                            </div>
                        </div>

                        <div class="bg-white rounded shadow p-3 flex items-start border gap-4">
                            <div>
                                <div class="h-14 w-14 rounded-full mt-0.5">
                                    @isset($homePage->work_section_driver_point5_image)
                                        <img class="w-full h-full object-contain"
                                            src="{{ asset('home_page_icons/' . $homePage->work_section_driver_point5_image) }}"
                                            alt="">
                                    @endisset
                                </div>
                            </div>
                            <div>
                                <p class="font-FuturaMdCnBT lg:text-[22px] text-xl">
                                    @isset($homePage->work_section_driver_point5_label)
                                        {{ $homePage->work_section_driver_point5_label }}
                                    @endisset
                                </p>
                                <p class="text-justify lg:text-lg md:text-base text-base">
                                    @isset($homePage->work_section_driver_point5_description)
                                        {{ $homePage->work_section_driver_point5_description }}
                                    @endisset
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section style="background-image: url('home_page_icons/{{ $homePage->doing_section_slider_image }}');"
        class="bg-cover w-full h-80 relative">
        <div
            class="absolute left-0 right-0 m-auto bg-blue-600 h-full w-full bg-opacity-50 flex justify-center items-start">
            <div class="text-center w-full md:w-2/5 m-auto">
                <h1 class="text-center text-white">
                    @isset($homePage->doing_section_main_heading)
                        {{ $homePage->doing_section_main_heading }}
                    @endisset
                </h1>
                <p class="lg:text-lg md:text-base text-base text-center text-white">
                    @isset($homePage->doing_section_main_text)
                        {{ $homePage->doing_section_main_text }}
                    @endisset
                </p>
                <div class="flex items-center justify-center gap-6 mt-6">
                    <a href="{{ route('post_ride', ['lang' => optional($selectedLanguage)->abbreviation]) }}"
                        class="bg-transparent border hover:bg-white hover:text-primary font-FuturaMdCnBT text-lg border-white px-4 py-2 rounded text-white">
                        @isset($homePage->doing_section_label1)
                            {{ $homePage->doing_section_label1 }}
                        @endisset
                    </a>
                    <a href="{{ route('search_ride', ['lang' => optional($selectedLanguage)->abbreviation]) }}"
                        class="bg-transparent border hover:bg-white hover:text-primary font-FuturaMdCnBT text-lg border-white px-4 py-2 rounded text-white">
                        @isset($homePage->doing_section_label2)
                            {{ $homePage->doing_section_label2 }}
                        @endisset
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="py-14 px-4 md:px-8 xl:px-0">
        <div class="container mx-auto">
            <h1 class="text-blue-600 text-center mb-0">
                @isset($homePage->reasons_section_main_heading)
                    {{ $homePage->reasons_section_main_heading }}
                @endisset
            </h1>
            <p class="lg:text-lg md:text-base text-base text-center">
                @isset($homePage->reasons_section_main_text)
                    {{ $homePage->reasons_section_main_text }}
                @endisset
            </p>
            <div
                class="grid lg:grid-cols-5 md:grid-cols-2 sm:grid-cols-1 gap-x-4 xl:gap-x-6 gap-y-8 xl:gap-y-10 mt-8 md:mt-12">

                <div class="relative h-full">
                    <div class=" h-full">
                        <div
                            class="absolute -top-5 left-0 right-0 mx-auto bg-white rounded-md p-1 border h-14 w-14 shadow flex justify-center items-center">
                            <img class=""
                                src="{{ asset('home_page_icons/' . $homePage->reasons_section_members_image) }}"
                                alt="">
                        </div>
                        <div class="w-full py-10 px-4 border border-gray-100 shadow rounded-md h-full">
                            <div class="text-center">
                                <h3 class="text-center mt-1">
                                    @isset($homePage->reasons_section_members_label)
                                        {{ $homePage->reasons_section_members_label }}
                                    @endisset
                                </h3>
                                <p class="text-justify mt-2 lg:text-lg md:text-base text-base">
                                    @isset($homePage->reasons_section_members_description)
                                        {{ $homePage->reasons_section_members_description }}
                                    @endisset
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative h-full">
                    <div class=" h-full">
                        <div
                            class="absolute -top-5 left-0 right-0 mx-auto bg-white rounded-md p-1 border h-14 w-14 shadow flex justify-center items-center">
                            <img class=""
                                src="{{ asset('home_page_icons/' . $homePage->reasons_section_driver_image) }}"
                                alt="">
                        </div>
                        <div class="w-full py-10 px-4 border border-gray-100 shadow rounded-md h-full">
                            <div class="text-center">
                                <h3 class="text-center mt-1">
                                    @isset($homePage->reasons_section_driver_label)
                                        {{ $homePage->reasons_section_driver_label }}
                                    @endisset
                                </h3>
                                <p class="text-justify mt-2 lg:text-lg md:text-base text-base">
                                    @isset($homePage->reasons_section_driver_description)
                                        {{ $homePage->reasons_section_driver_description }}
                                    @endisset
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative h-full">
                    <div class=" h-full">
                        <div
                            class="absolute -top-5 left-0 right-0 mx-auto bg-white rounded-md p-1 border h-14 w-14 shadow flex justify-center items-center">
                            <img class=""
                                src="{{ asset('home_page_icons/' . $homePage->reasons_section_quality_image) }}"
                                alt="">
                        </div>
                        <div class="w-full py-10 px-4 border border-gray-100 shadow rounded-md h-full">
                            <div class="text-center">
                                <h3 class="text-center mt-1">
                                    @isset($homePage->reasons_section_quality_label)
                                        {{ $homePage->reasons_section_quality_label }}
                                    @endisset
                                </h3>
                                <p class="text-justify mt-2 lg:text-lg md:text-base text-base">
                                    @isset($homePage->reasons_section_quality_description)
                                        {{ $homePage->reasons_section_quality_description }}
                                    @endisset
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative h-full">
                    <div class=" h-full">
                        <div
                            class="absolute -top-5 left-0 right-0 mx-auto bg-white rounded-md p-1 border h-14 w-14 shadow flex justify-center items-center">
                            <img class=""
                                src="{{ asset('home_page_icons/' . $homePage->reasons_section_policy_image) }}"
                                alt="">
                        </div>
                        <div class="w-full py-10 px-4 border border-gray-100 shadow rounded-md h-full">
                            <div class="text-center">
                                <h3 class="text-center mt-1">
                                    @isset($homePage->reasons_section_policy_label)
                                        {{ $homePage->reasons_section_policy_label }}
                                    @endisset
                                </h3>
                                <p class="text-justify mt-2 lg:text-lg md:text-base text-base">
                                    @isset($homePage->reasons_section_policy_description)
                                        {{ $homePage->reasons_section_policy_description }}
                                    @endisset
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative h-full">
                    <div class="h-full">
                        <div
                            class="absolute -top-5 left-0 right-0 mx-auto bg-white rounded-md p-1 border h-14 w-14 shadow flex justify-center items-center">
                            <img class=""
                                src="{{ asset('home_page_icons/' . $homePage->reasons_section_students_image) }}"
                                alt="">
                        </div>
                        <div class="w-full py-10 px-4 border border-gray-100 shadow rounded-md h-full">
                            <div class="text-center">
                                <h3 class="text-center mt-1">
                                    @isset($homePage->reasons_section_students_label)
                                        {{ $homePage->reasons_section_students_label }}
                                    @endisset
                                </h3>
                                <p class="text-justify mt-2 lg:text-lg md:text-base text-base">
                                    @isset($homePage->reasons_section_students_description)
                                        {{ $homePage->reasons_section_students_description }}
                                    @endisset
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative h-full">
                    <div class="h-full">
                        <div
                            class="absolute -top-5 left-0 right-0 mx-auto bg-white rounded-md p-1 border h-14 w-14 shadow flex justify-center items-center">
                            <img class=""
                                src="{{ asset('home_page_icons/' . $homePage->reasons_section_safety_image) }}"
                                alt="">
                        </div>
                        <div class="w-full py-10 px-4 border border-gray-100 shadow rounded-md h-full">
                            <div class="text-center">
                                <h3 class="text-center mt-1">
                                    @isset($homePage->reasons_section_safety_label)
                                        {{ $homePage->reasons_section_safety_label }}
                                    @endisset
                                </h3>
                                <p class="text-justify mt-2 lg:text-lg md:text-base text-base">
                                    @isset($homePage->reasons_section_safety_description)
                                        {{ $homePage->reasons_section_safety_description }}
                                    @endisset
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative h-full">
                    <div class="h-full">
                        <div
                            class="absolute -top-5 left-0 right-0 mx-auto bg-white rounded-md p-1 border h-14 w-14 shadow flex justify-center items-center">
                            <img class=""
                                src="{{ asset('home_page_icons/' . $homePage->reasons_section_price_image) }}"
                                alt="">
                        </div>
                        <div class="w-full py-10 px-4 border border-gray-100 shadow rounded-md h-full">
                            <div class="text-center">
                                <h3 class="text-center mt-1">
                                    @isset($homePage->reasons_section_price_label)
                                        {{ $homePage->reasons_section_price_label }}
                                    @endisset
                                </h3>
                                <p class="text-justify mt-2 lg:text-lg md:text-base text-base">
                                    @isset($homePage->reasons_section_price_description)
                                        {{ $homePage->reasons_section_price_description }}
                                    @endisset
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative h-full">
                    <div class=" h-full">
                        <div
                            class="absolute -top-5 left-0 right-0 mx-auto bg-white rounded-md p-1 border h-14 w-14 shadow flex justify-center items-center">
                            <img class=""
                                src="{{ asset('home_page_icons/' . $homePage->reasons_section_use_image) }}"
                                alt="">
                        </div>
                        <div class="w-full py-10 px-4 border border-gray-100 shadow rounded-md h-full">
                            <div class="text-center">
                                <h3 class="text-center mt-1">
                                    @isset($homePage->reasons_section_use_label)
                                        {{ $homePage->reasons_section_use_label }}
                                    @endisset
                                </h3>
                                <p class="text-justify mt-2 lg:text-lg md:text-base text-base">
                                    @isset($homePage->reasons_section_use_description)
                                        {{ $homePage->reasons_section_use_description }}
                                    @endisset
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative h-full">
                    <div class=" h-full">
                        <div
                            class="absolute -top-5 left-0 right-0 mx-auto bg-white rounded-md p-1 border h-14 w-14 shadow flex justify-center items-center">
                            <img class=""
                                src="{{ asset('home_page_icons/' . $homePage->reasons_section_reliable_image) }}"
                                alt="">
                        </div>
                        <div class="w-full py-10 px-4 border border-gray-100 shadow rounded-md h-full">
                            <div class="text-center">
                                <h3 class="text-center mt-1">
                                    @isset($homePage->reasons_section_reliable_label)
                                        {{ $homePage->reasons_section_reliable_label }}
                                    @endisset
                                </h3>
                                <p class="text-justify mt-2 lg:text-lg md:text-base text-base">
                                    @isset($homePage->reasons_section_reliable_description)
                                        {{ $homePage->reasons_section_reliable_description }}
                                    @endisset
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative h-full">
                    <div class=" h-full">
                        <div
                            class="absolute -top-5 left-0 right-0 mx-auto bg-white rounded-md p-1 border h-14 w-14 shadow flex justify-center items-center">
                            <img class=""
                                src="{{ asset('home_page_icons/' . $homePage->reasons_section_responsible_image) }}"
                                alt="">
                        </div>
                        <div class="w-full py-10 px-4 border border-gray-100 shadow rounded-md h-full">
                            <div class="text-center">
                                <h3 class="text-center mt-1">
                                    @isset($homePage->reasons_section_responsible_label)
                                        {{ $homePage->reasons_section_responsible_label }}
                                    @endisset
                                </h3>
                                <p class="text-justify mt-2 lg:text-lg md:text-base text-base">
                                    @isset($homePage->reasons_section_responsible_description)
                                        {{ $homePage->reasons_section_responsible_description }}
                                    @endisset
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-14 px-4 md:px-8 xl:px-0 bg-blue-600 w-full">
        <div class="container mx-auto grid lg:grid-cols-2 md:grid-cols-2 sm:grid-cols-1 gap-4 items-center">
            <div class="flex flex-col sm:flex-col md:flex-row lg:flex-row gap-4 items-center">
                <div class="rounded-full overflow-hidden w-24 h-24">
                    @isset($homePage->movement_section_icon)
                        <img class="w-full h-full object-contain"
                            src="{{ asset('home_page_icons/' . $homePage->movement_section_icon) }}" alt="">
                    @endisset
                </div>
                <h1 class="text-white text-center mb-0">
                    @isset($homePage->movement_section_heading)
                        {{ $homePage->movement_section_heading }}
                    @endisset
                </h1>
            </div>
            <div>
                <div class="text-justify lg:text-lg md:text-base text-base text-white ridesharing_movement_section">
                    @isset($homePage->movement_section_text)
                        {!! $homePage->movement_section_text !!}
                    @endisset
                </div>
            </div>
        </div>
    </section>

    <section class="py-14 px-4 md:px-8 xl:px-0">
        <div class="container mx-auto">
            <h1 class="text-blue-600 text-center mb-0">
                @isset($homePage->members_section_heading)
                    {{ $homePage->members_section_heading }}
                @endisset
            </h1>
            <p class="lg:text-lg md:text-base text-base text-center">
                @isset($homePage->members_section_text)
                    {{ $homePage->members_section_text }}
                @endisset
            </p>
            <div class="grid lg:grid-cols-3 md:grid-cols-2 sm:grid-cols-1 gap-4 xl:gap-6 mt-4 md:mt-6 xl:mt-10">
                @php
                    $maxWords = 20;
                @endphp
                @foreach ($reviews as $review)
                    @if ($review->from)
                        <div class="flex flex-col max-w-sm mx-4 my-6 shadow-lg">
                            <div class="px-4 py-12 h-full rounded-t-lg border sm:px-8">
                                <p class="relative py-1 text-lg italic text-center w-full">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor"
                                        class="w-8 h-8 dark:text-violet-400">
                                        <path d="M232,246.857V16H16V416H54.4ZM48,48H200V233.143L48,377.905Z"></path>
                                        <path d="M280,416h38.4L496,246.857V16H280ZM312,48H464V233.143L312,377.905Z"></path>
                                    </svg>
                                </p>
                                @php
                                    $words = explode(' ', $review->review);
                                    $shortText = implode(' ', array_slice($words, 0, $maxWords));
                                    $isLong = count($words) > $maxWords;
                                @endphp
                                <div class="px-4">
                                    <p>
                                        <span id="short-review-{{ $review->id }}" style="display: inline;">
                                            {{ $shortText }}@if ($isLong)
                                                ...
                                            @endif
                                        </span>

                                        <span id="full-review-{{ $review->id }}" style="display: none;">
                                            {{ $review->review }}
                                        </span>

                                        @if ($isLong)
                                            <button class="text-blue-500 ml-2"
                                                onclick="toggleReview({{ $review->id }})">
                                                <span id="toggle-btn-{{ $review->id }}">Read More</span>
                                            </button>
                                        @endif
                                    </p>
                                </div>
                                <div class="flex justify-end mt-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor"
                                        class="w-8 h-8 dark:text-violet-400">
                                        <path d="M280,185.143V416H496V16H457.6ZM464,384H312V198.857L464,54.1Z"></path>
                                        <path d="M232,16H193.6L16,185.143V416H232ZM200,384H48V198.857L200,54.1Z"></path>
                                    </svg>
                                </div>
                                <p>

                                </p>
                            </div>
                            <div class="flex flex-col items-center justify-center p-8 rounded-b-lg bg-primary text-white">
                                @if ($review->from->profile_image)
                                    <img class="w-16 h-16 mb-2 -mt-16 bg-center bg-cover rounded-full dark:bg-gray-500 bg-center"
                                        src="{{ $review->from->profile_image }}" alt="">
                                @else
                                    <img class="w-16 h-16 mb-2 -mt-16 bg-center bg-cover rounded-full dark:bg-gray-500 bg-center"
                                        src="{{ asset('assets/male.png') }}" alt="">
                                @endif
                                <p class="text-xl text-white">{{ $review->from->first_name }}
                                    {{ $review->from->last_name }}</p>
                                <!-- <p class="text-sm capitalize">Seoul, South korea</p> -->
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </section>
    <section class="py-14 px-4 md:px-8 xl:px-0 bg-gray-50">
        <div class="px-8">
            <h1 class="text-blue-600 text-center">
                @isset($homePage->news_section_heading)
                    {{ $homePage->news_section_heading }}
                @endisset
            </h1>

            <div class="flex flex-wrap justify-center items-center gap-4 md:gap-8 mt-4 md:mt-6 xl:mt-10">
                <div>
                    <a href="">
                        <div class="h-28 w-28 flex justify-center items-center">
                            @isset($homePage->news_section_icon1)
                                <img class="w-full h-full object-contain"
                                    src="{{ asset('home_page_icons/' . $homePage->news_section_icon1) }}" alt="">
                            @endisset
                        </div>
                    </a>
                </div>
                <div>
                    <a href="">
                        <div class="h-28 w-28 flex justify-center items-center">
                            @isset($homePage->news_section_icon2)
                                <img class="w-full h-full object-contain"
                                    src="{{ asset('home_page_icons/' . $homePage->news_section_icon2) }}" alt="">
                            @endisset
                        </div>
                    </a>
                </div>
                <div>
                    <a href="">
                        <div class="h-28 w-28 flex justify-center items-center">
                            @isset($homePage->news_section_icon3)
                                <img class="w-full h-full object-contain"
                                    src="{{ asset('home_page_icons/' . $homePage->news_section_icon3) }}" alt="">
                            @endisset
                        </div>
                    </a>
                </div>
                <div>
                    <a href="">
                        <div class="h-28 w-28 flex justify-center items-center">
                            @isset($homePage->news_section_icon4)
                                <img class="w-full h-full object-contain"
                                    src="{{ asset('home_page_icons/' . $homePage->news_section_icon4) }}" alt="">
                            @endisset
                        </div>
                    </a>
                </div>
            </div>
            <!-- <div class="flex flex-wrap justify-center items-center gap-4 md:gap-8 mt-4 md:mt-6 xl:mt-10">
                    @foreach ($articles as $article)
    <div class="rounded bg-white shadow-3xl p-5">
                            <div class="p-4">
                                <div>
                                    <p class="text-2xl font-FuturaMdCnBT">{{ $article->articleDetail[0]->title }}</p>
                                    <p class="lg:text-sm md:text-base">Agency: {{ $article->agency }}</p>
                                    <p class="lg:text-sm md:text-base">Added by: {{ $article->added_by }}</p>
                                </div>
                                <div class="flex justify-center items-center mt-3">
                                    <a href="{{ route('news_detail', ['lang' => optional($selectedLanguage)->abbreviation, 'id' => $article->id]) }}"
                                        class="bg-primary text-white py-2 px-3 rounded">
                                        Read article
                                    </a>
                                </div>
                            </div>
                        </div>
    @endforeach
                </div> -->
        </div>
    </section>

    <section class="py-14 px-4 md:px-8 xl:px-0 bg-blue-600">
        <div class="container mx-auto">
            <h1 class="text-white text-center mb-0">
                @isset($homePage->use_section_heading)
                    {{ $homePage->use_section_heading }}
                @endisset
            </h1>
            <p class="lg:text-lg md:text-base text-base text-center text-white">
                @isset($homePage->use_section_text)
                    {{ $homePage->use_section_text }}
                @endisset
            </p>
            <div class="grid lg:grid-cols-2 md:grid-cols-2 sm:grid-cols-1 gap-4 xl:gap-6 mt-4 md:mt-6 xl:mt-10">

                <div class="bg-white rounded shadow p-3 flex items-start border gap-4">
                    <div>
                        <div class="h-14 w-14 rounded-full mt-0.5">
                            @isset($homePage->use_section_point1_image)
                                <img class="w-full h-full object-contain"
                                    src="{{ asset('home_page_icons/' . $homePage->use_section_point1_image) }}"
                                    alt="">
                            @endisset
                        </div>
                    </div>

                    <div>
                        <p class="font-FuturaMdCnBT lg:text-[22px] text-xl">
                            @isset($homePage->use_section_point1_label)
                                {{ $homePage->use_section_point1_label }}
                            @endisset
                        </p>
                        <p class="text-justify lg:text-lg md:text-base text-base">
                            @isset($homePage->use_section_point1_description)
                                {{ $homePage->use_section_point1_description }}
                            @endisset
                        </p>
                    </div>
                </div>

                <div class="bg-white rounded shadow p-3 flex items-start border gap-4">
                    <div>
                        <div class="h-14 w-14 rounded-full mt-0.5">
                            @isset($homePage->use_section_point2_image)
                                <img class="w-full h-full object-contain"
                                    src="{{ asset('home_page_icons/' . $homePage->use_section_point2_image) }}"
                                    alt="">
                            @endisset
                        </div>
                    </div>

                    <div>
                        <p class="font-FuturaMdCnBT lg:text-[22px] text-xl">
                            @isset($homePage->use_section_point2_label)
                                {{ $homePage->use_section_point2_label }}
                            @endisset
                        </p>
                        <p class="text-justify lg:text-lg md:text-base text-base">
                            @isset($homePage->use_section_point2_description)
                                {{ $homePage->use_section_point2_description }}
                            @endisset
                        </p>
                    </div>
                </div>

                <div class="bg-white rounded shadow p-3 flex items-start border gap-4">
                    <div>
                        <div class="h-14 w-14 rounded-full mt-0.5">
                            @isset($homePage->use_section_point3_image)
                                <img class="w-full h-full object-contain"
                                    src="{{ asset('home_page_icons/' . $homePage->use_section_point3_image) }}"
                                    alt="">
                            @endisset
                        </div>
                    </div>

                    <div>
                        <p class="font-FuturaMdCnBT lg:text-[22px] text-xl">
                            @isset($homePage->use_section_point3_label)
                                {{ $homePage->use_section_point3_label }}
                            @endisset
                        </p>
                        <p class="text-justify lg:text-lg md:text-base text-base">
                            @isset($homePage->use_section_point3_description)
                                {{ $homePage->use_section_point3_description }}
                            @endisset
                        </p>
                    </div>
                </div>

                <div class="bg-white rounded shadow p-3 flex items-start border gap-4">
                    <div>
                        <div class="h-14 w-14 rounded-full mt-0.5">
                            @isset($homePage->use_section_point4_image)
                                <img class="w-full h-full object-contain"
                                    src="{{ asset('home_page_icons/' . $homePage->use_section_point4_image) }}"
                                    alt="">
                            @endisset
                        </div>
                    </div>

                    <div>
                        <p class="font-FuturaMdCnBT lg:text-[22px] text-xl">
                            @isset($homePage->use_section_point4_label)
                                {{ $homePage->use_section_point4_label }}
                            @endisset
                        </p>
                        <p class="text-justify lg:text-lg md:text-base text-base">
                            @isset($homePage->use_section_point4_description)
                                {{ $homePage->use_section_point4_description }}
                            @endisset
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-14 px-4 md:px-8 xl:px-0 bg-white">
        <div class="container mx-auto">
            <h1 class="text-blue-600 text-center mb-0">
                @isset($homePage->reliability_section_heading)
                    {{ $homePage->reliability_section_heading }}
                @endisset
            </h1>
            <p class="lg:text-lg md:text-base text-base text-center">
                @isset($homePage->reliability_section_text)
                    {{ $homePage->reliability_section_text }}
                @endisset
            </p>

            <div class="mt-4 md:mt-6 xl:mt-10 grid lg:grid-cols-2 md:grid-cols-2 sm:grid-cols-1 gap-6">
                <div class="rounded-md border shadow">
                    <div class="bg-blue-600 p-3 shadow border border-blue-600 rounded-t-md flex-1">
                        <h3 class="text-white text-center">
                            @isset($homePage->reliability_section_passengers_label)
                                {{ $homePage->reliability_section_passengers_label }}
                            @endisset
                        </h3>
                    </div>
                    <div class="p-4 flex-auto ridesharing_reliability_section">
                        @isset($homePage->reliability_section_passengers_description)
                            {!! str_replace(
                                '<li>',
                                '<li class="toggle-item">',
                                str_replace('<br />', '', nl2br($homePage->reliability_section_passengers_description)),
                            ) !!}
                        @endisset
                    </div>
                </div>

                <div class="rounded-md border shadow">
                    <div class="bg-blue-600 p-3 shadow border border-blue-600 rounded-t-md">
                        <h3 class="text-white text-center">
                            @isset($homePage->reliability_section_drivers_label)
                                {{ $homePage->reliability_section_drivers_label }}
                            @endisset
                        </h3>
                    </div>
                    <div class="p-4 ridesharing_reliability_section">
                        @isset($homePage->reliability_section_drivers_description)
                            {!! str_replace(
                                '<li>',
                                '<li class="toggle-item-driver">',
                                str_replace('<br />', '', nl2br($homePage->reliability_section_drivers_description)),
                            ) !!}
                        @endisset
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="pb-14 px-4 md:px-8 xl:px-0 bg-white">
        <div class="container mx-auto">
            <h1 class="text-blue-600 text-center mb-0">
                @isset($homePage->payment_section_heading)
                    {{ $homePage->payment_section_heading }}
                @endisset
            </h1>
            <p class="lg:text-lg md:text-base text-base text-center">
                @isset($homePage->payment_section_text)
                    {{ $homePage->payment_section_text }}
                @endisset
            </p>

            <div
                class="flex flex-col sm:flex-col md:flex-row lg:flex-row flex-wrap justify-center items-center gap-4 md:gap-6 mt-4 md:mt-6 xl:mt-10">
                <div class="flex justify-center items-center gap-4 md:gap-6">
                    <div class="flex justify-center items-center h-20">
                        @isset($homePage->payment_section_icon1)
                            <img class="h-full w-full object-contain"
                                src="{{ asset('home_page_icons/' . $homePage->payment_section_icon1) }}" alt="">
                        @endisset
                    </div>
                    <div class="flex justify-center items-center h-14">
                        @isset($homePage->payment_section_icon2)
                            <img class="h-full w-full object-contain"
                                src="{{ asset('home_page_icons/' . $homePage->payment_section_icon2) }}" alt="">
                        @endisset
                    </div>
                </div>
                <div class="flex justify-center items-center gap-4 md:gap-6">
                    <div class="flex justify-center items-center h-14 w-28">
                        @isset($homePage->payment_section_icon3)
                            <img class="h-full w-full object-contain"
                                src="{{ asset('home_page_icons/' . $homePage->payment_section_icon3) }}" alt="">
                        @endisset
                    </div>
                    <div class="flex justify-center items-center h-14">
                        @isset($homePage->payment_section_icon4)
                            <img class="h-full w-full object-contain"
                                src="{{ asset('home_page_icons/' . $homePage->payment_section_icon4) }}" alt="">
                        @endisset
                    </div>
                    <div class="flex justify-center items-center h-10 md:h-14">
                        @isset($homePage->payment_section_icon5)
                            <img class="h-full w-full object-contain"
                                src="{{ asset('home_page_icons/' . $homePage->payment_section_icon5) }}" alt="">
                        @endisset
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    @php
        $flatpickrLocale = match (app()->getLocale()) {
            'ar' => 'ar',
            'es' => 'es',
            'fr' => 'fr',
            'hi' => 'hi',
            'ru' => 'ru',
            'uk' => 'uk',
            'zh' => 'zh',
            default => null,
        };
    @endphp
    @if ($flatpickrLocale)
        <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/{{ $flatpickrLocale }}.js"></script>
    @endif

    <script>
        const dateInput = document.getElementById('departure_date');

        const profileLocale = @json(app()->getLocale());
        const flatpickrLocaleKey = @json($flatpickrLocale);
        let departureDatePicker = null;
        const departureDateClearButton = document.getElementById('departure-date-clear-button');
        
        const syncDepartureDateClearButton = () => {
            if (!departureDateClearButton || !dateInput) {
                return;
            }
            const hasDateValue = dateInput.value.trim() !== '';
            departureDateClearButton.classList.toggle('hidden', !hasDateValue);
        };

        const clearDepartureDate = () => {
            if (departureDatePicker) {
                departureDatePicker.clear();
            } else if (dateInput) {
                dateInput.value = '';
            }
            syncDepartureDateClearButton();
        };

        const flatpickrOptions = {
            dateFormat: 'F d, Y', // Display format (e.g., "January 15, 2024")
            altInput: true,
            altFormat: 'F d, Y',
            minDate: 'today', // Restrict to future dates only
            disableMobile: true, // Disable mobile-friendly mode for consistent experience
            allowInput: true, // Allow manual input
            clickOpens: true, // Open calendar on click
            theme: 'default', // Use default theme
            onChange: function(selectedDates, dateStr, instance) {
                syncDepartureDateClearButton();
            }
        };

        if (
            flatpickrLocaleKey &&
            window.flatpickr &&
            window.flatpickr.l10ns &&
            window.flatpickr.l10ns[flatpickrLocaleKey]
        ) {
            flatpickrOptions.locale = window.flatpickr.l10ns[flatpickrLocaleKey];
        }

        if (dateInput) {
            departureDatePicker = flatpickr(dateInput, flatpickrOptions);
        }

        // Form validation function
        const validateSearchForm = () => {
            const originInput = document.querySelector('input[name="origin[label]"]:not([type="hidden"])');
            const destinationInput = document.querySelector('input[name="destination[label]"]:not([type="hidden"])');
            const originComponent = originInput?.closest('[wire\\:id]');
            const destinationComponent = destinationInput?.closest('[wire\\:id]');
            
            let originCityId = null;
            let destinationCityId = null;
            let originLabel = '';
            let destinationLabel = '';

            // Get values from hidden inputs
            if (originComponent) {
                const originCityIdInput = originComponent.querySelector('input[name="origin[city_id]"]');
                originCityId = originCityIdInput?.value?.trim() || null;
                originLabel = originInput?.value?.trim() || '';
            }

            if (destinationComponent) {
                const destinationCityIdInput = destinationComponent.querySelector('input[name="destination[city_id]"]');
                destinationCityId = destinationCityIdInput?.value?.trim() || null;
                destinationLabel = destinationInput?.value?.trim() || '';
            }

            // Also check Livewire component state if available
            if (window.Livewire && originComponent && destinationComponent) {
                const originWireId = originComponent.getAttribute('wire:id');
                const destinationWireId = destinationComponent.getAttribute('wire:id');

                if (originWireId && destinationWireId) {
                    try {
                        const originLivewire = window.Livewire.find(originWireId);
                        const destinationLivewire = window.Livewire.find(destinationWireId);

                        if (originLivewire) {
                            const livewireCityId = originLivewire.get('cityId');
                            if (livewireCityId) {
                                originCityId = String(livewireCityId);
                            }
                            const livewireQuery = originLivewire.get('query')?.trim() || '';
                            if (livewireQuery && !originLabel) {
                                originLabel = livewireQuery;
                            }
                        }

                        if (destinationLivewire) {
                            const livewireCityId = destinationLivewire.get('cityId');
                            if (livewireCityId) {
                                destinationCityId = String(livewireCityId);
                            }
                            const livewireQuery = destinationLivewire.get('query')?.trim() || '';
                            if (livewireQuery && !destinationLabel) {
                                destinationLabel = livewireQuery;
                            }
                        }
                    } catch (e) {
                        console.warn('Error accessing Livewire components:', e);
                    }
                }
            }

            // Validate: both origin and destination must have valid city IDs
            const isValid = originCityId && destinationCityId && originLabel && destinationLabel;

            return {
                isValid,
                originCityId,
                destinationCityId,
                originLabel,
                destinationLabel
            };
        };

        // Initialize button visibility and event listeners
        document.addEventListener('DOMContentLoaded', function() {
            syncDepartureDateClearButton();

            // Handle departure date clear button
            if (departureDateClearButton) {
                departureDateClearButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    clearDepartureDate();
                });
            }

            // Form submission validation
            const homeSearchForm = document.getElementById('home-search-form');
            if (homeSearchForm) {
                homeSearchForm.addEventListener('submit', function(e) {
                    const validation = validateSearchForm();
                    
                    if (!validation.isValid) {
                        e.preventDefault();
                        e.stopPropagation();
                        
                        const originInput = document.querySelector('input[name="origin[label]"]:not([type="hidden"])');
                        const destinationInput = document.querySelector('input[name="destination[label]"]:not([type="hidden"])');
                        const originComponent = originInput?.closest('[wire\\:id]');
                        const destinationComponent = destinationInput?.closest('[wire\\:id]');
                        
                        const invalidCityMessage = @json(__('validation.custom.city_not_in_record.message'));
                        const originRequiredMessage = @json(__('validation.custom.origin.message'));
                        const destinationRequiredMessage = @json(__('validation.custom.destination.message'));
                        
                        
                        // Set error messages on Livewire components
                        if (window.Livewire && originComponent && destinationComponent) {
                            const originWireId = originComponent.getAttribute('wire:id');
                            const destinationWireId = destinationComponent.getAttribute('wire:id');
                            
                            if (originWireId && destinationWireId) {
                                try {
                                    const originLivewire = window.Livewire.find(originWireId);
                                    const destinationLivewire = window.Livewire.find(destinationWireId);
                                    
                                    if (originLivewire) {
                                        if (!validation.originCityId || !validation.originLabel) {
                                            originLivewire.set('errorMessage', validation.originLabel ? invalidCityMessage : originRequiredMessage);
                                        } else {
                                            originLivewire.set('errorMessage', null);
                                        }
                                    }
                                    
                                    if (destinationLivewire) {
                                        if (!validation.destinationCityId || !validation.destinationLabel) {
                                            destinationLivewire.set('errorMessage', validation.destinationLabel ? invalidCityMessage : destinationRequiredMessage);
                                        } else {
                                            destinationLivewire.set('errorMessage', null);
                                        }
                                    }
                                } catch (err) {
                                    console.warn('Error setting Livewire error messages:', err);
                                }
                            }
                        }
                        
                        return false;
                    } else {
                        // Clear error messages if validation passes
                        if (window.Livewire && originComponent && destinationComponent) {
                            const originWireId = originComponent.getAttribute('wire:id');
                            const destinationWireId = destinationComponent.getAttribute('wire:id');
                            
                            if (originWireId && destinationWireId) {
                                try {
                                    const originLivewire = window.Livewire.find(originWireId);
                                    const destinationLivewire = window.Livewire.find(destinationWireId);
                                    
                                    if (originLivewire) {
                                        originLivewire.set('errorMessage', null);
                                    }
                                    if (destinationLivewire) {
                                        destinationLivewire.set('errorMessage', null);
                                    }
                                } catch (err) {
                                    // Ignore errors when clearing
                                }
                            }
                        }
                    }
                });
            }
        });

        const mobileCloseRedirectUrl = "{{ route('mobile_close_redirect') }}";

        function isMobileClient() {
            return /android|iphone|ipad|ipod|iemobile|blackberry|kindle|silk|opera mini/i.test((navigator.userAgent ||
                navigator.vendor || window.opera || '').toLowerCase());
        }

        function handleSuccessModalClose(event) {
            event.preventDefault();
            if (isMobileClient()) {
                window.location.href = mobileCloseRedirectUrl;
                return;
            }
            closeModal('success-modal1');
        }


        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.style.display = 'none';
            }
        }

        function toggleReview(id) {
            const shortReview = document.getElementById(`short-review-${id}`);
            const fullReview = document.getElementById(`full-review-${id}`);
            const toggleBtn = document.getElementById(`toggle-btn-${id}`);

            const isExpanded = fullReview.style.display === 'inline';

            fullReview.style.display = isExpanded ? 'none' : 'inline';
            shortReview.style.display = isExpanded ? 'inline' : 'none';
            toggleBtn.textContent = isExpanded ? 'Read More' : 'Read Less';
        }


        document.addEventListener('DOMContentLoaded', function() {});

        $(document).ready(function() {
            var itemsToShow = 2; // Number of items to show initially
            var $listItems = $(".toggle-item");
            var $showMoreBtn = $(".show-more-btn");
            var $showLessBtn = $(".show-less-btn");
            var $listItemsDriver = $(".toggle-item-driver");
            var $showMoreDriverBtn = $(".show-more-driver-btn");
            var $showLessDriverBtn = $(".show-less-driver-btn");

            // Hide excess items initially
            $listItems.slice(itemsToShow).hide();
            $listItemsDriver.slice(itemsToShow).hide();

            // Initially, show only the "Show More" button
            $showMoreBtn.show();
            $showLessBtn.hide();
            $showMoreDriverBtn.show();
            $showLessDriverBtn.hide();

            // Show more items when "Show More" button is clicked
            $showMoreBtn.on("click", function(e) {
                e.preventDefault();
                $listItems.show();
                $showMoreBtn.hide();
                $showLessBtn.show();
            });
            $showMoreDriverBtn.on("click", function(e) {
                e.preventDefault();
                $listItemsDriver.show();
                $showMoreDriverBtn.hide();
                $showLessDriverBtn.show();
            });

            // Show less items when "Show Less" button is clicked
            $showLessBtn.on("click", function(e) {
                e.preventDefault();
                $listItems.slice(itemsToShow).hide();
                $showLessBtn.hide();
                $showMoreBtn.show();
            });
            $showLessDriverBtn.on("click", function(e) {
                e.preventDefault();
                $listItemsDriver.slice(itemsToShow).hide();
                $showLessDriverBtn.hide();
                $showMoreDriverBtn.show();
            });
        });



        function closeModal() {
            // Remove the modal from the DOM
            const modal = document.querySelector('[aria-modal="true"]');
            if (modal) {
                modal.remove();
            }

            // Alternatively, if you want to hide it instead of removing:
            // modal.style.display = 'none';
        }

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
