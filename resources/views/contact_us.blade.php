@extends('layouts.template')

@section('content')
{!! RecaptchaV3::initJs() !!}

<div class="container mx-auto my-14">
    <div class="bg-white border rounded p-4 border-gray-200 shadow w-full md:w-[70%] mx-auto">
        <div class="flex flex-col md:flex-row items-center justify-between">
            <h1 class="mb-0 font-FuturaMdCnBT">
                @isset($contactUsPage->main_heading)
                    {{ $contactUsPage->main_heading }}
                @endisset
            </h1>
            <span class="text-red-500">
*
                @isset($contactProximaPage->mobile_indicate_required_field_label)
                {{ $contactProximaPage->mobile_indicate_required_field_label }}
                @endisset
            </span>
        </div>
        <div class="pb-2">
            <form method="POST" action="{{ route('contact_us.store') }}" enctype="multipart/form-data">
                @csrf
                {!! RecaptchaV3::field('register') !!}

                @if(session('success'))
                    <div class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                            <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                                <div
                                    class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                                    <button type="button" onclick="closeModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-500 z-50">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                    <div class="bg-white px-4 mt-10 sm:mt-1 pb-4 pt-16 sm:p-6 sm:pb-4 sm:pt-16">
                                        <div class="sm:flex sm:items-start justify-center">
                                            <!-- <div
                                                class="mx-auto h-16 w-16">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="4" stroke="currentColor" class="w-12 h-12 text-greenXS">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                                </svg>
                                            </div> -->
                                        </div>
                                        <div class="text-center sm:ml-4 sm:mt-0">
                                            <div class="w-full">
                                                <p class="text-lg text-center text-black">{!! session('success') !!}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="px-4 pb-6 pt-4  sm:flex sm:flex-row-reverse sm:px-6 justify-center">
                                        <a href=""
                                            class="inline-flex justify-center rounded bg-greenXS px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-greenXS sm:ml-3 w-auto">Close</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4 mt-4">
                    @isset($contactUsPage->mailing_address_label)
                        <div>
                            <label for="">{{ $contactProximaPage->your_full_name_label }} <span class="text-red-500">*</span> </label>
                            <input placeholder="{{ $contactUsPage->placeholder_name }}" type="text" name="name" value="{{ old('name') }}" class=" block mt-1 border p-1.5 w-full text-base lg:text-lg rounded border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600">
                            @error('name')
                              <div class="relative tooltip -bottom-4 group-hover:flex">
                                <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                    <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                </div>
                              </div>
                            @enderror
                        </div>
                    @endisset
                    @isset($contactUsPage->name_email_placeholder)
                        <div>
                            <label for="">{{ $contactProximaPage->your_email_address_label }} <span class="text-red-500">*</span></label>
                            <input placeholder="{{ $contactUsPage->placeholder_email }}" type="email" name="email" value="{{ old('email') }}" class=" block mt-1 border p-1.5 w-full text-base lg:text-lg rounded border-gray-300 italic focus:ring-none focus:outline-none focus:border-blue-600">
                            @error('email')
                              <div class="relative tooltip -bottom-4 group-hover:flex">
                                <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                    <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                </div>
                              </div>
                            @enderror
                        </div>
                    @endisset
                    @isset($contactUsPage->telephone_label)
                        <div>
                            <label for="">{{ $contactProximaPage->your_phone_label }}</label>
                            <input placeholder="{{ $contactUsPage->placeholder_phone }}" type="tel" name="phone" value="{{ old('phone') }}" class=" block mt-1 border p-1.5 w-full text-base lg:text-lg italic rounded border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600">
                            @error('phone')
                              <div class="relative tooltip -bottom-4 group-hover:flex">
                                <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                    <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                </div>
                              </div>
                            @enderror
                        </div>
                    @endisset
                    @isset($contactUsPage->message_placeholder)
                        <div class="md:col-span-2">
                            <label for="">{{ $contactProximaPage->your_message_label }} <span class="text-red-500">*</span></label>
                            <textarea placeholder="{{ $contactUsPage->placeholder_message }}" id="message" rows="5" name="message" class=" block mt-1 border p-1.5 w-full text-base lg:text-lg italic rounded border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600">{{ old('message') }}</textarea>
                            @error('message')
                              <div class="relative tooltip -bottom-4 group-hover:flex">
                                <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                    <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                </div>
                              </div>
                            @enderror
                        </div>
                    @endisset

                    @isset($contactProximaPage->submit_button_text)
                        <div class="md:col-span-2 flex justify-center">
                            <button type="submit" class="w-28 button-exp-fill">{{ $contactProximaPage->submit_button_text }}</button>
                        </div>
                    @endisset
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
@section('script')
<script>
function closeModal() {
    // Hide all modals
    document.querySelectorAll('.relative.z-50').forEach(modal => {
        modal.style.display = 'none';
    });

    // Also remove any session messages from the URL
    if (window.history.replaceState) {
        const cleanUrl = window.location.href.split('?')[0];
        window.history.replaceState({}, document.title, cleanUrl);
    }
}
document.addEventListener('click', function(event) {
    if (event.target.classList.contains('fixed') && event.target.classList.contains('inset-0')) {
        closeModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeModal();
    }
});
</script>

@endsection
