@extends('layouts.template')

@section('content')

<div class="grid grid-cols-12 gap-4 container mx-auto p-4 my-14">
    @include('layouts.inc.profile_sidebar')

    @php
        $cardSelected = false;
    @endphp

    <div class="bg-white border border-gray-200 rounded p-4 lg:p-4 w-full col-span-12 lg:col-span-9">
        <div class="flex flex-wrap mt-4" id="tabs-id">
            <div class="w-full">
                <form id="submitForm" method="POST" action="{{ route('store_top_up_balance') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="gPayApplePayId" value="0">
                    <h1>Top up my balance</h1>
                    <div class="grid grid-cols-1 lg:grid-cols-1 gap-4">
                        <div class="col-span-1">
                            <div class="space-y-4">
                                <div class="bg-white rounded-lg overflow-hidden shadow-3xl">
                                    <div class="bg-white p-4">
                                        @if(session('message'))
                                            <div class="mt-4 mb-4 rounded-lg px-6 py-3 bg-red-100 text-gray-600" role="alert">
                                                {{ session('message') }}
                                            </div>
                                        @endif

                                        <div class="space-y-4 mb-4">
                                            <div class="w-full md:w-1/2">
                                                <label for="seats" class="block mb-2 font-medium text-gray-900">Purchase top up balance</label>
                                                <input type="number" id="dr_amount" step="any" name="dr_amount" value="{{ old('dr_amount') }}" placeholder="Enter the amount you want to add" class=" block mt-1 border p-1.5 w-full rounded text-base md:text-lg border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600 {{ $errors->has('dr_amount') ? 'border-red-500' : '' }}">
                                                @error('dr_amount')
                                                    <div class="relative tooltip -bottom-4 group-hover:flex">
                                                        <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                                            <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                                        </div>
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="space-y-4 mt-8">
                                            <div class="w-full md:w-1/2">
                                                <!-- <label for="payment_method" class="block mb-3 font-medium text-gray-900 mt-12 font-FuturaMdCnBT can-exp-h4">Pay with</label> -->
                                                <div class="border rounded-md overflow-hidden divide-y">
                                                    @php
                                                    $paymentMethodOld = old('payment_method');
                                                    $defaultPaymentMethod = $paymentMethodOld ?: (count($cards) > 0 ? 'credit_card' : 'paypal');
                                                @endphp
                                                    <div class="flex items-center justify-between p-3">
                                                        <input type="radio" id="paypal" name="payment_method" value="paypal" class="hidden peer" {{ $defaultPaymentMethod === 'paypal' ? 'checked' : '' }}>
                                                        <label for="paypal" class="inline-flex items-center space-x-3 w-full p-4 text-gray-800 bg-white border-2 border-gray-100 rounded cursor-pointer peer-checked:border-blue-500 peer-checked:border-2 peer-checked:text-blue-500 hover:border-2 hover:border-blue-500">
                                                            <img class="h-12" src="{{ asset('assets/paypal.png') }}" alt="">
                                                        </label>
                                                    </div>
                                                    <div>
                                                        <div class="flex items-center justify-between p-3">
                                                            <input type="radio" id="credit_card" name="payment_method" value="credit_card" class="hidden peer" {{ $defaultPaymentMethod === 'credit_card' ? 'checked' : '' }}>
                                                            <label for="credit_card" class="inline-flex items-center space-x-3 w-full p-4 text-gray-800 bg-white border-2 border-gray-100 rounded cursor-pointer peer-checked:border-blue-500 peer-checked:border-2 peer-checked:text-blue-500 hover:border-2 hover:border-blue-500">
                                                                <span class="font-medium text-xl">
                                                                    Debit or Credit Card
                                                                </span>
                                                            </label>
                                                        </div>
                                                        <div class="cards mt-2 pb-2 {{ $defaultPaymentMethod === 'credit_card' ? '' : 'hidden' }}">
                                                            @php
                                                                $cardCheckedAssigned = false;
                                                            @endphp
                                                            @foreach ($cards as $card)
                                                                @if ($card->paymentMethod)
                                                                    <div class="flex items-start justify-between p-3">
                                                                        <label for="card_id_{{ $card->id }}" class="font-normal text-gray-900 flex items-start space-x-1 cursor-pointer">
                                                                            <div>
                                                                                <p class="leading-normal mt-2">
                                                                                    **** **** **** {{ $card->paymentMethod->card->last4 }}
                                                                                </p>
                                                                                <div class="font-normal text-gray-900 flex lg:block items-center space-x-0.5 2xl:pr-8">
                                                                                    <small>{{ $card->paymentMethod->card->brand }}</small>
                                                                                </div>
                                                                            </div>
                                                                        </label>
                                                                        @php
                                                                            $checked = '';
                                                                            if ($paymentMethodOld && (string) old('card_id') === (string) $card->id) {
                                                                                $checked = 'checked';
                                                                                $cardSelected = true;
                                                                            } elseif (!$cardCheckedAssigned && $card->primary_card) {
                                                                                $checked = 'checked';
                                                                                $cardSelected = true;
                                                                                $cardCheckedAssigned = true;
                                                                            }
                                                                        @endphp
                                                                        <input type="radio" id="card_id_{{ $card->id }}" name="card_id" value="{{ $card->id }}"
                                                                            {{ $checked }} class="w-4 h-4 mt-2 ml-4 text-blue-600 cursor-pointer bg-white border-gray-500 rounded focus:ring-blue-500 focus:ring-2">
                                                                    </div>
                                                                @endif
                                                            @endforeach
                                                            @error('card_id')
                                                              <div class="relative tooltip -bottom-4 group-hover:flex">
                                                                <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                                                    <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                                                </div>
                                                              </div>
                                                            @enderror
                                                            <div class="flex justify-center items-center mt-4">
                                                                <a href="{{ route('my_cards.create', ['lang' => $selectedLanguage->abbreviation]) }}"
                                                                    class="button-exp-fill">Add new card</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="flex items-center justify-between p-3 border-t">
                                                        <span class="font-medium text-xl flex items-center gap-2">
                                                            <img src="https://www.gstatic.com/instantbuy/svg/dark_gpay.svg" alt="" class="h-6" width="48" height="24" onerror="this.style.display='none'">
                                                            Google Pay / Apple Pay
                                                        </span>
                                                    </div>
                                                    <div id="paymentSectionGPay" class="px-3 pb-3">
                                                        <p id="gpayPlaceholder" class="text-sm text-gray-500 mb-2">Enter an amount above to pay with Google Pay or Apple Pay.</p>
                                                        <div id="payment-request-button" class="min-h-[48px]"></div>
                                                        <p id="gpayError" class="text-red-500 text-sm mt-2 hidden" role="alert"></p>
                                                        <p id="gpayUnsupported" class="text-sm text-gray-500 mt-2 hidden">Google Pay and Apple Pay are not available. Requirements: <strong>HTTPS</strong>, domain registered in Stripe Dashboard (Settings → Payment methods), and a card in Google Pay (<a href="https://pay.google.com" target="_blank" rel="noopener" class="underline">pay.google.com</a>) or Apple Wallet. In Chrome, enable &quot;Payment methods&quot; in Settings → Privacy and security → Site settings.</p>
                                                    </div>
                                                </div>
                                                @error('payment_method')
                                                  <div class="relative tooltip -bottom-4 group-hover:flex">
                                                    <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                                        <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                                    </div>
                                                  </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="flex justify-center items-center mt-4 md:w-1/2 w-full">
                                            <button id="submitButton" class="button-exp-fill" type="submit">
                                               {{ $cardSelected == true ? "1 tap buy" : "Buy" }} 
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://js.stripe.com/v3/"></script>

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
        else if ($(this).parent().parent().parent().parent().find('.tooltip').length > 0) {
            $(this).parent().parent().parent().parent().find('.tooltip').addClass('hidden');
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


    $(document).ready(function () {
        $('input[type=radio][name=payment_method]').change(function() {
            if (this.value === 'credit_card') {
                $('.cards').removeClass('hidden');
                // $('.other_number').addClass('hidden');
            } else if (this.value === 'paypal') {
                $('.cards').addClass('hidden');
                // $('.other_number').removeClass('hidden');
            }
        });
    });

    document.getElementById('submitForm').addEventListener('submit', function () {
        document.getElementById('submitButton').setAttribute('disabled', 'true');
    });
</script>


<script>
    var stripePk = '{{ env('STRIPE_KEY') ?? '' }}';
    if (!stripePk || stripePk.length < 10) {
        document.addEventListener('DOMContentLoaded', function () {
            var el = document.getElementById('gpayUnsupported');
            if (el) {
                el.textContent = 'Google Pay / Apple Pay are not configured (missing Stripe key).';
                el.classList.remove('hidden');
            }
            var ph = document.getElementById('gpayPlaceholder');
            if (ph) ph.classList.add('hidden');
        });
    } else {
    const stripe = Stripe(stripePk);
    // Payment Request (Google Pay / Apple Pay) – currency must match create-payment-intent backend (CAD)
    const paymentRequest = stripe.paymentRequest({
        country: 'CA',
        currency: 'cad',
        total: {
            label: 'Top up balance',
            amount: 100, // 1 CAD minimum when section is shown
        },
        requestPayerName: true,
        requestPayerEmail: true,
        paymentMethodTypes: ['card'],
    });

    var gpayButtonMounted = false;
    var gpaySupported = null;

    function mountGPayButtonIfSupported() {
        if (gpayButtonMounted) return;
        var container = document.getElementById('payment-request-button');
        var placeholder = document.getElementById('gpayPlaceholder');
        var unsupported = document.getElementById('gpayUnsupported');
        var amount = parseFloat(document.querySelector('[name="dr_amount"]').value) || 0;
        if (amount <= 0) return;

        function tryMount(result) {
            gpaySupported = result;
            if (result && (result.googlePay || result.applePay)) {
                if (container && container.children.length === 0) {
                    var elements = stripe.elements();
                    var prButton = elements.create('paymentRequestButton', {
                        paymentRequest: paymentRequest,
                        style: {
                            paymentRequestButton: {
                                type: 'default',
                                theme: 'dark',
                                height: '48px',
                            },
                        },
                    });
                    prButton.mount('#payment-request-button');
                    gpayButtonMounted = true;
                }
                if (placeholder) placeholder.classList.add('hidden');
                if (unsupported) unsupported.classList.add('hidden');
            } else {
                if (placeholder) placeholder.classList.add('hidden');
                if (unsupported) unsupported.classList.remove('hidden');
            }
        }

        if (gpaySupported !== null) {
            tryMount(gpaySupported);
            return;
        }
        paymentRequest.canMakePayment().then(tryMount).catch(function (error) {
            console.error('Error checking Google Pay / Apple Pay availability:', error);
            if (placeholder) placeholder.classList.add('hidden');
            if (unsupported) unsupported.classList.remove('hidden');
        });
    }

    paymentRequest.canMakePayment().then(function (result) {
        gpaySupported = result;
    }).catch(function () {});

    paymentRequest.on('paymentmethod', async function (ev) {
        const amountInput = document.querySelector('[name="dr_amount"]');
        const amount = parseFloat(amountInput.value) || 0;
        const gpayErrorEl = document.getElementById('gpayError');

        function showError(msg) {
            if (gpayErrorEl) {
                gpayErrorEl.textContent = msg;
                gpayErrorEl.classList.remove('hidden');
            }
            ev.complete('fail');
        }

        if (amount <= 0) {
            showError('Please enter an amount greater than 0.');
            return;
        }

        try {
            const response = await fetch('{{ url("/create-payment-intent") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ payment_method: ev.paymentMethod.id, amount: amount }),
            });

            const data = await response.json();

            if (!response.ok) {
                showError(data.message || data.error || 'Unable to create payment. Please try again.');
                return;
            }

            const clientSecret = data.clientSecret;
            if (!clientSecret) {
                showError('Invalid payment response. Please try again.');
                return;
            }

            const { error, paymentIntent } = await stripe.confirmCardPayment(clientSecret, {
                payment_method: ev.paymentMethod.id,
            });

            if (error) {
                showError(error.message || 'Payment failed.');
                return;
            }

            ev.complete('success');
            if (gpayErrorEl) gpayErrorEl.classList.add('hidden');

            document.querySelector('[name="gPayApplePayId"]').value = paymentIntent.id;
            document.querySelector('[name="payment_method"][value="credit_card"]').checked = true;
            document.getElementById('submitForm').submit();
        } catch (err) {
            console.error(err);
            showError('Something went wrong. Please try again.');
        }
    });

    function updateGPaySection() {
        const amount = parseFloat($('#dr_amount').val()) || 0;
        const placeholder = document.getElementById('gpayPlaceholder');
        const amountCents = Math.max(100, Math.round(amount * 100));
        if (amount > 0) {
            if (placeholder) placeholder.classList.add('hidden');
            paymentRequest.update({
                total: {
                    label: 'Top up balance',
                    amount: amountCents,
                },
            });
            mountGPayButtonIfSupported();
        } else {
            if (placeholder) placeholder.classList.remove('hidden');
            var unsupported = document.getElementById('gpayUnsupported');
            if (unsupported) unsupported.classList.add('hidden');
            var gpayErr = document.getElementById('gpayError');
            if (gpayErr) gpayErr.classList.add('hidden');
        }
    }

    $('#dr_amount').on('input change', updateGPaySection);
    $(document).ready(updateGPaySection);
    }
</script>

@endsection
