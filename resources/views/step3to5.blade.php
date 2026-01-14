@extends('layouts.template')

@section('content')

<div class="mx-auto max-w-2xl lg:max-w-4xl my-6">
    <div class="bg-white rounded p-4 w-full col-span-12 md:col-span-9 mx-auto">
        <div class="bg-white border border-gray-100 pb-8 px-4 shadow rounded-md sm:px-6 my-4">
            <div class="py-2 flex items-center justify-center">
                <h1 class="font-FuturaMdCnBT text-primary text-3xl md:text-4xl lg:text-5xl mb-4 mt-10">
                    @isset($step3Page->main_heading)
                        {{ $step3Page->main_heading }}
                    @endisset
                </h1>
            </div>
            {{-- <h4 class="mb-4 text-red-500 font-FuturaMdCnBT text-center">
                {{ $step3Page->sub_heading ?? "Don’t forget your driver’s license below" }}
            </h4> --}}
            <p for="" class=" text-black mt-2">
                {!! $step3Page->main_label ?? "If you are signing up as a driver, please note that to be eligible to post ProximaRide and Extra-Care Rides, you must state your vehicle details on every ride, and must upload a valid driver’s license. " !!}
            </p>
            <p for="" class=" text-black mt-8">
                {!! $step3Page->sub_main_label ?? "If you intend to use ProximaRide as a passenger only, then this point is not applicable to you. You may “Skip” it" !!}
            </p>
            <div class="pb-2 flex items-center justify-start mt-3">
                <p class="text-red-500">
                    @isset($step3Page->required_label)
                        {{ $step3Page->required_label }}
                    @endisset
                </p>
            </div>
            <form class="rounded-lg" method="POST" action="{{ route('step3to5.store',$user->id) }}" enctype="multipart/form-data" id="step3Form">
                @csrf

                <div class="bg-primary text-white rounded-t-lg font-medium text-xl flex items-center justify-center space-x-2 p-4 font-FuturaMdCnBT">
                    {{ $step3Page->vehicle_section_heading ?? "Step 1 of 2 - Your vehicle information" }}
                </div>  

                <div class="bg-white rounded-b-lg overflow-hidden shadow-3xl grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4 p-4">
                    <div>
                        <label for="" class="font-FuturaMdCnBT">
                            @isset($step3Page->make_label)
                                {{ $step3Page->make_label }}
                            @endisset
                            <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="make" value="{{ old('make') }}"
                            @isset($step3Page->make_placeholder)
                                placeholder="{{ $step3Page->make_placeholder }}"
                            @endisset
                            class="font-FuturaMdCnBT block mt-1 border p-1.5 w-full rounded text-base border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600">
                        @error('make')
                        <div class="relative tooltip -bottom-4 group-hover:flex">
                            <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                            </div>
                        </div>
                        @enderror
                    </div>
                    <div>
                        <label for="" class="font-FuturaMdCnBT">
                            @isset($step3Page->model_label)
                                {{ $step3Page->model_label }}
                            @endisset
                            <span class="text-red-500">*</span>
                        </label>
                        <input placeholder="{{$step3Page->model_placeholder}}" type="text" name="model" value="{{ old('model') }}"
                            @isset($step3Page->model_placeholder)
                                placeholder="{{ $step3Page->model_placeholder }}"
                            @endisset
                            class="font-FuturaMdCnBT block mt-1 border p-1.5 w-full rounded text-base border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600">
                        @error('model')
                        <div class="relative tooltip -bottom-4 group-hover:flex">
                            <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                            </div>
                        </div>
                        @enderror
                    </div>
                    <div>
                        <label for="" class="font-FuturaMdCnBT">
                            @isset($step3Page->vehicle_type_label)
                                {{ $step3Page->vehicle_type_label }}
                            @endisset
                            <span class="text-red-500">*</span>
                        </label>
                        <select id="type" name="type"
                            class="font-FuturaMdCnBT block mt-1 border p-1.5 w-full italic rounded text-base border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600">

                            <option value=""
                                {{ old('type') === '' ? 'selected' : '' }}>
                                {{ $step3Page->vehicle_type_placeholder ?? "Select" }}
                            </option>
                            <option value="{{ $step3Page->vehicle_type_convertible_value ?? 'Convertable' }}"
                                {{ old('type') === ($step3Page->vehicle_type_convertible_value ?? 'Convertable') ? 'selected' : '' }}>
                                {{ $step3Page->vehicle_type_convertible_text ?? "Convertable"}}
                            </option>
                            <option value="{{ $step3Page->vehicle_type_coupe_value ??'Coupe' }}"
                                {{ old('type') === ($step3Page->vehicle_type_coupe_value ??'Coupe') ? 'selected' : '' }}>
                                {{ $step3Page->vehicle_type_coupe_text ?? "Coupe"}}
                            </option>
                            <option value="{{ $step3Page->vehicle_type_hatchback_value ??'Hatchback' }}"
                                {{ old('type') === ($step3Page->vehicle_type_hatchback_value ??'Hatchback') ? 'selected' : '' }}>
                                {{ $step3Page->vehicle_type_hatchback_text ?? "Hatchback"}}
                            </option>
                            <option value="{{ $step3Page->vehicle_type_minivan_value ??'Minivan' }}"
                                {{ old('type') === ($step3Page->vehicle_type_minivan_value ??'Minivan') ? 'selected' : '' }}>
                                {{ $step3Page->vehicle_type_minivan_text ?? "Minivan"}}
                            </option>
                            <option value="{{ $step3Page->vehicle_type_sedan_value ??'Sedan' }}"
                                {{ old('type') === ($step3Page->vehicle_type_sedan_value ??'Sedan') ? 'selected' : '' }}>
                                {{ $step3Page->vehicle_type_sedan_text ?? "Sedan"}}
                            </option>
                            <option value="{{ $step3Page->vehicle_type_station_wagon_value }}"
                                {{ old('type') === ($step3Page->vehicle_type_station_wagon_value ??'Station wagon') ? 'selected' : '' }}>
                                {{ $step3Page->vehicle_type_station_wagon_text ?? "Station wagon"}}
                            </option>
                            <option value="{{ $step3Page->vehicle_type_suv_value ??'SUV' }}"
                                {{ old('type') === ($step3Page->vehicle_type_suv_value ??'SUV') ? 'selected' : '' }}>
                                {{ $step3Page->vehicle_type_suv_text ?? "SUV"}}
                            </option>
                            <option value="{{ $step3Page->vehicle_type_truck_value ??'Truck' }}"
                                {{ old('type') === ($step3Page->vehicle_type_truck_value ??'Truck') ? 'selected' : '' }}>
                                {{ $step3Page->vehicle_type_truck_text ?? "Truck"}}
                            </option>
                            <option value="{{ $step3Page->vehicle_type_van_value ??'Van' }}"
                                {{ old('type') === ($step3Page->vehicle_type_van_value ??'Van') ? 'selected' : '' }}>
                                {{ $step3Page->vehicle_type_van_text ?? "Van"}}
                            </option>
                        </select>
                        @error('type')
                        <div class="relative tooltip -bottom-4 group-hover:flex">
                            <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                            </div>
                        </div>
                        @enderror
                    </div>

                    <div>
                        <label for="" class="font-FuturaMdCnBT">
                            @isset($step3Page->color_label)
                                {{ $step3Page->color_label }}
                            @endisset
                            <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="color" value="{{ old('color') }}" class="font-FuturaMdCnBT block mt-1 border p-1.5 w-full rounded text-base border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600">
                        @error('color')
                        <div class="relative tooltip -bottom-4 group-hover:flex">
                            <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                            </div>
                        </div>
                        @enderror
                    </div>

                    <div>
                        <label for="" class="font-FuturaMdCnBT">
                            @isset($step3Page->license_label)
                                {{ $step3Page->license_label }}
                            @endisset
                            <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="liscense_no" value="{{ old('liscense_no') }}" class="font-FuturaMdCnBT block mt-1 border p-1.5 w-full rounded text-base border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600">
                        @error('liscense_no')
                        <div class="relative tooltip -bottom-4 group-hover:flex">
                            <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                            </div>
                        </div>
                        @enderror
                    </div>

                    <div>
                        <label for="" class="font-FuturaMdCnBT">
                            @isset($step3Page->year_label)
                                {{ $step3Page->year_label }}
                            @endisset
                            <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="year" value="{{ old('year') }}" class="font-FuturaMdCnBT block mt-1 border p-1.5 w-full rounded text-base border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600">
                        @error('year')
                        <div class="relative tooltip -bottom-4 group-hover:flex">
                            <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                            </div>
                        </div>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                            <label for="" class="mb-2 block mt-4 font-FuturaMdCnBT">
                                @isset($step3Page->photo_label)
                                    {{ $step3Page->photo_label }}
                                @endisset
                            </label>
                            {{-- <div class="flex flex-col items-center justify-center w-full">
                                <label for="dropzone-file h-52"
                                    class="flex flex-col items-center justify-center w-full h-auto border-2 border-gray-300 border-dashed rounded cursor-pointer bg-white hover:bg-gray-100">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        @if ($user->car_image != '')
                                        <img id="profile-image" src="{{ $user->car_image }}" class="w-32 h-32 rounded-full mb-4">
                                        @else
                                            <img id="profile-image" class="w-12 h-12 object-contain mb-4" src="{{ asset('assets/image-placeholder.png')}}">
                                        @endif
                                        <p class="text-base lg:text-lg text-gray-900">Car Photo. 
                                            <span class="font-semibold text-primary">Choose file</span>
                                        </p>
                                        <p class="text-sm lg:text-base text-gray-900 font-normal text-center">
                                            (Allowed formats: JPG, JPEG. PNG, and GIF. 10MB max.)
                                        </p>
                                    </div>
                                    <input id="dropzone-file" name="image" type="file" onchange="previewImage(this)" class="hidden" />
                                    @if (session('uploaded_image'))
                                        <input type="hidden" name="existing_image" value="{{ session('uploaded_image') }}">
                                    @endif
                                </label>
                            </div> --}}
                            {{-- <div class="md:col-span-2"> --}}
                                <label for="dropzone-file"
                                    class="flex flex-col items-center justify-center w-full h-auto border-2 border-gray-300 border-dashed rounded cursor-pointer bg-white hover:bg-gray-100 font-FuturaMdCnBT">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6 p-4">
                                    <img id="profile-image" class="w-12 h-12 object-contain mb-3 cursor-pointer" src="{{ asset('assets/image-placeholder.png')}}">
                                    <p class="text-sm lg:text-lg text-gray-900">Vehicle photo
                                        <!-- <span class="text-primary"> 
                                            @isset($step3Page->mobile_photo_choose_file_label)
                                                {{ $step3Page->mobile_photo_choose_file_label }}
                                            @endisset    
                                        </span> -->
                                    </p>
                                    <p class="text-sm lg:text-base text-gray-900 ">
                                        @isset($step3Page->photo_detail_label)
                                            {{ $step3Page->photo_detail_label }}
                                        @endisset
                                    </p>
                                </div>
                                    <input id="dropzone-file" name="image" type="file" onchange="previewImage(this)" class="hidden" />
                                </label>
                                    @error('image')
                                    <div class="relative tooltip -bottom-4 group-hover:flex">
                                        <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                            <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                        </div>
                                    </div>
                                @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="car_type" class="text-gray-900 mb-2 font-FuturaMdCnBT">
                            @isset($step3Page->fuel_label)
                                {{ $step3Page->fuel_label }}
                            @endisset
                            <span class="text-red-500">*</span>
                        </label>
                        <div class="gap-4 md:gap-6 flex items-center mt-4">
                            <div class="flex items-center space-x-1.5 lg:space-x-2 mb-2">
                                <input id="" name="car_type" type="radio" value="Electric"
                                    {{ old('car_type') == 'Electric' ? 'checked' : '' }}
                                    class="h-4 w-4 border-gray-300 bg-gray-200 cursor-pointer text-sky-600 focus:ring-sky-600">
                                <label for="" class="block text-gray-900">
                                    @isset($step3Page->electric_option_label)
                                        {{ $step3Page->electric_option_label }}
                                    @endisset
                                </label>
                            </div>
                            <div class="flex items-center space-x-1.5 lg:space-x-2 mb-2">
                                <input id="" name="car_type" type="radio" value="Hybrid"
                                    {{ old('car_type') == 'Hybrid' ? 'checked' : '' }}
                                    class="h-4 w-4 border-gray-300 bg-gray-200 cursor-pointer text-sky-600 focus:ring-sky-600">
                                <label for="" class="block text-gray-900">
                                    @isset($step3Page->hybrid_option_label)
                                        {{ $step3Page->hybrid_option_label }}
                                    @endisset
                                </label>
                            </div>
                            <div class="flex items-center space-x-1.5 lg:space-x-2 mb-2">
                                <input id="" name="car_type" type="radio" value="Gas" checked
                                    {{ old('car_type') == 'Gas' ? 'checked' : '' }}
                                    class="h-4 w-4 border-gray-300 bg-gray-200 cursor-pointer text-sky-600 focus:ring-sky-600">
                                <label for="" class="block text-gray-900">
                                    @isset($step3Page->gas_option_label)
                                        {{ $step3Page->gas_option_label }}
                                    @endisset
                                </label>
                            </div>
                        </div>
                        @error('car_type')
                        <div class="relative tooltip -bottom-4 group-hover:flex">
                            <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                            </div>
                        </div>
                        @enderror
                        <!-- <div class="mt-4 flex justify-center space-x-2 md:col-span-2">
                            {{-- <a href="{{ route('step5to5', ['lang' => $selectedLanguage->abbreviation]) }}" class="button-exp-fill w-32">
                                @isset($step3Page->skip_button_label)
                                    {{ $step3Page->skip_button_label }}
                                @endisset
                            </a> --}}
                            <button type="submit" name='action' value='skip_vehicle_info' class="button-exp-fill w-36">
                                @isset($step3Page->skip_vehicle_info)
                                    {{ $step3Page->skip_vehicle_info }}
                                @endisset
                            </button>
                        </div> -->
                    </div>
                </div>

                <div class="mt-4 flex justify-center space-x-2 md:col-span-2">
                    <button type="button" onclick="showSkipConfirmation()" class="button-exp-fill w-36">
                        @isset($step3Page->skip_button_label)
                            {{ $step3Page->skip_button_label }}
                        @endisset
                    </button>
                    <button type="submit" id="nextButton" class="button-exp-fill w-36 opacity-50 cursor-not-allowed" disabled>
                        @isset($step3Page->next_button_label)
                            {{ $step3Page->next_button_label }}
                        @endisset
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<!-- Skip Confirmation Modal -->
<div id="skipModal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
            <div class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg w-full modal-border">
                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="text-center w-full">
                        <h6 class="text-lg font-FuturaMdCnBT font-medium text-gray-900 mb-4">
                            @isset($step3Page->skip_confirmation_heading)
                                {{ $step3Page->skip_confirmation_heading }}
                            @endisset
                        </h6>
                        <p class="text-gray-600">
                            @isset($step3Page->skip_confirmation_message)
                                {{ $step3Page->skip_confirmation_message }}
                            @endisset
                        </p>
                    </div>
                </div>
                <div class="px-4 pb-6 pt-4 sm:flex sm:flex-row-reverse sm:px-6 justify-center gap-3">
                    <a href="{{ route('step4to5', ['lang' => $selectedLanguage->abbreviation]) }}" class="inline-flex w-full justify-center rounded bg-primary px-3 py-2 font-FuturaMdCnBT text-lg text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-blue-400 sm:w-auto">Yes, skip it!</a>
                    <button type="button" onclick="hideSkipConfirmation()" class="inline-flex w-full justify-center rounded bg-gray-300 px-3 py-2 font-FuturaMdCnBT text-lg text-gray-700 hover:bg-gray-400 sm:w-auto">No, take me back</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Enhance Profile Modal (No Photo) -->
