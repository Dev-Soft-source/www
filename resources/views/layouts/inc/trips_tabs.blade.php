@php
    $currentRoute = Route::currentRouteName();
    $isPassengerSection = in_array($currentRoute, ['my_trips', 'past_trips', 'cancelled_trips']);
    $isDriverSection = in_array($currentRoute, ['my_rides', 'past_rides', 'cancelled_rides']);
    $isUpcoming = in_array($currentRoute, ['my_rides', 'my_trips']);
    $isCompleted = in_array($currentRoute, ['past_rides', 'past_trips']);
    $isCancelled = in_array($currentRoute, ['cancelled_rides', 'cancelled_trips']);
    $tabSelected = 'border-blue-600 border leading-normal text-white bg-blue-600';
    $tabUnselected = 'border-gray-100 border leading-normal text-blue-600 bg-white';
@endphp
<ul class="flex mb-0 list-none flex-wrap pb-4 flex-row">
    <li class="-mb-px mr-2 last:mr-0 flex-auto text-center">
        <a href="{{ route('my_trips', ['lang' => $selectedLanguage->abbreviation]) }}" class="text-2xl font-FuturaMdCnBT px-5 py-2 shadow-lg rounded block {{ $isPassengerSection ? $tabSelected : $tabUnselected }} cursor-pointer">
            {{ optional($tripsPage)->passenger_trips_heading ?? 'Passenger trips' }}
        </a>
    </li>
    <li class="-mb-px mr-2 last:mr-0 flex-auto text-center">
        <a href="{{ route('my_rides', ['lang' => $selectedLanguage->abbreviation]) }}" class="text-2xl font-FuturaMdCnBT px-5 py-2 shadow-lg rounded block {{ $isDriverSection ? $tabSelected : $tabUnselected }} cursor-pointer">
            {{ optional($tripsPage)->driver_rides_heading ?? 'Driver rides' }}
        </a>
    </li>
</ul>
<ul class="flex mb-0 list-none flex-wrap pt-3 pb-4 flex-row">
    <li class="-mb-px mr-2 last:mr-0 flex-auto text-center">
        <a href="{{ $isPassengerSection ? route('my_trips', ['lang' => $selectedLanguage->abbreviation]) : route('my_rides', ['lang' => $selectedLanguage->abbreviation]) }}" class="text-lg font-FuturaMdCnBT font-medium px-5 py-2 shadow-lg rounded block {{ $isUpcoming ? $tabSelected : $tabUnselected }} cursor-pointer">
            {{ optional($tripsPage)->upcoming_label ?? 'Upcoming' }}
        </a>
    </li>
    <li class="-mb-px mr-2 last:mr-0 flex-auto text-center">
        <a href="{{ $isPassengerSection ? route('past_trips', ['lang' => $selectedLanguage->abbreviation]) : route('past_rides', ['lang' => $selectedLanguage->abbreviation]) }}" class="text-lg font-FuturaMdCnBT font-medium px-5 py-2 shadow-lg rounded block {{ $isCompleted ? $tabSelected : $tabUnselected }} cursor-pointer">
            {{ optional($tripsPage)->completed_label ?? 'Completed' }}
        </a>
    </li>
    <li class="-mb-px mr-2 last:mr-0 flex-auto text-center">
        <a href="{{ $isPassengerSection ? route('cancelled_trips', ['lang' => $selectedLanguage->abbreviation]) : route('cancelled_rides', ['lang' => $selectedLanguage->abbreviation]) }}" class="text-lg font-FuturaMdCnBT font-medium px-5 py-2 shadow-lg rounded block {{ $isCancelled ? $tabSelected : $tabUnselected }} cursor-pointer">
            {{ optional($tripsPage)->cancelled_label ?? 'Cancelled' }}
        </a>
    </li>
</ul>
