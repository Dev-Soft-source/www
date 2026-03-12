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
                    <form id="submitForm" method="POST" action="{{ route('store_top_up_balance') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="gPayApplePayId" value="0">
                        <h1>{{ $paymentSettingDetail->top_up_my_balance_head ?? 'Top up my balance' }}</h1>
                        <div class="grid grid-cols-1 lg:grid-cols-1 gap-4">
                            <div class="col-span-1">
                                <div class="space-y-4">
                                    <div class="bg-white rounded-lg overflow-hidden shadow-3xl">
                                        <div class="bg-white p-4">
                                            @if (session('message'))
                                                <div class="mt-4 mb-4 rounded-lg px-6 py-3 bg-red-100 text-gray-600"
                                                    role="alert">
                                                    {{ session('message') }}
                                                </div>
                                            @endif

                                            <div class="space-y-4 mb-4">
                                                <div class="w-full md:w-1/2">
                                                    <label for="seats"
                                                        class="block mb-2 font-medium text-gray-900">{{ $paymentSettingDetail->purchase_amount_label ?? 'Purchase amount' }}
                                                        <span class="text-red-500">*</span></label>
                                                    <input type="number" id="dr_amount" step="any" name="dr_amount"
                                                        value="{{ old('dr_amount') }}"
                                                        placeholder="{{ $paymentSettingDetail->purchase_amount_placeholder ?? 'Enter the amount you want to add' }}"
                                                        class="block mt-1 border p-1.5 w-full rounded text-base md:text-lg border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600">
                                                    @error('dr_amount')
                                                        <div class="tooltip-error shadow-lg">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="space-y-4 mt-8">
                                                <div class="w-full md:w-1/2">
                                                    <x-payment-list :cards="$cards" :paymentSettingDetail="$paymentSettingDetail" />
                                                </div>
                                            </div>
                                            <div class="flex justify-center items-center mt-4 md:w-1/2 w-full">
                                                <button id="submitButton" class="bg-greenXS hover:bg-greenXS text-white text-sm rounded font-FuturaMdCnBT hover:font-FuturaMdCnBT px-5 py-3 hover:text-white text-center focus:bg-greenXS focus:text-white active:text-white active:bg-greenXS" type="submit">
                                                    {{ $paymentSettingDetail->buy_btn_text ?? 'Buy' }}
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
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://js.stripe.com/v3/"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Show/hide inline credit-card section like on coffee wall page:
            // when "Credit or Debit Card" (id="credit_card") is selected, show #credit-card-div,
            // otherwise hide it (when a saved card / PayPal / Google Pay / Apple Pay is selected).
            function toggleInlineCardSection() {
                var selected = document.querySelector('input[type="radio"][name="card_id"]:checked');
                var isCreditCardSelected = selected && selected.id === 'credit_card';
                var creditCardDiv = document.getElementById('credit-card-div');

                if (!creditCardDiv) {
                    return;
                }

                if (isCreditCardSelected) {
                    creditCardDiv.classList.remove('hidden');
                    // Initialize Stripe card element when the section is first shown
                    if (window.buyBalanceEnsureCardElement) {
                        window.buyBalanceEnsureCardElement();
                    }
                } else {
                    creditCardDiv.classList.add('hidden');
                }
            }

            var cardRadios = document.querySelectorAll('input[type="radio"][name="card_id"]');
            cardRadios.forEach(function(radio) {
                radio.addEventListener('change', toggleInlineCardSection);
            });
            // Run once on page load in case "Credit or Debit Card" is preselected
            toggleInlineCardSection();

            // Hide Apple Pay option on non-Apple devices
            var ua = navigator.userAgent || '';
            var isAppleDevice = /iPad|iPhone|iPod|Macintosh/.test(ua) && !window.MSStream;
            if (!isAppleDevice) {
                var appleContainers = document.querySelectorAll('.apple-pay-option');
                appleContainers.forEach(function(el) {
                    el.classList.add('hidden');
                });
                // If Apple Pay was pre-selected, clear selection
                var appleRadio = document.getElementById('apple_pay');
                if (appleRadio && appleRadio.checked) {
                    appleRadio.checked = false;
                }
            }

            // Hide Google Pay option when browser does not appear to support it
            // Heuristic: Android + Chrome + PaymentRequest API present
            var isAndroid = /Android/.test(ua);
            var isChrome = /Chrome/.test(ua) || /CriOS/.test(ua);
            var hasPaymentRequest = typeof window.PaymentRequest !== 'undefined';
            var isGooglePayCapable = isAndroid && isChrome && hasPaymentRequest;

            if (!isGooglePayCapable) {
                var googleContainers = document.querySelectorAll('.google-pay-option');
                googleContainers.forEach(function(el) {
                    el.classList.add('hidden');
                });
                // If Google Pay was pre-selected, clear selection
                var gpayRadio = document.getElementById('google_pay');
                if (gpayRadio && gpayRadio.checked) {
                    gpayRadio.checked = false;
                }
            }
        });

        var stripePk = '{{ env('STRIPE_KEY') ?? '' }}';
        if (stripePk && stripePk.length >= 10) {
            const stripe = Stripe(stripePk);

            // Inline card element (mirrors coffee_wall behavior) for the "Credit or Debit Card" option
            let inlineCardElement = null;
            window.buyBalanceEnsureCardElement = function() {
                if (inlineCardElement) {
                    return;
                }
                const elements = stripe.elements();
                inlineCardElement = elements.create('card', {
                    style: {
                        base: {
                            fontStyle: 'italic',
                        },
                    },
                });
                inlineCardElement.mount('#card-element');
            };
        }


        // Hide field tooltip error when user clicks/focuses inside its parent container.
        const form = document.getElementById('submitForm');

        function hideTooltipInParent(eventTarget) {
            if (!(eventTarget instanceof HTMLElement) || !form) return;
            let node = eventTarget.closest('div, section, label');

            // Walk up until form root and remove tooltips that belong to the current field
            while (node && node !== form) {
                // Check for tooltip as a direct child
                const tooltipInChildren = Array.from(node.children).find((child) =>
                    child instanceof HTMLElement && child.classList.contains('tooltip-error')
                );
                if (tooltipInChildren) {
                    tooltipInChildren.remove();
                    return;
                }

                // Check for tooltip as a sibling (for cases like terms checkbox where error is sibling of label)
                if (node.parentElement) {
                    const tooltipSibling = Array.from(node.parentElement.children).find((sibling) =>
                        sibling instanceof HTMLElement &&
                        sibling.classList.contains('tooltip-error') &&
                        sibling !== node
                    );
                    if (tooltipSibling) {
                        tooltipSibling.remove();
                        return;
                    }
                }
                node = node.parentElement?.closest('div, section') || null;
            }
        }

        if (form) {
            form.addEventListener('click', function(event) {
                hideTooltipInParent(event.target);
            });
            form.addEventListener('focusin', function(event) {
                hideTooltipInParent(event.target);
            });
        }
    </script>
@endsection
