@extends('layouts.template')

@section('content')
    <div class="grid grid-cols-12 gap-4 md:container md:mx-auto  my-6 md:my-10 xl:my-14 px-4 xl:px-0">
        @include('layouts.inc.profile_sidebar')

        <div class="bg-white border rounded p-4 border-gray-200 w-full col-span-12 lg:col-span-9 shadow ">
            @if (session('message'))
                <div id="myModal" class="relative z-50" aria-labelledby="modal-title"
                    role="dialog" aria-modal="true">
                    <div onclick="closeModal()" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                        <div
                            class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                            <div
                                class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
                                <button type="button" onclick="closeModal()"
                                    class="absolute top-3 right-3 text-gray-400 hover:text-gray-500">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                                    <div class="sm:flex sm:items-start justify-center">
                                        <div
                                            class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full sm:mx-0 sm:h-10 sm:w-10 bg-green-500">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                fill="currentColor" class="bi bi-exclamation-lg text-white w-8 h-8"
                                                viewBox="0 0 16 16">
                                                <path
                                                    d="M7.005 3.1a1 1 0 1 1 1.99 0l-.388 6.35a.61.61 0 0 1-1.214 0zM7 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="text-center sm:ml-4 sm:mt-0 sm:text-left">
                                        <div class="mt-5 w-full">
                                            <p class="can-exp-p text-center">{{ session('message') }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="px-4 pb-6 pt-4 flex items-center space-x-2 sm:space-x-4 sm:px-6 justify-center">
                                    <input type="hidden" id="notificationId" value="3094">
                                    <a href="#" onclick="closeModal()"
                                        class="button-exp-fill">{{ $siteText['close_btn_text'] }} </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="mb-2 mt-8">
                <h1 class="mb-0">{{ $closeAccountPage->main_heading ?? 'Close my account' }}</h1>
            </div>

            <div class="mt-4 mb-3 rounded-lg px-4 py-3 bg-red-100 flex items-start gap-3" role="alert">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-500 flex-shrink-0 mt-0.5" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <p class="text-red-600 font-medium">
                    {{ $closeAccountPage->warning_text ?? ($closeAccountPage->closing_account_label ?? 'Closing your account will delete all of your data from our platform and this action is permanent') }}
                </p>
            </div>
            <p class="text-red-500 font-medium mt-4">
                {{ $closeAccountPage->mobile_indicate_required_field_label ?? '* Indicates required field' }}</p>
            <div class="pr-4">
                <div class="rounded-lg py-3">
                    <p>
                        <span
                            class="text-primary text-xl md:text-2xl font-FuturaMdCnBT">{{ $closeAccountPage->apply_reason_label ?? 'You are closing your account' }}</span>
                        <span class="text-gray-900 text-xl md:text-1xl font-FuturaMdCnBT">
                            {{ $closeAccountPage->reason_label ?? 'select all the reasons that apply' }}</span><span
                            class="text-red-500 text-xl md:text-2xl font-bold">*</span>
                    </p>
                </div>
                <form method="POST" action="{{ route('close_account.update', $user->id) }}" id="close-account-form">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <ul class="space-y-4 ml-2">
                                <li>
                                    <div>
                                        <input type="checkbox" value="Prefer not to say" name="reasons[]" id="reason_prefer_not_say"
                                            {{ in_array('Prefer not to say', old('reasons', [])) ? 'checked' : '' }}
                                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                        <label for="reason_prefer_not_say"
                                            class="ml-2 text-gray-900 cursor-pointer">{{ $closeAccountPage->not_say_checkbox_label ?? 'Prefer not to say' }}</label>
                                    </div>
                                </li>
                                <li>
                                    <div>
                                        <input type="checkbox" value="I do not like the phone/email customer service"
                                            name="reasons[]" id="reason_customer_service"
                                            {{ in_array('I do not like the phone/email customer service', old('reasons', [])) ? 'checked' : '' }}
                                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                        <label for="reason_customer_service"
                                            class="ml-2 text-gray-900 cursor-pointer">{{ $closeAccountPage->customer_service_checkbox_label ?? 'I do not like the phone/email customer service' }}</label>
                                    </div>
                                </li>
                                <li>
                                    <div>
                                        <input type="checkbox" value="Technical issues with the website/app"
                                            name="reasons[]" id="reason_technical_issue"
                                            {{ in_array('Technical issues with the website/app', old('reasons', [])) ? 'checked' : '' }}
                                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                        <label for="reason_technical_issue"
                                            class="ml-2 text-gray-900 cursor-pointer">{{ $closeAccountPage->technical_issue_checkbox_label ?? 'Technical issues with the website/app' }}</label>
                                    </div>
                                </li>
                                <li>
                                    <div>
                                        <input type="checkbox" value="Difficulties making/receving payments"
                                            name="reasons[]" id="reason_payment_difficulties"
                                            {{ in_array('Difficulties making/receving payments', old('reasons', [])) ? 'checked' : '' }}
                                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                        <label for="reason_payment_difficulties"
                                            class="ml-2 text-gray-900 cursor-pointer">{{ $closeAccountPage->difficulties_making_receiving_payments_label ?? 'Difficulties making/receving payments' }}</label>
                                    </div>
                                </li>
                                <li>
                                    <div>
                                        <input type="checkbox" value="I don't use ridesharing anymore" name="reasons[]" id="reason_dont_use"
                                            {{ in_array("I don't use ridesharing anymore", old('reasons', [])) ? 'checked' : '' }}
                                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                        <label for="reason_dont_use"
                                            class="ml-2 text-gray-900 cursor-pointer">{{ $closeAccountPage->dont_use_checkbox_label ?? "I don't use ridesharing anymore" }}</label>
                                    </div>
                                </li>
                                <li>
                                    <div>
                                        <input type="checkbox" value="I have another account that I'll be using"
                                            name="reasons[]" id="reason_another_account"
                                            {{ in_array("I have another account that I'll be using", old('reasons', [])) ? 'checked' : '' }}
                                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                        <label for="reason_another_account"
                                            class="ml-2 text-gray-900 cursor-pointer">{{ $closeAccountPage->another_account_checkbox_label ?? "I have another account that I'll be using" }}</label>
                                    </div>
                                </li>
                                <li>
                                    <div>
                                        <input type="checkbox" value="I did not get bookings on the rides I posted"
                                            name="reasons[]" id="reason_no_bookings"
                                            {{ in_array('I did not get bookings on the rides I posted', old('reasons', [])) ? 'checked' : '' }}
                                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                        <label for="reason_no_bookings"
                                            class="ml-2 text-gray-900 cursor-pointer">{{ $closeAccountPage->did_not_get_booking_checkbox_label ?? 'I did not get bookings on the rides I posted' }}</label>
                                    </div>
                                </li>
                                <li>
                                    <div>
                                        <input type="checkbox" value="I did not find rides to my destination"
                                            name="reasons[]" id="reason_no_rides"
                                            {{ in_array('I did not find rides to my destination', old('reasons', [])) ? 'checked' : '' }}
                                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                        <label for="reason_no_rides"
                                            class="ml-2 text-gray-900 cursor-pointer">{{ $closeAccountPage->did_not_find_ride_checkbox_label ?? 'I did not find rides to my destination' }}</label>
                                    </div>
                                </li>
                                <li>
                                    <div>
                                        <input type="checkbox" value="Other" name="reasons[]" id="reason_other"
                                            {{ in_array('Other', old('reasons', [])) ? 'checked' : '' }}
                                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                        <label for="reason_other"
                                            class="ml-2 text-gray-900 cursor-pointer">{{ $closeAccountPage->other_checkbox_label ?? 'Other' }}</label>
                                    </div>
                                </li>
                                @error('reasons')
                                    <div class="tooltip-error shadow-lg">{{ $message }}</div>
                                @enderror
                                <div id="reasons_error_client" class="hidden mt-2">
                                    <div class="tooltip-error shadow-lg"><span class="reasons-error-text"></span></div>
                                </div>
                            </ul>
                        </div>

                        <div class="rounded-lg pt-3 md:col-span-2">
                            <p>
                                <span
                                    class="text-primary text-xl md:text-2xl font-FuturaMdCnBT">{{ $closeAccountPage->recommend_heading ?? 'Would you recommend ProximaRide to your friends?' }}</span><span
                                    class="text-red-500 text-xl md:text-2xl font-bold">*</span>
                            </p>
                        </div>
                        <div class="md:col-span-2">
                            <ul class="space-y-4">
                                <li>
                                    <div>
                                        <input type="radio" value="No" name="recommend" id="recommend_no"
                                            {{ old('recommend') === 'No' ? 'checked' : '' }}
                                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                        <label for="recommend_no"
                                            class="ml-2 text-gray-900 cursor-pointer">{{ $closeAccountPage->no_checkbox_label ?? 'No' }}</label>
                                    </div>
                                </li>
                                <li>
                                    <div>
                                        <input type="radio" value="Yes" name="recommend" id="recommend_yes"
                                            {{ old('recommend') === 'Yes' ? 'checked' : '' }}
                                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                        <label for="recommend_yes"
                                            class="ml-2 text-gray-900 cursor-pointer">{{ $closeAccountPage->yes_checkbox_label ?? 'Yes' }}</label>
                                    </div>
                                </li>
                                <li>
                                    <div>
                                        <input type="radio" value="Prefer not to say" name="recommend" id="recommend_prefer_not_say"
                                            {{ old('recommend') === 'Prefer not to say' || empty(old('recommend')) ? 'checked' : '' }}
                                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                        <label for="recommend_prefer_not_say"
                                            class="ml-2 text-gray-900 cursor-pointer">{{ $closeAccountPage->prefer_not_checkbox_label ?? 'Prefer not to say' }}</label>
                                    </div>
                                </li>
                            </ul>
                        </div>

                        <div class="md:col-span-2 mt-5">
                            <p>
                                <span
                                    class="text-primary text-base md:text-lg">{{ $closeAccountPage->why_closing_account_label ?? 'In your own words, please tell us why you’d like to close your account.' }}</span>
                                <span class="text-gray-900 text-base md:text-lg">
                                    {{ $closeAccountPage->why_closing_account_placeholder ?? 'This is optional, but your feedback would be greatly appreciated.' }}</span><span
                                    class="text-red-500 text-xl md:text-2xl font-bold">*</span>
                            </p>
                            <textarea rows="5" name="close_account_reason" id="close_account_reason"
                                class="block mt-1 border p-1.5 w-full text-base lg:text-lg rounded border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600 ">{{ old('close_account_reason') }}</textarea>
                            @error('close_account_reason')
                                <div class="tooltip-error shadow-lg">{{ $message }}</div>
                            @enderror
                            <div id="close_account_reason_error_client" class="hidden mt-2">
                                <div class="tooltip-error shadow-lg"><span class="error-text"></span></div>
                            </div>
                        </div>

                        <div class="md:col-span-2 mt-5">
                            <p>
                                <span
                                    class="text-primary text-base md:text-lg">{{ $closeAccountPage->improve_label ?? 'We’d love to hear how we can improve.' }}</span>
                                <span class="text-gray-900 text-base md:text-lg">
                                    {{ $closeAccountPage->why_closing_account_placeholder ?? 'Sharing is optional, but your input would mean a lot.' }}</span><span
                                    class="text-red-500 text-xl md:text-2xl font-bold">*</span>
                            </p>
                            <textarea rows="5" name="improve_message" id="improve_message"
                                class="block mt-1 border p-1.5 w-full text-base lg:text-lg rounded border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600 ">{{ old('improve_message') }}</textarea>
                            @error('improve_message')
                                <div class="tooltip-error shadow-lg">{{ $message }}</div>
                            @enderror
                            <div id="improve_message_error_client" class="hidden mt-2">
                                <div class="tooltip-error shadow-lg"><span class="error-text"></span></div>
                            </div>
                        </div>

                        <div class="md:col-span-2">
                            <div>
                                <input type="checkbox" value="1" name="confirm_close_account" id="close_account_checkbox"
                                {{ old('confirm_close_account') === '1' ? 'checked' : '' }}
                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 ml-2 focus:ring-none">
                                <label for="close_account_checkbox"
                                class="ml-2 text-gray-900 cursor-pointer">{{ $closeAccountPage->close_my_account_checkbox ?? 'Close my account' }}</label><span
                                class="text-red-500 font-bold">*</span>
                            </div>
                            @error('confirm_close_account')
                                <div class="tooltip-error shadow-lg">{{ $message }}</div>
                            @enderror
                            <div id="confirm_close_account_error_client" class="hidden mt-2">
                                <div class="tooltip-error shadow-lg"><span class="confirm-error-text"></span></div>
                            </div>
                        </div>

                        <div class="md:col-span-2 flex justify-center">
                            <button type="button" onclick="openCloseAccountConfirmationModal()"
                                class="button-exp-red-fill">{{ $closeAccountPage->close_account_button_text ?? 'Close my account' }}</button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <div id="closeAccountConfirmationModal" class="relative z-50 hidden" aria-labelledby="close-account-modal-title" role="dialog"
        aria-modal="true">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeCloseAccountConfirmationModal()"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                <div
                    class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border1">
                    <button type="button" onclick="closeCloseAccountConfirmationModal()"
                        class="absolute top-3 right-3 text-gray-400 hover:text-gray-500">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="text-center w-full">
                            <div class="sm:flex sm:items-start justify-center">
                                <svg width="64px" height="64px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#ff0000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M12 10V13" stroke="#db0000" stroke-width="2" stroke-linecap="round"></path> <path d="M12 16V15.9888" stroke="#db0000" stroke-width="2" stroke-linecap="round"></path> <path d="M10.2518 5.147L3.6508 17.0287C2.91021 18.3618 3.87415 20 5.39912 20H18.6011C20.126 20 21.09 18.3618 20.3494 17.0287L13.7484 5.147C12.9864 3.77538 11.0138 3.77538 10.2518 5.147Z" stroke="#db0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
                            </div>
                            <h3 id="close-account-modal-title" class="font-FuturaMdCnBT text-gray-700 mb-4">
                                {{ $closeAccountPage->web_irreversible_label ?? 'This Action Cannot Be Undone.' }}
                            </h3>
                            <p class="can-exp-p text-center">
                                {{ $closeAccountPage->close_account_sure_message_text ?? ($closeAccountPage->closing_account_label ?? 'Closing your account will delete all of your data from our platform and this action is permanent') }}
                            </p>
                        </div>
                    </div>
                    <div class="px-4 pb-6 pt-4 flex items-center justify-center space-x-2 sm:space-x-4 sm:px-6">
                         <button type="button" onclick="closeCloseAccountConfirmationModal()"
                            class="button-exp-fill">
                            {{ $siteText['take_me_back_button_label'] ?? 'No, take me back!' }}
                        </button>
                        <button type="button" onclick="submitCloseAccountForm()"
                            class="button-exp-red-fill">
                            {{ $closeAccountPage->close_it_button_label ?? 'Yes, close it!' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('script')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        var closeAccountValidationMessages = {
            reasons: '{{ addslashes($closeAccountPage->reasons_required_error ?? "The reasons are required") }}',
            close_account_reason: '{{ addslashes($closeAccountPage->why_closing_account_label ?? "Reason for closing account") }}',
            improve_message: '{{ addslashes($closeAccountPage->improve_label ?? "How we can improve") }}',
            confirm_close_account: '{{ addslashes($closeAccountPage->check_box_validation_message ?? "Just one last step! Please check this box to confirm.") }}'
        };

        $(function() {
            var $preferNotSay = $('#reason_prefer_not_say');
            var $otherReasons = $('input[name="reasons[]"]').not('#reason_prefer_not_say');

            $preferNotSay.on('change', function() {
                if ($(this).is(':checked')) {
                    $otherReasons.prop('checked', false);
                }
            });

            $otherReasons.on('change', function() {
                if ($(this).is(':checked')) {
                    $preferNotSay.prop('checked', false);
                }
            });

            // Hide client tooltips when user corrects the field
            $('input[name="reasons[]"]').on('change', function() { $('#reasons_error_client').addClass('hidden'); });
            $('#close_account_reason').on('input', function() { $('#close_account_reason_error_client').addClass('hidden'); });
            $('#improve_message').on('input', function() { $('#improve_message_error_client').addClass('hidden'); });
            $('#close_account_checkbox').on('change', function() { $('#confirm_close_account_error_client').addClass('hidden'); });
        });

        function closeModal() {
            const modal = document.getElementById('myModal');
            if (modal) {
                modal.classList.add('hidden');
            }
        }

        /** Returns true if all required fields are valid; otherwise shows tooltips and returns false. */
        function validateCloseAccountForm() {
            var isValid = true;
            // Hide all client tooltips first
            $('#reasons_error_client').addClass('hidden');
            $('#close_account_reason_error_client').addClass('hidden');
            $('#improve_message_error_client').addClass('hidden');
            $('#confirm_close_account_error_client').addClass('hidden');

            var reasonsChecked = $('input[name="reasons[]"]:checked').length;
            if (reasonsChecked === 0) {
                $('#reasons_error_client').find('.reasons-error-text').text(closeAccountValidationMessages.reasons);
                $('#reasons_error_client').removeClass('hidden');
                isValid = false;
            }

            var closeReason = $.trim($('#close_account_reason').val());
            if (closeReason === '') {
                $('#close_account_reason_error_client').removeClass('hidden').find('.error-text').text(closeAccountValidationMessages.close_account_reason);
                isValid = false;
            }

            var improveMsg = $.trim($('#improve_message').val());
            if (improveMsg === '') {
                $('#improve_message_error_client').removeClass('hidden').find('.error-text').text(closeAccountValidationMessages.improve_message);
                isValid = false;
            }

            if (!$('#close_account_checkbox').is(':checked')) {
                $('#confirm_close_account_error_client').find('.confirm-error-text').text(closeAccountValidationMessages.confirm_close_account);
                $('#confirm_close_account_error_client').removeClass('hidden');
                isValid = false;
            }

            if (!isValid) {
                var firstError = $('#reasons_error_client:not(.hidden), #close_account_reason_error_client:not(.hidden), #improve_message_error_client:not(.hidden), #confirm_close_account_error_client:not(.hidden)').first();
                if (firstError.length) {
                    firstError[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
            return isValid;
        }

        function openCloseAccountConfirmationModal() {
            if (!validateCloseAccountForm()) {
                return;
            }
            const modal = document.getElementById('closeAccountConfirmationModal');
            if (modal) {
                modal.classList.remove('hidden');
            }
        }

        function closeCloseAccountConfirmationModal() {
            const modal = document.getElementById('closeAccountConfirmationModal');
            if (modal) {
                modal.classList.add('hidden');
            }
        }

        function submitCloseAccountForm() {
            const form = document.getElementById('close-account-form');
            if (form) {
                form.submit();
            }
        }
    </script>
@endsection
