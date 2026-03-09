@extends('layouts.template')

@section('content')
    @php
        $currencySymbol = (string) ($rideDetailsData['currency'] ?? '$');
        $totalPriceMinor = (int) ($booking->total_price_minor ?? 0);
        $segmentPriceMinor = (int) ($booking->segment_price_minor ?? 0);
        $seats = max(1, (int) ($booking->seats ?? 1));
    @endphp

    <div class="container mx-auto my-10 xl:my-14 px-4 xl:px-0">
        <div class="max-w-4xl mx-auto">
            <h1>{{ optional($tripsPage)->cancel_booking_heading ?? 'Cancel booking' }}</h1>

            <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-4">
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg overflow-hidden shadow-3xl">
                        <h3 class="bg-primary text-white py-2 px-4 text-2xl xl:text-3xl">
                            {{ optional($rideDetailPage)->main_heading ?? 'Ride detail' }}
                        </h3>
                        <div class="p-4">
                            <x-px.ride-details :ride="$ride" :rideDetailPage="$rideDetailPage"
                                :parentOrigin="$rideDetailsData['parentOrigin']" :parentDestination="$rideDetailsData['parentDestination']"
                                :origin="$rideDetailsData['origin']" :destination="$rideDetailsData['destination']"
                                :pickupLocation="$rideDetailsData['pickupLocation']" :dropoffLocation="$rideDetailsData['dropoffLocation']"
                                :originDepartureAt="$rideDetailsData['originDepartureAt']" :pricePerSeatMinor="$rideDetailsData['pricePerSeatMinor']"
                                :currency="$rideDetailsData['currency']" :segmentStops="$rideDetailsData['segmentStops']"
                                :segmentMode="$rideDetailsData['segmentMode']"
                                :bookingModeLabel="$rideOptionDisplay['bookingModeLabel'] ?? null"
                                :bookingMethodLabel="$rideOptionDisplay['bookingMethodLabel'] ?? null"
                                :bookingModeCode="$rideOptionDisplay['bookingModeCode'] ?? null"
                                :bookingMethodCode="$rideOptionDisplay['bookingMethodCode'] ?? null"
                                :postRidePage="null" />
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg overflow-hidden shadow-3xl">
                        <h3 class="bg-primary text-white py-2 px-4 text-2xl xl:text-3xl">
                            {{ optional($tripsPage)->cancel_booking_main_heading ?? 'Cancel my booking' }}
                        </h3>
                        <form method="POST"
                            action="{{ route('px.booking.cancel', ['lang' => optional($selectedLanguage)->abbreviation, 'id' => $booking->id]) }}"
                            class="p-4 space-y-4" id="pxCancelBookingForm">
                            @csrf

                            <div class="rounded-lg bg-red-50 border border-red-200 p-4 text-red-700">
                                {{ optional($tripsPage)->cancel_booking_confirm_message ?? 'Are you sure you want to cancel this booking?' }}
                            </div>

                            <div class="rounded-lg bg-gray-50 p-4 space-y-2 text-black">
                                <div class="flex items-center justify-between gap-3">
                                    <span>{{ optional($tripsPage)->number_of_seat_booked ?? 'Number of seats booked' }}</span>
                                    <span class="font-semibold">{{ $seats }}</span>
                                </div>
                                <div class="flex items-center justify-between gap-3">
                                    <span>{{ optional($rideDetailPage)->price_per_seat_label ?? 'Price per seat' }}</span>
                                    <span class="font-semibold">{{ $currencySymbol . number_format($segmentPriceMinor / 100, 2) }}</span>
                                </div>
                                <div class="flex items-center justify-between gap-3">
                                    <span>{{ optional($tripsPage)->booking_cancel_btn_label ?? 'Cancellation total' }}</span>
                                    <span class="font-semibold">{{ $currencySymbol . number_format($totalPriceMinor / 100, 2) }}</span>
                                </div>
                                <div class="flex items-center justify-between gap-3">
                                    <span>{{ optional($rideDetailPage)->cancellation_policy_label ?? 'Cancellation policy' }}</span>
                                    <span class="font-semibold">{{ $rideOptionDisplay['cancelationPolicyLabel'] ?? 'Standard' }}</span>
                                </div>
                            </div>

                            <div>
                                <label for="px-cancel-booking-message" class="block text-lg font-medium text-gray-900 mb-2">
                                    {{ optional($tripsPage)->cancel_message_title ?? 'Message to your driver' }}
                                </label>
                                <textarea id="px-cancel-booking-message" rows="5" name="message"
                                    class="block p-2.5 w-full text-gray-900 bg-white rounded border border-gray-300 focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500"
                                    placeholder="{{ optional($tripsPage)->cancel_booking_trip_placeholder ?? 'Optional: tell your driver why you are cancelling this booking.' }}">{{ old('message') }}</textarea>
                                @error('message')
                                    <div class="tooltip-error shadow-lg mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="flex justify-center gap-3 pt-2">
                                <a href="{{ route('px.ride_detail', ['lang' => optional($selectedLanguage)->abbreviation, 'id' => $ride->id, 'from_stop_id' => (int) $booking->from_stop_id, 'to_stop_id' => (int) $booking->to_stop_id]) }}"
                                    class="button-exp-no-fill">
                                    {{ optional($tripsPage)->booking_cancel_btn_no_label ?? 'No, take me back' }}
                                </a>
                                <button type="submit" class="button-exp-fill">
                                    {{ optional($tripsPage)->booking_cancel_btn_yes_label ?? 'Yes, cancel it!' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
