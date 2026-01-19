@extends('layouts.template')

@section('content')
    <div class="mx-auto max-w-2xl lg:max-w-xl">
        <div class="flex min-h-full flex-col justify-center py-12 p-4 sm:px-6 lg:px-8">
            <div class=" bg-white rounded shadow-lg p-4 border border-gray-100">
                <div class="sm:mx-auto sm:w-full sm:max-w-md">
                    <h1 class="text-center">
                        @isset($forgotPasswordPage->main_heading)
                            {{ $forgotPasswordPage->main_heading }}
                        @endisset
                    </h1>

                    <p class="text-justify">
                        @isset($forgotPasswordPage->main_label)
                            {!! $forgotPasswordPage->main_label !!}
                        @endisset
                    </p>

                    <form class="space-y-6" method="POST" action="" id="forgot-password-form">
                        @csrf

                        <!-- Always render modals for AJAX use (hidden by default if no session, but don't create if session modal exists) -->
                        @if (!session('error'))
                        <div id="error-modal" class="relative z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true" onclick="closeModal('error-modal', event)">
                            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                                <div class="flex min-h-full justify-center p-4 text-center items-center sm:p-0">
                                    <div class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl modal-border" 
                                    onclick="event.stopPropagation()">
                                        <button onclick="closeModal('error-modal', event)" class="absolute top-2 right-2 p-1 rounded-full hover:bg-gray-100">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                                            <div class="sm:flex sm:items-start justify-center">
                                            </div>
                                            <div class="text-center">
                                                <div class="w-full">
                                                    <p class="can-exp-p text-center text-black"></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="px-4 pb-6 pt-4 flex items-center space-x-2 sm:space-x-4 sm:px-6 justify-center verify-email-container">
                                            <a onclick="closeModal('error-modal', event)" class="inline-flex w-full justify-center rounded bg-red-500 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-red-400 sm:ml-3 sm:w-24">Close</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if (!session('message'))
                        <div id="my-modal" class="relative z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                                <div class="relative flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeModal('my-modal', event)"></div>
                                    <div class="relative animate__animated animate__fadeIn z-20 transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
                                        <button onclick="closeModal('my-modal', event)" class="absolute top-2 right-2 p-1 rounded-full hover:bg-gray-100">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                                            <div class="sm:flex sm:items-start justify-center">
                                                <div
                                                    class="mx-auto h-16 w-16">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                        stroke-width="4" stroke="currentColor" class="w-12 h-12 text-greenXS">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="text-center sm:ml-4 sm:mt-0">
                                                <div class="w-full">
                                                    <p class="can-exp-p text-center text-black mt-5"></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="px-4 pb-6 pt-4 sm:flex sm:flex-row-reverse sm:px-6 justify-center">
                                            <a onclick="closeModal('my-modal', event)" class="inline-flex w-full justify-center rounded bg-greenXS px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-greenXS sm:ml-3 sm:w-24">Close</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <div>
                            <div class="">
                                <label for="email"></label>
                                <input id="email" placeholder=""
                                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6"
                                    type="text" name="email" value="{{ old('email') }}" autofocus />
                                @error('email')
                                    <div class="relative tooltip tooltip-error -bottom-4 group-hover:flex">
                                        <div role="tooltip"
                                            class="relative tooltiptext -top-2 z-10 leading-none shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                                            <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                        </div>
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="flex items-center justify-center flex-col sm:flex-col md:flex-row lg:flex-row">
                            <div>
                                @isset($forgotPasswordPage->button_label)
                                    <button class="button-exp-fill flex w-full justify-center" type="submit">
                                        {{ $forgotPasswordPage->button_label }}
                                    </button>
                                @endisset
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if (session('showModal'))
        <div id="my-modal" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div
                    class="relative flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                        onclick="closeModal('my-modal', event)"></div>
                    <div
                        class="relative animate__animated animate__fadeIn z-20 transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                        <button onclick="closeModal('my-modal', event)"
                            class="absolute top-2 right-2 p-1 rounded-full hover:bg-gray-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start justify-center">
                                <!-- <div
                                              class="mx-auto h-16 w-16">
                                              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                  stroke-width="4" stroke="currentColor" class="w-12 h-12 text-greenXS">
                                                  <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                              </svg>
                                          </div> -->
                            </div>
                            <div class="mt-2 w-full">
                                <p class="text-center can-exp-p">This email isn't verified yet.</p>
                            </div>
                        </div>
                        <!--footer-->
                        @php
                            $user = session('user');
                        @endphp
                        <div class="px-4 pb-6 pt-4  sm:flex sm:flex-row-reverse sm:px-6 justify-center">
                            <a href="{{ route('sendEmailVerify', ['email' => $user->email]) }}"
                                class="inline-flex w-full justinline-flex justify-center rounded bg-greenXS px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-greenXS sm:ml-3 sm:w-auto">Resend
                                verification email</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('script')
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<script>
    // Get the current language from the URL or use default
    function getCurrentLang() {
        const path = window.location.pathname;
        const match = path.match(/\/([a-z]{2})\//);
        return match ? match[1] : '';
    }

    // AJAX Form Submission
    $(document).ready(function() {
        $('#forgot-password-form').on('submit', function(e) {
            e.preventDefault();
            
            const form = $(this);
            const submitButton = form.find('button[type="submit"]');
            const originalButtonText = submitButton.html();
            
            // Disable submit button and show loading state
            submitButton.prop('disabled', true);
            submitButton.html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Loading...');
            
            // Clear previous errors
            $('.tooltip-error').removeClass('tooltip-show').addClass('tooltip-hide');
            setTimeout(() => {
                $('.tooltip-error').remove();
            }, 200);
            
            // Get form data
            const formData = form.serialize();
            const lang = getCurrentLang();
            const url = lang ? `/${lang}/forgot-password` : '/forgot-password';
            
            // Make AJAX request
            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    if (response.success) {
                        // Show success modal using existing my-modal
                        const successMessage = response.message || 'Password reset link has been sent to your email.';
                        showSuccessModal(successMessage);
                        // Reset form
                        form[0].reset();
                        submitButton.prop('disabled', false);
                        submitButton.html(originalButtonText);
                    } else {
                        // Handle errors
                        handleForgotPasswordError(response);
                        submitButton.prop('disabled', false);
                        submitButton.html(originalButtonText);
                    }
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;
                    
                    if (xhr.status === 422) {
                        // Validation errors
                        handleValidationErrors(response.errors || {});
                    } else if (response && response.error) {
                        // General error - use existing error-modal
                        showErrorModal(response.error, response.verify_email, response.email);
                    } else {
                        // Network or server error
                        showErrorModal('An error occurred. Please try again.');
                    }
                    
                    submitButton.prop('disabled', false);
                    submitButton.html(originalButtonText);
                }
            });
        });
    });

    // Handle validation errors
    function handleValidationErrors(errors) {
        // Remove existing error tooltips
        $('.tooltip-error').removeClass('tooltip-show').addClass('tooltip-hide');
        setTimeout(() => {
            $('.tooltip-error').remove();
        }, 200);
        
        // Add new error tooltips with animation
        setTimeout(() => {
            Object.keys(errors).forEach(function(field) {
                const input = $(`#${field}`);
                const errorMessage = errors[field][0];
                
                if (input.length) {
                    const tooltip = $(`
                        <div class="relative tooltip tooltip-error tooltip-init -bottom-4">
                            <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                                <p class="text-white leading-none text-sm lg:text-base">${errorMessage}</p>
                            </div>
                        </div>
                    `);
                    
                    input.parent().append(tooltip);
                    // Trigger animation - remove init class and add show class
                    setTimeout(() => {
                        tooltip.removeClass('tooltip-init').addClass('tooltip-show');
                    }, 10);
                }
            });
        }, 200);
    }

    // Handle forgot password errors
    function handleForgotPasswordError(response) {
        if (response.error) {
            showErrorModal(response.error, response.verify_email, response.email);
        } else if (response.errors) {
            handleValidationErrors(response.errors);
        }
    }

    // Show success modal using existing my-modal structure
    function showSuccessModal(message) {
        // Use existing my-modal from template
        let modal = $('#my-modal');
        if (modal.length === 0) {
            console.error('Success modal not found');
            alert(message);
            return;
        }
        
        // Update message in existing modal
        const messageElement = modal.find('.can-exp-p.text-center.text-black.mt-5');
        if (messageElement.length) {
            messageElement.html(message);
        } else {
            // Fallback: find any .can-exp-p in the modal
            const fallbackElement = modal.find('.can-exp-p');
            if (fallbackElement.length) {
                fallbackElement.html(message);
            }
        }
        
        // Remove hidden class and show modal
        modal.removeClass('hidden');
        modal.css('display', 'block');
        modal.show(); // Use jQuery show() as well to ensure it's visible
    }

    // Show error modal using existing error-modal structure
    function showErrorModal(message, verifyEmail, email) {
        // Use existing error-modal from template
        let modal = $('#error-modal');
        if (modal.length === 0) {
            console.error('Error modal not found');
            alert(message);
            return;
        }
        
        // Update message in existing modal
        const messageElement = modal.find('.can-exp-p.text-center.text-black');
        if (messageElement.length) {
            messageElement.html(message);
        } else {
            // Fallback: find any .can-exp-p in the modal
            const fallbackElement = modal.find('.can-exp-p');
            if (fallbackElement.length) {
                fallbackElement.html(message);
            }
        }
        
        // Handle verify email button if needed
        const buttonContainer = modal.find('.verify-email-container');
        
        if (verifyEmail && email && buttonContainer.length) {
            const lang = getCurrentLang();
            const verifyUrl = `/send-email-verify/${encodeURIComponent(email)}`;
            // Check if verify button already exists, if not add it
            if (buttonContainer.find('a[href*="send-email-verify"]').length === 0) {
                const closeButton = buttonContainer.find('a').last();
                closeButton.before(`
                    <a href="${verifyUrl}" class="inline-flex justify-center rounded bg-primary px-3 py-2 whitespace-nowrap font-FuturaMdCnBT text-lg text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-primary/80 sm:ml-3 w-auto">Request a new verification email</a>
                `);
            }
        } else if (buttonContainer.length) {
            // Remove verify button if it exists but shouldn't
            buttonContainer.find('a[href*="send-email-verify"]').remove();
        }
        
        // Remove hidden class and show modal
        modal.removeClass('hidden');
        modal.css('display', 'block');
        modal.show(); // Use jQuery show() as well to ensure it's visible
    }

    function hideTooltip(parms) {
        const tooltip = $(this).closest('.mt-2, .relative, div').find('.tooltip-error');
        if (tooltip.length > 0 && parms != 'label') {
            tooltip.removeClass('tooltip-show').addClass('tooltip-hide');
            setTimeout(() => {
                tooltip.remove();
            }, 200);
        }
    }

    const inputs = document.querySelectorAll('input, textarea');
    inputs.forEach(input => {
        input.addEventListener('input', hideTooltip);
    });

    const labels = document.querySelectorAll('label');
    labels.forEach(label => {
        label.addEventListener('click', function (e) {
            hideTooltip.call(this, 'label');
        });
    });

    function closeModal(modalId, event) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'none';
            // Clear validation errors and reset form
            const form = document.getElementById('forgot-password-form');
            if (form) {
                form.reset(); // Reset form fields
                const errorElements = form.querySelectorAll('.tooltip-error');
                errorElements.forEach(element => element.remove()); // Remove error messages
            }
        }
    }
