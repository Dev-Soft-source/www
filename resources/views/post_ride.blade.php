@extends('layouts.template')

@section('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        .features_tooltiptext::after {
            content: "";
            border-width: 10px;
            border-style: solid;
            border-color: #ef4444 transparent transparent transparent;
            position: absolute;
            bottom: -20px;
            /* left: 4rem; */
        }

        .luggage_tooltiptext::after {
            content: "";
            border-width: 10px;
            border-style: solid;
            border-color: #ef4444 transparent transparent transparent;
            position: absolute;
            bottom: -20px;
            /* left: 4rem; */
        }

        .payment_tooltiptext::after {
            content: "";
            border-width: 10px;
            border-style: solid;
            border-color: #ef4444 transparent transparent transparent;
            position: absolute;
            bottom: -20px;
            /* left: 4rem; */
        }

        /* Extra small devices */
        @media only screen and (max-width: 375px) {
            .tooltip_width {
                width: 16.5rem;
            }

            .tooltip_position {
                right: 13rem;
                top: -7.5rem;
            }

            .luggage_tooltiptext::after {
                right: 3.3rem;
            }

            .payment_tooltiptext_position {
                top: -6.3rem;
            }
        }

        @media only screen and (min-width:376px) and (max-width: 639px) {
            .tooltip_width {
                width: 20rem;
            }

            .tooltip_position {
                right: 16.5rem;
                top: -6.5rem;
            }

            .luggage_tooltiptext::after {
                right: 3.3rem;
            }
        }

        @media only screen and (max-width: 767px) {
            .features_tooltiptext::after {
                content: "";
                border-width: 10px;
                border-style: solid;
                border-color: transparent transparent #ef4444 transparent;
                position: absolute;
                top: -20px;
                bottom: auto;
                left: 5.8rem;
            }
        }

        /* Stops Along the Way – collapsible header and smooth slide panel (same as edit_ride) */
        .add-more-spots-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            text-align: left;
            cursor: pointer;
            border: none;
            transition: opacity 0.2s ease, background-color 0.2s ease;
        }

        .add-more-spots-header:hover {
            opacity: 0.95;
        }

        .add-more-spots-chevron {
            flex-shrink: 0;
            width: 1.5rem;
            height: 1.5rem;
            margin-left: 0.5rem;
            transition: transform 0.35s ease-out;
        }

        .add-more-spots-header[aria-expanded="false"] .add-more-spots-chevron {
            transform: rotate(-90deg);
        }

        .add-more-spots-panel {
            overflow: hidden;
            transition: height 0.35s ease-out;
        }
    </style>
@endsection

