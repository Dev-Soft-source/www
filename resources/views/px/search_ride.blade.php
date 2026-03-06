@extends('layouts.template')

@section('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        
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
                                'invalidErrorMessage' => $siteText['invalid_city_error_text'] ?? 'Please select a valid city from the dropdown',
                                'class' => 'h-full w-full border-0 bg-transparent pl-10 pr-4 font-semibold text-slate-900 placeholder-slate-900 focus:ring-0',
                            ], key('px-search-origin'))
                        </div>
                    </div>

                    <div class="w-full md:w-[4%] px-search-segment px-search-divider flex items-center justify-center bg-white">
                        <button type="button" onclick="swapLocations()" class="flex h-10 w-10 items-center justify-center rounded-full text-[#1677e6] transition hover:bg-blue-50">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8.28001 11.7193C8.13939 11.5789 7.94876 11.5 7.75001 11.5C7.55126 11.5 7.36064 11.5789 7.22001 11.7193L3.22001 15.7193C3.07956 15.86 3.00067 16.0506 3.00067 16.2493C3.00067 16.4481 3.07956 16.6387 3.22001 16.7793L7.22001 20.7793C7.28867 20.853 7.37147 20.9121 7.46347 20.9531C7.55547 20.9941 7.65479 21.0162 7.75549 21.0179C7.85619 21.0197 7.95622 21.0012 8.04961 20.9635C8.143 20.9257 8.22783 20.8696 8.29905 20.7984C8.37027 20.7272 8.42641 20.6423 8.46413 20.5489C8.50186 20.4555 8.52038 20.3555 8.5186 20.2548C8.51683 20.1541 8.49479 20.0548 8.45379 19.9628C8.4128 19.8708 8.3537 19.788 8.28001 19.7193L5.56001 16.9993L17 16.9993C17.1989 16.9993 17.3897 16.9203 17.5303 16.7797C17.671 16.639 17.75 16.4483 17.75 16.2493C17.75 16.0504 17.671 15.8597 17.5303 15.719C17.3897 15.5784 17.1989 15.4993 17 15.4993L5.56001 15.4993L8.28001 12.7793C8.42046 12.6387 8.49935 12.4481 8.49935 12.2493C8.49935 12.0506 8.42046 11.86 8.28001 11.7193Z" fill="currentColor" class="f12whk1"></path><path d="M15.77 12.2777C15.9106 12.4182 16.1012 12.4971 16.3 12.4971C16.4987 12.4971 16.6894 12.4182 16.83 12.2777L20.83 8.27773C20.9704 8.1371 21.0493 7.94648 21.0493 7.74773C21.0493 7.54898 20.9704 7.35835 20.83 7.21773L16.83 3.21773C16.7613 3.14404 16.6785 3.08494 16.5865 3.04395C16.4945 3.00296 16.3952 2.98092 16.2945 2.97914C16.1938 2.97736 16.0938 2.99589 16.0004 3.03361C15.907 3.07133 15.8222 3.12747 15.7509 3.19869C15.6797 3.26991 15.6236 3.35475 15.5859 3.44813C15.5481 3.54152 15.5296 3.64155 15.5314 3.74225C15.5332 3.84295 15.5552 3.94227 15.5962 4.03427C15.6372 4.12627 15.6963 4.20907 15.77 4.27773L18.49 6.99773L7.04998 6.99773C6.85106 6.99773 6.6603 7.07675 6.51965 7.2174C6.37899 7.35805 6.29998 7.54882 6.29998 7.74773C6.29998 7.94664 6.37899 8.13741 6.51965 8.27806C6.6603 8.41871 6.85106 8.49773 7.04998 8.49773L18.49 8.49773L15.77 11.2177C15.6295 11.3584 15.5506 11.549 15.5506 11.7477C15.5506 11.9465 15.6295 12.1371 15.77 12.2777Z" fill="currentColor" class="f12whk2"></path></svg>
                        </button>
                    </div>

                    <div class="w-full md:w-[26.5%] px-search-segment px-search-divider">
                        <div class="h-full">
                            @livewire('px.city-autocomplete', [
                                'field' => 'destination',
                                'placeholder' => $findRidePage->search_section_to_placeholder ?? 'Destination',
                                'initialLabel' => $oldDestinationLabel,
                                'initialCityId' => $oldDestinationCityId,
                                'invalidErrorMessage' => $siteText['invalid_city_error_text'] ?? 'Please select a valid city from the dropdown',
                                'class' => 'h-full w-full border-0 bg-transparent pl-10 pr-4 font-semibold text-slate-900 placeholder-slate-900 focus:ring-0',
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
                                class="h-full w-full border-0 bg-transparent pl-10 pr-4 font-semibold text-slate-900 placeholder-slate-900 focus:ring-0"
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
                            :price-minor="$ride->matched_segment_price_minor ?? $ride->price_minor"
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

