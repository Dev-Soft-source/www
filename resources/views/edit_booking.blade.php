@extends('layouts.template')

@section('style')
<style>
    /* Wider tooltip on mobile for coffee wall tooltip */
    @media (max-width: 639px) {
        .tooltip_width {
            width: min(90vw, 22rem) !important;
            min-width: 18rem;
        }
    }

    /* Student verification tooltips - expanded width */
    .student-verification-tooltip {
        position: relative;
        background-color: #c75b5b;
        border-radius: 0.5rem;
        padding: 0.75rem;
        width: 24rem;
        max-width: 70vw;
    }

    @media (max-width: 768px) {
        .student-verification-tooltip::after {
            left: 30px;
        }
    }

    .tooltip.shift-left {
        margin-left: -150px;
    }

    /* Payment method tooltip: above exclamation icon with arrow pointing down to icon */
    .payment-method-tooltip {
        position: relative;
        background-color: #c75b5b;
        border-radius: 0.5rem;
        padding: 0.75rem;
        min-width: 300px;
        max-width: 380px;
    }
    .payment-method-tooltip::after {
        content: '';
        position: absolute;
        top: 100%;
        left: 50%;
        transform: translateX(-50%);
        border: 8px solid transparent;
        border-top-color: #c75b5b;
    }
</style>
@endsection

@section('content')

@php
    $settingTaxPercentage = 0;
@endphp


@php
        $hidePaymentSection = false;
        $firm = '';
    @endphp
    @if ($ride->payment_method->features_setting_id === $postRidePage->payment_methods_option1->features_setting_id)
        @php
            $hidePaymentSection = true;
        @endphp
    @endif
    @isset($postRidePage->cancellation_policy_label2->features_setting_id)
        @php
            $firm = $postRidePage->cancellation_policy_label2->features_setting_id;
        @endphp
    @endisset
    @if ($setting)
        @php
            $settingBookingPrice = $setting->booking_price;
            $settingFirmDiscount = $setting->frim_discount;
        @endphp
    @else
        @php
            $settingBookingPrice = '';
            $settingFirmDiscount = '';
        @endphp
    @endif

