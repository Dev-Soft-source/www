@extends('layouts.template')

@section('content')

<div class="grid grid-cols-12 gap-4 md:container md:mx-auto my-6 md:my-10 xl:my-14 px-4 xl:px-0">
    @include('layouts.inc.profile_sidebar')

    <div class="bg-white border rounded p-4 border-gray-200 w-full col-span-12 lg:col-span-9 shadow">
        <div class="border-b pb-2 flex justify-between items-center">
            <h1 class="mb-0">@isset($myVehiclePage->edit_main_heading)
                {{ $myVehiclePage->edit_main_heading }}
            @endisset</h1>
            <span class="text-red-500">* @isset($myVehiclePage->mobile_indicate_field_label)
                        {{ $myVehiclePage->mobile_indicate_field_label }}
                    @endisset</span>
        </div>
        <form method="POST" action="{{ route('profile.vehicle.update', $vehicle->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4 mt-4">
                <div>
                    <label for="">
                        @isset($myVehiclePage->make_label)
                        {{ $myVehiclePage->make_label }}
                    @endisset
                    </label>
                    <input type="text" name="make"
                        @if ($errors->count() > 0)
                            value="{{ old('make') }}"
                        @else
                            value="{{ optional($vehicle)->make }}"
                        @endif
                        class="block mt-1 border p-1.5 w-full text-base lg:text-lg rounded border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600">
                    @error('make')
                      <div class="relative tooltip -bottom-4 group-hover:flex">
                        <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                            <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                        </div>
                      </div>
                    @enderror
                </div>
                <div>
                    <label for="">
                        @isset($myVehiclePage->model_label)
                        {{ $myVehiclePage->model_label }}
                    @endisset
                    <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="model"
                        @if ($errors->count() > 0)
                            value="{{ old('model') }}"
                        @else
                            value="{{ optional($vehicle)->model }}"
                        @endif
                        class="block mt-1 border p-1.5 w-full text-base lg:text-lg rounded border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600">
                    @error('model')
                      <div class="relative tooltip -bottom-4 group-hover:flex">
                        <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                            <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                        </div>
                      </div>
                    @enderror
                </div>
                <div>
                    <label for=""> @isset($myVehiclePage->vehicle_type_label)
                        {{ $myVehiclePage->vehicle_type_label }}
                    @endisset
                     <span class="text-red-500">*</span></label>
                    @php
                        $selectedType = $errors->count() > 0 ? old('type') : optional($vehicle)->type;
                    @endphp
                    <select id="type" name="type" class="block mt-1 border p-1.5 w-full rounded border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600">
                        <option value="" {{ $selectedType === '' || $selectedType === null ? 'selected' : '' }}>
                            {{ $myVehiclePage->vehicle_type_placeholder ?? "Select" }}
                        </option>
                        <option value="Convertable" {{ $selectedType === 'Convertable' ? 'selected' : '' }}>
                            Convertable
                        </option>
                        <option value="Coupe" {{ $selectedType === 'Coupe' ? 'selected' : '' }}>
                            Coupe
                        </option>
                        <option value="Hatchback" {{ $selectedType === 'Hatchback' ? 'selected' : '' }}>
                            Hatchback
                        </option>
                        <option value="Minivan" {{ $selectedType === 'Minivan' ? 'selected' : '' }}>
                            Minivan
                        </option>
                        <option value="Sedan" {{ $selectedType === 'Sedan' ? 'selected' : '' }}>
                            Sedan
                        </option>
                        <option value="Station wagon" {{ $selectedType === 'Station wagon' ? 'selected' : '' }}>
                            Station wagon
                        </option>
                        <option value="SUV" {{ $selectedType === 'SUV' ? 'selected' : '' }}>
                            SUV
                        </option>
                        <option value="Truck" {{ $selectedType === 'Truck' ? 'selected' : '' }}>
                            Truck
                        </option>
                        <option value="Van" {{ $selectedType === 'Van' ? 'selected' : '' }}>
                            Van
                        </option>
                    </select>
                    @error('type')
                      <div class="relative tooltip -bottom-4 group-hover:flex">
                        <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                            <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                        </div>
                      </div>
                    @enderror
                </div>
                <div class="md:col-span-2">
                    <label for="">
                        @isset($myVehiclePage->license_plate_number_label)
                        {{ $myVehiclePage->license_plate_number_label }}
                    @endisset
                    <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="liscense_no" maxlength="8"
                        @if ($errors->count() > 0)
                            value="{{ old('liscense_no') }}"
                        @else
                            value="{{ optional($vehicle)->liscense_no }}"
                        @endif
                        class="block mt-1 border p-1.5 w-full text-base lg:text-lg rounded border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600">
                    @error('liscense_no')
                      <div class="relative tooltip -bottom-4 group-hover:flex">
                        <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                            <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                        </div>
                      </div>
                    @enderror
                </div>
                <div>
                    <label for="">
                        @isset($myVehiclePage->color_label)
                        {{ $myVehiclePage->color_label }}
                    @endisset
                    <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="color" maxlength="15"
                        @if ($errors->count() > 0)
                            value="{{ old('color') }}"
                        @else
                            value="{{ optional($vehicle)->color }}"
                        @endif
                        class="block mt-1 border p-1.5 w-full text-base lg:text-lg rounded border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600">
                    @error('color')
                      <div class="relative tooltip -bottom-4 group-hover:flex">
                        <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                            <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                        </div>
                      </div>
                    @enderror
                </div>
                <div>
                    <label for=""> @isset($myVehiclePage->year_label)
                        {{ $myVehiclePage->year_label }}
                    @endisset <span class="text-red-500">*</span></label>
                    <input type="text" name="year" maxlength="4"
                        @if ($errors->count() > 0)
                            value="{{ old('year') }}"
                        @else
                            value="{{ optional($vehicle)->year }}"
                        @endif
                        class="block mt-1 border p-1.5 w-full text-base lg:text-lg rounded border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600">
                    @error('year')
                      <div class="relative tooltip -bottom-4 group-hover:flex">
                        <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                            <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                        </div>
                      </div>
                    @enderror
                </div>
                <div>
                    <label for="car_type" class="text-gray-900 mb-2">
                        @isset($myVehiclePage->fuel_label)
                            {{ $myVehiclePage->fuel_label }}
                        @endisset
                        <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-2 flex items-center gap-2">
                        <div class="flex items-center space-x-1.5 lg:space-x-3 mb-2 mr-2 lg:mr-2">
                            <input id="car_type_electric" name="car_type" type="radio" value="Electric"
                                {{ old('car_type', optional($vehicle)->car_type) === 'Electric' ? 'checked' : '' }}
                                class="h-5 w-5 border-gray-300 bg-gray-200 cursor-pointer text-indigo-600 focus:ring-indigo-600">
                            <label for="car_type_electric" class="block text-gray-900">
                                {{ $myVehiclePage->electric_checkbox_label ?? "Electric"}}
                            </label>
                        </div>
                        <div class="flex items-center space-x-1.5 lg:space-x-3 mb-2 mr-2 lg:mr-2">
                            <input id="car_type_hybrid" name="car_type" type="radio" value="Hybrid"
                                {{ old('car_type', optional($vehicle)->car_type) === 'Hybrid' ? 'checked' : '' }}
                                class="h-5 w-5 border-gray-300 bg-gray-200 cursor-pointer text-indigo-600 focus:ring-indigo-600">
                            <label for="car_type_hybrid" class="block text-gray-900">
                                {{ $myVehiclePage->hybrid_checkbox_label ?? "Hybrid"}}
                            </label>
                        </div>
                        <div class="flex items-center space-x-1.5 lg:space-x-3 mb-2 mr-2 lg:mr-2">
                            <input id="car_type_gas" name="car_type" type="radio" value="Gas"
                                {{ old('car_type', optional($vehicle)->car_type) === 'Gas' ? 'checked' : '' }}
                                class="h-5 w-5 border-gray-300 bg-gray-200 cursor-pointer text-indigo-600 focus:ring-indigo-600">
                            <label for="car_type_gas" class="block text-gray-900">
                                {{ $myVehiclePage->gas_checkbox_label ?? "Gas"}}
                            </label>
                        </div>
                    </div>
                    @error('car_type')
                      <div class="relative tooltip -bottom-4 group-hover:flex">
                        <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                            <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                        </div>
                      </div>
                    @enderror
                </div>
                <div class="justify-between md:items-center gap-2">
                    <label for="primary_vehicle" class="text-gray-900">{{ $myVehiclePage->set_primary_vehicle_label ?? "Set as primary vehicle"}} <span class="text-red-500">*</span></label>
                    <div class="mt-2 flex items-center gap-2">
                        <div class="flex items-center space-x-1.5 lg:space-x-3 mb-2 mr-2 lg:mr-2">
                            <input id="primary_vehicle_yes" name="primary_vehicle" type="radio" value="1"
                                {{ old('primary_vehicle', optional($vehicle)->primary_vehicle) == '1' ? 'checked' : '' }}
                                class="h-5 w-5 border-gray-300 bg-gray-200 cursor-pointer text-indigo-600 focus:ring-indigo-600">
                            <label for="primary_vehicle_yes" class="block text-gray-900">
                                {{ $myVehiclePage->yes_checkbox_label ?? "Yes"}}
                            </label>
                        </div>
                        <div class="flex items-center space-x-1.5 lg:space-x-3 mb-2 mr-2 lg:mr-2">
                            <input id="primary_vehicle_no" name="primary_vehicle" type="radio" value="0"
                                {{ old('primary_vehicle', optional($vehicle)->primary_vehicle) == '0' ? 'checked' : '' }}
                                class="h-5 w-5 border-gray-300 bg-gray-200 cursor-pointer text-indigo-600 focus:ring-indigo-600">
                            <label for="primary_vehicle_no" class="block text-gray-900">
                                {{ $myVehiclePage->no_checkbox_label ?? "No"}}
                            </label>
                        </div>
                    </div>
                    @error('primary_vehicle')
                      <div class="relative tooltip -bottom-4 group-hover:flex">
                        <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                            <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                        </div>
                      </div>
                    @enderror
                </div>

                <div class="flex flex-col sm:flex-col md:flex-row lg:flex-row justify-between md:items-center mt-4 gap-2 md:col-span-2">
                    <div id="hide-text2" class="text-gray-500"><p>
                    @isset($myVehiclePage->car_photo_label)
                        {{ $myVehiclePage->car_photo_label }}
                    @endisset
                </p></div>
                    <div id="show-text1" class="text-gray-500 hidden"><p>
                        @isset($myVehiclePage->image_description_label)
                        {{ $myVehiclePage->image_description_label }}
                    @endisset
                    </p></div>
                    <div id="show-text2" class="text-gray-500 hidden"><p>
                        @isset($myVehiclePage->edit_photo_label)
                        {{ $myVehiclePage->edit_photo_label }}
                    @endisset
                    </p></div>
                    <div id="hide-buttons" class="hidden flex items-center gap-2">
                        <label for="edit-file"
                            class="flex items-center justify-center rounded bg-primary p-1 cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor" class="w-6 h-6 text-white">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                            </svg>
                            <input id="edit-file" name="existing_image" type="file" onchange="previewImage(this)" class="hidden" />
                        </label>
                        <button class="bg-primary p-1 rounded cursor-pointer" type="button" onclick="toggleModalCard('card-modal-1')">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor" class="w-6 h-6 text-white bg-primary">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="md:col-span-2">
                    <label for="dropzone-file" id="dropzone-file-label"
                        class="flex flex-col items-center justify-center w-full h-auto border-2 border-gray-300 border-dashed rounded cursor-pointer bg-white hover:bg-gray-100">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6 p-4">
                            @if (session('uploaded_image'))
                                <img id="profile-image" class="w-40 h-40 object-contain mb-4 cursor-pointer" src="{{ asset('car_images/' . session('uploaded_image')) }}" alt="Uploaded Image">
                            @elseif ($vehicle->image)
                                <img id="profile-image" class="w-40 h-40 object-contain mb-4 cursor-pointer" src="{{ $vehicle->image }}" alt="Uploaded Image">
                            @else
                                <img id="profile-image" class="w-12 h-12 object-contain mb-3 cursor-pointer" src="{{ asset('assets/image-placeholder.png')}}">
                            @endif
                            <div id="hide-text1" class="justify-center text-center">
                                <p class="text-sm lg:text-lg text-gray-900">
                                    @isset($myVehiclePage->upload_profile_photo_image_placeholder)
                                    {{ $myVehiclePage->upload_profile_photo_image_placeholder }}
                                @endisset
                                    <span class="font-semibold text-primary"> @isset($myVehiclePage->choose_file_image_placeholder)
                                        {{ $myVehiclePage->choose_file_image_placeholder }}
                                    @endisset
                                </span>
                                </p>
                                <p class="text-sm lg:text-base text-gray-900 font-normal">
                                    {{--  (Allowed formats: JPG, JPEG. PNG, and GIF. 10MB max.)  --}}
                                    @isset($myVehiclePage->images_option_placeholder)
                                    {{ $myVehiclePage->images_option_placeholder }}
                                @endisset
                                </p>
                            </div>
                        </div>
                        <input id="dropzone-file" name="image" type="file" onchange="previewImage(this)" class="hidden" />
                        @if (session('uploaded_image'))
                            <input type="hidden" name="existing_image" value="{{ session('uploaded_image') }}">
                        @elseif ($vehicle->image)
                            @php
                                $imageName = basename($vehicle->image);
                            @endphp
                            <input id="existing_image" type="hidden" name="existing_image" value="{{ $imageName }}">
                        @endif
                        @error('image')
                            @if ($message !== 'The image is not uploaded yet' && $message !== 'The image failed to upload')
                                <p class="text-red-500">{{ $message }}</p>
                            @endif
                        @enderror
                    </label>
                </div>
               <div id="show-button" class="hidden">
                    <div class=" flex items-center">
                        <div class="flex items-center space-x-1.5 lg:space-x-3 mb-2 mr-2 lg:mr-2">
                            <input id="remove_image" name="remove_image" type="checkbox" value="1"
                                {{ old('remove_image', optional($vehicle)->remove_image) == '1' ? 'checked' : '' }}
                                class="h-5 w-5 border-gray-300 bg-gray-200 cursor-pointer text-indigo-600 focus:ring-indigo-600">
                            <label for="remove_image" class="block text-gray-900">
                                @isset($myVehiclePage->remove_car_photo_label)
                                    {{ $myVehiclePage->remove_car_photo_label }}
                                @endisset
                            </label>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="button-exp-fill"> @isset($myVehiclePage->update_vehicle_button_text)
                        {{ $myVehiclePage->update_vehicle_button_text }}
                    @endisset
                </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="hidden overflow-x-hidden overflow-y-auto fixed inset-0 z-50 outline-none focus:outline-none justify-center items-center" id="card-modal-1">
    <div class="relative h-screen my-6 mx-auto flex items-center justify-center w-full" id="card-modal-1-backdrop-1">
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
                        <p class="text-lg text-center text-black">Are you sure you want to remove this photo?</p>
                    </div>
                </div>
            </div>
            <div class="px-4 pb-6 pt-4 flex items-center space-x-2 sm:space-x-4 sm:px-6 justify-center">
                <button id="remove-photo" class="button-exp-fill sm:w-24">
                    {{ $messages->yes_remove_it_button_text ?? "Yes" }}
                </button>
                <button type="button" onclick="toggleModalCard('card-modal-1')" class="button-exp-fill sm:w-24">{{ $messages->no_go_back_button_text ?? "No" }}</button>
            </div>
        </div>
    </div>
