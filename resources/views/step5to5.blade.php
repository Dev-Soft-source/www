@extends('layouts.template')

@section('style')
<style>
    .verify-button-modern {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        font-size: 18px;
        border: none;
        transition: all 0.3s ease;
    }
    .verify-button-modern:hover {
        background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        transform: translateY(-1px);
        box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
    }
    .verify-button-modern:active {
        transform: translateY(0);
    }
</style>
@endsection

@section('content')
<div class="mx-auto max-w-2xl lg:max-w-4xl my-6">
    @if(session('error'))
        <div class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                    <div class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg w-full modal-border">
                        <button type="button" onclick="closeModal()" class="absolute top-3 right-3 text-gray-400 hover:text-gray-500">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <div class="text-center sm:ml-4 sm:mt-0 sm:text-left">
                                <h3 class="text-3xl text-center font-FuturaMdCnBT text-gray-900 mb-4" id="modal-title">{!! session('heading') !!}</h3>
                                <p class="can-exp-p text-center">{!! session('error') !!}</p>
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
    <div class="bg-white rounded p-4 w-full col-span-12 md:col-span-9 mx-auto">
        <div class="bg-white border border-gray-100 pb-8 px-4 shadow rounded-md sm:px-10 my-4">
            <div class="pb-2 flex items-center justify-center">
                <h1 class="font-FuturaMdCnBT mt-10 text-primary text-3xl md:text-4xl lg:text-5xl mb-4">@isset($step4Page->main_heading){{ $step4Page->main_heading }}@endisset</h1>
            </div>
            <p class="text-black">@isset($step4Page->main_label){!! $step4Page->main_label !!}@endisset</p>
            <form method="POST" action="{{ route('step5to5.update', $user->id) }}">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1  gap-4 mt-4">
                    <div class="us_number mt-2">
                        <div class="flex flex-col lg:flex-row items-start items-end gap-4 mt-1">
                            <div class="w-full lg:w-3/6">
                                <label class="text-gray-700 font-FuturaMdCnBT mb-2 block">Select Your Country</label>
                                <select name="country" id="country-dropdown" class="font-FuturaMdCnBT bg-white text-base block mt-1 border p-1.5 w-full rounded border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600 placeholder:text-gray-900 {{ $errors->has('country') ? 'border-red-500' : '' }}">
                                    {{-- <option value="">Select your country</option> --}}
                                    @foreach ($countries as $country)
                                        <option value="{{ $country->id }}" data-dial-code="{{ $country->dial_code }}" {{ old('country', $user->country) == $country->id ? 'selected' : '' }}>
                                            {{ $country->name }} {{ $country->dial_code ? '(' . $country->dial_code . ')' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('country')
                                    <div class="relative tooltip -bottom-4 group-hover:flex">
                                        <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                                            <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                        </div>
                                    </div>
                                @enderror
                            </div>
                            <div class="w-full lg:w-3/6">
                                @isset($step4Page->phone_label)<label for="phone" class="text-gray-700 font-FuturaMdCnBT mb-2 block">{{ $step4Page->phone_label }}</label>@endisset
                                <div class="flex gap-2">
                                    <div class="w-2/6 hidden">
                                        <input type="tel" name="country_code" value="{{ old('country_code', '+1') }}" maxlength="5" readonly class="font-FuturaMdCnBT bg-gray-100 mt-1 border p-1.5 w-full rounded text-base  border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600">
                                    </div>
                                    <div class="w-full">
                                        <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}" class="font-FuturaMdCnBT block mt-1 border p-1.5 w-full rounded text-base border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600 {{ $errors->has('phone') ? 'border-red-500' : '' }}" placeholder="Numbers only. With area code" maxlength="15" inputmode="numeric" pattern="[0-9]+">
                                        @error('phone')
                                            <div class="relative tooltip -bottom-4 group-hover:flex">
                                                <div role="tooltip" class="absolute tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                                                    <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                                </div>
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        @error('full_phone')
                            <div class="relative tooltip -bottom-4 group-hover:flex">
                                <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                                    <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                </div>
                            </div>
                        @enderror
                    </div>
                    <div class="font-FuturaMdCnBT mt-4 flex flex-col  md:flex-row lg:flex-row items-center gap-2 justify-center md:col-span-2">
                        <button type="button" onclick="sendVerificationCode()" id="sendCodeBtn" class="verify-button-modern w-full md:w-36 text-white text-base rounded font-FuturaMdCnBT px-5 py-2.5 text-center shadow-md">@isset($step4Page->verify_button_label){{ $step4Page->verify_button_label }}@endisset</button>
                        <div id="whatsappButtonContainer" class="hidden">
                            <button type="button" onclick="sendVerificationCodeWhatsApp()" id="sendWhatsAppBtn" class="w-full bg-green-500 hover:bg-green-600 text-white text-base rounded font-FuturaMdCnBT px-5 py-2.5 text-center shadow-md flex items-center justify-center gap-2">
                                Verify via WhatsApp
                            </button>
                        </div>
                        <div class="font-FuturaMdCnBT flex flex-col md:flex-row lg:flex-row items-center justify-center gap-2 w-full md:w-auto">
                            <button type="button" onclick="showSkipConfirmation()" class="button-exp-fill w-full md:w-36">@isset($step4Page->skip_button_label){{ $step4Page->skip_button_label }}@endisset</button>
                            <button type="button" onclick="showSaveUnverifiedConfirmation()" id="saveButton" class="font-FuturaMdCnBT button-exp-fill w-full md:w-36 opacity-50 cursor-not-allowed" disabled>@isset($step4Page->save_button_label){{ $step4Page->save_button_label }}@endisset</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Skip Confirmation Modal --}}
    <div id="skipModal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                <div class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg w-full modal-border">
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="text-center w-full">
                            <h6 class="text-lg font-medium text-gray-900 mb-4">
                                @isset($step4Page->confirm_title){{ $step4Page->confirm_title }}@endisset
                            </h6>
                            <p class="text-gray-600">
                                @isset($step4Page->confirm_text){{ $step4Page->confirm_text }}@endisset
                            </p>
                        </div>
                    </div>
                    <div class="px-4 pb-6 pt-4 sm:flex sm:flex-row-reverse sm:px-6 justify-center gap-3">
                        <a href="{{ route('profile', ['lang' => $selectedLanguage->abbreviation]) }}" class="inline-flex w-full justify-center rounded bg-primary px-3 py-2 font-FuturaMdCnBT text-lg text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-blue-400 sm:w-auto">
                            @isset($step4Page->confirm_skip){{ $step4Page->confirm_skip }}@endisset
                        </a>
                        <button type="button" onclick="hideSkipConfirmation()" class="inline-flex w-full justify-center rounded bg-gray-300 px-3 py-2 font-FuturaMdCnBT text-lg text-gray-700 hover:bg-gray-400 sm:w-auto">
                            @isset($step4Page->confirm_back){{ $step4Page->confirm_back }}@endisset
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Save Unverified Confirmation Modal --}}
    <div id="saveUnverifiedModal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity z-50 flex" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                <div class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg w-full modal-border">
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="text-center w-full">
                            <h6 class="text-lg font-medium text-gray-900 mb-4">
                                @isset($step4Page->skip_confirmation_heading){{ $step4Page->skip_confirmation_heading }}@endisset
                            </h6>
                            <p class="text-gray-600">
                                @isset($step4Page->skip_confirmation_message){{ $step4Page->skip_confirmation_message }}@endisset
                            </p>
                        </div>
                    </div>
                    <div class="px-4 pb-6 pt-4 sm:flex sm:flex-row-reverse sm:px-6 justify-center gap-3">
                        <button type="button" onclick="confirmSaveUnverified()" class="inline-flex w-full justify-center rounded bg-primary px-3 py-2 font-FuturaMdCnBT text-lg text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-blue-400 sm:w-auto">
                            @isset($step4Page->save_button_label_1){{ $step4Page->save_button_label_1 }}@endisset
                        </button>
                        <button type="button" onclick="hideSaveUnverifiedConfirmation()" class="inline-flex w-full justify-center rounded bg-gray-300 px-3 py-2 font-FuturaMdCnBT text-lg text-gray-700 hover:bg-gray-400 sm:w-auto">
                            @isset($step4Page->skip_button_label_1){{ $step4Page->skip_button_label_1 }}@endisset
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- WhatsApp Unavailable Notification Modal --}}
    <div id="whatsappUnavailableModal" class="hidden fixed inset-0 z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity z-40" onclick="closeWhatsAppUnavailableModal()"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                <div class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg w-full modal-border">
                    <button type="button" onclick="closeWhatsAppUnavailableModal()" class="absolute top-3 right-3 text-gray-400 hover:text-gray-500">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="text-center w-full">
                            <div id="whatsappUnavailableIcon" class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 mb-4">
                                <svg class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <h6 class="text-lg font-medium text-gray-900 mb-4" id="whatsappUnavailableTitle">
                                WhatsApp Not Available
                            </h6>
                            <p class="text-gray-600" id="whatsappUnavailableMessage">
                                WhatsApp is not available for this number. Verification code has been sent via SMS instead.
                            </p>
                        </div>
                    </div>
                    <div class="px-4 pb-6 pt-4 sm:flex sm:flex-row-reverse sm:px-6 justify-center">
                        <button type="button" onclick="closeWhatsAppUnavailableModal()" class="inline-flex w-full justify-center rounded bg-primary px-3 py-2 font-FuturaMdCnBT text-lg text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-blue-400 sm:w-auto">
                            OK
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Verification Modal --}}
    <form method="POST" action="{{ route('verify_number') }}" id="verifyForm">
        @csrf
        <input type="hidden" name="step" value="1">
        <div id="verifyModal" class="relative z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" id="verifyModalBackdrop" onclick="closeVerifyModal()"></div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                    <div class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-lg bg-white text-center shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
                        <button type="button" onclick="closeVerifyModal()" class="absolute top-3 right-3 text-gray-400 hover:text-gray-500">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <div class="mt-2">
                              <h6 class="text-left">
                                @isset($step4Page->verify_code_heading){{ $step4Page->verify_code_heading }}@endisset
                              </h6>
                              <p class="text-center mt-4">
                                @isset($step4Page->verify_code_label_1){{ $step4Page->verify_code_label_1 }}@endisset
                              </p>
                              <div class="flex justify-center mt-4 space-x-2">
                                <input type="text" name="code[]" maxlength="1" class="font-FuturaMdCnBT w-10 h-10 text-center block mt-1 border p-1.5 text-base rounded border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600">
                                <input type="text" name="code[]" maxlength="1" class="font-FuturaMdCnBT w-10 h-10 text-center block mt-1 border p-1.5 text-base rounded border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600">
                                <input type="text" name="code[]" maxlength="1" class="font-FuturaMdCnBT w-10 h-10 text-center block mt-1 border p-1.5 text-base rounded border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600">
                                <input type="text" name="code[]" maxlength="1" class="font-FuturaMdCnBT w-10 h-10 text-center block mt-1 border p-1.5 text-base rounded border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600">
                              </div>
                              <div id="codeError" class="hidden mt-2">
                                <p class="text-red-500 text-sm"></p>
                              </div>
                              <div id="whatsappResendContainer" class="hidden mt-4">
                                <p class="text-sm text-gray-600 mb-2">Didn't receive the code?</p>
                                <button type="button" onclick="resendViaWhatsApp()" id="resendWhatsAppBtn" class="w-full bg-green-500 hover:bg-green-600 text-white text-sm rounded font-FuturaMdCnBT px-4 py-2 text-center shadow-md flex items-center justify-center gap-2">
                                    Verify via WhatsApp
                                </button>
                              </div>
                            </div>
                        </div>
                        <div class="px-4 pb-6 pt-4 sm:flex sm:flex-row-reverse sm:px-6 justify-center">
                            <button type="submit" class="inline-flex w-42 justify-center rounded bg-primary px-3 py-2 font-FuturaMdCnBT text-lg text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-blue-400 sm:ml-3">
                                @isset($step4Page->verify_button_label_1){{ $step4Page->verify_button_label_1 }}@endisset
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('script')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
function closeModal() {
    document.querySelectorAll('.relative.z-50').forEach(modal => {
        modal.style.display = 'none';
    });
    if (window.history.replaceState) {
        const cleanUrl = window.location.href.split('?')[0];
        window.history.replaceState({}, document.title, cleanUrl);
    }
}
document.addEventListener('click', function(event) {
    if (event.target.classList.contains('fixed') && event.target.classList.contains('inset-0')) {
        closeModal();
    }
});
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeModal();
    }
});
window.addEventListener("pageshow", function(event) {
    if (event.persisted) {
        window.location.reload();
    }
});