@section('content')

    {{-- Early function definitions to prevent "not defined" errors on page load --}}
    <script>
        // Placeholder functions that will be properly initialized after jQuery loads
        function fromInput(index) {
            // Will be overwritten when full script loads
            if (typeof $ !== 'undefined' && typeof debounce !== 'undefined') {
                debounce(function() {
                    let searchTerm = $('#from_spot_' + index).val();
                    if (searchTerm.length >= 2) {
                        let searchData = $('#to_spot_' + index).val();
                        if (typeof fetchCities !== 'undefined') {
                            fetchCities(searchTerm, searchData, 'from_spot', index);
                        }
                    }
                }, 500)();
            }
        }

        function toInput(index) {
            // Will be overwritten when full script loads
            if (typeof $ !== 'undefined' && typeof debounce !== 'undefined') {
                debounce(function() {
                    let searchTerm = $('#to_spot_' + index).val();
                    if (searchTerm.length >= 2) {
                        let searchData = $('#from_spot_' + index).val();
                        if (typeof fetchCities !== 'undefined') {
                            fetchCities(searchTerm, searchData, 'to_spot', index);
                        }
                    }
                }, 500)();
            }
        }
    

        document.addEventListener('DOMContentLoaded', function() {
            var fromEl = document.getElementById('from_spot_0');
            var toEl = document.getElementById('to_spot_0');
            var fromError = document.getElementById('from-server-error');
            var toError = document.getElementById('to-server-error');
            function hideFromError() { if (fromError) fromError.classList.add('hidden'); }
            function hideToError() { if (toError) toError.classList.add('hidden'); }
            if (fromError) fromEl.addEventListener('input', hideFromError);
            if (toError) toEl.addEventListener('input', hideToError);
        });
    </script>

    <div class="container px-4 mx-auto my-14 page-post_a_ride">
        @if (session('error'))
            <div id="myModal" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div onclick="closeModal()" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                    <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                        <div
                            class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg w-full modal-border">
                            <button type="button" onclick="closeModal()"
                                class="absolute top-4 right-4 text-gray-400 hover:text-gray-500 z-50">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                            <div class="bg-white px-4 mt-10 sm:mt-1 pb-4 pt-16 sm:p-6 sm:pb-4 sm:pt-16">
                                <div class="sm:flex sm:items-start justify-center">
                                    <!-- <div
                                        class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full sm:mx-0 sm:h-10 sm:w-10 bg-red-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-lg text-white w-8 h-8" viewBox="0 0 16 16">
                                            <path d="M7.005 3.1a1 1 0 1 1 1.99 0l-.388 6.35a.61.61 0 0 1-1.214 0zM7 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0"/>
                                        </svg>
                                    </div> -->
                                </div>
                                <div class="text-center sm:mt-0 sm:text-left">
                                    <div class="">
                                        <h3 class="text-3xl text-center font-FuturaMdCnBT text-gray-900 mb-4"
                                            id="modal-title">{!! session('heading') !!}</h3>
                                    </div>
                                    <div class="mt-2 w-full">
                                        <p class="can-exp-p text-center">{!! session('error') !!}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="px-4 pb-6 pt-4  sm:flex sm:flex-row-reverse sm:px-6 justify-center">
                                <button type="button" onclick="closeModal()"
                                    class="inline-flex justify-center rounded bg-red-500 px-3 py-2 font-FuturaMdCnBT text-lg text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-red-400 sm:ml-3 w-auto">
                                    Close
                                </button>
                                {{-- <a href=""
                                class="inline-flex w-full justinline-flex justify-center rounded bg-red-500 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-red-400 sm:ml-3 sm:w-24">Close</a> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        @if (session('price_warning'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const warning = @json(session('price_warning'));
                    if (warning) {
                        showPriceWarningModal(function() {
                            // User clicked "Keep Current Price" - submit the form
                            console.log('User chose to keep current price, submitting form');
                            const formElement = document.getElementById('post-ride-form') || document.querySelector(
                                'form');
                            if (formElement) {
                                // Create a hidden input to bypass validation on next submit
                                const bypassInput = document.createElement('input');
                                bypassInput.type = 'hidden';
                                bypassInput.name = 'bypass_price_validation';
                                bypassInput.value = '1';
                                formElement.appendChild(bypassInput);
                                // Remove the event listener to prevent re-validation
                                const newForm = formElement.cloneNode(true);
                                formElement.parentNode.replaceChild(newForm, formElement);
                                // Submit the form
                                newForm.submit();
                            }
                        });
                    }
                });
            </script>
        @endif
        @if (session('message'))
            <div id="myModal" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div onclick="closeModal()" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                    <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                        <div
                            class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
                            <button type="button" onclick="closeModal()"
                                class="absolute top-4 right-4 text-gray-400 hover:text-gray-500 z-50">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                            <div class="bg-white px-4 mt-10 sm:mt-1 pb-4 pt-16 sm:p-6 sm:pb-4 sm:pt-16">
                                <div class="sm:flex sm:items-start justify-center">
                                    <!-- <div
                                        class="mx-auto h-16 w-16 flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="4" stroke="currentColor" class="w-12 h-12 text-greenXS">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                        </svg>
                                    </div> -->
                                </div>
                                <div class="text-center sm:mt-0 sm:text-left">
                                    <div class="">
                                        <h3 class="text-3xl text-center font-FuturaMdCnBT text-gray-900 mb-4"
                                            id="modal-title">{!! session('heading') !!}</h3>
                                    </div>
                                    <div class="mt-2 w-full">
                                        <p class="can-exp-p text-center">{!! session('message') !!}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="px-4 pb-6 pt-4 flex items-center space-x-2 sm:space-x-4 sm:px-6 justify-center">
                                @if (session('id'))
                                    <a href="{{ route('repost_ride', ['lang' => $selectedLanguage->abbreviation, 'id' => session('id')]) }}"
                                        class="inline-flex w-full justify-center rounded bg-greenXS px-3 py-2 font-FuturaMdCnBT text-lg text-white whitespace-nowrap hover:text-white hover:shadow-lg shadow-sm hover:bg-greenXS sm:ml-3 sm:w-fit">
                                        @isset($postRidePage->post_return_ride_label)
                                            {{ $postRidePage->post_return_ride_label }}
                                        @endisset
                                    </a>
                                @endif
                                <a href="" class="button-exp-fill">Close</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <div id="myModal" class="hidden relative z-50" id="Extra+-ride-modal" aria-labelledby="modal-title" role="dialog"
            aria-modal="true">
            <div onclick="closeModal()" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                    <div
                        class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
                        <button type="button" onclick="closeExtraCareRideModal()"
                            class="absolute top-3 right-3 text-gray-400 hover:text-gray-500">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
                            <div class="text-center sm:mt-0 sm:text-left">
                                <div class="">
                                    <h3 class="text-3xl text-center font-FuturaMdCnBT text-gray-900 mb-4"
                                        id="modal-title">{!! session('heading') !!}</h3>
                                </div>
                                <div class="mt-2 w-full">
                                    {{-- <p class="can-exp-p text-center">{!! session('message') !!}</p> --}}
                                    <p class="can-exp-p text-center">
                                        {{ $postRideSubDetailPage->extra_care_popup_eligible_text ?? '' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="px-4 pb-6 pt-4 flex items-center space-x-2 sm:space-x-4 sm:px-6 justify-center">
                            @if (session('id'))
                                <a href="{{ route('repost_ride', ['lang' => $selectedLanguage->abbreviation, 'id' => session('id')]) }}"
                                    class="inline-flex w-full justify-center rounded bg-greenXS px-3 py-2 font-FuturaMdCnBT text-lg text-white whitespace-nowrap hover:text-white hover:shadow-lg shadow-sm hover:bg-greenXS sm:ml-3 sm:w-fit">Repost
                                    ride</a>
                            @endif
                            <button type="button" onclick="closeExtraCareRideModal()"
                                class="button-exp-fill">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal: Address required for Pink or Extra+ Ride --}}
        <div id="pink-extra-address-required-modal" class="hidden fixed inset-0 z-50"
            aria-labelledby="pink-extra-address-modal-title" role="dialog" aria-modal="true">
            <div onclick="closePinkExtraAddressRequiredModal()"
                class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                    <div
                        class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
                        <button type="button" onclick="closePinkExtraAddressRequiredModal()"
                            class="absolute top-3 right-3 text-gray-400 hover:text-gray-500">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <h3 class="font-FuturaMdCnBT text-gray-800 mb-4" id="pink-extra-address-modal-title">Action
                                Required</h3>
                            <p class="text-gray-800 text-base text-left">To post a Pink or Extra+ Ride, we need your
                                residential address on file. Please add it in <a
                                    href="{{ route('profile.edit', ['lang' => optional($selectedLanguage)->abbreviation ?? 'en']) }}"
                                    class="underline font-medium hover:no-underline text-blue-600">Dashboard → Edit
                                    Profile</a>.</p>
                            <div class="mt-6 flex flex-wrap gap-2 justify-center">
                                <button type="button" onclick="closePinkExtraAddressRequiredModal()"
                                    class="button-exp-fill font-FuturaMdCnBT">Close</button>
                                <a href="{{ route('profile.edit', ['lang' => optional($selectedLanguage)->abbreviation ?? 'en']) }}"
                                    class="button-exp-fill bg-primary hover:bg-primary font-FuturaMdCnBT no-underline">Edit
                                    Profile</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal for 5+ seats warning -->
        <div id="seatsWarningModal" class="hidden fixed inset-0 z-50" aria-labelledby="seats-modal-title" role="dialog"
            aria-modal="true">
            <div onclick="closeSeatsWarningModal()" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity">
            </div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                    <div
                        class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
                        <button type="button" onclick="closeSeatsWarningModal()"
                            class="absolute top-4 right-4 text-gray-400 hover:text-gray-500 z-50">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                        <div class="bg-white px-4 mt-10 sm:mt-1 pb-4 pt-16 sm:p-6 sm:pb-4 sm:pt-16">
                            <div class="text-center sm:mt-0 sm:text-left">
                                <div class="">
                                    <h3 class="text-3xl text-center font-FuturaMdCnBT text-gray-900 mb-4"
                                        id="seats-modal-title">Heads up for 5+ seats</h3>
                                </div>
                                <div class="mt-2 w-full">
                                    <p class="can-exp-p text-center">Please note that for large vehicles, your total trip
                                        collection must stay within non-commercial limits. To keep this a standard carpool,
                                        we suggest a lower price per seat. By law, total contributions cannot exceed the
                                        standard reimbursement limit ($0.72/km).</p>
                                </div>
                            </div>
                        </div>
                        <div class="px-4 pb-6 pt-4 flex items-center space-x-2  justify-center">
                            <button type="button" onclick="closeSeatsWarningModal()" class="button-exp-fill">Got
                                it</button>
                            <button type="button"
                                onclick="window.location.href='{{ route('cost_sharing_compliance_policy', ['lang' => optional($selectedLanguage)->abbreviation ?? 'en']) }}'"
                                class="button-exp-no-fill inline-block text-center">Learn more about limits</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal for Price Error (Exceeds $0.72/km per seat) -->
        <div id="priceErrorModal" class="hidden fixed inset-0 z-50" aria-labelledby="price-error-modal-title"
            role="dialog" aria-modal="true">
            <div onclick="closePriceErrorModal()" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity">
            </div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                    <div
                        class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
                        <button type="button" onclick="closePriceErrorModal()"
                            class="absolute top-4 right-4 text-gray-400 hover:text-gray-500 z-50">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                        <div class="bg-white px-4 mt-10 sm:mt-1 pb-4 pt-16 sm:p-6 sm:pb-4 sm:pt-16">
                            <div class="text-center sm:mt-0 sm:text-left">
                                <div class="">
                                    <h3 class="text-3xl text-center font-FuturaMdCnBT text-gray-900 mb-4"
                                        id="priceErrorHeading">Price Limit Exceeded</h3>
                                </div>
                                <div class="mt-2 w-full">
                                    <p class="can-exp-p text-center mb-3" id="priceErrorParagraph1"></p>
                                    <p class="can-exp-p text-center mb-3" id="priceErrorParagraph2"></p>
                                    <p class="can-exp-p text-center" id="priceErrorParagraph3"></p>
                                </div>
                            </div>
                        </div>
                        <div class="px-4 pb-6 pt-4 flex items-center space-x-2 sm:space-x-4 sm:px-6 justify-center">
                            <button type="button" id="priceErrorAdjustBtn" onclick="adjustPriceFromError()"
                                class="button-exp-fill">Adjust Price</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal for Price Warning (Exceeds $0.66/km per seat but <= $0.72/km per seat) -->
        <div id="priceWarningModal" class="hidden fixed inset-0 z-50" aria-labelledby="price-warning-modal-title"
            role="dialog" aria-modal="true">
            <div onclick="closePriceWarningModal()" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity">
            </div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                    <div
                        class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
                        <button type="button" onclick="closePriceWarningModal()"
                            class="absolute top-4 right-4 text-gray-400 hover:text-gray-500 z-50">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                        <div class="bg-white px-4 mt-10 sm:mt-1 pb-4 pt-16 sm:p-6 sm:pb-4 sm:pt-16">
                            <div class="text-center sm:mt-0 sm:text-left">
                                <div class="">
                                    <h3 class="text-3xl text-center font-FuturaMdCnBT text-gray-900 mb-4">Recommended
                                        Contribution Limit</h3>
                                </div>
                                <div class="mt-2 w-full">
                                    <p class="can-exp-p text-center mb-3" id="priceWarningParagraph1"></p>
                                    <p class="can-exp-p text-center" id="priceWarningParagraph2"></p>
                                </div>
                            </div>
                        </div>
                        <div class="px-4 pb-6 pt-4 flex items-center space-x-2 sm:space-x-4 sm:px-6 justify-center">
                            <button type="button" id="priceWarningAdjustBtn"
                                onclick="adjustPriceFromWarning(); return false;" class="button-exp-fill">Adjust
                                Price</button>
                            <button type="button" id="priceWarningContinue" class="button-exp-fill">Keep Current
                                Price</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end md:items-center">
            <a href="{{ $isNewForm && ($routeType ?? '') === 'create' ? route('post_ride_again_completed', ['lang' => optional($selectedLanguage)->abbreviation]) : route('post_ride_again', ['lang' => optional($selectedLanguage)->abbreviation]) }}"
                class="bg-greenXS hover:bg-greenXS text-white text-base md:text-lg rounded font-FuturaMdCnBT hover:font-FuturaMdCnBT px-5 py-2 border border-greenXS hover:border-greenXS hover:text-white text-center focus:bg-greenXS focus:text-white active:text-white active:bg-greenXS">
                @isset($postRidePage->post_arrived_again_label)
                    {{ $postRidePage->post_arrived_again_label }}
                @endisset
            </a>
        </div>
        <div class="flex flex-col sm:flex-col md:flex-row lg:flex-row justify-between md:items-center">
            <h1>
                @isset($postRidePage->main_heading)
                    {{ $postRidePage->main_heading }}
                @endisset
            </h1>
            <p>
                <span class="text-red-500">*
                    {{ $postRideSubDetailPage->feilds_required_text ?? 'Indicates required fields' }} </span>
            </p>
        </div>
        <form method="POST" action="{{ route('post_ride.store') }}" enctype="multipart/form-data" id="post-ride-form">
            @csrf
            <div class="">
                <div class="bg-white rounded-lg overflow-hidden shadow-3xl">
                    <h3 class="text-2xl bg-primary text-white py-2 px-4">
                        @isset($postRidePage->ride_info_heading)
                            {{ $postRidePage->ride_info_heading }}
                        @endisset
                    </h3>
                    <div class="bg-white p-4 space-y-3">
                        <div class="flex flex-col md:flex-row justify-between items-start">
                            <div class="w-full md:w-[45%] mb-4 relative">
                                <div>
                                    <label for="from" class="block mb-2 text-gray-900">
                                        @isset($postRidePage->from_label)
                                            {{ $postRidePage->from_label }}
                                        @endisset
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative mt-2">
                                        <div class="absolute inset-y-0 start-0 flex items-center pl-2 pointer-events-none">
                                            <img src="{{ asset('assets/search-bar-from.png') }}" class="w-auto h-6"
                                                alt="">
                                        </div>

                                        @php
                                            // Controller already swaps departure/destination for repost, so just use the ride properties as-is
                                            $departure =
                                                isset($ride->defaultRideDetail) && isset($ride->defaultRideDetail[0])
                                                    ? $ride->defaultRideDetail[0]->departure
                                                    : '';
                                            $destination =
                                                isset($ride->defaultRideDetail) && isset($ride->defaultRideDetail[0])
                                                    ? $ride->defaultRideDetail[0]->destination
                                                    : '';
                                        @endphp

                                        <input type="text" id="from_spot_0" name="from"
                                            value="{{ old('from', $departure) }}" autocomplete="off"
                                            class="bg-gray-100 border border-gray-200 pl-7 text-gray-900 text-base lg:text-lg rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 block w-full p-2.5 mt-2"
                                            @isset($postRidePage->from_placeholder)
                                                placeholder="{{ $postRidePage->from_placeholder }}"
                                            @endisset>
                                        <div class="absolute hidden mt-1 z-10 left-0 top-full" id="fromInputError">
                                            <div
                                                class="tooltip-error shadow-lg rounded p-2 bg-red-500 text-white text-sm lg:text-base">
                                            </div>
                                        </div>
                                    </div>
                                    @error('from')
                                    <div class="absolute mt-1 z-10" id="from-server-error">
                                        <div class="tooltip-error shadow-lg rounded p-2 bg-red-500 text-white text-sm lg:text-base">{{ $message }} </div>
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="w-full md:w-[10%] md:mt-10 flex justify-center items-start">
                                <button type="button" onclick="swapLocations()">
                                    <img src="{{ asset('assets/arrow.png') }}" class="w-10 h-10 mx-auto" alt="">
                                </button>
                            </div>
                            <div class="w-full md:w-[45%] mb-4 relative">
                                <div>
                                    <label for="to" class="block mb-2 text-gray-900">
                                        @isset($postRidePage->to_label)
                                            {{ $postRidePage->to_label }}
                                        @endisset
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative mt-2">
                                        <div class="absolute inset-y-0 start-0 flex items-center pl-2 pointer-events-none">
                                            <img src="{{ asset('images/new-21-search-bar-to.png') }}" class="w-auto h-6"
                                                alt="">
                                        </div>
                                        <input type="text" id="to_spot_0" name="to"
                                            value="{{ old('to', $destination) }}" autocomplete="off"
                                            class="bg-gray-100 border pl-7 border-gray-200 text-base lg:text-lg text-gray-900  rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 mt-2 block w-full p-2.5"
                                            @isset($postRidePage->to_placeholder)
                                                placeholder="{{ $postRidePage->to_placeholder }}"
                                            @endisset>
                                        <div class="absolute hidden mt-1 z-10 left-0 top-full" id="toInputError">
                                            <div
                                                class="tooltip-error shadow-lg rounded p-2 bg-red-500 text-white text-sm lg:text-base">
                                            </div>
                                        </div>
                                    </div>
                                    @error('to')
                                    <div class="absolute mt-1 z-10" id="to-server-error">
                                        <div class="tooltip-error shadow-lg rounded p-2 bg-red-500 text-white text-sm lg:text-base">{{ $message }} </div>
                                    </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Delete Stop confirmation modal --}}
                        <div id="delete-stop-modal-post" class="relative z-50 hidden"
                            aria-labelledby="delete-stop-modal-title-post" role="dialog" aria-modal="true">
                            <div id="delete-stop-modal-backdrop-post" onclick="closeDeleteStopModalPostRide()"
                                class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                                <div
                                    class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                                    <div
                                        class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
                                        <button type="button" onclick="closeDeleteStopModalPostRide()"
                                            class="absolute top-3 right-3 text-gray-400 hover:text-gray-500">
                                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                                            <div class="text-center sm:mt-0 sm:text-left">
                                                <div class="mt-2 w-full">
                                                    <p id="delete-stop-modal-title-post"
                                                        class="can-exp-p text-center text-xl">Delete Stop?</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div
                                            class="px-4 pb-6 pt-4 flex items-center space-x-2 sm:space-x-4 sm:px-6 justify-center">
                                            <button type="button" id="delete-stop-no-post"
                                                class="w-24 bg-blue-600 p-2 rounded-md text-white hover:bg-blue-700">No</button>
                                            <button type="button" id="delete-stop-yes-post"
                                                class="w-24 bg-red-600 p-2 rounded-md text-white hover:bg-red-700">Yes</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-end flex-col md:flex-row justify-between mt-4">
                            <div class="w-full md:w-[45%] mb-4">
                                <label for="pickup_location" class="block mb-2 text-gray-900">
                                    @isset($postRidePage->pick_up_label)
                                        {{ $postRidePage->pick_up_label }}
                                    @endisset
                                    <span class="text-red-500">*</span>
                                </label>
                                <textarea id="pickup_location" rows="5" name="pickup"
                                    class="block p-2.5 w-full text-gray-900 bg-gray-100 rounded border border-gray-200 text-base lg:text-lg focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 mt-2"
                                    @isset($postRidePage->pick_up_placeholder)
                                    placeholder="{{ $postRidePage->pick_up_placeholder }}"
                                @endisset>{{ old('pickup', optional($ride)->pickup) }}</textarea>
                                @error('pickup')
                                <div class="absolute mt-1 z-10">
                                    <div class="tooltip-error shadow-lg rounded p-2 bg-red-500 text-white text-sm lg:text-base">{{ $message }} </div>
                                </div>
                                @enderror
                            </div>
                            <div class="w-full md:w-[45%] mb-4">
                                <label for="dropoff_location"class="block mb-2 text-gray-900">
                                    @isset($postRidePage->drop_off_label)
                                        {{ $postRidePage->drop_off_label }}
                                    @endisset
                                    <span class="text-red-500">*</span>
                                </label>
                                <textarea id="dropoff_location" rows="5" name="dropoff"
                                    class="block p-2.5 w-full text-gray-900 bg-gray-100 rounded border border-gray-200 text-base lg:text-lg focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 mt-2"
                                    @isset($postRidePage->drop_off_placeholder)
                                    placeholder="{{ $postRidePage->drop_off_placeholder }}"
                                @endisset>{{ old('dropoff', optional($ride)->dropoff) }}</textarea>
                                @error('dropoff')
                                <div class="absolute mt-1 z-10">
                                    <div class="tooltip-error shadow-lg rounded p-2 bg-red-500 text-white text-sm lg:text-base">{{ $message }} </div>
                                </div>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="date_time" class="block text-gray-900">
                                @isset($postRidePage->date_time_label)
                                    {{ $postRidePage->date_time_label }}
                                @endisset
                                <span class="text-red-500">*</span>
                            </label>
                            <div
                                class="flex flex-col sm:flex-col md:flex-row lg:flow-row items-start mb-4 justify-between">
                                <div class="w-full md:w-[45%] mb-4">
                                    <div class="relative mt-2">
                                        <div class="absolute inset-y-0 start-0 flex items-center pl-2 pointer-events-none">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                fill="currentColor" class="bi bi-calendar-event" viewBox="0 0 16 16">
                                                <path
                                                    d="M11 6.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1z" />
                                                <path
                                                    d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z" />
                                            </svg>
                                        </div>
                                        @php
                                            $dateValue = old('date');
                                            if (
                                                empty($dateValue) &&
                                                ($routeType ?? '') === 'copy' &&
                                                isset($ride) &&
                                                $ride
                                            ) {
                                                $copyDate =
                                                    $ride->date ??
                                                    (isset($ride->defaultRideDetail[0])
                                                        ? $ride->defaultRideDetail[0]->date
                                                        : null);
                                                if ($copyDate) {
                                                    $dateValue = \Carbon\Carbon::parse($copyDate)->format('F d, Y');
                                                }
                                            }
                                        @endphp
                                        <input type="text" id="dateInput" name="date" value="2026-03-16"
                                            class="bg-gray-100 border pl-7 border-gray-200 text-base lg:text-lg text-gray-900  rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 mt-2 block w-full p-2.5"
                                            placeholder="">
                                    </div>
                                    @error('date')
                                        <div class="absolute mt-1 z-10">
                                            <div
                                                class="tooltip-error shadow-lg rounded p-2 bg-red-500 text-white text-sm lg:text-base">
                                                {{ $message }}</div>
                                        </div>
                                    @enderror
                                </div>
                                <div class="w-full md:w-[10%] md:mt-4 text-center">
                                    <span class="text-center text-base lg:text-lg ">
                                        @isset($postRidePage->at_label)
                                            {{ $postRidePage->at_label }}
                                        @endisset
                                    </span>
                                </div>
                                <div class="w-full md:w-[45%] mb-4">
                                    <div class="relative mt-2">
                                        <div class="absolute inset-y-0 start-0 flex items-center pl-2 pointer-events-none">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        @php
                                            $timeValue = old('time');
                                            if (
                                                $timeValue === null &&
                                                ($routeType ?? '') !== '' &&
                                                in_array($routeType ?? '', ['repost', 'copy']) &&
                                                isset($ride) &&
                                                $ride &&
                                                !empty($ride->time)
                                            ) {
                                                $timeValue = \Carbon\Carbon::parse($ride->time)->format('H:i');
                                            }
                                            if (
                                                ($timeValue === null || $timeValue === '') &&
                                                ($routeType ?? '') !== '' &&
                                                in_array($routeType ?? '', ['repost', 'copy']) &&
                                                isset($ride) &&
                                                $ride &&
                                                !empty($ride->time)
                                            ) {
                                                $timeValue = \Carbon\Carbon::parse($ride->time)->format('H:i');
                                            }
                                        @endphp
                                        <input type="text" id="timeInput" name="time"
                                            value="{{ $timeValue ?? '' }}"
                                            class="bg-gray-100 border pl-10 border-gray-200 text-gray-900 text-base lg:text-lg rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 mt-2 block w-full p-2.5"
                                            placeholder="">
                                    </div>
                                    @error('time')
                                        <div class="absolute mt-1 z-10">
                                            <div
                                                class="tooltip-error shadow-lg rounded p-2 bg-red-500 text-white text-sm lg:text-base">
                                                {{ $message }}</div>
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Stops Along the Way (Optional) – repopulate from old() or ride data (for repost/copy) so segment price UI shows correctly --}}
                        @php
                            $stopsForDisplayPost = [];
                            $segmentPricesForPost = [];
                            $stopPickupDropoffForDisplayPost = [];
                            $routeTypeLocal = $routeType ?? '';
                            $chainSegmentsPost = [];
                            if (null !== old('stop_spot_display') && is_array(old('stop_spot_display'))) {
                                $stopsForDisplayPost = old('stop_spot_display');
                            } elseif (
                                null !== old('to_spot') &&
                                is_array(old('to_spot')) &&
                                count(old('to_spot')) > 0
                            ) {
                                $toSpotsPost = old('to_spot');
                                $nPost = count($toSpotsPost) - 1;
                                for ($i = 0; $i < $nPost; $i++) {
                                    $stopsForDisplayPost[] = $toSpotsPost[$i];
                                }
                            } elseif (
                                in_array($routeTypeLocal, ['repost', 'copy'], true) &&
                                isset($ride) &&
                                $ride &&
                                $ride->MoreRideDetail &&
                                $ride->MoreRideDetail->count() > 0
                            ) {
                                // Build ordered chain from origin to destination for repost/copy using only actual route segments
                                $defaultDetail = $ride->defaultRideDetail->first();
                                $originText =
                                    $defaultDetail && $defaultDetail->departure
                                        ? $defaultDetail->departure
                                        : $ride->rideDetail->first()->departure ?? '';
                                $destinationText =
                                    $defaultDetail && $defaultDetail->destination
                                        ? $defaultDetail->destination
                                        : $ride->rideDetail->last()->destination ?? '';

                                $details = $ride->MoreRideDetail->sortBy('id')->values();
                                $orderedPoints = collect([$originText]);
                                $current = $originText;
                                $remaining = $details;
                                $chainSegmentsPost = [];

                                while ($current !== $destinationText && $remaining->isNotEmpty()) {
                                    $nextSegment = $remaining->first(function ($d) use ($current) {
                                        return (string) $d->departure === (string) $current;
                                    });
                                    if (!$nextSegment) {
                                        break;
                                    }
                                    $chainSegmentsPost[] = $nextSegment;
                                    $orderedPoints->push($nextSegment->destination);
                                    $current = $nextSegment->destination;
                                    $remaining = $remaining->filter(function ($d) use ($nextSegment) {
                                        return $d->id != $nextSegment->id;
                                    });
                                }

                                // Intermediate stops (exclude origin and destination)
                                $chainStops =
                                    $orderedPoints->count() > 2
                                        ? $orderedPoints->slice(1, $orderedPoints->count() - 2)->values()
                                        : collect();

                                foreach ($chainStops as $stop) {
                                    $stopsForDisplayPost[] = $stop;
                                }

                                // Consecutive segment prices
                                foreach ($chainSegmentsPost as $seg) {
                                    $segmentPricesForPost[] = $seg->price ?? '';
                                }
                            }

                            // Build pickup/dropoff notes for each stop
                            if (null !== old('stop_pickup_dropoff') && is_array(old('stop_pickup_dropoff'))) {
                                $stopPickupDropoffForDisplayPost = old('stop_pickup_dropoff');
                            } elseif (
                                in_array($routeTypeLocal, ['repost', 'copy'], true) &&
                                !empty($chainSegmentsPost)
                            ) {
                                foreach ($chainSegmentsPost as $index => $seg) {
                                    if ($index >= count($stopsForDisplayPost)) {
                                        break;
                                    }
                                    $stopPickupDropoffForDisplayPost[] = $seg->dropoff ?? '';
                                }
                            }
                            // Build stop dates for display (repost/copy or old input)
                            $stopDatesForDisplayPost = [];
                            if (null !== old('stop_date') && is_array(old('stop_date'))) {
                                $stopDatesForDisplayPost = old('stop_date');
                            } elseif (!empty($chainSegmentsPost)) {
                                foreach ($chainSegmentsPost as $seg) {
                                    $stopDatesForDisplayPost[] = $seg->date ? \Carbon\Carbon::parse($seg->date)->format('F d, Y') : '';
                                }
                            }
                            // Normalize lengths
                            $realStopsPost = array_values(
                                array_filter($stopsForDisplayPost, function ($s) {
                                    return trim((string) $s) !== '';
                                }),
                            );
                            if (count($stopPickupDropoffForDisplayPost) !== count($stopsForDisplayPost)) {
                                $stopPickupDropoffForDisplayPost = array_pad(
                                    $stopPickupDropoffForDisplayPost,
                                    count($stopsForDisplayPost),
                                    '',
                                );
                            }
                            if (count($stopDatesForDisplayPost) !== count($stopsForDisplayPost)) {
                                $stopDatesForDisplayPost = array_pad($stopDatesForDisplayPost, count($stopsForDisplayPost), '');
                            }
                            $hasStopsPost = count($realStopsPost) > 0;
                        @endphp
                        <div class="bg-white rounded-lg overflow-hidden shadow-3xl mt-4" id="stops-section-wrapper"
                            data-segment-ids="[]">
                            <button type="button" id="add-more-spots-toggle"
                                class="add-more-spots-header text-2xl bg-primary text-white py-2 px-4 w-full"
                                aria-expanded="{{ $hasStopsPost ? 'true' : 'false' }}"
                                aria-controls="add-more-spots-panel" onclick="toggleAddMoreSpots(this)">
                                <h3 class="text-2xl">
                                    @isset($postRidePage->add_more_from_to){{ $postRidePage->add_more_from_to }}
                                    @else
                                    Stops Along the Way (Optional) @endisset
                                </h3>
                                <svg class="add-more-spots-chevron text-white" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div id="add-more-spots-panel" class="add-more-spots-panel" role="region"
                                aria-labelledby="add-more-spots-toggle"
                                style="{{ $hasStopsPost ? 'height: auto;' : 'height: 0;' }}">
                                <div class="add-more-spots-panel-inner bg-white p-4">
                                    <div class="flex items-center gap-2 mb-3">
                                        <h4 class="text-gray-900 text-xl font-medium">
                                            {{ $postRidePage->from_label ?? 'From' }}: </h4>
                                        <span id="stops-origin-label"
                                            class="text-gray-900 text-primary lg:text-lg"></span>
                                    </div>
                                    <h4 class="text-xl font-medium text-gray-900 mt-4 mb-3">Stops Along the Way: <span
                                            class="text-red-500">*</span></h4>
                                    <div class="space-y-3 mb-4" id="stops-rows-container">
                                        @if ($hasStopsPost)
                                            @foreach ($stopsForDisplayPost as $idx => $stopValue)
                                                @php $renderIndex = $idx + 1; @endphp
                                                <div class="flex items-center gap-3 stop-row flex-nowrap"
                                                    data-stop-index="{{ $renderIndex }}">
                                                    <div class="flex flex-row gap-2 items-stretch flex-1 min-w-0 flex-nowrap">
                                                        {{-- 1) Stop city --}}
                                                        <div class="relative flex-1 min-w-0 shrink">
                                                            <div
                                                                class="absolute inset-y-0 start-0 flex items-center pl-2 pointer-events-none">
                                                                <img src="{{ asset('assets/search-bar-from.png') }}"
                                                                    class="w-auto h-6" alt="">
                                                            </div>
                                                            <input type="text" name="stop_spot_display[]"
                                                                data-stop-index="{{ $renderIndex }}"
                                                                id="stop_spot_{{ $renderIndex }}"
                                                                value="{{ $stopValue }}" autocomplete="off"
                                                                class="bg-gray-100 border border-gray-200 pl-7 text-gray-900 text-base lg:text-lg rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 block w-full p-2.5"
                                                                placeholder="">
                                                            <div class="absolute hidden mt-1 z-10 left-0 top-full"
                                                                id="stopInputError_{{ $renderIndex }}">
                                                                <div
                                                                    class="tooltip-error shadow-lg rounded p-2 bg-red-500 text-white text-sm lg:text-base">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        {{-- 2) Pick up / drop off (text) --}}
                                                        <div class="relative flex-1 min-w-0 shrink">
                                                            <textarea name="stop_pickup_dropoff[]" data-stop-index="{{ $renderIndex }}"
                                                                id="stop_pickup_dropoff_{{ $renderIndex }}" rows="1"
                                                                placeholder="pick up / drop off"
                                                                class="bg-gray-100 border border-gray-200 text-gray-900 text-base lg:text-lg rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 block w-full p-2.5 resize-none">{{ old('stop_pickup_dropoff.' . $idx, $stopPickupDropoffForDisplayPost[$idx] ?? '') }}</textarea>
                                                            <div class="absolute hidden mt-1 z-10 left-0 top-full"
                                                                id="stopPickupDropoffError_{{ $renderIndex }}">
                                                                <div
                                                                    class="tooltip-error shadow-lg rounded p-2 bg-red-500 text-white text-sm lg:text-base">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        {{-- 3) Date --}}
                                                        <div class="w-32 sm:w-40 md:w-44 lg:w-48 shrink-0">
                                                            <div class="relative">
                                                                <div
                                                                    class="absolute inset-y-0 start-0 flex items-center pl-2 pointer-events-none">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                        viewBox="0 0 24 24" stroke-width="1.5"
                                                                        stroke="currentColor" class="w-6 h-6">
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                                                                    </svg>
                                                                </div>
                                                                <input type="text" name="stop_date[]"
                                                                    id="stop_date_{{ $renderIndex }}"
                                                                    value="{{ old('stop_date.' . $idx, $stopDatesForDisplayPost[$idx] ?? '') }}"
                                                                    class="bg-gray-100 border pl-10 border-gray-200 text-gray-900 text-base lg:text-lg rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 block w-full p-2.5"
                                                                    placeholder="">
                                                                <div class="absolute hidden mt-1 z-10 left-0 top-full"
                                                                    id="stopDateError_{{ $renderIndex }}">
                                                                    <div
                                                                        class="tooltip-error shadow-lg rounded p-2 bg-red-500 text-white text-sm lg:text-base">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        {{-- 4) Time --}}
                                                        <div class="w-32 sm:w-40 md:w-44 lg:w-48 shrink-0">
                                                            <div class="relative">
                                                                <div
                                                                    class="absolute inset-y-0 start-0 flex items-center pl-2 pointer-events-none">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                        viewBox="0 0 24 24" stroke-width="1.5"
                                                                        stroke="currentColor" class="w-6 h-6">
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                    </svg>
                                                                </div>
                                                                <input type="text" name="stop_time[]"
                                                                    id="stop_time_{{ $renderIndex }}"
                                                                    class="bg-gray-100 border pl-10 border-gray-200 text-gray-900 text-base lg:text-lg rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 block w-full p-2.5"
                                                                    placeholder="">
                                                                <div class="absolute hidden mt-1 z-10 left-0 top-full"
                                                                    id="stopTimeError_{{ $renderIndex }}">
                                                                    <div
                                                                        class="tooltip-error shadow-lg rounded p-2 bg-red-500 text-white text-sm lg:text-base">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <button type="button"
                                                        class="stop-delete-btn flex-shrink-0 p-2 text-red-600 hover:text-red-700 hover:bg-red-50 rounded focus:outline-none focus:ring-2 focus:ring-red-400"
                                                        onclick="confirmDeleteStopPostRide(this)" title="Delete stop"
                                                        aria-label="Delete stop">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                            class="w-6 h-6">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                    <button type="button" onclick="addStopRowPostRide();"
                                        class="button-exp-fill flex-shrink-0 whitespace-nowrap mb-4">+ Add Stop</button>
                                    <div class="flex items-center gap-2 mb-3">
                                        <h4 class="text-gray-900 text-xl font-medium">
                                            {{ $postRidePage->to_label ?? 'To' }}: </h4>
                                        <span id="stops-destination-label"
                                            class="text-gray-900 text-primary lg:text-lg"></span>
                                    </div>
                                    <div id="stops-segments-hidden" class="hidden"></div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center mb-4">
                            <input id="recurring_trip" type="checkbox" name="recurring" value="1"
                                {{ old('recurring') === '1' ? 'checked' : '' }}
                                class="w-4 h-4 text-blue-600 cursor-pointer bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                            <label for="recurring_trip" class="ml-2 text-gray-900">
                                @isset($postRidePage->recurring_label)
                                    {{ $postRidePage->recurring_label }}
                                @endisset
                            </label>
                        </div>

                        <div id="recurringtripDetails">
                            <div class="flex items-start flex-col md:flex-row mb-4 justify-between">
                                <div class="w-full md:w-[45%] mb-4">
                                    <label for="recurring_type" class="block mb-2 text-gray-900">
                                        @isset($postRidePage->recurring_type_label)
                                            {{ $postRidePage->recurring_type_label }}
                                        @endisset
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative mt-2">
                                        <select id="type" name="recurring_type"
                                            class="bg-gray-100 border border-gray-200 text-gray-900 text-base lg:text-lg rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 mt-2 block w-full p-2.5">
                                            <option value="" {{ old('recurring_type') === '' ? 'selected' : '' }}>
                                                Select
                                            </option>
                                            <option value="Daily"
                                                {{ old('recurring_type') === 'Daily' ? 'selected' : '' }}>
                                                Daily
                                            </option>
                                            <option value="Weekly"
                                                {{ old('recurring_type') === 'Weekly' ? 'selected' : '' }}>
                                                Weekly
                                            </option>
                                        </select>
                                    </div>
                                    @error('recurring_type')
                                        <div class="absolute mt-1 z-10">
                                            <div
                                                class="tooltip-error shadow-lg rounded p-2 bg-red-500 text-white text-sm lg:text-base">
                                                {{ $message }}</div>
                                        </div>
                                    @enderror
                                </div>
                                <div class="w-full md:w-[10%] hidden md:block mt-12 text-center">
                                    <span class="text-center text-base lg:text-lg ">
                                        or
                                    </span>
                                </div>
                                <div class="w-full md:w-[45%] mb-4">
                                    <label for="recurring_trips" class="block mb-2 text-gray-900">
                                        @isset($postRidePage->recurring_trips_label)
                                            {{ $postRidePage->recurring_trips_label }}
                                        @endisset
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative mt-2">
                                        <input type="number" min="1" max="10" name="recurring_trips"
                                            value="{{ old('recurring_trips') }}"
                                            @isset($postRidePage->recurring_trips_placeholder)
                                                placeholder="{{ $postRidePage->recurring_trips_placeholder }}"
                                            @endisset
                                            class="bg-gray-100 border border-gray-200 text-gray-900 text-base lg:text-lg rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 mt-2 block w-full p-2.5">
                                    </div>
                                    @error('recurring_trips')
                                        <div class="absolute mt-1 z-10">
                                            <div
                                                class="tooltip-error shadow-lg rounded p-2 bg-red-500 text-white text-sm lg:text-base">
                                                {{ $message }}</div>
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-6">
                    <div class="bg-white rounded-lg overflow-hidden shadow-3xl">
                        <h3 class="text-2xl bg-primary text-white py-2 px-4">
                            @isset($postRidePage->meeting_drop_off_description_label)
                                {{ $postRidePage->meeting_drop_off_description_label }}
                            @endisset
                            <span class="text-white">*</span>
                        </h3>
                        <div class="bg-white p-4 space-y-3">
                            <textarea id="meeting" rows="5" name="details"
                                class="block p-2.5 w-full text-gray-900 bg-gray-100 rounded border border-gray-200 text-base lg:text-lg focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 mt-2"
                                @isset($postRidePage->meeting_drop_off_description_placeholder)
                                    placeholder="{{ $postRidePage->meeting_drop_off_description_placeholder }}"
                                @endisset>{{ old('details', optional($ride)->details) }}</textarea>
                            @error('details')
                                <div class="absolute mt-1 z-10">
                                    <div
                                        class="tooltip-error shadow-lg rounded p-2 bg-red-500 text-white text-sm lg:text-base">
                                        {{ $message }}</div>
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mt-6">
                    <div class="bg-white rounded-lg overflow-hidden shadow-3xl">
                        <div class="text-2xl bg-primary text-white py-2 px-4">
                            <label for="no_of_seats" class="text-lg lg:text-3xl font-FuturaMdCnBT mb-2">
                                <h3 class="text-2xl">
                                    @isset($postRidePage->seats_label)
                                        {{ $postRidePage->seats_label }}
                                    @endisset
                                    <span class="text-white">*</span>
                                </h3>
                            </label>
                        </div>
                        <div class="bg-white p-4">
                            <div class="relative">
                                <div class="flex items-center flex-wrap gap-2 mt-2">
                                    @for ($i = 1; $i <= 7; $i++)
                                    <div class="relative">
                                        <label class="cursor-pointer inline-block" for="number-of-seat-{{ $i }}">
                                            <input id="number-of-seat-{{ $i }}" name="seats" type="radio" value="{{ $i }}" class="hidden" {{ old('seats', optional($ride)->seats) == $i ? 'checked' : '' }} onchange="seat_selected(this)" data-parsley-required="true" data-parsley-trigger="blur focusout change" data-parsley-required-message="Please select the available seats." data-parsley-errors-container="#parsley-seats-error">
                                            <span class="relative inline-block w-6 h-6 md:w-8 md:h-8">
                                                <img src="{{ old('seats', optional($ride)->seats) >= $i ? asset('assets/seat-hover-1.png') : asset('assets/seat.png') }}" class="w-8 h-8 object-cover cursor-pointer seat-image seat-unselect-{{ $i }}" alt="">
                                                <span class="absolute mt-2 inset-0 flex items-center justify-center text-sm seat-number seat-number-{{ $i }} {{ old('seats', optional($ride)->seats) >= $i ? 'text-green-300' : '' }}">{{ $i }}</span>
                                            </span>
                                        </label>
                                    </div>
                                    @endfor
                                </div>
                                @error('seats')
                                <div id="seats-server-error" class="mt-1 z-10 w-full">
                                    <div class="tooltip-error shadow-lg rounded p-2 bg-red-500 text-white text-sm lg:text-base">{{ $message }}</div>
                                </div>
                                @enderror
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 mt-6 gap-4">
                                <div>
                                    <label for="pickup_location" class="text-gray-900 mb-2">
                                        @isset($postRidePage->seats_middle_label)
                                            {{ $postRidePage->seats_middle_label }}
                                        @endisset
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <ul class="grid gap-2 grid-cols-2 mt-2">
                                        <li>
                                            <input type="radio" id="2-seats" name="middle_seats" value="2"
                                                class="hidden peer"
                                                {{ old('middle_seats', $ride->middle_seats) == '2' ? 'checked' : '' }}>
                                            <label for="2-seats"
                                                class="inline-flex items-center justify-center w-full p-1 text-gray-800 bg-white border-2 border-gray-100 rounded cursor-pointer peer-checked:border-green-500 peer-checked:border-2 peer-checked:text-green-500 hover:border-2 hover:border-green-500">
                                                <span class="font-medium text-md">
                                                    2 {{ $postRidePage->seats_text ?? 'seats' }}
                                                </span>
                                            </label>
                                        </li>
                                        <li>
                                            <input type="radio" id="3-seats" name="middle_seats" value="3"
                                                class="hidden peer"
                                                {{ old('middle_seats', $ride->middle_seats) == '3' ? 'checked' : '' }}>
                                            <label for="3-seats"
                                                class="inline-flex items-center justify-center w-full p-1 text-gray-800 bg-white border-2 border-gray-100 rounded cursor-pointer peer-checked:border-green-500 peer-checked:border-2 peer-checked:text-green-500 hover:border-2 hover:border-green-500">
                                                <span class="font-medium text-md">3
                                                    {{ $postRidePage->seats_text ?? 'seats' }}</span>
                                            </label>
                                        </li>
                                    </ul>
                                    @error('middle_seats')
                                        <div class="absolute mt-1 z-10">
                                            <div
                                                class="tooltip-error shadow-lg rounded p-2 bg-red-500 text-white text-sm lg:text-base">
                                                {{ $message }}</div>
                                        </div>
                                    @enderror
                                </div>
                                <div>
                                    <label for="back_seats" class="text-gray-900 mb-2">
                                        @isset($postRidePage->seats_back_label)
                                            {{ $postRidePage->seats_back_label }}
                                        @endisset
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <ul class="grid gap-2 grid-cols-2 mt-2">
                                        <li>
                                            <input type="radio" id="2-back_seats" name="back_seats" value="2"
                                                class="hidden peer"
                                                {{ old('back_seats', $ride->back_seats) == '2' ? 'checked' : '' }}>
                                            <label for="2-back_seats"
                                                class="inline-flex items-center justify-center w-full p-1 text-gray-800 bg-white border-2 border-gray-100 rounded cursor-pointer peer-checked:border-green-500 peer-checked:border-2 peer-checked:text-green-500 hover:border-2 hover:border-green-500">
                                                <span class="font-medium text-md">
                                                    2 {{ $postRidePage->seats_text ?? 'seats' }}
                                                </span>
                                            </label>
                                        </li>
                                        <li>
                                            <input type="radio" id="3-back_seats" name="back_seats" value="3"
                                                class="hidden peer"
                                                {{ old('back_seats', $ride->back_seats) == '3' ? 'checked' : '' }}>
                                            <label for="3-back_seats"
                                                class="inline-flex items-center justify-center w-full p-1 text-gray-800 bg-white border-2 border-gray-100 rounded cursor-pointer peer-checked:border-green-500 peer-checked:border-2 peer-checked:text-green-500 hover:border-2 hover:border-green-500">
                                                <span class="font-medium text-md">3
                                                    {{ $postRidePage->seats_text ?? 'seats' }}</span>
                                            </label>
                                        </li>
                                    </ul>
                                    @error('back_seats')
                                        <div class="absolute mt-1 z-10">
                                            <div
                                                class="tooltip-error shadow-lg rounded p-2 bg-red-500 text-white text-sm lg:text-base">
                                                {{ $message }}</div>
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 bg-white rounded-lg overflow-visible shadow-3xl">
                    <div class="text-2xl bg-primary text-white py-2 px-4 rounded-t-lg">
                        <h3 class="text-2xl">
                            @isset($postRidePage->price_payment_heading)
                                {{ $postRidePage->price_payment_heading }}
                            @endisset
                            <span class="text-white">*</span>
                        </h3>
                    </div>
                    <div id="post-ride-price-section" class="bg-white p-4 rounded-b-lg">

                        <div id="single-price-block">
                            <div>
                                <label for="" class=" text-gray-700 font-medium">
                                    @isset($postRidePage->price_per_seat_label)
                                        {{ $postRidePage->price_per_seat_label }}
                                    @endisset
                                    <span class="text-red-500">*</span>
                                </label>
                                <div class="relative mt-2">
                                    <span class="absolute inset-y-0 start-0 flex items-center pl-2 pointer-events-none">
                                        <svg fill="currentColor" width="800px" height="800px" viewBox="0 0 32 32"
                                            class="w-5 h-5 text-gray-500" xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M 15 3 L 15 5.09375 C 12.164063 5.570313 10 8.050781 10 11 C 10 12.777344 10.832031 14.148438 11.9375 15.03125 C 13.042969 15.914063 14.375 16.40625 15.625 16.90625 C 16.875 17.40625 18.042969 17.914063 18.8125 18.53125 C 19.582031 19.148438 20 19.773438 20 21 C 20 23.15625 18.207031 25 16 25 C 13.78125 25 12 23.21875 12 21 L 12 20 L 10 20 L 10 21 C 10 23.964844 12.164063 26.429688 15 26.90625 L 15 29 L 17 29 L 17 26.90625 C 19.84375 26.425781 22 23.925781 22 21 C 22 19.21875 21.167969 17.855469 20.0625 16.96875 C 18.957031 16.082031 17.625 15.5625 16.375 15.0625 C 15.125 14.5625 13.957031 14.082031 13.1875 13.46875 C 12.417969 12.855469 12 12.21875 12 11 C 12 8.808594 13.785156 7 16 7 C 18.21875 7 20 8.78125 20 11 L 20 12 L 22 12 L 22 11 C 22 8.035156 19.835938 5.570313 17 5.09375 L 17 3 Z" />
                                        </svg>
                                    </span>
                                    @php
                                        $defaultPrice =
                                            isset($ride->defaultRideDetail) && isset($ride->defaultRideDetail[0])
                                                ? $ride->defaultRideDetail[0]->price
                                                : '';
                                    @endphp
                                    <input type="number" step="any" name="price" autocomplete="off" id="priceData0"  placeholder=""
                                        value="{{ old('price', $defaultPrice) }}"
                                        class="bg-gray-100 border border-gray-200 pl-7 text-gray-900 text-base lg:text-lg rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 block w-full p-2.5 mt-2" />
                                </div>

                                @error('price')
                                    <div id="price-error-message" class="absolute mt-1 z-10">
                                        <div
                                            class="tooltip-error shadow-lg rounded p-2 bg-red-500 text-white text-sm lg:text-base">
                                            {{ $message }}</div>
                                    </div>
                                @enderror
                                <div id="price-client-error" class="absolute hidden mt-1 z-10 mb-2">
                                    <div
                                        class="tooltip-error shadow-lg rounded p-2 bg-red-500 text-white text-sm lg:text-base">
                                        Please enter the full route price.</div>
                                </div>
                            </div>
                        </div>
                        <div id="stops-segment-prices-dynamic" style="display: none;" data-bookings-readonly="0"
                            data-initial-segment-prices='@json($segmentPricesForPost ?? [])'>
                            <p class="text-gray-700 font-medium mt-2 mb-1">Full route price</p>
                            <div class="relative">
                                <div class="relative mt-2 mb-2">
                                    <span class="absolute inset-y-0 start-0 flex items-center pl-2 pointer-events-none">
                                        <svg fill="currentColor" width="800px" height="800px" viewBox="0 0 32 32"
                                            class="w-5 h-5 text-gray-500" xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M 15 3 L 15 5.09375 C 12.164063 5.570313 10 8.050781 10 11 C 10 12.777344 10.832031 14.148438 11.9375 15.03125 C 13.042969 15.914063 14.375 16.40625 15.625 16.90625 C 16.875 17.40625 18.042969 17.914063 18.8125 18.53125 C 19.582031 19.148438 20 19.773438 20 21 C 20 23.15625 18.207031 25 16 25 C 13.78125 25 12 23.21875 12 21 L 12 20 L 10 20 L 10 21 C 10 23.964844 12.164063 26.429688 15 26.90625 L 15 29 L 17 29 L 17 26.90625 C 19.84375 26.425781 22 23.925781 22 21 C 22 19.21875 21.167969 17.855469 20.0625 16.96875 C 18.957031 16.082031 17.625 15.5625 16.375 15.0625 C 15.125 14.5625 13.957031 14.082031 13.1875 13.46875 C 12.417969 12.855469 12 12.21875 12 11 C 12 8.808594 13.785156 7 16 7 C 18.21875 7 20 8.78125 20 11 L 20 12 L 22 12 L 22 11 C 22 8.035156 19.835938 5.570313 17 5.09375 L 17 3 Z" />
                                        </svg>
                                    </span>
                                    <input type="number" step="any" id="priceData0DynamicInput" placeholder=""
                                        value="{{ old('price', '') }}"
                                        class="full-route-price-input bg-gray-100 border border-gray-200 pl-7 text-gray-900 text-base lg:text-lg rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 block w-full p-2.5 mt-2" />
                                </div>
                                <div id="full-route-tooltip-container-dynamic"
                                    class="absolute hidden top-full left-1/2 -translate-x-1/2 mt-1 z-10">
                                    <div class="tooltip-error shadow-lg rounded p-2 bg-red-500 text-white text-sm lg:text-base">
                                        The full-route price can't be higher than the total of all route
                                        sections.<br>
                                        You can lower the full-route price or adjust section prices.
                                    </div>
                                </div>
                            </div>
                            <p class="text-gray-700 font-medium mt-2 mb-1">Total price (all sections)</p>
                            <div class="relative mt-2 mb-4">
                                <span
                                    class="absolute inset-y-0 start-0 flex items-center pl-2 pointer-events-none text-gray-500 font-medium">
                                    <svg fill="currentColor" width="800px" height="800px" viewBox="0 0 32 32"
                                        class="w-5 h-5 text-gray-500" xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M 15 3 L 15 5.09375 C 12.164063 5.570313 10 8.050781 10 11 C 10 12.777344 10.832031 14.148438 11.9375 15.03125 C 13.042969 15.914063 14.375 16.40625 15.625 16.90625 C 16.875 17.40625 18.042969 17.914063 18.8125 18.53125 C 19.582031 19.148438 20 19.773438 20 21 C 20 23.15625 18.207031 25 16 25 C 13.78125 25 12 23.21875 12 21 L 12 20 L 10 20 L 10 21 C 10 23.964844 12.164063 26.429688 15 26.90625 L 15 29 L 17 29 L 17 26.90625 C 19.84375 26.425781 22 23.925781 22 21 C 22 19.21875 21.167969 17.855469 20.0625 16.96875 C 18.957031 16.082031 17.625 15.5625 16.375 15.0625 C 15.125 14.5625 13.957031 14.082031 13.1875 13.46875 C 12.417969 12.855469 12 12.21875 12 11 C 12 8.808594 13.785156 7 16 7 C 18.21875 7 20 8.78125 20 11 L 20 12 L 22 12 L 22 11 C 22 8.035156 19.835938 5.570313 17 5.09375 L 17 3 Z" />
                                    </svg>
                                </span>
                                <input type="text" id="segment-total-price-input-dynamic" readonly placeholder="0.00"
                                    value="0.00"
                                    class="bg-gray-200 border border-gray-300 pl-7 text-gray-700 text-base lg:text-lg rounded block w-full p-2.5 mt-2 cursor-default" />
                            </div>
                            <div id="segment-price-rows-dynamic"></div>
                        </div>
                        <div class="mt-6">
                            <label for="" class="block mb-2 font-medium text-gray-900">
                                @isset($postRidePage->payment_methods_label)
                                    {{ $postRidePage->payment_methods_label }}
                                @endisset
                                <span class="text-red-500">*</span>
                            </label>
                            <div class="space-y-2 mt-2">
                                @if ($postRidePage->payment_methods_option1?->features_setting_id)
                                    <div class="flex items-center space-x-1 md:space-x-2 mb-2 mr-2 lg:mr-2">
                                        <input id="cash" name="payment_method" type="radio"
                                            value="{{ $postRidePage->payment_methods_option1->features_setting_id }}"
                                            {{ old('payment_method', $ride->payment_method) == $postRidePage->payment_methods_option1->features_setting_id ? 'checked' : '' }}
                                            class="h-5 w-5 rounded bg-white border border-gray-200 cursor-pointer text-indigo-600 focus:ring-indigo-600">
                                        <label for="cash"
                                            class="ml-3 font-normal text-gray-900 flex items-center space-x-1">
                                            @isset($postRidePage->payment_methods_option1->icon)
                                                <div class="w-8 h-6">
                                                    <img src="{{ asset('home_page_icons/' . $postRidePage->payment_methods_option1->icon) }}"
                                                        class="mx-auto w-full h-full object-contain" alt="">
                                                </div>
                                            @endisset
                                            <span class="">
                                                {{ $postRidePage->payment_methods_option1->name }}
                                            </span>
                                            <span class="inline-flex cursor-help payment-method-tooltip"
                                                data-tippy-content="{{ $postRidePage->payment_methods_option1_tooltip ?? '' }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" class="bi bi-info-circle-fill text-black"
                                                    viewBox="0 0 16 16">
                                                    <path
                                                        d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
                                                </svg>
                                            </span>
                                        </label>
                                    </div>
                                @endif
                                @if ($postRidePage->payment_methods_option2?->features_setting_id)
                                    <div class="flex items-center space-x-1 md:space-x-2 mb-2 mr-2 lg:mr-2">
                                        <input id="online" name="payment_method" type="radio"
                                            value="{{ $postRidePage->payment_methods_option2->features_setting_id }}"
                                            {{ old('payment_method', $ride->payment_method) == $postRidePage->payment_methods_option2->features_setting_id ? 'checked' : '' }}
                                            class="h-5 w-5 rounded bg-white border border-gray-200 cursor-pointer text-indigo-600 focus:ring-indigo-600">
                                        <label for="online"
                                            class="ml-3 font-normal text-gray-900 flex items-center space-x-1">
                                            @isset($postRidePage->payment_methods_option2->icon)
                                                <div class="w-8 h-6">
                                                    <img src="{{ asset('home_page_icons/' . $postRidePage->payment_methods_option2->icon) }}"
                                                        class="h-full w-full mx-auto object-contain" alt="">
                                                </div>
                                            @endisset
                                            <span class="">
                                                {{ $postRidePage->payment_methods_option2->name }}
                                            </span>
                                            <span class="inline-flex cursor-help payment-method-tooltip"
                                                data-tippy-content="{{ $postRidePage->payment_methods_option2_tooltip ?? '' }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" class="bi bi-info-circle-fill text-black"
                                                    viewBox="0 0 16 16">
                                                    <path
                                                        d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
                                                </svg>
                                            </span>
                                        </label>
                                    </div>
                                @endif
                                @if ($postRidePage->payment_methods_option3?->features_setting_id)
                                    <div class="flex items-center space-x-1 md:space-x-2 mb-2 mr-2 lg:mr-2">
                                        <input id="secured" name="payment_method" type="radio"
                                            value="{{ $postRidePage->payment_methods_option3->features_setting_id }}"
                                            {{ old('payment_method', $ride->payment_method) == $postRidePage->payment_methods_option3->features_setting_id ? 'checked' : '' }}
                                            class="h-5 w-5 rounded border border-gray-200 bg-white cursor-pointer text-indigo-600 focus:ring-indigo-600">
                                        <label for="secured"
                                            class="ml-3 font-normal text-gray-900 flex items-center space-x-1">
                                            @isset($postRidePage->payment_methods_option3->icon)
                                                <div class="w-8 h-6">
                                                    <img src="{{ asset('home_page_icons/' . $postRidePage->payment_methods_option3->icon) }}"
                                                        class="mx-auto h-full w-full object-contain" alt="">
                                                </div>
                                            @endisset
                                            <span class="">
                                                {{ $postRidePage->payment_methods_option3->name }}
                                            </span>
                                            <span class="inline-flex cursor-help payment-method-tooltip"
                                                data-tippy-content="{{ $postRidePage->payment_methods_option3_tooltip ?? '' }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" class="bi bi-info-circle-fill text-black"
                                                    viewBox="0 0 16 16">
                                                    <path
                                                        d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
                                                </svg>
                                            </span>
                                        </label>
                                    </div>
                                @endif
                            </div>
                            @error('payment_method')
                                <div class="absolute mt-1 z-10">
                                    <div
                                        class="tooltip-error shadow-lg rounded p-2 bg-red-500 text-white text-sm lg:text-base">
                                        {{ $message }}</div>
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mt-6 ">
                    <div class=" mt-6">
                        <div class="bg-white rounded-lg shadow-3xl">
                            <div class="text-2xl bg-primary rounded-t-lg text-white py-2 px-4">
                                <h3 class="text-2xl">
                                    @isset($postRidePage->booking_label)
                                        {{ $postRidePage->booking_label }}
                                    @endisset
                                    <span class="text-white">*</span>
                                </h3>
                            </div>
                            <div class="bg-white p-4">
                                <ul class="grid w-full gap-6 md:grid-cols-2">
                                    @if ($postRidePage->booking_option1?->features_setting_id)
                                        <li>
                                            <input type="radio" id="instant-booking" name="booking_method"
                                                value="{{ $postRidePage->booking_option1->features_setting_id }}"
                                                {{ old('booking_method', $ride->booking_method) == $postRidePage->booking_option1->features_setting_id ? 'checked' : '' }}
                                                class="hidden peer">
                                            <label for="instant-booking"
                                                class="inline-flex items-center space-x-3 w-full p-4 text-gray-800 bg-white border-2 border-gray-100 rounded cursor-pointer peer-checked:border-green-500 peer-checked:border-2 peer-checked:text-green-500 hover:border-2 hover:border-green-500">
                                                <img class="w-12 h-12"
                                                    src="{{ asset('home_page_icons/' . $postRidePage->booking_option1->icon) }}"
                                                    alt="">
                                                <span class="font-medium text-xl">
                                                    {{ $postRidePage->booking_option1->name }}
                                                </span>
                                                <span class="inline-flex cursor-help"
                                                    data-tippy-content="{{ $postRidePage->booking_option1_tooltip ?? '' }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                        fill="currentColor" class="bi bi-info-circle-fill text-black"
                                                        viewBox="0 0 16 16">
                                                        <path
                                                            d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
                                                    </svg>
                                                </span>
                                            </label>
                                        </li>
                                    @endif
                                    @if ($postRidePage->booking_option2?->features_setting_id)
                                        <li>
                                            <input type="radio" id="manual-approval" name="booking_method"
                                                value="{{ $postRidePage->booking_option2->features_setting_id }}"
                                                {{ old('booking_method', $ride->booking_method) == $postRidePage->booking_option2->features_setting_id ? 'checked' : '' }}
                                                class="hidden peer">
                                            <label for="manual-approval"
                                                class="inline-flex items-center space-x-3 w-full p-4 text-gray-800 bg-white border-2 border-gray-100 rounded cursor-pointer peer-checked:border-green-500 peer-checked:border-2 peer-checked:text-green-500 hover:border-2 hover:border-green-500">
                                                <img class="w-12 h-12"
                                                    src="{{ asset('home_page_icons/' . $postRidePage->booking_option2->icon) }}"
                                                    alt="">
                                                <span class="font-medium text-xl">
                                                    {{ $postRidePage->booking_option2->name }}
                                                </span>
                                                <span class="inline-flex cursor-help"
                                                    data-tippy-content="{{ $postRidePage->booking_option2_tooltip ?? '' }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                        fill="currentColor" class="bi bi-info-circle-fill text-black"
                                                        viewBox="0 0 16 16">
                                                        <path
                                                            d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
                                                    </svg>
                                                </span>
                                            </label>
                                        </li>
                                    @endif
                                </ul>
                                @error('booking_method')
                                    <div class="absolute mt-1 z-10">
                                        <div
                                            class="tooltip-error shadow-lg rounded p-2 bg-red-500 text-white text-sm lg:text-base">
                                            {{ $message }}</div>
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>


                <div class="bg-white rounded-lg overflow-hidden shadow-3xl mt-6">
                    <div class="text-2xl bg-primary text-white py-2 px-4">
                        <h3 class="text-2xl">
                            @isset($postRidePage->cancellation_policy_label)
                                {{ $postRidePage->cancellation_policy_label }}
                            @endisset
                            <span class="text-white">*</span>
                        </h3>
                    </div>
                    <div class="bg-white p-4">
                        <div>
                            <div class="space-y-2 mt-2">
                                @if ($postRidePage->cancellation_policy_label1?->features_setting_id)
                                    <div class="flex items-center space-x-1 md:space-x-2 mb-2 mr-2 lg:mr-2">
                                        <input id="standard" name="booking_type" type="radio"
                                            value="{{ $postRidePage->cancellation_policy_label1->features_setting_id }}"
                                            {{ old('booking_type', $ride->booking_type) == $postRidePage->cancellation_policy_label1->features_setting_id || empty(old('booking_type')) ? 'checked' : '' }}
                                            class="h-5 w-5 rounded bg-white border border-gray-200 cursor-pointer text-indigo-600 focus:ring-indigo-600">
                                        <label for="standard"
                                            class="ml-3 font-normal text-gray-900 flex items-center space-x-1">
                                            <span class="">
                                                {{ $postRidePage->cancellation_policy_label1->name }}
                                            </span>
                                        </label>
                                    </div>
                                @endif
                                @if ($postRidePage->cancellation_policy_label2?->features_setting_id)
                                    <div class="flex items-center space-x-1 md:space-x-2 mb-2 mr-2 lg:mr-2">
                                        <input id="firm" name="booking_type" type="radio"
                                            value="{{ $postRidePage->cancellation_policy_label2->features_setting_id }}"
                                            {{ old('booking_type', $ride->booking_type) == $postRidePage->cancellation_policy_label2->features_setting_id ? 'checked' : '' }}
                                            class="h-5 w-5 rounded bg-white border border-gray-200 cursor-pointer text-indigo-600 focus:ring-indigo-600">
                                        <label for="firm"
                                            class="ml-3 font-normal text-gray-900 flex items-center space-x-1">
                                            <span class="">
                                                {{ $postRidePage->cancellation_policy_label2->name }}
                                            </span>
                                        </label>
                                    </div>
                                @endif
                            </div>
                            @error('booking_type')
                                <div class="absolute mt-1 z-10">
                                    <div class="tooltip-error shadow-lg rounded p-2 bg-red-500 text-white text-sm lg:text-base">
                                        {{ $message }}</div>
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>


                

                <div class="mt-6">
                    <div class="bg-white rounded-lg overflow-hidden shadow-3xl">
                        <div class="text-2xl bg-primary text-white py-2 px-4">
                            <h3 class="text-2xl">
                                @isset($postRidePage->vehicle_label)
                                    {{ $postRidePage->vehicle_label }}
                                @endisset
                                <span class="text-white">*</span>
                            </h3>
                        </div>
                        <div class="bg-white p-4">
                            <div class="flex flex-col sm:flex-col md:flex-row justify-between mb-4">
                                <div>
                                    <input id="skip" type="checkbox" name="skip_vehicle" value="1"
                                        {{ old('skip_vehicle', $ride->skip_vehicle) == '1' ? 'checked' : '' }}
                                        class="w-4 h-4 text-blue-600 cursor-pointer bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                                    <label for="skip" class="ml-2  text-gray-900">
                                        @isset($postRidePage->skip_label)
                                            {{ $postRidePage->skip_label }}
                                        @endisset
                                    </label>
                                </div>
                                <div>
                                    <input id="add" type="checkbox" name="add_vehicle" value="1"
                                        {{ old('add_vehicle') == '1' ? 'checked' : '' }}
                                        class="w-4 h-4 text-blue-600 cursor-pointer bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                                    <label for="add" class="ml-2  text-gray-900">
                                        @isset($postRidePage->add_vehicle_label)
                                            {{ $postRidePage->add_vehicle_label }}
                                        @endisset
                                    </label>
                                </div>
                                @php
                                    $defaultAddedVehicle =
                                        $ride->added_vehicle ??
                                        ($ride->add_vehicle ??
                                            ($vehicles->firstWhere('primary_vehicle', '1') ? '1' : '0'));
                                @endphp
                                <div class="{{ $vehicles->count() > '0' ? '' : 'hidden' }}">
                                    <input id="added" type="checkbox" name="added_vehicle" value="1"
                                        {{ old('added_vehicle', $defaultAddedVehicle) == '1' ? 'checked' : '' }}
                                        class="w-4 h-4 text-blue-600 cursor-pointer bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                                    <label for="added" class="ml-2  text-gray-900">
                                        @isset($postRidePage->existing_label)
                                            {{ $postRidePage->existing_label }}
                                        @endisset
                                    </label>
                                </div>
                            </div>
                            @error('vehicle_selection')
                                <div class="absolute mt-1 z-10">
                                    <div
                                        class="tooltip-error shadow-lg rounded p-2 bg-red-500 text-white text-sm lg:text-base">
                                        {{ $message }}</div>
                                </div>
                            @enderror
                            <div id="skipVehicle">
                                <div class="mt-6 grid grid-cols-1 gap-6 md:grid-cols-4">
                                    <div class="md:col-span-2">
                                        <label for="make" class="text-gray-900 mb-2">
                                            @isset($postRidePage->make_label)
                                                {{ $postRidePage->make_label }}
                                            @endisset
                                            <span class="text-red-500">*</span>
                                        </label>
                                        <div class="mt-2">
                                            <input type="text" name="make" id=""
                                                @if ($errors->count() > 0) value="{{ old('make', $ride->make) }}"
                                                @else
                                                    value="{{ $ride->make }}" @endif
                                                @isset($postRidePage->make_placeholder)
                                                    placeholder="{{ $postRidePage->make_placeholder }}"
                                                @endisset
                                                class="bg-gray-100 border border-gray-200 text-gray-900 text-base lg:text-lg rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 mt-2 block w-full p-2.5">
                                        </div>
                                        @error('make')
                                            <div class="absolute mt-1 z-10">
                                                <div
                                                    class="tooltip-error shadow-lg rounded p-2 bg-red-500 text-white text-sm lg:text-base">
                                                    {{ $message }}</div>
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="md:col-span-2">
                                        <label for="modal" class="text-gray-900 mb-2">
                                            @isset($postRidePage->model_label)
                                                {{ $postRidePage->model_label }}
                                            @endisset
                                            <span class="text-red-500">*</span>
                                        </label>
                                        <div class="mt-2">
                                            <input type="text" name="model" id=""
                                                @if ($errors->count() > 0) value="{{ old('model', $ride->model) }}"
                                                @else
                                                    value="{{ $ride->model }}" @endif
                                                @isset($postRidePage->model_placeholder)
                                                    placeholder="{{ $postRidePage->model_placeholder }}"
                                                @endisset
                                                class="bg-gray-100 border border-gray-200 text-gray-900 text-base lg:text-lg rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 mt-2 block w-full p-2.5">
                                        </div>
                                        @error('model')
                                            <div class="absolute mt-1 z-10">
                                                <div
                                                    class="tooltip-error shadow-lg rounded p-2 bg-red-500 text-white text-sm lg:text-base">
                                                    {{ $message }}</div>
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="md:col-span-2">
                                        <label for="type" class="text-gray-900 mb-2">
                                            @isset($postRidePage->type_label)
                                                {{ $postRidePage->type_label }}
                                            @endisset
                                            <span class="text-red-500">*</span>
                                        </label>
                                        <div class="mt-2">
                                            <select id="type" name="vehicle_type"
                                                class="bg-gray-100 border border-gray-200 text-gray-900 text-base lg:text-lg rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 mt-2 block w-full p-2.5">

                                                <option
                                                    {{ old('vehicle_type', $ride->vehicle_type) == '' ? 'selected' : '' }}
                                                    value="">
                                                    @isset($postRidePage->vehicle_type_placeholder)
                                                        {{ $postRidePage->vehicle_type_placeholder }}
                                                    @endisset
                                                </option>

                                                <option
                                                    value="{{ $postRidePage->vehicle_type_convertible_value ?? 'Convertable' }}"
                                                    {{ old('vehicle_type', $ride->vehicle_type) === ($postRidePage->vehicle_type_convertible_value ?? 'Convertable') ? 'selected' : '' }}>
                                                    {{ $postRidePage->vehicle_type_convertible_text ?? 'Convertable' }}
                                                </option>
                                                <option value="{{ $postRidePage->vehicle_type_coupe_value ?? 'Coupe' }}"
                                                    {{ old('vehicle_type', $ride->vehicle_type) === ($postRidePage->vehicle_type_coupe_value ?? 'Coupe') ? 'selected' : '' }}>
                                                    {{ $postRidePage->vehicle_type_coupe_text ?? 'Coupe' }}
                                                </option>
                                                <option
                                                    value="{{ $postRidePage->vehicle_type_hatchback_value ?? 'Hatchback' }}"
                                                    {{ old('vehicle_type', $ride->vehicle_type) === ($postRidePage->vehicle_type_hatchback_value ?? 'Hatchback') ? 'selected' : '' }}>
                                                    {{ $postRidePage->vehicle_type_hatchback_text ?? 'Hatchback' }}
                                                </option>
                                                <option
                                                    value="{{ $postRidePage->vehicle_type_minivan_value ?? 'Minivan' }}"
                                                    {{ old('vehicle_type', $ride->vehicle_type) === ($postRidePage->vehicle_type_minivan_value ?? 'Minivan') ? 'selected' : '' }}>
                                                    {{ $postRidePage->vehicle_type_minivan_text ?? 'Minivan' }}
                                                </option>
                                                <option value="{{ $postRidePage->vehicle_type_sedan_value ?? 'Sedan' }}"
                                                    {{ old('vehicle_type', $ride->vehicle_type) === ($postRidePage->vehicle_type_sedan_value ?? 'Sedan') ? 'selected' : '' }}>
                                                    {{ $postRidePage->vehicle_type_sedan_text ?? 'Sedan' }}
                                                </option>
                                                <option value="{{ $postRidePage->vehicle_type_station_wagon_value }}"
                                                    {{ old('vehicle_type', $ride->vehicle_type) === ($postRidePage->vehicle_type_station_wagon_value ?? 'Station wagon') ? 'selected' : '' }}>
                                                    {{ $postRidePage->vehicle_type_station_wagon_text ?? 'Station wagon' }}
                                                </option>
                                                <option value="{{ $postRidePage->vehicle_type_suv_value ?? 'SUV' }}"
                                                    {{ old('vehicle_type', $ride->vehicle_type) === ($postRidePage->vehicle_type_suv_value ?? 'SUV') ? 'selected' : '' }}>
                                                    {{ $postRidePage->vehicle_type_suv_text ?? 'SUV' }}
                                                </option>
                                                <option value="{{ $postRidePage->vehicle_type_truck_value ?? 'Truck' }}"
                                                    {{ old('vehicle_type', $ride->vehicle_type) === ($postRidePage->vehicle_type_truck_value ?? 'Truck') ? 'selected' : '' }}>
                                                    {{ $postRidePage->vehicle_type_truck_text ?? 'Truck' }}
                                                </option>
                                                <option value="{{ $postRidePage->vehicle_type_van_value ?? 'Van' }}"
                                                    {{ old('vehicle_type', $ride->vehicle_type) === ($postRidePage->vehicle_type_van_value ?? 'Van') ? 'selected' : '' }}>
                                                    {{ $postRidePage->vehicle_type_van_text ?? 'Van' }}
                                                </option>
                                            </select>
                                        </div>
                                        @error('vehicle_type')
                                            <div class="absolute mt-1 z-10">
                                                <div
                                                    class="tooltip-error shadow-lg rounded p-2 bg-red-500 text-white text-sm lg:text-base">
                                                    {{ $message }}</div>
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="">
                                        <label for="type" class="text-gray-900 mb-2">
                                            @isset($postRidePage->year_label)
                                                {{ $postRidePage->year_label }}
                                            @endisset
                                            <span class="text-red-500">*</span>
                                        </label>
                                        <div class="mt-2">
                                            <input type="text" name="year" id="" placeholder=""
                                                @if ($errors->count() > 0) value="{{ old('year', $ride->year) }}"
                                                @else
                                                    value="{{ $ride->year }}" @endif
                                                class="bg-gray-100 border border-gray-200 text-gray-900 text-base lg:text-lg rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 mt-2 block w-full p-2.5">
                                        </div>
                                        @error('year')
                                            <div class="absolute mt-1 z-10">
                                                <div
                                                    class="tooltip-error shadow-lg rounded p-2 bg-red-500 text-white text-sm lg:text-base">
                                                    {{ $message }}</div>
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="">
                                        <label for="color" class="text-gray-900 mb-2">
                                            @isset($postRidePage->color_label)
                                                {{ $postRidePage->color_label }}
                                            @endisset
                                            <span class="text-red-500">*</span>
                                        </label>
                                        <div class="mt-2">
                                            <input type="text" name="color" id="" placeholder=""
                                                @if ($errors->count() > 0) value="{{ old('color', $ride->color) }}"
                                                @else
                                                    value="{{ $ride->color }}" @endif
                                                class="bg-gray-100 border border-gray-200 text-gray-900 text-base lg:text-lg rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 mt-2 block w-full p-2.5">
                                        </div>
                                        @error('color')
                                            <div class="absolute mt-1 z-10">
                                                <div
                                                    class="tooltip-error shadow-lg rounded p-2 bg-red-500 text-white text-sm lg:text-base">
                                                    {{ $message }}</div>
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="md:col-span-2">
                                        <label for="modal" class="text-gray-900 mb-2">
                                            @isset($postRidePage->liscense_label)
                                                {{ $postRidePage->liscense_label }}
                                            @endisset
                                            <span class="text-red-500">*</span>
                                        </label>
                                        <div class="mt-2">
                                            <input type="text" name="license_no" id="" placeholder=""
                                                @if ($errors->count() > 0) value="{{ old('license_no', $ride->license_no) }}"
                                                @else
                                                    value="{{ $ride->license_no }}" @endif
                                                class="bg-gray-100 border border-gray-200 text-gray-900 text-base lg:text-lg rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 mt-2 block w-full p-2.5">
                                        </div>
                                        @error('license_no')
                                            <div class="absolute mt-1 z-10">
                                                <div
                                                    class="tooltip-error shadow-lg rounded p-2 bg-red-500 text-white text-sm lg:text-base">
                                                    {{ $message }}</div>
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="md:col-span-4">
                                        <label for="modal" class="text-gray-900 mb-2">
                                            @isset($postRidePage->car_type_label)
                                                {{ $postRidePage->car_type_label }}
                                            @endisset
                                            <span class="text-red-500">*</span>
                                        </label>
                                        <div class=" flex items-center">
                                            @isset($postRidePage->electric_car_label)
                                                <div class="flex items-center space-x-1.5 lg:space-x-3 mb-2 mr-2 lg:mr-2">
                                                    <input id="" name="car_type" type="radio"
                                                        value="{{ $postRidePage->electric_car_label }}"
                                                        {{ old('car_type', $ride->car_type) == $postRidePage->electric_car_label ? 'checked' : '' }}
                                                        class="h-5 w-5 border-gray-300 bg-gray-200 cursor-pointer text-indigo-600 focus:ring-indigo-600">
                                                    <label for="" class="block text-gray-900">
                                                        {{ $postRidePage->electric_car_label }}
                                                    </label>
                                                </div>
                                            @endisset
                                            @isset($postRidePage->hybrid_car_label)
                                                <div class="flex items-center space-x-1.5 lg:space-x-3 mb-2 mr-2 lg:mr-2">
                                                    <input id="" name="car_type" type="radio"
                                                        value="{{ $postRidePage->hybrid_car_label }}"
                                                        {{ old('car_type', $ride->car_type) == $postRidePage->hybrid_car_label ? 'checked' : '' }}
                                                        class="h-5 w-5 border-gray-300 bg-gray-200 cursor-pointer text-indigo-600 focus:ring-indigo-600">
                                                    <label for="" class="block text-gray-900">
                                                        {{ $postRidePage->hybrid_car_label }}
                                                    </label>
                                                </div>
                                            @endisset
                                            @isset($postRidePage->gas_car_label)
                                                <div class="flex items-center space-x-1.5 lg:space-x-3 mb-2 mr-2 lg:mr-2">
                                                    <input id="" name="car_type" type="radio"
                                                        value="{{ $postRidePage->gas_car_label }}"
                                                        {{ old('car_type', $ride->car_type) == $postRidePage->gas_car_label || empty(old('car_type')) ? 'checked' : '' }}
                                                        class="h-5 w-5 border-gray-300 bg-gray-200 cursor-pointer text-indigo-600 focus:ring-indigo-600">
                                                    <label for="" class="block text-gray-900">
                                                        {{ $postRidePage->gas_car_label }}
                                                    </label>
                                                </div>
                                            @endisset
                                        </div>
                                        @error('car_type')
                                            <div class="absolute mt-1 z-10">
                                                <div
                                                    class="tooltip-error shadow-lg rounded p-2 bg-red-500 text-white text-sm lg:text-base">
                                                    {{ $message }}</div>
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="md:col-span-4">
                                        <div id="">
                                            {{-- <label for="car-photo" class="text-gray-900 mb-2">
                                                Car Photo
                                            </label> --}}
                                            <div class="md:col-span-2 mt-2">
                                                <label for="dropzone-file"
                                                    class="flex flex-col items-center justify-center w-full h-auto border-2 border-gray-300 border-dashed rounded cursor-pointer bg-gray-100 hover:bg-gray-100">
                                                    <div class="flex flex-col items-center justify-center pt-5 pb-6 p-4">
                                                        @if (session('uploaded_image'))
                                                            <img id="profile-image"
                                                                class="w-40 h-40 object-contain mb-4 cursor-pointer"
                                                                src="{{ asset('car_images/' . session('uploaded_image')) }}"
                                                                alt="Uploaded Image">
                                                        @elseif ($ride->car_image)
                                                            <img id="profile-image"
                                                                class="w-40 h-40 object-contain mb-4 cursor-pointer"
                                                                src="{{ $ride->car_image }}">
                                                        @else
                                                            <img id="profile-image"
                                                                class="w-12 h-12 object-contain mb-4 cursor-pointer"
                                                                src="{{ asset('assets/image-placeholder.png') }}">
                                                        @endif
                                                        <p class="text-sm lg:text-lg text-gray-900">
                                                            {{ $vehiclePage->image_description_label ?? 'Upload car photo' }}
                                                            <!-- <span class="font-semibold text-primary">Choose file</span> -->
                                                        </p>
                                                        <p class="text-sm lg:text-base text-gray-900 font-normal">
                                                            {{ $vehiclePage->images_option_placeholder ?? 'JPEG, JPG, PNG, GIF - 10MB max.' }}
                                                        </p>
                                                    </div>
                                                    <input id="dropzone-file" name="image" type="file"
                                                        onchange="previewImage(this)" class="hidden" />
                                                    @if (session('uploaded_image'))
                                                        <input type="hidden" name="existing_image"
                                                            value="{{ session('uploaded_image') }}">
                                                    @elseif ($ride->car_image)
                                                        @php
                                                            $imageName = basename($ride->car_image);
                                                        @endphp
                                                        <input type="hidden" name="existing_image"
                                                            value="{{ $imageName }}">
                                                    @endif
                                                    @error('image')
                                                        @if ($message !== 'The image is not uploaded yet')
                                                            <p class="text-red-500 text-base">{{ $message }}</p>
                                                        @endif
                                                    @enderror
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="showVehicles" class="md:col-span-2 group">
                                <label for="type" class="text-gray-900 mb-2">
                                    {{ $vehiclePage->vehicle_type_label ?? 'Select vehicle' }} <span
                                        class="text-red-500">*</span>
                                </label>
                                <div class="mt-2">
                                    @php
                                        $selectedVehicleId = old('vehicle_id', $ride->vehicle_id ?? null);
                                        if ($selectedVehicleId === '' || $selectedVehicleId === null) {
                                            $primaryVehicle = $vehicles->firstWhere('primary_vehicle', '1');
                                            if ($primaryVehicle) {
                                                $selectedVehicleId = $primaryVehicle->id;
                                            }
                                        }
                                    @endphp
                                    <select id="type" name="vehicle_id"
                                        class="bg-white border border-gray-300 text-gray-900 text-base lg:text-lg rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 mt-2 block w-full p-2.5">
                                        <option value=""
                                            {{ $selectedVehicleId === '' || $selectedVehicleId === null ? 'selected' : '' }}>
                                            {{ $vehiclePage->vehicle_type_placeholder ?? 'Select' }}
                                        </option>
                                        @foreach ($vehicles as $vehicle)
                                            <option value="{{ $vehicle->id }}"
                                                {{ (string) $selectedVehicleId === (string) $vehicle->id ? 'selected' : '' }}>
                                                {{ $vehicle->make }} / {{ $vehicle->model }} / {{ $vehicle->year }}
                                                @if ($vehicle->vehicle_type)
                                                    / {{ $vehicle->vehicle_type }}
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('vehicle_id')
                                        <div class="absolute mt-1 z-10">
                                            <div
                                                class="tooltip-error shadow-lg rounded p-2 bg-red-500 text-white text-sm lg:text-base">
                                                {{ $message }}</div>
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 bg-white rounded-lg shadow-3xl">
                    <div class="text-2xl bg-primary rounded-t-lg text-white py-2 px-4">
                        <h3 class="text-2xl">
                            @isset($postRidePage->luggage_label)
                                {{ $postRidePage->luggage_label }}
                            @endisset
                            <span class="text-white">*</span>
                        </h3>
                    </div>
                    <div class="bg-white p-4">
                        <div class="border rounded-md divide-y">
                            @if ($postRidePage->luggage_option1?->features_setting_id)
                                @php
                                    $luggageVal = old('luggage', $ride->luggage);
                                    $luggageFirstId = $postRidePage->luggage_option1->features_setting_id;
                                @endphp
                                <div class="flex items-center gap-4 p-3">
                                    <label for="{{ $postRidePage->luggage_option1->features_setting_id }}"
                                        class="font-normal text-gray-900 flex items-center space-x-1 gap-2 w-full">
                                        <input id="{{ $postRidePage->luggage_option1->features_setting_id }}"
                                            type="radio" name="luggage"
                                            value="{{ $postRidePage->luggage_option1->features_setting_id }}"
                                            {{ ($luggageVal && $luggageVal == $luggageFirstId) || !$luggageVal ? 'checked' : '' }}
                                            class="w-4 h-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                        @isset($postRidePage->luggage_option1->icon)
                                            <img class="w-10 h-10"
                                                src="{{ asset('home_page_icons/' . $postRidePage->luggage_option1->icon) }}"
                                                alt="">
                                        @endisset
                                        <span>
                                            {{ $postRidePage->luggage_option1->name }}
                                        </span>
                                        <span class="inline-flex cursor-help"
                                            data-tippy-content="{{ $postRidePage->luggage_option1_tooltip ?? '' }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                fill="currentColor" class="bi bi-info-circle-fill text-black"
                                                viewBox="0 0 16 16">
                                                <path
                                                    d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
                                            </svg>
                                        </span>
                                    </label>
                                </div>
                            @endif
                            @if ($postRidePage->luggage_option2?->features_setting_id)
                                <div class="flex items-center gap-4 p-3">
                                    <label for="{{ $postRidePage->luggage_option2->features_setting_id }}"
                                        class="font-normal text-gray-900 flex items-center space-x-1 gap-2 w-full">
                                        <input type="radio"
                                            id="{{ $postRidePage->luggage_option2->features_setting_id }}"
                                            name="luggage"
                                            value="{{ $postRidePage->luggage_option2->features_setting_id }}"
                                            {{ old('luggage', $ride->luggage) == $postRidePage->luggage_option2->features_setting_id ? 'checked' : '' }}
                                            class="w-4 h-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                        @isset($postRidePage->luggage_option2->icon)
                                            <img class="w-10 h-10"
                                                src="{{ asset('home_page_icons/' . $postRidePage->luggage_option2->icon) }}"
                                                alt="">
                                        @endisset
                                        <span class="">
                                            {{ $postRidePage->luggage_option2->name }}
                                        </span>
                                        <span class="inline-flex cursor-help"
                                            data-tippy-content="{{ $postRidePage->luggage_option2_tooltip ?? '' }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                fill="currentColor" class="bi bi-info-circle-fill text-black"
                                                viewBox="0 0 16 16">
                                                <path
                                                    d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
                                            </svg>
                                        </span>
                                    </label>
                                </div>
                            @endif
                            @if ($postRidePage->luggage_option3?->features_setting_id)
                                <div class="flex items-center gap-4 p-3">
                                    <label for="{{ $postRidePage->luggage_option3->features_setting_id }}"
                                        class="font-normal text-gray-900 flex items-center space-x-1 gap-2 w-full">
                                        <input type="radio"
                                            id="{{ $postRidePage->luggage_option3->features_setting_id }}"
                                            name="luggage"
                                            value="{{ $postRidePage->luggage_option3->features_setting_id }}"
                                            {{ old('luggage', $ride->luggage) == $postRidePage->luggage_option3->features_setting_id ? 'checked' : '' }}
                                            class="w-4 h-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                        @isset($postRidePage->luggage_option3->icon)
                                            <img class="w-10 h-10"
                                                src="{{ asset('home_page_icons/' . $postRidePage->luggage_option3->icon) }}"
                                                alt="">
                                        @endisset
                                        <span>
                                            {{ $postRidePage->luggage_option3->name }}
                                        </span>
                                        <span class="inline-flex cursor-help"
                                            data-tippy-content="{{ $postRidePage->luggage_option3_tooltip ?? '' }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                fill="currentColor" class="bi bi-info-circle-fill text-black"
                                                viewBox="0 0 16 16">
                                                <path
                                                    d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
                                            </svg>
                                        </span>
                                    </label>
                                </div>
                            @endif
                            @if ($postRidePage->luggage_option4?->features_setting_id)
                                <div class="flex items-center gap-4 p-3">
                                    <label for="{{ $postRidePage->luggage_option4->features_setting_id }}"
                                        class="font-normal text-gray-900 flex items-center space-x-1 gap-2 w-full">
                                        <input type="radio"
                                            id="{{ $postRidePage->luggage_option4->features_setting_id }}"
                                            name="luggage"
                                            value="{{ $postRidePage->luggage_option4->features_setting_id }}"
                                            {{ old('luggage', $ride->luggage) == $postRidePage->luggage_option4->features_setting_id ? 'checked' : '' }}
                                            class="w-4 h-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                        @isset($postRidePage->luggage_option4->icon)
                                            <img class="w-10 h-10"
                                                src="{{ asset('home_page_icons/' . $postRidePage->luggage_option4->icon) }}"
                                                alt="">
                                        @endisset
                                        <span>
                                            {{ $postRidePage->luggage_option4->name }}
                                        </span>
                                        <span class="inline-flex cursor-help"
                                            data-tippy-content="{{ $postRidePage->luggage_option4_tooltip ?? '' }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                fill="currentColor" class="bi bi-info-circle-fill text-black"
                                                viewBox="0 0 16 16">
                                                <path
                                                    d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
                                            </svg>
                                        </span>
                                    </label>
                                </div>
                            @endif
                            @if ($postRidePage->luggage_option5?->features_setting_id)
                                <div class="flex items-center gap-4 p-3">
                                    <label for="{{ $postRidePage->luggage_option5->features_setting_id }}"
                                        class="font-normal text-gray-900 flex items-start space-x-1 gap-2 w-full">
                                        <input type="radio"
                                            id="{{ $postRidePage->luggage_option5->features_setting_id }}"
                                            name="luggage"
                                            value="{{ $postRidePage->luggage_option5->features_setting_id }}"
                                            {{ old('luggage', $ride->luggage) == $postRidePage->luggage_option5->features_setting_id ? 'checked' : '' }}
                                            class="w-4 h-4 mt-2 text-blue-600 cursor-pointer bg-white border-gray-500 rounded focus:ring-blue-500  focus:ring-2">
                                        @isset($postRidePage->luggage_option5->icon)
                                            <img class="w-10 h-10"
                                                src="{{ asset('home_page_icons/' . $postRidePage->luggage_option5->icon) }}"
                                                alt="">
                                        @endisset
                                        <div>
                                            <p class="leading-normal mt-2">
                                                {{ $postRidePage->luggage_option5->name }}
                                            </p>
                                            <div
                                                class="font-normal text-gray-900 flex lg:block items-center space-x-0.5 2xl:pr-8">
                                                <small>{{ $postRidePage->luggage_option5_label }}</small>
                                                <span class="inline-flex cursor-help items-center"
                                                    data-tippy-content="{{ $postRidePage->luggage_option5_tooltip ?? '' }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                        height="16" fill="currentColor"
                                                        class="bi bi-info-circle-fill text-black" viewBox="0 0 16 16">
                                                        <path
                                                            d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
                                                    </svg>
                                                </span>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            @endisset
                    </div>
                    @error('luggage')
                        <div class="absolute mt-1 z-10">
                            <div class="tooltip-error shadow-lg rounded p-2 bg-red-500 text-white text-sm lg:text-base">
                                {{ $message }}</div>
                        </div>
                    @enderror
                    <div class="mt-6 space-y-2">
                        <div class="flex items-start">
                            <input id="heating" type="checkbox" name="accept_more_luggage" value="1"
                                {{ old('accept_more_luggage', $ride->accept_more_luggage) == '1' ? 'checked' : '' }}
                                class="w-4 h-4 mt-1 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                            <label for="heating" class="ml-2 font-normal text-gray-900 flex space-x-1">
                                <span class="">
                                    @isset($postRidePage->luggage_checkbox_label1)
                                        {{ $postRidePage->luggage_checkbox_label1 }}
                                    @endisset
                                </span>
                            </label>
                        </div>
                        {{-- <div class="flex items-start">
                                <input id="heating" type="checkbox" name="open_customized" value="1"
                                    {{ old('open_customized', $ride->open_customized) == '1' ? 'checked' : '' }}
                                    class="w-4 h-4 mt-1 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                <label for="heating"
                                    class="ml-2 font-normal text-gray-900 flex space-x-1">
                                    <span class="">
                                        @isset($postRidePage->luggage_checkbox_label2)
                                            {{ $postRidePage->luggage_checkbox_label2 }}
                                        @endisset
                                        <sup class="text-red-500">*</sup>
                                    </span>
                                </label>
                            </div> --}}
                    </div>

                </div>
            </div>

            <div class="mt-6 bg-white rounded-lg overflow-hidden shadow-3xl">
                <div class="text-2xl bg-primary text-white py-2 px-4">
                    <h3 class="text-2xl">
                        @isset($postRidePage->smoking_label)
                            {{ $postRidePage->smoking_label }}
                        @endisset
                        <span class="text-white">*</span>
                    </h3>
                </div>
                <div class="bg-white p-4">
                    <div class="border rounded-md overflow-hidden divide-y">
                        @if ($postRidePage->smoking_option1?->features_setting_id)
                            <div class="flex items-center gap-4 p-3 w-full">
                                <label for="{{ $postRidePage->smoking_option1->features_setting_id }}"
                                    class="font-normal text-gray-900 flex space-x-1 flex items-center gap-4 w-full">
                                    <input id="{{ $postRidePage->smoking_option1->features_setting_id }}"
                                        name="smoke" type="radio"
                                        value="{{ $postRidePage->smoking_option1->features_setting_id }}"
                                        {{ $isNewForm ? (old('smoke', $user->smoke) == $postRidePage->smoking_option1->features_setting_id ? 'checked' : (21 == $postRidePage->smoking_option1->features_setting_id ? 'checked' : '')) : (old('smoke', $ride->smoke) == $postRidePage->smoking_option1->features_setting_id ? 'checked' : '') }}
                                        class="h-4 w-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">

                                    <span class="">
                                        {{ $postRidePage->smoking_option1->name }}
                                    </span>
                                </label>

                            </div>
                        @endif
                        @if ($postRidePage->smoking_option2?->features_setting_id)
                            <div class="flex items-center gap-4 p-3">
                                <label for="{{ $postRidePage->smoking_option2->features_setting_id }}"
                                    class="font-normal text-gray-900 flex space-x-1 flex items-center gap-4 w-full">
                                    <input id="{{ $postRidePage->smoking_option2->features_setting_id }}"
                                        name="smoke" type="radio"
                                        value="{{ $postRidePage->smoking_option2->features_setting_id }}"
                                        {{ $isNewForm ? (old('smoke', $user->smoke) == $postRidePage->smoking_option2->features_setting_id ? 'checked' : '') : (old('smoke', $ride->smoke) == $postRidePage->smoking_option2->features_setting_id ? 'checked' : '') }}
                                        class="h-4 w-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                    {{ $postRidePage->smoking_option2->name }}
                                </label>
                            </div>
                        @endisset
                </div>
                @error('smoke')
                    <div class="absolute mt-1 z-10">
                        <div class="tooltip-error shadow-lg rounded p-2 bg-red-500 text-white text-sm lg:text-base">
                            {{ $message }}</div>
                    </div>
                @enderror
            </div>
        </div>

        <div class="mt-6 bg-white rounded-lg overflow-hidden shadow-3xl">
            <div class="text-2xl bg-primary text-white py-2 px-4">
                <h3 class="text-2xl">
                    @isset($postRidePage->animals_label)
                        {{ $postRidePage->animals_label }}
                    @endisset
                    <span class="text-white">*</span>
                </h3>
            </div>
            <div class="bg-white p-4">
                <div class="border rounded-md overflow-hidden divide-y">
                    @if ($postRidePage->animals_option1?->features_setting_id)
                        <div class="flex items-center gap-4 p-3">
                            <label for="{{ $postRidePage->animals_option1->features_setting_id }}"
                                class="font-normal text-gray-900 flex space-x-1 flex items-center gap-4 w-full">
                                <input id="{{ $postRidePage->animals_option1->features_setting_id }}"
                                    name="animal_friendly" type="radio"
                                    value="{{ $postRidePage->animals_option1->features_setting_id }}"
                                    {{ $isNewForm ? (old('animal_friendly') == $postRidePage->animals_option1->features_setting_id ? 'checked' : (23 == $postRidePage->animals_option1->features_setting_id ? 'checked' : '')) : (old('animal_friendly', $ride->animal_friendly) == $postRidePage->animals_option1->features_setting_id ? 'checked' : '') }}
                                    class="w-4 h-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                {{ $postRidePage->animals_option1->name }}
                            </label>
                        </div>
                    @endif
                    @if ($postRidePage->animals_option2?->features_setting_id)
                        <div class="flex items-center gap-4 p-3">
                            <label for="{{ $postRidePage->animals_option2->features_setting_id }}"
                                class="font-normal text-gray-900 flex space-x-1 flex items-center gap-4 w-full">
                                <input id="{{ $postRidePage->animals_option2->features_setting_id }}"
                                    name="animal_friendly" type="radio"
                                    value="{{ $postRidePage->animals_option2->features_setting_id }}"
                                    {{ $isNewForm ? (old('animal_friendly') == $postRidePage->animals_option2->features_setting_id ? 'checked' : '') : (old('animal_friendly', $ride->animal_friendly) == $postRidePage->animals_option2->features_setting_id ? 'checked' : '') }}
                                    class="w-4 h-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                {{ $postRidePage->animals_option2->name }}
                            </label>
                        </div>
                    @endif
                    @if ($postRidePage->animals_option3?->features_setting_id)
                        <div class="flex items-center gap-4 p-3">
                            <label for="{{ $postRidePage->animals_option3->features_setting_id }}"
                                class="font-normal text-gray-900 flex space-x-1 flex items-center gap-4 w-full">
                                <input id="{{ $postRidePage->animals_option3->features_setting_id }}"
                                    name="animal_friendly" type="radio"
                                    value="{{ $postRidePage->animals_option3->features_setting_id }}"
                                    {{ $isNewForm ? (old('animal_friendly') == $postRidePage->animals_option3->features_setting_id ? 'checked' : '') : (old('animal_friendly', $ride->animal_friendly) == $postRidePage->animals_option3->features_setting_id ? 'checked' : '') }}
                                    class="w-4 h-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                {{ $postRidePage->animals_option3->name }}
                            </label>
                        </div>
                    @endisset
            </div>
            @error('animal_friendly')
                <div class="absolute mt-1 z-10">
                    <div class="tooltip-error shadow-lg rounded p-2 bg-red-500 text-white text-sm lg:text-base">
                        {{ $message }}</div>
                </div>
            @enderror
        </div>
    </div>

    <div class="mt-6">
        <div class="bg-white rounded-lg shadow-3xl">
            <div class="text-2xl bg-primary rounded-t-lg text-white py-2 px-4">
                <h3 class="text-2xl">
                    @isset($postRidePage->preferences_label)
                        {{ $postRidePage->preferences_label }}
                    @endisset
                </h3>
            </div>
            <div class="bg-white p-4">
                @php
                    $hasDriverLicenseOnFile = !empty($user->driver_license_upload);
                    $hasNoAddress = empty($user->address);
                    $showPinkOrExtraAddressNotice =
                        $hasDriverLicenseOnFile &&
                        $hasNoAddress &&
                        ($postRidePage->features_option1?->features_setting_id ||
                            $postRidePage->features_option2?->features_setting_id);
                @endphp
                <div class="space-y-2">
                    @if ($postRidePage->features_option1?->features_setting_id)
                        <div class="flex items-center {{ $showPinkOrExtraAddressNotice ? 'cursor-pointer' : '' }}"
                            @if ($showPinkOrExtraAddressNotice) onclick="event.preventDefault(); showPinkExtraAddressRequiredModal();" role="button" tabindex="0" @endif>
                            <input id="pink-ride" type="checkbox" name="features[]"
                                @php $disabled = false; @endphp
                                @if ($user->pink_ride == '0') @php $disabled = true; @endphp
                                            @elseif ($user->pink_ride == '')
                                                @if ($pinkRideSetting)
                                                @if ($user->gender == 'female' && (empty($user->government_issued_id) || empty($user->address)))
                                                @php $disabled = true; @endphp
                                                 @elseif ($user->gender !== 'female')
                                                        @php $disabled = true; @endphp
                                                    @elseif ($pinkRideSetting->verfiy_phone === '1' && $user->phone_verified !== '1')
                                                        @php $disabled = true; @endphp
                                                    @elseif ($pinkRideSetting->verify_email === '1' && $user->email_verified !== '1')
                                                        @php $disabled = true; @endphp
                                                    @elseif ($pinkRideSetting->driver_license === '1' && $user->driver !== '1')
                                                        @php $disabled = true; @endphp @endif
                                @endif
                    @endif
                    @if ($disabled) {{ 'disabled' }} @endif
                    value="{{ $postRidePage->features_option1->features_setting_id }}"
                    {{ $isNewForm
                        ? (in_array($postRidePage->features_option1->features_setting_id, old('features', []))
                            ? 'checked'
                            : '')
                        : (old('features')
                            ? (in_array($postRidePage->features_option1->features_setting_id, old('features', []))
                                ? 'checked'
                                : '')
                            : (in_array($postRidePage->features_option1->features_setting_id, explode('=', $ride->features))
                                ? 'checked'
                                : '')) }}
                    class="w-4 h-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                    <label for="pink-ride" class="ml-2 text-gray-900 flex space-x-1">
                        <span
                            class="text-pink-500 font-medium
                                                @php $disabled = false; @endphp
                                                @if ($user->pink_ride == '0') @php $disabled = true; @endphp
                                                @elseif ($user->pink_ride == '')
                                                    @if ($pinkRideSetting)

                                                        @if ($user->gender == 'female' && (empty($user->government_issued_id) || empty($user->address)))
                                                         @php $disabled = true; @endphp
                                                        @elseif ($user->gender !== 'female')
                                                            @php $disabled = true; @endphp
                                                        @elseif ($pinkRideSetting->verfiy_phone === '1' && $user->phone_verified !== '1')
                                                            @php $disabled = true; @endphp
                                                        @elseif ($pinkRideSetting->verify_email === '1' && $user->email_verified !== '1')
                                                            @php $disabled = true; @endphp
                                                        @elseif ($pinkRideSetting->driver_license === '1' && $user->driver !== '1')
                                                            @php $disabled = true; @endphp @endif
                                                    @endif
                                                @endif
                                                @if ($disabled) {{ 'line-through' }} @endif">
                            {{ $postRidePage->features_option1->name }}
                        </span>
                        @php
                            $pinkRideTooltipText = '';
                            if ($user->pink_ride == '0') {
                                $pinkRideTooltipText = $postRidePage->pink_ride_tooltip_admin_disable_text ?? '';
                            } elseif ($user->pink_ride == '1') {
                                $pinkRideTooltipText = $postRidePage->pink_ride_tooltip_admin_enable_text ?? '';
                            } elseif ($pinkRideSetting) {
                                $pinkRideTooltipText =
                                    ($postRidePage->pink_ride_tooltip_only_text ?? '') .
                                    ' ' .
                                    ($postRidePage->pink_ride_tooltip_female_text ?? '') .
                                    ' ' .
                                    ($postRidePage->pink_ride_tooltip_driver_text ?? '');
                                if (
                                    $pinkRideSetting->verfiy_phone === '1' ||
                                    $pinkRideSetting->verify_email === '1' ||
                                    $pinkRideSetting->driver_license === '1' ||
                                    $pinkRideSetting->profile_complete === '1'
                                ) {
                                    $pinkRideTooltipText .= ' ' . ($postRidePage->pink_ride_tooltip_with_text ?? '');
                                    if ($pinkRideSetting->profile_complete === '1') {
                                        $pinkRideTooltipText .=
                                            ' ' . ($postRidePage->pink_ride_tooltip_complete_profile_text ?? '');
                                    }
                                    if (
                                        $pinkRideSetting->verfiy_phone === '1' ||
                                        $pinkRideSetting->verify_email === '1' ||
                                        $pinkRideSetting->driver_license === '1'
                                    ) {
                                        if ($pinkRideSetting->verfiy_phone === '1') {
                                            $pinkRideTooltipText .=
                                                ' ' . ($postRidePage->pink_ride_tooltip_phone_number_text ?? '');
                                        }
                                        if ($pinkRideSetting->verify_email === '1') {
                                            $pinkRideTooltipText .=
                                                ' ' . ($postRidePage->pink_ride_tooltip_email_text ?? '');
                                        }
                                        if ($pinkRideSetting->driver_license === '1') {
                                            $pinkRideTooltipText .=
                                                ' ' . ($postRidePage->pink_ride_tooltip_driver_license_text ?? '');
                                        }
                                        $pinkRideTooltipText .=
                                            ' ' . ($postRidePage->pink_ride_tooltip_verified_text ?? '');
                                    }
                                }
                                $pinkRideTooltipText .=
                                    ' ' . ($postRidePage->pink_ride_tooltip_select_this_ride_text ?? '');
                            }
                        @endphp
                        <span class="inline-flex cursor-help"
                            data-tippy-content="{{ $pinkRideTooltipText }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                fill="currentColor" class="bi bi-info-circle-fill text-black"
                                viewBox="0 0 16 16">
                                <path
                                    d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
                            </svg>
                        </span>
                    </label>
                </div>
                @endif
                @if ($postRidePage->features_option2?->features_setting_id)
                    @php
                        // Calculate the age based on the driver's date of birth
                        $dob = \Carbon\Carbon::parse($user->dob);
                        $age = $dob->diffInYears(\Carbon\Carbon::now());
                        $totalRidesCount = 0;
                        $ride_limit = 0;
                        if (isset($totalRides) && !empty($totalRides)) {
                            $totalRidesCount = $totalRides;
                        }

                        if (isset($setting->extra_rides_trip_limit) && !is_null($setting->extra_rides_trip_limit)) {
                            $ride_limit = $setting->extra_rides_trip_limit;
                        }
                    @endphp
                    <div class="flex items-center {{ $showPinkOrExtraAddressNotice ? 'cursor-pointer' : '' }}"
                        @if ($showPinkOrExtraAddressNotice) onclick="event.preventDefault(); event.stopPropagation(); showPinkExtraAddressRequiredModal();" role="button" tabindex="0" @endif>
                        <input id="Extra+" type="checkbox" name="features[]"
                            @php $disabled = false; @endphp
                            @if ($user->folks_ride == '0') @php $disabled = true; @endphp
                                            @elseif ($user->folks_ride == '')
                                                @if ($setting)
                                                    @if ($setting->verfiy_phone === '1' && !$user->phoneNumbers->contains('verified', 1))
                                                        @php $disabled = true; @endphp
                                                    @elseif ($setting->verify_email === '1' && $user->email_verified !== '1')
                                                        @php $disabled = true; @endphp
                                                    @elseif ($setting->driver_license === '1' && $user->driver !== '1')
                                                        @php $disabled = true; @endphp
                                                    @elseif (
                                                        $overallRating < $setting->average_rating ||
                                                            $age < $setting->driver_age ||
                                                            $totalRidesCount < $ride_limit ||
                                                            $noShowsCount > 0 ||
                                                            $cancellationCount > 0)
                                                        @php $disabled = true; @endphp
                                                    @elseif ($noshows > 0)
                                                        @php $disabled = true; @endphp
                                                        
                                                    @elseif (empty($user->government_issued_id) || empty($user->address))
                                                        @php $disabled = true; @endphp @endif
                            @endif
                @endif
                @if ($disabled) {{ 'disabled' }} @endif
                value="{{ $postRidePage->features_option2->features_setting_id }}"
                {{ $isNewForm
                    ? (in_array($postRidePage->features_option2->features_setting_id, old('features', []))
                        ? 'checked'
                        : '')
                    : (old('features')
                        ? (in_array($postRidePage->features_option2->features_setting_id, old('features', []))
                            ? 'checked'
                            : '')
                        : (in_array($postRidePage->features_option2->features_setting_id, explode('=', $ride->features))
                            ? 'checked'
                            : '')) }}
                class="w-4 h-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                <label for="Extra+" class="ml-2 text-gray-900 flex space-x-1">
                    <span
                        class="text-green-500 font-medium
                                                @php $disabled = false; @endphp
                                                
                                            @if ($user->folks_ride == '0') @php $disabled = true; @endphp
                                                @elseif ($user->folks_ride == '')
                                                    @if ($setting)
                                                        @if ($setting->verfiy_phone === '1' && !$user->phoneNumbers->contains('verified', 1))
                                                            @php $disabled = true; @endphp
                                                        @elseif ($setting->verify_email === '1' && $user->email_verified !== '1')
                                                            @php $disabled = true; @endphp
                                                        @elseif ($setting->driver_license === '1' && $user->driver !== '1')
                                                            @php $disabled = true; @endphp
                                                        @elseif (
                                                            $overallRating < $setting->average_rating ||
                                                                $age < $setting->driver_age ||
                                                                $totalRidesCount < $ride_limit ||
                                                                $noShowsCount > 0 ||
                                                                $cancellationCount > 0)
                                                            @php $disabled = true; @endphp
                                                        @elseif ($noshows > 0)
                                                            @php $disabled = true; @endphp
                                                        @elseif (empty($user->government_issued_id) || empty($user->address))
                                                        @php $disabled = true; @endphp @endif
                                                    @endif
                                                @endif
                                                @if ($disabled) {{ 'line-through' }} @endif
                                                    "
                        @if ($disabled) onclick="event.preventDefault(); {{ $showPinkOrExtraAddressNotice ? 'showPinkExtraAddressRequiredModal' : 'extraCareRideModal' }}();" @endif>
                        {{ $postRidePage->features_option2->name }}
                    </span>
                    @php
                        $extraCareTooltipText = '';
                        if ($user->folks_ride == '0') {
                            $extraCareTooltipText = $postRidePage->extra_care_tooltip_admin_disable_text ?? '';
                        } elseif ($user->folks_ride == '1') {
                            $extraCareTooltipText = $postRidePage->extra_care_tooltip_admin_enable_text ?? '';
                        } else {
                            $extraCareTooltipText =
                                ($postRidePage->extra_care_tooltip_driver_review_text ?? '') .
                                ' ' .
                                (isset($setting) ? $setting->average_rating : '0') .
                                ' ' .
                                ($postRidePage->extra_care_tooltip_greater_age_text ?? '') .
                                ' ' .
                                (isset($setting) ? $setting->driver_age : '0') .
                                ' ' .
                                ($postRidePage->extra_care_tooltip_greater_text ?? '');
                            if (
                                $setting &&
                                ($setting->verfiy_phone === '1' ||
                                    $setting->verify_email === '1' ||
                                    $setting->driver_license === '1' ||
                                    $setting->profile_complete === '1')
                            ) {
                                if (isset($pinkRideSetting) && $pinkRideSetting->profile_complete === '1') {
                                    $extraCareTooltipText .=
                                        ' ' . ($postRidePage->extra_care_tooltip_complete_profile_text ?? '');
                                }
                                $extraCareTooltipText .= ' ' . ($postRidePage->extra_care_tooltip_and_his_text ?? '');
                                if ($setting->verfiy_phone === '1') {
                                    $extraCareTooltipText .=
                                        ' ' . ($postRidePage->extra_care_tooltip_phone_number_text ?? '');
                                }
                                if ($setting->verify_email === '1') {
                                    $extraCareTooltipText .= ' ' . ($postRidePage->extra_care_tooltip_email_text ?? '');
                                }
                                if ($setting->driver_license === '1') {
                                    $extraCareTooltipText .=
                                        ' ' . ($postRidePage->extra_care_tooltip_driver_license_text ?? '');
                                }
                                $extraCareTooltipText .= ' ' . ($postRidePage->extra_care_tooltip_verified_text ?? '');
                            }
                            $extraCareTooltipText .= ' ' . ($postRidePage->extra_care_tooltip_eligible_text ?? '');
                        }
                    @endphp
                    <span class="inline-flex cursor-help"
                        data-tippy-content="{{ $extraCareTooltipText }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                            fill="currentColor" class="bi bi-info-circle-fill text-black"
                            viewBox="0 0 16 16">
                            <path
                                d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
                        </svg>
                    </span>
                </label>
            </div>
            @endif
            @if ($postRidePage->features_option3?->features_setting_id)
                <div class="flex items-start">
                    <input id="wi-fi" type="checkbox" name="features[]"
                        value="{{ $postRidePage->features_option3->features_setting_id }}"
                        {{ $isNewForm
                            ? (in_array($postRidePage->features_option3->features_setting_id, old('features', []))
                                ? 'checked'
                                : '')
                            : (old('features')
                                ? (in_array($postRidePage->features_option3->features_setting_id, old('features', []))
                                    ? 'checked'
                                    : '')
                                : (in_array($postRidePage->features_option3->features_setting_id, explode('=', $ride->features))
                                    ? 'checked'
                                    : '')) }}
                        class="mt-2 w-4 h-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                    <label for="wi-fi" class="ml-2 font-normal text-gray-900 flex space-x-1">
                        <span>
                            {{ $postRidePage->features_option3->name }}
                        </span>
                    </label>
                </div>
            @endif
            @if ($postRidePage->features_option4?->features_setting_id)
                <div class="flex items-start">
                    <input id="rating-passengers" type="checkbox" name="features[]"
                        value="{{ $postRidePage->features_option4->features_setting_id }}"
                        {{ $isNewForm
                            ? (in_array($postRidePage->features_option4->features_setting_id, old('features', []))
                                ? 'checked'
                                : '')
                            : (old('features')
                                ? (in_array($postRidePage->features_option4->features_setting_id, old('features', []))
                                    ? 'checked'
                                    : '')
                                : (in_array($postRidePage->features_option4->features_setting_id, explode('=', $ride->features))
                                    ? 'checked'
                                    : '')) }}
                        class="mt-2 w-4 h-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                    <label for="rating-passengers" class="ml-2 font-normal text-gray-900 flex space-x-1">
                        <span>
                            {{ $postRidePage->features_option4->name }}
                        </span>
                    </label>
                </div>
            @endif
            @if ($postRidePage->features_option5?->features_setting_id)
                <div class="flex items-start">
                    <input id="provide-babyseats" type="checkbox" name="features[]"
                        value="{{ $postRidePage->features_option5->features_setting_id }}"
                        {{ $isNewForm
                            ? (in_array($postRidePage->features_option5->features_setting_id, old('features', []))
                                ? 'checked'
                                : '')
                            : (old('features')
                                ? (in_array($postRidePage->features_option5->features_setting_id, old('features', []))
                                    ? 'checked'
                                    : '')
                                : (in_array($postRidePage->features_option5->features_setting_id, explode('=', $ride->features))
                                    ? 'checked'
                                    : '')) }}
                        class="mt-2 w-4 h-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                    <label for="provide-babyseats" class="ml-2 font-normal text-gray-900 flex space-x-1">
                        <span class="">
                            {{ $postRidePage->features_option5->name }}
                        </span>
                    </label>
                </div>
            @endif
            @if ($postRidePage->features_option6?->features_setting_id)
                <div class="flex items-start">
                    <input id="passenger-provide" type="checkbox" name="features[]"
                        value="{{ $postRidePage->features_option6->features_setting_id }}"
                        {{ $isNewForm
                            ? (in_array($postRidePage->features_option6->features_setting_id, old('features', []))
                                ? 'checked'
                                : '')
                            : (old('features')
                                ? (in_array($postRidePage->features_option6->features_setting_id, old('features', []))
                                    ? 'checked'
                                    : '')
                                : (in_array($postRidePage->features_option6->features_setting_id, explode('=', $ride->features))
                                    ? 'checked'
                                    : '')) }}
                        class="mt-2 w-4 h-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                    <label for="passenger-provide" class="ml-2 font-normal text-gray-900 flex space-x-1">
                        <span class="">
                            {{ $postRidePage->features_option6->name }}
                        </span>
                    </label>
                </div>
            @endif
            @if ($postRidePage->features_option7?->features_setting_id)
                <div class="flex items-start">
                    <input id="take-children" type="checkbox" name="features[]"
                        value="{{ $postRidePage->features_option7->features_setting_id }}"
                        {{ $isNewForm
                            ? (in_array($postRidePage->features_option7->features_setting_id, old('features', []))
                                ? 'checked'
                                : '')
                            : (old('features')
                                ? (in_array($postRidePage->features_option7->features_setting_id, old('features', []))
                                    ? 'checked'
                                    : '')
                                : (in_array($postRidePage->features_option7->features_setting_id, explode('=', $ride->features))
                                    ? 'checked'
                                    : '')) }}
                        class="mt-2 w-4 h-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                    <label for="take-children" class="ml-2 font-normal text-gray-900 flex space-x-1">
                        <span class="">
                            {{ $postRidePage->features_option7->name }}
                        </span>
                    </label>
                </div>
            @endif
            @if ($postRidePage->features_option8?->features_setting_id)
                <div class="flex items-start">
                    <input id="passenger-provide1" type="checkbox" name="features[]"
                        value="{{ $postRidePage->features_option8->features_setting_id }}"
                        {{ $isNewForm
                            ? (in_array($postRidePage->features_option8->features_setting_id, old('features', []))
                                ? 'checked'
                                : '')
                            : (old('features')
                                ? (in_array($postRidePage->features_option8->features_setting_id, old('features', []))
                                    ? 'checked'
                                    : '')
                                : (in_array($postRidePage->features_option8->features_setting_id, explode('=', $ride->features))
                                    ? 'checked'
                                    : '')) }}
                        class="mt-2 w-4 h-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                    <label for="passenger-provide1" class="ml-2 font-normal text-gray-900 flex space-x-1">
                        <span class="">
                            {{ $postRidePage->features_option8->name }}
                        </span>
                    </label>
                </div>
            @endif
            @if ($postRidePage->features_option9?->features_setting_id)
                <div class="flex items-start">
                    <input id="bike-rack" type="checkbox" name="features[]"
                        value="{{ $postRidePage->features_option9->features_setting_id }}"
                        {{ $isNewForm
                            ? (in_array($postRidePage->features_option9->features_setting_id, old('features', []))
                                ? 'checked'
                                : '')
                            : (old('features')
                                ? (in_array($postRidePage->features_option9->features_setting_id, old('features', []))
                                    ? 'checked'
                                    : '')
                                : (in_array($postRidePage->features_option9->features_setting_id, explode('=', $ride->features))
                                    ? 'checked'
                                    : '')) }}
                        class="mt-2 w-4 h-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                    <label for="bike-rack" class="ml-2 font-normal text-gray-900 flex space-x-1">
                        <span class="">
                            {{ $postRidePage->features_option9->name }}
                        </span>
                    </label>
                </div>
            @endif
            @if ($postRidePage->features_option10?->features_setting_id)
                <div class="flex items-start">
                    <input id="ski-rack" type="checkbox" name="features[]"
                        value="{{ $postRidePage->features_option10->features_setting_id }}"
                        {{ $isNewForm
                            ? (in_array($postRidePage->features_option10->features_setting_id, old('features', []))
                                ? 'checked'
                                : '')
                            : (old('features')
                                ? (in_array($postRidePage->features_option10->features_setting_id, old('features', []))
                                    ? 'checked'
                                    : '')
                                : (in_array($postRidePage->features_option10->features_setting_id, explode('=', $ride->features))
                                    ? 'checked'
                                    : '')) }}
                        class="mt-2 w-4 h-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                    <label for="ski-rack" class="ml-2 font-normal text-gray-900 flex space-x-1">
                        <span class="">
                            {{ $postRidePage->features_option10->name }}
                        </span>
                    </label>
                </div>
            @endif
            @if ($postRidePage->features_option11?->features_setting_id)
                <div class="flex items-start">
                    <input id="winter-tires" type="checkbox" name="features[]"
                        value="{{ $postRidePage->features_option11->features_setting_id }}"
                        {{ $isNewForm
                            ? (in_array($postRidePage->features_option11->features_setting_id, old('features', []))
                                ? 'checked'
                                : '')
                            : (old('features')
                                ? (in_array($postRidePage->features_option11->features_setting_id, old('features', []))
                                    ? 'checked'
                                    : '')
                                : (in_array($postRidePage->features_option11->features_setting_id, explode('=', $ride->features))
                                    ? 'checked'
                                    : '')) }}
                        class="mt-2 w-4 h-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                    <label for="winter-tires" class="ml-2 font-normal text-gray-900 flex space-x-1">
                        <span class="">
                            {{ $postRidePage->features_option11->name }}
                        </span>
                    </label>
                </div>
            @endif
            @if ($postRidePage->features_option12?->features_setting_id)
                <div class="flex items-start">
                    <input id="air-conditioning" type="checkbox" name="features[]"
                        value="{{ $postRidePage->features_option12->features_setting_id }}"
                        {{ $isNewForm
                            ? (in_array($postRidePage->features_option12->features_setting_id, old('features', []))
                                ? 'checked'
                                : '')
                            : (old('features')
                                ? (in_array($postRidePage->features_option12->features_setting_id, old('features', []))
                                    ? 'checked'
                                    : '')
                                : (in_array($postRidePage->features_option12->features_setting_id, explode('=', $ride->features))
                                    ? 'checked'
                                    : '')) }}
                        class="mt-2 w-4 h-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                    <label for="air-conditioning" class="ml-2 font-normal text-gray-900 flex space-x-1">
                        <span class="">
                            {{ $postRidePage->features_option12->name }}
                        </span>
                    </label>
                </div>
            @endif
            @if ($postRidePage->features_option13?->features_setting_id)
                <div class="flex items-start">
                    <input id="heating" type="checkbox" name="features[]"
                        value="{{ $postRidePage->features_option13->features_setting_id }}"
                        {{ $isNewForm
                            ? (in_array($postRidePage->features_option13->features_setting_id, old('features', []))
                                ? 'checked'
                                : '')
                            : (old('features')
                                ? (in_array($postRidePage->features_option13->features_setting_id, old('features', []))
                                    ? 'checked'
                                    : '')
                                : (in_array($postRidePage->features_option13->features_setting_id, explode('=', $ride->features))
                                    ? 'checked'
                                    : '')) }}
                        class="mt-2 w-4 h-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                    <label for="heating" class="ml-2 font-normal text-gray-900 flex space-x-1">
                        <span class="">
                            {{ $postRidePage->features_option13->name }}
                        </span>
                    </label>
                </div>
            @endif
            @if ($postRidePage->features_option14?->features_setting_id)
                <div class="flex items-start">
                    <input id="heating" type="checkbox" name="features[]"
                        value="{{ $postRidePage->features_option14->features_setting_id }}"
                        {{ $isNewForm
                            ? (in_array($postRidePage->features_option14->features_setting_id, old('features', []))
                                ? 'checked'
                                : '')
                            : (old('features')
                                ? (in_array($postRidePage->features_option14->features_setting_id, old('features', []))
                                    ? 'checked'
                                    : '')
                                : (in_array($postRidePage->features_option14->features_setting_id, explode('=', $ride->features))
                                    ? 'checked'
                                    : '')) }}
                        class="mt-2 w-4 h-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                    <label for="heating" class="ml-2 font-normal text-gray-900 flex space-x-1">
                        <span class="">
                            {{ $postRidePage->features_option14->name }}
                        </span>
                    </label>
                </div>
            @endif
            @if ($postRidePage->features_option15?->features_setting_id)
                <div class="flex items-start">
                    <input id="heating" type="checkbox" name="features[]"
                        value="{{ $postRidePage->features_option15->features_setting_id }}"
                        {{ $isNewForm
                            ? (in_array($postRidePage->features_option15->features_setting_id, old('features', []))
                                ? 'checked'
                                : '')
                            : (old('features')
                                ? (in_array($postRidePage->features_option15->features_setting_id, old('features', []))
                                    ? 'checked'
                                    : '')
                                : (in_array($postRidePage->features_option15->features_setting_id, explode('=', $ride->features))
                                    ? 'checked'
                                    : '')) }}
                        class="mt-2 w-4 h-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                    <label for="heating" class="ml-2 font-normal text-gray-900 flex space-x-1">
                        <span class="">
                            {{ $postRidePage->features_option15->name }}
                        </span>
                    </label>
                </div>
            @endif
            @if ($postRidePage->features_option16?->features_setting_id)
                <div class="flex items-start">
                    <input id="heating" type="checkbox" name="features[]"
                        value="{{ $postRidePage->features_option16->features_setting_id }}"
                        {{ $isNewForm
                            ? (in_array($postRidePage->features_option16->features_setting_id, old('features', []))
                                ? 'checked'
                                : '')
                            : (old('features')
                                ? (in_array($postRidePage->features_option16->features_setting_id, old('features', []))
                                    ? 'checked'
                                    : '')
                                : (in_array($postRidePage->features_option16->features_setting_id, explode('=', $ride->features))
                                    ? 'checked'
                                    : '')) }}
                        class="mt-2 w-4 h-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                    <label for="heating" class="ml-2 font-normal text-gray-900 flex space-x-1">
                        <span class="">
                            {{ $postRidePage->features_option16->name }}
                        </span>
                    </label>
                </div>
            @endif
            @if ($postRidePage->features_option18?->features_setting_id)
                <div class="flex items-start">
                    <input id="heating" type="checkbox" name="features[]"
                        value="{{ $postRidePage->features_option18->features_setting_id }}"
                        {{ $isNewForm
                            ? (in_array($postRidePage->features_option18->features_setting_id, old('features', []))
                                ? 'checked'
                                : '')
                            : (old('features')
                                ? (in_array($postRidePage->features_option18->features_setting_id, old('features', []))
                                    ? 'checked'
                                    : '')
                                : (in_array($postRidePage->features_option18->features_setting_id, explode('=', $ride->features))
                                    ? 'checked'
                                    : '')) }}
                        class="mt-2 w-4 h-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                    <label for="heating" class="ml-2 font-normal text-gray-900 flex space-x-1">
                        <span class="">
                            {{ $postRidePage->features_option18->name }}
                        </span>
                    </label>
                </div>
            @endif
        </div>
    </div>
