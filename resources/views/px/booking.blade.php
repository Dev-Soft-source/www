@extends('layouts.template')

@section('style')
<style>
    /* Tooltip styles for student verification pending notice */
    @media (max-width: 768px) {
        .student-verification-tooltip::after {
            left: 30px;
        }
    }

    .student-verification-tooltip {
        position: relative;
        background-color: #c75b5b;
        border-radius: 0.5rem;
        padding: 0.75rem;
        width: 28rem;
        max-width: 90vw;
    }

    .tooltip.shift-left {
        margin-left: -220px;
    }

    @media (max-width: 768px) {
        .tooltip.shift-left {
            margin-left: 0;
            left: 0;
            right: 0;
        }
    }

    /* Wider tooltip on mobile for coffee wall tooltips */
    @media (max-width: 639px) {
        .tooltip_width {
            width: min(90vw, 22rem) !important;
            min-width: 18rem;
        }
    }

    /* Payment method tooltip: above exclamation icon with arrow pointing down to icon */
    .payment-method-tooltip {
        position: relative;
        background-color: #c75b5b;
        border-radius: 0.5rem;
        padding: 0.75rem;
        min-width: 300px;
        max-width: 380px;
    }
    .payment-method-tooltip::after {
        content: '';
        position: absolute;
        top: 100%;
        left: 50%;
        transform: translateX(-50%);
        border: 8px solid transparent;
        border-top-color: #c75b5b;
    }
</style>
@endsection

