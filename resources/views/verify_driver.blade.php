@extends('layouts.template')

@section('content')
    <div class="grid grid-cols-12 gap-4 md:container md:mx-auto  my-6 md:my-10 xl:my-14 px-4 xl:px-0">
        @include('layouts.inc.profile_sidebar')

        <div class="bg-white border rounded p-4 border-gray-200 w-full col-span-12 lg:col-span-9 shadow">
            @if (session('message'))
                <div id="myModal" class="relative z-50" id="delete_message_confirmation" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                    <div onclick="closeModal()"  class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                        <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                            <div class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
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
                                            <p class="can-exp-p text-center">  {{ session('message') }}</p>
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

            <div class="pb-2">
                <h1 class="mb-0">
                    @isset($driverSettingPage->main_heading)
                        {{ $driverSettingPage->main_heading }}
                    @endisset
                </h1>
                <p class="text-gray-900">
                    @isset($driverSettingPage->driver_license_description_text)
                        {{ $driverSettingPage->driver_license_description_text }}
                    @endisset
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-1 lg:grid-cols-1 w-full md:w-1/2 gap-4 mt-4">
                <form method="POST" action="{{ route('driver.verify.update', $user->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <div class="mt-4 md:col-span-2">
                            <div class="text-gray-500 mb-2">
                                <p>
                                    @isset($driverSettingPage->driver_license_label)
                                        {{ $driverSettingPage->driver_license_label }}
                                    @endisset
                                </p>
                            </div>
                        </div>
                        <div class="md:col-span-2 relative">
                            <label for="dropzone-file" id="dropzone-file-label"
                                class="flex flex-col items-center justify-center w-full h-auto border-2 border-gray-300 border-dashed rounded cursor-pointer bg-white hover:bg-gray-100">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6 p-4">
                                    @if (session('uploaded_image'))
                                        <img id="profile-image" class="w-40 h-40 object-contain mb-4 cursor-pointer" src="{{ asset('driver_liscenses/' . session('uploaded_image')) }}" alt="Uploaded Image">
                                    @elseif ($user->driver_liscense)
                                        <img id="profile-image" class="w-40 h-40 object-contain mb-4 cursor-pointer" src="{{ $user->driver_liscense }}" alt="Uploaded Image">
                                    @else
                                        <img id="profile-image" class="w-12 h-12 object-contain mb-3" src="{{ asset('assets/image-placeholder.png')}}">
                                    @endif
                                    <div id="hide-text1" class="justify-center text-center">
                                        <p class="text-sm lg:text-lg text-gray-900">
                                            @isset($driverSettingPage->web_upload_image_placeholder)
                                                {{ $driverSettingPage->web_upload_image_placeholder }}
                                            @endisset
                                            <span class="font-semibold text-primary">
                                                @isset($driverSettingPage->mobile_choose_file_image_placeholder)
                                                    {{ $driverSettingPage->mobile_choose_file_image_placeholder }}
                                                @endisset
                                            </span>
                                        </p>
                                        <p class="text-sm lg:text-base text-gray-900 font-normal">
                                            @isset($driverSettingPage->mobile_image_type_placeholder)
                                                {{ $driverSettingPage->mobile_image_type_placeholder }}
                                            @endisset
                                        </p>
                                    </div>
                                </div>
                                <input id="dropzone-file" name="image" type="file" onchange="previewImage(this)" class="hidden" />
                                @if (session('uploaded_image'))
                                    <input type="hidden" name="existing_image" value="{{ session('uploaded_image') }}">
                                @elseif ($user->driver_liscense)
                                    @php
                                        $imageName = basename($user->driver_liscense);
                                    @endphp
                                    <input id="existing_image" type="hidden" name="existing_image" value="{{ $imageName }}">
                                @endif
                            </label>
                            @error('image')
                              @if ($message !== 'The image is not uploaded yet')
                                <div class="relative tooltip -bottom-4 group-hover:flex flex justify-center">
                                  <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                                    <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                  </div>
                               </div>
                              @endif
                            @enderror
                        </div>
                        <div id="hide-buttons" class="hidden justify-center gap-2 mt-3">
                            <label for="edit-file" class="text-white bg-primary p-2 w-20 rounded cursor-pointer flex items-center justify-center gap-1">
                                <div class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                        stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                    </svg>
                                    <span class="text-sm">Edit</span>
                                    <input id="edit-file" name="existing_image" type="file" onchange="previewImage(this)" class="hidden" />
                                </div>                                
                            </label>
                            <button id="remove-photo" type="button" class="text-white bg-primary p-2 w-20 rounded flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                    stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                </svg>
                                <span class="text-sm">Delete</span>
                            </button>
                        </div>
                    </div>
                    <div class="mt-4 flex justify-center">
                        <button type="submit" class="submitBtn button-exp-fill {{ isset($user->driver_liscense) && $user->driver_liscense != "" ? "disabled:bg-primary/20 cursor-not-allowed disabled:border-none" : "" }}"  {{ isset($user->driver_liscense) && $user->driver_liscense != "" ? "disabled" : "" }}>
                            @isset($driverSettingPage->upload_new_image_btn_label)
                                {{ $driverSettingPage->upload_new_image_btn_label }}
                            @endisset
                        </button>
                        
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<script>
    var hasErrors = {{ $errors->any() ? 'true' : 'false' }};
    var uploadedImage = "{{ session('uploaded_image') }}";
    var hasUploadedImage = uploadedImage && uploadedImage !== "null";
    var vehicleImage = "{{ $user->driver_liscense }}";
    var hasVehicleImage = vehicleImage && vehicleImage !== "null";

    $(document).ready(function() {
        if (hasErrors && hasUploadedImage || hasVehicleImage) {
            $('#profile-image').removeClass('w-12');
            $('#profile-image').removeClass('h-12');
            $('#profile-image').removeClass('object-contain');
            $('#profile-image').removeClass('mb-3');
            $('#profile-image').addClass('w-full');
            $('#profile-image').addClass('h-full');
            $('#profile-image').addClass('object-cover');
            $('#hide-text1').addClass('hidden');
            $('#show-button').removeClass('hidden');
            $('#hide-buttons').removeClass('hidden');
            $('#hide-buttons').addClass('flex');
            $('#dropzone-file-label').removeClass('cursor-pointer');
            $('#dropzone-file').prop('readonly', true);
        }
    });

    const profileImage = document.getElementById('profile-image');

    function previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();

            $(".submitBtn").removeAttr('disabled');
            $(".submitBtn").removeClass('disabled:bg-primary/20 cursor-not-allowed disabled:border-none');

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
                $('#show-button').removeClass('hidden');
                $('#hide-buttons').removeClass('hidden');
                $('#hide-buttons').addClass('flex');
                $('#dropzone-file-label').removeClass('cursor-pointer');
                $('#dropzone-file').prop('readonly', true);
            };

            reader.readAsDataURL(input.files[0]);
        }
    }

    document.getElementById('remove-photo').addEventListener('click', function() {
        event.preventDefault();


        $.ajax({
            url: "{{ route('driver.verify.remove') }}",
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}'
            },
            dataType: 'json',
            success: function(result) {
                
            }
        });

        // Check if there is an existing image displayed
        if (profileImage.src && profileImage.src !== '') {
            profileImage.src = "{{ asset('assets/image-placeholder.png')}}";

            // Clear the file input by creating a new input element and replacing the old one
            const fileInput = document.getElementById('dropzone-file');
            fileInput.value = '';
            if (hasVehicleImage) {
                const fileInput2 = document.getElementById('existing_image');
                fileInput2.value = '';
            }





            $('#profile-image').removeClass('w-40');
            $('#profile-image').removeClass('h-40');
            $('#profile-image').removeClass('mb-4');
            $('#profile-image').addClass('w-12');
            $('#profile-image').addClass('h-12');
            $('#profile-image').addClass('object-contain');
            $('#profile-image').addClass('mb-3');
            $('#profile-image').removeClass('w-full');
            $('#profile-image').removeClass('h-full');
            $('#profile-image').removeClass('object-cover');
            $('#hide-text1').removeClass('hidden');
            $('#show-button').addClass('hidden');
            $('#hide-buttons').addClass('hidden');
            $('#hide-buttons').addClass('flex');
            $('#dropzone-file-label').addClass('cursor-pointer');
            $('#dropzone-file').prop('readonly', false);
        }
    });
    function closeModal() {
    const modal = document.getElementById('myModal');
    if (modal) {
        modal.classList.add('hidden');
    }
}
</script>

@endsection