</div>
</div>

</div>
<div class="">
<div class="mt-6">

<div class=" mt-6">
    <div class="bg-white rounded-lg overflow-hidden shadow-3xl">
        <div class="text-2xl bg-primary text-white py-2 px-4">
            <label for="more" class="">
                <h3 class="text-2xl">
                    @isset($postRidePage->anything_to_add_label)
                        {{ $postRidePage->anything_to_add_label }}
                    @endisset
                </h3>
            </label>
        </div>
        <div class="bg-white p-4">
            <textarea id="more" rows="5" name="notes"
                class="block p-2.5 w-full mt-2 text-gray-900 bg-gray-100 text-base lg:text-lg rounded border border-gray-200 focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500"
                @isset($postRidePage->anything_to_add_placeholder)
                                    placeholder="{{ $postRidePage->anything_to_add_placeholder }}"
                                @endisset>{{ old('notes', $ride->notes) }}</textarea>
            @error('notes')
                <div class="absolute mt-1 z-10">
                    <div class="tooltip-error shadow-lg rounded p-2 bg-red-500 text-white text-sm lg:text-base">
                        {{ $message }}</div>
                </div>
            @enderror
        </div>
    </div>
</div>

<div class="mt-6">
    <div class="bg-white rounded-lg overflow-hidden shadow-3xl">
        <div class="text-2xl bg-primary text-white py-2 px-4">
            <h3 class="text-2xl">
                @isset($postRidePage->disclaimers_label)
                    {{ $postRidePage->disclaimers_label }}
                @endisset
                <span class="text-white">*</span>
            </h3>
        </div>
        <div class="bg-white p-4">
            @isset($postRidePage->disclaimers_description)
                {!! str_replace(
                    '<ol>',
                    '<ol class="list-decimal list-inside">',
                    str_replace(
                        '<li>',
                        '<li class="border-b border-gray-300 text-base lg:text-lg last:border-b-0 py-3">',
                        $postRidePage->disclaimers_description,
                    ),
                ) !!}
            @endisset
        </div>
        @php
            $pinkRideChecked = false;
            $extraCareRideChecked = false;
        @endphp
        @if ($postRidePage->features_option1?->features_setting_id)
            @php
                $pinkFeatureId = $postRidePage->features_option1->features_setting_id;
                $featuresArray = $isNewForm
                    ? old('features', [])
                    : (old('features') ?:
                    (isset($ride->features)
                        ? explode('=', $ride->features)
                        : []));
                $pinkRideChecked = is_array($featuresArray) && in_array($pinkFeatureId, $featuresArray);
            @endphp
            <div id="pink-ride-disclaimer"
                class="bg-white p-4 border-t border-gray-200 {{ $pinkRideChecked ? '' : 'hidden' }}">
                <p class="border-gray-300 text-base lg:text-lg py-3 text-gray-900">
                    <!-- {{ $postRidePage->pink_ride_disclaimer_text ?? 'I understand that this is a Pink Ride, exclusive to female members. I will not send a male driver in my place and will not accept any male passengers over 12 years old, even if the booking is made by a female.' }} -->
                    5. I understand that this is a Pink Ride, exclusive to female members. I will not send a
                    male driver in my place and will not accept any male passengers over 12 years old, even if
                    the booking is made by a female.
                </p>
            </div>
        @endif
        @if ($postRidePage->features_option2?->features_setting_id)
            @php
                $extraCareFeatureId = $postRidePage->features_option2->features_setting_id;
                $featuresArray = $isNewForm
                    ? old('features', [])
                    : (old('features') ?:
                    (isset($ride->features)
                        ? explode('=', $ride->features)
                        : []));
                $extraCareRideChecked = is_array($featuresArray) && in_array($extraCareFeatureId, $featuresArray);

            @endphp
            <div id="Extra+-ride-disclaimer"
                class="bg-white p-4 border-t border-gray-200 {{ $extraCareRideChecked ? '' : 'hidden' }}">
                <p class="border-gray-300 text-base lg:text-lg py-3 text-gray-900">
                    <!-- {{ $postRidePage->extra_care_ride_disclaimer_text ?? 'I understand that this is an Extra+ Ride, exclusive to members with highest review score. I will adhere to its standards' }} -->
                    <span id="extra-care-disclaimer-number">{{ $pinkRideChecked ? '6.' : '5.' }}</span>
                    I understand that this is an Extra+ Ride, exclusively for members with top-tier review
                    ratings. I commit to upholding the exceptional professionalism and courtesy that earned me
                    this rating, keeping my vehicle immaculate, driving safely and smoothly as always, and
                    ensuring a calm, respectful environment by preventing any passenger disputes.
                </p>
            </div>
        @endif
    </div>
