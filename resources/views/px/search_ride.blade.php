@extends('layouts.template')

@section('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
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
                <div class="flex items-end flex-col md:flex-row justify-between gap-4 md:gap-0 rounded-lg">
                    <div class="w-full md:w-[30%]">
                        <label class="block text-sm font-semibold mb-1">
                            @isset($findRidePage->search_section_from_label)
                                {{ $findRidePage->search_section_from_label }}
                            @else
                                From
                            @endisset
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 start-0 flex items-center pl-2 pointer-events-none">
                                <img src="{{ asset('assets/search-bar-from.png') }}" class="w-auto h-6" alt="">
                            </div>
                            @livewire('px.city-autocomplete', [
                                'field' => 'origin',
                                'placeholder' => $findRidePage->search_section_from_placeholder ?? 'Origin',
                                'initialLabel' => $oldOriginLabel,
                                'initialCityId' => $oldOriginCityId,
                            ], key('px-search-origin'))
                        </div>
                        @error('origin.label')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="w-full md:w-[5%] md:bg-gray-200 md:h-12 flex items-center justify-center">
                        <button type="button" onclick="swapLocations()" class="p-2">
                            <img src="{{ asset('assets/arrow.png') }}" class="w-8 h-8 mx-auto" alt="Swap">
                        </button>
                    </div>

                    <div class="w-full md:w-[30%]">
                        <label class="block text-sm font-semibold mb-1">
                            @isset($findRidePage->search_section_to_label)
                                {{ $findRidePage->search_section_to_label }}
                            @else
                                To
                            @endisset
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 start-0 flex items-center pl-2 pointer-events-none">
                                <img src="{{ asset('images/new-21-search-bar-to.png') }}" class="w-4 h-6" alt="">
                            </div>
                            @livewire('px.city-autocomplete', [
                                'field' => 'destination',
                                'placeholder' => $findRidePage->search_section_to_placeholder ?? 'Destination',
                                'initialLabel' => $oldDestinationLabel,
                                'initialCityId' => $oldDestinationCityId,
                            ], key('px-search-destination'))
                        </div>
                        @error('destination.label')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="w-full md:w-[30%]">
                        <label class="block text-sm font-semibold mb-1">
                            @isset($findRidePage->search_section_date_label)
                                {{ $findRidePage->search_section_date_label }}
                            @else
                                Departure Date
                            @endisset
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 start-0 flex items-center pl-2 pointer-events-none z-10">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-gray-500">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                                </svg>
                            </div>
                            <input type="text" 
                                name="departure_date" 
                                id="departure_date" 
                                value="{{ $oldDepartureDate }}"
                                readonly
                                class="bg-white rounded-md md:rounded-none px-7 sm:border-l italic border-gray-300 border-0 text-gray-900 text-lg focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 block w-full p-2.5 cursor-pointer"
                                placeholder="{{ $findRidePage->search_section_date_placeholder ?? 'Select date' }}">
                        </div>
                        @error('departure_date')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="w-24 mx-auto md:w-[5%] h-12 flex items-center justify-center">
                        <button type="submit"
                            class="bg-blue-500 w-full h-full flex items-center justify-center text-white rounded-lg hover:bg-blue-600 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                            </svg>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        @if($hasSearch)
            <div class="my-6">
                @if($rides && $rides->count() > 0)
                    <h2 class="text-center font-FuturaMdCnBT text-primary mb-4 text-xl md:text-2xl">
                        @isset($findRidePage->heading_ride_card_section)
                            {{ $findRidePage->heading_ride_card_section }}
                        @else
                            Available Rides
                        @endisset
                        ({{ $rides->total() }})
                    </h2>

                    <div class="space-y-4">
                        @foreach($rides as $ride)
                            <x-px.ride-card
                                :ride="$ride"
                                :lang="optional($selectedLanguage)->abbreviation"
                                :price-minor="$ride->matched_segment_price_minor ?? $ride->price_minor"
                                :detail-query="[
                                    'from_stop_id' => $ride->matched_from_stop_id,
                                    'to_stop_id' => $ride->matched_to_stop_id,
                                ]"
                                detail-route="px.ride_detail"
                            />
                        @endforeach
                    </div>
                    <div class="mt-6">
                        {{ $rides->links() }}
                    </div>
                @else
                    <div class="text-center py-12 bg-white rounded-lg shadow-md">
                        <p class="text-xl text-gray-600">
                            @isset($findRidePage->no_rides_found_message)
                                {{ $findRidePage->no_rides_found_message }}
                            @else
                                No rides found matching your search criteria.
                            @endisset
                        </p>
                    </div>
                @endif
            </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        // Initialize Flatpickr for date input
        flatpickr("#departure_date", {
            dateFormat: "Y-m-d",
            minDate: "today",
            enableTime: false,
        });

        // Swap locations function
        function swapLocations() {
            const originInput = document.querySelector('input[name="origin[label]"]');
            const destinationInput = document.querySelector('input[name="destination[label]"]');
            const originCityId = document.querySelector('input[name="origin[city_id]"]');
            const destinationCityId = document.querySelector('input[name="destination[city_id]"]');

            if (originInput && destinationInput) {
                const tempLabel = originInput.value;
                const tempCityId = originCityId ? originCityId.value : null;

                originInput.value = destinationInput.value;
                if (originCityId) {
                    originCityId.value = destinationCityId ? destinationCityId.value : '';
                }

                destinationInput.value = tempLabel;
                if (destinationCityId) {
                    destinationCityId.value = tempCityId || '';
                }

                // Trigger Livewire update if needed
                if (window.Livewire) {
                    Livewire.emit('updated');
                }
            }
        }
    </script>
@endsection

