@extends('layouts.template')

@section('content')

<div class="grid grid-cols-12 gap-4 md:container md:mx-auto  my-6 md:my-10 xl:my-14 px-4 xl:px-0">
    @include('layouts.inc.profile_sidebar')

    <div class="bg-white border rounded p-4 border-gray-200 w-full col-span-12 lg:col-span-9 shadow ">
        @if(session('message'))
            
            <div id="myModal" class="relative z-50" id="delete_message_confirmation" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                    <div onclick="closeModal()"  class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                        <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                            <div class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
                                <button type="button" onclick="closeModal()"  class="absolute top-3 right-3 text-gray-400 hover:text-gray-500">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                                    <div class="sm:flex sm:items-start justify-center">
                                        <div
                                            class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full sm:mx-0 sm:h-10 sm:w-10 bg-green-500">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-lg text-white w-8 h-8" viewBox="0 0 16 16">
                                                <path d="M7.005 3.1a1 1 0 1 1 1.99 0l-.388 6.35a.61.61 0 0 1-1.214 0zM7 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="text-center sm:ml-4 sm:mt-0 sm:text-left">
                                        <div class="mt-2 w-full">
                                            <p class="can-exp-p text-center">{{ session('message') }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="px-4 pb-6 pt-4 flex items-center space-x-2 sm:space-x-4 sm:px-6 justify-center">
                                <input type="hidden" id="notificationId" value="3094">
                                    <a href="#" onclick="closeModal()"  class="button-exp-fill">Close </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        @endif

        <div class="mb-2 mt-8">
            <h1 class="mb-0">{{ $closeAccountPage->main_heading ?? "Close my account"}}</h1>
        </div>

        <div class="mt-4 mb-3 rounded-lg px-4 py-3 bg-red-100 flex items-start gap-3" role="alert">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <p class="text-red-600 font-medium">{{ $closeAccountPage->warning_text ?? $closeAccountPage->closing_account_label ?? "Closing your account will delete all of your data from our platform and this action is permanent"}}</p>
        </div>
        <p class="text-red-500 font-medium mt-4">{{ $closeAccountPage->mobile_indicate_required_field_label ?? "* Indicates required field"}}</p>
        <div class="max-h-[48rem] overflow-y-auto pr-4">
        <div class="rounded-lg py-3">
            <p>
                <span class="text-primary text-xl md:text-2xl font-FuturaMdCnBT">{{ $closeAccountPage->apply_reason_label ?? "You are closing your account"}}</span>
                <span class="text-gray-900 text-xl md:text-1xl font-FuturaMdCnBT"> {{ $closeAccountPage->reason_label ?? "select all the reasons that apply"}}</span><span class="text-red-500 text-xl md:text-2xl font-bold">*</span>
            </p>
        </div>
        <form method="POST" action="{{ route('close_account.update',$user->id) }}">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <ul class="space-y-4 ml-2">
                        <li>
                            <div>
                                <input type="checkbox" value="Prefer not to say" name="reasons[]" {{ (in_array('Prefer not to say', old('reasons', [])) || empty(old('reasons'))) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                <label for="" class="ml-2 text-gray-900">{{ $closeAccountPage->not_say_checkbox_label ?? "Prefer not to say"}}</label>
                            </div>
                        </li>
                        <li>
                            <div>
                                <input type="checkbox" value="I do not like the phone/email customer service" name="reasons[]" {{ in_array('I do not like the phone/email customer service', old('reasons', [])) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                <label for="" class="ml-2 text-gray-900">{{ $closeAccountPage->customer_service_checkbox_label ?? "I do not like the phone/email customer service"}}</label>
                            </div>
                        </li>
                        <li>
                            <div>
                                <input type="checkbox" value="Technical issues with the website/app" name="reasons[]" {{ in_array('Technical issues with the website/app', old('reasons', [])) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                <label for="" class="ml-2 text-gray-900">{{ $closeAccountPage->technical_issue_checkbox_label ?? "Technical issues with the website/app"}}</label>
                            </div>
                        </li>
                        <li>
                            <div>
                                <input type="checkbox" value="Difficulties making/receving payments" name="reasons[]" {{ in_array('Difficulties making/receving payments', old('reasons', [])) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                <label for="" class="ml-2 text-gray-900">{{ $closeAccountPage->difficulties_making_receiving_payments_label ?? "Difficulties making/receving payments"}}</label>
                            </div>
                        </li>
                        <li>
                            <div>
                                <input type="checkbox" value="I don’t use ridesharing anymore" name="reasons[]" {{ in_array("I don’t use ridesharing anymore", old('reasons', [])) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                <label for="" class="ml-2 text-gray-900">{{ $closeAccountPage->dont_use_checkbox_label ?? "I don’t use ridesharing anymore"}}</label>
                            </div>
                        </li>
                        <li>
                            <div>
                                <input type="checkbox" value="I have another account that I’ll be using" name="reasons[]" {{ in_array("I have another account that I’ll be using", old('reasons', [])) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                <label for="" class="ml-2 text-gray-900">{{ $closeAccountPage->another_account_checkbox_label ?? "I have another account that I’ll be using"}}</label>
                            </div>
                        </li>
                        <li>
                            <div>
                                <input type="checkbox" value="I did not get bookings on the rides I posted" name="reasons[]" {{ in_array('I did not get bookings on the rides I posted', old('reasons', [])) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                <label for="" class="ml-2 text-gray-900">{{ $closeAccountPage->did_not_get_booking_checkbox_label ?? "I did not get bookings on the rides I posted"}}</label>
                            </div>
                        </li>
                        <li>
                            <div>
                                <input type="checkbox" value="I did not find rides to my destination" name="reasons[]" {{ in_array('I did not find rides to my destination', old('reasons', [])) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                <label for="" class="ml-2 text-gray-900">{{ $closeAccountPage->did_not_find_ride_checkbox_label ?? "I did not find rides to my destination"}}</label>
                            </div>
                        </li>
                        <li>
                            <div>
                                <input type="checkbox" value="Other" name="reasons[]" {{ in_array('Other', old('reasons', [])) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                <label for="" class="ml-2 text-gray-900">{{ $closeAccountPage->other_checkbox_label ?? "Other"}}</label>
                            </div>
                        </li>
                        @error('reasons')
                            <div class="relative tooltip -bottom-4 group-hover:flex">
                                <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                    <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                </div>
                            </div>
                        @enderror
                    </ul>
                </div>

                <div class="rounded-lg pt-3 md:col-span-2">
                    <p>
                        <span class="text-primary text-xl md:text-2xl font-FuturaMdCnBT">{{ $closeAccountPage->recommend_heading ?? "Would you recommend ProximaRide to your friends?"}}</span><span class="text-red-500 text-xl md:text-2xl font-bold">*</span>
                    </p>
                </div>
                <div class="md:col-span-2">
                    <ul class="space-y-4">
                        <li>
                            <div>
                                <input type="checkbox" value="No" name="recommend" {{ old('recommend') === 'No' ? 'checked' : '' }} class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                <label for="" class="ml-2 text-gray-900">{{ $closeAccountPage->no_checkbox_label ?? "No"}}</label>
                            </div>
                        </li>
                        <li>
                            <div>
                                <input type="checkbox" value="Yes" name="recommend" {{ old('recommend') === 'Yes' ? 'checked' : '' }} class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                <label for="" class="ml-2 text-gray-900">{{ $closeAccountPage->yes_checkbox_label ?? "Yes"}}</label>
                            </div>
                        </li>
                        <li>
                            <div>
                                <input type="checkbox" value="Prefer not to say" name="recommend" {{ (old('recommend') === 'Prefer not to say' || empty(old('recommend'))) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                <label for="" class="ml-2 text-gray-900">{{ $closeAccountPage->prefer_not_checkbox_label ?? "Prefer not to say"}}</label>
                            </div>
                        </li>
                    </ul>
                </div>

                <div class="md:col-span-2 mt-5">
                    <p>
                        <span class="text-primary text-base md:text-lg">{{ $closeAccountPage->why_closing_account_label ?? "In your own words, please tell us why you’d like to close your account."}}</span>
                        <span class="text-gray-900 text-base md:text-lg"> {{ $closeAccountPage->why_closing_account_placeholder ?? "This is optional, but your feedback would be greatly appreciated."}}</span><span class="text-red-500 text-xl md:text-2xl font-bold">*</span>
                    </p>
                    <textarea rows="5" name="close_account_reason" class="block mt-1 border p-1.5 w-full text-base lg:text-lg rounded border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600">{{ old('close_account_reason') }}</textarea>
                </div>

                <div class="md:col-span-2 mt-5">
                    <p>
                        <span class="text-primary text-base md:text-lg">{{ $closeAccountPage->improve_label ?? "We’d love to hear how we can improve."}}</span>
                        <span class="text-gray-900 text-base md:text-lg"> {{ $closeAccountPage->why_closing_account_placeholder ?? "Sharing is optional, but your input would mean a lot."}}</span><span class="text-red-500 text-xl md:text-2xl font-bold">*</span>
                    </p>
                    <textarea rows="5" name="improve_message" class="block mt-1 border p-1.5 w-full text-base lg:text-lg rounded border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600">{{ old('improve_message') }}</textarea>
                </div>

                <div class="md:col-span-2">
                    <input type="checkbox" value="1" name="close_account" id="close_account_checkbox" {{ old('close_account') === '1' ? 'checked' : '' }} class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 ml-2 focus:ring-none">
                    <label for="close_account_checkbox" class="ml-2 text-gray-900 cursor-pointer">{{ $closeAccountPage->close_my_account_checkbox ?? "Close my account"}}</label><span class="text-red-500 font-bold">*</span>
                    @error('close_account')
                        <div class="relative tooltip -bottom-4 group-hover:flex">
                            <div role="tooltip" class="relative tooltiptext z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                            </div>
                        </div>
                    @enderror
                    <div id="package-errors-div" class="hidden relative tooltip -bottom-4 group-hover:flex">
                        <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                            <p id="package-errors" class="text-white leading-none text-sm lg:text-base"></p>
                        </div>
                    </div>
                </div>

                <div class="md:col-span-2 flex justify-center">
                    <button type="button" id="show-modal" class="inline-flex justify-center rounded bg-red-500 border border-red-500 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-red-600 hover:border-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">{{ $closeAccountPage->close_account_button_text ?? "Close my account"}}</button>
                </div>
            </div>

            <div id="modal" class="relative z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity modal-backdrop"></div>
                <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                    <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                        <div
                            class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-lg bg-white text-center shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md modal-border">
                            <button id="hide-modal-btn" type="button" class="absolute top-4 right-4 text-gray-400 hover:text-gray-500 focus:outline-none">
                                <span class="sr-only">Close</span>
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                            <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                                <div class="mt-4 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                    <div class="mt-2">
                                        <h3 class="text-3xl text-center font-FuturaMdCnBT font-medium text-gray-900 mb-4" id="modal-title">{{ $closeAccountPage->web_irreversible_label ?? "This action is irreversible"}}</h3>
                                    </div>
                                    <div class="mt-2 w-full">
                                        <p class="text-lg text-center text-black">{{ $closeAccountPage->close_account_sure_message_text ?? "Are you sure to you want to close your account?"}}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="px-4 pb-6 pt-4  sm:flex sm:flex-row-reverse sm:px-6 justify-center">
                                <button type="submit" class="button-exp-fill">{{ $closeAccountPage->close_it_button_label ?? "Yes, close it"}}</button>
                                <button id="hide-modal-btn" type="button" class="button-exp-fill mr-1">{{ $closeAccountPage->take_me_back_button_label ?? "No, take me back"}}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    </div>
</div>
@endsection


@php
    $messageData = $closeAccountPage->check_box_validation_message ?? "Check the box";
@endphp

@section('script')

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    document.getElementById('show-modal').addEventListener('click', function() {
        var checkbox = $("input[name='close_account']:checked").val();
        if (checkbox) {
            var errorElementDiv = document.getElementById('package-errors-div');
            if (!errorElementDiv.classList.contains('hidden')) {
                errorElementDiv.classList.add('hidden');
            }
            document.getElementById('modal').style.display = 'block';
        } else {
            var errorElementDiv = document.getElementById('package-errors-div');
            errorElementDiv.classList.remove('hidden');

            var messageData = "{{ $messageData }}";
            var errorElement = document.getElementById('package-errors');
            errorElement.textContent = messageData;
        }
    });

    // Hide tooltip when checkbox is checked
    document.getElementById('close_account_checkbox').addEventListener('change', function() {
        var errorElementDiv = document.getElementById('package-errors-div');
        if (this.checked && !errorElementDiv.classList.contains('hidden')) {
            errorElementDiv.classList.add('hidden');
        }
    });

    // Handle "No, take me back" button click
    document.querySelectorAll('#hide-modal-btn').forEach(function(button) {
        button.addEventListener('click', function() {
            document.getElementById('modal').style.display = 'none';
        });
    });

    // Handle click outside modal to close
    document.addEventListener('DOMContentLoaded', function() {
        const modalBackdrop = document.querySelector('#modal .modal-backdrop');
        if (modalBackdrop) {
            modalBackdrop.addEventListener('click', function(event) {
                if (event.target === this) {
                    document.getElementById('modal').style.display = 'none';
                }
            });
        }
    });

    function closeModal() {
        const modal = document.getElementById('myModal');
        if (modal) {
            modal.classList.add('hidden');
        }
    }
</script>

@endsection