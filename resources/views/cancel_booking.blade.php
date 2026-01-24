@extends('layouts.template')

@section('content')
@if (session('failure'))
<div id="myModal" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
            <div
            class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg w-full modal-border">
            <button type="button" onclick="closeModalcancel()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-500 z-50">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            <div class="bg-white px-4 mt-10 sm:mt-1 pb-4 pt-16 sm:p-6 sm:pb-4 sm:pt-16">
                <div class="sm:flex sm:items-start justify-center">
                    <!-- <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full sm:mx-0 sm:h-10 sm:w-10 bg-red-500">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-lg text-white w-8 h-8" viewBox="0 0 16 16">
                                <path d="M7.005 3.1a1 1 0 1 1 1.99 0l-.388 6.35a.61.61 0 0 1-1.214 0zM7 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0"/>
                            </svg>
                        </div> -->
                    </div>
                    <div class="text-center">

                        <div class="w-full">
                            <p class="text-center can-exp-p">{!! session('failure') !!}</p>
                        </div>
                    </div>
                </div>
                <div class="px-4 pb-6 pt-4 sm:flex sm:flex-row-reverse sm:px-6 justify-center">
                    <a href=""
                    class="whitespace-nowrap inline-flex justify-center rounded bg-red-500 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-red-400 sm:ml-3 w-auto">Close</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
<div class="container mx-auto my-4">
    <div class="w-full md:w-2/3 mx-auto px-4 md:px-0 ">
    <form method="POST" action="{{ route('update_cancel_booking', $booking->id) }}"
        enctype="multipart/form-data" id="formCancelRide">
        @csrf
        @method('PUT')
        <!-- <h1>{{ $tripsPage->cancel_booking_main_heading ?? "Cancel my booking"}}</h1> -->
                <div class="space-y-4">
                    <div class="bg-white rounded-lg hidden shadow-3xl">
                        <div class="bg-white p-4">
                            <div class="flex items-center justify-between">
                                <p class="text-black">Booking fee</p>
                                <p class="totalAmount text-black"></p>
                                <input type="hidden" name="booking_credit" id="totalAmountInput" class="form-control" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg overflow-hidden shadow-3xl">
                        <div class="bg-white p-4">
                            <div class="space-y-4 mb-4">
                                <h1 class="text-primary text-center">{{ $tripsPage->cancel_booking_heading ?? ""}}</h1>
                                <div class="text-base md:text-lg"><span class="text-red-500">*  {{ $tripsPage->cancel_all_feilds_are_required ?? "All fields are required"}}</span></div>
                                <div class="flex justify-between  max-w-sm w-full">
                                    <p class="text-gray-900 font-medium text-lg lg:text-xl mb-2">{{ $tripsPage->number_of_seat_booked ?? "Number of seats booked"}}</p>
                                    <p class="mr-1">{{ $booking->seats }}</p>
                                </div>
                                <div class="flex justify-between items-center max-w-sm w-full">
                                    <label for="seats" class="text-gray-900 font-medium text-lg lg:text-xl mb-2">{{ $tripsPage->cancel_seat_label ?? "How many seats do you want to cancel?"}}</label>
                                    <select id="type" name="seats"
                                    class="bg-gray-100 border-0 text-gray-500 rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 mt-2 block pr-8 p-2.5">
                                    @for ($i = 1; $i <= $booking->seats; $i++)
                                    <option value="{{ $i }}" {{ old('seats') == $i ? 'selected' : ($i == 1 ? 'selected' : '') }}>
                                        {{ $i }}
                                    </option>
                                    @endfor
                                </select>
                            </div>
                            {{-- <div class="flex justify-between items-center max-w-sm w-full">
                                <label for="seats_amout" class="text-gray-900 font-medium text-lg lg:text-xl mb-2">Enter Amount</label>
                                <input type="number" name="seats_amout"
                                class="bg-gray-100 border-0 text-gray-500 rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 mt-2 block pr-8 p-2.5"/>
                            </div> --}}
                            <div class="mt-6">
                                <label for="meeting" class="text-gray-900 font-medium text-lg mb-2">{{ $tripsPage->cancel_message_title ?? "Message to your driver" }}</label>
                                <textarea id="meeting" rows="5" name="message"
                                class="block p-2.5 w-full text-gray-900 bg-white rounded border border-gray-300 focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 mt-2"
                                placeholder="{{ $tripsPage->cancel_booking_trip_placeholder ?? 'Please provide as many details as you want as to why you want to cancel this booking &#10;Your driver will receive a copy of this message'}}">{{ old('message') }}</textarea>
                                @error('message')
                                <div class="relative tooltip -bottom-4 group-hover:flex">
                                    <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                        <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                    </div>
                                </div>
                                @enderror
                            </div>
                        </div>
                            <div class="flex justify-center items-center mt-4">
                                <button class="button-exp-fill" type="submit" id="cancelRideBtn">
                                    {{ $tripsPage->booking_cancel_btn_label ?? "Cancel ride"}}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
    </form>
    </div>
    @php
        $cardExpTimestamp = strtotime(auth()->user()->student_card_exp_date) * 1000;
    @endphp 
    @if ($setting)
        @php
            $settingBookingPrice = $setting->booking_price;
        @endphp
    @else
        @php
            $settingBookingPrice = '';
        @endphp
    @endif
