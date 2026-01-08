@extends('layouts.template')

@section('content')

<div class="grid grid-cols-12 gap-4 md:container md:mx-auto  my-6 md:my-10 xl:my-14 px-4 xl:px-0">
    @include('layouts.inc.profile_sidebar')


    @if($errors->has('error'))
        <div id="poppModal" class=" relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                    <div
                        class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg w-full">
                        <button type="button" onclick="closePopup()" class="absolute top-3 right-3 text-gray-400 hover:text-gray-500">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start justify-center">
                                <!-- <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full sm:mx-0 sm:h-10 sm:w-10 bg-red-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-lg text-white w-8 h-8" viewBox="0 0 16 16">
                                        <path d="M7.005 3.1a1 1 0 1 1 1.99 0l-.388 6.35a.61.61 0 0 1-1.214 0zM7 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0"/>
                                    </svg>
                                </div> -->
                            </div>
                            <div class="text-center sm:ml-4 sm:mt-0 sm:text-left">
                                <div class="">
                                    <h3 class="text-3xl text-center font-FuturaMdCnBT font-medium text-gray-900 mb-4" id="modal-title">{!! session('heading') !!}</h3>
                                </div>
                                <div class="mt-2 w-full">
                                    <p class="text-lg text-center text-black">{{ $errors->first('error') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="px-4 pb-6 pt-4  sm:flex sm:flex-row-reverse sm:px-6 justify-center">
                            <a href="javascript:void(0);" onclick="closePopup()"
                                class="inline-flex w-full justinline-flex justify-center rounded bg-red-500 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-red-400 sm:ml-3 sm:w-24">Close</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white border rounded p-4 border-gray-200 w-full col-span-12 lg:col-span-9 shadow">
        <div class=" pb-2">
            <h1 class="mb-0">{{$paymentSettingDetail->main_heading ?? "Add card"}}</h1>
        </div>


        <form id="payment-form" method="POST" action="{{ route('my_cards.store') }}">
            @csrf
            {{-- <input type="hidden" name="param" value="{{ $param }}"> --}}
            <p class="text-red-500">*  @isset($paymentSettingDetail->indicate_field_label)
                {{ $paymentSettingDetail->indicate_field_label }}
            @endisset
            </p>


            <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4 mt-4">
                <div>
                    <label for="name_on_card">{{$paymentSettingDetail->name_on_card_label ?? "Name on card"}}</label>
                    <input type="text" id="name_on_card" name="name_on_card" value="{{ old('name_on_card') }}" class="block mt-1 border p-1.5 w-full rounded text-base md:text-lg border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600" aria-required="true">
                    @error('name_on_card')
                    <div class="relative tooltip -bottom-4 group-hover:flex">
                        <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                            <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                        </div>
                    </div>
                    @enderror
                    <div class="relative tooltip -bottom-4 group-hover:flex hidden client-error" data-field="name_on_card">
                        <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                            <p class="text-white leading-none text-sm lg:text-base"></p>
                        </div>
                    </div>
                </div>
                <div>
                    <label for="card_number">{{$paymentSettingDetail->card_number_label ?? "Card number"}}</label>
                    <div id="card-number-element" class="block mt-1 border p-1.5 py-[11px] w-full rounded text-base md:text-lg border-gray-300"></div>
                    <div class="relative tooltip -bottom-4 group-hover:flex hidden showCardError">
                        <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                            <p class="text-white leading-none text-sm lg:text-base" id="card-errors"></p>
                        </div>
                    </div>
                </div>

                <div>
                    <label for="card_type">{{$paymentSettingDetail->mobile_card_type_label ?? "Card type"}} <span class="text-red-500">*</span></label>
                    <select id="card_type" name="card_type"
                        class="block mt-1 border p-1.5 w-full rounded border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600">
                        <option value=""
                            {{ old('card_type') === '' ? 'selected' : '' }}>
                            {{ $paymentSettingDetail->select_card_type_text ?? "Select" }}
                        </option>
                        <option value="Visa"
                            {{ old('card_type') ==='Visa' ? 'selected' : '' }}>
                            Visa
                        </option>
                        <option value="MasterCard"
                            {{ old('card_type') ==='MasterCard' ? 'selected' : '' }}>
                            Master card
                        </option>
                        <option value="AmEx"
                            {{ old('card_type') ==='AmEx' ? 'selected' : '' }}>
                            American Express
                        </option>
                        <option value="Dis"
                            {{ old('card_type') ==='Dis' ? 'selected' : '' }}>
                            Discover
                        </option>
                        <option value="CUP"
                            {{ old('card_type') ==='CUP' ? 'selected' : '' }}>
                            Union Pay
                        </option>
                        <option value="JC"
                            {{ old('card_type') ==='JC' ? 'selected' : '' }}>
                            JCB International
                        </option>
                        <option value="DiC"
                            {{ old('card_type') ==='DiC' ? 'selected' : '' }}>
                            Diners Club International
                        </option>
                    </select>
                    @error('card_type')
                      <div class="relative tooltip -bottom-4 group-hover:flex">
                        <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                            <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                        </div>
                      </div>
                    @enderror
                </div>


                <div>
                    <label for="exp_month">{{$paymentSettingDetail->mobile_expiry_date_label ?? "Expiry date"}}</label>
                    <div id="card-expiry-element" class="block mt-1 border p-1.5 py-[11px] w-full rounded text-base md:text-lg border-gray-300"></div>
                    <div class="relative tooltip -bottom-4 group-hover:flex hidden showExpiryError">
                        <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                            <p id="card_errors_expiry" class="text-white leading-none text-sm lg:text-base"></p>
                        </div>
                    </div>
                </div>
                <div>
                    <label for="cvv_code">{{$paymentSettingDetail->security_code_label ?? "Security code (CVV / CVC)"}}</label>
                    <div id="card-cvc-element" class="block mt-1 border p-1.5 py-[11px] w-full rounded text-base md:text-lg border-gray-300"></div>
                    <div class="relative tooltip -bottom-4 group-hover:flex hidden showCvcError">
                        <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                            <p id="card_errors_cvc" class="text-white leading-none text-sm lg:text-base"></p>
                        </div>
                    </div>
                </div>
                <p>{{$paymentSettingDetail->mobile_billing_address_label ?? "Billing Address"}}</p>
                <div>
                    <label for="street_address">{{$paymentSettingDetail->mobile_street_name_label ?? "Street number/name"}}</label>
                    <input type="text" id="street_address" name="street_address" value="{{ old('street_address') }}" class="block mt-1 border p-1.5 w-full rounded text-base md:text-lg border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600" aria-required="true">
                    @error('street_address')
                    <div class="relative tooltip -bottom-4 group-hover:flex">
                        <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                            <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                        </div>
                    </div>
                    @enderror
                    <div class="relative tooltip -bottom-4 group-hover:flex hidden client-error" data-field="street_address">
                        <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                            <p class="text-white leading-none text-sm lg:text-base"></p>
                        </div>
                    </div>
                </div>
                <div>
                    <label for="house_apartment_number">{{$paymentSettingDetail->mobile_house_number_label ?? "House/apartment number (optional)"}}</label>
                    <input type="text" id="house_apartment_number" name="house_apartment_number" value="{{ old('house_apartment_number') }}" class="block mt-1 border p-1.5 w-full rounded text-base md:text-lg border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600">
                    @error('house_apartment_number')
                    <div class="relative tooltip -bottom-4 group-hover:flex">
                        <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                            <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                        </div>
                    </div>
                    @enderror
                </div>
                <div>
                    <label for="city">{{$paymentSettingDetail->mobile_city_label ?? "City"}}</label>
                    <input type="text" id="city" name="city" value="{{ old('city') }}" class="block mt-1 border p-1.5 w-full rounded text-base md:text-lg border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600" aria-required="true">
                    @error('city')
                    <div class="relative tooltip -bottom-4 group-hover:flex">
                        <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                            <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                        </div>
                    </div>
                    @enderror
                    <div class="relative tooltip -bottom-4 group-hover:flex hidden client-error" data-field="city">
                        <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                            <p class="text-white leading-none text-sm lg:text-base"></p>
                        </div>
                    </div>
                </div>
                <div>
                    <label for="province">{{$paymentSettingDetail->mobile_province_label ?? "Province"}}</label>
                    <input type="text" id="province" name="province" value="{{ old('province') }}" class="block mt-1 border p-1.5 w-full rounded text-base md:text-lg border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600" aria-required="true">
                    @error('province')
                    <div class="relative tooltip -bottom-4 group-hover:flex">
                        <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                            <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                        </div>
                    </div>
                    @enderror
                    <div class="relative tooltip -bottom-4 group-hover:flex hidden client-error" data-field="province">
                        <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                            <p class="text-white leading-none text-sm lg:text-base"></p>
                        </div>
                    </div>
                </div>
                <div>
                    <label for="country">{{$paymentSettingDetail->mobile_country_label ?? "Country"}}</label>
                    <input type="text" id="country" name="country" value="{{ old('country') }}" class="block mt-1 border p-1.5 w-full rounded text-base md:text-lg border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600" aria-required="true">
                    @error('country')
                    <div class="relative tooltip -bottom-4 group-hover:flex">
                        <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                            <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                        </div>
                    </div>
                    @enderror
                    <div class="relative tooltip -bottom-4 group-hover:flex hidden client-error" data-field="country">
                        <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                            <p class="text-white leading-none text-sm lg:text-base"></p>
                        </div>
                    </div>
                </div>
                <div>
                    <label for="postal_code">{{$paymentSettingDetail->mobile_postal_code_label ?? "Postal code"}}</label>
                    <input type="text" maxlength="7" id="postal_code" name="postal_code" value="{{ old('postal_code') }}" class="block mt-1 border p-1.5 w-full rounded text-base md:text-lg border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600" aria-required="true">
                    @error('postal_code')
                    <div class="relative tooltip -bottom-4 group-hover:flex">
                        <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                            <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                        </div>
                    </div>
                    @enderror
                    <div class="relative tooltip -bottom-4 group-hover:flex hidden client-error" data-field="postal_code">
                        <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                            <p class="text-white leading-none text-sm lg:text-base"></p>
                        </div>
                    </div>
                </div>
                <div class="md:col-span-2">
                    <div class="flex items-center space-x-1.5 lg:space-x-3 mb-2 mr-2 lg:mr-2">
                        <input name="primary_card" type="checkbox" value="1" {{ old('primary_card') == '1' ? 'checked' : '' }} class="h-5 w-5 border-gray-300 bg-gray-200 cursor-pointer text-indigo-600 focus:ring-indigo-600">
                        <label for="primary_card" class="block text-gray-900">{{$paymentSettingDetail->mobile_primary_card_placeholder ?? "Primary card"}}</label>
                    </div>
                </div>
                <div class="md:col-span-2 mt-4 flex justify-center">
                    <button type="submit" class="button-exp-fill">{{$paymentSettingDetail->save_button_text ?? "Save"}}</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@section('script')

<script src="https://js.stripe.com/v3/"></script>
<script>
    var stripe = Stripe('{{ env('STRIPE_KEY') }}');
    var elements = stripe.elements();


    var cardNumberElement = elements.create('cardNumber');
    cardNumberElement.mount('#card-number-element');

    var cardExpiryElement = elements.create('cardExpiry');
    cardExpiryElement.mount('#card-expiry-element');

    var cardCvcElement = elements.create('cardCvc');
    cardCvcElement.mount('#card-cvc-element');

    var form = document.getElementById('payment-form');

    // Track completion state of Stripe Elements
    var isCardNumberComplete = false;
    var isExpiryComplete = false;
    var isCvcComplete = false;

    function clearClientErrors() {
        document.querySelectorAll('.client-error').forEach(function(container){
            container.classList.add('hidden');
            var p = container.querySelector('p');
            if (p) { p.textContent = ''; }
        });
    }

    function showClientError(fieldId, message) {
        var container = document.querySelector('.client-error[data-field="' + fieldId + '"]');
        if (container) {
            var p = container.querySelector('p');
            if (p) { p.textContent = message; }
            container.classList.remove('hidden');
        }
    }

    function validateRequiredFields() {
        clearClientErrors();
        var requiredFieldIds = ['name_on_card','street_address','city','province','country','postal_code'];
        var firstErrorContainer = null;
        var hasErrors = false;
        requiredFieldIds.forEach(function(id){
            var el = document.getElementById(id);
            if (el && String(el.value).trim() === '') {
                hasErrors = true;
                showClientError(id, 'This field is required.');
                var container = document.querySelector('.client-error[data-field="' + id + '"]');
                if (!firstErrorContainer && container) { firstErrorContainer = container; }
            }
        });
        return { hasErrors: hasErrors, firstErrorEl: firstErrorContainer };
    }
    // Scroll to first error on page load if there are errors
document.addEventListener('DOMContentLoaded', function() {
    // Check if there are any validation errors
    @if($errors->any())
        // Find the first element with an error
        const firstErrorElement = document.querySelector('.tooltip:not(.hidden)');

        if (firstErrorElement) {
            // Scroll to the first error
            firstErrorElement.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
        }
    @endif

    // For Stripe errors that might appear after form submission
    const stripeErrorElements = [
        document.querySelector('.showCardError:not(.hidden)'),
        document.querySelector('.showExpiryError:not(.hidden)'),
        document.querySelector('.showCvcError:not(.hidden)')
    ].filter(el => el !== null);

    if (stripeErrorElements.length > 0) {
        stripeErrorElements[0].scrollIntoView({
            behavior: 'smooth',
            block: 'center'
        });
    }
});

    form.addEventListener('submit', function(event) {
        event.preventDefault();

        var textValidation = validateRequiredFields();
        var stripeFirstErrorEl = null;
        // Reset stripe error containers
        document.querySelectorAll('.showExpiryError, .showCardError, .showCvcError').forEach(function(el){ el.classList.add('hidden'); });

        if (!isCardNumberComplete) {
            var els = document.querySelectorAll('.showCardError');
            els.forEach(function(el){ el.classList.remove('hidden'); });
            var elMsg = document.getElementById('card-errors');
            if (elMsg) { elMsg.textContent = 'Card number is required.'; }
            if (!stripeFirstErrorEl && els.length) { stripeFirstErrorEl = els[0]; }
        }
        if (!isExpiryComplete) {
            var els = document.querySelectorAll('.showExpiryError');
            els.forEach(function(el){ el.classList.remove('hidden'); });
            var elMsg = document.getElementById('card_errors_expiry');
            if (elMsg) { elMsg.textContent = 'Expiry date is required.'; }
            if (!stripeFirstErrorEl && els.length) { stripeFirstErrorEl = els[0]; }
        }
        if (!isCvcComplete) {
            var els = document.querySelectorAll('.showCvcError');
            els.forEach(function(el){ el.classList.remove('hidden'); });
            var elMsg = document.getElementById('card_errors_cvc');
            if (elMsg) { elMsg.textContent = 'Security code is required.'; }
            if (!stripeFirstErrorEl && els.length) { stripeFirstErrorEl = els[0]; }
        }
        var hasStripeErrors = (!isCardNumberComplete || !isExpiryComplete || !isCvcComplete);
        if (textValidation.firstErrorEl) {
            textValidation.firstErrorEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
        } else if (stripeFirstErrorEl) {
            stripeFirstErrorEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        if (textValidation.hasErrors || hasStripeErrors) {
            return; // Do not attempt tokenization until all required fields are complete
        }

        stripe.createToken(cardNumberElement, {
            name: document.getElementById('name_on_card').value
        }).then(function(result) {
            if (result.error) {
                // debugger;
                document.querySelectorAll('.showExpiryError, .showCardError, .showCvcError').forEach(el => {
                el.classList.add('hidden');
            });

                var elements = document.querySelectorAll('.showExpiryError');
                elements.forEach(function(element) {
                    element.classList.add('hidden');
                });

                var elements = document.querySelectorAll('.showCardError');
                elements.forEach(function(element) {
                    element.classList.add('hidden');
                });

                var elements = document.querySelectorAll('.showCvcError');
                elements.forEach(function(element) {
                    element.classList.add('hidden');
                });
                if(result.error.code == "invalid_expiry_year"){
                    var elements = document.querySelectorAll('.showExpiryError');
                    elements.forEach(function(element) {
                        element.classList.remove('hidden');
                    });
                    var element = document.getElementById('card_errors_expiry');
                    element.textContent = result.error.message;
                    elements[0].scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });

                }else if(result.error.code == "incomplete_cvc"){
                    var elements = document.querySelectorAll('.showCvcError');
                    elements.forEach(function(element) {
                        element.classList.remove('hidden');
                    });
                    var element = document.getElementById('card_errors_cvc');
                    element.textContent = result.error.message;

                    elements[0].scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
                }else{
                    var elements = document.querySelectorAll('.showCardError');
                    elements.forEach(function(element) {
                        element.classList.remove('hidden');
                    });
                    var element = document.getElementById('card-errors');
                    element.textContent = result.error.message;

                    elements[0].scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
                }


            } else {
                stripeTokenHandler(result.token);
            }
        });
    });

    // Hide client error on input
    ['name_on_card','street_address','city','province','country','postal_code'].forEach(function(id){
        var el = document.getElementById(id);
        if (el) {
            el.addEventListener('input', function(){
                var container = document.querySelector('.client-error[data-field="' + id + '"]');
                if (container) { container.classList.add('hidden'); }
            });
        }
    });

    cardNumberElement.on('change', (event) => {
      isCardNumberComplete = !!event.complete;
      if (event.error) {
        var elements = document.querySelectorAll('.showCardError');
        elements.forEach(function(element) {
            element.classList.remove('hidden');
        });
        var element = document.getElementById('card-errors');
        element.textContent = event.error.message;
      } else {
        var elements = document.querySelectorAll('.showCardError');
        elements.forEach(function(element) {
            element.classList.add('hidden');
        });
      }
    });

    cardExpiryElement.on('change', (event) => {
      isExpiryComplete = !!event.complete;
      if (event.error) {
        var elements = document.querySelectorAll('.showExpiryError');
        elements.forEach(function(element) {
            element.classList.remove('hidden');
        });
        var element = document.getElementById('card_errors_expiry');
        element.textContent = event.error.message;
      } else {
        var elements = document.querySelectorAll('.showExpiryError');
        elements.forEach(function(element) {
            element.classList.add('hidden');
        });
      }
    });

    cardCvcElement.on('change', (event) => {
      isCvcComplete = !!event.complete;
      if (event.error) {
        var elements = document.querySelectorAll('.showCvcError');
        elements.forEach(function(element) {
            element.classList.remove('hidden');
        });
        var element = document.getElementById('card_errors_cvc');
        element.textContent = event.error.message;
      } else {
        var elements = document.querySelectorAll('.showCvcError');
        elements.forEach(function(element) {
            element.classList.add('hidden');
        });
    }
});

function stripeTokenHandler(token) {
    var form = document.getElementById('payment-form');
    var hiddenInput = document.createElement('input');
    hiddenInput.setAttribute('type', 'hidden');
    hiddenInput.setAttribute('name', 'stripeToken');
    hiddenInput.setAttribute('value', token.id);
    form.appendChild(hiddenInput);
    
    form.submit();
}

function closePopup() {
    var element = document.getElementById('poppModal');
        element.classList.add('hidden');
    
    }
</script>

@endsection
