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
                        <a href="{{ route('passenger_wallet_rides', ['lang' => $selectedLanguage->abbreviation]) }}" class="text-2xl font-FuturaMdCnBT px-5 py-2 shadow-lg rounded block border-gray-100 border leading-normal text-blue-600 bg-white cursor-pointer">
                            {{ $walletSettingPage->passenger_heading ?? "I'm a Passenger"}}
                        </a>
                    </li>
                    <li class="flex-auto text-center">
                        <a href="{{ route('driver_wallet_pending', ['lang' => $selectedLanguage->abbreviation]) }}" class="text-2xl font-FuturaMdCnBT px-5 py-2 shadow-lg rounded block border-blue-600 border leading-normal text-white bg-blue-600 cursor-pointer">
                            {{ $walletSettingPage->driver_heading ?? "I'm a Driver"}}
                        </a>
                    </li>
                </ul>
                <ul class="flex mb-0 list-none flex-wrap pt-3 pb-4 flex-row gap-2">
                    
                    <li class="flex-auto text-center">
                        <a href="{{ route('driver_wallet_available', ['lang' => $selectedLanguage->abbreviation]) }}" class="text-lg font-FuturaMdCnBT font-medium px-5 py-2 shadow-lg rounded block border-gray-100 border leading-normal text-blue-600 bg-white cursor-pointer">
                            {{ $walletSettingPage->driver_availabe_heading ?? "Available"}}
                        </a>
                    </li>
                    <li class="flex-auto text-center 123">
                        <a href="{{ route('driver_wallet_paid', ['lang' => $selectedLanguage->abbreviation]) }}" class="text-lg font-FuturaMdCnBT font-medium px-5 py-2 shadow-lg rounded block border-gray-100 border leading-normal text-blue-600 bg-white cursor-pointer">
                            {{ $walletSettingPage->driver_paid_out_heading ?? "Paid out"}}
                        </a>
                    </li>                    
                    <li class="flex-auto text-center">
                        <a href="{{ route('driver_wallet_pending', ['lang' => $selectedLanguage->abbreviation]) }}" class="text-lg font-FuturaMdCnBT font-medium px-5 py-2 shadow-lg rounded block border-gray-100 border leading-normal text-blue-600 bg-white cursor-pointer">
                            {{ $walletSettingPage->driver_pending_heading ?? "Pending"}}
                        </a>
                    </li>
                    <li class="flex-auto text-center">
                        <a href="{{ route('driver_wallet_reward', ['lang' => $selectedLanguage->abbreviation]) }}" class="text-lg font-FuturaMdCnBT font-medium px-5 py-2 shadow-lg rounded block border-greenXS border leading-normal text-white bg-greenXS cursor-pointer">
                            {{ $walletSettingPage->driver_reward_heading ?? "Reward"}}
                        </a>
                    </li>
                </ul>
                <div class="relative flex flex-col min-w-0 break-words py-5 bg-white w-full shadow-lg rounded">
                    <div class="max-h-[48.8rem] overflow-y-auto">
                    <div class="px-4 flex-auto">
                        <div class="tab-content tab-space">
                            <div class="block" id="tab-profile">
                                <div class="space-y-4">
                                    <p>You have {{ $driverTotalRewardPoint }} points as driver</p>
                                    <div class="relative">
                                        <div class="bg-white rounded-lg shadow-3xl border-[3px] border-solid  border-gray-100 " id="ride-29">
                                            <div class="border-gray-300 flex items-center justify-between space-x-2 p-4">
                                                <p class="text-blue-600 font-FuturaMdCnBT text-2xl">Rewards</p>
                                                <p class="text-blue-600 font-FuturaMdCnBT text-2xl">Points</p>                                                
                                            </div>
                                            @if (!empty($rewardPointSettings) && count($rewardPointSettings) > 0)
                                                @foreach ($rewardPointSettings as $rewardPointSetting)
                                                    <div class="border-t border-gray-300 flex items-center justify-between space-x-2 p-4">
                                                        <p>{{ $rewardPointSetting->reward_name }}</p>
                                                        <p>{{ $rewardPointSetting->rewardPointSetting->point }}</p>                                                        
                                                    </div>
                                                @endforeach
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
    </div>
</div>

@endsection
