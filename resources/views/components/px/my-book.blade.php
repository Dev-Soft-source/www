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
<div class="{{ $wrapperClass }} {{ $classes }}" >
        <div class="rounded-lg shadow-3xl border-[3px] border-solid border-gray-100" onclick="goToRideDetail('{{ route($detailRoute, ['lang'=>app()->getLocale(),'id'=>$booking->ride->id]) }}')">
            <div class="flex flex-col sm:flex-col lg:flex-row  justify-between gap-4 p-4">
                <div class="route-info">
                    <x-px.route-info 
                        :ride="$booking->ride" 
                    />
                </div>
                <div class="booking-seat-info">
                    @php
                        $booking_status = null;
                        if ($booking->isCompleted()) {
                            $booking_status = [
                                'label' => $bookingDetailPage->booking_completed_label,
                                'class' => 'bg-green-100 text-green-600',
                            ];
                        } elseif ($booking->isCancelled()) {
                            $booking_status = [
                                'label' => $bookingDetailPage->booking_cancelled_label,
                                'class' => 'bg-red-100 text-red-600',
                            ];
                        }
                    @endphp
                    @if($booking_status)
                        <p class="w-fit px-2 py-1 rounded text-sm {{ $booking_status['class'] }}">
                            {{ $booking_status['label'] }}
                        </p>
                    @endif

                    <div class="flex items-center gap-2 text-primary justify-end">
                        <p class="text-xl font-semibold text-primary">
                            ${{ number_format(floatval($booking->price / 100), 2) }}
                            <small>
                                {{ $rideDetailPage->card_section_per_seat ?? 'per seat' }}
                            </small></p>
                    </div>
                </div>
            </div>
            @if($showRideBookingInfo)
                <div class="border-t border-gray-300 grid grid-cols-2 divide-x divide-gray-300">
                    <div class="p-4">
                        <p class="text-center">
                            {{ $booking->ride->getBookedSeats() }}
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
                    @include('partials.ride_payment_icons', [ 'ride' => $booking->ride, ])
                    @include('partials.ride_preference_icons', [ 'ride' => $booking->ride, ])
                    @include('partials.ride_feature_icons', [ 'rideFeatures' => $booking->ride->features ])
                </div>
            @endif

            <div class="p-4 border-t border-gray-300 grid sm:grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-gray-300">
                <div class="flex items-center space-x-2">
                    <div class="w-12 h-12 rounded-full overflow-hidden">
                        <img class="w-full h-full object-cover"
                            src="{{ $booking->ride->driver->profile_image }}"
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
                                {{ $rideDetailPage->card_section_age ?? 'Age' }}: {{ $booking->ride->driver->getAge() }}</p>
                            <p class="mb-0 text-sm font-medium border-r border-gray-600 pr-2">
                                {{ ucfirst($booking->ride->driver?->gender) }}
                            </p>
                            <p class="mb-0 text-sm font-medium border-r border-gray-600 pr-2">
                                {{ $rideDetailPage->card_section_driven ?? 'Passenger Driven' }}: {{ $booking->ride->driver->getPassengersDrivenCount() }}
                            </p>
                            <p class="mb-0 text-sm font-medium">
                                {{ $rideDetailPage->card_section_review ?? 'Review' }}: {{ number_format((float) ($booking->ride->getDriverAverageRating() ?? 0), 1) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

</div>
{!! $wrapperEnd !!}

<script>
    function goToRideDetail(href){
        location.href = href;
    }
</script>