</div>

<div class="mt-4">

    <div class="flex items-start my-4">
        <input type="hidden" name="agree_terms" value="0">
        <input id="agree_terms" type="checkbox" name="agree_terms" value="1"
            {{ old('agree_terms') == '1' ? 'checked' : '' }}
            class="w-4 h-4 mt-3 text-blue-600 cursor-pointer bg-white border-gray-500 rounded focus:ring-blue-500  focus:ring-2">
        <label for="agree_terms"
            class="ml-2 font-normal text-gray-900 flex text-md items-center space-x-0.5">
            @isset($postRidePage->agree_terms_label)
                {!! $postRidePage->agree_terms_label !!}
            @endisset
            <span class="text-red-500">*</span>
        </label>
    </div>
    <div id="agree_terms_client_error" class="hidden absolute mt-1 z-10">
        <div class="tooltip-error shadow-lg rounded p-2 bg-red-500 text-white text-sm lg:text-base">Please
            check this box to continue.</div>
    </div>
    @error('agree_terms')
        <div class="absolute mt-1 z-10">
            <div class="tooltip-error shadow-lg rounded p-2 bg-red-500 text-white text-sm lg:text-base"> {{ $postRidePage->agree_term_error ?? 'Please accept our Terms and Conditions' }}</div>
        </div>
    @enderror
    <div class="hidden lg:flex justify-center items-center mt-8">
        <button
            class="post-ride-submit-btn bg-greenXS hover:bg-greenXS text-white text-base md:text-lg rounded font-FuturaMdCnBT hover:font-FuturaMdCnBT px-5 py-2 border border-greenXS hover:border-greenXS hover:text-white text-center focus:bg-greenXS focus:text-white active:text-white active:bg-greenXS disabled:opacity-70 disabled:cursor-not-allowed"
            type="submit">
            @if (($routeType ?? '') === 'repost')
            {{ $postRidePage->update_ride_label ?? 'Update Ride' }}
            @else
                @isset($postRidePage->submit_button_label)
                    {{ $postRidePage->submit_button_label }}
                @endisset
            @endif
        </button>
    </div>
