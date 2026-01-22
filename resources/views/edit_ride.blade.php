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
    @if(session('error'))
        <div class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                    <div
                        class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg w-full">
                        <button type="button" onclick="closeModal()" class="absolute top-3 right-3 text-gray-400 hover:text-gray-500">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
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
                                    <h3 class="text-3xl text-center font-FuturaMdCnBT font-medium text-gray-900 mb-4" id="modal-title">{!! session('heading') !!}</h3>
                                </div>
                                <div class="mt-2 w-full">
                                    <p class="text-lg text-center text-black">{!! session('error') !!}</p>
                                </div>
                            </div>
                        </div>
                        <div class="px-4 pb-6 pt-4  sm:flex sm:flex-row-reverse sm:px-6 justify-center">
                            <a href=""
                                class="inline-flex w-full justinline-flex justify-center rounded bg-red-500 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-red-400 sm:ml-3 sm:w-24">Close</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    @if(session('message'))
        
        <div id="myModal" class="relative z-50" id="delete_message_confirmation" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                    <div onclick="closeModal()"  class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                        <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                            <div class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
                                <button type="button" onclick="closeModal()"  class="absolute top-3 right-3 text-gray-400 hover:text-gray-500">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
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
                                        <div class="mt-2 w-full">
                                            <p class="can-exp-p text-center">{{ session('message') }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="px-4 pb-6 pt-4 flex items-center space-x-2 sm:space-x-4 sm:px-6 justify-center">
                                <input type="hidden" id="notificationId" value="3094">
                                    <a href="#" onclick="closeModal()"  class="button-exp-fill">Close </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
    @endif
    <div class="flex justify-between items-center">
        <h1>
            Edit ride
        </h1>
    </div>
    @php
        $bookings_count = $ride->bookings()->where('status', '<>', 3)->where('status', '<>', 4)->whereHas('passenger', function($query) { $query->whereNull('deleted_at'); })->sum('seats');
    @endphp
    <form method="POST" action="{{ route('update_ride', ['lang' => $selectedLanguage->abbreviation, 'ride_id' => $ride->id]) }}" enctype="multipart/form-data" id="edit-ride-form">
        @csrf
        @method('PUT')
        <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-4">
            <div class="col-span-3">
                <div class="bg-white rounded-lg overflow-hidden shadow-3xl">
                    <h3 class="bg-primary text-white py-2 px-4">
                        @isset($postRidePage->ride_info_heading)
                            {{ $postRidePage->ride_info_heading }}
                        @endisset
                    </h3>

                    <input type="hidden" value="{{$ride->defaultRideDetail[0]->id}}" name="default_ride_detail_id">
                    <div class="bg-white p-4 space-y-3">
                      <div class="flex flex-col md:flex-row justify-between items-center">
                        <div class="w-full md:w-[45%] mb-4">
                            <div>
                                <label for="from_spot_0"
                                    class="block mb-2 text-gray-900">
                                    @isset($postRidePage->from_label)
                                        {{ $postRidePage->from_label }}
                                    @endisset
                                </label>
                                <div class="relative mt-2">
                                    <div class="absolute inset-y-0 start-0 flex items-center pl-2 pointer-events-none">
                                        <img src="{{ asset('assets/search-bar-from.png') }}" class="w-auto h-6" alt="">
                                    </div>


                                    <input type="text" id="from_spot_0" name="from" value="{{ old('from', $ride->defaultRideDetail[0]->departure) }}" oninput="fromInput('0')"
                                        class="bg-gray-100 border border-gray-200 pl-7 text-gray-900 text-base lg:text-lg rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 block w-full p-2.5 mt-2"
                                        @isset($postRidePage->from_placeholder)
                                            placeholder="{{ $postRidePage->from_placeholder }}"
                                        @endisset>

                                        <div id="from_spot_suggestions0" class="absolute left-0 right-0 bg-white shadow-lg mt-1 max-h-60 overflow-y-auto z-50"></div>
                                </div>
                                @error('from')
                                  <div class="relative tooltip bottom-0 group-hover:flex">
                                    <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                        <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                    </div>
                                  </div>
                                @enderror
                            </div>
                        </div>
                        <div class="w-full md:w-[10%] mt-4 hidden md:flex justify-center items-start">
                            <button type="button" onclick="swapLocations()">
                                <img src="{{ asset('assets/arrow.png') }}" class="w-10 h-10 mx-auto" alt="">
                            </button>
                        </div>
                        <div class="w-full md:w-[45%] mb-4">
                            <div>
                                <label for="to_spot_0"
                                    class="block mb-2 text-gray-900">
                                    @isset($postRidePage->to_label)
                                        {{ $postRidePage->to_label }}
                                    @endisset
                                </label>
                                <div class="relative mt-2">
                                    <div class="absolute inset-y-0 start-0 flex items-center pl-2 pointer-events-none">
                                        <img src="{{ asset('images/new-21-search-bar-to.png') }}" class="w-4 h-6" alt="">
                                    </div>
                                    <input type="text" id="to_spot_0" name="to" value="{{ old('to', $ride->defaultRideDetail[0]->destination) }}"
                                        class="bg-gray-100 border pl-7 border-gray-200 text-base lg:text-lg text-gray-900  rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 mt-2 block w-full p-2.5"
                                        @isset($postRidePage->to_placeholder)
                                            placeholder="{{ $postRidePage->to_placeholder }}"
                                        @endisset>

                                        <div id="to_spot_suggestions0" class="absolute left-0 right-0 bg-white shadow-lg mt-1 max-h-60 overflow-y-auto z-50"></div>
                                </div>
                                @error('to')
                                  <div class="relative tooltip -bottom-4 group-hover:flex">
                                    <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                        <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                    </div>
                                  </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg overflow-hidden shadow-3xl">
                        <div class="text-2xl bg-primary text-white py-2 px-4">
                            <h3 class="text-2xl">{{ $postRidePage->add_more_from_to ?? "Add more spots" }}</h3>
                        </div>
                        <div class="bg-white p-4">

                            @php
                                $count = 1;
                                if(null !== old('from_spot')){
                                    $count = count(old('from_spot'));
                                }else if(!empty($ride->moreRideDetail)){
                                    $count = !empty($ride->moreRideDetail) && count($ride->moreRideDetail) > 0 ? count($ride->moreRideDetail) : 1;
                                }
                            @endphp

                            <input type="hidden" id="rowCount" value="{{ $count }}">
                            <div class="appendNewRow">
                                @if(null !== old('from_spot'))
                                        @foreach (old('from_spot') as $key => $item)
                                        @php
                                            $renderIndex = $key + 1;
                                        @endphp
                                            @include('post_ride_partial.add_more_from_to_partial', ['index' => $renderIndex, 'ride_detail' => null, 'type' => $routeType])
                                        @endforeach
                                    @elseif(!empty($ride->moreRideDetail) && count($ride->moreRideDetail) > 0)
                                    @foreach ($ride->moreRideDetail as $key =>  $moreRideDetail)
                                        @include('post_ride_partial.add_more_from_to_partial', ['index' => '{{$key + 1}}', 'ride_detail' => $moreRideDetail, 'type' => 'edit'])
                                    @endforeach
                                @else
                                    @include('post_ride_partial.add_more_from_to_partial', ['index' => '1', 'ride_detail' => null, 'type' => 'create'])
                                @endif

                            </div>
                            <div class="flex items-center mt-4">
                                <button type="button" onclick="addNewRow();" class="button-exp-fill">
                                    Add
                                </button>
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
                            <textarea id="pickup_location" rows="5" name="pickup" {{ $bookings_count > 0 ? 'readonly' : '' }}
                              class="block p-2.5 w-full text-gray-900 bg-gray-100 rounded border border-gray-200 text-base lg:text-lg focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 mt-2"
                              @isset($postRidePage->pick_up_placeholder)
                                placeholder="{{ $postRidePage->pick_up_placeholder }}"
                              @endisset
                            >{{ old('pickup', $ride->pickup) }}</textarea>
                            @error('pickup')
                              <div class="relative tooltip -bottom-4 group-hover:flex">
                                <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                    <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
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
                            <textarea id="dropoff_location" rows="5" name="dropoff" {{ $bookings_count > 0 ? 'readonly' : '' }}
                              class="block p-2.5 w-full text-gray-900 bg-gray-100 rounded border border-gray-200 text-base lg:text-lg focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 mt-2"
                              @isset($postRidePage->drop_off_placeholder)
                                placeholder="{{ $postRidePage->drop_off_placeholder }}"
                              @endisset
                            >{{ old('dropoff', $ride->dropoff) }}</textarea>
                            @error('dropoff')
                              <div class="relative tooltip -bottom-4 group-hover:flex">
                                <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                    <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                </div>
                              </div>
                            @enderror
                        </div>
                        <div class="map-container w-full h-64 block md:hidden">
                            <iframe
                               src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3806.452697041917!2d78.39076592375736!3d17.43803374982052!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3bcb9144cdba8c47%3A0x937fe346f411a645!2sTutorials%20Point%20(India)%20Ltd.!5e0!3m2!1sen!2sin!4v1673629212535!5m2!1sen!2sin"
                               width="100%"
                               height="100%"
                               style="border:0;"
                               allowfullscreen="" loading="lazy"
                               referrerpolicy="no-referrer-when-downgrade">
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
                            <div class="w-[10%] mt-4 text-center">
                                <span class="text-center text-base lg:text-lg ">
                                    @isset($postRidePage->at_label)
                                        {{ $postRidePage->at_label }}
                                    @endisset
                                </span>
                            </div>
                            <div class="w-[45%] mb-4">
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
                        <input id="recurring_trip" type="checkbox" name="recurring" value="1" {{ old('recurring') === '1' ? 'checked' : '' }} {{ $bookings_count > 0 ? 'disabled' : '' }}
                            class="w-4 h-4 text-blue-600 cursor-pointer bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                            @if ($bookings_count > 0)
                                <input type="hidden" name="recurring" value="{{ $ride->recurring }}">
                            @endif
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
                                    Recurring trips
                                </label>
                                <div class="relative mt-2">
                                    <input type="number" min="1" name="recurring_trips" value="{{ old('recurring_trips') }}"
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
                            <textarea id="meeting" rows="5" name="details" {{ $bookings_count > 0 ? 'readonly' : '' }}
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
                                        <input id="number-of-seat-{{ $i }}" name="seats" type="radio" value="{{ $i }}" class="hidden" {{ $bookings_count > 0 ? 'disabled' : '' }} {{ old('seats', $ride->seats) == $i ? 'checked' : '' }} onchange="seat_selected(this)" data-parsley-required="true" data-parsley-trigger="blur focusout change" data-parsley-required-message="Please select the available seats." data-parsley-errors-container="#parsley-seats-error">
                                        @if ($bookings_count > 0)
                                            <input type="hidden" name="seats" value="{{ $ride->seats }}">
                                        @endif
                                        <img src="{{ old('seats', $ride->seats) >= $i ? asset('assets/seat-hover-1.png') : asset('assets/seat.png') }}" class="w-10 h-10 mt-0.5 cursor-pointer seat-image seat-unselect-{{ $i }}" alt="">
                                        <span class="absolute left-4 top-3 seat-number seat-number-{{ $i }} {{ old('seats', $ride->seats) >= $i ? 'text-green-300' : '' }}">{{ $i }}</span>
                                    </label>
                                </div>
                                @endfor
                            </div>
                            @error('seats')
                              <div class="relative tooltip -bottom-4 group-hover:flex">
                                <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
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
                                            <input type="radio" id="2-seats" name="middle_seats" value="2" {{ $bookings_count > 0 ? 'disabled' : '' }} class="hidden peer"
                                                {{ old('middle_seats', $ride->middle_seats) == '2' ? 'checked' : '' }}>
                                            <label for="2-seats" class="inline-flex items-center justify-center w-full p-3 text-gray-800 bg-white border-2 border-gray-100 rounded cursor-pointer peer-checked:border-green-500 peer-checked:border-2 peer-checked:text-green-500 hover:border-2 hover:border-green-500">
                                                <span class="font-medium text-base">
                                                    2 seats
                                                </span>
                                            </label>
                                        </li>
                                        <li>
                                            <input type="radio" id="3-seats" name="middle_seats" value="3" {{ $bookings_count > 0 ? 'disabled' : '' }} class="hidden peer"
                                                {{ old('middle_seats', $ride->middle_seats) == '3' ? 'checked' : '' }}>
                                            <label for="3-seats" class="inline-flex items-center justify-center w-full p-3 text-gray-800 bg-white border-2 border-gray-100 rounded cursor-pointer peer-checked:border-green-500 peer-checked:border-2 peer-checked:text-green-500 hover:border-2 hover:border-green-500">
                                                <span class="font-medium text-base">3 seats</span>
                                            </label>
                                        </li>
                                        @if ($bookings_count > 0)
                                            <input type="hidden" name="middle_seats" value="{{ $ride->middle_seats }}">
                                        @endif
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
                                    <label for="pickup_location" class="text-gray-900 mb-2">
                                        @isset($postRidePage->seats_back_label)
                                            {{ $postRidePage->seats_back_label }}
                                        @endisset
                                    </label>
                                    <ul class="grid gap-2 grid-cols-2 mt-2">
                                        <li>
                                            <input type="radio" id="2-back_seats" name="back_seats" value="2" {{ $bookings_count > 0 ? 'disabled' : '' }} class="hidden peer"
                                                {{ old('back_seats', $ride->back_seats) == '2' ? 'checked' : '' }}>
                                            <label for="2-back_seats" class="inline-flex items-center justify-center w-full p-3 text-gray-800 bg-white border-2 border-gray-100 rounded cursor-pointer peer-checked:border-green-500 peer-checked:border-2 peer-checked:text-green-500 hover:border-2 hover:border-green-500">
                                                <span class="font-medium text-base">
                                                    2 seats
                                                </span>
                                            </label>
                                        </li>
                                        <li>
                                            <input type="radio" id="3-back_seats" name="back_seats" value="3" {{ $bookings_count > 0 ? 'disabled' : '' }} class="hidden peer"
                                                {{ old('back_seats', $ride->back_seats) == '3' ? 'checked' : '' }}>
                                            <label for="3-back_seats" class="inline-flex items-center justify-center w-full p-3 text-gray-800 bg-white border-2 border-gray-100 rounded cursor-pointer peer-checked:border-green-500 peer-checked:border-2 peer-checked:text-green-500 hover:border-2 hover:border-green-500">
                                                <span class="font-medium text-base">3 seats</span>
                                            </label>
                                        </li>
                                        @if ($bookings_count > 0)
                                            <input type="hidden" name="back_seats" value="{{ $ride->back_seats }}">
                                        @endif
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
                <div class="mt-6">
                    <div class="bg-white rounded-lg overflow-hidden shadow-3xl">
                        <div class="bg-primary text-white py-2 px-4">
                            <h3>
                                @isset($postRidePage->price_payment_heading)
                                    {{ $postRidePage->price_payment_heading }}
                                @endisset
                            </h3>
                        </div>
                        <div class="bg-white p-4">
                            <div>
                                <label for="" class=" text-gray-700 font-medium">
                                    @isset($postRidePage->price_per_seat_label)
                                        {{ $postRidePage->price_per_seat_label }}
                                    @endisset
                                </label>
                                @if ($bookings_count > 0)
                                    <p class="text-sm text-gray-500 mt-1 mb-2">Price cannot be changed once passengers have booked this ride.</p>
                                @endif
                                <div class="relative mt-2">
                                    <span class="absolute inset-y-0 start-0 flex items-center pl-2 pointer-events-none">
                                        <svg fill="currentColor" width="800px" height="800px" viewBox="0 0 32 32" class="w-5 h-5 text-gray-500" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M 15 3 L 15 5.09375 C 12.164063 5.570313 10 8.050781 10 11 C 10 12.777344 10.832031 14.148438 11.9375 15.03125 C 13.042969 15.914063 14.375 16.40625 15.625 16.90625 C 16.875 17.40625 18.042969 17.914063 18.8125 18.53125 C 19.582031 19.148438 20 19.773438 20 21 C 20 23.15625 18.207031 25 16 25 C 13.78125 25 12 23.21875 12 21 L 12 20 L 10 20 L 10 21 C 10 23.964844 12.164063 26.429688 15 26.90625 L 15 29 L 17 29 L 17 26.90625 C 19.84375 26.425781 22 23.925781 22 21 C 22 19.21875 21.167969 17.855469 20.0625 16.96875 C 18.957031 16.082031 17.625 15.5625 16.375 15.0625 C 15.125 14.5625 13.957031 14.082031 13.1875 13.46875 C 12.417969 12.855469 12 12.21875 12 11 C 12 8.808594 13.785156 7 16 7 C 18.21875 7 20 8.78125 20 11 L 20 12 L 22 12 L 22 11 C 22 8.035156 19.835938 5.570313 17 5.09375 L 17 3 Z"/>
                                        </svg>
                                    </span>
                                    <input type="number" step="any" name="price" id="priceData0" placeholder=""
                                        value="{{ old('price', $ride->defaultRideDetail[0]->price) }}"
                                        {{ $bookings_count > 0 ? 'readonly' : '' }}
                                        class="bg-gray-100 border border-gray-200 pl-7 text-gray-900 text-base lg:text-lg rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 block w-full p-2.5 mt-2 {{ $bookings_count > 0 ? 'cursor-not-allowed opacity-60' : '' }}"/>
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
                                </label>
                                <div class="space-y-2 mt-2">
                                    @isset($postRidePage->payment_methods_option1->features_setting_id)
                                        <div class="flex items-center space-x-1 md:space-x-2 mb-2 mr-2 lg:mr-2">
                                            <input id="cash" name="payment_method" type="radio" value="{{ $postRidePage->payment_methods_option1->features_setting_id }}" {{ $bookings_count > 0 ? 'disabled' : '' }}
                                                {{ old('payment_method', $ride->payment_method) == $postRidePage->payment_methods_option1->features_setting_id ? 'checked' : '' }}
                                                class="h-5 w-5 rounded bg-white border border-gray-200 cursor-pointer text-indigo-600 focus:ring-indigo-600">
                                            <label for="cash"
                                                class="ml-3 font-normal text-gray-900 flex items-center space-x-1">

                                                <span class="">
                                                    {{ $postRidePage->payment_methods_option1->name }}
                                                </span>
                                                <div class="sups relative inline-flex">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle-fill text-gray-400 peer" viewBox="0 0 16 16">
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
                                                                Passenger will give the driver cash payment at the pick-up location. Driver will only accept the local currency
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    @endisset
                                    @isset($postRidePage->payment_methods_option2->features_setting_id)
                                        <div class="flex items-center space-x-1 md:space-x-2 mb-2 mr-2 lg:mr-2">
                                            <input id="online" name="payment_method" type="radio" value="{{ $postRidePage->payment_methods_option2->features_setting_id }}" {{ $bookings_count > 0 ? 'disabled' : '' }}
                                                {{ old('payment_method', $ride->payment_method) == $postRidePage->payment_methods_option2->features_setting_id ? 'checked' : '' }}
                                                class="h-5 w-5 rounded bg-white border border-gray-200 cursor-pointer text-indigo-600 focus:ring-indigo-600">
                                            <label for="online"
                                                class="ml-3 font-normal text-gray-900 flex items-center space-x-1">

                                                <span class="">
                                                    {{ $postRidePage->payment_methods_option2->name }}
                                                </span>
                                                <div class="sups relative inline-flex">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle-fill text-gray-400 peer" viewBox="0 0 16 16">
                                                        <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                                                    </svg>
                                                    <div
                                                      class="absolute tooltip -top-[7.5rem] sm:-top-[6.5rem] md:-top-20 right-48 lg:right-52 lg:-top-[10.5rem] xl:-top-[7.3rem] 2xl:-top-[7.3rem] group-hover:flex hidden peer-hover:flex"
                                                    >
                                                        <div
                                                            role="tooltip"
                                                            class="absolute after:left-[10.8rem] lg:after:left-[11.8rem] payment_tooltiptext -left-1/2 -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-blue-500  border border-blue-500 text-gray-600 rounded tooltip_width sm:w-[25rem] md:w-[30rem] lg:w-72 xl:w-[23rem] 2xl:w-[25rem] px-4"
                                                        >
                                                            <p class="text-white font-semibold text-start text-sm lg:text-base">
                                                                Passenger will have to give online payment at the time of the booking process. Acceptable online payment methods are: PayPal, Credit card, Bank account and Interac transfer
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    @endisset
                                    @isset($postRidePage->payment_methods_option3->features_setting_id)
                                        <div class="flex items-center space-x-1 md:space-x-2 mb-2 mr-2 lg:mr-2">
                                            <input id="secured" name="payment_method" type="radio" value="{{ $postRidePage->payment_methods_option3->features_setting_id }}" {{ $bookings_count > 0 ? 'disabled' : '' }}
                                                {{ old('payment_method', $ride->payment_method) == $postRidePage->payment_methods_option3->features_setting_id ? 'checked' : '' }}
                                                class="h-5 w-5 rounded border border-gray-200 bg-white cursor-pointer text-indigo-600 focus:ring-indigo-600">
                                            <label for="secured"
                                                class="ml-3 font-normal text-gray-900 flex items-center space-x-1">
                                                <span class="">
                                                    {{ $postRidePage->payment_methods_option3->name }}
                                                </span>
                                                <div class="sups relative inline-flex">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle-fill text-gray-400 peer" viewBox="0 0 16 16">
                                                        <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                                                    </svg>
                                                    <div
                                                      class="absolute tooltip -top-[11.5rem] xs:-top-40 sm:-top-[7.5rem] md:-top-[6.5rem] right-44 md:right-48 lg:right-48 lg:-top-[13.5rem] xl:-top-48 2xl:-top-[10.3rem] group-hover:flex hidden peer-hover:flex"
                                                    >
                                                        <div
                                                            role="tooltip"
                                                            class="absolute after:left-[9.8rem] md:after:left-[10.8rem] payment_tooltiptext -left-1/2 -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-blue-500  border border-blue-500 text-gray-600 rounded tooltip_width sm:w-[25rem] md:w-[30rem] lg:w-72 xl:w-[23rem] 2xl:w-[25rem] px-4"
                                                        >
                                                            <p class="text-white font-semibold text-start text-sm lg:text-base">
                                                                In secured-cash, passenger will transfer payment to ProximaRide through online payment process and a code will be sent to driver. At the time of the meetup, passenger will give driver payment in cash and the online payment passenger sent will be returned to their wallet
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    @endisset
                                </div>
                                @if ($bookings_count > 0)
                                    <input type="hidden" name="payment_method" value="{{ $ride->payment_method }}">
                                @endif
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
                                            <input type="radio" id="instant-booking" name="booking_method" value="{{ $postRidePage->booking_option1->features_setting_id }}" {{ $bookings_count > 0 ? 'disabled' : '' }}
                                                {{ old('booking_method', $ride->booking_method) == $postRidePage->booking_option1->features_setting_id ? 'checked' : '' }} class="hidden peer">
                                            <label for="instant-booking" class="inline-flex items-center space-x-3 w-full p-4 text-gray-800 bg-white border-2 border-gray-100 rounded cursor-pointer peer-checked:border-green-500 peer-checked:border-2 peer-checked:text-green-500 hover:border-2 hover:border-green-500">
                                                <img class="w-12 h-12" src="{{ asset('assets/instant.png') }}" alt="">
                                                <span class="font-medium text-xl">
                                                    {{ $postRidePage->booking_option1->name }}
                                                </span>
                                            </label>
                                        </li>
                                    @endisset
                                    @isset($postRidePage->booking_option2->features_setting_id)
                                        <li>
                                            <input type="radio" id="manual-approval" name="booking_method" value="{{ $postRidePage->booking_option2->features_setting_id }}" {{ $bookings_count > 0 ? 'disabled' : '' }}
                                                {{ old('booking_method', $ride->booking_method) == $postRidePage->booking_option2->features_setting_id ? 'checked' : '' }} class="hidden peer">
                                            <label for="manual-approval" class="inline-flex items-center space-x-3 w-full p-4 text-gray-800 bg-white border-2 border-gray-100 rounded cursor-pointer peer-checked:border-green-500 peer-checked:border-2 peer-checked:text-green-500 hover:border-2 hover:border-green-500">
                                                <img class="w-12 h-12" src="{{ asset('assets/manual.png') }}" alt="">
                                                <span class="font-medium text-xl">
                                                    {{ $postRidePage->booking_option2->name }}
                                                </span>
                                            </label>
                                        </li>
                                    @endisset
                                </ul>
                                @if ($bookings_count > 0)
                                    <input type="hidden" name="booking_method" value="{{ $ride->booking_method }}">
                                @endif
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
                                <div class="flex justify-between mb-4">
                                    <div>
                                        <input id="skip" type="checkbox" name="skip_vehicle" value="1"
                                            {{ old('skip_vehicle', $ride->skip_vehicle) == '1' ? 'checked' : '' }} {{ $bookings_count > 0 ? 'disabled' : '' }}
                                            class="w-4 h-4 text-blue-600 cursor-pointer bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                                        @if ($bookings_count > 0)
                                            <input type="hidden" name="skip_vehicle" value="{{ $ride->skip_vehicle }}">
                                        @endif
                                        <label for="skip" class="ml-2  text-gray-900">
                                            @isset($postRidePage->skip_label)
                                                {{ $postRidePage->skip_label }}
                                            @endisset
                                        </label>
                                    </div>
                                    <div>
                                        <input id="add" type="checkbox" name="add_vehicle" value="1"
                                            {{ old('add_vehicle', $ride->add_vehicle) == '1' ? 'checked' : '' }} {{ $bookings_count > 0 ? 'disabled' : '' }}
                                            class="w-4 h-4 text-blue-600 cursor-pointer bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                                        @if ($bookings_count > 0)
                                            <input type="hidden" name="add_vehicle" value="{{ $ride->add_vehicle }}">
                                        @endif
                                        <label for="add" class="ml-2  text-gray-900">
                                            @isset($postRidePage->add_vehicle_label)
                                                {{ $postRidePage->add_vehicle_label }}
                                            @endisset
                                        </label>
                                    </div>
                                    <div class="{{ $vehicles->count() > '1' ? '' : 'hidden' }}">
                                        @php
                                            // Check if any vehicle has primary_vehicle = 1
                                            $hasPrimaryVehicle = false;
                                            foreach ($vehicles as $vehicle) {
                                                if (isset($vehicle->primary_vehicle) && ($vehicle->primary_vehicle == '1' || $vehicle->primary_vehicle == 1)) {
                                                    $hasPrimaryVehicle = true;
                                                    break;
                                                }
                                            }
                                            // Check if ride already has added_vehicle checked, or if there's a primary vehicle available
                                            $currentAddedVehicle = old('added_vehicle');
                                            if ($currentAddedVehicle === null) {
                                                $currentAddedVehicle = $ride->added_vehicle ?? null;
                                            }
                                            // Check the box if: already checked, OR if there's a primary vehicle (and not explicitly unchecked)
                                            $shouldCheckAddedVehicle = ($currentAddedVehicle == '1') || ($hasPrimaryVehicle && $currentAddedVehicle !== '0');
                                        @endphp
                                        <input id="added" type="checkbox" name="added_vehicle" value="1" {{ $bookings_count > 0 ? '' : 'disabled' }}
                                            {{ $shouldCheckAddedVehicle ? 'checked' : '' }}
                                            class="w-4 h-4 text-blue-600 cursor-pointer bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                                        @if ($bookings_count > 0)
                                            <input type="hidden" name="added_vehicle" value="{{ $ride->added_vehicle }}">
                                        @endif
                                        <label for="added" class="ml-2  text-gray-900">
                                            Existing
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
                                            </label>
                                            <div class="mt-2">
                                                <input type="text" name="make" id="" {{ $bookings_count > 0 ? 'readonly' : '' }}
                                                    @if ($errors->count() > 0)
                                                        value="{{ old('make', $ride->make) }}"
                                                    @else
                                                        value="{{ $ride->make }}"
                                                    @endif
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
                                            </label>
                                            <div class="mt-2">
                                                <input type="text" name="model" id="" {{ $bookings_count > 0 ? 'readonly' : '' }}
                                                    @if ($errors->count() > 0)
                                                        value="{{ old('model', $ride->model) }}"
                                                    @else
                                                        value="{{ $ride->model }}"
                                                    @endif
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
                                            </label>
                                            <div class="mt-2">
                                                <select id="type" name="vehicle_type" {{ $bookings_count > 0 ? 'disabled' : '' }}
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
                                            @if ($bookings_count > 0)
                                                <input type="hidden" name="vehicle_type" value="{{ $ride->vehicle_type }}">
                                            @endif
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
                                            </label>
                                            <div class="mt-2">
                                                <input type="text" name="year" id="" placeholder="" {{ $bookings_count > 0 ? 'readonly' : '' }}
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
                                            <label for="modal"
                                                class="text-gray-900 mb-2">
                                                @isset($postRidePage->color_label)
                                                    {{ $postRidePage->color_label }}
                                                @endisset
                                            </label>
                                            <div class="mt-2">
                                                <input type="text" name="color" id="" placeholder="" {{ $bookings_count > 0 ? 'readonly' : '' }}
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
                                            </label>
                                            <div class="mt-2">
                                                <input type="text" name="license_no" id="" placeholder="" {{ $bookings_count > 0 ? 'readonly' : '' }}
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
                                            <label for="modal" class="text-gray-900 mb-2">Fuel</label>
                                            <div class=" flex items-center">
                                                @isset($postRidePage->electric_car_label)
                                                    <div class="flex items-center space-x-1.5 lg:space-x-3 mb-2 mr-2 lg:mr-2">
                                                        <input id="" name="car_type" type="radio" value="{{ $postRidePage->electric_car_label }}" {{ $bookings_count > 0 ? 'disabled' : '' }}
                                                            {{ old('car_type', $ride->car_type) == $postRidePage->electric_car_label ? 'checked' : '' }}
                                                            class="h-5 w-5 border-gray-300 bg-gray-200 cursor-pointer text-indigo-600 focus:ring-indigo-600">
                                                        <label for="" class="block text-gray-900">
                                                            {{ $postRidePage->electric_car_label }}
                                                        </label>
                                                    </div>
                                                @endisset
                                                @isset($postRidePage->hybrid_car_label)
                                                    <div class="flex items-center space-x-1.5 lg:space-x-3 mb-2 mr-2 lg:mr-2">
                                                        <input id="" name="car_type" type="radio" value="{{ $postRidePage->hybrid_car_label }}" {{ $bookings_count > 0 ? 'disabled' : '' }}
                                                            {{ old('car_type', $ride->car_type) == $postRidePage->hybrid_car_label ? 'checked' : '' }}
                                                            class="h-5 w-5 border-gray-300 bg-gray-200 cursor-pointer text-indigo-600 focus:ring-indigo-600">
                                                        <label for="" class="block text-gray-900">
                                                            {{ $postRidePage->hybrid_car_label }}
                                                        </label>
                                                    </div>
                                                @endisset
                                                @isset($postRidePage->gas_car_label)
                                                    <div class="flex items-center space-x-1.5 lg:space-x-3 mb-2 mr-2 lg:mr-2">
                                                        <input id="" name="car_type" type="radio" value="{{ $postRidePage->gas_car_label }}" {{ $bookings_count > 0 ? 'disabled' : '' }}
                                                            {{ old('car_type', $ride->car_type) == $postRidePage->gas_car_label ? 'checked' : '' }}
                                                            class="h-5 w-5 border-gray-300 bg-gray-200 cursor-pointer text-indigo-600 focus:ring-indigo-600">
                                                        <label for="" class="block text-gray-900">
                                                            {{ $postRidePage->gas_car_label }}
                                                        </label>
                                                    </div>
                                                @endisset
                                            </div>
                                            @if ($bookings_count > 0)
                                                <input type="hidden" name="car_type" value="{{ $ride->car_type }}">
                                            @endif
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
                                                            <p class="text-sm lg:text-lg text-gray-900"> Upload car photo.
                                                                <span class="font-semibold text-primary"> Choose file</span>
                                                            </p>
                                                            <p class="text-sm lg:text-base text-gray-900 font-normal">
                                                                Allowed formats: JPG, JPEG. PNG, and GIF. 10MB max.
                                                            </p>
                                                        </div>
                                                        <input id="dropzone-file" name="image" type="file" onchange="previewImage(this)" class="hidden" {{ $bookings_count > 0 ? 'disabled' : '' }} />
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
                                        Select vehicle
                                    </label>
                                    <div class="mt-2">
                                        @php
                                            $primaryVehicle = collect($vehicles)->firstWhere('primary_vehicle', 1);
                                            $defaultVehicleId = old('vehicle_id', $ride->vehicle_id);
                                            // If no vehicle is selected and there's a primary vehicle, use primary vehicle
                                            if (empty($defaultVehicleId) && $primaryVehicle) {
                                                $defaultVehicleId = $primaryVehicle->id;
                                            }
                                        @endphp
                                        <select id="type" name="vehicle_id" {{ $bookings_count > 0 ? 'readonly' : '' }}
                                            class="bg-white border border-gray-300 text-gray-900 text-base lg:text-lg rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 mt-2 block w-full p-2.5">
                                            <option value=""
                                                {{ empty($defaultVehicleId) ? 'selected' : '' }}>
                                                Select
                                            </option>
                                            @foreach ($vehicles as $vehicle)
                                                <option value="{{ $vehicle->id }}"
                                                    {{ $defaultVehicleId == $vehicle->id ? 'selected' : '' }}>
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
                                    <div class="flex items-center justify-between p-3">
                                        <label for="no-luggage" class="font-normal text-gray-900 flex items-center space-x-1">
                                            <img class="w-10 h-10" src="{{ asset('assets/noluggage.png') }}" alt="">
                                            <span>
                                                {{ $postRidePage->luggage_option1->name }}
                                            </span>
                                        </label>
                                        <input type="radio" id="no-luggage" name="luggage" value="{{ $postRidePage->luggage_option1->features_setting_id }}" {{ $bookings_count > 0 ? 'disabled' : '' }}
                                            {{ old('luggage', $ride->luggage) == $postRidePage->luggage_option1->features_setting_id ? 'checked' : '' }} class="w-4 h-4 ml-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                    </div>
                                @endisset
                                @isset($postRidePage->luggage_option2)
                                    <div class="flex items-center justify-between p-3">
                                        <label for="small" class="font-normal text-gray-900 flex items-center space-x-1">
                                            <img class="w-10 h-10" src="{{ asset('assets/luggage.png') }}" alt="">
                                            <span class="">
                                                {{ $postRidePage->luggage_option2->name }}
                                            </span>
                                        </label>
                                        <input type="radio" id="small" name="luggage" value="{{ $postRidePage->luggage_option2->features_setting_id }}" {{ $bookings_count > 0 ? 'disabled' : '' }}
                                            {{ old('luggage', $ride->luggage) == $postRidePage->luggage_option2->features_setting_id ? 'checked' : '' }} class="w-4 h-4 ml-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                    </div>
                                @endisset
                                @isset($postRidePage->luggage_option3)
                                    <div class="flex items-center justify-between p-3">
                                        <label for="medium" class="font-normal text-gray-900 flex items-center space-x-1">
                                            <img class="w-10 h-10" src="{{ asset('assets/mediumluggage.png') }}" alt="">
                                            <span>
                                                {{ $postRidePage->luggage_option3->name }}
                                            </span>
                                        </label>
                                        <input type="radio" id="medium" name="luggage" value="{{ $postRidePage->luggage_option3->features_setting_id }}" {{ $bookings_count > 0 ? 'disabled' : '' }}
                                            {{ old('luggage', $ride->luggage) == $postRidePage->luggage_option3->features_setting_id ? 'checked' : '' }} class="w-4 h-4 ml-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                    </div>
                                @endisset
                                @isset($postRidePage->luggage_option4)
                                    <div class="flex items-center justify-between p-3">
                                        <label for="large" class="font-normal text-gray-900 flex items-center space-x-1">
                                            <img class="w-10 h-10" src="{{ asset('assets/largeluggage.png') }}" alt="">
                                            <span>
                                                {{ $postRidePage->luggage_option4->name }}
                                            </span>
                                        </label>
                                        <input type="radio" id="large" name="luggage" value="{{ $postRidePage->luggage_option4->features_setting_id }}" {{ $bookings_count > 0 ? 'disabled' : '' }}
                                            {{ old('luggage', $ride->luggage) == $postRidePage->luggage_option4->features_setting_id ? 'checked' : '' }} class="w-4 h-4 ml-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                    </div>
                                @endisset
                                @isset($postRidePage->luggage_option5)
                                    <div class="flex items-start justify-between p-3">
                                        <label for="xl-multiple" class="font-normal text-gray-900 flex items-start space-x-1">
                                            <img class="w-10 h-10" src="{{ asset('assets/extralargeluggage.png') }}" alt="">
                                            <div>
                                                <p class="leading-normal mt-2">
                                                    {{ $postRidePage->luggage_option5->name }}
                                                </p>
                                                <div class="font-normal text-gray-900 flex lg:block items-center space-x-0.5 2xl:pr-8">
                                                    <small>{{ $postRidePage->luggage_option5_label }} <sup class="text-red-500">*</sup></small>
                                                    <div class="sups relative inline-flex">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle-fill text-gray-400 peer" viewBox="0 0 16 16">
                                                            <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                                                        </svg>
                                                        <div
                                                          class="absolute tooltip tooltip_position sm:right-[13.8rem] sm:-top-20 md:right-64 lg:right-52 xl:right-36 xl:-top-[7.5rem] 2xl:right-16 2xl:-top-32 lg:-top-36 group-hover:flex hidden peer-hover:flex"
                                                        >
                                                            <div
                                                                role="tooltip"
                                                                class="absolute sm:after:left-1/2 lg:after:left-48 xl:after:left-[7.8rem] 2xl:after:left-[2.8rem] luggage_tooltiptext -left-1/2 -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-blue-500  border border-blue-500 text-gray-600 rounded tooltip_width sm:w-[25rem] md:w-[30rem] lg:w-72 xl:w-[23rem] 2xl:w-[25rem] px-4"
                                                            >
                                                                <p class="text-white font-semibold text-start text-sm lg:text-base">
                                                                    Should the ride come and passenger and the driver are unable to agree on the extra charge, cancellation policy will apply, ProximaRide will investigate
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </label>
                                        <input type="radio" id="xl-multiple" name="luggage" value="{{ $postRidePage->luggage_option5->features_setting_id }}" {{ $bookings_count > 0 ? 'disabled' : '' }}
                                            {{ old('luggage', $ride->luggage) == $postRidePage->luggage_option5->features_setting_id ? 'checked' : '' }} class="w-4 h-4 mt-2 ml-4 text-blue-600 cursor-pointer bg-white border-gray-500 rounded focus:ring-blue-500  focus:ring-2">
                                    </div>
                                @endisset
                            </div>
                            @if ($bookings_count > 0)
                                <input type="hidden" name="luggage" value="{{ $ride->luggage }}">
                            @endif
                            @error('luggage')
                              <div class="relative tooltip -bottom-4 group-hover:flex">
                                <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                    <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                </div>
                              </div>
                            @enderror
                            <div class="mt-6 space-y-2">
                                <div class="flex items-start">
                                    <input id="heating" type="checkbox" name="accept_more_luggage" value="1" {{ $bookings_count > 0 ? 'disabled' : '' }}
                                        {{ old('accept_more_luggage', $ride->accept_more_luggage) == '1' ? 'checked' : '' }}
                                        class="w-4 h-4 mt-1 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                    @if ($bookings_count > 0)
                                        <input type="hidden" name="accept_more_luggage" value="{{ $ride->accept_more_luggage }}">
                                    @endif
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
                                    <input id="heating" type="checkbox" name="open_customized" value="1" {{ $bookings_count > 0 ? 'disabled' : '' }}
                                        {{ old('open_customized', $ride->open_customized) == '1' ? 'checked' : '' }}
                                        class="w-4 h-4 mt-1 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                    @if ($bookings_count > 0)
                                        <input type="hidden" name="open_customized" value="{{ $ride->open_customized }}">
                                    @endif
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
                                    <div class="flex items-center justify-between p-3">
                                        <label for="smoke-1" class="font-normal text-gray-900 flex space-x-1">
                                            <span class="">
                                                {{ $postRidePage->smoking_option1->name }}
                                            </span>
                                        </label>
                                        <input id="smoke-1" name="smoke" type="radio" value="{{ $postRidePage->smoking_option1->features_setting_id }}" {{ $bookings_count > 0 ? 'disabled' : '' }}
                                            {{ old('smoke', $ride->smoke) == $postRidePage->smoking_option1->features_setting_id ? 'checked' : '' }}
                                            class="h-4 w-4 ml-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                    </div>
                                @endisset
                                @isset($postRidePage->smoking_option2->features_setting_id)
                                    <div class="flex items-center justify-between p-3">
                                        <label for="smoke-2" class="font-normal text-gray-900 flex space-x-1">
                                            {{ $postRidePage->smoking_option2->name }}
                                        </label>
                                        <input id="smoke-2" name="smoke" type="radio" value="{{ $postRidePage->smoking_option2->features_setting_id }}" {{ $bookings_count > 0 ? 'disabled' : '' }}
                                            {{ old('smoke', $ride->smoke) == $postRidePage->smoking_option2->features_setting_id ? 'checked' : '' }}
                                            class="h-4 w-4 ml-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                    </div>
                                @endisset
                            </div>
                            @if ($bookings_count > 0)
                                <input type="hidden" name="smoke" value="{{ $ride->smoke }}">
                            @endif
                            @error('smoke')
                              <div class="relative tooltip -bottom-4 group-hover:flex">
                                <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
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
                                    <div class="flex items-center justify-between p-3">
                                        <label for="animal-1" class="font-normal text-gray-900 flex space-x-1">
                                            {{ $postRidePage->animals_option1->name }}
                                        </label>
                                        <input id="animal-1" name="animal_friendly" type="radio" value="{{ $postRidePage->animals_option1->features_setting_id }}" {{ $bookings_count > 0 ? 'disabled' : '' }}
                                            {{ old('animal_friendly', $ride->animal_friendly) == $postRidePage->animals_option1->features_setting_id ? 'checked' : '' }}
                                            class="w-4 h-4 ml-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                    </div>
                                @endisset
                                @isset($postRidePage->animals_option2->features_setting_id)
                                    <div class="flex items-center justify-between p-3">
                                        <label for="animal-2" class="font-normal text-gray-900 flex space-x-1">
                                            {{ $postRidePage->animals_option2->name }}
                                        </label>
                                        <input id="animal-2" name="animal_friendly" type="radio" value="{{ $postRidePage->animals_option2->features_setting_id }}" {{ $bookings_count > 0 ? 'disabled' : '' }}
                                            {{ old('animal_friendly', $ride->animal_friendly) == $postRidePage->animals_option2->features_setting_id ? 'checked' : '' }}
                                            class="w-4 h-4 ml-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                    </div>
                                @endisset
                                @isset($postRidePage->animals_option3->features_setting_id)
                                    <div class="flex items-center justify-between p-3">
                                        <label for="animal-3" class="font-normal text-gray-900 flex space-x-1">
                                            {{ $postRidePage->animals_option3->name }}
                                        </label>
                                        <input id="animal-3" name="animal_friendly" type="radio" value="{{ $postRidePage->animals_option3->features_setting_id }}" {{ $bookings_count > 0 ? 'disabled' : '' }}
                                            {{ old('animal_friendly', $ride->animal_friendly) == $postRidePage->animals_option3->features_setting_id ? 'checked' : '' }}
                                            class="w-4 h-4 ml-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                    </div>
                                @endisset
                            </div>
                            @if ($bookings_count > 0)
                                <input type="hidden" name="animal_friendly" value="{{ $ride->animal_friendly }}">
                            @endif
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
                                        <input id="pink-ride" type="checkbox" name="features[]" {{ $bookings_count > 0 ? 'disabled' : '' }}
                                            @php $disabled = false; @endphp
                                            @if ($user->pink_ride == '0')
                                                @php $disabled = true; @endphp
                                            @elseif ($user->pink_ride == '')
                                                @if ($pinkRideSetting)
                                                    @if ($pinkRideSetting->female === '1' && $user->gender !== 'female')
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
                                            {{ in_array($postRidePage->features_option1->features_setting_id, explode('=', $ride->features)) ? 'checked' : '' }}
                                            class="w-4 h-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                        <label for="pink-ride"
                                            class="ml-2 text-gray-900 flex space-x-1">
                                            <span class="text-pink-500 font-medium
                                                @php $disabled = false; @endphp
                                                @if ($user->pink_ride == '0')
                                                    @php $disabled = true; @endphp
                                                @elseif ($user->pink_ride == '')
                                                    @if ($pinkRideSetting)
                                                        @if ($pinkRideSetting->female === '1' && $user->gender !== 'female')
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
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle-fill text-gray-400 peer" viewBox="0 0 16 16">
                                                    <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                                                </svg>
                                                <div
                                                  class="absolute right-28 md:right-24 tooltip -bottom-3 md:-top-14 lg:-top-16 group-hover:flex hidden peer-hover:flex"
                                                >
                                                    <div
                                                        role="tooltip"
                                                        class="absolute after:left-[4.8rem] features_tooltiptext -left-1/2 -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-blue-500  border border-blue-500 text-gray-600 rounded tooltip_width sm:w-[25rem] md:w-[30rem] px-4"
                                                    >
                                                        <p class="text-white font-semibold text-start text-sm lg:text-base">
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
                                @isset($postRidePage->features_option2)
                                    @php
                                        // Calculate the age based on the driver's date of birth
                                        $dob = \Carbon\Carbon::parse($user->dob);
                                        $age = $dob->diffInYears(\Carbon\Carbon::now());
                                    @endphp
                                    <div class="flex items-center">
                                        <input id="extra-care" type="checkbox" name="features[]" {{ $bookings_count > 0 ? 'disabled' : '' }}
                                            @php $disabled = false; @endphp
                                            @if ($user->folks_ride == '0')
                                                @php $disabled = true; @endphp
                                            @elseif ($user->folks_ride == '')
                                                @if ($setting)
                                                    @if ($setting->verfiy_phone === '1' && $user->phone_verified !== '1')
                                                        @php $disabled = true; @endphp
                                                    @elseif ($setting->verify_email === '1' && $user->email_verified !== '1')
                                                        @php $disabled = true; @endphp
                                                    @elseif ($setting->driver_license === '1' && $user->driver !== '1')
                                                        @php $disabled = true; @endphp
                                                    @elseif ($overallRating < $setting->average_rating || $age < $setting->driver_age)
                                                        @php $disabled = true; @endphp
                                                    @endif
                                                @endif
                                            @endif
                                            @if ($disabled)
                                                {{ 'disabled' }}
                                            @endif
                                            value="{{ $postRidePage->features_option2->features_setting_id }}"
                                            {{ in_array($postRidePage->features_option2->features_setting_id, explode('=', $ride->features)) ? 'checked' : '' }}
                                            class="w-4 h-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                        <label for="extra-care"
                                            class="ml-2 text-gray-900 flex space-x-1">
                                            <span class="text-green-500 font-medium
                                                @php $disabled = false; @endphp
                                                @if ($user->folks_ride == '0')
                                                    @php $disabled = true; @endphp
                                                @elseif ($user->folks_ride == '')
                                                    @if ($setting)
                                                        @if ($setting->verfiy_phone === '1' && $user->phone_verified !== '1')
                                                            @php $disabled = true; @endphp
                                                        @elseif ($setting->verify_email === '1' && $user->email_verified !== '1')
                                                            @php $disabled = true; @endphp
                                                        @elseif ($setting->driver_license === '1' && $user->driver !== '1')
                                                            @php $disabled = true; @endphp
                                                        @elseif ($overallRating < $setting->average_rating || $age < $setting->driver_age)
                                                            @php $disabled = true; @endphp
                                                        @endif
                                                    @endif
                                                @endif
                                                @if ($disabled)
                                                    {{ 'line-through' }}
                                                @endif
                                                ">
                                                {{ $postRidePage->features_option2->name }}
                                            </span>
                                            <div class="sups relative">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle-fill text-gray-400 peer" viewBox="0 0 16 16">
                                                    <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                                                </svg>
                                                <div
                                                    class="absolute right-28 md:right-24 tooltip -bottom-3 md:-top-14 lg:-top-16 group-hover:flex hidden peer-hover:flex"
                                                >
                                                    <div
                                                        role="tooltip"
                                                        class="absolute after:left-[4.8rem] features_tooltiptext -left-1/2 -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-blue-500  border border-blue-500 text-gray-600 rounded tooltip_width sm:w-[25rem] md:w-[30rem] px-4"
                                                    >
                                                        <p class="text-white font-semibold text-start text-sm lg:text-base">
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
                                @isset($postRidePage->features_option3)
                                    <div class="flex items-center">
                                        <input id="wi-fi" type="checkbox" name="features[]" value="{{ $postRidePage->features_option3->features_setting_id }}" {{ $bookings_count > 0 ? 'disabled' : '' }}
                                            {{ in_array($postRidePage->features_option3->features_setting_id, explode('=', $ride->features)) ? 'checked' : '' }}
                                            class="w-4 h-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                        <label for="wi-fi"
                                            class="ml-2 font-normal text-gray-900 flex space-x-1">
                                            <span>
                                                {{ $postRidePage->features_option3->name }}
                                            </span>
                                        </label>
                                    </div>
                                @endisset
                                @isset($postRidePage->features_option4)
                                    <div class="flex items-center">
                                        <input id="rating-passengers" type="checkbox" name="features[]" value="{{ $postRidePage->features_option4->features_setting_id }}" {{ $bookings_count > 0 ? 'disabled' : '' }}
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
                                        <input id="provide-babyseats" type="checkbox" name="features[]" value="{{ $postRidePage->features_option5->features_setting_id }}" {{ $bookings_count > 0 ? 'disabled' : '' }}
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
                                        <input id="passenger-provide" type="checkbox" name="features[]" value="{{ $postRidePage->features_option6->features_setting_id }}" {{ $bookings_count > 0 ? 'disabled' : '' }}
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
                                        <input id="take-children" type="checkbox" name="features[]" value="{{ $postRidePage->features_option7->features_setting_id }}" {{ $bookings_count > 0 ? 'disabled' : '' }}
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
                                        <input id="passenger-provide1" type="checkbox" name="features[]" value="{{ $postRidePage->features_option8->features_setting_id }}" {{ $bookings_count > 0 ? 'disabled' : '' }}
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
                                        <input id="bike-rack" type="checkbox" name="features[]" value="{{ $postRidePage->features_option9->features_setting_id }}" {{ $bookings_count > 0 ? 'disabled' : '' }}
                                            {{ in_array($postRidePage->features_option9->features_setting_id, explode('=', $ride->features)) ? 'checked' : '' }}
                                            class="w-4 h-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                        <label for="bike-rack"
                                            class="ml-2 font-normal text-gray-900 flex space-x-1">
                                            <span class="">
                                                {{ $postRidePage->features_option9->name }}
                                            </span>
                                        </label>
                                    </div>
                                @endisset
                                @isset($postRidePage->features_option10)
                                    <div class="flex items-center">
                                        <input id="ski-rack" type="checkbox" name="features[]" value="{{ $postRidePage->features_option10->features_setting_id }}" {{ $bookings_count > 0 ? 'disabled' : '' }}
                                            {{ in_array($postRidePage->features_option10->features_setting_id, explode('=', $ride->features)) ? 'checked' : '' }}
                                            class="w-4 h-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                        <label for="ski-rack"
                                            class="ml-2 font-normal text-gray-900 flex space-x-1">
                                            <span class="">
                                                {{ $postRidePage->features_option10->name }}
                                            </span>
                                        </label>
                                    </div>
                                @endisset
                                @isset($postRidePage->features_option11)
                                    <div class="flex items-center">
                                        <input id="winter-tires" type="checkbox" name="features[]" value="{{ $postRidePage->features_option11->features_setting_id }}" {{ $bookings_count > 0 ? 'disabled' : '' }}
                                            {{ in_array($postRidePage->features_option11->features_setting_id, explode('=', $ride->features)) ? 'checked' : '' }}
                                            class="w-4 h-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                        <label for="winter-tires"
                                            class="ml-2 font-normal text-gray-900 flex space-x-1">
                                            <span class="">
                                                {{ $postRidePage->features_option11->name }}
                                            </span>
                                        </label>
                                    </div>
                                @endisset
                                @isset($postRidePage->features_option12)
                                    <div class="flex items-center">
                                        <input id="air-conditioning" type="checkbox" name="features[]" value="{{ $postRidePage->features_option12->features_setting_id }}" {{ $bookings_count > 0 ? 'disabled' : '' }}
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
                                        <input id="heating" type="checkbox" name="features[]" value="{{ $postRidePage->features_option13->features_setting_id }}" {{ $bookings_count > 0 ? 'disabled' : '' }}
                                            {{ in_array($postRidePage->features_option13->features_setting_id, explode('=', $ride->features)) ? 'checked' : '' }}
                                            class="w-4 h-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                        <label for="heating"
                                            class="ml-2 font-normal text-gray-900 flex space-x-1">
                                            <span class="">
                                                {{ $postRidePage->features_option13->name }}
                                            </span>
                                        </label>
                                    </div>
                                @endisset
                                @isset($postRidePage->features_option14)
                                    <div class="flex items-center">
                                        <input id="heating" type="checkbox" name="features[]" value="{{ $postRidePage->features_option14->features_setting_id }}" {{ $bookings_count > 0 ? 'disabled' : '' }}
                                            {{ in_array($postRidePage->features_option14->features_setting_id, explode('=', $ride->features)) ? 'checked' : '' }}
                                            class="w-4 h-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                        <label for="heating"
                                            class="ml-2 font-normal text-gray-900 flex space-x-1">
                                            <span class="">
                                                {{ $postRidePage->features_option14->name }}
                                            </span>
                                        </label>
                                    </div>
                                @endisset
                                @isset($postRidePage->features_option15)
                                    <div class="flex items-center">
                                        <input id="heating" type="checkbox" name="features[]" value="{{ $postRidePage->features_option15->features_setting_id }}" {{ $bookings_count > 0 ? 'disabled' : '' }}
                                            {{ in_array($postRidePage->features_option15->features_setting_id, explode('=', $ride->features)) ? 'checked' : '' }}
                                            class="w-4 h-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                        <label for="heating"
                                            class="ml-2 font-normal text-gray-900 flex space-x-1">
                                            <span class="">
                                                {{ $postRidePage->features_option15->name }}
                                            </span>
                                        </label>
                                    </div>
                                @endisset
                                @isset($postRidePage->features_option16)
                                    <div class="flex items-center">
                                        <input id="heating" type="checkbox" name="features[]" value="{{ $postRidePage->features_option16->features_setting_id }}" {{ $bookings_count > 0 ? 'disabled' : '' }}
                                            {{ in_array($postRidePage->features_option16->features_setting_id, explode('=', $ride->features)) ? 'checked' : '' }}
                                            class="w-4 h-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                        <label for="heating"
                                            class="ml-2 font-normal text-gray-900 flex space-x-1">
                                            <span class="">
                                                {{ $postRidePage->features_option16->name }}
                                            </span>
                                        </label>
                                    </div>
                                @endisset
                            </div>
                            @if ($bookings_count > 0)
                                @php
                                    $features = !empty($ride->features) ? explode('=', $ride->features) : [];
                                @endphp
                                @foreach ($features as $feature)
                                    <input type="hidden" name="features[]" value="{{ $feature }}">
                                @endforeach
                            @endif
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
                                            <input id="standard" name="booking_type" type="radio" value="{{ $postRidePage->cancellation_policy_label1->features_setting_id }}"
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
                                    <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-primary text-gray-600 w-full md:w-1/2 rounded" >
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
                            <textarea id="more" rows="5" name="notes" {{ $bookings_count > 0 ? 'readonly' : '' }}
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
                                {!! str_replace('<ol>', '<ol class="list-decimal list-inside">', str_replace('<li>', '<li class="border-b border-gray-300 text-base lg:text-lg last:border-b-0 py-3">', $postRidePage->disclaimers_description)) !!}
                            @endisset
                        </div>
                    </div>
                </div>
                <div class="flex items-start my-4">
                    <input id="agree_checkbox" type="checkbox" name="agree_terms" value="1" {{ $bookings_count > 0 ? 'disabled' : '' }}
                        checked
                        class="w-4 h-4 text-blue-600 cursor-pointer bg-white mt-1 border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                    @if ($bookings_count > 0)
                        <input type="hidden" name="agree_terms" value="1">
                    @endif
                    <label for="agree_checkbox" class="ml-2 font-normal text-gray-900">
                        @isset($postRidePage->agree_terms_label)
                            {!! $postRidePage->agree_terms_label !!}
                        @endisset
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

@endsection

@section('script')

<script async
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAadtOhXUj_mb2QWOD1mCPYPRujBiQO4nE&libraries=places&callback=initMap">
</script>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<script>
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
    let autocomplete;
    function initMap() {
        autocomplete = new google.maps.places.Autocomplete(
            document.getElementById('from'),
            {
                types: ['establishment'],
                componentRestrictions: {'country' : ['AU']},
                fields: ['place_id', 'geometry', 'name']
            }
        );
        autocomplete = new google.maps.places.Autocomplete(
            document.getElementById('from'),
            {
                types: ['establishment'],
                componentRestrictions: {'country' : ['AU']},
                fields: ['place_id', 'geometry', 'name']
            }
        );
    }

    function swapLocations() {
        // Get the values of the "From" and "To" input fields
        const fromValue = document.getElementById('from').value;
        const toValue = document.getElementById('to').value;

        // Swap the values
        document.getElementById('from').value = toValue;
        document.getElementById('to').value = fromValue;
    }

    const dateInput = document.getElementById('dateInput');
    const timeInput = document.getElementById('timeInput');

    // Retrieve old values from Laravel's old() function
    const oldDate = '{{ old('date', $ride->date ? date('F d, Y', strtotime($ride->date)) : '') }}';
    const oldTime = '{{ old('time', $ride->time) }}';
    console.log(oldDate);

    // Initialize the date picker
    flatpickr(dateInput, {
        dateFormat: 'F d, Y',
        minDate: 'today',   // Restrict to future dates only
        defaultDate: oldDate || 'today', // Set default date to today
        disableMobile: true,
        onChange: function(selectedDates, dateStr, instance) {
            // Update minTime based on whether the selected date is today or a future date
            const isToday = instance.latestSelectedDateObj ? instance.latestSelectedDateObj.toDateString() === new Date().toDateString() : false;

            const minTime = isToday ? new Date().getHours() + ':' + (new Date().getMinutes()) : '00:00';

            // Update minTime dynamically without destroying the entire instance
            timeInput._flatpickr.set('minTime', minTime);

            // If the date is today, set the time input value to the current time
            if (isToday) {
                const currentTime = new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
                timeInput._flatpickr.setDate(currentTime, true, 'H:i');
            }
        },
    });

    // Initialize the date picker for time input
    flatpickr(timeInput, {
        enableTime: true,
        noCalendar: true,
        dateFormat: 'H:i',
        disableMobile: true,
        // minTime: oldTime || new Date().getHours() + ':' + (new Date().getMinutes()), // Set min time to current time
        defaultDate: oldTime || '',
        minuteIncrement: 1, // Set minute increment to 1
    });

    // Add a click event listener to the time input field
    timeInput.addEventListener('click', function() {
        debugger;
        // Check if the time input field is empty before setting the default time
        if (!timeInput._flatpickr.input.value) {
            // Set the default time to the current time when the field is clicked
            timeInput._flatpickr.setDate(new Date(), true, 'H:i');
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
    
    // Ensure all form fields are submitted, especially disabled/readonly ones
    document.getElementById('edit-ride-form').addEventListener('submit', function(e) {
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
                const existingHidden = Array.from(this.querySelectorAll(`input[type="hidden"][name="${name}"]`))
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
        }else if($("#priceData"+oldIndex+"").val() == ""){
            alert("Please select price spot");
            return;
        }
        var index = parseInt($("#rowCount").val() + 1);
        $.ajax({
            url: "{{ url('add-new-spots') }}",
            type: "POST",
            data: {
                index: index,
                _token: '{{ csrf_token() }}'
            },
            dataType: 'json',
            success: function(result) {
                $(".appendNewRow").append(result.spotHtml);
                $("#rowCount").val(index);
            }
        });
    }

    function removeRow(index, rideDetailId) {
        if(index != 1){
            if(rideDetailId != 0){
                $.ajax({
                    url: "{{ url('delete-spots') }}",
                    type: "POST",
                    data: {
                        rideDetailId: rideDetailId,
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: 'json',
                    success: function(result) {
                        if(result.status == "error"){
                            alert(result.message);
                        }
                    }
                });
            }

            $(".remove-row"+index+"").remove();
        }

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
            }
        });
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
            firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
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
