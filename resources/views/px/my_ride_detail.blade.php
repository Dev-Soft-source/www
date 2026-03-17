@extends('layouts.template')

@section('content')

    @if (session('message'))
        <div class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div
                    class="relative flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full w-full">
                    <div
                        class="relative animate__animated animate__fadeIn z-20 transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
                        <button onclick="closeModal('message-modal')"
                            class="absolute top-2 right-2 p-1 rounded-full hover:bg-gray-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start justify-center">
                            </div>
                            <div class="text-center w-full mt-4">
                                <p class="can-exp-p text-center">{!! session('message') !!}</p>
                            </div>
                        </div>
                        <div class="px-4 pb-6 pt-4  sm:flex sm:flex-row-reverse sm:px-6 justify-center">
                            <a href=""
                                class="inline-flex w-full justify-center rounded bg-red-500 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-red-400 sm:ml-3 sm:w-24">{{ $siteText['close_btn_text'] ?? 'Close' }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if (session('success'))
        <div id="my-modal" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div
                    class="relative flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeModal()"></div>
                    <div
                        class="relative animate__animated animate__fadeIn z-20 transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
                        <button onclick="closeModal('success-modal')"
                            class="absolute top-2 right-2 p-1 rounded-full hover:bg-gray-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start justify-center">
                            </div>
                            <div class="w-full mt-4">
                                <p class="can-exp-p text-center">{!! session('success') !!}</p>
                            </div>
                        </div>
                        <div class="px-4 pb-6 pt-4  sm:flex sm:flex-row-reverse sm:px-6 justify-center">
                            <a href=""
                                class="inline-flex w-full justify-center rounded bg-greenXS px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-greenXS sm:ml-3 sm:w-24">{{ $siteText['close_btn_text'] ?? 'Close' }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if (session('failure'))
        <div class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                    <div
                        class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg w-full modal-border">
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start justify-center">
                            </div>
                            <div class="text-center mt-4">
                                <div class="w-full">
                                    <p class="text-center can-exp-p">{!! session('failure') !!}</p>
                                </div>
                            </div>
                        </div>
                        <div class="px-4 pb-6 pt-4 sm:flex sm:flex-row-reverse sm:px-6 justify-center">
                            <a href=""
                                class="whitespace-nowrap inline-flex w-full justify-center rounded bg-red-500 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-red-400 sm:ml-3 sm:w-24">{{ $siteText['close_btn_text'] ?? 'Close' }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="relative z-50" aria-labelledby="error-modal-title" role="dialog" aria-modal="true">
            <div onclick="closeErrorModal()" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                    <div
                        class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
                        <button type="button" onclick="closeErrorModal()"
                            class="absolute top-3 right-3 text-gray-400 hover:text-gray-500">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start justify-center">
                                <div class="mx-auto h-16 w-16 flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="2" stroke="currentColor" class="w-12 h-12 text-red-500">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="text-center sm:ml-4 sm:mt-0 sm:text-left">
                                <h3 class="text-3xl text-center font-FuturaMdCnBT font-medium text-gray-900 mb-4"
                                    id="error-modal-title">Error</h3>
                                <div class="mt-2 w-full">
                                    <p class="can-exp-p text-center text-gray-700">{!! session('error') !!}</p>
                                </div>
                            </div>
                        </div>
                        <div class="px-4 pb-6 pt-4 flex items-center justify-center sm:px-6">
                            <button type="button" onclick="closeErrorModal()" class="button-exp-fill">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
            function closeErrorModal() {
                const modal = document.querySelector('[aria-labelledby="error-modal-title"]');
                if (modal) modal.style.display = 'none';
            }
        </script>
    @endif

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

        @if ($ride->status === 'completed')
            <div class="mt-4 rounded-lg px-6 py-3 bg-blue-100 text-gray-600" role="alert">
                {{ $rideDetailPage->ride_completed_text ?? 'This ride was completed' }}
            </div>
        @endif

        <h1>{{ $rideDetailPage->main_heading ?? 'My ride detail' }}</h1>

        @if ($ride->relationLoaded('bookings') && $ride->bookings->isNotEmpty())
            <div class="flex overflow-x-auto gap-4 py-4">
                @foreach ($ride->bookings as $waitingBooking)
                    @php
                        $passengerName = trim(
                            ($waitingBooking->passenger->first_name ?? '') .
                                ' ' .
                                ($waitingBooking->passenger->last_name ?? ''),
                        );
                        $passengerName =
                            $passengerName !== ''
                                ? $passengerName
                                : $waitingBooking->passenger->name ??
                                    ($waitingBooking->passenger->email ?? 'Passenger');
                        $passengerInitial = strtoupper(substr((string) $passengerName, 0, 1));

                        // Get origin and destination from booking stops
                        $bookingOrigin = $waitingBooking->fromStop->label ?? 'N/A';
                        $bookingDestination = $waitingBooking->toStop->label ?? 'N/A';
                        $bookingOriginPickup = $waitingBooking->fromStop->pickup_dropoff_location ?? null;
                        $bookingDestinationDropoff = $waitingBooking->toStop->pickup_dropoff_location ?? null;

                        // Check if ride has multiple stops (more than just origin and destination)
                        $hasMultipleStops = $ride->stops && $ride->stops->count() > 2;
                    @endphp
                    <div class="rounded-xl border border-[#e9cc6a] bg-[#f2f2f2] overflow-hidden max-w-lg">
                        <h3 class="px-6 py-2 text-black text-2xl font-FuturaMdCnBT border-b border-gray-300">
                            Booking Requested
                        </h3>
                        <div class="px-6 py-4 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                            <div class="flex items-center gap-4">
                                <span
                                    class="h-20 w-20 rounded-full bg-[#f57c00] text-white text-5xl font-FuturaMdCnBT flex items-center justify-center">
                                    {{ $passengerInitial !== '' ? $passengerInitial : 'P' }}
                                </span>
                                <div>
                                    <p class="text-2xl text-black font-FuturaMdCnBT">{{ $passengerName }}</p>
                                    <p class="text-gray-600 text-base">
                                        {{ (int) $waitingBooking->seats }}
                                        {{ (int) $waitingBooking->seats === 1 ? 'seat requested' : 'seats requested' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        @if ($hasMultipleStops)
                            <div class="px-6 py-3 border-t border-gray-300">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm text-gray-600 font-semibold">From</p>
                                        <p class="text-xl md:text-lg text-primary font-FuturaMdCnBT">{{ $bookingOrigin }}
                                        </p>
                                        @if ($bookingOriginPickup)
                                            <p class="text-xs text-gray-500 mt-1">Pick up: {{ $bookingOriginPickup }}</p>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600 font-semibold">To</p>
                                        <p class="text-xl md:text-lg text-primary font-FuturaMdCnBT">
                                            {{ $bookingDestination }}</p>
                                        @if ($bookingDestinationDropoff)
                                            <p class="text-xs text-gray-500 mt-1">Drop off:
                                                {{ $bookingDestinationDropoff }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="flex justify-center border-t border-gray-300 gap-4 w-full px-4 py-2">
                            <form method="POST" class="js-booking-decline-form"
                                action="{{ route('px.my_ride_detail.booking.decline', ['lang' => optional($selectedLanguage)->abbreviation, 'id' => $ride->id, 'bookingId' => $waitingBooking->id]) }}">
                                @csrf
                                <button type="submit"
                                    class="inline-flex justify-center rounded bg-red-700 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:opacity-90 min-w-[220px]">
                                    Decline
                                </button>
                            </form>
                            <form method="POST" class="js-booking-approve-form"
                                action="{{ route('px.my_ride_detail.booking.approve', ['lang' => optional($selectedLanguage)->abbreviation, 'id' => $ride->id, 'bookingId' => $waitingBooking->id]) }}">
                                @csrf
                                <button type="submit"
                                    class="inline-flex justify-center rounded bg-green-700 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:opacity-90 min-w-[220px]">
                                    Approve Booking
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-y-4 md:gap-4">
            <div class="col-span-2">
                <x-px.ride-details :ride="$ride" :rideDetailPage="$rideDetailPage" :parentOrigin="$rideDetailsData['parentOrigin']" :parentDestination="$rideDetailsData['parentDestination']" :origin="$rideDetailsData['origin']"
                    :destination="$rideDetailsData['destination']" :pickupLocation="$rideDetailsData['pickupLocation']" :dropoffLocation="$rideDetailsData['dropoffLocation']" :originDepartureAt="$rideDetailsData['originDepartureAt']" :pricePerSeatMinor="$rideDetailsData['pricePerSeatMinor']"
                    :currency="$rideDetailsData['currency']" :segmentStops="$rideDetailsData['segmentStops']" :segmentMode="$rideDetailsData['segmentMode']" :bookingModeLabel="$bookingModeLabel ?? null" :bookingMethodLabel="$bookingMethodLabel ?? null"
                    :bookingModeCode="$bookingModeCode ?? null" :bookingMethodCode="$bookingMethodCode ?? null" :postRidePage="$postRidePage ?? null" :type="'my_ride_detail'" />

            </div>

            <div class="col-span-1">
                <div class="space-y-4">
                    <div class="bg-white rounded-lg overflow-hidden shadow-3xl">
                        <h3 class="bg-primary text-white py-2 px-4 text-2xl xl:text-3xl">
                            {{ $rideDetailPage->vehicle_info_label ?? 'Vehicle info' }}
                        </h3>
                        <div class="flex items-start space-x-2 p-4 w-full">
                            @if ($ride->vehicle)
                                <div
                                    class="w-20 h-20 rounded-full overflow-hidden bg-gray-200 flex items-center justify-center">
                                    @if ($ride->vehicle->image)
                                        <img class="w-full h-full object-cover rounded-full"
                                            src="{{ $ride->vehicle->image }}" alt="">
                                    @else
                                        <svg class="w-10 h-10 text-gray-400" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    @endif
                                </div>
                                <div class="text-center flex-1">
                                    <div class="flex items-center flex-wrap gap-x-2 text-sm text-black">
                                        @if ($ride->vehicle->year)
                                            <p class="text-md">{{ $ride->vehicle->year }}</p>
                                        @endif
                                        <span>|</span>
                                        <p class="text-md">{{ $ride->vehicle->make }}</p>
                                        <span>|</span>
                                        <p class="text-md">{{ $ride->vehicle->model }}</p>
                                        <span>|</span>
                                        @if ($ride->vehicle->color)
                                            <p class="text-md">{{ $ride->vehicle->color }}</p>
                                        @endif
                                    </div>
                                    <p class="font-semibold text-lg text-black text-start">
                                        {{ $ride->vehicle->license_no }}</p>
                                    @if ($ride->vehicle->vehicle_type)
                                        <p class="text-md">{{ $ride->vehicle->vehicle_type }}</p>
                                    @endif
                                </div>
                            @else
                                <p class="text-gray-500">No vehicle information available</p>
                            @endif
                        </div>
                    </div>

                    <div class="bg-white rounded-lg overflow-hidden shadow-3xl">
                        <h3 class="bg-primary text-white py-2 px-4 text-2xl xl:text-3xl">
                            {{ $rideDetailPage->cancellation_policy_label ?? 'Cancellation policy' }}
                        </h3>
                        <div class="flex items-center space-x-2 p-4 w-full">
                            <div class="flex items-center justify-between w-full">
                                <label class="font-normal text-gray-900 flex space-x-1">
                                    <span class="text-lg">
                                        {{ $cancelationPolicyLabel ?? 'Standard' }}
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>

                    @php
                        $isUpcoming = $ride->departure_at > now();
                        $isUpcomingStatus = in_array($ride->status, ['draft', 'published', 'started']);
                        $isNotBooked = $ride->seats_available == $ride->seats_total;
                        $showEditAndCancel = $isUpcoming && $isUpcomingStatus && $isNotBooked;
                    @endphp

                    @if ($isUpcoming && $ride->status !== 'cancelled')
                        <div class="flex justify-center pt-8 gap-4">
                            @if ($showEditAndCancel)
                                <a href="{{ route('px.post_ride.edit', ['lang' => optional($selectedLanguage)->abbreviation, 'id' => $ride->id]) }}"
                                    class="button-exp-fill w-36">
                                    {{ $rideDetailPage->edit_ride_btn_label ?? 'Edit ride' }}
                                </a>
                                <a id="cancelRideBtn" href="#"
                                    class="inline-flex justify-center rounded bg-red-500 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-red-400 w-36">
                                    {{ $rideDetailPage->cancel_ride_btn_label ?? 'Cancel ride' }}
                                </a>
                            @elseif ($isUpcomingStatus)
                                <a id="cancelRideBtn" href="#"
                                    class="inline-flex justify-center rounded bg-red-500 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-red-400 w-36">
                                    {{ $rideDetailPage->cancel_ride_btn_label ?? 'Cancel ride' }}
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Cancel ride: confirmation modal --}}
    <div id="cancelRideConfirmModal" class="hidden fixed inset-0 z-50 w-screen overflow-y-auto" aria-modal="true"
        role="dialog">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
            onclick="closeBookingModal('cancelRideConfirmModal')"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div
                class="relative flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                <div
                    class="relative animate__animated animate__fadeIn z-20 transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border1">
                    <button type="button" onclick="closeBookingModal('cancelRideConfirmModal')"
                        class="absolute top-2 right-2 p-1 rounded-full hover:bg-gray-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="text-center w-full mt-4">
                            <p class="can-exp-p text-center">
                                {{ $rideDetailPage->cancel_ride_confirmation ?? 'Are you sure you want to cancel this ride?' }}
                            </p>
                            <p class="can-exp-p text-center mt-2">This action is irreversible!</p>
                        </div>
                    </div>
                    <div class="px-4 pb-6 pt-4 sm:flex sm:flex-row-reverse sm:px-6 justify-center gap-2">
                        <button type="button" id="cancelRideConfirmYes"
                            class="inline-flex justify-center rounded bg-red-500 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:bg-red-400 shadow-sm w-36">{{ $rideDetailPage->cancel_ride_yes_btn ?? 'Yes, cancel it!' }}</button>
                        <button type="button" onclick="closeBookingModal('cancelRideConfirmModal')"
                            class="inline-flex justify-center rounded bg-[#106BC7] px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:opacity-90 w-36">{{ $rideDetailPage->cancel_ride_no_btn ?? 'No, take me back' }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Cancel ride: result modal --}}
    <div id="cancelRideResultModal" class="hidden fixed inset-0 z-50 w-screen overflow-y-auto" aria-modal="true"
        role="dialog">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeCancelRideResultModal()">
        </div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div
                class="relative flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                <div
                    class="relative animate__animated animate__fadeIn z-20 transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
                    <button type="button" onclick="closeCancelRideResultModal()"
                        class="absolute top-2 right-2 p-1 rounded-full hover:bg-gray-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="text-center w-full mt-4">
                            <p class="can-exp-p text-center" id="cancelRideResultMessage"></p>
                        </div>
                    </div>
                    <div class="px-4 pb-6 pt-4 sm:flex sm:flex-row-reverse sm:px-6 justify-center">
                        <button type="button" id="cancelRideResultClose"
                            class="inline-flex w-full justify-center rounded bg-red-500 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-red-400 sm:ml-3 sm:w-24">{{ $siteText['close_btn_text'] ?? 'Close' }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Approve booking request: confirmation modal --}}
    <div id="approveBookingConfirmModal" class="hidden fixed inset-0 z-50 w-screen overflow-y-auto" aria-modal="true"
        role="dialog">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
            onclick="closeBookingModal('approveBookingConfirmModal')"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div
                class="relative flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                <div
                    class="relative animate__animated animate__fadeIn z-20 transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border-4 border-[#0ea5a6]">
                    <button type="button" onclick="closeBookingModal('approveBookingConfirmModal')"
                        class="absolute top-3 right-3 p-1 rounded-full hover:bg-gray-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    <div class="px-6 pt-10 pb-6">
                        <p class="text-left text-gray-800 text-xl font-FuturaMdCnBT">Are you sure you want to approve this
                            booking request?</p>
                    </div>
                    <div class="px-6 pb-8 flex flex-col sm:flex-row items-center sm:justify-center gap-3">
                        <button type="button" id="approveBookingConfirmYes"
                            class="inline-flex justify-center rounded bg-[#0ea5a6] px-7 py-3 font-FuturaMdCnBT text-xl text-white hover:opacity-90">
                            Yes, approve it!
                        </button>
                        <button type="button" onclick="closeBookingModal('approveBookingConfirmModal')"
                            class="inline-flex justify-center rounded border border-gray-300 bg-gray-100 px-7 py-3 font-FuturaMdCnBT text-xl text-gray-600 hover:bg-gray-200">
                            No, take me back!
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Decline booking request: confirmation modal --}}
    <div id="declineBookingConfirmModal" class="hidden fixed inset-0 z-50 w-screen overflow-y-auto" aria-modal="true"
        role="dialog">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
            onclick="closeBookingModal('declineBookingConfirmModal')"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div
                class="relative flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                <div
                    class="relative animate__animated animate__fadeIn z-20 transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border-4 border-[#ef4444]">
                    <button type="button" onclick="closeBookingModal('declineBookingConfirmModal')"
                        class="absolute top-3 right-3 p-1 rounded-full hover:bg-gray-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    <div class="px-6 pt-10 pb-6">
                        <p class="text-left text-gray-800 text-xl font-FuturaMdCnBT">Are you sure you want to decline this
                            booking request? This action cannot be undone.</p>
                    </div>
                    <div class="px-6 pb-8 flex flex-col sm:flex-row items-center sm:justify-center gap-3">
                        <button type="button" id="declineBookingConfirmYes"
                            class="inline-flex justify-center rounded bg-[#ef4444] px-7 py-3 font-FuturaMdCnBT text-xl text-white hover:opacity-90">
                            Yes, decline
                        </button>
                        <button type="button" onclick="closeBookingModal('declineBookingConfirmModal')"
                            class="inline-flex justify-center rounded border border-gray-300 bg-gray-100 px-7 py-3 font-FuturaMdCnBT text-xl text-gray-600 hover:bg-gray-200">
                            No, take me back!
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Cannot cancel ride alert modal --}}
    <div id="cannotCancelRideModal" class="hidden fixed inset-0 z-50 w-screen overflow-y-auto" aria-modal="true"
        role="dialog">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
            onclick="closeBookingModal('cannotCancelRideModal')"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div
                class="relative flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                <div
                    class="relative animate__animated animate__fadeIn z-20 transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border-4 border-[#ef4444]">
                    <button type="button" onclick="closeBookingModal('cannotCancelRideModal')"
                        class="absolute top-3 right-3 p-1 rounded-full hover:bg-gray-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    <div class="px-6 pt-10 pb-6">
                        <div class="flex justify-center mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-[#ef4444]" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <p class="text-center text-gray-800 text-xl font-FuturaMdCnBT">Cannot cancel ride with booked
                            seats. Please contact support.</p>
                    </div>
                    <div class="px-6 pb-8 flex justify-center">
                        <button type="button" onclick="closeBookingModal('cannotCancelRideModal')"
                            class="inline-flex justify-center rounded bg-[#106BC7] px-7 py-3 font-FuturaMdCnBT text-xl text-white hover:opacity-90">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script>
        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.style.display = 'none';
            }
        }

        function closeBookingModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('hidden');
                modal.style.display = 'none';
            }
        }

        function closeErrorModal() {
            const modal = document.querySelector('[aria-labelledby="error-modal-title"]');
            if (modal) modal.style.display = 'none';
        }

        const cancelRideMyRidesUrl = "{{ route('px.my_rides', ['lang' => optional($selectedLanguage)->abbreviation]) }}";

        function closeCancelRideResultModal(redirect) {
            closeBookingModal('cancelRideResultModal');
            if (redirect) {
                window.location.href = cancelRideMyRidesUrl;
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            let pendingApproveForm = null;
            let pendingDeclineForm = null;

            document.querySelectorAll('.js-booking-approve-form').forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    event.preventDefault();
                    pendingApproveForm = form;
                    const modal = document.getElementById('approveBookingConfirmModal');
                    if (modal) {
                        modal.classList.remove('hidden');
                        modal.style.display = 'block';
                    }
                });
            });

            document.querySelectorAll('.js-booking-decline-form').forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    event.preventDefault();
                    pendingDeclineForm = form;
                    const modal = document.getElementById('declineBookingConfirmModal');
                    if (modal) {
                        modal.classList.remove('hidden');
                        modal.style.display = 'block';
                    }
                });
            });

            const approveBookingConfirmYes = document.getElementById('approveBookingConfirmYes');
            if (approveBookingConfirmYes) {
                approveBookingConfirmYes.addEventListener('click', function() {
                    closeBookingModal('approveBookingConfirmModal');
                    if (pendingApproveForm) {
                        pendingApproveForm.submit();
                    }
                });
            }

            const declineBookingConfirmYes = document.getElementById('declineBookingConfirmYes');
            if (declineBookingConfirmYes) {
                declineBookingConfirmYes.addEventListener('click', function() {
                    closeBookingModal('declineBookingConfirmModal');
                    if (pendingDeclineForm) {
                        pendingDeclineForm.submit();
                    }
                });
            }

            const cancelRideBtn = document.getElementById('cancelRideBtn');
            if (!cancelRideBtn) return;

            cancelRideBtn.addEventListener('click', function(event) {
                event.preventDefault();
                const bookedSeats = {{ $ride->seats_total - $ride->seats_available }};

                if (bookedSeats === 0) {
                    const confirmModal = document.getElementById('cancelRideConfirmModal');
                    if (confirmModal) {
                        confirmModal.classList.remove('hidden');
                        confirmModal.style.display = 'block';
                    }
                } else {
                    // If there are booked seats, show alert modal
                    const alertModal = document.getElementById('cannotCancelRideModal');
                    if (alertModal) {
                        alertModal.classList.remove('hidden');
                        alertModal.style.display = 'block';
                    }
                }
            });

            const cancelRideConfirmYes = document.getElementById('cancelRideConfirmYes');
            if (cancelRideConfirmYes) {
                cancelRideConfirmYes.addEventListener('click', function() {
                    closeBookingModal('cancelRideConfirmModal');

                    fetch("{{ route('px.my_rides', ['lang' => optional($selectedLanguage)->abbreviation]) }}", {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                                'X-HTTP-Method-Override': 'PUT'
                            },
                            body: JSON.stringify({
                                ride_id: {{ $ride->id }},
                                action: 'cancel'
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            const resultModal = document.getElementById('cancelRideResultModal');
                            const resultMessage = document.getElementById('cancelRideResultMessage');
                            const resultCloseBtn = document.getElementById('cancelRideResultClose');
                            if (!resultModal || !resultMessage) return;

                            if (data.success) {
                                resultMessage.textContent = 'This ride has been cancelled';
                                resultCloseBtn.onclick = function() {
                                    closeCancelRideResultModal(true);
                                };
                            } else if (data.error && data.message) {
                                resultMessage.textContent = data.message;
                                resultCloseBtn.onclick = function() {
                                    closeCancelRideResultModal(false);
                                };
                            } else {
                                resultMessage.textContent = 'Failed to cancel the ride.';
                                resultCloseBtn.onclick = function() {
                                    closeCancelRideResultModal(false);
                                };
                            }
                            resultModal.classList.remove('hidden');
                            resultModal.style.display = 'block';
                        })
                        .catch(function() {
                            const resultModal = document.getElementById('cancelRideResultModal');
                            const resultMessage = document.getElementById('cancelRideResultMessage');
                            const resultCloseBtn = document.getElementById('cancelRideResultClose');
                            if (resultModal && resultMessage) {
                                resultMessage.textContent =
                                    'An error occurred while cancelling the ride.';
                                resultCloseBtn.onclick = function() {
                                    closeCancelRideResultModal(false);
                                };
                                resultModal.classList.remove('hidden');
                                resultModal.style.display = 'block';
                            }
                        });
                });
            }
        });
    </script>
@endsection
