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
                            {{ $walletSettingPage->passenger_heading ?? "I'm a passenger"}}
                        </a>
                    </li>
                    <li class="flex-auto text-center">
                        <a href="{{ route('driver_wallet_pending', ['lang' => $selectedLanguage->abbreviation]) }}" class="text-2xl font-FuturaMdCnBT px-5 py-2 shadow-lg rounded block border-gray-100 border leading-normal text-blue-600 bg-white cursor-pointer">
                            {{ $walletSettingPage->driver_heading ?? "I'm a driver"}}
                        </a>
                    </li>
                </ul>
                <ul class="flex mb-0 list-none flex-wrap pt-3 pb-4 flex-row gap-2">
                    <li class="flex-auto text-center">
                        <a href="{{ route('passenger_wallet_rides', ['lang' => $selectedLanguage->abbreviation]) }}" class="text-lg font-FuturaMdCnBT font-medium px-5 py-2 shadow-lg rounded block border-gray-100 border leading-normal text-blue-600 bg-white cursor-pointer">
                            {{ $walletSettingPage->passenger_my_ride_heading ?? "My trip"}}
                        </a>
                    </li>
                    <li class="flex-auto text-center">
                        <a href="{{ route('get_top_up_balance', ['lang' => $selectedLanguage->abbreviation]) }}" class="text-lg font-FuturaMdCnBT font-medium px-5 py-2 shadow-lg rounded block border-blue-600 border leading-normal text-white bg-blue-600 cursor-pointer">
                            {{ $walletSettingPage->balance_heading ?? "My balance"}}
                        </a>
                    </li>
                    <li class="flex-auto text-center">
                        <a href="{{ route('passenger_wallet_rewards', ['lang' => $selectedLanguage->abbreviation]) }}" class="text-lg font-FuturaMdCnBT font-medium px-5 py-2 shadow-lg rounded block border-gray-100 border leading-normal text-blue-600 bg-white cursor-pointer">
                            {{ $walletSettingPage->passenger_my_reward_heading ?? "My reward"}}
                        </a>
                    </li>
                </ul>
                <div class="flex items-center justify-between">
                    <p class="text-primary font-semibold">Total Balance: ${{ $balance }} CAD</p>
                    <a href="{{ route('create_top_up_balance', ['lang' => $selectedLanguage->abbreviation]) }}"
                        class="button-exp-fill">
                        {{ $walletSettingPage->balance_buy_more_button_text ?? "Buy more top up balance"}}
                    </a>
                </div>
                <div class=" mt-2 relative flex flex-col min-w-0 break-words py-5 bg-white w-full shadow-lg rounded">
                    <div class="max-h-[49.9rem] overflow-y-auto">
                    <div class="px-4 flex-auto">
                        <div class="tab-content tab-space">
                            <div class="block" id="tab-profile">
                                <div class="space-y-4">
                                    @if(session('error'))
                                        <div id="myModal" class="relative z-50" id="delete_message_confirmation" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                            <div onclick="closeModal()"  class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                                            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                                                <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                                                    <div class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                                                        <button type="button" onclick="closeModal()"  class="absolute top-3 right-3 text-gray-400 hover:text-gray-500">
                                                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
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
                                                                    <p class="can-exp-p text-center"> {{ session('error') }}</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="px-4 pb-6 pt-4 flex items-center space-x-2 sm:space-x-4 sm:px-6 justify-center">
                                                        <input type="hidden" id="notificationId" value="3094">
                                                            <a href="#" onclick="closeModal()"  class="button-exp-fill">Close </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if (!empty($topUpBalances) && count($topUpBalances) > 0)
                                        @foreach ($topUpBalances as $key => $topUpBalance)
                                            @if ($topUpBalance->dr_amount || $topUpBalance->cr_amount > 0)
                                                
                                                <div class="relative">
                                                    <a href="">
                                                        <div class="{{ $key % 2 != 0 ? "bg-gray-100" : "bg-white" }} rounded-lg shadow-3xl border-[3px] border-solid  border-gray-100 " id="ride-29">
                                                            <div class="border-gray-300 flex items-center justify-between space-x-2 p-4">
                                                                <p>
                                                                    @if (isset($topUpBalance->dr_amount) && $topUpBalance->dr_amount != 0.0 && !isset($topUpBalance->booking_id))
                                                                    Top-up transaction 
                                                                    @endif

                                                                    @if (isset($topUpBalance->dr_amount) && $topUpBalance->dr_amount != 0.0 && isset($topUpBalance->booking_id))
                                                                    Refund transaction
                                                                    @endif

                                                                    @if (isset($topUpBalance->cr_amount) && $topUpBalance->cr_amount != 0.0)
                                                                    Payment transaction 
                                                                    @endif
                                                                    
                                                                    {{ $walletSettingPage->balance_id_label ?? "ID"}}</p>
                                                                <p>{{ $topUpBalance->random_id }}</p>
                                                            </div>
                                                            <div class="border-t border-gray-300 flex items-center justify-between space-x-2 p-4">
                                                                <p>{{ $walletSettingPage->balance_amount_label ?? "Amount"}}</p>
                                                                @if ($topUpBalance->dr_amount)
                                                                    <p class="text-green-500">+{{ $topUpBalance->dr_amount }}</p>
                                                                @else
                                                                    <p class="text-red-500">-{{ $topUpBalance->cr_amount }}</p>
                                                                @endif
                                                            </div>
                                                            <div class="border-t border-gray-300 flex items-center justify-between space-x-2 p-4">
                                                                <p>{{ $walletSettingPage->balance_date_label ?? "Date"}}</p>
                                                                <p>{{ $topUpBalance->added_date }}</p>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                            @endif
                                        @endforeach
                                    @else
                                        {{-- <p>{{ $walletSettingPage->no_balance_found_message ?? "No balance found"}}</p> --}}
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

@section('script')
<script>
    function closeModal() {
        const modal = document.getElementById('myModal');
        if (modal) {
            modal.classList.add('hidden');
        }
    }
</script>
@endsection