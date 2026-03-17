@extends('layouts.template')

@section('title', 'My PX Trips')

@section('content')
    <div class="grid grid-cols-12 gap-4 md:container md:mx-auto my-6 md:my-10 xl:my-14 px-4 xl:px-0">
        @include('layouts.inc.profile_sidebar')
        @php
            $currentRoute = Route::currentRouteName();
            $isPassengerSection = in_array($currentRoute, ['px.my_trips']);
            $isDriverSection = in_array($currentRoute, ['px.my_rides']);
            $activeTab = $activeTab ?? 'upcoming';
            $tabSelected = 'border-blue-600 border leading-normal text-white bg-blue-600';
            $tabUnselected = 'border-gray-100 border leading-normal text-blue-600 bg-white';
            $currencyMap = ['USD' => '$', 'CAD' => 'C$'];
        @endphp
        <div class="bg-white rounded pt-0 lg:px-4 w-full col-span-12 lg:col-span-9">
            <ul class="flex mb-0 list-none flex-wrap pb-4 flex-row">
                <li class="-mb-px mr-2 last:mr-0 flex-auto text-center">
                    <a href="{{ route('px.my_trips', ['lang' => optional($selectedLanguage)->abbreviation]) }}"
                        class="text-2xl font-FuturaMdCnBT px-5 py-2 shadow-lg rounded block {{ $isPassengerSection ? $tabSelected : $tabUnselected }} cursor-pointer">
                        {{ optional($tripsPage)->passenger_trips_heading ?? 'Passenger trips' }}
                    </a>
                </li>
                <li class="-mb-px mr-2 last:mr-0 flex-auto text-center">
                    <a href="{{ route('px.my_rides', ['lang' => optional($selectedLanguage)->abbreviation]) }}"
                        class="text-2xl font-FuturaMdCnBT px-5 py-2 shadow-lg rounded block {{ $isDriverSection ? $tabSelected : $tabUnselected }} cursor-pointer">
                        {{ optional($tripsPage)->driver_rides_heading ?? 'Driver rides' }}
                    </a>
                </li>
            </ul>

            <ul class="flex mb-0 list-none flex-wrap pb-4 flex-row">
                <li class="-mb-px mr-2 last:mr-0 flex-auto text-center">
                    <a href="{{ route('px.my_trips', ['lang' => optional($selectedLanguage)->abbreviation, 'tab' => 'upcoming']) }}"
                        class="text-lg font-FuturaMdCnBT font-medium px-5 py-2 shadow-lg rounded block {{ $activeTab === 'upcoming' ? $tabSelected : $tabUnselected }} cursor-pointer">
                        {{ optional($tripsPage)->upcoming_label ?? 'Upcoming' }}@if (($upcomingCount ?? 0) > 0)
                            ({{ $upcomingCount }})
                        @endif
                    </a>
                </li>
                <li class="-mb-px mr-2 last:mr-0 flex-auto text-center">
                    <a href="{{ route('px.my_trips', ['lang' => optional($selectedLanguage)->abbreviation, 'tab' => 'completed']) }}"
                        class="text-lg font-FuturaMdCnBT font-medium px-5 py-2 shadow-lg rounded block {{ $activeTab === 'completed' ? $tabSelected : $tabUnselected }} cursor-pointer">
                        {{ optional($tripsPage)->completed_label ?? 'Completed' }}@if (($completedCount ?? 0) > 0)
                            ({{ $completedCount }})
                        @endif
                    </a>
                </li>
                <li class="-mb-px mr-2 last:mr-0 flex-auto text-center">
                    <a href="{{ route('px.my_trips', ['lang' => optional($selectedLanguage)->abbreviation, 'tab' => 'cancelled']) }}"
                        class="text-lg font-FuturaMdCnBT font-medium px-5 py-2 shadow-lg rounded block {{ $activeTab === 'cancelled' ? $tabSelected : $tabUnselected }} cursor-pointer">
                        {{ optional($tripsPage)->cancelled_label ?? 'Cancelled' }}@if (($cancelledCount ?? 0) > 0)
                            ({{ $cancelledCount }})
                        @endif
                    </a>
                </li>
            </ul>

            <div class="relative flex flex-col min-w-0 break-words bg-white w-full py-5 shadow-lg rounded">
                <div class="px-4 flex-auto">
                    <div class="space-y-4">
                        @if (!empty($bookings) && $bookings->count() > 0)
                            @foreach ($bookings as $booking)
                                @if ($booking->ride)
                                    @php
                                        $ride = $booking->ride;
                                        $currencyCode = strtoupper(
                                            (string) ($booking->currency ?? ($ride->currency ?? 'USD')),
                                        );
                                        $currencySymbol = $currencyMap[$currencyCode] ?? $currencyCode . ' ';
                                    @endphp
                                    <x-px.ride-card :ride="$ride" :lang="optional($selectedLanguage)->abbreviation" :price-minor="$booking->segment_price_minor" :detail-query="[
                                        'from_stop_id' => (int) $booking->from_stop_id,
                                        'to_stop_id' => (int) $booking->to_stop_id,
                                    ]"
                                        detail-route="px.ride_detail">
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-2 text-sm">
                                            <p><strong>Booked section:</strong> {{ $booking->fromStop->label ?? 'N/A' }} ->
                                                {{ $booking->toStop->label ?? 'N/A' }}</p>
                                            <p><strong>Seats:</strong> {{ (int) $booking->seats }}</p>
                                            <p><strong>Total paid:</strong>
                                                {{ $currencySymbol }}{{ number_format(((int) $booking->total_price_minor) / 100, 2) }}
                                            </p>
                                            <p><strong>Status:</strong> {{ ucfirst((string) $booking->status) }}</p>
                                            <p><strong>Booked at:</strong>
                                                {{ optional($booking->booked_at)->format('F d, Y h:i A') ?? 'N/A' }}</p>
                                        </div>
                                    </x-px.ride-card>
                                @endif
                            @endforeach

                            <div class="mt-6">
                                {{ $bookings->links() }}
                            </div>
                        @else
                            <div class="text-center py-12">
                                @if ($activeTab === 'upcoming')
                                    <p class="text-gray-600 text-lg mb-4">You have no upcoming PX trips.</p>
                                    <a href="{{ route('search_ride', ['lang' => optional($selectedLanguage)->abbreviation]) }}"
                                        class="button-exp-fill inline-block">
                                        Search PX Rides
                                    </a>
                                @elseif ($activeTab === 'completed')
                                    <p class="text-gray-600 text-lg mb-4">You have no completed PX trips.</p>
                                @else
                                    <p class="text-gray-600 text-lg mb-4">You have no cancelled PX trips.</p>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
