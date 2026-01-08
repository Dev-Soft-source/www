@extends('layouts.template')

@section('content')
@if(session('failure'))
<div class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
            <div
                class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg w-full">
                <button type="button" onclick="closeModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-500 z-50">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <div class="bg-white px-4 mt-10 sm:mt-1 pb-4 pt-16 sm:p-6 sm:pb-4 sm:pt-16">
                    <div class="sm:flex sm:items-start justify-center">
                        <!-- <div
                            class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full sm:mx-0 sm:h-10 sm:w-10 bg-red-500">
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
                            <p class="can-exp-p text-center">{!! session('failure') !!}</p>
                        </div>
                    </div>
                </div>
                <div class="px-4 pb-6 pt-4  sm:flex sm:flex-row-reverse sm:px-6 justify-center">
                    <a href='' id="close-modal" class="inline-flex justify-center rounded bg-red-500 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-red-400 sm:ml-3 w-auto">
                        Close
                    </a>
                    {{-- <a href=""
                        class="inline-flex w-full justinline-flex justify-center rounded bg-red-500 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-red-400 sm:ml-3 sm:w-24">Close</a> --}}
                </div>
            </div>
        </div>
    </div>
</div>
@endif
    <div class="container mx-auto my-4">
        <div class="w-full md:w-2/3 mx-auto px-4 md:px-0 ">
            <form method="POST" action="{{ route('update_cancel_ride', $ride->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <h1>{{ $tripsPage->cancel_ride_setting ?? 'You are cancelling this ride' }}</h1>
                <div class="space-y-4">
                    <div class="bg-white rounded-lg overflow-hidden shadow-3xl">
                        <div class="bg-white p-4">
                            <div class="space-y-4 mb-4">
                                <div class="mt-6">
                                    <label for="meeting" class="text-gray-900 font-medium text-lg mb-2">{{ $tripsPage->cancel_ride_label ?? 'Tell us why' }}</label>
                                    {{-- <textarea id="meeting" rows="4" name="message"
                                        class="block p-2.5 w-full text-gray-900 bg-white rounded border border-gray-300 focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 mt-2"
                                        placeholder={{ $tripsPage->cancel_ride_placeholder ?? "Provide as many details as you want as to why you want to cancel this ride &#10;Your passengers will receive a copy of this message &#10;ProximaRide will investigate each cancellation &#10;@if ($ride->bookings->where('status', '<>', 3)->where('status', '<>', 4)->count() > 0)Your passenger will receive a copy of this message @endif"}}
                                        >{{ old('message') }}</textarea> --}}
                                    <textarea id="meeting" rows="6" name="message"
                                        class="block p-2.5 w-full text-gray-900 bg-white rounded border border-gray-300 focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 mt-2">{{ old('message') }}</textarea>




                                    {{-- <textarea id="meeting" rows="6" name="message" placeholder="Tell us why.&#10;Canceling a ride with passengers causes significant inconvenience to them, making the ridesharing experience unpleasant and affecting ProximaRide's reliability. Moreover, this may leave your passengers stranded, disrupting their travel plans.&#10;Please explain why you are canceling this ride. Provide as much detail as possible. Your passengers will not see this message.&#10;Tell your passengers why.&#10;Please inform your passengers why you are canceling this ride. A kind word of apology goes a long way.&#10;I confirm that I want to cancel this ride. I understand the inconvenience this will cause my passengers and that this action is irreversible." class="block p-2.5 w-full text-gray-900 bg-white rounded border border-gray-300 focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 mt-2"></textarea> --}}


                                    @error('message')
                                        <p class="p-2 rounded-md px-4 bg-red-500 text-white mt-t w-fit">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="mt-6">
                                    <label for="reason" class="text-gray-900 font-medium text-lg mb-2">{{ $tripsPage->tell_passenger_why_label ?? 'Tell your passenger why' }}</label>
                                    <textarea id="reason" rows="6" name="reason"
                                        class="block p-2.5 w-full text-gray-900 bg-white rounded border border-gray-300 focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 mt-2">{{ old('reason') }}</textarea>

                                    @error('reason')
                                        <p class="p-2 rounded-md px-4 bg-red-500 text-white mt-t w-fit">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="flex justify-center items-center mt-4">
                                {{-- <button class="button-exp-fill" type="submit">
                                    {{ $tripsPage->booking_cancel_btn_label ?? "Cancel ride"}}
                                </button> --}}
                                <button id="cancelRideBtn" class="button-exp-fill" type="submit">
                                    {{ $tripsPage->booking_cancel_btn_label ?? 'Cancel ride' }}
                                </button>

                                <input type="checkbox" id="confirmCancel" class="display ml-4">
                                <label for="confirmCancel">&nbsp;&nbsp; {{ $tripsPage->Confirm_cancel_ride ?? 'I confirm that I want to cancel this ride' }}</label>

                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script>
        document.getElementById('meeting').placeholder =
            `{{ str_replace(["\r", "\n"], '\n', $tripsPage->cancel_ride_placeholder ?? "Tell us why.\nCanceling a ride with passengers causes significant inconvenience to them, making the ridesharing experience unpleasant and affecting ProximaRide's reliability.\nMoreover, this may leave your passengers stranded, disrupting their travel plans.\nPlease explain why you are canceling this ride. Provide as much detail as possible. Your passengers will not see this message.\nTell your passengers why.\nPlease inform your passengers why you are canceling this ride. A kind word of apology goes a long way.\nI confirm that I want to cancel this ride. I understand the inconvenience this will cause my passengers and that this action is irreversible.") }}`;
        document.getElementById('reason').placeholder =
            `{{ str_replace(["\r", "\n"], '\n', $tripsPage->tell_passenger_why_placeholder ?? "Tell us why.\nCanceling a ride with passengers causes significant inconvenience to them, making the ridesharing experience unpleasant and affecting ProximaRide's reliability.\nMoreover, this may leave your passengers stranded, disrupting their travel plans.\nPlease explain why you are canceling this ride. Provide as much detail as possible. Your passengers will not see this message.\nTell your passengers why.\nPlease inform your passengers why you are canceling this ride. A kind word of apology goes a long way.\nI confirm that I want to cancel this ride. I understand the inconvenience this will cause my passengers and that this action is irreversible.") }}`;
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function hideTooltip(parms) {
            if ($(this).parent().find('.tooltip').length > 0 && parms != 'label') {
                $(this).parent().find('.tooltip').addClass('hidden');
            }
            else if ($(this).parent().parent().find('.tooltip').length > 0 && parms != 'label') {
                $(this).parent().parent().find('.tooltip').addClass('hidden');
            }
            else if ($(this).parent().parent().parent().find('.tooltip').length > 0) {
                $(this).parent().parent().parent().find('.tooltip').addClass('hidden');
            }
        }

        const inputs = document.querySelectorAll('input, textarea');
        inputs.forEach(input => {
            input.addEventListener('input', hideTooltip); // no parameter on input typing
        });

        const labels = document.querySelectorAll('label');
        labels.forEach(input => {
            input.addEventListener('click', function (e) {
                hideTooltip.call(this, 'label'); // pass 'testing' on label click
            });
        });
        
        document.getElementById('cancelRideBtn').addEventListener('click', function(e) {
            e.preventDefault(); 
            let checkbox = document.getElementById('confirmCancel');

            if (!checkbox.checked) {
                Swal.fire({
                    // icon: 'error',
                    title: 'Please confirm your decision',
                    confirmButtonText: 'OK'
                    showCancelButton: true,
                });
                return;
            }

            Swal.fire({
                title: 'Are you sure?',
                text: "Canceling a ride may inconvenience your passengers.",
                // icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, cancel it',
                cancelButtonText: 'No, take me back'
            }).then((result) => {
                if (result.isConfirmed) {
                    let form = document.querySelector('form');
                    console.log(form);
                    form.submit();

                    // fetch(form.action, {
                    //     method: 'POST',
                    //     body: formData,

                    // }).then(data => {
                    //     console.log('data', data)
                    //     if (data.success) {
                    //         Swal.fire({
                    //             title: 'This ride has been cancelled',
                    //             icon: 'success',
                    //             confirmButtonText: 'Close',
                    //             confirmButtonColor: '#f87171'
                    //         }).then(() => {
                    //             window.location.href =
                    //                 "{{ route('my_rides', ['lang' => $selectedLanguage->abbreviation]) }}";
                    //         });
                    //     } else if (data.error) {
                    //         Swal.fire({
                    //             title: data.message,
                    //             icon: 'warning',
                    //             confirmButtonText: 'Close',
                    //             confirmButtonColor: '#f87171'
                    //         })
                    //     }
                    // });
                }
            });
        });
    </script>
    <style>
        .button-exp-fill {
            background-color: #f87171 !important;
            color: white !important;
        }

        .swal2-confirm {
            background-color: #f87171 !important;
            border-color: #f87171 !important;
            color: white !important;
        }

        .swal2-cancel {
            background-color: #106BC7 !important;
            border-color: #106BC7 !important;
            color: white !important;
        }
    </style>
@endsection
