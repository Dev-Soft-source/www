@extends('layouts.template')

@section('content')

    <div class="grid grid-cols-12 gap-4 md:container md:mx-auto  my-6 md:my-10 xl:my-14 px-4 xl:px-0">
        @include('layouts.inc.profile_sidebar')

        <div class="bg-white border rounded p-4 border-gray-200 w-full col-span-12 lg:col-span-9 shadow">
            @if (session('message'))
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

            <div class="my-4 flex justify-between items-center">
                <h1 class="mb-0">
                    @isset($myVehiclePage->main_heading)
                    {{ $myVehiclePage->main_heading }}
                @endisset</h1>
                <a href="{{ route('profile.vehicle.create', ['lang' => $selectedLanguage->abbreviation]) }}"
                    class="button-exp-fill">
                    @isset($myVehiclePage->add_vehicle_button_text)
                    {{ $myVehiclePage->add_vehicle_button_text }}
                @endisset
            </a>
            </div>
            <div class="max-h-[52rem] overflow-y-auto pr-2">
                @if (!empty($vehicles) && count($vehicles) > 0)
                    @foreach ($vehicles as $vehicle)
                        <div class="evem:bg-white odd:bg-gray-100 rounded border border-gray-100 shadow-md p-3 md:p-6 mt-3">
                            <div>
                                <div class='flex flex-col sm:flex-col md:flex-row lg:flex-row md:items-center justify-between space-x-2 md:space-x-4'>
                                    <div class="flex items-start space-x-4 w-full">
                                        <div class="w-20 md:w-28 h-20 md:h-28 bg-gray-50 border rounded-full overflow-hidden">
                                            <img class="w-full h-full object-contain rounded-full" src="{{ $vehicle->image }}"
                                                alt="">
                                        </div>
                                        <div class="w-[70%]">
                                            <div class="flex items-center gap-2 mb-1">
                                                <h6 class='text-xl md:text-2xl text-blue-500 leading-7'>
                                                    {{ $vehicle->year }} {{ $vehicle->make }} {{ $vehicle->model }}
                                                </h6>
                                                @if($vehicle->primary_vehicle == '1')
                                                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                                        {{ $myVehiclePage->primary_vehicle_label ?? "Primary" }}
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="text-gray-900">
                                                {{ $vehicle->liscense_no }}
                                            </p>
                                            <p>{{ $vehicle->car_type }}</p>
                                        </div>
                                    </div>
                                    <div
                                        class="flex mt-4 md:mt-0 md:justify-end items-center gap-2">
                                        <a href="{{ route('profile.vehicle.edit', ['lang' => $selectedLanguage->abbreviation, 'id' => $vehicle->id]) }}"
                                            class="button-exp-fill whitespace-nowrap w-32">
                                            @isset($myVehiclePage->edit_vehicle_button_text)
                                            {{ $myVehiclePage->edit_vehicle_button_text }}
                                        @endisset
                                        </a>
                                        <button type="button" onclick="toggleModalCard('card-modal', {{ $vehicle->id }}, '{{$selectedLanguage->abbreviation}}')"
                                            class="button-exp-fill whitespace-nowrap w-32">
                                            @isset($myVehiclePage->remove_vehicle_button_text)
                                                {{ $myVehiclePage->remove_vehicle_button_text }}
                                            @endisset
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p>
                        @isset($myVehiclePage->no_vehicle_message)
                            {{ $myVehiclePage->no_vehicle_message }}
                        @endisset
                    </p>
                @endif
            </div>
        </div>
    </div>



<div class="hidden overflow-x-hidden overflow-y-auto fixed inset-0 z-50 outline-none focus:outline-none justify-center items-center" id="card-modal">
    <div class="relative h-screen my-6 mx-auto flex items-center justify-center w-full">
        <!--content-->
        <div
            class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg w-full modal-border">
            <button type="button"  onclick="toggleModalCard('card-modal')" class="absolute top-3 right-3 text-gray-400 hover:text-gray-500">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
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
                <div class="text-center sm:ml-4 sm:mt-0 sm:text-left">
                    <div class="">
                        <h3 class="text-3xl text-center font-FuturaMdCnBT font-medium text-gray-900 mb-4" id="modal-title">{!! session('heading') !!}</h3>
                    </div>
                    <div class="mt-2 w-full">
                        <p class="can-exp-p text-center">{{ $myVehiclePage->delete_photo_message  }}</p>
                    </div>
                </div>
            </div>
            <div class="px-4 pb-6 pt-4 flex items-center space-x-2 sm:space-x-4 sm:px-6 justify-center">
                <a id="delete-card-link" href="#"
                    class="inline-flex w-full justify-center rounded bg-red-500 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white whitespace-nowrap hover:text-white hover:shadow-lg shadow-sm hover:bg-red-400 sm:ml-3 sm:w-24">Remove</a>
                <button type="button" onclick="toggleModalCard('card-modal')"
                    class="button-exp-fill sm:w-24">Cancel</button>
            </div>
        </div>
    </div>
</div>
<div class="hidden opacity-25 fixed inset-0 z-40 bg-black" id="card-modal-backdrop"></div>


<script>
    function toggleModalCard(modalId, cardId = null, lang = null) {
        let modal = document.getElementById(modalId);
        let backdrop = document.getElementById(modalId + "-backdrop");

        if (modal.classList.contains("hidden")) {
            modal.classList.remove("hidden");
            backdrop.classList.remove("hidden");

            // Update the delete link if cardId is provided
            if (cardId) {
                let deleteLink = document.getElementById("delete-card-link");

                deleteLink.href = `/${lang}/profile/vehicle/delete/${cardId}`;
            }
        } else {
            modal.classList.add("hidden");
            backdrop.classList.add("hidden");
        }
    }
</script>
@endsection


