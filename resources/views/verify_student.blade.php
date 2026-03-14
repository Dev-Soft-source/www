@extends('layouts.template')

@section('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    @if ($user->student == 1 && \Carbon\Carbon::parse($user->student_card_exp_date) > now())
        <style>
            @keyframes success-pulse {

                0%,
                100% {
                    transform: scale(1);
                    box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.4);
                }

                50% {
                    transform: scale(1.02);
                    box-shadow: 0 0 0 12px rgba(34, 197, 94, 0);
                }
            }

            @keyframes check-draw {
                0% {
                    stroke-dashoffset: 50;
                }

                100% {
                    stroke-dashoffset: 0;
                }
            }

            @keyframes float-confetti {

                0%,
                100% {
                    transform: translateY(0) rotate(0deg);
                    opacity: 1;
                }

                50% {
                    transform: translateY(-8px) rotate(5deg);
                    opacity: 0.9;
                }
            }

            @keyframes shimmer {
                0% {
                    background-position: -200% 0;
                }

                100% {
                    background-position: 200% 0;
                }
            }

            .student-approved-card {
                animation: success-pulse 2s ease-in-out infinite;
            }

            .check-svg path {
                stroke-dasharray: 50;
                stroke-dashoffset: 50;
                animation: check-draw 0.6s ease-out 0.3s forwards;
            }

            .confetti-dot {
                animation: float-confetti 2s ease-in-out infinite;
            }

            .confetti-dot:nth-child(1) {
                animation-delay: 0s;
                left: 10%;
            }

            .confetti-dot:nth-child(2) {
                animation-delay: 0.2s;
                left: 25%;
            }

            .confetti-dot:nth-child(3) {
                animation-delay: 0.4s;
                left: 40%;
            }

            .confetti-dot:nth-child(4) {
                animation-delay: 0.1s;
                left: 60%;
            }

            .confetti-dot:nth-child(5) {
                animation-delay: 0.3s;
                left: 75%;
            }

            .confetti-dot:nth-child(6) {
                animation-delay: 0.5s;
                left: 90%;
            }
        </style>
    @endif
@endsection

