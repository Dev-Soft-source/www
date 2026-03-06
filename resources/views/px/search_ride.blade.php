@extends('layouts.template')

@section('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        .px-search-shell {
            border: 2px solid #1677e6;
            border-radius: 18px;
            background: #fff;
            overflow: visible;
            box-shadow: 0 6px 18px rgba(15, 53, 110, 0.16);
        }

        .px-search-segment {
            min-height: 56px;
        }

        .px-search-divider {
            position: relative;
        }

        .px-search-divider::after {
            content: "";
            position: absolute;
            top: 12px;
            right: 0;
            width: 1px;
            height: calc(100% - 24px);
            background: #d8deea;
        }

        .px-search-submit {
            min-height: 56px;
            background: #1677e6;
            border-radius: 0 14px 14px 0;
        }

        .px-search-submit:hover {
            background: #0f67ca;
        }

        @media (max-width: 767px) {
            .px-search-shell {
                display: block;
                border-radius: 20px;
                overflow: visible;
                border-width: 2px;
                background: #f3f4f6;
                box-shadow: 0 10px 24px rgba(15, 53, 110, 0.14);
            }

            .px-search-segment {
                min-height: 64px;
                background: transparent;
                display: flex;
                align-items: center;
            }

            .px-search-shell > div:not(:last-child) {
                padding-left: 1rem;
                padding-right: 1rem;
            }

            .px-search-shell > div:last-child {
                margin-top: 0;
            }

            .px-search-divider {
                border-bottom: 1px solid #d8deea;
            }

            .px-search-shell .px-search-segment > .h-full,
            .px-search-shell .px-search-segment > .relative {
                width: 100%;
                height: 100%;
                display: flex;
                align-items: center;
            }

            .px-search-shell .px-search-segment input {
                min-height: 64px;
            }

            .px-search-shell .md\:w-\[4\%\] {
                display: none;
            }

            .px-search-submit {
                min-height: 58px;
                border-radius: 0 0 18px 18px;
            }

            .px-search-divider::after {
                display: none;
            }
        }
    </style>
@endsection

