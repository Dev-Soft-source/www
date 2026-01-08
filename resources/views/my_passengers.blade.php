@extends('layouts.template')

@section('content')

<div class="container mx-auto my-10 xl:my-14 px-4 xl:px-0">
    <div class="flex justify-between">
        <h1>{{$myPassengerPage->main_heading ?? "My passengers"}}</h1>
        <button onclick="history.back()" class="button-exp-fill me-1 mb-4">{{$myPassengerPage->web_back_button_label ?? "Back"}}</button>
    </div>
    <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-4">
        @foreach ($ride->bookings->where('status', 1) as $booking)
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
                                    <p class="text-white leading-4 mt-2 mr-4 text-base">{{$myPassengerPage->age ?? "Age"}}: <span>{{ $age }}</span></p>
                                    <p class="text-white leading-4 mt-2 ml-4 text-base">{{$myPassengerPage->gender ?? "Gender"}}: <span>
                                        @if($booking->passenger->gender=='male')
                                        M
                                        @elseif($booking->passenger->gender=='female')
                                        F
                                        @else
                                        {{ $booking->passenger->gender }}
                                        @endif
                                    </span></p>
                                </div>
                            </div>
                            <a href="{{ route('profile_info', ['lang' => $selectedLanguage->abbreviation, 'id' => $booking->passenger->id]) }}" class="text-white underline">
                                {{$myPassengerPage->review_profile_label ?? "View profile"}}
                            </a>
                        </div>
                    </div>
                    <div class="space-y-4 p-4">
                        <div class="flex justify-between items-center space-x-2 w-full border-b">
                            <p>{{$myPassengerPage->seat_booked_label ?? "Seat booked"}}</p>
                            <p>{{ $booking->seats }}</p>
                        </div>
                        <div class="flex justify-between items-center space-x-2 w-full border-b">
                            <p>{{$myPassengerPage->my_fare_label ?? "My fare"}}</p>
                            <p>${{ $booking->seats*$booking->price }}</p>
                        </div>
                        <div class="flex justify-between items-center space-x-2 w-full border-b">
                            <p>{{$myPassengerPage->booking_fee_label ?? "Booking fee"}}</p>
                            <p>${{ number_format($booking->booking_credit, 2) }}</p>
                        </div>
                        <div class="flex justify-between items-center space-x-2 w-full border-b">
                            <p>{{$myPassengerPage->total_amount_label ?? "My Total"}}</p>
                            <p>${{ ($booking->seats*$booking->price) + $booking->booking_credit + $booking->tax_amount}}</p>
                        </div>
                        <div class="flex items-center justify-between pt-4">
                            @if(strtotime($ride->date) > strtotime('today') || (strtotime($ride->date) == strtotime('today') && strtotime($ride->time) > strtotime('now')))
                                <div>
                                    <a href="{{ route('chat', ['lang' => $selectedLanguage->abbreviation,'departure' => $ride->rideDetail[0]->departure,'destination' => $ride->rideDetail[0]->destination,'id' => $ride->id,'passenger' => $booking->user_id]) }}" class="button-exp-fill me-1">
                                        {{$myPassengerPage->chat_passenger_btn_label ?? "Chat with passenger"}}
                                    </a>
                                </div>
                                <div>
                                    <a href="{{ route('passenger.cancel', ['lang' => $selectedLanguage->abbreviation, 'id' => $booking->id]) }}" class="button-exp-fill me-1 bg-red-500 hover:bg-red-500 border-red-500 focus:ring-0 focus:outline-none hover:border-red-500">
                                        {{$myPassengerPage->remove_ride_btn_label ?? "Remove from this ride"}}
                                    </a>
                                </div>
                                
                                {{-- @php
                                    if ($cancelSetting) {
                                        // Calculate the cancellation deadline
                                        $cancellationDeadline = strtotime('+' . $cancelSetting->driver_cancel_hours . ' hours', strtotime($booking->booked_on));
                                    }
                                @endphp
                                @if(isset($cancelSetting) && $cancelSetting && isset($cancellationDeadline))
                                    @if(strtotime('now') < $cancellationDeadline)
                                        
                                    @endif
                                @endif --}}
                            @endif
                            @if (auth()->user())
                                @php
                                    $userBookingId = $booking->ride->bookings()
                                        ->where('user_id', auth()->user()->id)
                                        ->where('status', '<>', 3)
                                        ->where('status', '<>', 4)
                                        ->whereHas('passenger', function($query) {
                                            $query->whereNull('deleted_at');
                                        })
                                        ->pluck('id')->first();
                                    $exist = \App\Models\NoShowHistory::where('ride_id', $booking->ride_id)->where('booking_id', $booking->id)
                                    ->where('user_id', $booking->user_id)->where('type', 'passenger')->first();
                                    // dd($exist);
                                @endphp
                            @endif
                            @if (strtotime($ride->date) < strtotime('today') || (strtotime($ride->date) == strtotime('today') && strtotime($ride->time) < strtotime('now')))
                                @if (strtotime($ride->completed_date) > strtotime('today') || (strtotime($ride->completed_date) == strtotime('today') && strtotime($ride->completed_time) > strtotime('now')))
                                @if(!isset($exist))   
                                <div>
                                        <a href="javascript:void(0)" id="noShowDriverButton" data-booking-id="{{ $booking->id }}" class="button-exp-fill me-1">
                                            {{--  Review your driver  --}}
                                            {{$myPassengerPage->no_show_passenger_label ?? "No show passenger"}}
                                        </a>
                                    </div>
                                    @else
                                    <div>
                                        <a href="javascript:void(0)" id="revertNoShowDriverButton" data-booking-id="{{ $booking->id }}" class="button-exp-fill me-1">
                                            {{--  Review your driver  --}}
                                            {{$myPassengerPage->revert_no_show_passenger_label ?? "Revert"}}
                                        </a>
                                    </div>
                                @endif
                                @endif
                            @endif
                            @if(strtotime($ride->date) < strtotime('today') || (strtotime($ride->date) == strtotime('today') && strtotime($ride->time) < strtotime('now')))
                                @php
                                    // Calculate the difference in days between today and the ride's date
                                    $rideDateTime = new DateTime($ride->date . ' ' . $ride->time);
                                    // Add the leave review days to the ride's DateTime
                                    $reviewDateTime = clone $rideDateTime;
                                    $reviewDateTime->add(new DateInterval('P' . $setting->leave_review_days . 'D'));

                                    // Get current DateTime
                                    $now = new DateTime();

                                    // Check if the current DateTime is before the review DateTime
                                    $reviewButtonVisible = $now < $reviewDateTime;
                                @endphp
                                @php
                                    $reviewed = false; // Flag to track if any rating meets the conditions
                                @endphp
                                <!-- Loop through ratings associated with this booking -->
                                @foreach($booking->ratings as $rating)
                                    @if ($rating->posted_by === $ride->driver?->id && $rating->type === '2' && $rating->ride_id === $ride->id)
                                        @php
                                            $reviewed = true; // Set the flag to true if a matching rating is found
                                            $review = $rating
                                        @endphp
                                        <!-- If at least one matching rating is found, break out of the loop -->
                                        @break
                                    @endif
                                @endforeach

                                <!-- Display button based on the flag value -->
                                @if ($reviewed)
                                    @php
                                        // Format average rating with one decimal place
                                        $formattedAverageRating = $review->average_rating ?? 0;
                                    @endphp
                                    <td class="border border-slate-300 px-4 py-2 text-center">
                                        <div class="flex">
                                            <p class="mr-1">{{$myPassengerPage->web_i_reviewed_label ?? "I Reviewed"}}</p>
                                            <img src="{{ asset('assets/11-review-full-star.png') }}" class="w-4 h-4 mt-1" alt="">
                                            <p class="ml-1">{{ $formattedAverageRating }}</p>
                                        </div>
                                    </td>
                                @elseif ($reviewButtonVisible)
                                    <!-- Show 'Review' button if no matching rating is found -->
                                    @isset($booking->uuid)
                                        <td class="border border-slate-300 px-4 py-2 text-center">
                                            <a href="{{ route('review_passenger', ['lang' => $selectedLanguage->abbreviation, 'id' => $booking->uuid]) }}" class="button-exp-fill me-1">
                                                {{$myPassengerPage->web_reviewd_label ?? "Review"}}
                                            </a>
                                        </td>
                                    @endisset
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>
</div>

