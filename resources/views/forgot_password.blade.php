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

                        @if (session('error'))
                            <div id="error-modal" class="relative z-50" aria-labelledby="modal-title" role="dialog"
                                aria-modal="true" onclick="closeModal('error-modal', event)">
                                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                                <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                                    <div class="flex min-h-full justify-center p-4 text-center items-center sm:p-0">
                                        <div class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl"
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
                            <div id="my-modal" class="relative z-50" aria-labelledby="modal-title" role="dialog"
                                aria-modal="true">
                                <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                                    <div
                                        class="relative flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                                        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                                            onclick="closeModal('my-modal', event)"></div>
                                        <div
                                            class="relative animate__animated animate__fadeIn z-20 transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                                            <button onclick="closeModal('my-modal', event)"
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
                                                        <p class="can-exp-p text-center text-black mt-5">
                                                            {{ session('message') }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="px-4 pb-6 pt-4  sm:flex sm:flex-row-reverse sm:px-6 justify-center">
                                                <a href=""
                                                    class="inline-flex w-full justify-center rounded bg-greenXS px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-greenXS sm:ml-3 sm:w-24">Close</a>
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
<script>
    //    function closeModal() {
    //         document.getElementById('my-modal').style.display = 'none';
    //     }
    function closeModal(modalId, event) {
        event.preventDefault(); // Prevent default behavior
        event.stopPropagation(); // Stop event from propagating to other elements
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'none';
            // Clear validation errors and reset form
            const form = document.getElementById('forgot-password-form');
            if (form) {
                form.reset(); // Reset form fields
                const errorElements = form.querySelectorAll('.tooltip');
                errorElements.forEach(element => element.style.display = 'none'); // Hide error messages
            }
        }
    }

    // Debugging: Log when form is submitted
    document.getElementById('forgot-password-form').addEventListener('submit', function(event) {
        console.log('Form submit attempted');
    });
</script>