<div class="container mx-auto my-10 xl:my-14 px-4 xl:px-0">
    <form method="POST"
        @isset($ride->booking_method->features_setting_id)
            @if ($ride->booking_method->features_setting_id == ($postRidePage->booking_option1->features_setting_id ?? null))
                action="{{ route('update_instant_booking', $booking->id) }}"
            @elseif ($ride->booking_method->features_setting_id == ($postRidePage->booking_option2->features_setting_id ?? null))
                action="{{ route('update_booking_request', $booking->id) }}"
            @endif
        @endisset
        enctype="multipart/form-data" id="submitForm">
        @csrf
        @method('PUT')
        <input type="hidden" name="ride_detail_id" value="{{ $booking->ride_detail_id }}">

        
        <input type="hidden" name="id" value="{{ $ride->id}}">
        <input type="hidden" name="gPayApplePayId" value="">
        
        <input type="hidden" value="{{ $ride->payment_method->features_setting_id === $postRidePage->payment_methods_option1->features_setting_id ? "cash" : "online" }}" id="check_payment_method">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-y-4 md:gap-4 items-baseline">
            <div class="col-span-2 flex flex-wrap items-center justify-between gap-3">
                <h1 class="-mb-2">Edit Booking</h1>
                <div class="text-red-500 text-lg mt-4 pr-4">
                    <span class="text-red-500">*</span> {{ $bookingPage->required_fields ?? ""}}
                </div>
            </div>
            <div class="col-span-2">
                <div class="bg-white rounded-lg shadow-3xl">
                    <div class="flex flex-col md:flex-row justify-between px-4 pb-4 md:pb-0">
                        <div class="w-full md:w-2/3 order-2 md:order-1">
                            <div class="relative mt-5 text-left">
                                <div class="flex items-center relative">
                                    <div
                                        class="border-r-2 border-black border-solid absolute h-full left-3 md:left-6 top-2 z-10">
                                        <span
                                            class="bg-primary rounded-full w-7 h-7 -top-[2px] -ml-[13px] absolute flex justify-center items-center">
                                            <img class="w-5 h-5 object-contain"
                                                src="{{ asset('./images/new-21-search-bar-from.png') }}" alt="">
                                        </span>
                                    </div>
                                    <div class="ml-12 md:ml-20">
                                        <div class="font-bold text-xl text-black">
                                            @isset($rideDetailPage->from_label)
                                                {{ $rideDetailPage->from_label }}
                                            @endisset
                                        </div>
                                        <div class="flex gap-2">
                                            <p class="text-primary text-xl md:mb-4">
                                                {{ $booking->departure }}.
                                            </p>
                                            <p class="text-sm mt-2">
                                                Pick-up at: {{ $ride->pickup }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center relative">
                                    <div
                                        class="border-r-2 border-black border-solid absolute h-0 left-3 md:left-5 top-2 z-10">
                                        <span
                                            class="bg-gray-200 rounded-full w-7 h-7 -top-[6px] -ml-[12px] md:-ml-[9px] absolute flex justify-center items-center">
                                            <img class="w-5 h-5 object-contain"
                                                src="{{ asset('./images/new-21-search-bar-to.png') }}" alt="">
                                        </span>
                                    </div>
                                    <div class="ml-12 md:ml-20">
                                        <div class="font-bold text-xl text-black">
                                            @isset($rideDetailPage->to_label)
                                                {{ $rideDetailPage->to_label }}
                                            @endisset
                                        </div>
                                        <div class="flex gap-2">
                                            <p class="text-primary text-xl md:mb-4">
                                                {{ $booking->destination }},
                                            </p>
                                            <p class="text-sm mt-2">
                                                Drop-off at: {{ $ride->dropoff }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 order-1 md:order-2">
                            @php
                                $departureDateTime = formatDepartureDateTime($ride->date, $selectedLanguage ?? null, $rideDetailPage ?? null);
                                $departureDateLabel = $departureDateTime['dateLabel'];
                                $departureTimeLabel = $departureDateTime['timeLabel'];
                            @endphp
                            <p class="whitespace-nowrap font-semibold">
                                {{ $departureDateLabel }}
                                {{ $rideDetailPage->at_label }}
                                {{ $departureTimeLabel ?? 'N/A' }}
                            </p>
                            
                        </div>
                    </div>
                    <div class="border-t border-gray-300 grid grid-cols-2 divide-x divide-gray-300">
                        <div class="p-4">
                            <p class="text-left font-semibold">
                                @if (auth()->user() &&
                                        $ride->bookings &&
                                        $ride->bookings->where('status', '<>', 3)->where('status', '<>', 4)->where('user_id', auth()->user()->id)->isNotEmpty())
                                    @if ($ride->bookings->where('status', '<>', 3)->where('status', '<>', 4)->where('user_id', auth()->user()->id)->first()->status !== '3')
                                        @if (strtotime($ride->date) > strtotime('today') ||
                                                (strtotime($ride->date) == strtotime('today') && strtotime($ride->time) > strtotime('now')))
                                            <div class="flex items-baseline">
                                                <a href="{{ route('booking', ['lang' => $selectedLanguage->abbreviation, 'id' => $ride->id, 'rideDetailId' => $ride->rideDetail[0]->id]) }}"
                                                    class="font-FuturaMdCnBT text-xl xl:text-2xl text-black">
                                                    @isset($bookingPage->seats_left_label)
                                                        {{ $bookingPage->seats_left_label }}
                                                    @endisset
                                                </a>
                                                <p class="text-xl text-primary font-normal ml-2" style="font-family: 'Nunito', sans-serif;">{{ intval($ride->seats) -intval($ride->bookings()->where('status', '<>', 3)->where('status', '<>', 4)->whereHas('passenger', function ($query) {$query->whereNull('deleted_at');})->sum('seats')) }}</p>
                                            </div>                                            
                                        @endif
                                    @endif
                                @elseif (
                                    $ride->seats -
                                        $ride->bookings()->where('status', '<>', 3)->where('status', '<>', 4)->whereHas('passenger', function ($query) {
                                                $query->whereNull('deleted_at');
                                            })->sum('seats') !=
                                        0)
                                    @if ($ride->status !== '2')
                                        <div class="flex ">
                                            <a class="font-FuturaMdCnBT text-xl xl:text-2xl text-black"    class="text-xl xl:text-2xl" href="{{ route('booking', ['lang' => $selectedLanguage->abbreviation, 'id' => $ride->id, 'rideDetailId' => $ride->rideDetail[0]->id]) }}"
                                                @isset($bookingPage->seats_left_label)
                                                    {{ $bookingPage->seats_left_label }}
                                                @endisset
                                            </a>
                                            <p class="text-xl text-primary font-normal ml-2" style="font-family: 'Nunito', sans-serif;">{{ intval($ride->seats) -intval($ride->bookings()->where('status', '<>', 3)->where('status', '<>', 4)->whereHas('passenger', function ($query) {$query->whereNull('deleted_at');})->sum('seats')) }}</p>                                            
                                        </div>
                                    @endif
                                @endif


                            </p>
                        </div>
                        <div class="flex flex-wrap items-center gap-3 p-4 items-baseline">
                            <h4 class="text-black text-xl xl:text-2xl">
                                Booking Price:
                            </h4>
                            <p class="text-lg font-normal text-left text-primary" style="font-family: 'Nunito', sans-serif;">${{ $booking->price }}
                                @isset($rideDetailPage->per_seat_label)
                                    {{ $rideDetailPage->per_seat_label }}
                                @endisset
                            </p>
                        </div>
                    </div>
                    <div
                        class="border-t border-gray-300 grid sm:grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-gray-300">
                        <div class="p-4 items-baseline">
                            <h4 class="font-medium text-xl xl:text-2xl text-left text-black font-FuturaMdCnBT">
                                @isset($rideDetailPage->payment_method_label)
                                    {{ $rideDetailPage->payment_method_label }}
                                @endisset
                                <span class="text-lg text-primary font-normal inline-block cursor-help" style="font-family: 'Nunito', sans-serif;"
                                    @if (optional($ride->payment_method)->features_setting_id === (optional($postRidePage->payment_methods_option1)->features_setting_id ?? null)) data-tippy-content="{{ optional($postRidePage)->payment_methods_option1_tooltip ?? '' }}"
                                    @elseif (optional($ride->payment_method)->features_setting_id === (optional($postRidePage->payment_methods_option2)->features_setting_id ?? null)) data-tippy-content="{{ optional($postRidePage)->payment_methods_option2_tooltip ?? '' }}"
                                    @elseif (optional($ride->payment_method)->features_setting_id === (optional($postRidePage->payment_methods_option3)->features_setting_id ?? null)) data-tippy-content="{{ optional($postRidePage)->payment_methods_option3_tooltip ?? '' }}"
                                    @elseif (optional($ride->payment_method)->features_setting_id === (optional($postRidePage->payment_methods_option4)->features_setting_id ?? null)) data-tippy-content="{{ optional($postRidePage)->payment_methods_option4_tooltip ?? '' }}"
                                    @endif>{{ $ride->payment_method->name ?? '' }}</span>
                            </h4>
                        </div>
                        <div class="p-4">
                            <div class="flex flex-wrap items-center gap-3 items-baseline">
                                <h4 class="text-black text-xl xl:text-2xl font-FuturaMdCnBT">
                                    Booking Method:
                                </h4>
                                @isset($ride->booking_method->features_setting_id)
                                    <div class="text-lg text-primary font-normal inline-block cursor-pointer" style="font-family: 'Nunito', sans-serif;"
                                        @if ($ride->booking_method->features_setting_id == (optional($postRidePage->booking_option1)->features_setting_id ?? null))
                                            data-tippy-content="{{ optional($postRidePage)->booking_option1_tooltip ?? '' }}"
                                        @elseif ($ride->booking_method->features_setting_id == (optional($postRidePage->booking_option2)->features_setting_id ?? null))
                                            data-tippy-content="{{ optional($postRidePage)->booking_option2_tooltip ?? '' }}"
                                        @endif>
                                        {{ $ride->booking_method->name }}
                                    </div>
                                @endisset
                            </div>
                        </div>
                    </div>
                    <a
                        @if (auth()->user() &&
                                $ride->bookings &&
                                $ride->bookings->where('status', '<>', 3)->where('status', '<>', 4)->where('user_id', auth()->user()->id)->isNotEmpty()) href="{{ route('my_co_passengers', ['lang' => $selectedLanguage->abbreviation, 'departure' => $booking->departure, 'destination' => $booking->destination, 'id' => $ride->id]) }}"
                    @else
                        href="javascript:void(0);" @endif>
                        <div
                            class="border-t border-gray-300 flex flex-col md:flex-row md:items-center justify-start md:space-x-2 p-4">
                            <div>
                                <h4 class="font-medium text-xl xl:text-2xl md:text-center text-black mr-4 font-FuturaMdCnBT">
                                    @isset($rideDetailPage->co_passenger_label)
                                        {{ $rideDetailPage->co_passenger_label }}
                                    @endisset :
                                </h4>
                            </div>
                            <div class="flex items-center space-x-2 no-scrollbar overflow-x-auto mt-2 md:mt-0">
                                @foreach ($ride->bookings->where('status', '<>', 3)->where('status', '<>', 4) as $bookingItem)
                                    @for ($i = 0; $i < $bookingItem->seats; $i++)
                                        @if ($bookingItem->passenger)
                                            @if ($bookingItem->passenger->profile_image)
                                                <img class="w-10 h-10 rounded-full"
                                                    src="{{ $bookingItem->passenger->profile_image }}" alt="">
                                            @else
                                                <img class="w-10 h-10 rounded-full"
                                                    src="{{ asset('images/59-booked-seat.png') }}" alt="">
                                            @endif
                                        @endif
                                    @endfor
                                @endforeach
                            </div>
                        </div>
                    </a>
                </div>
                <div class="bg-white rounded-lg overflow-hidden shadow-3xl mt-4">
                    <div class="bg-primary text-white px-4 py-2">
                        <h3 class="text-2xl xl:text-3xl">Ride Preferences</h3>
                    </div>
                    <div class="bg-white p-4 space-y-4">
                        <div class="flex items-center space-x-2">
                            @if ($ride->smoke == (optional($postRidePage->smoking_option1)->features_setting_id ?? null))
                                @isset(optional($postRidePage->smoking_option1)->icon)
                                    <img class="w-7 h-7" src="{{ asset('home_page_icons/' . optional($postRidePage->smoking_option1)->icon) }}" alt="">
                                @endisset
                                <div class="flex items-center gap-1">
                                    <p class="font-semibold">{{ $rideDetailPage->smoking_label ?? '' }} {{ optional($postRidePage->smoking_option1)->name }}</p>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-circle-fill text-black cursor-help inline-block" data-tippy-content="{{ optional($postRidePage)->smoking_option1_tooltip ?? '' }}" viewBox="0 0 16 16">
                                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 4zm.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2z"/>
                                    </svg>
                                </div>
                            @elseif ($ride->smoke == (optional($postRidePage->smoking_option2)->features_setting_id ?? null))
                                @isset(optional($postRidePage->smoking_option2)->icon)
                                    <img class="w-7 h-7" src="{{ asset('home_page_icons/' . optional($postRidePage->smoking_option2)->icon) }}" alt="">
                                @endisset
                                <div class="flex items-center gap-1">
                                    <p class="font-semibold">{{ $rideDetailPage->smoking_label ?? '' }} {{ optional($postRidePage->smoking_option2)->name }}</p>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-circle-fill text-black cursor-help inline-block" data-tippy-content="{{ optional($postRidePage)->smoking_option2_tooltip ?? '' }}" viewBox="0 0 16 16">
                                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 4zm.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2z"/>
                                    </svg>
                                </div>
                            @endif
                        </div>
                        @isset($ride->animal_friendly->features_setting_id)
                            <div class="flex items-center gap-2">
                                <img class="w-7 h-7"
                                    @if ($ride->animal_friendly->features_setting_id === (optional($postRidePage->animals_option1)->features_setting_id ?? null)) src="{{ asset('home_page_icons/' . optional($postRidePage->animals_option1)->icon) }}"
                                    @elseif ($ride->animal_friendly->features_setting_id === (optional($postRidePage->animals_option2)->features_setting_id ?? null)) src="{{ asset('home_page_icons/' . optional($postRidePage->animals_option2)->icon) }}"
                                    @elseif ($ride->animal_friendly->features_setting_id === (optional($postRidePage->animals_option3)->features_setting_id ?? null)) src="{{ asset('home_page_icons/' . optional($postRidePage->animals_option3)->icon) }}"
                                    @endif
                                    alt="">
                                <div class="flex items-center gap-1">
                                    <p class="font-semibold">{{ $rideDetailPage->pets_label ?? '' }} {{ $ride->animal_friendly->name }}</p>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-circle-fill text-black cursor-help inline-block"
                                        @if ($ride->animal_friendly->features_setting_id === (optional($postRidePage->animals_option1)->features_setting_id ?? null)) data-tippy-content="{{ optional($postRidePage)->animals_option1_tooltip ?? '' }}"
                                        @elseif ($ride->animal_friendly->features_setting_id === (optional($postRidePage->animals_option2)->features_setting_id ?? null)) data-tippy-content="{{ optional($postRidePage)->animals_option2_tooltip ?? '' }}"
                                        @elseif ($ride->animal_friendly->features_setting_id === (optional($postRidePage->animals_option3)->features_setting_id ?? null)) data-tippy-content="{{ optional($postRidePage)->animals_option3_tooltip ?? '' }}"
                                        @endif viewBox="0 0 16 16">
                                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 4zm.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2z"/>
                                    </svg>
                                </div>
                            </div>
                        @endisset
                        @isset($ride->luggage->features_setting_id)
                            <div class="flex items-center space-x-2">
                                <img class="w-7 h-7" src="{{ asset('home_page_icons/' . $ride->luggage->icon) }}" alt="">
                                <div class="flex items-center gap-1">
                                    <p class="font-semibold">{{ $rideDetailPage->luggage_label ?? '' }} {{ $ride->luggage->name }}</p>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-circle-fill text-black cursor-help inline-block"
                                        @if ($ride->luggage->features_setting_id === (optional($postRidePage->luggage_option1)->features_setting_id ?? null)) data-tippy-content="{{ optional($postRidePage)->luggage_option1_tooltip ?? '' }}"
                                        @elseif ($ride->luggage->features_setting_id === (optional($postRidePage->luggage_option2)->features_setting_id ?? null)) data-tippy-content="{{ optional($postRidePage)->luggage_option2_tooltip ?? '' }}"
                                        @elseif ($ride->luggage->features_setting_id === (optional($postRidePage->luggage_option3)->features_setting_id ?? null)) data-tippy-content="{{ optional($postRidePage)->luggage_option3_tooltip ?? '' }}"
                                        @elseif ($ride->luggage->features_setting_id === (optional($postRidePage->luggage_option4)->features_setting_id ?? null)) data-tippy-content="{{ optional($postRidePage)->luggage_option4_tooltip ?? '' }}"
                                        @elseif ($ride->luggage->features_setting_id === (optional($postRidePage->luggage_option5)->features_setting_id ?? null)) data-tippy-content="{{ optional($postRidePage)->luggage_option5_tooltip ?? '' }}"
                                        @endif viewBox="0 0 16 16">
                                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 4zm.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2z"/>
                                    </svg>
                                </div>
                            </div>
                        @endisset
                        @php
                            $features = !empty($ride->features) ? explode('=', $ride->features) : [];
                        @endphp
                        @include('partials.ride_feature_items', [
                            'features' => $features,
                        ])
                    </div>
                </div>
            </div>
            <div class="col-span-1">
                <div class="space-y-4 -mt-4">
                    <div class="bg-white rounded-lg overflow-hidden shadow-3xl hidden">
                        <div class="bg-primary text-white px-4 py-2">
                            <h3 class="text-2xl xl:text-3xl">Cancellation policy</h3>
                        </div>
                        <div class="bg-white p-4">
                            @isset($postRidePage->cancellation_policy_label1, $postRidePage->cancellation_policy_label2)
                                @if ($booking->type === $postRidePage->cancellation_policy_label1->features_setting_id)
                                    <div class="flex items-center space-x-1 md:space-x-2 mb-2 mr-2 lg:mr-2">
                                        <input id="standard" name="type" type="radio" value="{{ $postRidePage->cancellation_policy_label1->features_setting_id }}" checked
                                            class="h-5 w-5 rounded bg-white border border-gray-200 cursor-pointer text-indigo-600 focus:ring-indigo-600">
                                        <label for="standard"
                                            class="ml-3 font-normal text-gray-900 flex items-center space-x-1">
                                            <span class="">
                                                {{ $postRidePage->cancellation_policy_label1->name }}
                                            </span>
                                        </label>
                                    </div>
                                @elseif ($booking->type == $postRidePage->cancellation_policy_label2->features_setting_id)
                                    <div class="flex items-center space-x-1 md:space-x-2 mb-2 mr-2 lg:mr-2">
                                        <input id="firm" name="type" type="radio" value="{{ $postRidePage->cancellation_policy_label2->features_setting_id }}" checked
                                            class="h-5 w-5 rounded bg-white border border-gray-200 cursor-pointer text-indigo-600 focus:ring-indigo-600">
                                        <label for="firm"
                                            class="ml-3 font-normal text-gray-900 flex items-center space-x-1">
                                            <span class="">
                                                {{ $postRidePage->cancellation_policy_label2->name }}
                                            </span>
                                        </label>
                                    </div>
                                @endif
                            @endisset
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-3xl">
                        <div class="bg-primary text-white px-4 py-2 rounded-t-lg">
                            <h3 class="text-2xl xl:text-3xl">
                                @isset($bookingPage->booking_label)
                                    {{ $bookingPage->booking_label }}
                                @endisset
                            </h3>
                        </div>

                        <div class="bg-white p-4 rounded-b-lg">
                            <div class="space-y-4 mb-4">
                                <div class="flex items-center justify-between gap-2">
                                    <div class="flex relative">
                                        <h3 class="text-primary text-2xl xl:text-3xl">
                                            @isset($bookingPage->seats_available_label)
                                                {{ $bookingPage->seats_available_label }}
                                            @endisset
                                        </h3>
                                    </div>
                                </div>

                                @if (auth()->user() && (auth()->user()->student == '1' || auth()->user()->student == '2') && (optional($ride->payment_method)->features_setting_id ?? null) === (optional($postRidePage->payment_methods_option1)->features_setting_id ?? null))
                                    <div class="mb-3 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                        <p class="text-yellow-800 text-sm">
                                            <strong>Note for Students:</strong> You are limited to booking a maximum of 2 seats per ride for Cash payment rides.
                                        </p>
                                    </div>
                                @endif
                                <div class="flex items-center flex-wrap gap-2 mt-2" id="seat-selection-container">
                                    @foreach ($ride->seatDetail as $detail)
                                        @php
                                            $userBookedSeats = $userAllBookedSeats ?? $booking->seatDetail ?? collect();
                                            $isMyBookedSeat = $userBookedSeats->contains('id', $detail->id);
                                            $isBookedByOthers = $detail->status === 'booked' && !$isMyBookedSeat;
                                            $isHeldByOthers = $detail->status === 'hold' && $detail->user_id != optional(auth()->user())->id;
                                            $isUnavailable = $isBookedByOthers || $isHeldByOthers;
                                            $isSelectedByMe = !$isUnavailable && !$isMyBookedSeat && in_array($detail->id, old('seats_id', []));
                                        @endphp
                                        <div class="relative seat-item" data-seat-id="{{ $detail->id }}" data-seat-number="{{ $detail->seat_number ?? $loop->iteration }}" data-is-booked="{{ ($isUnavailable || $isMyBookedSeat) ? '1' : '0' }}">
                                            @if ($isUnavailable)
                                                <div class="opacity-50 cursor-not-allowed pointer-events-none">
                                                    <span class="relative inline-block w-6 h-6 md:w-8 md:h-8">
                                                        <img src="{{ asset('assets/seat.png') }}" class="w-8 h-8 object-cover seat-image seat-unselect-{{ $detail->id }}" alt="">
                                                        <span class="absolute mt-2 inset-0 flex items-center justify-center text-sm seat-number seat-number-{{ $detail->id }}">{{ $detail->seat_number ?? $loop->iteration }}</span>
                                                    </span>
                                                </div>
                                            @elseif ($isMyBookedSeat)
                                                <div class="opacity-70 cursor-not-allowed pointer-events-none select-none">
                                                    <input id="number-of-seat-{{ $detail->id }}" name="seats_id[]" type="checkbox" value="{{ $detail->id }}" class="hidden seat-checkbox seat-checkbox-locked" checked readonly tabindex="-1" data-seat-id="{{ $detail->id }}" data-seat-number="{{ $detail->seat_number ?? $loop->iteration }}">
                                                    <span class="relative inline-block w-6 h-6 md:w-8 md:h-8">
                                                        <img src="{{ asset('assets/seat-hover-1.png') }}" class="w-8 h-8 object-cover seat-image seat-unselect-{{ $detail->id }}" alt="">
                                                        <span class="absolute mt-2 inset-0 flex items-center justify-center text-sm seat-number seat-number-{{ $detail->id }} text-green-300">{{ $detail->seat_number ?? $loop->iteration }}</span>
                                                    </span>
                                                </div>
                                            @else
                                                <label class="cursor-pointer inline-block seat-clickable" for="number-of-seat-{{ $detail->id }}" data-seat-id="{{ $detail->id }}" data-seat-number="{{ $detail->seat_number ?? $loop->iteration }}" onclick="seat_selected(event, {{ $detail->id }}, {{ $detail->seat_number ?? $loop->iteration }})">
                                                    <input id="number-of-seat-{{ $detail->id }}" name="seats_id[]" type="checkbox" value="{{ $detail->id }}" class="hidden seat-checkbox" {{ $isSelectedByMe ? 'checked' : '' }} data-parsley-required="true" data-parsley-trigger="blur focusout change" data-parsley-required-message="Please select the available seats." data-parsley-errors-container="#parsley-seats-error" data-seat-id="{{ $detail->id }}" data-seat-number="{{ $detail->seat_number ?? $loop->iteration }}">
                                                    <span class="relative inline-block w-6 h-6 md:w-8 md:h-8">
                                                        <img src="{{ $isSelectedByMe ? asset('assets/seat-hover-1.png') : asset('assets/seat.png') }}" class="w-8 h-8 object-cover cursor-pointer seat-image seat-unselect-{{ $detail->id }}" alt="">
                                                        <span class="absolute mt-2 inset-0 flex items-center justify-center text-sm seat-number seat-number-{{ $detail->id }} {{ $isSelectedByMe ? 'text-green-300' : '' }}">{{ $detail->seat_number ?? $loop->iteration }}</span>
                                                    </span>
                                                </label>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                                @error('seats')
                                  <div class="relative tooltip -bottom-4 group-hover:flex">
                                    <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                        <p class="text-white leading-none text-sm lg:text-base">@isset($bookingPage->seats_available_tooltip) {{ $bookingPage->seats_available_tooltip }} @endisset</p>
                                    </div>
                                  </div>
                                @enderror

                                <div id ="seats-error" class="relative tooltip -bottom-4 group-hover:flex">
                                    <div role="tooltip" class="hidden relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 bg-red-500 text-gray-600 w-full md:w-1/2 rounded " >
                                        <p class="text-white leading-none text-sm lg:text-base"></p>
                                    </div>
                                  </div>
                                <!-- Hidden input to store count -->
                                <input type="hidden" id="seat-count" name="seats" value="">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4 bg-white rounded-lg shadow-3xl">   
                        <div class="bg-primary text-white px-4 py-2 rounded-t-lg">
                            <h3 class="text-2xl xl:text-3xl">
                                @isset($bookingPage->pricing_label)
                                    {{ $bookingPage->pricing_label }}
                                @endisset
                            </h3>
                        </div>
                        <div class="bg-white p-4 rounded-b-lg">
                            <div class="flex items-center justify-between gap-2">
                                <p class="text-black">
                                    <span id="selectedSeats">1</span>
                                    @isset($bookingPage->seat_label)
                                        {{ $bookingPage->seat_label }}
                                    @endisset
                                </p>
                                <p class="totalSeatsAmount text-black"></p>
                                <input type="hidden" name="seats_amount" class="totalSeatsAmountInput form-control" readonly>
                            </div>

                            @if ($ride->booking_type == $postRidePage->cancellation_policy_label2->features_setting_id)
                                <!-- <div class="flex items-center justify-between gap-2">
                                    <p class="text-black">
                                        {{ $bookingPage->firm_cancellation_label_price_section ?? "Firm cancellation" }} {{$settingFirmDiscount}}%
                                    </p>
                                </div> -->

                                <div class="flex items-center justify-between gap-2">
                                    <p class="text-black">
                                        {{ $bookingPage->firm_discount_label_price_section ?? "Discount" }}
                                    </p>
                                    <p class="firmDiscountAmt text-black"></p>
                                </div>
                                <div class="flex items-center justify-between gap-2">
                                    <p class="text-black">
                                        {{ $bookingPage->firm_your_price_label_price_section ?? "Your price" }}
                                    </p>
                                    <p class="yourPriceAmt text-black"></p>
                                </div>
                            @endif

                            <div class="flex items-center justify-between gap-2 mt-1">
                                <div class="flex items-center gap-2">
                                    <p class="text-black">
                                        @isset($bookingPage->booking_fee_label)
                                            {{ $bookingPage->booking_fee_label }}
                                        @endisset
                                    </p>
                                    @if (auth()->user() && auth()->user()->student == 2)
                                        <div class="relative sups inline-flex items-center group">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-yellow-500 cursor-help hover:text-yellow-600 peer">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                            </svg>
                                            <!-- Tooltip -->
                                            <div class="absolute tooltip hidden left-full bottom-full mb-2 z-50 shift-left group-hover:flex peer-hover:flex">
                                                <div class="student-verification-tooltip">
                                                    <p class="text-white text-sm">Your student verification is pending. Pay the Booking Fee now to secure your seat, and we will refund it automatically once your status is approved (usually within 72 hours).</p>
                                                </div>
                                            </div>
                                        </div>
                                    @elseif (auth()->user() && auth()->user()->charge_booking == '2')
                                        <div class="relative sups inline-flex items-center group">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-green-500 cursor-help hover:text-green-600 peer">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <!-- Tooltip -->
                                            <div class="absolute tooltip hidden left-full bottom-full mb-2 z-50 shift-left group-hover:flex peer-hover:flex">
                                                <div class="student-verification-tooltip bg-green-500">
                                                    <p class="text-white text-md">As a verified student, your booking fee is waived. You only pay the booking price.</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <p class="totalAmount text-black"></p>
                                <input type="hidden" name="booking_credit" class="totalAmountInput form-control" readonly>
                            </div>

                            @if (isset($setting->deduct_tax) && $setting->deduct_tax == "deduct_from_passenger")
                            @php
                                if($setting->tax_type == "state_wise_tax"){
                                    $settingTaxPercentage = $stateTax;
                                }else{
                                    $settingTaxPercentage = $setting->tax;
                                }
                            @endphp

                            <input type="hidden" value="{{$settingTaxPercentage}}" name="tax_percentage">
                            <input type="hidden" value="{{$setting->deduct_tax}}" name="deduct_tax">
                            <input type="hidden" value="{{$setting->tax_type}}" name="tax_type">

                                <div class="flex items-center justify-between gap-2 mt-1">
                                    <p class="text-black">
                                        @isset($bookingPage->tax_label)
                                            {{ $bookingPage->tax_label ?? "Tax" }}
                                        @endisset
                                    </p>
                                    <p class="taxAmount text-black">0</p>
                                    <input type="hidden" name="tax_amount" class="totalTaxAmountInput form-control" readonly>
                                </div>
                            @endif

                            @if ($coffeeBalance > 0)
                                @php
                                    $coffeePaymentOption1Id = is_object($postRidePage->payment_methods_option1 ?? null) ? ($postRidePage->payment_methods_option1->features_setting_id ?? null) : ($postRidePage->payment_methods_option1 ?? null);
                                    $coffeePaymentOption3Id = isset($postRidePage->payment_methods_option3) ? (is_object($postRidePage->payment_methods_option3) ? ($postRidePage->payment_methods_option3->features_setting_id ?? null) : $postRidePage->payment_methods_option3) : null;
                                    $coffeeRidePaymentId = is_object($ride->payment_method ?? null) ? ($ride->payment_method->features_setting_id ?? null) : ($ride->payment_method ?? null);
                                @endphp
                                <div class="flex items-center justify-between gap-2 mt-1">
                                    <div class="flex">
                                        <input type="checkbox" id="apply_coffee_wall" name="coffee_wall" value="1" class="form-control hidden peer">
                                        <label for="apply_coffee_wall" class="inline-flex items-center justify-center w-full px-2 py-0.5 text-primary bg-white border-2 border-primary rounded cursor-pointer peer-checked:bg-primary peer-checked:text-white">
                                            <span class="font-medium font-FuturaMdCnBT text-xl line-clamp-2 max-w-36 w-full">
                                                {{ $bookingPage->coffee_from_wall_label ?? 'Pay booking fee with Coffee from the Wall' }}
                                            </span>
                                        </label>
                                        <div class="sups relative inline-flex ml-2 group">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle-fill text-black cursor-help peer" viewBox="0 0 16 16">
                                                <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                                            </svg>
                                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 z-50 hidden group-hover:flex peer-hover:flex flex-col items-center">
                                                <div role="tooltip" class="payment-method-tooltip tooltip_width sm:w-[25rem] md:w-[30rem] lg:w-72 xl:w-[23rem] 2xl:w-[25rem]">
                                                    @if ($coffeeRidePaymentId !== null && $coffeeRidePaymentId == $coffeePaymentOption1Id)
                                                        <p class="text-white font-semibold text-start text-sm lg:text-base">To be paid in cash directly to the driver at the time of the ride.</p>
                                                    @elseif ($coffeePaymentOption3Id !== null && $coffeeRidePaymentId == $coffeePaymentOption3Id)
                                                        <p class="text-white font-semibold text-start text-sm lg:text-base">This amount is pre-authorized to ProximaRide now and will be refunded to you once you meet the driver and pay them in cash.</p>
                                                    @else
                                                        <p class="text-white font-semibold text-start text-sm lg:text-base">ProximaRide will transfer this amount to the driver only after the ride is completed.</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="hideBookingFee" class="hidden items-center space-x-1">
                                        <p class="text-black">-</p>
                                        <p class="totalAmount text-black"></p>
                                        <div class="sups relative inline-flex ml-2 group">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle-fill text-black cursor-help peer" viewBox="0 0 16 16">
                                                <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                                            </svg>
                                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 z-50 hidden group-hover:flex peer-hover:flex flex-col items-center">
                                                <div role="tooltip" class="payment-method-tooltip tooltip_width sm:w-[25rem] md:w-[30rem] lg:w-72 xl:w-[23rem] 2xl:w-[25rem]">
                                                    @if ($coffeeRidePaymentId !== null && $coffeeRidePaymentId == $coffeePaymentOption1Id)
                                                        <p class="text-white font-semibold text-start text-sm lg:text-base">To be paid in cash directly to the driver at the time of the ride.</p>
                                                    @elseif ($coffeePaymentOption3Id !== null && $coffeeRidePaymentId == $coffeePaymentOption3Id)
                                                        <p class="text-white font-semibold text-start text-sm lg:text-base">This amount is pre-authorized to ProximaRide now and will be refunded to you once you meet the driver and pay them in cash.</p>
                                                    @else
                                                        <p class="text-white font-semibold text-start text-sm lg:text-base">ProximaRide will transfer this amount to the driver only after the ride is completed.</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            @if ($ride->payment_method->features_setting_id === $postRidePage->payment_methods_option1->features_setting_id)
                                <div class="flex items-center justify-between gap-2 mt-1">
                                    {{-- <p>Total online payment</p>
                                    <p class="totalAmount text-black"></p> --}}
                                    <input type="hidden" name="online_payment" class="totalAmountIn form-control" readonly>
                                </div>
                                <div class="flex items-center justify-between gap-2 mt-1">
                                    {{-- <p>Total cash payment</p>
                                    <p class="totalSeatsAmount text-black"></p> --}}
                                    <input type="hidden" name="cash_payment" class="totalSeatsAmountInput form-control" readonly>
                                </div>
                            @else
                                <div class="flex items-center justify-between gap-2 mt-1">
                                    {{-- <p>Total online payment</p>
                                    <p class="totalSum text-black"></p> --}}
                                    <input type="hidden" name="online_payment" class="totalSumIn form-control" readonly>
                                </div>
                                <div class="flex items-center justify-between gap-2 mt-1">
                                    {{-- <p>Total cash payment</p>
                                    <p class="text-black">$0.00</p> --}}
                                    <input type="hidden" name="cash_payment" value="0" class="form-control" readonly>
                                </div>
                            @endif
                            <input type="hidden" name="booked_by_wallet" class="bookedByWallet form-control" readonly>
                            <div class="flex items-center justify-between gap-2 mt-1">
                                <p>
                                    @isset($bookingPage->total_label)
                                        {{ $bookingPage->total_label }}
                                    @endisset
                                </p>
                                <div>
                                    <p class="totalSum text-right"></p>
                                    <span id="discount" class="text-right"></span>
                                </div>
                                <input type="hidden" name="total" class="totalSumInput form-control" readonly>
                                <input type="hidden" id="stripeChargeAmount" value="">
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg overflow-hidden shadow-3xl">
                        <div class="bg-primary text-white px-4 py-2">
                            <h3 class="text-2xl xl:text-3xl">{{ $bookingPage->booking_term_label ?? 'User Declarations' }}</h3>
                        </div>
                        <div class="bg-white p-4">
                            <ul class="">
                                <li> <p class="text-left">● @isset($bookingPage->booking_disclaimer_on_time){!! $bookingPage->booking_disclaimer_on_time !!} @endisset</p></li>
                                <li> <p class="text-left mt-4"><strong>● Pink Rides: </strong> {{ $bookingPage->booking_disclaimer_pink_ride ?? "I know that ProximaRide are exclusive to ProximaRide female members. If I am booking on a Pink Ride, I will not be accompanied by male members who are above 12 years of age, nor will I send a male member in my place. If I do, the driver will not take me or them, and I will not be refunded"}}</p></li>
                                <li> <p class="text-left mt-4"><strong>● Extra+ Rides: </strong> {{ $bookingPage->booking_disclaimer_extra_care_ride ?? "I know that Extra+ Rides are exclusive to members with highest review score. If I am booking on an Extra+ Ride, I will adhere to its standards" }}</p></li>
                            </ul>

                            <div class="flex items-start my-4">
                                <input id="" type="checkbox" name="agree_terms" value="1"
                                    {{ old('agree_terms') == '1' ? 'checked' : '' }} onchange="getFirmAgreeTerms();"
                                    class="w-4 h-4 text-blue-600 cursor-pointer bg-white mt-2 border-gray-600 rounded focus:ring-blue-800  focus:ring-2">
                                <label for="" class="ml-2 font-normal text-gray-900">
                                    @isset($bookingPage->booking_term_agree_text)
                                        {!! $bookingPage->booking_term_agree_text !!}
                                    @endisset
                                    <span class="text-red-500">*</span>
                                </label>
                            </div>
                            @error('agree_terms')
                            <div id="agree_terms-laravel-error" class="relative tooltip -bottom-4 group-hover:flex">
                                <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                    <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                </div>
                            </div>
                            @enderror

                            <div id ="agree_terms-error" class="relative tooltip -bottom-4 group-hover:flex">
                                <div role="tooltip" class="hidden relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                    <p class="text-white leading-none text-sm lg:text-base"></p>
                                </div>
                            </div>



                            @if ($ride->booking_type == "37")
                                @php
                                    if ($setting){
                                        $settingFirmDiscount = $setting->frim_discount;
                                    }

                                    $firmText = str_replace(":discount", $settingFirmDiscount, $bookingPage->booking_disclaimer_firm);
                                @endphp
                                <div class="flex items-start my-4">
                                    <input id="" type="checkbox" name="firm_agree_terms" value="1"
                                        {{ old('firm_agree_terms') == '1' ? 'checked' : '' }} onchange="getFirmAgreeTerms();"
                                        class="w-4 h-4 text-blue-600 cursor-pointer bg-white mt-1 border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                    <label for="" class="ml-2 font-normal text-gray-900">
                                        @isset($firmText)
                                            {!! $firmText !!}
                                        @endisset
                                        <span class="text-red-500">*</span>
                                    </label>
                                </div>
                                @error('firm_agree_terms')
                                <div id="firm_agree_terms-laravel-error" class="relative tooltip -bottom-4 group-hover:flex">
                                    <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                        <p class="text-white leading-none text-sm lg:text-base">{{ $bookingPage->booking_disclaimer_firm_tooltip }}</p>
                                    </div>
                                </div>
                                @enderror

                                <div id ="firm_agree_terms-error" class="relative tooltip -bottom-4 group-hover:flex">
                                    <div role="tooltip" class="hidden relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                        <p class="text-white leading-none text-sm lg:text-base"></p>
                                    </div>
                                </div>

                            @endif

                            @if (collect($featureOptions ?? [])->contains(fn ($option) => $option['slug'] === 'pink_rides' && in_array($option['label'], $features)))
                                <div class="flex items-start my-4">
                                    <input id="" type="checkbox" name="pink_ride_agree_terms" value="1"
                                        {{ old('pink_ride_agree_terms') == '1' ? 'checked' : '' }} onchange="getFirmAgreeTerms();"
                                        class="w-4 h-4 text-blue-600 cursor-pointer bg-white mt-1 border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                    <label for="" class="ml-2 font-normal text-gray-900">
                                        @isset($bookingPage->booking_pink_ride_term_agree_text)
                                            {!! $bookingPage->booking_pink_ride_term_agree_text !!}
                                        @endisset
                                        <span class="text-red-500">*</span>
                                    </label>
                                </div>
                                @error('pink_ride_agree_terms')
                                    <div id="pink_ride_agree_terms-laravel-error" class="relative tooltip -bottom-4 group-hover:flex">
                                        <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                            <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                        </div>
                                    </div>
                                @enderror

                                <div id ="pink_ride_agree_terms-error" class="relative tooltip -bottom-4 group-hover:flex">
                                    <div role="tooltip" class="hidden relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                        <p class="text-white leading-none text-sm lg:text-base"></p>
                                    </div>
                                </div>
                            @endif

                            @if (collect($featureOptions ?? [])->contains(fn ($option) => $option['slug'] === 'extra_care_rides' && in_array($option['label'], $features)))
                                <div class="flex items-start my-4">
                                    <input id="" type="checkbox" name="extra_care_ride_agree_terms" value="1"
                                        {{ old('extra_care_ride_agree_terms') == '1' ? 'checked' : '' }} onchange="getFirmAgreeTerms();"
                                        class="w-4 h-4 text-blue-600 cursor-pointer bg-white mt-1 border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                    <label for="" class="ml-2 font-normal text-gray-900">
                                        @isset($bookingPage->booking_extra_care_ride_term_agree_text)
                                            {!! $bookingPage->booking_extra_care_ride_term_agree_text !!}
                                        @endisset
                                        <span class="text-red-500">*</span>
                                    </label>
                                </div>
                                @error('extra_care_ride_agree_terms')
                                    <div id="extra_care_ride_agree_terms-laravel-error" class="relative tooltip -bottom-4 group-hover:flex">
                                        <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                            <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                        </div>
                                    </div>
                                @enderror

                                <div id ="extra_care_ride_agree_terms-error" class="relative tooltip -bottom-4 group-hover:flex">
                                    <div role="tooltip" class="hidden relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                        <p class="text-white leading-none text-sm lg:text-base"></p>
                                    </div>
                                </div>
                            @endif


                            @if ($ride->payment_method->features_setting_id === $postRidePage->payment_methods_option1->features_setting_id && $ride->rideDetail[0]->price <= 15)

                            @else
                                <div id="paymentSection" class="space-y-4 mb-4 hidden">
                                    <h3 class="text-primary text-2xl xl:text-3xl">
                                        @isset($bookingPage->like_to_pay_label)
                                            {{ $bookingPage->like_to_pay_label }}
                                        @endisset
                                    </h3>
                                    <div class="bg-white md:p-4">
                                        <div class="border rounded-md overflow-hidden divide-y">
                                            <div class="flex items-center justify-between p-3">
                                                <input type="radio" id="paypal" name="payment_method" value="paypal" class="hidden peer">
                                                <label for="paypal" class="inline-flex items-center space-x-3 w-full p-4 text-gray-800 bg-white border-2 border-gray-100 rounded cursor-pointer peer-checked:border-blue-500 peer-checked:border-2 peer-checked:text-blue-500 hover:border-2 hover:border-blue-500">
                                                    <span class="font-medium text-xl">
                                                        @isset($bookingPage->paypal_label)
                                                            {!! $bookingPage->paypal_label !!}
                                                        @endisset
                                                    </span>
                                                </label>
                                            </div>
                                            <div>
                                                <div class="flex items-center justify-between p-3">
                                                    <input type="radio" id="credit_card" name="payment_method" value="credit_card" class="hidden peer" {{ old('payment_method') === 'credit_card' ? 'checked' : '' }}>
                                                    <label for="credit_card" class="inline-flex items-center space-x-3 w-full p-4 text-gray-800 bg-white border-2 border-gray-100 rounded cursor-pointer peer-checked:border-blue-500 peer-checked:border-2 peer-checked:text-blue-500 hover:border-2 hover:border-blue-500">
                                                        <span class="font-medium text-xl">
                                                            @isset($bookingPage->credit_card_label)
                                                                {!! $bookingPage->credit_card_label !!}
                                                            @endisset
                                                        </span>
                                                    </label>
                                                </div>
                                                <div class="cards mt-2 pb-2 {{ old('payment_method') === 'credit_card' ? '' : 'hidden' }}">
                                                    @foreach ($cards as $card)
                                                        @if ($card->paymentMethod)
                                                            <div class="flex items-start justify-between p-3">
                                                                <label for="card_id" class="font-normal text-gray-900 flex items-start space-x-1">
                                                                    <div>
                                                                        <p class="leading-normal mt-2">
                                                                            **** **** **** {{ $card->paymentMethod->card->last4 }}
                                                                        </p>
                                                                        <div class="font-normal text-gray-900 flex lg:block items-center space-x-0.5 2xl:pr-8">
                                                                            <small>{{ ucfirst($card->paymentMethod->card->brand) }}</small>
                                                                        </div>
                                                                    </div>
                                                                </label>
                                                                <input type="radio" id="card_id" name="card_id" value="{{ $card->id }}"
                                                                    {{ old('card_id', $card->primary_card ? $card->id : '') == $card->id ? 'checked' : '' }} class="w-4 h-4 mt-2 ml-4 text-blue-600 cursor-pointer bg-white border-gray-500 rounded focus:ring-blue-500  focus:ring-2">
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                    @error('card_id')
                                                      <div id="card_id-laravel-error" class="relative tooltip -bottom-4 group-hover:flex">
                                                        <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                                            <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                                        </div>
                                                      </div>
                                                    @enderror
                                                    @if($cards->isEmpty())
                                                    <div class="flex justify-center items-center mt-4">
                                                        {{-- <a href="{{ route('my_cards.create', ['lang' => $selectedLanguage->abbreviation, 'rideDetailId' => $ride->rideDetail[0]->id, 'rideId' => $ride->rideDetail[0]->ride_id, 'type' => 'booking']) }}" class="button-exp-fill"> --}}
                                                        <button onclick="storeDataAndRedirect()" class="button-exp-fill">
                                                            @isset($bookingPage->add_card_label)
                                                                {!! $bookingPage->add_card_label !!}
                                                            @endisset
                                                        </button>
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        @error('payment_method')
                                          <div id="payment_method-laravel-error" class="relative tooltip -bottom-4 group-hover:flex">
                                            <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                                <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                            </div>
                                          </div>
                                        @enderror
                                    </div>
                                </div>
                            @endif

                            @if ($ride->payment_method->features_setting_id === $postRidePage->payment_methods_option1->features_setting_id && $ride->rideDetail[0]->price <= 15)

                            @else
                                <div id="paymentSectionGPay" class="hidden">
                                    <div id="payment-request-button"></div>
                                </div>
                            @endif

                                @isset($ride->booking_method->features_setting_id)
                                <div class="flex justify-center items-center mt-4">
                                    <button id="submitButton" class="button-exp-fill" type="submit">
                                        <!-- {{ $ride->booking_method->name }} -->
                                        {{ $siteText['pay_and_request_to_book_btn_text'] ?? 'Pay and Request to Book' }}
                                    </button>
                                </div>
                                @endisset
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<div id="bookingModal" class="hidden fixed z-50 inset-0 overflow-y-auto">
    <div class="relative z-50">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeModal()"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto pointer-events-none">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0 pointer-events-none">
                <div
                    class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg w-full modal-border pointer-events-auto">
                    <button type="button" id="close-modal" class="absolute top-3 right-3 text-gray-400 hover:text-gray-500">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start justify-center">
                        </div>
                        <div class="text-center sm:ml-4 sm:mt-0 sm:text-left">
                            <div class="w-full">
                                <p class="text-md text-center mt-10 text-gray-500" id="bookingModalMessage"></p>
                            </div>
                        </div>
                    </div>
                    <div class="px-4 pb-6 pt-4 sm:flex sm:flex-row-reverse sm:px-6 justify-center">
                        <button type="button" id="close-popup" class="inline-flex justify-center rounded bg-blue-500 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-blue-400 sm:ml-3 sm:w-24">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Student Seat Limit Modal -->
<div id="studentSeatLimitModal" class="hidden fixed z-50 inset-0 overflow-y-auto" aria-labelledby="student-seat-limit-modal-title" role="dialog" aria-modal="true">
    <div onclick="closeStudentSeatLimitModal()" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border" onclick="event.stopPropagation()">
                <button type="button" onclick="closeStudentSeatLimitModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-500 z-50">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <div class="bg-white px-4 mt-10 sm:mt-1 pb-4 pt-16 sm:p-6 sm:pb-4 sm:pt-16">
                    <div class="sm:flex sm:items-start justify-center">
                        <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full sm:mx-0 sm:h-10 sm:w-10 bg-yellow-100">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-yellow-600">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                            </svg>
                        </div>
                    </div>
                    <div class="text-center sm:ml-4 sm:mt-0 sm:text-left">
                        <div class="">
                            <h3 class="text-3xl text-center font-FuturaMdCnBT text-gray-900 mb-4" id="student-seat-limit-modal-title">Seat Limit Reached</h3>
                        </div>
                        <div class="mt-2 w-full">
                            <p class="can-exp-p text-center">Students are limited to booking a maximum of 2 seats per ride for Cash payment rides.</p>
                        </div>
                    </div>
                </div>
                <div class="px-4 pb-6 pt-4 sm:flex sm:flex-row-reverse sm:px-6 justify-center">
                    <button type="button" onclick="closeStudentSeatLimitModal()" class="inline-flex justify-center rounded bg-primary px-6 py-2 font-FuturaMdCnBT text-lg text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-blue-600">
                        {{ $siteText['ok_btn_text'] }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://js.stripe.com/v3/"></script>


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
});
</script>



<script>
    const stripe = Stripe('{{ env('STRIPE_KEY') }}'); // Your public key from Stripe

        const paymentRequest = stripe.paymentRequest({
        country: 'CA',
        currency: 'cad',
        total: {
            label: 'Total',
            amount: 100,
        },
        requestPayerName: true,
        requestPayerEmail: true,
        paymentMethodTypes: ['card'],
        });

        // Check if the device/browser supports Apple Pay or Google Pay
        paymentRequest.canMakePayment().then(function(result){
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

        // Use the amount shown in Google/Apple Pay (same as paymentRequest.update)
        const amountInput = document.getElementById('stripeChargeAmount');
        const amount = amountInput && amountInput.value !== '' ? amountInput.value : (document.querySelectorAll('[name="online_payment"]')[1] ? document.querySelectorAll('[name="online_payment"]')[1].value : document.querySelector('[name="online_payment"]').value);
        
  const response = await fetch('/create-payment-intent', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
    },
    body: JSON.stringify({ payment_method: ev.paymentMethod.id, amount: amount}),
  });

  const { clientSecret } = await response.json();

  // Confirm the payment
  const { error, paymentIntent } = await stripe.confirmCardPayment(clientSecret, {
    payment_method: ev.paymentMethod.id,
  });

  if (error) {
    ev.complete('fail');
    console.error(error.message);
  } else {
    ev.complete('success');

    
    document.querySelector('[name="gPayApplePayId"]').value = paymentIntent.id;
    document.querySelector('[name="payment_method"][value="credit_card"]').checked = true;
    

    console.log('Transaction ID:', paymentIntent.id); // <--- HERE
    console.log('Status:', paymentIntent.status);

    document.getElementById('submitForm').submit();
    // Handle post-payment success (e.g., show a confirmation page)
    console.log('Payment Successful!');
  }
});

