@extends('layouts.template')
@section('style')
<style>
    .features_tooltiptext::after {
        content: "";
        border-width: 10px;
        border-style: solid;
        border-color: #3b82f6 transparent transparent transparent;
        position: absolute;
        bottom: -20px;
        /* left: 4rem; */
    }

    /* Tooltip animation styles - Fixed UI Issues */
    .tooltip {
        opacity: 0;
        visibility: hidden;
        transform: translateY(-5px);
        transition: opacity 0.5s ease-in-out, visibility 0.5s ease-in-out, transform 0.5s ease-in-out;
        pointer-events: none; /* Prevent interaction when hidden */
        position: relative; /* Ensure proper positioning */
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
</style>
@endsection
@section('content')

<div class="mx-auto max-w-2xl lg:max-w-4xl">
    <div class="flex min-h-full flex-col justify-center py-14 px-4 sm:px-6 lg:px-0">
        <div class="bg-white border border-gray-100 p-4 shadow rounded-md sm:px-10">
          <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <h1 class="text-center font-FuturaMdCnBT text-primary text-3xl md:text-4xl lg:text-5xl">
              @isset($signupPage->main_heading)
                  {{ $signupPage->main_heading }}
              @endisset
            </h1>

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
            </div>

            <p class="mt-3 text-center font-FuturaMdCnBT">
              @isset($signupPage->or_label)
                  {{ $signupPage->or_label }}
              @endisset
            </p>
          </div>
        </div>

        <div class="mx-auto w-full lg:px-8 ">
            <div class="pt-6 flex justify-start">
                @isset($signupPage->required_label)
                    <p class="text-red-500">* {{ $signupPage->required_label }}</p>
                @endisset
            </div>
            <form id="signupForm"  method="POST" action="{{ route('signup.store', ['lang' => $selectedLanguage->abbreviation]) }}">
              @csrf

              <input type="hidden" value="{{isset($uuid) && !is_null($uuid) ? $uuid : 0}}" name="uuid" id="uuid">
              <div>
                <div class="">
                    <label for="first_name" class="font-FuturaMdCnBT">
                        @isset($signupPage->first_name_label)
                            {{ $signupPage->first_name_label }}
                        @endisset
                        <span class="text-red-500">*</span>
                    </label>
                    <input
                        @isset($signupPage->first_name_placeholder)
                            placeholder="{{ $signupPage->first_name_placeholder }}"
                        @endisset
                        id="first_name" class="block w-full rounded text-lg border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-500 focus:ring-2 focus:ring-inset focus:ring-blue-600" type="text" name="first_name" value="{{ old('first_name') }}" autofocus />
                    @error('first_name')
                      <div class="relative tooltip -bottom-4 group-hover:flex">
                        <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                            <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                        </div>
                      </div>
                    @enderror
                </div>
              </div>

              <div>
                <div class="mt-2">
                    <label for="last_name" class="flex space-x-1 font-FuturaMdCnBT">
                        @isset($signupPage->last_name_label)
                            {{ $signupPage->last_name_label }}
                        @endisset
                        <span class="text-red-500">*</span>
                    </label>
                    <input
                        @isset($signupPage->last_name_placeholder)
                            placeholder="{{ $signupPage->last_name_placeholder }}"
                        @endisset
                        id="last_name" class="block w-full rounded border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-500 focus:ring-2 focus:ring-inset focus:ring-blue-600 text-lg" type="text" name="last_name" value="{{ old('last_name') }}" autofocus />
                    @error('last_name')
                      <div class="relative tooltip -bottom-4 group-hover:flex">
                        <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                            <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                        </div>
                      </div>
                    @enderror
                </div>
              </div>

              <div>
                <div class="mt-2">
                    <label for="email" class="font-FuturaMdCnBT">
                        @isset($signupPage->email_label)
                            {{ $signupPage->email_label }}
                        @endisset
                        <span class="text-red-500">*</span>
                    </label>
                    <input
                        @isset($signupPage->email_placeholder)
                            placeholder="{{ $signupPage->email_placeholder }}"
                        @endisset
                        id="email" class="block w-full rounded text-lg border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-500 focus:ring-2 focus:ring-inset focus:ring-blue-600" type="text" name="email" value="{{ old('email') }}" autofocus />
                    @error('email')
                      <div class="relative tooltip -bottom-4 group-hover:flex">
                        <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                            <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                        </div>
                      </div>
                    @enderror
                </div>
              </div>

              <div class="mt-2">
                <label for="password" class="flex space-x-1 font-FuturaMdCnBT">
                    @isset($signupPage->password_label)
                      {{ $signupPage->password_label }}
                    @endisset
                    <span class="text-red-500">*</span>

                </label>
                <div class="mt-2 relative">
                    <input
                        @isset($signupPage->confirm_password_placeholder)
                            placeholder="{{ $signupPage->confirm_password_placeholder }}"
                        @endisset
                        class="block w-full rounded text-lg border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-500 focus:ring-2 focus:ring-inset focus:ring-blue-600" id="password" type="password" name="password"
                        value="{{ old('password') }}"
                         autocomplete="current-password" />
                        <span id="togglePassword" class="absolute right-3 top-2.5">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 cursor-pointer text-gray-600">
                              <path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" />
                              <path fill-rule="evenodd" d="M1.323 11.447C2.811 6.976 7.028 3.75 12.001 3.75c4.97 0 9.185 3.223 10.675 7.69.12.362.12.752 0 1.113-1.487 4.471-5.705 7.697-10.677 7.697-4.97 0-9.186-3.223-10.675-7.69a1.762 1.762 0 0 1 0-1.113ZM17.25 12a5.25 5.25 0 1 1-10.5 0 5.25 5.25 0 0 1 10.5 0Z" clip-rule="evenodd" />
                            </svg>
                        </span>
                    @error('password')
                    <div class="relative tooltip -bottom-4 group-hover:flex">
                        <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                            <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                        </div>
                      </div>
                    @enderror
                </div>
              </div>
              <div class="mt-2">
                <label for="password_confirmation" class="font-FuturaMdCnBT">
                    @isset($signupPage->confirm_password_label)
                        {{ $signupPage->confirm_password_label }}
                    @endisset
                    <span class="text-red-500">*</span>
                </label>
                <div class="mt-2 relative">
                    <input
                        class="block w-full rounded text-lg border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-500 focus:ring-2 focus:ring-inset focus:ring-blue-600"
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        autocomplete="current-password"
                    />
                    <span id="togglePassword2" class="absolute right-3 top-2.5">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 cursor-pointer text-gray-600">
                              <path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" />
                              <path fill-rule="evenodd" d="M1.323 11.447C2.811 6.976 7.028 3.75 12.001 3.75c4.97 0 9.185 3.223 10.675 7.69.12.362.12.752 0 1.113-1.487 4.471-5.705 7.697-10.677 7.697-4.97 0-9.186-3.223-10.675-7.69a1.762 1.762 0 0 1 0-1.113ZM17.25 12a5.25 5.25 0 1 1-10.5 0 5.25 5.25 0 0 1 10.5 0Z" clip-rule="evenodd" />
                        </svg>
                    </span>
                    @error('password_confirmation')
                        <div class="relative tooltip -bottom-4 group-hover:flex">
                            <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                            </div>
                        </div>
                    @enderror
                </div>
              </div>
              <div class="">
                <div class="mt-2 relative">
                  <label for="remember-me"
                    class="relative flex text-sm text-gray-900 font-FuturaMdCnBT">

                    <input id="remember-me" name="remember-me" type="checkbox"
                      class="mt-1.5 mr-2 h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-600">

                    <div class="text_base relative">
                      @isset($signupPage->agree_terms_label)
                        {!! preg_replace_callback(
                            '/<a\s+([^>]+)>/i',
                            function ($matches) {
                                $attrs = $matches[1];
                                if (stripos($attrs, 'target=') === false) {
                                    return '<a ' . $attrs . ' target="_blank">';
                                }
                                return '<a ' . $attrs . '>';
                            },
                            $signupPage->agree_terms_label
                        ) !!}
                      @endisset

                      @error('remember-me')
                        <div
                          role="tooltip"
                          class="absolute left-0 top-full mt-2 z-10
                                transition duration-200 ease-out
                                shadow-lg p-2 bg-red-500 rounded
                                w-full md:w-1/2">
                          <p class="text-white text-sm lg:text-base">
                            {{ $message }}
                          </p>
                        </div>
                      @enderror
                    </div>

                  </label>
                </div>
              </div>

              <div class="">
                <div class="mt-2 relative">
                  @isset($signupPage->rideshare_label)
                    <label for="rideshare-disclaimer" class="flex text-sm text-gray-900 font-FuturaMdCnBT">
                      <input id="rideshare-disclaimer" name="rideshare_disclaimer" type="checkbox" class=" mr-2 h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-600">
                      <div class="text_base">
                        <p>{{ $signupPage->rideshare_label }}</p>
                      </div>
                    </label>
                  @endisset
                </div>
                @error('rideshare_disclaimer')
                  <div class="mt-2">
                    <div class="relative tooltip group-hover:flex">
                      <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                        <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                      </div>
                    </div>
                  </div>
                @enderror
              </div>

              <div class="flex justify-center font-FuturaMdCnBT mt-4">
                <button  id="signupButton" class="button-exp-fill w-28 text-lg" type="submit">
                    @isset($signupPage->button_label)
                        {{ $signupPage->button_label }}
                    @endisset
                </button>

                <!-- <p class="mt-3 text-center">
                    @isset($signupPage->after_button_label)
                        {{ $signupPage->after_button_label }}
                    @endisset
                </p> -->

              </div>
            </form>

          <div class="mt-5 text_center font-FutuMdCnBT Signup-bottom">
            @isset($signupPage->signin_label)
              <?php
                  // Define the desired URL
                  $desiredUrl = route('login', ['lang' => $selectedLanguage->abbreviation]);

                  // Define the pattern to match the href attribute
                  $pattern = '/href=["\']([^"\']+)["\']/';

                  // Replace all occurrences of the href attribute with the desired URL
                  $signupPageContent = preg_replace($pattern, 'href="' . $desiredUrl . '"', $signupPage->signin_label);
              ?>
              {!! $signupPageContent !!}
            @endisset
          </div>
          </div>
        </div>
    </div>
</div>

{{-- Always render modal but hide it initially - show via JavaScript for AJAX or session for regular requests --}}
<div id="my-modal" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: {{ session('showModal') ? 'block' : 'none' }};">
  <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
      <div class="relative flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeModal()"></div>
            <div class="relative animate__animated animate__fadeIn z-20 transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
              <div class="bg-white px-4 mt-10 sm:mt-1 pb-4 pt-16 sm:p-6 sm:pb-4 sm:pt-16">
                <button onclick="closeModal()" class="absolute top-4 right-4 p-1 rounded-full hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-400 z-50">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
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
                    <h3 class="text-3xl text-center font-FuturaMdCnBT text-gray-900 mb-4" id="modal-title">Registration Successful</h3>
                    <div class="mt-2 w-full">
                        @php
                            $user = session('user');
                            $messages = session('messages');
                        @endphp
                        <p class="can-exp-p text-center" id="modal-welcome-message">
                            @if($messages && $user)
                                {{ $messages->welcome_message }} {{ $user->first_name }},
                            @endif
                        </p>
                        <div id="modal-email-message">
                            @if($messages)
                                {!! $messages->email_sent_message !!}
                            @endif
                        </div>
                    </div>
                </div>
              </div>
              <div class="px-4 pb-6 pt-4  sm:flex sm:flex-row-reverse sm:px-6 justify-center">
                  <a href="{{ route('home', ['lang' => $selectedLanguage->abbreviation]) }}"
                      class="inline-flex w-auto justify-center rounded bg-greenXS px-3 py-2 font-FuturaMdCnBT text-lg text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-greenXS w-auto whitespace-nowrap">Go to Home Page</a>
              </div>
            </div>
        </div>
      </div>
  </div>
</div>

@endsection

@section('script')

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
   function closeModal() {
        var modal = document.getElementById('my-modal');
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
    }

    // Function to show tooltip with animation
    function showTooltip(tooltip) {
        if (!tooltip) return;
        
        // Remove hidden class first
        tooltip.classList.remove('hidden');
        
        // Reset any inline styles that might prevent animation
        tooltip.style.display = 'flex';
        tooltip.style.opacity = '';
        tooltip.style.visibility = '';
        tooltip.style.transform = '';
        
        // Force a reflow to ensure styles are applied
        void tooltip.offsetHeight;
        
        // Use requestAnimationFrame for smooth animation
        requestAnimationFrame(function() {
            requestAnimationFrame(function() {
                tooltip.classList.add('show');
            });
        });
    }

    // Function to hide tooltip with animation - Fixed
    function hideTooltip(tooltip) {
        if (!tooltip) return;
        
        // Remove show class to trigger fade out
        tooltip.classList.remove('show');
        
        // After animation completes, hide completely
        setTimeout(function() {
            tooltip.classList.add('hidden');
            tooltip.style.display = 'none';
            // Reset inline styles
            tooltip.style.opacity = '';
            tooltip.style.visibility = '';
            tooltip.style.transform = '';
        }, 200); // Match CSS transition duration
    }

    // Function to clear all error messages
    function clearAllErrors() {
        document.querySelectorAll('.tooltip').forEach(function(tooltip) {
            // Immediately hide and remove classes (synchronous)
            tooltip.classList.remove('show');
            tooltip.classList.add('hidden');
            tooltip.style.display = 'none';
            tooltip.style.opacity = '';
            tooltip.style.visibility = '';
            tooltip.style.transform = '';
        });
        document.querySelectorAll('#signupForm input').forEach(function(input) {
            input.classList.remove('ring-red-500', 'ring-2');
        });
    }

    // Function to display validation errors
    function displayValidationErrors(errors) {
        // Clear all errors immediately (synchronously)
        clearAllErrors();
        
        // Wait a tiny bit for DOM to update, then show new errors
        setTimeout(function() {
            if (errors && typeof errors === 'object') {
                Object.keys(errors).forEach(function(fieldName) {
                    var field = document.querySelector('[name="' + fieldName + '"]');
                    if (field) {
                        // Add error styling to input
                        field.classList.add('ring-red-500', 'ring-2');

                        // Find error container
                        var fieldContainer = field.closest('.mt-2') || field.parentElement;
                        var errorContainer = null;
                        
                        if (fieldContainer) {
                            errorContainer = fieldContainer.querySelector('.tooltip');
                        }
                        
                        if (!errorContainer && fieldContainer && fieldContainer.parentElement) {
                            errorContainer = fieldContainer.parentElement.querySelector('.tooltip');
                        }

                        if (errorContainer) {
                            // Update existing error message
                            var errorText = errorContainer.querySelector('p');
                            if (errorText) {
                                errorText.textContent = errors[fieldName][0];
                            }
                            // Reset and show
                            errorContainer.classList.remove('hidden');
                            errorContainer.style.display = 'flex';
                            errorContainer.style.opacity = '';
                            errorContainer.style.visibility = '';
                            errorContainer.style.transform = '';
                            
                            // Show with animation
                            showTooltip(errorContainer);
                        } else {
                            // Create new error container
                            var errorDiv = document.createElement('div');
                            errorDiv.className = 'relative tooltip -bottom-4 hidden';
                            errorDiv.innerHTML = '<div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded"><p class="text-white leading-none text-sm lg:text-base">' + errors[fieldName][0] + '</p></div>';
                            
                            if (fieldContainer) {
                                fieldContainer.appendChild(errorDiv);
                                // Animate after DOM insertion
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
                
                // Scroll to first error after a delay
                setTimeout(function() {
                    var firstErrorField = document.querySelector('#signupForm input.ring-red-500');
                    if (firstErrorField) {
                        firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }, 500);
            }
        }, 10);
    }

    // Function to show success modal
    function showSuccessModal(data) {
        var modal = document.getElementById('my-modal');
        if (modal) {
            // Update modal content if data is provided
            if (data && data.user) {
                var welcomeMessage = document.getElementById('modal-welcome-message');
                var emailMessage = document.getElementById('modal-email-message');
                
                if (data.messages && data.messages.welcome_message && welcomeMessage) {
                    welcomeMessage.textContent = data.messages.welcome_message + ' ' + data.user.first_name + ',';
                }
                if (data.messages && data.messages.email_sent_message && emailMessage) {
                    emailMessage.innerHTML = data.messages.email_sent_message || '';
                }
            }
            
            modal.style.display = 'block';
            document.body.style.overflow = 'hidden';
            
            // Scroll to top to show modal
            window.scrollTo({ top: 0, behavior: 'smooth' });
        } else {
            console.error('Modal element not found');
        }
    }

    // AJAX Form Submission - PREVENTS PAGE REFRESH
    var signupForm = document.getElementById('signupForm');
    if (signupForm) {
        signupForm.addEventListener('submit', function (e) {
            e.preventDefault(); // PREVENTS PAGE REFRESH
            e.stopPropagation();

            var form = this;
            var signupButton = document.getElementById('signupButton');
            var formData = new FormData(form);
            var originalButtonText = signupButton ? signupButton.innerHTML : '';

            // Clear previous errors
            clearAllErrors();

            // Disable button and show loading state
            if (signupButton) {
                signupButton.setAttribute('disabled', 'true');
                signupButton.innerHTML = '<span>Processing...</span>';
            }

            // Get CSRF token
            var csrfToken = document.querySelector('meta[name="csrf-token"]') ? 
                document.querySelector('meta[name="csrf-token"]').getAttribute('content') : 
                formData.get('_token');

            // AJAX SUBMISSION
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
                console.log('Signup response:', data);
                // Handle successful submission
                if (data.success && data.showModal) {
                    // Reset form
                    form.reset();
                    
                    // Show success modal
                    console.log('Showing success modal');
                    showSuccessModal(data);
                } else if (data.errors) {
                    // Display validation errors
                    displayValidationErrors(data.errors);
                } else {
                    console.warn('Unexpected response format:', data);
                }
            })
            .catch(function(error) {
                console.error('Error:', error);
                
                // Handle validation errors
                if (error.errors) {
                    displayValidationErrors(error.errors);
                } else {
                    alert('An error occurred. Please check your input and try again.');
                }
            })
            .finally(function() {
                // Re-enable button
                if (signupButton) {
                    signupButton.removeAttribute('disabled');
                    if (originalButtonText) {
                        signupButton.innerHTML = originalButtonText;
                    }
                }
            });
        });
    }

    // Password toggle functionality - PRESERVED
    const togglePassword = document.getElementById('togglePassword');
    const password = document.getElementById('password');

    if (togglePassword && password) {
        togglePassword.addEventListener('click', function () {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);

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

    const togglePassword2 = document.getElementById('togglePassword2');
    const password2 = document.getElementById('password_confirmation');

    if (togglePassword2 && password2) {
        togglePassword2.addEventListener('click', function () {
            const type = password2.getAttribute('type') === 'password' ? 'text' : 'password';
            password2.setAttribute('type', type);

            if (type === 'password') {
                togglePassword2.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 cursor-pointer text-gray-600">
                        <path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" />
                        <path fill-rule="evenodd" d="M1.323 11.447C2.811 6.976 7.028 3.75 12.001 3.75c4.97 0 9.185 3.223 10.675 7.69.12.362.12.752 0 1.113-1.487 4.471-5.705 7.697-10.677 7.697-4.97 0-9.186-3.223-10.675-7.69a1.762 1.762 0 0 1 0-1.113ZM17.25 12a5.25 5.25 0 1 1-10.5 0 5.25 5.25 0 0 1 10.5 0Z" clip-rule="evenodd" />
                    </svg> `;
            } else {
                togglePassword2.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" fill="currentColor" class="w-5 h-5 text-gray-600 cursor-pointer">
                    <path d="M38.8 5.1C28.4-3.1 13.3-1.2 5.1 9.2S-1.2 34.7 9.2 42.9l592 464c10.4 8.2 25.5 6.3 33.7-4.1s6.3-25.5-4.1-33.7L525.6 386.7c39.6-40.6 66.4-86.1 79.9-118.4c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C465.5 68.8 400.8 32 320 32c-68.2 0-125 26.3-169.3 60.8L38.8 5.1zM223.1 149.5C248.6 126.2 282.7 112 320 112c79.5 0 144 64.5 144 144c0 24.9-6.3 48.3-17.4 68.7L408 294.5c8.4-19.3 10.6-41.4 4.8-63.3c-11.1-41.5-47.8-69.4-88.6-71.1c-5.8-.2-9.2 6.1-7.4 11.7c2.1 6.4 3.3 13.2 3.3 20.3c0 10.2-2.4 19.8-6.6 28.3l-90.3-70.8zM373 389.9c-16.4 6.5-34.3 10.1-53 10.1c-79.5 0-144-64.5-144-144c0-6.9 .5-13.6 1.4-20.2L83.1 161.5C60.3 191.2 44 220.8 34.5 243.7c-3.3 7.9-3.3 16.7 0 24.6c14.9 35.7 46.2 87.7 93 131.1C174.5 443.2 239.2 480 320 480c47.8 0 89.9-12.9 126.2-32.5L373 389.9z"/>
                </svg>`;
            }
        });
    }

    // Hide error messages on input - PRESERVED WITH ANIMATIONS
    document.addEventListener('DOMContentLoaded', function () {
        // Handle password tooltip hover with animation
        var passwordLabel = document.querySelector('label[for="password"]');
        if (passwordLabel) {
            var passwordTooltip = document.querySelector('.sups .tooltip');
            var infoIcon = passwordLabel.querySelector('.sups');
            
            if (infoIcon && passwordTooltip) {
                infoIcon.addEventListener('mouseenter', function() {
                    showTooltip(passwordTooltip);
                });
                
                infoIcon.addEventListener('mouseleave', function() {
                    hideTooltip(passwordTooltip);
                });
            }
        }
        
        // Show existing error tooltips with animation on page load - Fixed
        setTimeout(function() {
            document.querySelectorAll('.tooltip:not(.hidden)').forEach(function(tooltip) {
                // Check if tooltip should be visible (has error message)
                var hasText = tooltip.querySelector('p') && tooltip.querySelector('p').textContent.trim() !== '';
                if (hasText && tooltip.style.display !== 'none') {
                    // Remove any inline styles that might prevent animation
                    tooltip.style.opacity = '';
                    tooltip.style.visibility = '';
                    tooltip.style.transform = '';
                    showTooltip(tooltip);
                }
            });
        }, 100);

        var inputs = document.querySelectorAll('#signupForm input');
        var rememberMeCheckbox = document.getElementById('remember-me');

        function hideErrorMessage(element) {
            if (!element) return;
            
            var parentDiv = element.closest('div');
            if (parentDiv) {
                var errorMessage = parentDiv.parentElement ? parentDiv.parentElement.querySelector('.tooltip') : null;
                if (errorMessage) {
                    hideTooltip(errorMessage);
                    element.classList.remove('ring-red-500', 'ring-2');
                }
            }
        }
        
        inputs.forEach(function(input) {
            input.addEventListener('input', function() {
                hideErrorMessage(input);
            });
        });
        
        if (rememberMeCheckbox) {
            rememberMeCheckbox.addEventListener('change', function() {
                hideErrorMessage(rememberMeCheckbox);
            });
        }

        var passwordInput = document.getElementById('password');
        if (passwordInput) {
            passwordInput.addEventListener('input', function() {
                var passwordContainer = passwordInput.closest('.relative');
                if (passwordContainer) {
                    var errorMessage = passwordContainer.querySelector('.tooltip');
                    if (errorMessage) {
                        hideTooltip(errorMessage);
                        passwordInput.classList.remove('ring-red-500', 'ring-2');
                    }
                }
            });
        }
    });
</script>
@endsection