</div>
</div>
<div class="flex lg:hidden justify-center items-center mt-8">
<button
    class="post-ride-submit-btn bg-greenXS hover:bg-greenXS text-white text-base md:text-lg rounded font-FuturaMdCnBT hover:font-FuturaMdCnBT px-5 py-2 border border-greenXS hover:border-greenXS hover:text-white text-center focus:bg-greenXS focus:text-white active:text-white active:bg-greenXS disabled:opacity-70 disabled:cursor-not-allowed"
    type="submit">
    @if (($routeType ?? '') === 'repost')
        {{ $postRidePage->update_ride_label ?? 'Update Ride' }}
    @else
        @isset($postRidePage->submit_button_label)
            {{ $postRidePage->submit_button_label }}
        @endisset
    @endif
</button>
</div>
</form>
</div>

@php
    $projectTimezone = config('app.timezone');
    $projectOffset = \Carbon\Carbon::now($projectTimezone)->offsetHours;
@endphp

@endsection

@section('script')
{{-- <script async
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAadtOhXUj_mb2QWOD1mCPYPRujBiQO4nE&libraries=places&callback=initMap">
</script> --}}

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<script>
    // Google Places (from/to) – used when API loads
    var selectedFromPlace = null;
    var selectedToPlace = null;
    var geocoderPostRide = null;
    var fromAutocompletePostRide = null;
    var toAutocompletePostRide = null;
    var isSettingPlaceValuePostRide = false;
    var isSelectingFromDropdownPostRide = false;
    var errorFromRequiredPostRide = 'The origin is required';
    var errorToRequiredPostRide = 'The destination is required';
    var errorCityMissingPostRide = 'We could not find this city name in our records, please double-check the spelling.';

    // Define the handler function
    function extraCareRideModal(parms) {
        document.getElementById('Extra+-ride-modal').classList.remove('hidden');
    }

    function closeExtraCareRideModal(parms) {
        document.getElementById('Extra+-ride-modal').classList.add('hidden');
    }

    function showPinkExtraAddressRequiredModal() {
        var el = document.getElementById('pink-extra-address-required-modal');
        if (el) el.classList.remove('hidden');
    }

    function closePinkExtraAddressRequiredModal() {
        var el = document.getElementById('pink-extra-address-required-modal');
        if (el) el.classList.add('hidden');
    }


    function hideTooltip(parms) {
        // Hide validation tooltips (wrapper is parent of .tooltip-error)
        if (parms === 'label') return;
        var $el = $(this);
        var $wrappers = $el.parent().find('.tooltip-error').parent();
        if ($wrappers.length === 0) $wrappers = $el.parent().parent().find('.tooltip-error').parent();
        if ($wrappers.length === 0) $wrappers = $el.parent().parent().parent().find('.tooltip-error').parent();
        // Do not hide From/To tooltips when the user is typing in another field (they have their own input/focus handlers)
        if (this.id !== 'from_spot_0' && this.id !== 'to_spot_0') {
            $wrappers = $wrappers.not('#fromInputError, #toInputError');
        }
        // Do not hide stop tooltips when the user is in another field (same as From/To — only hide when interacting with that stop's input)
        var currentStopIndex = null;
        if (this.id && this.id.indexOf('stop_spot_') === 0) currentStopIndex = this.id.replace('stop_spot_', '');
        else {
            var dataIdx = this.getAttribute('data-stop-index');
            if (dataIdx !== null && dataIdx !== '') currentStopIndex = dataIdx;
        }
        $wrappers = $wrappers.filter(function() {
            var id = this.id;
            if (!id || id.indexOf('stopInputError_') !== 0) return true;
            var stopIdx = id.replace('stopInputError_', '');
            return currentStopIndex !== null && String(stopIdx) === String(currentStopIndex);
        });
        if ($wrappers.length > 0) $wrappers.addClass('hidden');
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
        // Hide all modals
        document.querySelectorAll('.relative.z-50').forEach(modal => {
            modal.style.display = 'none';
        });

        // Also remove any session messages from the URL
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

    // Close modal with Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeModal();
        }
    });

    function scrollToFirstError() {
        const errorInner = document.querySelectorAll('.tooltip-error');
        const wrappers = Array.from(errorInner).map(el => el.parentElement).filter(Boolean);

        let firstVisible = null;
        wrappers.forEach(el => {
            if (firstVisible || !el) return;
            if (el.classList.contains('hidden')) return;
            const rect = el.getBoundingClientRect();
            if (rect.width > 0 && rect.height > 0) firstVisible = el;
        });

        if (firstVisible) {
            firstVisible.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
            const rel = firstVisible.closest('.relative');
            const relatedInput = rel ? rel.querySelector('input, select, textarea') : firstVisible
                .previousElementSibling?.querySelector('input, select, textarea');
            if (relatedInput) relatedInput.focus();
        }
    }

    // Add a small delay to ensure dynamic content is loaded
    document.addEventListener('DOMContentLoaded', function() {
        @if ($errors->any() || session('error'))
            setTimeout(scrollToFirstError, 300);
        @endif
    });

    // Cost-sharing cap validation constants
    const ERROR_TRIGGERING_CAP = 0.72; // $0.72 per km - BLOCK if exceeded
    const SOFT_WARNING_CAP = 0.66; // $0.66 per km - WARN but ALLOW

    // Store distance globally when fetched
    window.rideDistance = null;

    // Function to validate price per seat
    // Formula: (Distance × Cap) ÷ Seats = Max price per seat
    function validatePricePerSeat(price, distance, seats) {
        if (!price || !distance || !seats || distance <= 0 || price <= 0 || seats <= 0) {
            console.log('Validation skipped - missing data:', {
                price,
                distance,
                seats
            });
            return {
                valid: true,
                type: null
            };
        }

        const pricePerSeat = parseFloat(price);
        const distanceKm = parseFloat(distance);
        const numSeats = parseInt(seats);

        // Calculate max allowed price per seat using Error-Triggering Cap: $0.72/km
        const maxPricePerSeat = (distanceKm * ERROR_TRIGGERING_CAP) / numSeats;

        // Calculate soft warning price per seat: $0.66/km
        const softWarningPricePerSeat = (distanceKm * SOFT_WARNING_CAP) / numSeats;

        console.log('Price validation calculations:', {
            pricePerSeat: pricePerSeat,
            distanceKm: distanceKm,
            numSeats: numSeats,
            maxPricePerSeat: maxPricePerSeat,
            softWarningPricePerSeat: softWarningPricePerSeat,
            exceedsMax: pricePerSeat > maxPricePerSeat,
            exceedsSoftWarning: pricePerSeat > softWarningPricePerSeat
        });

        // Error-Triggering Cap: $0.72 per km - BLOCK if exceeded
        if (pricePerSeat > maxPricePerSeat) {
            console.log('ERROR CAP TRIGGERED');
            return {
                valid: false,
                type: 'error',
                maxPricePerSeat: maxPricePerSeat.toFixed(2)
            };
        }

        // Soft Warning Cap: $0.66 per km - WARN but ALLOW
        // Check if price is ABOVE (greater than) the soft warning cap
        // Note: If price equals soft warning, no warning is shown (user requirement: "above")
        if (pricePerSeat > softWarningPricePerSeat) {
            console.log('SOFT WARNING CAP TRIGGERED - Price exceeds soft warning');
            console.log('Price per seat:', pricePerSeat, '> Soft warning:', softWarningPricePerSeat);
            return {
                valid: true,
                type: 'warning',
                softWarningPrice: softWarningPricePerSeat.toFixed(2)
            };
        }

        console.log('No warning or error - price is within limits');
        return {
            valid: true,
            type: null
        };
    }

    // Function to show error modal (Price Limit Exceeded)
    function showPriceErrorModal(maxPricePerSeat) {
        const modal = document.getElementById('priceErrorModal');
        if (modal) {
            // Set the three paragraphs as specified
            document.getElementById('priceErrorParagraph1').textContent =
                'To comply with Canadian and Quebec carpooling regulations, the total amount collected for a trip cannot exceed the official 2026 reimbursement rate of $0.72/km.';
            document.getElementById('priceErrorParagraph2').textContent =
                'The maximum allowed for this trip is $' + maxPricePerSeat + ' per seat.';
            document.getElementById('priceErrorParagraph3').textContent =
                'This limit is mandatory to ensure your ride is classified as a non-commercial carpool, protecting your insurance coverage and maintaining the cost-sharing status of your contributions.';

            modal.classList.remove('hidden');
            modal.style.display = 'block';
        }
    }

    // Function to show warning modal (Recommended Contribution Limit)
    function showPriceWarningModal(callback) {
        console.log('showPriceWarningModal called');
        const modal = document.getElementById('priceWarningModal');
        if (!modal) {
            console.error('Price warning modal not found!');
            return;
        }

        // Set the two paragraphs as specified
        const para1 = document.getElementById('priceWarningParagraph1');
        const para2 = document.getElementById('priceWarningParagraph2');

        if (para1) {
            para1.textContent =
                'The price you entered is above the standard reimbursement rate recommended by the CRA and Revenu Québec';
        }
        if (para2) {
            para2.textContent =
                'While you can proceed, we suggest reducing the price per seat. This ensures your ride remains a standard carpool even if you drive long distances this year.';
        }

        // Show the modal - ensure it's visible
        // Remove hidden class and set display with !important to override Tailwind CSS
        modal.classList.remove('hidden');
        modal.style.setProperty('display', 'block', 'important');
        modal.style.setProperty('visibility', 'visible', 'important');
        modal.style.setProperty('opacity', '1', 'important');
        modal.style.setProperty('z-index', '50', 'important');

        console.log('Modal should be visible now', {
            hasClassHidden: modal.classList.contains('hidden'),
            display: modal.style.display,
            computedDisplay: window.getComputedStyle(modal).display,
            visibility: window.getComputedStyle(modal).visibility
        });

        // Wire "Keep Current Price" button: set onclick directly so it works every time (cloning would drop the handler on reopen)
        const continueBtn = document.getElementById('priceWarningContinue');
        if (continueBtn) {
            continueBtn.onclick = function(e) {
                e.preventDefault();
                e.stopPropagation();
                modal.classList.add('hidden');
                modal.style.setProperty('display', 'none', 'important');
                if (typeof callback === 'function') {
                    callback();
                }
            };
        } else {
            console.error('Continue button not found!');
        }
    }

    // Get the active price input (single or full-route in segment mode)
    function getActivePriceInput() {
        const dynamicBlock = document.getElementById('stops-segment-prices-dynamic');
        const isSegmentMode = dynamicBlock && dynamicBlock.offsetParent !== null && dynamicBlock.style.display !== 'none';
        if (isSegmentMode) {
            return document.getElementById('priceData0DynamicInput');
        }
        return document.getElementById('priceData0');
    }

    // When modal was triggered by a segment (stop) price, focus that input on "Adjust Price"
    window.lastSegmentPriceInputForModal = null;

    // Function to adjust price (focus on price input field)
    function adjustPriceFromError() {
        const modal = document.getElementById('priceErrorModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.style.display = 'none';
        }
        const priceInput = window.lastSegmentPriceInputForModal || getActivePriceInput();
        window.lastSegmentPriceInputForModal = null;
        if (priceInput) {
            priceInput.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
            setTimeout(() => {
                priceInput.focus();
                priceInput.select();
            }, 300);
        }
    }

    // Function to adjust price from warning (focus on price input field)
    // This should NOT submit the form - just close popup and focus on price field
    function adjustPriceFromWarning() {
        console.log('Adjust Price clicked - closing modal and focusing on price field (NOT submitting form)');
        const modal = document.getElementById('priceWarningModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.style.display = 'none';
        }
        const priceInput = window.lastSegmentPriceInputForModal || getActivePriceInput();
        window.lastSegmentPriceInputForModal = null;
        if (priceInput) {
            priceInput.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
            setTimeout(() => {
                priceInput.focus();
                priceInput.select();
            }, 300);
        }
        return false;
    }

    function closePriceErrorModal() {
        const modal = document.getElementById('priceErrorModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.style.display = 'none';
        }
    }

    function closePriceWarningModal() {
        const modal = document.getElementById('priceWarningModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.style.display = 'none';
        }
    }

    // Validate a single segment (stop) price against that segment's route distance; show error/warning popup if exceeded.
    function runSegmentPriceValidationOnInput(segmentInputEl) {
        if (!segmentInputEl || segmentInputEl.name !== 'price_spot_display[]') return;
        const price = parseFloat(segmentInputEl.value);
        if (!price || price <= 0) return;
        const from = segmentInputEl.getAttribute('data-segment-from');
        const to = segmentInputEl.getAttribute('data-segment-to');
        if (!from || !to) return;
        const seatsInput = document.querySelector('input[name="seats"]:checked');
        const seats = seatsInput ? parseInt(seatsInput.value) : 0;
        if (!seats || seats <= 0) return;

        function doValidate(distanceKm) {
            if (!distanceKm || distanceKm <= 0) return;
            const validation = validatePricePerSeat(price, distanceKm, seats);
            if (!validation.valid) {
                window.lastSegmentPriceInputForModal = segmentInputEl;
                showPriceErrorModal(validation.maxPricePerSeat);
                return;
            }
            if (validation.type === 'warning') {
                window.lastSegmentPriceInputForModal = segmentInputEl;
                showPriceWarningModal(function() {
                    closePriceWarningModal();
                });
            }
        }

        const cachedDist = segmentInputEl.getAttribute('data-segment-distance');
        if (cachedDist) {
            const distanceKm = parseFloat(cachedDist);
            doValidate(distanceKm);
            return;
        }
        if (typeof $ === 'undefined') return;
        $.ajax({
            url: '{{ url("get-cities-distance") }}',
            type: 'POST',
            data: {
                search: from,
                searchData: to,
                _token: '{{ csrf_token() }}'
            },
            dataType: 'json',
            success: function(result) {
                const distanceValue = result.distance || (result.data && result.data.distance) || null;
                if (distanceValue && parseFloat(distanceValue) > 0) {
                    const distanceKm = parseFloat(distanceValue);
                    segmentInputEl.setAttribute('data-segment-distance', distanceKm);
                    doValidate(distanceKm);
                }
            }
        });
    }

    // Handle form submission errors
    // Wait for DOM to be ready
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('post-ride-form') || document.querySelector(
            'form[method="POST"]') || document.querySelector('form');
        if (!form) {
            console.error('Form not found!');
            return;
        }

        // Prevent Enter key from submitting the form (allow Enter in textareas for new lines)
        form.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
                e.preventDefault();
            }
        });

        // Prevent double submit: disable submit buttons on submit, re-enable only if validation fails
        function disablePostRideSubmitButtons() {
            form.querySelectorAll('.post-ride-submit-btn').forEach(function(btn) {
                btn.disabled = true;
            });
        }

        function enablePostRideSubmitButtons() {
            form.querySelectorAll('.post-ride-submit-btn').forEach(function(btn) {
                btn.disabled = false;
            });
        }

        // When driver inputs an exceeded price, show the error or warning popup (Exceeds $0.72/km or $0.66–$0.72/km).
        // In segment mode: validate segment total first; if none, validate full-route field. Otherwise single price.
        function runPriceValidationOnInput() {
            const dynamicBlock = document.getElementById('stops-segment-prices-dynamic');
            const isSegmentMode = dynamicBlock && dynamicBlock.offsetParent !== null && dynamicBlock.style.display !== 'none';
            const priceInput = document.getElementById('priceData0');
            const dynamicPriceInput = document.getElementById('priceData0DynamicInput');
            // In segment mode the full-route input gets id="priceData0", so resolve by class when needed
            const fullRouteInput = dynamicPriceInput || (dynamicBlock ? dynamicBlock.querySelector('.full-route-price-input') : null) || priceInput;
            let price = null;
            if (isSegmentMode) {
                const segmentTotalEl = document.getElementById('segment-total-price-input-dynamic');
                let segmentTotal = 0;
                if (segmentTotalEl && segmentTotalEl.value) {
                    segmentTotal = parseFloat(segmentTotalEl.value) || 0;
                }
                if (segmentTotal <= 0 && dynamicBlock) {
                    const segmentInputs = dynamicBlock.querySelectorAll('input[name="price_spot_display[]"]');
                    segmentInputs.forEach(function(inp) {
                        const v = parseFloat(inp.value);
                        if (!isNaN(v)) segmentTotal += v;
                    });
                }
                if (segmentTotal > 0) {
                    price = segmentTotal;
                } else if (fullRouteInput && fullRouteInput.value) {
                    const fullRoutePrice = parseFloat(fullRouteInput.value);
                    const seatsInput = document.querySelector('input[name="seats"]:checked');
                    const seatsVal = seatsInput ? parseInt(seatsInput.value) : 0;
                    price = (seatsVal > 0 && !isNaN(fullRoutePrice)) ? fullRoutePrice / seatsVal : null;
                }
            } else if (priceInput && priceInput.value) {
                price = parseFloat(priceInput.value);
            }
            if (!price || price <= 0) return;
            let distance = null;
            const distSource = priceInput || fullRouteInput;
            if (distSource && typeof $ !== 'undefined') {
                distance = $(distSource).data('distance') || window.rideDistance;
            } else {
                distance = window.rideDistance;
            }
            if (!distance || distance <= 0) {
                const distanceInput = document.querySelector('input[name="distance"], input[id*="distance"]');
                if (distanceInput && distanceInput.value) {
                    distance = parseFloat(distanceInput.value);
                }
            }
            const seatsInput = document.querySelector('input[name="seats"]:checked');
            const seats = seatsInput ? parseInt(seatsInput.value) : 0;
            if (!distance || distance <= 0 || !seats || seats <= 0) return;
            const validation = validatePricePerSeat(price, distance, seats);
            if (!validation.valid) {
                if (window.lastSegmentPriceInputForModal !== undefined) window.lastSegmentPriceInputForModal = null;
                showPriceErrorModal(validation.maxPricePerSeat);
                return;
            }
            if (validation.type === 'warning') {
                if (window.lastSegmentPriceInputForModal !== undefined) window.lastSegmentPriceInputForModal = null;
                showPriceWarningModal(function() {
                    closePriceWarningModal();
                });
            }
        }

        var priceValidationDebounce = null;
        function schedulePriceValidationOnInput() {
            if (priceValidationDebounce) clearTimeout(priceValidationDebounce);
            priceValidationDebounce = setTimeout(runPriceValidationOnInput, 600);
        }

        var segmentPriceValidationDebounce = null;
        function scheduleSegmentPriceValidationOnInput(segmentInputEl) {
            if (segmentPriceValidationDebounce) clearTimeout(segmentPriceValidationDebounce);
            segmentPriceValidationDebounce = setTimeout(function() {
                if (typeof runSegmentPriceValidationOnInput === 'function') runSegmentPriceValidationOnInput(segmentInputEl);
            }, 600);
        }

        const priceInputEl = document.getElementById('priceData0');
        if (priceInputEl) {
            priceInputEl.addEventListener('input', schedulePriceValidationOnInput);
            priceInputEl.addEventListener('change', runPriceValidationOnInput);
        }
        const dynamicPriceInputEl = document.getElementById('priceData0DynamicInput');
        if (dynamicPriceInputEl) {
            dynamicPriceInputEl.addEventListener('input', schedulePriceValidationOnInput);
            dynamicPriceInputEl.addEventListener('change', runPriceValidationOnInput);
        }

        // When driver edits any segment (stop) price: update total, run full-total validation, and run per-segment validation (popup for individual route distance)
        form.addEventListener('input', function(e) {
            if (e.target && e.target.name === 'price_spot_display[]') {
                if (typeof updateSegmentTotalPricePostRide === 'function') updateSegmentTotalPricePostRide();
                schedulePriceValidationOnInput();
                scheduleSegmentPriceValidationOnInput(e.target);
            }
            if (e.target && (e.target.classList.contains('full-route-price-input') || e.target.id === 'priceData0DynamicInput')) {
                schedulePriceValidationOnInput();
            }
            if (e.target && (e.target.name === 'stop_date[]' || e.target.name === 'stop_time[]')) {
                var id = e.target.id || '';
                var idx = id.replace('stop_date_', '').replace('stop_time_', '');
                if (idx && id !== idx) {
                    var errId = (e.target.name === 'stop_date[]') ? 'stopDateError_' + idx : 'stopTimeError_' + idx;
                    var errEl = document.getElementById(errId);
                    if (errEl) errEl.classList.add('hidden');
                }
            }
            if (e.target && e.target.name === 'stop_pickup_dropoff[]') {
                var id = e.target.id || '';
                var idx = id.indexOf('stop_pickup_dropoff_') === 0 ? id.replace('stop_pickup_dropoff_', '') : '';
                if (idx) {
                    var errEl = document.getElementById('stopPickupDropoffError_' + idx);
                    if (errEl) errEl.classList.add('hidden');
                }
            }
        });
        form.addEventListener('change', function(e) {
            if (e.target && e.target.name === 'price_spot_display[]') {
                if (typeof updateSegmentTotalPricePostRide === 'function') updateSegmentTotalPricePostRide();
                runPriceValidationOnInput();
                if (typeof runSegmentPriceValidationOnInput === 'function') runSegmentPriceValidationOnInput(e.target);
            }
            if (e.target && (e.target.classList.contains('full-route-price-input') || e.target.id === 'priceData0DynamicInput')) {
                runPriceValidationOnInput();
            }
            if (e.target && (e.target.name === 'stop_date[]' || e.target.name === 'stop_time[]')) {
                var id = e.target.id || '';
                var idx = id.replace('stop_date_', '').replace('stop_time_', '');
                if (idx && id !== idx) {
                    var errId = (e.target.name === 'stop_date[]') ? 'stopDateError_' + idx : 'stopTimeError_' + idx;
                    var errEl = document.getElementById(errId);
                    if (errEl) errEl.classList.add('hidden');
                }
            }
            if (e.target && e.target.name === 'stop_pickup_dropoff[]') {
                var id = e.target.id || '';
                var idx = id.indexOf('stop_pickup_dropoff_') === 0 ? id.replace('stop_pickup_dropoff_', '') : '';
                if (idx) {
                    var errEl = document.getElementById('stopPickupDropoffError_' + idx);
                    if (errEl) errEl.classList.add('hidden');
                }
            }
        });

        // Add our submit handler with capture phase to run first
        form.addEventListener('submit', function(e) {
            disablePostRideSubmitButtons();
            try {
                console.log('Form submit event triggered - starting validation');
                var hasAnyValidationError = false;
                var firstErrorElement = null;

                // 1. Block submit if From/To tooltip errors are visible
                var fromInputError = document.getElementById('fromInputError');
                var toInputError = document.getElementById('toInputError');
                if (fromInputError && !fromInputError.classList.contains('hidden')) {
                    hasAnyValidationError = true;
                    if (!firstErrorElement) firstErrorElement = document.getElementById('from_spot_0');
                }
                if (toInputError && !toInputError.classList.contains('hidden')) {
                    hasAnyValidationError = true;
                    if (!firstErrorElement) firstErrorElement = document.getElementById('to_spot_0');
                }

                // 3. Validate stop inputs
                var stopsContainer = document.getElementById('stops-rows-container');
                var stopInputs = stopsContainer ? stopsContainer.querySelectorAll(
                    'input[name="stop_spot_display[]"]') : [];
                var firstInvalidStop = null;
                var stopInvalid = false;
                stopInputs.forEach(function(inp) {
                    var err = typeof getStopErrorElementPostRide === 'function' ?
                        getStopErrorElementPostRide(inp) : null;
                    if (err) err.classList.add('hidden');
                    if (!inp.value || !inp.value.trim()) {
                        stopInvalid = true;
                        if (err) {
                            var te = err.querySelector('.tooltip-error');
                            if (te) te.textContent = 'Please enter or select a city.';
                            err.classList.remove('hidden');
                        }
                        if (!firstInvalidStop) firstInvalidStop = inp;
                    }
                });
                if (stopInvalid) {
                    hasAnyValidationError = true;
                    if (!firstErrorElement) firstErrorElement = firstInvalidStop;
                }

                // 3b. Validate stop date, time, and pickup/dropoff when stop city is filled
                var stopRows = stopsContainer ? stopsContainer.querySelectorAll('.stop-row') : [];
                stopRows.forEach(function(row) {
                    var dataIndex = row.getAttribute('data-stop-index');
                    var dateErr = dataIndex ? document.getElementById('stopDateError_' + dataIndex) : null;
                    var timeErr = dataIndex ? document.getElementById('stopTimeError_' + dataIndex) : null;
                    var pickupDropoffErr = dataIndex ? document.getElementById('stopPickupDropoffError_' + dataIndex) : null;
                    if (dateErr) dateErr.classList.add('hidden');
                    if (timeErr) timeErr.classList.add('hidden');
                    if (pickupDropoffErr) pickupDropoffErr.classList.add('hidden');
                });
                stopRows.forEach(function(row) {
                    var cityInp = row.querySelector('input[name="stop_spot_display[]"]');
                    var dateInp = row.querySelector('input[name="stop_date[]"]');
                    var timeInp = row.querySelector('input[name="stop_time[]"]');
                    var pickupDropoffInp = row.querySelector('textarea[name="stop_pickup_dropoff[]"]');
                    var cityVal = cityInp ? (cityInp.value || '').trim() : '';
                    if (!cityVal) return;
                    var dataIndex = row.getAttribute('data-stop-index');
                    var dateErr = dataIndex ? document.getElementById('stopDateError_' + dataIndex) : null;
                    var timeErr = dataIndex ? document.getElementById('stopTimeError_' + dataIndex) : null;
                    var pickupDropoffErr = dataIndex ? document.getElementById('stopPickupDropoffError_' + dataIndex) : null;
                    var dateVal = dateInp ? (dateInp.value || '').trim() : '';
                    var timeVal = timeInp ? (timeInp.value || '').trim() : '';
                    var pickupDropoffVal = pickupDropoffInp ? (pickupDropoffInp.value || '').trim() : '';
                    if (!pickupDropoffVal) {
                        hasAnyValidationError = true;
                        if (pickupDropoffErr) {
                            var pd = pickupDropoffErr.querySelector('.tooltip-error');
                            if (pd) pd.textContent = 'Please enter pick up / drop off details.';
                            pickupDropoffErr.classList.remove('hidden');
                        }
                        if (!firstErrorElement && pickupDropoffInp) firstErrorElement = pickupDropoffInp;
                    }
                    if (!dateVal) {
                        hasAnyValidationError = true;
                        if (dateErr) {
                            var dt = dateErr.querySelector('.tooltip-error');
                            if (dt) dt.textContent = 'Please enter the stop date.';
                            dateErr.classList.remove('hidden');
                        }
                        if (!firstErrorElement && dateInp) firstErrorElement = dateInp;
                    }
                    if (!timeVal) {
                        hasAnyValidationError = true;
                        if (timeErr) {
                            var tt = timeErr.querySelector('.tooltip-error');
                            if (tt) tt.textContent = 'Please enter the stop time.';
                            timeErr.classList.remove('hidden');
                        }
                        if (!firstErrorElement && timeInp) firstErrorElement = timeInp;
                    }
                });

                // 4. Check HTML5 validation (date, time, seats, required fields, etc.) so we show all errors
                // Use the first *visible* invalid element so we don't scroll to hidden sections (e.g. recurring_trips when Recurring is unchecked)
                var firstHtml5Invalid = null;
                var recurringDetails = document.getElementById('recurringtripDetails');
                var invalidEls = this.querySelectorAll('input:invalid, select:invalid, textarea:invalid');
                for (var i = 0; i < invalidEls.length; i++) {
                    var el = invalidEls[i];
                    if (el.offsetParent === null) continue;
                    if (recurringDetails && recurringDetails.contains(el) && recurringDetails.style.display === 'none') continue;
                    firstHtml5Invalid = el;
                    break;
                }
                if (!firstHtml5Invalid) firstHtml5Invalid = this.querySelector(':invalid');
                if (firstHtml5Invalid) hasAnyValidationError = true;
                if (firstHtml5Invalid && !firstErrorElement) firstErrorElement = firstHtml5Invalid;

                // 5. If any validation failed: prevent submit, scroll to first error, re-enable will happen in finally
                if (hasAnyValidationError) {
                    e.preventDefault();
                    var scrollTarget = firstErrorElement || document.getElementById('from_spot_0');
                    if (scrollTarget) {
                        // When error is in a stop row, scroll to the row so the whole line and tooltips are visible
                        var stopRow = scrollTarget.closest ? scrollTarget.closest('.stop-row') : null;
                        var elementToScroll = (stopRow && scrollTarget.name && (
                            scrollTarget.name === 'stop_spot_display[]' ||
                            scrollTarget.name === 'stop_pickup_dropoff[]' ||
                            scrollTarget.name === 'stop_date[]' ||
                            scrollTarget.name === 'stop_time[]'
                        )) ? stopRow : scrollTarget;
                        elementToScroll.scrollIntoView({
                            behavior: 'smooth',
                            block: elementToScroll === document.getElementById(
                                'post-ride-price-section') ? 'start' : 'center'
                        });
                        if (firstHtml5Invalid && scrollTarget === firstHtml5Invalid) scrollTarget.focus();
                        else if (scrollTarget.focus) scrollTarget.focus();
                    }
                    return;
                }

                // Check if validation should be bypassed (user clicked "Keep Current Price")
                const bypassInput = this.querySelector('input[name="bypass_price_validation"]');
                if (bypassInput && bypassInput.value === '1') {
                    console.log('Bypassing price validation - user chose to keep current price');
                    return; // Allow form to submit normally
                }

                // First check HTML5 validation
                const firstInvalid = this.querySelector(':invalid');
                if (firstInvalid) {
                    e.preventDefault();
                    firstInvalid.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                    firstInvalid.focus();
                    return;
                }

                // Validate price per seat before submission (same as runPriceValidationOnInput: single, full-route, or segment total)
                const priceInput = document.getElementById('priceData0');
                const dynamicBlockSubmit = document.getElementById('stops-segment-prices-dynamic');
                const isSegmentModeSubmit = dynamicBlockSubmit && dynamicBlockSubmit.offsetParent !== null && dynamicBlockSubmit.style.display !== 'none';
                const dynamicPriceInputSubmit = document.getElementById('priceData0DynamicInput');

                let price = null;
                if (isSegmentModeSubmit) {
                    const segmentTotalEl = document.getElementById('segment-total-price-input-dynamic');
                    let segmentTotal = 0;
                    if (segmentTotalEl && segmentTotalEl.value) {
                        segmentTotal = parseFloat(segmentTotalEl.value) || 0;
                    }
                    if (segmentTotal <= 0 && dynamicBlockSubmit) {
                        dynamicBlockSubmit.querySelectorAll('input[name="price_spot_display[]"]').forEach(function(inp) {
                            const v = parseFloat(inp.value);
                            if (!isNaN(v)) segmentTotal += v;
                        });
                    }
                    if (segmentTotal > 0) {
                        price = segmentTotal;
                    } else if (dynamicPriceInputSubmit && dynamicPriceInputSubmit.value) {
                        const fullRoutePrice = parseFloat(dynamicPriceInputSubmit.value);
                        const seatsForPrice = document.querySelector('input[name="seats"]:checked');
                        const s = seatsForPrice ? parseInt(seatsForPrice.value) : 1;
                        price = s > 0 ? fullRoutePrice / s : fullRoutePrice;
                    }
                }
                if (price == null && priceInput && priceInput.value) {
                    price = parseFloat(priceInput.value);
                }

                // Get distance - try from data attribute first, then global variable
                let distance = null;
                const distSource = priceInput || dynamicPriceInputSubmit;
                if (distSource && typeof $ !== 'undefined') {
                    distance = $(distSource).data('distance') || window.rideDistance;
                } else {
                    distance = window.rideDistance;
                }
                if (!distance || distance <= 0) {
                    const distanceInput = document.querySelector(
                        'input[name="distance"], input[id*="distance"]');
                    if (distanceInput && distanceInput.value) {
                        distance = parseFloat(distanceInput.value);
                    }
                }

                // Get number of seats
                let seats = null;
                const seatsInput = document.querySelector('input[name="seats"]:checked');
                if (seatsInput) {
                    seats = parseInt(seatsInput.value);
                }

                // Debug logging
                console.log('Form submission validation:', {
                    price: price,
                    distance: distance,
                    seats: seats,
                    isSegmentMode: isSegmentModeSubmit,
                    hasPriceInput: !!priceInput,
                    hasSeatsInput: !!seatsInput,
                    windowRideDistance: window.rideDistance
                });

                // If we have price, distance, and seats, validate
                if (price && price > 0 && distance && distance > 0 && seats && seats > 0) {
                    const validation = validatePricePerSeat(price, distance, seats);

                    console.log('Price validation result:', validation);

                    if (!validation.valid) {
                        e.preventDefault();
                        e.stopPropagation();
                        e.stopImmediatePropagation();
                        showPriceErrorModal(validation.maxPricePerSeat);
                        return false;
                    }

                    if (validation.type === 'warning') {
                        e.preventDefault();
                        e.stopPropagation();
                        e.stopImmediatePropagation();
                        console.log('Showing soft warning modal - validation type is warning');
                        showPriceWarningModal(function() {
                            // User clicked "Keep Current Price" - submit the form
                            console.log('User chose to keep current price, submitting form');
                            const formElement = document.getElementById('post-ride-form') ||
                                document.querySelector('form');
                            if (formElement) {
                                // Create a hidden input to bypass validation on next submit
                                const bypassInput = document.createElement('input');
                                bypassInput.type = 'hidden';
                                bypassInput.name = 'bypass_price_validation';
                                bypassInput.value = '1';
                                formElement.appendChild(bypassInput);
                                // Remove the event listener to prevent re-validation
                                const newForm = formElement.cloneNode(true);
                                formElement.parentNode.replaceChild(newForm, formElement);
                                // Submit the form
                                newForm.submit();
                            }
                        });
                        return false;
                    }
                } else {
                    console.warn('Skipping price validation - missing required data:', {
                        hasPrice: !!price && price > 0,
                        hasDistance: !!distance && distance > 0,
                        hasSeats: !!seats && seats > 0,
                        priceValue: price,
                        distanceValue: distance,
                        seatsValue: seats
                    });

                    // If we have price and seats but no distance, we need to fetch it before allowing submission
                    if (price && price > 0 && seats && seats > 0 && (!distance || distance <= 0)) {
                        const fromInput = document.getElementById('from_spot_0') || document
                            .querySelector('input[name="from"]');
                        const toInput = document.getElementById('to_spot_0') || document.querySelector(
                            'input[name="to"]');

                        if (fromInput && toInput && fromInput.value && toInput.value) {
                            console.log('Distance missing - attempting to fetch before validation');
                            // Prevent form submission and fetch distance
                            e.preventDefault();
                            e.stopPropagation();

                            // Fetch distance asynchronously
                            if (typeof $ !== 'undefined') {
                                $.ajax({
                                    url: "{{ url('get-cities-distance') }}",
                                    type: "POST",
                                    data: {
                                        search: fromInput.value,
                                        searchData: toInput.value,
                                        _token: '{{ csrf_token() }}'
                                    },
                                    dataType: 'json',
                                    success: function(result) {
                                        console.log('Distance fetch response:', result);
                                        // Check if distance exists in response (could be result.distance or result.data.distance)
                                        const distanceValue = result.distance || (result
                                            .data && result.data.distance) || null;

                                        if (distanceValue) {
                                            const distanceKm = parseFloat(distanceValue);
                                            if (!isNaN(distanceKm) && distanceKm > 0) {
                                                window.rideDistance = distanceKm;
                                                // Store in price input data attribute as well
                                                if (priceInput && typeof $ !==
                                                    'undefined') {
                                                    $(priceInput).data('distance',
                                                        distanceKm);
                                                }
                                                console.log(
                                                    'Distance fetched successfully:',
                                                    distanceKm, 'km');

                                                // Now validate with the fetched distance
                                                const validation = validatePricePerSeat(
                                                    price, distanceKm, seats);
                                                console.log(
                                                    'Price validation result after fetching distance:',
                                                    validation);

                                                if (!validation.valid) {
                                                    showPriceErrorModal(validation
                                                        .maxPricePerSeat);
                                                    return;
                                                }

                                                if (validation.type === 'warning') {
                                                    showPriceWarningModal(function() {
                                                        const formElement = document
                                                            .getElementById(
                                                                'post-ride-form') ||
                                                            document.querySelector(
                                                                'form');
                                                        if (formElement) {
                                                            const bypassInput =
                                                                document
                                                                .createElement(
                                                                    'input');
                                                            bypassInput.type =
                                                                'hidden';
                                                            bypassInput.name =
                                                                'bypass_price_validation';
                                                            bypassInput.value = '1';
                                                            formElement.appendChild(
                                                                bypassInput);
                                                            formElement.submit();
                                                        }
                                                    });
                                                    return;
                                                }

                                                // No warning/error - submit form
                                                const formElement = document.getElementById(
                                                        'post-ride-form') || document
                                                    .querySelector('form');
                                                if (formElement) {
                                                    formElement.submit();
                                                }
                                            } else {
                                                console.error(
                                                    'Invalid distance value received:',
                                                    distanceValue);
                                                // If distance is invalid, let backend handle it
                                                const formElement = document.getElementById(
                                                        'post-ride-form') || document
                                                    .querySelector('form');
                                                if (formElement) {
                                                    alert(formElement.value);
                                                    formElement.submit();
                                                }
                                            }
                                        } else {
                                            // Distance might be 0 or missing - check the actual value
                                            const distanceValue = result.distance || (result
                                                .data && result.data.distance);
                                            if (distanceValue === 0 || distanceValue ===
                                                null || distanceValue === undefined) {
                                                console.warn(
                                                    'Distance is 0 or missing in API response. This might indicate invalid locations or API error.'
                                                    );
                                                console.log('Full API response:', result);
                                            } else {
                                                console.error(
                                                    'Distance fetch failed - unexpected response structure. Response:',
                                                    result);
                                            }
                                            // If we can't get valid distance, let backend handle validation
                                            const formElement = document.getElementById(
                                                    'post-ride-form') || document
                                                .querySelector('form');
                                            if (formElement) {
                                                formElement.submit();
                                            }
                                        }
                                    },
                                    error: function(xhr, status, error) {
                                        console.error('Error fetching distance:', error);
                                        // On error, let backend handle validation
                                        const formElement = document.getElementById(
                                            'post-ride-form') || document.querySelector(
                                            'form');
                                        if (formElement) {
                                            formElement.submit();
                                        }
                                    }
                                });
                            } else {
                                console.error(
                                    'jQuery not available - cannot fetch distance. Allowing backend validation.'
                                    );
                                // If jQuery is not available, let backend handle it
                            }
                            return false;
                        }
                    }
                }

            } finally {
                if (e.defaultPrevented) enablePostRideSubmitButtons();
            }
        }, true); // Use capture phase to run before other handlers
    });

    function swapLocations() {
        const fromValue = document.getElementById('from_spot_0').value;
        const toValue = document.getElementById('to_spot_0').value;
        document.getElementById('from_spot_0').value = toValue;
        document.getElementById('to_spot_0').value = fromValue;
        var tempPlace = selectedFromPlace;
        selectedFromPlace = selectedToPlace;
        selectedToPlace = tempPlace;
    }

    window.initPostRidePlaces = function() {
        if (typeof google === 'undefined' || !google.maps || !google.maps.places) return;
        geocoderPostRide = new google.maps.Geocoder();
        var fromInput = document.getElementById('from_spot_0');
        var toInput = document.getElementById('to_spot_0');
        if (!fromInput || !toInput) return;
        fromAutocompletePostRide = new google.maps.places.Autocomplete(fromInput, {
            componentRestrictions: {
                country: 'ca'
            },
            types: ['(cities)'],
            fields: ['address_components', 'formatted_address', 'name', 'place_id']
        });
        toAutocompletePostRide = new google.maps.places.Autocomplete(toInput, {
            componentRestrictions: {
                country: 'ca'
            },
            types: ['(cities)'],
            fields: ['address_components', 'formatted_address', 'name', 'place_id']
        });
        fromAutocompletePostRide.addListener('place_changed', function() {
            var place = fromAutocompletePostRide.getPlace();
            if (place.address_components && place.place_id) {
                isSettingPlaceValuePostRide = true;
                isSelectingFromDropdownPostRide = true;
                var formatted = formatPlaceAddressPostRide(place);
                selectedFromPlace = {
                    place_id: place.place_id,
                    formatted_address: formatted,
                    value: formatted
                };
                fromInput.value = formatted;
                var err = document.getElementById('fromInputError');
                if (err) err.classList.add('hidden');
                if (typeof fetchAndStoreDistance === 'function') setTimeout(function() {
                    fetchAndStoreDistance(0);
                }, 300);
                if (typeof updateStopsOriginDestinationLabelsPostRide === 'function')
                    updateStopsOriginDestinationLabelsPostRide();
                setTimeout(function() {
                    isSettingPlaceValuePostRide = false;
                    isSelectingFromDropdownPostRide = false;
                }, 100);
            }
        });
        toAutocompletePostRide.addListener('place_changed', function() {
            var place = toAutocompletePostRide.getPlace();
            if (place.address_components && place.place_id) {
                isSettingPlaceValuePostRide = true;
                isSelectingFromDropdownPostRide = true;
                var formatted = formatPlaceAddressPostRide(place);
                selectedToPlace = {
                    place_id: place.place_id,
                    formatted_address: formatted,
                    value: formatted
                };
                toInput.value = formatted;
                var err = document.getElementById('toInputError');
                if (err) err.classList.add('hidden');
                if (typeof fetchAndStoreDistance === 'function') setTimeout(function() {
                    fetchAndStoreDistance(0);
                }, 300);
                if (typeof updateStopsOriginDestinationLabelsPostRide === 'function')
                    updateStopsOriginDestinationLabelsPostRide();
                setTimeout(function() {
                    isSettingPlaceValuePostRide = false;
                    isSelectingFromDropdownPostRide = false;
                }, 100);
            }
        });
        fromInput.addEventListener('input', function() {
            if (!isSettingPlaceValuePostRide && selectedFromPlace && this.value.trim() !== selectedFromPlace
                .value) selectedFromPlace = null;
            if (typeof updateStopsOriginDestinationLabelsPostRide === 'function')
                updateStopsOriginDestinationLabelsPostRide();
            if (this.value.trim() !== '') {
                var el = document.getElementById('fromInputError');
                if (el) el.classList.add('hidden');
            }
        });
        toInput.addEventListener('input', function() {
            if (!isSettingPlaceValuePostRide && selectedToPlace && this.value.trim() !== selectedToPlace
                .value) selectedToPlace = null;
            if (typeof updateStopsOriginDestinationLabelsPostRide === 'function')
                updateStopsOriginDestinationLabelsPostRide();
            if (this.value.trim() !== '') {
                var el = document.getElementById('toInputError');
                if (el) el.classList.add('hidden');
            }
        });
        fromInput.addEventListener('focus', function() {
            var el = document.getElementById('fromInputError');
            if (el) el.classList.add('hidden');
        });
        toInput.addEventListener('focus', function() {
            var el = document.getElementById('toInputError');
            if (el) el.classList.add('hidden');
        });
        // Enter: resolve typed city (autocomplete logic like search_ride.blade.php)
        fromInput.addEventListener('keydown', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                resolveTypedCityValuePostRide(this.value, 'from');
            }
        });
        toInput.addEventListener('keydown', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                resolveTypedCityValuePostRide(this.value, 'to');
            }
        });
        fromInput.addEventListener('blur', function() {
            if (isSettingPlaceValuePostRide || isSelectingFromDropdownPostRide) return;
            var self = this;
            setTimeout(function() {
                if (isSettingPlaceValuePostRide || isSelectingFromDropdownPostRide) return;
                var currentValue = self.value.trim();
                var fromInputError = document.getElementById('fromInputError');
                if (currentValue !== '' && (!selectedFromPlace || currentValue !== selectedFromPlace
                        .value)) {
                    resolveTypedCityValuePostRide(currentValue, 'from').then(function() {
                        currentValue = self.value.trim();
                        if (currentValue === '' || !selectedFromPlace || currentValue !==
                            selectedFromPlace.value) {
                            selectedFromPlace = null;
                            if (currentValue !== '' && fromInputError) {
                                var te = fromInputError.querySelector('.tooltip-error');
                                if (te) te.textContent = errorCityMissingPostRide;
                                fromInputError.classList.remove('hidden');
                            }
                            // do not hide fromInputError here when user left the field (blur); only hide when they interact with From (input/focus/place_changed/Enter)
                        } else {
                            // resolved and valid: still do not hide on blur so tooltip does not disappear when driver types in another input
                        }
                        if (typeof fetchAndStoreDistance === 'function' && toInput &&
                            toInput.value) fetchAndStoreDistance(0);
                    });
                } else {
                    if (currentValue === '' || !selectedFromPlace || currentValue !==
                            selectedFromPlace.value) {
                        selectedFromPlace = null;
                        if (currentValue !== '' && fromInputError) {
                            var te = fromInputError.querySelector('.tooltip-error');
                            if (te) te.textContent = currentValue === '' ?
                                errorFromRequiredPostRide : errorCityMissingPostRide;
                            fromInputError.classList.remove('hidden');
                        }
                    }
                    // do not hide fromInputError on blur when user switched to another field
                    if (typeof fetchAndStoreDistance === 'function' && toInput && toInput.value)
                        fetchAndStoreDistance(0);
                }
            }, 200);
        });
        toInput.addEventListener('blur', function() {
            if (isSettingPlaceValuePostRide || isSelectingFromDropdownPostRide) return;
            var self = this;
            setTimeout(function() {
                if (isSettingPlaceValuePostRide || isSelectingFromDropdownPostRide) return;
                var currentValue = self.value.trim();
                var toInputError = document.getElementById('toInputError');
                if (currentValue !== '' && (!selectedToPlace || currentValue !== selectedToPlace
                        .value)) {
                    resolveTypedCityValuePostRide(currentValue, 'to').then(function() {
                        currentValue = self.value.trim();
                        if (currentValue === '' || !selectedToPlace || currentValue !==
                            selectedToPlace.value) {
                            selectedToPlace = null;
                            if (currentValue !== '' && toInputError) {
                                var te = toInputError.querySelector('.tooltip-error');
                                if (te) te.textContent = errorCityMissingPostRide;
                                toInputError.classList.remove('hidden');
                            }
                            // do not hide toInputError on blur so tooltip does not disappear when driver types in another input
                        } else {
                            // resolved and valid: still do not hide on blur
                        }
                        if (typeof fetchAndStoreDistance === 'function' && fromInput &&
                            fromInput.value) fetchAndStoreDistance(0);
                    });
                } else {
                    if (currentValue === '' || !selectedToPlace || currentValue !== selectedToPlace
                        .value) {
                        selectedToPlace = null;
                        if (currentValue !== '' && toInputError) {
                            var te = toInputError.querySelector('.tooltip-error');
                            if (te) te.textContent = currentValue === '' ? errorToRequiredPostRide :
                                errorCityMissingPostRide;
                            toInputError.classList.remove('hidden');
                        }
                    }
                    // do not hide toInputError on blur when user switched to another field
                    if (typeof fetchAndStoreDistance === 'function' && fromInput && fromInput.value)
                        fetchAndStoreDistance(0);
                }
            }, 200);
        });
        document.addEventListener('mousedown', function(e) {
            if (e.target.closest('.pac-container')) isSelectingFromDropdownPostRide = true;
            else setTimeout(function() {
                isSelectingFromDropdownPostRide = false;
            }, 50);
        });

        // Attach Google Places Autocomplete to all existing stop inputs (server-rendered rows)
        document.querySelectorAll('input[name="stop_spot_display[]"]').forEach(function(inp) {
            if (inp.id && inp.id.indexOf('stop_spot_') === 0 && !inp.getAttribute(
                    'data-autocomplete-attached')) {
                attachStopAutocompletePostRide(inp);
            }
        });
    };

    function getStopErrorElementPostRide(inputEl) {
        if (!inputEl) return null;
        var id = inputEl.id || '';
        var dataIndex = inputEl.getAttribute('data-stop-index');
        var index = dataIndex || (id.indexOf('stop_spot_') === 0 ? id.replace('stop_spot_', '') : null);
        return index ? document.getElementById('stopInputError_' + index) : null;
    }

    function attachStopAutocompletePostRide(inputElement) {
        if (!inputElement || typeof google === 'undefined' || !google.maps || !google.maps.places) return;
        if (inputElement.getAttribute('data-autocomplete-attached')) return;
        inputElement.setAttribute('data-autocomplete-attached', '1');
        inputElement.setAttribute('autocomplete', 'off');
        var autocomplete = new google.maps.places.Autocomplete(inputElement, {
            componentRestrictions: {
                country: 'ca'
            },
            types: ['(cities)'],
            fields: ['address_components', 'formatted_address', 'name', 'place_id']
        });
        autocomplete.addListener('place_changed', function() {
            var place = autocomplete.getPlace();
            if (place.address_components && place.place_id) {
                var formatted = typeof formatPlaceAddressPostRide === 'function' ? formatPlaceAddressPostRide(
                    place) : (place.formatted_address || place.name || '');
                if (formatted) inputElement.value = formatted;
                var err = getStopErrorElementPostRide(inputElement);
                if (err) err.classList.add('hidden');
                if (typeof updateStopsOriginDestinationLabelsPostRide === 'function')
                    updateStopsOriginDestinationLabelsPostRide();
                if (typeof syncSegmentPricesUIPostRide === 'function') syncSegmentPricesUIPostRide();
            }
        });
        inputElement.addEventListener('focus', function() {
            var err = getStopErrorElementPostRide(inputElement);
            if (err) err.classList.add('hidden');
        });
        // Enter: resolve typed city (autocomplete logic like search_ride.blade.php)
        inputElement.addEventListener('keydown', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                if (typeof resolveTypedCityValueForStopPostRide === 'function') {
                    resolveTypedCityValueForStopPostRide(this);
                }
            }
        });
        inputElement.addEventListener('blur', function() {
            if (isSettingPlaceValuePostRide || isSelectingFromDropdownPostRide) return;
            var self = this;
            setTimeout(function() {
                if (isSettingPlaceValuePostRide || isSelectingFromDropdownPostRide) return;
                var currentValue = self.value.trim();
                var err = getStopErrorElementPostRide(self);
                if (currentValue === '') {
                    if (err) err.classList.add('hidden');
                    return;
                }
                if (typeof resolveTypedCityValueForStopPostRide === 'function') {
                    resolveTypedCityValueForStopPostRide(self);
                }
            }, 200);
        });
    }

    function formatPlaceAddressPostRide(place) {
        var city = '',
            province = '',
            country = 'Canada';
        if (!place.address_components) return place.name || place.formatted_address || '';
        for (var i = 0; i < place.address_components.length; i++) {
            var c = place.address_components[i],
                t = c.types;
            if (!city && (t.indexOf('locality') !== -1 || t.indexOf('administrative_area_level_2') !== -1)) city = c
                .long_name;
            if (!province && t.indexOf('administrative_area_level_1') !== -1) province = c.short_name;
            if (t.indexOf('country') !== -1) country = c.long_name;
        }
        if (!city && place.name) {
            var p = place.name.split(',').map(function(s) {
                return s.trim();
            });
            if (p[0]) city = p[0];
            if (p[1] && p[1].length <= 3 && !province) province = p[1].toUpperCase();
        }
        if (!city && place.formatted_address) {
            var a = place.formatted_address.split(',').map(function(s) {
                return s.trim();
            });
            if (a[0]) city = a[0];
        }
        var out = city || '';
        if (province) out += (out ? ', ' : '') + province;
        if (country && out) out += ', ' + country;
        return out || place.name || place.formatted_address || '';
    }

    function resolveTypedCityValuePostRide(rawValue, target) {
        var value = (rawValue || '').trim();
        if (!value || !geocoderPostRide) return Promise.resolve(false);
        var inputId = target === 'from' ? 'from_spot_0' : 'to_spot_0';
        var input = document.getElementById(inputId);
        return new Promise(function(resolve) {
            geocoderPostRide.geocode({
                address: value,
                componentRestrictions: {
                    country: 'CA'
                }
            }, function(response, status) {
                if (status !== 'OK' || !response || !response.length) {
                    resolve(false);
                    return;
                }
                var result = null;
                for (var i = 0; i < response.length; i++) {
                    var item = response[i];
                    if (item.address_components && item.address_components.some(function(comp) {
                            return comp.types.indexOf('locality') !== -1 || comp.types.indexOf(
                                'administrative_area_level_2') !== -1;
                        })) {
                        result = item;
                        break;
                    }
                }
                if (!result) {
                    resolve(false);
                    return;
                }
                var formatted = formatPlaceAddressPostRide(result);
                if (!formatted) {
                    resolve(false);
                    return;
                }
                isSettingPlaceValuePostRide = true;
                var sel = {
                    place_id: result.place_id,
                    formatted_address: formatted,
                    value: formatted
                };
                if (target === 'from') selectedFromPlace = sel;
                else selectedToPlace = sel;
                if (input) input.value = formatted;
                var err = document.getElementById(target === 'from' ? 'fromInputError' :
                'toInputError');
                if (err) err.classList.add('hidden');
                setTimeout(function() {
                    isSettingPlaceValuePostRide = false;
                }, 100);
                resolve(true);
            });
        });
    }

    function resolveTypedCityValueForStopPostRide(inputElement) {
        var value = (inputElement && inputElement.value) ? inputElement.value.trim() : '';
        var err = getStopErrorElementPostRide(inputElement);
        if (!value) {
            if (err) err.classList.add('hidden');
            return Promise.resolve(true);
        }
        if (!geocoderPostRide) {
            if (err) {
                var te = err.querySelector('.tooltip-error');
                if (te) te.textContent = typeof errorCityMissingPostRide !== 'undefined' ? errorCityMissingPostRide :
                    'We could not find this city. Please double-check the spelling.';
                err.classList.remove('hidden');
            }
            return Promise.resolve(false);
        }
        return new Promise(function(resolve) {
            geocoderPostRide.geocode({
                address: value,
                componentRestrictions: {
                    country: 'CA'
                }
            }, function(response, status) {
                if (status !== 'OK' || !response || !response.length) {
                    if (err) {
                        var te = err.querySelector('.tooltip-error');
                        if (te) te.textContent = typeof errorCityMissingPostRide !== 'undefined' ?
                            errorCityMissingPostRide :
                            'We could not find this city. Please double-check the spelling.';
                        err.classList.remove('hidden');
                    }
                    resolve(false);
                    return;
                }
                var result = null;
                for (var i = 0; i < response.length; i++) {
                    var item = response[i];
                    if (item.address_components && item.address_components.some(function(comp) {
                            return comp.types.indexOf('locality') !== -1 || comp.types.indexOf(
                                'administrative_area_level_2') !== -1;
                        })) {
                        result = item;
                        break;
                    }
                }
                if (!result) {
                    if (err) {
                        var te = err.querySelector('.tooltip-error');
                        if (te) te.textContent = typeof errorCityMissingPostRide !== 'undefined' ?
                            errorCityMissingPostRide :
                            'We could not find this city. Please double-check the spelling.';
                        err.classList.remove('hidden');
                    }
                    resolve(false);
                    return;
                }
                var formatted = formatPlaceAddressPostRide(result);
                if (!formatted) {
                    if (err) {
                        var te = err.querySelector('.tooltip-error');
                        if (te) te.textContent = typeof errorCityMissingPostRide !== 'undefined' ?
                            errorCityMissingPostRide :
                            'We could not find this city. Please double-check the spelling.';
                        err.classList.remove('hidden');
                    }
                    resolve(false);
                    return;
                }
                isSettingPlaceValuePostRide = true;
                inputElement.value = formatted;
                if (err) err.classList.add('hidden');
                if (typeof updateStopsOriginDestinationLabelsPostRide === 'function')
                    updateStopsOriginDestinationLabelsPostRide();
                if (typeof syncSegmentPricesUIPostRide === 'function') syncSegmentPricesUIPostRide();
                setTimeout(function() {
                    isSettingPlaceValuePostRide = false;
                }, 100);
                resolve(true);
            });
        });
    }

    const dateInput = document.getElementById('dateInput');
    const timeInput = document.getElementById('timeInput');

    const projectOffset = {{ $projectOffset }};

    function getCurrentProjectTime() {
        const now = new Date();

        // System ka UTC offset (minutes me)
        const localOffset = now.getTimezoneOffset();

        // Laravel se aane wala offset hours me hota hai, isko minutes me convert karein
        const laravelOffsetMinutes = projectOffset * 60;

        // Adjust UTC time Laravel ke time zone ke mutabiq
        now.setMinutes(now.getMinutes() + localOffset + laravelOffsetMinutes);

        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');

        return `${hours}:${minutes}`;
    }

    // Retrieve old values from Laravel's old() function

    var rideDate = "";
    var rideTime = "";
    if (('{{ $routeType ?? '' }}' == "repost" || '{{ $routeType ?? '' }}' == "copy") &&
        {{ isset($ride) && $ride ? 'true' : 'false' }}) {
        rideTime = '{{ optional($ride)->time ? \Carbon\Carbon::parse(optional($ride)->time)->format('H:i') : '' }}';
    }
    if ('{{ $routeType ?? '' }}' == "copy" && {{ isset($ride) && $ride ? 'true' : 'false' }}) {
        @php
            $copyDate = optional($ride)->date ?? (isset($ride->defaultRideDetail[0]) ? $ride->defaultRideDetail[0]->date : null);
        @endphp
        rideDate = '{{ $copyDate ? \Carbon\Carbon::parse($copyDate)->format('F d, Y') : '' }}';
    }
    const oldDate = (rideDate && rideDate !== "") ? rideDate : '{{ old('date') }}';
    const oldTime = rideTime == "" ? '{{ old('time') }}' : rideTime;

    // Initialize the date picker
    flatpickr(dateInput, {
        dateFormat: 'F d, Y',
        minDate: 'today', // Restrict to future dates only
        // defaultDate: (oldDate && oldDate !== '') ? oldDate : 'today', // Set default date to today
        disableMobile: true,
        onChange: function(selectedDates, dateStr, instance) {
            // Update minTime based on whether the selected date is today or a future date
            const isToday = instance.latestSelectedDateObj ? instance.latestSelectedDateObj
            .toDateString() === new Date().toDateString() : false;

            const minTime = isToday ? getCurrentProjectTime() : '00:00';

            // Update minTime dynamically without destroying the entire instance
            timeInput._flatpickr.set('minTime', minTime);

            // If the date is today, set the time input value to the current time
            if (isToday) {
                const utcTime = getCurrentProjectTime();
                // Convert 24-hour to Date object for flatpickr
                const [hours, minutes] = utcTime.split(':');
                const date = new Date();
                date.setHours(parseInt(hours), parseInt(minutes), 0, 0);
                timeInput._flatpickr.setDate(date, true);
            }
        },
    });
    if (rideDate && rideDate !== "" && dateInput._flatpickr) {
        setTimeout(function() {
            try {
                dateInput._flatpickr.setDate(rideDate, true);
            } catch (e) {
                /* ignore if date is invalid or past */ }
        }, 10);
    }

    // Main time input: display 24-hour format (e.g. 14:30), no AM/PM
    function formatTimeDisplay24(date) {
        const h = date.getHours();
        const m = date.getMinutes();
        return String(h).padStart(2, '0') + ':' + String(m).padStart(2, '0');
    }
    // Stop time inputs: 12-hour with am/pm (e.g. 9:30 am, 2:30 pm)
    function formatTimeDisplay(date) {
        const hours = date.getHours();
        const minutes = date.getMinutes();
        const mins = minutes < 10 ? '0' + minutes : String(minutes);
        if (hours < 12) {
            const h = hours === 0 ? 12 : hours;
            return h + ':' + mins + ' am';
        } else {
            const h = hours === 12 ? 12 : hours;
            return h + ':' + mins;
        }
    }
    const timePicker = flatpickr(timeInput, {
        enableTime: true,
        noCalendar: true,
        dateFormat: 'H:i', // 24-hour format for backend (stored value)
        altInput: true,
        altFormat: 'H:i', // Display 24-hour in the time area
        time_24hr: false, // Show 24-hour in picker to match display
        disableMobile: true,
        minTime: getCurrentProjectTime(), // Set min time to current time
        defaultDate: oldTime || '',
        minuteIncrement: 1, // Set minute increment to 1
        clickOpens: false, // We handle open/close so second click on time area closes
        onChange: function(selectedDates) {
            if (selectedDates.length && timeInput._flatpickr.altInput) {
                timeInput._flatpickr.altInput.value = formatTimeDisplay(selectedDates[0]);
            }
        },
        onValueUpdate: function(selectedDates) {
            if (selectedDates.length && timeInput._flatpickr.altInput) {
                timeInput._flatpickr.altInput.value = formatTimeDisplay(selectedDates[0]);
            }
        },
        onClose: function(selectedDates) {
            if (selectedDates.length && timeInput._flatpickr.altInput) {
                timeInput._flatpickr.altInput.value = formatTimeDisplay(selectedDates[0]);
            }
        }
    });
    setTimeout(function() {
        if (timePicker.selectedDates.length && timePicker.altInput) {
            timePicker.altInput.value = formatTimeDisplay(timePicker.selectedDates[0]);
        }
    }, 0);

    // Shared initializer for stop time inputs (same behavior/style as main time input: first click opens, second click closes)
    function initStopDatePicker(el) {
        if (!el) return;
        try {
            if (el._flatpickr) {
                el._flatpickr.destroy();
            }
        } catch (e) {
            // ignore
        }
        flatpickr(el, {
            dateFormat: 'F d, Y',
            minDate: 'today',
            disableMobile: true,
        });
    }

    function initStopTimePicker(el) {
        if (!el) return;
        try {
            if (el._flatpickr) {
                el._flatpickr.destroy();
            }
        } catch (e) {
            // ignore
        }
        const existingVal = el.value || '';
        // Need a valid default time so the picker (and AM/PM) works; empty default can break AM/PM
        const defaultTime = existingVal && String(existingVal).trim() ? existingVal : getCurrentProjectTime();
        const picker = flatpickr(el, {
            enableTime: true,
            noCalendar: true,
            dateFormat: 'H:i',
            altInput: true,
            altFormat: 'H:i',
            time_24hr: false,
            disableMobile: true,
            minuteIncrement: 1,
            clickOpens: false,
        });
        // If there is already a selected date, ensure display is formatted
        setTimeout(function() {
            if (picker.selectedDates.length && picker.altInput) {
                picker.altInput.value = formatTimeDisplay(picker.selectedDates[0]);
            }
        }, 0);
        // Time area: first click opens, second click (same area) closes. Use wrapper so clock icon zone counts too.
        var stopTimeWrapper = el.closest('.relative') || el.parentElement;
        if (stopTimeWrapper) {
            stopTimeWrapper.addEventListener('click', function(e) {
                if (e.target.closest('.flatpickr-calendar')) return;
                if (!el._flatpickr) return;
                if (picker.isOpen) {
                    picker.close();
                    e.stopPropagation();
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    return;
                }
            }, true);
            stopTimeWrapper.addEventListener('click', function(e) {
                if (e.target.closest('.flatpickr-calendar')) return;
                if (!el._flatpickr) return;
                if (!el._flatpickr.input.value) {
                    const projectTime = getCurrentProjectTime();
                    const [hours, minutes] = projectTime.split(':');
                    const d = new Date();
                    d.setHours(parseInt(hours, 10), parseInt(minutes, 10), 0, 0);
                    el._flatpickr.setDate(d, true);
                }
                picker.open();
            }, false);
        } else {
            el.addEventListener('click', function() {
                if (!el._flatpickr) return;
                if (picker.isOpen) { picker.close(); return; }
                if (!el._flatpickr.input.value) {
                    const projectTime = getCurrentProjectTime();
                    const [hours, minutes] = projectTime.split(':');
                    const d = new Date();
                    d.setHours(parseInt(hours, 10), parseInt(minutes, 10), 0, 0);
                    el._flatpickr.setDate(d, true);
                }
                picker.open();
            });
        }
    }

    // Initialize all existing stop time inputs on page load
    document.querySelectorAll('input[name="stop_time[]"]').forEach(function(el) {
        initStopTimePicker(el);
    });
    // Initialize all existing stop date inputs on page load
    document.querySelectorAll('input[name="stop_date[]"]').forEach(function(el) {
        initStopDatePicker(el);
    });

    // Time area: first click opens dropdown, second click (same area) closes it. Use wrapper so clock icon zone counts too.
    var timeWrapper = timeInput.closest('.relative') || timeInput.parentElement;
    if (timeWrapper) {
        timeWrapper.addEventListener('click', function(e) {
            if (e.target.closest('.flatpickr-calendar')) return;
            if (!timeInput._flatpickr) return;
            if (timePicker.isOpen) {
                timePicker.close();
                e.stopPropagation();
                e.preventDefault();
                e.stopImmediatePropagation();
                return;
            }
        }, true);
        timeWrapper.addEventListener('click', function(e) {
            if (e.target.closest('.flatpickr-calendar')) return;
            if (!timeInput._flatpickr) return;
            if (!timeInput._flatpickr.input.value) {
                const projectTime = getCurrentProjectTime();
                const [hours, minutes] = projectTime.split(':');
                const date = new Date();
                date.setHours(parseInt(hours), parseInt(minutes), 0, 0);
                timeInput._flatpickr.setDate(date, true);
            }
            timePicker.open();
        }, false);
    } else {
        timeInput.addEventListener('click', function() {
            if (!timeInput._flatpickr) return;
            if (timePicker.isOpen) { timePicker.close(); return; }
            if (!timeInput._flatpickr.input.value) {
                const projectTime = getCurrentProjectTime();
                const [hours, minutes] = projectTime.split(':');
                const date = new Date();
                date.setHours(parseInt(hours), parseInt(minutes), 0, 0);
                timeInput._flatpickr.setDate(date, true);
            }
            timePicker.open();
        });
    }

    function seat_selected(th) {
        var seat = parseInt($(th).val());
        var seatsError = document.getElementById('seats-server-error');
        if (seatsError) seatsError.classList.add('hidden');

        for (i = 1; i <= seat; i++) {
            // Change the image source for selected seats
            $(".seat-image.seat-unselect-" + i).attr('src', '{{ asset('assets/seat-hover-1.png') }}');
            $(".seat-number.seat-number-" + i).addClass('text-green-300');
            $("#number-of-seat-cross-" + i).hide();
        }

        for (i = parseInt(seat) + 1; i <= 7; i++) {
            if (seat == 7) continue;
            // Change the image source back to unselected for remaining seats
            $(".seat-image.seat-unselect-" + i).attr('src', '{{ asset('assets/seat.png') }}');
            $(".seat-number.seat-number-" + i).removeClass('text-green-300');
            $("#number-of-seat-cross-" + i).show();
        }

        // Show warning popup for 5+ seats
        if (seat == 5 || seat == 6 || seat == 7) {
            const modal = document.getElementById('seatsWarningModal');
            if (modal) {
                modal.classList.remove('hidden');
                modal.style.display = 'block';
            }
        }
    }

    function closeSeatsWarningModal() {
        const modal = document.getElementById('seatsWarningModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.style.display = 'none';
        }
    }

    const profileImage = document.getElementById('profile-image');

    function previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function(e) {
                profileImage.src = e.target.result;
                $('#profile-image').addClass('w-40');
                $('#profile-image').addClass('h-40');
            };

            reader.readAsDataURL(input.files[0]);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Get the checkbox element
        var skipCheckbox = document.getElementById('skip');
        var addCheckbox = document.getElementById('add');
        var addedCheckbox = document.getElementById('added');
        var recurringTripCheckbox = document.getElementById('recurring_trip');

        // Get the disclaimer div
        var skipVehicle = document.getElementById('skipVehicle');
        var showVehicles = document.getElementById('showVehicles');
        var recurringtripDetails = document.getElementById('recurringtripDetails');

        // Array of all checkboxes for mutual exclusivity
        var checkboxes = [skipCheckbox, addCheckbox, addedCheckbox];

        // Function to uncheck other checkboxes when one is checked
        function handleCheckboxChange(checkedCheckbox) {
            checkboxes.forEach(function(checkbox) {
                if (checkbox !== checkedCheckbox) {
                    checkbox.checked = false; // Uncheck all other checkboxes
                }
            });
        }

        // Add an event listener to the checkbox
        skipCheckbox.addEventListener('change', function() {
            handleCheckboxChange(skipCheckbox);
            // If the checkbox is checked, show the disclaimer; otherwise, hide it
            skipVehicle.style.display = 'none';
            showVehicles.style.display = 'none';
        });
        addCheckbox.addEventListener('change', function() {
            handleCheckboxChange(addCheckbox);
            // If the checkbox is checked, show the disclaimer; otherwise, hide it
            skipVehicle.style.display = this.checked ? 'block' : 'none';
            showVehicles.style.display = 'none';

            // Enable vehicle fields and set required when add checkbox is checked
            const vehicleFields = skipVehicle.querySelectorAll(
                'input[name="make"], input[name="model"], select[name="vehicle_type"], input[name="year"], input[name="color"], input[name="license_no"], input[name="car_type"]'
                );
            vehicleFields.forEach(function(field) {
                if (this.checked) {
                    field.removeAttribute('disabled');
                    field.setAttribute('required', 'required');
                } else {
                    field.removeAttribute('required');
                }
            }.bind(this));
        });
        addedCheckbox.addEventListener('change', function() {
            handleCheckboxChange(addedCheckbox);
            // If the checkbox is checked, show the disclaimer; otherwise, hide it
            showVehicles.style.display = this.checked ? 'block' : 'none';
            skipVehicle.style.display = 'none';

            // Remove required from vehicle fields when using existing vehicle
            const vehicleFields = skipVehicle.querySelectorAll(
                'input[name="make"], input[name="model"], select[name="vehicle_type"], input[name="year"], input[name="color"], input[name="license_no"], input[name="car_type"]'
                );
            vehicleFields.forEach(function(field) {
                field.removeAttribute('required');
            });
        });
        recurringTripCheckbox.addEventListener('change', function() {
            // If the checkbox is checked, show the recurring details; otherwise, hide it
            recurringtripDetails.style.display = this.checked ? 'block' : 'none';
        });

        // Initial check when the page loads
        skipVehicle.style.display = addCheckbox.checked ? 'block' : 'none';
        showVehicles.style.display = addedCheckbox.checked ? 'block' : 'none';
        recurringtripDetails.style.display = recurringTripCheckbox.checked ? 'block' : 'none';

        // Set initial required state for vehicle fields
        const vehicleFields = skipVehicle.querySelectorAll(
            'input[name="make"], input[name="model"], select[name="vehicle_type"], input[name="year"], input[name="color"], input[name="license_no"], input[name="car_type"]'
            );
        vehicleFields.forEach(function(field) {
            if (addCheckbox.checked) {
                field.removeAttribute('disabled');
                field.setAttribute('required', 'required');
            } else {
                field.removeAttribute('required');
            }
        });
    });

    function debounce(func, delay) {
        let timer;
        return function(...args) {
            clearTimeout(timer);
            timer = setTimeout(() => {
                func.apply(this, args);
            }, delay);
        };
    }

    // Function to fetch cities based on search input
    function fetchCities(searchTerm, searchData, fieldId, fieldIndex) {
        // Get the state_id (if required) or set it to null or default
        let stateId = 0; // You can adjust this if you need to pass state_id
        let url = '{{ url('get-cities-by-state') }}';
        let params = {
            state_id: stateId,
            search: searchTerm,
            searchData: searchData
        };

        $.ajax({
            url: "{{ url('get-cities-by-state') }}",
            type: "POST",
            data: {
                search: searchTerm,
                _token: '{{ csrf_token() }}'
            },
            dataType: 'json',
            success: function(result) {
                let suggestionsContainer = $('#' + fieldId + '_suggestions' + fieldIndex + '');
                suggestionsContainer.empty(); // Clear previous suggestions

                $.each(result.cities, function(key, value) {
                    // Create a list item for each city
                    let displayText =
                        `${value.name}, ${value.state.abrv}, ${value.state.country.name}`;

                    let suggestionItem = $(
                            '<div class="suggestion-item p-2 hover:bg-gray-200 cursor-pointer"></div>'
                            )
                        .text(displayText)
                        .on('click', function() {
                            $('#' + fieldId + '_' + fieldIndex + '').val(displayText);
                            if (fieldId !== 'stop_spot') {
                                fromToInputChange(fieldIndex);
                            }
                            suggestionsContainer.empty();
                        });

                    suggestionsContainer.append(suggestionItem);
                });
            }
        });
    }


    // Function to fetch recommended price based on search input
    function fetchRecommendedPrice(searchTerm, searchData, index) {
        let stateId = 0;
        let url = '{{ url('get-cities-distance') }}';
        let params = {
            search: searchTerm,
            searchData: searchData
        };

        $.ajax({
            url: "{{ url('get-cities-distance') }}",
            type: "POST",
            data: {
                search: searchTerm,
                searchData: searchData,
                _token: '{{ csrf_token() }}'
            },
            dataType: 'json',
            success: function(result) {
                debugger;
                $("#priceData" + index + "").val(result.pricePerKm);
                // Store distance for price validation (globally and on input)
                // Check both result.distance and result.data.distance for compatibility
                const distanceValue = result.distance || (result.data && result.data.distance) || null;
                if (distanceValue && parseFloat(distanceValue) > 0) {
                    const distanceKm = parseFloat(distanceValue);
                    $("#priceData" + index + "").data('distance', distanceKm);
                    window.rideDistance = distanceKm; // Store globally for validation
                    console.log('Distance stored for validation:', distanceKm, 'km');
                } else {
                    const distanceValue = result.distance || (result.data && result.data.distance);
                    if (distanceValue === 0) {
                        console.warn('Distance is 0 - locations might be invalid or same city');
                    } else {
                        console.warn('No valid distance returned from API. Response:', result);
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching distance in fetchRecommendedPrice:', error, xhr);
            }
        });
    }

    const closeModalBtn = document.getElementById('close-modal');
    if (closeModalBtn) {
        closeModalBtn.addEventListener('click', function() {
            const modal = document.querySelector('.relative.z-50');
            if (modal) {
                modal.style.display = 'none'; // Hide the modal
            }
        });
    }

    function fromInput(index) {
        debounce(function() {
            let searchTerm = $('#from_spot_' + index).val();
            if (searchTerm.length >= 2) {
                let searchData = $('#to_spot_' + index).val();
                fetchCities(searchTerm, searchData, 'from_spot', index);
            }
        }, 500)();
    }

    function toInput(index) {
        debounce(function() {
            let searchTerm = $('#to_spot_' + index).val();
            if (searchTerm.length >= 2) {
                let searchData = $('#from_spot_' + index).val();
                fetchCities(searchTerm, searchData, 'to_spot', index);
            }
        }, 500)();
    }

    function fromToInputChange(index) {
        let searchTerm = $('#to_spot_' + index + '').val();
        let searchData = $('#from_spot_' + index + '').val();
        if (searchTerm != "" && searchData != "") {
            fetchRecommendedPrice(searchTerm, searchData, index);
        }
    }

    function stopInput(index) {
        debounce(function() {
            let searchTerm = $('#stop_spot_' + index).val();
            if (searchTerm.length >= 2 && typeof fetchCities !== 'undefined') {
                fetchCities(searchTerm, '', 'stop_spot', index);
            }
        }, 500)();
    }

    function toggleAddMoreSpots(button) {
        var panel = document.getElementById('add-more-spots-panel');
        if (!panel) return;
        var isOpen = button.getAttribute('aria-expanded') === 'true';
        if (isOpen) {
            panel.style.height = panel.scrollHeight + 'px';
            panel.offsetHeight;
            panel.style.height = '0';
            button.setAttribute('aria-expanded', 'false');
        } else {
            panel.style.height = panel.scrollHeight + 'px';
            button.setAttribute('aria-expanded', 'true');
            panel.addEventListener('transitionend', function onEnd() {
                panel.removeEventListener('transitionend', onEnd);
                if (button.getAttribute('aria-expanded') === 'true') {
                    panel.style.height = 'auto';
                }
            }, {
                once: true
            });
        }
    }

    var deleteStopTargetRowPost = null;

    function addStopRowPostRide() {
        var container = document.getElementById('stops-rows-container');
        if (!container) return;
        var rows = container.querySelectorAll('.stop-row');
        var nextIndex = 1;
        rows.forEach(function(r) {
            var idx = parseInt(r.getAttribute('data-stop-index'), 10);
            if (!isNaN(idx)) nextIndex = Math.max(nextIndex, idx + 1);
        });
        var row = document.createElement('div');
        row.className = 'flex items-center gap-3 stop-row flex-nowrap';
        row.setAttribute('data-stop-index', nextIndex);
        row.innerHTML = '<div class="flex flex-row gap-2 items-stretch flex-1 min-w-0 flex-nowrap">' +
            '<div class="relative flex-1 min-w-0 shrink">' +
            '<div class="absolute inset-y-0 start-0 flex items-center pl-2 pointer-events-none"><img src="{{ asset('assets/search-bar-from.png') }}" class="w-auto h-6" alt=""></div>' +
            '<input type="text" name="stop_spot_display[]" data-stop-index="' + nextIndex + '" id="stop_spot_' +
            nextIndex +
            '" value="" autocomplete="off" class="bg-gray-100 border border-gray-200 pl-7 text-gray-900 text-base lg:text-lg rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 block w-full p-2.5" placeholder="">' +
            '<div class="absolute hidden mt-1 z-10 left-0 top-full" id="stopInputError_' + nextIndex +
            '"><div class="tooltip-error shadow-lg rounded p-2 bg-red-500 text-white text-sm lg:text-base"></div></div>' +
            '</div>' +
            '<div class="relative flex-1 min-w-0 shrink">' +
            '<textarea name="stop_pickup_dropoff[]" data-stop-index="' + nextIndex + '" id="stop_pickup_dropoff_' +
            nextIndex +
            '" rows="1" placeholder="pick up / drop off" class="bg-gray-100 border border-gray-200 text-gray-900 text-base lg:text-lg rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 block w-full p-2.5 resize-none"></textarea>' +
            '<div class="absolute hidden mt-1 z-10 left-0 top-full" id="stopPickupDropoffError_' + nextIndex + '">' +
            '<div class="tooltip-error shadow-lg rounded p-2 bg-red-500 text-white text-sm lg:text-base"></div>' +
            '</div>' +
            '</div>' +
            '<div class="w-32 sm:w-40 md:w-44 lg:w-48 shrink-0">' +
            '<div class="relative">' +
            '<div class="absolute inset-y-0 start-0 flex items-center pl-2 pointer-events-none">' +
            '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">' +
            '<path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />' +
            '</svg>' +
            '</div>' +
            '<input type="text" name="stop_date[]" id="stop_date_' + nextIndex +
            '" value="" class="bg-gray-100 border pl-10 border-gray-200 text-gray-900 text-base lg:text-lg rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 block w-full p-2.5" placeholder="">' +
            '<div class="absolute hidden mt-1 z-10 left-0 top-full" id="stopDateError_' + nextIndex + '">' +
            '<div class="tooltip-error shadow-lg rounded p-2 bg-red-500 text-white text-sm lg:text-base"></div>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '<div class="w-32 sm:w-40 md:w-44 lg:w-48 shrink-0">' +
            '<div class="relative">' +
            '<div class="absolute inset-y-0 start-0 flex items-center pl-2 pointer-events-none">' +
            '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">' +
            '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />' +
            '</svg>' +
            '</div>' +
            '<input type="text" name="stop_time[]" id="stop_time_' + nextIndex +
            '" value="" class="bg-gray-100 border pl-10 border-gray-200 text-gray-900 text-base lg:text-lg rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 block w-full p-2.5" placeholder="">' +
            '<div class="absolute hidden mt-1 z-10 left-0 top-full" id="stopTimeError_' + nextIndex + '">' +
            '<div class="tooltip-error shadow-lg rounded p-2 bg-red-500 text-white text-sm lg:text-base"></div>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '<button type="button" class="stop-delete-btn flex-shrink-0 p-2 text-red-600 hover:text-red-700 hover:bg-red-50 rounded focus:outline-none focus:ring-2 focus:ring-red-400" onclick="confirmDeleteStopPostRide(this)" title="Delete stop" aria-label="Delete stop">' +
            '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>' +
            '</button>';
        container.appendChild(row);
        var newStopInput = document.getElementById('stop_spot_' + nextIndex);
        if (newStopInput && typeof attachStopAutocompletePostRide === 'function') {
            attachStopAutocompletePostRide(newStopInput);
        }
        var newStopDateInput = document.getElementById('stop_date_' + nextIndex);
        if (newStopDateInput && typeof initStopDatePicker === 'function') {
            initStopDatePicker(newStopDateInput);
        }
        // Initialize time picker for the new stop time input (same behavior as main time input)
        var newStopTimeInput = document.getElementById('stop_time_' + nextIndex);
        if (newStopTimeInput && typeof initStopTimePicker === 'function') {
            initStopTimePicker(newStopTimeInput);
        }
        updateStopsOriginDestinationLabelsPostRide();
        if (typeof syncSegmentPricesUIPostRide === 'function') {
            syncSegmentPricesUIPostRide();
        }
    }

    function confirmDeleteStopPostRide(btn) {
        var row = btn && btn.closest('.stop-row');
        if (!row) return;
        deleteStopTargetRowPost = row;
        var modal = document.getElementById('delete-stop-modal-post');
        if (modal) {
            modal.classList.remove('hidden');
            modal.style.display = 'block';
        }
    }

    function closeDeleteStopModalPostRide() {
        var modal = document.getElementById('delete-stop-modal-post');
        if (modal) {
            modal.classList.add('hidden');
            modal.style.display = 'none';
        }
        deleteStopTargetRowPost = null;
    }

    function deleteStopRowConfirmedPostRide() {
        if (deleteStopTargetRowPost && deleteStopTargetRowPost.parentNode) {
            deleteStopTargetRowPost.remove();
            updateStopsOriginDestinationLabelsPostRide();
            if (typeof syncSegmentPricesUIPostRide === 'function') syncSegmentPricesUIPostRide();
        }
        closeDeleteStopModalPostRide();
    }

    function updateStopsOriginDestinationLabelsPostRide() {
        var fromEl = document.getElementById('from_spot_0');
        var toEl = document.getElementById('to_spot_0');
        var originLabel = document.getElementById('stops-origin-label');
        var destLabel = document.getElementById('stops-destination-label');
        if (originLabel) originLabel.textContent = (fromEl && fromEl.value) ? fromEl.value.trim() : '';
        if (destLabel) destLabel.textContent = (toEl && toEl.value) ? toEl.value.trim() : '';
    }

    function updateSegmentTotalPricePostRide() {
        var container = document.getElementById('stops-segment-prices-dynamic');
        var totalInput = document.getElementById('segment-total-price-input-dynamic');
        if (!container || !totalInput) return;
        var inputs = container.querySelectorAll('input[name="price_spot_display[]"]');
        var sum = 0;
        inputs.forEach(function(inp) {
            var v = parseFloat(inp.value);
            if (!isNaN(v)) sum += v;
        });
        totalInput.value = sum.toFixed(2);
        var priceEl = document.querySelector('input[name="price"]');
        if (priceEl && priceEl.value && parseFloat(priceEl.value) > 0) {
            var errDiv = document.getElementById('price-error-message');
            if (errDiv) errDiv.style.display = 'none';
        }
        checkFullRouteVsTotalPostRide();
    }

    function checkFullRouteVsTotalPostRide() {
        var container = document.getElementById('stops-segment-prices-dynamic');
        if (!container) return;
        var fullRouteInput = container.querySelector('input[name="price"]') || container.querySelector(
            '.full-route-price-input');
        var totalInput = container.querySelector('#segment-total-price-input-dynamic');
        var tooltip = container.querySelector('#full-route-tooltip-container-dynamic');
        if (!fullRouteInput || !totalInput) return;
        if (container.style.display === 'none' || !container.offsetParent) return;
        var fullVal = parseFloat(fullRouteInput.value);
        var totalVal = parseFloat(totalInput.value);
        if (isNaN(fullVal)) fullVal = 0;
        if (isNaN(totalVal)) totalVal = 0;
        if (tooltip) {
            if (fullVal > totalVal) {
                tooltip.classList.remove('hidden');
            } else {
                tooltip.classList.add('hidden');
            }
        }
    }

    function syncSegmentPricesUIPostRide() {
        var singleBlock = document.getElementById('single-price-block');
        var dynamicBlock = document.getElementById('stops-segment-prices-dynamic');
        if (!dynamicBlock) return;
        var stopsContainer = document.getElementById('stops-rows-container');
        if (!stopsContainer) return;
        var stopInputs = stopsContainer.querySelectorAll('input[name="stop_spot_display[]"]');
        var stops = [];
        stopInputs.forEach(function(inp) {
            var v = (inp.value && inp.value.trim) ? inp.value.trim() : '';
            if (v) stops.push(v);
        });
        var origin = '';
        var destination = '';
        var originEl = document.getElementById('from_spot_0');
        var destEl = document.getElementById('to_spot_0');
        if (originEl) origin = originEl.value ? originEl.value.trim() : '';
        if (destEl) destination = destEl.value ? destEl.value.trim() : '';
        var mainPrice = '0';
        var singlePriceInput = singleBlock ? singleBlock.querySelector('input[type="number"]') : null;
        var dynFullRouteInput = dynamicBlock.querySelector('.full-route-price-input');
        var priceInputWithName = document.querySelector('#post-ride-price-section input[name="price"]');
        if (priceInputWithName && priceInputWithName.value !== '') mainPrice = priceInputWithName.value;
        else if (singlePriceInput && singlePriceInput.value !== '') mainPrice = singlePriceInput.value;
        else if (dynFullRouteInput && dynFullRouteInput.value !== '') mainPrice = dynFullRouteInput.value;
        else if (singlePriceInput) mainPrice = singlePriceInput.value !== '' ? singlePriceInput.value : '0';
        if (stops.length === 0) {
            if (singleBlock) singleBlock.style.display = '';
            dynamicBlock.style.display = 'none';
            if (dynFullRouteInput) {
                dynFullRouteInput.removeAttribute('name');
                dynFullRouteInput.id = 'priceData0DynamicInput';
            }
            if (singlePriceInput) {
                singlePriceInput.setAttribute('name', 'price');
                singlePriceInput.id = 'priceData0';
            }
            var rowsDyn = document.getElementById('segment-price-rows-dynamic');
            if (rowsDyn) rowsDyn.innerHTML = '';            
            return;
        }
        if (singleBlock) singleBlock.style.display = 'none';
        if (singlePriceInput) {
            singlePriceInput.removeAttribute('name');
            singlePriceInput.id = 'priceData0Backup';
        }
        if (dynFullRouteInput) {
            dynFullRouteInput.setAttribute('name', 'price');
            dynFullRouteInput.value = mainPrice;
            dynFullRouteInput.id = 'priceData0';
        }
        dynamicBlock.style.display = '';
        var points = [origin].concat(stops).concat([destination]);
        var numPoints = points.length;
        var segments = [];
        for (var i = 0; i < numPoints - 1; i++) {
            segments.push({
                from: points[i],
                to: points[i + 1]
            });
        }
        var rowsEl = document.getElementById('segment-price-rows-dynamic');
        if (!rowsEl) return;
        var existingInputs = rowsEl.querySelectorAll('input[name="price_spot_display[]"]');
        var existingValues = [];
        existingInputs.forEach(function(inp) {
            existingValues.push(inp.value);
        });
        rowsEl.innerHTML = '';

        // Initial per-segment prices from server (used on first load for repost/copy)
        var initialPrices = [];
        var initialAttr = dynamicBlock.getAttribute('data-initial-segment-prices');
        if (initialAttr) {
            try {
                initialPrices = JSON.parse(initialAttr);
            } catch (e) {
                initialPrices = [];
            }
        }
        var svgPath =
            'M 15 3 L 15 5.09375 C 12.164063 5.570313 10 8.050781 10 11 C 10 12.777344 10.832031 14.148438 11.9375 15.03125 C 13.042969 15.914063 14.375 16.40625 15.625 16.90625 C 16.875 17.40625 18.042969 17.914063 18.8125 18.53125 C 19.582031 19.148438 20 19.773438 20 21 C 20 23.15625 18.207031 25 16 25 C 13.78125 25 12 23.21875 12 21 L 12 20 L 10 20 L 10 21 C 10 23.964844 12.164063 26.429688 15 26.90625 L 15 29 L 17 29 L 17 26.90625 C 19.84375 26.425781 22 23.925781 22 21 C 22 19.21875 21.167969 17.855469 20.0625 16.96875 C 18.957031 16.082031 17.625 15.5625 16.375 15.0625 C 15.125 14.5625 13.957031 14.082031 13.1875 13.46875 C 12.417969 12.855469 12 12.21875 12 11 C 12 8.808594 13.785156 7 16 7 C 18.21875 7 20 8.78125 20 11 L 20 12 L 22 12 L 22 11 C 22 8.035156 19.835938 5.570313 17 5.09375 L 17 3 Z';
        for (var j = 0; j < segments.length; j++) {
            var seg = segments[j];
            var row = document.createElement('div');
            row.className = 'mt-4 segment-price-row-dynamic';
            var defaultValue = mainPrice;
            if (existingValues[j] !== undefined && existingValues[j] !== '') {
                defaultValue = existingValues[j];
            } else if (initialPrices[j] !== undefined && initialPrices[j] !== null && initialPrices[j] !== '') {
                defaultValue = initialPrices[j];
            }
            var fromAttr = (seg.from || '').replace(/"/g, '&quot;');
            var toAttr = (seg.to || '').replace(/"/g, '&quot;');
            row.innerHTML = '<p class="text-gray-700 font-medium mb-1 segment-label">' + (seg.from + ' \u2192 ' + seg
                    .to) + '</p>' +
                '<div class="relative mt-2">' +
                '<span class="absolute inset-y-0 start-0 flex items-center pl-2 pointer-events-none">' +
                '<svg fill="currentColor" width="800px" height="800px" viewBox="0 0 32 32" class="w-5 h-5 text-gray-500" xmlns="http://www.w3.org/2000/svg"><path d="' +
                svgPath + '"/></svg></span>' +
                '<input type="number" step="any" name="price_spot_display[]" placeholder="" value="' + defaultValue +
                '" data-segment-from="' + fromAttr + '" data-segment-to="' + toAttr + '" ' +
                'class="bg-gray-100 border border-gray-200 pl-7 text-gray-900 text-base lg:text-lg rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 block w-full p-2.5 mt-2"/>' +
                '</div>';
            rowsEl.appendChild(row);
        }
        updateSegmentTotalPricePostRide();
        checkFullRouteVsTotalPostRide();
    }

    (function() {
        var dynamicBlock = document.getElementById('stops-segment-prices-dynamic');
        if (dynamicBlock) {
            dynamicBlock.addEventListener('input', function(e) {
                if (e.target && e.target.name === 'price_spot_display[]') {
                    if (typeof updateSegmentTotalPricePostRide === 'function')
                        updateSegmentTotalPricePostRide();
                }
                if (e.target && (e.target.name === 'price' || e.target.id === 'priceData0DynamicInput' || e
                        .target.classList.contains('full-route-price-input'))) {
                    if (typeof checkFullRouteVsTotalPostRide === 'function')
                checkFullRouteVsTotalPostRide();
                    if (e.target.value && parseFloat(e.target.value) > 0) {
                        var errDiv = document.getElementById('price-error-message');
                        if (errDiv) errDiv.style.display = 'none';
                        var clientErr = document.getElementById('price-client-error');
                        if (clientErr) clientErr.classList.add('hidden');
                    }
                }
            });
            dynamicBlock.addEventListener('change', function(e) {
                if (e.target && e.target.name === 'price_spot_display[]') {
                    if (typeof updateSegmentTotalPricePostRide === 'function')
                        updateSegmentTotalPricePostRide();
                }
                if (e.target && (e.target.name === 'price' || e.target.id === 'priceData0DynamicInput' || e
                        .target.classList.contains('full-route-price-input'))) {
                    if (typeof checkFullRouteVsTotalPostRide === 'function')
                checkFullRouteVsTotalPostRide();
                    if (e.target.value && parseFloat(e.target.value) > 0) {
                        var errDiv = document.getElementById('price-error-message');
                        if (errDiv) errDiv.style.display = 'none';
                        var clientErr = document.getElementById('price-client-error');
                        if (clientErr) clientErr.classList.add('hidden');
                    }
                }
            });
        }
    })();

    function buildStopsSegmentsForSubmitPostRide() {
        var form = document.getElementById('post-ride-form');
        var container = document.getElementById('stops-rows-container');
        var hiddenContainer = document.getElementById('stops-segments-hidden');
        if (!form || !container || !hiddenContainer) return;
        var origin = (document.getElementById('from_spot_0') || form.querySelector('input[name="from"]')) ? (document
            .getElementById('from_spot_0') || form.querySelector('input[name="from"]')).value : '';
        var destination = (document.getElementById('to_spot_0') || form.querySelector('input[name="to"]')) ? (document
            .getElementById('to_spot_0') || form.querySelector('input[name="to"]')).value : '';
        var mainPrice = '0';
        var dynamicBlock = document.getElementById('stops-segment-prices-dynamic');
        var priceInput = (dynamicBlock && dynamicBlock.style.display !== 'none' && dynamicBlock.offsetParent !== null) ?
            (dynamicBlock.querySelector('input[name="price"]') || dynamicBlock.querySelector(
            '.full-route-price-input')) :
            (document.getElementById('priceData0') || form.querySelector('input[name="price"]'));
        if (priceInput) mainPrice = priceInput.value !== '' ? priceInput.value : '0';
        var segmentPriceInputs = form.querySelectorAll('input[name="price_spot_display[]"]');
        var stopInputs = container.querySelectorAll('input[name="stop_spot_display[]"]');
        var stops = [];
        stopInputs.forEach(function(inp) {
            var v = inp.value ? inp.value.trim() : '';
            if (v) stops.push(v);
        });
        hiddenContainer.innerHTML = '';
        if (stops.length === 0) return;
        var n = stops.length;
        for (var i = 0; i <= n; i++) {
            var fromVal = (i === 0) ? origin : stops[i - 1];
            var toVal = (i === n) ? destination : stops[i];
            if (!fromVal || !toVal) continue;
            var segPrice = mainPrice;
            if (segmentPriceInputs.length > i && segmentPriceInputs[i].value !== '') {
                segPrice = segmentPriceInputs[i].value;
            }
            var inpFrom = document.createElement('input');
            inpFrom.type = 'hidden';
            inpFrom.name = 'from_spot[]';
            inpFrom.value = fromVal;
            var inpTo = document.createElement('input');
            inpTo.type = 'hidden';
            inpTo.name = 'to_spot[]';
            inpTo.value = toVal;
            var inpPrice = document.createElement('input');
            inpPrice.type = 'hidden';
            inpPrice.name = 'price_spot[]';
            inpPrice.value = segPrice;
            hiddenContainer.appendChild(inpFrom);
            hiddenContainer.appendChild(inpTo);
            hiddenContainer.appendChild(inpPrice);
        }
    }

    // Function to fetch and store distance when both from and to are available
    function fetchAndStoreDistance(index) {
        const fromInput = $('#from_spot_' + index);
        const toInput = $('#to_spot_' + index);

        if (fromInput.length && toInput.length && fromInput.val() && toInput.val()) {
            if (typeof $ !== 'undefined') {
                $.ajax({
                    url: "{{ url('get-cities-distance') }}",
                    type: "POST",
                    data: {
                        search: fromInput.val(),
                        searchData: toInput.val(),
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: 'json',
                    success: function(result) {
                        const distanceValue = result.distance || (result.data && result.data.distance) ||
                            null;
                        if (distanceValue && parseFloat(distanceValue) > 0) {
                            const distanceKm = parseFloat(distanceValue);
                            window.rideDistance = distanceKm;
                            // Store in price input data attribute as well
                            const priceInput = $('#priceData' + index);
                            if (priceInput.length) {
                                priceInput.data('distance', distanceKm);
                            }
                            console.log('Distance fetched and stored:', distanceKm, 'km');
                        } else {
                            console.warn('No valid distance returned. Response:', result);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching distance:', error);
                    }
                });
            }
        }
    }

    // Add event listeners to fetch distance when from/to fields change
    document.addEventListener('DOMContentLoaded', function() {
        // Listen for changes on from_spot_0 and to_spot_0
        const fromInput0 = document.getElementById('from_spot_0');
        const toInput0 = document.getElementById('to_spot_0');

        if (fromInput0) {
            fromInput0.addEventListener('blur', function() {
                setTimeout(() => {
                    if (toInput0 && toInput0.value) {
                        fetchAndStoreDistance(0);
                    }
                }, 500);
            });
            fromInput0.addEventListener('input', function() {
                if (typeof updateStopsOriginDestinationLabelsPostRide === 'function')
                    updateStopsOriginDestinationLabelsPostRide();
            });
        }

        if (toInput0) {
            toInput0.addEventListener('blur', function() {
                setTimeout(() => {
                    if (fromInput0 && fromInput0.value) {
                        fetchAndStoreDistance(0);
                    }
                }, 500);
            });
            toInput0.addEventListener('input', function() {
                if (typeof updateStopsOriginDestinationLabelsPostRide === 'function')
                    updateStopsOriginDestinationLabelsPostRide();
            });
        }
        if (typeof updateStopsOriginDestinationLabelsPostRide === 'function')
            updateStopsOriginDestinationLabelsPostRide();
        if (typeof syncSegmentPricesUIPostRide === 'function') syncSegmentPricesUIPostRide();
        var deleteStopYes = document.getElementById('delete-stop-yes-post');
        var deleteStopNo = document.getElementById('delete-stop-no-post');
        if (deleteStopYes) deleteStopYes.addEventListener('click', deleteStopRowConfirmedPostRide);
        if (deleteStopNo) deleteStopNo.addEventListener('click', closeDeleteStopModalPostRide);
        var deleteStopBackdrop = document.getElementById('delete-stop-modal-backdrop-post');
        if (deleteStopBackdrop) deleteStopBackdrop.addEventListener('click', closeDeleteStopModalPostRide);
        var stopsContainerPost = document.getElementById('stops-rows-container');
        if (stopsContainerPost) {
            stopsContainerPost.addEventListener('input', function(e) {
                if (e.target && e.target.name === 'stop_spot_display[]' &&
                    typeof syncSegmentPricesUIPostRide === 'function') syncSegmentPricesUIPostRide();
            });
            stopsContainerPost.addEventListener('change', function(e) {
                if (e.target && e.target.name === 'stop_spot_display[]' &&
                    typeof syncSegmentPricesUIPostRide === 'function') syncSegmentPricesUIPostRide();
            });
        }
        var priceSectionPost = document.getElementById('post-ride-price-section');
        if (priceSectionPost) {
            priceSectionPost.addEventListener('focus', function(e) {
                var inp = e.target;
                if (inp && !inp.readOnly && (inp.name === 'price' || inp.classList.contains(
                        'full-route-price-input') || inp.name === 'price_spot_display[]')) {
                    var val = inp.value;
                    if (val !== '' && (parseFloat(val) === 0 || val === '0' || val === '0.0' || val ===
                            '0.00')) {
                        inp.value = '';
                    }
                }
            }, true);
            priceSectionPost.addEventListener('input', function(e) {
                if (e.target && e.target.classList && e.target.classList.contains(
                        'full-route-price-input') && typeof checkFullRouteVsTotalPostRide ===
                    'function') checkFullRouteVsTotalPostRide();
                if (e.target && (e.target.name === 'price' || e.target.classList.contains(
                        'full-route-price-input') || e.target.name === 'price_spot_display[]')) {
                    var clientErr = document.getElementById('price-client-error');
                    if (clientErr) clientErr.classList.add('hidden');
                }
            });
            priceSectionPost.addEventListener('change', function(e) {
                if (e.target && e.target.classList && e.target.classList.contains(
                        'full-route-price-input') && typeof checkFullRouteVsTotalPostRide ===
                    'function') checkFullRouteVsTotalPostRide();
                if (e.target && (e.target.name === 'price' || e.target.classList.contains(
                        'full-route-price-input') || e.target.name === 'price_spot_display[]')) {
                    var clientErr = document.getElementById('price-client-error');
                    if (clientErr) clientErr.classList.add('hidden');
                }
            });
        }
    });


    function addNewRow() {
        var oldIndex = parseInt($("#rowCount").val());
        if ($("#from_spot_" + oldIndex + "").val() == "") {
            alert("Please select from spot");
            return;
        } else if ($("#to_spot_" + oldIndex + "").val() == "") {
            alert("Please select to spot");
            return;
        }
        // else if($("#priceData"+oldIndex+"").val() == ""){
        //     alert("Please select price spot");
        //     return;
        // }
        var from_city = $("#from_spot_" + oldIndex + "").val()
        var to_city = $("#to_spot_" + oldIndex + "").val()
        var price = $("#price_" + oldIndex + "").val()
        var index = parseInt($("#rowCount").val() + 1);
        $.ajax({
            url: "{{ url('add-new-spots') }}",
            type: "POST",
            data: {
                from_spot: from_city,
                to_spot: to_city,
                price: price,
                index: index,
                _token: '{{ csrf_token() }}'
            },
            dataType: 'json',
            success: function(result) {
                console.log(result);
                if (result.status === 'error') {
                    console.log(result);
                    if (result.errors.from_spot) {
                        console.log(result);
                        // $('#from_spot_' + index + '_error').text(result.errors.from_spot[0]).show();
                        $('.to_spot_error_' + oldIndex).removeClass('hidden');
                        $('.to_spot_error_message').text(result.errors.to_spot[0]);

                    }
                    if (result.errors.to_spot) {
                        // Display error for to_spot
                        $('.from_spot_error_' + oldIndex).removeClass('hidden');
                        $('.from_spot_error_message').text(result.errors.from_spot[0]);
                    }

                    if (result.errors.price) {
                        console.log(result.errors.price[0]);
                        // Display error for to_spot
                        $('.price_' + oldIndex).removeClass('hidden');
                        $('.price_message').text(result.errors.price[0]);
                    }
                } else {
                    $('.from_spot_error_' + oldIndex).addClass('hidden');
                    $('.to_spot_error_' + oldIndex).addClass('hidden');

                    $(".appendNewRow").append(result.spotHtml);
                    $("#rowCount").val(index);
                }

            }
        });
    }

    function removeRow(index, rideDetailId) {
        if (index != 1) {
            $(".remove-row" + index + "").remove();
        }

    }

    function closeModal() {
        const modal = document.getElementById('myModal');
        if (modal) {
            modal.classList.add('hidden');
        }
    }

    // Helper function to show tooltip on a field
    function showFieldTooltip(field, message) {
        if (!field) return;

        // Remove existing tooltip if any
        removeFieldTooltip(field);

        // Add error styling
        field.classList.add('validation-error-border', 'border-red-500', 'ring-red-500');

        // Create tooltip element
        const tooltip = document.createElement('div');
        tooltip.className = 'validation-tooltip';
        tooltip.innerHTML = `
            <div class="validation-tooltip-arrow"></div>
            <div class="validation-tooltip-content">${message}</div>
        `;

        // Insert tooltip after the field
        field.parentNode.insertBefore(tooltip, field.nextSibling);
    }

    // Helper function to remove tooltip from a field
    function removeFieldTooltip(field) {
        if (!field) return;
        field.classList.remove('validation-error-border', 'border-red-500', 'ring-red-500');
        const existingTooltip = field.parentNode.querySelector('.validation-tooltip');
        if (existingTooltip) {
            existingTooltip.remove();
        }
    }

    // Clear tooltip when user focuses, starts typing or changes value
    function setupTooltipClearOnInput() {
        document.querySelectorAll('input, select, textarea').forEach(field => {
            field.addEventListener('focus', function() {
                removeFieldTooltip(this);
            });
            field.addEventListener('input', function() {
                removeFieldTooltip(this);
            });
            field.addEventListener('change', function() {
                removeFieldTooltip(this);
            });
        });
    }

    // Initialize tooltip clear listeners
    document.addEventListener('DOMContentLoaded', setupTooltipClearOnInput);

    // Form validation before submission
    function validatePostRideForm() {
        let isValid = true;
        let firstErrorField = null;

        // Get required field values
        const fromSpot = document.getElementById('from_spot_0');
        const toSpot = document.getElementById('to_spot_0');
        const dateInput = document.querySelector('input[name="departure_date"]');
        const timeInput = document.querySelector('input[name="departure_time"]');
        const seatsInput = document.querySelector('select[name="max_passengers"], input[name="max_passengers"]');
        const priceInput = document.getElementById('priceData0');
        const makeInput = document.querySelector('input[name="make"]');
        const modelInput = document.querySelector('input[name="model"]');
        const typeInput = document.querySelector('select[name="vehicle_type"]');

        // Clear all previous tooltips
        document.querySelectorAll('.validation-tooltip').forEach(el => el.remove());
        document.querySelectorAll('.validation-error-border').forEach(el => {
            el.classList.remove('validation-error-border', 'border-red-500', 'ring-red-500');
        });

        // Validate From location
        if (!fromSpot || !fromSpot.value.trim()) {
            isValid = false;
            showFieldTooltip(fromSpot, 'From location is required');
            if (!firstErrorField) firstErrorField = fromSpot;
        }

        // Validate To location
        if (!toSpot || !toSpot.value.trim()) {
            isValid = false;
            showFieldTooltip(toSpot, 'To location is required');
            if (!firstErrorField) firstErrorField = toSpot;
        }

        // Validate Date
        if (!dateInput || !dateInput.value.trim()) {
            isValid = false;
            showFieldTooltip(dateInput, 'Departure date is required');
            if (!firstErrorField) firstErrorField = dateInput;
        }

        // Validate Time
        if (!timeInput || !timeInput.value.trim()) {
            isValid = false;
            showFieldTooltip(timeInput, 'Departure time is required');
            if (!firstErrorField) firstErrorField = timeInput;
        }

        // Validate Seats
        if (!seatsInput || !seatsInput.value.trim()) {
            isValid = false;
            showFieldTooltip(seatsInput, 'Number of seats is required');
            if (!firstErrorField) firstErrorField = seatsInput;
        }

        // Validate Price
        if (!priceInput || !priceInput.value.trim()) {
            isValid = false;
            showFieldTooltip(priceInput, 'Price is required');
            if (!firstErrorField) firstErrorField = priceInput;
        }

        // Validate Make
        if (!makeInput || !makeInput.value.trim()) {
            isValid = false;
            showFieldTooltip(makeInput, 'Vehicle make is required');
            if (!firstErrorField) firstErrorField = makeInput;
        }

        // Validate Model
        if (!modelInput || !modelInput.value.trim()) {
            isValid = false;
            showFieldTooltip(modelInput, 'Vehicle model is required');
            if (!firstErrorField) firstErrorField = modelInput;
        }

        // Validate Vehicle Type
        if (!typeInput || !typeInput.value.trim()) {
            isValid = false;
            showFieldTooltip(typeInput, 'Vehicle type is required');
            if (!firstErrorField) firstErrorField = typeInput;
        }

        // Validate Terms & Conditions checkbox
        const agreeTermsCheckbox = document.getElementById('agree_terms');
        if (agreeTermsCheckbox && !agreeTermsCheckbox.checked) {
            isValid = false;
            // Just highlight the checkbox, no tooltip
            agreeTermsCheckbox.classList.add('validation-error-border', 'ring-2', 'ring-red-500');
            if (!firstErrorField) firstErrorField = agreeTermsCheckbox;
        }

        // Scroll to first error field
        if (!isValid && firstErrorField) {
            firstErrorField.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
            if (firstErrorField.type !== 'checkbox') {
                firstErrorField.focus();
            }
        }

        return isValid;
    }

    // Helper function to show tooltip on checkbox
    function showCheckboxTooltip(checkbox, message) {
        if (!checkbox) return;

        // Remove existing tooltip if any
        removeCheckboxTooltip(checkbox);

        // Add error styling to checkbox
        checkbox.classList.add('validation-error-border', 'ring-2', 'ring-red-500');

        // Create tooltip element
        const tooltip = document.createElement('div');
        tooltip.className = 'validation-tooltip checkbox-tooltip';
        tooltip.innerHTML = `
            <div class="validation-tooltip-arrow"></div>
            <div class="validation-tooltip-content">${message}</div>
        `;

        // Insert tooltip after the checkbox's parent container
        const container = checkbox.closest('.flex') || checkbox.parentNode;
        container.parentNode.insertBefore(tooltip, container.nextSibling);
    }

    // Helper function to remove tooltip from checkbox
    function removeCheckboxTooltip(checkbox) {
        if (!checkbox) return;
        checkbox.classList.remove('validation-error-border', 'ring-2', 'ring-red-500');
        const container = checkbox.closest('.flex') || checkbox.parentNode;
        if (!container) return;
        // Look for the tooltip as the next sibling of the container
        let nextEl = container.nextElementSibling;
        if (nextEl && nextEl.classList.contains('checkbox-tooltip')) {
            nextEl.remove();
        }
    }

    // Add focus/change listener to agree_terms checkbox
    document.addEventListener('DOMContentLoaded', function() {
        const agreeTermsCheckbox = document.getElementById('agree_terms');
        if (agreeTermsCheckbox) {
            agreeTermsCheckbox.addEventListener('change', function() {
                // Remove highlight when checkbox is checked
                this.classList.remove('validation-error-border', 'ring-2', 'ring-red-500');
            });
        }

        // Toggle Pink Ride disclaimer when Pink Ride checkbox is checked/unchecked
        const pinkRideCheckbox = document.getElementById('pink-ride');
        const pinkRideDisclaimer = document.getElementById('pink-ride-disclaimer');
        const extraCareDisclaimerNumber = document.getElementById('extra-care-disclaimer-number');
        if (pinkRideCheckbox && pinkRideDisclaimer) {
            pinkRideCheckbox.addEventListener('change', function() {
                pinkRideDisclaimer.classList.toggle('hidden', !this.checked);
                // Update Extra+ disclaimer number: 6 when Pink Ride is checked, 5 when not
                if (extraCareDisclaimerNumber) {
                    extraCareDisclaimerNumber.textContent = this.checked ? '6.' : '5.';
                }
            });
        }

        // Toggle Extra+ Ride disclaimer when Extra+ checkbox is checked/unchecked
        const extraCareRideCheckbox = document.getElementById('Extra+');
        const extraCareRideDisclaimer = document.getElementById('Extra+-ride-disclaimer');
        if (extraCareRideCheckbox && extraCareRideDisclaimer) {
            extraCareRideCheckbox.addEventListener('change', function() {
                extraCareRideDisclaimer.classList.toggle('hidden', !this.checked);
            });
        }
    });
</script>
<script
    src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_API_KEY') }}&libraries=places&callback=initPostRidePlaces"
    async defer></script>

<style>
.validation-tooltip {
position: relative;
margin-top: 4px;
}

.validation-tooltip-arrow {
width: 0;
height: 0;
border-left: 8px solid transparent;
border-right: 8px solid transparent;
border-bottom: 8px solid #ef4444;
margin-left: 10px;
}

.validation-tooltip-content {
background-color: #ef4444;
color: white;
padding: 6px 12px;
border-radius: 4px;
font-size: 14px;
display: inline-block;
}

.validation-error-border {
border-color: #ef4444 !important;
}
</style>
@endsection
<style>
.flatpickr-time input {
font-size: 18px !important;
}
</style>
