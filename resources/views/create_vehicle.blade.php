@extends('layouts.template')

@section('content')

<div class="grid grid-cols-12 gap-4 md:container md:mx-auto my-6 md:my-10 xl:my-14 px-4 xl:px-0">
    @include('layouts.inc.profile_sidebar')

    <div class="bg-white border rounded p-4 border-gray-200 w-full col-span-12 lg:col-span-9 shadow">
        <div class="pb-2">
            <h1 class="mb-0"> @isset($myVehiclePage->add_main_heading)
                {{ $myVehiclePage->add_main_heading }}
            @endisset</h1>
        </div>

        <form method="POST" action="{{ route('profile.vehicle.store') }}" enctype="multipart/form-data">
            @csrf
            <p class="text-red-500">*  @isset($myVehiclePage->mobile_indicate_field_label)
                {{ $myVehiclePage->mobile_indicate_field_label }}
            @endisset
            </p>
            <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4 mt-4">
                <div>
                    <label for=""> @isset($myVehiclePage->make_label)
                        {{ $myVehiclePage->make_label }}
                    @endisset <span class="text-red-500">*</span></label>
                    <input type="text" placeholder="@isset($myVehiclePage->make_placeholder) {{ $myVehiclePage->make_placeholder }} @else Default Placeholder @endisset" name="make" value="{{ old('make') }}" class="block mt-1 border p-1.5 w-full rounded text-base md:text-lg border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600">
                    @error('make')
                      <div class="tooltip-error shadow-lg">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <label for=""> @isset($myVehiclePage->model_label)
                        {{ $myVehiclePage->model_label }}
                    @endisset
                     <span class="text-red-500">*</span></label>
                    <input type="text" placeholder="@isset($myVehiclePage->model_placeholder) {{ $myVehiclePage->model_placeholder }} @else Default Placeholder @endisset" name="model" value="{{ old('model') }}" class="block mt-1 border p-1.5 w-full rounded text-base md:text-lg border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600">
                    @error('model')
                      <div class="tooltip-error shadow-lg">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <label for=""> @isset($myVehiclePage->vehicle_type_label)
                        {{ $myVehiclePage->vehicle_type_label }}
                    @endisset
                    <span class="text-red-500">*</span></label>
                    <select id="type" name="vehicle_type" class="block mt-1 border w-full rounded border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600">
                        <option value="">
                            {{ $myVehiclePage->vehicle_type_placeholder ?? "Select" }}
                        </option>
                        @foreach (($vehicleTypes ?? collect()) as $vehicleType)
                            <option value="{{ $vehicleType['id'] }}" {{ (int) old('vehicle_type') === $vehicleType['id'] ? 'selected' : '' }}>
                                {{ $vehicleType['label'] }}
                            </option>
                        @endforeach
                    </select>
                    @error('vehicle_type')
                      <div class="tooltip-error shadow-lg">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label for="">@isset($myVehiclePage->license_plate_number_label)
                        {{ $myVehiclePage->license_plate_number_label }}
                    @endisset
                    <span class="text-red-500">*</span></label>
                    <input type="text" name="license_no" value="{{ old('license_no') }}" maxlength="8" placeholder="@isset($myVehiclePage->license_plate_number_placeholder) {{ $myVehiclePage->license_plate_number_placeholder }} @endisset" class="block mt-1 border p-1.5 w-full rounded text-base md:text-lg border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600">
                    @error('license_no')
                      <div class="tooltip-error shadow-lg">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label for="">@isset($myVehiclePage->color_label)
                        {{ $myVehiclePage->color_label }}
                    @endisset <span class="text-red-500">*</span></label>
                    <input type="text" name="color" value="{{ old('color') }}" maxlength="15" placeholder="@isset($myVehiclePage->color_placeholder) {{ $myVehiclePage->color_placeholder }} @endisset" class="block mt-1 border p-1.5 w-full rounded text-base md:text-lg border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600">
                    @error('color')
                      <div class="tooltip-error shadow-lg">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <label for="">@isset($myVehiclePage->year_label)
                        {{ $myVehiclePage->year_label }}
                    @endisset <span class="text-red-500">*</span></label>
                    <input type="text" name="year" value="{{ old('year') }}" maxlength="4" placeholder="@isset($myVehiclePage->year_placeholder) {{ $myVehiclePage->year_placeholder }} @endisset" class="block mt-1 border p-1.5 w-full rounded text-base md:text-lg border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600">
                    @error('year')
                      <div class="tooltip-error shadow-lg">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <label for="power_type" class="text-gray-900 mb-2">{{ $myVehiclePage->fuel_label ?? "Fuel"}} <span class="text-red-500">*</span></label>
                    <div class="mt-2 flex items-center gap-2">
                        <div class="flex items-center space-x-1.5 lg:space-x-3 mb-2 mr-2 lg:mr-2">
                            <input id="power_type_electric" name="power_type" type="radio" value="Electric" {{ old('power_type') == 'Electric' ? 'checked' : '' }} class="h-5 w-5 border-gray-300 bg-gray-200 cursor-pointer text-sky-600 focus:ring-sky-600">
                            <label for="power_type_electric" class="block text-gray-900">
                                {{ $myVehiclePage->electric_checkbox_label ?? "Electric"}}
                            </label>
                        </div>
                        <div class="flex items-center space-x-1.5 lg:space-x-3 mb-2 mr-2 lg:mr-2">
                            <input id="power_type_hybrid" name="power_type" type="radio" value="Hybrid" {{ old('power_type') == 'Hybrid' ? 'checked' : '' }} class="h-5 w-5 border-gray-300 bg-gray-200 cursor-pointer text-sky-600 focus:ring-sky-600">
                            <label for="power_type_hybrid" class="block text-gray-900">
                                {{ $myVehiclePage->hybrid_checkbox_label ?? "Hybrid"}}
                            </label>
                        </div>
                        <div class="flex items-center space-x-1.5 lg:space-x-3 mb-2 mr-2 lg:mr-2">
                            <input id="power_type_gas" name="power_type" type="radio" value="Gas" {{ old('power_type') || empty(old('power_type')) == 'Gas' ? 'checked' : '' }} class="h-5 w-5 border-gray-300 bg-gray-200 cursor-pointer text-sky-600 focus:ring-sky-600">
                            <label for="power_type_gas" class="block text-gray-900">
                                {{ $myVehiclePage->gas_checkbox_label ?? "Gas"}}
                            </label>
                        </div>
                    </div>
                    @error('power_type')
                        <div class="tooltip-error shadow-lg">{{ $message }}</div>
                    @enderror
                </div>
                <div class="justify-between md:items-center gap-2">
                    <label for="primary_vehicle" class="text-gray-900">{{ $myVehiclePage->set_primary_vehicle_label ?? "Set as primary vehicle"}} <span class="text-red-500">*</span></label>
                    <div class="mt-2 flex items-center gap-2">
                        <div class="flex items-center space-x-1.5 lg:space-x-3 mb-2 mr-2 lg:mr-2">
                            <input id="primary_vehicle_yes" name="primary_vehicle" type="radio" value="1" {{ (old('primary_vehicle') == '1' || (!old('primary_vehicle') && isset($userVehicleCount) && $userVehicleCount == 0)) ? 'checked' : '' }} class="h-5 w-5 border-gray-300 bg-gray-200 cursor-pointer text-sky-600 focus:ring-sky-600">
                            <label for="primary_vehicle_yes" class="block text-gray-900">
                                {{ $myVehiclePage->yes_checkbox_label ?? "Yes"}}
                            </label>
                        </div>
                        <div class="flex items-center space-x-1.5 lg:space-x-3 mb-2 mr-2 lg:mr-2">
                            <input id="primary_vehicle_no" name="primary_vehicle" type="radio" value="0" {{ (old('primary_vehicle') == '0' || (!old('primary_vehicle') && isset($userVehicleCount) && $userVehicleCount > 0)) ? 'checked' : '' }} class="h-5 w-5 border-gray-300 bg-gray-200 cursor-pointer text-sky-600 focus:ring-sky-600">
                            <label for="primary_vehicle_no" class="block text-gray-900">
                                {{ $myVehiclePage->no_checkbox_label ?? "No"}}
                            </label>
                        </div>
                    </div>
                    @error('primary_vehicle')
                        <div class="tooltip-error shadow-lg">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="md:col-span-2">
                    <label for="dropzone-file" id="dropzone-file-label" class="flex flex-col items-center justify-center w-full h-auto border-2 border-gray-300 border-dashed rounded cursor-pointer bg-white hover:bg-gray-100">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6 p-4">
                            @if (session('uploaded_image'))
                                <img id="profile-image" class="w-40 h-40 object-contain mb-2 cursor-pointer" src="{{ asset('car_images/' . session('uploaded_image')) }}" alt="Uploaded Image">
                            @else
                                <img id="profile-image" class="w-14 h-14 object-contain mb-1 cursor-pointer" src="{{ asset('assets/image-placeholder.png')}}">
                            @endif
                            <div id="hide-text1" class="text-center w-full">
                                <p class="text-sm lg:text-lg text-gray-900 text-center">{{ $myVehiclePage->image_description_label ?? " Upload vehicle image."}}
                                    <!-- <span class="text-primary">{{ $myVehiclePage->choose_file_image_placeholder ?? " Choose file"}}</span> -->
                                </p>
                                <p class="text-sm lg:text-base text-gray-900 font-normal text-center">
                                    (JPG, PNG, JPEG, and GIF. 10MB max.)
                                </p>
                            </div>
                        </div>
                        <input id="dropzone-file" name="vehicle_image" type="file" onchange="previewImage(this)" accept="image/*" class="hidden" />
                        @if (session('uploaded_image'))
                            <input type="hidden" name="existing_image" value="{{ session('uploaded_image') }}">
                        @endif
                    </label>
                    @error('vehicle_image')
                        @if ($message !== 'The image is not uploaded yet' && $message !== 'The image failed to upload')
                            <div class="tooltip-error shadow-lg">{{ $message }}</div>
                        @endif
                    @enderror
                </div>

                <div id="show-button" class="hidden">
                    <div class="flex items-center">
                        <div class="flex items-center space-x-1.5 lg:space-x-3 mb-2 mr-2 lg:mr-2">
                            <input id="remove_image" name="remove_image" type="checkbox" value="1" {{ old('remove_image') == '1' ? 'checked' : '' }} class="h-5 w-5 border-gray-300 bg-gray-200 cursor-pointer text-sky-600 focus:ring-sky-600">
                            <label for="remove_image" class="block text-gray-900">
                                {{ $myVehiclePage->remove_car_photo_label ?? "Remove car photo"}}
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-6 flex justify-center">
                <button type="submit" class="button-exp-fill">{{ $myVehiclePage->add_vehicle_button_text ?? "Add vehicle"}}</button>
            </div>
        </form>
    </div>
