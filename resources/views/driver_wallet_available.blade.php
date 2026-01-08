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
                            {{ $walletSettingPage->passenger_heading ?? "As a passenger"}}
                        </a>
                    </li>
                    <li class="flex-auto text-center">
                        <a href="{{ route('driver_wallet_pending', ['lang' => $selectedLanguage->abbreviation]) }}" class="text-2xl font-FuturaMdCnBT px-5 py-2 shadow-lg rounded block border-blue-600 border leading-normal text-white bg-blue-600 cursor-pointer">
                            {{ $walletSettingPage->driver_heading ?? "As a driver"}}
                        </a>
                    </li>
                </ul>
                <ul class="flex mb-0 list-none flex-wrap pt-3 pb-4 flex-row gap-2">
                    <li class="flex-auto text-center">
                        <a href="{{ route('driver_wallet_paid', ['lang' => $selectedLanguage->abbreviation]) }}" class="text-lg font-FuturaMdCnBT font-medium px-5 py-2 shadow-lg rounded block border-gray-100 border leading-normal text-blue-600 bg-white cursor-pointer">
                            {{ $walletSettingPage->driver_paid_out_heading ?? "Paid out"}}
                        </a>
                    </li>
                    <li class="flex-auto text-center">
                        <a href="{{ route('driver_wallet_available', ['lang' => $selectedLanguage->abbreviation]) }}" class="text-lg font-FuturaMdCnBT font-medium px-5 py-2 shadow-lg rounded block border-blue-600 border leading-normal text-white bg-blue-600 cursor-pointer">
                            {{ $walletSettingPage->driver_availabe_heading ?? "Available"}}
                        </a>
                    </li>
                    <li class="flex-auto text-center">
                        <a href="{{ route('driver_wallet_pending', ['lang' => $selectedLanguage->abbreviation]) }}" class="text-lg font-FuturaMdCnBT font-medium px-5 py-2 shadow-lg rounded block border-gray-100 border leading-normal text-blue-600 bg-white cursor-pointer">
                            {{ $walletSettingPage->driver_pending_heading ?? "Pending"}}
                        </a>
                    </li>
                    <li class="flex-auto text-center">
                        <a href="{{ route('driver_wallet_reward', ['lang' => $selectedLanguage->abbreviation]) }}" class="text-lg font-FuturaMdCnBT font-medium px-5 py-2 shadow-lg rounded block border-gray-100 border leading-normal text-blue-600 bg-white cursor-pointer">
                            {{ $walletSettingPage->driver_reward_heading ?? "Reward"}}
                        </a>
                    </li>
                </ul>
                <div class="relative flex flex-col min-w-0 py-5 break-words bg-white w-full mb-6 shadow-lg rounded">
                    <div class="">
                        <div class="px-4 flex-auto">
                            <div class="tab-content tab-space">
                                <div class="block" id="tab-profile">
                                    <div class="space-y-4">
                                        @if (session('message'))
                                          
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
                                                                        <p class="can-exp-p text-center">{{ session('message') }}</p>
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
                                        @if (!empty($getAvailableBalance) && count($getAvailableBalance) > 0)
                                            @foreach ($getAvailableBalance as $key => $balance)
                                                <div class="relative">
                                                    <a href="{{ route('ride_fair_details', ['lang' => $selectedLanguage->abbreviation, 'id' => $balance->ride_id]) }}">
                                                        <div class="{{ $key % 2 != 0 ? "bg-gray-100" : "bg-white" }} rounded-lg shadow-3xl border-[3px] border-solid  border-gray-100 " id="ride-29">
                                                            @if ($balance->status == 'request')
                                                                <div class="flex items-center justify-end w-full gap-2">
                                                                    <div class="w-fit px-2 py-1 rounded bg-green-100 text-sm text-green-600">
                                                                        In processing
                                                                    </div>
                                                                </div>
                                                            @endif
                                                            <div class="border-gray-300 flex items-center justify-between space-x-2 p-4">
                                                                <p>{{ $walletSettingPage->driver_available_ride_id_label ?? "Ride ID"}}</p>
                                                                <p>{{ $balance->ride->random_id }}</p>
                                                            </div>
                                                            <div class="border-t border-gray-300 flex items-center justify-between space-x-2 p-4">
                                                                <p>{{ $walletSettingPage->driver_available_from_label ?? "From"}}</p>
                                                                <p>{{ $balance->ride->defaultRideDetail[0]->departure }}</p>
                                                            </div>
                                                            <div class="border-t border-gray-300 flex items-center justify-between space-x-2 p-4">
                                                                <p>{{ $walletSettingPage->driver_available_to_label ?? "To"}}</p>
                                                                <p>{{ $balance->ride->defaultRideDetail[0]->destination }}</p>
                                                            </div>
                                                            <div class="border-t border-gray-300 flex items-center justify-between space-x-2 p-4">
                                                                <p>{{ $walletSettingPage->driver_available_date_label ?? "Date"}}</p>
                                                                <p>{{ $balance->ride->completed_date }}</p>
                                                            </div>
                                                            <div class="border-t border-gray-300 flex items-center justify-between space-x-2 p-4">
                                                                <p>{{ $walletSettingPage->driver_available_total_amount_label ?? "Total amount"}}</p>
                                                                <p>{{ $balance->total_payout_cost }}</p>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                            @endforeach
                                            <div class="flex items-center justify-center">
                                                <button class="button-exp-fill" type="button" onclick="toggleModalCard('card-modal')">
                                                    {{ $walletSettingPage->request_transfer_label ?? "Request for transfer"}}
                                                </button>
                                            </div>

                                            <div class="hidden overflow-x-hidden overflow-y-auto fixed inset-0 z-50 outline-none focus:outline-none justify-center items-center" id="card-modal">
                                                <div class="relative h-screen my-6 mx-auto flex items-center justify-center w-full">
                                                    <!--content-->
                                                    <div
                                                    class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg w-full">
                                                    <button onclick="toggleModalCard('card-modal')" class="absolute top-2 right-2 p-1 rounded-full hover:bg-gray-100">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    </button>
                                                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                                                            <div class="sm:flex sm:items-start justify-center">
                                                            </div>
                                                            <div class="text-center sm:ml-4 sm:mt-0 sm:text-left">
                                                                {{-- <div class="">
                                                                    <h3 class="text-3xl text-center font-FuturaMdCnBT font-medium text-gray-900 mb-4" id="modal-title">{!! session('heading') !!}</h3>
                                                                </div> --}}
                                                                <div class="mt-2 w-full">
                                                                    <p class="text-lg text-center text-black">{{ $walletSettingPage->withdraw_message ?? "Are you sure you want to withdraw?"}}</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="px-4 pb-6 pt-4 flex items-center space-x-2 sm:space-x-4 sm:px-6 justify-center">
                                                            <a href="{{ route('send_payout_request') }}"
                                                                class="button-exp-fill">{{ $messages->popup_submit_btn_text ?? "Yes" }}</a>
                                                            <button type="button" onclick="toggleModalCard('card-modal')"
                                                                class="button-exp-fill sm:w-24">{{ $messages->popup_close_btn_text ?? "No" }}</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="hidden opacity-25 fixed inset-0 z-40 bg-black" id="card-modal-backdrop"></div>
                                        @else
                                            <p>{{ $walletSettingPage->no_balance_found_message ?? "No balance available found"}}</p>
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

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<script>
    function toggleModalCard(modalID) {
        const modal = document.getElementById(modalID);
        const backdrop = document.getElementById(modalID + '-backdrop');

        modal.classList.toggle('hidden');
        backdrop.classList.toggle('hidden');
        modal.classList.toggle('flex');
        backdrop.classList.toggle('flex');
    }

    function closeModal() {
    const modal = document.getElementById('myModal');
    if (modal) {
        modal.classList.add('hidden');
    }
}
</script>

@endsection
