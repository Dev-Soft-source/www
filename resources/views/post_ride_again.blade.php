@extends('layouts.template')

@section('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endsection

@section('content')

<div class="container mx-auto my-14">
    <div class="bg-white rounded pt-0 p-4 w-full">
        <div class="flex flex-wrap" id="tabs-id">
            <div class="w-full">
                <ul class="flex mb-0 list-none flex-wrap pt-3 pb-4 flex-row gap-2">
                    <li class="flex-auto text-center">
                        <a href="{{ route('post_ride_again', ['lang' => $selectedLanguage->abbreviation]) }}" class="text-2xl font-FuturaMdCnBT px-5 py-2 shadow-lg rounded block border-blue-600 border leading-normal  cursor-pointer {{ Route::currentRouteName() === 'post_ride_again' ? 'border-blue-600 border text-white bg-blue-600' : 'border-gray-100 border text-blue-600 bg-white' }}">
                            {{ $tripsPage->upcoming_label }}
                        </a>
                    </li>
                    <li class="flex-auto text-center">
                        <a href="{{ route('post_ride_again_completed', ['lang' => $selectedLanguage->abbreviation]) }}" class="text-2xl font-FuturaMdCnBT px-5 py-2 shadow-lg rounded block border-blue-600 border leading-normal cursor-pointer {{ Route::currentRouteName() === 'post_ride_again_completed' ? 'border-blue-600 border text-white bg-blue-600' : 'border-gray-100 border text-blue-600 bg-white' }}">
                            {{ $tripsPage->completed_label }}
                        </a>
                    </li>
                    <li class="flex-auto text-center">
                        <a href="{{ route('post_ride_again_cancelled', ['lang' => $selectedLanguage->abbreviation]) }}" class="text-2xl font-FuturaMdCnBT px-5 py-2 shadow-lg rounded block border-blue-600 border leading-normal cursor-pointer {{ Route::currentRouteName() === 'post_ride_again_cancelled' ? 'border-blue-600 border text-white bg-blue-600' : 'border-gray-100 border text-blue-600 bg-white' }}">
                            {{ $tripsPage->cancelled_label }}
                        </a>
                    </li>
                </ul>
                <div class="relative flex flex-col min-w-0 break-words bg-white w-full mb-6 shadow-lg rounded">
                    <div class="px-4 py-5 flex-auto">
                        <div class="tab-content tab-space">
                            <div class="block" id="tab-profile">
                                <div class="space-y-4">
                                    @if (!empty($rides) && count($rides) > 0)
                                        @foreach ($rides as $ride)
                                            @php
                                                $from = $ride->rideDetail[0]->departure;
                                                $to = $ride->rideDetail[0]->destination;
                                            @endphp
                                            <div class="relative">
                                                <a href="{{ route('copy_ride', ['lang' => $selectedLanguage->abbreviation, 'id' => $ride->id]) }}">
                                                    <div class="bg-white rounded-lg shadow-3xl border-[3px] border-solid  border-gray-100 " id="ride-29">
                                                        <div class="flex justify-between px-4">
                                                            <div class="w-full">
                                                                <div class="relative mt-5 text-left">
                                                                    <div class="flex items-center relative">
                                                                        <div class="border-r-2 border-black border-solid absolute h-full left-3 md:left-6 top-2 z-10">
                                                                            <span class="bg-primary rounded-full w-7 h-7 -top-[2px] -ml-[13px] absolute flex justify-center items-center">
                                                                                <img class="w-5 h-5 object-contain" src="{{ asset('./images/new-21-search-bar-from.png')}}" alt="">
                                                                            </span>
                                                                        </div>
                                                                        <div class="ml-20">
                                                                            <div class="font-bold text-black">From</div>
                                                                            <div class="text-primary md:mb-4">{{ $from }} </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="flex items-center relative">
                                                                        <div class="border-r-2 border-black border-solid absolute h-0 left-3 md:left-5 top-2 z-10">
                                                                            <span
                                                                                class="bg-gray-200 rounded-full w-7 h-7 -top-[6px] -ml-[9px] absolute flex justify-center items-center">
                                                                                <img class="w-5 h-5 object-contain" src="{{ asset('./images/new-21-search-bar-to.png')}}" alt="">
                                                                            </span>
                                                                        </div>
                                                                        <div class="ml-20">
                                                                            <div class="font-bold text-black">To</div>
                                                                            <div class="text-primary md:mb-4">{{ $to }}</div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                        @endforeach
                                        {{ $rides->links() }}
                                    @else
                                        <p>
                                            @if (Route::currentRouteName() === 'post_ride_again_completed')
                                                {{ $tripsPage->no_completed_rides_label }}
                                            @elseif (Route::currentRouteName() === 'post_ride_again_cancelled')
                                                {{ $tripsPage->no_cancelled_rides_label }}
                                            @else
                                                {{ $tripsPage->no_upcoming_rides_label }}
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
    </div>
</div>

@endsection
