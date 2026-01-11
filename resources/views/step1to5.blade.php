@extends('layouts.template')

@section('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endsection

@section('content')

<div class="mx-auto max-w-2xl lg:max-w-4xl my-14">
    <div class="bg-white rounded px-4 w-full col-span-12 md:col-span-9 mx-auto">
    <div class="bg-white border border-gray-100 pb-8 px-4 shadow rounded-md sm:px-10 my-4">
        <div class="pb-3 flex items-center justify-center">
            <h1 class="font-FuturaMdCnBT text-primary text-3xl md:text-4xl lg:text-5xl mb-4 mt-10">
                @isset($step1Page->main_heading)
                    {{ $step1Page->main_heading }}
                @endisset
            </h1>
        </div>
        <div class=" flex items-center justify-start">
            <p class="text-red-500">*
                @isset($step1Page->required_label)
                    {{ $step1Page->required_label }}
                @endisset
            </p>
        </div>

        <form method="POST" action="{{ route('step1to5.update',$user->id) }}">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4 mt-3">
                <div>
                    <label for="">
                        @isset($step1Page->first_name_label)
                            {{ $step1Page->first_name_label }}
                        @endisset
                        <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}" class="font-FuturaMdCnBT block mt-1 border p-1.5 w-full rounded text-base border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600 {{ $errors->has('first_name') ? 'border-red-500' : '' }}">
                    @error('first_name')
                      <div class="relative tooltip -bottom-4 group-hover:flex">
                        <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                            <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                        </div>
                      </div>
                    @enderror
                </div>
                <div>
                    <label for="">
                        @isset($step1Page->last_name_label)
                            {{ $step1Page->last_name_label }}
                        @endisset
                        <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}" class="font-FuturaMdCnBT block mt-1 border p-1.5 w-full rounded text-base border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600 {{ $errors->has('last_name') ? 'border-red-500' : '' }}">
                    @error('last_name')
                      <div class="relative tooltip -bottom-4 group-hover:flex">
                        <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                            <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                        </div>
                      </div>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="">
                        @isset($step1Page->gender_label)
                            {{ $step1Page->gender_label }}
                        @endisset
                        <span class="text-red-500">*</span>
                    </label>
                    <div class="flex flex-wrap gap-2 justify-normal md:gap-x-8 items-center mt-2 p-1.5">
                        <div>
                            <input id="bordered-radio-1" type="radio" value="male" name="gender" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none" {{ old('gender', $user->gender) === 'male' ? 'checked' : '' }}>
                            <label for="">
                                @isset($step1Page->male_option_label)
                                    {{ $step1Page->male_option_label }}
                                @endisset
                            </label>
                        </div>
                        <div>
                            <input id="bordered-radio-1" type="radio" value="female" name="gender" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none" {{ old('gender', $user->gender) === 'female' ? 'checked' : '' }}>
                            <label for="">
                                @isset($step1Page->female_option_label)
                                    {{ $step1Page->female_option_label }}
                                @endisset
                            </label>
                        </div>
                        <div>
                            <input id="bordered-radio-1" type="radio" value="prefer not to say" name="gender" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none" {{ old('gender', $user->gender) === 'prefer not to say' ? 'checked' : '' }}>
                            <label for="">
                                @isset($step1Page->prefer_option_label)
                                    {{ $step1Page->prefer_option_label }}
                                @endisset
                            </label>
                        </div>
                    </div>
                    @error('gender')
                      <div class="relative tooltip -bottom-4 group-hover:flex">
                        <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                            <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                        </div>
                      </div>
                    @enderror
                </div>

                <div>
                    <label for="">
                        @isset($step1Page->dob_label)
                            {{ $step1Page->dob_label }}
                        @endisset
                        <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="dateInput" name="dob" value="{{ old('dob', $user->dob) ? \Carbon\Carbon::parse($user->dob)->format('F d, Y') : '' }}"
                        class="font-FuturaMdCnBT block mt-1 border p-1.5 w-full rounded text-base border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600 {{ $errors->has('dob') ? 'border-red-500' : '' }}">
                    @error('dob')
                      <div class="relative tooltip -bottom-4 group-hover:flex">
                        <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                            <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                        </div>
                      </div>
                    @enderror
                </div>

                <div class="">
                    <label for="">
                        @isset($step1Page->country_label)
                            {{ $step1Page->country_label }}
                        @endisset
                        <span class="text-red-500">*</span>
                    </label>
                    <select name="country" id="country-dropdown" class="font-FuturaMdCnBT bg-white block mt-1 border p-1.5 w-full rounded text-base border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600 {{ $errors->has('country') ? 'border-red-500' : '' }}">
                        <option value="">Select your country</option>
                        @foreach ($countries as $country)
                            <option value="{{$country->id}}" {{ old('country', $user->country) == $country->id ? 'selected' : '' }}>
                                {{$country->name}}
                            </option>
                        @endforeach
                    </select>
                    @error('country')
                      <div class="relative tooltip -bottom-4 group-hover:flex">
                        <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                            <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                        </div>
                      </div>
                    @enderror
                </div>

                <div>
                    <label for="">
                        @isset($step1Page->state_label)
                            {{ $step1Page->state_label }}
                        @endisset
                        <span class="text-red-500">*</span>
                    </label>
                    <select name="state" id="state-dropdown" class="font-FuturaMdCnBT bg-white block mt-1 border p-1.5 w-full rounded text-base border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600">
                    </select>
                    @error('state')
                      <div class="relative tooltip -bottom-4 group-hover:flex">
                        <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                            <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                        </div>
                      </div>
                    @enderror
                </div>

                <div>
                    <label for="">
                        @isset($step1Page->city_label)
                            {{ $step1Page->city_label }}
                        @endisset
                        <span class="text-red-500">*</span>
                    </label>
                    <select name="city" id="city-dropdown" class="font-FuturaMdCnBT bg-white block mt-1 border p-1.5 w-full rounded text-base border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600">
                        <option value="">{{ $selectLocationSettingPage->select_state_first_label ?? '' }}</option>
                    </select>
                    @error('city')
                      <div class="relative tooltip -bottom-4 group-hover:flex">
                        <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                            <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                        </div>
                      </div>
                    @enderror
                </div>

                <div>
                    <label for="">
                        @isset($step1Page->zip_code_label)
                            {{ $step1Page->zip_code_label }}
                        @endisset
                        <span class="text-red-500">*</span>
                    </label>
                    <input type="text" maxlength="7" name="zipcode" value="{{ old('zipcode', $user->zipcode) }}" class="font-FuturaMdCnBT block mt-1 border p-1.5 w-full rounded text-base border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600 {{ $errors->has('zipcode') ? 'border-red-500' : '' }}">
                    @error('zipcode')
                      <div class="relative tooltip -bottom-4 group-hover:flex">
                        <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                            <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                        </div>
                      </div>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="">
                        @isset($step1Page->bio_label)
                            {{ $step1Page->bio_label }}
                        @endisset
                        <span class="text-red-500">*</span>
                    </label>
                    <textarea id="message" rows="5" name="bio" class="font-FuturaMdCnBT block mt-1 text-base border p-1.5 w-full rounded border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600 {{ $errors->has('bio') ? 'border-red-500' : '' }}" placeholder="{{ strip_tags($step1Page->bio_placeholder) }}">{{ old('bio', $user->about) }}</textarea>
                    @error('bio')
                      <div class="relative tooltip -bottom-4 group-hover:flex">
                        <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                            <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                        </div>
                      </div>
                    @enderror
                </div>

                {{-- @if ($errors->count() > 1)
                    <div class="md:col-span-2 mt-4 rounded-lg px-6 py-3 bg-red-100 text-gray-600" role="alert">
                        You must enter the required information above
                    </div>
                @elseif ($errors->count() === 1)
                    <div class="md:col-span-2 mt-4 rounded-lg px-6 py-3 bg-red-100 text-gray-600" role="alert">
                        @php
                            $errorKeys = array_keys($errors->messages());
                            $errorField = $errorKeys[0];
                        @endphp
                        Please fill in the {{ $errorField }} field
                    </div>
                @endif --}}

                <div class="md:col-span-2 flex justify-center mt-4">
                    <button type="submit" id="nextButton" class="button-exp-fill w-28 opacity-50 cursor-not-allowed" disabled>
                        @isset($step1Page->button_label)
                            {{ $step1Page->button_label }}
                        @endisset
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
</div>

