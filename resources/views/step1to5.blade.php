@extends('layouts.template')

@section('style')
    {{-- <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/ui-lightness/jquery-ui.css"> --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        
    </style>
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
                    <p class="text-red-500">
                        @isset($step1Page->required_label)
                            {{ $step1Page->required_label }}
                        @endisset
                    </p>
                </div>

                <form method="POST" action="{{ route('step1to5.update', $user->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4 mt-3">
                        <div>
                            <x-form.input 
                                label="{{ $step1Page->first_name_label }}"
                                name="first_name" 
                                :value="$user->first_name ?? ''"
                                required=true
                                type="text" 
                                class=""
                            />
                        </div>
                        <div>
                            <x-form.input 
                                label="{{ $step1Page->last_name_label }}"
                                name="last_name" 
                                :value="$user->last_name ?? ''"
                                required=true
                                type="text" 
                                class=""
                            />
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
                                    <input id="bordered-radio-1" type="radio" value="male" name="gender"
                                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none"
                                        {{ old('gender', $user->gender) === 'male' ? 'checked' : '' }}>
                                    <label for="">
                                        @isset($step1Page->male_option_label)
                                            {{ $step1Page->male_option_label }}
                                        @endisset
                                    </label>
                                </div>
                                <div>
                                    <input id="bordered-radio-1" type="radio" value="female" name="gender"
                                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none"
                                        {{ old('gender', $user->gender) === 'female' ? 'checked' : '' }}>
                                    <label for="">
                                        @isset($step1Page->female_option_label)
                                            {{ $step1Page->female_option_label }}
                                        @endisset
                                    </label>
                                </div>
                                <div>
                                    <input id="bordered-radio-1" type="radio" value="prefer not to say" name="gender"
                                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none"
                                        {{ old('gender', $user->gender) === 'prefer not to say' ? 'checked' : '' }}>
                                    <label for="">
                                        @isset($step1Page->prefer_option_label)
                                            {{ $step1Page->prefer_option_label }}
                                        @endisset
                                    </label>
                                </div>
                            </div>
                            @error('gender')
                                <div class="relative tooltip -bottom-4 group-hover:flex">
                                    <div role="tooltip"
                                        class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                                        <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                    </div>
                                </div>
                            @enderror
                        </div>

                        <div>
                            <x-form.input 
                                label="{{ $step1Page->dob_label }}"
                                name="dob" 
                                required=true
                                value="{{ old('dob', $user->dob) ? \Carbon\Carbon::parse($user->dob)->format('F d, Y') : '' }}"
                                type="text" 
                                class=""
                            />
                            
                        </div>

                        <div class="">
                            <label for="">
                                @isset($step1Page->country_label)
                                    {{ $step1Page->country_label }}
                                @endisset
                                <span class="text-red-500">*</span>
                            </label>
                            <select name="country" id="country-dropdown"
                                class="font-FuturaMdCnBT bg-white block border w-full rounded text-base border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600 {{ $errors->has('country') ? 'border-red-500' : '' }}">
                                <option value="">Select your country</option>
                                @foreach ($countries as $country)
                                    <option value="{{ $country->id }}"
                                        {{ $location['iso_code'] == $country->iso_code ? 'selected' : '' }}>
                                        {{ $country->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('country')
                                <div class="relative tooltip -bottom-4 group-hover:flex">
                                    <div role="tooltip"
                                        class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                                        <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                    </div>
                                </div>
                            @enderror
                        </div>

                        <div>
                            <label for="state-dropdown">
                                @isset($step1Page->state_label)
                                    {{ $step1Page->state_label }}
                                @endisset
                                <span class="text-red-500">*</span>
                            </label>
                            <select name="state" id="state-dropdown"
                                class="font-FuturaMdCnBT bg-white block border w-full rounded text-base border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600">
                            </select>
                            @error('state')
                                <div class="relative tooltip -bottom-4 group-hover:flex">
                                    <div role="tooltip"
                                        class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
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
                            <select name="city" id="city-dropdown"
                                class="form-control font-FuturaMdCnBT bg-white block border w-full rounded text-base border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600">
                                <option value="">{{ $selectLocationSettingPage->select_state_first_label ?? '' }}
                                </option>
                            </select>
                            @error('city')
                                <div class="relative tooltip -bottom-4 group-hover:flex">
                                    <div role="tooltip"
                                        class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                                        <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                    </div>
                                </div>
                            @enderror
                        </div>

                        <div>
                            <x-form.input 
                                label="{{ $step1Page->zip_code_label }}"
                                name="zipcode" 
                                required=true
                                value="{{ old('zipcode', $user->zipcode) }}"
                                type="text" 
                                class=""
                            />
                        </div>

                        <div class="md:col-span-2">
                            <label for="">
                                @isset($step1Page->bio_label)
                                    {{ $step1Page->bio_label }}
                                @endisset
                                <span class="text-red-500">*</span>
                            </label>
                            <textarea id="message" rows="5" name="bio"
                                class="font-FuturaMdCnBT block text-base border w-full rounded border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600 {{ $errors->has('bio') ? 'border-red-500' : '' }}"
                                placeholder="{{ strip_tags($step1Page->bio_placeholder) }}">{{ old('bio', $user->about) }}</textarea>
                            @error('bio')
                                <div class="relative tooltip -bottom-4 group-hover:flex">
                                    <div role="tooltip"
                                        class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
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
                            <button type="submit" id="nextButton"
                                class="button-exp-fill min-w-[7.5rem] opacity-50 cursor-not-allowed" disabled>
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

    @if (session('showModal'))
        <div id="my-modal" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div
                    class="relative flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeModal()"></div>
                    <div
                        class="relative animate__animated animate__fadeIn z-20 transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
                        <button onclick="closeModal()"
                            class="absolute top-2 right-2 p-1 rounded-full hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                        <div class="bg-white p-6 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start justify-center">
                                <div class="mx-auto h-16 w-16">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="4" stroke="currentColor" class="w-12 h-12 text-greenXS">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                    </svg>
                                </div>
                            </div>
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-center">
                                <h3 class="text-3xl text-center font-FuturaMdCnBT text-gray-900 mb-4" id="modal-title">
                                    Login
                                    Successful</h3>
                                <div class="mt-2">
                                    @php
                                        $user = session('user');
                                    @endphp
                                    <p class="text-lg text-center text-black">Hey {{ $user->first_name }}, nice to meet
                                        you.</p>
                                    <p class="text-lg text-center text-black">Please complete your profile; it only takes a
                                        couple of
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
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        // Load jQuery and jQuery UI if not already loaded
        if (typeof jQuery === 'undefined') {
            document.write('<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"><\/script>');
        }
        if (typeof jQuery !== 'undefined' && typeof jQuery.ui === 'undefined') {
            document.write('<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"><\/script>');
        }
    </script>

    <script>
        (function() {
            // Configuration constants
            const CONFIG = {
                selectedState: "{{ old('state', $user->state) }}",
                selectedCity: "{{ old('city', $user->city) }}",
                urls: {
                    statesByCountry: "{{ url('get-states-by-country') }}",
                    citiesByState: "{{ url('get-cities-by-state') }}"
                },
                labels: {
                    selectState: "{{ $selectLocationSettingPage->select_state_label ?? '' }}",
                    selectCity: "{{ $selectLocationSettingPage->select_city_label ?? '' }}",
                    selectStateFirst: "{{ $selectLocationSettingPage->select_state_first_label ?? '' }}"
                },
                csrfToken: '{{ csrf_token() }}'
            };

            // Initialize Flatpickr
            flatpickr("#dob", {
                dateFormat: 'F d, Y',
            });

            // Modal functions
            function closeModal() {
                const modal = document.getElementById('my-modal');
                if (modal) {
                    modal.style.display = 'none';
                }
            }
            window.closeModal = closeModal;

            // Handle browser back/forward cache
            window.addEventListener("pageshow", function(event) {
                if (event.persisted) {
                    window.location.reload();
                }
            });

            // Helper function to format city display text
            function formatCityDisplay(city) {
                if (city.state && city.state.abrv && city.state.country && city.state.country.name) {
                    return `${city.name}, ${city.state.abrv}, ${city.state.country.name}`;
                }
                return city.name;
            }

            // Load states by country
            function loadStatesByCountry(countryId, preselectedState = null, resetCity = true) {
                if (!countryId) {
                    $('#state-dropdown').html(`<option value="">${CONFIG.labels.selectState}</option>`);
                    if (resetCity) {
                        $('#city-dropdown').html(`<option value="">${CONFIG.labels.selectStateFirst}</option>`);
                    }
                    return;
                }

                $.ajax({
                    url: CONFIG.urls.statesByCountry,
                    type: "POST",
                    data: {
                        country_id: countryId,
                        _token: CONFIG.csrfToken
                    },
                    dataType: 'json',
                    success: function(result) {
                        let stateOptions = `<option value="">${CONFIG.labels.selectState}</option>`;
                        
                        $.each(result.states, function(key, value) {
                            const selected = preselectedState && value.id == preselectedState ? 'selected' : '';
                            stateOptions += `<option value="${value.id}" ${selected}>${value.name}</option>`;
                        });
                        
                        $('#state-dropdown').html(stateOptions);
                        
                        if (resetCity) {
                            const cityLabel = preselectedState ? CONFIG.labels.selectCity : CONFIG.labels.selectStateFirst;
                            $('#city-dropdown').html(`<option value="">${cityLabel}</option>`);
                        }
                        
                        // Auto-load cities if state is preselected
                        if (preselectedState) {
                            loadCitiesByState(preselectedState, CONFIG.selectedCity);
                        }
                    },
                    error: function() {
                        $('#state-dropdown').html(`<option value="">${CONFIG.labels.selectState}</option>`);
                        if (resetCity) {
                            $('#city-dropdown').html(`<option value="">${CONFIG.labels.selectStateFirst}</option>`);
                        }
                    }
                });
            }

            // Load cities by state
            function loadCitiesByState(stateId, preselectedCity = null) {
                if (!stateId) {
                    $('#city-dropdown').html(`<option value="">${CONFIG.labels.selectStateFirst}</option>`);
                    return;
                }

                $.ajax({
                    url: CONFIG.urls.citiesByState,
                    type: "POST",
                    data: {
                        state_id: stateId,
                        _token: CONFIG.csrfToken
                    },
                    dataType: 'json',
                    success: function(result) {
                        let cityOptions = `<option value="">${CONFIG.labels.selectCity}</option>`;
                        
                        $.each(result.cities, function(key, value) {
                            const displayText = formatCityDisplay(value);
                            const selected = preselectedCity && value.id == preselectedCity ? 'selected' : '';
                            cityOptions += `<option value="${value.id}" ${selected}>${displayText}</option>`;
                        });
                        
                        $('#city-dropdown').html(cityOptions);

                        validateStep1Form();
                    },
                    error: function() {
                        $('#city-dropdown').html(`<option value="">${CONFIG.labels.selectCity}</option>`);
                    }
                });
            }

            // Form validation
            function validateStep1Form() {
                const formData = {
                    firstName: $('input[name="first_name"]').val().trim(),
                    lastName: $('input[name="last_name"]').val().trim(),
                    gender: $('input[name="gender"]:checked').length > 0,
                    dob: $('input[name="dob"]').val().trim(),
                    country: $('select[name="country"]').val(),
                    state: $('select[name="state"]').val(),
                    city: $('select[name="city"]').val(),
                    zipcode: $('input[name="zipcode"]').val().trim(),
                    bio: $('textarea[name="bio"]').val().trim()
                };

                const isValid = formData.firstName && 
                    formData.lastName && 
                    formData.gender && 
                    formData.dob && 
                    formData.country && 
                    formData.state && 
                    formData.city && 
                    formData.zipcode && 
                    formData.bio;

                const $nextButton = $('#nextButton');
                
                if (isValid) {
                    $nextButton.prop('disabled', false)
                        .removeClass('opacity-50 cursor-not-allowed')
                        .addClass('opacity-100');
                } else {
                    $nextButton.prop('disabled', true)
                        .addClass('opacity-50 cursor-not-allowed')
                        .removeClass('opacity-100');
                }
            }

            // Initialize when DOM is ready
            $(document).ready(function() {
                // Initialize location dropdowns
                const countryId = $('#country-dropdown').val();
                if (countryId) {
                    loadStatesByCountry(countryId, CONFIG.selectedState, false);
                }

                // Country dropdown change handler
                $('#country-dropdown').on('change', function() {
                    const countryId = $(this).val();
                    loadStatesByCountry(countryId, null, true);
                    validateStep1Form();
                });

                // State dropdown change handler
                $('#state-dropdown').on('change', function() {
                    const stateId = $(this).val();
                    loadCitiesByState(stateId);
                    validateStep1Form();
                });

                // City dropdown change handler
                $('#city-dropdown').on('change', function() {
                    validateStep1Form();
                });

                // Form validation event listeners
                const formInputs = [
                    'input[name="first_name"]',
                    'input[name="last_name"]',
                    'input[name="gender"]',
                    'input[name="dob"]',
                    'input[name="zipcode"]',
                    'textarea[name="bio"]'
                ];

                $(formInputs.join(',')).on('input change', validateStep1Form);

                // Initial validation
                validateStep1Form();
            });
        })();
    </script>
@endsection
