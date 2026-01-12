@extends('layouts.template')
@section('style')
<style>
    /* Tooltip animation styles */
    .tooltip {
        opacity: 0;
        visibility: hidden;
        transform: translateY(-5px);
        transition: opacity 0.5s ease-in-out, visibility 0.5s ease-in-out, transform 0.5s ease-in-out;
        pointer-events: none;
        position: relative;
    }

    /* Visible tooltip state */
    .tooltip.show,
    .tooltip.show.hidden {
        opacity: 1 !important;
        visibility: visible !important;
        transform: translateY(0) !important;
        pointer-events: auto;
    }

    /* Tooltips that should be visible (error messages on load) */
    .tooltip:not(.hidden):not([style*="display: none"]) {
        display: flex;
    }

    /* Hide tooltips properly */
    .tooltip.hidden {
        opacity: 0;
        visibility: hidden;
        transform: translateY(-5px);
        pointer-events: none;
    }

    /* Hover tooltips - smooth animation */
    .group:hover .tooltip:not(.hidden),
    .peer:hover ~ .tooltip:not(.hidden) {
        display: flex !important;
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
        pointer-events: auto;
        transition: opacity 0.2s ease-in-out, visibility 0.2s ease-in-out, transform 0.2s ease-in-out;
    }

    /* Ensure tooltip container doesn't cause layout shifts */
    .tooltip {
        min-height: 0;
        margin: 0;
        padding: 0;
    }

    /* Fix tooltip text positioning */
    .tooltip .tooltiptext {
        position: relative;
        z-index: 1000;
    }

    /* Prevent input field from jumping when error appears */
    .mt-2 {
        min-height: auto;
    }

    /* Smooth error border transition */
    input.ring-red-500 {
        transition: ring-color 0.2s ease-in-out;
    }

    /* Modal border styling */
    .modal-border {
        border: 3px solid #00A99D;
    }