</div>

<div class="hidden overflow-x-hidden overflow-y-auto fixed inset-0 z-50 outline-none focus:outline-none justify-center items-center" id="card-modal-1">
    <div class="relative h-screen my-6 mx-auto flex items-center justify-center w-full" id="card-modal-1-backdrop-1">
        <!--content-->
        <div class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg w-full">
            <button onclick="toggleModalCard('card-modal-1')" class="absolute top-2 right-2 p-1 rounded-full hover:bg-gray-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start justify-center"></div>
                <div class="text-center sm:ml-4 sm:mt-0 sm:text-left">
                    <div class="mt-2 w-full">
                        <p class="text-lg text-center text-black">{{ $myVehiclePage->delete_photo_message ?? 'Are you sure you want to remove this photo?' }}</p>
                    </div>
                </div>
            </div>
            <div class="px-4 pb-6 pt-4 flex items-center space-x-2 sm:space-x-4 sm:px-6 justify-center">
                <button id="remove-photo" class="button-exp-red-fill">
                    {{ $successMessage->yes_remove_it_button_text ?? "Yes" }}
                </button>
                <button type="button" onclick="toggleModalCard('card-modal-1')" class="button-exp-fill">{{ $successMessage->no_go_back_button_text ?? "No" }}</button>
            </div>
        </div>
    </div>
