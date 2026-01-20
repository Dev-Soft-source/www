@extends('layouts.template')

@section('content')

<div class="mx-auto max-w-2xl lg:max-w-4xl my-6">
    <div class="bg-white rounded p-4 w-full col-span-12 md:col-span-9 mx-auto">
        <div class="bg-white border border-gray-100 pb-4 px-4 shadow rounded-md sm:px-10 my-4">
        <div class="pb-2 flex items-center justify-center">
            <h1 class="font-FuturaMdCnBT text-primary text-3xl md:text-4xl lg:text-5xl mb-4 mt-10">
                @isset($step2Page->main_heading)
                    {{ $step2Page->main_heading }}
                @endisset
            </h1>
        </div>
        

        <form method="POST" action="{{ route('step2to5.update',$user->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div>
                <div class="flex flex-col items-center justify-center w-full md:w-1/2 mx-auto">
                    <div class="mb-2 text-left w-full">
                        <span>{!! $step2Page->sub_heading_text ?? "If you are signing up as a driver, then please note that to be eligible to post ProximaRide and Extra-Care Rides, you must upload your profile photo" !!}</span>
                    </div>
                    <label for="dropzone-file"
                        class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-white hover:bg-gray-100">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6 p-4">
                            @if ($user->profile_image)
                                <img id="profile-image" src="{{ $user->profile_image }}" class="w-32 h-32 rounded mb-4 cursor-pointer">
                            @elseif ($user->gender == 'male')
                                <img id="profile-image" src="{{ asset('users_images/male.png') }}" class="w-32 h-32 rounded-full mb-4 cursor-pointer">
                            @elseif ($user->gender == 'female')
                                <img id="profile-image" src="{{ asset('users_images/female.png') }}" class="w-32 h-32 rounded-full mb-4 cursor-pointer">
                            @elseif ($user->gender == 'prefer not to say')
                                <img id="profile-image" src="{{ asset('users_images/neutral.png') }}" class="w-32 h-32 rounded-full mb-4 cursor-pointer">
                            @endif
                            <p class="mb-2 text-sm text-gray-500 ">
                                @isset($step2Page->photo_placeholder)
                                    {{ $step2Page->photo_placeholder }}
                                @endisset
                            </p>
                            <p class="text-gray-500 text-center text-sm">
                                @isset($step2Page->photo_label)
                                    {{ $step2Page->photo_label }}
                                @endisset
                            </p>
                        </div>
                        <input id="dropzone-file" name="image" type="file" onchange="previewImage(this)" class="hidden" />
                    </label>
                    @error('image')
                      <div id="profile-error" class="mt-10 relative tooltip -bottom-4 group-hover:flex">
                        <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full rounded" >
                            <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                        </div>
                      </div>
                    @enderror
                </div>
                
                @if ($errors->has('image') && Str::contains($errors->first('image'), 'greater than 10MB'))
                <div class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                        <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                            <div
                                class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg w-full">
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
                                        <p class="can-exp-p text-center">The image size must not be larger than 10 MB</p>
                                    </div>
                                </div>
                                <div class="px-4 pb-6 pt-4 sm:flex sm:flex-row-reverse sm:px-6 justify-center">
                                    <a href="" class="inline-flex w-full justify-center rounded bg-red-500 px-3 py-2 font-FuturaMdCnBT text-lg text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-red-400 sm:ml-3 sm:w-24">Close</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <div class="mt-6 flex justify-center space-x-2 md:col-span-2">
                    <button type="button" onclick="showSkipConfirmation()" class="button-exp-fill w-42">
                        @isset($step2Page->skip_button_label)
                            {{ $step2Page->skip_button_label }}
                        @endisset
                    </button>
                    <button type="submit" id="nextButton" class="button-exp-fill w-42 opacity-50 cursor-not-allowed" disabled>
                        @isset($step2Page->next_button_label)
                            {{ $step2Page->next_button_label }}
                        @endisset
                    </button>
                </div>
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
                        <h6 class="text-lg font-medium text-gray-900 mb-4">
                            @isset($step2Page->skip_confirmation_heading)
                                {{ $step2Page->skip_confirmation_heading }}
                            @endisset
                        </h6>
                        <p class="text-gray-600">
                            @isset($step2Page->skip_confirmation_message)
                                {{ $step2Page->skip_confirmation_message }}
                            @endisset
                        </p>
                    </div>
                </div>
                <div class="px-4 pb-6 pt-4 sm:flex sm:flex-row-reverse sm:px-6 justify-center gap-3">
                    <a href="{{ route('step3to5', ['lang' => $selectedLanguage->abbreviation]) }}" class="inline-flex w-full justify-center rounded bg-primary px-3 py-2 font-FuturaMdCnBT text-lg text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-blue-400 sm:w-auto">Yes, skip it!</a>
                    <button type="button" onclick="hideSkipConfirmation()" class="inline-flex w-full justify-center rounded bg-gray-300 px-3 py-2 font-FuturaMdCnBT text-lg text-gray-700 hover:bg-gray-400 sm:w-auto">No, take me back</button>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

@section('script')

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
    window.addEventListener("pageshow", function(event) {
        if (event.persisted) {
            window.location.reload();
        } else {
            // Manually check session image and update if necessary
            let sessionImage = sessionStorage.getItem("uploaded_profile_image");
            if (sessionImage) {
                document.getElementById("profile-image").src = sessionImage;
                $('#profile-error').addClass('hidden');
            }
        }
    });

    function previewImage(input) {
        const profileImage = document.getElementById('profile-image');
        const nextButton = document.getElementById('nextButton');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function(e) {
                profileImage.src = e.target.result;
                // Enable the button when image is uploaded
                nextButton.disabled = false;
                nextButton.classList.remove('opacity-50', 'cursor-not-allowed');
                nextButton.classList.add('opacity-100');
            };

            reader.readAsDataURL(input.files[0]);
        } else {
            // Disable button if no file selected
            nextButton.disabled = true;
            nextButton.classList.add('opacity-50', 'cursor-not-allowed');
            nextButton.classList.remove('opacity-100');
        }
    }
    
    // Check on page load if user already has a custom profile image
    $(document).ready(function() {
        const nextButton = document.getElementById('nextButton');
        const profileImage = document.getElementById('profile-image');
        const fileInput = document.getElementById('dropzone-file');
        
        // Check if user has a custom profile image (not the default ones)
        @php
            $hasCustomImage = false;
            if ($user->profile_image) {
                $imageBasename = basename($user->profile_image);
                $defaultImages = ['male.png', 'female.png', 'neutral.png'];
                $hasCustomImage = !in_array($imageBasename, $defaultImages);
            }
        @endphp
        
        @if ($hasCustomImage)
            // User already has a custom profile image
            nextButton.disabled = false;
            nextButton.classList.remove('opacity-50', 'cursor-not-allowed');
            nextButton.classList.add('opacity-100');
        @else
            // User has default image or no image, button stays disabled
            nextButton.disabled = true;
            nextButton.classList.add('opacity-50', 'cursor-not-allowed');
            nextButton.classList.remove('opacity-100');
        @endif
    });

    function showSkipConfirmation() {
        document.getElementById('skipModal').classList.remove('hidden');
    }

    function hideSkipConfirmation() {
        document.getElementById('skipModal').classList.add('hidden');
    }
</script>

@endsection
