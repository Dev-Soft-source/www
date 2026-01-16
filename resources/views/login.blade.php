@extends('layouts.template')

@section('content')

<div class="mx-auto max-w-2xl lg:max-w-xl">
    <div class="flex min-h-full flex-col justify-center my-14 px-4 sm:px-6 lg:px-8">
        <div class="bg-white border border-gray-100 p-4 shadow rounded-md sm:px-10">
            <div class="sm:mx-auto sm:w-full sm:max-w-md">
                <h1 class="text-center font-FuturaMdCnBT can-exp-h1 text-primary mt-10">
                    @isset($loginPage->main_heading)
                        {{ $loginPage->main_heading }}
                    @endisset
                </h1>
            </div>
            <div class="sm:mx-auto sm:w-full sm:max-w-xl md:min-w-[26rem]">
                <h2 class="my-4 text-center text-2xl md:text-3xl text-primary font-FuturaMdCnBT">
                    @isset($loginPage->continue_label)
                        {{ $loginPage->continue_label }}
                    @endisset
                </h2>
                <form id="login-form" class="space-y-6" method="POST" action="">
                    @csrf
                    @if(session('error'))
                        <div id="error-modal" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true" onclick="closeModal('error-modal')">
                            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                                <div class="flex min-h-full justify-center p-4 text-center items-center sm:p-0">
                                    <div
                                        class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl modal-border" onclick="event.stopPropagation()">
                                        <a onclick="closeModal('error-modal')" class="absolute top-4 right-4 p-1 rounded-full hover:bg-gray-100 z-50">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </a>
                                        <div class="bg-white px-4 mt-10 sm:mt-1 pb-4 pt-16 sm:p-6 sm:pb-4 sm:pt-16">
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
                                                    <p class="can-exp-p text-center text-black">{!! session('error') !!}</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="px-4 pb-6 pt-4 flex items-center space-x-2 sm:space-x-4 sm:px-6 justify-center">

                                            @if (session('verify_email') != null && session('verify_email') == true)
                                            <a href="{{route('sendEmailVerify', ['email' => session('email')])}}"
                                            class="inline-flex justify-center rounded bg-primary px-3 py-2 whitespace-nowrap font-FuturaMdCnBT text-lg text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-primary/80 sm:ml-3 w-auto">{{ $loginPage->new_verification_email_btn_label ?? "Request a new verification email" }}</a>
                                            @endif

                                            <a onclick="closeModal('error-modal')"
                                                class="inline-flex justify-center rounded bg-red-500 px-3 py-2 font-FuturaMdCnBT text-lg text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-red-400 sm:ml-3 w-auto">Close</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(session('message'))
                        <div id="message-modal" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity modal-backdrop"></div>
                            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                                <div class="flex min-h-full justify-center p-4 text-center items-center sm:p-0">
                                    <div
                                        class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl modal-border">
                                        <button onclick="closeModal('message-modal')" class="absolute top-4 right-4 p-1 rounded-full hover:bg-gray-100 z-50">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
                                            <div class="text-center">
                                                <div class="w-full">
                                                    <p class="can-exp-p text-center">{!! session('message') !!}</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="px-4 pb-6 pt-4 flex items-center space-x-2 sm:space-x-4 sm:px-6 justify-center">

                                            <a href=""
                                                class="inline-flex justify-center rounded bg-red-500 px-3 py-2 font-FuturaMdCnBT text-lg text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-red-400 sm:ml-3 w-auto">Close</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(session('success'))
                    <div id="my-modal" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                            <div class="relative flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity modal-backdrop"></div>
                                <div class="relative animate__animated animate__fadeIn z-20 transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
                                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                                        <div class="flex justify-end">
                                            <button onclick="closeModal('my-modal')" class="p-1 rounded-full hover:bg-gray-100">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                        <div class="text-center mt-2">
                                            <div class="w-full">
                                                <p class="can-exp-p text-center">{!! session('success') !!}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="px-4 pb-6 pt-4 flex items-center justify-center">
                                        <button onclick="closeModal('my-modal')" class="button-exp-fill px-8">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div>
                        <div class="mt-2">
                            @isset($loginPage->email_label)
                                <label for="email">
                                    {{ $loginPage->email_label }}
                                </label>
                            @endisset
                            <input id="email"
                                @isset($loginPage->email_placeholder)
                                    placeholder="{{ $loginPage->email_placeholder }}"
                                @endisset
                                class="block w-full rounded text-lg border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-500 focus:ring-2 focus:ring-inset focus:ring-blue-600"
                                type="text" name="email" value="{{ old('email') }}" autofocus />
                            @error('email')
                              <div class="relative tooltip tooltip-error -bottom-4 group-hover:flex">
                                <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                    <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                </div>
                              </div>
                            @enderror
                        </div>
                    </div>

                    <div>
                        @isset($loginPage->password_label)
                        <label for="password">
                            {{ $loginPage->password_label }}
                        </label>
                        @endisset
                        <div class="mt-2 relative">
                            <input
                                @isset($loginPage->password_placeholder)
                                    placeholder="{{ $loginPage->password_placeholder }}"
                                @endisset
                                class="block w-full rounded text-lg border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-500 focus:ring-2 focus:ring-inset focus:ring-blue-600"
                                id="password" type="password" name="password" autocomplete="current-password" />
                            <span id="togglePassword" class="absolute right-3 top-2.5">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 cursor-pointer text-gray-600">
                                    <path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" />
                                    <path fill-rule="evenodd" d="M1.323 11.447C2.811 6.976 7.028 3.75 12.001 3.75c4.97 0 9.185 3.223 10.675 7.69.12.362.12.752 0 1.113-1.487 4.471-5.705 7.697-10.677 7.697-4.97 0-9.186-3.223-10.675-7.69a1.762 1.762 0 0 1 0-1.113ZM17.25 12a5.25 5.25 0 1 1-10.5 0 5.25 5.25 0 0 1 10.5 0Z" clip-rule="evenodd" />
                                </svg>
                            </span>
                            @error('password')
                              <div class="relative tooltip tooltip-error -bottom-4 group-hover:flex">
                                <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                    <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                </div>
                              </div>
                            @enderror
                        </div>
                        <div class="mt-2 items-center flex gap-2">
                            <input type="checkbox" name="remember" id="remember" class="form-check-input">
                            <label for="remember" class="form-check-label mt-1 text-sm md:text-base">{{ $loginPage->remember_me_text ?? "Remember me" }}</label>
                        </div>
                    </div>
                    <div class="flex w-full justify-center">
                        @isset($loginPage->submit_button_label)
                            <button class="button-exp-fill w-28 " type="submit">
                                {{ $loginPage->submit_button_label }}
                            </button>
                        @endisset
                    </div>
                    <div class="flex items-center justify-end">
                        <div class="text-sm">
                            <a tabindex="-1" href="{{ route('forgot.password', ['lang' => $selectedLanguage->abbreviation]) }}"
                                class="font-medium text-blue-600 hover:text-indigo-500 text-sm md:text-base ">
                                @isset($loginPage->forgot_password_label)
                                    {{ $loginPage->forgot_password_label }}
                                @endisset
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <div class="sm:mx-auto sm:w-full sm:max-w-md flex justify-center border-b border-black mb-8">
                <p class="mt-6 text-center bg-white w-fit px-2 text-2xl md:text-3xl font-FuturaMdCnBT -mb-4">
                    @isset($loginPage->or_label)
                        {{ $loginPage->or_label }}
                    @endisset
                </p>
            </div>
            <div class="flex items-center justify-center space-x-1.5 md:space-x-4 my-4">
                <a href="{{ route('signup.redirectToProvider', ['lang' => $selectedLanguage->abbreviation, 'provider' => 'facebook']) }}" class="w-12 md:w-14 h-12 md:h-14 rounded border flex justify-center items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" x="0px" y="0px" width="100" height="100"
                        viewBox="0,0,256,256">
                        <g fill="#1877f2" fill-rule="nonzero" stroke="none" stroke-width="1" stroke-linecap="butt"
                            stroke-linejoin="miter" stroke-miterlimit="10" stroke-dasharray="" stroke-dashoffset="0"
                            font-family="none" font-weight="none" font-size="none" text-anchor="none"
                            style="mix-blend-mode: normal">
                            <g transform="scale(5.33333,5.33333)">
                                <path
                                    d="M29,3c-5.523,0 -10,4.477 -10,10v5h-6v8h6v19h8v-19h7l1,-8h-8v-4c0,-2.209 1.791,-4 4,-4h4v-6.678c-1.909,-0.197 -4.079,-0.326 -6,-0.322z">
                                </path>
                            </g>
                        </g>
                    </svg>
                </a>
                <a href="{{ route('signup.redirectToProvider', ['lang' => $selectedLanguage->abbreviation, 'provider' => 'linkedin-openid']) }}" class="w-12 md:w-14 h-12 md:h-14 rounded border flex justify-center items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" x="0px" y="0px" width="100" height="100"
                        viewBox="0 0 48 48">
                        <path fill="#0288d1"
                            d="M8.421 14h.052 0C11.263 14 13 12 13 9.5 12.948 6.945 11.263 5 8.526 5 5.789 5 4 6.945 4 9.5 4 12 5.736 14 8.421 14zM4 17H13V43H4zM44 26.5c0-5.247-4.253-9.5-9.5-9.5-3.053 0-5.762 1.446-7.5 3.684V17h-9v26h9V28h0c0-2.209 1.791-4 4-4s4 1.791 4 4v15h9C44 43 44 27.955 44 26.5z">
                        </path>
                    </svg>
                </a>
                <a href="{{ route('signup.redirectToProvider', ['lang' => $selectedLanguage->abbreviation, 'provider' => 'google']) }}" class="w-12 md:w-14 h-12 md:h-14 rounded border flex justify-center items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" x="0px" y="0px" width="100" height="100"
                        viewBox="0 0 48 48">
                        <path fill="#FFC107"
                            d="M43.611,20.083H42V20H24v8h11.303c-1.649,4.657-6.08,8-11.303,8c-6.627,0-12-5.373-12-12c0-6.627,5.373-12,12-12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C12.955,4,4,12.955,4,24c0,11.045,8.955,20,20,20c11.045,0,20-8.955,20-20C44,22.659,43.862,21.35,43.611,20.083z">
                        </path>
                        <path fill="#FF3D00"
                            d="M6.306,14.691l6.571,4.819C14.655,15.108,18.961,12,24,12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C16.318,4,9.656,8.337,6.306,14.691z">
                        </path>
                        <path fill="#4CAF50"
                            d="M24,44c5.166,0,9.86-1.977,13.409-5.192l-6.19-5.238C29.211,35.091,26.715,36,24,36c-5.202,0-9.619-3.317-11.283-7.946l-6.522,5.025C9.505,39.556,16.227,44,24,44z">
                        </path>
                        <path fill="#1976D2"
                            d="M43.611,20.083H42V20H24v8h11.303c-0.792,2.237-2.231,4.166-4.087,5.571c0.001-0.001,0.002-0.001,0.003-0.002l6.19,5.238C36.971,39.205,44,34,44,24C44,22.659,43.862,21.35,43.611,20.083z">
                        </path>
                    </svg>
                </a>
                <a id="apple-login-btn" href="{{ route('signup.redirectToProvider', ['lang' => $selectedLanguage->abbreviation, 'provider' => 'apple']) }}" class="w-12 md:w-14 h-12 md:h-14 rounded border flex justify-center items-center" style="display: none;">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" x="0px" y="0px" width="100" height="100"
                        viewBox="0 0 48 48">
                        <path fill="#000000" d="M 39.928 17.352 C 39.686 17.54 35.406 19.952 35.406 25.315 C 35.406 31.519 40.852 33.712 41.015 33.767 C 40.99 33.901 40.15 36.773 38.144 39.699 C 36.354 42.274 34.486 44.844 31.644 44.844 C 28.801 44.844 28.07 43.193 24.788 43.193 C 21.59 43.193 20.454 44.898 17.854 44.898 C 15.253 44.898 13.439 42.516 11.353 39.589 C 8.936 36.154 6.985 30.815 6.985 25.75 C 6.985 17.623 12.268 13.314 17.469 13.314 C 20.232 13.314 22.535 15.128 24.27 15.128 C 25.92 15.128 28.496 13.206 31.639 13.206 C 32.83 13.206 37.111 13.314 39.928 17.352 Z M 30.147 9.765 C 31.447 8.222 32.367 6.082 32.367 3.942 C 32.367 3.645 32.341 3.344 32.286 3.102 C 30.174 3.181 27.655 4.51 26.138 6.271 C 24.947 7.625 23.835 9.765 23.835 11.935 C 23.835 12.26 23.89 12.587 23.914 12.691 C 24.048 12.716 24.266 12.745 24.482 12.745 C 26.381 12.745 28.768 11.475 30.147 9.765 Z" style="stroke-width: 1;"></path>
                    </svg>
                </a>
            </div>

            <div class="sm:mx-auto sm:w-full sm:max-w-md">
                <p class="mt-3 text-center">

                </p>
            </div>



            <div class="sm:mx-auto sm:w-full sm:max-w-md">
                <div class="mt-6 text_center Signup-bottom">
                    @isset($loginPage->signup_label)
                        <?php
                            // Define the desired URL
                            $desiredUrl = route('signup', ['lang' => $selectedLanguage->abbreviation]);

                            // Define the pattern to match the href attribute
                            $pattern = '/href=["\']([^"\']+)["\']/';

                            // Replace all occurrences of the href attribute with the desired URL
                            $loginPageContent = preg_replace($pattern, 'href="' . $desiredUrl . '"', $loginPage->signup_label);
                        ?>
                        {!! $loginPageContent !!}
                    @endisset
                </div>
            </div>
        </div>

        <div class="rounded-md p-3 mt-6 mb-10 shadow bg-white"><div class="flex items-center gap-2"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#2563eb" class="bi bi-shield-shaded w-5 h-5" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M8 14.933a.615.615 0 0 0 .1-.025c.076-.023.174-.061.294-.118.24-.113.547-.29.893-.533a10.726 10.726 0 0 0 2.287-2.233c1.527-1.997 2.807-5.031 2.253-9.188a.48.48 0 0 0-.328-.39c-.651-.213-1.75-.56-2.837-.855C9.552 1.29 8.531 1.067 8 1.067v13.866zM5.072.56C6.157.265 7.31 0 8 0s1.843.265 2.928.56c1.11.3 2.229.655 2.887.87a1.54 1.54 0 0 1 1.044 1.262c.596 4.477-.787 7.795-2.465 9.99a11.775 11.775 0 0 1-2.517 2.453 7.159 7.159 0 0 1-1.048.625c-.28.132-.581.24-.829.24s-.548-.108-.829-.24a7.158 7.158 0 0 1-1.048-.625 11.777 11.777 0 0 1-2.517-2.453C1.928 10.487.545 7.169 1.141 2.692A1.54 1.54 0 0 1 2.185 1.43 62.456 62.456 0 0 1 5.072.56z"></path></svg>
            <p class="can-exp-p"> {{ $loginPage->protect_account_heading ?? "Protect your account" }}</p>
        </div>
        <div class="mt-2 text-left mb-2">
            <p class="text-left">{{ $loginPage->protect_account_text ?? "Whenever you sign in to the ProximaRide website, ensure that the web address in the browser starts with:" }}</p></div></div>
    </div>