@section('content')
    @php
        $isEditMode = (bool) ($isEditMode ?? false);
        $existingBooking = $existingBooking ?? null;
        $fromLabel = $fromStop->label ?? 'N/A';
        $toLabel = $toStop->label ?? 'N/A';
        $perSeatMinor = (int) ($segmentPriceMinor ?? 0);
        $currencyCode = strtoupper((string) ($ride->currency ?? ($selectedCurrency ?? 'USD')));
        $currencyMap = ['USD' => '$', 'CAD' => 'C$'];
        $currencySymbol = $currencyMap[$currencyCode] ?? $currencyCode . ' ';
        $currentBookedSeats = $isEditMode ? (int) ($existingBooking->seats ?? 1) : 0;
        $segmentAvailableSeats = (int) ($segmentAvailableSeats ?? $ride->seats_available);
        $maxSeatsAllowed = $isEditMode
            ? max(1, $segmentAvailableSeats + $currentBookedSeats)
            : max(1, $segmentAvailableSeats);
        $bookingModeCodeValue = strtolower((string) ($bookingModeCode ?? ''));
        $bookingMethodCode = strtolower((string) ($bookingMethodCode ?? ''));
        $isCashBookingMethod = $bookingMethodCode === 'cash';
        if ($isEditMode) {
            $payButtonLabel = 'Update Booking';
        } elseif ($isCashBookingMethod) {
            $payButtonLabel = $bookingModeCodeValue === 'manual' ? 'Request to Book' : 'Book Seats';
        } else {
            $payButtonLabel = $bookingModeCodeValue === 'manual' ? 'Pay and Request to Book' : 'Pay and Book Seats';
        }

        // Prepare data for ride-details component
        $parentOrigin = $ride->route->origin_label ?? 'N/A';
        $parentDestination = $ride->route->destination_label ?? 'N/A';
        $origin = $fromLabel;
        $destination = $toLabel;

        // Get pickup/dropoff locations from stops
        $orderedStops = $ride->stops->sortBy('stop_order');
        $firstStop = $orderedStops->first();
        $lastStop = $orderedStops->last();

        // Find the stop that matches the displayed origin
        $originStop = $orderedStops->first(function ($stop) use ($origin) {
            return trim($stop->label ?? '') === trim($origin);
        });

        // Find the stop that matches the displayed destination
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

        // Get departure date/time from origin stop
        $originDepartureAt = null;
        if ($originStop && $originStop->eta_at) {
            $originDepartureAt = $originStop->eta_at;
        } elseif ($firstStop && $firstStop->eta_at) {
            $originDepartureAt = $firstStop->eta_at;
        } else {
            $originDepartureAt = $ride->departure_at;
        }

        $pricePerSeatMinor = $perSeatMinor;
        $currency = $currencySymbol;
        $segmentMode = $segmentStops->isNotEmpty();

        // Fetch rideDetailPage if not available
        $rideDetailPage = null;
        if (isset($selectedLanguage) && $selectedLanguage) {
            $selectedLangId = $selectedLanguage->id ?? null;
            $defaultLangId = isset($defaultLang) && $defaultLang ? $defaultLang->id : null;
            if ($selectedLangId || $defaultLangId) {
                $rideDetailPage = \App\Models\RideDetailPageSettingDetail::getByLanguageWithFallback(
                    $selectedLangId,
                    $defaultLangId,
                );
            }
        }
    @endphp
    <div class="container mx-auto my-10 xl:my-14 px-4 xl:px-0">
        @if (session('success'))
            <div class="mb-4 rounded-md border border-green-200 bg-green-50 text-green-700 px-4 py-3">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 rounded-md border border-red-200 bg-red-50 text-red-700 px-4 py-3">
                {{ session('error') }}
            </div>
        @endif
        <h1>{{ $bookingPage->main_heading ?? 'Ride detail' }}</h1>
        <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-y-4 md:gap-4">
            <div class="col-span-2">
                <x-px.ride-details :ride="$ride" :rideDetailPage="$rideDetailPage" :parentOrigin="$parentOrigin" :parentDestination="$parentDestination" :origin="$origin"
                    :destination="$destination" :pickupLocation="$pickupLocation" :dropoffLocation="$dropoffLocation" :originDepartureAt="$originDepartureAt" :pricePerSeatMinor="$pricePerSeatMinor"
                    :currency="$currency" :segmentStops="$segmentStops" :segmentMode="$segmentMode" :bookingModeLabel="$bookingModeCode ?? null" :bookingMethodLabel="$bookingMethodLabel ?? null"
                    :postRidePage="null" />
            </div>

            <div class="col-span-1">

                <form method="POST"
                    action="{{ $isEditMode ? route('px.booking.update', ['lang' => optional($selectedLanguage)->abbreviation, 'id' => $existingBooking->id]) : route('px.booking.pay', ['lang' => optional($selectedLanguage)->abbreviation]) }}"
                    class="">
                    @csrf
                    @if ($isEditMode)
                        @method('PUT')
                    @endif

                    @if (!$isEditMode)
                        <input type="hidden" name="from_stop_id" value="{{ $fromStop->id }}">
                        <input type="hidden" name="to_stop_id" value="{{ $toStop->id }}">
                    @endif
                    <div class="bg-white rounded-lg shadow-3xl">
                        <div class="bg-primary text-white px-4 py-2 rounded-t-lg">
                            <h3 class="text-2xl xl:text-3xl">
                                @isset($bookingPage->booking_label)
                                    {{ $bookingPage->booking_label }}
                                @endisset
                            </h3>
                        </div>

                        <div class="bg-white p-4 rounded-b-lg">
                            <div class="space-y-4">
                                <div class="flex items-center justify-between gap-2">
                                    <div class="flex relative">
                                        <h3 class="text-primary text-2xl xl:text-3xl">
                                            @isset($bookingPage->seats_available_label)
                                                {{ $bookingPage->seats_available_label }}
                                            @endisset
                                        </h3>
                                    </div>
                                </div>

                                @php
                                    // Check if PX rides have seatDetail, otherwise use number input
                                    $hasSeatDetail = isset($ride->seatDetail) && $ride->seatDetail->isNotEmpty();
                                    $postRidePage = null; // PX rides might not have postRidePage
                                @endphp

                                @if (auth()->user() && (auth()->user()->student == '1' || auth()->user()->student == '2') && $isCashBookingMethod)
                                    <div class="mb-3 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                        <p class="text-yellow-800 text-sm">
                                            <strong>Note for Students:</strong> You are limited to booking a maximum of 2 seats per ride for Cash payment rides.
                                        </p>
                                    </div>
                                @endif

                                @if ($hasSeatDetail)
                                    <div class="flex items-center flex-wrap gap-2 mt-2" id="seat-selection-container">
                                        @foreach ($ride->seatDetail as $detail)
                                            @php
                                                $isBooked = $detail->status === 'booked';
                                                $isHeldByOthers = $detail->status === 'hold' && $detail->user_id != optional(auth()->user())->id;
                                                $isUnavailable = $isBooked || $isHeldByOthers;
                                                $isSelectedByMe = !$isUnavailable && ($detail->user_id == optional(auth()->user())->id || in_array($detail->id, old('seats_id', [])));
                                            @endphp
                                            <div class="relative seat-item" data-seat-id="{{ $detail->id }}" data-seat-number="{{ $detail->seat_number ?? $loop->iteration }}" data-is-booked="{{ $isUnavailable ? '1' : '0' }}">
                                                @if ($isUnavailable)
                                                    <div class="opacity-50 cursor-not-allowed pointer-events-none">
                                                        <span class="relative inline-block w-8 md:w-12">
                                                            <img src="{{ asset('assets/seat.png') }}" class="w-8 md:w-12 object-cover seat-image seat-unselect-{{ $detail->id }}" alt="">
                                                            <span class="absolute mt-2 inset-0 flex items-center justify-center text-sm seat-number seat-number-{{ $detail->id }}">{{ $detail->seat_number ?? $loop->iteration }}</span>
                                                        </span>
                                                    </div>
                                                @else
                                                    <label class="cursor-pointer inline-block seat-clickable" for="number-of-seat-{{ $detail->id }}" data-seat-id="{{ $detail->id }}" data-seat-number="{{ $detail->seat_number ?? $loop->iteration }}" onclick="seat_selected(event, {{ $detail->id }}, {{ $detail->seat_number ?? $loop->iteration }})">
                                                        <input id="number-of-seat-{{ $detail->id }}" name="seats_id[]" type="checkbox" value="{{ $detail->id }}" class="hidden seat-checkbox" {{ $isSelectedByMe ? 'checked' : '' }} data-parsley-required="true" data-parsley-trigger="blur focusout change" data-parsley-required-message="Please select the available seats." data-parsley-errors-container="#parsley-seats-error" data-seat-id="{{ $detail->id }}" data-seat-number="{{ $detail->seat_number ?? $loop->iteration }}">
                                                        <span class="relative inline-block w-8 md:w-12">
                                                            <img src="{{ $isSelectedByMe ? asset('assets/seat-hover-1.png') : asset('assets/seat.png') }}" class="w-8 md:w-12 object-cover cursor-pointer seat-image seat-unselect-{{ $detail->id }}" alt="">
                                                            <span class="absolute mt-2 inset-0 flex items-center justify-center text-sm seat-number seat-number-{{ $detail->id }} {{ $isSelectedByMe ? 'text-green-300' : '' }}">{{ $detail->seat_number ?? $loop->iteration }}</span>
                                                        </span>
                                                    </label>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                    @error('seats')
                                        <div class="tooltip-error shadow-lg mt-2">{{ $message }}</div>
                                    @enderror
                                    <!-- Hidden input to store count -->
                                    <input type="hidden" id="seat-count" name="seats" value="">
                                @else
                                    {{-- Fallback to number input if seatDetail is not available --}}
                                    <div>
                                        <label class="block text-sm font-semibold mb-1 required">Seats</label>
                                        <input id="px-booking-seats" name="seats" type="number" min="1"
                                            max="{{ $maxSeatsAllowed }}"
                                            value="{{ old('seats', $isEditMode ? $currentBookedSeats : 1) }}"
                                            class="w-full rounded border-gray-300" required>
                                        <p class="text-xs text-gray-500 mt-1">
                                            Available:
                                            {{ $isEditMode ? $segmentAvailableSeats + $currentBookedSeats : $segmentAvailableSeats }}
                                        </p>
                                        @error('seats')
                                            <div class="tooltip-error shadow-lg">{{ $message }}</div>
                                        @enderror
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Booking Summary Section --}}
                    <div class="mt-4 bg-white rounded-lg shadow-3xl">
                        <div class="bg-primary text-white px-4 py-2 rounded-t-lg">
                            <h3 class="text-2xl xl:text-3xl">
                                @isset($bookingPage->pricing_label)
                                    {{ $bookingPage->pricing_label }}
                                @endisset
                            </h3>
                        </div>
                        <div class="bg-white p-4 rounded-b-lg">
                            <div class="flex items-center justify-between gap-2">
                                <div class="flex items-center gap-2">
                                    <p class="text-black">
                                        <span id="px-seats-booked">1</span>
                                        @isset($bookingPage->seat_label)
                                            {{ $bookingPage->seat_label }}
                                        @endisset
                                    </p>
                                </div>
                                <p class="totalSeatsAmount text-black"></p>
                                <input type="hidden" name="seats_amount" class="totalSeatsAmountInput form-control" readonly>
                            </div>

                            @php
                                $postRidePage = null;
                                $firm = null;
                                $settingFirmDiscount = '';
                                if (isset($setting)) {
                                    $settingFirmDiscount = $setting->frim_discount ?? '';
                                }
                                // Check if PX rides have cancellation policy - this might not apply to PX
                                // Leaving it here for potential future use
                            @endphp

                            <div class="flex items-center justify-between gap-2 mt-1">
                                <div class="flex items-center gap-2">
                                    <p class="text-black">
                                        @isset($bookingPage->booking_fee_label)
                                            {{ $bookingPage->booking_fee_label }}
                                        @endisset
                                    </p>
                                    @if (auth()->user() && auth()->user()->student == 2)
                                        <div class="relative sups inline-flex items-center group">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle-fill text-black peer" viewBox="0 0 16 16">
                                                <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                                            </svg>
                                            <!-- Tooltip -->
                                            <div class="absolute tooltip hidden left-full bottom-full mb-2 z-50 shift-left group-hover:flex peer-hover:flex">
                                                <div class="student-verification-tooltip">
                                                    <p class="text-white text-sm">Your student verification is pending. Pay the Booking Fee now to secure your seat, and we will refund it automatically once your status is approved (usually within 72 hours).</p>
                                                </div>
                                            </div>
                                        </div>
                                    @elseif (auth()->user() && auth()->user()->charge_booking == '2')
                                        <div class="relative sups inline-flex items-center group">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle-fill text-black peer" viewBox="0 0 16 16">
                                                <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                                            </svg>
                                            <!-- Tooltip -->
                                            <div class="absolute tooltip hidden left-full bottom-full mb-2 z-50 shift-left group-hover:flex peer-hover:flex">
                                                <div class="student-verification-tooltip bg-green-500">
                                                    <p class="text-white text-md">As a verified student, your booking fee is waived. You only pay the booking price.</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <p class="totalAmount text-black"></p>
                                <input type="hidden" name="booking_credit" class="totalAmountInput form-control" readonly>
                            </div>
                            @if (isset($setting->deduct_tax) && $setting->deduct_tax == "deduct_from_passenger")
                                @php
                                    if($setting->tax_type == "state_wise_tax"){
                                        $settingTaxPercentage = $stateTax ?? 0;
                                    }else{
                                        $settingTaxPercentage = $setting->tax ?? 0;
                                    }
                                @endphp

                                <input type="hidden" value="{{$settingTaxPercentage}}" name="tax_percentage">
                                <input type="hidden" value="{{$setting->deduct_tax}}" name="deduct_tax">
                                <input type="hidden" value="{{$setting->tax_type}}" name="tax_type">

                                <div class="flex items-center justify-between gap-2 mt-1">
                                    <p class="text-black">
                                        @isset($bookingPage->tax_label)
                                            {{ $bookingPage->tax_label ?? "Tax" }}
                                        @endisset
                                    </p>
                                    <p class="taxAmount text-black">0</p>
                                    <input type="hidden" name="tax_amount" class="totalTaxAmountInput form-control" readonly>
                                </div>
                            @endif

                            @if (isset($coffeeBalance) && $coffeeBalance > 0)
                                <div class="flex items-center justify-between gap-2 mt-1">
                                    <div class="flex">
                                        <input type="checkbox" id="px-coffee-wall" name="coffee_wall" value="1" class="form-control hidden peer">
                                        <label for="px-coffee-wall" class="inline-flex items-center justify-center w-full px-2 py-0.5 text-primary bg-white border-2 border-primary rounded cursor-pointer peer-checked:bg-primary peer-checked:text-white">
                                            <span class="font-medium font-FuturaMdCnBT text-xl line-clamp-2 max-w-36 w-full">
                                                {{ $bookingPage->coffee_from_wall_label ?? 'Pay booking fee with Coffee from the Wall' }}
                                            </span>
                                        </label>
                                        <div class="sups relative inline-flex ml-2 group">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle-fill text-black cursor-help peer" viewBox="0 0 16 16">
                                                <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                                            </svg>
                                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 z-50 hidden group-hover:flex peer-hover:flex flex-col items-center">
                                                <div role="tooltip" class="payment-method-tooltip tooltip_width sm:w-[25rem] md:w-[30rem] lg:w-72 xl:w-[23rem] 2xl:w-[25rem]">
                                                    @php
                                                        // For PX rides, we might not have postRidePage, so use a default message
                                                        $coffeePaymentOption1Id = null;
                                                        $coffeePaymentOption3Id = null;
                                                        $coffeeRidePaymentId = null;
                                                        if (isset($ride->payment_method)) {
                                                            $coffeeRidePaymentId = is_object($ride->payment_method) ? ($ride->payment_method->features_setting_id ?? null) : ($ride->payment_method ?? null);
                                                        }
                                                    @endphp
                                                    @if ($coffeeRidePaymentId !== null && $coffeePaymentOption1Id !== null && $coffeeRidePaymentId == $coffeePaymentOption1Id)
                                                        <p class="text-white font-semibold text-start text-sm lg:text-base">To be paid in cash directly to the driver at the time of the ride.</p>
                                                    @elseif ($coffeePaymentOption3Id !== null && $coffeeRidePaymentId == $coffeePaymentOption3Id)
                                                        <p class="text-white font-semibold text-start text-sm lg:text-base">This amount is pre-authorized to ProximaRide now and will be refunded to you once you meet the driver and pay them in cash.</p>
                                                    @else
                                                        <p class="text-white font-semibold text-start text-sm lg:text-base">ProximaRide will transfer this amount to the driver only after the ride is completed.</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="hideBookingFee" class="hidden items-center space-x-1">
                                        <p class="text-black">-</p>
                                        <p class="totalAmount text-black"></p>
                                        <div class="sups relative inline-flex ml-2 group">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle-fill text-black cursor-help peer" viewBox="0 0 16 16">
                                                <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                                            </svg>
                                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 z-50 hidden group-hover:flex peer-hover:flex flex-col items-center">
                                                <div role="tooltip" class="payment-method-tooltip tooltip_width sm:w-[25rem] md:w-[30rem] lg:w-72 xl:w-[23rem] 2xl:w-[25rem]">
                                                    @if ($coffeeRidePaymentId !== null && $coffeePaymentOption1Id !== null && $coffeeRidePaymentId == $coffeePaymentOption1Id)
                                                        <p class="text-white font-semibold text-start text-sm lg:text-base">To be paid in cash directly to the driver at the time of the ride.</p>
                                                    @elseif ($coffeePaymentOption3Id !== null && $coffeeRidePaymentId == $coffeePaymentOption3Id)
                                                        <p class="text-white font-semibold text-start text-sm lg:text-base">This amount is pre-authorized to ProximaRide now and will be refunded to you once you meet the driver and pay them in cash.</p>
                                                    @else
                                                        <p class="text-white font-semibold text-start text-sm lg:text-base">ProximaRide will transfer this amount to the driver only after the ride is completed.</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @php
                                // Determine payment method for PX rides
                                $pxPaymentMethod = $isCashBookingMethod ? "cash" : "online";
                            @endphp
                            <input type="hidden" value="{{ $pxPaymentMethod }}" id="check_payment_method">

                            @if ($isCashBookingMethod)
                                <div class="flex items-center justify-between gap-2 mt-1">
                                    <input type="hidden" name="online_payment" class="totalAmountIn form-control" readonly>
                                </div>
                                <div class="flex items-center justify-between gap-2 mt-1">
                                    <input type="hidden" name="cash_payment" class="totalSeatsAmountInput form-control" readonly>
                                </div>
                            @else
                                <div class="flex items-center justify-between gap-2 mt-1">
                                    <input type="hidden" name="online_payment" class="totalSumIn form-control" readonly>
                                </div>
                                <div class="flex items-center justify-between gap-2 mt-1">
                                    <input type="hidden" name="cash_payment" value="0" class="form-control" readonly>
                                </div>
                            @endif
                            <input type="hidden" name="booked_by_wallet" class="bookedByWallet form-control" readonly>
                            <div class="flex items-center justify-between gap-2 mt-1">
                                <p>
                                    @isset($bookingPage->total_label)
                                        {{ $bookingPage->total_label }}
                                    @endisset
                                </p>
                                <div>
                                    <p class="totalSum text-right"></p>
                                    <span id="discount" class="text-right"></span>
                                </div>
                                <input type="hidden" name="total" class="totalSumInput form-control" readonly>
                                <input type="hidden" id="stripeChargeAmount" value="">
                            </div>
                        </div>
                    </div>


                    {{-- Message to Driver Section --}}
                    <div class="mt-4 bg-white rounded-lg shadow-3xl">
                        <div class="bg-primary text-white px-4 py-2 rounded-t-lg">
                            <h3 class="text-2xl xl:text-3xl">
                                {{ $bookingPage->message_to_driver_label ?? 'Message to driver' }}
                            </h3>
                        </div>
                        <div class="bg-white p-4 rounded-b-lg">
                            <textarea id="px-driver-message" rows="5" name="driver_message"
                                class="block p-2.5 w-full text-gray-900 bg-white rounded border border-gray-300 focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500"
                                placeholder="{{ $bookingPage->message_driver_placeholder ?? 'Tell the driver why you\'re traveling, introduce yourself, or just say hi. Drivers are more likely to accept passengers who introduce themselves.' }}">{{ old('driver_message', $isEditMode ? $existingBooking->meta['driver_message'] ?? '' : '') }}</textarea>
                            @error('driver_message')
                                <div class="tooltip-error shadow-lg">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- User Declarations Section --}}
                    <div class="mt-4 bg-white rounded-lg shadow-3xl">
                        <div class="bg-primary text-white px-4 py-2 rounded-t-lg">
                            <h3 class="text-2xl xl:text-3xl">
                                {{ $bookingPage->booking_term_label ?? 'User Declarations' }}
                            </h3>
                        </div>
                
                        <div class="bg-white p-4 rounded-b-lg space-y-4">
                            <ul class="space-y-2">
                                <li>
                                    <p class="text-left text-gray-700">
                                        ● @isset($bookingPage->booking_disclaimer_on_time)
                                            {!! $bookingPage->booking_disclaimer_on_time !!}
                                        @else
                                            I understand that I must be on time for my ride. If I am late, the driver may leave
                                            without me and I will not be refunded.
                                        @endisset
                                    </p>
                                </li>
                                <li>
                                    <p class="text-left text-gray-700 mt-4">
                                        <strong>● Pink Rides: </strong>
                                        {{ $bookingPage->booking_disclaimer_pink_ride ?? 'I know that ProximaRide are exclusive to ProximaRide female members. If I am booking on a Pink Ride, I will not be accompanied by male members who are above 12 years of age, nor will I send a male member in my place. If I do, the driver will not take me or them, and I will not be refunded' }}
                                    </p>
                                </li>
                                <li>
                                    <p class="text-left text-gray-700 mt-4">
                                        <strong>● Extra+ Rides: </strong>
                                        {{ $bookingPage->booking_disclaimer_extra_care_ride ?? 'I know that Extra+ Rides are exclusive to members with highest review score. If I am booking on an Extra+ Ride, I will adhere to its standards' }}
                                    </p>
                                </li>
                            </ul>

                            {{-- Main terms checkbox --}}
                            <div class="flex items-start my-4">
                                <input type="checkbox" name="agree_terms" value="1" id="agree_terms"
                                    {{ old('agree_terms') == '1' || ($isEditMode && $existingBooking) ? 'checked' : '' }}
                                    class="w-4 h-4 text-blue-600 cursor-pointer bg-white mt-2 border-gray-600 rounded focus:ring-blue-500 focus:ring-2">
                                <label for="agree_terms" class="ml-2 font-normal text-gray-900 required">
                                    @isset($bookingPage->booking_term_agree_text)
                                        {!! $bookingPage->booking_term_agree_text !!}
                                    @else
                                        I have read and agree to the terms and conditions above.
                                    @endisset
                                </label>
                            </div>
                            @error('agree_terms')
                                <div class="tooltip-error shadow-lg">{{ $message }}</div>
                            @enderror

                            @php
                                $settingFirmDiscount = $setting->frim_discount ?? null;
                            @endphp

                            {{-- Firm cancellation declarations (if this PX ride uses a firm policy) --}}
                            @if (!empty($ride->cancelation_policy))
                                <div class="flex items-start my-4">
                                    <label class="flex items-start cursor-pointer font-normal text-gray-900">
                                        <input type="checkbox" name="firm_agree_terms" value="1"
                                            {{ old('firm_agree_terms') == '1' ? 'checked' : '' }}
                                            class="w-4 h-4 text-blue-600 cursor-pointer bg-white mt-2 border-gray-600 rounded focus:ring-blue-500 focus:ring-2">
                                        <span class="ml-2">
                                            @isset($bookingPage->booking_disclaimer_firm)
                                                {!! $bookingPage->booking_disclaimer_firm !!}
                                            @endisset
                                            <span class="text-red-500">*</span>
                                        </span>
                                    </label>
                                </div>
                                @error('firm_agree_terms')
                                    <div class="tooltip-error shadow-lg">
                                        <div class="tooltip-error shadow-lg">
                                            <p class="text-sm">{{ $message }}</p>
                                        </div>
                                    </div>
                                @enderror

                                <div class="flex items-start my-4">
                                    <label class="flex items-start cursor-pointer font-normal text-gray-900">
                                        <input id="firm_cancellation_understand" type="checkbox" name="firm_cancellation_understand"
                                            value="1" {{ old('firm_cancellation_understand') == '1' ? 'checked' : '' }}
                                            class="w-4 h-4 text-blue-600 cursor-pointer bg-white mt-2 border-gray-600 rounded focus:ring-blue-500 focus:ring-2">
                                        <span class="ml-2">
                                            @isset($bookingPage->firm_cancellation_understand_text)
                                                {!! $bookingPage->firm_cancellation_understand_text !!}
                                            @endisset
                                            <span class="text-red-500">*</span>
                                        </span>
                                    </label>
                                </div>
                                @error('firm_cancellation_understand')
                                    <div class="tooltip-error shadow-lg">
                                        <div class="tooltip-error shadow-lg">
                                            <p class="text-sm">{{ $message }}</p>
                                        </div>
                                    </div>
                                @enderror
                            @endif

                            {{-- Pink Ride declarations (if this PX ride is women_only) --}}
                            @if (!empty($ride->women_only))
                                <div class="flex items-start my-4">
                                    <label class="flex items-start cursor-pointer font-normal text-gray-900">
                                        <input type="checkbox" name="pink_ride_agree_terms" value="1"
                                            {{ old('pink_ride_agree_terms') == '1' ? 'checked' : '' }}
                                            class="w-4 h-4 text-blue-600 cursor-pointer bg-white mt-2 border-gray-600 rounded focus:ring-blue-500 focus:ring-2">
                                        <span class="ml-2">
                                            @isset($bookingPage->booking_pink_ride_term_agree_text)
                                                {!! $bookingPage->booking_pink_ride_term_agree_text !!}
                                            @endisset
                                            <span class="text-red-500">*</span>
                                        </span>
                                    </label>
                                </div>
                                @error('pink_ride_agree_terms')
                                    <div class="tooltip-error shadow-lg">
                                        <p class="text-sm">
                                            @isset($bookingPage->pink_ride_tooltip)
                                                {{ $bookingPage->pink_ride_tooltip }}
                                            @endisset
                                        </p>
                                    </div>
                                @enderror
                            @endif

                            {{-- Extra+ Ride declarations (if this PX ride is extra_care) --}}
                            @if (!empty($ride->extra_care))
                                <div class="flex items-start my-4">
                                    <label class="flex items-start cursor-pointer font-normal text-gray-900">
                                        <input type="checkbox" name="extra_care_ride_agree_terms" value="1"
                                            {{ old('extra_care_ride_agree_terms') == '1' ? 'checked' : '' }}
                                            class="w-4 h-4 text-blue-600 cursor-pointer bg-white mt-2 border-gray-600 rounded focus:ring-blue-500 focus:ring-2">
                                        <span class="ml-2">
                                            @isset($bookingPage->booking_extra_care_ride_term_agree_text)
                                                {!! $bookingPage->booking_extra_care_ride_term_agree_text !!}
                                            @endisset
                                            <span class="text-red-500">*</span>
                                        </span>
                                    </label>
                                </div>
                                @error('extra_care_ride_agree_terms')
                                    <div class="tooltip-error shadow-lg">
                                        <p class="text-sm">
                                            @isset($bookingPage->extra_care_ride_tooltip)
                                                {{ $bookingPage->extra_care_ride_tooltip }}
                                            @endisset
                                        </p>
                                    </div>
                                @enderror
                            @endif
                        </div>
                    </div>


                    @if (!$isCashBookingMethod)
                        <div class="border border-gray-200 rounded-lg p-4 mb-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-3">Saved Cards</h2>
                            @if ($cards->isNotEmpty())
                                @php
                                    $primaryCard = $cards->firstWhere('primary_card', '1');
                                    $defaultCardId = $isEditMode
                                        ? ((int) ($existingBooking->card_id ?? 0))
                                        : ($primaryCard
                                            ? $primaryCard->id
                                            : $cards->first()->id);
                                @endphp
                                <div class="space-y-2">
                                    @foreach ($cards as $card)
                                        @php
                                            $isPrimaryCard = (string) $card->primary_card === '1';
                                        @endphp
                                        <label class="flex items-center gap-3 text-sm">
                                            <input type="radio" name="card_id" value="{{ $card->id }}"
                                                @checked(old('card_id', $defaultCardId) == $card->id)
                                                @if (!$isEditMode && $isPrimaryCard) required @endif>
                                            <span>
                                                {{ $card->card_type ?: 'Card' }}
                                                @if ($card->card_number)
                                                    ending {{ substr((string) $card->card_number, -4) }}
                                                @endif
                                                @if ($isPrimaryCard)
                                                    (Primary)
                                                @endif
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('card_id')
                                    <div class="tooltip-error shadow-lg">{{ $message }}</div>
                                @enderror
                            @else
                                <p class="text-sm text-gray-600">No saved cards found. Add a card in payment options before
                                    paying.</p>
                            @endif
                        </div>
                    @endif

                    <div class="flex flex-wrap items-center gap-3">
                        <button type="submit" class="button-exp-fill">
                            {{ $payButtonLabel }}
                        </button>
                        @if (!$isCashBookingMethod)
                            <a href="{{ route('my_cards', ['lang' => optional($selectedLanguage)->abbreviation]) }}"
                                class="button-exp-no-fill">Manage Cards</a>
                        @endif
                    </div>
                    <p class="text-xs text-gray-500 mt-3">
                        @if ($isCashBookingMethod)
                            This booking is cash. No card charge will be made.
                        @else
                            Charges the selected saved card for this route section.
                        @endif
                    </p>
                </form>
            </div>
        </div>
    @endsection

    @section('script')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const seatsInput = document.getElementById('px-booking-seats');
                const perSeatMinor = {{ (int) $perSeatMinor }};
                const perSeatMajor = perSeatMinor / 100;
                const currencySymbol = @json($currencySymbol);

                // Booking fee calculation variables
                const chargeBooking =
                    {{ auth()->user() && auth()->user()->charge_booking ? auth()->user()->charge_booking : '1' }};
                const isStudentFeeWaived = (chargeBooking == '2');
                const settingTaxPercentage =
                    {{ isset($setting->deduct_tax) && $setting->deduct_tax == 'deduct_from_passenger' ? ($setting->tax_type == 'state_wise_tax' && isset($stateTax) ? $stateTax : $setting->tax ?? 0) : 0 }};
                const hasTax =
                    {{ isset($setting->deduct_tax) && $setting->deduct_tax == 'deduct_from_passenger' ? 'true' : 'false' }};

                function formatMajor(minorValue) {
                    return (minorValue / 100).toFixed(2);
                }

                function calculateBookingFee(seatPriceMajor, seats) {
                    if (isStudentFeeWaived) {
                        return 0.0;
                    }
                    // Booking fee is 10% of seat price for rides above $15 per seat
                    if (seatPriceMajor <= 15) {
                        return 0.0;
                    }
                    return (seatPriceMajor * 0.1) * seats;
                }

                function syncTotals() {
                    // Get seats count - either from number input or from seat checkboxes
                    let seats = 1;
                    if (seatsInput) {
                        seats = Math.max(1, parseInt(seatsInput.value || '1', 10) || 1);
                    } else {
                        // Count selected seats from checkboxes
                        const selectedSeats = document.querySelectorAll('input.seat-checkbox:checked');
                        seats = Math.max(1, selectedSeats.length || 1);
                    }
                    
                    const seatsAmount = perSeatMajor * seats;
                    const bookingFee = calculateBookingFee(perSeatMajor, seats);
                    const taxAmount = hasTax ? (bookingFee * settingTaxPercentage / 100) : 0;

                    // Check if Coffee from the Wall is selected
                    const coffeeWallCheckbox = document.getElementById('px-coffee-wall');
                    const useCoffeeWall = coffeeWallCheckbox && coffeeWallCheckbox.checked;

                    let totalDue = seatsAmount + bookingFee + taxAmount;
                    let totalAmountIn = bookingFee;
                    let totalSumIn = totalDue;
                    
                    if (useCoffeeWall) {
                        totalDue = seatsAmount + taxAmount; // Booking fee is paid with coffee
                        totalSumIn = totalDue;
                        totalAmountIn = 0;
                    }

                    // Update display elements
                    const seatsBookedEl = document.getElementById('px-seats-booked');
                    const seatsLabelEl = document.getElementById('px-seats-label');
                    const seatsAmountEl = document.getElementById('px-seats-amount');
                    const bookingFeeEl = document.getElementById('px-booking-fee');
                    const taxAmountEl = document.getElementById('px-tax-amount');
                    const totalDueEl = document.getElementById('px-total-due');
                    const coffeeDeductionEl = document.getElementById('px-coffee-deduction');
                    const coffeeDeductionAmountEl = document.getElementById('px-coffee-deduction-amount');
                    const hideBookingFeeDiv = document.getElementById('hideBookingFee');

                    if (seatsBookedEl) seatsBookedEl.textContent = seats;
                    if (seatsLabelEl) seatsLabelEl.textContent = seats === 1 ? 'seat' : 'seats';
                    if (seatsAmountEl) seatsAmountEl.textContent = formatMajor(seatsAmount * 100);
                    if (bookingFeeEl) bookingFeeEl.textContent = formatMajor(bookingFee * 100);
                    if (taxAmountEl) taxAmountEl.textContent = formatMajor(taxAmount * 100);
                    if (totalDueEl) totalDueEl.textContent = formatMajor(totalDue * 100);

                    // Update hidden inputs
                    const totalSeatsAmountInput = document.querySelector('.totalSeatsAmountInput');
                    const totalAmountInput = document.querySelector('.totalAmountInput');
                    const totalTaxAmountInput = document.querySelector('.totalTaxAmountInput');
                    const totalSumInput = document.querySelector('.totalSumInput');
                    const totalAmountInInput = document.querySelector('.totalAmountIn');
                    const totalSumInInput = document.querySelector('.totalSumIn');
                    const totalSeatsAmountDisplay = document.querySelector('.totalSeatsAmount');
                    const totalAmountDisplay = document.querySelector('.totalAmount');
                    const totalSumDisplay = document.querySelector('.totalSum');

                    if (totalSeatsAmountInput) totalSeatsAmountInput.value = seatsAmount.toFixed(2);
                    if (totalAmountInput) totalAmountInput.value = bookingFee.toFixed(2);
                    if (totalTaxAmountInput) totalTaxAmountInput.value = taxAmount.toFixed(2);
                    if (totalSumInput) totalSumInput.value = totalDue.toFixed(2);
                    if (totalAmountInInput) totalAmountInInput.value = totalAmountIn.toFixed(2);
                    if (totalSumInInput) totalSumInInput.value = totalSumIn.toFixed(2);
                    if (totalSeatsAmountDisplay) totalSeatsAmountDisplay.textContent = currencySymbol + seatsAmount.toFixed(2);
                    if (totalAmountDisplay) totalAmountDisplay.textContent = currencySymbol + bookingFee.toFixed(2);
                    if (totalSumDisplay) totalSumDisplay.textContent = currencySymbol + totalDue.toFixed(2);

                    // Show/hide coffee deduction
                    if (coffeeDeductionEl && coffeeDeductionAmountEl) {
                        if (useCoffeeWall && bookingFee > 0) {
                            coffeeDeductionEl.classList.remove('hidden');
                            coffeeDeductionEl.classList.add('flex');
                            coffeeDeductionAmountEl.textContent = formatMajor(bookingFee * 100);
                        } else {
                            coffeeDeductionEl.classList.add('hidden');
                            coffeeDeductionEl.classList.remove('flex');
                        }
                    }

                    // Show/hide booking fee deduction in coffee wall section
                    if (hideBookingFeeDiv) {
                        if (useCoffeeWall && bookingFee > 0) {
                            hideBookingFeeDiv.classList.remove('hidden');
                            hideBookingFeeDiv.classList.add('flex');
                            const hideBookingFeeAmount = hideBookingFeeDiv.querySelector('.totalAmount');
                            if (hideBookingFeeAmount) hideBookingFeeAmount.textContent = currencySymbol + bookingFee.toFixed(2);
                        } else {
                            hideBookingFeeDiv.classList.add('hidden');
                            hideBookingFeeDiv.classList.remove('flex');
                        }
                    }

                    // Update Stripe charge amount if needed
                    const stripeChargeAmountInput = document.getElementById('stripeChargeAmount');
                    const checkPaymentMethod = document.getElementById('check_payment_method');
                    if (stripeChargeAmountInput && checkPaymentMethod) {
                        if (checkPaymentMethod.value === 'cash') {
                            const chargeAmount = totalAmountIn + taxAmount;
                            stripeChargeAmountInput.value = chargeAmount.toFixed(2);
                        } else {
                            stripeChargeAmountInput.value = totalSumIn.toFixed(2);
                        }
                    }
                }

                if (seatsInput) {
                    seatsInput.addEventListener('input', syncTotals);
                }

                // Listen for coffee wall checkbox changes
                const coffeeWallCheckbox = document.getElementById('px-coffee-wall');
                if (coffeeWallCheckbox) {
                    coffeeWallCheckbox.addEventListener('change', syncTotals);
                }

                syncTotals();

                // Hide field tooltip error when user clicks/focuses inside its parent container
                const bookingForm = document.querySelector('form');

                function hideTooltipInParent(eventTarget) {
                    if (!(eventTarget instanceof HTMLElement) || !bookingForm) return;
                    let node = eventTarget.closest('div, section, label');

                    while (node && node !== bookingForm) {
                        const tooltipInChildren = Array.from(node.children).find((child) =>
                            child instanceof HTMLElement && child.classList.contains('tooltip-error')
                        );
                        if (tooltipInChildren) {
                            tooltipInChildren.remove();
                            return;
                        }

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

                if (bookingForm) {
                    bookingForm.addEventListener('click', function(event) {
                        hideTooltipInParent(event.target);
                    });
                    bookingForm.addEventListener('focusin', function(event) {
                        hideTooltipInParent(event.target);
                    });
                }

                // Scroll to first error on page load if validation errors exist
                const firstError = document.querySelector('.tooltip-error');
                if (firstError) {
                    let errorContainer = firstError.closest('div');

                    while (errorContainer && errorContainer !== bookingForm) {
                        if (errorContainer.tagName === 'SECTION' ||
                            errorContainer.querySelector('input, select, textarea, label')) {
                            break;
                        }
                        errorContainer = errorContainer.parentElement;
                    }

                    const scrollTarget = errorContainer || firstError;
                    scrollTarget.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }

                // Update seat count when seat checkboxes change (if using visual seat selection)
                const seatCheckboxes = document.querySelectorAll('input.seat-checkbox');
                if (seatCheckboxes.length > 0) {
                    seatCheckboxes.forEach(function(checkbox) {
                        checkbox.addEventListener('change', function() {
                            const selectedCount = document.querySelectorAll('input.seat-checkbox:checked').length;
                            const seatCountInput = document.getElementById('seat-count');
                            if (seatCountInput) {
                                seatCountInput.value = selectedCount;
                            }
                            syncTotals();
                        });
                    });
                }
            });

            // Seat selection function (if using visual seat selection)
            function seat_selected(event, clickedSeatId, clickedSeatNumber) {
                event.preventDefault();
                event.stopPropagation();

                var isStudent = {{ (auth()->user() && (auth()->user()->student == '1' || auth()->user()->student == '2')) ? 'true' : 'false' }};
                var paymentMethod = document.getElementById('check_payment_method') ? document.getElementById('check_payment_method').value : 'online';
                var isCashPayment = (paymentMethod === 'cash');
                var maxSeatsForStudent = 2;

                // Build list of available seats (not booked) in order
                var availableSeats = [];
                document.querySelectorAll('#seat-selection-container .seat-item[data-is-booked="0"]').forEach(function(item) {
                    var seatId = parseInt(item.getAttribute('data-seat-id'), 10);
                    var seatNum = parseInt(item.getAttribute('data-seat-number'), 10);
                    availableSeats.push({ id: seatId, seatNumber: seatNum });
                });
                availableSeats.sort(function(a, b) { return a.seatNumber - b.seatNumber; });

                // Get seats to select: all available seats with seat_number <= clicked seat_number
                var seatsToSelect = availableSeats.filter(function(s) { return s.seatNumber <= clickedSeatNumber; });

                // Student limit: cap at 2 seats for Cash payment
                if (isStudent && isCashPayment && seatsToSelect.length > maxSeatsForStudent) {
                    seatsToSelect = seatsToSelect.slice(0, maxSeatsForStudent);
                    alert('Students are limited to booking a maximum of 2 seats per ride for Cash payment rides.');
                }

                // Check if this is a toggle-off: clicked seat was the rightmost selected
                var currentlyChecked = [];
                document.querySelectorAll('input.seat-checkbox:checked').forEach(function(cb) {
                    currentlyChecked.push(parseInt(cb.value, 10));
                });
                var currentlySelectedIds = currentlyChecked;
                var rightmostSelected = currentlySelectedIds.length > 0 ? Math.max.apply(null, currentlySelectedIds.map(function(id) {
                    var s = availableSeats.find(function(s) { return s.id == id; });
                    return s ? s.seatNumber : 0;
                })) : 0;

                var newSelectionIds = [];
                if (rightmostSelected === clickedSeatNumber && currentlySelectedIds.length > 0) {
                    // Toggle off: deselect all
                    newSelectionIds = [];
                } else {
                    newSelectionIds = seatsToSelect.map(function(s) { return s.id; });
                }

                // Update UI immediately
                document.querySelectorAll('input.seat-checkbox').forEach(function(cb) {
                    cb.checked = false;
                });
                newSelectionIds.forEach(function(id) {
                    var checkbox = document.getElementById('number-of-seat-' + id);
                    if (checkbox) checkbox.checked = true;
                });

                document.querySelectorAll('.seat-image').forEach(function(img) {
                    img.src = '{{ asset("assets/seat.png") }}';
                });
                document.querySelectorAll('.seat-number').forEach(function(span) {
                    span.classList.remove('text-green-300');
                });
                newSelectionIds.forEach(function(id) {
                    var img = document.querySelector('.seat-image.seat-unselect-' + id);
                    var span = document.querySelector('.seat-number.seat-number-' + id);
                    if (img) img.src = '{{ asset("assets/seat-hover-1.png") }}';
                    if (span) span.classList.add('text-green-300');
                });

                // Update seat count
                const seatCountInput = document.getElementById('seat-count');
                if (seatCountInput) {
                    seatCountInput.value = newSelectionIds.length;
                }

                // Update totals
                if (typeof syncTotals === 'function') {
                    syncTotals();
                }
            }
        </script>
    @endsection
