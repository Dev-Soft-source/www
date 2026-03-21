@props([
    'ride',
    'findRidePage' => null,
    'postRidePage' => null,
    'rideDetailPage' => null,
    'selectedLanguage' => null,
    'searchOptionGroups' => null,
])

@php
    $seatPrice = ($ride->detail->price ?? 0) / 100;
@endphp

<div class="col-span-2">
    <div class="bg-white rounded-lg shadow-3xl">
        <div class="flex flex-col p-4 pb-4 md:pb-0">
            <x-px.route-info
                :ride="$ride"
            />
        </div>

        <div class="border-t border-gray-300 grid grid-cols-2 divide-x divide-gray-300">
            <div class="flex flex-wrap items-end gap-3 p-4 items-baseline">
                <h4 class="text-black text-xl xl:text-2xl font-FuturaMdCnBT">
                    {{ $rideDetailPage->seats_left_label ?? 'Seats Left' }}:
                </h4>
                <p class="text-xl text-primary font-normal ml-2">
                    {{ $ride->getRemainingSeats() }}
                </p>
            </div>

            <div class="flex flex-wrap items-end gap-3 p-4 items-baseline">
                <h4 class="text-black text-xl xl:text-2xl font-FuturaMdCnBT">
                    {{ $rideDetailPage->booking_price_label ?? 'Booking Price' }}:
                </h4>
                <p class="text-lg font-normal text-left text-primary"
                    style="font-family: 'Roboto', sans-serif;">
                    ${{ number_format(floatval($seatPrice), 2) }}
                    @isset($rideDetailPage->per_seat_label)
                        {{ $rideDetailPage->per_seat_label }}
                    @endisset
                </p>
            </div>
        </div>
        <div class="border-t border-gray-300 grid sm:grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-gray-300">
            <div class="p-4 items-baseline">
                <div class="flex flex-wrap items-end gap-3">
                    <h4 class="font-medium text-xl xl:text-2xl text-left text-black font-FuturaMdCnBT">
                        {{ $rideDetailPage->payment_method_label ?? 'Payment Method' }}:
                    </h4>
                    @php
                        $payment_method = $ride->resolvePaymentMethodOption($rideFeatureOptions['payment_method'] ?? []);
                    @endphp
                    <p class="text-lg text-primary font-normal inline-block cursor-pointer"
                        data-tippy-content="{{ optional($payment_method)->tooltip }}">
                        {{ optional($payment_method)->name }}
                    </p>
                </div>
            </div>

            <div class="p-4 items-baseline">
                <div class="flex flex-wrap items-end gap-3">
                    <h4 class="text-black text-xl xl:text-2xl font-FuturaMdCnBT">
                        {{ $rideDetailPage->booking_method_label ?? 'Booking Method' }}:
                    </h4>
                    @php
                        $booking_method = $ride->resolveBookingMethodOption($rideFeatureOptions['booking_method'] ?? []);
                    @endphp
                    <p class="text-lg text-primary font-normal inline-block cursor-pointer"
                        data-tippy-content="{{ optional($booking_method)->tooltip }}">
                        {{ optional($booking_method)->name }}
                    </p>
                </div>
            </div>
        </div>


        <div class="border-t border-gray-300 flex flex-col md:flex-row md:items-center justify-start md:space-x-2 p-4">
            <h4 class="font-medium text-xl xl:text-2xl md:text-center text-black mr-4 font-FuturaMdCnBT">
                {{ $rideDetailPage->co_passenger_label ?? 'My Co-Passengers' }}:
            </h4>
            
            @php
                $user = auth()->user();

                $href = ($user && $ride->hasNonRejectedBookingForUser($user))
                    ? route('my_co_passengers', [
                        'lang' => app()->getLocale(),
                        'id' => $ride->id,
                    ])
                    : null;

                $bookings = $ride->bookings()->notRejected()->get();
            @endphp

            <div class="flex items-center no-scrollbar overflow-x-auto mt-2 md:mt-0">
                @if ($href)
                    <a class="flex" href="{{ $href }}">
                @else
                    <div class="flex items-center">
                @endif
                    @foreach ($bookings as $booking)
                        @php
                            $image = $booking->passenger?->profile_image ?? asset('images/59-booked-seat.png');
                        @endphp
                        @for ($i = 0; $i < $booking->seats; $i++)
                            <img class="w-10 h-10 rounded-full -ml-3 first:ml-0 hover:z-10 transition" src="{{ $image }}" alt="">
                        @endfor
                    @endforeach
                @if ($href)
                    </a>
                @else
                    </div>
                @endif

            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg overflow-hidden shadow-3xl mt-4">
        <h3 class="bg-primary text-white py-2 px-4 text-2xl xl:text-3xl">
            {{ $rideDetailPage->ride_features_label ?? 'Ride Preferences' }}
        </h3>
        <div class="bg-white p-4 space-y-3">
            @include('partials.ride_preference_items', [ 'ride' => $ride, ])
            @include('partials.ride_feature_items', ['features' => $ride->features])
        </div>
    </div>
</div>