</div>
<div class="hidden opacity-25 fixed inset-0 z-40 bg-black" id="card-modal-1-backdrop"></div>

<div id="modal" class="relative z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full" id="card-modal-backdrop">
            <div class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg w-full">
                <button onclick="toggleModalCard('modal')" class="absolute top-2 right-2 p-1 rounded-full hover:bg-gray-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start justify-center">
                        <div class="text-3xl text-center font-FuturaMdCnBT text-black">
                            {{ $messages->alert_label ?? "Alert"}}
                        </div>
                    </div>
                    <div class="mt-2 w-full">
                        <p class="can-exp-p text-center">{{ $myVehiclePage->delete_photo_message ?? "Are you sure you want to remove your car photo"}}</p>
                    </div>
                </div>
                <div class="px-4 pb-6 pt-4 sm:flex sm:flex-row-reverse sm:px-6 justify-center">
                    <button class="no-button inline-flex w-full justinline-flex justify-center rounded bg-blue-500 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-blue-400 sm:ml-3 sm:w-auto" onclick="toggleModalCard('modal')">{{ $messages->no_go_back_button_text ?? "No, go back"}}</button>
                    <button class="yes-button inline-flex w-full justinline-flex justify-center rounded bg-blue-500 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-blue-400 sm:ml-3 sm:w-auto">{{ $messages->yes_remove_it_button_text ?? "Yes, remove it"}}</button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<script>
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
        input.addEventListener('input', hideTooltip);
    });

    // Also add change listener for select elements to clear validation errors
    const selects = document.querySelectorAll('select');
    selects.forEach(select => {
        select.addEventListener('change', hideTooltip);
    });

    const labels = document.querySelectorAll('label');
    labels.forEach(input => {
        input.addEventListener('click', function (e) {
            hideTooltip.call(this, 'label');
        });
    });

    var hasErrors = {{ $errors->any() ? 'true' : 'false' }};
    var uploadedImage = "{{ session('uploaded_image') }}";
    var hasUploadedImage = uploadedImage && uploadedImage !== "null";
    var vehicleImage = "{{ $vehicle->image ?? '' }}";
    var hasVehicleImage = vehicleImage && vehicleImage !== "null";

    $(document).ready(function() {
        if (hasErrors) {
            scrollToFirstError();
        }

        if (hasUploadedImage || hasVehicleImage) {
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

        $('form').on('submit', function(e) {
            if ($('.error').length > 0) {
                scrollToFirstError();
                return false;
            }
        });

        $(document).on('click', function(event) {
            if ($(event.target).is('#card-modal-1-backdrop-1')) {
                toggleModalCard('card-modal-1');
            } else if ($(event.target).is('#card-modal-backdrop')) {
                toggleModalCard('modal');
            }
        });
    });

    document.querySelector('input[name="year"]').addEventListener('input', function (e) {
        this.value = this.value.replace(/\D/g, '').slice(0, 4);
    });

    function scrollToFirstError() {
        const firstErrorElement = $('[class*="tooltip"]').first();
        if (firstErrorElement.length) {
            const offset = firstErrorElement.offset();
            $('html, body').animate({
                scrollTop: offset.top - 100
            }, 500);
        }
    }

    $('#remove_image').on('change', function() {
        if (this.checked) {
            $('#modal').removeClass('hidden');
            $('#card-modal-backdrop').removeClass('hidden');
        }
    });

    $('#modal .no-button').on('click', function(event) {
        event.preventDefault();
        $('#modal').addClass('hidden');
        $('#card-modal-backdrop').addClass('hidden');
        $('#remove_image').prop('checked', false);
    });

    $('#modal .yes-button').on('click', function(event) {
        event.preventDefault();
        $('#modal').addClass('hidden');
        $('#card-modal-backdrop').addClass('hidden');
        $('#remove_image').prop('checked', true);

        if (profileImage.src && profileImage.src !== '') {
            profileImage.src = "{{ asset('assets/image-placeholder.png') }}";
            const fileInput = document.getElementById('dropzone-file');
            fileInput.value = '';
            const fileInput2 = document.getElementById('existing_image');
            if (fileInput2) fileInput2.value = '';

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

    document.getElementById('remove-photo').addEventListener('click', function(event) {
        event.preventDefault();
        if (profileImage.src && profileImage.src !== '') {
            profileImage.src = "{{ asset('assets/image-placeholder.png') }}";
            const fileInput = document.getElementById('dropzone-file');
            fileInput.value = '';
            const fileInput2 = document.getElementById('existing_image');
            if (fileInput2) fileInput2.value = '';

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
        const backdropID = modalID === 'modal' ? 'card-modal-backdrop' : 'card-modal-1-backdrop';
        const backdrop = document.getElementById(backdropID);

        modal.classList.toggle('hidden');
        modal.classList.toggle('flex');
        
        if(backdropID == 'card-modal-1-backdrop'){
        backdrop.classList.toggle('hidden');
        backdrop.classList.toggle('flex');
        }
    }
</script>

@endsection