</div>
<div class="hidden opacity-25 fixed inset-0 z-40 bg-black" id="card-modal-1-backdrop"></div>
{{-- <div class="hidden opacity-25 fixed inset-0 z-40 bg-black" id="card-modal-backdrop"></div> --}}

<div id="modal" class="relative z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full" id="card-modal-backdrop">
            <div class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg w-full modal-border">
                <button onclick="toggleModalCard('modal')" class="absolute top-2 right-2 p-1 rounded-full hover:bg-gray-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start justify-center">
                        <div class="text-3xl text-center font-FuturaMdCnBT text-black">
                            {{ $successMessage->alert_label ?? "Alert"}}
                        </div>
                    </div>
                    <div class="mt-2 w-full">
                        <p class="can-exp-p text-center">{{ $myVehiclePage->delete_photo_message ?? "Are you sure you want to remove this photo?"}}</p>
                    </div>
                </div>
                <!--footer-->
                <div class="px-4 pb-6 pt-4 sm:flex sm:flex-row-reverse sm:px-6 justify-center">
                    <a href="#" class="no-button inline-flex w-full justify-center rounded bg-blue-500 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-blue-400 sm:ml-3 sm:w-auto">{{ $successMessage->no_go_back_button_text ?? "No, go back"}}</a>
                    <a href="#" class="yes-button inline-flex w-full justify-center rounded bg-blue-500 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-blue-400 sm:ml-3 sm:w-auto">{{ $successMessage->yes_remove_it_button_text ?? "Yes, remove it"}}</a>
                </div>
            </div>
        </div>
    </div>