@section('content')
    @if (session('message'))
        <div id="myModal" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeModal()"></div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                    <div
                        class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
                        <button onclick="closeModal()"
                            class="absolute top-2 right-2 p-1 rounded-full hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
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
                                <div class="">
                                    <h3 class="text-3xl text-center font-FuturaMdCnBT font-medium text-gray-900 mb-4"
                                        id="modal-title">{!! session('heading') !!}</h3>
                                </div>
                                <div class="mt-2 w-full">
                                    <p class="can-exp-p text-center">{!! session('message') !!}</p>
                                </div>
                            </div>
                        </div>
                        <div class="px-4 pb-6 pt-4 flex items-center space-x-2 sm:space-x-4 sm:px-6 justify-center">
                            @if (session('id'))
                                <a href="{{ route('student.verify', ['lang' => $selectedLanguage->abbreviation, 'id' => session('id')]) }}"
                                    class="inline-flex w-full justify-center rounded bg-greenXS px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white whitespace-nowrap hover:text-white hover:shadow-lg shadow-sm hover:bg-greenXS sm:ml-3 sm:w-fit">
                                    Repost ride</a>
                            @endif
                            <a href="" class="button-exp-fill w-auto">{{ $siteText['close_btn_text'] }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-12 gap-4 md:container md:mx-auto  my-6 md:my-10 xl:my-14 px-4 xl:px-0">
        @include('layouts.inc.profile_sidebar')

        <div class="bg-white border rounded p-4 border-gray-200 w-full col-span-12 lg:col-span-9 shadow">
            {{-- @if (session('message'))
          

            <div id="myModal" class="relative z-50" id="delete_message_confirmation" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div onclick="closeModal()"  class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                    <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                        <div class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
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
                                        <p class="can-exp-p text-center">{{ session('message') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="px-4 pb-6 pt-4 flex items-center space-x-2 sm:space-x-4 sm:px-6 justify-center">
                            <input type="hidden" id="notificationId" value="3094">
                                <a href="#" onclick="closeModal()"  class="button-exp-fill">{{ $siteText['close_btn_text'] }} </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif --}}

            <div class="pb-2">
                <h1 class="mb-0">
                    @isset($studentCardPage->main_heading)
                        {{ $studentCardPage->main_heading }}
                    @endisset
                </h1>
            </div>
            {{-- @if ($user->student == 2)
        <div class="mt-4 rounded-lg px-6 py-3 bg-red-100 text-gray-600" role="alert">
                Your student card is under review. You can update it anytime if needed.
            </div> --}}
            @if ($user->student == 1 && \Carbon\Carbon::parse($user->student_card_exp_date) > now())
                <div id="student-approved-celebration"
                    class="mt-4 relative overflow-hidden rounded-xl px-6 py-6 student-approved-card bg-gradient-to-r from-emerald-50 via-green-50 to-teal-50 border-2 border-emerald-200"
                    role="alert">
                    <div class="absolute inset-0 opacity-30">
                        <span class="confetti-dot absolute w-2 h-2 rounded-full bg-emerald-500 top-2"></span>
                        <span class="confetti-dot absolute w-2 h-2 rounded-full bg-green-400 top-3"></span>
                        <span class="confetti-dot absolute w-2 h-2 rounded-full bg-teal-400 top-4"></span>
                        <span class="confetti-dot absolute w-2 h-2 rounded-full bg-emerald-400 top-2"></span>
                        <span class="confetti-dot absolute w-2 h-2 rounded-full bg-green-500 top-3"></span>
                        <span class="confetti-dot absolute w-2 h-2 rounded-full bg-teal-500 top-4"></span>
                    </div>
                    <div class="relative flex flex-col sm:flex-row items-center justify-center gap-4">
                        <div
                            class="flex-shrink-0 w-16 h-16 rounded-full bg-emerald-500 flex items-center justify-center shadow-lg">
                            <svg class="check-svg w-10 h-10 text-white" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 6L9 17l-5-5" />
                            </svg>
                        </div>
                        <div class="text-center sm:text-left">
                            <h3 class="text-xl font-bold text-emerald-800 animate__animated animate__fadeIn">Student Card
                                Approved!</h3>
                            <p class="text-emerald-700 mt-1">Thank you for uploading your student card. You're all set to
                                enjoy student benefits.</p>
                        </div>
                    </div>
                </div>
            @elseif ($user->student == 1 && \Carbon\Carbon::parse($user->student_card_exp_date) <= now())
                <div class="mt-4 rounded-lg px-6 py-3 bg-red-100 text-gray-600" role="alert">
                    Your student card has been expired.
                </div>
            @else
                <p class="text-gray-900 mt-3">
                    @isset($studentCardPage->student_card_description_text)
                        {{ $studentCardPage->student_card_description_text }}
                    @endisset
                </p>
            @endif
            <form method="POST" action="{{ route('student.verify.update', $user->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="w-full md:w-1/2 mt-4">
                    <div class="mb-4">
                        @if (!empty($user->student_card))
                            <div for="dropzone-file"
                                class="flex flex-col items-center justify-center w-full h-auto p-4 border-2 border-gray-300 border-dashed rounded bg-white hover:bg-gray-100">
                                <div class="flex flex-col items-center justify-center pb-6">
                                    <img id="profile-image" src="{{ $user->student_card }}" class="w-48 h-48 object-cover">
                                </div>
                            </div>
                        @else
                            <label for="dropzone-file"
                                class="flex flex-col items-center justify-center w-full h-auto p-4 border-2 border-gray-300 border-dashed rounded cursor-pointer bg-white hover:bg-gray-100">
                                <div class="flex flex-col items-center justify-center pb-6">
                                    <img id="profile-image" class="w-14 h-14 object-contain mt-4"
                                        src="{{ asset('assets/image-placeholder.png') }}">
                                    <p class="text-sm lg:text-lg text-gray-900">
                                        @if ($user->student == 0)
                                            <label for="">
                                                @isset($studentCardPage->student_card_image_placeholder)
                                                    {{ $studentCardPage->student_card_image_placeholder }}
                                                @endisset
                                            </label>
                                        @else
                                            <label for="">Use a different copy</label>
                                        @endif
                                    </p>
                                    <p class="text-sm lg:text-base text-gray-900 font-normal">
                                        @isset($studentCardPage->mobile_image_type_placeholder)
                                            {{ $studentCardPage->mobile_image_type_placeholder }}
                                        @endisset
                                    </p>
                                </div>
                                <input id="dropzone-file" name="student_card" type="file" onchange="previewImage(this)" accept="image/*"
                                    class="hidden" />
                            </label>
                        @endif
                        @if (!empty($user->student_card))
                            @php
                                $imageName = basename($user->student_card);
                            @endphp
                            <input type="hidden" name="existing_image" value="{{ $imageName }}">
                            <label for="dropzone-file" class="cursor-pointer">
                                <div class="text-primary border border-primary px-6 py-3 rounded w-full mt-3 text-center">
                                    <input id="dropzone-file" name="student_card" type="file" 
                                        onchange="previewImage(this)" class="hidden" />
                                    {{ $studentCardPage->upload_another_image_btn_label ?? 'Upload another image' }}
                                </div>
                            </label>
                        @endif
                        @if ($user->student_card_exp_date)
                            <input type="hidden" id="expDate" name="exp_date"
                                value="{{ $user->student_card_exp_date }}">
                        @endif
                        @error('student_card')
                            <div class="relative tooltip -bottom-4 group-hover:flex flex justify-center">
                                <div role="tooltip"
                                    class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                                    <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                </div>
                            </div>
                        @enderror
                    </div>
                    <div class="mt-10 flex flex-col items-center justify-center">
                        <label for="dateInput"
                            class="text-xl md:text-2xl flex items-center gap-2 text-primary font-FuturaMdCnBT justify-center">
                            @isset($studentCardPage->expiry_date_label)
                                {{ $studentCardPage->expiry_date_label }}
                            @endisset
                        </label>
                        <div class="flex gap-4 items-center mt-2 justify-center">
                            <div class="relative w-40">
                                <input type="text" id="monthInput" name="month" placeholder="Month" readonly
                                    onchange="changefield();"
                                    class="border p-2 w-full bg-gray-50 rounded border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600 cursor-pointer">
                                <div id="monthDropdown"
                                    class="absolute z-10 hidden bg-white rounded-md py-2 px-4 w-full shadow-lg cursor-pointer">
                                    <!-- Dropdown content will be dynamically generated here -->
                                </div>
                            </div>
                            <div class="relative w-24">
                                <input type="text" id="yearInput" name="year" placeholder="Year" readonly
                                    onchange="changefield();"
                                    class="border p-2 w-full bg-gray-50 rounded border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600 cursor-pointer">
                                <div id="yearDropdown"
                                    class="absolute z-10 w-full py-2 px-4 hidden bg-white rounded-md shadow-lg cursor-pointer">
                                    <!-- Dropdown content will be dynamically generated here -->
                                </div>
                            </div>
                        </div>
                        <input type="hidden" id="expiryDate" name="expiry_date">
                    </div>

                    <div class="mt-4 flex justify-center">
                        <button id="submit_btn" type="submit"
                            class="submitBtn w-auto px-6 button-exp-fill opacity-50 cursor-not-allowed" disabled>
                            @isset($studentCardPage->submit_for_verification_button_text)
                                {{ $studentCardPage->submit_for_verification_button_text }}
                            @else
                                Submit for Verification
                            @endisset
                        </button>
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
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    @php
        $flatpickrLocale = match (app()->getLocale()) {
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
    @if ($flatpickrLocale)
        <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/{{ $flatpickrLocale }}.js"></script>
    @endif
    @if ($user->student == 1 && \Carbon\Carbon::parse($user->student_card_exp_date) > now())
        <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.2/dist/confetti.browser.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof confetti === 'function') {
                    setTimeout(function() {
                        confetti({
                            particleCount: 80,
                            spread: 70,
                            origin: {
                                y: 0.6
                            },
                            colors: ['#10b981', '#34d399', '#6ee7b7', '#059669', '#14b8a6', '#2dd4bf']
                        });
                        setTimeout(function() {
                            confetti({
                                particleCount: 50,
                                angle: 60,
                                spread: 55,
                                origin: {
                                    x: 0
                                },
                                colors: ['#10b981', '#34d399', '#059669']
                            });
                            confetti({
                                particleCount: 50,
                                angle: 120,
                                spread: 55,
                                origin: {
                                    x: 1
                                },
                                colors: ['#14b8a6', '#2dd4bf', '#0d9488']
                            });
                        }, 200);
                    }, 300);
                }
            });
        </script>
    @endif

    <script>
        function hideTooltip(parms) {
            if ($(this).parent().find('.tooltip').length > 0 && parms != 'label') {
                $(this).parent().find('.tooltip').addClass('hidden');
            } else if ($(this).parent().parent().find('.tooltip').length > 0 && parms != 'label') {
                $(this).parent().parent().find('.tooltip').addClass('hidden');
            } else if ($(this).parent().parent().parent().find('.tooltip').length > 0) {
                $(this).parent().parent().parent().find('.tooltip').addClass('hidden');
            }
        }

        const inputs = document.querySelectorAll('input, textarea');
        inputs.forEach(input => {
            input.addEventListener('input', hideTooltip); // no parameter on input typing
        });

        const labels = document.querySelectorAll('label');
        labels.forEach(input => {
            input.addEventListener('click', function(e) {
                hideTooltip.call(this, 'label'); // pass 'testing' on label click
            });
        });

        function closeModal() {
            const modal = document.getElementById('myModal');
            if (modal) {
                modal.classList.add('hidden');
            }
        }

        // Also make the "Close" button link use the same function
        document.addEventListener('DOMContentLoaded', function() {
            const closeLinks = document.querySelectorAll('a[href=""].button-exp-fill');
            closeLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    closeModal();
                });
            });
        });
        const profileImage = document.getElementById('profile-image');
        const submitBtn = document.getElementById('submit_btn');

        function changefield() {
            submitBtn.removeAttribute('disabled');
        }

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
                    profileImage.className = 'w-56 h-56 object-contain mb-3 cursor-pointer rounded-lg';

                    // Enable button and make it full color when image is uploaded
                    submitBtn.removeAttribute('disabled');
                    submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                    submitBtn.classList.add('opacity-100');
                };

                reader.readAsDataURL(input.files[0]);
            } else {
                // Disable button if no file selected
                submitBtn.setAttribute('disabled', 'disabled');
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                submitBtn.classList.remove('opacity-100');
            }
        }

        const monthInput = document.getElementById('monthInput');
        const yearInput = document.getElementById('yearInput');
        const monthDropdown = document.getElementById('monthDropdown');
        const yearDropdown = document.getElementById('yearDropdown');
        const expiryDateInput = document.getElementById('expiryDate');

        const currentDate = new Date();
        const currentYear = currentDate.getFullYear();
        const currentMonth = currentDate.getMonth();
        const appLocale = @json(app()->getLocale());
        const flatpickrLocaleKey = @json($flatpickrLocale);


        function getLastDateOfMonth(year, monthIndex) {
            return new Date(year, monthIndex + 1, 0).getDate();
        }

        function updateExpiryDate() {
            const selectedMonthIndex = months.findIndex(m => m === monthInput.value);
            const selectedYear = parseInt(yearInput.value);
            if (selectedMonthIndex !== -1 && selectedYear) {
                const lastDate = getLastDateOfMonth(selectedYear, selectedMonthIndex);
                expiryDateInput.value = `${selectedYear}-${String(selectedMonthIndex + 1).padStart(2, '0')}-${lastDate}`;
            }
        }

        const flatpickrLocale =
            (flatpickrLocaleKey && window.flatpickr?.l10ns?.[flatpickrLocaleKey]) ||
            window.flatpickr?.l10ns?.default;

        const months = flatpickrLocale?.months?.longhand ?
            [...flatpickrLocale.months.longhand] :
            Array.from({
                length: 12
            }, (_, i) => {
                return new Intl.DateTimeFormat(appLocale, {
                    month: 'long'
                }).format(
                    new Date(2000, i, 1)
                );
            });

        const years = Array.from({
            length: 5
        }, (_, i) => currentYear + i + 1); // Start from next year, add 5 years (currentYear+1 to currentYear+5)

        // Update the month dropdown dynamically
        function updateMonthsDropdown() {
            monthDropdown.innerHTML = ""; // Clear previous options

            const selectedYear = parseInt(yearInput.value);
            let startMonthIndex = 0;

            if (selectedYear === currentYear) {
                startMonthIndex = currentMonth;
            }

            months.slice(startMonthIndex).forEach(month => {
                const option = document.createElement('div');
                option.textContent = month;
                option.classList.add('dropdown-option');
                option.addEventListener('click', () => {
                    monthInput.value = month;
                    updateExpiryDate();
                    monthDropdown.classList.add('hidden');
                });
                monthDropdown.appendChild(option);
            });

            if (startMonthIndex > 0 && !months.slice(startMonthIndex).includes(monthInput.value)) {
                monthInput.value = months[startMonthIndex];
                updateExpiryDate();
            }
        }

        // Generate year options for the dropdown
        years.forEach(year => {
            const option = document.createElement('div');
            option.textContent = year;
            option.classList.add('dropdown-option');
            option.addEventListener('click', () => {
                yearInput.value = year;
                updateMonthsDropdown(); // Update months when year changes
                updateExpiryDate();
                yearDropdown.classList.add('hidden');
            });
            yearDropdown.appendChild(option);
        });

        // Initialize the input fields
        monthInput.value = months[currentMonth];
        yearInput.value = currentYear + 1; // Start with next year since current year is removed
        updateExpiryDate();
        updateMonthsDropdown(); // Initialize months dropdown


        // Check if user already has a student card uploaded, enable button if so
        @if (empty($user->student_card))
            submitBtn.removeAttribute('disabled');
            submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            submitBtn.classList.add('opacity-100');
        @endif

        // Event listeners for showing/hiding dropdowns
        monthInput.addEventListener('focus', () => {
            monthDropdown.classList.remove('hidden');
        });
        monthInput.addEventListener('blur', () => {
            setTimeout(() => {
                monthDropdown.classList.add('hidden');
            }, 200);
        });

        yearInput.addEventListener('focus', () => {
            yearDropdown.classList.remove('hidden');
        });
        yearInput.addEventListener('blur', () => {
            setTimeout(() => {
                yearDropdown.classList.add('hidden');
            }, 200);
        });
    </script>
@endsection