@if(session('showModal'))

<div id="my-modal" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                    <div class="relative flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                      <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeModal()"></div>
                          <div class="relative animate__animated animate__fadeIn z-20 transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
                            <button onclick="closeModal()" class="absolute top-2 right-2 p-1 rounded-full hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                              </button>
                            <div class="bg-white p-6 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start justify-center">
                            <div
                                class="mx-auto h-16 w-16">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="4" stroke="currentColor" class="w-12 h-12 text-greenXS">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                            </div>
                        </div>
                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-center">
                            <h3 class="text-3xl text-center font-FuturaMdCnBT text-gray-900 mb-4" id="modal-title">Login
                                Successful</h3>
                            <div class="mt-2">
                                @php
                                    $user = session('user');
                                @endphp
                                <p class="text-lg text-center text-black">Hey {{ $user->first_name }}, nice to meet you.</p>
                                <p class="text-lg text-center text-black">Please complete your profile; it only takes a couple of
                                    minutes.</p>
                            </div>
                        </div>
                    </div>
                    <div class=" px-4 pb-6 pt-4 sm:flex sm:flex-row-reverse sm:px-6 justify-center">
                        <a href="{{ route('step1to5', ['lang' => $selectedLanguage->abbreviation]) }}"
                            class="inline-flex w-full justify-center rounded bg-greenXS px-3 py-2 font-FuturaMdCnBT text-lg text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-greenXS sm:ml-3 sm:w-24">Proceed</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endif