</script>

<script>

    function storeDataAndRedirect() {
        var data = {
            bookingId: @json($booking->id),
            type: 'edit-booking',
            lang: @json( $selectedLanguage->abbreviation) ,
            _token: '{{ csrf_token() }}'
        };

        $.ajax({
            url: '{{ route("my_cards.sessionData") }}',
            type: 'POST',
            data: data,
            success: function (response) {
                window.location.href = '{{ route("my_cards.create", ["lang" => "__lang__"]) }}'.replace('__lang__', data.lang);

            },

        });
    }

    var editBookingSeatsStorageKey = 'edit_booking_seats_{{ $ride->id }}_{{ $booking->ride_detail_id }}';

    // Clear seat selection when leaving page without submitting (e.g. back button) so it doesn't persist on return
    window.addEventListener('beforeunload', function() {
        try { sessionStorage.removeItem(editBookingSeatsStorageKey); } catch (e) { /* ignore */ }
    });

    $(document).ready(function () {
        // Restore seat selection only after in-page refresh (beforeunload clears on navigate away)
        try {
            var saved = sessionStorage.getItem(editBookingSeatsStorageKey);
            if (saved) {
                var ids = JSON.parse(saved);
                var selectedImg = '{{ asset("assets/seat-hover-1.png") }}';
                var unselectedImg = '{{ asset("assets/seat.png") }}';
                $("input[name='seats_id[]']").each(function() {
                    var id = $(this).val();
                    var shouldCheck = ids.indexOf(id) !== -1;
                    $(this).prop('checked', shouldCheck);
                    $(".seat-image.seat-unselect-" + id).attr('src', shouldCheck ? selectedImg : unselectedImg);
                    if (shouldCheck) {
                        $(".seat-number.seat-number-" + id).addClass('text-green-300');
                    } else {
                        $(".seat-number.seat-number-" + id).removeClass('text-green-300');
                    }
                });
            }
        } catch (e) { /* ignore */ }
        updateTotalAmount();

        $('input[name="coffee_wall"]').change(function () {
            updateTotalAmount();
        });

        // Trigger the change event on page load
        $('#type').trigger('change');

        $('input[type=radio][name=payment_method]').change(function() {
            if (this.value === 'credit_card') {
                $('.cards').removeClass('hidden');
                // $('.other_number').addClass('hidden');
            } else if (this.value === 'paypal') {
                $('.cards').addClass('hidden');
                // $('.other_number').removeClass('hidden');
            }
        });
    });

    // Get the current date
    var currentDate = new Date();

    var settingBookingPrice = "{{ $settingBookingPrice }}";

    // Check if $setting is defined and not null
    var bookingPrice;

    // Check if user is a student who should NOT be charged booking fee
    // charge_booking = '2' means booking fee is waived (student with valid card)
    // charge_booking = '1' means booking fee is charged (regular user or student with expired card)
    var chargeBooking = {{ auth()->user() && auth()->user()->charge_booking ? auth()->user()->charge_booking : '1' }};
    var isStudentFeeWaived = (chargeBooking == '2');

    if (isStudentFeeWaived) {
        // Student with valid card - booking fee is waived
        bookingPrice = 0.0;
    } else {
        // 10% of seat price for all rides above $15 (same logic as booking.blade.php)
        if (@json($booking->price) <= 15) {
            bookingPrice = 0.0;
        } else {
            bookingPrice = parseFloat((10 / 100) * @json($booking->price));
        }
    }
    // Function to update the total amount
    function updateTotalAmount() {
        var seatPrice = parseFloat({{ $booking->price }});
        var bookedSeats = parseInt("{{ $booking->seats }}") || 1;
        var checkboxCount = $("input[name='seats_id[]']:checked").length;
        // Use checkbox count when available, otherwise fall back to booked seats (e.g. when seatDetail is empty)
        var selectedSeats = checkboxCount > 0 ? checkboxCount : bookedSeats;
        var totalAmount = bookingPrice * selectedSeats;
        var totalBookingCredit = "{{ $booking->booking_credit }}";
        var totalFare = "{{ $booking->fare }}";
        var totaltaxAmount = "{{ $booking->tax_amount }}";
        var totalPaid = Number(totalFare) + Number(totalBookingCredit) + Number(totaltaxAmount);
        var totalSeatsAmount = seatPrice * selectedSeats;

        const seatCountInput = document.getElementById('seat-count');
        // Update the hidden field's value
        seatCountInput.value = selectedSeats;

        $('#discount').text('');

        var firm = "{{ $firm }}";
        var isFirmRide = {{ ($ride->booking_type == ($postRidePage->cancellation_policy_label2->features_setting_id ?? false)) ? 'true' : 'false' }};
        var totalRideSeatAmout = totalSeatsAmount;
        var firmSelected = isFirmRide || ($('input[name="type"]:checked').length && $('input[name="type"]:checked').val() === firm);
        if (firmSelected) {
            var settingFirmDiscount = "{{ $settingFirmDiscount }}";
            if (settingFirmDiscount && settingFirmDiscount !== '') {
                var firmAmt = (totalSeatsAmount * settingFirmDiscount / 100);
                totalSeatsAmount = totalSeatsAmount - firmAmt;
                $(".firmDiscountAmt").text('$'+firmAmt.toFixed(2));
                $(".yourPriceAmt").text('$'+totalSeatsAmount.toFixed(2));
            }
        }

        var settingTaxPercentage = "{{$settingTaxPercentage}}";
        var taxAmount = (totalAmount * settingTaxPercentage) / 100;

        // Calculate the sum of totalAmount and totalSeatsAmount
        var totalSum = totalAmount + totalSeatsAmount + taxAmount;

        var totalAmountIn = totalAmount;

        const isTermChecked = document.querySelector('[name="agree_terms"]').checked;
        let isFirmChecked = false;
        const firmFields = document.getElementsByName('firm_agree_terms');
        if (firmFields.length > 0) {
            isFirmChecked = document.querySelector('[name="firm_agree_terms"]').checked;
        }else{
            isFirmChecked = true;
        }

        let pinkRideAgreeTerms = true;
        const pinkFields = document.getElementsByName('pink_ride_agree_terms');
        if (pinkFields.length > 0) {
            pinkRideAgreeTerms = document.querySelector('[name="pink_ride_agree_terms"]').checked;
        }else{
            pinkRideAgreeTerms = true;
        } 

        let extraCareRideAgreeTerms = true;
        const extraFields = document.getElementsByName('extra_care_ride_agree_terms');
        if (extraFields.length > 0) {
            extraCareRideAgreeTerms = document.querySelector('[name="extra_care_ride_agree_terms"]').checked;
        }else{
            extraCareRideAgreeTerms = true;
        }

        var errorElementDiv = document.getElementById('paymentSection');
        if (errorElementDiv && isTermChecked && isFirmChecked && pinkRideAgreeTerms && extraCareRideAgreeTerms) {
            if (errorElementDiv.classList.contains('hidden')) {
                errorElementDiv.classList.remove('hidden');
            }
        }

        var errorElementDivGPay = document.getElementById('paymentSectionGPay');
        if (errorElementDivGPay && isTermChecked && isFirmChecked && pinkRideAgreeTerms && extraCareRideAgreeTerms) {
            if (errorElementDivGPay.classList.contains('hidden')) {
                errorElementDivGPay.classList.remove('hidden');
            }
        }


        
        
        var totalPaidAmt = 0;
        if($("#check_payment_method").val() =="cash"){
            if ($('input[name="coffee_wall"]:checked').val()) {
                var totalPayableAmount = 0;
                totalPaid = totalPaid - Number(totalBookingCredit);
                totalSum = totalSum - totalAmount;
                totalAmountIn = 0;
                $('#showDeduction').removeClass('hidden');
                $('#showDeduction').addClass('flex');
                var hidePaymentSection = "{{ $hidePaymentSection }}";
                if (hidePaymentSection) {
                    if (errorElementDiv) {
                        if (!errorElementDiv.classList.contains('hidden')) {
                            errorElementDiv.classList.add('hidden');
                        }
                    }

                    if (errorElementDivGPay) {
                        if (!errorElementDivGPay.classList.contains('hidden')) {
                            errorElementDivGPay.classList.add('hidden');
                        }
                    }
                }
            } else {
                totalPaidAmt = Number(totalBookingCredit) + Number(totaltaxAmount);
                totalPayableAmount = Math.abs((totalAmount - totalBookingCredit) + (Number(taxAmount) - Number(totaltaxAmount)));
                $('#showDeduction').addClass('hidden');
                $('#showDeduction').removeClass('flex');
            }
        }else{
            
            if ($('input[name="coffee_wall"]:checked').val()) {

                totalPaidAmt = totalPaid;
                totalPayableAmount = Math.abs((totalSum - totalPaid) - (Math.abs(totalAmount) - Math.abs(totalBookingCredit)));
                $('#showDeduction').addClass('hidden');
                $('#showDeduction').removeClass('flex');
            }else{
                totalPaidAmt = totalPaid;
                totalPayableAmount = Math.abs((totalSum - totalPaid));
                $('#showDeduction').addClass('hidden');
                $('#showDeduction').removeClass('flex');
            }
            
        }


        

        // Format the sums to two decimal places
        var formattedTotalAmount = totalAmount.toFixed(2);
        var formattedTotalPayableAmount = totalPayableAmount;
        var formattedTaxAmount = taxAmount.toFixed(2);
        var formattedTotalPayableOnlineAmount = Math.abs(totalSum - totalPaid)
        var formattedTotalSeatsAmount = totalSeatsAmount.toFixed(2);
        var formattedTotalRideSeatAmout = totalRideSeatAmout.toFixed(2);
        var formattedTotalSum = totalSum.toFixed(2);
        // $booking->seats*$ride\+$booking->booking_credit
        // Update the content of the <p> tags
        $('#selectedSeats').text(selectedSeats);
        $('.totalAmount').text('$' + formattedTotalAmount);
        $('.taxAmount').text('$' + formattedTaxAmount);
        $('.totalPayableAmount').text('$' + formattedTotalPayableAmount.toFixed(2));
        $('.totalPaidAmount').text('$' + totalPaidAmt.toFixed(2));
        $('.totalPaidOnlineAmount').text('$' + totalPaid.toFixed(2));
        $('.totalPayableOnlineAmount').text('$' + formattedTotalPayableOnlineAmount.toFixed(2));
        $('.totalAmountInput').val(totalAmount);
        $(".totalTaxAmountInput").val(taxAmount);
        $('.totalAmountIn').val(totalAmountIn);
        $('.totalSeatsAmountInput').val(totalSeatsAmount);
        $('.totalSeatsAmount').text('$' + formattedTotalRideSeatAmout);
        $('.totalSum').text('$' + formattedTotalSum);
        $('.totalSumInput').val(totalSum);

        if(selectedSeats >= bookedSeats)
        {
            $('.payable').removeClass('hidden');
            $('.payable').addClass('flex');
            $('.refund').addClass('hidden');
            $('.refund').removeClass('flex');
        } else {
            $('.payable').addClass('hidden');
            $('.payable').removeClass('flex');
            $('.refund').removeClass('hidden');
            $('.refund').addClass('flex');
        }


        $('#stripeChargeAmount').val(totalPayableAmount);
        if($("#check_payment_method").val() =="cash"){
                paymentRequest.update({
                total: {
                    label: 'Total',
                    amount: Math.round(totalPayableAmount * 100)
                },
            });
            }else{
                paymentRequest.update({
                total: {
                    label: 'Total',
                    amount: Math.round(totalPayableAmount * 100)
                },
            });
        }
    }

    // var lastSelectedIndex = -1;
    
    // Define modal functions early to ensure they're available
    function showStudentSeatLimitModal() {
        const modal = document.getElementById('studentSeatLimitModal');
        if (!modal) {
            console.error('Student seat limit modal not found');
            // Fallback to alert if modal not found
            alert('Students are limited to booking a maximum of 2 seats per ride for Cash payment rides.');
            return;
        }
        // Remove hidden class and ensure visibility
        modal.classList.remove('hidden');
        modal.style.setProperty('display', 'block', 'important');
        modal.style.setProperty('visibility', 'visible', 'important');
        modal.style.setProperty('opacity', '1', 'important');
        modal.style.setProperty('z-index', '50', 'important');
        
        // Also ensure the backdrop is visible
        const backdrop = modal.querySelector('.fixed.inset-0.bg-gray-500');
        if (backdrop) {
            backdrop.style.setProperty('display', 'block', 'important');
        }
    }
    
    function closeStudentSeatLimitModal() {
        const modal = document.getElementById('studentSeatLimitModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.style.removeProperty('display');
            modal.style.removeProperty('visibility');
            modal.style.removeProperty('opacity');
            modal.style.removeProperty('z-index');
        }
    }
    
    // Make functions globally available immediately
    window.showStudentSeatLimitModal = showStudentSeatLimitModal;
    window.closeStudentSeatLimitModal = closeStudentSeatLimitModal;

    function persistSeatSelection() {
        try {
            var ids = [];
            $("input[name='seats_id[]']:checked").each(function() { ids.push($(this).val()); });
            sessionStorage.setItem(editBookingSeatsStorageKey, JSON.stringify(ids));
        } catch (e) { /* ignore */ }
    }

    function seat_selected(event, clickedSeatId, clickedSeatNumber) {
        event.preventDefault();
        event.stopPropagation();

        var isStudent = {{ (auth()->user() && (auth()->user()->student == '1' || auth()->user()->student == '2')) ? 'true' : 'false' }};
        var paymentMethod = $('#check_payment_method').val();
        var isCashPayment = (paymentMethod === 'cash');
        var maxSeatsForStudent = 2;

        // Build list of available seats (not booked) in order
        var availableSeats = [];
        $('#seat-selection-container .seat-item[data-is-booked="0"]').each(function() {
            var seatId = $(this).data('seat-id');
            var seatNum = parseInt($(this).data('seat-number'), 10);
            availableSeats.push({ id: seatId, seatNumber: seatNum });
        });
        availableSeats.sort(function(a, b) { return a.seatNumber - b.seatNumber; });

        // Get seats to select: all available seats with seat_number <= clicked seat_number
        var seatsToSelect = availableSeats.filter(function(s) { return s.seatNumber <= clickedSeatNumber; });

        // Student limit: cap at 2 seats for Cash payment
        if (isStudent && isCashPayment && seatsToSelect.length > maxSeatsForStudent) {
            seatsToSelect = seatsToSelect.slice(0, maxSeatsForStudent);
            showStudentSeatLimitModal();
        }

        // Check if this is a toggle-off: clicked seat was the rightmost selected (exclude locked booked seats)
        var currentlyChecked = [];
        $("input.seat-checkbox:checked:not(.seat-checkbox-locked)").each(function() { currentlyChecked.push(parseInt($(this).val(), 10)); });
        var currentlySelectedIds = currentlyChecked;
        var rightmostSelected = currentlySelectedIds.length > 0 ? Math.max.apply(null, currentlySelectedIds.map(function(id) {
            var s = availableSeats.find(function(s) { return s.id == id; });
            return s ? s.seatNumber : 0;
        })) : 0;

        var newSelectionIds = [];
        if (rightmostSelected === clickedSeatNumber && currentlySelectedIds.length > 0) {
            // Toggle off: deselect all
            newSelectionIds = [];
        } else {
            newSelectionIds = seatsToSelect.map(function(s) { return s.id; });
        }

        // Update UI immediately (exclude locked booked seats from uncheck)
        $("input.seat-checkbox:not(.seat-checkbox-locked)").prop('checked', false);
        newSelectionIds.forEach(function(id) {
            $("#number-of-seat-" + id).prop('checked', true);
        });

        // Reset only selectable seats (exclude locked booked seats - they keep their selected styling)
        $('#seat-selection-container .seat-item[data-is-booked="0"] .seat-image').attr('src', '{{ asset("assets/seat.png") }}');
        $('#seat-selection-container .seat-item[data-is-booked="0"] .seat-number').removeClass('text-green-300');
        newSelectionIds.forEach(function(id) {
            $(".seat-image.seat-unselect-" + id).attr('src', '{{ asset("assets/seat-hover-1.png") }}');
            $(".seat-number.seat-number-" + id).addClass('text-green-300');
        });

        // Edit booking: seats are already booked - no seat_on_hold API needed.
        // Backend handles seat changes on form submit. Client-side selection only.
        updateTotalAmount();
        persistSeatSelection();
    }

    function keepChecked(checkbox) {
        // Ensure the checkbox stays checked
        if (!checkbox.checked) {
            checkbox.checked = true;
        }
    }
    
    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeStudentSeatLimitModal();
        }
    });

    document.getElementById('close-modal').addEventListener('click', function () {
    const modal = document.getElementById('bookingModal');
    modal.classList.add('hidden');
});

    document.getElementById('close-popup').addEventListener('click', function () {
    const modal = document.getElementById('bookingModal');
    modal.classList.add('hidden');
});