<div id="enhanceProfileModal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
            <div class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg w-full modal-border">
                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="text-center w-full">
                        <h6 class="text-2xl font-FuturaMdCnBT text-gray-900 mb-4">
                            @isset($step3Page->enhance_profile_heading)
                                {{ $step3Page->enhance_profile_heading }}
                            @endisset
                        </h6>
                        <div class="text-left space-y-3">
                            <p class="text-gray-700">
                                @isset($step3Page->enhance_profile_message)
                                    {{ $step3Page->enhance_profile_message }}
                                @endisset
                            </p>
                            <p class="text-gray-700">
                                @isset($step3Page->enhance_profile_message_2)
                                    {{ $step3Page->enhance_profile_message_2 }}
                                @endisset
                            </p>
                        </div>
                    </div>
                </div>
                <div class="px-4 pb-6 pt-4 sm:flex sm:flex-row-reverse sm:px-6 justify-center gap-3">
                    <button type="button" onclick="hideEnhanceProfileModal()" class="inline-flex w-full justify-center rounded bg-primary px-3 py-2 font-FuturaMdCnBT text-lg text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-blue-400 sm:w-auto">+ Add Photo</button>
                    <button type="button" onclick="proceedWithoutPhoto()" class="inline-flex w-full justify-center rounded bg-gray-300 px-3 py-2 font-FuturaMdCnBT text-lg text-gray-700 hover:bg-gray-400 sm:w-auto">Maybe Later</button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')

