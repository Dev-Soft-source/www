@extends('layouts.template')

@section('content')
    <div class="container mx-auto my-10 md:my-14 px-4">
        <div class="pb-2">
            <h1 class="mb-0 font-FuturaMdCnBT">
                @isset($coffeeWallPage->main_heading)
                    {{ $coffeeWallPage->main_heading }}
                @endisset
            </h1>
        </div>

        <div class="pb-2">

            @isset($coffeeWallPage->main_text)
                @php
                    $storyUrl = route('coffee_on_wall_story', ['lang' => $selectedLanguage->abbreviation ?? 'en']);
                    $mainText = preg_replace(
                        '/\{\{\s*route\s*\(\s*[\'"]coffee_on_wall_story[\'"].*?\}\}\s*/s',
                        $storyUrl,
                        $coffeeWallPage->main_text,
                    );
                @endphp
                {!! $mainText !!}
            @endisset
        </div>
        <div class="text-right md:mr-20 text-red-500 text-lg">
            <span class="text-red-500">*</span> {{ $coffeeWallPage->required_field_label ?? 'Indicate required field' }}
        </div>
        <div class="relative flex flex-col min-w-0 break-words bg-white w-full mb-6 shadow-lg rounded">
            <div class="px-4 pb-5 flex-auto">
                <div class="tab-content tab-space">
                    <div class="block" id="tab-profile">
                        <form id="payment-form" method="POST" novalidate
                            action="{{ url((isset($selectedLanguage) ? $selectedLanguage->abbreviation : 'en') . '/coffee-on-the-wall') }}"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="space-y-4">
                                <div class="mt-6">
                                    <div class="mt-6">
                                        <div class="bg-white rounded-lg overflow-visible shadow-3xl">
                                            <div class="text-2xl bg-primary text-white py-2 px-4 rounded-t-lg">
                                                <h3 class="text-2xl">
                                                    @isset($coffeeWallPage->frequency_label)
                                                        {!! $coffeeWallPage->frequency_label !!}
                                                    @endisset
                                                    <span class="">*</span>
                                                </h3>
                                            </div>
                                            <div class="p-4">
                                                <div class="">
                                                    <ul
                                                        class="text-sm font-medium text-center text-gray-500 rounded-md md:rounded-lg shadow-sm grid grid-cols-3 border-2 dark:divide-gray-700 dark:text-gray-400">
                                                        <li class="focus-within:z-10">
                                                            <input type="radio" id="one_time" name="frequency"
                                                                value=""
                                                                {{ old('frequency', 'monthly') == '' ? 'checked' : '' }}
                                                                class="hidden peer">
                                                            <label for="one_time" id="one_time_label"
                                                                class="text-lg md:text-2xl font-FuturaMdCnBT font-medium py-4 shadow-lg rounded-l-md flex items-center justify-center h-16 bg-gray-50 border border-gray-100 leading-normal cursor-pointer hover:shadow-md hover:border-2 hover:border-green-500 peer-checked:border-green-500 peer-checked:border-2 peer-checked:text-green-500">
                                                                {{ $coffeeWallPage->quarterly_label ?? 'One time' }}
                                                            </label>
                                                        </li>
                                                        <li class="focus-within:z-10">
                                                            <input type="radio" id="quarterly" name="frequency"
                                                                value="weekly"
                                                                {{ old('frequency', 'monthly') == 'weekly' ? 'checked' : '' }}
                                                                class="hidden peer">
                                                            <label for="quarterly" id="quarterly_label"
                                                                class="text-lg md:text-2xl font-FuturaMdCnBT font-medium py-4 shadow-lg flex items-center justify-center h-16 bg-gray-50 border border-gray-100 leading-normal cursor-pointer hover:shadow-md hover:border-2 hover:border-green-500 peer-checked:border-green-500 peer-checked:border-2 peer-checked:text-green-500">
                                                                {{ $coffeeWallPage->semi_annually_label ?? 'Weekly' }}
                                                            </label>
                                                        </li>
                                                        <li class="focus-within:z-10">
                                                            <input type="radio" id="monthly" name="frequency"
                                                                value="monthly"
                                                                {{ old('frequency', 'monthly') == 'monthly' ? 'checked' : '' }}
                                                                class="hidden peer">
                                                            <label for="monthly" id="monthly_label"
                                                                class="text-lg md:text-2xl font-FuturaMdCnBT font-medium py-4 shadow-lg rounded-r-md flex items-center justify-center h-16 bg-gray-50 border border-gray-100 leading-normal cursor-pointer hover:shadow-md hover:border-2 hover:border-green-500 peer-checked:border-green-500 peer-checked:border-2 peer-checked:text-green-500">
                                                                {{ $coffeeWallPage->monthly_label ?? 'Monthly' }}
                                                            </label>
                                                        </li>
                                                    </ul>

                                                    <div class="">
                                                        <div class="bg-white p-4">
                                                            <ul id="packages-dropdown"
                                                                class="my-8 grid grid-cols-2 md:grid-cols-6 gap-4">
                                                                @foreach ($packages as $package)
                                                                    <li>
                                                                        <input type="radio"
                                                                            id="package-{{ $package->id }}"
                                                                            value="{{ $package->id }}" name="package"
                                                                            {{ old('custom_amount') ? '' : (old('package', $package->is_default ? $package->id : '') == $package->id ? 'checked' : '') }}
                                                                            class="package-checkbox hidden peer">
                                                                        <label for="package-{{ $package->id }}"
                                                                            class="bg-gray-50 rounded-md border shadow text-base md:text-2xl font-FuturaMdCnBT flex items-center justify-center h-24 md:h-28 hover:shadow-md border-gray-100 cursor-pointer peer-checked:border-green-500 peer-checked:border-2 peer-checked:text-green-500 hover:border-2 hover:border-green-500">
                                                                            ${{ $package->price }}
                                                                        </label>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                            <div class="mt-2 min-h-[2.5rem]">
                                                                @error('package')
                                                                    <div class="tooltip-error shadow-lg">{{ $message }}
                                                                    </div>
                                                                @enderror
                                                            </div>
                                                            <div class="w-full mt-4">
                                                                <div id="custom_field" class="">
                                                                    <div class="relative flex items-center gap-2">
                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                            fill="none" viewBox="0 0 24 24"
                                                                            stroke-width="1.5" stroke="currentColor"
                                                                            class="absolute left-2 h-7 text-gray-500">
                                                                            <path stroke-linecap="round"
                                                                                stroke-linejoin="round"
                                                                                d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                                        </svg>
                                                                        <input type="number" name="custom_amount"
                                                                            id="custom_amount_input" min="1"
                                                                            value="{{ old('custom_amount') }}"
                                                                            class="block mt-1 border-2 p-2.5 w-full rounded border-blue-500 focus:ring-1 focus:outline-none focus:border-green-500 pl-10 h-20 text-lg"
                                                                            placeholder="{{ $coffeeWallPage->custom_amount_label }}">
                                                                    </div>
                                                                    <div class="mt-2 min-h-[2.5rem]">
                                                                        @error('custom_amount')
                                                                            <div class="tooltip-error shadow-lg">
                                                                                {{ $message }}</div>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2">
                                        <div class="mb-1 mt-9 bg-primary text-white py-2 px-4 rounded col-span-2">
                                            <h3 class=" text-2xl">
                                                {{ $coffeeWallPage->designation_label ?? 'Designation' }}
                                                <span class="text-white">*</span>
                                            </h3>
                                        </div>
                                        <div class="mt-1">
                                            <ul id="designation-dropdown"
                                                class="my-8 grid grid-cols-2 md:grid-cols-4 gap-4">
                                                <li>
                                                    <input type="checkbox" id="designation-1" value="All"
                                                        name="designation[]"
                                                        {{ old('designation') ? (is_array(old('designation')) && in_array('All', old('designation')) ? 'checked' : '') : 'checked' }}
                                                        class="hidden designation-checkbox designation-all">
                                                    <label for="designation-1" id="designation-label-1"
                                                        class="bg-gray-50 rounded-md border shadow text-lg md:text-2xl font-FuturaMdCnBT flex items-center justify-center h-24 text-center p-4 hover:shadow-md cursor-pointer border-green-500 border-2 text-green-500 hover:border-green-500">
                                                        {{ $coffeeWallPage->designation_option1 ?? 'All' }}
                                                    </label>
                                                </li>
                                                <li>
                                                    <input type="checkbox" id="designation-2" value="Students"
                                                        name="designation[]"
                                                        {{ old('designation') ? (is_array(old('designation')) && in_array('Students', old('designation')) ? 'checked' : '') : '' }}
                                                        class="hidden designation-checkbox designation-individual">
                                                    <label for="designation-2" id="designation-label-2"
                                                        class="bg-gray-50 rounded-md border shadow text-lg md:text-2xl font-FuturaMdCnBT flex items-center justify-center h-24 text-center p-4 hover:shadow-md border-gray-100 cursor-pointer hover:border-2 hover:border-green-500">
                                                        {{ $coffeeWallPage->designation_option2 ?? 'Students' }}
                                                    </label>
                                                </li>
                                                <li>
                                                    <input type="checkbox" id="designation-3" value="Female passengers"
                                                        name="designation[]"
                                                        {{ old('designation') ? (is_array(old('designation')) && in_array('Female passengers', old('designation')) ? 'checked' : '') : '' }}
                                                        class="hidden designation-checkbox designation-individual">
                                                    <label for="designation-3" id="designation-label-3"
                                                        class="bg-gray-50 rounded-md border shadow text-lg md:text-2xl font-FuturaMdCnBT flex items-center justify-center h-24 text-center p-4 hover:shadow-md border-gray-100 cursor-pointer hover:border-2 hover:border-green-500">
                                                        {{ $coffeeWallPage->designation_option3 ?? 'Female passengers' }}
                                                    </label>
                                                </li>
                                                <li>
                                                    <input type="checkbox" id="designation-4" value="Visible minorities"
                                                        name="designation[]"
                                                        {{ old('designation') ? (is_array(old('designation')) && in_array('Visible minorities', old('designation')) ? 'checked' : '') : '' }}
                                                        class="hidden designation-checkbox designation-individual">
                                                    <label for="designation-4" id="designation-label-4"
                                                        class="bg-gray-50 rounded-md border shadow text-lg md:text-2xl font-FuturaMdCnBT flex items-center justify-center h-24 text-center p-4 hover:shadow-md border-gray-100 cursor-pointer hover:border-2 hover:border-green-500">
                                                        {{ $coffeeWallPage->designation_option4 ?? 'Visible minorities' }}
                                                    </label>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2">
                                        <div class="mb-1 mt-9 bg-primary text-white py-2 px-4 rounded col-span-2">
                                            <h3 class=" text-2xl">{{ $coffeeWallPage->contact_infomation_label }}
                                                <span class="text-white">*</span>
                                            </h3>
                                        </div>
                                        <div class="w-full mt-4">
                                            <label for="anonymous" class="flex items-center justify-between w-full mb-1">
                                                <div class="flex items-center gap-2 w-full">
                                                    <input type="checkbox" name="anonymous" value="1"
                                                        id="anonymous" {{ old('anonymous') == '1' ? 'checked' : '' }}
                                                        class="h-5 w-5">
                                                    <span
                                                        class="text-base md:text-lg">{{ $coffeeWallPage->annually_label ?? 'Make donation anonymous' }}</span>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                    <div id="name_field"
                                        class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2">
                                        <div class="w-full mt-1">
                                            <label for="name" class="flex items-center justify-between w-full mb-1">
                                                <div class="flex items-center gap-1 w-full">
                                                    @isset($coffeeWallPage->name_label)
                                                        {!! $coffeeWallPage->name_label !!}
                                                    @endisset
                                                    <span class="text-red-500">*</span>
                                                </div>
                                            </label>
                                            <input type="text" id="name" name="name"
                                                value="{{ old('name') }}"
                                                class=" block mt-1 border p-1.5 w-full text-base lg:text-lg rounded border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600 {{ $errors->has('name') ? 'border-red-500' : '' }}">
                                            @error('name')
                                                <div class="tooltip-error shadow-lg">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div id="email_field"
                                        class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2">
                                        <div class="mt-1">
                                            <label for="email">
                                                @isset($coffeeWallPage->email_label)
                                                    {!! $coffeeWallPage->email_label !!}
                                                @endisset
                                                <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" name="email" value="{{ old('email') }}"
                                                class=" block mt-1 border p-1.5 w-full text-base lg:text-lg rounded border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600 {{ $errors->has('email') ? 'border-red-500' : '' }}">
                                            @error('email')
                                                <div class="tooltip-error shadow-lg">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div id="phone_field"
                                        class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2">
                                        <div class="mt-1">
                                            <label for="phone">
                                                @isset($coffeeWallPage->phone_label)
                                                    {!! $coffeeWallPage->phone_label !!}
                                                @endisset
                                            </label>
                                            <input type="text" name="phone" value="{{ old('phone') }}"
                                                class=" block mt-1 border p-1.5 w-full text-base lg:text-lg rounded border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600 {{ $errors->has('phone') ? 'border-red-500' : '' }}">
                                            @error('phone')
                                                <div class="tooltip-error shadow-lg">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div id="notify_field"
                                        class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2">
                                        <div class="mt-4">
                                            <label for="notify_coffee_used" class="flex items-center gap-2">
                                                <input type="checkbox" name="notify_coffee_used" value="1"
                                                    id="notify_coffee_used"
                                                    {{ old('notify_coffee_used') == '1' ? 'checked' : '' }}
                                                    class="h-5 w-5">
                                                <span class="text-base md:text-lg">
                                                    @isset($coffeeWallPage->notify_coffee_used_label)
                                                        {{ $coffeeWallPage->notify_coffee_used_label }}
                                                    @endisset
                                                </span>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor"
                                                    class="bi bi-exclamation-circle-fill text-black cursor-help inline-block ml-1 align-middle flex-shrink-0"
                                                    data-tippy-content="{{ $coffeeWallPage->notify_coffee_used_tooltip ?? '' }}"
                                                    viewBox="0 0 16 16">
                                                    <path
                                                        d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 4zm.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2z" />
                                                </svg>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2">
                                        <div class="mb-1 mt-9 bg-primary text-white py-2 px-4 rounded col-span-2">
                                            <h3 class=" text-2xl">
                                                @isset($coffeeWallPage->select_payment_method_label)
                                                    {{ $coffeeWallPage->select_payment_method_label }}
                                                @endisset
                                                <span class="text-white">*</span>
                                            </h3>
                                        </div>
                                        <div>
                                            <div
                                                class="flex flex-col md:flex-row gap-4 md:justify-normal justify-between md:gap-x-8 items-start md:items-center mt-2 p-1.5">
                                                <div class="flex items-center gap-2">
                                                    <input id="credit-card" type="radio" value="stripe"
                                                        name="payment_method"
                                                        class="w-5 h-5 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-2 accent-blue-600"
                                                        checked>
                                                    <label for="credit-card" class="text-base md:text-lg cursor-pointer">
                                                        @isset($coffeeWallPage->credit_card_label)
                                                            {{ $coffeeWallPage->credit_card_label }}
                                                        @endisset
                                                    </label>
                                                </div>

                                                <div class="flex items-center gap-2">
                                                    <input id="paypal" type="radio" value="paypal"
                                                        name="payment_method"
                                                        class="w-5 h-5 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-2 accent-blue-600"
                                                        {{ old('payment_method') === 'paypal' ? 'checked' : '' }}>
                                                    <label for="paypal" class="text-base md:text-lg cursor-pointer">
                                                        @isset($coffeeWallPage->paypal_label)
                                                            {{ $coffeeWallPage->paypal_label }}
                                                        @endisset
                                                    </label>
                                                </div>
                                            </div>
                                            @error('payment_method')
                                                <div class="tooltip-error shadow-lg">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4 mt-4">
                                    <div id="credit-card-div"
                                        class="hidden mt-4 p-4 bg-white border border-b-4 border-gray-400 rounded">
                                        <div>
                                            <label
                                                for="name_on_card">{{ $paymentSettingDetail->name_on_card_label ?? 'Cardholder’s name' }}</label>
                                            <input type="text" id="name_on_card" name="name_on_card"
                                                value="{{ old('name_on_card') }}"
                                                class="block mt-1 border p-1.5 w-full rounded text-base md:text-lg border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600">
                                            <div id="card-name-error"
                                                class="{{ $errors->has('name_on_card') ? '' : 'hidden' }}">
                                                <div class="tooltip-error shadow-lg mt-2">
                                                    @error('name_on_card')
                                                        {{ $message }}
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-4">
                                            <label class="font-normal text-gray-700">
                                                {{ $paymentSettingDetail->card_number_label ?? 'Card details' }}
                                            </label>
                                            <div id="card-element" name="card_element"
                                                class="block mt-1 border p-2.5 w-full rounded text-base md:text-lg border-gray-300">
                                            </div>
                                            <div id="card-errors"
                                                class="{{ $errors->has('card_element') ? '' : 'hidden' }}">
                                                <div class="tooltip-error shadow-lg mt-2">
                                                    @error('card_element')
                                                        {{ $message }}
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <!-- Disclaimer Checkboxes -->
                                <div class="grid grid-cols-1 gap-4 mt-6">
                                    <!-- First Disclaimer -->
                                    <div class="disclaimer-container group">
                                        <div class="flex items-start gap-2">
                                            <input id="donation_acknowledgment" type="checkbox"
                                                name="donation_acknowledgment" value="1"
                                                {{ old('donation_acknowledgment') == '1' ? 'checked' : '' }}
                                                class="h-5 w-5 mt-1 flex-shrink-0 accent-blue-600" required>
                                            <label for="donation_acknowledgment" class="text-base md:text-lg">
                                                @isset($coffeeWallPage->donation_acknowledgment_label)
                                                    {{ $coffeeWallPage->donation_acknowledgment_label }}
                                                @endisset
                                            </label>
                                        </div>
                                        @error('donation_acknowledgment')
                                            <div class="tooltip-error shadow-lg">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Second Disclaimer -->
                                    <div class="disclaimer-container group">
                                        <div class="flex items-start gap-2">
                                            <input id="terms_privacy" type="checkbox" name="terms_privacy"
                                                value="1" {{ old('terms_privacy') == '1' ? 'checked' : '' }}
                                                class="h-5 w-5 mt-1 flex-shrink-0 accent-blue-600" required>
                                            <label for="terms_privacy" class="text-base md:text-lg">
                                                @isset($coffeeWallPage->agree_terms_label)
                                                    {!! $coffeeWallPage->agree_terms_label !!}
                                                @endisset
                                            </label>
                                        </div>
                                        @error('terms_privacy')
                                            <div class="tooltip-error shadow-lg">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4 mt-4">
                                    <div class="md:col-span-2 flex justify-center mt-4">
                                        <button type="submit" class="button-exp-fill px-6 py-3">
                                            {{ $coffeeWallPage->pay_button_label ?? 'Make Someone\'s Day' }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FAQ Sections -->
    <div class="container mx-auto my-10 px-4">
        <div class="space-y-4">
            <!-- FAQ for the Donors -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <button
                    class="w-full bg-blue-600 text-white px-6 py-4 text-left text-xl font-FuturaMdCnBT flex items-center justify-between hover:bg-blue-700 focus:outline-none"
                    onclick="toggleFAQ('donors')">
                    <span>{{ $coffeeWallPage->faq_donors_label }}</span>
                    <svg id="donors-icon" class="w-6 h-6 transform transition-transform duration-200" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div id="donors-faq" class="hidden p-6 bg-gray-50 border-t">
                    <div class="space-y-4 text-gray-700">
                        <div>
                            <h4 class="font-semibold mb-2">{{ $coffeeWallPage->faq_donors_1_question }}</h4>
                            <p>{{ $coffeeWallPage->faq_donors_1_answer }}</p>
                        </div>
                        <div>
                            <h4 class="font-semibold mb-2">{{ $coffeeWallPage->faq_donors_2_question }}</h4>
                            <p>{{ $coffeeWallPage->faq_donors_2_answer }}</p>
                        </div>
                        <div>
                            <h4 class="font-semibold mb-2">{{ $coffeeWallPage->faq_donors_3_question }}</h4>
                            <p>{{ $coffeeWallPage->faq_donors_3_answer }}</p>
                        </div>
                        <div>
                            <h4 class="font-semibold mb-2">{{ $coffeeWallPage->faq_donors_4_question }}</h4>
                            <p>{{ $coffeeWallPage->faq_donors_4_answer }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FAQ for the Beneficiary -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <button
                    class="w-full bg-blue-600 text-white px-6 py-4 text-left text-xl font-FuturaMdCnBT flex items-center justify-between hover:bg-blue-700 focus:outline-none"
                    onclick="toggleFAQ('beneficiary')">
                    <span>{{ $coffeeWallPage->faq_beneficiary_label }}</span>
                    <svg id="beneficiary-icon" class="w-6 h-6 transform transition-transform duration-200" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div id="beneficiary-faq" class="hidden p-6 bg-gray-50 border-t">
                    <div class="space-y-4 text-gray-700">
                        <div>
                            <h4 class="font-semibold mb-2">{{ $coffeeWallPage->faq_beneficiary_1_question }}</h4>
                            <p>{{ $coffeeWallPage->faq_beneficiary_1_answer }}</p>
                        </div>
                        <div>
                            <h4 class="font-semibold mb-2">{{ $coffeeWallPage->faq_beneficiary_2_question }}</h4>
                            <p>{{ $coffeeWallPage->faq_beneficiary_2_answer }}</p>
                        </div>
                        <div>
                            <h4 class="font-semibold mb-2">{{ $coffeeWallPage->faq_beneficiary_3_question }}</h4>
                            <p>{{ $coffeeWallPage->faq_beneficiary_3_answer }}</p>
                        </div>
                        <div>
                            <h4 class="font-semibold mb-2">{{ $coffeeWallPage->faq_beneficiary_4_question }}</h4>
                            <p>{{ $coffeeWallPage->faq_beneficiary_4_answer }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (session('message'))
        <div class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full justify-center p-4 text-center items-center sm:p-0">
                    <div
                        class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
                        <button onclick="closeModal()" class="absolute top-2 right-2 p-1 rounded-full hover:bg-gray-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
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
                            <div class="text-center">
                                <div class="">
                                    <p class="text-3xl font-FuturaMdCnBT text-center text-black">{!! session('heading') !!}
                                    </p>
                                </div>
                            </div>
                            <div class="mt-4 text-center">
                                <div class="mt-2 w-full">
                                    <p class="text-lg text-center text-black">{!! session('message') !!}</p>
                                </div>
                            </div>
                            <div class="mt-4 text-center">
                                <div class="mt-2 w-full">
                                    <p class="text-lg text-center text-black">{{ env('APP_NAME') }} team</p>
                                </div>
                            </div>
                        </div>
                        <div class="px-4 pb-6 pt-4 flex items-center space-x-2 sm:space-x-4 sm:px-6 justify-center">
                            <a href=""
                                class="inline-flex w-full justify-center rounded bg-greenXS px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-greenXS sm:ml-3 sm:w-24">{{ $siteText['close_btn_text'] }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('script')
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        function closeModal() {
            const modal = document.querySelector('[aria-modal="true"]');
            if (modal) {
                modal.remove();
            }
        }

        function toggleFAQ(section) {
            const faq = document.getElementById(`${section}-faq`);
            const icon = document.getElementById(`${section}-icon`);

            if (!faq || !icon) {
                return;
            }

            faq.classList.toggle('hidden');
            icon.style.transform = faq.classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(180deg)';
        }

        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('payment-form');
            const customAmountField = document.getElementById('custom_amount_input');
            const packageRadios = document.querySelectorAll('input[name="package"]');
            const frequencyRadios = document.querySelectorAll('input[name="frequency"]');
            const designationAll = document.getElementById('designation-1');
            const designationIndividuals = document.querySelectorAll('.designation-individual');
            const donationAckCheckbox = document.getElementById('donation_acknowledgment');
            const termsPrivacyCheckbox = document.getElementById('terms_privacy');
            const donationAckDiv = document.getElementById('donation-acknowledgment-div');
            const termsPrivacyDiv = document.getElementById('terms-privacy-div');
            const creditCardRadio = document.getElementById('credit-card');
            const paypalRadio = document.getElementById('paypal');
            const creditCardDiv = document.getElementById('credit-card-div');
            const anonymousCheckbox = document.getElementById('anonymous');
            const nameField = document.getElementById('name_field');
            const emailField = document.getElementById('email_field');
            const phoneField = document.getElementById('phone_field');
            const notifyField = document.getElementById('notify_field');
            let stripe = null;
            let cardElement = null;

            function toggleClasses(element, active, activeClasses, inactiveClasses) {
                if (!element) {
                    return;
                }

                activeClasses.forEach((className) => element.classList.toggle(className, active));
                inactiveClasses.forEach((className) => element.classList.toggle(className, !active));
            }

            function updateFrequencyStyles() {
                const selectedValue = document.querySelector('input[name="frequency"]:checked')?.value ?? '';
                const monthlyLabel = document.getElementById('monthly_label');
                const quarterlyLabel = document.getElementById('quarterly_label');
                const oneTimeLabel = document.getElementById('one_time_label');
                const activeClasses = ['bg-blue-600', 'border-blue-600', 'text-white'];
                const inactiveClasses = ['bg-white', 'border-gray-100', 'text-blue-600'];

                toggleClasses(monthlyLabel, selectedValue === 'monthly', activeClasses, inactiveClasses);
                toggleClasses(quarterlyLabel, selectedValue === 'weekly', activeClasses, inactiveClasses);
                toggleClasses(oneTimeLabel, selectedValue === '', activeClasses, inactiveClasses);
            }

            function updateDesignationStyles() {
                const allLabel = document.getElementById('designation-label-1');
                if (allLabel) {
                    allLabel.classList.toggle('border-green-500', !!designationAll?.checked);
                    allLabel.classList.toggle('border-2', !!designationAll?.checked);
                    allLabel.classList.toggle('text-green-500', !!designationAll?.checked);
                    allLabel.classList.toggle('border-gray-100', !designationAll?.checked);
                }

                designationIndividuals.forEach((checkbox, index) => {
                    const label = document.getElementById(`designation-label-${index + 2}`);
                    if (!label) {
                        return;
                    }

                    label.classList.toggle('border-green-500', checkbox.checked);
                    label.classList.toggle('border-2', checkbox.checked);
                    label.classList.toggle('text-green-500', checkbox.checked);
                    label.classList.toggle('border-gray-100', !checkbox.checked);
                });
            }

            function updateCustomAmountStyle() {
                if (!customAmountField) {
                    return;
                }

                customAmountField.classList.toggle('border-green-500', customAmountField.value.trim() !== '');
                customAmountField.classList.toggle('border-blue-500', customAmountField.value.trim() === '');
            }

            function syncAnonymousFields() {
                const hidden = !!anonymousCheckbox?.checked;
                [nameField, emailField, phoneField, notifyField].forEach((field) => {
                    if (field) {
                        field.classList.toggle('hidden', hidden);
                    }
                });
            }

            function ensureStripe() {
                if (cardElement || !creditCardDiv) {
                    return;
                }

                stripe = Stripe('{{ $stripeKey }}');
                const elements = stripe.elements();
                cardElement = elements.create('card', {
                    style: {
                        base: {
                            fontStyle: 'italic'
                        }
                    }
                });
                cardElement.mount('#card-element');
            }

            function togglePaymentSection() {
                if (!creditCardDiv) {
                    return;
                }

                const useStripe = !!creditCardRadio?.checked;
                creditCardDiv.classList.toggle('hidden', !useStripe);

                if (useStripe) {
                    ensureStripe();
                    return;
                }

                const cardErrors = document.getElementById('card-errors');
                if (cardErrors) {
                    cardErrors.classList.add('hidden');
                }
            }

            function hideTooltipOnCheck(checkbox, container) {
                checkbox?.addEventListener('change', function() {
                    if (this.checked && container) {
                        container.classList.add('hidden');
                    }
                });
            }

            frequencyRadios.forEach((radio) => {
                radio.addEventListener('change', updateFrequencyStyles);
            });

            designationAll?.addEventListener('change', function() {
                if (this.checked) {
                    designationIndividuals.forEach((checkbox) => {
                        checkbox.checked = false;
                    });
                }
                updateDesignationStyles();
            });

            designationIndividuals.forEach((checkbox) => {
                checkbox.addEventListener('change', function() {
                    if (this.checked && designationAll) {
                        designationAll.checked = false;
                    }

                    const anyChecked = Array.from(designationIndividuals).some((item) => item
                        .checked);
                    if (!anyChecked && designationAll) {
                        designationAll.checked = true;
                    }

                    updateDesignationStyles();
                });
            });

            if (customAmountField) {
                customAmountField.addEventListener('keydown', function(event) {
                    if (['e', 'E', '+', '-'].includes(event.key)) {
                        event.preventDefault();
                    }
                });

                customAmountField.addEventListener('input', function() {
                    this.value = this.value.replace(/[eE]/g, '');
                    packageRadios.forEach((radio) => {
                        radio.checked = false;
                    });
                    updateCustomAmountStyle();
                });

                customAmountField.addEventListener('focus', function() {
                    packageRadios.forEach((radio) => {
                        radio.checked = false;
                    });
                });
            }

            packageRadios.forEach((radio) => {
                radio.addEventListener('change', function() {
                    if (customAmountField) {
                        customAmountField.value = '';
                    }
                    updateCustomAmountStyle();
                });
            });

            [creditCardRadio, paypalRadio].forEach((radio) => {
                radio?.addEventListener('change', togglePaymentSection);
            });

            anonymousCheckbox?.addEventListener('change', syncAnonymousFields);
            hideTooltipOnCheck(donationAckCheckbox, donationAckDiv);
            hideTooltipOnCheck(termsPrivacyCheckbox, termsPrivacyDiv);

            // Hide field tooltip error when user clicks/focuses inside its parent container.
            function hideTooltipInParent(eventTarget) {
                if (!(eventTarget instanceof HTMLElement) || !form) return;
                let node = eventTarget.closest('div, section, label');

                // Walk up until form root and remove tooltips that belong to the current field
                while (node && node !== form) {
                    // Check for tooltip as a direct child
                    const tooltipInChildren = Array.from(node.children).find((child) =>
                        child instanceof HTMLElement && child.classList.contains('tooltip-error')
                    );
                    if (tooltipInChildren) {
                        tooltipInChildren.remove();
                        return;
                    }

                    // Check for tooltip as a sibling (for cases like terms checkbox where error is sibling of label)
                    if (node.parentElement) {
                        const tooltipSibling = Array.from(node.parentElement.children).find((sibling) =>
                            sibling instanceof HTMLElement &&
                            sibling.classList.contains('tooltip-error') &&
                            sibling !== node
                        );
                        if (tooltipSibling) {
                            tooltipSibling.remove();
                            return;
                        }
                    }
                    node = node.parentElement?.closest('div, section') || null;
                }
            }

            if (form) {
                form.addEventListener('click', function(event) {
                    hideTooltipInParent(event.target);
                });
                form.addEventListener('focusin', function(event) {
                    hideTooltipInParent(event.target);
                });
            }

            // On submit, create Stripe token when paying by card
            if (form) {
                form.addEventListener('submit', function(event) {
                    const selectedPaymentMethod = document.querySelector(
                        'input[name="payment_method"]:checked')?.value;

                    // For PayPal (or no method), let the form submit normally
                    if (selectedPaymentMethod !== 'stripe') {
                        return;
                    }

                    event.preventDefault();
                    ensureStripe();

                    const nameOnCardInput = document.getElementById('name_on_card');
                    const nameError = document.getElementById('card-name-error');
                    const nameErrorText = nameError?.querySelector('.tooltip-error');
                    const cardErrors = document.getElementById('card-errors');
                    const cardErrorsText = cardErrors?.querySelector('.tooltip-error');

                    // Clear previous errors
                    if (nameError) {
                        nameError.classList.add('hidden');
                    }
                    if (cardErrors) {
                        cardErrors.classList.add('hidden');
                    }

                    // Validate cardholder name before calling Stripe
                    if (!nameOnCardInput || nameOnCardInput.value.trim() === '') {
                        if (nameError && nameErrorText) {
                            nameError.classList.remove('hidden');
                            nameErrorText.textContent = @json(__('validation.custom.name_on_card.required_if'));
                            nameError.scrollIntoView({
                                behavior: 'smooth',
                                block: 'center'
                            });
                        }
                        return;
                    }

                    stripe.createToken(cardElement, {
                        name: nameOnCardInput.value
                    }).then(function(result) {
                        if (result.error) {
                            if (cardErrors && cardErrorsText) {
                                cardErrors.classList.remove('hidden');
                                cardErrorsText.textContent = result.error.message;
                                cardErrors.scrollIntoView({
                                    behavior: 'smooth',
                                    block: 'center'
                                });
                            }
                            return;
                        }

                        // Inject stripeToken and a dummy card_element value for backend validation
                        const tokenInput = document.createElement('input');
                        tokenInput.type = 'hidden';
                        tokenInput.name = 'stripeToken';
                        tokenInput.value = result.token.id;
                        form.appendChild(tokenInput);

                        const cardElementInput = document.createElement('input');
                        cardElementInput.type = 'hidden';
                        cardElementInput.name = 'card_element';
                        cardElementInput.value = 'card_provided';
                        form.appendChild(cardElementInput);

                        form.submit();
                    });
                });
            }

            // On page load, scroll to the first *visible* tooltip with an error (server-side validation)
            const allTooltips = document.querySelectorAll('.tooltip-error');
            for (const tooltip of allTooltips) {
                let node = tooltip;
                let hiddenByAncestor = false;

                // Skip tooltips inside any container that has the 'hidden' class
                while (node && node !== document.body) {
                    if (node.classList && node.classList.contains('hidden')) {
                        hiddenByAncestor = true;
                        break;
                    }
                    node = node.parentElement;
                }

                if (!hiddenByAncestor) {
                    const wrapper = tooltip.parentElement || tooltip;
                    wrapper.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                    break;
                }
            }

            updateFrequencyStyles();
            updateDesignationStyles();
            updateCustomAmountStyle();
            syncAnonymousFields();
            togglePaymentSection();
        });
    </script>
@endsection
