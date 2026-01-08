@extends('layouts.template')

@section('content')
    <div class="container mx-auto my-10 md:my-14 px-4">
        <div class="pb-2">
            <h1 class="mb-0">
                @isset($coffeeWallPage->main_heading)
                    {{ $coffeeWallPage->main_heading }}
                @endisset
            </h1>
        </div>

        <div class="pb-2">
            @isset($coffeeWallPage->main_text)
                {!! $coffeeWallPage->main_text !!}
            @endisset
        </div>
        <div class="text-right md:mr-20 text-red-500 text-lg">
            <span class="text-red-500">*</span> {{ $coffeeWallPage->required_field_label ?? 'Indicate required field' }}
        </div>
        <div class="relative flex flex-col min-w-0 break-words bg-white w-full mb-6 shadow-lg rounded">
            <div class="px-4 pb-5 flex-auto">
                <div class="tab-content tab-space">
                    <div class="block" id="tab-profile">
                        <form id="payment-form" method="POST"
                            action="{{ url((isset($selectedLanguage) ? $selectedLanguage->abbreviation : 'en') . '/coffee-on-the-wall') }}"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="space-y-4">
                                <input type="hidden" name="gPayApplePayId" value="">

                                <div class="mt-6">
                                    <div class="mt-6">
                                        <div class="bg-white rounded-lg overflow-hidden shadow-3xl">
                                            <div class="text-2xl bg-primary text-white py-2 px-4">
                                                <h3 class="text-2xl">
                                                    @isset($coffeeWallPage->frequency_label)
                                                        {!! $coffeeWallPage->frequency_label !!}
                                                    @endisset
                                                    <span class="text-red-500">*</span>
                                                </h3>
                                            </div>
                                            <div class="p-4">
                                                <div class="">
                                                    <ul
                                                        class="text-sm font-medium text-center text-gray-500 rounded-md md:rounded-lg shadow-sm grid grid-cols-3 dark:divide-gray-700 dark:text-gray-400 border">
                                                        <li class="focus-within:z-10">
                                                            <input type="radio" id="one_time" name="frequency"
                                                                value=""
                                                                {{ old('frequency', 'monthly') == '' ? 'checked' : '' }}
                                                                class="hidden peer">
                                                            <label for="one_time" id="one_time_label"
                                                                class="text-lg md:text-2xl font-FuturaMdCnBT font-medium py-4 shadow-lg rounded-l-md flex items-center justify-center h-16 border-gray-100 border leading-normal text-blue-600 bg-white cursor-pointer">
                                                                {{ $coffeeWallPage->quarterly_label ?? 'One time' }}
                                                            </label>
                                                        </li>
                                                        <li class="focus-within:z-10">
                                                            <input type="radio" id="quarterly" name="frequency"
                                                                value="weekly"
                                                                {{ old('frequency', 'monthly') == 'weekly' ? 'checked' : '' }}
                                                                class="hidden peer">
                                                            <label for="quarterly" id="quarterly_label"
                                                                class="text-lg md:text-2xl font-FuturaMdCnBT font-medium py-4 shadow-lg flex items-center justify-center h-16 border-gray-100 border leading-normal text-blue-600 bg-white cursor-pointer">
                                                                {{ $coffeeWallPage->semi_annually_label ?? 'Weekly' }}
                                                            </label>
                                                        </li>
                                                        <li class="focus-within:z-10">
                                                            <input type="radio" id="monthly" name="frequency"
                                                                value="monthly"
                                                                {{ old('frequency', 'monthly') == 'monthly' ? 'checked' : '' }}
                                                                class="hidden peer">
                                                            <label for="monthly" id="monthly_label"
                                                                class="text-lg md:text-2xl font-FuturaMdCnBT font-medium py-4 shadow-lg rounded-r-md flex items-center justify-center h-16 border-gray-100 border leading-normal text-white bg-blue-600 cursor-pointer">
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
                                                            @error('package')
                                                                <div class="relative tooltip -bottom-4 group-hover:flex">
                                                                    <div role="tooltip"
                                                                        class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                                                                        <p class="text-white leading-none text-sm lg:text-base">
                                                                            {{ $message }}</p>
                                                                    </div>
                                                                </div>
                                                            @enderror
                                                            <div id="package-errors-div"
                                                                class="hidden relative tooltip -bottom-4 group-hover:flex">
                                                                <div role="tooltip"
                                                                    class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                                                                    <p id="package-errors"
                                                                        class="text-white leading-none text-sm lg:text-base">
                                                                    </p>
                                                                </div>
                                                            </div>
                                                            <div class="w-full">
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
                                                                            id="custom_amount_input"
                                                                            min="1"
                                                                            value="{{ old('custom_amount') }}"
                                                                            class="block mt-1 border-2 p-2.5 w-full rounded border-blue-500 focus:ring-1 focus:outline-none focus:border-green-500 pl-10 h-20 text-lg"
                                                                            placeholder="{{ $coffeeWallPage->custom_amount_label }}">
                                                                    </div>
                                                                    @error('custom_amount')
                                                                        <div
                                                                            class="relative tooltip -bottom-4 group-hover:flex">
                                                                            <div role="tooltip"
                                                                                class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                                                                                <p
                                                                                    class="text-white leading-none text-sm lg:text-base">
                                                                                    {{ $message }}</p>
                                                                            </div>
                                                                        </div>
                                                                    @enderror
                                                                    <div id="amount-errors-div"
                                                                        class="hidden relative tooltip -bottom-4 group-hover:flex">
                                                                        <div role="tooltip"
                                                                            class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                                                                            <p id="amount-errors"
                                                                                class="text-white leading-none text-sm lg:text-base">
                                                                            </p>
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
                                </div>

                                <div class="space-y-2">
                                    <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2">
                                        <div class="mb-1 mt-9 bg-primary text-white py-2 px-4 rounded col-span-2">
                                            <h3 class=" text-2xl">
                                                {{ $coffeeWallPage->designation_label ?? 'Designation' }}</h3>
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
                                            <h3 class=" text-2xl">Your Contact Information</h3>
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
                                                <div class="relative tooltip -bottom-4 group-hover:flex">
                                                    <div role="tooltip"
                                                        class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                                                        <p class="text-white leading-none text-sm lg:text-base">
                                                            {{ $message }}</p>
                                                    </div>
                                                </div>
                                            @enderror
                                            <div id="name-errors-div"
                                                class="hidden relative tooltip -bottom-4 group-hover:flex">
                                                <div role="tooltip"
                                                    class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                                                    <p id="name-errors"
                                                        class="text-white leading-none text-sm lg:text-base"></p>
                                                </div>
                                            </div>
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
                                                <div class="relative tooltip -bottom-4 group-hover:flex">
                                                    <div role="tooltip"
                                                        class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                                                        <p class="text-white leading-none text-sm lg:text-base">
                                                            {{ $message }}</p>
                                                    </div>
                                                </div>
                                            @enderror
                                            <div id="email-errors-div"
                                                class="hidden relative tooltip -bottom-4 group-hover:flex">
                                                <div role="tooltip"
                                                    class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                                                    <p id="email-errors"
                                                        class="text-white leading-none text-sm lg:text-base"></p>
                                                </div>
                                            </div>
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
                                                <div class="relative tooltip -bottom-4 group-hover:flex">
                                                    <div role="tooltip"
                                                        class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                                                        <p class="text-white leading-none text-sm lg:text-base">
                                                            {{ $message }}</p>
                                                    </div>
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div id="notify_field" class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2">
                                        <div class="mt-4">
                                            <label for="notify_coffee_used" class="flex items-center gap-2">
                                                <input type="checkbox" name="notify_coffee_used" value="1"
                                                    id="notify_coffee_used"
                                                    {{ old('notify_coffee_used') == '1' ? 'checked' : '' }}
                                                    class="h-5 w-5">
                                                <span class="text-base md:text-lg">Notify me when my Coffee is used</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2">
                                        <div class="mb-1 mt-9 bg-primary text-white py-2 px-4 rounded col-span-2">
                                            <h3 class=" text-2xl">Select Your Payment Method</h3>
                                        </div>
                                        <div>
                                            <div
                                                class="flex flex-col md:flex-row gap-4 md:justify-normal justify-between md:gap-x-8 items-start md:items-center mt-2 p-1.5">
                                                <div class="flex items-center gap-2">
                                                    <input id="credit-card" type="radio" value="stripe"
                                                        name="payment_method"
                                                        class="w-5 h-5 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-2 accent-blue-600"
                                                        checked>
                                                    <label for="credit-card" class="text-base md:text-lg cursor-pointer">Debit or Credit Card</label>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <input id="paypal" type="radio" value="paypal"
                                                        name="payment_method"
                                                        class="w-5 h-5 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-2 accent-blue-600"
                                                        {{ old('payment_method') === 'paypal' ? 'checked' : '' }}>
                                                    <label for="paypal" class="text-base md:text-lg cursor-pointer">PayPal</label>
                                                </div>
                                                <div class="flex items-center gap-2" id="gpay-container">
                                                    <input id="gpay" type="radio" value="gpay"
                                                        name="payment_method"
                                                        class="w-5 h-5 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-2 accent-blue-600"
                                                        {{ old('payment_method') === 'gpay' ? 'checked' : '' }}>
                                                    <label for="gpay" class="text-base md:text-lg cursor-pointer" id="gpay-label">GPay / Apple Pay</label>
                                                </div>
                                            </div>
                                            @error('payment_method')
                                                <div class="relative tooltip -bottom-4 group-hover:flex">
                                                    <div role="tooltip"
                                                        class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                                                        <p class="text-white leading-none text-sm lg:text-base">
                                                            {{ $message }}</p>
                                                    </div>
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div id="paymentSectionGPay" class="hidden">
                                    <div id="payment-request-button"></div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4 mt-4">
                                    <div id="credit-card-div"
                                        class="hidden mt-4 p-4 bg-white border border-gray-400 rounded">
                                        <div>
                                            <label
                                                for="name_on_card">{{ $paymentSettingDetail->name_on_card_label ?? 'Cardholder’s name' }}</label>
                                            <input type="text" id="name_on_card" name="name_on_card"
                                                value="{{ old('name_on_card') }}"
                                                class="block mt-1 border p-1.5 w-full rounded text-base md:text-lg border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600">
                                            @error('name_on_card')
                                                <div class="relative tooltip -bottom-4 group-hover:flex">
                                                    <div role="tooltip"
                                                        class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                                                        <p class="text-white leading-none text-sm lg:text-base">
                                                            {{ $message }}</p>
                                                    </div>
                                                </div>
                                            @enderror
                                        </div>
                                        <!-- Single row for Card Number, Expiry Date, and CVV -->
                                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                                            <!-- Card Number - takes more space -->
                                            <div class="md:col-span-6">
                                                <label
                                                    for="card_number">{{ $paymentSettingDetail->card_number_label ?? 'Card number' }}</label>
                                                <div id="card-number-element"
                                                    class="block mt-1 border p-1.5 py-[11px] w-full rounded text-base md:text-lg border-gray-300">
                                                </div>
                                                @error('card_number')
                                                    <div class="relative tooltip -bottom-4 group-hover:flex">
                                                        <div role="tooltip"
                                                            class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                                                            <p class="text-white leading-none text-sm lg:text-base">
                                                                {{ $message }}</p>
                                                        </div>
                                                    </div>
                                                @enderror
                                            </div>

                                            <!-- Expiry Date -->
                                            <div class="md:col-span-3">
                                                <label
                                                    for="exp_month">{{ $paymentSettingDetail->mobile_expiry_date_label ?? 'Expiry date' }}
                                                    (MM / YY)</label>
                                                <div id="card-expiry-element"
                                                    class="block mt-1 border p-1.5 py-[11px] w-full rounded text-base md:text-lg border-gray-300">
                                                </div>
                                                @error('expiry_date')
                                                    <div class="relative tooltip -bottom-4 group-hover:flex">
                                                        <div role="tooltip"
                                                            class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                                                            <p class="text-white leading-none text-sm lg:text-base">
                                                                {{ $message }}</p>
                                                        </div>
                                                    </div>
                                                @enderror
                                                <div id="expiry-error-div"
                                                    class="hidden relative tooltip -bottom-4 group-hover:flex">
                                                    <div role="tooltip"
                                                        class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                                                        <p id="expiry-error"
                                                            class="text-white leading-none text-sm lg:text-base"></p>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- CVV -->
                                            <div class="md:col-span-3">
                                                <label
                                                    for="cvv_code">{{ $paymentSettingDetail->security_code_label ?? 'Security code (CVV / CVC)' }}</label>
                                                <div id="card-cvc-element"
                                                    class="block mt-1 border p-1.5 py-[11px] w-full rounded text-base md:text-lg border-gray-300">
                                                </div>
                                                @error('cvv_code')
                                                    <div class="relative tooltip -bottom-4 group-hover:flex">
                                                        <div role="tooltip"
                                                            class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                                                            <p class="text-white leading-none text-sm lg:text-base">
                                                                {{ $message }}</p>
                                                        </div>
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4 mt-4">
                                    <div id="card-errors-div" class="hidden relative tooltip -bottom-4 group-hover:flex">
                                        <div role="tooltip"
                                            class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                                            <p id="card-errors" class="text-white leading-none text-sm lg:text-base"></p>
                                        </div>
                                    </div>
                                </div>
                                <!-- Disclaimer Checkboxes -->
                                <div class="grid grid-cols-1 gap-4 mt-6">
                                    <!-- First Disclaimer -->
                                    <div class="disclaimer-container">
                                        <div class="flex items-start gap-2">
                                            <input id="donation_acknowledgment" type="checkbox"
                                                name="donation_acknowledgment" value="1"
                                                {{ old('donation_acknowledgment') == '1' ? 'checked' : '' }}
                                                class="h-5 w-5 mt-1 flex-shrink-0 accent-blue-600" required>
                                            <span class="text-base md:text-lg">I understand that my contribution is a voluntary
                                                act of kindness. As such, it is considered a donation and is
                                                non-refundable.</span>
                                        </div>
                                        @error('donation_acknowledgment')
                                            <div class="relative tooltip mt-1 ml-7">
                                                <div role="tooltip"
                                                    class="relative tooltiptext z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 rounded inline-block">
                                                    <p class="text-white leading-none text-sm lg:text-base">This field is required</p>
                                                </div>
                                            </div>
                                        @enderror
                                        <!-- Error div for JavaScript validation - positioned next to checkbox -->
                                        <div id="donation-acknowledgment-div" class="hidden relative tooltip mt-1 ml-7">
                                            <div role="tooltip"
                                                class="relative tooltiptext z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 rounded inline-block">
                                                <p id="donation-acknowledgment-error" class="text-white leading-none text-sm lg:text-base">This field is required</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Second Disclaimer -->
                                    <div class="disclaimer-container">
                                        <div class="flex items-start gap-2">
                                            <input id="terms_privacy" type="checkbox" name="terms_privacy" value="1"
                                                {{ old('terms_privacy') == '1' ? 'checked' : '' }}
                                                class="h-5 w-5 mt-1 flex-shrink-0 accent-blue-600" required>
                                            <span class="text-base md:text-lg">By clicking "Make Someone's Day", you agree to
                                                our <a href="#" class="text-blue-600 hover:underline">Terms and
                                                    Conditions</a> and <a href="#"
                                                    class="text-blue-600 hover:underline">Privacy Policy</a></span>
                                        </div>
                                        @error('terms_privacy')
                                            <div class="relative tooltip mt-1 ml-7">
                                                <div role="tooltip"
                                                    class="relative tooltiptext z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 rounded inline-block">
                                                    <p class="text-white leading-none text-sm lg:text-base">This field is required</p>
                                                </div>
                                            </div>
                                        @enderror
                                        <!-- Error div for JavaScript validation - positioned next to checkbox -->
                                        <div id="terms-privacy-div" class="hidden relative tooltip mt-1 ml-7">
                                            <div role="tooltip"
                                                class="relative tooltiptext z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 rounded inline-block">
                                                <p id="terms-privacy-error" class="text-white leading-none text-sm lg:text-base">This field is required</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4 mt-4">
                                    <div class="md:col-span-2 flex justify-center mt-4">
                                        <button type="submit" class="button-exp-fill px-6 py-3">
                                            @isset($coffeeWallPage->pay_button_label)
                                                {!! $coffeeWallPage->pay_button_label !!}
                                            @else
                                                Make Someone's Day
                                            @endisset
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
                    <span>FAQ for the Donors</span>
                    <svg id="donors-icon" class="w-6 h-6 transform transition-transform duration-200" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div id="donors-faq" class="hidden p-6 bg-gray-50 border-t">
                    <div class="space-y-4 text-gray-700">
                        <div>
                            <h4 class="font-semibold mb-2">What is "Coffee on the Wall"?</h4>
                            <p>Coffee on the Wall is a tradition that started in cafes where customers could buy an extra
                                coffee for someone in need. We've digitized this concept to help provide rides for those who
                                cannot afford them.</p>
                        </div>
                        <div>
                            <h4 class="font-semibold mb-2">How does my donation work?</h4>
                            <p>When you make a donation, your contribution goes directly toward providing free or discounted
                                rides for people in need in your community.</p>
                        </div>
                        <div>
                            <h4 class="font-semibold mb-2">Will I get updates on how my donation is used?</h4>
                            <p>If you check "Notify me when my Coffee is used", you'll receive an email when your donation
                                helps someone get a ride.</p>
                        </div>
                        <div>
                            <h4 class="font-semibold mb-2">Is my donation tax-deductible?</h4>
                            <p>Please consult with your tax advisor regarding the deductibility of your donation in your
                                jurisdiction.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FAQ for the Beneficiary -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <button
                    class="w-full bg-blue-600 text-white px-6 py-4 text-left text-xl font-FuturaMdCnBT flex items-center justify-between hover:bg-blue-700 focus:outline-none"
                    onclick="toggleFAQ('beneficiary')">
                    <span>FAQ for the Beneficiary</span>
                    <svg id="beneficiary-icon" class="w-6 h-6 transform transition-transform duration-200" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div id="beneficiary-faq" class="hidden p-6 bg-gray-50 border-t">
                    <div class="space-y-4 text-gray-700">
                        <div>
                            <h4 class="font-semibold mb-2">How do I qualify for a free ride?</h4>
                            <p>Free rides are available for students, female passengers, visible minorities, and others in
                                need. Contact us to learn about qualification requirements.</p>
                        </div>
                        <div>
                            <h4 class="font-semibold mb-2">How do I request a sponsored ride?</h4>
                            <p>You can request a sponsored ride through our app when booking. Look for the "Coffee on the
                                Wall" option during the booking process.</p>
                        </div>
                        <div>
                            <h4 class="font-semibold mb-2">Are there limits on how many free rides I can get?</h4>
                            <p>Limits may apply based on donation availability and community guidelines. We aim to help as
                                many people as possible while being fair to all users.</p>
                        </div>
                        <div>
                            <h4 class="font-semibold mb-2">Is my information kept private?</h4>
                            <p>Yes, your information is kept confidential. Donors will not see your personal details, and we
                                respect your privacy throughout the process.</p>
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
                        class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
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
                                class="inline-flex w-full justify-center rounded bg-greenXS px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-greenXS sm:ml-3 sm:w-24">Close</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('script')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        // Expiry date validation and formatting functions
        function validateExpiryDate(expiryDate) {
            // Remove all whitespace
            var cleanDate = expiryDate.replace(/\s/g, '');

            // Check if the format matches MM/YYYY or M/YYYY or MM/YY or M/YY
            var regex = /^(\d{1,2})\/(\d{2,4})$/;
            var match = cleanDate.match(regex);

            if (!match) {
                showExpiryError(
                    'Please enter the expiry date in this exact format: MM/YYYY. Use two digits for the month and four digits for the year, even if the month is a single digit. For example, enter April as "04", not "4".'
                    );
                return false;
            }

            var month = parseInt(match[1], 10);
            var year = parseInt(match[2], 10);

            // Validate month (1-12)
            if (month < 1 || month > 12) {
                showExpiryError('Please enter a valid month (01-12).');
                return false;
            }

            // Handle 2-digit years by converting to 4-digit
            if (year < 100) {
                // Assume years 00-30 are 20xx, years 31-99 are 19xx
                if (year <= 30) {
                    year += 2000;
                } else {
                    year += 1900;
                }
            }

            // Check if year is valid (current year or future, but not too far in future)
            var currentDate = new Date();
            var currentYear = currentDate.getFullYear();
            var currentMonth = currentDate.getMonth() + 1; // getMonth() returns 0-11

            if (year < currentYear || (year === currentYear && month < currentMonth)) {
                showExpiryError('Please enter a valid expiry date. The card appears to be expired.');
                return false;
            }

            if (year > currentYear + 20) {
                showExpiryError('Please enter a valid expiry date. The year seems too far in the future.');
                return false;
            }

            // Hide error if validation passes
            hideExpiryError();
            return true;
        }

        function parseExpiryDate(expiryDate) {
            var cleanDate = expiryDate.replace(/\s/g, '');
            var match = cleanDate.match(/^(\d{1,2})\/(\d{2,4})$/);

            if (!match) return null;

            var month = parseInt(match[1], 10);
            var year = parseInt(match[2], 10);

            // Handle 2-digit years
            if (year < 100) {
                if (year <= 30) {
                    year += 2000;
                } else {
                    year += 1900;
                }
            }

            // Ensure month is two digits
            var monthStr = month.toString().padStart(2, '0');

            return {
                month: monthStr,
                year: year.toString()
            };
        }

        function showExpiryError(message) {
            var errorDiv = document.getElementById('expiry-error-div');
            var errorElement = document.getElementById('expiry-error');

            if (errorDiv && errorElement) {
                errorElement.textContent = message;
                errorDiv.classList.remove('hidden');
            }

            // Add red border to input
            var expiryInput = document.getElementById('card-expiry-element');
            if (expiryInput) {
                expiryInput.classList.add('border-red-500');
                expiryInput.classList.remove('border-gray-300');
            }
        }

        function hideExpiryError() {
            var errorDiv = document.getElementById('expiry-error-div');
            if (errorDiv) {
                errorDiv.classList.add('hidden');
            }

            // Remove red border from input
            var expiryInput = document.getElementById('card-expiry-element');
            if (expiryInput) {
                expiryInput.classList.remove('border-red-500');
                expiryInput.classList.add('border-gray-300');
            }
        }

        // Email validation helper function
        function isValidEmail(email) {
            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }

        // Format expiry date as user types
        function formatExpiryDate(input) {
            var value = input.value.replace(/\D/g, ''); // Remove non-digits

            if (value.length >= 2) {
                value = value.substring(0, 2) + '/' + value.substring(2, 6);
            }

            input.value = value;
        }
    </script>
    <script>
        function hideTooltip(parms) {
            if ($(this).parent().find('.tooltip').length > 0 && parms != 'label') {
                $(this).parent().find('.tooltip').addClass('hidden');
            } else if ($(this).parent().parent().find('.tooltip').length > 0 && parms != 'label') {
                $(this).parent().parent().find('.tooltip').addClass('hidden');
            } else if ($(this).parent().parent().parent().find('.tooltip').length > 0) {
                $(this).parent().parent().parent().find('.tooltip').addClass('hidden');
            }
        }

        const inputs = document.querySelectorAll('input, textarea');
        inputs.forEach(input => {
            input.addEventListener('input', hideTooltip); // no parameter on input typing
        });

        const labels = document.querySelectorAll('label');
        labels.forEach(input => {
            input.addEventListener('click', function(e) {
                hideTooltip.call(this, 'label'); // pass 'testing' on label click
            });
        });


        document.addEventListener('DOMContentLoaded', function() {
            // Get all input fields that might show validation errors
            const inputs = [{
                    element: document.querySelector('input[name="name"]'),
                    errorDiv: document.getElementById('name-errors-div')
                },
                {
                    element: document.querySelector('input[name="email"]'),
                    errorDiv: document.getElementById('email-errors-div')
                },
                {
                    element: document.querySelector('input[name="custom_amount"]'),
                    errorDiv: document.getElementById('amount-errors-div')
                },
                {
                    element: document.querySelector('input[name="package"]:checked'),
                    errorDiv: document.getElementById('package-errors-div')
                },
                {
                    element: document.querySelector('input[name="name_on_card"]'),
                    errorDiv: document.querySelector('[for="name_on_card"] + .tooltip')
                },
                {
                    element: document.getElementById('card-number-element'),
                    errorDiv: document.querySelector('[for="card_number"] + .tooltip')
                },
                {
                    element: document.getElementById('card-expiry-element'),
                    errorDiv: document.querySelector('[for="exp_month"] + .tooltip')
                },
                {
                    element: document.getElementById('card-cvc-element'),
                    errorDiv: document.querySelector('[for="cvv_code"] + .tooltip')
                },
                {
                    element: document.getElementById('donation_acknowledgment'),
                    errorDiv: document.getElementById('donation-acknowledgment-div')
                },
                {
                    element: document.getElementById('terms_privacy'),
                    errorDiv: document.getElementById('terms-privacy-div')
                }
            ];

            // Add input event listeners to each field
            inputs.forEach(input => {
                if (input.element && input.errorDiv) {
                    input.element.addEventListener('input', function() {
                        // Hide the error message
                        input.errorDiv.classList.add('hidden');

                        // Remove error styling from input
                        if (input.element.classList.contains('border-red-500')) {
                            input.element.classList.remove('border-red-500');
                            input.element.classList.add('border-gray-300');
                        }
                    });
                }
            });

            // For radio buttons (package selection)
            const packageRadios = document.querySelectorAll('input[name="package"]');
            packageRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    document.getElementById('package-errors-div').classList.add('hidden');
                });
            });

            // For custom amount field specifically
            const customAmountField = document.querySelector('input[name="custom_amount"]');
            if (customAmountField) {
                customAmountField.addEventListener('input', function() {
                    document.getElementById('amount-errors-div').classList.add('hidden');
                    // Also uncheck any selected package radio
                    document.querySelectorAll('input[name="package"]').forEach(radio => {
                        radio.checked = false;
                    });
                });
            }

            // For frequency selection
            const frequencyRadios = document.querySelectorAll('input[name="frequency"]');
            frequencyRadios.forEach(radio => {
                radio.addEventListener('change', function() {

                });
            });

            // For designation selection
            const designationRadios = document.querySelectorAll('input[name="designation"]');
            designationRadios.forEach(radio => {
                radio.addEventListener('change', function() {

                });
            });
        });
    </script>
    <script>
        // Event listener for dropdown change
        const radioButtons = document.querySelectorAll('input[name="frequency"]');

        // Add change event listener to all radio buttons
        radioButtons.forEach(radio => {
            radio.addEventListener('change', updatePayoutFields);
        });

        // Initialize fields visibility on page load
        document.addEventListener('DOMContentLoaded', updatePayoutFields);

        document.addEventListener("DOMContentLoaded", function() {
            const customAmountInput = document.querySelector('input[name="custom_amount"]');

            customAmountInput.addEventListener("keydown", function(event) {
                if (event.key === "e" || event.key === "E" || event.key === "+" || event.key === "-") {
                    event.preventDefault();
                }
            });

            customAmountInput.addEventListener("input", function(event) {
                this.value = this.value.replace(/[eE]/g, ''); // Remove "e" if pasted
            });

            // ============================================
            // DESIGNATION MULTI-SELECT WITH "ALL" TOGGLE
            // ============================================
            const designationAll = document.getElementById('designation-1');
            const designationIndividuals = document.querySelectorAll('.designation-individual');

            function updateDesignationStyles() {
                // Update "All" checkbox label
                const allLabel = document.getElementById('designation-label-1');
                if (designationAll.checked) {
                    allLabel.classList.add('border-green-500', 'border-2', 'text-green-500');
                    allLabel.classList.remove('border-gray-100');
                } else {
                    allLabel.classList.remove('border-green-500', 'border-2', 'text-green-500');
                    allLabel.classList.add('border-gray-100');
                }

                // Update individual checkbox labels
                designationIndividuals.forEach((checkbox, index) => {
                    const label = document.getElementById(`designation-label-${index + 2}`);
                    if (checkbox.checked) {
                        label.classList.add('border-green-500', 'border-2', 'text-green-500');
                        label.classList.remove('border-gray-100');
                    } else {
                        label.classList.remove('border-green-500', 'border-2', 'text-green-500');
                        label.classList.add('border-gray-100');
                    }
                });
            }

            // When "All" is selected, deselect individual options
            designationAll.addEventListener('change', function() {
                if (this.checked) {
                    designationIndividuals.forEach(checkbox => {
                        checkbox.checked = false;
                    });
                }
                updateDesignationStyles();
            });

            // When any individual option is selected, deselect "All"
            designationIndividuals.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    if (this.checked) {
                        designationAll.checked = false;
                    }
                    // If no individual options are checked, check "All"
                    const anyIndividualChecked = Array.from(designationIndividuals).some(cb => cb.checked);
                    if (!anyIndividualChecked) {
                        designationAll.checked = true;
                    }
                    updateDesignationStyles();
                });
            });

            // Initialize styles on page load
            updateDesignationStyles();

            // ============================================
            // CUSTOM AMOUNT FIELD STYLING
            // ============================================
            const customAmountField = document.getElementById('custom_amount_input');
            const packageRadios = document.querySelectorAll('input[name="package"]');

            function updateCustomAmountStyle() {
                if (customAmountField.value.trim() !== '') {
                    customAmountField.classList.remove('border-blue-500');
                    customAmountField.classList.add('border-green-500');
                } else {
                    customAmountField.classList.remove('border-green-500');
                    customAmountField.classList.add('border-blue-500');
                }
            }

            customAmountField.addEventListener('input', updateCustomAmountStyle);
            customAmountField.addEventListener('focus', function() {
                // Deselect all packages when typing custom amount
                packageRadios.forEach(radio => radio.checked = false);
            });

            // Reset custom amount border when package is selected
            packageRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    customAmountField.value = '';
                    customAmountField.classList.remove('border-green-500');
                    customAmountField.classList.add('border-blue-500');
                });
            });

            // Initialize on page load
            updateCustomAmountStyle();

            // ============================================
            // CHECKBOX TOOLTIP FIX - INDEPENDENT DISMISSAL
            // ============================================
            const donationAckCheckbox = document.getElementById('donation_acknowledgment');
            const termsPrivacyCheckbox = document.getElementById('terms_privacy');
            const donationAckDiv = document.getElementById('donation-acknowledgment-div');
            const termsPrivacyDiv = document.getElementById('terms-privacy-div');

            // Hide donation acknowledgment tooltip only when its checkbox is checked
            donationAckCheckbox.addEventListener('change', function() {
                if (this.checked && donationAckDiv) {
                    donationAckDiv.classList.add('hidden');
                }
            });

            // Hide terms/privacy tooltip only when its checkbox is checked
            termsPrivacyCheckbox.addEventListener('change', function() {
                if (this.checked && termsPrivacyDiv) {
                    termsPrivacyDiv.classList.add('hidden');
                }
            });

            // ============================================
            // PLATFORM-SPECIFIC PAYMENT OPTION
            // ============================================
            const gpayContainer = document.getElementById('gpay-container');
            const gpayLabel = document.getElementById('gpay-label');

            // Detect if user is on iOS/macOS (Apple devices)
            const isAppleDevice = /iPad|iPhone|iPod|Macintosh/.test(navigator.userAgent) &&
                                  !window.MSStream;

            // Detect if user is on Android
            const isAndroid = /Android/.test(navigator.userAgent);

            if (gpayLabel) {
                if (isAppleDevice) {
                    gpayLabel.textContent = 'Apple Pay';
                } else if (isAndroid) {
                    gpayLabel.textContent = 'Google Pay';
                } else {
                    gpayLabel.textContent = 'GPay / Apple Pay';
                }
            }
        });

        function updatePayoutFields() {
            const selectedRadio = document.querySelector('input[name="frequency"]:checked');
            const selectedValue = selectedRadio ? selectedRadio.value : null;
            const monthlyLabel = document.getElementById('monthly_label');
            const quaterlyLabel = document.getElementById('quarterly_label');
            const oneTimeLabel = document.getElementById('one_time_label');

            if (selectedValue === 'monthly') {
                quaterlyLabel.classList.add('bg-white', 'border-gray-100', 'text-blue-600');
                quaterlyLabel.classList.remove('bg-blue-600', 'border-blue-600', 'text-white');
                one_time_label.classList.add('bg-white', 'border-gray-100', 'text-blue-600');
                one_time_label.classList.remove('bg-blue-600', 'border-blue-600', 'text-white');
                monthlyLabel.classList.add('bg-blue-600', 'border-blue-600', 'text-white');
                monthlyLabel.classList.remove('bg-white', 'border-gray-100', 'text-blue-600');
            } else if (selectedValue === 'weekly') {
                monthlyLabel.classList.add('bg-white', 'border-gray-100', 'text-blue-600');
                monthlyLabel.classList.remove('bg-blue-600', 'border-blue-600', 'text-white');
                one_time_label.classList.add('bg-white', 'border-gray-100', 'text-blue-600');
                one_time_label.classList.remove('bg-blue-600', 'border-blue-600', 'text-white');
                quaterlyLabel.classList.add('bg-blue-600', 'border-blue-600', 'text-white');
                quaterlyLabel.classList.remove('bg-white', 'border-gray-100', 'text-blue-600');
            } else if (selectedValue === '') {
                quaterlyLabel.classList.add('bg-white', 'border-gray-100', 'text-blue-600');
                quaterlyLabel.classList.remove('bg-blue-600', 'border-blue-600', 'text-white');
                monthlyLabel.classList.add('bg-white', 'border-gray-100', 'text-blue-600');
                monthlyLabel.classList.remove('bg-blue-600', 'border-blue-600', 'text-white');
                one_time_label.classList.add('bg-blue-600', 'border-blue-600', 'text-white');
                one_time_label.classList.remove('bg-white', 'border-gray-100', 'text-blue-600');
            }
        }

        $(document).ready(function() {
            const creditCardCheckbox = document.getElementById('credit-card');
            const paypalCheckbox = document.getElementById('paypal');
            const gPayCheckbox = document.getElementById('gpay');
            const CreditCardDiv = document.getElementById('credit-card-div');

            // Stripe variables to prevent re-initialization
            let stripe = null;
            let elements = null;
            let cardNumberElement = null;
            let cardExpiryElement = null;
            let cardCvcElement = null;
            let stripeInitialized = false;

            function checkCheckboxes() {
                $("#paymentSectionGPay").addClass('hidden');
                if (creditCardCheckbox.checked) {
                    CreditCardDiv.classList.remove('hidden');
                    
                    // Initialize Stripe only once
                    if (!stripeInitialized) {
                        stripe = Stripe('{{ $stripeKey }}');
                        elements = stripe.elements();

                        cardNumberElement = elements.create('cardNumber', {
                            placeholder: "{{ $paymentSettingDetail->card_number_placeholder ?? '' }}"
                        });
                        cardNumberElement.mount('#card-number-element');

                        cardExpiryElement = elements.create('cardExpiry', {
                            placeholder: "MM / YY"
                        });
                        cardExpiryElement.mount('#card-expiry-element');
                        
                        cardCvcElement = elements.create('cardCvc', {
                            placeholder: "{{ $paymentSettingDetail->cvc_placeholder ?? '' }}"
                        });
                        cardCvcElement.mount('#card-cvc-element');
                        
                        stripeInitialized = true;
                    }



                    // Add form submit listener only once
                    if (!window.formSubmitListenerAdded) {
                        var form = document.getElementById('payment-form');
                        form.addEventListener('submit', function(event) {
                        event.preventDefault();

                        var hasValidationErrors = false;

                        // Check if the `package` field is filled
                        var packageDropdown = $("input[name='package']:checked").val();
                        var amountValue = $("input[name='custom_amount']").val();
                        if (!packageDropdown && !amountValue) {
                            hasValidationErrors = true;
                            var errorElementDiv = document.getElementById('package-errors-div');
                            errorElementDiv.classList.remove('hidden');

                            var errorElement = document.getElementById('package-errors');
                            errorElement.textContent = 'Please select at least one package';
                        }

                        // Check if anonymous donation
                        var isAnonymous = $('#anonymous').is(':checked');

                        if (!isAnonymous) {
                            var nameValue = $("input[name='name']").val();
                            if (!nameValue) {
                                hasValidationErrors = true;
                                var errorElementDiv = document.getElementById('name-errors-div');
                                errorElementDiv.classList.remove('hidden');

                                var errorElement = document.getElementById('name-errors');
                                errorElement.textContent = 'Please enter your name';
                            } else {
                                var errorElementDiv = document.getElementById('name-errors-div');
                                if (!errorElementDiv.classList.contains('hidden')) {
                                    errorElementDiv.classList.add('hidden');
                                }
                            }

                            var emailValue = $("input[name='email']").val();
                            if (!emailValue) {
                                hasValidationErrors = true;
                                var errorElementDiv = document.getElementById('email-errors-div');
                                errorElementDiv.classList.remove('hidden');

                                var errorElement = document.getElementById('email-errors');
                                errorElement.textContent = 'Please enter your email';
                            } else if (!isValidEmail(emailValue)) {
                                hasValidationErrors = true;
                                var errorElementDiv = document.getElementById('email-errors-div');
                                errorElementDiv.classList.remove('hidden');

                                var errorElement = document.getElementById('email-errors');
                                errorElement.textContent = 'Please use a valid email';
                            } else {
                                var errorElementDiv = document.getElementById('email-errors-div');
                                if (!errorElementDiv.classList.contains('hidden')) {
                                    errorElementDiv.classList.add('hidden');
                                }
                            }
                        }

                        // Check donation acknowledgment checkbox
                        var donationAckCheckbox = document.getElementById('donation_acknowledgment');
                        if (!donationAckCheckbox.checked) {
                            hasValidationErrors = true;
                            var donationAckDiv = document.getElementById('donation-acknowledgment-div');
                            donationAckDiv.classList.remove('hidden');

                            var donationAckError = document.getElementById('donation-acknowledgment-error');
                            donationAckError.textContent = 'This field is required';
                        }
                        // Note: Tooltip is now only hidden when its specific checkbox is checked (handled in separate event listener)

                        // Check terms and privacy checkbox
                        var termsPrivacyCheckbox = document.getElementById('terms_privacy');
                        if (!termsPrivacyCheckbox.checked) {
                            hasValidationErrors = true;
                            var termsPrivacyDiv = document.getElementById('terms-privacy-div');
                            termsPrivacyDiv.classList.remove('hidden');

                            var termsPrivacyError = document.getElementById('terms-privacy-error');
                            termsPrivacyError.textContent = 'This field is required';
                        }
                        // Note: Tooltip is now only hidden when its specific checkbox is checked (handled in separate event listener)

                        // Stop form submission if there are validation errors
                        if (hasValidationErrors) {
                            return;
                        }

                        stripe.createToken(cardNumberElement, {
                            name: document.getElementById('name_on_card').value
                        }).then(function(result) {
                            if (result.error) {
                                if (creditCardCheckbox.checked) {
                                    var nameOnCardValue = $("input[name='name_on_card']").val();
                                    if (!nameOnCardValue) {
                                        var errorElementDiv = document.getElementById(
                                            'card-errors-div');
                                        errorElementDiv.classList.remove('hidden');

                                        var errorElement = document.getElementById('card-errors');
                                        errorElement.textContent = "Please enter cardholder's name";
                                    } else {
                                        var errorElementDiv = document.getElementById(
                                            'card-errors-div');
                                        errorElementDiv.classList.remove('hidden');

                                        var errorElement = document.getElementById('card-errors');
                                        errorElement.textContent = result.error.message;
                                    }
                                }
                            } else {
                                stripeTokenHandler(result.token);
                            }
                        });
                        });
                        
                        window.formSubmitListenerAdded = true;
                    }

                    function stripeTokenHandler(token) {
                        var form = document.getElementById('payment-form');
                        var hiddenInput = document.createElement('input');
                        hiddenInput.setAttribute('type', 'hidden');
                        hiddenInput.setAttribute('name', 'stripeToken');
                        hiddenInput.setAttribute('value', token.id);
                        form.appendChild(hiddenInput);

                        form.submit();
                    }
                } else if (gPayCheckbox.checked) {

                    CreditCardDiv.classList.add('hidden');
                    $("#paymentSectionGPay").removeClass('hidden');

                    let isValid = true;

                    var packageDropdown = $("input[name='package']:checked").val();
                    var amountValue = $("input[name='custom_amount']").val();
                    if (!packageDropdown && !amountValue) {
                        isValid = false;

                        var errorElementDiv = document.getElementById('package-errors-div');
                        errorElementDiv.classList.remove('hidden');

                        var errorElement = document.getElementById('package-errors');
                        errorElement.textContent = 'Please select at least one package';
                    }

                    var isAnonymous = $('#anonymous').is(':checked');
                    var nameValue = $("input[name='name']").val();
                    var emailValue = $("input[name='email']").val();

                    if (!isAnonymous) {
                        if (!nameValue) {
                            isValid = false;

                            var errorElementDiv = document.getElementById('name-errors-div');
                            errorElementDiv.classList.remove('hidden');

                            var errorElement = document.getElementById('name-errors');
                            errorElement.textContent = 'Please enter your name';
                        } else {
                            var errorElementDiv = document.getElementById('name-errors-div');
                            if (!errorElementDiv.classList.contains('hidden')) {
                                errorElementDiv.classList.add('hidden');
                            }
                        }

                        if (!emailValue) {
                            isValid = false;

                            var errorElementDiv = document.getElementById('email-errors-div');
                            errorElementDiv.classList.remove('hidden');

                            var errorElement = document.getElementById('email-errors');
                            errorElement.textContent = 'Please enter your email';
                        } else if (!isValidEmail(emailValue)) {
                            isValid = false;

                            var errorElementDiv = document.getElementById('email-errors-div');
                            errorElementDiv.classList.remove('hidden');

                            var errorElement = document.getElementById('email-errors');
                            errorElement.textContent = 'Please use a valid email';
                        } else {
                            var errorElementDiv = document.getElementById('email-errors-div');
                            if (!errorElementDiv.classList.contains('hidden')) {
                                errorElementDiv.classList.add('hidden');
                            }
                        }
                    }

                    // Check donation acknowledgment checkbox
                    var donationAckCheckbox = document.getElementById('donation_acknowledgment');
                    if (!donationAckCheckbox.checked) {
                        isValid = false;

                        var donationAckDiv = document.getElementById('donation-acknowledgment-div');
                        donationAckDiv.classList.remove('hidden');

                        var donationAckError = document.getElementById('donation-acknowledgment-error');
                        donationAckError.textContent = 'This field is required';
                    }
                    // Note: Tooltip is only hidden when its specific checkbox is checked (handled in separate event listener)

                    // Check terms and privacy checkbox
                    var termsPrivacyCheckbox = document.getElementById('terms_privacy');
                    if (!termsPrivacyCheckbox.checked) {
                        isValid = false;

                        var termsPrivacyDiv = document.getElementById('terms-privacy-div');
                        termsPrivacyDiv.classList.remove('hidden');

                        var termsPrivacyError = document.getElementById('terms-privacy-error');
                        termsPrivacyError.textContent = 'This field is required';
                    }
                    // Note: Tooltip is only hidden when its specific checkbox is checked (handled in separate event listener)

                    if (!isValid) {
                        // Uncheck the Google Pay checkbox
                        gPayCheckbox.checked = false;

                        $("#paymentSectionGPay").addClass('hidden');

                        return; // Stop further execution (don't show GPay button)
                    }

                    const packages = @json($packages);
                    let selectedPackagePrice = 0;
                    if (packageDropdown) {
                        const selectedPackage = packages.find(pkg => pkg.id == packageDropdown);
                        if (selectedPackage) {
                            selectedPackagePrice = parseFloat(selectedPackage.price);
                        }
                    } else {
                        selectedPackagePrice = parseFloat(amountValue);
                    }

                    if (!stripe) {
                        stripe = Stripe('{{ $stripeKey }}');
                    }
                    const paymentRequest = stripe.paymentRequest({
                        country: 'US',
                        currency: 'usd',
                        total: {
                            label: 'Total',
                            amount: Math.round(selectedPackagePrice * 100),
                        },
                        requestPayerName: true,
                        requestPayerEmail: true,
                        paymentMethodTypes: ['card'],
                    });

                    // Check if the device/browser supports Apple Pay or Google Pay
                    paymentRequest.canMakePayment().then(function(result) {
                        console.log(result); // Log the result to understand what's being returned

                        if (result && result.googlePay) {
                            // Google Pay is available, enable the button
                            const elements = stripe.elements();
                            const prButton = elements.create('paymentRequestButton', {
                                paymentRequest: paymentRequest,
                            });


                            prButton.mount('#payment-request-button');

                            //validateBookingAndShowGPay();

                        } else if (result && result.applePay) {
                            // Apple Pay is available (on Safari for Apple devices), enable the button
                            const elements = stripe.elements();
                            const prButton = elements.create('paymentRequestButton', {
                                paymentRequest: paymentRequest,
                            });

                            prButton.mount('#payment-request-button');
                        } else {
                            // If neither is available, log a message
                            console.log("Neither Apple Pay nor Google Pay is available on this device.");
                        }
                    }).catch(function(error) {
                        // Handle errors
                        console.error('Error checking payment method availability:', error);
                    });


                    paymentRequest.on('paymentmethod', async (ev) => {
                        const response = await fetch('/create-subscription', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]')
                                    .value
                            },
                            body: JSON.stringify({
                                payment_method: ev.paymentMethod.id,
                                email: emailValue ?? null,
                                package_id: packageDropdown ? packageDropdown : null,
                                custom_amount: amountValue ? amountValue : null
                            }),
                        });

                        const {
                            clientSecret,
                            subscriptionId
                        } = await response.json();

                        if (subscriptionId) {
                            ev.complete('success');


                            document.querySelector('[name="gPayApplePayId"]').value = subscriptionId;
                            document.querySelector('[name="payment_method"][value="stripe"]').checked =
                                true;

                            document.getElementById('payment-form').submit();
                            // Handle post-payment success (e.g., show a confirmation page)
                            console.log('Payment Successful!');
                        }
                    });
                } else {
                    CreditCardDiv.classList.add('hidden');
                    var errorElementDiv = document.getElementById('card-errors-div');
                    errorElementDiv.classList.add('hidden');
                }
            }

            const customAmountField = document.querySelector('input[name="custom_amount"]');
            const packageRadios = document.querySelectorAll('input[name="package"]');

            customAmountField.addEventListener("input", function() {
                checkCheckboxes();
                packageRadios.forEach(radio => {
                    radio.checked = false;
                });
            });

            packageRadios.forEach(radio => {
                radio.addEventListener("change", function() {
                    customAmountField.value = "";
                    checkCheckboxes();
                });
            });

            creditCardCheckbox.addEventListener('change', checkCheckboxes);
            paypalCheckbox.addEventListener('change', checkCheckboxes);
            gPayCheckbox.addEventListener('change', checkCheckboxes);

            // Initial check
            checkCheckboxes();

            var anonymous = document.getElementById('anonymous');
            anonymous.addEventListener('change', function() {
                checkFields(this);
            });

            const nameField = document.getElementById('name_field');
            const emailField = document.getElementById('email_field');
            const phoneField = document.getElementById('phone_field');
            const notifyField = document.getElementById('notify_field');

            function checkFields(checkbox) {
                if (checkbox.checked) {
                    nameField.classList.add('hidden');
                    emailField.classList.add('hidden');
                    phoneField.classList.add('hidden');
                    notifyField.classList.add('hidden');
                } else {
                    nameField.classList.remove('hidden');
                    emailField.classList.remove('hidden');
                    phoneField.classList.remove('hidden');
                    notifyField.classList.remove('hidden');
                }
            }

            // Check on page load
            if (anonymous.checked) {
                nameField.classList.add('hidden');
                emailField.classList.add('hidden');
                phoneField.classList.add('hidden');
                notifyField.classList.add('hidden');
            }
        });

        function closeModal() {
            const modal = document.querySelector('[aria-modal="true"]');
            if (modal) {
                modal.remove();
            }
        }

        function toggleFAQ(section) {
            const faq = document.getElementById(section + '-faq');
            const icon = document.getElementById(section + '-icon');

            if (faq.classList.contains('hidden')) {
                faq.classList.remove('hidden');
                icon.style.transform = 'rotate(180deg)';
            } else {
                faq.classList.add('hidden');
                icon.style.transform = 'rotate(0deg)';
            }
        }
    </script>
@endsection
