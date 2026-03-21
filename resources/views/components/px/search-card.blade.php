@props([
    'ride',
    'wrapperClass' => 'relative even:bg-gray-200 odd:bg-white',
    'showStatus' => false,
    'showBookingInfo' => true,
    'showOptions' => true,
    'priceMinor' => null,
    'priceMajor' => null,
    'rightInfo' => null,
])

@php
    $seatPrice = ($ride->detail->price ?? 0) / 100;
@endphp

@php

    $classes = 'rounded-lg shadow-3xl border-[3px] border-solid';
    $wrapperStart = '';
    $wrapperEnd = '';

    if ($showKindBorder) {
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
    <div class="rounded-lg shadow-3xl border-[3px] border-solid border-gray-100">

        <div class="flex flex-col sm:flex-col lg:flex-row  justify-between gap-4 p-4">
            <div class="route-info">
                <x-px.route-info 
                    :ride="$ride" 
                    />
            </div>
            <div class="booking-info">
                <div class="pr-2">
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
                    <p class="text-primary text-right">
                        {{ $ride->getRemainingSeats() }}
                        {{ $rideDetailPage->seat_available_label ?? 'seats available' }}
                    </p>
                </div>
                @if ($showBookingButton)
                    <div class="my-4 min-w-64">
                        @if ($ride->isInstantBooking())
                            <a href="{{ route('ride_detail', ['lang' => app()->getLocale(), 'id' => $ride->id]) }}"
                                class="button-exp-green-fill flex justify-center w-full"
                                data-tippy-content="{{ $rideFeatureOptions['booking_method']['instant']->tooltip }}">
                                <img class="w-8 h-8 mr-2"
                                    src="{{ asset('home_page_icons/' . $rideFeatureOptions['booking_method']['instant']->icon) }}" />
                                {{ $rideDetailPage->instant_booking_btn_label ?? 'Instant booking' }}
                            </a>
                        @else
                            <a href="{{ route('ride_detail', ['lang' => app()->getLocale(), 'id' => $ride->id]) }}"
                                class="button-exp-sky-fill flex justify-center w-full"
                                data-tippy-content="{{ $rideFeatureOptions['booking_method']['manual']->tooltip }}">
                                <img class="w-8 h-8 mr-2"
                                    src="{{ asset('home_page_icons/' . $rideFeatureOptions['booking_method']['manual']->icon) }}" />
                                {{ $rideDetailPage->request_booing_btn_label ?? 'Request to book' }}
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        @if ($showDriverInfo)
            <div class="border-t border-gray-300">
                <div class="flex items-center justify-between p-4 w-full">
                    <div class="flex items-center space-x-2">
                        <div>
                            <p class="font-semibold">
                                {{ $ride->driver?->first_name }}
                            </p>
                            <p class="text-sm">
                                {{ $rideDetailPage->passenger_age_label }} {{ $ride->driver?->getAge() }}
                            </p>
                            <p class="text-sm">
                                {{ $ride->driver?->getCompletedPassengerBookingsCount() }}
                                {{ $rideDetailPage->passengers_driven_label }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="flex items-center justify-end">
                            <span class="font-semibold text-gray-800">
                                @if ($ride->getDriverHasRatings())
                                    {{ number_format($ride->getDriverAverageRating(), 1) }}
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                        class="w-6 h-6 text-yellow-500 stroke-gray-600">
                                        <path fill-rule="evenodd"
                                            d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                @else
                                    {{ $rideDetailPage->no_reviews_label ?? 'No Reviews' }}
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if ($showOptions && $ride->features)
            <div class="border-t border-gray-300 p-3">
                <div class="flex flex-wrap items-center gap-2">
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
