@extends('layouts.template')

@section('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        @keyframes booking-request-pulse {

            0%,
            100% {
                opacity: 1;
                transform: scale(1);
                box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            }

            50% {
                opacity: 0.95;
                transform: scale(1.08);
                box-shadow: 0 10px 15px -3px rgb(35 168 168 / 0.2);
            }
        }

        .booking-request-alert {
            animation: booking-request-pulse 1.5s ease-in-out 5;
        }
    </style>
@endsection

@section('content')
    @if (session('error'))
        <div id="errorModal" class="relative z-50" aria-labelledby="error-modal-title" role="dialog" aria-modal="true">
            <div onclick="closeErrorModal()" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                    <div
                        class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
                        <button type="button" onclick="closeErrorModal()"
                            class="absolute top-3 right-3 text-gray-400 hover:text-gray-500">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start justify-center">
                                <div class="mx-auto h-16 w-16 flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="2" stroke="currentColor" class="w-12 h-12 text-red-500">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="text-center sm:ml-4 sm:mt-0 sm:text-left">
                                <h3 class="text-3xl text-center font-FuturaMdCnBT font-medium text-gray-900 mb-4"
                                    id="error-modal-title">Notice</h3>
                                <div class="mt-2 w-full">
                                    <p class="can-exp-p text-center text-gray-700">{!! session('error') !!}</p>
                                </div>
                            </div>
                        </div>
                        <div class="px-4 pb-6 pt-4 flex items-center justify-center sm:px-6">
                            <button type="button" onclick="closeErrorModal()"
                                class="button-exp-fill">{{ $siteText['close_btn_text'] }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
            function closeErrorModal() {
                const modal = document.getElementById('errorModal');
                if (modal) modal.style.display = 'none';
            }
        </script>
    @endif
    @if (session('message'))
        <div id="myModal" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div onclick="closeModal()" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                    <div
                        class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
                        <button type="button" onclick="closeModal()"
                            class="absolute top-3 right-3 text-gray-400 hover:text-gray-500">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start justify-center">
                                <div class="mx-auto h-16 w-16 flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="4" stroke="currentColor" class="w-12 h-12 text-greenXS">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                    </svg>
                                </div>

                                <!-- <div
                                class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full sm:mx-0 sm:h-10 sm:w-10 bg-green-500">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-lg text-white w-8 h-8" viewBox="0 0 16 16">
                                    <path d="M7.005 3.1a1 1 0 1 1 1.99 0l-.388 6.35a.61.61 0 0 1-1.214 0zM7 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0"/>
                                </svg>
                            </div> -->
                            </div>
                            <div class="text-center sm:ml-4 sm:mt-0 sm:text-left">
                                <div class="">
                                    <h3 class="text-3xl text-center font-FuturaMdCnBT font-medium text-gray-900 mb-4"
                                        id="modal-title">{!! session('heading') !!}</h3>
                                </div>
                                <div class="mt-2 w-full">
                                    <p class="can-exp-p text-center">{!! session('message') !!}</p>
                                </div>
                            </div>
                        </div>
                        <div class="px-4 pb-6 pt-4 flex items-center space-x-2 sm:space-x-4 sm:px-6 justify-center">
                            @if (session('id'))
                                <a href="{{ route('repost_ride', ['lang' => $selectedLanguage->abbreviation, 'id' => session('id')]) }}"
                                    class="inline-flex w-full justify-center rounded bg-greenXS px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white whitespace-nowrap hover:text-white hover:shadow-lg shadow-sm hover:bg-greenXS sm:ml-3 sm:w-fit">Post
                                    a Return Ride</a>
                            @endif
                            <a href="" class="button-exp-fill">{{ $siteText['close_btn_text'] }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    @if (session('price_warning'))
        <!-- Modal for Price Warning (Exceeds $0.66/km per seat but <= $0.72/km per seat) -->
        <div id="priceWarningModal" class="hidden fixed inset-0 z-50" aria-labelledby="price-warning-modal-title"
            role="dialog" aria-modal="true">
            <div onclick="closePriceWarningModal()" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity">
            </div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                    <div
                        class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
                        <button type="button" onclick="closePriceWarningModal()"
                            class="absolute top-4 right-4 text-gray-400 hover:text-gray-500 z-50">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                        <div class="bg-white px-4 mt-10 sm:mt-1 pb-4 pt-16 sm:p-6 sm:pb-4 sm:pt-16">
                            <div class="text-center sm:ml-4 sm:mt-0 sm:text-left">
                                <div class="">
                                    <h3 class="text-3xl text-center font-FuturaMdCnBT text-gray-900 mb-4">Recommended
                                        Contribution Limit</h3>
                                </div>
                                <div class="mt-2 w-full">
                                    <p class="can-exp-p text-center mb-3" id="priceWarningParagraph1">The price you
                                        entered is above the standard reimbursement rate recommended by the CRA and Revenu
                                        Québec.</p>
                                    <p class="can-exp-p text-center" id="priceWarningParagraph2">While you can proceed, we
                                        suggest reducing the price per seat. This ensures your ride remains a standard
                                        carpool even if you drive long distances this year.</p>
                                </div>
                            </div>
                        </div>
                        <div class="px-4 pb-6 pt-4 flex items-center space-x-2 sm:space-x-4 sm:px-6 justify-center">
                            <button type="button" onclick="closePriceWarningModal()" class="button-exp-fill">Got
                                it</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const modal = document.getElementById('priceWarningModal');
                if (modal) {
                    modal.classList.remove('hidden');
                    modal.style.display = 'block';
                    modal.style.visibility = 'visible';
                    modal.style.opacity = '1';
                    modal.style.zIndex = '50';
                }
            });

            function closePriceWarningModal() {
                const modal = document.getElementById('priceWarningModal');
                if (modal) {
                    modal.classList.add('hidden');
                    modal.style.display = 'none';
                }
            }
        </script>
    @endif

     @php
        $currentRoute = Route::currentRouteName();
        $isPassengerSection = in_array($currentRoute, ['my_trips']);
        $isDriverSection = in_array($currentRoute, ['my_rides']);
        $tabSelected = 'border-blue-600 border leading-normal text-white bg-blue-600';
        $tabUnselected = 'border-gray-100 border leading-normal text-blue-600 bg-white';
    @endphp
    <div class="grid grid-cols-12 gap-4 md:container md:mx-auto  my-6 md:my-10 xl:my-14 px-4 xl:px-0">
        @include('layouts.inc.profile_sidebar')

        <div class="bg-white rounded pt-0 lg:px-4 w-full col-span-12 lg:col-span-9">
            <ul class="flex mb-0 list-none flex-wrap pb-4 flex-row">
                <li class="-mb-px mr-2 last:mr-0 flex-auto text-center">
                    <a href="{{ route('my_trips', ['lang' => $selectedLanguage->abbreviation]) }}" class="text-2xl font-FuturaMdCnBT px-5 py-2 shadow-lg rounded block {{ $isPassengerSection ? $tabSelected : $tabUnselected }} cursor-pointer">
                        {{ optional($tripsPage)->passenger_trips_heading ?? 'Passenger trips' }}
                    </a>
                </li>
                <li class="-mb-px mr-2 last:mr-0 flex-auto text-center">
                    <a href="{{ route('my_rides', ['lang' => $selectedLanguage->abbreviation]) }}" class="text-2xl font-FuturaMdCnBT px-5 py-2 shadow-lg rounded block {{ $isDriverSection ? $tabSelected : $tabUnselected }} cursor-pointer">
                        {{ optional($tripsPage)->driver_rides_heading ?? 'Driver rides' }}
                    </a>
                </li>
            </ul>
            <div class="flex flex-wrap" id="tabs-id">
                <div class="w-full">
                    @php
                        $activeTab = $activeTab ?? 'upcoming';
                        $tabSelected = 'border-blue-600 border leading-normal text-white bg-blue-600';
                        $tabUnselected = 'border-gray-100 border leading-normal text-blue-600 bg-white';
                    @endphp
                    <ul class="flex mb-0 list-none flex-wrap pb-4 flex-row">
                        <li class="-mb-px mr-2 last:mr-0 flex-auto text-center">
                            <a href="{{ route('my_rides', ['lang' => optional($selectedLanguage)->abbreviation, 'tab' => 'upcoming']) }}" 
                               class="text-lg font-FuturaMdCnBT font-medium px-5 py-2 shadow-lg rounded block {{ $activeTab === 'upcoming' ? $tabSelected : $tabUnselected }} cursor-pointer">
                                {{ optional($tripsPage)->upcoming_label ?? 'Upcoming' }}@if(($upcomingCount ?? 0) > 0) ({{ $upcomingCount }})@endif
                            </a>
                        </li>
                        <li class="-mb-px mr-2 last:mr-0 flex-auto text-center">
                            <a href="{{ route('my_rides', ['lang' => optional($selectedLanguage)->abbreviation, 'tab' => 'completed']) }}" 
                               class="text-lg font-FuturaMdCnBT font-medium px-5 py-2 shadow-lg rounded block {{ $activeTab === 'completed' ? $tabSelected : $tabUnselected }} cursor-pointer">
                                {{ optional($tripsPage)->completed_label ?? 'Completed' }}@if(($completedCount ?? 0) > 0) ({{ $completedCount }})@endif
                            </a>
                        </li>
                        <li class="-mb-px mr-2 last:mr-0 flex-auto text-center">
                            <a href="{{ route('my_rides', ['lang' => optional($selectedLanguage)->abbreviation, 'tab' => 'cancelled']) }}" 
                               class="text-lg font-FuturaMdCnBT font-medium px-5 py-2 shadow-lg rounded block {{ $activeTab === 'cancelled' ? $tabSelected : $tabUnselected }} cursor-pointer">
                                {{ optional($tripsPage)->cancelled_label ?? 'Cancelled' }}@if(($cancelledCount ?? 0) > 0) ({{ $cancelledCount }})@endif
                            </a>
                        </li>
                    </ul>
                    <div class="relative flex flex-col min-w-0 break-words bg-white w-full py-5 shadow-lg rounded">
                        <div class="">
                            <div class="px-4 flex-auto">
                                <div class="tab-content tab-space">
                                    <div class="block" id="tab-profile">
                                        <div class="space-y-4">
                                            @if (!empty($rides) && count($rides) > 0)
                                                @foreach ($rides as $ride)
                                                    <x-px.my-card 
                                                    :ride="$ride" 
                                                    :detail-route="'my_ride_detail'" 
                                                    :show-ride-booking-info="false" 
                                                    :show-status="true" 
                                                    :show-options="true" 
                                                    :show-kind-border="false" 
                                                    />
                                                @endforeach
                                                {{ $rides->links() }}
                                            @else
                                                @if ($activeTab === 'upcoming')
                                                    <p>{{ $tripsPage->no_upcoming_rides_label ?? 'You have no upcoming rides scheduled.' }}</p>
                                                @elseif ($activeTab === 'completed')
                                                    <p>{{ $tripsPage->no_completed_rides_label ?? 'You have no completed rides' }}</p>
                                                @elseif ($activeTab === 'cancelled')
                                                    <p>{{ $tripsPage->no_cancelled_rides_label ?? 'You have no cancelled rides' }}</p>
                                                @endif
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
