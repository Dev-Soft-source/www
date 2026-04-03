@props([
    'ride',
    'booking',
    'detailRoute' => 'ride_detail',
    'wrapperClass' => 'relative even:bg-gray-200 odd:bg-white',
    'showStatus' => false,
    'showBookingInfo' => true,
    'showRideBookingInfo' => true,
    'showOptions' => true,
    'showKindBorder' => false,
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
<div class="{{ $wrapperClass }} {{ $classes }}">
    <div class="rounded-lg shadow-3xl border-[3px] border-solid border-gray-100"
        onclick="goToRideDetail('{{ route($detailRoute, ['lang' => app()->getLocale(), 'id' => $booking->ride_id, 'from_stop_id' => $booking->from_stop_id, 'to_stop_id' => $booking->to_stop_id]) }}')">
        <div class="flex flex-col sm:flex-col lg:flex-row  justify-between gap-4 p-4">
            <div class="route-info">
                <x-px.route-info :ride="$booking->ride" />
            </div>
            <div class="booking-seat-info flex flex-col items-end gap-2">
                @php
                    $booking_status = null;
                    $review_status = false;

                    if ($booking->isCompleted()) {
                        $booking_status = [
                            'label' => $tripsPage->completed_label,
                            'class' => 'bg-green-100 text-green-600',
                        ];
                    } elseif ($booking->isCancelled()) {
                        $booking_status = [
                            'label' => $tripsPage->cancelled_label,
                            'class' => 'bg-red-100 text-red-600',
                        ];
                    }
                @endphp
                @if ($booking_status)
                    <p class="w-fit px-2 py-1 rounded text-sm {{ $booking_status['class'] }}">
                        {{ $booking_status['label'] }}
                    </p>
                @endif

                <div class="flex items-center gap-2 text-primary justify-end">
                    @php
                        $seatPrice = $booking->price / 100;
                    @endphp
                    @if (isset($firm_cancellation_discount) && $firm_cancellation_discount != '' && $booking->ride->isFirmCancellation())
                        <span class="line-through">
                            ${{ number_format((float) $seatPrice, 2) }}
                        </span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="h-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
                        </svg>
                        <span>
                            ${{ number_format($seatPrice - ($seatPrice * $firm_cancellation_discount) / 100, 2) }}
                        </span>
                    @else
                        ${{ number_format((float) $seatPrice, 2) }}
                    @endif
                    <small>
                        {{ $rideDetailPage->card_section_per_seat ?? 'per seat' }}
                    </small>
                    @if (isset($firm_cancellation_discount) && $firm_cancellation_discount != '' && $booking->ride->isFirmCancellation())
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-info-circle-fill text-black" viewBox="0 0 16 16"
                            data-tippy-content="{!! nl2br($rideFeatureOptions['cancellation']['firm']->tooltip) ??
                                'This ride has the Firm cancellation policy, so its booking price is reduced by 10%' !!}">
                            <path
                                d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
                        </svg>
                    @endif
                </div>

                @php
                    $user = auth()->user();
                @endphp


                @if ($booking->isCompleted())
                    {{-- To leave review to driver --}}
                    @if (!$user->hasRatedToDriver($booking->id))
                        <div class="mt-4">
                            <a href="{{ route('review_driver', [
                                'lang' => app()->getLocale(),
                                'id' => $booking->uuid ?? 0,
                            ]) }}"
                                class="button-exp-fill me-1">
                                {{ $rideDetailPage->card_section_review ?? 'Review' }}
                            </a>
                        </div>
                    @endif
                @endif
            </div>
        </div>
        @if ($showRideBookingInfo)
            <div class="border-t border-gray-300 grid grid-cols-2 divide-x divide-gray-300">
                <div class="p-4">
                    <p class="text-center">
                        {{ $user->getPassengerSeatsCount($booking) }}
                        {{ $rideDetailPage->trips_card_section_seat_booked ?? 'Seat(s) Booked' }}
                    </p>
                </div>
                <div class="p-4">
                    <p class="text-center">
                        {{ $booking->ride->getRemainingSeats() }}
                        {{ $rideDetailPage->trips_card_section_seat_available ?? 'Seat(s) Available' }}
                    </p>
                </div>
            </div>
            <div class="border-t border-gray-300 flex flex-row p-4 gap-2">
                @include('partials.ride_payment_icons', ['ride' => $booking->ride])
                @include('partials.ride_preference_icons', ['ride' => $booking->ride])
                @include('partials.ride_feature_icons', ['rideFeatures' => $booking->ride->features])
            </div>
        @endif

        <div
            class="p-4 border-t border-gray-300 grid sm:grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-gray-300">
            <div class="flex items-center space-x-2">
                <div class="w-12 h-12 rounded-full overflow-hidden">
                    <img class="w-full h-full object-cover" src="{{ $booking->ride->driver->profile_image }}"
                        alt="">
                </div>
                <div class="text-center">
                    <p class="font-semibold">
                        <span>
                            {{ $booking->ride->driver->getDisplayName() }}
                        </span>
                    </p>

                    <div class="flex items-center gap-2 flex-wrap">
                        <p class="mb-0 text-sm font-medium border-r border-gray-600 pr-2">
                            {{ $rideDetailPage->card_section_age ?? 'Age' }}: {{ $booking->ride->driver->getAge() }}
                        </p>
                        <p class="mb-0 text-sm font-medium border-r border-gray-600 pr-2">
                            {{ ucfirst($booking->ride->driver?->gender) }}
                        </p>
                        <p class="mb-0 text-sm font-medium border-r border-gray-600 pr-2">
                            {{ $rideDetailPage->card_section_driven ?? 'Passenger Driven' }}:
                            {{ $booking->ride->driver->getPassengersDrivenCount() }}
                        </p>
                        <p class="mb-0 text-sm font-medium">
                            @if ($booking->ride->getDriverAverageRating())
                                {{ $rideDetailPage->review_label ?? 'Review' }}:
                                <span>{{ number_format($booking->ride->getDriverAverageRating(), 1) }}</span>
                            @else
                                {{ $rideDetailPage->no_reviews_label ?? 'No Reviews' }}
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
{!! $wrapperEnd !!}

<script>
    function goToRideDetail(href) {
        location.href = href;
    }
</script>