<script>
    window.addEventListener("pageshow", function(event) {
        if (event.persisted) {
            window.location.reload();
        }
    });

    let sessionImage = "{{ session('uploaded_profile_image') }}";
    console.log(sessionImage);
    sessionStorage.setItem("uploaded_profile_image", "{{ session('uploaded_profile_image') }}");
    const profileImage = document.getElementById('profile-image');
    const profileImage1 = document.getElementById('profile-image1');

    function previewImage1(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function(e) {
                profileImage1.src = e.target.result;
            };

            reader.readAsDataURL(input.files[0]);
        }
    }

    let hasVehiclePhoto = false;

    function previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function(e) {
                profileImage.src = e.target.result;
                profileImage.className = 'w-68 h-58 object-contain mb-3 cursor-pointer rounded-lg';
                hasVehiclePhoto = true;
                validateStep3Form();
            };

            reader.readAsDataURL(input.files[0]);
        } else {
            hasVehiclePhoto = false;
            validateStep3Form();
        }
    }

    function showSkipConfirmation() {
        document.getElementById('skipModal').classList.remove('hidden');
    }

    function hideSkipConfirmation() {
        document.getElementById('skipModal').classList.add('hidden');
    }

    // Form validation for Step 3
    function validateStep3Form() {
        const make = document.querySelector('input[name="make"]').value.trim();
        const model = document.querySelector('input[name="model"]').value.trim();
        const type = document.querySelector('select[name="type"]').value;
        const color = document.querySelector('input[name="color"]').value.trim();
        const licenseNo = document.querySelector('input[name="liscense_no"]').value.trim();
        const year = document.querySelector('input[name="year"]').value.trim();
        const carType = document.querySelector('input[name="car_type"]:checked');
        
        const nextButton = document.getElementById('nextButton');
        
        // Check if all required fields are filled (vehicle photo is optional for button enablement)
        const isValid = make && model && type && color && licenseNo && year && carType;
        
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
    
    function showEnhanceProfileModal() {
        document.getElementById('enhanceProfileModal').classList.remove('hidden');
    }
    
    function hideEnhanceProfileModal() {
        document.getElementById('enhanceProfileModal').classList.add('hidden');
        // Scroll to the photo upload section
        document.getElementById('dropzone-file').scrollIntoView({ behavior: 'smooth', block: 'center' });
        // Focus on the file input
        setTimeout(() => {
            document.getElementById('dropzone-file').click();
        }, 500);
    }
    
    function proceedWithoutPhoto() {
        // Submit the form without photo
        document.getElementById('enhanceProfileModal').classList.add('hidden');
        document.getElementById('step3Form').submit();
    }

    // Add event listeners to all form inputs for real-time validation
    document.addEventListener('DOMContentLoaded', function() {
        const formInputs = [
            'input[name="make"]',
            'input[name="model"]',
            'select[name="type"]',
            'input[name="color"]',
            'input[name="liscense_no"]',
            'input[name="year"]',
            'input[name="car_type"]'
        ];
        
        formInputs.forEach(selector => {
            const elements = document.querySelectorAll(selector);
            elements.forEach(element => {
                element.addEventListener('input', validateStep3Form);
                element.addEventListener('change', validateStep3Form);
            });
        });
        
        // Check if user already has a vehicle photo
        const fileInput = document.getElementById('dropzone-file');
        const profileImage = document.getElementById('profile-image');
        
        // Check if image is not the placeholder (user has uploaded an image)
        if (profileImage && profileImage.src && !profileImage.src.includes('image-placeholder.png')) {
            // Check if it's a data URL (newly uploaded) or an existing image
            if (profileImage.src.startsWith('data:') || profileImage.src.includes('car_images/')) {
                hasVehiclePhoto = true;
            }
        }
        
        // Also check if file input has a file
        if (fileInput && fileInput.files && fileInput.files.length > 0) {
            hasVehiclePhoto = true;
        }
        
        // Prevent form submission if no photo
        document.getElementById('step3Form').addEventListener('submit', function(e) {
            const make = document.querySelector('input[name="make"]').value.trim();
            const model = document.querySelector('input[name="model"]').value.trim();
            const type = document.querySelector('select[name="type"]').value;
            const color = document.querySelector('input[name="color"]').value.trim();
            const licenseNo = document.querySelector('input[name="liscense_no"]').value.trim();
            const year = document.querySelector('input[name="year"]').value.trim();
            const carType = document.querySelector('input[name="car_type"]:checked');
            const fileInput = document.getElementById('dropzone-file');
            
            // Check if all fields are filled but no photo
            const allFieldsFilled = make && model && type && color && licenseNo && year && carType;
            
            if (allFieldsFilled && !hasVehiclePhoto && !fileInput.files.length) {
                e.preventDefault();
                showEnhanceProfileModal();
                return false;
            }
        });
        
        // Initial validation check
        validateStep3Form();
    });
</script>

@endsection
