@props([
    'ride',
    'detailRoute' => 'my_ride_detail',
    'wrapperClass' => 'relative even:bg-gray-200 odd:bg-white',
    'showStatus' => false,
    'showBookingInfo' => true,
    'showRideBookingInfo' => true,
    'showOptions' => true,
    'showKindBorder' => false,
])

@php
    $seatPrice = ($ride->detail->price ?? 0) / 100;
    $requestedBookingsCount = $ride->relationLoaded('bookings')
        ? (int) $ride->bookings->filter(function ($booking) {
            return in_array($booking->status, ['waiting', 0, '0'], true);
        })->count()
        : (int) $ride->bookings()->whereIn('status', ['waiting', 0])->count();
@endphp

@php

    $classes = 'rounded-lg shadow-3xl border-[3px] border-solid';
    $wrapperStart = '';
    $wrapperEnd = '';

    if($showKindBorder){
        if ($ride->isPinkExtraCareRide()) {
            $wrapperStart = '<div class="rounded-lg border-[3px] border-solid border-green-500 p-[2px] shadow-3xl">';
            $wrapperEnd = '</div>';
            $classes .= ' border-pink-500';
        } elseif ($ride->isPinkRide()) {
            $classes .= ' border-pink-500';
        } elseif ($ride->isExtraCareRide()) {
            $classes .= ' border-green-500';
        } elseif ($ride->isShortDistanceRide()) {
            $classes .= ' border-blue-500';
        }
    }