// Auto-populate country code when country is selected
document.addEventListener('DOMContentLoaded', function() {
    let countryCodeInput = document.querySelector('input[name="country_code"]');
    let countryDropdown = document.querySelector('select[name="country"]');

    if (countryDropdown && countryCodeInput) {
        // Auto-populate country code when country is selected
        countryDropdown.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const dialCode = selectedOption.getAttribute('data-dial-code');

            if (dialCode) {
                countryCodeInput.value = dialCode;
            } else {
                countryCodeInput.value = '+1'; // Default to US
            }
        });

        // Set initial country code if country is already selected
        if (countryDropdown.value) {
            const selectedOption = countryDropdown.options[countryDropdown.selectedIndex];
            const dialCode = selectedOption.getAttribute('data-dial-code');
            if (dialCode) {
                countryCodeInput.value = dialCode;
            }
        }
    }
});
function loadStatesByCountry(countryId, selectedState) {
    $.ajax({
        url: "{{ url('get-states-by-country') }}",
        type: "POST",
        data: { country_id: countryId, _token: '{{ csrf_token() }}' },
        dataType: 'json',
        success: function(result) {
            $('#state-dropdown').html('<option value="">Select State</option>');
            $.each(result.states, function(key, value) {
                var option = $('<option value="' + value.id + '">' + value.name + '</option>');
                if (value.id == selectedState) option.prop('selected', true);
                $("#state-dropdown").append(option);
            });
            $('#city-dropdown').html('<option value="">Select State First</option>');
        }
    });
}
function loadCitiesByState(selectedState, selectedCity) {
    $.ajax({
        url: "{{ url('get-cities-by-state') }}",
        type: "POST",
        data: { state_id: selectedState, _token: '{{ csrf_token() }}' },
        dataType: 'json',
        success: function(result) {
            $('#city-dropdown').html('<option value="">Select City</option>');
            $.each(result.cities, function(key, value) {
                var option = $('<option value="' + value.id + '">' + value.name + '</option>');
                if (value.id == selectedCity) option.prop('selected', true);
                $("#city-dropdown").append(option);
            });
        }
    });
}
$(document).ready(function() {
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
            url: "{{ url('get-states-by-country') }}",
            type: "POST",
            data: { country_id: country_id, _token: '{{ csrf_token() }}' },
            dataType: 'json',
            success: function(result) {
                $('#state-dropdown').html('<option value="">Select State</option>');
                $.each(result.states, function(key, value) {
                    $("#state-dropdown").append('<option value="' + value.id + '">' + value.name + '</option>');
                });
                $('#city-dropdown').html('<option value="">Select State First</option>');
            }
        });
    });
    $('#state-dropdown').on('change', function() {
        var state_id = this.value;
        $("#city-dropdown").html('');
        $.ajax({
            url: "{{ url('get-cities-by-state') }}",
            type: "POST",
            data: { state_id: state_id, _token: '{{ csrf_token() }}' },
            dataType: 'json',
            success: function(result) {
                $('#city-dropdown').html('<option value="">Select City</option>');
                $.each(result.cities, function(key, value) {
                    $("#city-dropdown").append('<option value="' + value.id + '">' + value.name + '</option>');
                });
            }
        });
    });
});
let countryCodeInput = document.querySelector('input[name="country_code"]');
let phoneInput = document.querySelector('input[name="phone"]');

