@extends('layouts.template')

@section('content')
    @if (session('failure'))
        <div id="myModal" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                    <div
                        class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg w-full modal-border">
                        <button type="button" onclick="closeModalcancel()"
                            class="absolute top-4 right-4 text-gray-400 hover:text-gray-500 z-50">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                        <div class="bg-white px-4 mt-10 sm:mt-1 pb-4 pt-16 sm:p-6 sm:pb-4 sm:pt-16">
                            <div class="sm:flex sm:items-start justify-center"></div>
                            <div class="text-center">

                                <div class="w-full">
                                    <p class="text-center can-exp-p">{!! session('failure') !!}</p>
                                </div>
                            </div>
                        </div>
                        <div class="px-4 pb-6 pt-4 sm:flex sm:flex-row-reverse sm:px-6 justify-center">
                            <a href=""
                                class="whitespace-nowrap inline-flex justify-center rounded bg-red-500 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-red-400 sm:ml-3 w-auto">{{ $siteText['close_btn_text'] }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    {{-- Confirmation modal for cancel booking --}}
    <div id="cancelConfirmModal" class="relative z-50 hidden" aria-labelledby="cancel-confirm-title" role="dialog"
        aria-modal="true">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeCancelConfirmModal()"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                <div class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg w-full modal-border1"
                    onclick="event.stopPropagation()">
                    <button type="button" onclick="closeCancelConfirmModal()"
                        class="absolute top-4 right-4 text-gray-400 hover:text-gray-500 z-50">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </button>
                    <div class="bg-white px-4 mt-10 sm:mt-1 pb-4 pt-16 sm:p-6 sm:pb-4 sm:pt-16">
                        <div class="text-center">
                            <div class="w-full">
                                <p class="text-xl text-center text-black mb-4" id="cancel-confirm-title">
                                    {{ $sureMessage ?? 'Are you sure you want to cancel booking?' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="px-4 pb-6 pt-4 sm:flex sm:flex-row-reverse sm:px-6 justify-center gap-3">
                        <button type="button" onclick="closeCancelConfirmModal()"
                            class="whitespace-nowrap inline-flex justify-center rounded px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:shadow-lg shadow-sm w-auto"
                            style="background-color: #106BC7;">
                            {{ optional($tripsPage)->booking_cancel_btn_no_label ?? 'No, take me back' }}
                        </button>
                        <button type="button" onclick="confirmCancelBooking()"
                            class="whitespace-nowrap inline-flex justify-center rounded px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:shadow-lg shadow-sm w-auto"
                            style="background-color: #f87171;">
                            {{ optional($tripsPage)->booking_cancel_btn_yes_label ?? 'Yes, cancel it!' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container mx-auto my-4">
        <div class="w-full md:w-2/3 mx-auto px-4 md:px-0 ">
            <form method="POST" action="{{ route('update_cancel_booking', $booking->id) }}" enctype="multipart/form-data"
                id="formCancelRide">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    <div class="bg-white rounded-lg hidden shadow-3xl">
                        <div class="bg-white p-4">
                            <div class="flex items-center justify-between">
                                <p class="text-black">Booking fee</p>
                                <p class="totalAmount text-black"></p>
                                <input type="hidden" name="booking_credit" id="totalAmountInput" class="form-control"
                                    readonly>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg overflow-hidden shadow-3xl">
                        <div class="bg-white p-4">
                            <div class="space-y-4 mb-4">
                                <h1 class="text-primary text-center">{{ $tripsPage->cancel_booking_heading ?? '' }}</h1>
                                <div class="text-base md:text-lg"><span class="text-red-500">*
                                        {{ $tripsPage->cancel_all_feilds_are_required ?? 'All fields are required' }}</span>
                                </div>
                                <div class="flex justify-between  max-w-sm w-full">
                                    <label
                                        class="text-gray-900 font-medium text-lg lg:text-xl mb-2">{{ $tripsPage->number_of_seat_booked ?? 'Number of seats booked' }}</label>
                                    <p class="mr-1">{{ $booking->seats }}</p>
                                </div>
                                <div class="flex justify-between items-center max-w-sm w-full">
                                    <label for="seats"
                                        class="text-gray-900 font-medium text-lg lg:text-xl mb-2">{{ $tripsPage->cancel_seat_label ?? 'How many seats do you want to cancel?' }}</label>
                                    <select id="type" name="seats"
                                        class="bg-gray-100 border-0 text-gray-500 rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 mt-2 block pr-8 p-2.5">
                                        @for ($i = 1; $i <= $booking->seats; $i++)
                                            <option value="{{ $i }}"
                                                {{ old('seats') == $i ? 'selected' : ($i == 1 ? 'selected' : '') }}>
                                                {{ $i }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="mt-6">
                                    <label for="message"
                                        class="text-gray-900 font-medium text-lg lg:text-xl mb-2">{{ $tripsPage->cancel_message_title ?? 'Message to your driver' }}</label>
                                    <textarea id="message" rows="5" name="message"
                                        class="block p-2.5 w-full text-gray-900 bg-white rounded border border-gray-300 focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 mt-2"
                                        placeholder="{{ $tripsPage->cancel_booking_trip_placeholder ?? 'Please provide as many details as you want as to why you want to cancel this booking &#10;Your driver will receive a copy of this message' }}">{{ old('message') }}</textarea>
                                    @error('message')
                                        <div class="tooltip-error shadow-lg mt-1">{{ $message }}</div>
                                    @enderror

                                </div>
                            </div>
                            <div class="flex justify-center items-center mt-4">
                                <button class="button-exp-fill" type="submit">
                                    {{ $tripsPage->booking_cancel_btn_label ?? 'Cancel ride' }}
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
                /*
                In this page, it is then pushed into JavaScript as settingBookingPrice and used as the per-seat booking fee for the cancellation calculation when the ride price is above the lower thresholds. That logic is in cancel_booking.blade.php (line 166).
                So in plain terms:  $settingBookingPrice means “the configured booking fee amount from the booking settings,” not the ride price itself.
                */
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
        $(document).ready(function() {
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
            } else if (@json($booking->price) <= 35 && @json(auth()->user()->student) == '1' && cardExpDate <
                currentDate) {
                bookingPrice = parseFloat((15 / 100) * @json($booking->price));
            } else {
                if (settingBookingPrice && settingBookingPrice !== '' && @json(auth()->user()->student) !== '1') {
                    // Get the booking price from $setting
                    bookingPrice = parseFloat(settingBookingPrice);
                } else if (settingBookingPrice && settingBookingPrice !== '' && @json(auth()->user()->student) ==
                    '1' && cardExpDate < currentDate) {
                    // Get the booking price from $setting
                    bookingPrice = parseFloat(settingBookingPrice);
                } else {
                    // Set a default value if $setting is null or not defined
                    bookingPrice = 0.0;
                }
            }

            function updateTotalAmount() {
                var selectedSeats = parseFloat($('#type').val()) || 0;
                var totalAmount = bookingPrice * selectedSeats;
                var formattedTotalAmount = totalAmount.toFixed(2);

                $('.totalAmount').text('$' + formattedTotalAmount);
                $('#totalAmountInput').val(totalAmount);
            }

            $('#type').on('change', function() {
                updateTotalAmount();
            });

            $('#type').trigger('change');
        });
    </script>

    <script>
        const cancelRideForm = document.getElementById('formCancelRide');
        const messageField = document.querySelector('textarea[name="message"]');
        const messageErrorEl = document.getElementById('messageError');
        const requiredMessage = '{{ $tripsPage->cancel_all_feilds_are_required ?? 'All fields are required' }}';
        let cancelBookingConfirmed = false;

        

        function closeModalcancel() {
            const modal = document.getElementById('myModal');
            if (modal) {
                modal.style.display = 'none';
            }
        }

        function closeCancelConfirmModal() {
            document.getElementById('cancelConfirmModal').classList.add('hidden');
        }

        function confirmCancelBooking() {
            cancelBookingConfirmed = true;
            closeCancelConfirmModal();
            cancelRideForm.submit();
        }
    </script>
@endsection