// Function to open the modal
function openModal() {
    document.getElementById('bookingModal').classList.remove('hidden');
}

// Function to close the modal
function closeModal() {
    document.getElementById('bookingModal').classList.add('hidden');
}

// Add event listener to close the modal when the background overlay is clicked
document.getElementById('bookingModal').addEventListener('click', function (event) {
    // Close the modal only if the user clicks on the overlay, not the modal itself
    if (event.target === this) {
        closeModal();
    }
});


    // Hide payment/card error tooltips immediately when user selects an option
    function hidePaymentErrors() {
        function hideEl(id) {
            var el = document.getElementById(id);
            if (el) el.classList.add('hidden');
        }
        document.querySelectorAll('[name="payment_method"]').forEach(function(radio) {
            radio.addEventListener('change', function() { hideEl('payment_method-laravel-error'); });
        });
        document.querySelectorAll('[name="card_id"]').forEach(function(radio) {
            radio.addEventListener('change', function() { hideEl('card_id-laravel-error'); });
        });
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', hidePaymentErrors);
    } else {
        hidePaymentErrors();
    }

    function getFirmAgreeTerms() {
        updateTotalAmount();
        // Hide error tooltips immediately when checkbox is checked
        function hideErrorIfChecked(checkboxName, errorId, laravelErrorId) {
            var cb = document.querySelector('[name="' + checkboxName + '"]');
            if (!cb || !cb.checked) return;
            var el = document.getElementById(errorId);
            if (el) { el.classList.add('hidden'); var tt = el.querySelector('.tooltiptext'); if (tt) tt.classList.add('hidden'); }
            el = document.getElementById(laravelErrorId);
            if (el) el.classList.add('hidden');
        }
        hideErrorIfChecked('agree_terms', 'agree_terms-error', 'agree_terms-laravel-error');
        hideErrorIfChecked('firm_agree_terms', 'firm_agree_terms-error', 'firm_agree_terms-laravel-error');
        hideErrorIfChecked('pink_ride_agree_terms', 'pink_ride_agree_terms-error', 'pink_ride_agree_terms-laravel-error');
        hideErrorIfChecked('extra_care_ride_agree_terms', 'extra_care_ride_agree_terms-error', 'extra_care_ride_agree_terms-laravel-error');
    }

    var submitFormEl = document.getElementById('submitForm');
    if (submitFormEl) {
        submitFormEl.addEventListener('submit', function () {
            try { sessionStorage.removeItem(editBookingSeatsStorageKey); } catch (e) { /* ignore */ }
        });
    }

</script>
<script>
window.addEventListener("pageshow", function () {

    const navEntries = performance.getEntriesByType("navigation");

    if (navEntries.length > 0 && navEntries[0].type === "back_forward") {
        // User came using browser back button - redirect to my_trips to avoid showing stale edit form
        window.location.replace('{{ route("my_trips", ["lang" => $selectedLanguage->abbreviation ?? "en"]) }}');
    }

});
</script>

@endsection