</script>

<style>
    /* Tooltip Animation Styles */
    .tooltip-error {
        opacity: 1;
        transform: scale(1) translateY(0);
        pointer-events: auto;
        transition: opacity 0.2s ease-out, transform 0.2s ease-out;
        display: block;
    }

    /* Initial hidden state for dynamically created tooltips */
    .tooltip-error.tooltip-init {
        opacity: 0;
        transform: scale(0.95) translateY(-5px);
        pointer-events: none;
    }

    .tooltip-error.tooltip-show {
        opacity: 1;
        transform: scale(1) translateY(0);
        pointer-events: auto;
    }

    .tooltip-error.tooltip-hide {
        opacity: 0;
        transform: scale(0.95) translateY(-5px);
        pointer-events: none;
    }

    /* Ensure tooltips are visible when they have the show class */
    .tooltip-error.tooltip-show .tooltiptext {
        display: flex !important;
    }

    /* Loading spinner styles */
    .spinner-border-sm {
        width: 1rem;
        height: 1rem;
        border-width: 0.15em;
        display: inline-block;
        vertical-align: text-bottom;
        border: 0.15em solid currentColor;
        border-right-color: transparent;
        border-radius: 50%;
        animation: spinner-border 0.75s linear infinite;
    }

    @keyframes spinner-border {
        to {
            transform: rotate(360deg);
        }
    }
</style>
@endsection