@endphp
{!! $wrapperStart !!}
<div class="{{ $wrapperClass }} {{ $classes }}" id="ride-{{ $ride->id }}">
        <div class="rounded-lg shadow-3xl border-[3px] border-solid border-gray-100" onclick="goToRideDetail('{{ route($detailRoute, ['lang'=>app()->getLocale(),'id'=>$ride->id]) }}')">
            <div class="flex flex-col sm:flex-col lg:flex-row  justify-between gap-4 p-4">
                <div class="route-info">
                    <x-px.route-info 
                        :ride="$ride" 
                    />
                </div>
                <div class="ride-seat-info flex flex-col items-end gap-2">
                    @php
                        $ride_status = null;
                        if ($ride->isCompleted()) {
                            $ride_status = [
                                'label' => $rideDetailPage->ride_completed_label,
                                'class' => 'bg-green-100 text-green-600',
                            ];
                        } elseif ($ride->isCancelled()) {
                            $ride_status = [
                                'label' => $rideDetailPage->ride_cancelled_label,
                                'class' => 'bg-red-100 text-red-600',
                            ];
                        }
                    @endphp
                    @if($ride_status)
                        <p class="w-fit px-2 py-1 rounded text-sm {{ $ride_status['class'] }}">
                            {{ $ride_status['label'] }}
                        </p>
                    @endif

                    <p class="font-medium text-2xl text-right">
                        {{ str_replace(':count', $ride->seats, $rideDetailPage->total_seats_label ?? 'Total :count seats') }}
                    </p>
                    <div class="flex items-center gap-2 text-primary justify-end">
                        @if (isset($firm_cancellation_discount) && $firm_cancellation_discount != '' && $ride->isFirmCancellation())
                            <span class="line-through">
                                ${{ number_format((float) $seatPrice, 2) }}
                            </span>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="h-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
                            </svg>
                            <span>
                                ${{ $seatPrice - ($seatPrice * $firm_cancellation_discount) / 100 }}
                            </span>
                        @else
                            ${{ number_format((float) $seatPrice, 2) }}
                        @endif

                        <small>
                            {{ $rideDetailPage->per_seat_label ?? 'per seat' }}
                        </small>
                        @if (isset($firm_cancellation_discount) && $firm_cancellation_discount != '' && $ride->isFirmCancellation())
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-info-circle-fill text-black" viewBox="0 0 16 16"
                                data-tippy-content="{!! nl2br($rideFeatureOptions['cancellation']['firm']->tooltip) ??
                                    'This ride has the Firm cancellation policy, so its booking price is reduced by 10%' !!}">
                                <path
                                    d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
                            </svg>
                        @endif
                    </div>
                    @if ($requestedBookingsCount > 0)
                        @once
                            <style>
                                @keyframes px-booking-request-zoom {
                                    0%, 100% { transform: scale(1); }
                                    50% { transform: scale(1.08); }
                                }
                                ._request_booking_alert{
                                    background-color: #ffcccf;
                                    border: 2px solid #e47780
                                }
                            </style>
                        @endonce
                        <span
                        {{-- uppercase tracking-[0.2em]  --}}
                            class="inline-flex items-center self-end rounded-full px-3 py-1 text-xl text-amber-700 rounded-lg _request_booking_alert"
                            style="animation: px-booking-request-zoom 1.15s ease-in-out infinite;"
                        >
                            {{ $requestedBookingsCount }} {{ $requestedBookingsCount === 1 ? 'booking request' : 'booking requests' }}
                        </span>
                    @endif
                </div>
            </div>
            @if($showRideBookingInfo)
                <div class="border-t border-gray-300 grid grid-cols-2 divide-x divide-gray-300">
                    <div class="flex items-baseline p-4">
                        <h4 class="font-medium text-xl xl:text-2xl text-left text-black font-FuturaMdCnBT">
                            @isset($rideDetailPage->seats_left_label)
                                {{ $rideDetailPage->seats_left_label }}:
                            @endisset                                
                        </h4>
                        <p class="text-xl text-primary font-normal ml-2">{{ intval($ride->seats) -intval($ride->bookings()->where('status', '<>', 3)->where('status', '<>', 4)->whereHas('passenger', function ($query) {$query->whereNull('deleted_at');})->sum('seats')) }}</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-3 p-4 items-baseline">
                        <h4 class="text-black text-xl xl:text-2xl">
                            {{ $rideDetailPage->booking_price_label ?? 'Booking Price' }}:
                        </h4>
                        <p class="text-lg text-primary font-normal">${{ number_format(floatval($ride->detail->price/100), 2) }}
                            @isset($rideDetailPage->per_seat_label)
                                {{ $rideDetailPage->per_seat_label }}
                            @endisset
                        </p>
                    </div>
                </div>
                <div class="border-t border-gray-300 grid sm:grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-gray-300">
                    <div class="p-4 items-baseline">
                        <div class="flex flex-wrap items-end gap-3">
                            @php
                                $payment_method = $ride->resolvePaymentMethodOption($rideFeatureOptions['payment_method'] ?? []);
                            @endphp
                            <h4 class="font-medium text-xl xl:text-2xl text-left text-black font-FuturaMdCnBT">
                                {{ $rideDetailPage->payment_method_label ?? 'Payment Method' }}:
                            </h4>
                            <p class="text-lg text-primary font-normal inline-block cursor-pointer" data-tippy-content="{{ optional($payment_method)->tooltip }}">
                                {{ optional($payment_method)->name }}
                            </p>
                        </div>
                    </div>

                    <div class="p-4 items-baseline">
                        <div class="flex flex-wrap items-end gap-3">
                            @php
                                $booking_method = $ride->resolveBookingMethodOption($rideFeatureOptions['booking_method'] ?? []);
                            @endphp
                            <h4 class="text-black text-xl xl:text-2xl font-FuturaMdCnBT">
                                {{ $rideDetailPage->booking_method_label ?? 'Booking Method' }}:
                            </h4>
                            <p class="text-lg text-primary font-normal inline-block cursor-pointer"
                                data-tippy-content="{{ optional($booking_method)->tooltip }}">
                                {{ optional($booking_method)->name }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="border-t border-gray-300 grid sm:grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-gray-300">
                <div class="p-4 flex items-center">
                    <h4 class="text-black text-xl xl:text-2xl font-FuturaMdCnBT">
                        @php
                            $bookedSeatsCount = $ride->getBookedSeats();
                        @endphp
                        {{ $rideDetailPage->booked_on_column_label ?? 'Booked' }}: 
                    </h4>
                    <p class="text-primary font-normal text-lg ml-2">
                        {{ $bookedSeatsCount }}
                        {{ $bookedSeatsCount == 1
                            ? ($rideDetailPage->seat_on_column_label ?? 'seat')
                            : ($rideDetailPage->ride_seat_label ?? 'seats') }}
                    </p>
                </div>
                <div class="p-4">
                    <div class="flex items-center justify-between">
                        <h4 class="text-black text-xl xl:text-2xl font-FuturaMdCnBT">
                            {{ $rideDetailPage->mobile_seat_fare_label ?? 'Fare' }}: </h4>
                        <p class="text-primary ">
                            ${{ number_format($ride->getMobileSeatFareTotal(), 2) }}
                        </p>
                    </div>
                    <div class="flex items-center justify-between">
                        <h4 class="text-black text-xl xl:text-2xl font-FuturaMdCnBT">
                            {{ $rideDetailPage->mobile_seat_booking_fee_label ?? 'Booking fee' }}: </h4>
                        <p class="text-primary ">
                            ${{ number_format($ride->getMobileSeatBookingFeeTotal(), 2) }}
                        </p>
                    </div>
                    <div class="flex items-center justify-between">
                        <h4 class="text-black text-xl xl:text-2xl font-FuturaMdCnBT">
                            {{ $rideDetailPage->mobile_seat_total_amount_label ?? 'Total amount' }}: </h4>
                        <p class="text-primary ">
                            ${{ number_format($ride->getMobileSeatTotalAmount(), 2) }}
                        </p>
                    </div>
                </div>
            </div>

            @if ($showOptions)
                <div class="border-t border-gray-300 p-3">
                    <div class="flex flex-wrap items-center gap-2">
                        @include('partials.ride_payment_icons', [ 'ride' => $ride, ])
                        @include('partials.ride_preference_icons', [ 'ride' => $ride ])
                        @include('partials.ride_feature_icons', [
                            'rideFeatures' => $ride->features,
                        ])
                    </div>
                </div>
            @endif

            @if (isset($slot) && !$slot->isEmpty())
                <div class="border-t border-gray-300 pt-0 p-3">
                    {{ $slot }}
                </div>
            @endif
        </div>

</div>
{!! $wrapperEnd !!}

<script>
    function goToRideDetail(href){
        location.href = href;
    }
</script>
