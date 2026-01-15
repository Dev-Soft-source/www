@extends('layouts.template')

@section('content')

<div class="grid grid-cols-12 gap-4 md:container md:mx-auto  my-6 md:my-10 xl:my-14 px-4 xl:px-0">
    @include('layouts.inc.profile_sidebar')

    <div class="bg-white border rounded p-4 border-gray-200 w-full col-span-12 lg:col-span-9 shadow">
        @if(session('message'))
            <div id="myModal" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div onclick="closeModal()" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                    <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                        <div class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
                            <button type="button" onclick="closeModal()" class="absolute top-3 right-3 text-gray-400 hover:text-gray-500">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                            <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                                <div class="text-center sm:ml-4 sm:mt-0 sm:text-left">
                                    <div class="mt-2 w-full">
                                        <p class="can-exp-p text-center">{{ session('message') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="px-4 pb-6 pt-4 flex items-center space-x-2 sm:space-x-4 sm:px-6 justify-center">
                                <a href="#" onclick="closeNotificationModal()" class="button-exp-fill w-28">Close </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="mb-4 pb-2 flex justify-between items-center">
            <h1 class="mb-0">{{$paymentSettingDetail->main_heading ?? "Payment options"}}</h1>
            <button type="button" onclick="openAddPaymentMethodModal()" class="button-exp-fill">{{$paymentSettingDetail->add_new_card_button_text ?? "Add Payment Method"}}</button>
        </div>

        <div class="max-h-[52rem] overflow-y-auto pr-2">
            @if (!empty($cards) && count($cards) > 0)
                @foreach ($cards as $card)
                    <div class="even:bg-gray-100 odd:bg-white rounded border border-gray-100 shadow-md p-3 md:p-6 mt-3 mb-4">
                        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                            <div class="flex-1">
                                @if($card->payment_method_type == 'card' && $card->paymentMethod && $card->paymentMethod->card)
                                    <h6 class="card-title leading-7 m-0 capitalize font-semibold">
                                        {{ $card->paymentMethod->card->brand }}
                                    </h6>
                                    <p class="text-gray-900 mt-1">
                                        ending in {{ $card->paymentMethod->card->last4 }}
                                    </p>
                                    <p id="exp_date_{{ $card->id }}" class="text-sm text-gray-600"></p>
                                @elseif($card->payment_method_type == 'paypal')
                                    <h6 class="card-title leading-7 m-0 font-semibold">
                                        PayPal
                                    </h6>
                                    <p class="text-gray-900 mt-1">
                                        {{ $card->paypal_email ?? 'PayPal account' }}
                                    </p>
                                @elseif($card->payment_method_type == 'apple_pay')
                                    <h6 class="card-title leading-7 m-0 font-semibold">
                                        Apple Pay
                                    </h6>
                                    @php
                                        $details = is_array($card->payment_method_details) ? $card->payment_method_details : json_decode($card->payment_method_details, true);
                                    @endphp
                                    <p class="text-gray-900 mt-1">
                                        {{ $details['card_brand'] ?? 'Card' }} •••• {{ $details['last4'] ?? '****' }}
                                    </p>
                                @elseif($card->payment_method_type == 'google_pay')
                                    <h6 class="card-title leading-7 m-0 font-semibold">
                                        G Pay
                                    </h6>
                                    @php
                                        $details = is_array($card->payment_method_details) ? $card->payment_method_details : json_decode($card->payment_method_details, true);
                                    @endphp
                                    <p class="text-gray-900 mt-1">
                                        {{ $details['card_brand'] ?? 'Card' }} •••• {{ $details['last4'] ?? '****' }}
                                    </p>
                                @endif
                            </div>
                            <div class="flex flex-col sm:flex-row gap-2 w-full md:w-auto">
                                @if ($card->primary_card === '0' || $card->primary_card == 0)
                                    <a href="{{ route('my_cards.set_primary', $card->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded font-FuturaMdCnBT whitespace-nowrap text-center">{{$paymentSettingDetail->set_primary_card_label ?? "Make Primary"}}</a>
                                @else
                                    <span class="bg-green-600 text-white px-4 py-2 rounded font-FuturaMdCnBT whitespace-nowrap text-center cursor-default">{{$paymentSettingDetail->mobile_default_card_tab ?? "Primary"}}</span>
                                @endif
                                <button type="button" onclick="toggleModalCard('card-modal', {{ $card->id }})" class="button-exp-fill whitespace-nowrap">
                                    {{$paymentSettingDetail->delete_card_button_text ?? "Delete"}}
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <p class="text-gray-600 text-center py-8">No payment methods added yet.</p>
            @endif
        </div>
    </div>
</div>

<!-- Add Payment Method Modal -->
<div class="hidden overflow-x-hidden overflow-y-auto fixed inset-0 z-50 outline-none focus:outline-none justify-center items-center" id="add-payment-method-modal">
    <div class="relative h-screen my-6 mx-auto flex items-center justify-center w-full">
        <div class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border max-h-[90vh] overflow-y-auto">
            <button type="button" onclick="closeAddPaymentMethodModal()" class="absolute top-3 right-3 text-gray-400 hover:text-gray-500 z-10">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                <h3 class="text-2xl font-FuturaMdCnBT font-medium text-gray-900 mb-6">Add Payment Method</h3>
                
                <!-- Payment Method Selection -->
                <div id="payment-method-selection" class="space-y-4">
                    <!-- Apple Pay (only on Apple devices) -->
                    <div id="apple-pay-button-container" class="hidden">
                        <div id="apple-pay-button" style="width: 100%; height: 50px; cursor: pointer; -webkit-appearance: -apple-pay-button; -apple-pay-button-type: plain; -apple-pay-button-style: black;"></div>
                    </div>
                    
                    <!-- Google Pay -->
                    <div id="google-pay-button-container">
                        <div id="google-pay-button" style="width: 100%; height: 50px;"></div>
                    </div>
                    
                    <!-- PayPal -->
                    <div id="paypal-button-container" style="width: 100%;"></div>
                    
                    <!-- Divider -->
                    <div class="relative flex items-center my-6">
                        <div class="flex-grow border-t border-gray-300"></div>
                        <span class="flex-shrink mx-4 text-gray-500">OR</span>
                        <div class="flex-grow border-t border-gray-300"></div>
                    </div>
                    
                    <!-- Credit/Debit Card Button -->
                    <button type="button" onclick="showCardForm()" class="button-exp-fill w-full">
                        Credit or Debit Card
                    </button>
                </div>
                
                <!-- Card Form (hidden by default) -->
                <div id="card-form-container" class="hidden mt-6">
                    <form id="payment-form" method="POST" action="{{ route('my_cards.store') }}">
                        @csrf
                        <input type="hidden" name="payment_method_type" value="card">
                        <input type="hidden" name="stripeToken" id="stripeToken">
                        
                        <div class="space-y-4">
                            <div>
                                <label for="name_on_card" class="block text-sm font-medium text-gray-700 mb-1">{{$paymentSettingDetail->name_on_card_label ?? "Name on card"}}</label>
                                <input type="text" id="name_on_card" name="name_on_card" value="{{ old('name_on_card') }}" class="block mt-1 border p-2 w-full rounded border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600" required>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Card details</label>
                                <div id="payment-element" class="border p-2 rounded border-gray-300"></div>
                                <div id="payment-element-errors" class="text-red-500 text-sm mt-1"></div>
                            </div>
                            
                            <div>
                                <label for="street_address" class="block text-sm font-medium text-gray-700 mb-1">{{$paymentSettingDetail->mobile_street_name_label ?? "Street address"}}</label>
                                <input type="text" id="street_address" name="street_address" value="{{ old('street_address') }}" class="block mt-1 border p-2 w-full rounded border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600" required>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="city" class="block text-sm font-medium text-gray-700 mb-1">{{$paymentSettingDetail->mobile_city_label ?? "City"}}</label>
                                    <input type="text" id="city" name="city" value="{{ old('city') }}" class="block mt-1 border p-2 w-full rounded border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600" required>
                                </div>
                                <div>
                                    <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-1">{{$paymentSettingDetail->mobile_postal_code_label ?? "Postal code"}}</label>
                                    <input type="text" id="postal_code" name="postal_code" value="{{ old('postal_code') }}" class="block mt-1 border p-2 w-full rounded border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600" required>
                                </div>
                            </div>
                            
                            <div>
                                <label for="country" class="block text-sm font-medium text-gray-700 mb-1">{{$paymentSettingDetail->mobile_country_label ?? "Country"}}</label>
                                <input type="text" id="country" name="country" value="{{ old('country') }}" class="block mt-1 border p-2 w-full rounded border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600" required>
                            </div>
                            
                            <button type="submit" id="submit-card-button" class="button-exp-fill w-full mt-4" disabled>Save Card</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="hidden opacity-25 fixed inset-0 z-40 bg-black" id="add-payment-method-modal-backdrop"></div>

<!-- Delete Confirmation Modal -->
<div class="hidden overflow-x-hidden overflow-y-auto fixed inset-0 z-50 outline-none focus:outline-none justify-center items-center" id="card-modal">
    <div class="relative h-screen my-6 mx-auto flex items-center justify-center w-full">
        <div class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
            <button type="button" onclick="toggleModalCard('card-modal')" class="absolute top-3 right-3 text-gray-400 hover:text-gray-500">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                <div class="text-center sm:ml-4 sm:mt-0 sm:text-left">
                    <div class="">
                        <h3 class="text-3xl text-center font-FuturaMdCnBT font-medium text-gray-900 mb-4" id="modal-title">Delete Payment Method?</h3>
                    </div>
                    <div class="mt-2 w-full">
                        <p class="can-exp-p text-center">Are you sure you want to delete this payment method?</p>
                    </div>
                </div>
            </div>
            <div class="px-4 pb-6 pt-4 flex items-center space-x-2 sm:space-x-4 sm:px-6 justify-center">
                <a id="delete-card-link" href="#" class="inline-flex w-auto justify-center rounded bg-red-500 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white whitespace-nowrap hover:text-white hover:shadow-lg shadow-sm hover:bg-red-400 sm:ml-3 sm:w-24">Delete</a>
                <button type="button" onclick="toggleModalCard('card-modal')" class="button-exp-fill sm:w-24">Cancel</button>
            </div>
        </div>
    </div>
</div>
<div class="hidden opacity-25 fixed inset-0 z-40 bg-black" id="card-modal-backdrop"></div>

@endsection

@section('script')

<script src="https://js.stripe.com/v3/"></script>
<script src="https://www.paypal.com/sdk/js?client-id={{ env('PAYPAL_CLIENT_ID') }}&currency=USD&intent=capture&vault=true"></script>
<script src="https://pay.google.com/gp/p/js/pay.js"></script>

<script>
    const stripe = Stripe('{{ env('STRIPE_KEY') }}');
    let elements;
    let paymentElement;
    let isAppleDevice = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
    
    function openAddPaymentMethodModal() {
        const modal = document.getElementById('add-payment-method-modal');
        const backdrop = document.getElementById('add-payment-method-modal-backdrop');
        modal.classList.remove('hidden');
        backdrop.classList.remove('hidden');
        initializePaymentMethods();
    }
    
    function closeAddPaymentMethodModal() {
        const modal = document.getElementById('add-payment-method-modal');
        const backdrop = document.getElementById('add-payment-method-modal-backdrop');
        modal.classList.add('hidden');
        backdrop.classList.add('hidden');
        document.getElementById('payment-method-selection').classList.remove('hidden');
        document.getElementById('card-form-container').classList.add('hidden');
    }
    
    function showCardForm() {
        document.getElementById('payment-method-selection').classList.add('hidden');
        document.getElementById('card-form-container').classList.remove('hidden');
        initializeStripePaymentElement();
    }
    
    function initializePaymentMethods() {
        // Show/hide Apple Pay based on device
        if (isAppleDevice && window.ApplePaySession && ApplePaySession.canMakePayments()) {
            document.getElementById('apple-pay-button-container').classList.remove('hidden');
            initializeApplePay();
        } else {
            document.getElementById('apple-pay-button-container').classList.add('hidden');
        }
        
        initializeGooglePay();
        initializePayPal();
    }
    
    let setupIntentClientSecret = null;
    
    async function initializeStripePaymentElement() {
        if (!paymentElement) {
            try {
                // Create SetupIntent
                const response = await fetch('{{ route("my_cards.create_setup_intent") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                const data = await response.json();
                
                if (!data.clientSecret) {
                    throw new Error('Failed to create setup intent');
                }
                
                setupIntentClientSecret = data.clientSecret;
                
                elements = stripe.elements({
                    clientSecret: setupIntentClientSecret,
                    appearance: {
                        theme: 'stripe',
                    }
                });
                
                paymentElement = elements.create('payment', {
                    layout: 'tabs'
                });
                paymentElement.mount('#payment-element');
                
                paymentElement.on('ready', function() {
                    document.getElementById('submit-card-button').disabled = false;
                });
                
                paymentElement.on('change', function(event) {
                    const displayError = document.getElementById('payment-element-errors');
                    if (event.error) {
                        displayError.textContent = event.error.message;
                    } else {
                        displayError.textContent = '';
                    }
                });
            } catch (error) {
                console.error('Error initializing payment element:', error);
                document.getElementById('payment-element-errors').textContent = 'Failed to initialize payment form. Please refresh and try again.';
            }
        }
    }
    
    // Handle card form submission
    document.getElementById('payment-form')?.addEventListener('submit', async function(event) {
        event.preventDefault();
        const submitButton = document.getElementById('submit-card-button');
        submitButton.disabled = true;
        submitButton.textContent = 'Processing...';
        
        try {
            const {error: submitError} = await elements.submit();
            if (submitError) {
                document.getElementById('payment-element-errors').textContent = submitError.message;
                submitButton.disabled = false;
                submitButton.textContent = 'Save Card';
                return;
            }
            
            const {error, setupIntent} = await stripe.confirmSetup({
                elements,
                clientSecret: setupIntentClientSecret,
                confirmParams: {
                    payment_method_data: {
                        billing_details: {
                            name: document.getElementById('name_on_card').value,
                            address: {
                                line1: document.getElementById('street_address').value,
                                city: document.getElementById('city').value,
                                postal_code: document.getElementById('postal_code').value,
                                country: document.getElementById('country').value,
                            }
                        }
                    }
                },
                redirect: 'if_required'
            });
            
            if (error) {
                document.getElementById('payment-element-errors').textContent = error.message;
                submitButton.disabled = false;
                submitButton.textContent = 'Save Card';
            } else {
                document.getElementById('stripeToken').value = setupIntent.payment_method;
                this.submit();
            }
        } catch (err) {
            console.error('Payment confirmation error:', err);
            document.getElementById('payment-element-errors').textContent = 'An error occurred. Please try again.';
            submitButton.disabled = false;
            submitButton.textContent = 'Save Card';
        }
    });
    
    let applePayClickHandler = null;
    
    function initializeApplePay() {
        const applePayButton = document.getElementById('apple-pay-button');
        
        // Remove existing event listener if any
        if (applePayClickHandler) {
            applePayButton.removeEventListener('click', applePayClickHandler);
        }
        
        // Create new handler
        applePayClickHandler = function() {
            if (!window.ApplePaySession || !ApplePaySession.canMakePayments()) {
                alert('Apple Pay is not available on this device.');
                return;
            }
            
            const request = {
                countryCode: 'US',
                currencyCode: 'USD',
                supportedNetworks: ['visa', 'masterCard', 'amex', 'discover'],
                merchantCapabilities: ['supports3DS'],
                total: {
                    label: 'ProximaRide',
                    amount: '0.00'
                }
            };
            
            const session = new ApplePaySession(3, request);
            
            session.onvalidatemerchant = function(event) {
                // In production, validate with your server
                fetch('/api/apple-pay/validate', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({validationURL: event.validationURL})
                })
                .then(response => response.json())
                .then(data => {
                    session.completeMerchantValidation(data);
                })
                .catch(() => {
                    session.abort();
                });
            };
            
            session.onpaymentauthorized = function(event) {
                // Tokenize the payment method
                const payment = event.payment;
                saveApplePayPaymentMethod(payment);
                session.completePayment(ApplePaySession.STATUS_SUCCESS);
            };
            
            session.begin();
        };
        
        // Add the event listener
        applePayButton.addEventListener('click', applePayClickHandler);
    }
    
    function saveApplePayPaymentMethod(payment) {
        fetch('{{ route("my_cards.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                payment_method_type: 'apple_pay',
                payment_method_details: {
                    card_brand: payment.token.paymentMethod.network,
                    last4: payment.token.paymentMethod.displayName
                },
                apple_pay_token: payment.token
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            }
        });
    }
    
    function initializeGooglePay() {
        // Clear any existing buttons first
        const googlePayContainer = document.getElementById('google-pay-button');
        googlePayContainer.innerHTML = '';
        
        const paymentsClient = new google.payments.api.PaymentsClient({
            environment: '{{ env("APP_ENV") === "production" ? "PRODUCTION" : "TEST" }}'
        });
        
        const button = paymentsClient.createButton({
            onClick: onGooglePayButtonClicked,
            buttonColor: 'default',
            buttonType: 'pay',
            buttonSizeMode: 'fill'
        });
        
        googlePayContainer.appendChild(button);
    }
    
    function onGooglePayButtonClicked() {
        const paymentDataRequest = {
            apiVersion: 2,
            apiVersionMinor: 0,
            allowedPaymentMethods: [{
                type: 'CARD',
                parameters: {
                    allowedAuthMethods: ['PAN_ONLY', 'CRYPTOGRAM_3DS'],
                    allowedCardNetworks: ['AMEX', 'DISCOVER', 'JCB', 'MASTERCARD', 'VISA']
                },
                tokenizationSpecification: {
                    type: 'PAYMENT_GATEWAY',
                    parameters: {
                        gateway: 'stripe',
                        'stripe:version': '2018-10-31',
                        'stripe:publishableKey': '{{ env("STRIPE_KEY") }}'
                    }
                }
            }],
            merchantInfo: {
                merchantId: '{{ env("GOOGLE_MERCHANT_ID") }}',
                merchantName: 'ProximaRide'
            },
            transactionInfo: {
                totalPriceStatus: 'NOT_CURRENTLY_KNOWN',
                currencyCode: 'USD'
            }
        };
        
        const paymentsClient = new google.payments.api.PaymentsClient({
            environment: '{{ env("APP_ENV") === "production" ? "PRODUCTION" : "TEST" }}'
        });
        
        paymentsClient.loadPaymentData(paymentDataRequest)
            .then(function(paymentData) {
                processGooglePayPayment(paymentData);
            })
            .catch(function(err) {
                console.error('Google Pay error:', err);
            });
    }
    
    function processGooglePayPayment(paymentData) {
        const token = paymentData.paymentMethodData.tokenizationData.token;
        
        fetch('{{ route("my_cards.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                payment_method_type: 'google_pay',
                payment_method_details: {
                    card_brand: paymentData.paymentMethodData.description,
                    last4: paymentData.paymentMethodData.info.cardDetails
                },
                google_pay_token: token
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            }
        });
    }
    
    function initializePayPal() {
        // Clear any existing PayPal buttons first
        const paypalContainer = document.getElementById('paypal-button-container');
        paypalContainer.innerHTML = '';
        
        paypal.Buttons({
            createOrder: function(data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            value: '0.00'
                        }
                    }],
                    application_context: {
                        brand_name: 'ProximaRide',
                        landing_page: 'NO_PREFERENCE',
                        user_action: 'PAY_NOW',
                        return_url: window.location.origin + '/paypal/return',
                        cancel_url: window.location.origin + '/paypal/cancel'
                    }
                });
            },
            onApprove: function(data, actions) {
                return actions.order.capture().then(function(details) {
                    savePayPalPaymentMethod(details);
                });
            }
        }).render('#paypal-button-container');
    }
    
    function savePayPalPaymentMethod(details) {
        fetch('{{ route("my_cards.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                payment_method_type: 'paypal',
                paypal_email: details.payer.email_address,
                paypal_payer_id: details.payer.payer_id
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            }
        });
    }
    
    function toggleModalCard(modalId, cardId = null) {
        let modal = document.getElementById(modalId);
        let backdrop = document.getElementById(modalId + "-backdrop");

        if (modal.classList.contains("hidden")) {
            modal.classList.remove("hidden");
            backdrop.classList.remove("hidden");

            if (cardId) {
                let deleteLink = document.getElementById("delete-card-link");
                deleteLink.href = `/delete-card/${cardId}`;
            }
        } else {
            modal.classList.add("hidden");
            backdrop.classList.add("hidden");
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        var months = {
            1: 'January', 2: 'February', 3: 'March', 4: 'April', 5: 'May', 6: 'June',
            7: 'July', 8: 'August', 9: 'September', 10: 'October', 11: 'November', 12: 'December'
        };

        @foreach ($cards as $card)
            @if ($card->paymentMethod && $card->paymentMethod->card)
                var exp_month = {{ $card->paymentMethod->card->exp_month }};
                var exp_year = {{ $card->paymentMethod->card->exp_year }};
                var exp_date = months[exp_month] + ' ' + exp_year;
                var expElement = document.getElementById("exp_date_{{ $card->id }}");
                if (expElement) {
                    expElement.textContent = 'Expires ' + exp_date;
                }
            @endif
        @endforeach
    });

    function closeModal() {
        const modal = document.getElementById('myModal');
        if (modal) {
            modal.classList.add('hidden');
        }
    }
    
    function closeNotificationModal() {
        closeModal();
    }
</script>

@endsection
