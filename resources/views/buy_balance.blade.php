@extends('layouts.template')

@section('content')

<div class="grid grid-cols-12 gap-4 container mx-auto p-4 my-14">
    @include('layouts.inc.profile_sidebar')

    @php
        $cardSelected = false;
    @endphp

    <div class="bg-white border border-gray-200 rounded p-4 lg:p-4 w-full col-span-12 lg:col-span-9">
        <div class="flex flex-wrap" id="tabs-id">
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
                                        <div class="space-y-4 mb-4">
                                            <div class="w-full md:w-1/2">
                                                <label for="payment_method" class="block mb-3 font-medium text-gray-900 mt-12 font-FuturaMdCnBT can-exp-h4">Pay with</label>
                                                <div class="border rounded-md overflow-hidden divide-y">
                                                    <div class="flex items-center justify-between p-3">
                                                        <input type="radio" id="paypal" name="payment_method" value="paypal" class="hidden peer">
                                                        <label for="paypal" class="inline-flex items-center space-x-3 w-full p-4 text-gray-800 bg-white border-2 border-gray-100 rounded cursor-pointer peer-checked:border-blue-500 peer-checked:border-2 peer-checked:text-blue-500 hover:border-2 hover:border-blue-500">
                                                            <img class="h-12" src="{{ asset('assets/paypal.png') }}" alt="">
                                                        </label>
                                                    </div>
                                                    <div>
                                                        <div class="flex items-center justify-between p-3">
                                                            <input type="radio" id="credit_card" name="payment_method" value="credit_card" class="hidden peer" {{ old('payment_method') === 'credit_card' ? 'checked' : '' }}>
                                                            <label for="credit_card" class="inline-flex items-center space-x-3 w-full p-4 text-gray-800 bg-white border-2 border-gray-100 rounded cursor-pointer peer-checked:border-blue-500 peer-checked:border-2 peer-checked:text-blue-500 hover:border-2 hover:border-blue-500">
                                                                <span class="font-medium text-xl">
                                                                    Pay with credit card
                                                                </span>
                                                            </label>
                                                        </div>
                                                        <div class="cards mt-2 pb-2 {{ old('payment_method') === 'credit_card' ? '' : 'hidden' }}">
                                                            @foreach ($cards as $card)
                                                                @if ($card->paymentMethod)
                                                                    <div class="flex items-start justify-between p-3">
                                                                        <label for="card_id" class="font-normal text-gray-900 flex items-start space-x-1">
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
                                                                         $checked = "";
                                                                        if(old('card_id') == $card->id){
                                                                            $checked = "checked";
                                                                            
                                                                        }else if($card->primary_card == true){
                                                                            $checked = "checked";
                                                                            $cardSelected = true;
                                                                        } 
                                                                        @endphp
                                                                        <input type="radio" id="card_id" name="card_id" value="{{ $card->id }}"
                                                                            {{ $checked}} class="w-4 h-4 mt-2 ml-4 text-blue-600 cursor-pointer bg-white border-gray-500 rounded focus:ring-blue-500  focus:ring-2">
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
                                                    <br>
                                                    <div id="paymentSectionGPay" class="hidden">
                                                        <div id="payment-request-button"></div>
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
    const stripe = Stripe('{{ env('STRIPE_KEY') }}'); // Your public key from Stripe

        const paymentRequest = stripe.paymentRequest({
        country: 'US',
        currency: 'usd',
        total: {
            label: 'Total',
            amount: 100,
        },
        requestPayerName: true,
        requestPayerEmail: true,
        paymentMethodTypes: ['card'],
        });

        // Check if the device/browser supports Apple Pay or Google Pay
        paymentRequest.canMakePayment().then(function(result){
        console.log(result); // Log the result to understand what's being returned

        if (result && result.googlePay) {
            // Google Pay is available, enable the button
            const elements = stripe.elements();
            const prButton = elements.create('paymentRequestButton', {
            paymentRequest: paymentRequest,
            });

            
            prButton.mount('#payment-request-button');

            //validateBookingAndShowGPay();

        } else if (result && result.applePay) {
            // Apple Pay is available (on Safari for Apple devices), enable the button
            const elements = stripe.elements();
            const prButton = elements.create('paymentRequestButton', {
            paymentRequest: paymentRequest,
            });

            prButton.mount('#payment-request-button');
        } else {
            // If neither is available, log a message
            console.log("Neither Apple Pay nor Google Pay is available on this device.");
        }
        }).catch(function(error) {
        // Handle errors
        console.error('Error checking payment method availability:', error);
        });


    paymentRequest.on('paymentmethod', async (ev) => {

        const amount = document.querySelector('[name="dr_amount"]').value;
        
  const response = await fetch('/create-payment-intent', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
    },
    body: JSON.stringify({ payment_method: ev.paymentMethod.id, amount: amount}),
  });

  const { clientSecret } = await response.json();

  // Confirm the payment
  const { error, paymentIntent } = await stripe.confirmCardPayment(clientSecret, {
    payment_method: ev.paymentMethod.id,
  });

  if (error) {
    ev.complete('fail');
    console.error(error.message);
  } else {
    ev.complete('success');

    
    document.querySelector('[name="gPayApplePayId"]').value = paymentIntent.id;
    document.querySelector('[name="payment_method"][value="credit_card"]').checked = true;
    

    console.log('Transaction ID:', paymentIntent.id); // <--- HERE
    console.log('Status:', paymentIntent.status);

    document.getElementById('submitForm').submit();
    // Handle post-payment success (e.g., show a confirmation page)
    console.log('Payment Successful!');
  }
});


    $('#dr_amount').change(function () {
        var amount = $('#dr_amount').val();
        if(parseInt(amount) > 0){
            $('#paymentSectionGPay').removeClass('hidden');
        }else{
            $('#paymentSectionGPay').addClass('hidden');
        }
        paymentRequest.update({
            total: {
                label: 'Total',
                amount: Math.round(amount * 100)
            },
        });
    });

</script>

@endsection