// Restrict to numbers only and enforce max 15 digits
function enforceNumericInput(e) {
    // Allow: backspace, delete, tab, escape, enter
    if ([46, 8, 9, 27, 13].indexOf(e.keyCode) !== -1 ||
        // Allow: Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
        (e.keyCode === 65 && e.ctrlKey === true) ||
        (e.keyCode === 67 && e.ctrlKey === true) ||
        (e.keyCode === 86 && e.ctrlKey === true) ||
        (e.keyCode === 88 && e.ctrlKey === true) ||
        // Allow: home, end, left, right
        (e.keyCode >= 35 && e.keyCode <= 39)) {
        return;
    }
    // Ensure that it is a number and stop the keypress if not
    if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
        e.preventDefault();
    }
}

function sanitizePhoneInput() {
    // Remove any non-numeric characters
    let value = phoneInput.value.replace(/\D/g, '');
    // Limit to 15 digits
    if (value.length > 15) {
        value = value.substring(0, 15);
    }
    phoneInput.value = value;
}

if (phoneInput) {
    phoneInput.addEventListener('keydown', enforceNumericInput);
    phoneInput.addEventListener('input', sanitizePhoneInput);
    phoneInput.addEventListener('paste', function() {
        setTimeout(sanitizePhoneInput, 0);
    });
}

