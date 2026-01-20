@extends('layouts.template')

@section('content')

<div class="mx-auto max-w-2xl lg:max-w-xl">
  <div class="flex min-h-full flex-col justify-center my-14 px-4 sm:px-6 lg:px-8">
      <div class="bg-white border border-gray-100 p-4 shadow rounded-md sm:px-10">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
          <h1 class="text-center can-exp-h1 mt-6 text-primary">
            @isset($resetPasswordPage->main_heading)
                {{ $resetPasswordPage->main_heading }}
            @else
                Reset Your Password
            @endisset
          </h1>
          <p class="text-center">
            @isset($resetPasswordPage->main_label)
                {{ $resetPasswordPage->main_label }}
            @endisset
          </p>
        </div>

        <div class="sm:mx-auto sm:w-full sm:max-w-xl md:min-w-[26rem]">
          <div class="bg-white pb-4 px-4 rounded-md sm:px-10">
            <form class="space-y-4" method="POST" action="{{ route('update.password') }}">
              @csrf
              @if(session('message'))
                <div class="mt-4 mb-4 rounded-lg px-6 py-3 bg-red-100 text-gray-600" role="alert">
                    {{ session('message') }}
                </div>
              @endif

              <input type="hidden" name="token" value="{{ $request->route('token') }}">
              <div>
                <label for="password" class="text-lg font-medium">
                  @isset($resetPasswordPage->password_label)
                  {{ $resetPasswordPage->password_label }}
                  @else
                  New Password
                  @endisset
                </label>
                <div class="mt-2 relative">
                  <input class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6" id="password" type="password" name="password" autocomplete="current-password" />
                  <span id="togglePassword" class="absolute right-3 top-2.5">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 cursor-pointer text-gray-600">
                      <path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" />
                      <path fill-rule="evenodd" d="M1.323 11.447C2.811 6.976 7.028 3.75 12.001 3.75c4.97 0 9.185 3.223 10.675 7.69.12.362.12.752 0 1.113-1.487 4.471-5.705 7.697-10.677 7.697-4.97 0-9.186-3.223-10.675-7.69a1.762 1.762 0 0 1 0-1.113ZM17.25 12a5.25 5.25 0 1 1-10.5 0 5.25 5.25 0 0 1 10.5 0Z" clip-rule="evenodd" />
                    </svg>
                  </span>
                </div>
                @error('password')
                  <div class="relative tooltip -bottom-4 group-hover:flex">
                    <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                      <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                    </div>
                  </div>
                @enderror
              </div>

              <div>
                <label for="password_confirmation" class="text-lg font-medium">
                  @isset($resetPasswordPage->confirm_password_label)
                    {{ $resetPasswordPage->confirm_password_label }}
                  @else
                    Confirm New Password
                  @endisset
                </label>
                <div class="mt-2 relative">
                  <input class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6" id="password_confirmation" type="password" name="password_confirmation" autocomplete="current-password" />
                  <span id="togglePassword2" class="absolute right-3 top-2.5">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 cursor-pointer text-gray-600">
                      <path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" />
                      <path fill-rule="evenodd" d="M1.323 11.447C2.811 6.976 7.028 3.75 12.001 3.75c4.97 0 9.185 3.223 10.675 7.69.12.362.12.752 0 1.113-1.487 4.471-5.705 7.697-10.677 7.697-4.97 0-9.186-3.223-10.675-7.69a1.762 1.762 0 0 1 0-1.113ZM17.25 12a5.25 5.25 0 1 1-10.5 0 5.25 5.25 0 0 1 10.5 0Z" clip-rule="evenodd" />
                    </svg>
                  </span>
                </div>
                @error('password_confirmation')
                  <div class="relative tooltip -bottom-4 group-hover:flex">
                    <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                      <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                    </div>
                  </div>
                @enderror
              </div>

              <div class="flex items-center justify-center flex-col sm:flex-col md:flex-row lg:flex-row">
                <div>
                    <button class="button-exp-fill flex w-full justify-center text-lg font-semibold px-4" type="submit">
                        @isset($resetPasswordPage->button_label)
                            {{ $resetPasswordPage->button_label }}
                        @else
                            Submit
                        @endisset
                    </button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
</div>

@if(session('showModal'))

<div class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
  <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
  <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
    <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
      <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
        <button onclick="closeModal()" class="absolute top-4 right-4 p-1.5 rounded-full hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-400 z-50">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        <div class="bg-white px-4 mt-10 sm:mt-1 pb-4 pt-16 sm:p-6 sm:pb-4 sm:pt-16">
          <div class="text-center">
            <div class="flex justify-center">
              <p class="can-exp-p text-center text-gray-700 font-medium">Your account email needs verification</p>
            </div>
          </div>
        </div>
        @php
            $user = session('user');
        @endphp
        <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 justify-center">
          <a href="{{ route('sendEmailVerify', ['email' => $user->email]) }}" class="inline-flex justify-center rounded-md bg-greenXS px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-greenXS sm:ml-3 w-auto">Send verification email</a>
        </div>
      </div>
    </div>
  </div>
</div>

@endif

@endsection

@section('script')

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<script>
     function closeModal() {
    document.querySelector('.relative.z-50').style.display = 'none';
  }
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

@endsection
