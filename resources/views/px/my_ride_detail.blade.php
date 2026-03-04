@extends('layouts.template')

@section('content')

    @if (session('message'))
        <div class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div
                    class="relative flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full w-full">
                    <div
                        class="relative animate__animated animate__fadeIn z-20 transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
                        <button onclick="closeModal('message-modal')"
                            class="absolute top-2 right-2 p-1 rounded-full hover:bg-gray-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start justify-center">
                            </div>
                            <div class="text-center w-full mt-4">
                                <p class="can-exp-p text-center">{!! session('message') !!}</p>
                            </div>
                        </div>
                        <div class="px-4 pb-6 pt-4  sm:flex sm:flex-row-reverse sm:px-6 justify-center">
                            <a href=""
                                class="inline-flex w-full justify-center rounded bg-red-500 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-red-400 sm:ml-3 sm:w-24">{{ $siteText['close_btn_text'] ?? 'Close' }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if (session('success'))
        <div id="my-modal" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div
                    class="relative flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeModal()"></div>
                    <div
                        class="relative animate__animated animate__fadeIn z-20 transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
                        <button onclick="closeModal('success-modal')"
                            class="absolute top-2 right-2 p-1 rounded-full hover:bg-gray-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start justify-center">
                            </div>
                            <div class="w-full mt-4">
                                <p class="can-exp-p text-center">{!! session('success') !!}</p>
                            </div>
                        </div>
                        <div class="px-4 pb-6 pt-4  sm:flex sm:flex-row-reverse sm:px-6 justify-center">
                            <a href=""
                                class="inline-flex w-full justify-center rounded bg-greenXS px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-greenXS sm:ml-3 sm:w-24">{{ $siteText['close_btn_text'] ?? 'Close' }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if (session('failure'))
        <div class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                    <div
                        class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg w-full modal-border">
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start justify-center">
                            </div>
                            <div class="text-center mt-4">
                                <div class="w-full">
                                    <p class="text-center can-exp-p">{!! session('failure') !!}</p>
                                </div>
                            </div>
                        </div>
                        <div class="px-4 pb-6 pt-4 sm:flex sm:flex-row-reverse sm:px-6 justify-center">
                            <a href=""
                                class="whitespace-nowrap inline-flex w-full justify-center rounded bg-red-500 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-red-400 sm:ml-3 sm:w-24">{{ $siteText['close_btn_text'] ?? 'Close' }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="relative z-50" aria-labelledby="error-modal-title" role="dialog" aria-modal="true">
            <div onclick="closeErrorModal()" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                    <div class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
                        <button type="button" onclick="closeErrorModal()" class="absolute top-3 right-3 text-gray-400 hover:text-gray-500">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start justify-center">
                                <div class="mx-auto h-16 w-16 flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-12 h-12 text-red-500">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="text-center sm:ml-4 sm:mt-0 sm:text-left">
                                <h3 class="text-3xl text-center font-FuturaMdCnBT font-medium text-gray-900 mb-4" id="error-modal-title">Error</h3>
                                <div class="mt-2 w-full">
                                    <p class="can-exp-p text-center text-gray-700">{!! session('error') !!}</p>
                                </div>
                            </div>
                        </div>
                        <div class="px-4 pb-6 pt-4 flex items-center justify-center sm:px-6">
                            <button type="button" onclick="closeErrorModal()" class="button-exp-fill">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
            function closeErrorModal() {
                const modal = document.querySelector('[aria-labelledby="error-modal-title"]');
                if (modal) modal.style.display = 'none';
            }
        </script>
    @endif

    <div class="container mx-auto my-10 xl:my-14 px-4 xl:px-0">
        @if ($ride->seats_available == 0)
            <div class="mt-4 rounded-lg px-6 py-3 bg-blue-100 text-gray-600" role="alert">
                {{ $rideDetailPage->all_seats_booked_label ?? 'All seats are booked' }}
            </div>
        @endif

        @if ($ride->status === 'cancelled')
            <div class="mt-4 rounded-lg px-6 py-3 bg-red-100 text-gray-600" role="alert">
                {{ $rideDetailPage->ride_canceller_by_driver ?? 'This ride was cancelled by the driver' }}
            </div>
        @endif

        @if ($ride->status === 'completed')
            <div class="mt-4 rounded-lg px-6 py-3 bg-blue-100 text-gray-600" role="alert">
                {{ $rideDetailPage->ride_completed_text ?? 'This ride was completed' }}
            </div>
        @endif

        <h1>{{ $rideDetailPage->main_heading ?? 'My ride detail' }}</h1>

        <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-y-4 md:gap-4">
            <div class="col-span-2">
                <div class="bg-white rounded-lg shadow-3xl">
                    <div class="flex flex-col md:flex-row justify-between px-4">
                        @php
                            $origin = $ride->route->origin_label ?? 'N/A';
                            $destination = $ride->route->destination_label ?? 'N/A';
                            $pickupLocation = $ride->meta['pickup_location'] ?? null;
                            $dropoffLocation = $ride->meta['dropoff_location'] ?? null;
                            $pricePerSeatMinor = (int) $ride->price_minor;
                            $currencyCode = strtoupper((string) ($ride->currency ?? ($selectedCurrency ?? 'USD')));
                            $currencyMap = ['USD' => '$', 'CAD' => 'C$'];
                            $currency = $currencyMap[$currencyCode] ?? ($currencyCode . ' ');
                        @endphp
                        <div class="w-full md:w-2/3 order-2 md:order-1">
                            @if ($origin || $destination)
                                <div class="relative mt-5 text-left rounded-lg bg-white p-4">
                                    <div class="space-y-0">
                                        @if ($origin)
                                            <div class="flex items-center relative">
                                                <div class="border-r-2 border-black border-solid absolute h-full left-3 md:left-6 top-2 z-10">
                                                    <span class="bg-primary rounded-full w-7 h-7 -top-[2px] -ml-[13px] absolute flex justify-center items-center">
                                                        <img class="w-5 h-5 object-contain" src="{{ asset('./images/new-21-search-bar-from.png') }}" alt="">
                                                    </span>
                                                </div>
                                                <div class="items-center ml-12 md:ml-20">
                                                    <p class="font-bold text-xl text-black">
                                                        {{ $rideDetailPage->from_label ?? 'From' }}</p>
                                                    <div class="flex gap-2 items-baseline">
                                                        <h3
                                                            class="text-primary font-FuturaMdCnBT text-xl md:text-2xl md:mb-4">
                                                            {{ $origin }}.
                                                        </h3>
                                                        @if ($pickupLocation)
                                                            <p class="text-gray-600 text-sm ml-2"><strong>Pick-up at:</strong>
                                                                {{ $pickupLocation }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        @if ($ride->stops->isNotEmpty())
                                            <div class="flex items-center relative">
                                                <div
                                                    class="border-r-2 border-black border-solid absolute h-full left-3 md:left-6 top-2 z-10">
                                                </div>
                                                <div class="ml-12 md:ml-20 flex">
                                                    <p class="font-bold text-xl text-black mb-2">
                                                        {{ $rideDetailPage->stops_label ?? 'Stops on the way' }}</p>
                                                    <ul class="flex flex-col gap-2 text-sm ml-4 mt-1 mb-4">
                                                         @foreach ($ride->stops->where('is_pickup', true)->where('is_dropoff', true) as $stop)
                                                            <li class="flex items-center px-2 py-0.5 rounded border border-gray-300 bg-gray-50 text-gray-700">
                                                                <span class="h-4 w-4 inline-flex mr-2 ">
                                                                    <svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg" fill="#000000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><path fill="#666666" d="M256 17.108c-75.73 0-137.122 61.392-137.122 137.122.055 23.25 6.022 46.107 11.58 56.262L256 494.892l119.982-274.244h-.063c11.27-20.324 17.188-43.18 17.202-66.418C393.122 78.5 331.73 17.108 256 17.108zm0 68.56a68.56 68.56 0 0 1 68.56 68.562A68.56 68.56 0 0 1 256 222.79a68.56 68.56 0 0 1-68.56-68.56A68.56 68.56 0 0 1 256 85.67z"></path></g></svg>
                                                                </span>
                                                                {{ $stop->label }}
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                        @endif

                                        @if ($destination)
                                            <div class="flex items-center relative">
                                                <div class="border-r-2 border-black border-solid absolute h-0 left-3 md:left-5 top-2 z-10">
                                                    <span class="bg-gray-200 rounded-full w-7 h-7 -top-[6px] -ml-[12px] md:-ml-[9px] absolute flex justify-center items-center">
                                                        <img class="w-5 h-5 object-contain" src="{{ asset('./images/new-21-search-bar-to.png') }}" alt="">
                                                    </span>
                                                </div>
                                                <div class="items-center ml-12 md:ml-20">
                                                    <p class="font-bold text-xl text-black">
                                                        {{ $rideDetailPage->to_label ?? 'To' }}</p>
                                                    <div class="flex gap-2 items-baseline">
                                                        <h3
                                                            class="text-primary font-FuturaMdCnBT text-xl md:text-2xl md:mb-4">
                                                            {{ $destination }}.
                                                        </h3>
                                                        @if ($dropoffLocation)
                                                            <p class="text-gray-600 text-sm ml-2"><strong>Drop-off at:</strong>
                                                                {{ $dropoffLocation }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="mt-4 order-1 md:order-2">
                            <p class="whitespace-nowrap font-semibold">
                                {{ $ride->departure_at->format('F d, Y') }} at
                                {{ $ride->departure_at->format('h:i A') == '12:00 PM' ? '12 noon' : ($ride->departure_at->format('h:i A') == '12:00 AM' ? '12 midnight' : $ride->departure_at->format('h:i A')) }}
                            </p>
                        </div>
                    </div>

                    <div class="border-t border-gray-300 grid grid-cols-2 divide-x divide-gray-300">
                        <div class="flex items-baseline p-4">
                            <h4 class="font-medium text-xl xl:text-2xl text-left text-black font-FuturaMdCnBT">
                                @isset($rideDetailPage->seats_left_label)
                                    {{ $rideDetailPage->seats_left_label }}:
                                @endisset
                            </h4>
                            <p class="text-xl text-primary font-normal ml-2" style="font-family: 'Roboto', sans-serif;">{{ $ride->seats_available }}</p>
                        </div>
                        <div class="flex flex-wrap items-center gap-3 p-4 items-baseline">
                            <h4 class="text-black text-xl xl:text-2xl">
                                Booking Price:
                            </h4>
                            <p class="text-lg text-primary font-normal" style="font-family: 'Roboto', sans-serif;">{{ $currency }}{{ number_format($pricePerSeatMinor / 100, 2) }}
                                @isset($rideDetailPage->per_seat_label)
                                    {{ $rideDetailPage->per_seat_label }}
                                @endisset
                            </p>
                        </div>
                    </div>

                    <div
                        class="border-t border-gray-300 grid sm:grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-gray-300">
                        <div class="p-4 items-baseline">
                            <h4 class="font-medium text-xl xl:text-2xl text-left text-black font-FuturaMdCnBT">
                                @isset($rideDetailPage->payment_method_label)
                                    {{ $rideDetailPage->payment_method_label }}:
                                @else
                                    Payment method:
                                @endisset
                                <span class="text-primary font-normal text-lg" style="font-family: 'Roboto', sans-serif;">{{ $bookingModeLabel ?? 'N/A' }}</span>
                            </h4>
                        </div>
                        <div class="p-4 items-baseline">
                            <div class="flex flex-wrap items-center gap-3">
                                <h4 class="text-black text-xl xl:text-2xl font-FuturaMdCnBT">
                                    Booking method:
                                    <span class="text-primary font-normal text-lg" style="font-family: 'Roboto', sans-serif;">
                                        {{ $bookingMethodLabel ?? 'N/A' }}
                                    </span>
                                </h4>
                            </div>
                        </div>
                    </div>

                    <div
                        class="border-t border-gray-300 grid sm:grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-gray-300">
                        <div class="p-4 flex items-baseline">
                            <h4 class="text-black text-xl xl:text-2xl font-FuturaMdCnBT">
                                {{ $rideDetailPage->booked_on_column_label ?? 'Booked' }}:
                            </h4>
                            <h4 class="text-primary font-normal text-lg ml-2" style="font-family: 'Roboto', sans-serif;">
                                {{ $ride->seats_total - $ride->seats_available }}
                                {{ ($ride->seats_total - $ride->seats_available) == 1 ? ($rideDetailPage->seat_on_column_label ?? 'seat') : ($rideDetailPage->ride_seat_label ?? 'seats') }}
                            </h4>
                        </div>
                        <div class="p-4">
                            <div class="flex items-center justify-between">
                                <h4 class="text-black text-xl xl:text-2xl font-FuturaMdCnBT">
                                    {{ $rideDetailPage->mobile_seat_fare_label ?? 'Fare' }}:
                                </h4>
                                <p class="">
                                    {{ $currency }}{{ number_format((($pricePerSeatMinor * ($ride->seats_total - $ride->seats_available)) / 100), 2) }}
                                </p>
                            </div>
                            <div class="flex items-center justify-between">
                                <h4 class="text-black text-xl xl:text-2xl font-FuturaMdCnBT">
                                    {{ $rideDetailPage->booking_fee_label ?? 'Booking Fee' }}:
                                </h4>
                                <p class="">
                                    {{ $currency }}
                                </p>
                            </div>
                            <div class="flex items-center justify-between">
                                <h4 class="text-black text-xl xl:text-2xl font-FuturaMdCnBT">
                                    {{ $rideDetailPage->total_amount_label ?? 'Total Amount' }}:
                                </h4>
                                <p class="">
                                    {{ $currency }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg overflow-hidden shadow-3xl mt-4">
                    <h3 class="bg-primary text-white py-2 px-4 text-2xl xl:text-3xl">
                        {{ $rideDetailPage->ride_features_label ?? 'Ride features' }}
                    </h3>
                    <div class="bg-white p-4 space-y-3">
                        @if ($ride->options->isNotEmpty())
                            @foreach ($ride->options as $option)
                                <div class="flex items-center space-x-2">
                                    @if ($option->icon)
                                        <img class="w-7 h-7" src="{{ asset('home_page_icons/' . $option->icon) }}" alt="{{ $option->display_label }}">
                                    @endif
                                    <p>{{ $option->display_label }}</p>
                                    <span class="inline-flex cursor-help w-4 h-4" data-tippy-content="{{ $option->display_description }}">
                                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M22 12C22 17.5228 17.5228 22 12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12ZM12 17.75C12.4142 17.75 12.75 17.4142 12.75 17V11C12.75 10.5858 12.4142 10.25 12 10.25C11.5858 10.25 11.25 10.5858 11.25 11V17C11.25 17.4142 11.5858 17.75 12 17.75ZM12 7C12.5523 7 13 7.44772 13 8C13 8.55228 12.5523 9 12 9C11.4477 9 11 8.55228 11 8C11 7.44772 11.4477 7 12 7Z" fill="#666666"></path></svg>
                                    </span>
                                </div>
                            @endforeach
                        @else
                            <p class="text-gray-500">No ride features selected</p>
                        @endif
                    </div>
                </div>

                @if ($ride->notes)
                    <div class="mt-4 mb-4 rounded-lg px-6 py-3 bg-blue-100 text-gray-600" role="alert">
                        <p class="text-gray-800">Important note from the driver: <span class="text-gray-500">{{ $ride->notes }}</span></p>
                    </div>
                @endif
            </div>

            <div class="col-span-1">
                <div class="space-y-4">
                    <div class="bg-white rounded-lg overflow-hidden shadow-3xl">
                        <h3 class="bg-primary text-white py-2 px-4 text-2xl xl:text-3xl">
                            {{ $rideDetailPage->vehicle_info_label ?? 'Vehicle info' }}
                        </h3>
                        <div class="flex items-start space-x-2 p-4 w-full">
                            @if ($ride->vehicle)
                                <div class="w-20 h-20 rounded-full overflow-hidden bg-gray-200 flex items-center justify-center">
                                    @if ($ride->vehicle->image)
                                        <img class="w-full h-full object-cover rounded-full" src="{{ $ride->vehicle->image }}" alt="">
                                    @else
                                        <svg class="w-10 h-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    @endif
                                </div>
                                <div class="text-center flex-1">
                                    <div class="flex items-center flex-wrap gap-x-2 text-sm text-black">
                                        @if ($ride->vehicle->year)
                                            <p class="text-md">{{ $ride->vehicle->year }}</p>
                                        @endif
                                        <span>|</span>
                                        <p class="text-md">{{ $ride->vehicle->make }}</p>
                                        <span>|</span>
                                        <p class="text-md">{{ $ride->vehicle->model }}</p>
                                        <span>|</span>
                                        @if ($ride->vehicle->color)
                                            <p class="text-md">{{ $ride->vehicle->color }}</p>
                                        @endif
                                    </div>
                                    <p class="font-semibold text-lg text-black text-start">{{ $ride->vehicle->liscense_no }}</p>
                                    @if ($ride->vehicle->vehicle_type)
                                        <p class="text-md">{{ $ride->vehicle->vehicle_type }}</p>
                                    @endif
                                </div>
                            @else
                                <p class="text-gray-500">No vehicle information available</p>
                            @endif
                        </div>
                    </div>

                    <div class="bg-white rounded-lg overflow-hidden shadow-3xl">
                        <h3 class="bg-primary text-white py-2 px-4 text-2xl xl:text-3xl">
                            {{ $rideDetailPage->cancellation_policy_label ?? 'Cancellation policy' }}
                        </h3>
                        <div class="flex items-center space-x-2 p-4 w-full">
                            <div class="flex items-center justify-between w-full">
                                <label class="font-normal text-gray-900 flex space-x-1">
                                    <span class="text-lg">
                                        {{ $cancelationPolicyLabel ?? 'Standard' }}
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>

                    @php
                        $isUpcoming = $ride->departure_at > now();
                        $isUpcomingStatus = in_array($ride->status, ['draft', 'published', 'started']);
                        $isNotBooked = $ride->seats_available == $ride->seats_total;
                        $showEditAndCancel = $isUpcoming && $isUpcomingStatus && $isNotBooked;
                    @endphp

                    @if ($isUpcoming && $ride->status !== 'cancelled')
                        <div class="flex justify-center pt-8 gap-4">
                            @if ($showEditAndCancel)
                                <a href="{{ route('px.post_ride.edit', ['lang' => optional($selectedLanguage)->abbreviation, 'id' => $ride->id]) }}"
                                    class="button-exp-fill w-36">
                                    {{ $rideDetailPage->edit_ride_btn_label ?? 'Edit ride' }}
                                </a>
                                <a id="cancelRideBtn" href="#"
                                    class="inline-flex justify-center rounded bg-red-500 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-red-400 w-36">
                                    {{ $rideDetailPage->cancel_ride_btn_label ?? 'Cancel ride' }}
                                </a>
                            @elseif ($isUpcomingStatus)
                                <a id="cancelRideBtn" href="#"
                                    class="inline-flex justify-center rounded bg-red-500 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-red-400 w-36">
                                    {{ $rideDetailPage->cancel_ride_btn_label ?? 'Cancel ride' }}
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Cancel ride: confirmation modal --}}
    <div id="cancelRideConfirmModal" class="hidden fixed inset-0 z-50 w-screen overflow-y-auto" aria-modal="true" role="dialog">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeBookingModal('cancelRideConfirmModal')"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="relative flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                <div class="relative animate__animated animate__fadeIn z-20 transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border1">
                    <button type="button" onclick="closeBookingModal('cancelRideConfirmModal')" class="absolute top-2 right-2 p-1 rounded-full hover:bg-gray-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="text-center w-full mt-4">
                            <p class="can-exp-p text-center">{{ $rideDetailPage->cancel_ride_confirmation ?? 'Are you sure you want to cancel this ride?' }}</p>
                            <p class="can-exp-p text-center mt-2">This action is irreversible!</p>
                        </div>
                    </div>
                    <div class="px-4 pb-6 pt-4 sm:flex sm:flex-row-reverse sm:px-6 justify-center gap-2">
                        <button type="button" id="cancelRideConfirmYes" class="inline-flex justify-center rounded bg-red-500 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:bg-red-400 shadow-sm w-36">{{ $rideDetailPage->cancel_ride_yes_btn ?? 'Yes, cancel it!' }}</button>
                        <button type="button" onclick="closeBookingModal('cancelRideConfirmModal')" class="inline-flex justify-center rounded bg-[#106BC7] px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:opacity-90 w-36">{{ $rideDetailPage->cancel_ride_no_btn ?? 'No, take me back' }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Cancel ride: result modal --}}
    <div id="cancelRideResultModal" class="hidden fixed inset-0 z-50 w-screen overflow-y-auto" aria-modal="true" role="dialog">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeCancelRideResultModal()"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="relative flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                <div class="relative animate__animated animate__fadeIn z-20 transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
                    <button type="button" onclick="closeCancelRideResultModal()" class="absolute top-2 right-2 p-1 rounded-full hover:bg-gray-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="text-center w-full mt-4">
                            <p class="can-exp-p text-center" id="cancelRideResultMessage"></p>
                        </div>
                    </div>
                    <div class="px-4 pb-6 pt-4 sm:flex sm:flex-row-reverse sm:px-6 justify-center">
                        <button type="button" id="cancelRideResultClose" class="inline-flex w-full justify-center rounded bg-red-500 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-red-400 sm:ml-3 sm:w-24">{{ $siteText['close_btn_text'] ?? 'Close' }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script>
        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.style.display = 'none';
            }
        }

        function closeBookingModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('hidden');
                modal.style.display = 'none';
            }
        }

        function closeErrorModal() {
            const modal = document.querySelector('[aria-labelledby="error-modal-title"]');
            if (modal) modal.style.display = 'none';
        }

        const cancelRideMyRidesUrl = "{{ route('px.my_rides', ['lang' => optional($selectedLanguage)->abbreviation]) }}";

        function closeCancelRideResultModal(redirect) {
            closeBookingModal('cancelRideResultModal');
            if (redirect) {
                window.location.href = cancelRideMyRidesUrl;
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const cancelRideBtn = document.getElementById('cancelRideBtn');
            if (!cancelRideBtn) return;

            cancelRideBtn.addEventListener('click', function(event) {
                event.preventDefault();
                const bookedSeats = {{ $ride->seats_total - $ride->seats_available }};
                
                if (bookedSeats === 0) {
                    const confirmModal = document.getElementById('cancelRideConfirmModal');
                    if (confirmModal) {
                        confirmModal.classList.remove('hidden');
                        confirmModal.style.display = 'block';
                    }
                } else {
                    // If there are booked seats, redirect to cancellation page or show different message
                    alert('Cannot cancel ride with booked seats. Please contact support.');
                }
            });

            const cancelRideConfirmYes = document.getElementById('cancelRideConfirmYes');
            if (cancelRideConfirmYes) {
                cancelRideConfirmYes.addEventListener('click', function() {
                    closeBookingModal('cancelRideConfirmModal');

                    fetch("{{ route('px.my_rides', ['lang' => optional($selectedLanguage)->abbreviation]) }}", {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                                'X-HTTP-Method-Override': 'PUT'
                            },
                            body: JSON.stringify({
                                ride_id: {{ $ride->id }},
                                action: 'cancel'
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            const resultModal = document.getElementById('cancelRideResultModal');
                            const resultMessage = document.getElementById('cancelRideResultMessage');
                            const resultCloseBtn = document.getElementById('cancelRideResultClose');
                            if (!resultModal || !resultMessage) return;

                            if (data.success) {
                                resultMessage.textContent = 'This ride has been cancelled';
                                resultCloseBtn.onclick = function() { closeCancelRideResultModal(true); };
                            } else if (data.error && data.message) {
                                resultMessage.textContent = data.message;
                                resultCloseBtn.onclick = function() { closeCancelRideResultModal(false); };
                            } else {
                                resultMessage.textContent = 'Failed to cancel the ride.';
                                resultCloseBtn.onclick = function() { closeCancelRideResultModal(false); };
                            }
                            resultModal.classList.remove('hidden');
                            resultModal.style.display = 'block';
                        })
                        .catch(function() {
                            const resultModal = document.getElementById('cancelRideResultModal');
                            const resultMessage = document.getElementById('cancelRideResultMessage');
                            const resultCloseBtn = document.getElementById('cancelRideResultClose');
                            if (resultModal && resultMessage) {
                                resultMessage.textContent = 'An error occurred while cancelling the ride.';
                                resultCloseBtn.onclick = function() { closeCancelRideResultModal(false); };
                                resultModal.classList.remove('hidden');
                                resultModal.style.display = 'block';
                            }
                        });
                });
            }
        });
    </script>
@endsection
