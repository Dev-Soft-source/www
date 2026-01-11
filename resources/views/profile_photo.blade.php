@extends('layouts.template')

@section('content')

<div class="grid grid-cols-12 gap-4 md:container md:mx-auto  my-6 md:my-10 xl:my-14 px-4 xl:px-0">
    @include('layouts.inc.profile_sidebar')

    <div class="bg-white border rounded p-4 border-gray-200 w-full col-span-12 lg:col-span-9 shadow">
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

        <div class="mt-4">
            <h1 class="mb-0">
                @isset($profilePhotoPage->main_heading)
                    {{ $profilePhotoPage->main_heading }}
                @endisset
            </h1>
        </div>

        <form method="POST" action="{{ route('profile.photo.update',$user->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="">
                <div class="flex flex-col items-center justify-center w-full md:w-1/2">
                    <div class="mt-6 mb-2 text-left w-full">
                        <span>{!! $step2Page->sub_heading_text ?? "If you are signing up as a driver, then please note that to be eligible to post ProximaRide and Extra-Care Rides, you must upload your profile photo" !!}</span>
                    </div>
                    <label for="dropzone-file"
                        class="flex flex-col items-center justify-center w-full h-auto border-2 border-gray-300 border-dashed rounded cursor-pointer bg-white hover:bg-gray-100">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6 p-4">
                            @if ($user->profile_image != '')
                            <img id="profile-image" src="{{ $user->profile_image }}" class="h-32 mb-4">
                            @else
                                <img id="profile-image" class="w-12 h-12 object-contain mb-4 cursor-pointer" src="{{ asset('assets/image-placeholder.png')}}">
                            @endif
                            <p class="text-sm lg:text-base text-gray-900">
                                @isset($profilePhotoPage->upload_profile_photo_placeholder)
                                    {{ $profilePhotoPage->upload_profile_photo_placeholder }}
                                @endisset
                                <!-- <span class="text-primary">
                                    @isset($profilePhotoPage->choose_file_placeholder)
                                        {{ $profilePhotoPage->choose_file_placeholder }}
                                    @endisset
                                </span> -->
                            </p>
                            <p class="text-sm lg:text-base text-gray-900 font-normal">
                                @isset($profilePhotoPage->images_option_placeholder)
                                    {{ $profilePhotoPage->images_option_placeholder }}
                                @endisset
                            </p>
                        </div>
                        <input id="dropzone-file" name="image" type="file" onchange="previewImage(this)" class="hidden" />
                        @if (session('uploaded_image'))
                            <input type="hidden" name="existing_image" value="{{ session('uploaded_image') }}">
                        @endif
                    </label>

                    @error('image')
                        <div class="relative tooltip -bottom-4 group-hover:flex">
                            <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/4 rounded" >
                                <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                            </div>
                        </div>
                    @enderror


                    <div class="mt-4 flex justify-center md:justify-start">
                        <button type="submit" class="submitBtn button-exp-fill {{ isset($user->profile_image) && $user->profile_image != "" ? "disabled:bg-primary/20 cursor-not-allowed disabled:border-none" : "" }} w-28" {{ isset($user->profile_image) && $user->profile_image != "" ? "disabled" : "" }}>
                            @isset($profilePhotoPage->save_button_text)
                                {{ $profilePhotoPage->save_button_text }}
                            @endisset
                        </button>
                    </div>
                </div>
                        
            </div>
        </form>
    </div>
</div>


@endsection

@section('script')

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
    function previewImage(input) {
        const profileImage = document.getElementById('profile-image');
        if (input.files && input.files[0]) {

            $(".submitBtn").removeAttr('disabled');
            $(".submitBtn").removeClass('disabled:bg-primary/20 cursor-not-allowed disabled:border-none');
            const reader = new FileReader();

            reader.onload = function(e) {
                profileImage.src = e.target.result;
            };

            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

@endsection