function sendVerificationCode(channel = 'sms') {
    // Get form data
    const country = document.querySelector('select[name="country"]').value;
    const countryCode = document.querySelector('input[name="country_code"]').value;
    const phone = document.querySelector('input[name="phone"]').value;

    // Validate inputs
    if (!country || !countryCode || !phone) {
        alert('Please fill in all fields');
        return;
    }

    // Check if number is international (not +1)
    const isInternational = countryCode !== '+1';
    
    const lang = '{{ $selectedLanguage->abbreviation }}';
    const url = channel === 'whatsapp' 
        ? `/${lang}/step5-5-send-verification-whatsapp`
        : `/${lang}/step5-5-send-verification`;

    // Show loading state
    const button = event.target;
    const originalText = button.innerHTML;
    button.disabled = true;
    button.innerHTML = 'Sending...';

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            country: country,
            country_code: countryCode,
            phone: phone,
            channel: channel
        })
    })
    .then(response => {
        // Check response status before parsing JSON
        if (!response.ok) {
            return response.json().then(data => {
                throw { status: response.status, data: data };
            });
        }
        return response.json();
    })
    .then(data => {
        button.disabled = false;
        button.innerHTML = originalText;

        if (data.success) {
            // Show modal if WhatsApp was requested but unavailable (SMS used instead)
            if (data.whatsapp_unavailable && channel === 'whatsapp') {
                showWhatsAppUnavailableModal('WhatsApp is not available for this number. Verification code has been sent via SMS instead.');
            }
            
            // Update button visibility based on number type
            const sendCodeBtn = document.getElementById('sendCodeBtn');
            const whatsappContainer = document.getElementById('whatsappButtonContainer');
            
            if (!data.is_north_american) {
                // International number: Hide SMS button, show WhatsApp button
                if (sendCodeBtn) {
                    sendCodeBtn.classList.add('hidden');
                }
                if (whatsappContainer) {
                    whatsappContainer.classList.remove('hidden');
                }
                
                // Show WhatsApp resend option in verification modal
                const whatsappResendContainer = document.getElementById('whatsappResendContainer');
                if (whatsappResendContainer) {
                    whatsappResendContainer.classList.remove('hidden');
                }
            } else {
                // North American number: Show SMS button, hide WhatsApp button
                if (sendCodeBtn) {
                    sendCodeBtn.classList.remove('hidden');
                }
                if (whatsappContainer) {
                    whatsappContainer.classList.add('hidden');
                }
            }
            
            // Hide WhatsApp button if WhatsApp is unavailable
            if (data.whatsapp_unavailable) {
                if (whatsappContainer) {
                    whatsappContainer.classList.add('hidden');
                }
                // Show SMS button as fallback
                if (sendCodeBtn) {
                    sendCodeBtn.classList.remove('hidden');
                }
                const whatsappResendContainer = document.getElementById('whatsappResendContainer');
                if (whatsappResendContainer) {
                    whatsappResendContainer.classList.add('hidden');
                }
            }
            
            // Show remaining attempts if provided
            if (data.remaining_attempts !== undefined) {
                console.log('Remaining verification attempts:', data.remaining_attempts);
                if (data.remaining_attempts <= 1) {
                    showWhatsAppUnavailableModal('Warning: You have ' + data.remaining_attempts + ' verification attempt remaining.');
                }
            }
            
            openVerifyModal();
        } else {
            // Show error in modal for WhatsApp failures
            if (channel === 'whatsapp') {
                showWhatsAppUnavailableModal(data.message || 'Error sending verification code via WhatsApp. Please try again.');
            } else {
                showWhatsAppUnavailableModal(data.message || 'Error sending verification code. Please try again.');
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        button.disabled = false;
        button.innerHTML = originalText;
        
        if (error.status === 429) {
            showWhatsAppUnavailableModal(error.data?.message || 'Maximum verification attempts (3) reached for this number. Please try again after 24 hours.');
        } else {
            // Show error in modal for WhatsApp failures
            if (channel === 'whatsapp') {
                showWhatsAppUnavailableModal(error.data?.message || 'Error sending verification code via WhatsApp. Please try again.');
            } else {
                showWhatsAppUnavailableModal(error.data?.message || 'Error sending verification code. Please try again.');
            }
        }
    });
}

function sendVerificationCodeWhatsApp() {
    sendVerificationCode('whatsapp');
}

function resendViaWhatsApp() {
    // Get form data
    const country = document.querySelector('select[name="country"]').value;
    const countryCode = document.querySelector('input[name="country_code"]').value;
    const phone = document.querySelector('input[name="phone"]').value;

    if (!country || !countryCode || !phone) {
        alert('Please fill in all fields');
        return;
    }

    const lang = '{{ $selectedLanguage->abbreviation }}';
    const url = `/${lang}/step5-5-send-verification-whatsapp`;

    // Show loading state
    const button = document.getElementById('resendWhatsAppBtn');
    const originalText = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '<span class="animate-spin">⏳</span> Sending...';

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            country: country,
            country_code: countryCode,
            phone: phone,
            channel: 'whatsapp'
        })
    })
    .then(response => {
        // Check response status before parsing JSON
        if (!response.ok) {
            return response.json().then(data => {
                throw { status: response.status, data: data };
            });
        }
        return response.json();
    })
    .then(data => {
        button.disabled = false;
        button.innerHTML = originalText;

        if (data.success) {
            // Show modal if WhatsApp was requested but unavailable (SMS used instead)
            if (data.whatsapp_unavailable) {
                showWhatsAppUnavailableModal('WhatsApp is not available for this number. Verification code has been sent via SMS instead.', 'WhatsApp Not Available');
            } else {
                showWhatsAppUnavailableModal('Verification code sent via WhatsApp successfully!', 'Success');
            }
        } else {
            showWhatsAppUnavailableModal(data.message || 'Error sending verification code via WhatsApp. Please try again.', 'Error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        button.disabled = false;
        button.innerHTML = originalText;
        
        if (error.status === 429) {
            showWhatsAppUnavailableModal(error.data?.message || 'Maximum verification attempts (3) reached for this number. Please try again after 24 hours.');
        } else {
            showWhatsAppUnavailableModal(error.data?.message || 'Error sending verification code via WhatsApp. Please try again.');
        }
    });
}

// Store if number is international to show WhatsApp option in modal
let isInternationalNumber = false;

// Update WhatsApp button visibility based on country code
document.addEventListener('DOMContentLoaded', function() {
    const countryDropdown = document.querySelector('select[name="country"]');
    const countryCodeInput = document.querySelector('input[name="country_code"]');
    const sendCodeBtn = document.getElementById('sendCodeBtn');
    
    function updateWhatsAppButtonVisibility() {
        const countryCode = countryCodeInput ? countryCodeInput.value : '+1';
        isInternationalNumber = countryCode !== '+1';
        
        // Show/hide WhatsApp button in main form if international
        const whatsappContainer = document.getElementById('whatsappButtonContainer');
        if (whatsappContainer) {
            if (isInternationalNumber) {
                whatsappContainer.classList.remove('hidden');
            } else {
                whatsappContainer.classList.add('hidden');
            }
        }
        
        // Hide SMS verify button for international numbers, show for North American
        if (sendCodeBtn) {
            if (isInternationalNumber) {
                sendCodeBtn.classList.add('hidden');
            } else {
                sendCodeBtn.classList.remove('hidden');
            }
        }
    }
    
    if (countryDropdown) {
        countryDropdown.addEventListener('change', updateWhatsAppButtonVisibility);
    }
    
    if (countryCodeInput) {
        countryCodeInput.addEventListener('input', updateWhatsAppButtonVisibility);
    }
    
    // Initial check
    updateWhatsAppButtonVisibility();
});

function openVerifyModal() {
    const verifyModal = document.getElementById('verifyModal');
    if (verifyModal) {
        verifyModal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';

        // Focus first input
        const inputs = verifyModal.querySelectorAll('input[name="code[]"]');
        if (inputs.length > 0) {
            inputs[0].focus();
        }
    }
}

function closeVerifyModal() {
    const verifyModal = document.getElementById('verifyModal');
    if (verifyModal) {
        verifyModal.classList.add('hidden');
        document.body.style.overflow = 'auto';

        // Clear inputs
        const inputs = verifyModal.querySelectorAll('input[name="code[]"]');
        inputs.forEach(input => input.value = '');

        // Clear error
        const errorDiv = document.getElementById('codeError');
        if (errorDiv) {
            errorDiv.classList.add('hidden');
        }
    }
}

// Auto-focus and auto-move between verification code inputs
document.addEventListener('DOMContentLoaded', function() {
    const codeInputs = document.querySelectorAll('#verifyModal input[name="code[]"]');

    codeInputs.forEach((input, index) => {
        // Move to next input on input
        input.addEventListener('input', function() {
            if (this.value.length === 1 && index < codeInputs.length - 1) {
                codeInputs[index + 1].focus();
            }
        });

        // Handle backspace to move to previous field
        input.addEventListener('keydown', function(event) {
            if (event.key === 'Backspace' && this.value === '' && index > 0) {
                codeInputs[index - 1].focus();
            } else if (event.key === 'ArrowLeft' && index > 0) {
                codeInputs[index - 1].focus();
                event.preventDefault();
            } else if (event.key === 'ArrowRight' && index < codeInputs.length - 1) {
                codeInputs[index + 1].focus();
                event.preventDefault();
            }
        });

        // Handle paste event
        input.addEventListener('paste', function(event) {
            event.preventDefault();
            const pastedData = event.clipboardData.getData('text').trim();
            if (pastedData.length === codeInputs.length) {
                pastedData.split('').forEach((char, i) => {
                    if (codeInputs[i]) {
                        codeInputs[i].value = char;
                    }
                });
                codeInputs[codeInputs.length - 1].focus();
            }
        });
    });
});

function showSkipConfirmation() {
    document.getElementById('skipModal').classList.remove('hidden');
}

function hideSkipConfirmation() {
    document.getElementById('skipModal').classList.add('hidden');
}

function showWhatsAppUnavailableModal(message, title = null) {
    const modal = document.getElementById('whatsappUnavailableModal');
    const messageElement = document.getElementById('whatsappUnavailableMessage');
    const titleElement = document.getElementById('whatsappUnavailableTitle');
    const iconElement = document.getElementById('whatsappUnavailableIcon');
    
    if (modal && messageElement) {
        if (message) {
            messageElement.textContent = message;
        }
        if (title && titleElement) {
            titleElement.textContent = title;
        } else if (titleElement) {
            // Set default title based on message content
            if (message && message.includes('successfully')) {
                titleElement.textContent = 'Success';
                // Change icon to success (green checkmark)
                if (iconElement) {
                    iconElement.className = 'mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4';
                    iconElement.innerHTML = '<svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>';
                }
            } else if (message && (message.includes('Error') || message.includes('error') || message.includes('failed'))) {
                titleElement.textContent = 'Error';
                // Change icon to error (red X)
                if (iconElement) {
                    iconElement.className = 'mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4';
                    iconElement.innerHTML = '<svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>';
                }
            } else if (message && message.includes('Warning')) {
                titleElement.textContent = 'Warning';
                // Keep warning icon (yellow)
                if (iconElement) {
                    iconElement.className = 'mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 mb-4';
                    iconElement.innerHTML = '<svg class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>';
                }
            } else {
                titleElement.textContent = 'WhatsApp Not Available';
                // Keep warning icon (yellow)
                if (iconElement) {
                    iconElement.className = 'mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 mb-4';
                    iconElement.innerHTML = '<svg class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>';
                }
            }
        }
        modal.classList.remove('hidden');
        modal.style.setProperty('display', 'block', 'important');
        modal.style.setProperty('visibility', 'visible', 'important');
        modal.style.setProperty('opacity', '1', 'important');
        modal.style.setProperty('z-index', '50', 'important');
        document.body.style.overflow = 'hidden';
    }
}

function closeWhatsAppUnavailableModal() {
    const modal = document.getElementById('whatsappUnavailableModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
}

// Close modal on Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const whatsappModal = document.getElementById('whatsappUnavailableModal');
        if (whatsappModal && !whatsappModal.classList.contains('hidden')) {
            closeWhatsAppUnavailableModal();
        }
    }
});

