@extends('layouts.template')

@section('content')
@if(session('message'))
        <div id="myModal" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div onclick="closeModal()" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                    <div
                        class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
                        <button type="button" onclick="closeModal()" class="absolute top-3 right-3 text-gray-400 hover:text-gray-500">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start justify-center">
                                <div
                                    class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full sm:mx-0 sm:h-10 sm:w-10 bg-green-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 text-white">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                    </svg>
                                </div> 
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
        
        <div class="mt-4 mb-6">
            <p class="text-base md:text-lg mb-3">
                Your <strong>Ride Contributions</strong> will be available in <strong>"My Wallet"</strong> 48 hours after the ride is completed.
            </p>
            <p class="text-base md:text-lg">
                You can then request a payout to your default method below.
            </p>
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
                            <input type="radio" id="interac" name="payout_method" value="interac"
                                {{ old('payout_method', 'interac') == 'interac' ? 'checked' : '' }} class="hidden peer">
                            <label for="interac" id="interac_label" class="text-2xl font-FuturaMdCnBT font-medium px-5 py-2 shadow-lg rounded block border-gray-100 border leading-normal text-white bg-blue-600 cursor-pointer">
                                Interac e-Transfer
                            </label>
                        </li>
                        <li class="-mb-px mr-2 last:mr-0 flex-auto text-center">
                            <input type="radio" id="bank" name="payout_method" value="bank"
                                {{ old('payout_method') == 'bank' ? 'checked' : '' }} class="hidden peer">
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

            @if ($errors->has('interac_email') || $errors->has('interac_email_confirm'))
            <div id="interac_transfer_fields" class="pt-5">
            @else
            <div id="interac_transfer_fields" style="display: {{ old('payout_method', 'interac') == 'interac' ? 'block' : 'none' }};" class="pt-5">
            @endif
                <div class="">
                    <h2>Interac e-Transfer Details:</h2>
                </div>

                <!-- Info text at top of tab -->
                <div class="mt-4 mb-4">
                    <p class="text-base md:text-lg text-gray-700">
                        Please ensure the email above matches the one registered for Autodeposit at your bank. This ensures your funds are deposited instantly without needing a security question.
                    </p>
                </div>

                <p class="text-base md:text-lg font-medium text-red-500">{{ $payoutOptionPage->mobile_indicate_required_field_label ?? "(*) Indicates required fields"}} </p>
                <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label for="interac_email">Email Address<span class="text-red-500">*</span></label>
                        <input type="email" id="interac_email" name="interac_email" value="{{ old('interac_email', $userBankDetail->interac_email ?? '') }}" {{ optional($userBankDetail)->interac_email ? 'readonly' : '' }} placeholder=" e.g. name@email.com " class="block mt-1 border p-1.5 w-full rounded text-base md:text-lg border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600">
                        @error('interac_email')
                          <div class="relative tooltip -bottom-4 group-hover:flex">
                            <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                            </div>
                          </div>
                        @enderror
                    </div>
                    <div>
                        <label for="interac_email_confirm">Confirm Email Address<span class="text-red-500">*</span></label>
                        <input type="email" id="interac_email_confirm" name="interac_email_confirm" value="{{ old('interac_email_confirm', '') }}" placeholder=" Re-enter your email address " class="block mt-1 border p-1.5 w-full rounded text-base md:text-lg border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600">
                        @error('interac_email_confirm')
                          <div class="relative tooltip -bottom-4 group-hover:flex">
                            <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                            </div>
                          </div>
                        @enderror
                    </div>
                    <div class="md:col-span-2">
                        <div class="flex items-center space-x-1.5 lg:space-x-3 mb-2 mr-2 lg:mr-2">
                            <input id="interac_autodeposit" name="interac_autodeposit" type="checkbox" value="1" class="h-5 w-5 border-gray-300 bg-gray-200 cursor-pointer text-indigo-600 focus:ring-indigo-600">
                            <label for="interac_autodeposit" class="block text-gray-900">
                                I have enabled Interac 
                                <span class="inline-flex items-center relative sups">
                                    Autodeposit
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-blue-500 cursor-help hover:text-blue-700 ml-1 autodeposit-info-icon">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                                    </svg>
                                    <!-- Tooltip -->
                                    <div class="tooltip hidden shift-right">
                                        <div class="password-tooltip">
                                            <p class="text-white">Autodeposit is a bank setting that lets you receive funds instantly without a security question. You can enable it in your bank's mobile app under 'Interac e-Transfer settings'.</p>
                                        </div>
                                    </div>
                                </span>
                                for this email address.
                            </label>
                        </div>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-base md:text-lg italic text-gray-600 mb-4">
                            Processing Fee: $2.00 CAD per withdrawal.
                        </p>
                    </div>
                    <div class="md:col-span-2">
                        <div class="flex items-center space-x-1.5 lg:space-x-3 mb-2 mr-2 lg:mr-2">
                            <input name="set_default" type="radio" id="set_default_interac" value="interac" {{ optional($userBankDetail)->set_default == 'interac' ? 'checked' : '' }} class="h-5 w-5 border-gray-300 bg-gray-200 cursor-pointer text-indigo-600 focus:ring-indigo-600">
                            <label for="set_default_interac" class="block text-gray-900">{{ $payoutOptionPage->set_default_checkbox_label ?? "Set as default"}}</label>
                        </div>
                    </div>
                    <div class="md:col-span-2 mt-4" id="interac_button_container">
                        <button type="submit" id="save_interac_btn" class="button-exp-fill opacity-50 cursor-not-allowed" disabled>
                            Save Payout Method
                        </button>
                    </div>
                </div>
            </div>

            @if ($errors->has('account_holder_number') || $errors->has('account_holder_name') || $errors->has('institution_number') || $errors->has('branch_number'))
            <div id="bank_transfer_fields" class="pt-5">
            @else
            <div id="bank_transfer_fields" style="display: none;" class="pt-5">
            @endif
                <div class="">
                    <h2>{{ $payoutOptionPage->bank_detail_heading ?? "Bank details"}}:</h2>
                </div>

                <!-- Info text at top of tab -->
                <div class="mt-4 mb-4">
                    <p class="text-base md:text-lg text-gray-700">
                        Enter your bank details to receive funds via Direct Deposit (EFT)
                    </p>
                </div>

                <p class="text-base md:text-lg font-medium text-red-500">{{ $payoutOptionPage->mobile_indicate_required_field_label ?? "(*) Indicates required fields"}} </p>
                <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4 mt-4">

                    <div>
                        <label for="account_holder_name">Account Holder Name<span class="text-red-500">*</span></label>
                        <input type="text" id="account_holder_name" name="account_holder_name" placeholder="As it appears on your bank statement" value="{{ old('account_holder_name', $userBankDetail->bank_title ?? '') }}" {{ optional($userBankDetail)->bank_title ? 'readonly' : '' }} class="block mt-1 border p-1.5 w-full rounded text-base md:text-lg border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600">
                        @error('account_holder_name')
                          <div class="relative tooltip -bottom-4 group-hover:flex">
                            <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                            </div>
                          </div>
                        @enderror
                    </div>

                    <div>
                        <label for="transit_number">Transit Number (5 digits)<span class="text-red-500">*</span></label>
                        <input type="text" id="transit_number" name="branch_number" placeholder="The branch code" value="{{ old('branch_number', $userBankDetail->branch_number ?? '') }}" {{ optional($userBankDetail)->branch_number ? 'readonly' : '' }} class="block mt-1 border p-1.5 w-full rounded text-base md:text-lg border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600">
                        @error('branch_number')
                          <div class="relative tooltip -bottom-4 group-hover:flex">
                            <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                            </div>
                          </div>
                        @enderror
                    </div>

                    <div>
                        <label for="institution_number">Institution Number (3 digits)<span class="text-red-500">*</span></label>
                        <input type="text" id="institution_number" name="institution_number" placeholder="The bank code (e.g., 004 for TD, 001 for BMO)" value="{{ old('institution_number', $userBankDetail->institution_number ?? '') }}" {{ optional($userBankDetail)->institution_number ? 'readonly' : '' }} class="block mt-1 border p-1.5 w-full rounded text-base md:text-lg border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600">
                        @error('institution_number')
                          <div class="relative tooltip -bottom-4 group-hover:flex">
                            <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                            </div>
                          </div>
                        @enderror
                    </div>

                    <div>
                        <label for="account_number">Account Number<span class="text-red-500">*</span></label>
                        <input type="text" id="account_number" name="account_holder_number" placeholder="The unique account string (7–12 digits)" value="{{ old('account_holder_number', $userBankDetail->acc_no ?? '') }}" {{ optional($userBankDetail)->acc_no ? 'readonly' : '' }} class="block mt-1 border p-1.5 w-full rounded text-base md:text-lg border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600">
                        @error('account_holder_number')
                          <div class="relative tooltip -bottom-4 group-hover:flex">
                            <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                            </div>
                          </div>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <p class="text-base md:text-lg italic text-gray-600 mb-4">
                            Processing Fee: $2.0 CAD per withdrawal.
                        </p>
                    </div>

                    <div class="md:col-span-2">
                        <p class="text-base md:text-lg text-gray-700 mb-4">
                            Note: Funds typically arrive in 1–3 business days.
                        </p>
                    </div>

                    <div class="md:col-span-2">
                        <div class="flex items-center space-x-1.5 lg:space-x-3 mb-2 mr-2 lg:mr-2">
                            <input id="set_default_bank" name="set_default" type="radio" value="bank" {{ optional($userBankDetail)->set_default == 'bank' ? 'checked' : '' }} class="h-5 w-5 border-gray-300 bg-gray-200 cursor-pointer text-indigo-600 focus:ring-indigo-600">
                            <label for="set_default_bank" class="block text-gray-900">{{ $payoutOptionPage->set_default_checkbox_label ?? "Set as default"}}</label>
                        </div>
                    </div>

                    <div class="md:col-span-2 mt-4" id="bank_button_container">
                        <button type="submit" id="save_bank_btn" class="button-exp-fill opacity-50 cursor-not-allowed" disabled>
                            Save Payout Method
                        </button>
                    </div>
                </div>
            </div>

            @if ($errors->has('paypal_email') || $errors->has('paypal_email_confirm'))
            <div id="paypal_transfer_field" class="pt-5">
            @else
            <div id="paypal_transfer_field" style="display: none;" class="pt-5">
            @endif
                <div class="">
                    <h2>{{ $payoutOptionPage->paypal_detail_heading ?? "PayPal details"}}:</h2>
                </div>

                <!-- Info text at top of tab -->
                <div class="mt-4 mb-4">
                    <p class="text-base md:text-lg text-gray-700">
                        Enter the email address associated with your PayPal account.
                    </p>
                </div>

                <p class="text-base md:text-lg font-medium text-red-500">{{ $payoutOptionPage->mobile_indicate_required_field_label ?? "(*) Indicates required fields"}} </p>

                <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label for="paypal_email">PayPal Email Address<span class="text-red-500">*</span></label>
                        <input type="email" id="paypal_email" name="paypal_email" value="{{ old('paypal_email', $userBankDetail->paypal_email ?? '') }}"  placeholder=" e.g. name@email.com " class="block mt-1 border p-1.5 w-full rounded text-base md:text-lg border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600">
                        @error('paypal_email')
                          <div class="relative tooltip -bottom-4 group-hover:flex">
                            <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                            </div>
                          </div>
                        @enderror
                    </div>
                    <div>
                        <label for="paypal_email_confirm">Confirm PayPal Email<span class="text-red-500">*</span></label>
                        <input type="email" id="paypal_email_confirm" name="paypal_email_confirm" value="{{ old('paypal_email_confirm', '') }}" placeholder=" Re-enter your PayPal email " class="block mt-1 border p-1.5 w-full rounded text-base md:text-lg border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600">
                        @error('paypal_email_confirm')
                          <div class="relative tooltip -bottom-4 group-hover:flex">
                            <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                            </div>
                          </div>
                        @enderror
                    </div>
                    <div class="md:col-span-2">
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-4">
                            <h3 class="text-lg font-semibold text-gray-800 mb-3">Important Fee Information:</h3>
                            <div class="space-y-2 text-base text-gray-700">
                                <p><strong>ProximaRide Fee:</strong> $2.00</p>
                                <p><strong>PayPal Receiving Fee:</strong> PayPal typically charges a fee (approx. 2.9% + $0.30) to receive funds.</p>
                                <p class="mt-3 italic text-gray-600">
                                    <strong>Example:</strong> If you withdraw $100, ProximaRide sends $98.00. After PayPal fees, you will see approximately $94.85 in your wallet.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="md:col-span-2">
                        <div class="flex items-center space-x-1.5 lg:space-x-3 mb-2 mr-2 lg:mr-2">
                            <input id="set_default_paypal" name="set_default" type="radio" value="paypal" {{ optional($userBankDetail)->set_default == 'paypal' ? 'checked' : '' }} class="h-5 w-5 border-gray-300 bg-gray-200 cursor-pointer text-indigo-600 focus:ring-indigo-600">
                            <label for="set_default_paypal" class="block text-gray-900">{{ $payoutOptionPage->set_default_checkbox_label ?? "Set as default"}}</label>
                        </div>
                    </div>
                    <div class="md:col-span-2 mt-4" id="paypal_button_container">
                        <button type="submit" id="save_paypal_btn" class="button-exp-fill opacity-50 cursor-not-allowed" disabled>
                            Save Payout Method
                        </button>
                    </div>
                </div>
            </div>

            @if (optional($userBankDetail)->status === 'admin_verify')
                <div class="md:col-span-2 mt-4 flex justify-center">
                    <button type="submit" class="button-exp-fill">{{ $payoutOptionPage->verify_button_text ?? "Verify Bank"}}</button>
                </div>
            @else
                <div id="default_submit_btn_container" class="md:col-span-2 mt-4 flex justify-center" style="display: {{ old('payout_method', 'interac') == 'interac' ? 'none' : 'flex' }};">
                    <button type="submit" class="button-exp-fill w-28">{{ $payoutOptionPage->save_btn_label ?? "Submit"}}</button>
                </div>
            @endif
            
            <div class="mt-6 pt-4 border-t border-gray-200">
                <p class="text-sm md:text-base text-gray-600 text-center">
                    <strong>Expecting a refund?</strong> Refunds for passengers are credited to your wallet or original payment method. Please ensure your payout details are accurate to avoid delays with manual withdrawals.
                </p>
            </div>
        </form>
    </div>
