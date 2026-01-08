@extends('layouts.template')

@section('content')
@if(session('message'))
        <div id="myModal" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div onclick="closeModal()" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                    <div
                        class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                        <button type="button" onclick="closeModal()" class="absolute top-3 right-3 text-gray-400 hover:text-gray-500">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start justify-center">
                                <!-- <div
                                    class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full sm:mx-0 sm:h-10 sm:w-10 bg-green-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 text-white">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                    </svg>
                                </div> -->
                            </div>
                            <div class="text-center sm:ml-4 sm:mt-0 sm:text-left">
                                <div class="">
                                    <h3 class="text-3xl text-center font-FuturaMdCnBT font-medium text-gray-900 mb-4" id="modal-title">{!! session('heading') !!}</h3>
                                </div>
                                <div class="w-full">
                                    <p class="text-center can-exp-p">{{ session('message') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="px-4 pb-6 pt-4 flex items-center space-x-2 sm:space-x-4 sm:px-6 justify-center">
                            @if(session('id'))
                                <a href="{{ route('repost_ride', ['lang' => $selectedLanguage->abbreviation, 'id' => session('id')]) }}"
                                    class="inline-flex w-full justify-center rounded bg-greenXS px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white whitespace-nowrap hover:text-white hover:shadow-lg shadow-sm hover:bg-greenXS sm:ml-3 sm:w-fit">Repost ride</a>
                            @endif
                            <a href=""
                                class="button-exp-fill">Close</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
<div class="grid grid-cols-12 gap-4 md:container md:mx-auto  my-6 md:my-10 xl:my-14 px-4 xl:px-0">


    @include('layouts.inc.profile_sidebar')

    <div class="bg-white border rounded p-4 border-gray-200 w-full col-span-12 lg:col-span-9 shadow">
        <div class=" pb-2">
            <h1 class="mb-0">{{ $payoutOptionPage->main_heading ?? "Payout options settings"}}</h1>
        </div>


        <form method="POST"
            @if (optional($userBankDetail)->status === 'admin_verify')
                action="{{ route('payout.verifyBank') }}"
            @else
                action="{{ route('payout.store') }}"
            @endif
        >
            @csrf

            <div class="flex flex-wrap" id="tabs-id">
                <div class="w-full">
                    <ul class="flex mb-0 list-none flex-wrap pt-3 pb-4 flex-row">
                        <li class="-mb-px mr-2 last:mr-0 flex-auto text-center">
                            <input type="radio" id="bank" name="payout_method" value="bank"
                                {{ old('payout_method', 'bank') == 'bank' ? 'checked' : '' }} class="hidden peer">
                            <label for="bank" id="bank_label" class="text-2xl font-FuturaMdCnBT font-medium px-5 py-2 shadow-lg rounded block border-gray-100 border leading-normal text-white bg-blue-600 cursor-pointer">
                                {{ $payoutOptionPage->web_bank_transfer_description ?? "Bank tranfer"}}
                            </label>
                        </li>
                        <li class="-mb-px mr-2 last:mr-0 flex-auto text-center">
                            <input type="radio" id="paypal" name="payout_method" value="paypal"
                                {{ old('payout_method') == 'paypal' ? 'checked' : '' }} class="hidden peer">
                            <label for="paypal" id="paypal_label" class="text-2xl font-FuturaMdCnBT font-medium px-5 py-2 shadow-lg rounded block border-gray-100 border leading-normal text-white bg-blue-600 cursor-pointer">
                                {{ $payoutOptionPage->web_paypal_transfer_description ?? "PayPal tranfer"}}
                            </label>
                        </li>
                    </ul>
                </div>
            </div>

            @php
                $isBankDetailsEditable = is_null($userBankDetail); // Check if bank details exist
            @endphp

            @if ($errors->has('account_no') || $errors->has('bank_name') || $errors->has('ifsc_code') || $errors->has('country'))
            <div id="bank_transfer_fields" class="pt-5">
            @else
            <div id="bank_transfer_fields" style="display: none;" class="pt-5">
            @endif
                <div class="">
                    <h2>{{ $payoutOptionPage->bank_detail_heading ?? "Bank details"}}:</h2>
                </div>

                <p class="text-base md:text-lg font-medium text-red-500">{{ $payoutOptionPage->mobile_indicate_required_field_label ?? "(*) Indicates required fields"}} </p>
                <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4 mt-4">

                    <div>
                        <label for="">{{ $payoutOptionPage->bank_name_label ?? "Bank name"}}<span class="text-red-500">*</span></label>
                        <select name="bank_name" {{ optional($userBankDetail)->bank_id ? 'disabled' : '' }} class="bg-white block mt-1 border p-1.5 w-full rounded text-base md:text-lg border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600 text-gray-500">
                            <option value="">{{ $payoutOptionPage->select_bank_label ?? "Select bank"}}</option>
                            @foreach ($banks as $bank)
                                <option value="{{ $bank->id }}" {{ (old('bank_name') == $bank->id || (isset($userBankDetail) && $userBankDetail->bank_id == $bank->id)) ? 'selected' : '' }}>{{ $bank->name }}</option>
                            @endforeach
                        </select>
                        @error('bank_name')
                          <div class="relative tooltip -bottom-4 group-hover:flex">
                            <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                            </div>
                          </div>
                        @enderror
                        @if (optional($userBankDetail)->bank_id)
                            <input type="hidden" name="bank_id" value="{{ $userBankDetail->bank_id }}">
                        @endif
                    </div>

                    <div>
                        <label for="">{{ $payoutOptionPage->institution_number_label ?? "Institution number"}}<span class="text-red-500">*</span></label>
                        <input type="text" name="institution_number" value="{{ old('institution_number', $userBankDetail->institution_number ?? '') }}" {{ optional($userBankDetail)->institution_number ? 'readonly' : '' }} placeholder="{{ $payoutOptionPage->institution_number_placeholder ?? ""}}"  class=" block mt-1 border p-1.5 w-full rounded text-base md:text-lg border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600">
                        @error('institution_number')
                          <div class="relative tooltip -bottom-4 group-hover:flex">
                            <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                            </div>
                          </div>
                        @enderror
                    </div>

                    <div>
                        <label for="">{{ $payoutOptionPage->branch_label ?? "Branch name"}} <span class="text-red-500">*</span></label>
                        <input type="text" name="branch" value="{{ old('branch', $userBankDetail->branch ?? '') }}" {{ optional($userBankDetail)->branch ? 'readonly' : '' }} class=" block mt-1 border p-1.5 w-full rounded text-base md:text-lg border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600"
                        placeholder="{{ $payoutOptionPage->branch_placeholder ?? ""}}"
                        >
                        @error('branch')
                          <div class="relative tooltip -bottom-4 group-hover:flex">
                            <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                            </div>
                          </div>
                        @enderror
                    </div>

                    <div>
                        <label for="">{{ $payoutOptionPage->branch_address_label ?? "Branch address"}} <span class="text-red-500">*</span></label>
                        <input type="text" name="branch_address" value="{{ old('branch_address', $userBankDetail->branch_address ?? '') }}" {{ optional($userBankDetail)->branch_address ? 'readonly' : '' }} placeholder="{{ $payoutOptionPage->branch_address_placeholder ?? ""}}" class=" block mt-1 border p-1.5 w-full rounded text-base md:text-lg border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600">
                        @error('branch_address')
                          <div class="relative tooltip -bottom-4 group-hover:flex">
                            <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                            </div>
                          </div>
                        @enderror
                    </div>

                    <div>
                        <label for="">{{ $payoutOptionPage->branch_number_label ?? "Branch number"}} <span class="text-red-500">*</span></label>
                        <input type="text" name="branch_number" value="{{ old('branch_number', $userBankDetail->branch_number ?? '') }}" {{ optional($userBankDetail)->branch_number ? 'readonly' : '' }} placeholder="{{ $payoutOptionPage->branch_number_placeholder ?? ""}}" class=" block mt-1 border p-1.5 w-full rounded text-base md:text-lg border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600">
                        @error('branch_number')
                          <div class="relative tooltip -bottom-4 group-hover:flex">
                            <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                            </div>
                          </div>
                        @enderror
                    </div>

                    <div>
                        <label for="">{{ $payoutOptionPage->bank_title_label ?? "Account holder's name"}} <span class="text-red-500">*</span></label>
                        <input type="text" name="account_holder_name" placeholder="{{ $payoutOptionPage->bank_title_placeholder ?? ""}}" value="{{ old('account_holder_name', $userBankDetail->bank_title ?? '') }}" {{ optional($userBankDetail)->bank_title ? 'readonly' : '' }} class=" block mt-1 border p-1.5 w-full rounded text-base md:text-lg border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600">
                        @error('account_holder_name')
                          <div class="relative tooltip -bottom-4 group-hover:flex">
                            <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                            </div>
                          </div>
                        @enderror
                    </div>
                    <div>
                        <label for="">{{ $payoutOptionPage->account_number_label ?? "Account holder's  number"}}<span class="text-red-500">*</span></label>
                        <input type="text" name="account_holder_number" placeholder="{{ $payoutOptionPage->account_number_placeholder ?? ""}}" value="{{ old('account_holder_number', $userBankDetail->acc_no ?? '') }}" {{ optional($userBankDetail)->acc_no ? 'readonly' : '' }} class=" block mt-1 border p-1.5 w-full rounded text-base md:text-lg border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600">
                        @error('account_holder_number')
                          <div class="relative tooltip -bottom-4 group-hover:flex">
                            <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                            </div>
                          </div>
                        @enderror
                    </div>

                    <div>
                        <label for="">{{ $payoutOptionPage->address_label ?? "Address"}}<span class="text-red-500">*</span></label>
                        <input type="text" name="account_holder_address" value="{{ old('account_holder_address', $userBankDetail->address ?? '') }}" {{ optional($userBankDetail)->address ? 'readonly' : '' }} placeholder="{{ $payoutOptionPage->account_address_placeholder ?? ""}}" class=" block mt-1 border p-1.5 w-full rounded text-base md:text-lg border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600">
                        @error('account_holder_address')
                          <div class="relative tooltip -bottom-4 group-hover:flex">
                            <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                            </div>
                          </div>
                        @enderror
                    </div>
                    @if (optional($userBankDetail)->status === 'admin_verify')
                        <div>
                            <label for="">{{ $payoutOptionPage->admin_sent_amount_placeholder ?? "Admin sent amount"}}</label>
                            <input type="number" min="0" name="user_verify_amount" value="{{ $userBankDetail->user_verify_amount ?? '' }}" {{ optional($userBankDetail)->user_verify_amount ? 'readonly' : '' }} class=" block mt-1 border p-1.5 w-full rounded text-base md:text-lg border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600">
                            @error('user_verify_amount')
                              <div class="relative tooltip -bottom-4 group-hover:flex">
                                <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                    <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                </div>
                              </div>
                            @enderror
                        </div>
                    @endif
                    <div class="md:col-span-2">
                        <div class="flex items-center space-x-1.5 lg:space-x-3 mb-2 mr-2 lg:mr-2">
                            <input name="set_default" type="radio" value="bank" {{ optional($userBankDetail)->set_default == 'bank' ? 'checked' : '' }} class="h-5 w-5 border-gray-300 bg-gray-200 cursor-pointer text-indigo-600 focus:ring-indigo-600">
                            <label for="set_default" class="block text-gray-900">{{ $payoutOptionPage->set_default_checkbox_label ?? "Set as default"}}</label>
                        </div>
                    </div>
                </div>
            </div>

            @if ($errors->has('paypal_email'))
            <div id="paypal_transfer_field" class="pt-5">
            @else
            <div id="paypal_transfer_field" style="display: none;" class="pt-5">
            @endif
                <div class="">
                    <h2>{{ $payoutOptionPage->paypal_detail_heading ?? "PayPal details"}}:</h2>
                </div>

                <p class="text-base md:text-lg font-medium text-red-500">{{ $payoutOptionPage->mobile_indicate_required_field_label ?? "(*) Indicates required fields"}} </p>

                <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label for="">{{ $payoutOptionPage->paypal_email_label ?? "PayPal email"}}</label>
                        <input type="text" name="paypal_email" value="{{ old('paypal_email', $userBankDetail->paypal_email ?? '') }}" {{ optional($userBankDetail)->paypal_email ? 'readonly' : '' }} class=" block mt-1 border p-1.5 w-full rounded text-base md:text-lg border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600">
                        @error('paypal_email')
                          <div class="relative tooltip -bottom-4 group-hover:flex">
                            <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                            </div>
                          </div>
                        @enderror
                    </div>
                    <div class="md:col-span-2">
                        <div class="flex items-center space-x-1.5 lg:space-x-3 mb-2 mr-2 lg:mr-2">
                            <input name="set_default" type="radio" value="paypal" {{ optional($userBankDetail)->set_default == 'paypal' ? 'checked' : '' }} class="h-5 w-5 border-gray-300 bg-gray-200 cursor-pointer text-indigo-600 focus:ring-indigo-600">
                            <label for="set_default" class="block text-gray-900">{{ $payoutOptionPage->set_default_checkbox_label ?? "Set as default"}}</label>
                        </div>
                    </div>
                </div>
            </div>

            @if (optional($userBankDetail)->status === 'admin_verify')
                <div class="md:col-span-2 mt-4 flex justify-center">
                    <button type="submit" class="button-exp-fill">{{ $payoutOptionPage->verify_button_text ?? "Verify Bank"}}</button>
                </div>
            @else
                <div class="md:col-span-2 mt-4 flex justify-center">
                    <button type="submit" class="button-exp-fill">{{ $payoutOptionPage->save_btn_label ?? "Submit"}}</button>
                </div>
            @endif
        </form>
    </div>
</div>

@endsection

@section('script')

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
        input.addEventListener('input', hideTooltip); // no parameter on input typing
    });

    const labels = document.querySelectorAll('label');
    labels.forEach(input => {
        input.addEventListener('click', function (e) {
            hideTooltip.call(this, 'label'); // pass 'testing' on label click
        });
    });
    
    function updatePayoutFields() {
        const selectedRadio = document.querySelector('input[name="payout_method"]:checked');
        const selectedValue = selectedRadio ? selectedRadio.value : null;
        const bankTransferFields = document.getElementById('bank_transfer_fields');
        const paypalTransferField = document.getElementById('paypal_transfer_field');
        const paypalLabel = document.getElementById('paypal_label');
        const bankLabel = document.getElementById('bank_label');

        if (selectedValue === 'bank') {
            bankTransferFields.style.display = 'block';
            paypalTransferField.style.display = 'none';
            paypalLabel.classList.add('bg-white', 'border-gray-100' , 'text-blue-600');
            paypalLabel.classList.remove('bg-blue-600', 'border-blue-600', 'text-white');
            bankLabel.classList.add('bg-blue-600', 'border-blue-600', 'text-white');
            bankLabel.classList.remove('bg-white', 'border-gray-100' , 'text-blue-600');
        } else if (selectedValue === 'paypal') {
            bankTransferFields.style.display = 'none';
            paypalTransferField.style.display = 'block';
            bankLabel.classList.add('bg-white', 'border-gray-100' , 'text-blue-600');
            bankLabel.classList.remove('bg-blue-600', 'border-blue-600', 'text-white');
            paypalLabel.classList.add('bg-blue-600', 'border-blue-600', 'text-white');
            paypalLabel.classList.remove('bg-white', 'border-gray-100' , 'text-blue-600');
        } else {
            bankTransferFields.style.display = 'none';
            paypalTransferField.style.display = 'none';
        }
    }

    // Event listener for dropdown change
    const radioButtons = document.querySelectorAll('input[name="payout_method"]');

    // Add change event listener to all radio buttons
    radioButtons.forEach(radio => {
        radio.addEventListener('change', updatePayoutFields);
    });

    // Initialize fields visibility on page load
    // document.addEventListener('DOMContentLoaded', updatePayoutFields);
    function scrollToFirstError() {
        // Find the first error element (either from server-side or client-side validation)
        const firstErrorElement = document.querySelector('.tooltip:not([style*="display: none"]), .tooltip:not(.hidden)');

        if (firstErrorElement) {
            // Scroll to the error with smooth behavior
            firstErrorElement.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
        }
    }

    // Initialize fields visibility and scroll to errors on page load
    document.addEventListener('DOMContentLoaded', function() {
        updatePayoutFields();

        // Check if there are any validation errors
        @if($errors->any())
            // Show the appropriate tab based on where the error occurred
            @if($errors->has('paypal_email'))
                document.getElementById('paypal').checked = true;
            @else
                document.getElementById('bank').checked = true;
            @endif
            updatePayoutFields();

            // Scroll to first error after a small delay to allow fields to render
            setTimeout(scrollToFirstError, 100);
        @endif
    });

    // Also add scroll to error for form submission (client-side validation)
    document.querySelector('form').addEventListener('submit', function(e) {
        // This will catch HTML5 validation errors
        if (!this.checkValidity()) {
            // Scroll to first invalid element
            const firstInvalid = this.querySelector(':invalid');
            if (firstInvalid) {
                e.preventDefault();
                firstInvalid.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
                firstInvalid.focus();
            }
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