// Form validation for Step 5
function validateStep5Form() {
    const country = document.querySelector('select[name="country"]').value;
    const phone = document.querySelector('input[name="phone"]').value.trim();
    
    const saveButton = document.getElementById('saveButton');
    
    // Check if all required fields are filled
    const isValid = country && phone;
    
    if (isValid) {
        saveButton.disabled = false;
        saveButton.classList.remove('opacity-50', 'cursor-not-allowed');
        saveButton.classList.add('opacity-100');
    } else {
        saveButton.disabled = true;
        saveButton.classList.add('opacity-50', 'cursor-not-allowed');
        saveButton.classList.remove('opacity-100');
    }
}

// Add event listeners to all form inputs for real-time validation
document.addEventListener('DOMContentLoaded', function() {
    const formInputs = [
        'select[name="country"]',
        'input[name="phone"]'
    ];
    
    formInputs.forEach(selector => {
        const element = document.querySelector(selector);
        if (element) {
            element.addEventListener('input', validateStep5Form);
            element.addEventListener('change', validateStep5Form);
        }
    });
    
    // Initial validation check
    validateStep5Form();
});

// Save Unverified Confirmation Modal Functions
function showSaveUnverifiedConfirmation() {
    const modal = document.getElementById('saveUnverifiedModal');
    if (modal) {
        modal.classList.remove('hidden');
    }
}

