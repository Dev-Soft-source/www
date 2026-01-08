<x-guest-layout>
    <x-auth-card>
        <x-slot name="logo">
            <a href="/">
                <img class="h-32 mx-auto" src="{{asset('assets/PROXIMARIDE.png')}}" alt="">
            </a>
        </x-slot>
        <h2 class="my-2 can-exp-h2 text-primary text-center">
            {{ __('Forgot password') }}
        </h2>
        <p class="my-3 can-exp-p">
            {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
        </p>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <!-- Validation Errors -->
        @if ($errors->any())
        <div class="mb-4">
            <div class="font-medium text-red-600">
                {{ __('Whoops! Something went wrong.') }}
            </div>
        </div>
    @endif
        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <!-- Email Address -->
            <div class="relative">
                <x-input id="email" placeholder="Enter email address" class="can-exp-input w-full mt-1" type="email" name="email" :value="old('email')"  autofocus />
                <div class="relative tooltip -bottom-4 group-hover:flex">
                    @error('email')
                        <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-primary text-gray-600 w-full md:w-1/2 rounded">
                            <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                        </div>
                    @enderror
                </div>
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-button class="button-exp-fill">
                    {{ __('Email password reset link') }}
                </x-button>
            </div>
        </form>
    </x-auth-card>
</x-guest-layout>
