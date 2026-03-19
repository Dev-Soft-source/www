@extends('layouts.template')

@section('title', 'Post PX Ride')

@section('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endsection

@section('content')
    @php
        // Handle edit/copy mode - populate from ride if exists
        $isEditMode = isset($ride) && isset($isEditMode) && $isEditMode;
        $isCopyMode = isset($ride) && isset($isCopyMode) && $isCopyMode;
        $prefillRide = $isEditMode || $isCopyMode ? $ride : null;

        if ($prefillRide) {
            // Populate origin data
            if (!old('origin.label') && $prefillRide->route) {
                $oldOriginLabel = $prefillRide->route->origin_label;
                $oldOriginCityId = $prefillRide->route->origin_city_id;
            } else {
                $oldOriginLabel = old('origin.label');
                $oldOriginCityId = old('origin.city_id');
            }

            // Populate destination data
            if (!old('destination.label') && $prefillRide->route) {
                $oldDestinationLabel = $prefillRide->route->destination_label;
                $oldDestinationCityId = $prefillRide->route->destination_city_id;
            } else {
                $oldDestinationLabel = old('destination.label');
                $oldDestinationCityId = old('destination.city_id');
            }

            // Populate pickup/dropoff locations
            $oldPickupLocation = old('origin.pickup_location', $prefillRide->meta['pickup_location'] ?? '');
            $oldDropoffLocation = old('destination.dropoff_location', $prefillRide->meta['dropoff_location'] ?? '');

            // Populate departure_at
            $oldDepartureAt = old('departure_at', $prefillRide->departure_at);
            $oldDepartureAtFormatted = '';
            if ($oldDepartureAt) {
                try {
                    $dt = \Illuminate\Support\Carbon::parse($oldDepartureAt);
                    $oldDepartureAtFormatted = $dt->format('Y-m-d H:i');
                } catch (\Throwable $e) {
                    // Keep empty if parsing fails
                }
            }
            // For backward compatibility, also check separate date/time fields
            $oldDepartureDate = old('departure_date');
            $oldDepartureTime = old('departure_time');
            if (empty($oldDepartureAtFormatted) && $oldDepartureDate && $oldDepartureTime) {
                $oldDepartureAtFormatted = trim($oldDepartureDate . ' ' . $oldDepartureTime);
            }

            // Populate stops (excluding origin and destination - already filtered in controller)
            $oldStops = old('stops', []);
            if (empty($oldStops) && isset($prefillRide->intermediate_stops)) {
                $oldStops = $prefillRide->intermediate_stops;
            }
            $oldDestinationPriceDeltaMinor = old('destination.price_delta_minor');
            if (
                $oldDestinationPriceDeltaMinor === null &&
                isset($prefillRide->stops) &&
                $prefillRide->stops->isNotEmpty()
            ) {
                $destinationStop = $prefillRide->stops->sortBy('stop_order')->last();
                $oldDestinationPriceDeltaMinor = $destinationStop ? $destinationStop->price_delta_minor ?? 0 : 0;
            }

            // Populate other fields
            $oldSeatsTotal = old('seats_total', $prefillRide->seats_total);
            $oldPriceMinor = old('price_minor', $prefillRide->price_minor);
            $oldCurrency = old('currency', $prefillRide->currency);
            $oldVehicleId = old('vehicle_id', $prefillRide->vehicle_id);
            $oldNotes = old('notes', $prefillRide->notes);
            $oldStatus = old('status', $isCopyMode ? 'published' : $prefillRide->status);
            $oldVisibility = old('visibility', $prefillRide->visibility);
            $oldBookingMode = old('booking_mode', $prefillRide->booking_mode);
            $oldBookingMethod = old('booking_method', $prefillRide->booking_method);
            $oldCancelationPolicy = old('cancelation_policy', $prefillRide->cancelation_policy);
            $oldSmokingAllowed = old('smoking_allowed', $prefillRide->smoking_allowed);
            $oldPetsAllowed = old('pets_allowed', $prefillRide->pets_allowed);
            $oldLuggageSize = old('luggage_size', $prefillRide->luggage_size);
            $oldAcceptMoreLuggage = old('accept_more_luggage', $prefillRide->meta['accept_more_luggage'] ?? false);
            $oldIsRecurring = old(
                'is_recurring',
                isset($prefillRide->meta['recurring']['enabled']) && $prefillRide->meta['recurring']['enabled'],
            );
            $oldRecurringFrequency = old('recurring_frequency', $prefillRide->meta['recurring']['frequency'] ?? '');
            $oldRecurringTrips = old('recurring_trips', $prefillRide->meta['recurring']['trips'] ?? 0);
            $oldPickDropOffDescription = old(
                'pick_drop_off_description',
                $prefillRide->meta['pick_drop_off_description'] ?? '',
            );

            // Populate selected ride options
            $oldRideOptionIds = old('ride_option_ids', $prefillRide->options->pluck('id')->toArray());

            // Determine vehicle mode
            if ($prefillRide->vehicle_id) {
                $oldVehicleMode = old('vehicle_mode', 'existing');
            } else {
                $oldVehicleMode = old('vehicle_mode', 'skip');
            }
        } else {
            // Create mode - use old() values
            $oldOriginLabel = old('origin.label');
            $oldOriginCityId = old('origin.city_id');
            $oldDestinationLabel = old('destination.label');
            $oldDestinationCityId = old('destination.city_id');
            $oldPickupLocation = old('origin.pickup_location');
            $oldDropoffLocation = old('destination.dropoff_location');
            $oldDepartureAt = old('departure_at');
            $oldDepartureAtFormatted = '';
            if ($oldDepartureAt) {
                try {
                    $dt = \Illuminate\Support\Carbon::parse($oldDepartureAt);
                    $oldDepartureAtFormatted = $dt->format('Y-m-d H:i');
                } catch (\Throwable $e) {
                    // Keep empty if parsing fails
                }
            }
            // For backward compatibility, also check separate date/time fields
            $oldDepartureDate = old('departure_date');
            $oldDepartureTime = old('departure_time');
            if (empty($oldDepartureAtFormatted) && $oldDepartureDate && $oldDepartureTime) {
                $oldDepartureAtFormatted = trim($oldDepartureDate . ' ' . $oldDepartureTime);
            }
            $oldStops = old('stops', []);
            $oldDestinationPriceDeltaMinor = old('destination.price_delta_minor');
            $oldSeatsTotal = old('seats_total');
            $oldPriceMinor = old('price_minor');
            $oldCurrency = old('currency', strtoupper((string) env('PX_DEFAULT_CURRENCY', 'CAD')));
            $oldVehicleId = old('vehicle_id');
            $oldNotes = old('notes');
            $oldStatus = old('status', 'published');
            $oldVisibility = old('visibility', 'public');
            $oldBookingMode = old('booking_mode');
            $oldBookingMethod = old('booking_method');
            $oldCancelationPolicy = old('cancelation_policy');
            $oldSmokingAllowed = old('smoking_allowed');
            $oldPetsAllowed = old('pets_allowed');
            $oldLuggageSize = old('luggage_size');
            $oldAcceptMoreLuggage = old('accept_more_luggage', false);
            $oldIsRecurring = old('is_recurring', false);
            $oldRecurringFrequency = old('recurring_frequency', '');
            $oldRecurringTrips = old('recurring_trips', 0);
            $oldPickDropOffDescription = old('pick_drop_off_description', '');
            $oldRideOptionIds = old('ride_option_ids', []);
            $oldVehicleMode = old('vehicle_mode', 'existing');
        }

        $oldPriceMajorDisplay =
            $oldPriceMinor !== null && $oldPriceMinor !== ''
                ? number_format(((int) $oldPriceMinor) / 100, 2, '.', '')
                : '';
        $pxCurrencyMap = [
            'USD' => '$',
            'CAD' => 'C$',
        ];
        $oldCurrencyCode = strtoupper((string) ($oldCurrency ?: env('PX_DEFAULT_CURRENCY', 'CAD')));
        if ($oldCurrencyCode === '') {
            $oldCurrencyCode = 'CAD';
        }
        $oldCurrencySymbol = $pxCurrencyMap[$oldCurrencyCode] ?? $oldCurrencyCode . ' ';
        $oldSegmentPrices = old('meta.segment_prices', $prefillRide->meta['segment_prices'] ?? []);
        if (!is_array($oldSegmentPrices)) {
            $oldSegmentPrices = [];
        }

        $stopsExpanded = !empty($oldStops);

        if (!$stopsExpanded && $errors->any()) {
            foreach ($errors->keys() as $errorKey) {
                if (str_starts_with($errorKey, 'stops')) {
                    $stopsExpanded = true;
                    break;
                }
            }
        }

        // Ensure oldDepartureAtFormatted is set if we have oldDepartureAt
        if ($oldDepartureAt && empty($oldDepartureAtFormatted)) {
            try {
                $dt = \Illuminate\Support\Carbon::parse($oldDepartureAt);
                $oldDepartureAtFormatted = $dt->format('Y-m-d H:i');
            } catch (\Throwable $e) {
                // Keep empty if parsing fails
            }
        }

        $bookingMethodGroup = collect($optionGroups ?? [])->firstWhere('code', 'booking_method');
        $bookingMethodOptions = collect(optional($bookingMethodGroup)->options ?? []);
        $defaultBookingMethodId = optional($bookingMethodOptions->first())->id;
        $bookingMethodIcons = [
            'cash' => optional($postRidePage->payment_methods_option1)->icon,
            'online_payment' => optional($postRidePage->payment_methods_option2)->icon,
            'secured_cash' => optional($postRidePage->payment_methods_option3)->icon,
        ];
        $bookingModeGroup = collect($optionGroups ?? [])->firstWhere('code', 'booking_mode');
        $bookingModeOptions = collect(optional($bookingModeGroup)->options ?? []);
        $defaultBookingModeId = optional($bookingModeOptions->first())->id;

        // Check if pink_rides and extra_plus_rides are initially checked
        $pinkRideChecked = false;
        $extraCareRideChecked = false;
        if (!empty($oldRideOptionIds)) {
            foreach ($optionGroups as $group) {
                if ($group->is_checkbox) {
                    foreach ($group->options as $option) {
                        if ($option->code === 'pink_rides' && in_array($option->id, $oldRideOptionIds)) {
                            $pinkRideChecked = true;
                        }
                        if ($option->code === 'extra_plus_rides' && in_array($option->id, $oldRideOptionIds)) {
                            $extraCareRideChecked = true;
                        }
                    }
                }
            }
        }
    @endphp
    <div class="container px-4 mx-auto my-14 page-post_a_ride">
        <div class="flex justify-end md:items-center">
            <a href="{{ route('px.post_ride_again', ['lang' => optional($selectedLanguage)->abbreviation]) }}"
                class="bg-greenXS hover:bg-greenXS text-white text-base md:text-lg rounded font-FuturaMdCnBT hover:font-FuturaMdCnBT px-5 py-2 border border-greenXS hover:border-greenXS hover:text-white text-center focus:bg-greenXS focus:text-white active:text-white active:bg-greenXS">
                @isset($postRidePage->post_arrived_again_label)
                    {{ $postRidePage->post_arrived_again_label }}
                @endisset
            </a>
        </div>
        <div class="flex flex-col sm:flex-col md:flex-row lg:flex-row justify-between md:items-center">
            <h1>
                @if (isset($isEditMode) && $isEditMode)
                    Edit a Ride
                @elseif (isset($isCopyMode) && $isCopyMode)
                    {{ $postRidePage->post_ride_again_main_heading ?? 'Post a Ride Again' }}
                @else
                    {{ $postRidePage->main_heading }}
                @endif
            </h1>
            <p>
                <span class="text-red-500">*
                    {{ $postRideSubDetailPage->feilds_required_text ?? 'Indicates required fields' }}
                </span>
            </p>
        </div>

        <div class="">
            @if (session('message'))
                {{-- <div class="mb-4 rounded-md border border-green-200 bg-green-50 text-green-700 px-4 py-3">
                    {{ session('message') }}
                </div> --}}
            @endif
            <form method="POST"
                action="{{ isset($isEditMode) && $isEditMode ? route('px.post_ride.update', ['lang' => optional($selectedLanguage)->abbreviation, 'id' => $ride->id]) : route('px.post_ride.store', ['lang' => optional($selectedLanguage)->abbreviation]) }}"
                enctype="multipart/form-data" class="">
                @csrf
                @if (isset($isEditMode) && $isEditMode)
                    @method('PUT')
                @endif
                <section class="bg-white rounded-lg shadow-3xl">
                    <h3 class="text-2xl bg-primary rounded-t-lg text-white py-2 px-4">
                        @isset($postRidePage->ride_info_heading)
                            {{ $postRidePage->ride_info_heading }}
                        @endisset
                    </h3>
                    <div class="bg-white p-4 space-y-6">

                        <div class="flex flex-col md:flex-row justify-between items-start">
                            <div class="w-full md:w-[45%] ">
                                <label class="block text-sm mb-4 required">{{ $postRidePage->from_label }}</label>
                                @livewire(
                                    'px.city-autocomplete',
                                    [
                                        'field' => 'origin',
                                        'placeholder' => $postRidePage->from_placeholder,
                                        'initialLabel' => $oldOriginLabel ?? old('origin.label'),
                                        'initialCityId' => $oldOriginCityId ?? old('origin.city_id'),
                                    ],
                                    key('px-origin-city-autocomplete')
                                )
                                @error('origin.label')
                                    <div class="tooltip-error shadow-lg">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="w-full md:w-[10%] md:mt-10 flex justify-center items-start">
                                <button type="button" onclick="swapLocations()">
                                    <img src="{{ asset('assets/arrow.png') }}" class="w-10 h-10 mx-auto" alt="">
                                </button>
                            </div>
                            <div class="w-full md:w-[45%] ">
                                <label class="block text-sm mb-4 required">{{ $postRidePage->to_label }}</label>
                                @livewire(
                                    'px.city-autocomplete',
                                    [
                                        'field' => 'destination',
                                        'placeholder' => $postRidePage->to_placeholder,
                                        'initialLabel' => $oldDestinationLabel ?? old('destination.label'),
                                        'initialCityId' => $oldDestinationCityId ?? old('destination.city_id'),
                                    ],
                                    key('px-destination-city-autocomplete')
                                )
                                @error('destination.label')
                                    <div class="tooltip-error shadow-lg">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="flex items-end flex-col md:flex-row justify-between mt-4">
                            <div class="w-full md:w-[45%]">
                                <label class="block text-sm mb-4 required">{{ $postRidePage->pick_up_label }}</label>
                                <textarea name="origin[pickup_location]" rows="2" class="w-full rounded border-gray-300" autocomplete="off"
                                    placeholder="{{ $postRidePage->pick_up_placeholder }}">{{ $oldPickupLocation ?? old('origin.pickup_location') }}</textarea>
                                @error('origin.pickup_location')
                                    <div class="tooltip-error shadow-lg">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="w-full md:w-[45%]">
                                <label class="block text-sm mb-4 required">{{ $postRidePage->drop_off_label }}</label>
                                <textarea name="destination[dropoff_location]" rows="2" class="w-full rounded border-gray-300" autocomplete="off"
                                    placeholder="{{ $postRidePage->drop_off_placeholder }}">{{ $oldDropoffLocation ?? old('destination.dropoff_location') }}</textarea>
                                @error('destination.dropoff_location')
                                    <div class="tooltip-error shadow-lg">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm mb-4 required">{{ $postRidePage->date_time_label }}</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 start-0 flex items-center pl-2 pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                        fill="currentColor" aria-hidden="true">
                                        <path fill="#888888" fill-rule="evenodd"
                                            d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <input id="px-departure-at" name="departure_at"
                                    value="{{ $oldDepartureAtFormatted ?? old('departure_at') }}" type="text"
                                    class="w-full pl-8 rounded border-gray-300 placeholder-gray-400"
                                    placeholder="Select departure date and time" autocomplete="off">
                            </div>
                            @error('departure_at')
                                <div class="tooltip-error shadow-lg">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="bg-white rounded-lg shadow-3xl mt-6">
                            <button type="button" id="px-stops-toggle"
                                class="bg-primary rounded-lg text-white w-full flex items-center justify-between text-left px-4 py-2"
                                aria-expanded="{{ $stopsExpanded ? 'true' : 'false' }}" aria-controls="px-stops-content">
                                <h3 class="text-2xl">
                                    @isset($postRidePage->add_more_from_to)
                                        {{ $postRidePage->add_more_from_to }}
                                    @else
                                        Stops Along the Way (Optional)
                                    @endisset
                                </h3>
                                <svg id="px-stops-chevron"
                                    class="w-5 h-5 text-white transition-transform {{ $stopsExpanded ? 'rotate-180' : '' }}"
                                    viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.51a.75.75 0 01-1.08 0l-4.25-4.51a.75.75 0 01.02-1.06z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                            <div id="px-stops-content" class="px-4 py-4 {{ $stopsExpanded ? '' : 'hidden' }}">
                                @livewire(
                                    'px.stops-repeater',
                                    [
                                        'initialStops' => $oldStops,
                                        'originLabel' => $oldOriginLabel ?? old('origin.label', ''),
                                        'destinationLabel' => $oldDestinationLabel ?? old('destination.label', ''),
                                    ],
                                    key('px-stops-repeater')
                                )
                            </div>
                        </div>


                        <div class="mt-4 md:col-span-2">
                            <label class="inline-flex items-center gap-2 mb-3">
                                <input type="hidden" name="is_recurring" value="0">
                                <input type="checkbox" id="px-is-recurring" name="is_recurring" value="1"
                                    @checked($oldIsRecurring ?? old('is_recurring', false)) @checked(old('is_recurring'))
                                    class="rounded border-gray-300">
                                @isset($postRidePage->recurring_label)
                                    {{ $postRidePage->recurring_label }}
                                @endisset
                            </label>
                            <div id="px-recurring-fields" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm mb-4 required">Frequency</label>
                                    <select name="recurring_frequency" class="w-full rounded border-gray-300">
                                        <option value="">Select frequency</option>
                                        <option value="daily" @selected(($oldRecurringFrequency ?? old('recurring_frequency')) === 'daily')>Daily</option>
                                        <option value="weekly" @selected(($oldRecurringFrequency ?? old('recurring_frequency')) === 'weekly')>Weekly</option>
                                    </select>
                                    @error('recurring_frequency')
                                        <div class="tooltip-error shadow-lg">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm mb-4 required">Number of Trips</label>
                                    <input type="number" min="1" max="365" name="recurring_trips"
                                        value="{{ $oldRecurringTrips ?? old('recurring_trips') }}"
                                        class="w-full rounded border-gray-300" placeholder="e.g. 10">
                                    @error('recurring_trips')
                                        <div class="tooltip-error shadow-lg">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="bg-white rounded-lg overflow-hidden shadow-3xl mt-6">
                    <h3 class="text-2xl bg-primary text-white py-2 px-4">
                        @isset($postRidePage->meeting_drop_off_description_label)
                            {{ $postRidePage->meeting_drop_off_description_label }}
                        @endisset
                        <span class="text-white">*</span>
                    </h3>
                    <div class="bg-white p-4 space-y-3">
                        <textarea name="pick_drop_off_description" rows="6" class="w-full rounded border-gray-300"
                            placeholder="{{ $postRidePage->meeting_drop_off_description_placeholder }}">{{ $oldPickDropOffDescription ?? old('pick_drop_off_description') }}</textarea>
                        @error('pick_drop_off_description')
                            <div class="tooltip-error shadow-lg">{{ $message }}</div>
                        @enderror
                    </div>
                </section>

                <section class="bg-white rounded-lg overflow-hidden shadow-3xl mt-6">
                    <h3 class="text-2xl bg-primary text-white py-2 px-4">
                        @isset($postRidePage->seats_label)
                            {{ $postRidePage->seats_label }}
                        @endisset
                        <span class="text-white">*</span>
                    </h3>
                    <div class="bg-white p-4 space-y-3">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2 border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center flex-wrap gap-2 mt-2">
                                    @for ($i = 1; $i <= 7; $i++)
                                        <div class="relative">
                                            <label class="cursor-pointer inline-block"
                                                for="number-of-seat-{{ $i }}">
                                                <input id="number-of-seat-{{ $i }}" name="seats_total"
                                                    type="radio" value="{{ $i }}" class="hidden"
                                                    @checked((string) ($oldSeatsTotal ?? old('seats_total', '1')) === (string) $i) onchange="seat_selected(this)"
                                                    @required($i === 1)>
                                                <span class="relative inline-block w-12 h-12">
                                                    <img src="{{ (int) ($oldSeatsTotal ?? old('seats_total', 1)) >= $i ? asset('assets/seat-hover-1.png') : asset('assets/seat.png') }}"
                                                        class="w-12 object-cover cursor-pointer seat-image seat-unselect-{{ $i }}"
                                                        alt="">
                                                    <span
                                                        class="absolute mt-2 inset-0 flex items-center justify-center text-sm seat-number seat-number-{{ $i }} {{ (int) ($oldSeatsTotal ?? old('seats_total', 1)) >= $i ? 'text-green-300' : '' }}">
                                                        {{ $i }}
                                                    </span>
                                                </span>
                                            </label>
                                        </div>
                                    @endfor
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                                    <div>
                                        <label class="block text-sm mb-4 required">
                                            @isset($postRidePage->seats_middle_label)
                                                {{ $postRidePage->seats_middle_label }}
                                            @endisset
                                        </label>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                            @foreach ([2, 3] as $seatCount)
                                                <label class="block cursor-pointer">
                                                    <input type="radio" name="middle_seats"
                                                        value="{{ $seatCount }}" @checked((string) old('middle_seats', '2') === (string) $seatCount)
                                                        @required($seatCount === 2) class="peer sr-only">
                                                    <span
                                                        class="flex items-center justify-center text-center rounded-lg border-2 border-gray-200 bg-gray-50 px-3 py-2 transition peer-checked:border-green-500 peer-checked:bg-blue-50 peer-checked:text-green-600">
                                                        <span class="leading-none">{{ $seatCount }} Seats</span>
                                                    </span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm mb-4 required">
                                            @isset($postRidePage->seats_back_label)
                                                {{ $postRidePage->seats_back_label }}
                                            @endisset
                                        </label>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                            @foreach ([2, 3] as $seatCount)
                                                <label class="block cursor-pointer">
                                                    <input type="radio" name="back_seats" value="{{ $seatCount }}"
                                                        @checked((string) old('back_seats', '2') === (string) $seatCount) @required($seatCount === 2)
                                                        class="peer sr-only">
                                                    <span
                                                        class="flex items-center justify-center text-center rounded-lg border-2 border-gray-200 bg-gray-50 px-3 py-2 transition peer-checked:border-green-500 peer-checked:bg-blue-50 peer-checked:text-green-600">
                                                        <span class="flex items-center justify-center gap-2">
                                                            <span class="leading-none">{{ $seatCount }} Seats</span>
                                                        </span>
                                                    </span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>
                </section>

                <section class="bg-white rounded-lg overflow-hidden shadow-3xl mt-6">
                    <h3 class="text-2xl bg-primary text-white py-2 px-4">
                        @isset($postRidePage->price_payment_heading)
                            {{ $postRidePage->price_payment_heading }}
                        @endisset
                        <span class="text-white">*</span>
                    </h3>
                    <div class="bg-white p-4 space-y-3">
                        <div class="md:col-span-2">
                            <label id="px-price-label" class="block text-sm mb-4 required">Price per
                                Seat</label>
                            <div class="mb-3">
                                <label class="block text-sm mb-4 hidden">Currency</label>
                                <select id="px-currency-select" name="currency" class="w-full rounded border-gray-300">
                                    @foreach ($availableCurrencies ?? ['USD' => ['code' => 'USD'], 'CAD' => ['code' => 'CAD']] as $currencyCode => $currencyData)
                                        <option value="{{ strtoupper((string) $currencyCode) }}"
                                            @selected($oldCurrencyCode === strtoupper((string) $currencyCode))>
                                            {{ strtoupper((string) $currencyCode) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div id="px-price-single-wrap">
                                <input id="px-price-minor-input" name="price_minor" value="{{ $oldPriceMajorDisplay }}"
                                    type="number" min="0" step="0.01" class="w-full rounded border-gray-300"
                                    placeholder="e.g. {{ $oldCurrencySymbol }}25.00">
                                <p id="px-price-single-expected" class="mt-2 text-xs text-gray-500 hidden"></p>
                                @error('price_minor')
                                    <div class="tooltip-error shadow-lg">{{ $message }}</div>
                                @enderror

                            </div>
                            <div id="px-price-segments-wrap" class="hidden space-y-3">
                                <div id="px-price-segments-list" class="space-y-2"></div>
                                <div
                                    class="flex items-center justify-between rounded-md bg-gray-50 border border-gray-200 px-3 py-2">
                                    <span class="text-gray-700">Parent route price per seat</span>
                                    <span id="px-price-segments-total" class="text-gray-900">0.00</span>
                                </div>
                                <input type="hidden" id="px-price-minor-hidden"
                                    value="{{ (int) ($oldPriceMinor ?? old('price_minor', 0)) }}">
                                <input type="hidden" id="px-destination-price-delta-initial"
                                    value="{{ $oldDestinationPriceDeltaMinor ?? old('destination.price_delta_minor', 0) }}">
                                <script type="application/json" id="px-initial-segment-prices-json">{!! json_encode(array_values($oldSegmentPrices), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>
                                <input type="hidden" name="distance_meters" id="px-distance-meters-input"
                                    value="{{ old('distance_meters', '') }}">
                                @error('price_minor')
                                    <div class="tooltip-error shadow-lg">{{ $message }}</div>
                                @enderror
                            </div>
                            @error('stops.*.price_delta_minor')
                                <div class="tooltip-error shadow-lg">{{ $message }}</div>
                            @enderror
                            @if ($bookingMethodGroup && $bookingMethodOptions->isNotEmpty())
                                <div class="mt-8">
                                    <label class="block text-sm mb-4">Payment Method</label>
                                    <div class="space-y-4 mt-2">
                                        @foreach ($bookingMethodOptions as $option)
                                            @php
                                                $bookingMethodIcon = $bookingMethodIcons[$option->code] ?? null;
                                            @endphp
                                            <label class="flex items-center gap-2 text-sm">
                                                <input type="radio" name="booking_method" value="{{ $option->id }}"
                                                    @checked((string) ($oldBookingMethod ?? old('booking_method', $defaultBookingMethodId)) === (string) $option->id) class="mt-0.5">
                                                @if ($bookingMethodIcon)
                                                    <img src="{{ asset('home_page_icons/' . $bookingMethodIcon) }}"
                                                        class="h-6 w-6 object-contain" alt="">
                                                @endif
                                                <span
                                                    class="font-medium text-gray-800">{{ $option->display_label }}</span>
                                                <span class="inline-flex cursor-help w-4 h-4"
                                                    data-tippy-content="{{ $option->display_description }}">
                                                    <svg viewBox="0 0 24 24" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                            d="M22 12C22 17.5228 17.5228 22 12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12ZM12 17.75C12.4142 17.75 12.75 17.4142 12.75 17V11C12.75 10.5858 12.4142 10.25 12 10.25C11.5858 10.25 11.25 10.5858 11.25 11V17C11.25 17.4142 11.5858 17.75 12 17.75ZM12 7C12.5523 7 13 7.44772 13 8C13 8.55228 12.5523 9 12 9C11.4477 9 11 8.55228 11 8C11 7.44772 11.4477 7 12 7Z"
                                                            fill="#666666"></path>
                                                    </svg>
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                    </div>
                </section>
                <section class="bg-white rounded-lg overflow-hidden shadow-3xl mt-6">
                    <h3 class="text-2xl bg-primary text-white py-2 px-4">
                        @isset($postRidePage->booking_label)
                            {{ $postRidePage->booking_label }}
                        @endisset
                        <span class="text-white">*</span>
                    </h3>
                    <div class="bg-white p-4 space-y-3">


                        @if ($bookingModeGroup && $bookingModeOptions->isNotEmpty())
                            <div class="md:col-span-2 p-2">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    @foreach ($bookingModeOptions as $option)
                                        @php
                                            $isInstant = $option->code === 'instant';
                                        @endphp
                                        <label class="block cursor-pointer">
                                            <input type="radio" name="booking_mode" value="{{ $option->id }}"
                                                @checked((string) ($oldBookingMode ?? old('booking_mode', $defaultBookingModeId)) === (string) $option->id) class="peer sr-only">
                                            <span
                                                class="flex items-center gap-3 rounded-lg border-2 border-gray-200 bg-gray-50 px-4 py-4 transition peer-checked:border-green-500 peer-checked:bg-blue-50">
                                                <span class="inline-flex items-center justify-center">
                                                    @if ($isInstant)
                                                        <img class="w-12 h-12"
                                                            src="{{ asset('home_page_icons/' . $postRidePage->booking_option1->icon) }}"
                                                            alt="">
                                                    @else
                                                        <img class="w-12 h-12"
                                                            src="{{ asset('home_page_icons/' . $postRidePage->booking_option2->icon) }}"
                                                            alt="">
                                                    @endif
                                                </span>
                                                <span class="flex items-center gap-2">
                                                    <span
                                                        class="text-xl leading-none {{ $isInstant ? 'text-green-600' : 'text-blue-800' }}">{{ $option->display_label }}</span>
                                                    <span class="cursor-help w-4 h-4"
                                                        data-tippy-content="{{ $option->display_description }}">
                                                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                                d="M22 12C22 17.5228 17.5228 22 12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12ZM12 17.75C12.4142 17.75 12.75 17.4142 12.75 17V11C12.75 10.5858 12.4142 10.25 12 10.25C11.5858 10.25 11.25 10.5858 11.25 11V17C11.25 17.4142 11.5858 17.75 12 17.75ZM12 7C12.5523 7 13 7.44772 13 8C13 8.55228 12.5523 9 12 9C11.4477 9 11 8.55228 11 8C11 7.44772 11.4477 7 12 7Z"
                                                                fill="#666666" />
                                                        </svg>
                                                    </span>
                                                </span>
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                    </div>
                </section>

                <section class="bg-white rounded-lg overflow-hidden shadow-3xl mt-6">
                    <h3 class="text-2xl bg-primary text-white py-2 px-4">
                        @isset($postRidePage->vehicle_label)
                            {{ $postRidePage->vehicle_label }}
                        @endisset
                        <span class="text-white">*</span>
                    </h3>
                    <div class="bg-white p-4 space-y-3">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2 p-4">
                                <div class="flex flex-wrap items-center gap-12">
                                    <label class="inline-flex items-center gap-2 text-sm">
                                        <input type="radio" name="vehicle_mode" value="skip"
                                            class="rounded border-gray-300" @checked($oldVehicleMode === 'skip')>
                                        Skip This Time
                                    </label>
                                    <label class="inline-flex items-center gap-2 text-sm">
                                        <input type="radio" name="vehicle_mode" value="add_new"
                                            class="rounded border-gray-300" @checked($oldVehicleMode === 'add_new')>
                                        Add new vehicle
                                    </label>
                                    <label class="inline-flex items-center gap-2 text-sm">
                                        <input type="radio" name="vehicle_mode" value="existing"
                                            class="rounded border-gray-300" @checked($oldVehicleMode === 'existing')>
                                        Existing
                                    </label>
                                </div>


                                <div id="px-vehicle-existing-fields" class="md:col-span-2 mt-8">
                                    <label class="block text-sm mb-4">Existing Vehicle</label>
                                    <select name="vehicle_id" class="w-full rounded border-gray-300">
                                        <option value="">Select vehicle</option>
                                        @foreach ($vehicles as $vehicle)
                                            <option value="{{ $vehicle->id }}" @selected((string) ($oldVehicleId ?? old('vehicle_id')) === (string) $vehicle->id)>
                                                #{{ $vehicle->id }} - {{ $vehicle->make }} {{ $vehicle->model }}
                                                ({{ $vehicle->liscense_no }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('vehicle_id')
                                        <div class="tooltip-error shadow-lg">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div id="px-vehicle-new-fields"
                                    class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4 mt-8">
                                    <div>
                                        <label class="block text-sm mb-4 required">Make</label>
                                        <input name="new_vehicle[make]" value="{{ old('new_vehicle.make') }}"
                                            type="text" class="w-full rounded border-gray-300">
                                        @error('new_vehicle.make')
                                            <div class="tooltip-error shadow-lg">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm mb-4 required">Model</label>
                                        <input name="new_vehicle[model]" value="{{ old('new_vehicle.model') }}"
                                            type="text" class="w-full rounded border-gray-300">
                                        @error('new_vehicle.model')
                                            <div class="tooltip-error shadow-lg">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm mb-4 required">Vehicle Type</label>
                                        <select name="new_vehicle[type]" class="w-full rounded border-gray-300">
                                            <option value="">Select type</option>
                                            @foreach (['Convertable', 'Coupe', 'Hatchback', 'Minivan', 'Sedan', 'Station wagon', 'SUV', 'Truck', 'Van'] as $type)
                                                <option value="{{ $type }}" @selected(old('new_vehicle.type') === $type)>
                                                    {{ $type }}</option>
                                            @endforeach
                                        </select>
                                        @error('new_vehicle.type')
                                            <div class="tooltip-error shadow-lg">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm mb-4 required">License Plate Number</label>
                                        <input name="new_vehicle[liscense_no]"
                                            value="{{ old('new_vehicle.liscense_no') }}" maxlength="8" type="text"
                                            class="w-full rounded border-gray-300">
                                        @error('new_vehicle.liscense_no')
                                            <div class="tooltip-error shadow-lg">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm mb-4 required">Color</label>
                                        <input name="new_vehicle[color]" value="{{ old('new_vehicle.color') }}"
                                            maxlength="15" type="text" class="w-full rounded border-gray-300">
                                        @error('new_vehicle.color')
                                            <div class="tooltip-error shadow-lg">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm mb-4 required">Year</label>
                                        <input name="new_vehicle[year]" value="{{ old('new_vehicle.year') }}"
                                            maxlength="4" type="text" class="w-full rounded border-gray-300">
                                        @error('new_vehicle.year')
                                            <div class="tooltip-error shadow-lg">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm mb-4">Fuel</label>
                                        <div class="flex flex-wrap items-center gap-4 mt-2">
                                            @foreach (['Electric', 'Hybrid', 'Gas'] as $fuel)
                                                <label class="inline-flex items-center gap-2 text-sm">
                                                    <input type="radio" name="new_vehicle[car_type]"
                                                        value="{{ $fuel }}" class="rounded border-gray-300"
                                                        @checked(old('new_vehicle.car_type', 'Electric') === $fuel)>
                                                    {{ $fuel }}
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm mb-4">Set as primary vehicle</label>
                                        <div class="flex items-center gap-4 mt-2">
                                            <label class="inline-flex items-center gap-2 text-sm">
                                                <input type="radio" name="new_vehicle[primary_vehicle]" value="1"
                                                    class="rounded border-gray-300" @checked(old('new_vehicle.primary_vehicle', '1') === '1')>
                                                Yes
                                            </label>
                                            <label class="inline-flex items-center gap-2 text-sm">
                                                <input type="radio" name="new_vehicle[primary_vehicle]" value="0"
                                                    class="rounded border-gray-300" @checked(old('new_vehicle.primary_vehicle') === '0')>
                                                No
                                            </label>
                                        </div>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm mb-4">Car Image (optional)</label>
                                        <label for="dropzone-file"
                                            class="flex flex-col items-center justify-center w-full h-auto border-2 border-gray-300 border-dashed rounded cursor-pointer bg-gray-100 hover:bg-gray-100">
                                            <div class="flex flex-col items-center justify-center pt-5 pb-6 p-4">
                                                <img id="px-vehicle-image-preview"
                                                    class="w-12 h-12 object-contain mb-4 cursor-pointer"
                                                    src="{{ asset('assets/image-placeholder.png') }}" alt="">
                                                <p class="text-sm lg:text-lg text-gray-900">Upload car photo.</p>
                                                <p class="text-sm lg:text-base text-gray-900 font-normal">JPEG, JPG, PNG,
                                                    GIF -
                                                    10MB max.</p>
                                            </div>
                                            <input id="dropzone-file" name="new_vehicle_image" type="file"
                                                accept="image/jpeg,image/png,image/jpg,image/gif" class="hidden" />
                                        </label>
                                        @error('new_vehicle_image')
                                            <div class="tooltip-error shadow-lg">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>


                @foreach ($optionGroups as $group)
                    @continue(in_array($group->code, ['booking_method','booking_mode']))

                    <section class="bg-white rounded-lg overflow-hidden shadow-3xl mt-6">
                        @php
                            $defaultValues = [
                                'smoking_allowed' => $oldSmokingAllowed ?? null,
                                'pets_allowed' => $oldPetsAllowed ?? null,
                                'luggage_size' => $oldLuggageSize ?? null,
                                'cancelation_policy' => $oldCancelationPolicy ?? null,
                            ];

                            $selectedSingleValue =
                                old($group->code) ??
                                ($defaultValues[$group->code] ?? null ?? optional($group->options->first())->id);

                            $groupTitle =
                                $group->code === 'cancelation_policy'
                                    ? $postRidePage->cancellation_policy_label ?? 'Cancellation Policy'
                                    : ucwords(str_replace('_', ' ', $group->code));

                            $rideOptionIds = $oldRideOptionIds ?? old('ride_option_ids', []);

                            $luggageIcons = [
                                'no_luggage' => optional($postRidePage->luggage_option1)->icon,
                                'small' => optional($postRidePage->luggage_option2)->icon,
                                'medium' => optional($postRidePage->luggage_option3)->icon,
                                'large' => optional($postRidePage->luggage_option4)->icon,
                                'xl_multiple' => optional($postRidePage->luggage_option5)->icon,
                            ];
                        @endphp

                        <h3 class="text-2xl bg-primary text-white py-2 px-4">
                            {{ $groupTitle }}
                            <span class="text-white">*</span>
                        </h3>

                        <div class="bg-white p-4 space-y-3">
                            <div class="grid grid-cols-1 gap-4">

                                @foreach ($group->options as $option)
                                    @php
                                        $optionClass = '';
                                        $disabledClass = '';
                                        $checkboxDisabled = false;

                                        if ($option->code === 'pink_rides') {
                                            $optionClass = 'text-pink-500';
                                            if (!$isPinkRideDisabled) {
                                                $disabledClass = 'line-through';
                                                $checkboxDisabled = true;
                                            }
                                        }

                                        if ($option->code === 'extra_plus_rides') {
                                            $optionClass = 'text-green-500';
                                            if ($isExtraRideDisabled) {
                                                $disabledClass = 'line-through';
                                                $checkboxDisabled = true;
                                            }
                                        }

                                        $luggageIcon = $luggageIcons[$option->code] ?? null;
                                    @endphp
                                    <label class="flex items-start gap-2 text-sm">
                                        {{-- checkbox / radio --}}
                                        @if ($group->is_checkbox)
                                            <input type="checkbox" name="ride_option_ids[]" value="{{ $option->id }}"
                                                @checked(in_array($option->id, $rideOptionIds)) @disabled($checkboxDisabled)
                                                class="mt-0.5 w-4 h-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500 focus:ring-2"
                                                data-ride-option-code="{{ $option->code }}">
                                        @else
                                            <input type="radio" name="{{ $group->code }}"
                                                value="{{ $option->id }}" @checked((string) $selectedSingleValue === (string) $option->id)
                                                class="mt-0.5 w-4 h-4 text-blue-600 cursor-pointer bg-white border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                                        @endif

                                        {{-- luggage icon --}}
                                        @if ($group->code === 'luggage_size' && $luggageIcon)
                                            <img src="{{ asset('home_page_icons/' . $luggageIcon) }}"
                                                class="w-6 h-6 object-contain" alt="">
                                        @endif

                                        <span
                                            class="font-medium text-gray-800 {{ $optionClass }} {{ $disabledClass }}">
                                            {{ $option->display_label }}
                                        </span>

                                        <span class="inline-flex cursor-help w-4 h-4"
                                            data-tippy-content="{{ $option->display_description }}">
                                            <svg viewBox="0 0 24 24" fill="none">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M22 12C22 17.5228 17.5228 22 12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12ZM12 17.75C12.4142 17.75 12.75 17.4142 12.75 17V11C12.75 10.5858 12.4142 10.25 12 10.25C11.5858 10.25 11.25 10.5858 11.25 11V17C11.25 17.4142 11.5858 17.75 12 17.75ZM12 7C12.5523 7 13 7.44772 13 8C13 8.55228 12.5523 9 12 9C11.4477 9 11 8.55228 11 8C11 7.44772 11.4477 7 12 7Z"
                                                    fill="#666666" />
                                            </svg>
                                        </span>

                                    </label>
                                @endforeach

                            </div>

                            @if ($group->code === 'luggage_size')
                                <div class="mt-3 pt-3 border-t border-gray-100">
                                    <label class="inline-flex items-start gap-2 text-sm">
                                        <input type="hidden" name="accept_more_luggage" value="0">
                                        <input type="checkbox" name="accept_more_luggage" value="1"
                                            @checked($oldAcceptMoreLuggage) class="mt-0.5">
                                        <span class="font-medium text-gray-800">
                                            I accept more luggage for extra charge
                                        </span>
                                    </label>
                                </div>
                            @endif
                        </div>
                    </section>
                @endforeach

                <section class="bg-white rounded-lg overflow-hidden shadow-3xl mt-6">
                    <h3 class="text-2xl bg-primary text-white py-2 px-4">
                        @isset($postRidePage->anything_to_add_label)
                            {{ $postRidePage->anything_to_add_label }}
                        @endisset
                        <span class="text-white">*</span>
                    </h3>
                    <div class="bg-white p-4 space-y-3">
                        <textarea name="notes" rows="5" class="w-full rounded border-gray-300" placeholder="Optional ride notes">{{ $oldNotes ?? old('notes') }}</textarea>
                    </div>
                </section>

                <section class="bg-white rounded-lg overflow-hidden shadow-3xl mt-6">
                    <h3 class="text-2xl bg-primary text-white py-2 px-4">
                        @isset($postRidePage->disclaimers_label)
                            {{ $postRidePage->disclaimers_label }}
                        @endisset
                        <span class="text-white">*</span>
                    </h3>
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
                        {{-- if pink_rides checked --}}
                        <div id="pink-ride-disclaimer"
                            class="bg-white border-t border-gray-200 {{ $pinkRideChecked ? '' : 'hidden' }}">
                            <p class="border-gray-300 text-base lg:text-lg py-3 text-gray-900">
                                    {{ $postRidePage->pink_ride_disclaimer_text ?? 'I understand that this is a Pink Ride, exclusive to female members. I will not send a male driver in my place and will not accept any male passengers over 12 years old, even if the booking is made by a female.' }}
                                    <!-- 5. I understand that this is a Pink Ride, exclusive to female members. I will not send a
                                    male driver in my place and will not accept any male passengers over 12 years old, even if
                                    the booking is made by a female. -->
                                </p>
                        </div>
                        {{-- if extra_care_rides checked --}}
                        <div id="extra-care-ride-disclaimer"
                            class="bg-white border-t border-gray-200 {{ $extraCareRideChecked ? '' : 'hidden' }}">
                            <p class="border-gray-300 text-base lg:text-lg py-3 text-gray-900">
                                    
                                    <span id="extra-care-disclaimer-number">{{ $pinkRideChecked ? '6.' : '5.' }}</span>
                                    {{ $postRidePage->extra_care_ride_disclaimer_text ?? 'I understand that this is an Extra+ Ride, exclusive to members with highest review score. I will adhere to its standards' }}
                                    <!-- I understand that this is an Extra+ Ride, exclusively for members with top-tier review
                                    ratings. I commit to upholding the exceptional professionalism and courtesy that earned me
                                    this rating, keeping my vehicle immaculate, driving safely and smoothly as always, and
                                    ensuring a calm, respectful environment by preventing any passenger disputes. -->
                                </p>
                        </div>
                    </div>
                </section>


                <section class="mt-6">
                    <label class="inline-flex items-start gap-2 text-sm w-full required">
                        <input type="checkbox" name="agree_terms" value="1" @checked(old('agree_terms'))
                            class="mt-0.5 rounded border-gray-300">
                        <span class="font-medium text-gray-800">
                            @isset($postRidePage->agree_terms_label)
                                {{ strip_tags($postRidePage->agree_terms_label) }}
                            @endisset
                        </span>
                    </label>
                    @error('agree_terms')
                        <div class="tooltip-error shadow-lg">{{ $message }}</div>
                    @enderror
                </section>

                <div class="pt-2 mt-8">
                    <button type="submit" class="button-exp-fill">Post Ride</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal for 5+ seats warning -->
    <div id="pxSeatsWarningModal" class="hidden fixed inset-0 z-50" aria-labelledby="px-seats-modal-title"
        role="dialog" aria-modal="true">
        <div onclick="closePxSeatsWarningModal()" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity">
        </div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                <div
                    class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
                    <button type="button" onclick="closePxSeatsWarningModal()"
                        class="absolute top-4 right-4 text-gray-400 hover:text-gray-500 z-50">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    <div class="bg-white px-4 mt-10 sm:mt-1 pb-4 pt-16 sm:p-6 sm:pb-4 sm:pt-16">
                        <div class="text-center sm:ml-4 sm:mt-0 sm:text-left">
                            <div class="">
                                <h3 class="text-3xl text-center font-FuturaMdCnBT text-gray-900 mb-4"
                                    id="px-seats-modal-title">Heads up for 5+ seats</h3>
                            </div>
                            <div class="mt-2 w-full">
                                <p class="can-exp-p text-center">Please note that for large vehicles, your total trip
                                    collection must stay within non-commercial limits. To keep this a standard carpool, we
                                    suggest a lower price per seat. By law, total contributions cannot exceed the standard
                                    reimbursement limit ($0.72/km).</p>
                            </div>
                        </div>
                    </div>
                    <div class="px-4 pb-6 pt-4 flex items-center space-x-2 justify-center">
                        <button type="button" onclick="closePxSeatsWarningModal()" class="button-exp-fill">Got
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
    <div id="pxPriceErrorModal" class="hidden fixed inset-0 z-50" aria-labelledby="px-price-error-modal-title"
        role="dialog" aria-modal="true">
        <div onclick="closePxPriceErrorModal()" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                <div
                    class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
                    <button type="button" onclick="closePxPriceErrorModal()"
                        class="absolute top-4 right-4 text-gray-400 hover:text-gray-500 z-50">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    <div class="bg-white px-4 mt-10 sm:mt-1 pb-4 pt-16 sm:p-6 sm:pb-4 sm:pt-16">
                        <div class="text-center sm:ml-4 sm:mt-0 sm:text-left">
                            <div class="">
                                <h3 class="text-3xl text-center font-FuturaMdCnBT text-gray-900 mb-4"
                                    id="pxPriceErrorHeading">Price Limit Exceeded</h3>
                            </div>
                            <div class="mt-2 w-full">
                                <p class="can-exp-p text-center mb-3" id="pxPriceErrorParagraph1"></p>
                                <p class="can-exp-p text-center mb-3" id="pxPriceErrorParagraph2"></p>
                                <p class="can-exp-p text-center" id="pxPriceErrorParagraph3"></p>
                            </div>
                        </div>
                    </div>
                    <div class="px-4 pb-6 pt-4 flex items-center space-x-2 sm:space-x-4 sm:px-6 justify-center">
                        <button type="button" id="pxPriceErrorAdjustBtn" onclick="adjustPxPriceFromError()"
                            class="button-exp-fill">Adjust Price</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Validation Error -->
    <div id="pxValidationErrorModal" class="hidden fixed inset-0 z-50" aria-labelledby="px-validation-error-modal-title"
        role="dialog" aria-modal="true">
        <div onclick="closePxValidationErrorModal()" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity">
        </div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                <div
                    class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
                    <button type="button" onclick="closePxValidationErrorModal()"
                        class="absolute top-4 right-4 text-gray-400 hover:text-gray-500 z-50">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    <div class="bg-white px-4 mt-10 sm:mt-1 pb-4 pt-16 sm:p-6 sm:pb-4 sm:pt-16">
                        <div class="text-center sm:ml-4 sm:mt-0 sm:text-left">
                            <div class="mx-auto h-16 w-16 flex items-center justify-center mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="2" stroke="currentColor" class="w-12 h-12 text-red-500">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                </svg>
                            </div>
                            <h3 class="text-3xl text-center font-FuturaMdCnBT text-gray-900 mb-4"
                                id="pxValidationErrorHeading">Validation Failed</h3>
                            <div class="mt-2 w-full">
                                <p class="can-exp-p text-center text-gray-700" id="pxValidationErrorParagraph"></p>
                            </div>
                        </div>
                    </div>
                    <div class="px-4 pb-6 pt-4 flex items-center justify-center sm:px-6">
                        <button type="button" onclick="closePxValidationErrorModal()"
                            class="button-exp-fill">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Price Warning (Exceeds $0.66/km per seat but <= $0.72/km per seat) -->
    <div id="pxPriceWarningModal" class="hidden fixed inset-0 z-50" aria-labelledby="px-price-warning-modal-title"
        role="dialog" aria-modal="true">
        <div onclick="closePxPriceWarningModal()" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity">
        </div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                <div
                    class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
                    <button type="button" onclick="closePxPriceWarningModal()"
                        class="absolute top-4 right-4 text-gray-400 hover:text-gray-500 z-50">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    <div class="bg-white px-4 mt-10 sm:mt-1 pb-4 pt-16 sm:p-6 sm:pb-4 sm:pt-16">
                        <div class="text-center sm:ml-4 sm:mt-0 sm:text-left">
                            <div class="">
                                <h3 class="text-3xl text-center font-FuturaMdCnBT text-gray-900 mb-4">Recommended
                                    Contribution Limit</h3>
                            </div>
                            <div class="mt-2 w-full">
                                <p class="can-exp-p text-center mb-3" id="pxPriceWarningParagraph1"></p>
                                <p class="can-exp-p text-center" id="pxPriceWarningParagraph2"></p>
                            </div>
                        </div>
                    </div>
                    <div class="px-4 pb-6 pt-4 flex items-center space-x-2 sm:space-x-4 sm:px-6 justify-center">
                        <button type="button" id="pxPriceWarningAdjustBtn"
                            onclick="adjustPxPriceFromWarning(); return false;" class="button-exp-fill">Adjust
                            Price</button>
                        <button type="button" id="pxPriceWarningContinue" class="button-exp-fill">Keep Current
                            Price</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
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

        document.addEventListener('DOMContentLoaded', function() {
            // Toggle vehicle form sections based on selected vehicle mode.
            const vehicleModeInputs = document.querySelectorAll('input[name="vehicle_mode"]');
            const existingVehicleFields = document.getElementById('px-vehicle-existing-fields');
            const newVehicleFields = document.getElementById('px-vehicle-new-fields');

            function setSectionEnabled(container, enabled) {
                if (!container) return;
                container.classList.toggle('hidden', !enabled);
                const fields = container.querySelectorAll('input, select, textarea');
                fields.forEach((field) => {
                    field.disabled = !enabled;
                });
            }

            function syncVehicleMode() {
                const selected = document.querySelector('input[name="vehicle_mode"]:checked')?.value || 'skip';
                setSectionEnabled(existingVehicleFields, selected === 'existing');
                setSectionEnabled(newVehicleFields, selected === 'add_new');
            }

            vehicleModeInputs.forEach((input) => {
                input.addEventListener('change', syncVehicleMode);
            });
            syncVehicleMode();

            // Expand/collapse ordered intermediate stops panel.
            const stopsToggle = document.getElementById('px-stops-toggle');
            const stopsContent = document.getElementById('px-stops-content');
            const stopsChevron = document.getElementById('px-stops-chevron');

            if (stopsToggle && stopsContent && stopsChevron) {
                stopsToggle.addEventListener('click', function() {
                    const expanded = stopsToggle.getAttribute('aria-expanded') === 'true';
                    const nextExpanded = !expanded;
                    stopsToggle.setAttribute('aria-expanded', nextExpanded ? 'true' : 'false');
                    stopsContent.classList.toggle('hidden', !nextExpanded);
                    stopsChevron.classList.toggle('rotate-180', nextExpanded);
                });
            }

            // Enable recurring inputs only when recurring trip is checked.
            const recurringToggle = document.getElementById('px-is-recurring');
            const recurringFields = document.getElementById('px-recurring-fields');
            const recurringInputs = recurringFields ? recurringFields.querySelectorAll('select, input, textarea') :
                [];

            function syncRecurringState() {
                if (!recurringToggle || !recurringFields) return;
                const enabled = recurringToggle.checked;

                // Show/hide the recurring fields block
                recurringFields.classList.toggle('hidden', !enabled);
                recurringFields.classList.toggle('opacity-60', !enabled);

                // Enable/disable inner inputs so they don't submit when hidden
                recurringInputs.forEach((el) => {
                    el.disabled = !enabled;
                    // If you ever want hard required on these, uncomment:
                    // if (el.name === 'recurring_frequency' || el.name === 'recurring_trips') {
                    //     el.required = enabled;
                    // }
                });
            }

            if (recurringToggle) {
                recurringToggle.addEventListener('change', syncRecurringState);
                syncRecurringState();
            }

            // Seat visual selector: highlight all seats up to selected total.
            window.showPxSeatsWarningModal = function() {
                openModalById('pxSeatsWarningModal');
            };

            window.closePxSeatsWarningModal = function() {
                closeModalById('pxSeatsWarningModal');
            };

            window.seat_selected = function(th, showWarning = true) {
                const seat = parseInt(th?.value || '0', 10);
                const maxSeats = 7;

                for (let i = 1; i <= maxSeats; i++) {
                    const image = document.querySelector('.seat-image.seat-unselect-' + i);
                    const number = document.querySelector('.seat-number.seat-number-' + i);
                    const selected = i <= seat;

                    if (image) {
                        image.src = selected ? '{{ asset('assets/seat-hover-1.png') }}' :
                            '{{ asset('assets/seat.png') }}';
                    }
                    if (number) {
                        number.classList.toggle('text-green-300', selected);
                    }
                }

                if (showWarning && seat >= 5) {
                    window.showPxSeatsWarningModal();
                }
            };
            window.seat_selected(document.querySelector('input[name="seats_total"]:checked'), false);

            const postRideForm = document.querySelector('form[action*="px.post_ride.store"]') || document
                .querySelector('form[action*="px.post_ride.update"]') || document.querySelector('form');
            const priceLabel = document.getElementById('px-price-label');
            const priceSingleWrap = document.getElementById('px-price-single-wrap');
            const priceSegmentsWrap = document.getElementById('px-price-segments-wrap');
            const priceSegmentsList = document.getElementById('px-price-segments-list');
            const priceSegmentsTotal = document.getElementById('px-price-segments-total');
            const priceMinorInput = document.getElementById('px-price-minor-input');
            const priceSingleExpected = document.getElementById('px-price-single-expected');
            const priceMinorHiddenInput = document.getElementById('px-price-minor-hidden');
            const destinationPriceDeltaInitialInput = document.getElementById('px-destination-price-delta-initial');
            const initialSegmentPricesJson = document.getElementById('px-initial-segment-prices-json');
            const currencySelect = document.getElementById('px-currency-select');
            const currencySymbols = @json($pxCurrencyMap);
            const segmentDistanceEstimateUrl = @json(route('px.post_ride.segment_distance_estimates', ['lang' => optional($selectedLanguage)->abbreviation]));
            let lastPxPriceValidationSignature = null;
            const initialSegmentPrices = (() => {
                if (!initialSegmentPricesJson) return [];
                try {
                    const parsed = JSON.parse(initialSegmentPricesJson.textContent || '[]');
                    return Array.isArray(parsed) ? parsed : [];
                } catch (error) {
                    return [];
                }
            })();
            let segmentDistanceState = {
                key: '',
                pendingKey: '',
                legDistancesMeters: [],
                segmentDistancesMeters: {},
                totalDistanceMeters: 0,
            };
            let segmentDistanceDebounceTimer = null;

            function getSelectedCurrencySymbol() {
                const currencyCode = currencySelect ? currencySelect.value.toUpperCase() : '';
                return currencySymbols[currencyCode] || (currencyCode ? `${currencyCode} ` : '');
            }

            function syncPricePlaceholders() {
                const currencySymbol = getSelectedCurrencySymbol();
                if (priceMinorInput) {
                    priceMinorInput.placeholder = `e.g. ${currencySymbol}25.00`;
                }
                if (priceSegmentsList) {
                    priceSegmentsList.querySelectorAll('.px-segment-price-input').forEach((input) => {
                        input.placeholder = `e.g. ${currencySymbol}12.00`;
                    });
                }
            }

            function toMinorInt(value) {
                const parsed = parseInt((value ?? '').toString().trim(), 10);
                return Number.isNaN(parsed) || parsed < 0 ? 0 : parsed;
            }

            function toMinorFromMajor(value) {
                const normalized = (value ?? '').toString().trim().replace(',', '.');
                const parsed = parseFloat(normalized);
                if (!Number.isFinite(parsed) || parsed < 0) {
                    return 0;
                }
                return Math.round(parsed * 100);
            }

            function toMajorFromMinor(minorValue) {
                return (toMinorInt(minorValue) / 100).toFixed(2);
            }

            function getFirstInputValueByName(name) {
                const direct = document.querySelector(`input[name="${name}"]`);
                if (direct) {
                    return direct.value.trim();
                }
                const anyInput = Array.from(document.querySelectorAll('input[type="text"],input[type="hidden"]'))
                    .find((input) => input.name && input.name.includes(name));
                return anyInput ? anyInput.value.trim() : '';
            }

            function getStopsData() {
                const stopLabelInputs = document.querySelectorAll('input[name^="stops["][name$="[label]"]');
                const stopCityIdInputs = document.querySelectorAll('input[name^="stops["][name$="[city_id]"]');
                const stopIsPickupInputs = document.querySelectorAll('input[name^="stops["][name$="[is_pickup]"]');
                const stopIsDropoffInputs = document.querySelectorAll(
                    'input[name^="stops["][name$="[is_dropoff]"]');
                const stopPriceDeltaInputs = document.querySelectorAll(
                    'input[name^="stops["][name$="[price_delta_minor]"]');
                const stopsData = new Map();

                stopLabelInputs.forEach(function(input) {
                    const match = input.name.match(/^stops\[(\d+)\]\[label\]$/);
                    if (!match) return;
                    const index = parseInt(match[1], 10);
                    if (!stopsData.has(index)) {
                        stopsData.set(index, {});
                    }
                    stopsData.get(index).label = input.value.trim();
                });

                stopCityIdInputs.forEach(function(input) {
                    const match = input.name.match(/^stops\[(\d+)\]\[city_id\]$/);
                    if (!match) return;
                    const index = parseInt(match[1], 10);
                    if (!stopsData.has(index)) {
                        stopsData.set(index, {});
                    }
                    stopsData.get(index).cityId = input.value.trim();
                });

                stopIsPickupInputs.forEach(function(input) {
                    const match = input.name.match(/^stops\[(\d+)\]\[is_pickup\]$/);
                    if (!match) return;
                    const index = parseInt(match[1], 10);
                    if (!stopsData.has(index)) {
                        stopsData.set(index, {});
                    }
                    stopsData.get(index).isPickup = input.value;
                });

                stopIsDropoffInputs.forEach(function(input) {
                    const match = input.name.match(/^stops\[(\d+)\]\[is_dropoff\]$/);
                    if (!match) return;
                    const index = parseInt(match[1], 10);
                    if (!stopsData.has(index)) {
                        stopsData.set(index, {});
                    }
                    stopsData.get(index).isDropoff = input.value;
                });

                stopPriceDeltaInputs.forEach(function(input) {
                    const match = input.name.match(/^stops\[(\d+)\]\[price_delta_minor\]$/);
                    if (!match) return;
                    const index = parseInt(match[1], 10);
                    if (!stopsData.has(index)) {
                        stopsData.set(index, {});
                    }
                    stopsData.get(index).priceDeltaMinor = toMinorInt(input.value);
                });

                return Array.from(stopsData.keys())
                    .sort((a, b) => a - b)
                    .map((index) => ({
                        index,
                        ...stopsData.get(index)
                    }));
            }

            function getValidStopsData() {
                return getStopsData().filter((stop) => stop.label && stop.cityId);
            }

            function hasValidCityId(fieldName) {
                const cityIdValue = getFirstInputValueByName(fieldName);
                if (cityIdValue === '') {
                    return false;
                }

                const parsed = Number.parseInt(cityIdValue, 10);
                return !Number.isNaN(parsed) && parsed > 0;
            }

            function canRequestDistanceEstimates(validStops) {
                if (!hasValidCityId('origin[city_id]') || !hasValidCityId('destination[city_id]')) {
                    return false;
                }

                return validStops.every((stop) => {
                    const parsed = Number.parseInt(stop.cityId || '0', 10);
                    return !Number.isNaN(parsed) && parsed > 0;
                });
            }

            function getSegmentPriceKey(fromIndex, toIndex) {
                return `${fromIndex}:${toIndex}`;
            }

            function getInitialSegmentPriceMap() {
                const priceMap = new Map();
                initialSegmentPrices.forEach((segmentPrice) => {
                    const fromIndex = Number.parseInt(segmentPrice?.from_index ?? '', 10);
                    const toIndex = Number.parseInt(segmentPrice?.to_index ?? '', 10);
                    const priceMinor = toMinorInt(segmentPrice?.price_minor ?? 0);
                    if (!Number.isNaN(fromIndex) && !Number.isNaN(toIndex) && toIndex > fromIndex) {
                        priceMap.set(getSegmentPriceKey(fromIndex, toIndex), priceMinor);
                    }
                });
                return priceMap;
            }

            function getAllRoutePoints(validStops) {
                const originLabel = getFirstInputValueByName('origin[label]') || 'Origin';
                const destinationLabel = getFirstInputValueByName('destination[label]') || 'Destination';

                return [{
                        index: 0,
                        label: originLabel
                    },
                    ...validStops.map((stop, idx) => ({
                        index: idx + 1,
                        label: stop.label || `Stop ${idx + 1}`,
                        stopIndex: stop.index
                    })),
                    {
                        index: validStops.length + 1,
                        label: destinationLabel
                    }
                ];
            }

            function getPointLabelsForDistance(points) {
                return points
                    .map((point) => (point?.label ?? '').toString().trim())
                    .filter((label) => label !== '');
            }

            function getDistanceRequestKey(points) {
                return getPointLabelsForDistance(points).join('||');
            }

            function buildLegacyAdjacentPriceMap(validStops, points) {
                const adjacentPriceMap = new Map();
                validStops.forEach((stop, idx) => {
                    adjacentPriceMap.set(getSegmentPriceKey(idx, idx + 1), toMinorInt(stop
                    .priceDeltaMinor));
                });

                const destinationDeltaMinor = toMinorInt(destinationPriceDeltaInitialInput?.value ?? 0);
                if (points.length >= 2) {
                    adjacentPriceMap.set(
                        getSegmentPriceKey(points.length - 2, points.length - 1),
                        destinationDeltaMinor
                    );
                }

                return adjacentPriceMap;
            }

            function resolveDefaultSegmentPriceMinor(fromIndex, toIndex, configuredPrices, adjacentPrices,
                parentPriceMinor) {
                const configuredPrice = configuredPrices.get(getSegmentPriceKey(fromIndex, toIndex));
                if (configuredPrice !== undefined) {
                    return configuredPrice;
                }

                let adjacentSum = 0;
                let hasAllAdjacentPrices = true;
                for (let idx = fromIndex; idx < toIndex; idx++) {
                    const adjacentPrice = adjacentPrices.get(getSegmentPriceKey(idx, idx + 1));
                    if (adjacentPrice === undefined) {
                        hasAllAdjacentPrices = false;
                        break;
                    }
                    adjacentSum += adjacentPrice;
                }

                if (hasAllAdjacentPrices && adjacentSum > 0) {
                    return adjacentSum;
                }

                if (fromIndex === 0 && parentPriceMinor > 0) {
                    return parentPriceMinor;
                }

                return 0;
            }

            function getSelectedSeatsTotal() {
                const selectedSeatsInput = document.querySelector('input[name="seats_total"]:checked');
                const selectedSeats = selectedSeatsInput ? Number.parseInt(selectedSeatsInput.value || '0', 10) : 0;
                return Number.isNaN(selectedSeats) || selectedSeats <= 0 ? 0 : selectedSeats;
            }

            function calculateExpectedSegmentPriceMinor(distanceMeters, seatsTotal) {
                const normalizedDistanceMeters = Math.max(0, Number.parseInt(distanceMeters || '0', 10) || 0);
                const normalizedSeatsTotal = Math.max(0, Number.parseInt(seatsTotal || '0', 10) || 0);

                if (normalizedDistanceMeters <= 0 || normalizedSeatsTotal <= 0) {
                    return {
                        suggestedMinor: 0,
                        maxMinor: 0,
                    };
                }

                const distanceKm = normalizedDistanceMeters / 1000;
                const suggestedMajor = (distanceKm * SOFT_WARNING_CAP) / normalizedSeatsTotal;
                const maxMajor = (distanceKm * ERROR_TRIGGERING_CAP) / normalizedSeatsTotal;

                return {
                    suggestedMinor: Math.round(suggestedMajor * 100),
                    maxMinor: Math.round(maxMajor * 100),
                };
            }

            function getSegmentDistanceMeters(fromIndex, toIndex) {
                const segmentDistancesMeters = segmentDistanceState &&
                    segmentDistanceState.segmentDistancesMeters &&
                    typeof segmentDistanceState.segmentDistancesMeters === 'object' ?
                    segmentDistanceState.segmentDistancesMeters :
                    {};

                return Number.parseInt(segmentDistancesMeters[`${fromIndex}:${toIndex}`] || '0', 10) || 0;
            }

            function refreshExpectedSegmentPriceHints() {
                if (!priceSegmentsList) {
                    return;
                }

                const allSegmentInputs = Array.from(priceSegmentsList.querySelectorAll('.px-segment-price-input'));
                if (allSegmentInputs.length === 0) {
                    return;
                }

                const seatsTotal = getSelectedSeatsTotal();

                allSegmentInputs.forEach((input) => {
                    const fromIndex = Number.parseInt(input.getAttribute('data-from-index') || '-1', 10);
                    const toIndex = Number.parseInt(input.getAttribute('data-to-index') || '-1', 10);
                    const hint = input.parentElement?.querySelector('.px-segment-price-expected');
                    if (Number.isNaN(fromIndex) || Number.isNaN(toIndex) || !hint) {
                        return;
                    }

                    let suggestedMinor = 0;
                    let maxMinor = 0;
                    let distanceSuffix = 'distance unavailable';

                    const segmentDistanceMeters = getSegmentDistanceMeters(fromIndex, toIndex);
                    input.setAttribute('data-distance-meters', String(segmentDistanceMeters));
                    if (segmentDistanceMeters > 0) {
                        const priceEstimate = calculateExpectedSegmentPriceMinor(segmentDistanceMeters,
                            seatsTotal);
                        suggestedMinor = priceEstimate.suggestedMinor;
                        maxMinor = priceEstimate.maxMinor;
                        distanceSuffix = `${(segmentDistanceMeters / 1000).toFixed(1)} km`;
                    }

                    const suggestedMajor = toMajorFromMinor(suggestedMinor);
                    const maxMajor = toMajorFromMinor(maxMinor);
                    hint.textContent =
                        `Suggested: ${getSelectedCurrencySymbol()}${suggestedMajor} | Max: ${getSelectedCurrencySymbol()}${maxMajor} (${distanceSuffix})`;
                });
            }

            function refreshSingleRouteExpectedPriceHint() {
                if (!priceSingleExpected) {
                    return;
                }

                const originLabel = getFirstInputValueByName('origin[label]');
                const destinationLabel = getFirstInputValueByName('destination[label]');
                const seatsTotal = getSelectedSeatsTotal();
                const totalDistanceMeters = toMinorInt(segmentDistanceState.totalDistanceMeters);

                if (!originLabel || !destinationLabel || seatsTotal <= 0) {
                    priceSingleExpected.classList.add('hidden');
                    priceSingleExpected.textContent = '';
                    return;
                }

                if (totalDistanceMeters <= 0) {
                    priceSingleExpected.classList.remove('hidden');
                    priceSingleExpected.textContent = 'Suggested/Max price will appear when distance is available.';
                    return;
                }

                const priceEstimate = calculateExpectedSegmentPriceMinor(totalDistanceMeters, seatsTotal);
                const suggestedMajor = toMajorFromMinor(priceEstimate.suggestedMinor);
                const maxMajor = toMajorFromMinor(priceEstimate.maxMinor);
                const distanceKm = (totalDistanceMeters / 1000).toFixed(1);

                priceSingleExpected.classList.remove('hidden');
                priceSingleExpected.textContent =
                    `Suggested: ${getSelectedCurrencySymbol()}${suggestedMajor} | Max: ${getSelectedCurrencySymbol()}${maxMajor} (${distanceKm} km)`;
            }

            async function requestSegmentDistanceEstimates(points) {
                const pointLabels = getPointLabelsForDistance(points);
                const requestKey = pointLabels.join('||');

                if (pointLabels.length < 2) {
                    segmentDistanceState = {
                        key: '',
                        pendingKey: '',
                        legDistancesMeters: [],
                        segmentDistancesMeters: {},
                        totalDistanceMeters: 0,
                    };
                    window.pxRideDistanceKm = null;
                    const distanceMetersInput = document.getElementById('px-distance-meters-input');
                    if (distanceMetersInput) {
                        distanceMetersInput.value = '';
                    }
                    refreshExpectedSegmentPriceHints();
                    refreshSingleRouteExpectedPriceHint();
                    return;
                }

                if (segmentDistanceState.key === requestKey || segmentDistanceState.pendingKey === requestKey) {
                    refreshExpectedSegmentPriceHints();
                    return;
                }

                segmentDistanceState.pendingKey = requestKey;

                const csrfToken = postRideForm?.querySelector('input[name="_token"]')?.value || '';

                try {
                    const response = await fetch(segmentDistanceEstimateUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify({
                            point_labels: pointLabels,
                        }),
                    });

                    if (!response.ok) {
                        throw new Error(`Distance estimate request failed: ${response.status}`);
                    }

                    const payload = await response.json();
                    if (segmentDistanceState.pendingKey !== requestKey) {
                        return;
                    }

                    segmentDistanceState = {
                        key: requestKey,
                        pendingKey: '',
                        legDistancesMeters: Array.isArray(payload.leg_distances_meters) ? payload
                            .leg_distances_meters : [],
                        segmentDistancesMeters: payload.segment_distances_meters && typeof payload
                            .segment_distances_meters === 'object' ?
                            payload.segment_distances_meters :
                            {},
                        totalDistanceMeters: Number.parseInt(payload.total_distance_meters || '0', 10) || 0,
                    };

                    if (segmentDistanceState.totalDistanceMeters > 0) {
                        window.pxRideDistanceKm = segmentDistanceState.totalDistanceMeters / 1000;
                        const distanceMetersInput = document.getElementById('px-distance-meters-input');
                        if (distanceMetersInput) {
                            distanceMetersInput.value = String(segmentDistanceState.totalDistanceMeters);
                        }
                    }
                } catch (error) {
                    if (segmentDistanceState.pendingKey === requestKey) {
                        segmentDistanceState.pendingKey = '';
                    }
                    console.warn('PX segment distance estimates failed', error);
                } finally {
                    refreshExpectedSegmentPriceHints();
                    refreshSingleRouteExpectedPriceHint();
                }
            }

            function scheduleSegmentDistanceEstimates(points) {
                if (segmentDistanceDebounceTimer) {
                    clearTimeout(segmentDistanceDebounceTimer);
                }

                segmentDistanceDebounceTimer = setTimeout(() => {
                    requestSegmentDistanceEstimates(points);
                }, 350);
            }

            function syncSegmentPriceTotal() {
                if (!priceSegmentsList || !priceMinorHiddenInput) return;

                const parentSegmentInput = priceSegmentsList.querySelector(
                    '.px-segment-price-input[data-parent-segment="1"]'
                );
                const parentPriceMinor = parentSegmentInput ? toMinorFromMajor(parentSegmentInput.value) : 0;
                priceMinorHiddenInput.value = String(parentPriceMinor);
                if (priceSegmentsTotal) {
                    priceSegmentsTotal.textContent = toMajorFromMinor(parentPriceMinor);
                }
                refreshExpectedSegmentPriceHints();
                refreshSingleRouteExpectedPriceHint();
                maybeShowPxLivePriceAlert();
            }

            function syncStopPriceDeltaInputsFromSegmentRows() {
                if (!priceSegmentsList) return;
                const segmentInputs = priceSegmentsList.querySelectorAll(
                    '.px-segment-price-input[data-adjacent-stop-index]');
                segmentInputs.forEach((input) => {
                    const stopIndex = input.getAttribute('data-adjacent-stop-index');
                    if (stopIndex === null || stopIndex === '') {
                        return;
                    }
                    const hiddenStopPrice = document.querySelector(
                        `input[name="stops[${stopIndex}][price_delta_minor]"]`);
                    if (hiddenStopPrice) {
                        hiddenStopPrice.value = String(toMinorFromMajor(input.value));
                    }
                });
            }

            function syncPriceInputMode() {
                if (!priceLabel || !priceSingleWrap || !priceSegmentsWrap || !priceMinorInput || !
                    priceMinorHiddenInput || !priceSegmentsList) {
                    return;
                }

                const validStops = getValidStopsData();
                const hasValidStops = validStops.length > 0;
                const canEstimateDistance = canRequestDistanceEstimates(validStops);

                if (!hasValidStops) {
                    const points = getAllRoutePoints(validStops);
                    if (canEstimateDistance) {
                        scheduleSegmentDistanceEstimates(points);
                    } else {
                        segmentDistanceState = {
                            key: '',
                            pendingKey: '',
                            legDistancesMeters: [],
                            segmentDistancesMeters: {},
                            totalDistanceMeters: 0,
                        };
                        window.pxRideDistanceKm = null;
                        const distanceMetersInput = document.getElementById('px-distance-meters-input');
                        if (distanceMetersInput) {
                            distanceMetersInput.value = '';
                        }
                    }
                    priceLabel.textContent = 'Price per Seat';
                    priceSingleWrap.classList.remove('hidden');
                    priceSegmentsWrap.classList.add('hidden');
                    priceSegmentsList.innerHTML = '';
                    priceMinorInput.disabled = false;
                    priceMinorInput.name = 'price_minor';
                    priceMinorHiddenInput.name = '';
                    refreshSingleRouteExpectedPriceHint();
                    return;
                }

                const points = getAllRoutePoints(validStops);
                if (canEstimateDistance) {
                    scheduleSegmentDistanceEstimates(points);
                } else {
                    segmentDistanceState = {
                        key: '',
                        pendingKey: '',
                        legDistancesMeters: [],
                        segmentDistancesMeters: {},
                        totalDistanceMeters: 0,
                    };
                    window.pxRideDistanceKm = null;
                    const distanceMetersInput = document.getElementById('px-distance-meters-input');
                    if (distanceMetersInput) {
                        distanceMetersInput.value = '';
                    }
                }

                priceLabel.textContent = 'Price per Seat (all route sections)';
                priceSingleWrap.classList.add('hidden');
                priceSegmentsWrap.classList.remove('hidden');
                priceMinorInput.disabled = true;
                priceMinorInput.name = '';
                priceMinorHiddenInput.name = 'price_minor';

                const previousValues = new Map();
                Array.from(priceSegmentsList.querySelectorAll('.px-segment-price-input')).forEach((input) => {
                    const fromIndex = input.getAttribute('data-from-index');
                    const toIndex = input.getAttribute('data-to-index');
                    if (fromIndex === null || toIndex === null) {
                        return;
                    }
                    previousValues.set(
                        getSegmentPriceKey(Number.parseInt(fromIndex, 10), Number.parseInt(toIndex,
                        10)),
                        toMinorFromMajor(input.value)
                    );
                });

                const configuredPrices = getInitialSegmentPriceMap();
                const adjacentPrices = buildLegacyAdjacentPriceMap(validStops, points);
                const baseTotalMinor = priceMinorHiddenInput.value ?
                    toMinorInt(priceMinorHiddenInput.value) :
                    toMinorFromMajor(priceMinorInput.value);
                priceSegmentsList.innerHTML = '';

                for (let fromIndex = 0; fromIndex < points.length - 1; fromIndex++) {
                    const group = document.createElement('div');
                    group.className = 'rounded-md border border-gray-200 bg-gray-50 p-3 space-y-2';

                    const groupTitle = document.createElement('div');
                    groupTitle.className = 'text-sm font-semibold text-gray-700';
                    groupTitle.textContent = `From ${points[fromIndex].label}`;
                    group.appendChild(groupTitle);

                    for (let toIndex = fromIndex + 1; toIndex < points.length; toIndex++) {
                        const from = points[fromIndex].label || 'Point A';
                        const to = points[toIndex].label || 'Point B';
                        const previousKey = getSegmentPriceKey(fromIndex, toIndex);
                        let initialMinor = previousValues.has(previousKey) ?
                            previousValues.get(previousKey) :
                            resolveDefaultSegmentPriceMinor(fromIndex, toIndex, configuredPrices, adjacentPrices,
                                baseTotalMinor);

                        if (fromIndex === 0 && toIndex === points.length - 1 && initialMinor <= 0 &&
                            baseTotalMinor > 0) {
                            initialMinor = baseTotalMinor;
                        }

                        const row = document.createElement('div');
                        row.className = 'grid grid-cols-1 md:grid-cols-2 gap-3 items-end';

                        const routeLabelWrap = document.createElement('div');
                        const routeLabel = document.createElement('label');
                        routeLabel.className = 'block text-primary text-gray-700';
                        routeLabel.textContent = `${from} \u2192 ${to}`;
                        routeLabelWrap.appendChild(routeLabel);

                        const expectedHint = document.createElement('p');
                        expectedHint.className = 'px-segment-price-expected text-xs text-gray-500 mt-1';
                        expectedHint.textContent = `Expected: ${getSelectedCurrencySymbol()}0.00`;
                        routeLabelWrap.appendChild(expectedHint);

                        const priceInput = document.createElement('input');
                        priceInput.type = 'number';
                        priceInput.min = '0';
                        priceInput.step = '0.01';
                        priceInput.value = toMajorFromMinor(initialMinor);
                        priceInput.className = 'px-segment-price-input w-full rounded border-gray-300';
                        priceInput.placeholder = `e.g. ${getSelectedCurrencySymbol()}12.00`;
                        priceInput.setAttribute('data-from-index', String(fromIndex));
                        priceInput.setAttribute('data-to-index', String(toIndex));
                        priceInput.setAttribute('data-distance-meters', '0');

                        if (toIndex === fromIndex + 1 && toIndex <= validStops.length) {
                            priceInput.setAttribute('data-adjacent-stop-index', String(validStops[toIndex - 1]
                                .index));
                        }

                        if (fromIndex === 0 && toIndex === points.length - 1) {
                            priceInput.setAttribute('data-parent-segment', '1');
                        }

                        priceInput.addEventListener('input', function() {
                            syncStopPriceDeltaInputsFromSegmentRows();
                            syncSegmentPriceTotal();
                            maybeShowPxLivePriceAlert(false, priceInput);
                        });

                        priceInput.addEventListener('blur', function() {
                            maybeShowPxLivePriceAlert(true, priceInput);
                        });

                        row.appendChild(routeLabelWrap);
                        row.appendChild(priceInput);
                        group.appendChild(row);
                    }

                    priceSegmentsList.appendChild(group);
                }

                syncStopPriceDeltaInputsFromSegmentRows();
                syncSegmentPriceTotal();
                refreshExpectedSegmentPriceHints();
            }

            if (priceMinorInput) {
                priceMinorInput.addEventListener('input', function() {
                    if (priceMinorHiddenInput) {
                        priceMinorHiddenInput.value = String(toMinorFromMajor(priceMinorInput.value));
                    }
                    maybeShowPxLivePriceAlert();
                });
                priceMinorInput.addEventListener('blur', function() {
                    maybeShowPxLivePriceAlert(true);
                });
            }

            if (currencySelect) {
                currencySelect.addEventListener('change', function() {
                    syncPricePlaceholders();
                    refreshExpectedSegmentPriceHints();
                });
            }

            document.addEventListener('input', function(event) {
                const target = event.target;
                if (!(target instanceof HTMLInputElement)) {
                    return;
                }
                if (target.name && (
                        target.name.includes('origin[label]') ||
                        target.name.includes('destination[label]') ||
                        target.name.match(/^stops\[\d+\]\[(label|city_id|price_delta_minor)\]$/)
                    )) {
                    syncPriceInputMode();
                }
            });

            document.addEventListener('change', function(event) {
                const target = event.target;
                if (!(target instanceof HTMLInputElement)) {
                    return;
                }

                if (target.name === 'seats_total') {
                    refreshExpectedSegmentPriceHints();
                    refreshSingleRouteExpectedPriceHint();
                    maybeShowPxLivePriceAlert();
                }
            });

            if (window.Livewire && typeof window.Livewire.hook === 'function') {
                window.Livewire.hook('message.processed', function() {
                    setTimeout(syncPriceInputMode, 60);
                });
            }

            syncPriceInputMode();
            syncPricePlaceholders();

            // Filter out empty stops before form submission
            if (postRideForm) {
                postRideForm.addEventListener('submit', function(event) {
                    // Check if bypass flag is already set (user already saw warning and chose to continue)
                    const bypassInput = postRideForm.querySelector('input[name="bypass_price_validation"]');
                    if (bypassInput && bypassInput.value === '1') {
                        console.log('PX Price validation bypassed - user already confirmed');
                        // Continue with normal form submission
                    } else {
                        const submitValidation = getPxSubmitValidationResult();

                        console.log('PX Form submission validation:', submitValidation);

                        if (submitValidation.type === 'error') {
                            event.preventDefault();
                            event.stopPropagation();
                            event.stopImmediatePropagation();
                            showPxPriceErrorModal(
                                submitValidation.maxPricePerSeat,
                                submitValidation.routeLabel
                            );
                            return false;
                        }

                        if (submitValidation.type === 'warning') {
                            event.preventDefault();
                            event.stopPropagation();
                            event.stopImmediatePropagation();
                            console.log('Showing PX soft warning modal');
                            showPxPriceWarningModal(function() {
                                console.log('User chose to keep current price, submitting PX form');
                                const bypassInput = document.createElement('input');
                                bypassInput.type = 'hidden';
                                bypassInput.name = 'bypass_price_validation';
                                bypassInput.value = '1';
                                postRideForm.appendChild(bypassInput);
                                const newForm = postRideForm.cloneNode(true);
                                postRideForm.parentNode.replaceChild(newForm, postRideForm);
                                newForm.submit();
                            }, submitValidation.routeLabel, submitValidation.softWarningPrice);
                            return false;
                        }
                    }

                    if (priceMinorInput && !priceMinorInput.disabled) {
                        priceMinorInput.value = String(toMinorFromMajor(priceMinorInput.value));
                    }
                    syncStopPriceDeltaInputsFromSegmentRows();
                    syncSegmentPriceTotal();

                    const stopLabelInputs = document.querySelectorAll(
                        'input[name^="stops["][name$="[label]"]');
                    const stopCityIdInputs = document.querySelectorAll(
                        'input[name^="stops["][name$="[city_id]"]');
                    const stopIsPickupInputs = document.querySelectorAll(
                        'input[name^="stops["][name$="[is_pickup]"]');
                    const stopIsDropoffInputs = document.querySelectorAll(
                        'input[name^="stops["][name$="[is_dropoff]"]');
                    const stopPriceDeltaInputs = document.querySelectorAll(
                        'input[name^="stops["][name$="[price_delta_minor]"]');
                    const existingDestinationPriceDeltaInput = postRideForm.querySelector(
                        'input[name="destination[price_delta_minor]"][data-generated="1"]');
                    if (existingDestinationPriceDeltaInput) {
                        existingDestinationPriceDeltaInput.remove();
                    }
                    postRideForm.querySelectorAll('input[data-generated-segment-price="1"]').forEach((
                        input) => {
                            input.remove();
                        });

                    const validStops = getValidStopsData().map((stop) => ({
                        label: stop.label,
                        cityId: stop.cityId,
                        isPickup: stop.isPickup || '1',
                        isDropoff: stop.isDropoff || '1',
                        priceDeltaMinor: toMinorInt(stop.priceDeltaMinor),
                    }));

                    // Source-of-truth for stop leg prices: visible segment rows.
                    // Map first N segment rows to N intermediate stops (in route order).
                    if (priceSegmentsList && validStops.length > 0) {
                        const adjacentSegmentInputs = Array.from(
                            priceSegmentsList.querySelectorAll(
                                '.px-segment-price-input[data-adjacent-stop-index]')
                        );
                        adjacentSegmentInputs.forEach((input) => {
                            const stopIndex = input.getAttribute('data-adjacent-stop-index');
                            if (stopIndex === null || stopIndex === '') {
                                return;
                            }
                            const matchingStop = validStops.find((stop) => String(stop.index) ===
                                String(stopIndex));
                            if (matchingStop) {
                                matchingStop.priceDeltaMinor = toMinorFromMajor(input.value);
                            }
                        });

                        const allSegmentInputs = Array.from(priceSegmentsList.querySelectorAll(
                            '.px-segment-price-input'));
                        const lastAdjacentSegmentInput = allSegmentInputs.find((input) => {
                            const fromIndex = Number.parseInt(input.getAttribute(
                                'data-from-index') || '-1', 10);
                            const toIndex = Number.parseInt(input.getAttribute('data-to-index') ||
                                '-1', 10);
                            const pointCount = validStops.length + 2;
                            return fromIndex === pointCount - 2 && toIndex === pointCount - 1;
                        });

                        if (lastAdjacentSegmentInput) {
                            const destinationPriceDeltaInput = document.createElement('input');
                            destinationPriceDeltaInput.type = 'hidden';
                            destinationPriceDeltaInput.name = 'destination[price_delta_minor]';
                            destinationPriceDeltaInput.value = String(toMinorFromMajor(
                                lastAdjacentSegmentInput
                                .value));
                            destinationPriceDeltaInput.setAttribute('data-generated', '1');
                            postRideForm.appendChild(destinationPriceDeltaInput);
                        }

                        allSegmentInputs.forEach((input, index) => {
                            const fromIndex = input.getAttribute('data-from-index');
                            const toIndex = input.getAttribute('data-to-index');
                            if (fromIndex === null || toIndex === null) {
                                return;
                            }

                            const priceMinor = toMinorFromMajor(input.value);
                            [
                                ['from_index', fromIndex],
                                ['to_index', toIndex],
                                ['price_minor', String(priceMinor)],
                            ].forEach(([field, value]) => {
                                const hiddenInput = document.createElement('input');
                                hiddenInput.type = 'hidden';
                                hiddenInput.name =
                                    `meta[segment_prices][${index}][${field}]`;
                                hiddenInput.value = value;
                                hiddenInput.setAttribute('data-generated-segment-price',
                                    '1');
                                postRideForm.appendChild(hiddenInput);
                            });
                        });
                    }

                    // Remove all existing stop inputs
                    stopLabelInputs.forEach(function(input) {
                        input.remove();
                    });
                    stopCityIdInputs.forEach(function(input) {
                        input.remove();
                    });
                    stopIsPickupInputs.forEach(function(input) {
                        input.remove();
                    });
                    stopIsDropoffInputs.forEach(function(input) {
                        input.remove();
                    });
                    stopPriceDeltaInputs.forEach(function(input) {
                        input.remove();
                    });

                    // Add back only valid stops with sequential indices
                    validStops.forEach(function(stop, newIndex) {
                        const labelInput = document.createElement('input');
                        labelInput.type = 'text';
                        labelInput.name = `stops[${newIndex}][label]`;
                        labelInput.value = stop.label;
                        labelInput.style.display = 'none';
                        postRideForm.appendChild(labelInput);

                        const cityIdInput = document.createElement('input');
                        cityIdInput.type = 'hidden';
                        cityIdInput.name = `stops[${newIndex}][city_id]`;
                        cityIdInput.value = stop.cityId;
                        postRideForm.appendChild(cityIdInput);

                        const isPickupInput = document.createElement('input');
                        isPickupInput.type = 'hidden';
                        isPickupInput.name = `stops[${newIndex}][is_pickup]`;
                        isPickupInput.value = stop.isPickup;
                        postRideForm.appendChild(isPickupInput);

                        const isDropoffInput = document.createElement('input');
                        isDropoffInput.type = 'hidden';
                        isDropoffInput.name = `stops[${newIndex}][is_dropoff]`;
                        isDropoffInput.value = stop.isDropoff;
                        postRideForm.appendChild(isDropoffInput);

                        const priceDeltaInput = document.createElement('input');
                        priceDeltaInput.type = 'hidden';
                        priceDeltaInput.name = `stops[${newIndex}][price_delta_minor]`;
                        priceDeltaInput.value = String(stop.priceDeltaMinor);
                        postRideForm.appendChild(priceDeltaInput);
                    });
                });
            }

            // Hide field tooltip error when user clicks/focuses inside its parent container.
            function hideTooltipInParent(eventTarget) {
                if (!(eventTarget instanceof HTMLElement) || !postRideForm) return;
                let node = eventTarget.closest('div, section, label');

                // Walk up until form root and remove tooltips that belong to the current field
                while (node && node !== postRideForm) {
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

            if (postRideForm) {
                postRideForm.addEventListener('click', function(event) {
                    hideTooltipInParent(event.target);
                });
                postRideForm.addEventListener('focusin', function(event) {
                    hideTooltipInParent(event.target);
                });
            }

            // Initialize departure date/time picker (combined)
            const departureAtInput = document.getElementById('px-departure-at');
            if (!departureAtInput || typeof flatpickr === 'undefined') {
                return;
            }

            const departureAtFp = flatpickr(departureAtInput, {
                enableTime: true,
                dateFormat: 'Y-m-d H:i',
                altInput: true,
                altFormat: 'F d, Y at H:i',
                minDate: 'today',
                time_24hr: true,
                minuteIncrement: 5,
            });

            // Client-side image preview for "add new vehicle" upload.
            const vehicleImageInput = document.getElementById('dropzone-file');
            const vehicleImagePreview = document.getElementById('px-vehicle-image-preview');
            if (vehicleImageInput && vehicleImagePreview) {
                vehicleImageInput.addEventListener('change', function(event) {
                    const file = event.target.files && event.target.files[0];
                    if (!file) {
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        vehicleImagePreview.src = e.target?.result || '';
                        vehicleImagePreview.classList.remove('w-12', 'h-12');
                        vehicleImagePreview.classList.add('w-40', 'h-40');
                    };
                    reader.readAsDataURL(file);
                });
            }

            // Toggle Pink Ride and Extra+ Ride disclaimers based on checkbox state
            function updateRideDisclaimers() {
                const pinkRideCheckbox = document.querySelector('input[data-ride-option-code="pink_rides"]');
                const extraCareCheckbox = document.querySelector('input[data-ride-option-code="extra_plus_rides"]');
                const pinkRideDisclaimer = document.getElementById('pink-ride-disclaimer');
                const extraCareDisclaimer = document.getElementById('extra-care-ride-disclaimer');
                const extraCareNumber = document.getElementById('extra-care-disclaimer-number');

                if (!pinkRideCheckbox || !extraCareCheckbox || !pinkRideDisclaimer || !extraCareDisclaimer || !
                    extraCareNumber) {
                    return;
                }

                const isPinkRideChecked = pinkRideCheckbox.checked;
                const isExtraCareChecked = extraCareCheckbox.checked;

                toggleElementHidden(pinkRideDisclaimer, !isPinkRideChecked);
                toggleElementHidden(extraCareDisclaimer, !isExtraCareChecked);

                // Update numbering: if pink ride is checked, extra+ is 6, otherwise 5
                if (isExtraCareChecked) {
                    extraCareNumber.textContent = isPinkRideChecked ? '6.' : '5.';
                }
            }

            // Add event listeners to ride option checkboxes
            document.addEventListener('change', function(event) {
                const target = event.target;
                if (target && target.hasAttribute('data-ride-option-code')) {
                    const optionCode = target.getAttribute('data-ride-option-code');
                    if (optionCode === 'pink_rides' || optionCode === 'extra_plus_rides') {
                        updateRideDisclaimers();
                    }
                }
            });

            // Initialize disclaimers on page load
            updateRideDisclaimers();

            // Handle server-side validation error (from validation redirect)
            @if (session('validation_error') && session('validation_heading'))
                const validationError = {
                    message: @json(session('validation_error')),
                    heading: @json(session('validation_heading', 'Validation Failed'))
                };
                if (validationError.message && validationError.heading) {
                    showPxValidationErrorModal(validationError.heading, validationError.message);
                }
            @endif

            // Handle server-side price error (from validation redirect)
            @if (session('error') && session('max_price_per_seat'))
                const serverError = {
                    message: @json(session('error')),
                    heading: @json(session('heading', 'Price Limit Exceeded')),
                    maxPricePerSeat: parseFloat(@json(session('max_price_per_seat')))
                };
                if (serverError.message && serverError.maxPricePerSeat) {
                    // Show the error modal first (this sets default content)
                    showPxPriceErrorModal(serverError.maxPricePerSeat);

                    // Then update with server-side message and heading
                    const errorHeading = document.getElementById('pxPriceErrorHeading');
                    if (errorHeading && serverError.heading) {
                        errorHeading.textContent = serverError.heading;
                    }
                    const errorPara1 = document.getElementById('pxPriceErrorParagraph1');
                    if (errorPara1 && serverError.message) {
                        errorPara1.textContent = serverError.message;
                    }
                }
            @endif

            // Handle server-side price warning (from validation redirect)
            @if (session('price_warning'))
                const priceWarning = @json(session('price_warning'));
                if (priceWarning && priceWarning.message) {
                    showPxPriceWarningModal(function() {
                        // User clicked "Keep Current Price" - submit the form with bypass flag
                        const bypassInput = document.createElement('input');
                        bypassInput.type = 'hidden';
                        bypassInput.name = 'bypass_price_validation';
                        bypassInput.value = '1';
                        postRideForm.appendChild(bypassInput);

                        // Remove the event listener to prevent re-validation
                        const newForm = postRideForm.cloneNode(true);
                        postRideForm.parentNode.replaceChild(newForm, postRideForm);
                        // Submit the form
                        newForm.submit();
                    });
                }
            @endif

            // Scroll to first error on page load if validation errors exist
            const firstError = document.querySelector('.tooltip-error');
            if (firstError) {
                // Find the parent container that contains the error (usually a form field wrapper)
                let errorContainer = firstError.closest('div');

                // Walk up to find a meaningful container (section or field wrapper)
                while (errorContainer && errorContainer !== postRideForm) {
                    // Check if this container is a section or has a meaningful structure
                    if (errorContainer.tagName === 'SECTION' ||
                        errorContainer.querySelector('input, select, textarea, label')) {
                        break;
                    }
                    errorContainer = errorContainer.parentElement;
                }

                // Scroll to the error container or the error itself
                const scrollTarget = errorContainer || firstError;
                scrollTarget.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }

        });

        // Cost-sharing cap validation constants
        const ERROR_TRIGGERING_CAP = 0.72; // $0.72 per km - BLOCK if exceeded
        const SOFT_WARNING_CAP = 0.66; // $0.66 per km - WARN but ALLOW

        // Store distance globally when available (from Google API calculation)
        window.pxRideDistanceKm = null;
        let lastPxPriceValidationInput = null;
        const acknowledgedPxWarningSignatures = new Set();

        function setModalVisibility(modalId, isVisible) {
            const modal = document.getElementById(modalId);
            if (!modal) {
                return null;
            }

            modal.classList.toggle('hidden', !isVisible);
            modal.style.display = isVisible ? 'block' : 'none';
            return modal;
        }

        function openModalById(modalId) {
            return setModalVisibility(modalId, true);
        }

        function closeModalById(modalId) {
            return setModalVisibility(modalId, false);
        }

        function setElementText(elementId, value) {
            const element = document.getElementById(elementId);
            if (element) {
                element.textContent = value;
            }
        }

        function toggleElementHidden(element, isHidden) {
            if (element) {
                element.classList.toggle('hidden', isHidden);
            }
        }

        function focusPxPriceInput(targetInput = null) {
            const priceInput = targetInput instanceof HTMLElement ? targetInput : document.getElementById(
                'px-price-minor-input');
            if (!priceInput) {
                return;
            }

            priceInput.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
            setTimeout(() => {
                priceInput.focus();
                priceInput.select();
            }, 300);
        }

        // Function to validate price per seat
        // Formula: (Distance × Cap) ÷ Seats = Max price per seat
        function validatePxPricePerSeat(priceMinor, distanceKm, seats) {
            if (!priceMinor || !distanceKm || !seats || distanceKm <= 0 || priceMinor <= 0 || seats <= 0) {
                console.log('PX Price validation skipped - missing data:', {
                    priceMinor,
                    distanceKm,
                    seats
                });
                return {
                    valid: true,
                    type: null
                };
            }

            // Convert price from minor units (cents) to major units (dollars)
            const pricePerSeat = parseFloat(priceMinor) / 100;
            const distance = parseFloat(distanceKm);
            const numSeats = parseInt(seats);

            // Calculate max allowed price per seat using Error-Triggering Cap: $0.72/km
            const maxPricePerSeat = (distance * ERROR_TRIGGERING_CAP) / numSeats;

            // Calculate soft warning price per seat: $0.66/km
            const softWarningPricePerSeat = (distance * SOFT_WARNING_CAP) / numSeats;

            console.log('PX Price validation calculations:', {
                pricePerSeat: pricePerSeat,
                distanceKm: distance,
                numSeats: numSeats,
                maxPricePerSeat: maxPricePerSeat,
                softWarningPricePerSeat: softWarningPricePerSeat,
                exceedsMax: pricePerSeat > maxPricePerSeat,
                exceedsSoftWarning: pricePerSeat > softWarningPricePerSeat
            });

            // Error-Triggering Cap: $0.72 per km - BLOCK if exceeded
            if (pricePerSeat > maxPricePerSeat) {
                console.log('PX ERROR CAP TRIGGERED');
                return {
                    valid: false,
                    type: 'error',
                    maxPricePerSeat: maxPricePerSeat.toFixed(2)
                };
            }

            // Soft Warning Cap: $0.66 per km - WARN but ALLOW
            if (pricePerSeat > softWarningPricePerSeat) {
                console.log('PX SOFT WARNING CAP TRIGGERED');
                return {
                    valid: true,
                    type: 'warning',
                    softWarningPrice: softWarningPricePerSeat.toFixed(2)
                };
            }

            console.log('PX No warning or error - price is within limits');
            return {
                valid: true,
                type: null
            };
        }

        function buildPxPriceValidationContext(priceMinor, seatsTotal, distanceMeters, routeLabel) {
            const parsedDistanceMeters = Number.parseInt(distanceMeters || '0', 10) || 0;

            return {
                priceMinor: Number.isNaN(priceMinor) ? 0 : priceMinor,
                seatsTotal: Number.isNaN(seatsTotal) ? 0 : seatsTotal,
                distanceKm: parsedDistanceMeters > 0 ? parsedDistanceMeters / 1000 : null,
                routeLabel,
            };
        }

        function getCurrentPxPriceValidationContext(sourceInput = null) {
            const selectedSeatsInput = document.querySelector('input[name="seats_total"]:checked');
            const seatsTotal = selectedSeatsInput ? parseInt(selectedSeatsInput.value || '0', 10) : 0;

            if (sourceInput instanceof HTMLInputElement && sourceInput.classList.contains('px-segment-price-input')) {
                const distanceMeters = Number.parseInt(sourceInput.getAttribute('data-distance-meters') || '0', 10) || 0;
                const routeLabel = sourceInput.parentElement?.querySelector('label')?.textContent?.trim() || 'this segment';
                const normalizedPrice = (sourceInput.value ?? '').toString().trim().replace(',', '.');
                const parsedPrice = parseFloat(normalizedPrice);
                const priceMinor = Number.isFinite(parsedPrice) && parsedPrice >= 0 ? Math.round(parsedPrice * 100) : 0;

                return buildPxPriceValidationContext(
                    priceMinor,
                    seatsTotal,
                    distanceMeters,
                    routeLabel
                );
            }

            const priceMinorInput = document.getElementById('px-price-minor-input');
            const priceMinorHiddenInput = document.getElementById('px-price-minor-hidden');
            const distanceMetersInput = document.getElementById('px-distance-meters-input');
            const priceMinor = priceMinorHiddenInput && priceMinorHiddenInput.name === 'price_minor' ?
                parseInt(priceMinorHiddenInput.value || '0', 10) :
                (priceMinorInput ? Math.round((parseFloat(priceMinorInput.value || '0') || 0) * 100) : 0);

            let distanceMeters = 0;
            if (distanceMetersInput && distanceMetersInput.value) {
                distanceMeters = parseInt(distanceMetersInput.value || '0', 10) || 0;
            } else if (window.pxRideDistanceKm) {
                distanceMeters = Math.round((parseFloat(window.pxRideDistanceKm) || 0) * 1000);
            }

            return buildPxPriceValidationContext(priceMinor, seatsTotal, distanceMeters, 'this trip');
        }

        function maybeShowPxLivePriceAlert(force = false, sourceInput = null) {
            lastPxPriceValidationInput = sourceInput instanceof HTMLElement ? sourceInput : document.getElementById(
                'px-price-minor-input');
            const {
                priceMinor,
                seatsTotal,
                distanceKm,
                routeLabel
            } = getCurrentPxPriceValidationContext(sourceInput);

            if (!priceMinor || !seatsTotal || !distanceKm || distanceKm <= 0) {
                lastPxPriceValidationSignature = null;
                return;
            }

            const validation = validatePxPricePerSeat(priceMinor, distanceKm, seatsTotal);

            if (!validation.type) {
                lastPxPriceValidationSignature = null;
                return;
            }

            const signature = JSON.stringify({
                type: validation.type,
                routeLabel,
                priceMinor,
                seatsTotal,
                distanceKm: Number(distanceKm).toFixed(2),
                maxPricePerSeat: validation.maxPricePerSeat ?? null,
                softWarningPrice: validation.softWarningPrice ?? null,
            });

            if (!force && lastPxPriceValidationSignature === signature) {
                return;
            }

            if (validation.type === 'warning' && acknowledgedPxWarningSignatures.has(signature)) {
                lastPxPriceValidationSignature = signature;
                return;
            }

            lastPxPriceValidationSignature = signature;

            if (!force) {
                return;
            }

            if (validation.type === 'error') {
                showPxPriceErrorModal(validation.maxPricePerSeat, routeLabel);
                return;
            }

            if (validation.type === 'warning') {
                showPxPriceWarningModal(function() {
                    closeModalById('pxPriceWarningModal');
                }, routeLabel, validation.softWarningPrice);
            }
        }

        function getPxSubmitValidationResult() {
            const segmentInputs = Array.from(document.querySelectorAll('.px-segment-price-input'));
            const contexts = [];

            if (segmentInputs.length > 0 && priceSegmentsWrap && !priceSegmentsWrap.classList.contains('hidden')) {
                segmentInputs.forEach((input) => {
                    contexts.push(getCurrentPxPriceValidationContext(input));
                });
            } else {
                contexts.push(getCurrentPxPriceValidationContext());
            }

            let firstWarning = null;

            for (const context of contexts) {
                if (!context.priceMinor || !context.seatsTotal || !context.distanceKm || context.distanceKm <= 0) {
                    continue;
                }

                const validation = validatePxPricePerSeat(
                    context.priceMinor,
                    context.distanceKm,
                    context.seatsTotal
                );

                if (!validation.valid) {
                    return {
                        type: 'error',
                        routeLabel: context.routeLabel,
                        maxPricePerSeat: validation.maxPricePerSeat,
                    };
                }

                if (validation.type === 'warning' && firstWarning === null) {
                    firstWarning = {
                        type: 'warning',
                        routeLabel: context.routeLabel,
                        softWarningPrice: validation.softWarningPrice,
                    };
                }
            }

            return firstWarning ?? {
                type: null,
            };
        }

        // Function to show error modal (Price Limit Exceeded)
        function showPxPriceErrorModal(maxPricePerSeat, routeLabel = 'this trip') {
            setElementText('pxPriceErrorParagraph1',
                'To comply with Canadian and Quebec carpooling regulations, the total amount collected for a trip cannot exceed the official 2026 reimbursement rate of $0.72/km.'
            );
            setElementText('pxPriceErrorParagraph2',
                'The maximum allowed for ' + routeLabel + ' is $' + maxPricePerSeat + ' per seat.'
            );
            setElementText('pxPriceErrorParagraph3',
                'This limit is mandatory to ensure your ride is classified as a non-commercial carpool, protecting your insurance coverage and maintaining the cost-sharing status of your contributions.'
            );
            openModalById('pxPriceErrorModal');
        }

        // Function to show warning modal (Recommended Contribution Limit)
        function showPxPriceWarningModal(callback, routeLabel = 'this trip', softWarningPrice = null) {
            console.log('showPxPriceWarningModal called');
            const modal = document.getElementById('pxPriceWarningModal');
            if (!modal) {
                console.error('PX Price warning modal not found!');
                return;
            }

            const para1 = document.getElementById('pxPriceWarningParagraph1');
            const para2 = document.getElementById('pxPriceWarningParagraph2');

            if (para1) {
                para1.textContent =
                    'The price you entered for ' + routeLabel +
                    ' is above the standard reimbursement rate recommended by the CRA and Revenu Québec';
            }
            if (para2) {
                para2.textContent = softWarningPrice ?
                    'We suggest keeping this segment at or below $' + softWarningPrice + ' per seat.' :
                    'While you can proceed, we suggest reducing the price per seat. This ensures your ride remains a standard carpool even if you drive long distances this year.';
            }

            modal.classList.remove('hidden');
            modal.style.display = 'block';

            // Set up continue button callback
            const continueBtn = document.getElementById('pxPriceWarningContinue');
            if (continueBtn) {
                continueBtn.onclick = function() {
                    if (lastPxPriceValidationSignature) {
                        acknowledgedPxWarningSignatures.add(lastPxPriceValidationSignature);
                    }
                    const activePostRideForm = document.querySelector('form[action*="px.post_ride.store"]') ||
                        document.querySelector('form[action*="px.post_ride.update"]') ||
                        document.querySelector('form');
                    if (!activePostRideForm || !activePostRideForm.querySelector(
                            'input[name="bypass_price_validation"]')) {
                        focusPxPriceInput(lastPxPriceValidationInput);
                    }
                    if (callback) callback();
                };
            }
        }

        // Function to adjust price from error (focus on price input field)
        function adjustPxPriceFromError() {
            closeModalById('pxPriceErrorModal');
            focusPxPriceInput(lastPxPriceValidationInput);
        }

        // Function to adjust price from warning (focus on price input field)
        function adjustPxPriceFromWarning() {
            console.log('Adjust PX Price clicked - closing modal and focusing on price field');
            closeModalById('pxPriceWarningModal');
            focusPxPriceInput(lastPxPriceValidationInput);
            return false;
        }

        function showPxValidationErrorModal(heading, message) {
            setElementText('pxValidationErrorHeading', heading);
            setElementText('pxValidationErrorParagraph', message);
            openModalById('pxValidationErrorModal');
        }

        function closePxPriceErrorModal() {
            closeModalById('pxPriceErrorModal');
        }

        function closePxPriceWarningModal() {
            closeModalById('pxPriceWarningModal');
        }

        function closePxValidationErrorModal() {
            closeModalById('pxValidationErrorModal');
        }
        document.addEventListener('keydown', function(event) {
            if (event.key !== 'Enter') {
                return;
            }

            const target = event.target;
            if (target instanceof HTMLInputElement && target.classList.contains('px-segment-price-input')) {
                event.preventDefault();
            }
        });
    </script>
@endsection
