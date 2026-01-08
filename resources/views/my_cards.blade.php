@extends('layouts.template')

@section('content')

<div class="grid grid-cols-12 gap-4 md:container md:mx-auto  my-6 md:my-10 xl:my-14 px-4 xl:px-0">
    @include('layouts.inc.profile_sidebar')

    <div class="bg-white border rounded p-4 border-gray-200 w-full col-span-12 lg:col-span-9 shadow">
        @if(session('message'))

            <div id="myModal" class="relative z-50" id="delete_message_confirmation" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div onclick="closeModal()" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                    <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                        <div class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                            <button type="button" onclick="closeModal()" class="absolute top-3 right-3 text-gray-400 hover:text-gray-500">
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
                            <input type="hidden" id="notificationId" value="3089">
                                <a href="#" onclick="closeNotificationModal()" class="button-exp-fill">Close </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="mb-4 pb-2 flex justify-between items-center">
            <h1 class="mb-0">{{$paymentSettingDetail->main_heading ?? "Payment options"}}</h1>
            <a href="{{ route('my_cards.create', ['lang' => $selectedLanguage->abbreviation]) }}"
                class="button-exp-fill">{{$paymentSettingDetail->add_new_card_button_text ?? "Add new card"}}</a>
        </div>

        <div class="max-h-[52rem] overflow-y-auto pr-2">

            @if (!empty($cards) && count($cards) > 0)
                @foreach ($cards as $card)
                    @if ($card->paymentMethod)
                        <div class="even:bg-gray-100 odd:bg-white rounded border border-gray-100 shadow-md p-3 md:p-6 mt-3">
                            <div>
                                <div class='flex items-center justify-between space-x-4'>
                                    <div class="flex items-center space-x-4">
                                        <div>
                                            <h6 class='card-title leading-7 m-0 capitalize'>
                                                {{ $card->paymentMethod->card->brand }}
                                            </h6>
                                            <p class="text-gray-900">
                                                **** **** **** {{ $card->paymentMethod->card->last4 }}
                                            </p>
                                            <p id="exp_date_{{ $card->id }}"></p>
                                        </div>
                                    </div>
                                    <div class="flex flex-col lg:flex-row justify-center mt-4 md:mt-0 md:justify-end items-center gap-2">
                                        @if ($card->primary_card === '0')
                                            <a href="{{ route('my_cards.set_primary', $card->id) }}" class="button-exp-fill whitespace-nowrap max-w-32 w-full block">{{$paymentSettingDetail->set_primary_card_label ?? "Set as primary"}}</a>
                                        @endif
                                        @if ($card->primary_card !== '0')
                                            <a class="bg-green-100 text-green-600 border-green-100 button-exp-fill whitespace-nowrap max-w-32 w-full block">{{$paymentSettingDetail->mobile_default_card_tab ?? "Default card"}}</a>
                                        @endif
                                        <button type="button" onclick="toggleModalCard('card-modal', {{ $card->id }})"
                                            class="button-exp-fill whitespace-nowrap max-w-32 w-full block">
                                            {{$paymentSettingDetail->delete_card_button_text ?? "Delete card"}}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            @endif
        </div>
    </div>
</div>

<div class="hidden overflow-x-hidden overflow-y-auto fixed inset-0 z-50 outline-none focus:outline-none justify-center items-center" id="card-modal">
    <div class="relative h-screen my-6 mx-auto flex items-center justify-center w-full">
        <!--content-->
        <div
        class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
        <button type="button" onclick="toggleModalCard('card-modal')" class="absolute top-3 right-3 text-gray-400 hover:text-gray-500">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
            <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start justify-center">
                    <!-- <div
                        class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full sm:mx-0 sm:h-10 sm:w-10 bg-red-500">
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
                        <p class="can-exp-p text-center">Are you sure you want to delete ?</p>
                    </div>
                </div>
            </div>
            <div class="px-4 pb-6 pt-4 flex items-center space-x-2 sm:space-x-4 sm:px-6 justify-center">
                <a id="delete-card-link" href="#"
                    class="inline-flex w-auto justify-center rounded bg-red-500 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white whitespace-nowrap hover:text-white hover:shadow-lg shadow-sm hover:bg-red-400 sm:ml-3 sm:w-24">Delete</a>
                <button type="button" onclick="toggleModalCard('card-modal')"
                    class="button-exp-fill sm:w-24">Cancel</button>
            </div>
        </div>
    </div>
</div>
<div class="hidden opacity-25 fixed inset-0 z-40 bg-black" id="card-modal-backdrop"></div>

@endsection

@section('script')

<script>
    function toggleModalCard(modalId, cardId = null) {
        let modal = document.getElementById(modalId);
        let backdrop = document.getElementById(modalId + "-backdrop");

        if (modal.classList.contains("hidden")) {
            modal.classList.remove("hidden");
            backdrop.classList.remove("hidden");

            // Update the delete link if cardId is provided
            if (cardId) {
                let deleteLink = document.getElementById("delete-card-link");
                deleteLink.href = `/delete-card/${cardId}`;
            }
        } else {
            modal.classList.add("hidden");
            backdrop.classList.add("hidden");
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        // Define an array mapping month numbers to month names
        var months = {
            1: 'January',
            2: 'February',
            3: 'March',
            4: 'April',
            5: 'May',
            6: 'June',
            7: 'July',
            8: 'August',
            9: 'September',
            10: 'October',
            11: 'November',
            12: 'December'
        };

        // Loop through each card and update expiry date
        @foreach ($cards as $card)
            @if ($card->paymentMethod && $card->paymentMethod->card)
                var exp_month = {{ $card->paymentMethod->card->exp_month }};
                var exp_year = {{ $card->paymentMethod->card->exp_year }};
                var exp_date = months[exp_month] + ' ' + exp_year;
                document.getElementById("exp_date_{{ $card->id }}").textContent = exp_date;
            @endif
        @endforeach
    });

    function closeModal() {
    const modal = document.getElementById('myModal');
    if (modal) {
        modal.classList.add('hidden');
    }
}
</script>

@endsection
