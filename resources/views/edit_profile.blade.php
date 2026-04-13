@extends('layouts.template')

@section('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        /* error tooltip css */
.tooltip-dob-error {
    position: relative;
    margin-top: 6px;
    padding: 8px 12px;
    background: #c75b5b;
    color: #fff;
    border-radius: 8px;
    font-family: 'Carlito', Calibri, Candara, Segoe, "Segoe UI", Optima, Arial, sans-serif;
    font-size: 16px;
    font-weight: 400;
    line-height: 1.4;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    /* display: inline-block; */
    transform-origin: top center;
    animation: tooltipErrorIn 0.28s ease-out forwards;
    /* min-width: max-content; */
    z-index: 9999;
}

.tooltip-dob-error::before {
    content: "";
    position: absolute;
    bottom: 100%;
    /* At the top of the tooltip */
    left: 50%;
    border-width: 6px;
    border-style: solid;
    border-color: transparent transparent #c75b5b transparent;
}
    </style>
@endsection

@section('content')

<div class="grid grid-cols-12 gap-4 md:container md:mx-auto  my-6 md:my-10 xl:my-14 px-4 xl:px-0">
    @include('layouts.inc.profile_sidebar')

    <div class="bg-white border rounded p-4 border-gray-200 w-full col-span-12 lg:col-span-9 shadow">
        @if ($user->first_name !== null && $user->first_name !== '' &&
            $user->last_name !== null && $user->last_name !== '' &&
            $user->gender !== null && $user->gender !== '' &&
            $user->dob !== null && $user->dob !== '' &&
            $user->country !== null && $user->country !== '' &&
            $user->address !== null && $user->address !== '' &&
            $user->state !== null && $user->state !== '' &&
            $user->city !== null && $user->city !== '' &&
            $user->zipcode !== null && $user->zipcode !== '' &&
            $user->about !== null && $user->about !== '' &&
            $user->government_issued_id !== null && $user->government_issued_id !== '')
            <div class="flex justify-between">
                <div class="pt-4">
                    <p class="text-gray-900">
                        {!! $editProfilePage->edit_profile_text ?? 'Manage your profile details below. A government-issued photo ID is required to <strong>book or post</strong> Pink Rides or Extra+ Rides; <strong>additionally</strong>, drivers posting these rides must provide their residential address.' !!}
                    </p>
                    <p class="text-base md:text-lg font-medium text-red-500">{{ $siteText['required_fields_label'] ?? '* Indicates required fields' }}</p>
                </div>
            </div>
        @else
            <div class="">
                <h1 class="mb-0">{{ $editProfilePage->welcome_onboard ?? 'Welcome onboard' }} {{ $user->first_name }}</h1>
            </div>
            <div class="pt-4">
                    @if ($user->gender == 'female' && in_array('Extra care rides', explode(';', $user->features)) && in_array('Pink rides', explode(';', $user->features)))
                        <p class="text-gray-900">You have selected the "ProximaRide" and the "My Extra+ Rides"</p>
                        <p class="text-gray-900">To be eligible to use them, you must provide your complete address, upload
                            a valid Government-issued photo ID, and a proof of address, and you must fill in a small bio about
                            yourself</p>
                        <p class="text-gray-900">Our members will not see your address or ID, but they will see your bio</p>
                    @elseif ($user->gender == 'female' && in_array('Pink rides', explode(';', $user->features)))
                        <p class="text-gray-900">You have selected the "ProximaRide"</p>
                        <p class="text-gray-900">To be eligible to use them, you must upload a valid Government-issued photo
                            ID*, and you must fill in a small bio about yourself</p>
                        <p class="text-gray-900">Our members will not see your address or ID, but they will see your bio</p>
                    @elseif (in_array('Extra care rides', explode(';', $user->features)))
                        <p class="text-gray-900">You have selected the "My Extra+ Rides"</p>
                        <p class="text-gray-900">To be eligible to use them, you must provide your complete address, upload
                            a valid Government-issued photo ID, and a proof of address, and you must fill in a small bio about
                            yourself</p>
                        <p class="text-gray-900">Our members will not see your address or ID, but they will see your bio</p>
                    @else
                        <p class="text-gray-900">{!! $editProfilePage->edit_profile_text ?? 'Manage your profile details below. A government-issued photo ID is required to <strong>book or post</strong> Pink Rides or Extra+ Rides; <strong>additionally</strong>, drivers posting these rides must provide their residential address.' !!}</p>
                    @endif
                <div class="flex justify-end">
                    <p class="text-base md:text-lg font-medium text-red-500 sm:flex-shrink-0">{{ $siteText['required_fields_label'] ?? '* Indicates required fields' }}</p>
                </div>
            </div>
            
        @endif
        <form method="POST" action="{{ route('profile.update',$user->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <div class="min-w-0">
                    <label for="">{{ $editProfilePage->first_name_label ?? 'First name' }} <span class="text-red-500">*</span></label>
                    <input type="text" name="first_name" placeholder="{{ $editProfilePage->first_name_placeholder ?? 'Enter your first name' }}" value="{{ old('first_name', $user->first_name) }}" class=" block mt-1 border p-1.5 w-full text-base lg:text-lg rounded border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600 {{ $errors->has('first_name') ? 'border-red-500' : '' }}">
                    @error('first_name')
                      <div class="tooltip-error shadow-lg">{{ $message }}</div>
                    @enderror
                </div>
                <div class="min-w-0">
                    <label for="">{{ $editProfilePage->last_name_label ?? 'Last name' }} <span class="text-red-500">*</span></label>
                    <input type="text" name="last_name" placeholder="{{ $editProfilePage->last_name_placeholder ?? 'Enter your last name' }}" value="{{ old('last_name', $user->last_name) }}" class=" block mt-1 border p-1.5 w-full text-base lg:text-lg rounded border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600 {{ $errors->has('last_name') ? 'border-red-500' : '' }}">
                    @error('last_name')
                      <div class="tooltip-error shadow-lg">{{ $message }}</div>
                    @enderror
                </div>

                <div class="min-w-0">
                    <label for="">{{ $editProfilePage->email_label ?? 'Email' }} <span class="text-red-500">*</span></label>
                    <input type="text" name="email" value="{{ old('email', $user->email) }}" disabled class=" block mt-1 border p-1.5 w-full text-base lg:text-lg rounded border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600 {{ $errors->has('email') ? 'border-red-500' : '' }}">
                </div>

                <div class="min-w-0">
                    <label for="">{{ $editProfilePage->dob_label ?? 'Date of birth' }} <span class="text-red-500">*</span></label>
                    <input type="text" id="dateInput" name="dob" value="{{ old('dob', $user->dob) ? \Carbon\Carbon::parse($user->dob)->format('Y-m-d') : '' }}"
                        placeholder="{{ $editProfilePage->dob_placeholder ?? 'Select date of birth' }}"
                        class="block mt-1 border p-1.5 w-full rounded text-base lg:text-lg border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600 placeholder:text-gray-900 {{ $errors->has('dob') ? 'border-red-500' : '' }}">
                    @error('dob')
                      <div class="tooltip-error shadow-lg">{{ $message }}</div>
                    @enderror
                    <div id="dob-under-18-error" class="hidden tooltip-dob-error shadow-lg">
                        {{ $step1Page->alert_age_limit_text ?? 'You must be at least 18 years old to join ProximaRide. Please check your date of birth or refer to our Terms of Service.' }}
                    </div>
                </div>

                <div class="min-w-0">
                    <label for="">{{ $editProfilePage->gender_label ?? 'Gender' }} <span class="text-red-500">*</span></label>
                    <div class="mt-2 flex flex-wrap gap-4">
                        <div class="inline-flex items-center gap-2 whitespace-nowrap">
                            <input id="bordered-radio-1" type="radio" value="male" name="gender" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none" {{ old('gender', $user->gender) === 'male' ? 'checked' : '' }}>
                            <label for="bordered-radio-1" class="leading-5">{{ $editProfilePage->male_label ?? 'Male' }}</label>
                        </div>
                        <div class="inline-flex items-center gap-2 whitespace-nowrap">
                            <input id="bordered-radio-2" type="radio" value="female" name="gender" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none" {{ old('gender', $user->gender) === 'female' ? 'checked' : '' }}>
                            <label for="bordered-radio-2" class="leading-5">{{ $editProfilePage->female_label ?? 'Female' }}</label>
                        </div>
                        <div class="inline-flex items-center gap-2 whitespace-nowrap">
                            <input id="bordered-radio-3" type="radio" value="prefer not to say" name="gender" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none" {{ old('gender', $user->gender) === 'prefer not to say' ? 'checked' : '' }}>
                            <label for="bordered-radio-3" class="leading-5">{{ $editProfilePage->prefer_no_to_say_label ?? 'Prefer not to say' }}</label>
                        </div>
                    </div>
                    @error('gender')
                      <div class="tooltip-error shadow-lg">{{ $message }}</div>
                    @enderror
                </div>

                <div class="min-w-0">
                    <label for="">{{ $editProfilePage->country_label ?? 'Country' }} <span class="text-red-500">*</span></label>
                    <select name="country" id="country-dropdown" class="bg-white text-base lg:text-lg block mt-1 border p-1.5 w-full rounded border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600 placeholder:text-gray-900 {{ $errors->has('country') ? 'border-red-500' : '' }}">
                        <option value="">{{ $editProfilePage->country_placeholder ?? 'Select country' }}</option>
                        @foreach ($countries as $country)
                            <option value="{{$country->id}}" {{ old('country', $user->country) == $country->id ? 'selected' : '' }}>
                                {{$country->name}}
                            </option>
                        @endforeach
                    </select>
                    @error('country')
                      <div class="tooltip-error shadow-lg">{{ $message }}</div>
                    @enderror
                </div>

                <div class="min-w-0">
                    <label for="">{{ $editProfilePage->state_label ?? 'State/Province' }} <span class="text-red-500">*</span></label>
                    <select name="state" id="state-dropdown" class="bg-white block mt-1 text-base lg:text-lg border p-1.5 w-full rounded border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600 placeholder:text-gray-900 {{ $errors->has('country') ? 'border-red-500' : '' }}">
                    </select>
                    @error('state')
                      <div class="tooltip-error shadow-lg">{{ $message }}</div>
                    @enderror
                </div>

                <div class="min-w-0">
                    <label for="">{{ $editProfilePage->city_label ?? 'City' }} <span class="text-red-500">*</span></label>
                    <select name="city" id="city-dropdown" class="bg-white block text-base lg:text-lg mt-1 border p-1.5 w-full rounded border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600 placeholder:text-gray-900 {{ $errors->has('country') ? 'border-red-500' : '' }}">
                    </select>
                    @error('city')
                      <div class="tooltip-error shadow-lg">{{ $message }}</div>
                    @enderror
                </div>

                <div class="min-w-0">
                    <label for="">{{ $editProfilePage->address_label ?? 'Address' }}</label>
                    <input type="text" name="address" placeholder="{{ $editProfilePage->address_placeholder ?? 'Enter your address' }}" value="{{ old('address', $user->address) }}" class=" block mt-1 text-base lg:text-lg border p-1.5 w-full rounded border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600 {{ $errors->has('address') ? 'border-red-500' : '' }}">
                    @error('address')
                      <div class="tooltip-error shadow-lg">{{ $message }}</div>
                    @enderror
                </div>

                <div class="min-w-0">
                    <label for="">{{ $editProfilePage->zip_label ?? 'Postal/Zip code' }} <span class="text-red-500">*</span></label>
                    <input type="text" name="zipcode" maxlength="7" value="{{ old('zipcode', $user->zipcode) }}" class=" block text-base lg:text-lg mt-1 border p-1.5 w-full rounded border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600 {{ $errors->has('zipcode') ? 'border-red-500' : '' }}">
                    @error('zipcode')
                      <div class="tooltip-error shadow-lg">{{ $message }}</div>
                    @enderror
                </div>

                <div class="md:col-span-2 min-w-0">
                    <label for="">{{ $editProfilePage->notification_label ?? 'Notifications' }}</label>
                    <div class="mt-2 flex flex-col sm:flex-row sm:flex-wrap items-start gap-4 sm:gap-6">
                        @php
                            $emailNotifChecked = old('email_notification') !== null ? (old('email_notification') === 'on' || old('email_notification') == 1) : ($user->email_notification == 1);
                            $smsNotifChecked = old('sms_notification') !== null ? (old('sms_notification') === 'on' || old('sms_notification') == 1) : ($user->sms_notification == 1);
                        @endphp
                        <div class="flex items-center gap-2">
                            <input type="checkbox" id="email_notification" name="email_notification" value="1" {{ $emailNotifChecked ? 'checked' : '' }}>
                            <label for="email_notification">{{ $editProfilePage->email_notification_label ?? 'Email Notification' }}</label>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="checkbox" id="sms_notification" name="sms_notification" value="1" {{ $smsNotifChecked ? 'checked' : '' }}>
                            <label for="sms_notification">{{ $editProfilePage->sms_notification_label ?? 'Sms Notification' }}</label>
                        </div>
                    </div>

                </div>

                <div class= "mt-12 text-center md:text-left md:col-span-2 min-w-0">                  
                    
                    <div class="bg-white rounded-lg overflow-hidden shadow-3xl">
                        <h3 class="text-2xl bg-primary text-white py-2 px-4">
                            {{ $editProfilePage->govt_id_label ?? 'Government-issued photo ID' }}
                        </h3>
                        <div class="bg-white p-4 space-y-3">
                              <div class="mt-2"><p>{{ $editProfilePage->govt_id_text ?? 'Upload a valid government-issued photo ID' }}</p></div>
                    <label for="dropzone-file"
                        class="flex flex-col items-center justify-center w-full h-72 border-2 border-gray-300 border-dashed rounded cursor-pointer bg-white hover:bg-gray-100">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6 p-4">
                            @if (session('uploaded_image'))
                                <img id="profile-image" class="w-40 h-40 object-contain mb-4 cursor-pointer" src="{{ asset('users_government_ids/' . session('uploaded_image')) }}" alt="Uploaded Image">
                            @elseif ($user->government_issued_id)
                                <img id="profile-image" class="w-40 h-40 object-contain mb-4 cursor-pointer" src="{{ $user->government_issued_id }}" alt="Uploaded Image">
                            @else
                                <img id="profile-image" class="w-40 h-40 object-contain mb-4 cursor-pointer" src="{{ asset('assets/image-placeholder.png')}}">
                            @endif
                            <p class="text-left flex text-sm lg:text-base text-gray-900">
                                <span> {{ $editProfilePage->image_placeholder ?? 'Drag and drop or' }}</span>
                                <!-- <span class="text-primary"> {{ $editProfilePage->choose_file_placeholder ?? 'choose a file' }}</span> -->
                            </p>
                            <p class="text-sm lg:text-base text-gray-900 font-normal">
                                (JPG, PNG, JPEG, and GIF. 10MB max.)
                            </p>
                        </div>
                        <input id="dropzone-file" name="government_issued_id" type="file" onchange="previewImage(this)" accept="image/*" class="hidden" />
                        @if (session('uploaded_image'))
                            <input type="hidden" name="existing_image" value="{{ session('uploaded_image') }}">
                        @elseif ($user->government_issued_id)
                            @php
                                $imageName = basename($user->government_issued_id);
                            @endphp
                            <input type="hidden" name="existing_image" value="{{ $imageName }}">
                        @endif
                    </label>
                    @error('government_issued_id')
                        @if ($message !== 'The image is not uploaded yet')
                            <div class="relative tooltip -bottom-4 group-hover:flex">
                                <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                    <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                </div>
                            </div>
                        @endif
                    @enderror
                    </div>
                    </div>
                  
               

                <div class="md:col-span-2 mt-8 min-w-0">
                    <label for="">{{ $editProfilePage->mini_bio_label ?? 'Mini bio' }} <span class="text-red-500">*</span></label>
                    <textarea id="message" rows="5" name="bio" class=" block mt-1 text-base lg:text-lg border p-1.5 w-full rounded border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600 {{ $errors->has('bio') ? 'border-red-500' : '' }}">{{ old('bio', $user->about) }}</textarea>
                    @error('bio')
                      <div class="tooltip-error shadow-lg">{{ $message }}</div>
                    @enderror
                </div>

                

                <div class="md:col-span-2 flex justify-center mt-4">
                    <button type="submit" class="button-exp-fill w-32">{{ $editProfilePage->save_button_text ?? 'Save' }}</button>
                </div>
            </div>
        </form>
    </div>
</div>

<x-image-size-error-modal
        title="Upload Error"
        button-label="{{ $siteText['close_btn_text'] ?? 'Close' }}"
        modal-border-class="modal-border1"
    />
@endsection

@section('script')

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
@php
    $flatpickrLocale = match(app()->getLocale()) {
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
@if($flatpickrLocale)
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/{{ $flatpickrLocale }}.js"></script>
@endif
<script>
    function previewImage(input) {
        const profileImage = document.getElementById('profile-image');
        if (input.files && input.files[0]) {
            
            if (input.files[0].size > ({{ env('MAX_IMAGE_SIZE', 10) }} * 1024 * 1024)) {
                input.value = '';
                showImageSizeErrorModal();
                return;
            }

            const reader = new FileReader();

            reader.onload = function(e) {
                profileImage.src = e.target.result;
            };

            reader.readAsDataURL(input.files[0]);
        }
    }

    const dateInput = document.getElementById('dateInput');
    let dobPickerUserOpened = false;

    // Check if DOB indicates user is at least 18.
    // Flatpickr altInput can produce strings like "February 01, 2026", so we parse those reliably.
    function isAtLeast18(dob) {
        if (dob == null || dob === '') return true;
                var birth = dob instanceof Date ? dob : new Date(String(dob).trim());
                if (isNaN(birth.getTime())) return true;

                var today = new Date();
                var age = today.getFullYear() - birth.getFullYear();
                var m = today.getMonth() - birth.getMonth();
                if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) age--;


                return age >= 18;
    }

    // Only show the tooltip after the user has focused/opened the picker.
    function setDobTooltipFromSelection(selectedDate) {
        
        
        var $dobError = $('#dob-under-18-error');
        if (selectedDate == null) {
            $dobError.addClass('hidden');
            return;
        }


        if (isAtLeast18(selectedDate)) {
            $dobError.addClass('hidden');
        } else {
            $dobError.removeClass('hidden');
        } 
    }

    const profileLocale = @json(app()->getLocale());
    const flatpickrLocaleKey = @json($flatpickrLocale);
    const flatpickrOptions = {
        dateFormat: 'Y-m-d',
        altInput: true,
        maxDate: 'today',
        disableMobile: true,
        allowInput: true,
        clickOpens: true,
        theme: 'default',
        onChange: function(selectedDates) {
            // Flatpickr provides a Date array
            setDobTooltipFromSelection(selectedDates && selectedDates.length ? selectedDates[0] : null);
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

    flatpickr(dateInput, flatpickrOptions);

    $(document).ready(function() {
        // Allow tooltip to show only after user has focused DOB (not on refresh)
        $('#dateInput').one('focus', function() { dobPickerUserOpened = true; });
        // Force-hide tooltip on load
        setTimeout(function() { $('#dob-under-18-error').addClass('hidden'); }, 0);

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
                    $('#state-dropdown').html('<option value="">Select State</option>');
                    $.each(result.states, function(key, value) {
                        var option = $('<option value="' + value.id + '">' + value.name + '</option>');
                        if (value.id == selectedState) {
                            option.prop('selected', true);
                        }
                        $("#state-dropdown").append(option);
                    });
                    $('#city-dropdown').html('<option value="">Select State First</option>');
                    loadCitiesByState(selectedState, selectedCity);
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
                    $('#city-dropdown').html('<option value="">Select City</option>');
                    $.each(result.cities, function(key, value) {
                        var option = $('<option value="' + value.id + '">' + value.name + '</option>');
                        if (value.id == selectedCity) {
                            option.prop('selected', true);
                        }
                        $("#city-dropdown").append(option);
                    });
                }
            });
        }

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
                    $('#state-dropdown').html('<option value="">Select State</option>');
                    $.each(result.states,function(key,value){
                        $("#state-dropdown").append('<option value="'+value.id+'">'+value.name+'</option>');
                    });
                    $('#city-dropdown').html('<option value="">Select State First</option>');
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
                    $('#city-dropdown').html('<option value="">Select City</option>');
                    $.each(result.cities,function(key,value){
                        $("#city-dropdown").append('<option value="'+value.id+'">'+value.name+'</option>');
                    });
                }
            });
        });
    });
</script>

@endsection
