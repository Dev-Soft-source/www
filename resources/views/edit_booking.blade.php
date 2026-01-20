@extends('layouts.template')

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
            @if ($ride->booking_method->features_setting_id == $postRidePage->booking_option1)
                action="{{ route('update_instant_booking', $booking->id) }}"
            @elseif ($ride->booking_method->features_setting_id == $postRidePage->booking_option2)
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

        <h1>Edit booking</h1>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-y-4 md:gap-4">
            <div class="col-span-2">
                <div class="bg-white rounded-lg shadow-3xl">
                    <div class="flex flex-col md:flex-row justify-between px-4 pb-4 md:pb-0">
                        <div class="w-full md:w-2/3 order-2 md:order-1">
                            @php
                                $from = $booking->departure;
                                $to = $booking->destination;
                            @endphp
                            <div class="relative mt-5 text-left">
                                <div class="flex items-center relative">
                                    <div class="border-r-2 border-black border-solid absolute h-full left-3 md:left-6 top-2 z-10">
                                        <span class="bg-primary rounded-full w-7 h-7 -top-[2px] -ml-[13px] absolute flex justify-center items-center">
                                            <img class="w-5 h-5 object-contain" src="{{ asset('./images/new-21-search-bar-from.png')}}" alt="">
                                        </span>
                                    </div>
                                    <div class="ml-20">
                                        <div class="font-bold text-xl text-black">From</div>
                                        <div class="text-primary md:mb-4">{{ $booking->departure }}, <br class="md:hidden"> {{ $ride->pickup }}</div>
                                    </div>
                                </div>
                                <div class="flex items-center relative">
                                    <div class="border-r-2 border-black border-solid absolute h-0 left-3 md:left-5 top-2 z-10">
                                        <span class="bg-gray-200 rounded-full w-7 h-7 -top-[6px] -ml-[9px] absolute flex justify-center items-center">
                                            <img class="w-5 h-5 object-contain" src="{{ asset('./images/new-21-search-bar-to.png')}}" alt="">
                                        </span>
                                    </div>
                                    <div class="ml-20">
                                        <div class="font-bold text-xl text-black">To</div>
                                        <div class="text-primary md:mb-4">{{ $booking->destination }}, <br class="md:hidden"> {{ $ride->dropoff }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 order-1 md:order-2">
                            <p class="whitespace-nowrap font-medium">
                                {{ \Carbon\Carbon::parse($ride->date)->format('l, F j, Y') }} at {{ \Carbon\Carbon::parse($ride->time)->format('h:i A') == '12:00 PM' ? '12 noon' : (\Carbon\Carbon::parse($ride->time)->format('h:i A') == '12:00 AM' ? '12 midnight' : \Carbon\Carbon::parse($ride->time)->format('h:i A')) }}
                            </p>
                        </div>
                    </div>
                    <div class="border-t border-gray-300 flex flex-col md:flex-row md:items-center justify-start md:space-x-2 p-4">
                        <div>
                            <p class="font-medium text-left text-black mr-4">My co-passenger:</p>
                        </div>
                        <div class="flex items-center space-x-2 no-scrollbar overflow-x-auto mt-2 md:mt-0">
                        @foreach ($ride->bookings->where('status', '<>', 3)->where('status', '<>', 4) as $booking)
                            @for ($i = 0; $i < $booking->seats; $i++)
                                @if ($booking->passenger)
                                        @if ($booking->passenger->profile_image)
                                            <img class="w-10 h-10 rounded-full" src="{{ $booking->passenger->profile_image }}" alt="">
                                        @else
                                            <img class="w-10 h-10 rounded-full" src="{{ asset('images/59-booked-seat.png') }}" alt="">
                                        @endif
                                @endif
                            @endfor
                        @endforeach
                        </div>
                    </div>
                    <div class="border-t border-gray-300 grid sm:grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-gray-300">
                        <div class="p-4">
                            <p class="font-medium text-left text-gray-800">Payment method: <span class="text-black">{{ $ride->payment_method->name }}</span></p>
                        </div>
                        <div class="p-4">
                            <p class="font-medium text-left text-gray-800">{{ $rideDetailPage->luggage_label }} <span class="text-black">{{ $ride->luggage }}</span></p>
                        </div>
                    </div>
                    <div class="border-t border-gray-300 grid grid-cols-2 divide-x divide-gray-300">
                        <div class="p-4">
                            <p class="text-left font-medium">{{ intval($ride->seats) - intval($ride->bookings()->where('status', '<>', 3)->where('status', '<>', 4)->whereHas('passenger', function($query) { $query->whereNull('deleted_at'); })->sum('seats')) }} seats left </p>
                        </div>
                        <div class="p-4">
                            <p class="font-medium text-left text-primary">${{ $booking->price }} per seat</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg overflow-hidden shadow-3xl mt-4">
                    <div class="bg-primary text-white px-4 py-2">
                        <h3 class="text-2xl xl:text-3xl">Ride features</h3>
                    </div>
                    <div class="bg-white p-4 p space-y-4">
                        <div class="flex items-center space-x-2">
                            @if ($ride->smoke == $postRidePage->smoking_option1->features_setting_id)
                                @isset($postRidePage->smoking_option1->icon)
                                    <img class="w-7 h-7" src="{{asset('home_page_icons/' . $postRidePage->smoking_option1->icon)}}" alt="">
                                @endisset
                                <p>{{ $rideDetailPage->smoking_label }} {{ $postRidePage->smoking_option1->name }}</p>
                            @elseif ($ride->smoke == $postRidePage->smoking_option2->features_setting_id)
                                @isset($postRidePage->smoking_option2->icon)
                                    <img class="w-7 h-7" src="{{asset('home_page_icons/' . $postRidePage->smoking_option2->icon)}}" alt="">
                                @endisset
                                <p>{{ $rideDetailPage->smoking_label }} {{ $postRidePage->smoking_option2->name }}</p>
                            @endif
                        </div>
                        @isset ($ride->animal_friendly->features_setting_id)
                            <div class="flex items-center space-x-2">
                                <img class="w-7 h-7"
                                    @if ($ride->animal_friendly->features_setting_id === $postRidePage->animals_option1->features_setting_id) src="{{asset('home_page_icons/' . $postRidePage->animals_option1->icon)}}"
                                    @elseif ($ride->animal_friendly->features_setting_id === $postRidePage->animals_option2->features_setting_id) src="{{asset('home_page_icons/' . $postRidePage->animals_option2->icon)}}"
                                    @elseif ($ride->animal_friendly->features_setting_id === $postRidePage->animals_option3->features_setting_id)
                                        src="{{asset('home_page_icons/' . $postRidePage->animals_option3->icon)}}" @endif
                                    alt="">
                                <p>{{ $rideDetailPage->pets_label }} {{ $ride->animal_friendly->name }}</p>
                            </div>
                        @endisset
                        @php
                            $features = !empty($ride->features) ? explode('=', $ride->features) : [];
                        @endphp
                        @foreach ($features as $feature)
                            <div class="flex items-center space-x-2">
                                @if ($feature === $postRidePage->features_option11->name)
                                    <img class="w-7 h-7" src="{{asset('home_page_icons/' . $postRidePage->features_option11->icon)}}" alt="">
                                @elseif ($feature === $postRidePage->features_option1->name)
                                    <img class="w-7 h-7" src="{{asset('home_page_icons/' . $postRidePage->features_option1->icon)}}" alt="">
                                @elseif ($feature === $postRidePage->features_option2->name)
                                    <img class="w-7 h-7" src="{{asset('home_page_icons/' . $postRidePage->features_option2->icon)}}" alt="">
                                @elseif ($feature === $postRidePage->features_option9->name)
                                    <img class="w-7 h-7" src="{{asset('home_page_icons/' . $postRidePage->features_option9->icon)}}" alt="">
                                @elseif ($feature === $postRidePage->features_option8->name)
                                    <img class="w-7 h-7" src="{{asset('home_page_icons/' . $postRidePage->features_option8->icon)}}" alt="">
                                @elseif ($feature === $postRidePage->features_option10->name)
                                    <img class="w-7 h-7" src="{{asset('home_page_icons/' . $postRidePage->features_option10->icon)}}" alt="">
                                @elseif ($feature === $postRidePage->features_option3->name)
                                    <img class="w-7 h-7" src="{{asset('home_page_icons/' . $postRidePage->features_option3->icon)}}" alt="">
                                @elseif ($feature === $postRidePage->features_option12->name)
                                    <img class="w-7 h-7" src="{{asset('home_page_icons/' . $postRidePage->features_option12->icon)}}" alt="">
                                @elseif ($feature === $postRidePage->features_option4->name)
                                    <img class="w-7 h-7" src="{{asset('home_page_icons/' . $postRidePage->features_option4->icon)}}" alt="">
                                @elseif ($feature === $postRidePage->features_option5->name)
                                    <img class="w-7 h-7" src="{{asset('home_page_icons/' . $postRidePage->features_option5->icon)}}" alt="">
                                @elseif ($feature === $postRidePage->features_option6->name)
                                    <img class="w-7 h-7" src="{{asset('home_page_icons/' . $postRidePage->features_option6->icon)}}" alt="">
                                @elseif ($feature === $postRidePage->features_option7->name)
                                    <img class="w-7 h-7" src="{{asset('home_page_icons/' . $postRidePage->features_option7->icon)}}" alt="">
                                @elseif ($feature === $postRidePage->features_option13->name)
                                    <img class="w-7 h-7" src="{{asset('home_page_icons/' . $postRidePage->features_option13->icon)}}" alt="">
                                @elseif ($feature === $postRidePage->features_option14->name)
                                    <img class="w-7 h-7" src="{{asset('home_page_icons/' . $postRidePage->features_option14->icon)}}" alt="">
                                @elseif ($feature === $postRidePage->features_option15->name)
                                    <img class="w-7 h-7" src="{{asset('home_page_icons/' . $postRidePage->features_option15->icon)}}" alt="">
                                @elseif ($feature === $postRidePage->features_option16->name)
                                    <img class="w-7 h-7" src="{{asset('home_page_icons/' . $postRidePage->features_option16->icon)}}" alt="">
                                @else
                                    <input id="wi-fi" type="checkbox" name="features[]" value="" checked disabled
                                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                @endif
                                <p>{{ $feature }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
                {{-- <div class="mt-4 mb-5 rounded-lg px-6 py-3 bg-blue-100 text-gray-600" role="alert">
                    <p class="text-gray-800">Important note from the driver: <span class="text-gray-500">{{ $ride->notes }}</span></p>
                </div> --}}

            </div>
            <div class="col-span-1">
                <div class="space-y-4">
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
                    <div class="text-left text-red-500 text-lg">
                        <span class="text-red-500">*</span> {{ $bookingPage->required_fields ?? ""}}
                    </div>
                    <div class="bg-white rounded-lg overflow-hidden shadow-3xl">
                        <div class="bg-primary text-white px-4 py-2">
                            <h3 class="text-2xl xl:text-3xl">Booking summary</h3>
                        </div>
                        <div class="bg-white p-4">
                            <div class="flex items-center justify-between">
                                <p class="text-black"><span id="selectedSeats">1</span> seat</p>
                                <p class="totalSeatsAmount text-black"></p>
                                <input type="hidden" name="seats_amount" class="totalSeatsAmountInput form-control" readonly>
                            </div>

                            @if ($ride->booking_type == $postRidePage->cancellation_policy_label2->features_setting_id)
                                <div class="flex items-center justify-between">
                                    <p class="text-black">
                                        {{ $bookingPage->firm_cancellation_label_price_section ?? "Firm cancellation" }} {{$settingFirmDiscount}}%
                                    </p>
                                </div>

                                <div class="flex items-center justify-between">
                                    <p class="text-black">
                                        {{ $bookingPage->firm_discount_label_price_section ?? "Discount" }}
                                    </p>
                                    <p class="firmDiscountAmt text-black"></p>
                                </div>
                                <div class="flex items-center justify-between">
                                    <p class="text-black">
                                        {{ $bookingPage->firm_your_price_label_price_section ?? "Your price" }}
                                    </p>
                                    <p class="yourPriceAmt text-black"></p>
                                </div>
                            @endif

                            <div class="flex items-center justify-between">
                                <p class="text-black">Booking fee</p>
                                <p class="totalAmount text-black"></p>
                                <input type="hidden" name="booking_credit" class="totalAmountInput form-control" readonly>
                            </div>

                            @if (isset($booking->transaction[0]->deduct_type) && $booking->transaction[0]->deduct_type == "deduct_from_passenger")
                            @php
                                $settingTaxPercentage = $booking->transaction[0]->tax_percentage;
                            @endphp

                            <input type="hidden" value="{{$settingTaxPercentage}}" name="tax_percentage">
                            <input type="hidden" value="{{$setting->deduct_tax}}" name="deduct_tax">
                            <input type="hidden" value="{{$setting->tax_type}}" name="tax_type">

                                <div class="flex items-center justify-between mt-1">
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
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-1">
                                        <input type="checkbox" id="apply_coffee_wall" name="coffee_wall" value="1" class="form-control hidden peer">
                                        <label for="apply_coffee_wall" class="inline-flex items-center justify-center w-full px-2 py-0.5 text-primary bg-white border-2 border-primary rounded cursor-pointer peer-checked:bg-primary peer-checked:text-white">
                                            <span class="font-medium font-FuturaMdCnBT text-xl">
                                                Apply coffee wall
                                            </span>
                                        </label>
                                        <div class="sups relative inline-flex">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle-fill text-black peer" viewBox="0 0 16 16">
                                                <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                                            </svg>
                                            <div
                                              class="absolute tooltip payment_tooltiptext_position -top-20 sm:-top-16 right-32 lg:-top-28 xl:right-32 xl:-top-24 2xl:-top-24 group-hover:flex hidden peer-hover:flex"
                                            >
                                                <div
                                                    role="tooltip"
                                                    class="absolute after:left-[6.8rem] md:after:left-[6.8rem] payment_tooltiptext -left-1/2 -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-blue-500  border border-blue-500 text-gray-600 rounded tooltip_width sm:w-[25rem] md:w-[30rem] lg:w-72 xl:w-[23rem] 2xl:w-[25rem] px-4"
                                                >
                                                    <p class="text-white font-semibold text-start text-sm lg:text-base">
                                                        If you can afford the booking fee, please go ahead and pay it; and leave the “Coffee on the Wall” for those who need it more If you, honestly and truthfully, cannot afford it, help yourself to a “Coffee from the Wall”; it has been donated from kind-hearted people like you, for good people like you
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="showDeduction" class="hidden items-center space-x-1">
                                        <p class="text-black">-</p>
                                        <p class="totalAmount text-black"></p>
                                    </div>
                                </div>
                            @endif
                            @if ($ride->payment_method->features_setting_id == $postRidePage->payment_methods_option1->features_setting_id)
                                <div class="flex items-center justify-between">
                                    {{-- <p>Total online payment</p>
                                    <p class="totalAmount text-black"></p> --}}
                                    <input type="hidden" name="online_payment" class="totalAmountIn form-control" readonly>
                                </div>
                                <div class="flex items-center justify-between">
                                    {{-- <p>Total cash payment</p>
                                    <p class="totalSeatsAmount text-black"></p> --}}
                                </div>
                            @else
                                <div class="flex items-center justify-between">
                                    {{-- <p>Total online payment</p>
                                    <p class="totalSum text-black"></p> --}}
                                    <input type="hidden" name="online_payment" class="totalSumInput form-control" readonly>
                                </div>
                                <div class="flex items-center justify-between">
                                    {{-- <p>Total cash payment</p>
                                    <p class="text-black">$0.00</p> --}}
                                </div>
                            @endif
                            <div class="flex items-center justify-between">
                                <p>Total</p>
                                <div>
                                    <p class="totalSum text-right"></p>
                                    <span id="discount" class="text-right"></span>
                                </div>
                            </div>
                            <div class="border-t mt-4 pt-4">
                                {{-- @if ($ride->payment_method->features_setting_id == $postRidePage->payment_methods_option1->features_setting_id) --}}
                                    <div class="flex items-center justify-between">
                                        <p>Total paid online payment</p>
                                        <p class="totalPaidAmount text-black"></p>
                                    </div>
                                    {{-- <div class="refund items-center justify-between">
                                        <p>Total refund payment</p>
                                        <p class="totalPayableAmount text-black"></p>
                                    </div> --}}

                                    <div class="payable items-center justify-between">
                                        <p>
                                            @isset($bookingPage->payable_amount_label)
                                            {{ $bookingPage->payable_amount_label }}
                                            @endisset
                                        </p>
                                        <p class="totalPayableAmount text-black"></p>
                                    </div>
                                {{-- @endif --}}
                                @if ($ride->payment_method->features_setting_id == $postRidePage->payment_methods_option2)
                                    {{-- <div class="flex items-center justify-between">
                                        <p>Total paid online payment</p>
                                        <p class="totalPaidOnlineAmount text-black">${{ number_format(floatval($booking->fare+$booking->booking_credit), 2) }}</p>
                                    </div> --}}
                                    {{-- <div class="refund items-center justify-between">
                                        <p>Total refund payment</p>
                                        <p class="totalPayableOnlineAmount text-black"></p>
                                    </div> --}}
                                    
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg overflow-hidden shadow-3xl">
                        <div class="bg-primary text-white px-4 py-2">
                            <h3 class="text-2xl xl:text-3xl">Booking</h3>
                        </div>
                        <div class="bg-white p-4">
                            <div class="space-y-4 mb-4">

                                <div class="flex items-center justify-between">
                                    <div class="flex relative">
                                        <h3 class="text-primary text-2xl xl:text-3xl">
                                            @isset($bookingPage->seats_available_label)
                                                {{ $bookingPage->seats_available_label }}
                                            @endisset
                                        </h3>
                                        <div class="sups inline-flex">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle-fill text-black peer" viewBox="0 0 16 16">
                                                <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                                            </svg>
                                            <div
                                              class="absolute tooltip payment_tooltiptext_position top-8 left-0 group-hover:flex hidden peer-hover:flex"
                                            >
                                                <div
                                                    role="tooltip"
                                                    class="absolute after:left-[6.8rem] md:after:left-[6.8rem] payment_tooltiptext -left-1/2 -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-blue-500  border border-blue-500 text-gray-600 rounded tooltip_width sm:w-[25rem] md:w-[30rem] lg:w-72 xl:w-[23rem] 2xl:w-[25rem] px-4"
                                                >
                                                    <p class="text-white font-semibold text-start text-sm lg:text-base">
                                                        {{ $bookingPage->seats_available_info_text ?? 'seat avaialbe info text' }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- {{ $booking->seatDetail }} --}}
                                <div class="flex">
                                    @foreach ($booking->seatDetail as $detail)
                                        <div class="relative">
                                            <label for="number-of-seat-{{ $detail->id }}">
                                                <input id="number-of-seat-{{ $detail->id }}" name="seats_id[]" type="checkbox" value="{{ $detail->id }}" class="hidden" checked onchange="keepChecked(this)" data-parsley-required="true" data-parsley-trigger="blur focusout change" data-parsley-required-message="Please select the available seats." data-parsley-errors-container="#parsley-seats-error">
                                                <img src="{{ asset('assets/seat-hover-1.png') }}" class="w-10 h-10 mt-0.5 cursor-pointer seat-image seat-unselect-{{ $detail->id }}" alt="">
                                                <span class="absolute left-4 top-3 seat-number seat-number-{{ $detail->id }} {{ old('seats') == $detail->id ? 'text-green-300' : '' }}"></span>
                                            </label>
                                        </div>
                                    @endforeach
                                    @foreach ($ride->pendingSeatDetail as $detail)
                                        <div class="relative">
                                            <label for="number-of-seat-{{ $detail->id }}">
                                                <input id="number-of-seat-{{ $detail->id }}" name="seats_id[]" type="checkbox" value="{{ $detail->id }}" class="hidden" {{ in_array($detail->id, old('seats_id', [])) || ($detail->user_id == auth()->user()->id) ? 'checked' : '' }} onchange="seat_selected(this)" data-parsley-required="true" data-parsley-trigger="blur focusout change" data-parsley-required-message="Please select the available seats." data-parsley-errors-container="#parsley-seats-error">
                                                <img src="{{ in_array($detail->id, old('seats_id', [])) || ($detail->user_id == auth()->user()->id) ? asset('assets/seat-hover-1.png') : asset('assets/seat.png') }}" class="w-10 h-10 mt-0.5 cursor-pointer seat-image seat-unselect-{{ $detail->id }}" alt="">
                                                <span class="absolute left-4 top-3 seat-number seat-number-{{ $detail->id }} {{ old('seats') == $detail->id ? 'text-green-300' : '' }}"></span>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                <!-- Hidden input to store count -->
                                <input type="hidden" id="seat-count" name="seats" value="">
                                {{-- <div class="">
                                    <label for="seats" class="text-gray-900 font-medium text-lg lg:text-xl mb-2">Seats</label>
                                    <select id="type" name="seats"
                                        class="bg-gray-100 border-0 text-gray-500 rounded focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 mt-2 block w-full pr-8 p-2.5">
                                        @for ($i = $booking->seats; $i <= $ride->seats - $ride->bookings->where('status', '<>', 3)->where('status', '<>', 4)->where('user_id', '!=', auth()->user()->id)->sum('seats'); $i++)
                                            <option value="{{ $i }}" {{ old('seats', $booking->seats) == $i ? 'selected' : ($i == 1 ? 'selected' : '') }}>
                                                {{ $i }}
                                            </option>
                                        @endfor
                                    </select>
                                </div> --}}
                            </div>
                            

                            <ul class="">
                                <li><p class="text-left">{{ $bookingPage->booking_disclaimer_on_time ?? "I will show up at least ten minutes before the time of the ride. If I am late, the driver has the right to leave without me and I will not be refunded" }}</p></li>
                                <li><p class="text-left">{{ $bookingPage->booking_disclaimer_pink_ride ?? "I know that ProximaRide are exclusive to ProximaRide female members. If I am booking on a Pink Ride, I will not be accompanied by male members who are above 12 years of age, nor will I send a male member in my place. If I do, the driver will not take me or them, and I will not be refunded"}}</p></li>
                                <li><p class="text-left">{{ $bookingPage->booking_disclaimer_extra_care_ride ?? "I know that Extra-Care Rides are exclusive to members with highest review score. If I am booking on an Extra-Care Ride, I will adhere to its standards" }}</p></li>
                            </ul>

                            <div class="flex items-start my-4">
                                <input id="" type="checkbox" name="agree_terms" value="1"
                                    {{ old('agree_terms') == '1' ? 'checked' : '' }} onchange="getFirmAgreeTerms();"
                                    class="w-4 h-4 text-blue-600 cursor-pointer bg-white mt-1 border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                    <label for="" class="ml-2 font-normal text-gray-900">
                                        {{ $bookingPage->booking_term_agree_text ?? "I agree to these rules, and I have read, and agree to ProximaRide's terms and conditions. I also confirm that I am at least 18 years of age" }}
                                        <span class="text-red-500">*</span>
                                    </label>
                                </div>
                                @error('agree_terms')
                                <div class="relative tooltip -bottom-4 group-hover:flex">
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
                                            {{ isset($firmText) && $firmText != "" ? $firmText : "I know that this ride has the Firm cancellation policy which entitles me to a 10% discount of the booking price, and it is not refundable; regardless of the cancellation time" }}
                                            <span class="text-red-500">*</span>
                                        </label>
                                    </div>
                                    @error('firm_agree_terms')
                                    <div class="relative tooltip -bottom-4 group-hover:flex">
                                        <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                            <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                        </div>
                                    </div>
                                    @enderror

                                    <div id ="firm_agree_terms-error" class="relative tooltip -bottom-4 group-hover:flex">
                                        <div role="tooltip" class="hidden relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                            <p class="text-white leading-none text-sm lg:text-base"></p>
                                        </div>
                                    </div>

                                @endif

                                @if (in_array($postRidePage->features_option1->name, $features))
                                    <div class="flex items-start my-4">
                                        <input id="" type="checkbox" name="pink_ride_agree_terms" value="1"
                                            {{ old('pink_ride_agree_terms') == '1' ? 'checked' : '' }} onchange="getFirmAgreeTerms();"
                                            class="w-4 h-4 text-blue-600 cursor-pointer bg-white mt-1 border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                        <label for="" class="ml-2 font-normal text-gray-900">
                                            {{ $bookingPage->booking_pink_ride_term_agree_text ?? "I understand that I am booking on a Pink Ride, which is exclusive for female passengers and drivers. I will not send a male passenger in my place, or bring one along with me, who is above 12 years of age. I understand that, if I do, I will not be allowed in the ride, and my booking fee and booking price will not be refunded" }}
                                            <span class="text-red-500">*</span>
                                        </label>
                                    </div>
                                    @error('pink_ride_agree_terms')
                                        <div class="relative tooltip -bottom-4 group-hover:flex">
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

                                @if (in_array($postRidePage->features_option2->name, $features))
                                    <div class="flex items-start my-4">
                                        <input id="" type="checkbox" name="extra_care_ride_agree_terms" value="1"
                                            {{ old('extra_care_ride_agree_terms') == '1' ? 'checked' : '' }} onchange="getFirmAgreeTerms();"
                                            class="w-4 h-4 text-blue-600 cursor-pointer bg-white mt-1 border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                        <label for="" class="ml-2 font-normal text-gray-900">
                                            {{ $bookingPage->booking_extra_care_ride_term_agree_text ?? "I understand that I am booking on a Extra-care Ride" }}
                                            <span class="text-red-500">*</span>
                                        </label>
                                    </div>
                                    @error('extra_care_ride_agree_terms')
                                        <div class="relative tooltip -bottom-4 group-hover:flex">
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
                                                        {{ $bookingPage->paypal_label }}
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
                                                                {{ $bookingPage->credit_card_label }}
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
                                                      <div class="relative tooltip -bottom-4 group-hover:flex">
                                                        <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                                            <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                                        </div>
                                                      </div>
                                                    @enderror
                                                    <div class="flex justify-center items-center mt-4">
                                                        {{-- <a href="{{ route('my_cards.create', ['lang' => $selectedLanguage->abbreviation, 'rideDetailId' => $ride->rideDetail[0]->id, 'rideId' => $ride->rideDetail[0]->ride_id, 'type' => 'booking']) }}" class="button-exp-fill"> --}}
                                                        <button onclick="storeDataAndRedirect()" class="button-exp-fill">
                                                            @isset($bookingPage->add_card_label)
                                                                {{ $bookingPage->add_card_label }}
                                                            @endisset
                                                        </button>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                        @error('payment_method')
                                          <div class="relative tooltip -bottom-4 group-hover:flex">
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
                                        {{ $ride->booking_method->name }}
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
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity z-40" onclick="closeModal()"></div>
        <div class="fixed inset-0 z-50 w-screen overflow-y-auto modal-border">
            <button onclick="closeModal()" class="absolute top-2 right-2 p-1 rounded-full hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-400 ">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                <div
                    class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg w-full">
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start justify-center">
                            <!-- <div
                                class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full sm:mx-0 sm:h-10 sm:w-10 bg-blue-500">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-lg text-white w-8 h-8" viewBox="0 0 16 16">
                                    <path d="M7.005 3.1a1 1 0 1 1 1.99 0l-.388 6.35a.61.61 0 0 1-1.214 0zM7 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0"/>
                                </svg>
                            </div> -->
                        </div>
                        <div class="text-center sm:ml-4 sm:mt-0 sm:text-left">
                            <div class="">
                                <h3 class="text-3xl text-center font-FuturaMdCnBT font-medium text-gray-900 mb-4" id="modal-title">{!! session('heading') !!}</h3>
                            </div>
                            <div class="mt-2 w-full">
                                <p class="text-sm text-center text-gray-500"></p>
                            </div>
                        </div>
                    </div>
                    <div class="px-4 pb-6 pt-4  sm:flex sm:flex-row-reverse sm:px-6 justify-center">
                        <button type="button" id="close-modal" class="inline-flex w-full justify-center rounded bg-blue-500 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-blue-400 sm:ml-3 sm:w-24">
                            Close
                        </button>
                    </div>
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
        country: 'US',
        currency: 'usd',
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

        const amount = document.querySelector('[name="online_payment"]').value;
        
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

    $(document).ready(function () {
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

    if (@json($booking->price) <= 15) {
        // Set a default value if $setting is null or not defined
        bookingPrice = 0.0;
    } else if (@json($booking->price) <= 30) {
        bookingPrice = parseFloat((10 / 100) * @json($booking->price));
    } else {
        if (settingBookingPrice && settingBookingPrice !== '') {
            // Get the booking price from $setting
            bookingPrice = parseFloat(settingBookingPrice);
        } else {
            // Set a default value if $setting is null or not defined
            bookingPrice = 0.0;
        }
    }
    // Function to update the total amount
    function updateTotalAmount() {
        var seatPrice = parseFloat({{ $booking->price }});
        var selectedSeats = $("input[name='seats_id[]']:checked").length;
        var totalAmount = bookingPrice * selectedSeats;
        var totalBookingCredit = "{{ $booking->booking_credit }}";
        var totalFare = "{{ $booking->fare }}";
        var bookedSeats = "{{ $booking->seats }}";
        var totaltaxAmount = "{{ $booking->tax_amount }}";
        var totalPaid = Number(totalFare) + Number(totalBookingCredit) + Number(totaltaxAmount);
        var totalSeatsAmount = seatPrice * selectedSeats;

        const seatCountInput = document.getElementById('seat-count');
        // Update the hidden field's value
        seatCountInput.value = selectedSeats;

        $('#discount').text('');

        var firm = "{{ $firm }}";

        var totalRideSeatAmout = totalSeatsAmount;
        if ($('input[name="type"]:checked').val() === firm) {
            var settingFirmDiscount = "{{ $settingFirmDiscount }}";
            if (settingFirmDiscount && settingFirmDiscount !== '') {
                // Get the booking price from $setting
                totalSeatsAmount = totalSeatsAmount - (totalSeatsAmount * settingFirmDiscount / 100);
                var firmAmt = (totalSeatsAmount * settingFirmDiscount / 100);
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
    function seat_selected(th) {
        // Get the count of selected seats
        var selectedSeats = $("input[name='seats_id[]']:checked").length;
        console.log('Number of selected seats:', selectedSeats);

        var seat = $(th).val();
        var clickedIndex = $("input[name='seats_id[]']").index(th);

        // Change the image source for selected seats
        // if ($(th).is(':checked')) {
        //     // Change the image source for selected seats
        //     $(".seat-image.seat-unselect-" + seat).attr('src', '{{ asset("assets/seat-hover-1.png") }}');
        //     $(".seat-number.seat-number-" + seat).addClass('text-green-300');
        // } else {
        //     // Revert the image source for unselected seats
        //     $(".seat-image.seat-unselect-" + seat).attr('src', '{{ asset("assets/seat.png") }}');
        //     $(".seat-number.seat-number-" + seat).removeClass('text-green-300');
        // }
    $("input[name='seats_id[]']").each(function(index) {

        var seatValue = $(this).val();

        // if (index <= clickedIndex) {
        //     // Select this seat (checked)
        //     $(this).prop('checked', true);

        //     // Change the image source for selected seats
        //     $(".seat-image.seat-unselect-" + seatValue).attr('src', '{{ asset("assets/seat-hover-1.png") }}');
        //     $(".seat-number.seat-number-" + seatValue).addClass('text-green-300');
        // } else {
        //     // Unselect this seat (unchecked)
        //     $(this).prop('checked', false);

        //     // Revert the image source for unselected seats
        //     $(".seat-image.seat-unselect-" + seatValue).attr('src', '{{ asset("assets/seat.png") }}');
        //     $(".seat-number.seat-number-" + seatValue).removeClass('text-green-300');
        // }
        if ($(th).is(':checked')) {
            // Change the image source for selected seats
            $(".seat-image.seat-unselect-" + seat).attr('src', '{{ asset("assets/seat-hover-1.png") }}');
            $(".seat-number.seat-number-" + seat).addClass('text-green-300');
        } else {
            // Revert the image source for unselected seats
            $(".seat-image.seat-unselect-" + seat).attr('src', '{{ asset("assets/seat.png") }}');
            $(".seat-number.seat-number-" + seat).removeClass('text-green-300');
        }
    });
    updateTotalAmount();

        $.ajax({
            url: '{{ route("seat_on_hold") }}', // Laravel route for the seat_on_hold
            type: 'POST',
            data: {
                seat_id: seat,
                _token: '{{ csrf_token() }}' // CSRF token for security
            },
            success: function(response) {
                console.log('Seats on hold:', response);

                // Update the modal content with the response message
                //const modalMessageElement = document.querySelector('#bookingModal .text-sm.text-gray-500');
                //if (modalMessageElement) {
                  //  modalMessageElement.textContent = response.message; // Assuming 'message' is part of the response
                //}
                //const modal = document.getElementById('bookingModal');
                //modal.classList.remove('hidden');
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                // Handle error response
            }
        });
    }

    function keepChecked(checkbox) {
        // Ensure the checkbox stays checked
        if (!checkbox.checked) {
            checkbox.checked = true;
        }
    }

    document.getElementById('close-modal').addEventListener('click', function () {
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


    function getFirmAgreeTerms() {
        updateTotalAmount();
    }


</script>




@endsection
