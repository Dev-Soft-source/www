@extends('layouts.template')

@section('content')
@if(session('failure'))
<div class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
            <div
                class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                <button type="button" onclick="closeModal()" class="absolute top-3 right-3 text-gray-400 hover:text-gray-500">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
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
                    <div class="text-center sm:ml-4 sm:mt-0 sm:text-left">
                        <div class="">
                            <h3 class="text-3xl text-center font-FuturaMdCnBT font-medium text-gray-900 mb-4" id="modal-title">{!! session('heading') !!}</h3>
                        </div>
                        <div class="mt-2 w-full">
                            <p class="can-exp-p text-center">{!! session('failure') !!}</p>
                        </div>
                    </div>
                </div>
                <div class="px-4 pb-6 pt-4 flex items-center space-x-2 sm:space-x-4 sm:px-6 justify-center">
                 
                    <a href=""
                        class="button-exp-fill">Close</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
<div class="container mx-auto my-4 p-4">
    <form method="POST" action="{{ route('update_remove_passenger', $booking->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <h1 class="">{{ $tripsPage->remove_passenger_heading ?? ""}}</h1>
        <p>{{ $tripsPage->remove_passenger_text ?? ""}}</p>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <div class="col-span-3">
                <div class="space-y-4">
                    <div class="bg-white rounded-lg hidden shadow-3xl">
                        <div class="bg-white p-4">
                            <div class="flex items-center justify-between">
                                <p class="text-black">Booking fee</p>
                                <p class="totalAmount text-black"></p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg overflow-hidden shadow-3xl">
                        <div class="bg-white p-4">
                            <div class="space-y-4 mb-4">
                                <div class="mt-2 relative space-y-2">
                                    <div class="flex items-center mt-1">
                                        <label for="removed_permanently" class="flex text-sm text-gray-900">
                                            <input id="removed_permanently" name="removed_permanently" type="radio" value="0" checked class="mr-2 h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-600">
                                            {{ $tripsPage->remove_from_this_ride_message ?? "Remove passenger from this ride"}}
                                        </label>
                                    </div>
                                    <div class="flex items-center mt-1">
                                        <label for="removed_permanently" class="flex text-sm text-gray-900">
                                            <input id="removed_permanently" name="removed_permanently" type="radio" value="1" {{ old('removed_permanently') == '1' ? 'checked' : '' }} class="mr-2 h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-600">
                                            {{ $tripsPage->remove_passenger_and_block_message ?? "Remove this passenger from this and prevent him from booking on any of my ride in future"}}
                                        </label>
                                    </div>
                                </div>
                                <div class="text-base md:text-lg"><span class="text-red-500">*  All fields are required</span></div>
                                <div class="flex items-center gap-1 mb-2 max-w-md w-full">
                                    <p class="text-gray-900 font-medium text-lg lg:text-xl w-full">
                                        {{ $tripsPage->number_of_seat_booked ?? "Number of seats booked"}} :
                                    </p>
                                    <p class=" w-full"> {{ $booking->seats }}</p>
                                </div>

                                <div class="bg-white p-4 justify-center hidden showRemoveTypeButton">
                                    <ul class="grid w-full gap-6 md:grid-cols-2">
                                        <li>
                                            <input type="radio" id="temporarily" name="remove_type" value="temporarily"
                                                {{ old('remove_type') == "temporarily" ? 'checked' : '' }} class="hidden peer">
                                            <label for="temporarily" class="inline-flex items-center space-x-3 w-full p-4 text-gray-800 bg-white border-2 border-gray-100 rounded cursor-pointer peer-checked:border-green-500 peer-checked:border-2 peer-checked:text-green-500 hover:border-2 hover:border-green-500">

                                                <span class="font-medium text-xl">
                                                    {{ $tripsPage->block_temporarily_label ?? "Block temporarily" }}
                                                </span>
                                            </label>
                                        </li>

                                        <li>
                                            <input type="radio" id="permanently" name="remove_type" value="permanently"
                                                {{ old('remove_type') == "permanently" ? 'checked' : '' }} class="hidden peer">
                                            <label for="permanently" class="inline-flex items-center space-x-3 w-full p-4 text-gray-800 bg-white border-2 border-gray-100 rounded cursor-pointer peer-checked:border-green-500 peer-checked:border-2 peer-checked:text-green-500 hover:border-2 hover:border-green-500">

                                                <span class="font-medium text-xl">
                                                    {{ $tripsPage->block_permanently_label ?? "Block permanently" }}
                                                </span>
                                            </label>
                                        </li>
                                    </ul>
                                    @error('remove_type')
                                      <div class="relative tooltip -bottom-4 group-hover:flex">
                                        <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                            <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                        </div>
                                      </div>
                                    @enderror
                                </div>

                                <div id="block_days" class="hidden justify-between w-full max-w-md  items-center">
                                    <label for="block_day" class="text-gray-900 font-medium text-lg lg:text-xl mb-2 w-full">{{ $tripsPage->remove_day_label ?? "How many seats do you want to cancel?"}} :</label>
                                    <div>
                                        <input type="number" step="1" min="1" id="block_day" placeholder="{{ $tripsPage->remove_day_placeholder ?? "Select"}}" name="block_day"
                                            class="border border-gray-300 text-gray-500 rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 mt-2 block pr-8 p-2.5">

                                            @error('block_day')
                                                <div class="relative tooltip -bottom-4 group-hover:flex">
                                                    <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                                        <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                                    </div>
                                                </div>
                                            @enderror
                                    </div>
                                </div>
                                <div class="mt-6 w-full">
                                    <label for="meeting" class="text-gray-900 font-medium text-lg mb-2">{{ $tripsPage->driver_remove_reason_label ?? "Tell us why"}}</label>
                                    <textarea id="meeting" rows="5" name="admin_message"
                                        class="block p-2.5 w-full text-gray-900 bg-white rounded border border-gray-300 focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 mt-2"
                                        placeholder="{{ $tripsPage->driver_remove_reason_placeholder ?? 'Please tell us why you want to remove this passenger from your ride Your passenger will not receive a copy of this message'}}">{{ old('admin_message') }}</textarea>
                                    @error('admin_message')
                                    <div class="relative tooltip -bottom-4 group-hover:flex">
                                        <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                            <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                        </div>
                                    </div>
                                    @enderror
                                </div>
                                <div class="mt-6">
                                    <label for="meeting" class="text-gray-900 font-medium text-lg mb-2">{{ $tripsPage->passenger_remove_reason_label ?? "Tell your passenger why"}}</label>
                                    <textarea id="meeting" rows="5" name="passenger_message"
                                        class="block p-2.5 w-full text-gray-900 bg-white rounded border border-gray-300 focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 mt-2"
                                        placeholder="{{ $tripsPage->passenger_remove_reason_placeholder ?? '"Please tell the passenger why you are removing them from this ride'}}">{{ old('passenger_message') }}</textarea>
                                    @error('passenger_message')
                                    <div class="relative tooltip -bottom-4 group-hover:flex">
                                        <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                            <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                        </div>
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="flex justify-center items-center mt-4">
                                <button class="button-exp-fill" type="button" onclick="toggleModalCard('card-modal')">
                                    {{ $tripsPage->passenger_cancel_ride_btn_label ?? "Remove passenger" }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="hidden overflow-x-hidden overflow-y-auto fixed inset-0 z-50 outline-none focus:outline-none justify-center items-center" id="card-modal">
            <div class="relative h-screen my-6 mx-auto flex items-center justify-center w-full">
                <!--content-->
                <div
                class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg w-full">
                <button onclick="toggleModalCard('card-modal')" class="absolute top-2 right-2 p-1 rounded-full hover:bg-gray-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
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
                                <p class="text-lg text-center text-black">{{ $tripsPage->passenger_cancel_sure_message ?? "Are you sure you want to delete this passenger?"}}</p>
                            </div>
                        </div>
                    </div>
                    <div class="px-4 pb-6 pt-4 flex items-center space-x-2 sm:space-x-4 sm:px-6 justify-center">
                        <button class="button-exp-fill" type="submit">
                            {{ $tripsPage->passenger_cancel_ride_btn_label ?? "Remove passenger"}}
                        </button>
                        <button type="button" onclick="toggleModalCard('card-modal')"
                            class="button-exp-fill sm:w-24">{{ $messages->popup_close_btn_text ?? "Close" }}</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="hidden opacity-25 fixed inset-0 z-40 bg-black" id="card-modal-backdrop"></div>
    </form>
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
    
    function toggleModalCard(modalID) {
        const modal = document.getElementById(modalID);
        const backdrop = document.getElementById(modalID + '-backdrop');

        modal.classList.toggle('hidden');
        backdrop.classList.toggle('hidden');
        modal.classList.toggle('flex');
        backdrop.classList.toggle('flex');
    }
    $(document).ready(function () {
        function toggleRemoveType() {
            let selectedValue = $('input[type=radio][name=removed_permanently]:checked').val();
            if (selectedValue === '1') {
                $('.showRemoveTypeButton').removeClass('hidden').addClass('flex');
            } else {
                $('.showRemoveTypeButton').removeClass('flex').addClass('hidden');
            }
        }

        function toggleBlockDays() {
            if ($('#temporarily').is(":checked")) {
                $('#block_days').removeClass('hidden').addClass('flex');
            } else {
                $('#block_days').removeClass('flex').addClass('hidden');
            }
        }

        // Page load par function call karein
        toggleBlockDays();
        toggleRemoveType();

        // Radio button change hone par bhi function call karein
        $('input[type=radio][name=removed_permanently]').change(function() {
            toggleRemoveType();
        });
        $('#temporarily').change(function() {
            toggleBlockDays();
        });
        $('#permanently').change(function() {
            toggleBlockDays();
        });
    });



</script>

@endsection
