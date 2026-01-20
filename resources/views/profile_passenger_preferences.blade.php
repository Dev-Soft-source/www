@extends('layouts.template')

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
            <h1 class="mb-0">Passenger preferences</h1>
        </div>

        <form method="POST" action="{{ route('profile.passenger_preferences.update',$user->id) }}">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4 mt-4">
                <div class="mt-2 md:col-span-2">
                    <label for="">Smoking :</label>
                    <div class="flex gap-4 md:justify-normal justify-between md:gap-x-8 items-center mt-2 p-1.5">
                        @isset($findRidePage->smoking_option1)
                            <div>
                                <input id="smoke" type="radio" value="{{ $findRidePage->smoking_option1 }}" name="smoke" {{ old('smoke', $user->passenger_smoke) === $findRidePage->smoking_option1 ? 'checked' : '' }} class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                <label for="smoke" class="ml-2 text-gray-900">{{ $findRidePage->smoking_option1 }}</label>
                            </div>
                        @endisset
                        @isset($findRidePage->smoking_option2)
                            <div>
                                <input id="smoke" type="radio" value="{{ $findRidePage->smoking_option2 }}" name="smoke" {{ old('smoke', $user->passenger_smoke) === $findRidePage->smoking_option2 ? 'checked' : '' }} class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                <label for="smoke" class="ml-2 text-gray-900">{{ $findRidePage->smoking_option2 }}</label>
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
                        @isset($findRidePage->pets_allowed_option1)
                            <div>
                                <input id="pets" type="radio" value="{{ $findRidePage->pets_allowed_option1 }}" name="pets" {{ old('pets', $user->passenger_pets) === $findRidePage->pets_allowed_option1 ? 'checked' : '' }} class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                <label for="" class="ml-2 text-gray-900">{{ $findRidePage->pets_allowed_option1 }}</label>
                            </div>
                        @endisset
                        @isset($findRidePage->pets_allowed_option2)
                            <div>
                                <input id="pets" type="radio" value="{{ $findRidePage->pets_allowed_option2 }}" name="pets" {{ old('pets', $user->passenger_pets) === $findRidePage->pets_allowed_option2 ? 'checked' : '' }} class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                <label for="" class="ml-2 text-gray-900">{{ $findRidePage->pets_allowed_option2 }}</label>
                            </div>
                        @endisset
                        @isset($findRidePage->pets_allowed_option3)
                            <div>
                                <input id="pets" type="radio" value="{{ $findRidePage->pets_allowed_option3 }}" name="pets" {{ old('pets', $user->passenger_pets) === $findRidePage->pets_allowed_option3 ? 'checked' : '' }} class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                <label for="" class="ml-2 text-gray-900">{{ $findRidePage->pets_allowed_option3 }}</label>
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
                        @isset($findRidePage->ride_features_option4)
                            <li>
                                <div>
                                    <input id="bordered-radio-4" type="checkbox" value="{{ $findRidePage->ride_features_option4 }}" name="features[]" {{ in_array($findRidePage->ride_features_option4, old('features', explode(';', $user->passenger_features))) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                    <label for="" class="ml-2 text-gray-900">{{ $findRidePage->ride_features_option4 }}</label>
                                </div>
                            </li>
                        @endisset
                        @isset($findRidePage->ride_features_option5)
                            <li>
                                <div>
                                    <input id="bordered-radio-5" type="checkbox" value="{{ $findRidePage->ride_features_option5 }}" name="features[]" {{ in_array($findRidePage->ride_features_option5, old('features', explode(';', $user->passenger_features))) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                    <label for="" class="ml-2 text-gray-900">{{ $findRidePage->ride_features_option5 }}</label>
                                </div>
                            </li>
                        @endisset
                        @isset($findRidePage->ride_features_option6)
                            <li>
                                <div>
                                    <input id="bordered-radio-6" type="checkbox" value="{{ $findRidePage->ride_features_option6 }}" name="features[]" {{ in_array($findRidePage->ride_features_option6, old('features', explode(';', $user->passenger_features))) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                    <label for="" class="ml-2 text-gray-900">{{ $findRidePage->ride_features_option6 }}</label>
                                </div>
                            </li>
                        @endisset
                        @isset($findRidePage->ride_features_option7)
                            <li>
                                <div>
                                    <input id="bordered-radio-7" type="checkbox" value="{{ $findRidePage->ride_features_option7 }}" name="features[]" {{ in_array($findRidePage->ride_features_option7, old('features', explode(';', $user->passenger_features))) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                    <label for="" class="ml-2 text-gray-900">{{ $findRidePage->ride_features_option7 }}</label>
                                </div>
                            </li>
                        @endisset
                        @isset($findRidePage->ride_features_option13)
                            <li>
                                <div>
                                    <input id="bordered-radio-13" type="checkbox" value="{{ $findRidePage->ride_features_option13 }}" name="features[]" {{ in_array($findRidePage->ride_features_option13, old('features', explode(';', $user->passenger_features))) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                    <label for="" class="ml-2 text-gray-900">{{ $findRidePage->ride_features_option13 }}</label>
                                </div>
                            </li>
                        @endisset
                        @isset($findRidePage->ride_features_option8)
                            <li>
                                <div>
                                    <input id="bordered-radio-8" type="checkbox" value="{{ $findRidePage->ride_features_option8 }}" name="features[]" {{ in_array($findRidePage->ride_features_option8, old('features', explode(';', $user->passenger_features))) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                    <label for="" class="ml-2 text-gray-900">{{ $findRidePage->ride_features_option8 }}</label>
                                </div>
                            </li>
                        @endisset
                        @isset($findRidePage->ride_features_option9)
                            <li>
                                <div>
                                    <input id="bordered-radio-9" type="checkbox" value="{{ $findRidePage->ride_features_option9 }}" name="features[]" {{ in_array($findRidePage->ride_features_option9, old('features', explode(';', $user->passenger_features))) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                    <label for="" class="ml-2 text-gray-900">{{ $findRidePage->ride_features_option9 }}</label>
                                </div>
                            </li>
                        @endisset
                        @isset($findRidePage->ride_features_option10)
                            <li>
                                <div>
                                    <input id="bordered-radio-10" type="checkbox" value="{{ $findRidePage->ride_features_option10 }}" name="features[]" {{ in_array($findRidePage->ride_features_option10, old('features', explode(';', $user->passenger_features))) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                    <label for="" class="ml-2 text-gray-900">{{ $findRidePage->ride_features_option10 }}</label>
                                </div>
                            </li>
                        @endisset
                        @isset($findRidePage->ride_features_option11)
                            <li>
                                <div>
                                    <input id="bordered-radio-11" type="checkbox" value="{{ $findRidePage->ride_features_option11 }}" name="features[]" {{ in_array($findRidePage->ride_features_option11, old('features', explode(';', $user->passenger_features))) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-none">
                                    <label for="" class="ml-2 text-gray-900">{{ $findRidePage->ride_features_option11 }}</label>
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