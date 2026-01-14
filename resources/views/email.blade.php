@extends('layouts.template')

@section('content')
    <div class="grid grid-cols-12 gap-4 md:container md:mx-auto  my-6 md:my-10 xl:my-14 px-4 xl:px-0">
        @include('layouts.inc.profile_sidebar')

        <div class="bg-white border rounded p-4 border-gray-200 w-full col-span-12 lg:col-span-9 shadow">
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                    {{ session('error') }}
                </div>
            @endif
            <div class="pb-2">
                <h1 class="mb-0">
                    @isset($emailSettingPage->main_heading)
                        {{ $emailSettingPage->main_heading }}
                    @endisset
                </h1>
                <p>
                    @isset($emailSettingPage->email_description_text)
                        {{ $emailSettingPage->email_description_text }}
                    @endisset
                </p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-1 lg:grid-cols-1 w-full md:w-1/2 gap-4 mt-4">
                <form method="POST" action="{{ route('email.update', $user->id) }}">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="">
                            @isset($emailSettingPage->email_label)
                                {{ $emailSettingPage->email_label }}
                            @endisset
                        </label>
                        <input type="text" name="old_email" value="{{ $user->email }}" readonly
                            class="block mt-1 border p-1.5 w-full text-base lg:text-lg bg-gray-50 rounded border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600">
                    </div>
                    <div class="mt-3 flex justify-center">
                        <button id="showUpdateForm" type="button" class="button-exp-fill w-28"
                            @if ($errors->any()) class="button-exp-fill hidden" @else class="button-exp-fill block" @endif>
                            @isset($emailSettingPage->update_button_text)
                                {{ $emailSettingPage->update_button_text }}
                            @endisset
                        </button>
                    </div>

                    <div id="updateForm" @if ($errors->any()) class="block" @else class="hidden" @endif>
                        <div>
                            <label for="">
                                @isset($emailSettingPage->new_email_label)
                                    {{ $emailSettingPage->new_email_label }}
                                @endisset
                            </label>
                            <input type="text" name="email" value="{{ old('email') }}"
                                class=" block mt-1 border p-1.5 w-full text-base lg:text-lg rounded border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600">
                            @error('email')
                                <div class="relative tooltip -bottom-4 group-hover:flex mb-2">
                                    <div role="tooltip"
                                        class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                                        <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                    </div>
                                </div>
                            @enderror
                        </div>
                        <div class="mt-3">
                            <label for="">
                                @isset($emailSettingPage->confirm_email_label)
                                    {{ $emailSettingPage->confirm_email_label }}
                                @endisset
                            </label>
                            <input type="text" name="email_confirmation" value="{{ old('email_confirmation') }}"
                                class=" block mt-1 border p-1.5 w-full text-base lg:text-lg rounded border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600">
                                @error('email_confirmation')
                                <div class="relative tooltip -bottom-4 group-hover:flex mb-2">
                                    <div role="tooltip"
                                        class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                                        <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                    </div>
                                </div>
                            @enderror
                            </div>
                        {{-- <div class="mt-3">
                            <label for="">Current password</label>
                            <input type="password" name="password"
                                class="block mt-1 border p-1.5 w-full rounded text-base md:text-lg border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600">
                            @error('password')
                                <div class="relative tooltip -bottom-4 group-hover:flex">
                                    <div role="tooltip"
                                        class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                                        <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                    </div>
                                </div>
                            @enderror
                        </div> --}}
                        <div class="mt-3 flex justify-center w-full">
                            <button type="submit" class="button-exp-fill w-28">
                                @isset($emailSettingPage->save_btn_label)
                                    {{ $emailSettingPage->save_btn_label }}
                                @endisset
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <input type="hidden" id="selectedLangForUrl" value="{{ $selectedLanguage->abbreviation }}">
        </div>
    </div>
@endsection


@section('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const showUpdateFormBtn = document.getElementById('showUpdateForm');
    const updateForm = document.getElementById('updateForm');
    const selectedLang = document.getElementById('selectedLangForUrl');

    if (!showUpdateFormBtn || !updateForm || !selectedLang) return;

    // Check if form should be visible due to errors or URL
    const hasErrors = {{ $errors->any() ? 'true' : 'false' }};
    const isEditEmailUrl = window.location.pathname.includes('/edit-email');
    let isFormOpened = hasErrors || isEditEmailUrl;

    // Initialize form state based on errors or URL
    if (isFormOpened) {
        updateForm.style.display = 'block';
        updateForm.classList.remove('hidden');
        updateForm.classList.add('block');
        if (showUpdateFormBtn) {
            showUpdateFormBtn.style.display = 'none';
            showUpdateFormBtn.classList.add('hidden');
        }
        if (isEditEmailUrl && !hasErrors) {
            const lang = selectedLang.value;
            history.replaceState({ formVisible: true }, '', `/${lang}/edit-email`);
        }
    }

    // Button click handler
    if (showUpdateFormBtn) {
        showUpdateFormBtn.addEventListener('click', function() {
            if (isFormOpened) return;
            isFormOpened = true;

            updateForm.style.display = 'block';
            updateForm.classList.remove('hidden');
            updateForm.classList.add('block');
            this.style.display = 'none';
            this.classList.add('hidden');

            const lang = selectedLang.value;
            const newUrl = `/${lang}/edit-email`;
            history.pushState({ formVisible: true }, '', newUrl);
        });
    }

    // Handle browser back/forward
    window.addEventListener('popstate', function(event) {
        if (event.state && event.state.formVisible && !isFormOpened) {
            updateForm.style.display = 'block';
            updateForm.classList.remove('hidden');
            updateForm.classList.add('block');
            if (showUpdateFormBtn) {
                showUpdateFormBtn.style.display = 'none';
                showUpdateFormBtn.classList.add('hidden');
            }
            isFormOpened = true;
        } else if (!event.state || !event.state.formVisible) {
            if (!hasErrors) {
                updateForm.style.display = 'none';
                updateForm.classList.remove('block');
                updateForm.classList.add('hidden');
                if (showUpdateFormBtn) {
                    showUpdateFormBtn.style.display = 'block';
                    showUpdateFormBtn.classList.remove('hidden');
                }
                isFormOpened = false;
            }
        }
    });
});
</script>
@endsection
