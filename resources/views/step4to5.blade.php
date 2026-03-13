@extends('layouts.template')

@section('content')
    <div class="mx-auto max-w-2xl lg:max-w-4xl my-6">
        <div class="bg-white rounded p-4 w-full col-span-12 md:col-span-9 mx-auto">
            <div class="bg-white border border-gray-100 pb-8 px-4 shadow rounded-md sm:px-6 my-4">
                <div class="py-2 flex items-center justify-center">
                    <h1 class="font-FuturaMdCnBT text-primary text-3xl md:text-4xl lg:text-5xl mb-4 mt-10">
                        @isset($step4Page->main_heading)
                            {{ $step4Page->main_heading }}
                        @endisset
                    </h1>
                </div>
                <p for="" class=" text-black mt-2">
                    {!! $step4Page->main_label ??
                        'If you are signing up as a driver, please note that to be eligible to post ProximaRide and Extra+ Rides, you must state your vehicle details on every ride, and must upload a valid driver’s license ' !!}
                </p>
                <p for="" class=" text-black mt-4">
                    {!! $step4Page->sub_main_label ??
                        'If you intend to use ProximaRide as a passenger only, then this point is not applicable to you. You may “Skip” it' !!}
                </p>

                <form class="rounded-lg" method="POST"
                    action="{{ route('step4to5.store', ['id' => $user->id, 'lang' => $selectedLanguage->abbreviation]) }}"
                    enctype="multipart/form-data">
                    @csrf


                    <div
                        class="bg-white rounded-b-lg overflow-hidden shadow-3xl grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4 p-4 mt-6">
                        <div class="md:col-span-2">
                            <div class="relative w-full">
                                <button type="button" id="remove-license-photo-btn" onclick="removeLicensePhoto()"
                                    title="Remove photo"
                                    class="hidden absolute top-2 right-2 z-10 p-2 rounded-full bg-red-500 text-white hover:bg-red-600 shadow focus:outline-none focus:ring-2 focus:ring-red-400"
                                    aria-label="Remove photo">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                                <label for="dropzone-file1"
                                    class="flex flex-col items-center justify-center w-full h-auto border-2 border-gray-300 border-dashed rounded cursor-pointer bg-white hover:bg-gray-100">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6 p-4">
                                        @if (!empty($user->driver_liscense))
                                            <img id="profile-image1"
                                                class="w-48 h-48 object-contain mb-3 cursor-pointer rounded-lg"
                                                src="{{ $user->driver_liscense }}" alt="Driver license">
                                        @else
                                            <img id="profile-image1" class="w-12 h-12 object-contain mb-3 cursor-pointer"
                                                src="{{ asset('assets/image-placeholder.png') }}"
                                                alt="Driver license placeholder">
                                        @endif
                                        <p class="text-sm lg:text-lg text-gray-900">
                                            @isset($step4Page->driver_liscense_photo_label)
                                                {{ $step4Page->driver_liscense_photo_label }}
                                            @endisset
                                        </p>
                                        <p class="text-sm lg:text-base text-gray-900 font-normal text-center">
                                            @isset($step4Page->photo_detail_label)
                                                {{ $step4Page->photo_detail_label }}
                                            @endisset
                                        </p>
                                    </div>
                                    <input id="dropzone-file1" name="driver_liscense" type="file"
                                        onchange="previewImage1(this)" class="hidden" />
                                    <input type="hidden" name="remove_driver_license" id="remove_driver_license"
                                        value="0">
                                </label>
                            </div>
                            @error('driver_liscense')
                                <div class="relative tooltip -bottom-4 group-hover:flex">
                                    <div role="tooltip"
                                        class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                                        <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                    </div>
                                </div>
                            @enderror
                            <div class="mt-4 flex justify-center space-x-2 md:col-span-2">
                                <button type="button" onclick="showSkipConfirmation()" class="w-42 button-exp-fill">
                                    @isset($step4Page->skip_license)
                                        {{ $step4Page->skip_license }}
                                    @endisset
                                </button>
                                <button type="submit" id="nextButton"
                                    class="bg-greenXS px-3 py-2 text-white w-auto opacity-50 cursor-not-allowed font-FuturaMdCnBT rounded-sm"
                                    disabled>
                                    @isset($step4Page->next_button_label)
                                        {{ $step4Page->next_button_label }}
                                    @endisset
                                </button>
                            </div>
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
                            <h6 class="text-2xl font-FuturaMdCnBT text-gray-900 mb-4">
                                @isset($step4Page->skip_confirmation_heading)
                                    {{ $step4Page->skip_confirmation_heading }}
                                @endisset
                            </h6>
                            <p class="text-gray-600">
                                @isset($step4Page->skip_confirmation_message)
                                    {{ $step4Page->skip_confirmation_message }}
                                @endisset
                            </p>
                        </div>
                    </div>
                    <div class="px-4 pb-6 pt-4 sm:flex sm:flex-row-reverse sm:px-6 justify-center gap-3">
                        <a href="{{ route('step5to5', ['lang' => $selectedLanguage->abbreviation, 'skip' => 1]) }}"
                            class="inline-flex w-full justify-center rounded bg-primary px-3 py-2 font-FuturaMdCnBT text-lg text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-blue-400 sm:w-auto">{{ $siteText['skip_ok_btn_text'] ?? 'Yes, skip it!' }}</a>
                        <button type="button" onclick="hideSkipConfirmation()"
                            class="inline-flex w-full justify-center rounded bg-gray-300 px-3 py-2 font-FuturaMdCnBT text-lg text-gray-700 hover:bg-gray-400 sm:w-auto">{{ $siteText['no_take_btn_text'] ?? 'No, take me back' }}</button>
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
        const profileImage1 = document.getElementById('profile-image1');
        const licensePhotoPlaceholderUrl = "{{ asset('assets/image-placeholder.png') }}";

        function updateRemoveLicensePhotoButtonVisibility() {
            const img = document.getElementById('profile-image1');
            const btn = document.getElementById('remove-license-photo-btn');
            if (!img || !btn) return;
            const isPlaceholder = (img.src || '').indexOf('image-placeholder.png') !== -1;
            if (isPlaceholder) {
                btn.classList.add('hidden');
            } else {
                btn.classList.remove('hidden');
            }
        }

        function removeLicensePhoto() {
            const img = document.getElementById('profile-image1');
            const fileInput = document.getElementById('dropzone-file1');
            const removeFlag = document.getElementById('remove_driver_license');
            if (img) {
                img.src = licensePhotoPlaceholderUrl;
                img.className = 'w-12 h-12 object-contain mb-3 cursor-pointer';
            }
            if (fileInput) fileInput.value = '';
            if (removeFlag) removeFlag.value = '1';
            validateStep4Form();
            document.getElementById('remove-license-photo-btn').classList.add('hidden');
        }

        function previewImage1(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    profileImage1.src = e.target.result;
                    profileImage1.className = 'w-48 h-48 object-contain mb-3 cursor-pointer rounded-lg';
                    document.getElementById('remove_driver_license').value = '0';
                    updateRemoveLicensePhotoButtonVisibility();
                    validateStep4Form();
                };
                reader.readAsDataURL(input.files[0]);
            } else {
                updateRemoveLicensePhotoButtonVisibility();
                validateStep4Form();
            }
        }

        function showSkipConfirmation() {
            document.getElementById('skipModal').classList.remove('hidden');
        }

        function hideSkipConfirmation() {
            document.getElementById('skipModal').classList.add('hidden');
        }

        function confirmSkip() {
            // Create a hidden form to submit with skip action
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route('step4to5.store', ['id' => $user->id, 'lang' => $selectedLanguage->abbreviation]) }}';

            // Add CSRF token
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            form.appendChild(csrfInput);

            // Add action input
            const actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            actionInput.value = 'skip_license';
            form.appendChild(actionInput);

            document.body.appendChild(form);
            form.submit();
        }

        // Form validation for Step 4 (valid if file selected or existing license image shown and not removed)
        function validateStep4Form() {
            const fileInput = document.querySelector('input[name="driver_liscense"]');
            const nextButton = document.getElementById('nextButton');
            const img = document.getElementById('profile-image1');
            const removeFlag = document.getElementById('remove_driver_license');
            const hasFile = fileInput && fileInput.files && fileInput.files.length > 0;
            const hasExistingAndNotRemoved = img && (img.src || '').indexOf('image-placeholder.png') === -1 && removeFlag &&
                removeFlag.value !== '1';
            const isValid = hasFile || hasExistingAndNotRemoved;

            if (nextButton) {
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
        }

        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.querySelector('input[name="driver_liscense"]');
            if (fileInput) {
                fileInput.addEventListener('change', validateStep4Form);
            }
            updateRemoveLicensePhotoButtonVisibility();
            validateStep4Form();
        });
    </script>
@endsection