function hideSaveUnverifiedConfirmation() {
    const modal = document.getElementById('saveUnverifiedModal');
    if (modal) {
        modal.classList.add('hidden');
    }
}

function confirmSaveUnverified() {
    // Create a hidden form to submit with save action
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("step5to5.update", $user->id) }}';
    
    // Add CSRF token
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = '{{ csrf_token() }}';
    form.appendChild(csrfInput);
    
    // Add method
    const methodInput = document.createElement('input');
    methodInput.type = 'hidden';
    methodInput.name = '_method';
    methodInput.value = 'PUT';
    form.appendChild(methodInput);
    
    // Add action input
    const actionInput = document.createElement('input');
    actionInput.type = 'hidden';
    actionInput.name = 'action';
    actionInput.value = 'save';
    form.appendChild(actionInput);
    
    // Add country and phone data
    const country = document.querySelector('select[name="country"]').value;
    const phone = document.querySelector('input[name="phone"]').value;
    const countryCode = document.querySelector('input[name="country_code"]').value;
    
    if (country) {
        const countryInput = document.createElement('input');
        countryInput.type = 'hidden';
        countryInput.name = 'country';
        countryInput.value = country;
        form.appendChild(countryInput);
    }
    
    if (phone) {
        const phoneInput = document.createElement('input');
        phoneInput.type = 'hidden';
        phoneInput.name = 'phone';
        phoneInput.value = phone;
        form.appendChild(phoneInput);
    }
    
    if (countryCode) {
        const countryCodeInput = document.createElement('input');
        countryCodeInput.type = 'hidden';
        countryCodeInput.name = 'country_code';
        countryCodeInput.value = countryCode;
        form.appendChild(countryCodeInput);
    }
    
    document.body.appendChild(form);
    form.submit();
}
</script>
@endsection
