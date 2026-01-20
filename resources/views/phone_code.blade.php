@extends('layouts.template')

@section('content')

<div class="grid grid-cols-12 gap-4 container mx-auto my-14">
    @include('layouts.inc.profile_sidebar')

    <div class="bg-white border rounded p-4 border-gray-200 w-full col-span-12 md:col-span-9 shadow">
        @if(session('message'))
            <div id="deleteModal" class="relative z-50 delete-modal" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity delete-modal-backdrop" id="deleteModalBackdrop" onclick="closeDeleteModal()"></div>
                <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                    <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                        <div class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                            <button type="button" onclick="closeDeleteModal()" class="absolute top-3 right-3 text-gray-400 hover:text-gray-500">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                            <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                                <div class="sm:flex sm:items-start justify-center">
                                    <!-- <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full sm:mx-0 sm:h-10 sm:w-10 bg-green-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-lg text-white w-8 h-8" viewBox="0 0 16 16">
                                            <path d="M7.005 3.1a1 1 0 1 1 1.99 0l-.388 6.35a.61.61 0 0 1-1.214 0zM7 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0"/>
                                        </svg>
                                    </div> -->
                                </div>
                                <div class="text-center sm:ml-4 sm:mt-0 sm:text-left">
                                    <div class="mt-2 w-full">
                                        <p class="can-exp-p text-center">{{ session('message') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="px-4 pb-6 pt-4 flex items-center space-x-2 sm:space-x-4 sm:px-6 justify-center">
                                <input type="hidden" id="notificationId" value="3094">
                                <a href="#" onclick="closeDeleteModal()" class="button-exp-fill">Close</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="border-b pb-2">
            <h1 class="mb-0">My phone number</h1>
            <p class="text-gray-900">
                To be eligible to post "ProximaRide" and "Extra-Care Rides", you must verify your phone number
            </p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-1 lg:grid-cols-1 w-full md:w-1/2 gap-4 mt-4">
            @foreach ($phone_numbers as $phone_number)
                <div>
                    @if ($phone_number->verified === '1')
                        <p>Verified number</p>
                    @else
                        <p>Unverified number</p>
                    @endif
                    <div class="flex justify-between items-center mt-1 border p-1.5 w-full text-base lg:text-lg text-left bg-gray-50 rounded border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600">
                        <p>{{ $phone_number->phone }}</p>
                        @if ($phone_number->verified === '1')
                            <div>
                                <button type="button" class="button-exp-fill">Set as default</button>
                                <a href="{{ route('phone.destroy', $phone_number->id) }}" class="button-exp-fill py-1.5">Delete</a>
                            </div>
                        @else
                            <div>
                                <a href="{{ route('send_verification_code', $phone_number->id) }}" class="button-exp-fill py-1.5">Send verification code</a>
                                <a href="{{ route('phone.destroy', $phone_number->id) }}" class="button-exp-fill py-1.5">Delete</a>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<form method="POST" action="{{ route('verify_number') }}">
    @csrf
    <div id="verifyModal" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" id="verifyModalBackdrop" onclick="closeModal()"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                <div class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-lg bg-white text-center shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
                    <button type="button" onclick="closeModal()" class="absolute top-3 right-3 text-gray-400 hover:text-gray-500">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="mt-2">
                          <p class="text-left">Please enter the four digit code you received on your phone number</p>
                          <p class="text-center mt-4">Enter code</p>
                          <div class="flex justify-center mt-4 space-x-2">
                            <input type="text" name="code[]" maxlength="1" class="w-10 h-10 text-center block mt-1 border p-1.5 text-base lg:text-lg rounded border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600">
                            <input type="text" name="code[]" maxlength="1" class="w-10 h-10 text-center block mt-1 border p-1.5 text-base lg:text-lg rounded border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600">
                            <input type="text" name="code[]" maxlength="1" class="w-10 h-10 text-center block mt-1 border p-1.5 text-base lg:text-lg rounded border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600">
                            <input type="text" name="code[]" maxlength="1" class="w-10 h-10 text-center block mt-1 border p-1.5 text-base lg:text-lg rounded border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600">
                          </div>
                          @error('code')
                            <div class="relative tooltip -bottom-4 group-hover:flex left-0 right-0 mx-auto">
                              <div role="tooltip" class="mt-2 relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded mx-auto">
                                <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                              </div>
                            </div>
                          @endif
                        </div>
                    </div>
                    <div class="px-4 pb-6 pt-4 sm:flex sm:flex-row-reverse sm:px-6 justify-center">
                        <button type="submit" class="inline-flex w-full justify-center rounded bg-primary px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-blue-400 sm:ml-3 sm:w-36">Verify phone number</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

@endsection

@section('script')

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const inputs = document.querySelectorAll("input[name='code[]']");
    
        inputs.forEach((input, index) => {
            // Move to the next field on input
            input.addEventListener("input", function() {
                if (this.value.length === 1 && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
            });
    
            // Handle backspace to move to previous field
            input.addEventListener("keydown", function(event) {
                if (event.key === "Backspace" && this.value === "" && index > 0) {
                    inputs[index - 1].focus();
                }
            });
    
            // Paste event to split the code into inputs
            input.addEventListener("paste", function(event) {
                event.preventDefault();
                const pastedData = event.clipboardData.getData("text").trim();
                if (pastedData.length === inputs.length) {
                    pastedData.split("").forEach((char, i) => {
                        if (inputs[i]) {
                            inputs[i].value = char;
                        }
                    });
                    inputs[inputs.length - 1].focus(); // Move focus to the last field
                }
            });
        });

        // Show deleteModal if session message exists
        const deleteModal = document.getElementById('deleteModal');
        if (deleteModal && '{{ session('message') }}') {
            deleteModal.classList.remove('delete-modal');
            const backdrop = document.getElementById('deleteModalBackdrop');
            if (backdrop) {
                backdrop.classList.remove('delete-modal-backdrop');
            }
            document.body.style.overflow = 'hidden';
        }
    });
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get all code inputs
    const inputs = document.querySelectorAll('input[name="code[]"]');
    
    // Focus first input on page load
    if (inputs.length > 0) {
        inputs[0].focus();
    }
    
    // Add event listeners to all inputs
    inputs.forEach((input, index) => {
        // Handle input event (when user types/pastes)
        input.addEventListener('input', function(e) {
            if (this.value.length === 1) {
                // Move to next input if available
                if (index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
            }
        });
        
        // Handle keydown for backspace and arrow keys
        input.addEventListener('keydown', function(e) {
            // On backspace with empty input, move to previous
            if (e.key === 'Backspace' && this.value.length === 0 && index > 0) {
                inputs[index - 1].focus();
            }
            // Allow left arrow to move to previous input
            else if (e.key === 'ArrowLeft' && index > 0) {
                inputs[index - 1].focus();
                e.preventDefault(); // Prevent cursor movement within current input
            }
            // Allow right arrow to move to next input
            else if (e.key === 'ArrowRight' && index < inputs.length - 1) {
                inputs[index + 1].focus();
                e.preventDefault(); // Prevent cursor movement within current input
            }
        });
        
        // Handle paste event (to handle multi-digit paste)
        input.addEventListener('paste', function(e) {
            e.preventDefault();
            const pasteData = e.clipboardData.getData('text').trim();
            
            // Fill current and subsequent inputs with paste data
            for (let i = 0; i < pasteData.length && (index + i) < inputs.length; i++) {
                inputs[index + i].value = pasteData[i];
            }
            
            // Focus the last filled input
            const lastFilledIndex = Math.min(index + pasteData.length - 1, inputs.length - 1);
            inputs[lastFilledIndex].focus();
        });
    });

    // Add click event listener for outside click
    document.addEventListener('click', function(event) {
        const deleteModal = document.getElementById('deleteModal');
        const verifyModal = document.getElementById('verifyModal');
        const deleteModalBackdrop = document.getElementById('deleteModalBackdrop');
        const verifyModalBackdrop = document.getElementById('verifyModalBackdrop');

        if (deleteModal && !deleteModal.classList.contains('delete-modal')) {
            const modalContent = deleteModal.querySelector('.animate__fadeIn');
            if (modalContent && (!modalContent.contains(event.target) || (deleteModalBackdrop && deleteModalBackdrop.contains(event.target)))) {
                closeDeleteModal();
            }
        }
        if (verifyModal && !verifyModal.classList.contains('hidden')) {
            const modalContent = verifyModal.querySelector('.animate__fadeIn');
            if (modalContent && (!modalContent.contains(event.target) || (verifyModalBackdrop && verifyModalBackdrop.contains(event.target)))) {
                closeModal();
            }
        }
    });
});

function closeDeleteModal() {
    const deleteModal = document.getElementById('deleteModal');
    const deleteModalBackdrop = document.getElementById('deleteModalBackdrop');
    
    if (deleteModal && !deleteModal.classList.contains('delete-modal')) {
        deleteModal.classList.add('delete-modal');
        if (deleteModalBackdrop) {
            deleteModalBackdrop.classList.add('delete-modal-backdrop');
        }
    }
    document.body.style.overflow = 'auto'; // Restore scrolling when modal is closed
}

function closeModal() {
    const verifyModal = document.getElementById('verifyModal');
    const verifyModalBackdrop = document.getElementById('verifyModalBackdrop');
    
    if (verifyModal && !verifyModal.classList.contains('hidden')) {
        verifyModal.classList.add('hidden');
        if (verifyModalBackdrop) {
            verifyModalBackdrop.classList.add('hidden');
        }
    }
    document.body.style.overflow = 'auto'; // Restore scrolling when modal is closed
}
</script>

@endsection