</div>

@endsection

@section('script')

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<script>
    $(document).ready(function () {
        // Convert the Unix timestamp to a JavaScript Date object
        var cardExpDate = new Date({{ $cardExpTimestamp }});
        
        // Get the current date
        var currentDate = new Date();
        
        var settingBookingPrice = "{{ $settingBookingPrice }}";

        // Check if $setting is defined and not null
        var bookingPrice;

        if (@json($booking->price) <= 15) {
            // Set a default value if $setting is null or not defined
            bookingPrice = 0.0;
        } else if (@json($booking->price) <= 35 && @json(auth()->user()->student) !== '1') {
            bookingPrice = parseFloat((15 / 100) * @json($booking->price));
        } else if (@json($booking->price) <= 35 && @json(auth()->user()->student) == '1' && cardExpDate < currentDate) {
            bookingPrice = parseFloat((15 / 100) * @json($booking->price));
        } else {
            if (settingBookingPrice && settingBookingPrice !== '' && @json(auth()->user()->student) !== '1') {
                // Get the booking price from $setting
                bookingPrice = parseFloat(settingBookingPrice);
            } else if (settingBookingPrice && settingBookingPrice !== '' && @json(auth()->user()->student) == '1' && cardExpDate < currentDate) {
                // Get the booking price from $setting
                bookingPrice = parseFloat(settingBookingPrice);
            } else {
                // Set a default value if $setting is null or not defined
                bookingPrice = 0.0;
            }
        }

        // Function to update the total amount
        function updateTotalAmount() {
            var seatPrice = parseFloat({{ $booking->price }});
            var selectedSeats = $('#type').val();
            var totalAmount = bookingPrice * selectedSeats;
            var totalBookingCredit = "{{ $booking->booking_credit }}";
            var bookedSeats = "{{ $booking->seats }}";
            var totalPaid = Number(bookedSeats) * Number(seatPrice) + Number(totalBookingCredit);
            var totalSeatsAmount = seatPrice * selectedSeats;

            // Calculate the sum of totalAmount and totalSeatsAmount
            var totalSum = totalAmount + totalSeatsAmount;

            // Format the sums to two decimal places
            var formattedTotalAmount = totalAmount.toFixed(2);
            var formattedTotalPayableAmount = Math.abs(totalAmount - totalBookingCredit);
            var formattedTotalPayableOnlineAmount = Math.abs(totalSum - totalPaid)
            var formattedTotalSeatsAmount = totalSeatsAmount.toFixed(2);
            var formattedTotalSum = totalSum.toFixed(2);
            
            // Update the content of the <p> tags
            $('#selectedSeats').text(selectedSeats);
            $('.totalAmount').text('$' + formattedTotalAmount);
            $('.totalPayableAmount').text('$' + formattedTotalPayableAmount.toFixed(2));
            $('.totalPayableOnlineAmount').text('$' + formattedTotalPayableOnlineAmount.toFixed(2));
            $('#totalAmountInput').val(totalAmount);
            $('.totalSeatsAmount').text('$' + formattedTotalSeatsAmount);
            $('.totalSum').text('$' + formattedTotalSum);

            if(selectedSeats >= bookedSeats)
            {
                $('.payable').removeClass('hidden');
                $('.payable').addClass('flex');
                $('.refund').addClass('hidden');
                $('.refund').removeClass('flex');
            } else {
                $('.payable').addClass('hidden');
                $('.payable').removeClass('flex');
                $('.refund').removeClass('hidden');
                $('.refund').addClass('flex');
            }
        }

        // Attach an event listener to the select element
        $('#type').on('change', function () {
            // Call the function to update the total amount
            updateTotalAmount();
        });

        // Trigger the change event on page load
        $('#type').trigger('change');
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
   const cancelRideBtn = document.getElementById('cancelRideBtn');
        cancelRideBtn.addEventListener('click', function(event) {
        event.preventDefault();
        swal.fire({
        title: '{{ $sureMessage ?? "Are you sure you want to cancel booking?"}}',
        // icon: 'warning',
        showCloseButton: true,
        showCancelButton: true,
        confirmButtonColor: '#f87171',
        cancelButtonColor: '#106BC7',
        confirmButtonText: '{{ $tripsPage->booking_cancel_btn_yes_label ?? "Yes, cancel it!"}}',
        cancelButtonText: '{{ $tripsPage->booking_cancel_btn_no_label ?? "No, take me back"}}',
        customClass: {
            popup: 'modal-border'
        }
        }).then((result) => {
        if (result.isConfirmed) {
            const cancelRideBtn = document.getElementById('formCancelRide');
            $("#formCancelRide").submit();
        }
        });
        
    });

    function closeModalcancel() {
    const modal = document.getElementById('myModal');
    if (modal) {
        modal.style.display = 'none';
    }
}
  </script>
<style>

    .swal2-confirm {
      background-color: #f87171 !important; /* Red background for "Yes, cancel it!" and "Close" */
      border-color: #f87171 !important; /* Red border */
    }

    .swal2-cancel {
      background-color: #106BC7 !important; /* Blue background for "No, take me back" */
      border-color: #106BC7 !important; /* Blue border */
    }
  </style>

@endsection