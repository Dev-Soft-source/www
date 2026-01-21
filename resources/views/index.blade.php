@extends('layouts.template')

@section('style')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"
  />
@endsection

@section('content')
<div class="md:h-96 w-full bg-cover relative z-10" style="background-image:url('home_page_icons/{{ $homePage->slider_image }}');">

    @if(session('message'))
    <div class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                <div
                    class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg w-full modal-border">
                    <button onclick="closeModal('message-modal')" class="absolute top-2 right-2 p-1 rounded-full hover:bg-gray-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
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
                            class="inline-flex w-full justify-center rounded bg-red-500 px-3 py-2 font-FuturaMdCnBT text-lg text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-red-400 sm:ml-3 sm:w-24">Close</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    <!-- <div class="pr-111112"> -->
    @if(session('success'))
    <div id="my-modal" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                    <div class="relative flex min-h-full items-center justify-center p-4  sm:items-center sm:p-0 w-full">
                      <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeModal()"></div>
                          <div class="relative animate__animated animate__fadeIn z-20 transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
                            <button onclick="closeModal('success-modal')" class="absolute top-2 right-2 p-1 rounded-full hover:bg-gray-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
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
                            class="inline-flex w-full justify-center rounded bg-greenXS px-3 py-2 font-FuturaMdCnBT text-lg text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-greenXS sm:ml-3 sm:w-24">Close</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if(session('success1'))
        <div id="my-modal" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="relative flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeModal()"></div>
                    <div class="relative animate__animated animate__fadeIn z-20 transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
                        <button onclick="closeModal('success-modal1')" class="absolute top-2 right-2 p-1 rounded-full hover:bg-gray-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                           <div class="text-center sm:ml-4 sm:mt-0">
                                <div class="w-full mt-8">
                                    <p class="can-exp-p text-center font-FuturaMdCnBT">{!! session('success1') !!}</p>
                                </div>
                            </div>
                        </div>
                        <div class="px-4 pb-6 pt-4 items-center space-x-2 sm:space-x-4 sm:px-6 justify-center hidden md:flex">
                            <a href="{{ route('step1to5', ['lang' => $selectedLanguage->abbreviation]) }}"
                                class="inline-flex w-full justify-center rounded bg-greenXS px-3 py-2 font-FuturaMdCnBT text-lg text-white whitespace-nowrap hover:text-white hover:shadow-lg shadow-sm hover:bg-greenXS sm:ml-3 sm:w-fit">{!! session('create_my_profile_btn') !!}</a>
                            <a href="#"
                                class="button-exp-fill" onclick="handleSuccessModalClose(event)">Close</a>
                        </div>
                        @if(auth()->check() && isset($token))
                        <div class="px-4 pb-6 pt-4 flex items-center space-x-2 sm:space-x-4 sm:px-6 justify-center md:hidden">
                            <a href="{{ route('login_with_app', ['lang' => $selectedLanguage->abbreviation, 'token' => $token]) }}"
                                class="inline-flex w-full justify-center rounded bg-greenXS px-3 py-2 font-FuturaMdCnBT text-lg text-white whitespace-nowrap hover:text-white hover:shadow-lg shadow-sm hover:bg-greenXS sm:ml-3 sm:w-fit">{!! session('continue_with_app_btn') !!}</a>
                            <a href="#"
                                class="button-exp-fill" onclick="handleSuccessModalClose(event)">Close</a>
                        </div>
                        @else
                        <div class="px-4 pb-6 pt-4 flex items-center space-x-2 sm:space-x-4 sm:px-6 justify-center md:hidden">
                            <a href="{{ route('login_with_app', ['lang' => $selectedLanguage->abbreviation]) }}"
                                class="inline-flex w-full justify-center rounded bg-greenXS px-3 py-2 font-FuturaMdCnBT text-lg text-white whitespace-nowrap hover:text-white hover:shadow-lg shadow-sm hover:bg-greenXS sm:ml-3 sm:w-fit">{!! session('continue_with_app_btn') !!}</a>
                            <a href="#"
                                class="button-exp-fill" onclick="handleSuccessModalClose(event)">Close</a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="bg-black bg-opacity-50 h-full relative top-0 z-30">
        <section class="pt-14 pb-14 flex flex-col justify-center items-center h-full space-y-8" >
            <div>
                <div class="text-white text-lg font-FuturaMdCnBT px-4 py-2 bg-blue-600 rounded">
                    @isset($homePage->slider_heading)
                        {{ $homePage->slider_heading }}
                    @endisset
                </div>
            </div>
            <div class="flex flex-col sm:flex-col md:flex-row lg:flex-row gap-4 px-4 md:px-8 xl:px-0">
                <div class="flex flex-col sm:flex-col md:flex-row lg:flex-row md:items-center gap-2 relative">
                    <div>
                        <div class="relative">
                            <div class="bg-gray-100 absolute top-0 rounded-l w-8 flex justify-center items-center h-full">
                                <div class="w-6 h-6">
                                    @isset($homePage->from_field_icon)
                                        <img class="w-full h-full object-contain" src="{{asset('home_page_icons/' . $homePage->from_field_icon)}}" alt="">
                                    @endisset
                                </div>
                            </div>
                            <input id="fromInput" type="text"
                                @isset($homePage->slider_from_placeholder)
                                    placeholder="{{ $homePage->slider_from_placeholder }}"
                                @endisset
                                class="bg-white pl-10 bg-opacity-60 text-lg font-medium w-full rounded text-black p-1.5 placeholder:text-black outline-none ring-2 ring-blue-500 focus:ring-2 focus:ring-blue-500 caret-gray-800 border-0">
                            <!-- Suggestions Container for 'from' field -->
                            <div id="fromInput-suggestions" class="absolute left-0 right-0 bg-white shadow-lg mt-1 max-h-60 overflow-y-auto z-50"></div>
                        </div>
                        <div id="fromError" class="hidden absolute z-10 top-16 bg-blue-600 text-white px-4 py-2 rounded-md shadow-md before:absolute before:left-1/2 before:-top-2 before:-translate-x-1/2 before:w-0 before:h-0 before:border-l-8 before:border-l-transparent before:border-r-8 before:border-r-transparent before:border-b-8 before:border-b-blue-600 mx-auto left-0 right-0 w-fit">
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-center items-center">
                            <button onclick="swapLocations()">
                            <div class="w-8 h-8">
                                @isset($homePage->swap_field_icon)
                                    <img class="w-full h-full object-contain" src="{{asset('home_page_icons/' . $homePage->swap_field_icon)}}" alt="">
                                @endisset
                            </div>
                            </button>
                        </div>
                    </div>
                    <div>
                        <div class="relative">
                            <div class="bg-gray-100 absolute top-0 rounded-l w-8 flex justify-center items-center h-full">
                            <div class="w-6 h-6">
                                @isset($homePage->to_field_icon)
                                    <img class="w-full h-full object-contain" src="{{asset('home_page_icons/' . $homePage->to_field_icon)}}" alt="">
                                @endisset
                            </div>
                            </div>
                            <input id="toInput" type="text"
                                @isset($homePage->slider_to_placeholder)
                                    placeholder="{{ $homePage->slider_to_placeholder }}"
                                @endisset
                                class="bg-white pl-10 bg-opacity-60 text-lg font-medium w-full rounded text-black p-1.5 placeholder:text-black outline-none ring-2 ring-blue-500 focus:ring-2 focus:ring-blue-500 caret-gray-800 border-0">
                            <!-- Suggestions Container for 'from' field -->
                            <div id="toInput-suggestions" class="absolute left-0 right-0 bg-white shadow-lg mt-1 max-h-60 overflow-y-auto z-50"></div>
                        </div>
                        <p id="toError" class="text-sm hidden text-white"></p>
                    </div>
                </div>
                <div class="w-44 mx-auto md:mx-0 md:w-auto flex flex-col sm:flex-col md:flex-row items-center gap-4">
                    <div class="relative w-full">
                        <div class="bg-gray-100 absolute top-0 rounded-l w-8 flex justify-center items-center h-full">
                            <div class="w-6 h-6">
                                @isset($homePage->date_field_icon)
                                    <img class="w-full h-full object-contain" src="{{asset('home_page_icons/' . $homePage->date_field_icon)}}" alt="">
                                @endisset
                            </div>
                        </div>
                        <input id="dateInput" type="text"
                            @isset($homePage->slider_date_placeholder)
                                placeholder="{{ $homePage->slider_date_placeholder }}"
                            @endisset
                            class="bg-white pl-10 bg-opacity-60 text-lg font-medium w-full rounded text-black p-1.5 placeholder:text-black outline-none ring-2 ring-blue-500 focus:ring-2 focus:ring-blue-500 caret-gray-800 border-0 cursor-pointer">
                    </div>
                    <div class="flex justify-center items-center">
                        <button onclick="navigateToSearchRoute()" class="bg-primary py-2 px-3 rounded button-exp-fill">
                            <span class="block md:hidden">Search</span>
                            <div class="w-auto h-6 hidden md:block">
                                @isset($homePage->search_field_icon)
                                    <img class="w-full h-full object-contain" src="{{asset('home_page_icons/' . $homePage->search_field_icon)}}" alt="">
                                @endisset
                            </div>
                        </button>
                    </div>
                </div>
            </div>
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
                                <img class="" src="{{asset('home_page_icons/' . $homePage->section1_pink_rides_image)}}" alt="">
                            </div>
                            <div>
                                <h3 class="mt-4 text-center">
                                    @isset($homePage->section1_pink_rides_label)
                                        {{ $homePage->section1_pink_rides_label }}
                                    @endisset
                                </h3>
                                <p class="text-justify mt-4 lg:text-lg md:text-base text-base">
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
                                <img class="" src="{{asset('home_page_icons/' . $homePage->section1_folks_rides_image)}}" alt="">
                            </div>
                            <div>
                                <h3 class="mt-4 text-center">
                                    @isset($homePage->section1_folks_rides_label)
                                        {{ $homePage->section1_folks_rides_label }}
                                    @endisset
                                </h3>
                                <p class="text-justify mt-4 lg:text-lg md:text-base text-base">
                                    @isset($homePage->section1_folks_rides_description)
                                        {!! $homePage->section1_folks_rides_description !!}
                                    @endisset
                                </p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <div class="relative">
                <div class="h-full">

                    <div class="w-full h-full py-8 px-4 border border-gray-100 shadow rounded-md">
                        <div class="h-20 w-20 mx-auto flex justify-center items-center">
                            <img class="" src="{{asset('home_page_icons/' . $homePage->section1_customize_image)}}" alt="">
                        </div>
                        <div>
                            <h3 class="mt-2 text-center">
                                @isset($homePage->section1_customize_label)
                                    {{ $homePage->section1_customize_label }}
                                @endisset
                            </h3>
                            <p class="text-justify mt-2 lg:text-lg md:text-base text-base">
                                @isset($homePage->section1_customize_description)
                                    {!! $homePage->section1_customize_description !!}
                                @endisset
                            </p>
                        </div>
                    </div>
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

                <div  class="pb-8">
                    <div class="h-16 w-16 flex justify-center items-center">
                        <img src="{{asset('home_page_icons/' . $homePage->section2_profile_verification_image)}}" alt="">
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
                        <img src="{{asset('home_page_icons/' . $homePage->section2_policies_image)}}" alt="">
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
                        <img src="{{asset('home_page_icons/' . $homePage->section2_car_insurance_image)}}" alt="">
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
                        <img src="{{asset('home_page_icons/' . $homePage->section2_help_image)}}" alt="">
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
                                <img class="" src="{{asset('home_page_icons/' . $homePage->section3_safe_image)}}" alt="">
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
                                <img class="" src="{{asset('home_page_icons/' . $homePage->section3_affordable_image)}}" alt="">
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
                                <img class="" src="{{asset('home_page_icons/' . $homePage->section3_reliable_image)}}" alt="">
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
            <div class="space-y-4">
                @if (!empty($rides))
                    @foreach ($rides->take(2) as $ride)

                        @php
                            // Check if defaultRideDetail has elements before accessing
                            $defaultRideDetail = $ride->defaultRideDetail->first();
                            $from = $defaultRideDetail ? $defaultRideDetail->departure : '';
                            $to = $defaultRideDetail ? $defaultRideDetail->destination : '';

                        @endphp
                        
                        @if($defaultRideDetail)
                            <div class="relative">
                                @if (auth()->user())
                                    @php
                                        $user_id = auth()->user()->id;

                                        // Assuming $ratings is a collection
                                        $filteredRatings = $ratings->where('status', 1)->where('type', '2')->filter(function ($rating) use ($user_id) {
                                            return $rating->booking->user_id === $user_id;
                                        });

                                        $totalAverage = $filteredRatings->avg('average_rating') ?? 0;
                                    @endphp
                                @endif
                                <a href="{{ route('ride_detail', ['lang' => $selectedLanguage->abbreviation, 'departure' => $from, 'destination' => $to, 'id' => $ride->id]) }}">
                                    <div class="bg-white rounded-lg shadow-3xl border-[3px] border-solid border-gray-100"
                                        id="ride-{{ $ride->id }}">
                                        <div class="flex flex-col md:flex-row items-start md:items-center justify-between pb-0 p-4">
                                            <p class="flex items-center space-x-2 font-semibold">
                                                {{ \Carbon\Carbon::parse($ride->date)->format('l, F d, Y') }}
                                                @isset($findRidePage->card_section_at_label)
                                                    {{ $findRidePage->card_section_at_label }}
                                                @endisset
                                                {{ \Carbon\Carbon::parse($ride->time)->format('h:i A') == '12:00 PM' ? '12 noon' : (\Carbon\Carbon::parse($ride->time)->format('h:i A') == '12:00 AM' ? '12 midnight' : \Carbon\Carbon::parse($ride->time)->format('h:i A')) }}
                                            </p>
                                            <div class="pr-8">
                                                <div class="pr-8">
                                                    <p class="font-medium">
                                                        Total {{ $ride->seats }} seats</p>
                                                </div>
                                                <p class="text-xl font-semibold text-primary">${{ number_format(floatval($defaultRideDetail ? $defaultRideDetail->price : 0), 2) }}
                                                    <small>
                                                        @isset($findRidePage->card_section_per_seat)
                                                            {{ $findRidePage->card_section_per_seat }}
                                                        @endisset
                                                    </small>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex justify-between px-4">
                                            <div class="md:w-2/3">
                                                <div class="relative mt-5 text-left">
                                                    <div class="flex items-center relative">
                                                        <div
                                                            class="border-r-2 border-black border-solid absolute h-full left-3 md:left-6 top-2 z-10">
                                                            <span
                                                                class="bg-primary rounded-full w-7 h-7 -top-[2px] -ml-[13px] absolute flex justify-center items-center">
                                                                <img class="w-5 h-5 object-contain" src="{{ asset('./images/new-21-search-bar-from.png')}}" alt="">
                                                            </span>
                                                        </div>
                                                        <div class="ml-12 md:ml-20">
                                                            <div class="font-bold text-black">From</div>
                                                            <div class="text-primary md:mb-4">{{ $ride->pickup }},
                                                                {{ $from }}</div>
                                                        </div>
                                                    </div>

                                                    <div class="flex items-center relative">
                                                        <div
                                                            class="border-r-2 border-black border-solid absolute h-0 left-3 md:left-5 top-2 z-10">
                                                            <span
                                                                class="bg-gray-200 rounded-full w-7 h-7 -top-[6px] -ml-[9px] absolute flex justify-center items-center">
                                                                <img class="w-5 h-5 object-contain" src="{{ asset('./images/new-21-search-bar-to.png')}}" alt="">
                                                            </span>
                                                        </div>
                                                        <div class="ml-12 md:ml-20">
                                                            <div class="font-bold text-black">To</div>
                                                            <div class="text-primary md:mb-4">{{ $ride->dropoff }},
                                                                {{ $to }}</div>
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
                                                <div class="flex-none w-12 h-12 bg-gray-100 border rounded-full">
                                                    <img class="w-full h-full object-cover rounded-full"
                                                        src="{{ $ride->car_image }}"
                                                        alt="">
                                                </div>
                                                <div class="flex items-center space-x-1">
                                                    @if ($ride->booking_method == ($postRidePage->booking_option1->features_setting_id ?? null))
                                                        <a href="javascript:void(0);" onclick="toggleModal1('modal-id2', '{{ $postRidePage->booking_option1_tooltip }}')"><img class="w-8 h-8"
                                                            src="{{asset('home_page_icons/' . $postRidePage->booking_option1->icon)}}"
                                                            alt=""></a>
                                                    @elseif ($ride->booking_method == ($postRidePage->booking_option2->features_setting_id ?? null))
                                                        <a href="javascript:void(0);" onclick="toggleModal1('modal-id2', '{{ $postRidePage->booking_option2_tooltip }}')"><img class="w-8 h-8"
                                                            src="{{asset('home_page_icons/' . $postRidePage->booking_option2->icon)}}"
                                                            alt=""></a>
                                                    @endif
                                                    @if ($ride->payment_method == ($findRidePage->payment_methods_option2->features_setting_id ?? null))
                                                        <a href="javascript:void(0);" onclick="toggleModal1('modal-id2', '{{ $postRidePage->payment_methods_option1_tooltip }}')"><img class="w-8 h-8" src="{{asset('home_page_icons/' . $findRidePage->payment_methods_option2->icon)}}" alt=""></a>
                                                    @elseif ($ride->payment_method == ($findRidePage->payment_methods_option3->features_setting_id ?? null))
                                                        <a href="javascript:void(0);" onclick="toggleModal1('modal-id2', '{{ $postRidePage->payment_methods_option2_tooltip }}')"><img class="w-8 h-8" src="{{asset('home_page_icons/' . $findRidePage->payment_methods_option3->icon)}}" alt=""></a>
                                                    @elseif ($ride->payment_method == ($findRidePage->payment_methods_option4->features_setting_id ?? null))
                                                        <a href="javascript:void(0);" onclick="toggleModal1('modal-id2', '{{ $postRidePage->payment_methods_option3_tooltip }}')"><img class="w-8 h-8" src="{{asset('home_page_icons/' . $findRidePage->payment_methods_option4->icon)}}" alt=""></a>
                                                    @endif
                                                    @if ($ride->smoke == ($findRidePage->smoking_option1->features_setting_id ?? null))
                                                        <a href="javascript:void(0);" onclick="toggleModal1('modal-id2', '{{ $postRidePage->smoking_option1_tooltip }}')"><img class="w-8 h-8" src="{{asset('home_page_icons/' . $findRidePage->smoking_option1->icon)}}" alt=""></a>
                                                    @elseif ($ride->smoke == ($findRidePage->smoking_option2->features_setting_id ?? null))
                                                        <a href="javascript:void(0);" onclick="toggleModal1('modal-id2', '{{ $postRidePage->smoking_option2_tooltip }}')"><img class="w-8 h-8" src="{{asset('home_page_icons/' . $findRidePage->smoking_option2->icon)}}" alt=""></a>
                                                    @endif
                                                    @if ($ride->animal_friendly == ($findRidePage->pets_allowed_option1->features_setting_id ?? null))
                                                        <a href="javascript:void(0);" onclick="toggleModal1('modal-id2', '{{ $postRidePage->animals_option1_tooltip }}')"><img class="w-8 h-8"
                                                            src="{{asset('home_page_icons/' . $findRidePage->pets_allowed_option1->icon)}}"
                                                            alt=""></a>
                                                    @elseif ($ride->animal_friendly == ($findRidePage->pets_allowed_option2->features_setting_id ?? null))
                                                        <a href="javascript:void(0);" onclick="toggleModal1('modal-id2', '{{ $postRidePage->animals_option2_tooltip }}')"><img class="w-8 h-8"
                                                            src="{{asset('home_page_icons/' . $findRidePage->pets_allowed_option2->icon)}}"
                                                            alt=""></a>
                                                    @elseif ($ride->animal_friendly == ($findRidePage->pets_allowed_option3->features_setting_id ?? null))
                                                        <a href="javascript:void(0);" onclick="toggleModal1('modal-id2', '{{ $postRidePage->animals_option3_tooltip }}')"><img class="w-8 h-8"
                                                            src="{{asset('home_page_icons/' . $findRidePage->pets_allowed_option3->icon)}}"
                                                            alt=""></a>
                                                    @endif
                                                    @if ($ride->luggage == ($findRidePage->luggage_option1->features_setting_id ?? null))
                                                        <a href="javascript:void(0);" onclick="toggleModal1('modal-id2', '{{ $postRidePage->luggage_option1_tooltip }}')"><img class="w-8 h-8"
                                                            src="{{asset('home_page_icons/' . $findRidePage->luggage_option1->icon)}}"
                                                            alt=""></a>
                                                    @elseif ($ride->luggage == ($findRidePage->luggage_option2->features_setting_id ?? null))
                                                        <a href="javascript:void(0);" onclick="toggleModal1('modal-id2', '{{ $postRidePage->luggage_option2_tooltip }}')"><img class="w-8 h-8"
                                                            src="{{asset('home_page_icons/' . $findRidePage->luggage_option2->icon)}}"
                                                            alt=""></a>
                                                    @elseif ($ride->luggage == ($findRidePage->luggage_option3->features_setting_id ?? null))
                                                        <a href="javascript:void(0);" onclick="toggleModal1('modal-id2', '{{ $postRidePage->luggage_option3_tooltip }}')"><img class="w-8 h-8"
                                                            src="{{asset('home_page_icons/' . $findRidePage->luggage_option3->icon)}}"
                                                            alt=""></a>
                                                    @elseif ($ride->luggage == ($findRidePage->luggage_option4->features_setting_id ?? null))
                                                        <a href="javascript:void(0);" onclick="toggleModal1('modal-id2', '{{ $postRidePage->luggage_option4_tooltip }}')"><img class="w-8 h-8"
                                                            src="{{asset('home_page_icons/' . $findRidePage->luggage_option4->icon)}}"
                                                            alt=""></a>
                                                    @elseif ($ride->luggage == ($findRidePage->luggage_option5->features_setting_id ?? null))
                                                        <a href="javascript:void(0);" onclick="toggleModal1('modal-id2', '{{ $postRidePage->luggage_option5_tooltip }}')"><img class="w-8 h-8"
                                                            src="{{asset('home_page_icons/' . $findRidePage->luggage_option5->icon)}}"
                                                            alt=""></a>
                                                    @endif
                                                    @if (in_array($findRidePage->ride_features_option1->features_setting_id ?? null, explode('=', $ride->features)))
                                                        <a href="javascript:void(0);" onclick="toggleModal1('modal-id2', '{{ $postRidePage->features_option1_tooltip }}')"><img class="w-8 h-8" src="{{asset('home_page_icons/' . $findRidePage->ride_features_option1->icon)}}" alt=""></a>
                                                    @endif
                                                    @if (in_array($findRidePage->ride_features_option2->features_setting_id ?? null, explode('=', $ride->features)))
                                                        <a href="javascript:void(0);" onclick="toggleModal1('modal-id2', '{{ $postRidePage->features_option2_tooltip }}')"><img class="w-8 h-8" src="{{asset('home_page_icons/' . $findRidePage->ride_features_option2->icon)}}" alt=""></a>
                                                    @endif
                                                    @if (in_array($findRidePage->ride_features_option3->features_setting_id ?? null, explode('=', $ride->features)))
                                                        <a href="javascript:void(0);" onclick="toggleModal1('modal-id2', '{{ $postRidePage->features_option3_tooltip }}')"><img class="w-8 h-8" src="{{asset('home_page_icons/' . $findRidePage->ride_features_option3->icon)}}" alt=""></a>
                                                    @endif
                                                    @if (in_array($findRidePage->ride_features_option8->features_setting_id ?? null, explode('=', $ride->features)))
                                                        <a href="javascript:void(0);" onclick="toggleModal1('modal-id2', '{{ $postRidePage->features_option8_tooltip }}')"><img class="w-8 h-8" src="{{asset('home_page_icons/' . $findRidePage->ride_features_option8->icon)}}" alt=""></a>
                                                    @endif
                                                    @if (in_array($findRidePage->ride_features_option9->features_setting_id ?? null, explode('=', $ride->features)))
                                                        <a href="javascript:void(0);" onclick="toggleModal1('modal-id2', '{{ $postRidePage->features_option9_tooltip }}')"><img class="w-8 h-8" src="{{asset('home_page_icons/' . $findRidePage->ride_features_option9->icon)}}" alt=""></a>
                                                    @endif
                                                    @if (in_array($findRidePage->ride_features_option10->features_setting_id ?? null, explode('=', $ride->features)))
                                                        <a href="javascript:void(0);" onclick="toggleModal1('modal-id2', '{{ $postRidePage->features_option10_tooltip }}')"><img class="w-8 h-8" src="{{asset('home_page_icons/' . $findRidePage->ride_features_option10->icon)}}" alt=""></a>
                                                    @endif
                                                    @if (in_array($findRidePage->ride_features_option11->features_setting_id ?? null, explode('=', $ride->features)))
                                                        <a href="javascript:void(0);" onclick="toggleModal1('modal-id2', '{{ $postRidePage->features_option11_tooltip }}')"><img class="w-8 h-8" src="{{asset('home_page_icons/' . $findRidePage->ride_features_option11->icon)}}" alt=""></a>
                                                    @endif
                                                    @if (in_array($findRidePage->ride_features_option12->features_setting_id ?? null, explode('=', $ride->features)))
                                                        <a href="javascript:void(0);" onclick="toggleModal1('modal-id2', '{{ $postRidePage->features_option12_tooltip }}')"><img class="w-8 h-8" src="{{asset('home_page_icons/' . $findRidePage->ride_features_option12->icon)}}" alt=""></a>
                                                    @endif
                                                    @if (in_array($findRidePage->ride_features_option13->features_setting_id ?? null, explode('=', $ride->features)))
                                                        <a href="javascript:void(0);" onclick="toggleModal1('modal-id2', '{{ $postRidePage->features_option13_tooltip }}')"><img class="w-8 h-8" src="{{asset('home_page_icons/' . $findRidePage->ride_features_option13->icon)}}" alt=""></a>
                                                    @endif
                                                    @if (in_array($findRidePage->ride_features_option14->features_setting_id ?? null, explode('=', $ride->features)))
                                                        <a href="javascript:void(0);" onclick="toggleModal1('modal-id2', '{{ $postRidePage->features_option14_tooltip }}')"><img class="w-8 h-8" src="{{asset('home_page_icons/' . $findRidePage->ride_features_option14->icon)}}" alt=""></a>
                                                    @endif
                                                    @if (in_array($findRidePage->ride_features_option15->features_setting_id ?? null, explode('=', $ride->features)))
                                                        <a href="javascript:void(0);" onclick="toggleModal1('modal-id2', '{{ $postRidePage->features_option15_tooltip }}')"><img class="w-8 h-8" src="{{asset('home_page_icons/' . $findRidePage->ride_features_option15->icon)}}" alt=""></a>
                                                    @endif
                                                    @if (in_array($findRidePage->ride_features_option16->features_setting_id ?? null, explode('=', $ride->features)))
                                                        <a href="javascript:void(0);" onclick="toggleModal1('modal-id2', '{{ $postRidePage->features_option16_tooltip }}')"><img class="w-8 h-8" src="{{asset('home_page_icons/' . $findRidePage->ride_features_option16->icon)}}" alt=""></a>
                                                    @endif
                                                    @if (in_array($postRidePage->features_option4->features_setting_id ?? null, explode('=', $ride->features)))
                                                        <a href="javascript:void(0);" onclick="toggleModal1('modal-id2', '{{ $postRidePage->features_option4_tooltip }}')"><img class="w-8 h-8" src="{{asset('home_page_icons/' . $postRidePage->features_option4->icon)}}" alt=""></a>
                                                    @endif
                                                    @if (in_array($postRidePage->features_option5->features_setting_id ?? null, explode('=', $ride->features)))
                                                        <a href="javascript:void(0);" onclick="toggleModal1('modal-id2', '{{ $postRidePage->features_option5_tooltip }}')"><img class="w-8 h-8" src="{{asset('home_page_icons/' . $postRidePage->features_option5->icon)}}" alt=""></a>
                                                    @endif
                                                    @if (in_array($postRidePage->features_option6->features_setting_id ?? null, explode('=', $ride->features)))
                                                        <a href="javascript:void(0);" onclick="toggleModal1('modal-id2', '{{ $postRidePage->features_option6_tooltip }}')"><img class="w-8 h-8" src="{{asset('home_page_icons/' . $postRidePage->features_option6->icon)}}" alt=""></a>
                                                    @endif
                                                    @if (in_array($postRidePage->features_option7->features_setting_id ?? null, explode('=', $ride->features)))
                                                        <a href="javascript:void(0);" onclick="toggleModal1('modal-id2', '{{ $postRidePage->features_option7_tooltip }}')"><img class="w-8 h-8" src="{{asset('home_page_icons/' . $postRidePage->features_option7->icon)}}" alt=""></a>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="border-t border-gray-300 grid grid-cols-1 divide-x divide-gray-300">
                                            <div class="flex items-center justify-between p-4 w-full">
                                                <div class="flex items-center space-x-2">
                                                    <div class="w-12 h-12 rounded-full overflow-hidden">
                                                        <img class="w-full h-full object-contain"
                                                            src="{{ $ride->driver?->profile_image }}" alt="">
                                                    </div>
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
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endif

                        {{-- <div class="bg-white rounded shadow p-0 md:p-6">
                            <a >
                                <div style="cursor:pointer;">
                                    <div class="flex md:hidden items-center justify-between px-4 pt-4">
                                        <div class="w-auto">
                                            <div class='bg-primary w-min rounded p-2'>
                                                <span class='text-white'>{{ date_format(new DateTime($ride->date),'M') }}</span>
                                                <span class='bg-white px-1 rounded text-black'>{{ date_format(new DateTime($ride->date),'d') }}</span>
                                            </div>
                                        </div>
                                        <div class="w-auto">
                                            <h6 class='pt-2 whitespace-nowrap'></h6>
                                        </div>
                                    </div>
                                    <div class='flex items-center justify-between md:justify-center p-4 md:p-0 md:space-x-4'>
                                        <div class="hidden md:inline-flex w-1/12">
                                            <div class='bg-primary w-min rounded p-2'>
                                                <span class='text-white'>{{ date_format(new DateTime($ride->date),'M') }}</span>
                                                <span class='bg-white px-1 rounded text-black'>{{ date_format(new DateTime($ride->date),'d') }}</span>
                                            </div>
                                        </div>
                                        <div class="w-5/12 md:w-4/12">
                                            <h6 class='card-title leading-7 m-0'></h6>
                                            <p class='card-text'></p>
                                        </div>
                                        <div class="w-1/12">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="text-primary w-6 h-6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                            </svg>
                                        </div>
                                        <div class="w-5/12 md:w-4/12">
                                            <h6 class='card-title leading-7 m-0'></h6>
                                            <p class='card-text'></p>
                                        </div>
                                        <div class="hidden md:inline-flex w-2/12">
                                            <h6 class='pt-2 whitespace-nowrap'>$ {{ number_format(floatval($ride->price), 2) }}</h6>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div> --}}
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
                        <iframe class="mx-auto rounded-md md:w-full md:h-[700px] w-full h-[700px]"
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
                                <img class="w-full h-full object-contain" src="{{asset('home_page_icons/' . $homePage->work_section_passenger_point1_image)}}" alt="">
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
                                <img class="w-full h-full object-contian" src="{{asset('home_page_icons/' . $homePage->work_section_passenger_point2_image)}}" alt="">
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
                                <img class="" src="{{asset('home_page_icons/' . $homePage->work_section_passenger_point3_image)}}" alt="">
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
                            <img class="" src="{{asset('home_page_icons/' . $homePage->work_section_passenger_point4_image)}}" alt="">
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
                                <img class="" src="{{asset('home_page_icons/' . $homePage->work_section_passenger_point5_image)}}" alt="">
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
                                    <img class="w-full h-full object-contain" src="{{asset('home_page_icons/' . $homePage->work_section_driver_point1_image)}}" alt="">
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
                                    <img class="w-full h-full object-contain" src="{{asset('home_page_icons/' . $homePage->work_section_driver_point2_image)}}" alt="">
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
                                    <img class="w-full h-full object-contain" src="{{asset('home_page_icons/' . $homePage->work_section_driver_point3_image)}}" alt="">
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
                                    <img class="w-full h-full object-contain" src="{{asset('home_page_icons/' . $homePage->work_section_driver_point4_image)}}" alt="">
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
                                    <img class="w-full h-full object-contain" src="{{asset('home_page_icons/' . $homePage->work_section_driver_point5_image)}}" alt="">
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

