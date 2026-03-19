@extends('layouts.template')

@section('style')
    <style>
        /* Match ride_detail: body text in Roboto, sizes from Tailwind (text-xl, text-sm) */
        .booking-page p {
            font-family: 'Roboto', sans-serif;
        }

    </style>
@endsection

@section('content')
    <div class="font-FuturaMdCnBT booking-page">

        @if ($setting)
            @php
                $settingBookingPrice = $setting->booking_price;
                $settingFirmDiscount = $setting->frim_discount;
            @endphp
        @else
            @php
                $settingBookingPrice = '';
                $settingFirmDiscount = '';
            @endphp
        @endif


        @if (session('failure'))

        {{-- todo  --}}
            <div id="myModal" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div onclick="closeModal()" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                        <div
                            class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg w-full modal-border1">
                            <button type="button" onclick="closeModal()"
                                class="absolute top-3 right-3 text-gray-400 hover:text-gray-500">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                            <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                                <div class="sm:flex sm:items-start justify-center">

                                </div>
                                <div class="mt-10 text-center sm:text-left">
                                    <div class="mt-2">
                                        <p class="text-lg text-center text-black">{!! session('failure') !!}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="px-4 pb-6 pt-4  sm:flex sm:flex-row-reverse sm:px-6 justify-center">
                                <a href="javascript:void(0);" onclick="closeModal()" class="whitespace-nowrap inline-flex justify-center rounded bg-red-500 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-red-400 sm:ml-3">
                                    {{ $siteText['close_btn_text'] }}</a>
                                @if (session()->has('phone') && !is_null(session('phone')))
                                    <a href="{{ route('send_verification_code_booking', session('phone')->id) }}"
                                        class="button-exp-fill py-1.5 px-2 text-center inline-block ">
                                        Send verification code
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div id="myModal" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div onclick="closeModal()" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                        <div
                            class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg w-full modal-border">
                            <button type="button" onclick="closeModal()"
                                class="absolute top-3 right-3 text-gray-400 hover:text-gray-500">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                            <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                                <div class="sm:flex sm:items-start justify-center">

                                </div>
                                <div class="text-center">

                                    <div class="">
                                        <p class="text-lg text-center text-black">{!! session('error') !!}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="px-4 pb-6 pt-4  sm:flex sm:flex-row-reverse sm:px-6 justify-center">
                                <a href="" class="button-exp-fill">{{ $siteText['close_btn_text'] }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        
        @if (session('phone_code'))
        {{-- TODO multilingual --}}
            <form method="POST" action="{{ route('verify_number') }}">
                @csrf
                <input type="hidden" name="page" value="booking">

                <div class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                            <div
                                class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-lg bg-white text-center shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">

                                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                                    {{-- <h1 class="mt-4 mb-0">Enter verfication code</h1> --}}
                                    <div class="mt-2">
                                        <p class="text-left">Please enter the four digit code you received on your phone number</p>
                                        <p class="text-center mt-4">Enter code</p>
                                        <div class="flex justify-center mt-4 space-x-2">
                                            <input type="text" name="code[]" maxlength="1"
                                                class="w-10 h-10 text-center block mt-1 border p-1.5 text-base lg:text-lg rounded border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600">
                                            <input type="text" name="code[]" maxlength="1"
                                                class="w-10 h-10 text-center block mt-1 border p-1.5 text-base lg:text-lg rounded border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600">
                                            <input type="text" name="code[]" maxlength="1"
                                                class="w-10 h-10 text-center block mt-1 border p-1.5 text-base lg:text-lg rounded border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600">
                                            <input type="text" name="code[]" maxlength="1"
                                                class="w-10 h-10 text-center block mt-1 border p-1.5 text-base lg:text-lg rounded border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600">

                                        </div>
                                        @error('code')
                                            <div class="relative tooltip -bottom-4 group-hover:flex left-0 right-0 mx-auto">
                                                <div role="tooltip"
                                                    class="mt-2 relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded mx-auto">
                                                    <p class="text-white leading-none text-sm lg:text-base">{{ $message }}
                                                    </p>
                                                </div>
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="px-4 pb-6 pt-4 sm:flex sm:flex-row-reverse sm:px-6 justify-center">
                                    <button type="submit" class="button-exp-sky-fill">Verify phone number</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        @endif
        <div class="container mx-auto my-10 xl:my-14 px-4 xl:px-0">
            @php
                $action = $ride->isInstantBooking() ? route('instant_booking', $ride->id)
                    : ($ride->isRequestBooking() ? route('booking_request', $ride->id) : '');
            @endphp

            <form id="submitForm" method="POST" action="{{ $action }}" enctype="multipart/form-data">
                @csrf

                <input type="hidden" name="ride_detail_id" value="{{ $ride->detail->id }}">
                <input type="hidden" name="type" value="{{ $ride->booking_type->features_setting_id }}">
                <input type="hidden" name="id" value="{{ $ride->id }}">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-y-4 md:gap-4">
                    <div class="col-span-2 flex flex-wrap items-center justify-between gap-3 items-baseline">
                        <h1 class="-mb-2">
                            {{ $bookingPage->main_heading ?? 'Booking a Request' }}
                        </h1>
                        <div class="text-red-500 text-lg mt-4 pr-4">
                            <span class="text-red-500">*</span> {{ $bookingPage->required_fields ?? '' }}
                        </div>
                    </div>
                    @php
                        // Default
                        $bg = null;
                        $text = null;

                        if ($isShortDistanceRide ?? false) {
                            $bg = 'blue';
                            $text = $siteText['proximalocal_ride_description'] ?? 'This is a Short-Distance Ride, and ProximaRide does not apply any Booking Fee.';
                        } elseif ($ride->isPinkExtraCareRide()) {
                            $bg = 'orange';
                            $text = $siteText['pink_extra_ride_description'] ?? 'This is a Pink Ride and an Extra+ Ride.';
                        } elseif ($ride->isExtraCareRide()) {
                            $bg = 'green';
                            $text = $siteText['extra_ride_description'] ?? 'This is a Extra+ Ride.';
                        } elseif ($ride->isPinkRide()) {
                            $bg = 'pink';
                            $text = $siteText['pink_ride_description'] ?? 'This is a Pink Ride.';
                        }
                    @endphp

                    @if ($bg && $text)
                        <div class="col-span-3 w-full">
                        <div class="bg-{{ $bg }}-100 border-l-4 border-{{ $bg }}-500 text-{{ $bg }}-800 px-4 py-2 rounded flex items-center"
                                role="alert">

                            <svg class="w-6 h-6 mr-2 text-{{ $bg }}-500 flex-shrink-0" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none" />
                                    <path d="M12 8v4m0 4h.01" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>

                            <span class="text-lg">{{ $text }}</span>
                                    </div>
                                                    </div>
                                                @endif

                    <x-px.ride-detail-info
                        :ride="$ride"
                        :findRidePage="$findRidePage"
                        :postRidePage="$postRidePage"
                        :rideDetailPage="$rideDetailPage"
                        :selectedLanguage="$selectedLanguage ?? null"
                        :searchOptionGroups="$searchOptionGroups"
                    />


                    <div class="col-span-1">
                        <div class="">
                            <div class=" bg-white rounded-lg shadow-3xl">
                                <div class="bg-primary text-white px-4 py-2 rounded-t-lg">
                                    <h3 class="text-2xl xl:text-3xl">
                                        @isset($bookingPage->booking_label)
                                            {{ $bookingPage->booking_label }}
                                        @endisset
                                    </h3>
                                </div>

                                <div class="bg-white p-4 rounded-b-lg">
                                    <div class="space-y-4 mb-4">
                                        <div class="flex items-center justify-between gap-2">
                                            <div class="flex relative">
                                                <h3 class="text-primary text-2xl xl:text-3xl">
                                                    @isset($bookingPage->seats_available_label)
                                                        {{ $bookingPage->seats_available_label }}
                                                    @endisset
                                                </h3>
                                            </div>
                                        </div>
                                        @if($user->isStudent() && $ride->isCashPayment())
                                            <div class="mb-3 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                                <p class="text-yellow-800 text-sm">
                                                    {!! $bookingPage->note_for_students_text ?? '<strong>Note for Students:</strong> You are limited to booking a maximum of 2 seats per ride for Cash payment rides.' !!}
                                                </p>
                                            </div>
                                        @endif

                                        <div class="flex items-center flex-wrap gap-2 mt-2" id="seat-selection-container">
                                            {{-- TODO : to check more --}}
                                            @foreach ($ride->seatDetail as $detail)
                                                @php
                                               
                                                    $availableForSegment = $availableSeatIdsForSegment ?? $ride->seatDetail->pluck('id')->all();
                                                    $isBooked = $detail->status === 'booked';
                                                    $isHeldByOthers =
                                                        $detail->status === 'hold' &&
                                                        $detail->user_id != optional(auth()->user())->id;
                                                    $isUnavailableForSegment = !in_array($detail->id, $availableForSegment);
                                                    $isUnavailable = $isBooked || $isHeldByOthers || $isUnavailableForSegment;
                                                    $isSelectedByMe =
                                                        !$isUnavailable &&
                                                        ($detail->user_id == optional(auth()->user())->id ||
                                                            in_array($detail->id, old('seats_id', [])));
                                                @endphp
                                                <div class="relative seat-item" data-seat-id="{{ $detail->id }}"
                                                    data-seat-number="{{ $detail->seat_number ?? $loop->iteration }}"
                                                    data-is-booked="{{ $isUnavailable ? '1' : '0' }}">
                                                    @if ($isUnavailable)
                                                        <div class="opacity-50 cursor-not-allowed pointer-events-none">
                                                            <span class="relative inline-block w-6 h-6 md:w-8 md:h-8">
                                                                <img src="{{ asset('assets/seat.png') }}"
                                                                    class="w-8 h-8 object-cover seat-image seat-unselect-{{ $detail->id }}"
                                                                    alt="">
                                                                <span
                                                                    class="absolute mt-2 inset-0 flex items-center justify-center text-sm seat-number seat-number-{{ $detail->id }}">{{ $detail->seat_number ?? $loop->iteration }}</span>
                                                            </span>
                                                        </div>
                                                    @else
                                                        <label class="cursor-pointer inline-block seat-clickable"
                                                            for="number-of-seat-{{ $detail->id }}"
                                                            data-seat-id="{{ $detail->id }}"
                                                            data-seat-number="{{ $detail->seat_number ?? $loop->iteration }}"
                                                            onclick="seat_selected(event, {{ $detail->id }}, {{ $detail->seat_number ?? $loop->iteration }})">
                                                            <input id="number-of-seat-{{ $detail->id }}"
                                                                name="seats_id[]" type="checkbox"
                                                                value="{{ $detail->id }}" class="hidden seat-checkbox"
                                                                {{ $isSelectedByMe ? 'checked' : '' }}
                                                                data-parsley-required="true"
                                                                data-parsley-trigger="blur focusout change"
                                                                data-parsley-required-message="Please select the available seats."
                                                                data-parsley-errors-container="#parsley-seats-error"
                                                                data-seat-id="{{ $detail->id }}"
                                                                data-seat-number="{{ $detail->seat_number ?? $loop->iteration }}">
                                                            <span class="relative inline-block w-6 h-6 md:w-8 md:h-8">
                                                                <img src="{{ $isSelectedByMe ? asset('assets/seat-hover-1.png') : asset('assets/seat.png') }}"
                                                                    class="w-8 h-8 object-cover cursor-pointer seat-image seat-unselect-{{ $detail->id }}"
                                                                    alt="">
                                                                <span
                                                                    class="absolute mt-2 inset-0 flex items-center justify-center text-sm seat-number seat-number-{{ $detail->id }} {{ $isSelectedByMe ? 'text-green-300' : '' }}">{{ $detail->seat_number ?? $loop->iteration }}</span>
                                                            </span>
                                                        </label>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                        @error('seats')
                                            <div id="seats-laravel-error" class="tooltip-error shadow-lg mt-1">{{ $bookingPage->seats_available_tooltip ?? $message }}</div>
                                        @enderror
                                        <!-- Hidden input to store count -->
                                        <input type="hidden" id="seat-count" name="seats" value="">
                                    </div>
                                </div>
                            </div>

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
                                                <span id="selectedSeats">1</span>
                                                @isset($bookingPage->seat_label)
                                                    {{ $bookingPage->seat_label }}
                                                @endisset
                                            </p>
                                        </div>
                                        <p class="totalSeatsAmount text-black"></p>
                                        <input type="hidden" name="seats_amount" class="totalSeatsAmountInput form-control" readonly>
                                    </div>

                                    @if ($ride->isFirmCancellation())
                                        <div class="flex items-center justify-between gap-2">
                                            <p class="text-black">
                                                {{ $bookingPage->firm_discount_label_price_section ?? 'Discount' }}
                                            </p>
                                            <p class="firmDiscountAmt text-black"></p>
                                        </div>
                                        <div class="flex items-center justify-between gap-2">
                                            <p class="text-black">
                                                {{ $bookingPage->firm_your_price_label_price_section ?? 'Your price' }}
                                            </p>
                                            <p class="yourPriceAmt text-black"></p>
                                        </div>
                                    @endif

                                    <div class="flex items-center justify-between gap-2 mt-1">
                                        <div class="flex items-center gap-2">
                                            <p class="text-black">
                                                    {{ $bookingPage->booking_fee_label }}
                                            </p>
                                            @if ($user->isPendingStudent())
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor"
                                                    class="bi bi-info-circle-fill text-black cursor-help inline-block"
                                                    data-tippy-content="{{ $bookingPage->fee_student_pending_text ?? 'Your student verification is pending. Pay the Booking Fee now to secure your seat, and we will refund it automatically once your status is approved (usually within 72 hours).' }}"
                                                    viewBox="0 0 16 16">
                                                    <path
                                                        d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
                                                </svg>
                                            @elseif ($user->hasBookingFeeWaiverFlag())
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor"
                                                    class="bi bi-info-circle-fill text-black cursor-help inline-block"
                                                    data-tippy-content="{{ $bookingPage->fee_charge_waived ?? 'As a verified student, your booking fee is waived. You only pay the booking price.' }}"
                                                    viewBox="0 0 16 16">
                                                    <path
                                                        d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
                                                </svg>
                                            @endif
                                        </div>
                                        <p class="totalAmount text-black"></p>
                                        <input type="hidden" name="booking_credit" class="totalAmountInput form-control" readonly>
                                    </div>
                                    @if (isset($setting->deduct_tax) && $setting->deduct_tax == 'deduct_from_passenger')
                                        <input type="hidden" value="{{ $settingTaxPercentage }}" name="tax_percentage">
                                        <input type="hidden" value="{{ $setting->deduct_tax }}" name="deduct_tax">
                                        <input type="hidden" value="{{ $setting->tax_type }}" name="tax_type">
                                        <div class="flex items-center justify-between gap-2 mt-1">
                                            <p class="text-black">
                                                @isset($bookingPage->tax_label)
                                                    {{ $bookingPage->tax_label ?? 'Tax' }}
                                                @endisset
                                            </p>
                                            <p class="taxAmount text-black">0</p>
                                            <input type="hidden" name="tax_amount" class="totalTaxAmountInput form-control" readonly>
                                        </div>
                                    @endif

                                    @php
                                        $pricePerSeat = (float) ($ride->price_minor ?? 0);
                                        $bookingFeeZero = $user->hasBookingFeeWaiverFlag() || $pricePerSeat < 15;
                                    @endphp
                                    @if ($coffeeBalance > 0)
                                        <div class="flex items-center justify-between gap-2 mt-1">
                                            <div
                                                class="flex {{ $bookingFeeZero ? 'opacity-50 pointer-events-none cursor-not-allowed' : '' }}">
                                                <input type="checkbox" id="apply_coffee_wall" name="coffee_wall"
                                                    value="1" class="form-control hidden peer"
                                                    {{ $bookingFeeZero ? 'disabled' : '' }}>
                                                <label for="apply_coffee_wall" class="inline-flex items-center justify-center w-full px-2 py-0.5 text-primary bg-white border-2 border-primary rounded {{ $bookingFeeZero ? 'cursor-not-allowed' : 'cursor-pointer' }} peer-checked:bg-primary peer-checked:text-white">
                                                    <span class="font-medium font-FuturaMdCnBT text-xl line-clamp-2 max-w-36 w-full">
                                                        {{ $bookingPage->coffee_from_wall_label ?? 'Pay booking fee with Coffee from the Wall' }}
                                                    </span>
                                                </label>
                                                @php
                                                    if($ride->isCashPayment())
                                                        $paymentMethodTooltipText = $bookingPage->coffee_wall_cash_text ?? "To be paid in cash directly to the driver at the time of the ride.";
                                                    elseif ($ride->isOnlinePayment()) {
                                                        $paymentMethodTooltipText = $bookingPage->coffee_wall_online_payment_text ?? "ProximaRide will transfer this amount to the driver only after the ride is completed.";
                                                    } else {
                                                        $paymentMethodTooltipText = $bookingPage->coffee_wall_secure_cash_text ?? "This amount is pre-authorized to ProximaRide now and will be refunded to you once you meet the driver and pay them in cash.";
                                                    }
                                                @endphp
                                                <div class="flex items-center gap-1 ml-2">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                        fill="currentColor"
                                                        class="bi bi-info-circle-fill text-black cursor-help inline-block"
                                                        data-tippy-content="{{ $paymentMethodTooltipText }}"
                                                        viewBox="0 0 16 16">
                                                        <path
                                                            d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
                                                    </svg>
                                                </div>
                                            </div>
                                            <div id="hideBookingFee" class="hidden items-center space-x-1">
                                                <p class="text-black">-</p>
                                                <p class="totalAmount text-black"></p>
                                                <div class="flex items-center gap-1 ml-2">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                        fill="currentColor"
                                                        class="bi bi-info-circle-fill text-black cursor-help inline-block"
                                                        data-tippy-content="{{ $paymentMethodTooltipText }}"
                                                        viewBox="0 0 16 16">
                                                        <path
                                                            d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <input type="hidden" value="{{ $ride->isCashPayment() ? 'cash' : 'online' }}" id="check_payment_method">

                                    @if ($ride->isCashPayment())
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
                                                {{ $bookingPage->total_label }}
                                        </p>
                                        <div>
                                            <p class="totalSum text-right"></p>
                                            <span id="discount" class="text-right"></span>
                                        </div>
                                        <input type="hidden" name="total" class="totalSumInput form-control" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 bg-white rounded-lg overflow-hidden shadow-3xl">
                                <div class="bg-primary text-white px-4 py-2">
                                    <h3 class="text-2xl xl:text-3xl">
                                        {{ $bookingPage->message_to_driver_label ?? 'Message to driver' }}
                                    </h3>
                                </div>
                                <div class="bg-white p-4">
                                    <div class="mb-4 w-full">
                                        <label for="meeting" class="text-gray-900 font-medium text-lg mb-2"></label>
                                        <textarea id="meeting" rows="5" name="driver_message"
                                            class="block p-2.5 w-full text-gray-900 bg-white rounded border border-gray-300 focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 mt-2"
                                            placeholder="{{ $bookingPage->message_driver_placeholder ?? '' }}">{{ old('driver_message') }}</textarea>
                                        @error('driver_message')
                                            <div class="tooltip-error shadow-lg mt-1">{{ $bookingPage->chat_with_driver_tooltip ?? $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 bg-white rounded-lg overflow-hidden shadow-3xl">
                                <div class="bg-primary text-white px-4 py-2">
                                    <h3 class="text-2xl xl:text-3xl">
                                        {{ $bookingPage->user_declarations_label ?? 'User declarations' }}
                                    </h3>
                                </div>
                                <div class="bg-white p-4">
                                    
                                            <p class="text-left">● @isset($bookingPage->booking_disclaimer_on_time)
                                                    {!! $bookingPage->booking_disclaimer_on_time !!}
                                                @endisset
                                            </p>
                                
                                    <p class="text-left mt-4">●<strong> Pink Rides: </strong>
                                                {{ $bookingPage->booking_disclaimer_pink_ride ?? 'I know that ProximaRide are exclusive to ProximaRide female members. If I am booking on a Pink Ride, I will not be accompanied by male members who are above 12 years of age, nor will I send a male member in my place. If I do, the driver will not take me or them, and I will not be refunded' }}
                                            </p>
                                
                                    <p class="text-left mt-4">●<strong> Extra+ Rides: </strong>
                                                {{ $bookingPage->booking_disclaimer_extra_care_ride ?? 'I know that Extra+ Rides are exclusive to members with highest review score. If I am booking on an Extra+ Ride, I will adhere to its standards' }}
                                            </p>

                                    <div class="relative">
                                        <div class="flex items-start my-4">
                                            <label class="flex items-start cursor-pointer font-normal text-gray-900">
                                                <input id="" type="checkbox" name="agree_terms" value="1" @checked(old('agree_terms'))
                                                    class="w-4 h-4 text-blue-600 cursor-pointer bg-white mt-2 border-gray-600 rounded focus:ring-blue-500  focus:ring-2">
                                                <span class="ml-2">
                                                    @isset($bookingPage->booking_term_agree_text)
                                                        {!! $bookingPage->booking_term_agree_text !!}
                                                    @endisset
                                                    <span class="text-red-500">*</span>
                                                </span>
                                            </label>
                                        </div>
                                    @error('agree_terms')
                                            <div class="tooltip-error shadow-lg mt-1">{{ $bookingPage->aggreement_tooltip ?? $message }}</div>
                                    @enderror
                                    </div>

                                    <div class="relative">
                                        @if ($ride->isFirmCancellation())
                                            @php
                                                if ($setting) {
                                                    $settingFirmDiscount = $setting->frim_discount;
                                                }
                                                $firmText = str_replace(
                                                    ':discount',
                                                    $settingFirmDiscount,
                                                    $bookingPage->booking_disclaimer_firm,
                                                );
                                            @endphp
                                            <div class="flex items-start my-4">
                                                <label class="flex items-start cursor-pointer font-normal text-gray-900">
                                                    <input id="firm_agree_terms" type="checkbox" name="firm_agree_terms"
                                                        value="1" @checked(old('firm_agree_terms'))
                                                        class="w-4 h-4 text-blue-600 cursor-pointer bg-white mt-2 border-gray-600 rounded focus:ring-blue-500  focus:ring-2">
                                                    <span class="ml-2">
                                                        {!! $firmText !!}
                                                        <span class="text-red-500">*</span>
                                                    </span>
                                                </label>
                                            </div>
                                            @error('firm_agree_terms')
                                                <div class="tooltip-error shadow-lg mt-1">{{ $bookingPage->booking_disclaimer_firm_tooltip ?? $message }}</div>
                                            @enderror

                                                {{-- Second checkbox for Firm Cancellation Policy --}}
                                                <div class="flex items-start my-4">
                                                    <label class="flex items-start cursor-pointer font-normal text-gray-900">
                                                        <input id="firm_cancellation_understand" type="checkbox"
                                                        name="firm_cancellation_understand" value="1" @checked(old('firm_cancellation_understand'))
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
                                                <div class="tooltip-error shadow-lg mt-1">{{ $message }}</div>
                                            @enderror
                                        @endif
                                    </div>

                                    @if ($ride->isPinkRide())
                                        <div class="flex items-start my-4">
                                            <label class="flex items-start cursor-pointer font-normal text-gray-900">
                                                <input id="" type="checkbox" name="pink_ride_agree_terms"
                                                    value="1" @checked(old('pink_ride_agree_terms'))
                                                    class="w-4 h-4 text-blue-600 cursor-pointer bg-white mt-2 border-gray-600 rounded focus:ring-blue-500  focus:ring-2">
                                                <span class="ml-2">
                                                    @isset($bookingPage->booking_pink_ride_term_agree_text)
                                                        {!! $bookingPage->booking_pink_ride_term_agree_text !!}
                                                    @endisset
                                                    <span class="text-red-500">*</span>
                                                </span>
                                            </label>
                                        </div>
                                        @error('pink_ride_agree_terms')
                                            <div class="tooltip-error shadow-lg mt-1">{{ $bookingPage->pink_ride_tooltip ?? $message }}</div>
                                        @enderror

                                    @endif

                                    @if ($ride->isExtraCareRide())
                                        <div class="flex items-start my-4">
                                            <label class="flex items-start cursor-pointer font-normal text-gray-900">
                                                <input id="" type="checkbox" name="extra_care_ride_agree_terms"
                                                    value="1" @checked(old('extra_care_ride_agree_terms'))
                                                    class="w-4 h-4 text-blue-600 cursor-pointer bg-white mt-2 border-gray-600 rounded focus:ring-blue-500  focus:ring-2">
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
                                                {{ $bookingPage->extra_care_ride_tooltip ?? $message }}
                                        </div>
                                        @enderror
                                    @endif

                                    @if ($ride->isCashPayment() && $ride->price_minor <= 15)
                                        <div></div>
                                    @else
                                        <div id="paymentSection" class="space-y-4 mb-4">
                                            <h3 class="text-primary text-2xl xl:text-3xl">
                                                {{ $bookingPage->like_to_pay_label ?? 'Choose Your Payment Method' }}
                                            </h3>
                                            <x-payment-list :cards="$cards" :paymentSettingDetail="$paymentSettingDetail" />
                                        </div>
                                    @endif

                                        <div class="flex justify-center items-center mt-4">
                                            <button id="submitButton" class="button-exp-fill" type="submit">
                                            {{ $bookingPage->pay_and_request_to_book_btn_text ?? 'Pay and Request to Book' }}
                                            </button>
                                        </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

        </div>



        <div id="bookingModal" class="hidden fixed z-50 inset-0 overflow-y-auto">
            <div class="relative z-50">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                        <div
                            class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg w-full modal-border">
                            <button type="button" id="close-modal"
                                class="absolute top-3 right-3 text-gray-400 hover:text-gray-500">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                            <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                                <div class="sm:flex sm:items-start justify-center">

                                </div>
                                <div class="text-center  sm:ml-4 sm:mt-0 sm:text-left">

                                    <div class="w-full">
                                        <p class="text-md text-center mt-10 text-gray-500"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="px-4 pb-6 pt-4 sm:flex sm:flex-row-reverse sm:px-6 justify-center">
                                <button type="button" id="close-popup"
                                    class="inline-flex justify-center rounded bg-blue-500 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-blue-400 sm:ml-3 sm:w-24">
                                    Close
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Student Seat Limit Modal -->
        <div id="studentSeatLimitModal" class="hidden fixed z-50 inset-0 overflow-y-auto"
            aria-labelledby="student-seat-limit-modal-title" role="dialog" aria-modal="true">
            <div onclick="closeStudentSeatLimitModal()"
                class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0">
                    <div class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border"
                        onclick="event.stopPropagation()">
                        <button type="button" onclick="closeStudentSeatLimitModal()"
                            class="absolute top-4 right-4 text-gray-400 hover:text-gray-500 z-50">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                        <div class="bg-white px-4 mt-10 sm:mt-1 pb-4 pt-16 sm:p-6 sm:pb-4 sm:pt-16">
                            <div class="sm:flex sm:items-start justify-center">
                                <div
                                    class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full sm:mx-0 sm:h-10 sm:w-10 bg-yellow-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-yellow-600">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="text-center sm:ml-4 sm:mt-0 sm:text-left">
                                <div class="">
                                    <h3 class="text-3xl text-center font-FuturaMdCnBT text-gray-900 mb-4"
                                        id="student-seat-limit-modal-title">Seat Limit Reached</h3>
                                </div>
                                <div class="mt-2 w-full">
                                    <p class="can-exp-p text-center">Students are limited to booking a maximum of 2 seats per ride for Cash payment rides.</p>
                                </div>
                            </div>
                        </div>
                        <div class="px-4 pb-6 pt-4 sm:flex sm:flex-row-reverse sm:px-6 justify-center">
                            <button type="button" onclick="closeStudentSeatLimitModal()"
                                class="inline-flex justify-center rounded bg-primary px-6 py-2 font-FuturaMdCnBT text-lg text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-blue-600">
                                {{ $siteText['ok_btn_text'] }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <script>
        (function() {
            'use strict';

            // ============================================
            // Configuration & Constants
            // ============================================
            const BOOKING_SEATS_STORAGE_KEY = 'booking_seats_{{ $ride->id }}_{{ $ride->detail->id }}';
            const SEAT_IMAGES = {
                selected: '{{ asset('assets/seat-hover-1.png') }}',
                unselected: '{{ asset('assets/seat.png') }}'
            };
            const SEAT_HOLD_ROUTE = '{{ route('seat_on_hold') }}';
            const CSRF_TOKEN = '{{ csrf_token() }}';
            const MAX_STUDENT_SEATS = 2;
            const BOOKING_FEE_PERCENTAGE = 0.10; // 10%
            const MIN_PRICE_FOR_BOOKING_FEE = 15;

            // Calculate booking price based on user status and ride price
            const chargeBooking = {{ auth()->user() && auth()->user()->charge_booking ? auth()->user()->charge_booking : '1' }};
            const isStudentFeeWaived = (chargeBooking == '2');
            const pricePerSeat = parseFloat(@json($ride->price_minor ?? 0));
            const bookingPrice = (isStudentFeeWaived || pricePerSeat < MIN_PRICE_FOR_BOOKING_FEE) 
                ? 0.0 
                : parseFloat(pricePerSeat * BOOKING_FEE_PERCENTAGE);

            // Ride configuration
            const rideConfig = {
                isFirmRide: {{ $ride->isFirmCancellation() ? 'true' : 'false' }},
                firmDiscount: parseFloat("{{ $settingFirmDiscount ?? 0 }}"),
                taxPercentage: parseFloat("{{ $settingTaxPercentage ?? 0 }}"),
                seatPrice: parseFloat({{ $ride->price_minor ?? 0 }}),
                paymentMethod: '{{ $ride->isCashPayment() ? 'cash' : 'online' }}'
            };

            // ============================================
            // Verification Code Input Handler
            // ============================================
        document.addEventListener('DOMContentLoaded', function() {
                const codeInputs = document.querySelectorAll('input[name="code[]"]');
                if (codeInputs.length === 0) return;

                codeInputs[0].focus();

                codeInputs.forEach((input, index) => {
                    input.addEventListener('input', function() {
                        if (this.value.length === 1 && index < codeInputs.length - 1) {
                            codeInputs[index + 1].focus();
                        }
                    });

                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Backspace' && this.value.length === 0 && index > 0) {
                            codeInputs[index - 1].focus();
                        } else if (e.key === 'ArrowLeft' && index > 0) {
                            codeInputs[index - 1].focus();
                            e.preventDefault();
                        } else if (e.key === 'ArrowRight' && index < codeInputs.length - 1) {
                            codeInputs[index + 1].focus();
                            e.preventDefault();
                        }
                    });

                input.addEventListener('paste', function(e) {
                    e.preventDefault();
                    const pasteData = e.clipboardData.getData('text').trim();
                        const maxIndex = Math.min(index + pasteData.length, codeInputs.length);
                        
                        for (let i = 0; i < pasteData.length && (index + i) < codeInputs.length; i++) {
                            codeInputs[index + i].value = pasteData[i];
                        }
                        
                        if (maxIndex > 0) {
                            codeInputs[Math.min(maxIndex - 1, codeInputs.length - 1)].focus();
                        }
                });
            });
        });

            // ============================================
            // Seat Selection Management
            // ============================================
            function restoreSeatSelection() {
                try {
                    const saved = sessionStorage.getItem(BOOKING_SEATS_STORAGE_KEY);
                    if (!saved) return;

                    const savedIds = JSON.parse(saved);
                    $("input[name='seats_id[]']").each(function() {
                        const id = $(this).val();
                        const shouldCheck = savedIds.indexOf(id) !== -1;
                        $(this).prop('checked', shouldCheck);
                        
                        const $seatImage = $(".seat-image.seat-unselect-" + id);
                        const $seatNumber = $(".seat-number.seat-number-" + id);
                        
                        $seatImage.attr('src', shouldCheck ? SEAT_IMAGES.selected : SEAT_IMAGES.unselected);
                        $seatNumber.toggleClass('text-green-300', shouldCheck);
                    });
                } catch (e) {
                    console.warn('Failed to restore seat selection:', e);
                }
            }

            function persistSeatSelection() {
                try {
                    const ids = [];
                    $("input[name='seats_id[]']:checked").each(function() {
                        ids.push($(this).val());
                    });
                    sessionStorage.setItem(BOOKING_SEATS_STORAGE_KEY, JSON.stringify(ids));
                } catch (e) {
                    console.warn('Failed to persist seat selection:', e);
                }
            }

            function updateSeatUI(seatIds) {
                $(".seat-image").attr('src', SEAT_IMAGES.unselected);
                $(".seat-number").removeClass('text-green-300');
                
                seatIds.forEach(function(id) {
                    $(".seat-image.seat-unselect-" + id).attr('src', SEAT_IMAGES.selected);
                    $(".seat-number.seat-number-" + id).addClass('text-green-300');
                });
            }

            // ============================================
            // Modal Management
            // ============================================
            function showStudentSeatLimitModal() {
                const modal = document.getElementById('studentSeatLimitModal');
                if (!modal) {
                    alert('Students are limited to booking a maximum of 2 seats per ride for Cash payment rides.');
                    return;
                }
                modal.classList.remove('hidden');
                modal.style.cssText = 'display: block !important; visibility: visible !important; opacity: 1 !important; z-index: 50 !important;';
                
                const backdrop = modal.querySelector('.fixed.inset-0.bg-gray-500');
                if (backdrop) {
                    backdrop.style.display = 'block';
                }
            }

            function closeStudentSeatLimitModal() {
                const modal = document.getElementById('studentSeatLimitModal');
                if (modal) {
                    modal.classList.add('hidden');
                    ['display', 'visibility', 'opacity', 'z-index'].forEach(prop => {
                        modal.style.removeProperty(prop);
                    });
                }
            }

            function closeModal() {
                const modal = document.getElementById('myModal');
                if (modal) {
                    modal.classList.add('hidden');
                }
            }

            // Make modal functions globally available
            window.showStudentSeatLimitModal = showStudentSeatLimitModal;
            window.closeStudentSeatLimitModal = closeStudentSeatLimitModal;
            window.closeModal = closeModal;

            // ============================================
            // Price Calculation
            // ============================================
            function getCheckedTerms() {
                const getCheckboxValue = (name) => {
                    const field = document.querySelector(`[name="${name}"]`);
                    return field ? field.checked : true;
                };

                return {
                    agreeTerms: getCheckboxValue('agree_terms'),
                    firmTerms: getCheckboxValue('firm_agree_terms'),
                    pinkRideTerms: getCheckboxValue('pink_ride_agree_terms'),
                    extraCareTerms: getCheckboxValue('extra_care_ride_agree_terms')
                };
            }

            function updateTotalAmount() {
                const selectedSeats = $("input[name='seats_id[]']:checked").length;
                const seatCountInput = document.getElementById('seat-count');
                if (seatCountInput) {
                    seatCountInput.value = selectedSeats;
                }

                let totalAmount = bookingPrice * selectedSeats;
                let totalSeatsAmount = rideConfig.seatPrice * selectedSeats;
                const totalRideSeatAmount = totalSeatsAmount;

                // Apply firm ride discount
                if (rideConfig.isFirmRide && rideConfig.firmDiscount > 0) {
                    const firmDiscountAmount = (totalSeatsAmount * rideConfig.firmDiscount) / 100;
                    totalSeatsAmount -= firmDiscountAmount;
                    $(".firmDiscountAmt").text('$' + firmDiscountAmount.toFixed(2));
                    $(".yourPriceAmt").text('$' + totalSeatsAmount.toFixed(2));
                }

                // Calculate tax
                const taxAmount = (totalAmount * rideConfig.taxPercentage) / 100;
                let totalSum = totalAmount + totalSeatsAmount + taxAmount;
                let totalAmountIn = totalAmount;
                let totalSumIn = totalSum;

                // Handle coffee wall option
                const isCoffeeWallChecked = $('input[name="coffee_wall"]:checked').val() === '1';
                const hideBookingFeeDiv = document.getElementById('hideBookingFee');
                
                if (isCoffeeWallChecked) {
                    totalSumIn = totalSum - totalAmount;
                    totalSum = totalSum - totalAmount;
                    totalAmountIn = 0;
                if (hideBookingFeeDiv) {
                        hideBookingFeeDiv.classList.remove('hidden');
                        hideBookingFeeDiv.classList.add('flex');
                }
            } else {
                if (hideBookingFeeDiv) {
                        hideBookingFeeDiv.classList.add('hidden');
                        hideBookingFeeDiv.classList.remove('flex');
                    }
                }

                // Show/hide payment section based on terms
                const terms = getCheckedTerms();
                const allTermsChecked = terms.agreeTerms && terms.firmTerms && 
                                       terms.pinkRideTerms && terms.extraCareTerms;
                
                ['paymentSection', 'paymentSectionGPay'].forEach(sectionId => {
                    const section = document.getElementById(sectionId);
                    if (section && allTermsChecked) {
                        section.classList.remove('hidden');
                    }
                });

                // Update UI
            $('#selectedSeats').text(selectedSeats);
                $('.totalAmount').text('$' + totalAmount.toFixed(2));
                $('.taxAmount').text('$' + taxAmount.toFixed(2));
                $('.totalSeatsAmount').text('$' + totalRideSeatAmount.toFixed(2));
                $('.totalSum').text('$' + totalSum.toFixed(2));
                
                // Update hidden inputs
                $('.totalTaxAmountInput').val(taxAmount);
            $('.totalAmountInput').val(totalAmount);
            $('.totalAmountIn').val(totalAmountIn);
            $('.totalSeatsAmountInput').val(totalSeatsAmount);
            $('.totalSumIn').val(totalSumIn);
                $('.totalSumInput').val(totalSum);

                // Update payment request if available
            if (typeof paymentRequest !== 'undefined' && paymentRequest && typeof paymentRequest.update === 'function') {
                    const chargeAmount = rideConfig.paymentMethod === 'cash' 
                        ? totalAmountIn + taxAmount 
                        : totalSumIn;
                    paymentRequest.update({
                        total: {
                            label: 'Total',
                            amount: Math.round(chargeAmount * 100)
                        }
                    });
                }
            }

            // ============================================
            // Seat Selection Handler
            // ============================================
        function seat_selected(event, clickedSeatId, clickedSeatNumber) {
            event.preventDefault();
            event.stopPropagation();

                const isStudent = {{ auth()->user() && (auth()->user()->student == '1' || auth()->user()->student == '2') ? 'true' : 'false' }};
                const paymentMethod = $('#check_payment_method').val();
                const isCashPayment = (paymentMethod === 'cash');

                // Build sorted list of available seats
                const availableSeats = [];
            $('#seat-selection-container .seat-item[data-is-booked="0"]').each(function() {
                availableSeats.push({
                        id: $(this).data('seat-id'),
                        seatNumber: parseInt($(this).data('seat-number'), 10)
                });
            });
                availableSeats.sort((a, b) => a.seatNumber - b.seatNumber);

                // Get seats to select (all seats <= clicked seat)
                let seatsToSelect = availableSeats.filter(s => s.seatNumber <= clickedSeatNumber);

                // Apply student limit for cash payments
                if (isStudent && isCashPayment && seatsToSelect.length > MAX_STUDENT_SEATS) {
                    seatsToSelect = seatsToSelect.slice(0, MAX_STUDENT_SEATS);
                showStudentSeatLimitModal();
            }

                // Get currently selected seats
                const currentlySelectedIds = [];
            $("input.seat-checkbox:checked").each(function() {
                    currentlySelectedIds.push(parseInt($(this).val(), 10));
                });

                // Find rightmost selected seat number
                const rightmostSelected = currentlySelectedIds.length > 0
                    ? Math.max(...currentlySelectedIds.map(id => {
                        const seat = availableSeats.find(s => s.id === id);
                        return seat ? seat.seatNumber : 0;
                    }))
                    : 0;

                // Determine new selection (toggle off if clicking rightmost)
                const newSelectionIds = (rightmostSelected === clickedSeatNumber && currentlySelectedIds.length > 0)
                    ? []
                    : seatsToSelect.map(s => s.id);

                // Update checkboxes
            $("input.seat-checkbox").prop('checked', false);
                newSelectionIds.forEach(id => {
                $("#number-of-seat-" + id).prop('checked', true);
            });

                // Update error visibility
                const seatsErrorEl = document.getElementById('seats-laravel-error') ||
                document.querySelector('#seat-selection-container + .tooltip-error');
                const seatsErrorContainer = document.getElementById('seats-error');
                
            if (newSelectionIds.length > 0) {
                if (seatsErrorEl) {
                    seatsErrorEl.classList.add('hidden');
                    seatsErrorEl.style.display = 'none';
                }
                if (seatsErrorContainer) {
                    seatsErrorContainer.classList.add('hidden');
                    seatsErrorContainer.style.display = 'none';
                }
            } else {
                if (seatsErrorEl) {
                    seatsErrorEl.classList.remove('hidden');
                    seatsErrorEl.style.display = '';
                }
                    if (seatsErrorContainer) {
                        seatsErrorContainer.classList.add('hidden');
                    }
                }

                // Update seat UI
                updateSeatUI(newSelectionIds);

                // Determine seats to hold/release
                const toHold = newSelectionIds.filter(id => currentlySelectedIds.indexOf(id) < 0);
                const toRelease = currentlySelectedIds.filter(id => newSelectionIds.indexOf(id) < 0);

                // If no changes, just update totals
                if (toHold.length === 0 && toRelease.length === 0) {
                    updateTotalAmount();
                    persistSeatSelection();
                    return;
                }

                // Make API calls to hold/release seats
                const apiCalls = [];
                [...toRelease, ...toHold].forEach(seatId => {
                apiCalls.push($.ajax({
                        url: SEAT_HOLD_ROUTE,
                    type: 'POST',
                        data: { seat_id: seatId, _token: CSRF_TOKEN }
                }));
            });

                const seatHoldInfoMessage = {!! json_encode(
                $bookingPage->seats_available_info_text_ ??
                    "Your selected seat(s) will be held for 10 minutes. If the booking isn't completed within that time, the seat(s) will be released and made available to others.",
            ) !!};
                
                const isSuccessMessage = (msg) => {
                if (!msg) return false;
                    return msg === 'Seat on hold successfully' || msg.indexOf('will be held for 10 minutes') !== -1;
                };

                $.when(...apiCalls).done(function() {
                    const responses = arguments.length === 1 ? [arguments[0]] : Array.from(arguments);
                    const hasError = responses.some(r => r[0] && r[0].message && !isSuccessMessage(r[0].message));
                    
                    const modalMessageEl = document.querySelector('#bookingModal .text-md.text-gray-500');
                    const bookingModal = document.getElementById('bookingModal');
                    
                if (hasError) {
                        const errMsg = (responses.find(r => r[0] && r[0].message && !isSuccessMessage(r[0].message)) || [{}])[0].message;
                        if (modalMessageEl) modalMessageEl.textContent = errMsg || 'Seat could not be held.';
                        if (bookingModal) bookingModal.classList.remove('hidden');
                } else if (toHold.length > 0) {
                        if (modalMessageEl) modalMessageEl.textContent = seatHoldInfoMessage;
                        if (bookingModal) bookingModal.classList.remove('hidden');
                }
                    
                updateTotalAmount();
                persistSeatSelection();
            }).fail(function() {
                // Revert on error
                $("input.seat-checkbox").prop('checked', false);
                    currentlySelectedIds.forEach(id => {
                    $("#number-of-seat-" + id).prop('checked', true);
                });
                    updateSeatUI(currentlySelectedIds);
                updateTotalAmount();
            });
        }

            // Make seat_selected globally available
            window.seat_selected = seat_selected;

            // ============================================
            // Event Handlers
            // ============================================
            $(document).ready(function() {
                restoreSeatSelection();
                updateTotalAmount();

                // Update totals when type or coffee_wall changes
                $('input[name="type"], input[name="coffee_wall"]').on('change', updateTotalAmount);

                // Handle payment method changes
                $('input[type=radio][name=payment_method]').on('change', function() {
                    if (this.value === 'credit_card') {
                        $('.cards').removeClass('hidden');
                    } else if (this.value === 'paypal') {
                        $('.cards').addClass('hidden');
                    }
                });

                // Modal close handlers
                const bookingModal = document.getElementById('bookingModal');
                if (bookingModal) {
                    const closeHandlers = ['close-modal', 'close-popup'];
                    closeHandlers.forEach(id => {
                        const el = document.getElementById(id);
                        if (el) {
                            el.addEventListener('click', () => {
                                bookingModal.classList.add('hidden');
                            });
                        }
                    });
                }
            });

        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeStudentSeatLimitModal();
            }
        });

            // Handle browser back button
            window.addEventListener("pageshow", function() {
            const navEntries = performance.getEntriesByType("navigation");
            if (navEntries.length > 0 && navEntries[0].type === "back_forward") {
                    window.location.replace('{{ route('my_trips', ['lang' => $selectedLanguage->abbreviation ?? 'en']) }}');
            }
        });
        })();
    </script>
@endsection
