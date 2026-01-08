@extends('layouts.template')

@section('content')

<div class="mx-auto max-w-2xl lg:max-w-4xl my-6">
    <div class="bg-white rounded p-4 w-full col-span-12 md:col-span-9 mx-auto">
        <div class="pb-2 flex items-center justify-center">
            <p class="text-xl font-semibold">Step 5 of 5: Verify your phone number</p>
        </div>

        <div class="pb-2 flex items-center justify-center">
            <p class="text-gray-400">(Optional. Do it to be eligible for the "ProximaRide" and "Extra-Care Rides")</p>
        </div>

        <form method="POST" action="{{ route('step5to5.update',$user->id) }}">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4 mt-4">
                <div class="us_number mt-2">
                    <label for="">Phone number</label>
                    <div class="flex items-start space-x-4 mt-1">
                        <select name="country_code" id="country_code-dropdown" class="bg-gray-50 text-base lg:text-lg block border p-1.5 w-2/6 rounded border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600 text-gray-500">
                            <option value="+1 ">Canada (+1)</option>
                        </select>
                        <div class="w-4/6">
                            <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}" class=" block mt-1 border p-1.5 w-full rounded text-base md:text-lg border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600 {{ $errors->has('phone') ? 'border-red-500' : '' }}">
                            @error('phone')
                              <div class="relative tooltip -bottom-4 group-hover:flex">
                                <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                    <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                </div>
                              </div>
                            @endif
                        </div>
                    </div>

                    <div class="mt-6 flex items-center space-x-4">
                        <button type="submit" name="action" value="save" class="button-exp-fill">Save Unverified</button>
                        <button type="submit" name="action" value="send" class="button-exp-fill">Send Verification Code</button>
                    </div>
                </div>

                <div class="mt-4 flex justify-end md:col-span-2">
                    <a href="{{ route('profile', ['lang' => $selectedLanguage->abbreviation]) }}" class="px-6 py-2 mr-1 bg-blue-600 rounded text-white">Skip</a>
                </div>
            </div>
        </form>
    </div>
</div>

<form method="POST" action="{{ route('verify_number') }}">
    @csrf
    <div id="verifyModal" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                <div class="relative animate__animated animate__fadeIn transform overflow-hidden rounded bg-white text-center shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md">
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="mt-2">
                            <p class="text-left">{{ $step4Page->verify_code_label }}</p>
                            <div id="resendMessage" class="hidden text-center text-green-600 mb-2"></div>
                            <p class="text-center mt-2">Enter code</p>
                            <div class="flex justify-center mt-4 space-x-2">
                                <input type="hidden" name="step" value="1" class="w-10 h-10 text-center block mt-1 border p-1.5 text-base lg:text-lg rounded border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600">
                                <input type="text" name="code[]" maxlength="1" class="w-10 h-10 text-center block mt-1 border p-1.5 text-base lg:text-lg rounded border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600" id="code1">
                                <input type="text" name="code[]" maxlength="1" class="w-10 h-10 text-center block mt-1 border p-1.5 text-base lg:text-lg rounded border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600" id="code2">
                                <input type="text" name="code[]" maxlength="1" class="w-10 h-10 text-center block mt-1 border p-1.5 text-base lg:text-lg rounded border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600" id="code3">
                                <input type="text" name="code[]" maxlength="1" class="w-10 h-10 text-center block mt-1 border p-1.5 text-base lg:text-lg rounded border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600" id="code4">
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
                    <div class="px-4 pb-6 pt-4 sm:flex sm:flex-row sm:px-6 justify-center">
                        <div class="flex space-x-3">
                            <button type="button" id="resendCodeBtn" class="inline-flex justify-center rounded bg-primary px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-blue-400 sm:w-36">Resend Code</button>
                            <button type="submit" class="inline-flex justify-center rounded bg-primary px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-blue-400 sm:w-36">Verify</button>
                        </div>
                    </div>
                    <button type="button" id="closeModalBtn" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 text-2xl font-bold focus:outline-none">
                        &times;
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@section('script')
<script>
    document.getElementById('resendCodeBtn').addEventListener('click', function() {
        const phone = "{{ session('phone') }}";
        const country_code = "{{ session('country_code') }}";
        const country_id = "{{ session('country_id') }}";

        fetch("{{ route('resend_code') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                phone: phone,
                country_code: country_code,
                country_id: country_id,
                action: 'send'
            })
        })
        .then(response => response.json())
        .then(data => {
            const resendMessage = document.getElementById('resendMessage');
            if (data.success) {
                resendMessage.textContent = 'Verification code sent';
                resendMessage.classList.remove('hidden');
                // Hide message after 3 seconds
                setTimeout(() => {
                    resendMessage.classList.add('hidden');
                }, 5000);
            } else {
                resendMessage.textContent = 'Failed to resend verification code: ' + (data.message || 'Unknown error');
                resendMessage.classList.remove('hidden');
                resendMessage.classList.add('text-red-600');
                // Hide message after 3 seconds
                setTimeout(() => {
                    resendMessage.classList.add('hidden');
                    resendMessage.classList.remove('text-red-600');
                }, 5000);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            const resendMessage = document.getElementById('resendMessage');
            resendMessage.textContent = 'An error occurred while resending the code.';
            resendMessage.classList.remove('hidden');
            resendMessage.classList.add('text-red-600');
            setTimeout(() => {
                resendMessage.classList.add('hidden');
                resendMessage.classList.remove('text-red-600');
            }, 3000);
        });
    });
</script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const modal = document.getElementById("verifyModal");
    const closeModalBtn = document.getElementById("closeModalBtn");

    if (closeModalBtn && modal) {
        closeModalBtn.addEventListener("click", function() {
            modal.style.display = "none";
            document.body.style.overflow = "auto";
        });
    }

    modal.addEventListener("click", function(event) {
        if (event.target === modal) {
            modal.style.display = "none";
            document.body.style.overflow = "auto";
        }
    });

    document.addEventListener("keydown", function(e) {
        if (e.key === "Escape" && modal.style.display !== "none") {
            modal.style.display = "none";
            document.body.style.overflow = "auto";
        }
    });
});
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const inputs = document.querySelectorAll("input[name='code[]']");
    
        inputs.forEach((input, index) => {
            input.addEventListener("input", function() {
                if (this.value.length === 1 && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
            });
    
            input.addEventListener("keydown", function(event) {
                if (event.key === "Backspace" && this.value === "" && index > 0) {
                    inputs[index - 1].focus();
                }
            });
    
            input.addEventListener("paste", function(event) {
                event.preventDefault();
                const pastedData = event.clipboardData.getData("text").trim();
                if (pastedData.length === inputs.length) {
                    pastedData.split("").forEach((char, i) => {
                        if (inputs[i]) {
                            inputs[i].value = char;
                        }
                    });
                    inputs[inputs.length - 1].focus();
                }
            });
        });
    });
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const code1 = document.getElementById('code1');
    code1.focus();
    
    const inputs = document.querySelectorAll('input[name="code[]"]');
    
    inputs.forEach((input, index) => {
        input.addEventListener('input', function() {
            if (this.value.length === 1) {
                if (index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
            }
        });
        
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Backspace' && this.value.length === 0) {
                if (index > 0) {
                    inputs[index - 1].focus();
                }
            }
        });
    });
});
</script>
@endsection