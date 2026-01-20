@extends('layouts.template')
@section('style')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
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
    /* Extra small devices */
    @media only screen and (max-width: 375px) {
        .tooltip_width{
            width: 16.5rem;
        }
    } 
    @media only screen and (min-width:376px) and (max-width: 639px) {
        .tooltip_width{
            width: 20rem;
        } 
    }
    @media only screen and (max-width: 767px) {
        .features_tooltiptext::after {
            content: "";
            border-width: 10px;
            border-style: solid;
            border-color: transparent transparent #3b82f6 transparent;
            position: absolute;
            top: -20px;
            bottom: auto;
            left: 5.8rem;
        }
    }
</style>
@endsection
@section('content')

<div class="grid grid-cols-12 gap-4 container mx-auto my-14">
    @include('layouts.inc.profile_sidebar')

    <div class="bg-white border rounded p-4 border-gray-200 w-full col-span-12 md:col-span-9 shadow">
        @if(session('message'))
            <div id="my-modal" class="relative z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <!-- Backdrop with transition -->
                <div class="fixed inset-0 bg-gray-500 bg-opacity-0 transition-opacity duration-300 ease-in-out z-10" id="modal-backdrop"></div>
            
                <!-- Modal container with transition -->
                <div class="fixed inset-0 flex items-center justify-center p-4 z-20 opacity-0 scale-95 transition-all duration-300 ease-in-out" id="modal-container">
                    <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                        <!-- Modal content with transition -->
                        <div class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
                            <button type="button" onclick="closeModal()" class="absolute top-3 right-3 text-gray-400 hover:text-gray-500">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                            <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                                <div class="sm:flex sm:items-start justify-center">
                                    <!-- <div class="mx-auto h-16 w-16 flex-shrink-0 flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="4" stroke="currentColor" class="w-12 h-12 text-greenXS">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                        </svg>
                                    </div> -->
                                </div>
                                <div class="mt-2 w-full">
                                    <p class="can-exp-p text-center">{!! session('message') !!}</p>
                                </div>
                            </div>
                            <div class="px-4 pb-6 pt-4 sm:flex sm:flex-row-reverse sm:px-6 justify-center">
                                <button onclick="closeModal()"
                                    class="inline-flex w-full justify-center rounded bg-greenXS px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-greenXS sm:ml-3 sm:w-24 transition-colors duration-200">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                // Function to show modal with transitions
                function showModal() {
                    const modal = document.getElementById('my-modal');
                    const backdrop = document.getElementById('modal-backdrop');
                    const container = document.getElementById('modal-container');
            
                    modal.classList.remove('hidden');
            
                    // Trigger reflow to enable transitions
                    void modal.offsetWidth;
            
                    backdrop.classList.remove('bg-opacity-0');
                    backdrop.classList.add('bg-opacity-75');
            
                    container.classList.remove('opacity-0', 'scale-95');
                    container.classList.add('opacity-100', 'scale-100');
                }
            
                // Function to close modal with transitions
                function closeModal() {
                    const backdrop = document.getElementById('modal-backdrop');
                    const container = document.getElementById('modal-container');
            
                    backdrop.classList.remove('bg-opacity-75');
                    backdrop.classList.add('bg-opacity-0');
            
                    container.classList.remove('opacity-100', 'scale-100');
                    container.classList.add('opacity-0', 'scale-95');
            
                    // Wait for transition to complete before hiding
                    setTimeout(() => {
                        document.getElementById('my-modal').classList.add('hidden');
                    }, 300);
                }
            
                // Auto-show modal if there's a message
                @if(session('message'))
                    document.addEventListener('DOMContentLoaded', showModal);
                @endif
            </script>
        @endif
        
        <div class=" pb-2">
            <h1 class="mb-0">Driver preferences</h1>
        </div>

        <form method="POST" action="{{ route('profile.preferences.update',$user->id) }}">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4 mt-4">
                <div class="mt-2 md:col-span-2">
                    <label for="">Smoking :</label>
                    <div class="flex gap-4 md:justify-normal justify-between md:gap-x-8 items-center mt-2 p-1.5">
                        @isset($postRidePage->smoking_option1)
                            <div>
                                <input id="smoke" type="radio" value="{{ $postRidePage->smoking_option1 }}" name="smoke" {{ old('smoke', $user->smoke) === $postRidePage->smoking_option1 ? 'checked' : '' }} class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                <label for="smoke" class="ml-2 text-gray-900">{{ $postRidePage->smoking_option1 }}</label>
                            </div>
                        @endisset
                        @isset($postRidePage->smoking_option2)
                            <div>
                                <input id="smoke" type="radio" value="{{ $postRidePage->smoking_option2 }}" name="smoke" {{ old('smoke', $user->smoke) === $postRidePage->smoking_option2 ? 'checked' : '' }} class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                <label for="smoke" class="ml-2 text-gray-900">{{ $postRidePage->smoking_option2 }}</label>
                            </div>
                        @endisset
                    </div>
                    @error('smoke')
                      <div class="relative tooltip -bottom-4 group-hover:flex">
                        <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/4 rounded" >
                            <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                        </div>
                      </div>
                    @enderror
                </div>
    
                <div class="mt-2 md:col-span-2">
                    <label for="">Pets :</label>
                    <div class="flex gap-4 md:justify-normal justify-between md:gap-x-8 items-center mt-2 p-1.5">
                        @isset($postRidePage->animals_option1)
                            <div>
                                <input id="pets" type="radio" value="{{ $postRidePage->animals_option1 }}" name="pets" {{ old('pets', $user->pets) === $postRidePage->animals_option1 ? 'checked' : '' }} class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                <label for="" class="ml-2 text-gray-900">{{ $postRidePage->animals_option1 }}</label>
                            </div>
                        @endisset
                        @isset($postRidePage->animals_option2)
                            <div>
                                <input id="pets" type="radio" value="{{ $postRidePage->animals_option2 }}" name="pets" {{ old('pets', $user->pets) === $postRidePage->animals_option2 ? 'checked' : '' }} class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                <label for="" class="ml-2 text-gray-900">{{ $postRidePage->animals_option2 }}</label>
                            </div>
                        @endisset
                        @isset($postRidePage->animals_option3)
                            <div>
                                <input id="pets" type="radio" value="{{ $postRidePage->animals_option3 }}" name="pets" {{ old('pets', $user->pets) === $postRidePage->animals_option3 ? 'checked' : '' }} class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                <label for="" class="ml-2 text-gray-900">{{ $postRidePage->animals_option3 }}</label>
                            </div>
                        @endisset
                    </div>
                    @error('pets')
                      <div class="relative tooltip -bottom-4 group-hover:flex">
                        <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/4 rounded" >
                            <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                        </div>
                      </div>
                    @enderror
                </div>
    
                <div class="mt-2 md:col-span-2">
                    <ul class="space-y-4">
                        @isset($postRidePage->features_option1)
                            <li>
                                <div class="flex items-center">
                                    <input id="bordered-radio-1" type="checkbox" value="{{ $postRidePage->features_option1 }}" name="features[]" {{ in_array($postRidePage->features_option1, old('features', explode(';', $user->features))) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                    <label for="pink-ride"
                                    class="ml-2 text-gray-900 flex space-x-1">
                                    <span class="text-pink-500 font-medium ">
                                        {{ $postRidePage->features_option1 }}
                                    </span>
                                    <div class="sups relative">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle-fill text-gray-400 peer cursor-pointer" viewBox="0 0 16 16">
                                            <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                                        </svg>
                                        <div
                                          class="absolute right-28 md:right-24 tooltip -bottom-3 md:-top-14 lg:-top-16 group-hover:flex hidden peer-hover:flex"
                                        >
                                            <div
                                                role="tooltip"
                                                class="absolute after:left-[4.8rem] features_tooltiptext -left-1/2 -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-blue-500  border border-blue-500 text-gray-600 rounded tooltip_width sm:w-[25rem] md:w-[30rem] px-4"
                                            >
                                                <p class="text-white font-semibold text-start text-sm lg:text-base">
                                                    Only female driver can select this ride after they have added complete address in their profile
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                                </div>
                            </li>
                            <div class="hidden overflow-x-hidden overflow-y-auto fixed inset-0 z-50 outline-none focus:outline-none justify-center items-center"
                                id="validation_message_1">
                                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                                <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                                    <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                                    <!--content-->
                                    <div
                                        class="relative transform overflow-hidden rounded-2xl bg-white text-center shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md">
                                        <!--body-->
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
                                                <div class="mt-2 w-full">
                                                    <p class="can-exp-p text-center modal-message">Only female driver can select this ride after they have added complete address in their profile</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                            </div>
                        @endisset
                        @isset($postRidePage->features_option2)
                            <li>
                                <div class="flex items-center">
                                    <input id="bordered-radio-2" type="checkbox" {{ $overallRating < '4.5' || $user->address == '' ? 'disabled' : '' }} value="{{ $postRidePage->features_option2 }}" name="features[]" {{ in_array($postRidePage->features_option2, old('features', explode(';', $user->features))) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                    <label for=""
                                            class="ml-2 text-gray-900 flex space-x-1">
                                        <span class="text-green-500 font-medium">
                                            {{ $postRidePage->features_option2 }}
                                        </span>
                                        <div class="sups relative">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle-fill text-gray-400 peer cursor-pointer" viewBox="0 0 16 16">
                                                <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                                            </svg>
                                            <div
                                                class="absolute right-28 md:right-24 tooltip -bottom-3 md:-top-14 lg:-top-16 group-hover:flex hidden peer-hover:flex"
                                            >
                                                <div
                                                    role="tooltip"
                                                    class="absolute after:left-[4.8rem] features_tooltiptext -left-1/2 -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-blue-500  border border-blue-500 text-gray-600 rounded tooltip_width sm:w-[25rem] md:w-[30rem] px-4"
                                                >
                                                    <p class="text-white font-semibold text-start text-sm lg:text-base">
                                                        Driver whose review is 0 or greater and his age is 0 or greater is eligible for extra care ride
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </li>
                        @endisset
                        @isset($postRidePage->features_option3)
                            <li>
                                <div>
                                    <input id="bordered-radio-3" type="checkbox" value="{{ $postRidePage->features_option3 }}" name="features[]" {{ in_array($postRidePage->features_option3, old('features', explode(';', $user->features))) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                    <label for="" class="ml-2 text-gray-900">{{ $postRidePage->features_option3 }}</label>
                                </div>
                            </li>
                        @endisset
                        @isset($postRidePage->features_option4)
                            <li>
                                <div>
                                    <input id="bordered-radio-4" type="checkbox" value="{{ $postRidePage->features_option4 }}" name="features[]" {{ in_array($postRidePage->features_option4, old('features', explode(';', $user->features))) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                    <label for="" class="ml-2 text-gray-900">{{ $postRidePage->features_option4 }}</label>
                                </div>
                            </li>
                        @endisset
                        @isset($postRidePage->features_option5)
                            <li>
                                <div>
                                    <input id="bordered-radio-5" type="checkbox" value="{{ $postRidePage->features_option5 }}" name="features[]" {{ in_array($postRidePage->features_option5, old('features', explode(';', $user->features))) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                    <label for="" class="ml-2 text-gray-900">{{ $postRidePage->features_option5 }}</label>
                                </div>
                            </li>
                        @endisset
                        @isset($postRidePage->features_option6)
                            <li>
                                <div>
                                    <input id="bordered-radio-6" type="checkbox" value="{{ $postRidePage->features_option6 }}" name="features[]" {{ in_array($postRidePage->features_option6, old('features', explode(';', $user->features))) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                    <label for="" class="ml-2 text-gray-900">{{ $postRidePage->features_option6 }}</label>
                                </div>
                            </li>
                        @endisset
                        @isset($postRidePage->features_option7)
                            <li>
                                <div>
                                    <input id="bordered-radio-7" type="checkbox" value="{{ $postRidePage->features_option7 }}" name="features[]" {{ in_array($postRidePage->features_option7, old('features', explode(';', $user->features))) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                    <label for="" class="ml-2 text-gray-900">{{ $postRidePage->features_option7 }}</label>
                                </div>
                            </li>
                        @endisset
                        @isset($postRidePage->features_option13)
                            <li>
                                <div>
                                    <input id="bordered-radio-13" type="checkbox" value="{{ $postRidePage->features_option13 }}" name="features[]" {{ in_array($postRidePage->features_option13, old('features', explode(';', $user->features))) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                    <label for="" class="ml-2 text-gray-900">{{ $postRidePage->features_option13 }}</label>
                                </div>
                            </li>
                        @endisset
                        @isset($postRidePage->features_option14)
                            <li>
                                <div>
                                    <input id="bordered-radio-14" type="checkbox" value="{{ $postRidePage->features_option14 }}" name="features[]" {{ in_array($postRidePage->features_option14, old('features', explode(';', $user->features))) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                    <label for="" class="ml-2 text-gray-900">{{ $postRidePage->features_option14 }}</label>
                                </div>
                            </li>
                        @endisset
                        @isset($postRidePage->features_option15)
                            <li>
                                <div>
                                    <input id="bordered-radio-15" type="checkbox" value="{{ $postRidePage->features_option15 }}" name="features[]" {{ in_array($postRidePage->features_option15, old('features', explode(';', $user->features))) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                    <label for="" class="ml-2 text-gray-900">{{ $postRidePage->features_option15 }}</label>
                                </div>
                            </li>
                        @endisset
                        @isset($postRidePage->features_option16)
                            <li>
                                <div>
                                    <input id="bordered-radio-16" type="checkbox" value="{{ $postRidePage->features_option16 }}" name="features[]" {{ in_array($postRidePage->features_option16, old('features', explode(';', $user->features))) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                    <label for="" class="ml-2 text-gray-900">{{ $postRidePage->features_option16 }}</label>
                                </div>
                            </li>
                        @endisset
                        @isset($postRidePage->features_option8)
                            <li>
                                <div>
                                    <input id="bordered-radio-8" type="checkbox" value="{{ $postRidePage->features_option8 }}" name="features[]" {{ in_array($postRidePage->features_option8, old('features', explode(';', $user->features))) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                    <label for="" class="ml-2 text-gray-900">{{ $postRidePage->features_option8 }}</label>
                                </div>
                            </li>
                        @endisset
                        @isset($postRidePage->features_option9)
                            <li>
                                <div>
                                    <input id="bordered-radio-9" type="checkbox" value="{{ $postRidePage->features_option9 }}" name="features[]" {{ in_array($postRidePage->features_option9, old('features', explode(';', $user->features))) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                    <label for="" class="ml-2 text-gray-900">{{ $postRidePage->features_option9 }}</label>
                                </div>
                            </li>
                        @endisset
                        @isset($postRidePage->features_option10)
                            <li>
                                <div>
                                    <input id="bordered-radio-10" type="checkbox" value="{{ $postRidePage->features_option10 }}" name="features[]" {{ in_array($postRidePage->features_option10, old('features', explode(';', $user->features))) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                    <label for="" class="ml-2 text-gray-900">{{ $postRidePage->features_option10 }}</label>
                                </div>
                            </li>
                        @endisset
                        @isset($postRidePage->features_option11)
                            <li>
                                <div>
                                    <input id="bordered-radio-11" type="checkbox" value="{{ $postRidePage->features_option11 }}" name="features[]" {{ in_array($postRidePage->features_option11, old('features', explode(';', $user->features))) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                    <label for="" class="ml-2 text-gray-900">{{ $postRidePage->features_option11 }}</label>
                                </div>
                            </li>
                        @endisset
                        @isset($postRidePage->features_option12)
                            <li>
                                <div>
                                    <input id="bordered-radio-12" type="checkbox" value="{{ $postRidePage->features_option12 }}" name="features[]" {{ in_array($postRidePage->features_option12, old('features', explode(';', $user->features))) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                    <label for="" class="ml-2 text-gray-900">{{ $postRidePage->features_option12 }}</label>
                                </div>
                            </li>
                        @endisset
                    </ul>
                </div>
               
                <div class="md:col-span-2">
                    <button type="submit" class="button-exp-fill">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>
    
@endsection

@section('script')

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const checkbox = document.getElementById('bordered-radio-1');
        const validationMessage = document.getElementById('validation_message_1');

        checkbox.addEventListener('click', function () {
            if (checkbox.checked && (@json($user->gender) !== 'female' || @json($user->address) === '')) {
                validationMessage.classList.remove('hidden');
                checkbox.checked = false; // Uncheck the checkbox
                checkbox.disabled = true; // Disable the checkbox
                setTimeout(function () {
                    validationMessage.classList.add('hidden');
                    checkbox.disabled = false; // Re-enable the checkbox after 2 seconds
                }, 9000); // 9000 milliseconds = 9 seconds
            }
        });
    });
</script>
    
@endsection