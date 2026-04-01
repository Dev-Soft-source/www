@extends('layouts.template')

@section('content')

<div class="container mx-auto my-14 ">
    <h1>{{ $rideDetailPage->co_passenger_label ?? 'My Co-Passengers' }}</h1>
    <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-4">
        @foreach ($ride->bookings->whereNotIn('status', [3, 4]) as $booking)
            @if ($booking->passenger)
                <div class="bg-white rounded-lg overflow-hidden shadow-3xl">
                    <div class="bg-primary text-white py-2 px-4">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3>{{ $booking->passenger->first_name }}</h3>
                                @php
                                    // Calculate the age based on the driver's date of birth
                                    $dob = \Carbon\Carbon::parse($booking->passenger->dob);
                                    $age = $dob->diffInYears(\Carbon\Carbon::now());
                                @endphp
                                <div class="flex">
                                    <p class="text-white leading-4 mt-2 mr-4 text-base">{{ $myPassengerPage->age ?? 'Age' }}: <span>{{ $age }}</span></p>
                                    <p class="text-white leading-4 mt-2 ml-4 text-base">{{ $myPassengerPage->gender ?? 'Gender' }}: <span>{{ ucfirst($booking->passenger->gender) }}</span></p>
                                </div>
                            </div>
                            <a href="{{ route('profile_info', ['lang' => $selectedLanguage->abbreviation, 'id' => $booking->passenger->id]) }}" class="text-white underline">
                                {{ $myPassengerPage->review_profile_label ?? 'View Profile' }}
                            </a>
                        </div>
                    </div>
                    <div class="space-y-4 p-4">
                        <div class="flex justify-between items-center space-x-2 w-full border-b">
                            <p>{{ $myPassengerPage->seat_booked_label ?? 'Seats booked' }}</p>
                            <p>{{ $booking->seats }}</p>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>
</div>

@endsection