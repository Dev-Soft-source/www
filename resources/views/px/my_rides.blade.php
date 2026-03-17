@extends('layouts.template')

@section('title', 'My PX Rides')

@section('content')
    @if(session('message'))
        <div id="myModal" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div onclick="closeModal()" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                    <div class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
                        <button type="button" onclick="closeModal()" class="absolute top-3 right-3 text-gray-400 hover:text-gray-500">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start justify-center">
                                <div class="mx-auto h-16 w-16 flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="4" stroke="currentColor" class="w-12 h-12 text-greenXS">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                    </svg>
                                </div>
                            </div>
                            <div class="text-center sm:ml-4 sm:mt-0 sm:text-left">
                                <div class="">
                                    <h3 class="text-3xl text-center font-FuturaMdCnBT font-medium text-gray-900 mb-4" id="modal-title">Success</h3>
                                </div>
                                <div class="mt-2 w-full">
                                    <p class="can-exp-p text-center">{!! session('message') !!}</p>
                                </div>
                            </div>
                        </div>
                        <div class="px-4 pb-6 pt-4 flex items-center space-x-2 sm:space-x-4 sm:px-6 justify-center">
                            <a href="" onclick="closeModal(); return false;" class="button-exp-fill">Close</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
            function closeModal() {
                const modal = document.getElementById('myModal');
                if (modal) modal.style.display = 'none';
            }
        </script>
    @endif

    <div class="grid grid-cols-12 gap-4 md:container md:mx-auto my-6 md:my-10 xl:my-14 px-4 xl:px-0">
        @include('layouts.inc.profile_sidebar')
        @php
            $currentRoute = Route::currentRouteName();
            $isPassengerSection = in_array($currentRoute, ['px.my_trips']);
            $isDriverSection = in_array($currentRoute, ['px.my_rides']);
            $tabSelected = 'border-blue-600 border leading-normal text-white bg-blue-600';
            $tabUnselected = 'border-gray-100 border leading-normal text-blue-600 bg-white';
        @endphp
        <div class="bg-white rounded pt-0 lg:px-4 w-full col-span-12 lg:col-span-9">
            <ul class="flex mb-0 list-none flex-wrap pb-4 flex-row">
                <li class="-mb-px mr-2 last:mr-0 flex-auto text-center">
                    <a href="{{ route('px.my_trips', ['lang' => $selectedLanguage->abbreviation]) }}" class="text-2xl font-FuturaMdCnBT px-5 py-2 shadow-lg rounded block {{ $isPassengerSection ? $tabSelected : $tabUnselected }} cursor-pointer">
                        {{ optional($tripsPage)->passenger_trips_heading ?? 'Passenger trips' }}
                    </a>
                </li>
                <li class="-mb-px mr-2 last:mr-0 flex-auto text-center">
                    <a href="{{ route('px.my_rides', ['lang' => $selectedLanguage->abbreviation]) }}" class="text-2xl font-FuturaMdCnBT px-5 py-2 shadow-lg rounded block {{ $isDriverSection ? $tabSelected : $tabUnselected }} cursor-pointer">
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
                            <a href="{{ route('px.my_rides', ['lang' => optional($selectedLanguage)->abbreviation, 'tab' => 'upcoming']) }}" 
                               class="text-lg font-FuturaMdCnBT font-medium px-5 py-2 shadow-lg rounded block {{ $activeTab === 'upcoming' ? $tabSelected : $tabUnselected }} cursor-pointer">
                                {{ optional($tripsPage)->upcoming_label ?? 'Upcoming' }}@if(($upcomingCount ?? 0) > 0) ({{ $upcomingCount }})@endif
                            </a>
                        </li>
                        <li class="-mb-px mr-2 last:mr-0 flex-auto text-center">
                            <a href="{{ route('px.my_rides', ['lang' => optional($selectedLanguage)->abbreviation, 'tab' => 'completed']) }}" 
                               class="text-lg font-FuturaMdCnBT font-medium px-5 py-2 shadow-lg rounded block {{ $activeTab === 'completed' ? $tabSelected : $tabUnselected }} cursor-pointer">
                                {{ optional($tripsPage)->completed_label ?? 'Completed' }}@if(($completedCount ?? 0) > 0) ({{ $completedCount }})@endif
                            </a>
                        </li>
                        <li class="-mb-px mr-2 last:mr-0 flex-auto text-center">
                            <a href="{{ route('px.my_rides', ['lang' => optional($selectedLanguage)->abbreviation, 'tab' => 'cancelled']) }}" 
                               class="text-lg font-FuturaMdCnBT font-medium px-5 py-2 shadow-lg rounded block {{ $activeTab === 'cancelled' ? $tabSelected : $tabUnselected }} cursor-pointer">
                                {{ optional($tripsPage)->cancelled_label ?? 'Cancelled' }}@if(($cancelledCount ?? 0) > 0) ({{ $cancelledCount }})@endif
                            </a>
                        </li>
                    </ul>
                    <div class="relative flex flex-col min-w-0 break-words bg-white w-full py-5 shadow-lg rounded">
                        <div class="px-4 flex-auto">
                            <div class="tab-content tab-space">
                                <div class="block" id="tab-profile">
                                    <div class="space-y-4">
                                        @if (!empty($rides) && $rides->count() > 0)
                                            @foreach ($rides as $ride)
                                                <x-px.ride-card
                                                    :ride="$ride"
                                                    :lang="optional($selectedLanguage)->abbreviation"
                                                    :show-status="true"
                                                    :price-minor="$ride->price_minor"
                                                >
                                                    {{-- @if ($ride->notes)
                                                        <div class="mt-3 border-t border-gray-200">
                                                            <p class="font-semibold mb-1">Notes:</p>
                                                            <p class="text-gray-600 text-sm">{{ $ride->notes }}</p>
                                                        </div>
                                                    @endif --}}
                                                    

                                                    @if (isset($ride->meta['recurring']['enabled']) && $ride->meta['recurring']['enabled'])
                                                        <div class="mt-3 border-t border-gray-200">
                                                            <p class="font-semibold mb-1">Recurring Trip:</p>
                                                            <p class="text-gray-600 text-sm">
                                                                Frequency: {{ ucfirst($ride->meta['recurring']['frequency'] ?? 'N/A') }},
                                                                {{ $ride->meta['recurring']['trips'] ?? 0 }} trips
                                                            </p>
                                                            @if (isset($ride->meta['recurring']['pick_drop_off_description']))
                                                                <p class="text-gray-600 text-sm mt-1">
                                                                    <strong>Description:</strong> {{ $ride->meta['recurring']['pick_drop_off_description'] }}
                                                                </p>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </x-px.ride-card>
                                            @endforeach

                                            <div class="mt-6">
                                                {{ $rides->links() }}
                                            </div>
                                        @else
                                            <div class="text-center py-12">
                                                @if ($activeTab === 'upcoming')
                                                    <p class="text-gray-600 text-lg mb-4">You have no upcoming rides.</p>
                                                    <a href="{{ route('px.post_ride.create', ['lang' => optional($selectedLanguage)->abbreviation]) }}" 
                                                       class="button-exp-fill inline-block">
                                                        Post New PX Ride
                                                    </a>
                                                @elseif ($activeTab === 'completed')
                                                    <p class="text-gray-600 text-lg mb-4">You have no completed rides.</p>
                                                @elseif ($activeTab === 'cancelled')
                                                    <p class="text-gray-600 text-lg mb-4">You have no cancelled rides.</p>
                                                @endif
                                            </div>
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
@endsection