@section('content')
    <div class="container mx-auto my-6 md:px-8 xl:px-0">
        <div class="bg-gray-100 rounded-md p-4 py-6">
            <div class="text-center mb-6">
                <h1 class="font-FuturaMdCnBT text-2xl md:text-3xl">
                    @isset($findRidePage->main_heading)
                        {{ $findRidePage->main_heading }}
                    @else
                        Search PX Rides
                    @endisset
                </h1>
            </div>

            <form method="GET" action="{{ route('px.search_ride', ['lang' => optional($selectedLanguage)->abbreviation]) }}" id="px-search-form">
                <div class="px-search-shell flex flex-col md:flex-row md:items-stretch">
                    <div class="w-full md:w-[26.5%] px-search-segment px-search-divider">
                        <div class="h-full">
                            @livewire('px.city-autocomplete', [
                                'field' => 'origin',
                                'placeholder' => $findRidePage->search_section_from_placeholder ?? 'Origin',
                                'initialLabel' => $oldOriginLabel,
                                'initialCityId' => $oldOriginCityId,
                                'invalidErrorMessage' => $homePage->slider_required_error ?? 'Please select a valid city from the dropdown',
                            ], key('px-search-origin'))
                        </div>
                    </div>

                    <div class="w-full md:w-[4%] px-search-segment px-search-divider flex items-center justify-center bg-white">
                        <button type="button" onclick="swapLocations()" class="flex h-10 w-10 items-center justify-center rounded-full text-[#1677e6] transition hover:bg-blue-50">
                            <img src="{{ asset('assets/arrow.png') }}" class="w-5 h-5 mx-auto" alt="Swap">
                        </button>
                    </div>

                    <div class="w-full md:w-[26.5%] px-search-segment px-search-divider">
                        <div class="h-full">
                            @livewire('px.city-autocomplete', [
                                'field' => 'destination',
                                'placeholder' => $findRidePage->search_section_to_placeholder ?? 'Destination',
                                'initialLabel' => $oldDestinationLabel,
                                'initialCityId' => $oldDestinationCityId,
                                'invalidErrorMessage' => $homePage->slider_required_error ?? 'Please select a valid city from the dropdown',
                                'class' => 'h-full w-full border-0 bg-transparent pl-12 pr-4 font-semibold text-slate-900 placeholder-slate-900 focus:ring-0',
                            ], key('px-search-destination'))
                        </div>
                    </div>

                    <div class="w-full md:w-[23%] px-search-segment px-search-divider bg-white">
                        <div class="relative h-full">
                            <div class="absolute inset-y-0 start-0 flex items-center pl-4 pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                    fill="currentColor" aria-hidden="true">
                                    <path fill="#888888" fill-rule="evenodd"
                                        d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input id="departure_date" name="departure_date"
                                value="{{ $oldDepartureDate }}" type="text"
                                readonly
                                class="h-full w-full border-0 bg-transparent pl-12 pr-4 font-semibold text-slate-900 placeholder-slate-900 focus:ring-0"
                                placeholder="{{ $findRidePage->search_section_date_placeholder ?? 'Select date' }}"
                                autocomplete="off">
                        </div>
                        @error('departure_date')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="w-full md:w-[20%]">
                        <button type="submit" name="search" value="1"
                            class="px-search-submit w-full h-full flex items-center justify-center text-base font-semibold text-white transition-colors">
                            Search
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="my-6">
            @if($rides && $rides->count() > 0)
                <h2 class="text-center font-FuturaMdCnBT text-primary mb-4 text-xl md:text-2xl">
                    @if($hasSearch)
                        @isset($findRidePage->heading_ride_card_section)
                            {{ $findRidePage->heading_ride_card_section }}
                        @else
                            Available Rides
                        @endisset
                    @else
                        Recent Added Rides
                    @endif
                    ({{ $rides->total() }})
                </h2>

                <div class="space-y-4">
                    @foreach($rides as $ride)
                        <x-px.ride-card
                            :ride="$ride"
                            :lang="optional($selectedLanguage)->abbreviation"
                            detail-route="px.ride_detail"
                            :show-status="false"
                            :show-booking-info="false"
                            :show-options="false"
                            :price-minor="$ride->price_minor"
                        />
                    @endforeach
                </div>
                <div class="mt-6">
                    {{ $rides->links() }}
                </div>
            @else
                <div class="text-center py-12 bg-white rounded-lg shadow-md">
                    <p class="text-xl text-gray-600">
                        @if($hasSearch)
                            @isset($findRidePage->no_rides_found_message)
                                {{ $findRidePage->no_rides_found_message }}
                            @else
                                No rides found matching your search criteria.
                            @endisset
                        @else
                            No recent rides available.
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        // Initialize Flatpickr for date input
        flatpickr("#departure_date", {
            dateFormat: "Y-m-d",
            minDate: "today",
            enableTime: false,
        });

        document.addEventListener('DOMContentLoaded', function() {
            const originInput = document.querySelector('input[name="origin[label]"]');
            const destinationInput = document.querySelector('input[name="destination[label]"]');
            const departureDateInput = document.querySelector('#departure_date');

            if (originInput) {
                originInput.addEventListener('keydown', function(event) {
                    if (event.key !== 'Enter') {
                        return;
                    }

                    setTimeout(() => {
                        const originCityIdInput = document.querySelector('input[name="origin[city_id]"]');
                        const destinationInput = document.querySelector('input[name="destination[label]"]');

                        if (!originCityIdInput || !destinationInput || !originCityIdInput.value) {
                            return;
                        }

                        destinationInput.focus();
                        destinationInput.select();
                    }, 150);
                });
            }

            if (destinationInput) {
                destinationInput.addEventListener('keydown', function(event) {
                    if (event.key !== 'Enter') {
                        return;
                    }

                    setTimeout(() => {
                        const destinationCityIdInput = document.querySelector('input[name="destination[city_id]"]');

                        if (!destinationCityIdInput || !departureDateInput || !destinationCityIdInput.value) {
                            return;
                        }

                        departureDateInput.focus();
                    }, 150);
                });
            }
        });

        // Swap origin and destination values
        function swapLocations() {
            // Find Livewire components by their wire:id
            const originComponent = document.querySelector('input[name="origin[label]"]')?.closest('[wire\\:id]');
            const destinationComponent = document.querySelector('input[name="destination[label]"]')?.closest('[wire\\:id]');

            // Get origin and destination city_id hidden inputs
            const originCityIdInput = document.querySelector('input[name="origin[city_id]"]');
            const destinationCityIdInput = document.querySelector('input[name="destination[city_id]"]');

            // Get origin pickup_location and destination dropoff_location textareas
            const originPickupTextarea = document.querySelector('textarea[name="origin[pickup_location]"]');
            const destinationDropoffTextarea = document.querySelector('textarea[name="destination[dropoff_location]"]');

            // Get current city IDs
            const originCityId = originCityIdInput ? parseInt(originCityIdInput.value) : null;
            const destinationCityId = destinationCityIdInput ? parseInt(destinationCityIdInput.value) : null;

            // Swap city IDs using Livewire's selectCity method
            if (window.Livewire && originComponent && destinationComponent) {
                const originWireId = originComponent.getAttribute('wire:id');
                const destinationWireId = destinationComponent.getAttribute('wire:id');

                if (originWireId && destinationWireId) {
                    try {
                        const originLivewire = window.Livewire.find(originWireId);
                        const destinationLivewire = window.Livewire.find(destinationWireId);

                        // Swap city selections
                        if (destinationCityId && originLivewire) {
                            originLivewire.call('selectCity', destinationCityId);
                        }
                        if (originCityId && destinationLivewire) {
                            destinationLivewire.call('selectCity', originCityId);
                        }

                        // If one is null, clear the other
                        if (!destinationCityId && originLivewire) {
                            originLivewire.set('query', '');
                            originLivewire.set('cityId', null);
                        }
                        if (!originCityId && destinationLivewire) {
                            destinationLivewire.set('query', '');
                            destinationLivewire.set('cityId', null);
                        }
                    } catch (e) {
                        console.error('Error swapping Livewire components:', e);
                        // Fallback: try direct input manipulation
                        const originInput = originComponent.querySelector('input[name="origin[label]"]');
                        const destinationInput = destinationComponent.querySelector('input[name="destination[label]"]');
                        if (originInput && destinationInput) {
                            const temp = originInput.value;
                            originInput.value = destinationInput.value;
                            destinationInput.value = temp;
                            originInput.dispatchEvent(new Event('input', {
                                bubbles: true
                            }));
                            destinationInput.dispatchEvent(new Event('input', {
                                bubbles: true
                            }));
                        }
                    }
                }
            } else {
                // Fallback: direct input manipulation if Livewire is not available
                const originInput = document.querySelector('input[name="origin[label]"]');
                const destinationInput = document.querySelector('input[name="destination[label]"]');
                if (originInput && destinationInput) {
                    const temp = originInput.value;
                    originInput.value = destinationInput.value;
                    destinationInput.value = temp;
                    originInput.dispatchEvent(new Event('input', {
                        bubbles: true
                    }));
                    destinationInput.dispatchEvent(new Event('input', {
                        bubbles: true
                    }));
                }
            }

            // Swap pickup/dropoff location values
            if (originPickupTextarea && destinationDropoffTextarea) {
                const tempLocation = originPickupTextarea.value;
                originPickupTextarea.value = destinationDropoffTextarea.value;
                destinationDropoffTextarea.value = tempLocation;

                // Trigger input event
                originPickupTextarea.dispatchEvent(new Event('input', {
                    bubbles: true,
                    cancelable: true
                }));
                destinationDropoffTextarea.dispatchEvent(new Event('input', {
                    bubbles: true,
                    cancelable: true
                }));
            }
        }

    </script>
@endsection

