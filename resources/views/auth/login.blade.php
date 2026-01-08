<x-guest-layout>
<div class="2xl:h-screen">
    <div class="relative isolate overflow-hidden bg-white h-full flex justify-center items-center">
        <img src="https://images.unsplash.com/photo-1490623970972-ae8bb3da443e?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&crop=focalpoint&fp-y=.8&w=2830&h=1500&q=80" alt="" class="absolute inset-0 -z-10 h-full w-full object-cover">
        <div class="relative mx-auto max-w-7xl px-6 lg:px-8">
          <div class="mx-auto max-w-2xl lg:max-w-xl">
            <div class="flex min-h-full flex-col justify-center py-12">
                <div class="sm:mx-auto sm:w-full sm:max-w-md">
                    <img src="{{asset('assets/PROXIMARIDE.png')}}" class="l-light h-32 text-center mx-auto" alt="">
                  </div>
                  
                  <div class="mt-2 sm:mx-auto sm:w-full sm:max-w-xl md:min-w-[30rem]">
                    <div class="mt-6 bg-white pb-8 pt-4 px-4 shadow rounded-md sm:px-10">
                      <h1 class="text-center can-exp-h1 text-primary">Sign in to your account</h1>
                      <x-auth-session-status class="mb-4" :status="session('status')" />

                    <!-- Validation Errors -->
                    <x-auth-validation-errors class="mb-4" :errors="$errors" />
                    <form class="space-y-6 mt-6" method="POST" action="{{ route('admin.login') }}">
                        @csrf
                      <div>
                        <label for="email" class="block leading-6 text-gray-900">Email address</label>
                        <div class="mt-2">
                            <input id="email" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6" type="email" name="email" :value="old('email')" required autofocus />
                        </div>
                      </div>

                      <div>
                        <label for="password" class="block leading-6 text-gray-900">Password</label>
                        <div class="mt-2">
                            <input class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6" id="password" type="password" name="password" required autocomplete="current-password" />
                        </div>
                      </div>

                      <div class="flex items-center justify-between flex-col sm:flex-col md:flex-row lg:flex-row">
                        <div class="flex items-center">
                          <input id="remember-me" name="remember-me" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-600">
                          <label for="remember-me" class="ml-2 block text-sm text-gray-900">Remember me</label>
                        </div>

                        <div class="text-sm">
                            @if (Route::has('password.request'))
                            <a tabindex="-1" href="{{ route('password.request') }}" class="font-medium text-blue-600 hover:text-indigo-500 text-sm md:text-base ">Forgot your password?</a>
                            @endif
                        </div>
                      </div>

                      <div>
                        <button class="button-exp-fill flex w-full justify-center" type="submit">
                            Log in
                        </button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
          </div>
        </div>
      </div>
    </div>

</x-guest-layout>