</div>

@endsection

@section('style')
<style>
    /* Tooltip container */
    .tooltip {
        position: absolute;
        top: calc(100% + 8px); /* BELOW icon */
        left: 50%;
        transform: translateX(-50%) translateY(-5px);
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transition: opacity 0.2s ease, transform 0.2s ease;
        z-index: 50;
    }

    /* Show tooltip */
    .tooltip.show {
        opacity: 1;
        visibility: visible;
        transform: translateX(calc(-50% + 30px)) translateY(0);
        pointer-events: auto;
    }

    /* Tooltip box */
    .password-tooltip {
        position: relative;
        background-color: #c75b5b;
        border-radius: 0.5rem;
        padding: 0.75rem;
        width: 25rem;
        text-align: left;
    }

    /* Arrow pointing UP to icon */
    .password-tooltip::before {
        content: "";
        position: absolute;
        top: -8px;
        left: 50%;
        transform: translateX(-50%);
        width: 0;
        height: 0;
        border-left: 8px solid transparent;
        border-right: 8px solid transparent;
        border-bottom: 8px solid #c75b5b;
    }

    .shift-right {
        margin-right: 50px;
    }
</style>
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
        const interacTransferFields = document.getElementById('interac_transfer_fields');
        const bankTransferFields = document.getElementById('bank_transfer_fields');
        const paypalTransferField = document.getElementById('paypal_transfer_field');
        const interacLabel = document.getElementById('interac_label');
        const paypalLabel = document.getElementById('paypal_label');
        const bankLabel = document.getElementById('bank_label');

        // Reset all labels to inactive state
        interacLabel.classList.add('bg-white', 'border-gray-100', 'text-blue-600');
        interacLabel.classList.remove('bg-blue-600', 'border-blue-600', 'text-white');
        bankLabel.classList.add('bg-white', 'border-gray-100', 'text-blue-600');
        bankLabel.classList.remove('bg-blue-600', 'border-blue-600', 'text-white');
        paypalLabel.classList.add('bg-white', 'border-gray-100', 'text-blue-600');
        paypalLabel.classList.remove('bg-blue-600', 'border-blue-600', 'text-white');

        // Hide all fields
        interacTransferFields.style.display = 'none';
        bankTransferFields.style.display = 'none';
        paypalTransferField.style.display = 'none';

        const defaultSubmitContainer = document.getElementById('default_submit_btn_container');
        const saveInteracBtn = document.getElementById('save_interac_btn');
        const interacButtonContainer = document.getElementById('interac_button_container');
        const saveBankBtn = document.getElementById('save_bank_btn');
        const bankButtonContainer = document.getElementById('bank_button_container');
        const savePayPalBtn = document.getElementById('save_paypal_btn');
        const paypalButtonContainer = document.getElementById('paypal_button_container');

        if (selectedValue === 'interac') {
            interacTransferFields.style.display = 'block';
            interacLabel.classList.remove('bg-white', 'border-gray-100', 'text-blue-600');
            interacLabel.classList.add('bg-blue-600', 'border-blue-600', 'text-white');
            if (defaultSubmitContainer) {
                defaultSubmitContainer.style.display = 'none';
            }
            if (saveInteracBtn && interacButtonContainer) {
                validateInteracForm();
            }
        } else if (selectedValue === 'bank') {
            bankTransferFields.style.display = 'block';
            bankLabel.classList.remove('bg-white', 'border-gray-100', 'text-blue-600');
            bankLabel.classList.add('bg-blue-600', 'border-blue-600', 'text-white');
            if (defaultSubmitContainer) {
                defaultSubmitContainer.style.display = 'none';
            }
            if (saveBankBtn && bankButtonContainer) {
                validateBankForm();
            }
        } else if (selectedValue === 'paypal') {
            paypalTransferField.style.display = 'block';
            paypalLabel.classList.remove('bg-white', 'border-gray-100', 'text-blue-600');
            paypalLabel.classList.add('bg-blue-600', 'border-blue-600', 'text-white');
            if (defaultSubmitContainer) {
                defaultSubmitContainer.style.display = 'none';
            }
            if (savePayPalBtn && paypalButtonContainer) {
                validatePayPalForm();
            }
        } else {
            // If no method selected or default, show default submit button
            if (defaultSubmitContainer) {
                defaultSubmitContainer.style.display = 'flex';
            }
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

    // Validate PayPal form and enable/disable Save button
    function validatePayPalForm() {
        const emailInput = document.getElementById('paypal_email');
        const emailConfirmInput = document.getElementById('paypal_email_confirm');
        const saveButton = document.getElementById('save_paypal_btn');

        if (!emailInput || !emailConfirmInput || !saveButton) {
            return;
        }

        const email = emailInput.value.trim();
        const emailConfirm = emailConfirmInput.value.trim();

        // Email validation regex
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const isValidEmail = emailRegex.test(email);
        const isValidEmailConfirm = emailRegex.test(emailConfirm);
        const emailsMatch = email === emailConfirm && email !== '';

        // Enable button only if all conditions are met (checkbox is optional)
        const isValid = isValidEmail && isValidEmailConfirm && emailsMatch;

        if (isValid) {
            saveButton.classList.remove('opacity-50', 'cursor-not-allowed');
            saveButton.classList.add('opacity-100');
            saveButton.disabled = false;
        } else {
            saveButton.classList.add('opacity-50', 'cursor-not-allowed');
            saveButton.classList.remove('opacity-100');
            saveButton.disabled = true;
        }
    }

    // Validate Bank Transfer form and enable/disable Save button
    function validateBankForm() {
        const accountHolderName = document.getElementById('account_holder_name');
        const transitNumber = document.getElementById('transit_number');
        const institutionNumber = document.getElementById('institution_number');
        const accountNumber = document.getElementById('account_number');
        const saveButton = document.getElementById('save_bank_btn');
        const defaultSubmitContainer = document.getElementById('default_submit_btn_container');

        if (!accountHolderName || !transitNumber || !institutionNumber || !accountNumber || !saveButton) {
            return;
        }

        const accountName = accountHolderName.value.trim();
        const transit = transitNumber.value.trim();
        const institution = institutionNumber.value.trim();
        const account = accountNumber.value.trim();

        // Validate required fields are filled (checkbox is optional)
        const isValid = accountName !== '' && transit !== '' && institution !== '' && account !== '';

        if (isValid) {
            saveButton.classList.remove('opacity-50', 'cursor-not-allowed');
            saveButton.classList.add('opacity-100');
            saveButton.disabled = false;
        } else {
            saveButton.classList.add('opacity-50', 'cursor-not-allowed');
            saveButton.classList.remove('opacity-100');
            saveButton.disabled = true;
        }
    }

    // Validate Interac e-Transfer form and enable/disable Save button
    function validateInteracForm() {
        const emailInput = document.getElementById('interac_email');
        const emailConfirmInput = document.getElementById('interac_email_confirm');
        const autodepositCheckbox = document.getElementById('interac_autodeposit');
        const saveButton = document.getElementById('save_interac_btn');
        const defaultSubmitContainer = document.getElementById('default_submit_btn_container');

        if (!emailInput || !emailConfirmInput || !autodepositCheckbox || !saveButton) {
            return;
        }

        const email = emailInput.value.trim();
        const emailConfirm = emailConfirmInput.value.trim();
        const isAutodepositChecked = autodepositCheckbox.checked;

        // Email validation regex
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const isValidEmail = emailRegex.test(email);
        const isValidEmailConfirm = emailRegex.test(emailConfirm);
        const emailsMatch = email === emailConfirm && email !== '';

        // Enable button only if all conditions are met
        const isValid = isValidEmail && isValidEmailConfirm && emailsMatch && isAutodepositChecked;

        if (isValid) {
            saveButton.classList.remove('opacity-50', 'cursor-not-allowed');
            saveButton.classList.add('opacity-100');
            saveButton.disabled = false;
        } else {
            saveButton.classList.add('opacity-50', 'cursor-not-allowed');
            saveButton.classList.remove('opacity-100');
            saveButton.disabled = true;
        }
    }

    // Function to show tooltip with animation
    function showTooltip(tooltip) {
        if (!tooltip) return;
        
        // Remove hidden class first
        tooltip.classList.remove('hidden');
        
        // Reset any inline styles that might prevent animation
        tooltip.style.display = 'flex';
        tooltip.style.opacity = '';
        tooltip.style.visibility = '';
        tooltip.style.transform = '';
        
        // Force a reflow to ensure styles are applied
        void tooltip.offsetHeight;
        
        // Use requestAnimationFrame for smooth animation
        requestAnimationFrame(function() {
            requestAnimationFrame(function() {
                tooltip.classList.add('show');
            });
        });
    }

    // Function to hide tooltip with animation
    function hideTooltip(tooltip) {
        if (!tooltip) return;
        
        // Remove show class to trigger fade out
        tooltip.classList.remove('show');
        
        // After animation completes, hide completely
        setTimeout(function() {
            tooltip.classList.add('hidden');
            tooltip.style.display = 'none';
            // Reset inline styles
            tooltip.style.opacity = '';
            tooltip.style.visibility = '';
            tooltip.style.transform = '';
        }, 200); // Match CSS transition duration
    }

    // Handle tooltip for Autodeposit info icon
    function initAutodepositTooltip() {
        const infoIcon = document.querySelector('.autodeposit-info-icon');
        if (!infoIcon) return;
        
        // Find the tooltip within the same parent span (.sups)
        const parentSpan = infoIcon.closest('.sups');
        if (!parentSpan) return;
        
        const tooltip = parentSpan.querySelector('.tooltip');
        
        if (infoIcon && tooltip) {
            infoIcon.addEventListener('mouseenter', function() {
                showTooltip(tooltip);
            });
            
            infoIcon.addEventListener('mouseleave', function() {
                hideTooltip(tooltip);
            });
        }
    }

    // Initialize fields visibility and scroll to errors on page load
    document.addEventListener('DOMContentLoaded', function() {
        updatePayoutFields();
        initAutodepositTooltip();

        // Add event listeners for Interac form validation
        const emailInput = document.getElementById('interac_email');
        const emailConfirmInput = document.getElementById('interac_email_confirm');
        const autodepositCheckbox = document.getElementById('interac_autodeposit');

        if (emailInput) {
            emailInput.addEventListener('input', validateInteracForm);
            emailInput.addEventListener('blur', validateInteracForm);
        }
        if (emailConfirmInput) {
            emailConfirmInput.addEventListener('input', validateInteracForm);
            emailConfirmInput.addEventListener('blur', validateInteracForm);
        }
        if (autodepositCheckbox) {
            autodepositCheckbox.addEventListener('change', validateInteracForm);
        }

        // Add event listeners for Bank form validation
        const accountHolderName = document.getElementById('account_holder_name');
        const transitNumber = document.getElementById('transit_number');
        const institutionNumber = document.getElementById('institution_number');
        const accountNumber = document.getElementById('account_number');

        if (accountHolderName) {
            accountHolderName.addEventListener('input', validateBankForm);
            accountHolderName.addEventListener('blur', validateBankForm);
        }
        if (transitNumber) {
            transitNumber.addEventListener('input', validateBankForm);
            transitNumber.addEventListener('blur', validateBankForm);
        }
        if (institutionNumber) {
            institutionNumber.addEventListener('input', validateBankForm);
            institutionNumber.addEventListener('blur', validateBankForm);
        }
        if (accountNumber) {
            accountNumber.addEventListener('input', validateBankForm);
            accountNumber.addEventListener('blur', validateBankForm);
        }

        // Add event listeners for PayPal form validation
        const paypalEmailInput = document.getElementById('paypal_email');
        const paypalEmailConfirmInput = document.getElementById('paypal_email_confirm');

        if (paypalEmailInput) {
            paypalEmailInput.addEventListener('input', validatePayPalForm);
            paypalEmailInput.addEventListener('blur', validatePayPalForm);
        }
        if (paypalEmailConfirmInput) {
            paypalEmailConfirmInput.addEventListener('input', validatePayPalForm);
            paypalEmailConfirmInput.addEventListener('blur', validatePayPalForm);
        }

        // Initial validation
        validateInteracForm();
        validateBankForm();
        validatePayPalForm();

        // Check if there are any validation errors
        @if($errors->any())
            // Show the appropriate tab based on where the error occurred
            @if($errors->has('interac_email') || $errors->has('interac_email_confirm'))
                document.getElementById('interac').checked = true;
            @elseif($errors->has('account_holder_number') || $errors->has('account_holder_name') || $errors->has('institution_number') || $errors->has('branch_number'))
                document.getElementById('bank').checked = true;
            @elseif($errors->has('paypal_email') || $errors->has('paypal_email_confirm'))
                document.getElementById('paypal').checked = true;
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
