@extends('layouts.template')

@section('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endsection

@section('content')

<div class="grid grid-cols-12 gap-4 md:container md:mx-auto  my-6 md:my-10 xl:my-14 px-4 xl:px-0">
    @include('layouts.inc.profile_sidebar')

    <div class="bg-white rounded pt-0 lg:px-4 w-full col-span-12 lg:col-span-9">
        <div class="flex flex-wrap" id="tabs-id">
            <div class="w-full">
                <ul class="flex mb-0 list-none flex-wrap pb-4 flex-row gap-2">
                    <li class="flex-auto text-center">
                        <a href="{{ route('passenger_wallet_rides', ['lang' => $selectedLanguage->abbreviation]) }}" class="text-2xl font-FuturaMdCnBT px-5 py-2 shadow-lg rounded block border-blue-600 border leading-normal text-white bg-blue-600 cursor-pointer">
                            {{ $walletSettingPage->passenger_heading ?? "As a passenger"}}
                        </a>
                    </li>
                    <li class="flex-auto text-center">
                        <a href="{{ route('driver_wallet_available', ['lang' => $selectedLanguage->abbreviation]) }}" class="text-2xl font-FuturaMdCnBT px-5 py-2 shadow-lg rounded block border-gray-100 border leading-normal text-blue-600 bg-white cursor-pointer">
                            {{ $walletSettingPage->driver_heading ?? "As a driver"}}
                        </a>
                    </li>
                </ul>
                <ul class="flex mb-0 list-none flex-wrap pt-3 pb-4 flex-row gap-2">
                    <li class="flex-auto text-center">
                        <a href="{{ route('passenger_wallet_rides', ['lang' => $selectedLanguage->abbreviation]) }}" class="text-lg font-FuturaMdCnBT font-medium px-5 py-2 shadow-lg rounded block border-greenXS border leading-normal text-white bg-greenXS cursor-pointer">
                            {{ $walletSettingPage->passenger_my_ride_heading ?? "My trip"}}
                        </a>
                    </li>
                    <li class="flex-auto text-center">
                        <a href="{{ route('get_top_up_balance', ['lang' => $selectedLanguage->abbreviation]) }}" class="text-lg font-FuturaMdCnBT font-medium px-5 py-2 shadow-lg rounded block border-gray-100 border leading-normal text-blue-600 bg-white cursor-pointer">
                            {{ $walletSettingPage->balance_heading ?? "My balance"}}
                        </a>
                    </li>
                    <li class="flex-auto text-center">
                        <a href="{{ route('passenger_wallet_rewards', ['lang' => $selectedLanguage->abbreviation]) }}" class="text-lg font-FuturaMdCnBT font-medium px-5 py-2 shadow-lg rounded block border-gray-100 border leading-normal text-blue-600 bg-white cursor-pointer">
                            {{ $walletSettingPage->passenger_my_reward_heading ?? "My reward"}}
                        </a>
                    </li>
                </ul>
                <div class="relative flex flex-col min-w-0 break-words py-5 bg-white w-full shadow-lg rounded">
                    <div class="max-h-[48.8rem] overflow-y-auto">
                    <div class="px-4 flex-auto">
                        <div class="tab-content tab-space">
                            <div class="block" id="tab-profile">
                                <div class="space-y-4">
                                    @if (!empty($myRides) && count($myRides) > 0)
                                        @foreach ($myRides as $key => $booking)
                                            <div class="relative">
                                                <a href="">
                                                    <div class="{{ $key % 2 != 0 ? "bg-gray-100" : "bg-white" }}  rounded-lg shadow-3xl border-[3px] border-solid  border-gray-100 " id="ride-29">
                                                        <div class="border-gray-300 flex items-center justify-between space-x-2 p-4">
                                                            <p>{{ $walletSettingPage->passenger_ride_id_label ?? "Ride ID"}}</p>
                                                            <p>{{ $booking->ride->random_id }}</p>
                                                        </div>
                                                        <div class="border-t border-gray-300 flex items-center justify-between space-x-2 p-4">
                                                            <p>{{ $walletSettingPage->passenger_my_ride_from_label ?? "From"}}</p>
                                                            <p>{{ $booking->destination }}</p>
                                                        </div>
                                                        <div class="border-t border-gray-300 flex items-center justify-between space-x-2 p-4">
                                                            <p>{{ $walletSettingPage->passenger_my_ride_to_label ?? "To"}}</p>
                                                            <p>{{ $booking->departure }}</p>
                                                        </div>
                                                        <div class="border-t border-gray-300 flex items-center justify-between space-x-2 p-4">
                                                            <p>{{ $walletSettingPage->passenger_my_ride_from_label ?? "Date"}}</p>
                                                            <p>{{ $booking->ride->date }}</p>
                                                        </div>
                                                        <div class="border-t border-gray-300 flex items-center justify-between space-x-2 p-4">
                                                            <p>{{ $walletSettingPage->passenger_my_ride_booking_fee_label ?? "Booking fee"}}</p>
                                                            <p>{{ number_format($booking->booking_credit, 2) }}</p>
                                                        </div>
                                                        <div class="border-t border-gray-300 flex items-center justify-between space-x-2 p-4">
                                                            <p>{{ $walletSettingPage->passenger_my_ride_fare_label ?? "Fare"}}</p>
                                                            <p>{{ $booking->fare }}</p>
                                                        </div>
                                                        <div class="border-t border-gray-300 flex items-center justify-between space-x-2 p-4">
                                                            <p>{{ $walletSettingPage->passenger_my_ride_total_amount_label ?? "Total amount"}}</p>
                                                            <p>{{ $booking->booking_credit + $booking->fare + $booking->tax_amount }}</p>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                        @endforeach
                                    @else
                                        <p>{{ $walletSettingPage->no_my_ride_message ?? "No rides found"}}</p>
                                    @endif
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

@endsection
