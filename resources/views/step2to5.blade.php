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


                <form method="POST" action="{{ route('step2to5.update', $user->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div>
                        <div class="flex flex-col items-center justify-center w-full md:w-1/2 mx-auto">
                            <div class="mb-2 text-left w-full">
                                <span>
                                    @isset($step2Page->sub_heading_text)
                                        @php
                                            $profile_photo_guidelines_url = route('profile_photo_guidelines', [
                                                'lang' => $selectedLanguage->abbreviation ?? 'en',
                                            ]);
                                            $new_sub_heading_text = preg_replace(
                                                '/\{\{\s*route\s*\(\s*[\'"]profile_photo_guidelines[\'"].*?\}\}\s*/s',
                                                $profile_photo_guidelines_url,
                                                $step2Page->sub_heading_text,
                                            );
                                        @endphp
                                        {!! $new_sub_heading_text !!}
                                    @endisset
                                </span>

                            </div>
                            <div class="relative w-full">
                                <button type="button" id="remove-photo-btn" onclick="removePhoto()" title="Remove photo"
                                    class="hidden absolute top-2 right-2 z-10 p-2 rounded-full bg-red-500 text-white hover:bg-red-600 shadow focus:outline-none focus:ring-2 focus:ring-red-400"
                                    aria-label="Remove photo">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                                <label for="dropzone-file"
                                    class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-white hover:bg-gray-100">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6 p-4">
                                        @if ($user->profile_image)
                                            <img id="profile-image" src="{{ $user->profile_image }}"
                                                class="w-32 h-32 rounded mb-4 cursor-pointer">
                                        @elseif ($user->gender == 'male')
                                            <img id="profile-image" src="{{ asset('users_images/male.png') }}"
                                                class="w-32 h-32 rounded-full mb-4 cursor-pointer">
                                        @elseif ($user->gender == 'female')
                                            <img id="profile-image" src="{{ asset('users_images/female.png') }}"
                                                class="w-32 h-32 rounded-full mb-4 cursor-pointer">
                                        @elseif ($user->gender == 'prefer not to say')
                                            <img id="profile-image" src="{{ asset('users_images/neutral.png') }}"
                                                class="w-32 h-32 rounded-full mb-4 cursor-pointer">
                                        @endif
                                        <p class="mb-2 text-md text-gray-500 ">
                                            @isset($step2Page->photo_placeholder)
                                                {{ $step2Page->photo_placeholder }}
                                            @endisset
                                        </p>
                                        <p class="text-gray-500 text-md">
                                            @isset($step2Page->photo_label)
                                                {{ $step2Page->photo_label }}
                                            @endisset
                                        </p>
                                    </div>
                                    <input id="dropzone-file" name="image" type="file" onchange="previewImage(this)"
                                        class="hidden" />
                                    <input type="hidden" name="remove_profile_photo" id="remove_profile_photo"
                                        value="0">
                                </label>
                            </div>
                            @error('image')
                                <div id="profile-error" class="mt-10 relative tooltip -bottom-4 group-hover:flex">
                                    <div role="tooltip"
                                        class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full rounded">
                                        <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                    </div>
                                </div>
                            @enderror
                        </div>

                        <div class="mt-6 flex justify-center space-x-2 md:col-span-2">
                            <button type="button" onclick="showSkipConfirmation()" class="button-exp-fill w-auto">
                                @isset($step2Page->skip_button_label)
                                    {{ $step2Page->skip_button_label }}
                                @endisset
                            </button>
                            <button type="submit" id="nextButton"
                                class="bg-greenXS px-3 py-2 text-white w-auto opacity-50 cursor-not-allowed font-FuturaMdCnBT rounded-sm"
                                disabled>
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
    <div id="skipModal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity z-50"
        aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                <div
                    class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg w-full modal-border1">
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="text-center w-full">
                            <h3 class="font-FuturaMdCnBT text-gray-700 mb-4">
                                @isset($step2Page->skip_confirmation_heading)
                                    {{ $step2Page->skip_confirmation_heading }}
                                @endisset
                            </h3>
                            <p class="text-gray-600">
                                @isset($step2Page->skip_confirmation_message)
                                    {{ $step2Page->skip_confirmation_message }}
                                @endisset
                            </p>
                        </div>
                    </div>
                    <div class="px-4 pb-6 pt-4 sm:flex sm:flex-row-reverse sm:px-6 justify-center gap-3">
                        <a href="{{ route('step3to5', ['lang' => $selectedLanguage->abbreviation, 'skip' => 1]) }}"
                            class="inline-flex w-full justify-center rounded bg-primary px-3 py-2 font-FuturaMdCnBT text-lg text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-blue-400 sm:w-auto">{{ $siteText['skip_ok_btn_text'] ?? 'Yes, skip it!' }}</a>
                        <button type="button" onclick="hideSkipConfirmation()"
                            class="inline-flex w-full justify-center rounded bg-gray-300 px-3 py-2 font-FuturaMdCnBT text-lg text-gray-700 hover:bg-gray-400 sm:w-auto">{{ $siteText['no_take_btn_text'] ?? 'No, take me back' }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="imageSizeErrorModal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity z-50"
        aria-labelledby="image-size-error-modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                <div
                    class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg w-full modal-border1">
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="text-center w-full">
                            <h3 id="image-size-error-modal-title" class="font-FuturaMdCnBT text-gray-700 mb-4">
                                Upload Error
                            </h3>
                            <p id="imageSizeErrorMessage" class="text-gray-600">
                                The image must be less than 10MB.
                            </p>
                        </div>
                    </div>
                    <div class="px-4 pb-6 pt-4 sm:flex sm:flex-row-reverse sm:px-6 justify-center gap-3">
                        <button type="button" onclick="hideImageSizeErrorModal()"
                            class="inline-flex w-full justify-center rounded bg-primary px-3 py-2 font-FuturaMdCnBT text-lg text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-blue-400 sm:w-auto">
                            OK
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
        @php
            $defaultAvatar = 'male.png';
            if ($user->gender == 'female') {
                $defaultAvatar = 'female.png';
            } elseif ($user->gender == 'prefer not to say') {
                $defaultAvatar = 'neutral.png';
            }
        @endphp
        var defaultAvatarUrl = "{{ asset('users_images/' . $defaultAvatar) }}";
        var maxProfileImageSizeBytes = 10 * 1024 * 1024;

        window.addEventListener("pageshow", function(event) {
            if (event.persisted) {
                window.location.reload();
            } else {
                let sessionImage = sessionStorage.getItem("uploaded_profile_image");
                if (sessionImage) {
                    document.getElementById("profile-image").src = sessionImage;
                    $('#profile-error').addClass('hidden');
                    updateRemoveButtonVisibility();
                }
            }
        });

        function showImageSizeErrorModal(message = 'The image must be less than 10MB.') {
            var modal = document.getElementById('imageSizeErrorModal');
            var messageEl = document.getElementById('imageSizeErrorMessage');

            if (messageEl) {
                messageEl.textContent = message;
            }

            if (modal) {
                modal.classList.remove('hidden');
            }
        }

        function hideImageSizeErrorModal() {
            var modal = document.getElementById('imageSizeErrorModal');
            if (modal) {
                modal.classList.add('hidden');
            }
        }

        function updateRemoveButtonVisibility() {
            var profileImage = document.getElementById('profile-image');
            var btn = document.getElementById('remove-photo-btn');
            if (!profileImage || !btn) return;
            var src = (profileImage.src || '').split('?')[0];
            var isDataUrl = profileImage.src && profileImage.src.indexOf('data:') === 0;
            var isDefault = src.indexOf('male.png') !== -1 || src.indexOf('female.png') !== -1 || src.indexOf(
                'neutral.png') !== -1;
            if (isDataUrl || !isDefault) {
                btn.classList.remove('hidden');
            } else {
                btn.classList.add('hidden');
            }
        }

        function removePhoto() {
            var profileImage = document.getElementById('profile-image');
            var fileInput = document.getElementById('dropzone-file');
            var nextButton = document.getElementById('nextButton');
            var removeFlag = document.getElementById('remove_profile_photo');
            if (profileImage) {
                profileImage.src = defaultAvatarUrl;
                profileImage.classList.add('rounded-full');
                profileImage.classList.remove('rounded');
            }
            if (fileInput) fileInput.value = '';
            if (removeFlag) removeFlag.value = '1';
            if (nextButton) {
                nextButton.disabled = true;
                nextButton.classList.add('opacity-50', 'cursor-not-allowed');
                nextButton.classList.remove('opacity-100');
            }
            sessionStorage.removeItem('uploaded_profile_image');
            document.getElementById('remove-photo-btn').classList.add('hidden');
        }

        function previewImage(input) {
            const profileImage = document.getElementById('profile-image');
            const nextButton = document.getElementById('nextButton');

            if (input.files && input.files[0]) {
                if (input.files[0].size > maxProfileImageSizeBytes) {
                    input.value = '';
                    nextButton.disabled = true;
                    nextButton.classList.add('opacity-50', 'cursor-not-allowed');
                    nextButton.classList.remove('opacity-100');
                    showImageSizeErrorModal('The image must be less than 10MB.');
                    updateRemoveButtonVisibility();
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    profileImage.src = e.target.result;
                    profileImage.classList.remove('rounded-full');
                    profileImage.classList.add('rounded');
                    nextButton.disabled = false;
                    nextButton.classList.remove('opacity-50', 'cursor-not-allowed');
                    nextButton.classList.add('opacity-100');
                    document.getElementById('remove_profile_photo').value = '0';
                    updateRemoveButtonVisibility();
                };
                reader.readAsDataURL(input.files[0]);
            } else {
                nextButton.disabled = true;
                nextButton.classList.add('opacity-50', 'cursor-not-allowed');
                nextButton.classList.remove('opacity-100');
                updateRemoveButtonVisibility();
            }
        }

        $(document).ready(function() {
            const nextButton = document.getElementById('nextButton');
            const profileImage = document.getElementById('profile-image');

            @php
                $hasCustomImage = false;
                if ($user->profile_image) {
                    $imageBasename = basename($user->profile_image);
                    $defaultImages = ['male.png', 'female.png', 'neutral.png'];
                    $hasCustomImage = !in_array($imageBasename, $defaultImages);
                }
            @endphp

            @if ($hasCustomImage)
                nextButton.disabled = false;
                nextButton.classList.remove('opacity-50', 'cursor-not-allowed');
                nextButton.classList.add('opacity-100');
            @else
                nextButton.disabled = true;
                nextButton.classList.add('opacity-50', 'cursor-not-allowed');
                nextButton.classList.remove('opacity-100');
            @endif
            updateRemoveButtonVisibility();

            @if ($errors->has('image'))
                showImageSizeErrorModal(@json($errors->first('image')));
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