<div id="bookingModal" class="hidden fixed z-50 inset-0 overflow-y-auto">
    <div class="relative z-50">
        <div id="close-modal" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                <div
                    class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg w-full">
                    <button type="button" id="close-modal" class="absolute top-3 right-3 text-gray-400 hover:text-gray-500">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start justify-center">
                            <!-- <div
                                class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full sm:mx-0 sm:h-10 sm:w-10 bg-blue-500">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-lg text-white w-8 h-8" viewBox="0 0 16 16">
                                    <path d="M7.005 3.1a1 1 0 1 1 1.99 0l-.388 6.35a.61.61 0 0 1-1.214 0zM7 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0"/>
                                </svg>
                            </div> -->
                        </div>
                        <div class="text-center sm:ml-4 sm:mt-0 sm:text-left">
                            <div class="">
                                <h3 class="text-3xl text-center font-FuturaMdCnBT font-medium text-gray-900 mb-4" id="modal-title">{!! session('heading') !!}</h3>
                            </div>
                            <div class="mt-2 w-full">
                                <p class="can-exp-p text-center"></p>
                            </div>
                        </div>
                    </div>
                    <div class="px-4 pb-6 pt-4 sm:flex sm:flex-row-reverse sm:px-6 justify-center">
                        <button type="button" id="close-modal" class="inline-flex w-full justify-center rounded bg-blue-500 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-blue-400 sm:ml-3 sm:w-24">
                            {{$messages->popup_close_btn_text ?? "Close"}}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="revertModal" class="hidden fixed z-50 inset-0 overflow-y-auto">
    <div class="relative z-50">
        <div id="close-revert-modal" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                <div
                    class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg w-full">
                    <button type="button" id="close-revert-modal" class="absolute top-3 right-3 text-gray-400 hover:text-gray-500">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start justify-center">
                            <!-- <div
                                class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full sm:mx-0 sm:h-10 sm:w-10 bg-blue-500">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-lg text-white w-8 h-8" viewBox="0 0 16 16">
                                    <path d="M7.005 3.1a1 1 0 1 1 1.99 0l-.388 6.35a.61.61 0 0 1-1.214 0zM7 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0"/>
                                </svg>
                            </div> -->
                        </div>
                        <div class="text-center sm:ml-4 sm:mt-0 sm:text-left">
                            <div class="mt-2">
                                <p class="can-exp-p">{{ $messages->cancel_noshow_are_you_sure??'f' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="px-4 pb-6 pt-4 sm:flex sm:flex-row-reverse sm:px-6 justify-center">
                        <button type="button" id="take-me-back-modal" class="inline-flex w-full justify-center rounded bg-blue-500 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-blue-400 sm:ml-3 sm:w-24">
                            {{$messages->cancel_noshow_take_me_back ?? "No take me back"}}
                        </button>
                        <button type="button" id="close-revert-modal" class="inline-flex w-full justify-center rounded bg-blue-500 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-blue-400 sm:ml-3 sm:w-24">
                            {{$messages->confirm_cancel_noshow ?? "Yes"}}
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
    document.addEventListener('DOMContentLoaded', function () {
        // Attach click event to all buttons with ID 'noShowDriverButton'
        document.querySelectorAll('#noShowDriverButton').forEach(button => {
            button.addEventListener('click', function () {
                // Get the booking ID from the data attribute
                const booking_id = this.getAttribute('data-booking-id');
                console.log('Booking ID:', booking_id);

                $.ajax({
                    url: '{{ route("no_show_passenger") }}', // Laravel route for the no_show_driver
                    type: 'POST',
                    data: {
                        booking_id: booking_id,
                        _token: '{{ csrf_token() }}' // CSRF token for security
                    },
                    success: function(response) {
                        console.log('Seats on hold:', response);
                        
                        // Update the modal content with the response message
                        const modalMessageElement = document.querySelector('#bookingModal .text-sm.text-gray-500');
                        if (modalMessageElement) {
                            modalMessageElement.textContent = response.message; // Assuming 'message' is part of the response
                        }
                        const modal = document.getElementById('bookingModal');
                        modal.classList.remove('hidden');
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                        // Handle error response
                    }
                });
            });
        });
    });


document.addEventListener('DOMContentLoaded', function () {
    let selectedBookingId = null;

    // Step 1: Listen for clicks on the revert button
    document.querySelectorAll('#revertNoShowDriverButton').forEach(button => {
        button.addEventListener('click', function () {
            selectedBookingId = this.getAttribute('data-booking-id');
            console.log('Selected Booking ID:', selectedBookingId);

            // Show the modal
            const modal = document.getElementById('revertModal');
            modal.classList.remove('hidden');
        });
    });

    // Step 2: Handle the click on the "Yes" button in the modal
    const confirmButton = document.getElementById('close-revert-modal');
    confirmButton.addEventListener('click', function () {
        if (!selectedBookingId) return;

        $.ajax({
            url: '{{ route("revert_no_show_passenger") }}',
            type: 'POST',
            data: {
                booking_id: selectedBookingId,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                console.log('Seats on hold:', response);

                // Update the modal message
                const modalMessageElement = document.querySelector('#bookingModal .text-sm.text-gray-500');
                if (modalMessageElement) {
                    modalMessageElement.textContent = response.message || 'Action completed.';
                }

                // Optionally hide the modal
                const modal = document.getElementById('revertModal');
                modal.classList.add('hidden');
                window.location.reload();

                // Clear stored ID
                selectedBookingId = null;
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    });
});

    document.getElementById('close-modal').addEventListener('click', function () {
        const modal = document.getElementById('bookingModal');
        window.location.reload();
        modal.classList.add('hidden');
    });
    
    document.getElementById('take-me-back-modal').addEventListener('click', function () {
        const modal = document.getElementById('revertModal');
        modal.classList.add('hidden');
    });
</script>

@endsection