@extends('layouts.template')

@section('title', 'Post PX Ride Again')

@section('content')
    <div class="container mx-auto my-14 px-4">
        <div class="bg-white rounded pt-0 p-4 w-full">
            <div class="flex flex-wrap" id="tabs-id">
                <div class="w-full">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 pb-4">
                        <h1 class="text-3xl font-FuturaMdCnBT">
                            {{ $postRidePage->post_ride_again_main_heading ?? 'Post a Ride Again' }}
                        </h1>
                        <a href="{{ route('px.post_ride.create', ['lang' => optional($selectedLanguage)->abbreviation]) }}"
                            class="button-exp-fill inline-block text-center">
                            {{ $postRidePage->submit_button_label ?? 'Post PX Ride' }}
                        </a>
                    </div>

                    <ul class="flex mb-0 list-none flex-wrap pt-3 pb-4 flex-row gap-2">
                        <li class="flex-auto text-center">
                            <a href="{{ route('px.post_ride_again', ['lang' => optional($selectedLanguage)->abbreviation]) }}"
                                class="text-2xl font-FuturaMdCnBT px-5 py-2 shadow-lg rounded block border-blue-600 border leading-normal cursor-pointer {{ Route::currentRouteName() === 'px.post_ride_again' ? 'border-blue-600 text-white bg-blue-600' : 'border-gray-100 text-blue-600 bg-white' }}">
                                {{ $tripsPage->upcoming_label ?? 'Upcoming' }}
                            </a>
                        </li>
                        <li class="flex-auto text-center">
                            <a href="{{ route('px.post_ride_again_completed', ['lang' => optional($selectedLanguage)->abbreviation]) }}"
                                class="text-2xl font-FuturaMdCnBT px-5 py-2 shadow-lg rounded block border-blue-600 border leading-normal cursor-pointer {{ Route::currentRouteName() === 'px.post_ride_again_completed' ? 'border-blue-600 text-white bg-blue-600' : 'border-gray-100 text-blue-600 bg-white' }}">
                                {{ $tripsPage->completed_label ?? 'Completed' }}
                            </a>
                        </li>
                        <li class="flex-auto text-center">
                            <a href="{{ route('px.post_ride_again_cancelled', ['lang' => optional($selectedLanguage)->abbreviation]) }}"
                                class="text-2xl font-FuturaMdCnBT px-5 py-2 shadow-lg rounded block border-blue-600 border leading-normal cursor-pointer {{ Route::currentRouteName() === 'px.post_ride_again_cancelled' ? 'border-blue-600 text-white bg-blue-600' : 'border-gray-100 text-blue-600 bg-white' }}">
                                {{ $tripsPage->cancelled_label ?? 'Cancelled' }}
                            </a>
                        </li>
                    </ul>

                    <div class="relative flex flex-col min-w-0 break-words bg-white w-full mb-6 shadow-lg rounded">
                        <div class="px-4 py-5 flex-auto">
                            <div class="space-y-4">
                                @if (!empty($rides) && $rides->count() > 0)
                                    @foreach ($rides as $ride)
                                        <x-px.ride-card
                                            :ride="$ride"
                                            :lang="optional($selectedLanguage)->abbreviation"
                                            detail-route="px.post_ride.copy"
                                            :show-status="false"
                                            :show-booking-info="false"
                                            :show-options="false"
                                            :price-minor="$ride->price_minor"
                                        />
                                    @endforeach

                                    <div class="mt-6">
                                        {{ $rides->links() }}
                                    </div>
                                @else
                                    <p class="text-gray-700">
                                        @if ($activeTab === 'completed')
                                            {{ $tripsPage->no_completed_rides_label ?? 'No completed rides found.' }}
                                        @elseif ($activeTab === 'cancelled')
                                            {{ $tripsPage->no_cancelled_rides_label ?? 'No cancelled rides found.' }}
                                        @else
                                            {{ $tripsPage->no_upcoming_rides_label ?? 'No upcoming rides found.' }}
                                        @endif
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