</div>

<x-image-size-error-modal
        title="Upload Error"
        button-label="{{ $siteText['close_btn_text'] ?? 'Close' }}"
        modal-border-class="modal-border1"
    />
@endsection

@section('script')

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<script>
    
    var uploadedImage = "{{ session('uploaded_image') }}";
    var hasUploadedImage = uploadedImage && uploadedImage !== "null";

    $(document).ready(function() {

        if (hasErrors && hasUploadedImage) {
            $('#profile-image').removeClass('w-12');
            $('#profile-image').removeClass('h-12');
            $('#profile-image').removeClass('object-contain');
            $('#profile-image').removeClass('mb-3');
            $('#profile-image').addClass('w-full');
            $('#profile-image').addClass('h-full');
            $('#profile-image').addClass('object-cover');
            $('#hide-text1').addClass('hidden');
            $('#hide-text2').addClass('hidden');
            $('#show-button').removeClass('hidden');
            $('#hide-buttons').removeClass('hidden');
            $('#hide-buttons').addClass('flex');
            $('#show-text1').removeClass('hidden');
            $('#dropzone-file-label').removeClass('cursor-pointer');
            $('#dropzone-file').prop('readonly', true);
        }

        

        // Handle backdrop clicks to close modals
        $(document).on('click', function(event) {
            console.log('backdrop 1 is clicked ', $(event.target).is('#card-modal-1-backdrop-1'));
            console.log('backdrop is clicked ',$(event.target).is('#card-modal-backdrop'));
            if ($(event.target).is('#card-modal-1-backdrop-1')) {
                console.log('backdrop 1 clicked');
                toggleModalCard('card-modal-1');
            } else if ($(event.target).is('#card-modal-backdrop')) {
                console.log('backdrop clicked');
                toggleModalCard('modal');
            }
        });
    });

    document.querySelector('input[name="year"]').addEventListener('input', function (e) {
        this.value = this.value.replace(/\D/g, '').slice(0, 4); // removes non-digits and limits to 4 digits
    });

    

    // Event listener for checkbox change
    $('#remove_image').on('change', function() {
        // If the checkbox is unchecked
        if (this.checked) {
            $('#modal').removeClass('hidden'); // Show the modal
            $('#card-modal-backdrop').removeClass('hidden'); // Show the backdrop
        }
    });

    // Handle "No, go back" button click
    $('#modal .no-button').on('click', function(event) {
        event.preventDefault();
        $('#modal').addClass('hidden');
        $('#card-modal-backdrop').addClass('hidden');
        $('#remove_image').prop('checked', false); // Uncheck the checkbox
    });

    // Handle "Yes, remove it" button click
    $('#modal .yes-button').on('click', function(event) {
        event.preventDefault();
        $('#modal').addClass('hidden');
        $('#card-modal-backdrop').addClass('hidden');
        $('#remove_image').prop('checked', true); // Check the checkbox

        if (profileImage.src && profileImage.src !== '') {
            profileImage.src = "{{ asset('assets/image-placeholder.png')}}";

            // Clear the file input by creating a new input element and replacing the old one
            const fileInput = document.getElementById('dropzone-file');
            fileInput.value = '';

            $('#profile-image').addClass('w-12');
            $('#profile-image').addClass('h-12');
            $('#profile-image').addClass('object-contain');
            $('#profile-image').addClass('mb-3');
            $('#profile-image').removeClass('w-full');
            $('#profile-image').removeClass('h-full');
            $('#profile-image').removeClass('object-cover');
            $('#hide-text1').removeClass('hidden');
            $('#hide-text2').removeClass('hidden');
            $('#show-button').addClass('hidden');
            $('#hide-buttons').addClass('hidden');
            $('#hide-buttons').addClass('flex');
            $('#show-text1').addClass('hidden');
            $('#dropzone-file-label').addClass('cursor-pointer');
            $('#dropzone-file').prop('readonly', false);
        }
    });

    const profileImage = document.getElementById('profile-image');

    function previewImage(input) {
        if (input.files && input.files[0]) {

            if (input.files[0].size > ({{ env('MAX_IMAGE_SIZE', 10) }} * 1024 * 1024)) {
                input.value = '';
                showImageSizeErrorModal();
                return;
            }

            const reader = new FileReader();

            reader.onload = function(e) {
                profileImage.src = e.target.result;
                $('#profile-image').removeClass('w-12');
                $('#profile-image').removeClass('h-12');
                $('#profile-image').removeClass('object-contain');
                $('#profile-image').removeClass('mb-3');
                $('#profile-image').addClass('w-full');
                $('#profile-image').addClass('h-full');
                $('#profile-image').addClass('object-cover');
                $('#hide-text1').addClass('hidden');
                $('#hide-text2').addClass('hidden');
                $('#show-button').removeClass('hidden');
                $('#hide-buttons').removeClass('hidden');
                $('#hide-buttons').addClass('flex');
                $('#show-text1').removeClass('hidden');
                $('#dropzone-file-label').removeClass('cursor-pointer');
                $('#dropzone-file').prop('readonly', true);
            };

            reader.readAsDataURL(input.files[0]);
        }
    }

    document.getElementById('remove-photo').addEventListener('click', function() {
        event.preventDefault();
        // Check if there is an existing image displayed
        if (profileImage.src && profileImage.src !== '') {
            profileImage.src = "{{ asset('assets/image-placeholder.png')}}";

            // Clear the file input by creating a new input element and replacing the old one
            const fileInput = document.getElementById('dropzone-file');
            fileInput.value = '';

            $('#profile-image').addClass('w-12');
            $('#profile-image').addClass('h-12');
            $('#profile-image').addClass('object-contain');
            $('#profile-image').addClass('mb-3');
            $('#profile-image').removeClass('w-full');
            $('#profile-image').removeClass('h-full');
            $('#profile-image').removeClass('object-cover');
            $('#hide-text1').removeClass('hidden');
            $('#hide-text2').removeClass('hidden');
            $('#show-button').addClass('hidden');
            $('#hide-buttons').addClass('hidden');
            $('#hide-buttons').addClass('flex');
            $('#show-text1').addClass('hidden');
            $('#dropzone-file-label').addClass('cursor-pointer');
            $('#dropzone-file').prop('readonly', false);
        }
        toggleModalCard('card-modal-1');
    });

    function toggleModalCard(modalID) {
        const modal = document.getElementById(modalID);
        const backdropID = modalID === 'modal'? 'card-modal-backdrop' : modalID + '-backdrop';
        const backdrop = document.getElementById(backdropID);
        console.log('Toggling modal:', modalID);
        console.log('Toggling backdrop:', backdropID);

        modal.classList.toggle('hidden');
        modal.classList.toggle('flex');
        if(backdropID == 'card-modal-1-backdrop'){
            backdrop.classList.toggle('hidden');
            backdrop.classList.toggle('flex');
        }
    }
</script>

@endsection