<section style="background-image: url('home_page_icons/{{ $homePage->doing_section_slider_image }}');" class="bg-cover w-full h-80 relative">
    <div class="absolute left-0 right-0 m-auto bg-blue-600 h-full w-full bg-opacity-50 flex justify-center items-start">
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
                <a href="{{ route('post_ride', ['lang' => optional($selectedLanguage)->abbreviation]) }}" class="bg-transparent border hover:bg-white hover:text-primary font-FuturaMdCnBT text-lg border-white px-4 py-2 rounded text-white">
                    @isset($homePage->doing_section_label1)
                        {{ $homePage->doing_section_label1 }}
                    @endisset
                </a>
                <a href="{{ route('search_ride', ['lang' => optional($selectedLanguage)->abbreviation]) }}" class="bg-transparent border hover:bg-white hover:text-primary font-FuturaMdCnBT text-lg border-white px-4 py-2 rounded text-white">
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
        <div class="grid lg:grid-cols-5 md:grid-cols-2 sm:grid-cols-1 gap-x-4 xl:gap-x-6 gap-y-8 xl:gap-y-10 mt-8 md:mt-12">

            <div class="relative h-full">
                <div class=" h-full">
                    <div class="absolute -top-5 left-0 right-0 mx-auto bg-white rounded-md p-1 border h-14 w-14 shadow flex justify-center items-center">
                        <img class="" src="{{asset('home_page_icons/' . $homePage->reasons_section_members_image)}}" alt="">
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
                    <div class="absolute -top-5 left-0 right-0 mx-auto bg-white rounded-md p-1 border h-14 w-14 shadow flex justify-center items-center">
                        <img class="" src="{{asset('home_page_icons/' . $homePage->reasons_section_driver_image)}}" alt="">
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
                    <div class="absolute -top-5 left-0 right-0 mx-auto bg-white rounded-md p-1 border h-14 w-14 shadow flex justify-center items-center">
                        <img class="" src="{{asset('home_page_icons/' . $homePage->reasons_section_quality_image)}}" alt="">
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
                    <div class="absolute -top-5 left-0 right-0 mx-auto bg-white rounded-md p-1 border h-14 w-14 shadow flex justify-center items-center">
                        <img class="" src="{{asset('home_page_icons/' . $homePage->reasons_section_policy_image)}}" alt="">
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
                    <div class="absolute -top-5 left-0 right-0 mx-auto bg-white rounded-md p-1 border h-14 w-14 shadow flex justify-center items-center">
                        <img class="" src="{{asset('home_page_icons/' . $homePage->reasons_section_students_image)}}" alt="">
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
                    <div class="absolute -top-5 left-0 right-0 mx-auto bg-white rounded-md p-1 border h-14 w-14 shadow flex justify-center items-center">
                        <img class="" src="{{asset('home_page_icons/' . $homePage->reasons_section_safety_image)}}" alt="">
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
                    <div class="absolute -top-5 left-0 right-0 mx-auto bg-white rounded-md p-1 border h-14 w-14 shadow flex justify-center items-center">
                        <img class="" src="{{asset('home_page_icons/' . $homePage->reasons_section_price_image)}}" alt="">
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
                    <div class="absolute -top-5 left-0 right-0 mx-auto bg-white rounded-md p-1 border h-14 w-14 shadow flex justify-center items-center">
                        <img class="" src="{{asset('home_page_icons/' . $homePage->reasons_section_use_image)}}" alt="">
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
                    <div class="absolute -top-5 left-0 right-0 mx-auto bg-white rounded-md p-1 border h-14 w-14 shadow flex justify-center items-center">
                        <img class="" src="{{asset('home_page_icons/' . $homePage->reasons_section_reliable_image)}}" alt="">
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
                    <div class="absolute -top-5 left-0 right-0 mx-auto bg-white rounded-md p-1 border h-14 w-14 shadow flex justify-center items-center">
                        <img class="" src="{{asset('home_page_icons/' . $homePage->reasons_section_responsible_image)}}" alt="">
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
                    <img class="w-full h-full object-contain" src="{{asset('home_page_icons/' . $homePage->movement_section_icon)}}" alt="">
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
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor" class="w-8 h-8 dark:text-violet-400">
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
                                        {{ $shortText }}@if($isLong)...@endif
                                    </span>

                                    <span id="full-review-{{ $review->id }}" style="display: none;">
                                        {{ $review->review }}
                                    </span>

                                    @if($isLong)
                                        <button class="text-blue-500 ml-2" onclick="toggleReview({{ $review->id }})">
                                            <span id="toggle-btn-{{ $review->id }}">Read More</span>
                                        </button>
                                    @endif
                                </p>
                            </div>
                            <div class="flex justify-end mt-2">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor" class="w-8 h-8 dark:text-violet-400">
                                    <path d="M280,185.143V416H496V16H457.6ZM464,384H312V198.857L464,54.1Z"></path>
                                    <path d="M232,16H193.6L16,185.143V416H232ZM200,384H48V198.857L200,54.1Z"></path>
                                </svg>
                            </div>
                            <p>

                            </p>
                        </div>
                        <div class="flex flex-col items-center justify-center p-8 rounded-b-lg bg-primary text-white">
                            @if ($review->from->profile_image)
                                <img class="w-16 h-16 mb-2 -mt-16 bg-center bg-cover rounded-full dark:bg-gray-500 bg-center" src="{{ $review->from->profile_image }}" alt="">
                            @else
                                <img class="w-16 h-16 mb-2 -mt-16 bg-center bg-cover rounded-full dark:bg-gray-500 bg-center" src="{{ asset('assets/male.png') }}" alt="">
                            @endif
                                <p class="text-xl font-semibold text-white">{{ $review->from->first_name }} {{ $review->from->last_name }}</p>
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
                            <img class="w-full h-full object-contain" src="{{asset('home_page_icons/' . $homePage->news_section_icon1)}}" alt="">
                        @endisset
                    </div>
                </a>
            </div>
            <div>
                <a href="">
                    <div class="h-28 w-28 flex justify-center items-center">
                        @isset($homePage->news_section_icon2)
                            <img class="w-full h-full object-contain" src="{{asset('home_page_icons/' . $homePage->news_section_icon2)}}" alt="">
                        @endisset
                    </div>
                </a>
            </div>
            <div>
                <a href="">
                    <div class="h-28 w-28 flex justify-center items-center">
                        @isset($homePage->news_section_icon3)
                            <img class="w-full h-full object-contain" src="{{asset('home_page_icons/' . $homePage->news_section_icon3)}}" alt="">
                        @endisset
                    </div>
                </a>
            </div>
            <div>
                <a href="">
                    <div class="h-28 w-28 flex justify-center items-center">
                        @isset($homePage->news_section_icon4)
                            <img class="w-full h-full object-contain" src="{{asset('home_page_icons/' . $homePage->news_section_icon4)}}" alt="">
                        @endisset
                    </div>
                </a>
            </div>
        </div>
        <div class="flex flex-wrap justify-center items-center gap-4 md:gap-8 mt-4 md:mt-6 xl:mt-10">
            @foreach ($articles as $article)
                <div class="rounded bg-white shadow-3xl p-5">
                    <div class="p-4">
                        <div>
                            <p class="text-2xl font-FuturaMdCnBT">{{ $article->articleDetail[0]->title }}</p>
                            <p class="lg:text-sm md:text-base">Agency: {{ $article->agency }}</p>
                            <p class="lg:text-sm md:text-base">Added by: {{ $article->added_by }}</p>
                        </div>
                        <div class="flex justify-center items-center mt-3">
                            <a href="{{ route('news_detail', ['lang' => optional($selectedLanguage)->abbreviation, 'id' => $article->id]) }}" class="bg-primary text-white py-2 px-3 rounded">
                                Read article
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
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
                            <img class="w-full h-full object-contain" src="{{asset('home_page_icons/' . $homePage->use_section_point1_image)}}" alt="">
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
                            <img class="w-full h-full object-contain" src="{{asset('home_page_icons/' . $homePage->use_section_point2_image)}}" alt="">
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
                            <img class="w-full h-full object-contain" src="{{asset('home_page_icons/' . $homePage->use_section_point3_image)}}" alt="">
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
                            <img class="w-full h-full object-contain" src="{{asset('home_page_icons/' . $homePage->use_section_point4_image)}}" alt="">
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
                        {!! str_replace('<li>', '<li class="toggle-item">', str_replace('<br />', '', nl2br($homePage->reliability_section_passengers_description))) !!}
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
                        {!! str_replace('<li>', '<li class="toggle-item-driver">', str_replace('<br />', '', nl2br($homePage->reliability_section_drivers_description))) !!}
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

        <div class="flex flex-col sm:flex-col md:flex-row lg:flex-row flex-wrap justify-center items-center gap-4 md:gap-6 mt-4 md:mt-6 xl:mt-10">
            <div class="flex justify-center items-center gap-4 md:gap-6">
                <div class="flex justify-center items-center h-20">
                    @isset($homePage->payment_section_icon1)
                        <img class="h-full w-full object-contain" src="{{asset('home_page_icons/' . $homePage->payment_section_icon1)}}" alt="">
                    @endisset
                </div>
                <div class="flex justify-center items-center h-14">
                    @isset($homePage->payment_section_icon2)
                        <img class="h-full w-full object-contain" src="{{asset('home_page_icons/' . $homePage->payment_section_icon2)}}" alt="">
                    @endisset
                </div>
            </div>
            <div class="flex justify-center items-center gap-4 md:gap-6">
                <div class="flex justify-center items-center h-14 w-28">
                    @isset($homePage->payment_section_icon3)
                        <img class="h-full w-full object-contain" src="{{asset('home_page_icons/' . $homePage->payment_section_icon3)}}" alt="">
                    @endisset
                </div>
                <div class="flex justify-center items-center h-14">
                    @isset($homePage->payment_section_icon4)
                        <img class="h-full w-full object-contain" src="{{asset('home_page_icons/' . $homePage->payment_section_icon4)}}" alt="">
                    @endisset
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@section('script')

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    const dateInput = document.getElementById('dateInput');

    // Initialize the date picker
    flatpickr(dateInput, {
        dateFormat: 'F d, Y', // Display format (e.g., "January 15, 2024")
        altInput: true,
        altFormat: 'F d, Y',
        minDate: 'today',   // Restrict to future dates only
        disableMobile: true, // Disable mobile-friendly mode for consistent experience
        allowInput: true, // Allow manual input
        clickOpens: true, // Open calendar on click
        theme: 'default' // Use default theme
    });
    const mobileCloseRedirectUrl = "{{ route('mobile_close_redirect') }}";

    function isMobileClient() {
        return /android|iphone|ipad|ipod|iemobile|blackberry|kindle|silk|opera mini/i.test((navigator.userAgent || navigator.vendor || window.opera || '').toLowerCase());
    }

    function handleSuccessModalClose(event) {
        event.preventDefault();
        if (isMobileClient()) {
            window.location.href = mobileCloseRedirectUrl;
            return;
        }
        closeModal('success-modal1');
    }

    //  function closeModal() {
    //     document.getElementById('my-modal').style.display = 'none';
    // }
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

    // Rest of your existing JavaScript...
    const togglePassword = document.getElementById('togglePassword');
    const password = document.getElementById('password');

    togglePassword.addEventListener('click', function () {
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);

        if (type === 'password') {
            togglePassword.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 cursor-pointer text-gray-600">
                    <path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" />
                    <path fill-rule="evenodd" d="M1.323 11.447C2.811 6.976 7.028 3.75 12.001 3.75c4.97 0 9.185 3.223 10.675 7.69.12.362.12.752 0 1.113-1.487 4.471-5.705 7.697-10.677 7.697-4.97 0-9.186-3.223-10.675-7.69a1.762 1.762 0 0 1 0-1.113ZM17.25 12a5.25 5.25 0 1 1-10.5 0 5.25 5.25 0 0 1 10.5 0Z" clip-rule="evenodd" />
                </svg>`;
        } else {
            togglePassword.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" fill="currentColor" class="w-5 h-5 text-gray-600 cursor-pointer">
                <path d="M38.8 5.1C28.4-3.1 13.3-1.2 5.1 9.2S-1.2 34.7 9.2 42.9l592 464c10.4 8.2 25.5 6.3 33.7-4.1s6.3-25.5-4.1-33.7L525.6 386.7c39.6-40.6 66.4-86.1 79.9-118.4c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C465.5 68.8 400.8 32 320 32c-68.2 0-125 26.3-169.3 60.8L38.8 5.1zM223.1 149.5C248.6 126.2 282.7 112 320 112c79.5 0 144 64.5 144 144c0 24.9-6.3 48.3-17.4 68.7L408 294.5c8.4-19.3 10.6-41.4 4.8-63.3c-11.1-41.5-47.8-69.4-88.6-71.1c-5.8-.2-9.2 6.1-7.4 11.7c2.1 6.4 3.3 13.2 3.3 20.3c0 10.2-2.4 19.8-6.6 28.3l-90.3-70.8zM373 389.9c-16.4 6.5-34.3 10.1-53 10.1c-79.5 0-144-64.5-144-144c0-6.9 .5-13.6 1.4-20.2L83.1 161.5C60.3 191.2 44 220.8 34.5 243.7c-3.3 7.9-3.3 16.7 0 24.6c14.9 35.7 46.2 87.7 93 131.1C174.5 443.2 239.2 480 320 480c47.8 0 89.9-12.9 126.2-32.5L373 389.9z"/>
            </svg>`;
        }
    });

    function swapLocations() {
        // Get the values of the "From" and "To" input fields
        const fromValue = document.getElementById('fromInput').value;
        const toValue = document.getElementById('toInput').value;

        // Swap the values
        document.getElementById('fromInput').value = toValue;
        document.getElementById('toInput').value = fromValue;
    }
    
   
    document.addEventListener('DOMContentLoaded', function () {
    });

    $(document).ready(function () {
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
        $showMoreBtn.on("click", function (e) {
            e.preventDefault();
            $listItems.show();
            $showMoreBtn.hide();
            $showLessBtn.show();
        });
        $showMoreDriverBtn.on("click", function (e) {
            e.preventDefault();
            $listItemsDriver.show();
            $showMoreDriverBtn.hide();
            $showLessDriverBtn.show();
        });

        // Show less items when "Show Less" button is clicked
        $showLessBtn.on("click", function (e) {
            e.preventDefault();
            $listItems.slice(itemsToShow).hide();
            $showLessBtn.hide();
            $showMoreBtn.show();
        });
        $showLessDriverBtn.on("click", function (e) {
            e.preventDefault();
            $listItemsDriver.slice(itemsToShow).hide();
            $showLessDriverBtn.hide();
            $showMoreDriverBtn.show();
        });
    });

    // Add an event listener to the input fields
    document.getElementById('fromInput').addEventListener('keypress', function (event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            navigateToSearchRoute();
        }
    });

    document.getElementById('toInput').addEventListener('keypress', function (event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            navigateToSearchRoute();
        }
    });

    document.getElementById('dateInput').addEventListener('keypress', function (event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            navigateToSearchRoute();
        }
    })

    function navigateToSearchRoute() {
        // Get the values of the "From," "To," and "Date" input fields
        const fromValue = document.getElementById('fromInput').value;
        const toValue = document.getElementById('toInput').value;
        const dateValue = document.getElementById('dateInput').value;

        // Get the error message elements
        const fromError = document.getElementById('fromError');
        const toError = document.getElementById('toError');

        // Check if "From" and "To" fields are empty
        if (fromValue.trim() === '') {
            @isset($homePage->slider_required_error)
                fromError.textContent = '{{ $homePage->slider_required_error }}';
            @endisset
            fromError.classList.remove('hidden');
            toError.classList.add('hidden');
            return;
        } else if (toValue.trim() === '') {
            @isset($homePage->slider_required_error)
                toError.textContent = '{{ $homePage->slider_required_error }}';
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
        let searchUrl = `{{ route('search_ride', ['lang' => optional($selectedLanguage)->abbreviation]) }}?from=${fromValue}&to=${toValue}`;

        // Check if a date is provided and append it to the URL if available
        if (dateValue) {
            searchUrl += `&date=${dateValue}`;
        }

        // Navigate to the constructed URL
        window.location.href = searchUrl;
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
            } else {
                let suggestionsContainer = $('#fromInput-suggestions');
                suggestionsContainer.empty();  // Clear previous suggestions
            }
        }, 500));

        $('#toInput').on('input', debounce(function() {
            let searchTerm = $('#toInput').val();
            if (searchTerm.length >= 2) {  // Start searching after 3 characters are entered
                fetchCities(searchTerm, 'toInput');
            } else {
                let suggestionsContainer = $('#toInput-suggestions');
                suggestionsContainer.empty();  // Clear previous suggestions
            }
        }, 500));
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

</script>

@endsection