</div>

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
        $('#login-form').on('submit', function(e) {
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
            const url = lang ? `/${lang}/login` : '/login';
            
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
                        // Redirect on success
                        if (response.redirect_url) {
                            window.location.href = response.redirect_url;
                        } else {
                            window.location.reload();
                        }
                    } else {
                        // Handle errors
                        handleLoginError(response);
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
                        // General error
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

    // Handle login errors
    function handleLoginError(response) {
        if (response.error) {
            showErrorModal(response.error, response.verify_email, response.email);
        } else if (response.errors) {
            handleValidationErrors(response.errors);
        }
    }

    // Show error modal
    function showErrorModal(message, verifyEmail, email) {
        // Create or update error modal
        let modal = $('#error-modal');
        if (modal.length === 0) {
            // Create modal if it doesn't exist
            modal = $(`
                <div id="error-modal" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true" onclick="closeModal('error-modal')">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                        <div class="flex min-h-full justify-center p-4 text-center items-center sm:p-0">
                            <div class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl modal-border" onclick="event.stopPropagation()">
                                <a onclick="closeModal('error-modal')" class="absolute top-4 right-4 p-1 rounded-full hover:bg-gray-100 z-50">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </a>
                                <div class="bg-white px-4 mt-10 sm:mt-1 pb-4 pt-16 sm:p-6 sm:pb-4 sm:pt-16">
                                    <div class="text-center">
                                        <div class="w-full">
                                            <p class="can-exp-p text-center text-black error-message"></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="px-4 pb-6 pt-4 flex items-center space-x-2 sm:space-x-4 sm:px-6 justify-center">
                                    <div class="verify-email-button"></div>
                                    <a onclick="closeModal('error-modal')" class="inline-flex justify-center rounded bg-red-500 px-3 py-2 font-FuturaMdCnBT text-lg text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-red-400 sm:ml-3 w-auto">Close</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `);
            $('body').append(modal);
        }
        
        modal.find('.error-message').html(message);
        
        if (verifyEmail && email) {
            const lang = getCurrentLang();
            const verifyUrl = `/send-email-verify/${encodeURIComponent(email)}`;
            modal.find('.verify-email-button').html(`
                <a href="${verifyUrl}" class="inline-flex justify-center rounded bg-primary px-3 py-2 whitespace-nowrap font-FuturaMdCnBT text-lg text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-primary/80 sm:ml-3 w-auto">Request a new verification email</a>
            `);
        } else {
            modal.find('.verify-email-button').empty();
        }
        
        modal.show();
    }

    function hideTooltip(parms) {
        const tooltip = $(this).closest('.mt-2, .relative').find('.tooltip-error');
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

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'none';
        }
    }

    const togglePassword = document.getElementById('togglePassword');
    const password = document.getElementById('password');

    if (togglePassword && password) {
        togglePassword.addEventListener('click', function () {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);

            // Change eye icon based on password visibility
            if (type === 'password') {
                togglePassword.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 cursor-pointer text-gray-600">
                        <path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" />
                        <path fill-rule="evenodd" d="M1.323 11.447C2.811 6.976 7.028 3.75 12.001 3.75c4.97 0 9.185 3.223 10.675 7.69.12.362.12.752 0 1.113-1.487 4.471-5.705 7.697-10.677 7.697-4.97 0-9.186-3.223-10.675-7.69a1.762 1.762 0 0 1 0-1.113ZM17.25 12a5.25 5.25 0 1 1-10.5 0 5.25 5.25 0 0 1 10.5 0Z" clip-rule="evenodd" />
                    </svg> `;
            } else {
                togglePassword.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" fill="currentColor" class="w-5 h-5 text-gray-600 cursor-pointer">
                    <path d="M38.8 5.1C28.4-3.1 13.3-1.2 5.1 9.2S-1.2 34.7 9.2 42.9l592 464c10.4 8.2 25.5 6.3 33.7-4.1s6.3-25.5-4.1-33.7L525.6 386.7c39.6-40.6 66.4-86.1 79.9-118.4c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C465.5 68.8 400.8 32 320 32c-68.2 0-125 26.3-169.3 60.8L38.8 5.1zM223.1 149.5C248.6 126.2 282.7 112 320 112c79.5 0 144 64.5 144 144c0 24.9-6.3 48.3-17.4 68.7L408 294.5c8.4-19.3 10.6-41.4 4.8-63.3c-11.1-41.5-47.8-69.4-88.6-71.1c-5.8-.2-9.2 6.1-7.4 11.7c2.1 6.4 3.3 13.2 3.3 20.3c0 10.2-2.4 19.8-6.6 28.3l-90.3-70.8zM373 389.9c-16.4 6.5-34.3 10.1-53 10.1c-79.5 0-144-64.5-144-144c0-6.9 .5-13.6 1.4-20.2L83.1 161.5C60.3 191.2 44 220.8 34.5 243.7c-3.3 7.9-3.3 16.7 0 24.6c14.9 35.7 46.2 87.7 93 131.1C174.5 443.2 239.2 480 320 480c47.8 0 89.9-12.9 126.2-32.5L373 389.9z"/>
                </svg>`;
            }
        });
    }

    // Detect Apple devices and show Apple login button
    (function() {
        function isAppleDevice() {
            const userAgent = navigator.userAgent || navigator.vendor || window.opera;
            
            // Check for iOS devices (iPhone, iPad, iPod)
            if (/iPad|iPhone|iPod/.test(userAgent) && !window.MSStream) {
                return true;
            }
            
            // Check for macOS (Safari on Mac)
            if (/Macintosh|Mac OS X/.test(userAgent)) {
                // Also check if it's Safari browser
                if (/Safari/.test(userAgent) && !/Chrome|Chromium|Edge/.test(userAgent)) {
                    return true;
                }
                // Or if it's any browser on macOS (since Apple Sign In works on macOS in any browser)
                return true;
            }
            
            return false;
        }
        
        // Show Apple login button if on Apple device
        const appleLoginBtn = document.getElementById('apple-login-btn');
        if (appleLoginBtn && isAppleDevice()) {
            appleLoginBtn.style.display = 'flex';
        }
    })();
</script>

@endsection
<style>
    /* Initial state: off-screen */
    .modal-hidden {
        transform: translateX(100%); /* Slide from the right */
    }

    /* When modal is visible */
    .modal-visible {
        transform: translateX(0); /* Slide to the center */
        transition: transform 0.3s ease-in-out; /* Smooth transition */
    }

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
