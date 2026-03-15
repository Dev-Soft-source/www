@extends('layouts.template')

@section('content')
    <div class="grid grid-cols-12 gap-4 md:container md:mx-auto  my-6 md:my-10 xl:my-14 px-4 xl:px-0">
        @include('layouts.inc.profile_sidebar')

        <div class="bg-white border rounded p-4 border-gray-200 w-full col-span-12 lg:col-span-9 shadow">
            @if (session('message'))
                <div id="myModal" class="relative z-50" id="delete_message_confirmation" aria-labelledby="modal-title"
                    role="dialog" aria-modal="true">
                    <div onclick="closeModal()" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                        <div
                            class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                            <div
                                class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
                                <button type="button" onclick="closeModal()"
                                    class="absolute top-3 right-3 text-gray-400 hover:text-gray-500">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                                    <div class="sm:flex sm:items-start justify-center">
                                        <!-- <div
                                                    class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full sm:mx-0 sm:h-10 sm:w-10 bg-green-500">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-lg text-white w-8 h-8" viewBox="0 0 16 16">
                                                        <path d="M7.005 3.1a1 1 0 1 1 1.99 0l-.388 6.35a.61.61 0 0 1-1.214 0zM7 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0"/>
                                                    </svg>
                                                </div> -->
                                    </div>
                                    <div class="text-center sm:ml-4 sm:mt-0 sm:text-left">
                                        <div class="mt-2 w-full text-center">
                                            <div class="can-exp-p text-center flex flex-col items-center">
                                                {!! session('message') !!}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="px-4 pb-6 pt-4 flex items-center space-x-2 sm:space-x-4 sm:px-6 justify-center">
                                    <input type="hidden" id="notificationId" value="3094">
                                    <a href="#" onclick="closeModal()"
                                        class="button-exp-fill">{{ $siteText['close_btn_text'] }} </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div id="myModal" class="relative z-50" id="delete_message_confirmation" aria-labelledby="modal-title"
                    role="dialog" aria-modal="true">
                    <div onclick="closeModal()" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                        <div
                            class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                            <div
                                class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
                                <button type="button" onclick="closeModal()"
                                    class="absolute top-3 right-3 text-gray-400 hover:text-gray-500">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                                    <div class="sm:flex sm:items-start justify-center">
                                        <!-- <div
                                                    class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full sm:mx-0 sm:h-10 sm:w-10 bg-green-500">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-lg text-white w-8 h-8" viewBox="0 0 16 16">
                                                        <path d="M7.005 3.1a1 1 0 1 1 1.99 0l-.388 6.35a.61.61 0 0 1-1.214 0zM7 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0"/>
                                                    </svg>
                                                </div> -->
                                    </div>
                                    <div class="text-center sm:ml-4 sm:mt-0 sm:text-left">
                                        <div class="mt-2 w-full">
                                            <p class="can-exp-p text-center">{{ session('error') }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="px-4 pb-6 pt-4 flex items-center space-x-2 sm:space-x-4 sm:px-6 justify-center">
                                    <input type="hidden" id="notificationId" value="3094">
                                    <a href="#" onclick="closeModal()"
                                        class="button-exp-fill">{{ $siteText['close_btn_text'] }} </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- AJAX Error Popup Modal (for dynamic messages from JS) --}}
            <div id="ajaxErrorModal" class="relative z-50 hidden" aria-labelledby="modal-title" role="dialog"
                aria-modal="true">
                <div onclick="closeAjaxErrorModal()" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity">
                </div>
                <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                    <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                        <div
                            class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border1">
                            <button type="button" onclick="closeAjaxErrorModal()"
                                class="absolute top-3 right-3 text-gray-400 hover:text-gray-500">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                            <div class="sm:flex sm:items-start justify-center pt-5">
                                <div class="text-3xl text-center font-FuturaMdCnBT text-black">
                                    <svg width="64px" height="64px" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg" stroke="#ff0000">
                                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                            stroke-linejoin="round"></g>
                                        <g id="SVGRepo_iconCarrier">
                                            <path d="M12 10V13" stroke="#db0000" stroke-width="2"
                                                stroke-linecap="round"></path>
                                            <path d="M12 16V15.9888" stroke="#db0000" stroke-width="2"
                                                stroke-linecap="round"></path>
                                            <path
                                                d="M10.2518 5.147L3.6508 17.0287C2.91021 18.3618 3.87415 20 5.39912 20H18.6011C20.126 20 21.09 18.3618 20.3494 17.0287L13.7484 5.147C12.9864 3.77538 11.0138 3.77538 10.2518 5.147Z"
                                                stroke="#db0000" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round"></path>
                                        </g>
                                    </svg>
                                </div>
                            </div>
                            <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                                <div class="text-center">
                                    <div class="mt-2 w-full">
                                        <p id="ajaxErrorModalMessage" class="can-exp-p text-center"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="px-4 pb-6 pt-4 flex items-center space-x-2 sm:space-x-4 sm:px-6 justify-center">
                                <a href="#" onclick="closeAjaxErrorModal(); return false;"
                                    class="button-exp-fill">{{ $siteText['close_btn_text'] }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class=" pb-2">
                <h1 class="mb-0">
                    @isset($phoneSetting->main_heading)
                        {{ $phoneSetting->main_heading }}
                    @endisset
                </h1>
                <p class="text-gray-900">
                    @if (isset($phone_numbers) && count($phone_numbers) > 0)
                        @isset($phoneSetting->phone_no_description_text)
                            {!! $phoneSetting->phone_no_description_text !!}
                        @endisset
                    @else
                        @isset($phoneSetting->phone_no_description_text2)
                            {!! $phoneSetting->phone_no_description_text2 !!}
                        @endisset
                    @endif
                </p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-1 lg:grid-cols-1 w-full gap-4 mt-4">
                @foreach ($phone_numbers as $phone_number)
                    <div>
                        @if ($phone_number->verified === '1')
                            <p class="text-primary text-lg md:text-xl lg:text-2xl font-FuturaMdCnBT font-medium mb-2">
                                @isset($phoneSetting->verified_number_label)
                                    {{ $phoneSetting->verified_number_label }}
                                @endisset
                            </p>
                        @else
                            <p class="text-primary text-lg md:text-xl lg:text-2xl font-FuturaMdCnBT font-medium mb-2">
                                @isset($phoneSetting->unverified_number_label)
                                    {{ $phoneSetting->unverified_number_label }}
                                @endisset
                            </p>
                        @endif
                        <div
                            class="flex flex-col sm:flex-col md:flex-row lg:flex-row justify-between items-center gap-2 mt-1 border p-2 md:p-1.5 w-full text-base lg:text-lg text-left bg-gray-50 rounded border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600">
                            <div class="w-full">
                                {{-- <label for="">Country <span class="text-red-500">*</span></label> --}}
                                <select disabled name="country" id="country-dropdown"
                                    class="bg-white text-base lg:text-lg block mt-1 border p-1.5 w-full rounded border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600 placeholder:text-gray-900 {{ $errors->has('country') ? 'border-red-500' : '' }}">
                                    @foreach ($countries as $country)
                                        <option value="{{ $country->id }}"
                                            {{ old('country', $phone_number->country_id) == $country->id ? 'selected' : '' }}>
                                            {{ $country->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('country')
                                    <div class="tooltip-error shadow-lg">{{ $message }}</div>
                                @enderror
                            </div>
                            <p class="w-full text-center">{{ $phone_number->phone }}</p>
                            <div class="w-full">
                                @if ($phone_number->verified === '1')
                                    @if ($phone_number->default !== '1')
                                        <div class="flex items-center justify-end space-x-1">
                                            <a href="{{ route('phone.set_default', $phone_number->id) }}"
                                                class="button-exp-green-fill py-1.5 w-36 px-2 text-center inline-block ">
                                                @isset($phoneSetting->set_as_default_label)
                                                    {{ $phoneSetting->set_as_default_label }}
                                                @endisset
                                            </a>
                                            <a href="{{ route('phone.destroy', $phone_number->id) }}"
                                                class="delete-button button-exp-red-fill py-1.5 w-24 px-2 text-center inline-block ">
                                                @isset($phoneSetting->delete_button_text)
                                                    {{ $phoneSetting->delete_button_text }}
                                                @endisset
                                            </a>
                                        </div>
                                    @else
                                        <div class="flex items-center justify-end space-x-1">
                                            <span class="bg-green-600 text-white px-4 py-1.5 rounded-md">
                                                {{ $phoneSetting->primary_number_label ?? 'Primary Number' }}
                                            </span>
                                            <a href="{{ route('phone.destroy', $phone_number->id) }}"
                                                class="delete-button button-exp-red-fill py-1.5 w-24 px-2 text-center inline-block">
                                                @isset($phoneSetting->delete_button_text)
                                                    {{ $phoneSetting->delete_button_text }}
                                                @endisset
                                            </a>
                                        </div>
                                    @endif
                                @else
                                    <div class="flex items-center justify-end space-x-1">
                                        <button type="button" onclick="sendVerificationCode({{ $phone_number->id }})"
                                            class="button-exp-fill py-1.5 w-48 px-2 text-center inline-block ">
                                            @isset($phoneSetting->web_send_verification_code_button_text)
                                                {{ $phoneSetting->web_send_verification_code_button_text }}
                                            @endisset
                                        </button>
                                        <a href="{{ route('phone.destroy', $phone_number->id) }}"
                                            class="delete-button button-exp-red-fill py-1.5 w-24 px-2 text-center inline-block ">
                                            @isset($phoneSetting->delete_button_text)
                                                {{ $phoneSetting->delete_button_text }}
                                            @endisset
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="flex justify-center mt-2">
                    <button id="showForm" type="button"
                        @if ($errors->any()) class="button-exp-fill hidden" @else class="button-exp-fill w-fit" @endif>
                        @if (isset($phone_numbers) && count($phone_numbers) > 0)
                            @isset($phoneSetting->add_another_phone_number_button_text)
                                {{ $phoneSetting->add_another_phone_number_button_text }}
                            @endisset
                        @else
                            @isset($phoneSetting->add_phone_number_button_text)
                                {{ $phoneSetting->add_phone_number_button_text }}
                            @endisset
                        @endif
                    </button>
                </div>

                <form id="addForm" method="POST" action="{{ route('phone.store') }}"
                    @if ($errors->any()) class="" @else class="hidden" @endif>
                    @csrf
                    <div class="mt-6">
                        <p class="text-primary text-lg md:text-xl lg:text-2xl font-FuturaMdCnBT font-medium mb-2">
                            @if (isset($phone_numbers) && count($phone_numbers) > 0)
                                @isset($phoneSetting->add_another_phone_number_button_text)
                                    {{ $phoneSetting->add_another_phone_number_button_text }}
                                @endisset
                            @else
                                @isset($phoneSetting->add_phone_number_button_text)
                                    {{ $phoneSetting->add_phone_number_button_text }}
                                @endisset
                            @endif
                        </p>
                    </div>
                    <div class="mt-2">
                        <div class="flex flex-col sm:flex-col md:flex-row lg:flex-row items-start gap-4 mt-1">
                            <div class="w-full md:w-3/6">
                                <label for="">{{ $phoneSetting->country_id_label_web ?? 'Country' }} <span
                                        class="text-red-500">*</span></label>
                                <select name="country" id="country-dropdown"
                                    class="bg-white text-base lg:text-lg block mt-1 border p-1.5 w-full rounded border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600 placeholder:text-gray-900 {{ $errors->has('country') ? 'border-red-500' : '' }}">
                                    @foreach ($countries as $country)
                                        <option value="{{ $country->id }}" data-dial-code="{{ $country->dial_code }}"
                                            {{ old('country', $user->country) == $country->id ? 'selected' : '' }}>
                                            {{ $country->name }}
                                            {{ $country->dial_code ? '(' . $country->dial_code . ')' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('country')
                                    <div class="tooltip-error shadow-lg">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="w-full hidden">
                                <label
                                    for="">{{ $phoneSetting->country_code_label_web ?? 'Country Code' }}</label>
                                <input type="tel" name="country_code" value="{{ old('country_code', '+1') }}"
                                    maxlength="5" readonly
                                    class="bg-gray-100 block mt-1 border p-1.5 w-full rounded text-base md:text-lg border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600">
                            </div>
                            <div class="w-full">
                                <label for="">{{ $phoneSetting->phone_number_label_web ?? 'Phone  number' }}<span
                                        class="text-red-500">*</span></label>
                                <input type="tel" pattern="[0-9]+" name="phone" value="{{ old('phone') }}"
                                    placeholder="{{ $phoneSetting->phone_number_placeholder ?? 'Numbers only, with area code' }}"
                                    maxlength="15" inputmode="numeric"
                                    class="font-FuturaMdCnBT block mt-1 border p-1.5 w-full rounded text-base md:text-lg border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600">
                                @error('phone')
                                    <div class="tooltip-error shadow-lg">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        @error('full_phone')
                            <div class="tooltip-error shadow-lg">{{ $message }}</div>
                        @enderror
                    </div>
                    <input type="hidden" id="error_count" value="{{ $errors->any() ?? '' }}">
                    <div class="mt-6 flex items-center space-x-4 justify-center">
                        <button type="submit" name="action" value="save" class="button-exp-fill">
                            @isset($phoneSetting->save_phoneno_button_text)
                                {{ $phoneSetting->save_phoneno_button_text }}
                            @endisset
                        </button>
                        <button type="button" onclick="sendVerificationCodeFromForm()" class="button-exp-sky-fill"
                            id="sendVerifyBtn">
                            @isset($phoneSetting->web_send_verification_code_button_text)
                                {{ $phoneSetting->web_send_verification_code_button_text }}
                            @endisset
                        </button>
                    </div>
                </form>
                
                <div id="modal" class="relative z-50 hidden" aria-labelledby="modal-title" role="dialog"
                    aria-modal="true">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeModal()"></div>
                    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                        <div
                            class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                            <div
                                class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg w-full modal-border1">
                                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                                    <button type="button" onclick="closeModal()"
                                        class="absolute top-4 right-4 text-gray-400 hover:text-gray-500">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>

                                    <div class="sm:flex sm:items-start justify-center">
                                        <div class="text-3xl text-center font-FuturaMdCnBT text-black">
                                            <svg width="64px" height="64px" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg" stroke="#ff0000">
                                                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                                    stroke-linejoin="round"></g>
                                                <g id="SVGRepo_iconCarrier">
                                                    <path d="M12 10V13" stroke="#db0000" stroke-width="2"
                                                        stroke-linecap="round"></path>
                                                    <path d="M12 16V15.9888" stroke="#db0000" stroke-width="2"
                                                        stroke-linecap="round"></path>
                                                    <path
                                                        d="M10.2518 5.147L3.6508 17.0287C2.91021 18.3618 3.87415 20 5.39912 20H18.6011C20.126 20 21.09 18.3618 20.3494 17.0287L13.7484 5.147C12.9864 3.77538 11.0138 3.77538 10.2518 5.147Z"
                                                        stroke="#db0000" stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round"></path>
                                                </g>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="mt-2 w-full">
                                        <p class="text-center can-exp-p">{{ $phoneSetting->remove_description }}</p>
                                    </div>
                                </div>
                                <!--footer-->
                                <div class="px-4 pb-6 pt-4  sm:flex sm:flex-row-reverse sm:px-6 justify-center">
                                    <button type="button" id="hide-modal"
                                        class="no-button inline-flex w-full justinline-flex justify-center rounded bg-blue-500 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-blue-400 sm:ml-3 sm:w-auto">
                                        {{ $successMessage->no_go_back_button_text ?? 'No, go back' }}</button>
                                    <a id="delete-card-link"
                                        class="cursor-pointer button-exp-red-fill inline-flex w-full justinline-flex justify-center rounded px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm sm:ml-3 sm:w-auto">
                                        {{ $successMessage->yes_remove_it_button_text ?? 'Yes, remove it' }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" id="selectedLangForUrl" value="{{ $selectedLanguage->abbreviation }}">

            </div>

        </div>
    </div>

    {{-- Verification Modal --}}
    <form method="POST" action="{{ route('verify_number') }}" id="verifyForm">
        @csrf
        <div id="verifyModal" class="relative z-50 hidden" aria-labelledby="modal-title" role="dialog"
            aria-modal="true">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" id="verifyModalBackdrop"
                onclick="closeVerifyModal()"></div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                    <div
                        class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-lg bg-white text-center shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
                        <button type="button" onclick="closeVerifyModal()"
                            class="absolute top-3 right-3 text-gray-400 hover:text-gray-500">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">

                            <div class="mt-2">
                                <h6 class="text-left">
                                    @isset($phoneSetting->verify_code_heading)
                                        {{ $phoneSetting->verify_code_heading }}
                                    @endisset
                                </h6>
                                <p class="text-center mt-4">
                                    @isset($phoneSetting->verify_code_label_1)
                                        {{ $phoneSetting->verify_code_label_1 }}
                                    @endisset
                                </p>
                                <div class="flex justify-center mt-4 space-x-2">
                                    <input type="text" name="code[]" maxlength="1"
                                        class="w-10 h-10 text-center block mt-1 border p-1.5 text-base lg:text-lg rounded border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600">
                                    <input type="text" name="code[]" maxlength="1"
                                        class="w-10 h-10 text-center block mt-1 border p-1.5 text-base lg:text-lg rounded border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600">
                                    <input type="text" name="code[]" maxlength="1"
                                        class="w-10 h-10 text-center block mt-1 border p-1.5 text-base lg:text-lg rounded border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600">
                                    <input type="text" name="code[]" maxlength="1"
                                        class="w-10 h-10 text-center block mt-1 border p-1.5 text-base lg:text-lg rounded border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600">
                                </div>
                                @error('code')
                                    <div class="tooltip-error shadow-lg">{{ $message }}</div>
                                @enderror
                                </div>
                            </div>

                            <div class="px-4 pb-6 pt-4 sm:flex sm:flex-row-reverse sm:px-6 justify-center">
                                <button type="submit"
                                    class="inline-flex w-auto justify-center rounded bg-primary px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-blue-400 sm:ml-3 sm:w-42">
                                    @isset($phoneSetting->verify_button_label_1)
                                        {{ $phoneSetting->verify_button_label_1 }}
                                    @endisset
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    @endsection

    @section('script')
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

        <script>
            
            function initializeCountryCode() {
                let countryCodeInput = document.querySelector('#addForm input[name="country_code"]');
                let countryDropdown = document.querySelector('#addForm select[name="country"]');

                if (countryDropdown && countryCodeInput) {
                    // Remove any existing event listeners to avoid duplicates
                    countryDropdown.removeEventListener('change', countryChangeHandler);

                    // Add the event listener
                    countryDropdown.addEventListener('change', countryChangeHandler);

                    // Set initial country code if country is already selected
                    updateCountryCode();
                }
            }

            function countryChangeHandler() {
                updateCountryCode();
            }

            function updateCountryCode() {
                let countryCodeInput = document.querySelector('#addForm input[name="country_code"]');
                let countryDropdown = document.querySelector('#addForm select[name="country"]');

                console.log('updateCountryCode called'); // Debug
                console.log('countryDropdown:', countryDropdown); // Debug
                console.log('countryCodeInput:', countryCodeInput); // Debug

                if (countryDropdown && countryCodeInput && countryDropdown.selectedIndex >= 0) {
                    const selectedOption = countryDropdown.options[countryDropdown.selectedIndex];
                    const dialCode = selectedOption.getAttribute('data-dial-code');

                    console.log('Country selected:', selectedOption.text, 'Dial code:', dialCode); // Debug log
                    console.log('Selected option value:', selectedOption.value); // Debug

                    if (dialCode && dialCode.trim() !== '') {
                        countryCodeInput.value = dialCode;
                        console.log('Set country code to:', dialCode); // Debug
                    } else {
                        countryCodeInput.value = '+1'; // Default to US
                        console.log('No dial code found, using default +1'); // Debug
                    }
                } else {
                    console.log('Elements not found or no selection'); // Debug
                }
            }

            document.getElementById('showForm').addEventListener('click', function() {
                document.getElementById('addForm').style.display = 'block';
                this.style.display = 'none';
                const lang = document.getElementById('selectedLangForUrl').value;

                const newUrl = `/${lang}/add-phone`;
                history.pushState(null, '', newUrl);

                // Initialize country code functionality when form is shown
                setTimeout(() => {
                    console.log('Initializing country code after form shown'); // Debug
                    initializeCountryCode();
                    setupPhoneLimiting();
                }, 100);
            });

            // Initialize country code functionality on page load if form is visible (errors exist)
            document.addEventListener('DOMContentLoaded', function() {
                console.log('DOM Content Loaded'); // Debug
                const addForm = document.getElementById('addForm');
                if (addForm && !addForm.classList.contains('hidden')) {
                    console.log('Form is visible, initializing country code'); // Debug
                    initializeCountryCode();
                    setupPhoneLimiting();
                }
            });

            function setupPhoneLimiting() {
                let countryCodeInput = document.querySelector('#addForm input[name="country_code"]');
                let phoneInput = document.querySelector('#addForm input[name="phone"]');

                if (phoneInput) {
                    // Restrict to numbers only and enforce max 15 digits
                    function enforceNumericInput(e) {
                        // Allow: backspace, delete, tab, escape, enter
                        if ([46, 8, 9, 27, 13].indexOf(e.keyCode) !== -1 ||
                            // Allow: Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
                            (e.keyCode === 65 && e.ctrlKey === true) ||
                            (e.keyCode === 67 && e.ctrlKey === true) ||
                            (e.keyCode === 86 && e.ctrlKey === true) ||
                            (e.keyCode === 88 && e.ctrlKey === true) ||
                            // Allow: home, end, left, right
                            (e.keyCode >= 35 && e.keyCode <= 39)) {
                            return;
                        }
                        // Ensure that it is a number and stop the keypress if not
                        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                            e.preventDefault();
                        }
                    }

                    function sanitizePhoneInput() {
                        // Remove any non-numeric characters
                        let value = phoneInput.value.replace(/\D/g, '');
                        // Limit to 15 digits
                        if (value.length > 15) {
                            value = value.substring(0, 15);
                        }
                        phoneInput.value = value;
                    }

                    // Remove existing listeners to avoid duplicates
                    phoneInput.removeEventListener('keydown', enforceNumericInput);
                    phoneInput.removeEventListener('input', sanitizePhoneInput);
                    phoneInput.removeEventListener('paste', sanitizePhoneInput);

                    // Add new listeners
                    phoneInput.addEventListener('keydown', enforceNumericInput);
                    phoneInput.addEventListener('input', sanitizePhoneInput);
                    phoneInput.addEventListener('paste', function() {
                        setTimeout(sanitizePhoneInput, 0);
                    });
                }
            }

            document.addEventListener('DOMContentLoaded', function() {
                const deleteButtons = document.querySelectorAll('.delete-button');
                const modal = document.getElementById('modal');
                const modalContent = modal.querySelector('.relative.animate__animated');
                const deleteLink = document.getElementById('delete-card-link');
                const hideModalButton = document.getElementById('hide-modal');

                let deleteUrl = '';

                deleteButtons.forEach(function(button) {
                    button.addEventListener('click', function(event) {
                        event.preventDefault();
                        deleteUrl = event.target.href;
                        modal.classList.remove('hidden');
                        modal.classList.add('block');
                    });
                });

                deleteLink.addEventListener('click', function() {
                    window.location.href = deleteUrl;
                });

                hideModalButton.addEventListener('click', function() {
                    modal.classList.add('hidden');
                    modal.classList.remove('block');
                });


                // Close modal when clicking outside
                modal.addEventListener('click', function(event) {
                    if (!modalContent.contains(event.target)) {
                        closeModal();
                    }
                });
            });

            function closeModal() {
                const modal = document.getElementById('modal');
                const myModal = document.getElementById('myModal');
                if (modal) {
                    modal.classList.add('hidden');
                    modal.classList.remove('block');
                }
                if (myModal) {
                    myModal.classList.add('hidden');
                    myModal.classList.remove('block');
                }
            }

            function showAjaxErrorModal(message) {
                const modal = document.getElementById('ajaxErrorModal');
                const messageEl = document.getElementById('ajaxErrorModalMessage');
                if (modal && messageEl) {
                    messageEl.textContent = message;
                    modal.classList.remove('hidden');
                    modal.classList.add('block');
                    document.body.style.overflow = 'hidden';
                }
            }

            function closeAjaxErrorModal() {
                const modal = document.getElementById('ajaxErrorModal');
                if (modal) {
                    modal.classList.add('hidden');
                    modal.classList.remove('block');
                    document.body.style.overflow = 'auto';
                }
            }

            function sendVerificationCode(phoneNumberId) {
                const lang = document.getElementById('selectedLangForUrl').value;
                const url = `/${lang}/send-verification-code/${phoneNumberId}`;

                fetch(url, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            openVerifyModal();
                        } else {
                            console.error(data.message || 'Error sending verification code');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        console.error('Error sending verification code. Please try again.');
                    });
            }

            function sendVerificationCodeFromForm() {
                // Get form data
                const form = document.getElementById('addForm');
                const country = form.querySelector('select[name="country"]').value;
                const countryCode = form.querySelector('input[name="country_code"]').value;
                const phone = form.querySelector('input[name="phone"]').value;

                // Clear previous errors
                form.querySelectorAll('.tooltip').forEach(tooltip => {
                    tooltip.classList.add('hidden');
                });

                // Validate inputs
                if (!country || !countryCode || !phone) {
                    showAjaxErrorModal('Please fill in all fields');
                    return;
                }

                const lang = document.getElementById('selectedLangForUrl').value;
                const url = `/${lang}/phone/store-and-verify`;

                // Show loading state
                const button = document.getElementById('sendVerifyBtn');
                const originalText = button.innerHTML;
                button.disabled = true;
                button.innerHTML = 'Sending...';

                // Create FormData
                const formData = new FormData();
                formData.append('country', country);
                formData.append('country_code', countryCode);
                formData.append('phone', phone);
                formData.append('action', 'send');
                formData.append('_token', '{{ csrf_token() }}');

                fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        button.disabled = false;
                        button.innerHTML = originalText;

                        if (data.success) {
                            openVerifyModal();
                        } else {
                            // Show validation errors if any
                            if (data.errors) {
                                let errorMessage = '';
                                for (let field in data.errors) {
                                    errorMessage += data.errors[field].join('\n') + '\n';
                                }
                                showAjaxErrorModal(errorMessage.trim());
                            } else {
                                showAjaxErrorModal(data.message || 'Error sending verification code');
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        button.disabled = false;
                        button.innerHTML = originalText;
                        showAjaxErrorModal('Error sending verification code. Please try again.');
                    });
            }

            function openVerifyModal() {
                const verifyModal = document.getElementById('verifyModal');
                if (verifyModal) {
                    verifyModal.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';

                    // Focus first input
                    const inputs = verifyModal.querySelectorAll('input[name="code[]"]');
                    if (inputs.length > 0) {
                        inputs[0].focus();
                    }
                }
            }

            function closeVerifyModal() {
                const verifyModal = document.getElementById('verifyModal');
                if (verifyModal) {
                    verifyModal.classList.add('hidden');
                    document.body.style.overflow = 'auto';

                    // Clear inputs
                    const inputs = verifyModal.querySelectorAll('input[name="code[]"]');
                    inputs.forEach(input => input.value = '');
                }
            }

            // Auto-focus and auto-move between verification code inputs
            document.addEventListener('DOMContentLoaded', function() {
                const codeInputs = document.querySelectorAll('#verifyModal input[name="code[]"]');

                codeInputs.forEach((input, index) => {
                    // Move to next input on input
                    input.addEventListener('input', function() {
                        if (this.value.length === 1 && index < codeInputs.length - 1) {
                            codeInputs[index + 1].focus();
                        }
                    });

                    // Handle backspace to move to previous field
                    input.addEventListener('keydown', function(event) {
                        if (event.key === 'Backspace' && this.value === '' && index > 0) {
                            codeInputs[index - 1].focus();
                        } else if (event.key === 'ArrowLeft' && index > 0) {
                            codeInputs[index - 1].focus();
                            event.preventDefault();
                        } else if (event.key === 'ArrowRight' && index < codeInputs.length - 1) {
                            codeInputs[index + 1].focus();
                            event.preventDefault();
                        }
                    });

                    // Handle paste event
                    input.addEventListener('paste', function(event) {
                        event.preventDefault();
                        const pastedData = event.clipboardData.getData('text').trim();
                        if (pastedData.length === codeInputs.length) {
                            pastedData.split('').forEach((char, i) => {
                                if (codeInputs[i]) {
                                    codeInputs[i].value = char;
                                }
                            });
                            codeInputs[codeInputs.length - 1].focus();
                        }
                    });
                });
            });
        </script>
    @endsection
