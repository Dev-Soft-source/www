@props([
    'title' => 'Upload Error',
    'message' => 'The selected image is too large.',
    'buttonLabel' => 'OK',
    'modalBorderClass' => 'modal-border1',
])

<div id="imageSizeErrorModal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity z-50"
    aria-labelledby="image-size-error-modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
            <div
                class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg w-full {{ $modalBorderClass }}">
                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start justify-center">
                        <svg width="64px" height="64px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#ff0000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M12 10V13" stroke="#db0000" stroke-width="2" stroke-linecap="round"></path> <path d="M12 16V15.9888" stroke="#db0000" stroke-width="2" stroke-linecap="round"></path> <path d="M10.2518 5.147L3.6508 17.0287C2.91021 18.3618 3.87415 20 5.39912 20H18.6011C20.126 20 21.09 18.3618 20.3494 17.0287L13.7484 5.147C12.9864 3.77538 11.0138 3.77538 10.2518 5.147Z" stroke="#db0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
                    </div>
                    <div class="text-center w-full">
                        <h3 class="font-FuturaMdCnBT text-gray-700 mb-4">
                            {{ $title }}
                        </h3>
                        <p class="text-center text-gray-600">
                            {{ $message }}
                        </p>
                    </div>
                </div>
                <div class="px-4 pb-6 pt-4 sm:flex sm:flex-row-reverse sm:px-6 justify-center gap-3">
                    <button type="button" onclick="hideImageSizeErrorModal()"
                        class="inline-flex w-full justify-center rounded bg-primary px-3 py-2 font-FuturaMdCnBT text-lg text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-blue-400 sm:w-auto">
                        {{ $buttonLabel }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function hideImageSizeErrorModal() {
        const modal = document.getElementById('imageSizeErrorModal');
        if (modal) {
            modal.classList.add('hidden');
        }
    }
</script>
