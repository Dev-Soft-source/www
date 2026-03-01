@extends('layouts.template')

@section('title', 'Post PX Ride')

@section('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endsection

@section('content')
    @php
        $oldDepartureAt = old('departure_at');
        $oldDepartureDate = old('departure_date');
        $oldDepartureTime = old('departure_time');
        $oldStops = old('stops', []);
        $stopsExpanded = !empty($oldStops);

        if (!$stopsExpanded && $errors->any()) {
            foreach ($errors->keys() as $errorKey) {
                if (str_starts_with($errorKey, 'stops')) {
                    $stopsExpanded = true;
                    break;
                }
            }
        }

        if ($oldDepartureAt && (!$oldDepartureDate || !$oldDepartureTime)) {
            try {
                $dt = \Illuminate\Support\Carbon::parse($oldDepartureAt);
                $oldDepartureDate = $oldDepartureDate ?: $dt->format('Y-m-d');
                $oldDepartureTime = $oldDepartureTime ?: $dt->format('H:i');
            } catch (\Throwable $e) {
                // Keep user-entered values as-is if parsing fails.
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
    @endphp
    <div class="container mx-auto my-10 px-4">
        <div class="max-w-4xl mx-auto bg-white border border-gray-200 rounded-xl shadow p-6 md:p-8">
            <div class="flex items-start justify-between gap-4 mb-6">
                <div>
                    <h1 class="text-2xl md:text-3xl font-FuturaMdCnBT text-primary mb-1">Post a PX Ride</h1>
                    <p class="text-sm text-gray-600">This creates rides in the new <code>px_*</code> schema.</p>
                </div>
                <a href="{{ route('post_ride', ['lang' => optional($selectedLanguage)->abbreviation]) }}"
                    class="button-exp-no-fill whitespace-nowrap">
                    Back to Legacy Post Ride
                </a>
            </div>

            @if (session('message'))
                <div class="mb-4 rounded-md border border-green-200 bg-green-50 text-green-700 px-4 py-3">
                    {{ session('message') }}
                </div>
            @endif

            {{-- @if ($errors->any())
                <div class="mb-4 rounded-md border border-red-200 bg-red-50 text-red-700 px-4 py-3">
                    <p class="font-semibold mb-1">Please fix the errors below:</p>
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif --}}

            <form method="POST"
                action="{{ route('px.post_ride.store', ['lang' => optional($selectedLanguage)->abbreviation]) }}"
                enctype="multipart/form-data" class="space-y-8">
                @csrf
                <p>{{ $postRidePage->indicates_required_field_text }}</p>
                <section>
                    <h2 class="text-xl font-FuturaMdCnBT text-gray-900 mb-4">Route</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold mb-1 required">{{ $postRidePage->from_label }}</label>
                            @livewire(
                                'px.city-autocomplete',
                                [
                                    'field' => 'origin',
                                    'placeholder' => $postRidePage->from_placeholder,
                                    'initialLabel' => old('origin.label'),
                                    'initialCityId' => old('origin.city_id'),
                                ],
                                key('px-origin-city-autocomplete')
                            )
                            @error('origin.label')
                                <div class="tooltip-error shadow-lg">{{ $message }}</div>
                            @enderror
                        </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1 required">{{ $postRidePage->to_label }}</label>
                        @livewire(
                            'px.city-autocomplete',
                            [
                                    'field' => 'destination',
                                    'placeholder' => $postRidePage->to_placeholder,
                                    'initialLabel' => old('destination.label'),
                                    'initialCityId' => old('destination.city_id'),
                                ],
                                key('px-destination-city-autocomplete')
                            )
                            @error('destination.label')
                                <div class="tooltip-error shadow-lg">{{ $message }}</div>
                            @enderror
                    </div>
                    <div>
                        <label
                            class="block text-sm font-semibold mb-1 required">{{ $postRidePage->pick_up_label }}</label>
                            <input name="origin[pickup_location]" value="{{ old('origin.pickup_location') }}" type="text"
                                class="w-full rounded border-gray-300" placeholder="Exact pick-up point" >
                            @error('origin.pickup_location')
                                <div class="tooltip-error shadow-lg">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label
                                class="block text-sm font-semibold mb-1 required">{{ $postRidePage->drop_off_label }}</label>
                            <input name="destination[dropoff_location]" value="{{ old('destination.dropoff_location') }}"
                                type="text" class="w-full rounded border-gray-300" placeholder="Exact drop-off point"
                                >
                            @error('destination.dropoff_location')
                                <div class="tooltip-error shadow-lg">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="mt-4 border border-gray-200 rounded-lg">
                        <button type="button" id="px-stops-toggle" class="w-full flex items-center justify-between text-left px-4 py-3"
                            aria-expanded="{{ $stopsExpanded ? 'true' : 'false' }}" aria-controls="px-stops-content">
                            <span class="block text-sm font-semibold">Ordered Intermediate Stops (optional)</span>
                            <svg id="px-stops-chevron" class="w-5 h-5 text-gray-500 transition-transform {{ $stopsExpanded ? 'rotate-180' : '' }}"
                                viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.51a.75.75 0 01-1.08 0l-4.25-4.51a.75.75 0 01.02-1.06z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div id="px-stops-content" class="px-4 pb-4 {{ $stopsExpanded ? '' : 'hidden' }}">
                            @livewire('px.stops-repeater', ['initialStops' => $oldStops], key('px-stops-repeater'))
                            <p class="text-xs text-gray-500 mt-1">
                                Search will use stop order direction. Example: B ? E valid, E ? B invalid.
                            </p>
                        </div>
                    </div>
                    <div class="mt-4 md:col-span-2 border border-gray-200 rounded-lg p-4">
                        <label class="inline-flex items-center gap-2 text-sm font-semibold mb-3">
                            <input type="hidden" name="is_recurring" value="0">
                            <input type="checkbox" id="px-is-recurring" name="is_recurring" value="1"
                                @checked(old('is_recurring')) class="rounded border-gray-300">
                            Recurring Trip
                        </label>
                        <div id="px-recurring-fields" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold mb-1 required">Frequency</label>
                                <select name="recurring_frequency" class="w-full rounded border-gray-300">
                                    <option value="">Select frequency</option>
                                    <option value="daily" @selected(old('recurring_frequency') === 'daily')>Daily</option>
                                    <option value="weekly" @selected(old('recurring_frequency') === 'weekly')>Weekly</option>
                                </select>
                                @error('recurring_frequency')
                                    <div class="tooltip-error shadow-lg">{{ $message }}</div>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-semibold mb-1 required">Number of Trips</label>
                                <input type="number" min="1" max="365" name="recurring_trips"
                                    value="{{ old('recurring_trips') }}" class="w-full rounded border-gray-300"
                                    placeholder="e.g. 10">
                                @error('recurring_trips')
                                    <div class="tooltip-error shadow-lg">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </section>

                <section>
                    <h2 class="text-xl font-FuturaMdCnBT text-gray-900 mb-4">Schedule & Price</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold mb-1 required">Departure Date</label>
                            <input id="px-departure-date" name="departure_date" value="{{ $oldDepartureDate }}"
                                type="text" class="w-full rounded border-gray-300" placeholder="Select departure date"
                                autocomplete="off" >
                            @error('departure_date')
                                <div class="tooltip-error shadow-lg">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-1 required">Departure Time</label>
                            <input id="px-departure-time" name="departure_time" value="{{ $oldDepartureTime }}"
                                type="text" class="w-full rounded border-gray-300" placeholder="Select departure time"
                                autocomplete="off" >
                            @error('departure_time')
                                <div class="tooltip-error shadow-lg">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="md:col-span-2 border border-gray-200 rounded-lg p-4">
                            <label class="block text-sm font-semibold mb-3 required">Total Seats</label>
                            <div class="flex items-center flex-wrap gap-2 mt-2">
                                @for ($i = 1; $i <= 7; $i++)
                                    <div class="relative">
                                        <label class="cursor-pointer inline-block" for="number-of-seat-{{ $i }}">
                                            <input
                                                id="number-of-seat-{{ $i }}"
                                                name="seats_total"
                                                type="radio"
                                                value="{{ $i }}"
                                                class="hidden"
                                                @checked((string) old('seats_total', '1') === (string) $i)
                                                onchange="seat_selected(this)"
                                                @required($i === 1)
                                            >
                                            <span class="relative inline-block w-10 h-10">
                                                <img
                                                    src="{{ (int) old('seats_total', 1) >= $i ? asset('assets/seat-hover-1.png') : asset('assets/seat.png') }}"
                                                    class="w-12 object-cover cursor-pointer seat-image seat-unselect-{{ $i }}"
                                                    alt=""
                                                >
                                                <span class="absolute mt-2 inset-0 flex items-center justify-center text-sm seat-number seat-number-{{ $i }} {{ (int) old('seats_total', 1) >= $i ? 'text-green-300' : '' }}">
                                                    {{ $i }}
                                                </span>
                                            </span>
                                        </label>
                                    </div>
                                @endfor
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold mb-2 required">Middle seats</label>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        @foreach([2, 3] as $seatCount)
                                            <label class="block cursor-pointer">
                                                <input
                                                    type="radio"
                                                    name="middle_seats"
                                                    value="{{ $seatCount }}"
                                                    @checked((string) old('middle_seats', '2') === (string) $seatCount)
                                                    @required($seatCount === 2)
                                                    class="peer sr-only"
                                                >
                                                <span class="flex items-center justify-center text-center rounded-lg border-2 border-gray-200 bg-gray-50 px-3 py-2 transition peer-checked:border-green-500 peer-checked:bg-blue-50 peer-checked:text-green-600">
                                                    <span class="leading-none">{{ $seatCount }} Seats</span>
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold mb-2 required">Back seats</label>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        @foreach([2, 3] as $seatCount)
                                            <label class="block cursor-pointer">
                                                <input
                                                    type="radio"
                                                    name="back_seats"
                                                    value="{{ $seatCount }}"
                                                    @checked((string) old('back_seats', '2') === (string) $seatCount)
                                                    @required($seatCount === 2)
                                                    class="peer sr-only"
                                                >
                                                <span class="flex items-center justify-center text-center rounded-lg border-2 border-gray-200 bg-gray-50 px-3 py-2 transition peer-checked:border-green-500 peer-checked:bg-blue-50 peer-checked:text-green-600">
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
                        <div class="md:col-span-2 border border-gray-200 rounded-lg p-4">
                            <label class="block text-sm font-semibold mb-1 required">Price per seat</label>
                            <input name="price_minor" value="{{ old('price_minor') }}" type="number" min="0"
                                class="w-full rounded border-gray-300" placeholder="e.g. 2500 = 25.00" >
                            @error('price_minor')
                                <div class="tooltip-error shadow-lg">{{ $message }}</div>
                            @enderror
                            @if ($bookingMethodGroup && $bookingMethodOptions->isNotEmpty())
                                <div class="mt-4">
                                    <label class="block text-sm font-semibold mb-1">Payment Method</label>
                                    <div class="space-y-2 mt-2">
                                        @foreach ($bookingMethodOptions as $option)
                                            @php
                                                $bookingMethodIcon = $bookingMethodIcons[$option->code] ?? null;
                                            @endphp
                                            <label class="flex items-center gap-2 text-sm">
                                                <input
                                                    type="radio"
                                                    name="booking_method"
                                                    value="{{ $option->id }}"
                                                    @checked((string) old('booking_method', $defaultBookingMethodId) === (string) $option->id)
                                                    class="mt-0.5"
                                                >
                                                @if ($bookingMethodIcon)
                                                    <img src="{{ asset('home_page_icons/' . $bookingMethodIcon) }}" class="h-6 w-6 object-contain" alt="">
                                                @endif
                                                <span class="font-medium text-gray-800">{{ $option->display_label }}</span>
                                                <span class="inline-flex cursor-help w-4 h-4" data-tippy-content="{{ $option->display_description }}">
                                                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M22 12C22 17.5228 17.5228 22 12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12ZM12 17.75C12.4142 17.75 12.75 17.4142 12.75 17V11C12.75 10.5858 12.4142 10.25 12 10.25C11.5858 10.25 11.25 10.5858 11.25 11V17C11.25 17.4142 11.5858 17.75 12 17.75ZM12 7C12.5523 7 13 7.44772 13 8C13 8.55228 12.5523 9 12 9C11.4477 9 11 8.55228 11 8C11 7.44772 11.4477 7 12 7Z" fill="#666666"></path></svg>
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        @if ($bookingModeGroup && $bookingModeOptions->isNotEmpty())
                            <div class="md:col-span-2 border border-gray-200 rounded-lg p-4">
                                <label class="block text-sm font-semibold mb-2">Booking Mode</label>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    @foreach ($bookingModeOptions as $option)
                                        @php
                                            $isInstant = $option->code === 'instant';
                                        @endphp
                                        <label class="block cursor-pointer">
                                            <input
                                                type="radio"
                                                name="booking_mode"
                                                value="{{ $option->id }}"
                                                @checked((string) old('booking_mode', $defaultBookingModeId) === (string) $option->id)
                                                class="peer sr-only"
                                            >
                                            <span class="flex items-center gap-3 rounded-lg border-2 border-gray-200 bg-gray-50 px-3 py-2 transition peer-checked:border-green-500 peer-checked:bg-blue-50">
                                                <span class="inline-flex items-center justify-center">
                                                    @if ($isInstant)
                                                        <img class="w-12 h-12" src="{{ asset('home_page_icons/' . $postRidePage->booking_option1->icon) }}" alt="">
                                                    @else
                                                        <img class="w-12 h-12" src="{{ asset('home_page_icons/' . $postRidePage->booking_option2->icon) }}" alt="">
                                                    @endif
                                                </span>
                                                <span class="flex items-center gap-2">
                                                    <span class="text-xl leading-none {{ $isInstant ? 'text-green-600' : 'text-blue-800' }}">{{ $option->display_label }}</span>
                                                    <span class="cursor-help w-4 h-4" data-tippy-content="{{ $option->display_description }}">
                                                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path fill-rule="evenodd" clip-rule="evenodd" d="M22 12C22 17.5228 17.5228 22 12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12ZM12 17.75C12.4142 17.75 12.75 17.4142 12.75 17V11C12.75 10.5858 12.4142 10.25 12 10.25C11.5858 10.25 11.25 10.5858 11.25 11V17C11.25 17.4142 11.5858 17.75 12 17.75ZM12 7C12.5523 7 13 7.44772 13 8C13 8.55228 12.5523 9 12 9C11.4477 9 11 8.55228 11 8C11 7.44772 11.4477 7 12 7Z" fill="#666666" /></svg>
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

                <section>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2 border border-gray-200 rounded-lg p-4">
                            <label class="block text-sm font-semibold mb-2">Vehicle</label>
                            <div class="flex flex-wrap items-center gap-4">
                                <label class="inline-flex items-center gap-2 text-sm">
                                    <input type="radio" name="vehicle_mode" value="skip"
                                        class="rounded border-gray-300" @checked(old('vehicle_mode', 'skip') === 'skip')>
                                    Skip This Time
                                </label>
                                <label class="inline-flex items-center gap-2 text-sm">
                                    <input type="radio" name="vehicle_mode" value="add_new"
                                        class="rounded border-gray-300" @checked(old('vehicle_mode') === 'add_new')>
                                    Add new vehicle
                                </label>
                                <label class="inline-flex items-center gap-2 text-sm">
                                    <input type="radio" name="vehicle_mode" value="existing"
                                        class="rounded border-gray-300" @checked(old('vehicle_mode') === 'existing')>
                                    Existing
                                </label>
                            </div>


                            <div id="px-vehicle-existing-fields" class="md:col-span-2 mt-4">
                                <label class="block text-sm font-semibold mb-1">Existing Vehicle</label>
                                <select name="vehicle_id" class="w-full rounded border-gray-300">
                                    <option value="">Select vehicle</option>
                                    @foreach ($vehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}" @selected((string) old('vehicle_id') === (string) $vehicle->id)>
                                            #{{ $vehicle->id }} - {{ $vehicle->make }} {{ $vehicle->model }}
                                            ({{ $vehicle->liscense_no }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div id="px-vehicle-new-fields"
                                class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                <div>
                                    <label class="block text-sm font-semibold mb-1 required">Make</label>
                                    <input name="new_vehicle[make]" value="{{ old('new_vehicle.make') }}" type="text"
                                        class="w-full rounded border-gray-300">
                                    @error('new_vehicle.make')
                                        <div class="tooltip-error shadow-lg">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold mb-1 required">Model</label>
                                    <input name="new_vehicle[model]" value="{{ old('new_vehicle.model') }}"
                                        type="text" class="w-full rounded border-gray-300">
                                    @error('new_vehicle.model')
                                        <div class="tooltip-error shadow-lg">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold mb-1 required">Vehicle Type</label>
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
                                    <label class="block text-sm font-semibold mb-1 required">License Plate Number</label>
                                    <input name="new_vehicle[liscense_no]" value="{{ old('new_vehicle.liscense_no') }}"
                                        maxlength="8" type="text" class="w-full rounded border-gray-300">
                                    @error('new_vehicle.liscense_no')
                                        <div class="tooltip-error shadow-lg">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold mb-1 required">Color</label>
                                    <input name="new_vehicle[color]" value="{{ old('new_vehicle.color') }}"
                                        maxlength="15" type="text" class="w-full rounded border-gray-300">
                                    @error('new_vehicle.color')
                                        <div class="tooltip-error shadow-lg">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold mb-1 required">Year</label>
                                    <input name="new_vehicle[year]" value="{{ old('new_vehicle.year') }}" maxlength="4"
                                        type="text" class="w-full rounded border-gray-300">
                                    @error('new_vehicle.year')
                                        <div class="tooltip-error shadow-lg">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold mb-1">Fuel</label>
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
                                    <label class="block text-sm font-semibold mb-1">Set as primary vehicle</label>
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
                                    <label class="block text-sm font-semibold mb-1">Car Image (optional)</label>
                                    <label for="dropzone-file"
                                        class="flex flex-col items-center justify-center w-full h-auto border-2 border-gray-300 border-dashed rounded cursor-pointer bg-gray-100 hover:bg-gray-100">
                                        <div class="flex flex-col items-center justify-center pt-5 pb-6 p-4">
                                            <img id="px-vehicle-image-preview"
                                                class="w-12 h-12 object-contain mb-4 cursor-pointer"
                                                src="{{ asset('assets/image-placeholder.png') }}" alt="">
                                            <p class="text-sm lg:text-lg text-gray-900">Upload car photo.</p>
                                            <p class="text-sm lg:text-base text-gray-900 font-normal">JPEG, JPG, PNG, GIF -
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
                </section>

                <section>
                    <div class="space-y-5">
                        @foreach ($optionGroups as $group)
                            @continue($group->code === 'booking_method')
                            @continue($group->code === 'booking_mode')
                            <div class="border border-gray-200 rounded-lg p-4">
                                <h3 class="text-base font-semibold text-gray-800 mb-3">
                                    {{ ucwords(str_replace('_', ' ', $group->code)) }}</h3>

                                <div class="grid grid-cols-1 gap-3">
                                    @foreach ($group->options as $option)
                                        <label class="flex items-start gap-2 text-sm">
                                            @if ($group->is_checkbox)
                                                <input type="checkbox" name="ride_option_ids[]"
                                                    value="{{ $option->id }}" @checked(in_array($option->id, old('ride_option_ids', [])))
                                                    class="mt-0.5">
                                            @else
                                                <input type="radio" name="{{ $group->code }}"
                                                    value="{{ $option->id }}" @checked((string) old($group->code, optional($group->options->first())->id) === (string) $option->id)
                                                    class="mt-0.5">
                                            @endif
                                            {{-- icon --}}
                                            @if ($group->code === 'luggage_size')
                                                @php
                                                    $luggageIcons = [
                                                        'no_luggage' => optional($postRidePage->luggage_option1)
                                                            ->icon,
                                                        'small' => optional($postRidePage->luggage_option2)->icon,
                                                        'medium' => optional($postRidePage->luggage_option3)->icon,
                                                        'large' => optional($postRidePage->luggage_option4)->icon,
                                                        'xl_multiple' => optional($postRidePage->luggage_option5)
                                                            ->icon,
                                                    ];
                                                    $luggageIcon = $luggageIcons[$option->code] ?? null;
                                                @endphp
                                                @if ($luggageIcon)
                                                    <img src="{{ asset('home_page_icons/' . $luggageIcon) }}"
                                                        class="w-6 h-6 object-contain" alt="">
                                                @endif
                                            @endif
                                            <span class="font-medium text-gray-800">{{ $option->display_label }}</span>
                                            <span class="inline-flex cursor-help w-4 h-4"
                                                data-tippy-content="{{ $option->display_description }}">
                                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path fill-rule="evenodd" clip-rule="evenodd" d="M22 12C22 17.5228 17.5228 22 12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12ZM12 17.75C12.4142 17.75 12.75 17.4142 12.75 17V11C12.75 10.5858 12.4142 10.25 12 10.25C11.5858 10.25 11.25 10.5858 11.25 11V17C11.25 17.4142 11.5858 17.75 12 17.75ZM12 7C12.5523 7 13 7.44772 13 8C13 8.55228 12.5523 9 12 9C11.4477 9 11 8.55228 11 8C11 7.44772 11.4477 7 12 7Z" fill="#666666" /></svg>
                                            </span>
                                        </label>
                                    @endforeach
                                </div>

                                @if ($group->code === 'luggage_size')
                                    <div class="mt-3 pt-3 border-t border-gray-100">
                                        <label class="inline-flex items-start gap-2 text-sm">
                                            <input type="hidden" name="accept_more_luggage" value="0">
                                            <input type="checkbox" name="accept_more_luggage" value="1"
                                                @checked(old('accept_more_luggage')) class="mt-0.5">
                                            <span class="font-medium text-gray-800">I accept more luggage for extra
                                                charge</span>
                                        </label>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </section>

                <section>
                    <h2 class="text-xl font-FuturaMdCnBT text-gray-900 mb-4">Anything to Add?</h2>
                    <textarea name="notes" rows="5" class="w-full rounded border-gray-300" placeholder="Optional ride notes">{{ old('notes') }}</textarea>
                </section>

                <section>
                    <h2 class="text-xl font-FuturaMdCnBT text-gray-900 mb-4">Disclaimers</h2>
                    <div class="space-y-5">
                        <div class="border border-gray-200 rounded-lg p-4">
                        </div>
                    </div>
                </section>


                <section>
                    <label class="inline-flex items-start gap-2 text-sm">
                        <input type="checkbox" name="agree_terms" value="1" @checked(old('agree_terms'))
                            class="mt-0.5 rounded border-gray-300" >
                        <span>I have read and agree to these rules, as well as ProximaRide's policies, terms and
                            conditions.</span>
                    </label>
                    @error('agree_terms')
                        <div class="tooltip-error shadow-lg">{{ $message }}</div>
                    @enderror
                </section>

                <div class="pt-2">
                    <button type="submit" class="button-exp-fill">Post PX Ride</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
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
            const recurringInputs = recurringFields ? recurringFields.querySelectorAll('select, input') : [];

            function syncRecurringState() {
                if (!recurringToggle || !recurringFields) return;
                const enabled = recurringToggle.checked;
                recurringFields.classList.toggle('opacity-60', !enabled);
                recurringInputs.forEach((el) => {
                    el.disabled = !enabled;
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
            window.seat_selected = function(th) {
                const seat = parseInt(th?.value || '0', 10);
                const maxSeats = 7;

                for (let i = 1; i <= maxSeats; i++) {
                    const image = document.querySelector('.seat-image.seat-unselect-' + i);
                    const number = document.querySelector('.seat-number.seat-number-' + i);
                    const selected = i <= seat;

                    if (image) {
                        image.src = selected ? '{{ asset('assets/seat-hover-1.png') }}' : '{{ asset('assets/seat.png') }}';
                    }
                    if (number) {
                        number.classList.toggle('text-green-300', selected);
                    }
                }
            };
            window.seat_selected(document.querySelector('input[name="seats_total"]:checked'));

            const postRideForm = document.querySelector('form[action*="px.post_ride.store"]') || document.querySelector('form');

            // Hide field tooltip error when user clicks/focuses inside its parent container.
            function hideTooltipInParent(eventTarget) {
                if (!(eventTarget instanceof HTMLElement) || !postRideForm) return;
                let node = eventTarget.closest('div');

                // Walk up until form root and remove only a tooltip that belongs
                // to the current field wrapper (direct child), not nested/global tooltips.
                while (node && node !== postRideForm) {
                    const tooltip = Array.from(node.children).find((child) =>
                        child instanceof HTMLElement && child.classList.contains('tooltip-error')
                    );
                    if (tooltip) {
                        tooltip.remove();
                        return;
                    }
                    node = node.parentElement?.closest('div') || null;
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

            // Initialize departure date/time pickers and guard submission for empty values.
            const departureDateInput = document.getElementById('px-departure-date');
            const departureTimeInput = document.getElementById('px-departure-time');
            if (!departureDateInput || !departureTimeInput || typeof flatpickr === 'undefined') {
                return;
            }

            const departureDateFp = flatpickr(departureDateInput, {
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'F d, Y',
                minDate: 'today',
            });

            const departureTimeFp = flatpickr(departureTimeInput, {
                enableTime: true,
                noCalendar: true,
                time_24hr: true,
                minuteIncrement: 5,
                dateFormat: 'H:i',
            });

            // if (postRideForm && departureDateFp && departureDateFp.input && departureDateFp.altInput &&
            //     departureTimeFp && departureTimeFp.input && departureTimeFp.altInput) {
            //     // On submit, if either departure date or time is empty, prevent submission and focus the empty field.
            //     postRideForm.addEventListener('submit', function(e) {
            //         if (!departureDateFp.input.value) {
            //             e.preventDefault();
            //             departureDateFp.altInput.scrollIntoView({
            //                 behavior: 'smooth',
            //                 block: 'center'
            //             });
            //             departureDateFp.altInput.focus();
            //             return;
            //         }

            //         if (!departureTimeFp.input.value) {
            //             e.preventDefault();
            //             departureTimeFp.altInput.scrollIntoView({
            //                 behavior: 'smooth',
            //                 block: 'center'
            //             });
            //             departureTimeFp.altInput.focus();
            //         }
            //     });
            // }

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

        });
    </script>
@endsection