@endsection

@section('script')

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
     function closeModal() {
        document.getElementById('my-modal').style.display = 'none';
    }

    window.addEventListener("pageshow", function(event) {
        if (event.persisted) {
            window.location.reload();
        }
    });

    const oldDate = '{{ old('dob') }}';
    const dateInput = document.getElementById('dateInput');
    // Initialize the date picker
    flatpickr(dateInput, {
            dateFormat: 'F d, Y', // Display format (e.g., "January 15, 2024")
            altInput: true,
            altFormat: 'F d, Y',
            maxDate: 'today', // Restrict to past dates only (for date of birth)
            defaultDate: oldDate || '', // Set default date if available
            disableMobile: true, // Disable mobile-friendly mode for consistent experience
            allowInput: true, // Allow manual input
            clickOpens: true, // Open calendar on click
            theme: 'default' // Use default theme
        });

    function loadStatesByCountry(countryId, selectedState) {
        $.ajax({
            url: "{{ url('get-states-by-country') }}",
            type: "POST",
            data: {
                country_id: countryId,
                _token: '{{ csrf_token() }}'
            },
            dataType: 'json',
            success: function(result) {
                var selectState = "{{ $selectLocationSettingPage->select_state_label ?? '' }}";
                var selectCity = "{{ $selectLocationSettingPage->select_city_label ?? '' }}";
                var selectStateFirst = "{{ $selectLocationSettingPage->select_state_first_label ?? '' }}";
                $('#state-dropdown').html('<option value="">' + selectState + '</option>');
                $.each(result.states, function(key, value) {
                    var option = $('<option value="' + value.id + '">' + value.name + '</option>');
                    if (value.id == selectedState) {
                        option.prop('selected', true);
                        $('#city-dropdown').html('<option value="">' + selectCity + '</option>');
                    } else {
                        $('#city-dropdown').html('<option value="">' + selectStateFirst + '</option>');
                    }
                    $("#state-dropdown").append(option);
                });
                // loadCitiesByState(selectedState, selectedCity);
            }
        });
    }
    function loadCitiesByState(selectedState, selectedCity) {
        $.ajax({
            url: "{{ url('get-cities-by-state') }}",
            type: "POST",
            data: {
                state_id: selectedState,
                _token: '{{ csrf_token() }}'
            },
            dataType: 'json',
            success: function(result) {
                var selectCity = "{{ $selectLocationSettingPage->select_city_label ?? '' }}";
                $('#city-dropdown').html('<option value="">' + selectCity + '</option>');
                $.each(result.cities, function(key, value) {
                    var displayText = value.name;
                    if (value.state && value.state.abrv && value.state.country && value.state.country.name) {
                        displayText = value.name + ', ' + value.state.abrv + ', ' + value.state.country.name;
                    }
                    var option = $('<option value="' + value.id + '">' + displayText + '</option>');
                    if (value.id == selectedCity) {
                        option.prop('selected', true);
                    }
                    $("#city-dropdown").append(option);
                });
            }
        });
    }

    $(document).ready(function() {
        var countryId = $('#country-dropdown').val();
        if (countryId) {
            var selectedState = "{{ old('state', $user->state) }}";
            loadStatesByCountry(countryId, selectedState);
            if (selectedState) {
                var selectedCity = "{{ old('city', $user->city) }}";
                loadCitiesByState(selectedState, selectedCity);
            }
        }

        $('#country-dropdown').on('change', function() {
            var country_id = this.value;
            $("#state-dropdown").html('');
            $.ajax({
                url:"{{url('get-states-by-country')}}",
                type: "POST",
                data: {
                    country_id: country_id,
                    _token: '{{csrf_token()}}'
                },
                dataType : 'json',
                success: function(result){
                    var selectState = "{{ $selectLocationSettingPage->select_state_label ?? '' }}";
                    var selectCity = "{{ $selectLocationSettingPage->select_city_label ?? '' }}";
                    $('#state-dropdown').html('<option value="">' + selectState + '</option>');
                    $.each(result.states,function(key,value){
                        $("#state-dropdown").append('<option value="'+value.id+'">'+value.name+'</option>');
                    });
                    $('#city-dropdown').html('<option value="">' + selectCity + '</option>');
                }
            });
        });

        $('#state-dropdown').on('change', function() {
            var state_id = this.value;
            $("#city-dropdown").html('');
            $.ajax({
                url:"{{url('get-cities-by-state')}}",
                type: "POST",
                data: {
                    state_id: state_id,
                    _token: '{{csrf_token()}}'
                },
                dataType : 'json',
                success: function(result){
                    var selectCity = "{{ $selectLocationSettingPage->select_city_label ?? '' }}";
                    $('#city-dropdown').html('<option value="">' + selectCity + '</option>');
                    $.each(result.cities,function(key,value){
                        var displayText = value.name;
                        if (value.state && value.state.abrv && value.state.country && value.state.country.name) {
                            displayText = value.name + ', ' + value.state.abrv + ', ' + value.state.country.name;
                        }
                        $("#city-dropdown").append('<option value="'+value.id+'">'+displayText+'</option>');
                    });
                }
            });
        });
    });

    // Form validation for Step 1
    function validateStep1Form() {
        const firstName = document.querySelector('input[name="first_name"]').value.trim();
        const lastName = document.querySelector('input[name="last_name"]').value.trim();
        const gender = document.querySelector('input[name="gender"]:checked');
        const dob = document.querySelector('input[name="dob"]').value.trim();
        const country = document.querySelector('select[name="country"]').value;
        const state = document.querySelector('select[name="state"]').value;
        const city = document.querySelector('select[name="city"]').value;
        const zipcode = document.querySelector('input[name="zipcode"]').value.trim();
        const bio = document.querySelector('textarea[name="bio"]').value.trim();
        
        const nextButton = document.getElementById('nextButton');
        
        // Check if all required fields are filled
        const isValid = firstName && lastName && gender && dob && country && 
                       (state && state !== '0') && (city && city !== '0') && zipcode && bio;
        
        if (isValid) {
            nextButton.disabled = false;
            nextButton.classList.remove('opacity-50', 'cursor-not-allowed');
            nextButton.classList.add('opacity-100');
        } else {
            nextButton.disabled = true;
            nextButton.classList.add('opacity-50', 'cursor-not-allowed');
            nextButton.classList.remove('opacity-100');
        }
    }

    // Add event listeners to all form inputs for real-time validation
    document.addEventListener('DOMContentLoaded', function() {
        const formInputs = [
            'input[name="first_name"]',
            'input[name="last_name"]', 
            'input[name="gender"]',
            'input[name="dob"]',
            'select[name="country"]',
            'select[name="state"]',
            'select[name="city"]',
            'input[name="zipcode"]',
            'textarea[name="bio"]'
        ];
        
        formInputs.forEach(selector => {
            const elements = document.querySelectorAll(selector);
            elements.forEach(element => {
                element.addEventListener('input', validateStep1Form);
                element.addEventListener('change', validateStep1Form);
            });
        });
        
        // Initial validation check
        validateStep1Form();
    });
</script>

@endsection
