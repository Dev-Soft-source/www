@extends('layouts.template')

@section('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
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
            <div class="pt-1">
                <p class="text-gray-900">
                    {{ $editProfilePage->edit_profile_text ?? 'To be eligible for the "ProximaRide" and "Extra-Care Rides" , you must complete all fields below' }}
                </p>
            </div>
        @else
            <div class="">
                <h1 class="mb-0">Welcome onboard {{ $user->first_name }}</h1>
            </div>
            <div class="pb-4">
                @if ($user->gender == 'female' && in_array('Extra care rides', explode(';', $user->features)) && in_array('Pink rides', explode(';', $user->features)))
                    <p class="text-gray-900">You have selected the "ProximaRide" and the "My Extra-Care Rides"</p>
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
                    <p class="text-gray-900">You have selected the "My Extra-Care Rides"</p>
                    <p class="text-gray-900">To be eligible to use them, you must provide your complete address, upload
                        a valid Government-issued photo ID, and a proof of address, and you must fill in a small bio about
                        yourself</p>
                    <p class="text-gray-900">Our members will not see your address or ID, but they will see your bio</p>
                @else
                    <p class="text-gray-900">{{ $editProfilePage->edit_profile_text ?? 'To be eligible for the "ProximaRide" and "Extra-Care Rides" , you must complete all fields below' }}</p>
                @endif
            </div>
        @endif
            <p class="text-base md:text-lg font-medium text-red-500">(*) Indicates required fields</p>
        <form method="POST" action="{{ route('profile.update',$user->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4 mt-4">
                <div>
                    <label for="">{{ $editProfilePage->first_name_label ?? 'First name' }} <span class="text-red-500">*</span></label>
                    <input type="text" name="first_name" placeholder="{{ $editProfilePage->first_name_placeholder ?? 'Enter your first name' }}" value="{{ old('first_name', $user->first_name) }}" class=" block mt-1 border p-1.5 w-full text-base lg:text-lg rounded border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600 {{ $errors->has('first_name') ? 'border-red-500' : '' }}">
                    @error('first_name')
                      <div class="relative tooltip -bottom-4 group-hover:flex">
                        <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                            <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                        </div>
                      </div>
                    @enderror
                </div>
                <div>
                    <label for="">{{ $editProfilePage->last_name_label ?? 'Last name' }} <span class="text-red-500">*</span></label>
                    <input type="text" name="last_name" placeholder="{{ $editProfilePage->last_name_placeholder ?? 'Enter your last name' }}" value="{{ old('last_name', $user->last_name) }}" class=" block mt-1 border p-1.5 w-full text-base lg:text-lg rounded border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600 {{ $errors->has('last_name') ? 'border-red-500' : '' }}">
                    @error('last_name')
                      <div class="relative tooltip -bottom-4 group-hover:flex">
                        <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                            <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                        </div>
                      </div>
                    @enderror
                </div>

                <div>
                    <label for="">Email <span class="text-red-500">*</span></label>
                    <input type="text" name="email" value="{{ old('email', $user->email) }}" disabled class=" block mt-1 border p-1.5 w-full text-base lg:text-lg rounded border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600 {{ $errors->has('email') ? 'border-red-500' : '' }}">
                </div>

                <div>
                    <label for="">{{ $editProfilePage->dob_label ?? 'Date of birth' }} <span class="text-red-500">*</span></label>
                    <input type="text" id="dateInput" name="dob" value="{{ old('dob', $user->dob) ? \Carbon\Carbon::parse($user->dob)->format('F d, Y') : '' }}"
                        class="block mt-1 border p-1.5 w-full rounded text-base lg:text-lg border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600 placeholder:text-gray-900 {{ $errors->has('dob') ? 'border-red-500' : '' }}">
                    @error('dob')
                      <div class="relative tooltip -bottom-4 group-hover:flex">
                        <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                            <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                        </div>
                      </div>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="">{{ $editProfilePage->gender_label ?? 'Gender' }} <span class="text-red-500">*</span></label>
                    <div class="flex gap-4 md:justify-normal justify-between md:gap-x-8 items-center mt-2 p-1.5">
                        <div>
                            <input id="bordered-radio-1" type="radio" value="male" name="gender" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none" {{ old('gender', $user->gender) === 'male' ? 'checked' : '' }}>
                            <label for="">{{ $editProfilePage->male_label ?? 'Male' }}</label>
                        </div>
                        <div>
                            <input id="bordered-radio-1" type="radio" value="female" name="gender" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none" {{ old('gender', $user->gender) === 'female' ? 'checked' : '' }}>
                            <label for="">{{ $editProfilePage->female_label ?? 'Female' }}</label>
                        </div>
                        <div>
                            <input id="bordered-radio-1" type="radio" value="prefer not to say" name="gender" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none" {{ old('gender', $user->gender) === 'prefer not to say' ? 'checked' : '' }}>
                            <label for="">{{ $editProfilePage->prefer_no_to_say_label ?? 'Prefer not to say' }}</label>
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

                <div class="md:col-span-2">
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
                      <div class="relative tooltip -bottom-4 group-hover:flex">
                        <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                            <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                        </div>
                      </div>
                    @enderror
                </div>

                <div>
                    <label for="">{{ $editProfilePage->state_label ?? 'State/Province' }} <span class="text-red-500">*</span></label>
                    <select name="state" id="state-dropdown" class="bg-white block mt-1 text-base lg:text-lg border p-1.5 w-full rounded border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600 placeholder:text-gray-900 {{ $errors->has('country') ? 'border-red-500' : '' }}">
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
                    <label for="">{{ $editProfilePage->city_label ?? 'City' }} <span class="text-red-500">*</span></label>
                    <select name="city" id="city-dropdown" class="bg-white block text-base lg:text-lg mt-1 border p-1.5 w-full rounded border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600 placeholder:text-gray-900 {{ $errors->has('country') ? 'border-red-500' : '' }}">
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
                    <label for="">{{ $editProfilePage->address_label ?? 'Address' }}</label>
                    <input type="text" name="address" placeholder="{{ $editProfilePage->address_placeholder ?? 'Enter your address' }}" value="{{ old('address', $user->address) }}" class=" block mt-1 text-base lg:text-lg border p-1.5 w-full rounded border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600 {{ $errors->has('address') ? 'border-red-500' : '' }}">
                    @error('address')
                      <div class="relative tooltip -bottom-4 group-hover:flex">
                        <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                            <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                        </div>
                      </div>
                    @enderror
                </div>

                <div>
                    <label for="">{{ $editProfilePage->zip_label ?? 'Postal/Zip code' }} <span class="text-red-500">*</span></label>
                    <input type="text" name="zipcode" maxlength="7" value="{{ old('zipcode', $user->zipcode) }}" class=" block text-base lg:text-lg mt-1 border p-1.5 w-full rounded border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600 {{ $errors->has('zipcode') ? 'border-red-500' : '' }}">
                    @error('zipcode')
                      <div class="relative tooltip -bottom-4 group-hover:flex">
                        <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                            <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                        </div>
                      </div>
                    @enderror
                </div>

                <div class="col-span-2">
                    <label for="">Notifications</label>
                    <div class="flex flex-col sm:flex-col md:flex-row lg:flex-row items-center gap-6 mt-2">
                        <div class="flex items-center gap-2">
                            <input {{ $user->email_notification ? 'checked' : '' }} type="checkbox" id="email_notification" name="email_notification" >
                            <label for="email_notification">Email Notification</label>
                        </div>
                        <div class="flex items-center gap-2">
                            <input {{ $user->sms_notification ? 'checked' : '' }} type="checkbox" id="sms_notification" name="sms_notification" >
                            <label for="sms_notification">Sms Notification</label>
                        </div>
                    </div>

                </div>

                <div class= "mt-12">
                    <label for="">{{ $editProfilePage->govt_id_label ?? 'Government-issued photo ID' }}</label>
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
                                <span class="text-primary"> {{ $editProfilePage->choose_file_placeholder ?? 'choose a file' }}</span>
                            </p>
                            <p class="text-sm lg:text-base text-gray-900 font-normal">
                                {{ $editProfilePage->image_option_placeholder ?? '(Allowed formats: JPG, JPEG, PNG. 10MB max.)' }}
                            </p>
                        </div>
                        <input id="dropzone-file" name="government_issued_id" type="file" onchange="previewImage(this)" class="hidden" />
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

                <div class="md:col-span-2">
                    <label for="">{{ $editProfilePage->mini_bio_label ?? 'Mini bio' }} <span class="text-red-500">*</span></label>
                    <textarea id="message" rows="5" name="bio" class=" block mt-1 text-base lg:text-lg border p-1.5 w-full rounded border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600 {{ $errors->has('bio') ? 'border-red-500' : '' }}">{{ old('bio', $user->about) }}</textarea>
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
                        To be eligible for the rides you selected, you must enter the required information above
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

                <div class="md:col-span-2 flex justify-center">
                    <button type="submit" class="button-exp-fill w-32">{{ $editProfilePage->save_button_text ?? 'Save' }}</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@section('script')

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
    function previewImage(input) {
        const profileImage = document.getElementById('profile-image');
        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function(e) {
                profileImage.src = e.target.result;
            };

            reader.readAsDataURL(input.files[0]);
        }
    }

    const dateInput = document.getElementById('dateInput');
    // Initialize the date picker
    flatpickr(dateInput, {
            dateFormat: 'F d, Y', // Display format (e.g., "January 15, 2024")
            altInput: true,
            altFormat: 'F d, Y',
            maxDate: 'today', // Restrict to past dates only (for date of birth)
            disableMobile: true, // Disable mobile-friendly mode for consistent experience
            allowInput: true, // Allow manual input
            clickOpens: true, // Open calendar on click
            theme: 'default' // Use default theme
        });

    $(document).ready(function() {
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