</style>
@endsection
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

                    <form class="space-y-6" method="POST" action="{{ route('forgot.password', ['lang' => $selectedLanguage->abbreviation]) }}" id="forgot-password-form">
                        @csrf

                        @if (session('error'))
                            <div id="error-modal" class="relative z-50" aria-labelledby="modal-title" role="dialog"
                                aria-modal="true" onclick="closeModal('error-modal', event)">
                                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                                <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                                    <div class="flex min-h-full justify-center p-4 text-center items-center sm:p-0">
                                        <div class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl modal-border"
                                            onclick="event.stopPropagation()">
                                            <button onclick="closeModal('error-modal', event)"
                                                class="absolute top-2 right-2 p-1 rounded-full hover:bg-gray-100">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M6 18L18 6M6 6l12 12" />
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
                                                <div class="text-center">
                                                    <div class="w-full">
                                                        <p class="can-exp-p text-center text-black">{!! session('error') !!}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div
                                                class="px-4 pb-6 pt-4 flex items-center space-x-2 sm:space-x-4 sm:px-6 justify-center">
                                                <a href=""
                                                    class="inline-flex w-full justify-center rounded bg-red-500 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-red-400 sm:ml-3 {{ session('verify_email') != null && session('verify_email') == true ? '' : 'sm:w-24' }}">Close</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if (session('message'))
                            <div id="success-modal" class="relative z-50" aria-labelledby="modal-title" role="dialog"
                                aria-modal="true" style="display: block;">
                                <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                                    <div
                                        class="relative flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                                        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                                            onclick="closeModal('success-modal', event)"></div>
                                        <div
                                            class="relative animate__animated animate__fadeIn z-20 transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
                                            <button onclick="closeModal('success-modal', event)"
                                                class="absolute top-2 right-2 p-1 rounded-full hover:bg-gray-100">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
                                                <div class="text-center sm:ml-4 sm:mt-0">
                                                    <div class="w-full">
                                                        <p class="can-exp-p text-center text-black mt-5" id="success-modal-message">
                                                            {{ session('message') }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="px-4 pb-6 pt-4  sm:flex sm:flex-row-reverse sm:px-6 justify-center">
                                                <button onclick="closeModal('success-modal', event)"
                                                    class="inline-flex w-full justify-center rounded bg-greenXS px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-greenXS sm:ml-3 sm:w-24">Close</button>
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
                                    <div class="relative tooltip -bottom-4 group-hover:flex">
                                        <div role="tooltip"
                                            class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                                            <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                        </div>
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="flex items-center justify-center flex-col sm:flex-col md:flex-row lg:flex-row">
                            <div>
                                @isset($forgotPasswordPage->button_label)
                                    <button id="forgot-password-button" class="button-exp-fill flex w-full justify-center" type="submit">
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
                        class="relative animate__animated animate__fadeIn z-20 transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
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
<script>
    // Function to close modal
    function closeModal(modalId, event) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
    }

    // Function to show tooltip with animation
    function showTooltip(tooltip) {
        if (!tooltip) return;
        
        tooltip.classList.remove('hidden');
        tooltip.style.display = 'flex';
        tooltip.style.opacity = '';
        tooltip.style.visibility = '';
        tooltip.style.transform = '';
        
        void tooltip.offsetHeight;
        
        requestAnimationFrame(function() {
            requestAnimationFrame(function() {
                tooltip.classList.add('show');
            });
        });
    }

    // Function to hide tooltip with animation
    function hideTooltip(tooltip) {
        if (!tooltip) return;
        
        tooltip.classList.remove('show');
        
        setTimeout(function() {
            tooltip.classList.add('hidden');
            tooltip.style.display = 'none';
            tooltip.style.opacity = '';
            tooltip.style.visibility = '';
            tooltip.style.transform = '';
        }, 200);
    }

    // Function to clear all error messages
    function clearAllErrors() {
        document.querySelectorAll('.tooltip').forEach(function(tooltip) {
            tooltip.classList.remove('show');
            tooltip.classList.add('hidden');
            tooltip.style.display = 'none';
            tooltip.style.opacity = '';
            tooltip.style.visibility = '';
            tooltip.style.transform = '';
        });
        document.querySelectorAll('#forgot-password-form input').forEach(function(input) {
            input.classList.remove('ring-red-500', 'ring-2');
        });
    }

    // Function to display validation errors
    function displayValidationErrors(errors) {
        clearAllErrors();
        
        setTimeout(function() {
            if (errors && typeof errors === 'object') {
                Object.keys(errors).forEach(function(fieldName) {
                    var field = document.querySelector('[name="' + fieldName + '"]');
                    if (field) {
                        field.classList.add('ring-red-500', 'ring-2');

                        var fieldContainer = field.closest('.mt-2') || field.parentElement;
                        var errorContainer = null;
                        
                        if (fieldContainer) {
                            errorContainer = fieldContainer.querySelector('.tooltip');
                        }
                        
                        if (!errorContainer && fieldContainer && fieldContainer.parentElement) {
                            errorContainer = fieldContainer.parentElement.querySelector('.tooltip');
                        }

                        if (errorContainer) {
                            var errorText = errorContainer.querySelector('p');
                            if (errorText) {
                                errorText.textContent = errors[fieldName][0];
                            }
                            errorContainer.classList.remove('hidden');
                            errorContainer.style.display = 'flex';
                            errorContainer.style.opacity = '';
                            errorContainer.style.visibility = '';
                            errorContainer.style.transform = '';
                            showTooltip(errorContainer);
                        } else {
                            var errorDiv = document.createElement('div');
                            errorDiv.className = 'relative tooltip -bottom-4 hidden';
                            errorDiv.innerHTML = '<div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded"><p class="text-white leading-none text-sm lg:text-base">' + errors[fieldName][0] + '</p></div>';
                            
                            if (fieldContainer) {
                                fieldContainer.appendChild(errorDiv);
                                setTimeout(function() {
                                    showTooltip(errorDiv);
                                }, 50);
                            } else if (field.parentElement) {
                                field.parentElement.appendChild(errorDiv);
                                setTimeout(function() {
                                    showTooltip(errorDiv);
                                }, 50);
                            }
                        }
                    }
                });
                
                setTimeout(function() {
                    var firstErrorField = document.querySelector('#forgot-password-form input.ring-red-500');
                    if (firstErrorField) {
                        firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }, 500);
            }
        }, 10);
    }

    // Function to show error modal
    function showErrorModal(message) {
        var errorModal = document.getElementById('error-modal');
        if (errorModal) {
            var messageElement = errorModal.querySelector('.can-exp-p');
            if (messageElement) {
                messageElement.innerHTML = message;
            }
            errorModal.style.display = 'block';
            document.body.style.overflow = 'hidden';
            window.scrollTo({ top: 0, behavior: 'smooth' });
        } else {
            alert(message);
        }
    }

    // Function to show success modal
    function showSuccessModal(message) {
        var successModal = document.getElementById('success-modal');
        if (!successModal) {
            // Create modal if it doesn't exist
            var modalHtml = '<div id="success-modal" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: block;">' +
                '<div class="fixed inset-0 z-10 w-screen overflow-y-auto">' +
                '<div class="relative flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">' +
                '<div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeModal(\'success-modal\', event)"></div>' +
                '<div class="relative animate__animated animate__fadeIn z-20 transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">' +
                '<button onclick="closeModal(\'success-modal\', event)" class="absolute top-2 right-2 p-1 rounded-full hover:bg-gray-100">' +
                '<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">' +
                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />' +
                '</svg></button>' +
                '<div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">' +
                '<div class="sm:flex sm:items-start justify-center"></div>' +
                '<div class="text-center sm:ml-4 sm:mt-0">' +
                '<div class="w-full">' +
                '<p class="can-exp-p text-center text-black mt-5" id="success-modal-message">' + message + '</p>' +
                '</div></div></div>' +
                '<div class="px-4 pb-6 pt-4 sm:flex sm:flex-row-reverse sm:px-6 justify-center">' +
                '<button onclick="closeModal(\'success-modal\', event)" class="inline-flex w-full justify-center rounded bg-greenXS px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-greenXS sm:ml-3 sm:w-24">Close</button>' +
                '</div></div></div></div></div>';
            document.body.insertAdjacentHTML('beforeend', modalHtml);
            successModal = document.getElementById('success-modal');
        }
        if (successModal) {
            var messageElement = successModal.querySelector('#success-modal-message') || successModal.querySelector('.can-exp-p');
            if (messageElement) {
                messageElement.textContent = message;
            }
            successModal.style.display = 'block';
            document.body.style.overflow = 'hidden';
            window.scrollTo({ top: 0, behavior: 'smooth' });
        } else {
            alert(message);
        }
    }

    // Function to show email verification modal
    function showEmailVerificationModal(userEmail) {
        var verificationModal = document.getElementById('email-verification-modal');
        if (verificationModal) {
            var linkElement = verificationModal.querySelector('a[href*="sendEmailVerify"]');
            if (linkElement && userEmail) {
                linkElement.setAttribute('href', linkElement.getAttribute('href').replace(/email=[^&]+/, 'email=' + userEmail));
            }
            verificationModal.style.display = 'block';
            document.body.style.overflow = 'hidden';
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    }

    // AJAX Form Submission
    var forgotPasswordForm = document.getElementById('forgot-password-form');
    if (forgotPasswordForm) {
        forgotPasswordForm.addEventListener('submit', function (e) {
            e.preventDefault();
            e.stopPropagation();

            var form = this;
            var submitButton = document.getElementById('forgot-password-button');
            var formData = new FormData(form);
            var originalButtonText = submitButton ? submitButton.innerHTML : '';

            clearAllErrors();

            if (submitButton) {
                submitButton.setAttribute('disabled', 'true');
                submitButton.innerHTML = '<span>Processing...</span>';
            }

            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(function(response) {
                var contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    return response.json().then(function(data) {
                        if (!response.ok) {
                            throw data;
                        }
                        return data;
                    });
                } else {
                    return response.text().then(function(html) {
                        if (response.status === 422) {
                            throw { errors: 'Validation error occurred' };
                        }
                        return { html: html };
                    });
                }
            })
            .then(function(data) {
                console.log('Forgot password response:', data);
                
                if (data.success) {
                    form.reset();
                    
                    if (data.message) {
                        showSuccessModal(data.message);
                    } else {
                        showSuccessModal('Password reset email has been sent successfully.');
                    }
                } else if (data.showModal) {
                    showEmailVerificationModal(data.user ? data.user.email : null);
                } else if (data.errors) {
                    displayValidationErrors(data.errors);
                } else if (data.error) {
                    showErrorModal(data.error);
                } else {
                    console.warn('Unexpected response format:', data);
                }
            })
            .catch(function(error) {
                console.error('Error:', error);
                
                if (error.errors) {
                    displayValidationErrors(error.errors);
                } else if (error.error) {
                    showErrorModal(error.error);
                } else {
                    alert('An error occurred. Please check your input and try again.');
                }
            })
            .finally(function() {
                if (submitButton) {
                    submitButton.removeAttribute('disabled');
                    if (originalButtonText) {
                        submitButton.innerHTML = originalButtonText;
                    }
                }
            });
        });
    }

    // Hide error messages on input
    document.addEventListener('DOMContentLoaded', function () {
        setTimeout(function() {
            document.querySelectorAll('.tooltip:not(.hidden)').forEach(function(tooltip) {
                var hasText = tooltip.querySelector('p') && tooltip.querySelector('p').textContent.trim() !== '';
                if (hasText && tooltip.style.display !== 'none') {
                    tooltip.style.opacity = '';
                    tooltip.style.visibility = '';
                    tooltip.style.transform = '';
                    showTooltip(tooltip);
                }
            });
        }, 100);

        var emailInput = document.getElementById('email');
        if (emailInput) {
            emailInput.addEventListener('input', function() {
                var parentDiv = this.closest('div');
                if (parentDiv) {
                    var errorMessage = parentDiv.querySelector('.tooltip');
                    if (errorMessage) {
                        hideTooltip(errorMessage);
                        this.classList.remove('ring-red-500', 'ring-2');
                    }
                }
            });
        }
    });
</script>
@endsection