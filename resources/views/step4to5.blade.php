@extends('layouts.template')

@section('content')

<div class="mx-auto max-w-2xl lg:max-w-4xl my-6">
    <div class="bg-white rounded p-4 w-full col-span-12 md:col-span-9 mx-auto">
        <div class="bg-white border border-gray-100 pb-8 px-4 shadow rounded-md sm:px-6 my-4">
            <div class="py-2 flex items-center justify-center">
                <h1 class="font-FuturaMdCnBT text-primary text-3xl md:text-4xl lg:text-5xl mb-4 mt-10">
                    @isset($step4Page->main_heading)
                        {{ $step4Page->main_heading }}
                    @endisset
                </h1>
            </div>
            <p for="" class=" text-black mt-2">
                {!! $step4Page->main_label ?? "If you are signing up as a driver, please note that to be eligible to post ProximaRide and Extra-Care Rides, you must state your vehicle details on every ride, and must upload a valid driver’s license " !!}
            </p>
            <p for="" class=" text-black mt-4">
                {!! $step4Page->sub_main_label ?? "If you intend to use ProximaRide as a passenger only, then this point is not applicable to you. You may “Skip” it" !!}
            </p>
            
            <!-- <div class="pb-2 flex items-center justify-start mt-3">
                <p class="text-red-500">*
                    @isset($step4Page->required_label)
                        {{ $step4Page->required_label }}
                    @endisset
                </p>
            </div> -->
            <form class="rounded-lg" method="POST" action="{{ route('step4to5.store', ['id' => $user->id, 'lang' => $selectedLanguage->abbreviation]) }}" enctype="multipart/form-data">
                @csrf

                <div class="bg-primary text-white rounded-t-lg text-xl mt-4 flex items-center justify-center space-x-2 p-4 font-FuturaMdCnBT">
                    {{ $step4Page->liecense_section_heading ?? "Step 4 - Your driver’s license" }}
                </div>

                <div class="bg-white rounded-b-lg overflow-hidden shadow-3xl grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4 p-4">
                    <div class="md:col-span-2">
                     
                        <label for="dropzone-file1"
                            class="flex flex-col items-center justify-center w-full h-auto border-2 border-gray-300 border-dashed rounded cursor-pointer bg-white hover:bg-gray-100">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6 p-4">
                                <img id="profile-image1" class="w-12 h-12 object-contain mb-3 cursor-pointer" src="{{ asset('assets/image-placeholder.png') }}">
                                <p class="text-sm lg:text-lg text-gray-900">
                                    @isset($step4Page->driver_liscense_photo_label)
                                        {{ $step4Page->driver_liscense_photo_label }}
                                    @endisset
                                </p>
                            </div>
                            <input id="dropzone-file1" name="driver_liscense" type="file" onchange="previewImage1(this)" class="hidden" />
                        </label>
                        @error('driver_liscense')
                        <div class="relative tooltip -bottom-4 group-hover:flex">
                            <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                                <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                            </div>
                        </div>
                        @enderror
                        <div class="mt-4 flex justify-center space-x-2 md:col-span-2">
                            <button type="button" onclick="showSkipConfirmation()" class="w-40 button-exp-fill">
                                @isset($step4Page->skip_license)
                                    {{ $step4Page->skip_license }}
                                @endisset
                            </button>
                            <button type="submit" id="nextButton" class="button-exp-fill w-40 opacity-50 cursor-not-allowed" disabled>
                                @isset($step4Page->next_button_label)
                                    {{ $step4Page->next_button_label }}
                                @endisset
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Skip Confirmation Modal -->
<div id="skipModal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
            <div class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg w-full modal-border">
                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="text-center w-full">
                        <h6 class="text-lg font-medium text-gray-900 mb-4">
                            @isset($step4Page->skip_confirmation_heading)
                                {{ $step4Page->skip_confirmation_heading }}
                            @endisset
                        </h6>
                        <p class="text-gray-600">
                            @isset($step4Page->skip_confirmation_message)
                                {{ $step4Page->skip_confirmation_message }}
                            @endisset
                        </p>
                    </div>
                </div>
                <div class="px-4 pb-6 pt-4 sm:flex sm:flex-row-reverse sm:px-6 justify-center gap-3">
                    <button type="button" onclick="confirmSkip()" class="inline-flex w-full justify-center rounded bg-primary px-3 py-2 font-FuturaMdCnBT text-lg text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-blue-400 sm:w-auto">Yes, skip it!</button>
                    <button type="button" onclick="hideSkipConfirmation()" class="inline-flex w-full justify-center rounded bg-gray-300 px-3 py-2 font-FuturaMdCnBT text-lg text-gray-700 hover:bg-gray-400 sm:w-auto">No, take me back</button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
    window.addEventListener("pageshow", function(event) {
        if (event.persisted) {
            window.location.reload();
        }
    });

    let sessionImage = "{{ session('uploaded_profile_image') }}";
    console.log(sessionImage);
    sessionStorage.setItem("uploaded_profile_image", "{{ session('uploaded_profile_image') }}");
    const profileImage1 = document.getElementById('profile-image1');

    function previewImage1(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                profileImage1.src = e.target.result;
                profileImage1.className = 'w-68 h-58 object-contain mb-3 cursor-pointer rounded-lg';
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function showSkipConfirmation() {
        document.getElementById('skipModal').classList.remove('hidden');
    }

    function hideSkipConfirmation() {
        document.getElementById('skipModal').classList.add('hidden');
    }

    function confirmSkip() {
        // Create a hidden form to submit with skip action
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("step4to5.store", ["id" => $user->id, "lang" => $selectedLanguage->abbreviation]) }}';
        
        // Add CSRF token
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';
        form.appendChild(csrfInput);
        
        // Add action input
        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.value = 'skip_license';
        form.appendChild(actionInput);
        
        document.body.appendChild(form);
        form.submit();
    }

    // Form validation for Step 4
    function validateStep4Form() {
        const fileInput = document.querySelector('input[name="driver_liscense"]');
        const nextButton = document.getElementById('nextButton');
        
        // Check if file is selected
        const isValid = fileInput && fileInput.files && fileInput.files.length > 0;
        
        if (isValid) {
            nextButton.disabled = false;
            nextButton.classList.remove('opacity-50', 'cursor-not-allowed');
            nextButton.classList.add('opacity-100');
        } else {
            nextButton.disabled = true;
            nextButton.classList.add('opacity-50', 'cursor-not-allowed');
            nextButton.classList.remove('opacity-100');
        }
    }

    // Add event listener to file input for real-time validation
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.querySelector('input[name="driver_liscense"]');
        if (fileInput) {
            fileInput.addEventListener('change', validateStep4Form);
        }
        
        // Initial validation check
        validateStep4Form();
    });
</script>
@endsection