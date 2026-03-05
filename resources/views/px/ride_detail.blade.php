@extends('layouts.template')

@section('content')
    <div class="container mx-auto my-10 xl:my-14 px-4 xl:px-0">
        @if ($ride->seats_available == 0)
            <div class="mt-4 rounded-lg px-6 py-3 bg-blue-100 text-gray-600" role="alert">
                {{ $rideDetailPage->all_seats_booked_label ?? 'All seats are booked' }}
            </div>
        @endif

        @if ($ride->status === 'cancelled')
            <div class="mt-4 rounded-lg px-6 py-3 bg-red-100 text-gray-600" role="alert">
                {{ $rideDetailPage->ride_canceller_by_driver ?? 'This ride was cancelled by the driver' }}
            </div>
        @endif

        <h1>{{ $rideDetailPage->main_heading ?? 'Ride detail' }}</h1>

        @php
            $parentOrigin = $ride->route->origin_label ?? 'N/A';
            $parentDestination = $ride->route->destination_label ?? 'N/A';
            $origin = $displayOrigin ?? ($ride->route->origin_label ?? 'N/A');
            $destination = $displayDestination ?? ($ride->route->destination_label ?? 'N/A');

            // Get pickup/dropoff locations from stops
            $orderedStops = $ride->stops->sortBy('stop_order');
            $firstStop = $orderedStops->first(); // First stop (origin)
            $lastStop = $orderedStops->last(); // Last stop (destination)

            // Find the stop that matches the displayed origin (could be a middle stop in segment view)
            $originStop = $orderedStops->first(function ($stop) use ($origin) {
                return trim($stop->label ?? '') === trim($origin);
            });

            // Find the stop that matches the displayed destination (could be a middle stop in segment view)
            $destinationStop = $orderedStops->first(function ($stop) use ($destination) {
                return trim($stop->label ?? '') === trim($destination);
            });

            // Use pickup_dropoff_location from the matching origin stop, otherwise fall back to first stop or meta
            $pickupLocation = null;
            if ($originStop && $originStop->pickup_dropoff_location) {
                $pickupLocation = $originStop->pickup_dropoff_location;
            } elseif ($firstStop && $firstStop->pickup_dropoff_location) {
                $pickupLocation = $firstStop->pickup_dropoff_location;
            } else {
                $pickupLocation = $ride->meta['pickup_location'] ?? null;
            }

            // Use pickup_dropoff_location from the matching destination stop, otherwise fall back to last stop or meta
            $dropoffLocation = null;
            if ($destinationStop && $destinationStop->pickup_dropoff_location) {
                $dropoffLocation = $destinationStop->pickup_dropoff_location;
            } elseif ($lastStop && $lastStop->pickup_dropoff_location) {
                $dropoffLocation = $lastStop->pickup_dropoff_location;
            } else {
                $dropoffLocation = $ride->meta['dropoff_location'] ?? null;
            }

            // Get departure date/time from origin stop (could be a middle stop in segment view)
            $originDepartureAt = null;
            if ($originStop && $originStop->eta_at) {
                $originDepartureAt = $originStop->eta_at;
            } elseif ($firstStop && $firstStop->eta_at) {
                $originDepartureAt = $firstStop->eta_at;
            } else {
                $originDepartureAt = $ride->departure_at;
            }

            $pricePerSeatMinor = (int) ($displayPriceMinor ?? $ride->price_minor);
            $currencyCode = strtoupper((string) ($ride->currency ?? ($selectedCurrency ?? 'USD')));
            $currencyMap = ['USD' => '$', 'CAD' => 'C$'];
            $currency = $currencyMap[$currencyCode] ?? $currencyCode . ' ';
            $segmentStops = $displaySegmentStops ?? collect();
            $segmentMode = (bool) ($isSegmentView ?? false);
        @endphp

        <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-y-4 md:gap-4">
            <div class="col-span-2">
                <x-px.ride-details
                    :ride="$ride"
                    :rideDetailPage="$rideDetailPage"
                    :parentOrigin="$parentOrigin"
                    :parentDestination="$parentDestination"
                    :origin="$origin"
                    :destination="$destination"
                    :pickupLocation="$pickupLocation"
                    :dropoffLocation="$dropoffLocation"
                    :originDepartureAt="$originDepartureAt"
                    :pricePerSeatMinor="$pricePerSeatMinor"
                    :currency="$currency"
                    :segmentStops="$segmentStops"
                    :segmentMode="$segmentMode"
                    :bookingModeLabel="$bookingModeLabel ?? null"
                    :bookingMethodLabel="$bookingMethodLabel ?? null"
                    :postRidePage="$postRidePage ?? null"
                />
            </div>

            <div class="col-span-1">
                <div class="space-y-4">
                    <div class="bg-white rounded-lg overflow-hidden shadow-3xl">
                        <h3 class="bg-primary text-white py-2 px-4 text-2xl xl:text-3xl">Driver</h3>
                        <div class="p-4">
                            <p class="text-black text-lg font-semibold">
                                {{ trim(($ride->driver->first_name ?? '') . ' ' . ($ride->driver->last_name ?? '')) ?: $ride->driver->name ?? 'N/A' }}
                            </p>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg overflow-hidden shadow-3xl">
                        <h3 class="bg-primary text-white py-2 px-4 text-2xl xl:text-3xl">
                            {{ $rideDetailPage->vehicle_info_label ?? 'Vehicle info' }}
                        </h3>
                        <div class="p-4">
                            @if ($ride->vehicle)
                                <div class="flex items-center flex-wrap gap-x-2 text-sm text-black">
                                    @if ($ride->vehicle->year)
                                        <p class="text-md">{{ $ride->vehicle->year }}</p>
                                    @endif
                                    <span>|</span>
                                    <p class="text-md">{{ $ride->vehicle->make }}</p>
                                    <span>|</span>
                                    <p class="text-md">{{ $ride->vehicle->model }}</p>
                                    @if ($ride->vehicle->color)
                                        <span>|</span>
                                        <p class="text-md">{{ $ride->vehicle->color }}</p>
                                    @endif
                                </div>
                                <p class="font-semibold text-lg text-black text-start">{{ $ride->vehicle->liscense_no }}
                                </p>
                            @else
                                <p class="text-gray-500">No vehicle information available</p>
                            @endif
                        </div>
                    </div>

                    @if (strtotime($ride->departure_at) > strtotime('now') && $ride->driver?->id !== Auth::id())
                        <div class="bg-white rounded-lg overflow-hidden shadow-3xl">
                            <h3 class="bg-primary text-white py-2 px-4 text-2xl xl:text-3xl">
                                @isset($rideDetailPage->driver_chat_heading)
                                    {{ $rideDetailPage->driver_chat_heading }}
                                @endisset
                            </h3>
                            <div class=" p-4 w-full">
                                <p>
                                    @isset($rideDetailPage->driver_chat_label)
                                        {{ $rideDetailPage->driver_chat_label }}
                                    @endisset
                                </p>
                                <div class="flex justify-center mt-4">
                                    @if (Auth::check())
                                        @if ($ride->driver?->id)
                                            <a href="{{ route('chat', ['lang' => app()->getLocale(), 'departure' => $ride->rideDetail[0]->departure ?? 'unknown', 'destination' => $ride->rideDetail[0]->destination ?? 'unknown', 'id' => $ride->id, 'passenger' => $ride->driver->id]) }}"
                                                class="bg-greenXS hover:bg-greenXS text-white text-base md:text-lg rounded font-FuturaMdCnBT hover:font-FuturaMdCnBT px-5 py-2 border border-greenXS hover:border-greenXS hover:text-white text-center focus:bg-greenXS focus:text-white active:text-white active:bg-greenXS w-36">
                                                @isset($rideDetailPage->driver_chat_button_label)
                                                    {{ $rideDetailPage->driver_chat_button_label }}
                                                @endisset
                                            </a>
                                        @endif
                                    @else
                                        <button type="button"
                                            class="bg-greenXS hover:bg-greenXS text-white text-base md:text-lg rounded font-FuturaMdCnBT hover:font-FuturaMdCnBT px-5 py-2 border border-greenXS hover:border-greenXS hover:text-white text-center focus:bg-greenXS focus:text-white active:text-white active:bg-greenXS w-36"
                                            onclick="togglePopupModal1()">
                                            @isset($rideDetailPage->driver_chat_button_label)
                                                {{ $rideDetailPage->driver_chat_button_label }}
                                            @endisset
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="bg-white rounded-lg overflow-hidden shadow-3xl">
                        <h3 class="bg-primary text-white py-2 px-4 text-2xl xl:text-3xl">
                            {{ $rideDetailPage->cancellation_policy_label ?? 'Cancellation policy' }}
                        </h3>
                        <div class="p-4">
                            <p class="text-lg">{{ $cancelationPolicyLabel ?? 'Standard' }}</p>
                        </div>
                    </div>

                    @if ($ride->driver?->id !== Auth::id())
                        @if (!empty($existingBooking))
                            <div class="flex justify-center mt-4 gap-3">
                                <a href="{{ route('px.booking.edit', ['lang' => optional($selectedLanguage)->abbreviation, 'id' => $existingBooking->id]) }}"
                                    class="group flex items-center button-exp-fill rounded cursor-pointer justify-center py-2 px-4 text-lg font-FuturaMdCnBT">
                                    <span class="font-medium text-xl">Update Booking</span>
                                </a>
                                <button type="button" onclick="toggleModal('px-cancel-booking-modal')"
                                    class="group flex items-center button-exp-no-fill rounded cursor-pointer justify-center py-2 px-4 text-lg font-FuturaMdCnBT">
                                    <span class="font-medium text-xl">Cancel Booking</span>
                                </button>
                            </div>
                        @elseif (
                            (int) $ride->seats_available > 0 &&
                                $ride->status !== 'cancelled' &&
                                strtotime($ride->departure_at) > strtotime('now'))
                            <div class="flex justify-center mt-4">
                                <a href="{{ route('px.booking', ['lang' => optional($selectedLanguage)->abbreviation, 'from_stop_id' => $selectedFromStopId, 'to_stop_id' => $selectedToStopId]) }}"
                                    class="group flex items-center button-exp-fill rounded cursor-pointer justify-center py-2 px-4 text-lg font-FuturaMdCnBT">
                                    @if ($bookingModeCode === 'manual')
                                        <img class="w-8 h-8 rounded-full"
                                            src="{{ asset('home_page_icons/' . $postRidePage->booking_option2->icon) }}"
                                            alt="">
                                    @elseif ($bookingModeCode === 'instant')
                                        <img class="w-8 h-8 rounded-full"
                                            src="{{ asset('home_page_icons/' . $postRidePage->booking_option1->icon) }}"
                                            alt="">
                                    @endif
                                    <span class="font-medium text-xl">
                                        Book Your Seats
                                    </span>
                                </a>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if (!empty($existingBooking))
        <div class="hidden overflow-x-hidden overflow-y-auto fixed inset-0 z-50 outline-none focus:outline-none justify-center items-center"
            id="px-cancel-booking-modal">
            <div class="relative w-auto my-6 mx-auto max-w-lg">
                <div
                    class="border-0 rounded-lg shadow-lg relative flex flex-col w-full bg-white outline-none focus:outline-none">
                    <div class="p-6 text-center">
                        <h3 class="text-xl font-FuturaMdCnBT text-gray-900 mb-3">Cancel Booking</h3>
                        <p class="text-gray-600 mb-6">Are you sure you want to cancel this booking?</p>
                        <div class="flex justify-center gap-3">
                            <form method="POST"
                                action="{{ route('px.booking.cancel', ['lang' => optional($selectedLanguage)->abbreviation, 'id' => $existingBooking->id]) }}">
                                @csrf
                                <button type="submit" class="button-exp-fill">Yes, cancel</button>
                            </form>
                            <button type="button" onclick="toggleModal('px-cancel-booking-modal')"
                                class="button-exp-no-fill">No</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="hidden opacity-25 fixed inset-0 z-40 bg-black" id="px-cancel-booking-modal-backdrop"></div>
    @endif
@endsection
