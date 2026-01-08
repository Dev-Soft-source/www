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

        <div class="mx-auto w-full lg:px-8 ">
            <div class="pt-1 flex justify-start">
                @isset($signupPage->required_label)
                    <p class="text-red-500">* {{ $signupPage->required_label }}</p>
                @endisset
            </div>
            <form id="signupForm" class="space-y-6" method="POST" action="{{ route('signup.store', ['lang' => $selectedLanguage->abbreviation]) }}">
              @csrf

              <input type="hidden" value="{{isset($uuid) && !is_null($uuid) ? $uuid : 0}}" name="uuid" id="uuid">
              <div>
                <div class="mt-2">
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

              <div>
                <label for="password" class="flex space-x-1 font-FuturaMdCnBT">
                    <p>
                    @isset($signupPage->password_label)
                      {{ $signupPage->password_label }}
                    @endisset
                    <span class="text-red-500">*</span>
                    </p>
                    <div class="sups relative">
                        <div class="absolute tooltip top-5 group-hover:flex hidden peer-hover:flex w-52 sm:w-60 md:w-96 left-0 right-0 mx-auto">
                            <div role="tooltip" class="absolute p-2 rounded shadow bg-blue-600 z-10">
                                <p class="text-white text-start text-sm lg:text-base">
                                @isset($signupPage->password_placeholder) {{ $signupPage->password_placeholder }} @endisset
                                </p>
                            </div>
                        </div>
                    </div>
                </label>
                <div class="mt-2 relative">
                    <input
                        @isset($signupPage->confirm_password_placeholder)
                            placeholder="{{ $signupPage->confirm_password_placeholder }}"
                        @endisset
                        class="block w-full rounded text-lg border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-500 focus:ring-2 focus:ring-inset focus:ring-blue-600 placeholder:text-[12px]" id="password" type="password" name="password"
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
              <div>
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
                            <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                                <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                            </div>
                        </div>
                    @enderror
                </div>
            </div>
              <div class="">
                <div class="flex items-center">
                  <label for="remember-me" class="flex text-sm text-gray-900 font-FuturaMdCnBT">
                    <input id="remember-me" name="remember-me" type="checkbox" class="mt-1.5 mr-2 h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-600">
                    @isset($signupPage->agree_terms_label)
                      {{-- <div class="text_base">{!! $signupPage->agree_terms_label !!}</div> --}}
                      @isset($signupPage->agree_terms_label)
                        <div class="text_base">
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
                        </div>
                      @endisset

                    @endisset
                  </label>
                </div>
                @error('remember-me')
                  <div class="relative tooltip -bottom-4 group-hover:flex">
                    <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                      <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                    </div>
                  </div>
                @enderror
              </div>

              <div class="">
                <div class="flex items-center">
                  <label for="rideshare-disclaimer" class="flex text-sm text-gray-900 font-FuturaMdCnBT">
                    <input id="rideshare-disclaimer" name="rideshare_disclaimer" type="checkbox" class="mt-1.5 mr-2 h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-600">
                    <div class="text_base">
                      <p>I understand that ride sharing is not a business and cannot be used as a business.</p>
                    </div>
                  </label>
                </div>
                @error('rideshare_disclaimer')
                  <div class="relative tooltip -bottom-4 group-hover:flex">
                    <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                      <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                    </div>
                  </div>
                @enderror
              </div>

              <div class="flex justify-center font-FuturaMdCnBT">
                <button  id="signupButton" class="button-exp-fill " type="submit">
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

@if(session('showModal'))

<div id="my-modal" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
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
                              <p class="can-exp-p text-center">{{ $messages->welcome_message }} {{ $user->first_name }},</p> <div>{!! $messages->email_sent_message !!}</div>
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

@endif

@endsection

@section('script')

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
   function closeModal() {
        document.getElementById('my-modal').style.display = 'none';
    }

    document.getElementById('signupForm').addEventListener('submit', function () {
        document.getElementById('signupButton').setAttribute('disabled', 'true');
    });

    const togglePassword = document.getElementById('togglePassword');
    const password = document.getElementById('password');

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

    const togglePassword2 = document.getElementById('togglePassword2');
    const password2 = document.getElementById('password_confirmation');

    togglePassword2.addEventListener('click', function () {
        const type = password2.getAttribute('type') === 'password' ? 'text' : 'password';
        password2.setAttribute('type', type);

        // Change eye icon based on password visibility
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
</script>
<script>
//    document.addEventListener('DOMContentLoaded', function () {
//   const inputs = document.querySelectorAll('#signupForm input');
//   const rememberMeCheckbox = document.getElementById('remember-me');
//   const hideErrorMessage = (element) => {
//     const parentDiv = element.closest('div');
//     if (parentDiv) {
//       const errorMessage = parentDiv.querySelector('.tooltip');
//       if (errorMessage) {
//         errorMessage.style.display = 'none';
//       }
//     }
//   };

//   inputs.forEach(input => {
//     input.addEventListener('input', function () {
//       hideErrorMessage(input);
//     });
//   });

//   if (rememberMeCheckbox) {
//     rememberMeCheckbox.addEventListener('change', function () {
//       hideErrorMessage(rememberMeCheckbox);
//     });
//   }
// });
document.addEventListener('DOMContentLoaded', function () {
  const inputs = document.querySelectorAll('#signupForm input');
  const rememberMeCheckbox = document.getElementById('remember-me');

  const hideErrorMessage = (element) => {
    const parentDiv = element.closest('div');
    if (parentDiv) {
      const errorMessage = parentDiv.parentElement.querySelector('.tooltip');
      if (errorMessage) {
        console.log('Error Message Found:', errorMessage);
        errorMessage.style.display = 'none';
      } else {
        console.log('Error Message Not Found');
      }
    } else {
      console.log('Parent Div Not Found');
    }
  };
  inputs.forEach(input => {
    input.addEventListener('input', function () {
      hideErrorMessage(input);
    });
  });
  if (rememberMeCheckbox) {
    rememberMeCheckbox.addEventListener('change', function () {
      hideErrorMessage(rememberMeCheckbox);
    });
  }
});
document.addEventListener('DOMContentLoaded', function () {
  const passwordInput = document.getElementById('password');

  if (passwordInput) {
    passwordInput.addEventListener('input', function () {
      const errorMessage = passwordInput.closest('.relative')?.querySelector('.tooltip');
      if (errorMessage) {
        errorMessage.style.display = 'none';
      }
    });
  }
});

  </script>
@endsection
