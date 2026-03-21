@extends('layouts.template')

@section('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endsection

@section('content')
    @if (session('success'))
        <div id="myModal" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div onclick="closeModal()" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center:p-0">
                    <div
                        class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
                        <button type="button" onclick="closeModal()"
                            class="absolute top-3 right-3 text-gray-400 hover:text-gray-500">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start justify-center">
                                <!-- <div
                                class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full sm:mx-0 sm:h-10 sm:w-10 bg-green-500">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-lg text-white w-8 h-8" viewBox="0 0 16 16">
                                    <path d="M7.005 3.1a1 1 0 1 1 1.99 0l-.388 6.35a.61.61 0 0 1-1.214 0zM7 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0"/>
                                </svg>
                            </div> -->
                            </div>
                            <div class="text-center sm:ml-4 sm:mt-0 w-full">
                                <div class="mt-2">
                                    <h3 class="text-3xl text-center font-FuturaMdCnBT font-medium text-gray-900 mb-4"
                                        id="modal-title">{!! session('heading') !!}</h3>
                                </div>
                                <div class="mt-2 w-full">
                                    <p class="can-exp-p text-center">{!! session('success') !!}</p>
                                </div>
                            </div>
                        </div>
                        <div class="px-4 pb-6 pt-4 flex items-center space-x-2 sm:space-x-4 sm:px-6 justify-center">
                            @if (session('id'))
                                <a href="{{ route('repost_ride', ['lang' => $selectedLanguage->abbreviation, 'id' => session('id')]) }}"
                                    class="inline-flex w-full justify-center rounded bg-greenXS px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white whitespace-nowrap hover:text-white hover:shadow-lg shadow-sm hover:bg-greenXS sm:ml-3 sm:w-fit">Repost
                                    ride</a>
                            @endif
                            <a href="" class="button-exp-fill">{{ $siteText['close_btn_text'] ?? 'Close' }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <div class="grid grid-cols-12 gap-4 md:container md:mx-auto  my-6 md:my-10 xl:my-14 px-4 xl:px-0">
        @include('layouts.inc.profile_sidebar')

        @php
            $currentRoute = Route::currentRouteName();
            $isPassengerSection = in_array($currentRoute, ['my_trips']);
            $isDriverSection = in_array($currentRoute, ['my_rides']);
            $activeTab = $activeTab ?? 'upcoming';
            $tabSelected = 'border-blue-600 border leading-normal text-white bg-blue-600';
            $tabUnselected = 'border-gray-100 border leading-normal text-blue-600 bg-white';
        @endphp

        <div class="bg-white rounded pt-0 lg:px-4 w-full col-span-12 lg:col-span-9">
            <ul class="flex mb-0 list-none flex-wrap pb-4 flex-row">
                <li class="-mb-px mr-2 last:mr-0 flex-auto text-center">
                    <a href="{{ route('my_trips', ['lang' => optional($selectedLanguage)->abbreviation]) }}"
                        class="text-2xl font-FuturaMdCnBT px-5 py-2 shadow-lg rounded block {{ $isPassengerSection ? $tabSelected : $tabUnselected }} cursor-pointer">
                        {{ optional($tripsPage)->passenger_trips_heading ?? 'Passenger trips' }}
                    </a>
                </li>
                <li class="-mb-px mr-2 last:mr-0 flex-auto text-center">
                    <a href="{{ route('my_rides', ['lang' => optional($selectedLanguage)->abbreviation]) }}"
                        class="text-2xl font-FuturaMdCnBT px-5 py-2 shadow-lg rounded block {{ $isDriverSection ? $tabSelected : $tabUnselected }} cursor-pointer">
                        {{ optional($tripsPage)->driver_rides_heading ?? 'Driver rides' }}
                    </a>
                </li>
            </ul>

            
            <div class="flex flex-wrap" id="tabs-id">
                <div class="w-full">
                <ul class="flex mb-0 list-none flex-wrap pb-4 flex-row">
                    <li class="-mb-px mr-2 last:mr-0 flex-auto text-center">
                        <a href="{{ route('my_trips', ['lang' => optional($selectedLanguage)->abbreviation, 'tab' => 'upcoming']) }}"
                            class="text-lg font-FuturaMdCnBT font-medium px-5 py-2 shadow-lg rounded block {{ $activeTab === 'upcoming' ? $tabSelected : $tabUnselected }} cursor-pointer">
                            {{ optional($tripsPage)->upcoming_label ?? 'Upcoming' }}@if (($upcomingCount ?? 0) > 0)
                                ({{ $upcomingCount }})
                            @endif
                        </a>
                    </li>
                    <li class="-mb-px mr-2 last:mr-0 flex-auto text-center">
                        <a href="{{ route('my_trips', ['lang' => optional($selectedLanguage)->abbreviation, 'tab' => 'completed']) }}"
                            class="text-lg font-FuturaMdCnBT font-medium px-5 py-2 shadow-lg rounded block {{ $activeTab === 'completed' ? $tabSelected : $tabUnselected }} cursor-pointer">
                            {{ optional($tripsPage)->completed_label ?? 'Completed' }}@if (($completedCount ?? 0) > 0)
                                ({{ $completedCount }})
                            @endif
                        </a>
                    </li>
                    <li class="-mb-px mr-2 last:mr-0 flex-auto text-center">
                        <a href="{{ route('my_trips', ['lang' => optional($selectedLanguage)->abbreviation, 'tab' => 'cancelled']) }}"
                            class="text-lg font-FuturaMdCnBT font-medium px-5 py-2 shadow-lg rounded block {{ $activeTab === 'cancelled' ? $tabSelected : $tabUnselected }} cursor-pointer">
                            {{ optional($tripsPage)->cancelled_label ?? 'Cancelled' }}@if (($cancelledCount ?? 0) > 0)
                                ({{ $cancelledCount }})
                            @endif
                        </a>
                    </li>
                </ul>
                    <div class="relative flex flex-col min-w-0 break-words bg-white w-full py-5 shadow-lg rounded">
                        <div class="">
                            <div class="px-4 flex-auto">
                                <div class="tab-content tab-space">
                                    <div class="block" id="tab-profile">
                                        <div class="space-y-4">
                                            @if (!empty($bookings) && count($bookings) > 0)
                                                @foreach ($bookings as $booking)
                                                    <div class="relative even:bg-gray-100 odd:bg-white">
                                                        <x-px.my-book 
                                                            :booking="$booking" 
                                                            :detail-route="'ride_detail'" 
                                                            :show-kind-border="false" 
                                                            />
                                                    </div>
                                                @endforeach
                                                {{ $bookings->links() }}
                                            @else
                                                @if ($activeTab === 'upcoming')
                                                    <p>{{ $tripsPage->no_upcoming_trips_label ?? 'You have no upcoming trips scheduled.' }}</p>
                                                @elseif ($activeTab === 'completed')
                                                    <p>{{ $tripsPage->no_completed_trips_label ?? 'You have no completed trips' }}</p>
                                                @elseif ($activeTab === 'cancelled')
                                                    <p>{{ $tripsPage->no_cancelled_trips_label ?? 'You have no cancelled trips' }}</p>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="bookingModal" class="hidden fixed z-50 inset-0 overflow-y-auto">
        <div class="relative z-50">
            <div id="close-modal" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full iteitems-end justify-center p-4 text-center sm:items-center w-full">
                    <div
                        class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-xl w-full">
                        <button type="button" id="close-modal"
                            class="absolute top-3 right-3 text-gray-400 hover:text-gray-500">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
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
                                    <h3 class="text-3xl text-center font-FuturaMdCnBT font-medium text-gray-900 mb-4 modal-message mt-3"
                                        id="modal-title">{!! session('heading') !!}</h3>
                                </div>
                                <div class="mt-2 w-full">
                                    <p class="text-center can-exp-p"></p>
                                </div>
                            </div>
                        </div>
                        <div class="px-4 pb-6 pt-4  sm:flex sm:flex-row-reverse sm:px-6 justify-center">
                            <button type="button" id="close-modal"
                                class="inline-flex w-full justify-center rounded bg-blue-500 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-blue-400 sm:ml-3 sm:w-24">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="revertModal" class="hidden fixed z-50 inset-0 overflow-y-auto">
        <div class="relative z-50">
            <div id="take-me-back-modal" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center">
                    <div
                        class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg w-full">
                        <button type="button" id="take-me-back-modal"
                            class="absolute top-3 right-3 text-gray-400 hover:text-gray-500">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
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
                                    <h3 class="text-3xl text-center font-FuturaMdCnBT font-medium text-gray-900 mb-4"
                                        id="modal-title">{{ $successMessage->cancel_noshow_are_you_sure ?? 'are you sure' }}
                                    </h3>
                                </div>
                                <div class="mt-2">
                                    <p class="can-exp-p"></p>
                                </div>
                            </div>
                        </div>
                        <div class="px-4 pb-6 pt-4 sm:flex sm:flex-row-reverse sm:px-6 justify-center">
                            <button type="button" id="take-me-back-modal"
                                class="inline-flex w-full justify-center rounded bg-blue-500 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-blue-400 sm:ml-3 sm:w-24">
                                {{ $successMessage->cancel_noshow_take_me_back ?? 'No take me back' }}
                            </button>
                            <button type="button" id="close-revert-modal"
                                class="inline-flex w-full justify-center rounded bg-blue-500 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-blue-400 sm:ml-3 sm:w-24">
                                {{ $successMessage->confirm_cancel_noshow ?? 'Yes' }}
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
        document.addEventListener('DOMContentLoaded', function() {
            // Attach click event to all buttons with ID 'noShowDriverButton'
            document.querySelectorAll('#noShowDriverButton').forEach(button => {
                button.addEventListener('click', function() {
                    // Get the booking ID from the data attribute
                    const booking_id = this.getAttribute('data-booking-id');
                    console.log('Booking ID:', booking_id);

                    $.ajax({
                        url: '{{ route('no_show_driver') }}', // Laravel route for the no_show_driver
                        type: 'POST',
                        data: {
                            booking_id: booking_id,
                            _token: '{{ csrf_token() }}' // CSRF token for security
                        },
                        success: function(response) {
                            console.log('Seats on hold:', response);

                            // Update the modal content with the response message
                            const modalMessageElement = document.querySelector(
                                '.modal-message');
                            if (modalMessageElement) {
                                modalMessageElement.textContent = response
                                .message; // Assuming 'message' is part of the response
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

        // document.addEventListener('DOMContentLoaded', function () {
        //     // Attach click event to all buttons with ID 'noShowDriverButton'
        //     document.querySelectorAll('#revertNoShowDriverButton').forEach(button => {
        //         button.addEventListener('click', function () {
        //             // Get the booking ID from the data attribute
        //             const booking_id = this.getAttribute('data-booking-id');
        //             console.log('Booking ID:', booking_id);

        //             $.ajax({
        //                 url: '{{ route('revert_no_show_driver') }}', // Laravel route for the no_show_driver
        //                 type: 'POST',
        //                 data: {
        //                     booking_id: booking_id,
        //                     _token: '{{ csrf_token() }}' // CSRF token for security
        //                 },
        //                 success: function(response) {
        //                     console.log('Seats on hold:', response);

        //                     // Update the modal content with the response message
        //                     const modalMessageElement = document.querySelector('#bookingModal .text-sm.text-gray-500');
        //                     if (modalMessageElement) {
        //                         modalMessageElement.textContent = response.message; // Assuming 'message' is part of the response
        //                     }
        //                     const modal = document.getElementById('bookingModal');
        //                     modal.classList.remove('hidden');
        //                 },
        //                 error: function(xhr, status, error) {
        //                     console.error('Error:', error);
        //                     // Handle error response
        //                 }
        //             });
        //         });
        //     });
        // });
        document.addEventListener('DOMContentLoaded', function() {
            let selectedBookingId = null;

            // Step 1: Listen for clicks on the revert button
            document.querySelectorAll('#revertNoShowDriverButton').forEach(button => {
                button.addEventListener('click', function() {
                    selectedBookingId = this.getAttribute('data-booking-id');
                    console.log('Selected Booking ID:', selectedBookingId);

                    // Show the modal
                    const modal = document.getElementById('revertModal');
                    modal.classList.remove('hidden');
                });
            });

            // Step 2: Handle the click on the "Yes" button in the modal
            const confirmButton = document.getElementById('close-revert-modal');
            confirmButton.addEventListener('click', function() {
                if (!selectedBookingId) return;

                $.ajax({
                    url: '{{ route('revert_no_show_driver') }}',
                    type: 'POST',
                    data: {
                        booking_id: selectedBookingId,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        console.log('Seats on hold:', response);

                        // Update the modal message
                        const modalMessageElement = document.querySelector(
                            '#bookingModal .text-sm.text-gray-500');
                        if (modalMessageElement) {
                            modalMessageElement.textContent = response.message ||
                                'Action completed.';
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
        document.getElementById('close-modal').addEventListener('click', function() {
            const modal = document.getElementById('bookingModal');
            window.location.reload();
            modal.classList.add('hidden');
        });
        document.getElementById('take-me-back-modal').addEventListener('click', function() {
            const modal = document.getElementById('revertModal');
            modal.classList.add('hidden');
        });

        function closeModal() {
            const modal = document.getElementById('myModal');
            if (modal) {
                modal.classList.add('hidden');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Get the modal and close buttons
            const modal = document.getElementById('bookingModal');
            const closeButtons = document.querySelectorAll('#close-modal');

            // Add click event to all close buttons
            closeButtons.forEach(button => {
                button.addEventListener('click', function() {
                    modal.classList.add('hidden');
                });
            });

            // Optional: Close when clicking outside the modal content
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    modal.classList.add('hidden');
                }
            });
        });
    </script>
@endsection
