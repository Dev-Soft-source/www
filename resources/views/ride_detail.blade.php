@extends('layouts.template')

@section('style')
    <style>
        .chat {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .chat li {
            margin-bottom: 15px;
        }

        .chat li .chat-body p {
            margin: 0;
            color: #777777;
        }

        .panel-body {
            overflow-y: auto;
            height: 350px;
        }

        ::-webkit-scrollbar-track {
            -webkit-box-shadow: #3b82f6;
            background-color: #0369A1;
        }

        ::-webkit-scrollbar {
            width: 10px;
            background-color: #0369A1;
        }

        ::-webkit-scrollbar-thumb {
            -webkit-box-shadow: #3b82f6;
            background-color: #555;
        }
    </style>

    <!-- Scripts -->
        <script>
            window.authUserId = {{ Auth::id() ?? 'null' }};
        window.ride = @json($ride->id); // Pass $ride to JavaScript
        window.passenger = @json($ride->added_by); // Pass $ride to JavaScript
    </script>
        <script src="{{ asset('js/web.js') }}" defer></script>
@endsection

@section('content')

    @if (session('message'))
        <div id="my-modal" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                    <div
                        class="relative relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
                        <button onclick="closeModal()"
                            class="absolute top-2 right-2 p-1 rounded-full hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start justify-center">
                                <!-- <div class="mx-auto flex h-16 w-16 flex-shrink-0 items-center justify-center rounded-full bg-red-500 p-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-12 text-white">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                                                </svg>
                                            </div> -->
                            </div>
                            <div class="mt-3 text-center">
                                <div class="mt-2">
                                    <h3 class="text-3xl text-center font-FuturaMdCnBT font-medium text-gray-900 mb-4"
                                        id="modal-title">{!! session('heading') !!}</h3>
                                </div>
                                <div class="mt-2 w-full">
                                    <p class="text-center can-exp-p">{!! session('message') !!}</p>
                                </div>
                            </div>
                        </div>
                        {{-- <div class="px-4 pb-6 pt-4  sm:flex sm:flex-row-reverse sm:px-6 justify-center">
                        <a href=""
                            class="inline-flex w-full justify-center rounded bg-red-500 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-red-400 sm:ml-3 sm:w-24">Close</a>
                    </div> --}}
                        <div class="px-4 pb-6 pt-4 sm:flex sm:flex-row-reverse sm:px-6 justify-center gap-2">
                            <a href="{{ route('login', ['lang' => app()->getLocale()]) }}"
                                class="inline-flex w-full justify-center rounded bg-blue-500 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-blue-400 sm:w-24">
                                Login
                            </a>
                            <a href="{{ route('signup', ['lang' => app()->getLocale()]) }}" class="button-exp-no-fill">
                                Signup
                            </a>
                            <button onclick="closeModal()"
                                class="inline-flex w-full justify-center rounded bg-red-500 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-red-400 sm:w-24">
                                Close
                            </button>
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
                        <button onclick="closeModal()"
                            class="absolute top-2 right-2 p-1 rounded-full hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start justify-center">
                                <!-- <div class="mx-auto h-16 w-16">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="4" stroke="currentColor" class="w-12 h-12 text-greenXS">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                                </svg>
                                            </div> -->
                            </div>
                            <div class="w-full">
                                <p class="text-center can-exp-p">{!! session('success') !!}</p>
                            </div>
                        </div>
                        <div class="px-4 pb-6 pt-4  sm:flex sm:flex-row-reverse sm:px-6 justify-center">
                            <a href=""
                                class="inline-flex w-full justify-center rounded bg-greenXS px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-greenXS sm:ml-3 sm:w-24">Close</a>
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
                        <button onclick="closeModal()"
                            class="absolute top-2 right-2 p-1 rounded-full hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start justify-center">
                                <!-- <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full sm:mx-0 sm:h-10 sm:w-10 bg-red-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-lg text-white w-8 h-8" viewBox="0 0 16 16">
                                            <path d="M7.005 3.1a1 1 0 1 1 1.99 0l-.388 6.35a.61.61 0 0 1-1.214 0zM7 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0"/>
                                        </svg>
                                    </div> -->
                            </div>
                            <div class="text-center">

                                <div class="w-full">
                                    <p class="text-center can-exp-p">{!! session('failure') !!}</p>
                                </div>
                            </div>
                        </div>
                        <div class="px-4 pb-6 pt-4 sm:flex sm:flex-row-reverse sm:px-6 justify-center">
                            <a href=""
                                class="whitespace-nowrap inline-flex w-full justify-center rounded bg-red-500 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-red-400 sm:ml-3 sm:w-24">Close</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <div id="my-chat-pop-modal" class="hidden relative z-50" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                <div
                    class="relative relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
                    <button onclick="closePopupModal()"
                        class="absolute top-2 right-2 p-1 rounded-full hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start justify-center">
                            <!-- <div
                                            class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full sm:mx-0 sm:h-10 sm:w-10 bg-red-500">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                                class="bi bi-exclamation-lg text-white w-8 h-8" viewBox="0 0 16 16">
                                                <path
                                                    d="M7.005 3.1a1 1 0 1 1 1.99 0l-.388 6.35a.61.61 0 0 1-1.214 0zM7 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0" />
                                            </svg>
                                        </div> -->
                        </div>
                        <div class="text-center">
                            <div class="w-full">
                                <h3 class="text-3xl text-center font-FuturaMdCnBT font-medium text-gray-900 mb-4"
                                    id="modal-title">{!! session('heading') !!}</h3>
                            </div>
                            <div class="mt-2 w-full">
                                <p class="can-exp-p text-center">
                                    @isset($rideDetailPage->chat_error_message)
                                        {{ $rideDetailPage->chat_error_message }} {{ $ride->driver?->first_name }}
                                    @endisset
                                </p>
                            </div>
                        </div>
                    </div>
                    {{-- <div class="px-4 pb-6 pt-4  sm:flex sm:flex-row-reverse sm:px-6 justify-center">
                        <a href=""
                            class="inline-flex w-full justify-center rounded bg-red-500 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-red-400 sm:ml-3 sm:w-24">Close</a>
                    </div> --}}
                    <div class="px-4 pb-6 pt-4 sm:flex sm:flex-row-reverse sm:px-6 justify-center gap-2">
                        <a href="{{ route('login', ['lang' => app()->getLocale()]) }}"
                            class="inline-flex w-full justify-center rounded bg-blue-500 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-blue-400 sm:w-24">
                            {{ $successMessage->popup_login_btn_text ?? 'Login' }}

                        </a>
                        <a href="{{ route('signup', ['lang' => app()->getLocale()]) }}"
                            class="inline-flex w-full justify-center rounded bg-greenXS px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-greenXS sm:w-24">
                            {{ $successMessage->popup_signup_btn_text ?? 'Signup' }}

                        </a>
                        <button onclick="closePopupModal()"
                            class="inline-flex w-full justify-center rounded bg-red-500 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-red-400 sm:w-24">
                            {{ $successMessage->popup_close_btn_text ?? 'Close' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="verified_email_phone" class="hidden relative z-50" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div
                class="relative flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeVerifyModal()">
                </div>
                <div
                    class="relative animate__animated animate__fadeIn z-20 transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
                    <button onclick="closeVerifyModal()"
                        class="absolute top-2 right-2 p-1 rounded-full hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start justify-center">
                            <!-- <div class="mx-auto h-16 w-16">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="4" stroke="currentColor" class="w-12 h-12 text-greenXS">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                            </svg>
                                        </div> -->
                        </div>
                        <div class="w-full">
                            <p class="text-center can-exp-p" id="verify-popup-text"></p>
                        </div>
                    </div>
                    <div class="px-4 pb-6 pt-4  sm:flex sm:flex-row-reverse sm:px-6 justify-center">
                        <a href="#" onclick="closeVerifyModal()"
                            class="inline-flex w-full justify-center rounded bg-greenXS px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-greenXS sm:ml-3 sm:w-24">Close</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto my-10 xl:my-14 px-4 xl:px-0">
        @if (
            $ride->seats -
                $ride->bookings()->where('status', '<>', 3)->where('status', '<>', 4)->whereHas('passenger', function ($query) {
                        $query->whereNull('deleted_at');
                    })->sum('seats') ===
                0)
            <div class="rounded-lg px-6 py-3 bg-blue-100 text-gray-600" role="alert">
                @isset($rideDetailPage->all_seats_booked_label)
                    {{ $rideDetailPage->all_seats_booked_label }}
                @endisset
            </div>
        @endif
        <h1>
            @isset($rideDetailPage->main_heading)
                {{ $rideDetailPage->main_heading }}
            @endisset
        </h1>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-y-4 lg:gap-4">
            <div class="col-span-2">
                <div class="bg-white rounded-lg shadow-3xl">
                    <div class="flex flex-col md:flex-row justify-between px-4 pb-4 md:pb-0">
                        <div class="w-full md:w-2/3 order-2 md:order-1">
                            <div class="relative mt-5 text-left">
                                <div class="flex items-center relative">
                                    <div
                                        class="border-r-2 border-black border-solid absolute h-full left-3 md:left-6 top-2 z-10">
                                        <span
                                            class="bg-primary rounded-full w-7 h-7 -top-[2px] -ml-[13px] absolute flex justify-center items-center">
                                            <img class="w-5 h-5 object-contain"
                                                src="{{ asset('./images/new-21-search-bar-from.png') }}" alt="">
                                        </span>
                                    </div>
                                    <div class="ml-20">
                                        <div class="font-bold text-xl text-black">
                                            @isset($rideDetailPage->from_label)
                                                {{ $rideDetailPage->from_label }}
                                            @endisset
                                        </div>
                                        <div class="text-primary md:mb-4">{{ $ride->rideDetail[0]->departure }}, <br
                                                class="md:hidden"> {{ $ride->pickup }}</div>
                                    </div>
                                </div>

                                <div class="flex items-center relative">
                                    <div
                                        class="border-r-2 border-black border-solid absolute h-0 left-3 md:left-5 top-2 z-10">
                                        <span
                                            class="bg-gray-200 rounded-full w-7 h-7 -top-[6px] -ml-[12px] md:-ml-[9px] absolute flex justify-center items-center">
                                            <img class="w-5 h-5 object-contain"
                                                src="{{ asset('./images/new-21-search-bar-to.png') }}" alt="">
                                        </span>
                                    </div>
                                    <div class="ml-20">
                                        <div class="font-bold text-xl text-black">
                                            @isset($rideDetailPage->to_label)
                                                {{ $rideDetailPage->to_label }}
                                            @endisset
                                        </div>
                                        <div class="text-primary md:mb-4">{{ $ride->rideDetail[0]->destination }}, <br
                                                class="md:hidden"> {{ $ride->dropoff }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- <div class="mt-4 order-1 md:order-2">
                            <p class="whitespace-nowrap font-semibold">
                                {{ \Carbon\Carbon::parse($ride->date)->format('F d, Y') }}
                                @isset($rideDetailPage->at_label)
                                    {{ $rideDetailPage->at_label }}
                                @endisset
                                {{ \Carbon\Carbon::parse($ride->time)->format('h:i A') == '12:00 PM' ? '12 noon' : (\Carbon\Carbon::parse($ride->time)->format('h:i A') == '12:00 AM' ? '12 midnight' : \Carbon\Carbon::parse($ride->time)->format('h:i A')) }}
                            </p>
                        </div> --}}
                        <div class="mt-4 order-1 md:order-2">
                            <p class="whitespace-nowrap font-semibold">
                                {{ \Carbon\Carbon::parse($ride->date)->format('l, F j, Y') }}
                                @isset($rideDetailPage->at_label)
                                    {{ $rideDetailPage->at_label }}
                                @endisset
                                {{ \Carbon\Carbon::parse($ride->time)->format('h:i A') == '12:00 PM' ? '12 noon' : (\Carbon\Carbon::parse($ride->time)->format('h:i A') == '12:00 AM' ? '12 midnight' : \Carbon\Carbon::parse($ride->time)->format('h:i A')) }}
                            </p>
                        </div>
                    </div>
                    <div class="border-t border-gray-300 grid grid-cols-2 divide-x divide-gray-300">
                        <div class="p-4">

                            <p class="text-left font-semibold">
                                @if (auth()->user() &&
                                        $ride->bookings &&
                                        $ride->bookings->where('status', '<>', 3)->where('status', '<>', 4)->where('user_id', auth()->user()->id)->isNotEmpty())
                                    @if ($ride->bookings->where('status', '<>', 3)->where('status', '<>', 4)->where('user_id', auth()->user()->id)->first()->status !== '3')
                                        @if (strtotime($ride->date) > strtotime('today') ||
                                                (strtotime($ride->date) == strtotime('today') && strtotime($ride->time) > strtotime('now')))
                                            <a
                                                href="{{ route('booking.edit', ['lang' => $selectedLanguage->abbreviation,'id' => $ride->bookings->where('status', '<>', 3)->where('status', '<>', 4)->where('user_id', auth()->user()->id)->first()->id]) }}">
                                                @isset($rideDetailPage->seats_left_label)
                                                    {{ $rideDetailPage->seats_left_label }}:
                                                @endisset
                                                {{ intval($ride->seats) -intval($ride->bookings()->where('status', '<>', 3)->where('status', '<>', 4)->whereHas('passenger', function ($query) {$query->whereNull('deleted_at');})->sum('seats')) }}
                                            </a>
                                            </td>
                                        @endif
                                    @endif
                                @elseif (
                                    $ride->seats -
                                        $ride->bookings()->where('status', '<>', 3)->where('status', '<>', 4)->whereHas('passenger', function ($query) {
                                                $query->whereNull('deleted_at');
                                            })->sum('seats') !=
                                        0)
                                    @if ($ride->status !== '2')
                                        <div class="flex">
                                            <a href="{{ route('booking', ['lang' => $selectedLanguage->abbreviation, 'id' => $ride->id, 'rideDetailId' => $ride->rideDetail[0]->id]) }}"
                                                class="">
                                                @isset($rideDetailPage->seats_left_label)
                                                    {{ $rideDetailPage->seats_left_label }}:
                                                @endisset
                                                {{ intval($ride->seats) -intval($ride->bookings()->where('status', '<>', 3)->where('status', '<>', 4)->whereHas('passenger', function ($query) {$query->whereNull('deleted_at');})->sum('seats')) }}
                                            </a>
                                        </div>
                                    @endif
                                @endif


                            </p>
                        </div>
                        <div class="p-4">
                            <p class="font-semibold text-left text-primary">${{ $ride->rideDetail[0]->price }}
                                @isset($rideDetailPage->per_seat_label)
                                    {{ $rideDetailPage->per_seat_label }}
                                @endisset
                            </p>
                        </div>
                    </div>
                    <div
                        class="border-t border-gray-300 grid sm:grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-gray-300">
                        <div class="p-4">
                            <p class="font-medium text-left text-black">
                                @isset($rideDetailPage->payment_method_label)
                                    {{ $rideDetailPage->payment_method_label }}
                                @endisset
                                <span class="text-black font-normal">{{ $ride->payment_method }}</span>
                            </p>
                        </div>
                        <div class="p-4">
                            <div class="w-full">
                                @isset($ride->booking_method->features_setting_id)
                                    <div class="w-full flex items-center justify-center">
                                        <div
                                            class="bg-greenXS hover:bg-greenXS text-white text-base md:text-lg rounded font-FuturaMdCnBT hover:font-FuturaMdCnBT px-5 py-2 border border-greenXS hover:border-greenXS hover:text-white text-center focus:bg-greenXS focus:text-white active:text-white active:bg-greenXS">
                                            {{ $ride->booking_method->name }}
                                        </div>
                                    </div>
                                @endisset
                            </div>

                            {{-- <p class="font-medium text-left text-black">
                                @isset($rideDetailPage->luggage_label)
                                    {{ $rideDetailPage->luggage_label }}
                                @endisset
                                <span class="text-black font-normal">{{ $ride->luggage }}</span></p> --}}
                        </div>
                    </div>
                    <a
                        @if (auth()->user() &&
                                $ride->bookings &&
                                $ride->bookings->where('status', '<>', 3)->where('status', '<>', 4)->where('user_id', auth()->user()->id)->isNotEmpty()) href="{{ route('my_co_passengers', ['lang' => $selectedLanguage->abbreviation, 'departure' => $ride->rideDetail[0]->departure, 'destination' => $ride->rideDetail[0]->destination, 'id' => $ride->id]) }}"
                    @else
                        href="javascript:void(0);" @endif>
                        <div
                            class="border-t border-gray-300 flex flex-col md:flex-row md:items-center justify-start md:space-x-2 p-4">
                            <div>
                                <p class="font-medium md:text-center text-black mr-4">
                                    @isset($rideDetailPage->co_passenger_label)
                                        {{ $rideDetailPage->co_passenger_label }}
                                    @endisset :
                                </p>
                            </div>
                            <div class="flex items-center space-x-2 no-scrollbar overflow-x-auto mt-2 md:mt-0">
                                @foreach ($ride->bookings->where('status', '<>', 3)->where('status', '<>', 4) as $booking)
                                    @for ($i = 0; $i < $booking->seats; $i++)
                                        @if ($booking->passenger)
                                            @if ($booking->passenger->profile_image)
                                                <img class="w-10 h-10 rounded-full"
                                                    src="{{ $booking->passenger->profile_image }}" alt="">
                                            @else
                                                <img class="w-10 h-10 rounded-full"
                                                    src="{{ asset('images/59-booked-seat.png') }}" alt="">
                                            @endif
                                        @endif
                                    @endfor
                                @endforeach
                            </div>
                        </div>
                    </a>
                </div>
                <div class="bg-white rounded-lg overflow-hidden shadow-3xl mt-4">
                    <h3 class="bg-primary text-white py-2 px-4 text-2xl xl:text-3xl">
                        @isset($rideDetailPage->ride_features_label)
                            {{ $rideDetailPage->ride_features_label }}
                        @endisset
                    </h3>
                    <div class="bg-white p-4 space-y-3">
                        <div class="flex items-center space-x-2">
                            {{-- @if ($ride->smoke == $postRidePage->smoking_option1->features_setting_id)
                                @isset($postRidePage->smoking_option1->icon)
                                    <img class="w-7 h-7"
                                        src="{{ asset('home_page_icons/' . $postRidePage->smoking_option1->icon) }}"
                                        alt="">
                                @endisset
                                <p>Smoking: {{ $postRidePage->smoking_option1->name }}</p> --}}
                            @if ($ride->smoke == $postRidePage->smoking_option2->features_setting_id)
                                @isset($postRidePage->smoking_option2->icon)
                                    <img class="w-7 h-7"
                                        src="{{ asset('home_page_icons/' . $postRidePage->smoking_option2->icon) }}"
                                        alt="">
                                @endisset
                                <p>{{ $rideDetailPage->smoking_label }} {{ $postRidePage->smoking_option2->name }}</p>
                            @endif
                        </div>
                        @isset($ride->animal_friendly->features_setting_id)
                            @if ($ride->animal_friendly->features_setting_id !== $postRidePage->animals_option1->features_setting_id)
                                <div class="flex items-center space-x-2">
                                    <img class="w-7 h-7"
                                        @if ($ride->animal_friendly->features_setting_id === $postRidePage->animals_option1->features_setting_id) src="{{ asset('home_page_icons/' . $postRidePage->animals_option1->icon) }}"
                                    @elseif ($ride->animal_friendly->features_setting_id === $postRidePage->animals_option2->features_setting_id) src="{{ asset('home_page_icons/' . $postRidePage->animals_option2->icon) }}"
                                    @elseif ($ride->animal_friendly->features_setting_id === $postRidePage->animals_option3->features_setting_id)
                                        src="{{ asset('home_page_icons/' . $postRidePage->animals_option3->icon) }}" @endif
                                        alt="">
                                    <p>{{ $rideDetailPage->pets_label }} {{ $ride->animal_friendly->name }}</p>
                                </div>
                            @endif
                        @endisset
                        @isset($ride->luggage->features_setting_id)
                            <div class="flex items-center space-x-2">
                                <img class="w-7 h-7"
                                    src="{{ asset('home_page_icons/' . $ride->luggage->icon) }}"
                                    alt="">
                                <p>{{ $rideDetailPage->luggage_label }} {{ $ride->luggage->name }}</p>
                            </div>
                        @endisset
                        @php
                            $features = !empty($ride->features) ? explode('=', $ride->features) : [];
                        @endphp
                        @foreach ($features as $feature)
                            <div class="flex items-start space-x-2">
                                @if ($feature === $postRidePage->features_option11->name)
                                    <img class="w-7 h-7"
                                        src="{{ asset('home_page_icons/' . $postRidePage->features_option11->icon) }}"
                                        alt="">
                                @elseif ($feature === $postRidePage->features_option1->name)
                                    <img class="w-7 h-7"
                                        src="{{ asset('home_page_icons/' . $postRidePage->features_option1->icon) }}"
                                        alt="">
                                @elseif ($feature === $postRidePage->features_option2->name)
                                    <img class="w-7 h-7"
                                        src="{{ asset('home_page_icons/' . $postRidePage->features_option2->icon) }}"
                                        alt="">
                                @elseif ($feature === $postRidePage->features_option9->name)
                                    <img class="w-7 h-7"
                                        src="{{ asset('home_page_icons/' . $postRidePage->features_option9->icon) }}"
                                        alt="">
                                @elseif ($feature === $postRidePage->features_option8->name)
                                    <img class="w-7 h-7"
                                        src="{{ asset('home_page_icons/' . $postRidePage->features_option8->icon) }}"
                                        alt="">
                                @elseif ($feature === $postRidePage->features_option10->name)
                                    <img class="w-7 h-7"
                                        src="{{ asset('home_page_icons/' . $postRidePage->features_option10->icon) }}"
                                        alt="">
                                @elseif ($feature === $postRidePage->features_option3->name)
                                    <img class="w-7 h-7"
                                        src="{{ asset('home_page_icons/' . $postRidePage->features_option3->icon) }}"
                                        alt="">
                                @elseif ($feature === $postRidePage->features_option12->name)
                                    <img class="w-7 h-7"
                                        src="{{ asset('home_page_icons/' . $postRidePage->features_option12->icon) }}"
                                        alt="">
                                @elseif ($feature === $postRidePage->features_option4->name)
                                    <img class="w-7 h-7"
                                        src="{{ asset('home_page_icons/' . $postRidePage->features_option4->icon) }}"
                                        alt="">
                                @elseif ($feature === $postRidePage->features_option5->name)
                                    <img class="w-7 h-7"
                                        src="{{ asset('home_page_icons/' . $postRidePage->features_option5->icon) }}"
                                        alt="">
                                @elseif ($feature === $postRidePage->features_option6->name)
                                    <img class="w-7 h-7"
                                        src="{{ asset('home_page_icons/' . $postRidePage->features_option6->icon) }}"
                                        alt="">
                                @elseif ($feature === $postRidePage->features_option7->name)
                                    <img class="w-7 h-7"
                                        src="{{ asset('home_page_icons/' . $postRidePage->features_option7->icon) }}"
                                        alt="">
                                @elseif ($feature === $postRidePage->features_option13->name)
                                    <img class="w-7 h-7"
                                        src="{{ asset('home_page_icons/' . $postRidePage->features_option13->icon) }}"
                                        alt="">
                                @elseif ($feature === $postRidePage->features_option14->name)
                                    <img class="w-7 h-7"
                                        src="{{ asset('home_page_icons/' . $postRidePage->features_option14->icon) }}"
                                        alt="">
                                @elseif ($feature === $postRidePage->features_option15->name)
                                    <img class="w-7 h-7"
                                        src="{{ asset('home_page_icons/' . $postRidePage->features_option15->icon) }}"
                                        alt="">
                                @elseif ($feature === $postRidePage->features_option16->name)
                                    <img class="w-7 h-7"
                                        src="{{ asset('home_page_icons/' . $postRidePage->features_option16->icon) }}"
                                        alt="">
                                @else
                                    <input id="wi-fi" type="checkbox" name="features[]" value="" checked
                                        disabled
                                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500  focus:ring-2">
                                @endif
                                <p>{{ $feature }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="col-span-1">
                <div class="space-y-4">
                    <div class="bg-white rounded-lg overflow-hidden shadow-3xl">
                        <h3 class="bg-primary text-white py-2 px-4 text-2xl xl:text-3xl">
                            @isset($rideDetailPage->vehicle_info_label)
                                {{ $rideDetailPage->vehicle_info_label }}
                            @endisset
                        </h3>
                        <div class="flex items-start space-x-2 p-4 w-full">
                            @if (auth()->user() &&
                                    $ride->bookings &&
                                    $ride->bookings->where('status', '<>', 3)->where('status', '<>', 4)->where('user_id', auth()->user()->id)->isNotEmpty())
                                <div>
                                    <div class="w-24 h-24 rounded-full overflow-hidden">
                                        <img class="w-full h-full object-cover" src="{{ $ride->car_image }}"
                                            alt="">
                                    </div>
                                </div>
                            @endif
                            <div class="text-center">
                                <div class="flex items-center space-x-2 text-sm text-black">
                                    @if ($ride->year)
                                        <p class="text-sm">{{ $ride->year }}</p>
                                    @endif
                                    {{-- <span>/</span> --}}
                                    <p class="text-sm">{{ $ride->make }}</p>
                                    <p class="text-sm">{{ $ride->model }}</p>
                                    {{-- <span>/</span> --}}
                                    @if ($ride->color)
                                        <p class="text-sm">{{ $ride->color }}</p>
                                    @endif
                                </div>
                                <p class="font-semibold text-lg text-left text-black">{{ $ride->license_no }}</p>
                                @if ($ride->vehicle_type)
                                    <p class="text-sm text-left text-black">{{ $ride->vehicle_type }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg overflow-hidden shadow-3xl">
                        <h3 class="cursor-pointer bg-primary text-white py-2 px-4 text-2xl xl:text-3xl w-full"
                            onclick="window.location.href='{{ route('driver_info', ['lang' => $selectedLanguage->abbreviation, 'id' => $ride->id]) }}'">

                            @if ($ride_cancelled)
                                @isset($rideDetailPage->review_driver_info_label)
                                    {{ $rideDetailPage->review_driver_info_label }}
                                @endisset
                            @else
                                @isset($rideDetailPage->driver_info_label)
                                    {{ $rideDetailPage->driver_info_label }}
                                @endisset
                            @endif

                        </h3>

                        <div class="flex items-center justify-between p-4 w-full">
                            <div class="flex items-center space-x-2">
                                @if (auth()->user() &&
                                        $ride->bookings &&
                                        $ride->bookings->where('status', '<>', 3)->where('status', '<>', 4)->where('user_id', auth()->user()->id)->isNotEmpty())
                                    <div class="w-24 h-24 rounded-full overflow-hidden">
                                        @php
                                            $uuid = $ride->bookings
                                                ->where('user_id', auth()->user()->id)
                                                ->where('status', 1)
                                                ->pluck('uuid')
                                                ->first();
                                        @endphp

                                        @isset($uuid)
                                            <a
                                                href="{{ route('review_driver', ['lang' => $selectedLanguage->abbreviation, 'id' => $uuid]) }}">
                                                @isset($ride->driver?->profile_image)
                                                    <img class="w-full h-full object-cover"
                                                        src="{{ $ride->driver?->profile_image }}" alt="">
                                                @endisset
                                            </a>
                                        @else
                                            <img class="w-full h-full object-cover" src="{{ $ride->driver?->profile_image }}"
                                                alt="">
                                        @endisset
                                    </div>
                                @endif
                                <div class="text-center">
                                    <p class="font-semibold text-lg">
                                        @isset($rideDetailPage->driver_label)
                                            {{ $rideDetailPage->driver_label }}
                                        @endisset
                                        <span>
                                            @if ($ride->driver?->type === '2')
                                                {{ $ride->driver?->last_name }}
                                            @elseif ($ride->driver?->type === '3')
                                                {{ $ride->driver?->first_name }} {{ $ride->driver?->last_name }}
                                            @else
                                                {{ $ride->driver?->first_name }}
                                            @endif
                                            {{-- @if ($ride->driver?->gender && $ride->driver?->gender !== 'Prefer not to say')
                                                    ({{ strtoupper(substr($ride->driver?->gender, 0, 1)) }})
                                                @endif --}}
                                        </span>
                                    </p>
                                    {{-- @php
                                            // Calculate the age based on the driver's date of birth
                                            $dob = \Carbon\Carbon::parse($ride->driver?->dob);
                                            $age = $dob->diffInYears(\Carbon\Carbon::now());
                                        @endphp --}}
                                    {{-- <p class="mb-0">
                                            @isset($rideDetailPage->driver_age_label)
                                                {{ $rideDetailPage->driver_age_label }}
                                            @endisset
                                            {{ $age }}
                                        </p> --}}
                                    <p class="font-semibold text-lg">
                                        @isset($rideDetailPage->passengers_driven_label)
                                            {{ $rideDetailPage->passengers_driven_label }}
                                        @endisset
                                        <span>
                                            {{ $ride->driver?->rides()->where('status', '!=', 2)->where(function ($query) {
                                                    $query->whereDate('rides.date', '<', now()->toDateString())->orWhere(function ($query) {
                                                        $query->whereDate('rides.date', '=', now()->toDateString())->whereTime('rides.time', '<=', now()->toTimeString());
                                                    });
                                                })->get()->flatMap(function ($ride) {
                                                    return $ride->bookings()->pluck('seats');
                                                })->sum() }}
                                        </span>
                                    </p>
                                    <div class="flex items-center gap-4 w-full">
                                        <div class="flex items-center gap-1 w-auto">
                                            @php
                                                $filteredRatings = $ratings
                                                    ->where('status', 1)
                                                    ->where('type', '1')
                                                    ->filter(function ($rating) use ($ride) {
                                                        return $rating->ride && $rating->ride->added_by === $ride->added_by;
                                                    });

                                                $totalAverage = $filteredRatings->avg('average_rating') ?? 0;
                                            @endphp
                                            <p class="font-medium text-black">{{ number_format($totalAverage, 1) }}</p>
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                fill="currentColor" class="w-6 h-6 text-yellow-500">
                                                <path fill-rule="evenodd"
                                                    d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                        @php
                                            $hasVerifiedPhone = App\Models\PhoneNumber::where(
                                                'user_id',
                                                $ride->driver?->id,
                                            )
                                                ->where('verified', 1)
                                                ->exists();
                                        @endphp

                                        <div class="flex items-center gap-2 w-auto">
                                            @if ($hasVerifiedPhone)
                                                <span
                                                    onclick="openVerifyModal('{{ $rideDetailPage->verified_phone ?? 'Phone number is verified' }}')">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                        class="h-5 ">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
                                                    </svg>
                                                </span>
                                            @endif
                                            @if ($ride->driver?->email_verified == '1')
                                                <span
                                                    onclick="openVerifyModal('{{ $rideDetailPage->verified_email ?? 'Email is verified' }}')">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                        class="h-5">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                                                    </svg>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if (strtotime($ride->date) < strtotime('today') ||
                                    (strtotime($ride->date) == strtotime('today') && strtotime($ride->time) < strtotime('now')))
                                @if (auth()->user() &&
                                        $ride->bookings &&
                                        $ride->bookings->where('user_id', auth()->user()->id)->where('status', 1)->isNotEmpty())
                                    @php
                                        // Calculate the difference in days between today and the ride's date
                                        $rideDateTime = new DateTime($ride->date . ' ' . $ride->time);
                                        // Add the leave review days to the ride's DateTime
                                        $reviewDateTime = clone $rideDateTime;
                                        $reviewDateTime->add(new DateInterval('P' . $setting->leave_review_days . 'D'));

                                        // Get current DateTime
                                        $now = new DateTime();

                                        // Check if the current DateTime is before the review DateTime
                                        $reviewButtonVisible = $now < $reviewDateTime;
                                    @endphp
                                    @php
                                        $reviewed = false; // Flag to track if any rating meets the conditions
                                    @endphp
                                    <!-- Loop through ratings associated with this booking -->
                                    @foreach ($ride->ratings as $rating)
                                        @if ($rating->posted_by === auth()->user()->id && $rating->type === '1' && $rating->ride_id === $ride->id)
                                            @php
                                                $reviewed = true; // Set the flag to true if a matching rating is found
                                                $review = $rating;
                                            @endphp
                                            <!-- If at least one matching rating is found, break out of the loop -->
                                            @break
                                        @endif
                                    @endforeach

                                    <!-- Display button based on the flag value -->
                                    @if ($reviewed)
                                        @php
                                            // Format average rating with one decimal place
                                            $formattedAverageRating = $review->average_rating ?? 0;
                                        @endphp
                                        <div class="">
                                            <p class="mr-1">
                                                @isset($rideDetailPage->i_reviewed_label)
                                                    {{ $rideDetailPage->i_reviewed_label }}
                                                @endisset
                                            </p>
                                            <div class="flex">
                                                <img src="{{ asset('assets/11-review-full-star.png') }}"
                                                    class="w-4 h-4 mt-1" alt="">
                                                <p class="ml-1">{{ $formattedAverageRating }}</p>
                                            </div>
                                        </div>
                                    @elseif ($reviewButtonVisible)
                                        @php
                                            $uuid = $ride->bookings
                                                ->where('user_id', auth()->user()->id)
                                                ->where('status', 1)
                                                ->pluck('uuid')
                                                ->first();
                                        @endphp

                                        @isset($uuid)
                                            <!-- Show 'Review' button if no matching rating is found -->
                                            <a href="{{ route('review_driver', ['lang' => $selectedLanguage->abbreviation, 'id' => $uuid]) }}"
                                                class="button-exp-fill me-1">
                                                @isset($rideDetailPage->review_button_label)
                                                    {{ $rideDetailPage->review_button_label }}
                                                @endisset
                                            </a>
                                        @endisset
                                    @endif
                                @endif
                            @endif
                        </div>
                    </div>
                    @if (strtotime($ride->date) > strtotime('today') ||
                            (strtotime($ride->date) == strtotime('today') && strtotime($ride->time) > strtotime('now')))
                        <div class="bg-white rounded-lg overflow-hidden shadow-3xl">
                            <h3 class="bg-primary text-white py-2 px-4 text-2xl xl:text-3xl">
                                @isset($rideDetailPage->driver_chat_heading)
                                    {{ $rideDetailPage->driver_chat_heading }}
                                @endisset
                            </h3>
                            <div class=" p-4 w-full">
                                <p>
                                    @isset($rideDetailPage->driver_chat_label)
                                        {{ $rideDetailPage->driver_chat_label }}
                                    @endisset
                                </p>
                                <div class="flex justify-center mt-4">
                                    @if(Auth::check())
                                        <a href="{{ route('chat', ['lang' => app()->getLocale(), 'departure' => $ride->rideDetail[0]->departure ?? 'unknown', 'destination' => $ride->rideDetail[0]->destination ?? 'unknown', 'id' => $ride->id, 'passenger' => $ride->driver?->id]) }}"
                                            class="bg-greenXS hover:bg-greenXS text-white text-base md:text-lg rounded font-FuturaMdCnBT hover:font-FuturaMdCnBT px-5 py-2 border border-greenXS hover:border-greenXS hover:text-white text-center focus:bg-greenXS focus:text-white active:text-white active:bg-greenXS w-36">
                                            @isset($rideDetailPage->driver_chat_button_label)
                                                {{ $rideDetailPage->driver_chat_button_label }}
                                            @endisset
                                        </a>
                                    @else
                                        <button type="button"
                                            class="bg-greenXS hover:bg-greenXS text-white text-base md:text-lg rounded font-FuturaMdCnBT hover:font-FuturaMdCnBT px-5 py-2 border border-greenXS hover:border-greenXS hover:text-white text-center focus:bg-greenXS focus:text-white active:text-white active:bg-greenXS w-36"
                                            onclick="togglePopupModal1()">
                                            @isset($rideDetailPage->driver_chat_button_label)
                                                {{ $rideDetailPage->driver_chat_button_label }}
                                            @endisset
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- <div class="bg-white rounded-lg overflow-hidden shadow-3xl {{isset($ride->booking_type) && $ride->booking_type=='Firm cancellation'?'border-4 border-red-500':'' }}">
                        <h3 class="bg-primary text-white py-2 px-4 text-2xl xl:text-3xl relative">
                            Cancellation policy
                            @if (isset($ride->booking_type) && $ride->booking_type == 'Firm cancellation')
                            <div class="sups inline-flex">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle-fill text-black peer" viewBox="0 0 16 16">
                                    <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                                </svg>
                                <div
                                  class="absolute tooltip payment_tooltiptext_position top-8 left-0 group-hover:flex hidden peer-hover:flex"
                                >
                                    <div
                                        role="tooltip"
                                        class="absolute after:left-[6.8rem] md:after:left-[6.8rem] payment_tooltiptext -left-1/2 -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-blue-500  border border-blue-500 text-gray-600 rounded tooltip_width sm:w-[25rem] md:w-[30rem] lg:w-72 xl:w-[23rem] 2xl:w-[25rem] px-4"
                                    >
                                        <p class="text-white font-semibold text-start text-sm lg:text-base">

                                            {{ $rideDetailPage->cancellation_policy_tooltip ?? '' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </h3>
                        <div class=" p-4 w-full">
                            <p class="text-left">
                                {{ $ride->booking_type }}
                            </p>
                        </div>
                    </div> --}}
                    <div
                        class="bg-white rounded-lg shadow-3xl {{ isset($ride->booking_type) && $ride->booking_type == 'Firm cancellation' ? 'border-4 border-red-500' : '' }}">
                        <h3 class="bg-primary text-white py-2 px-4 text-2xl xl:text-3xl relative">
                            {{-- Cancellation policy --}}
                            @isset($rideDetailPage->cancellation_policy)
                                {{ $rideDetailPage->cancellation_policy }}
                            @endisset
                            @if (isset($ride->booking_type) && $ride->booking_type == 'Firm cancellation')
                                <div class="relative inline-flex group">
                                    <!-- Info Icon -->
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        fill="currentColor"
                                        class="bi bi-info-circle-fill text-gray-800 hover:text-gray-800 transition-colors cursor-pointer"
                                        viewBox="0 0 16 16">
                                        <path
                                            d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
                                    </svg>

                                    <!-- Tooltip -->
                                    <div
                                        class="absolute z-50 hidden group-hover:block w-64 bottom-full left-1/2 transform -translate-x-1/2 mb-2">
                                        <div class="bg-primary text-white rounded-lg shadow-lg p-4 relative">
                                            <!-- Tooltip arrow -->
                                            <div
                                                class="absolute top-full left-1/2 -translate-x-1/2 w-4 h-4 bg-primary text-white transform rotate-45 -mt-2">
                                            </div>

                                            <p class="text-white text-sm lg:text-base font-medium"
                                                style="font-family: 'Roboto', sans-serif;">
                                                {{ $rideDetailPage->cancellation_policy_tooltip ?? 'Cancellation policy information' }}
                                                @if (isset($rideDetailPage->cancellation_policy_tooltip_url))
                                                    @php
                                                        $url = $rideDetailPage->cancellation_policy_tooltip_url;
                                                        if (!Str::startsWith($url, ['http://', 'https://'])) {
                                                            $url = 'https://' . $url;
                                                        }
                                                    @endphp
                                                    <a target="_blank" href="{{ $url }}"
                                                        class="text-red-500 hover:text-red-500 text-sm lg:text-base font-medium block mt-1">
                                                        {{ $rideDetailPage->cancellation_policy_tooltip_url }}
                                                    </a>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </h3>
                        <div class=" p-4 w-full">
                            <p class="text-left">
                                @if (isset($ride->booking_type) && $ride->booking_type == 'Standard cancellation')
                                    <a href="{{ route('cancellation_policy', ['lang' => $selectedLanguage->abbreviation, 'type' => 'standard']) }}"
                                        class="font-bold text-black no-underline hover:no-underline" target="_blank">
                                        {{ $ride->booking_type }}
                                    </a>
                                @elseif(isset($ride->booking_type) && $ride->booking_type == 'Firm cancellation')
                                    <a href="{{ route('firm_cancellation_policy', ['lang' => $selectedLanguage->abbreviation, 'type' => 'firm']) }}"
                                        class="font-bold text-black no-underline hover:no-underline" target="_blank">
                                        {{ $ride->booking_type }}
                                    </a>
                                @else
                                    {{ $ride->booking_type ?? '' }}
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 w-full justify-center lg:justify-center">
                        @if (auth()->user() &&
                                $ride->bookings &&
                                $ride->bookings->where('status', '<>', 3)->where('status', '<>', 4)->where('user_id', auth()->user()->id)->isNotEmpty())
                            <div class="grid grid-cols-1 gap-4">
                                @foreach ($ride->bookings->where('status', '<>', 3)->where('status', '<>', 4)->where('user_id', auth()->user()->id) as $booking)
                                    <div class="w-full flex items-center justify-end">
                                        @if ($booking->status !== '3')
                                            @if (strtotime($ride->date) > strtotime('today') ||
                                                    (strtotime($ride->date) == strtotime('today') && strtotime($ride->time) > strtotime('now')))
                                                <td class="border border-slate-300 px-4 py-2 text-center">
                                                    <a href="{{ route('booking.edit', ['lang' => $selectedLanguage->abbreviation, 'id' => $booking->id]) }}"
                                                        class="button-exp-fill whitespace-nowrap me-1">
                                                        @isset($rideDetailPage->edit_button_actions_label)
                                                            {{ $rideDetailPage->edit_button_actions_label }}
                                                        @endisset
                                                    </a>
                                                </td>
                                            @endif
                                        @endif
                                    </div>
                                @endforeach
                                {{-- <div class="col-span-2 bg-white rounded-lg overflow-hidden shadow-3xl mt-4"> --}}

                                {{-- <h3 class="w-full">
                                        @isset($rideDetailPage->booking_table_heading)
                                            {{ $rideDetailPage->booking_table_heading }}
                                        @endisset
                                    </h3> --}}

                                {{--  <div class="bg-white p-4 space-y-3">
                                        <table class="border-collapse border border-slate-400 w-full">
                                            <thead>
                                                <tr>
                                                    <th class="border border-slate-300 px-4 py-2">
                                                        @isset($rideDetailPage->passenger_column_label)
                                                            {{ $rideDetailPage->passenger_column_label }}
                                                        @endisset
                                                    </th>
                                                    <th class="border border-slate-300 px-4 py-2">
                                                        @isset($rideDetailPage->seat_booked_column_label)
                                                            {{ $rideDetailPage->seat_booked_column_label }}
                                                        @endisset
                                                    </th>
                                                    <th class="border border-slate-300 px-4 py-2">
                                                        @isset($rideDetailPage->total_cost_column_label)
                                                            {{ $rideDetailPage->total_cost_column_label }}
                                                        @endisset
                                                    </th>
                                                    <th class="border border-slate-300 px-4 py-2">
                                                        @isset($rideDetailPage->booked_on_column_label)
                                                            {{ $rideDetailPage->booked_on_column_label }}
                                                        @endisset
                                                    </th>
                                                    @if ($ride->booking_method->features_setting_id === $postRidePage->booking_option2->features_setting_id)
                                                        <th class="border border-slate-300 px-4 py-2">
                                                            @isset($rideDetailPage->status_column_label)
                                                                {{ $rideDetailPage->status_column_label }}
                                                            @endisset
                                                        </th>
                                                    @endif
                                                    @if (strtotime($ride->date) > strtotime('today') || (strtotime($ride->date) == strtotime('today') && strtotime($ride->time) > strtotime('now')))
                                                        <th class="border border-slate-300 px-4 py-2">
                                                            @isset($rideDetailPage->actions_column_label)
                                                                {{ $rideDetailPage->actions_column_label }}
                                                            @endisset
                                                        </th>
                                                    @endif
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($ride->bookings->where('user_id', auth()->user()->id)->where('status', '!=', '4') as $booking)
                                                    @if ($booking->passenger)
                                                        <tr>
                                                            <td class="border border-slate-300 px-4 py-2 text-center">{{ $booking->passenger->first_name }}
                                                                {{ $booking->passenger->last_name }}</td>
                                                            <td class="border border-slate-300 px-4 py-2 text-center flex items-center space-x-3">
                                                                @for ($i = 1; $i <= $booking->seats; $i++)
                                                                    <div class="relative">
                                                                        <img src="{{ asset('assets/seat-hover-1.png') }}"
                                                                            class="w-10 h-10 mt-0.5 cursor-pointer" alt="">
                                                                        <span
                                                                            class="absolute left-4 top-3 text-green-300">{{ $i }}</span>
                                                                    </div>
                                                                @endfor
                                                            </td>
                                                            <td class="border border-slate-300 px-4 py-2 text-center">
                                                                {{ $booking->seats * $booking->ride->price + $booking->booking_credit }}</td>
                                                            <td class="border border-slate-300 px-4 py-2 text-center">{{ $booking->booked_on }}</td>
                                                            @if ($ride->booking_method->features_setting_id === $postRidePage->booking_option2->features_setting_id)
                                                                @if ($booking->status === '0')
                                                                    <td class="border border-slate-300 px-4 py-2 text-center">
                                                                        @isset($rideDetailPage->booking_requested_status_label)
                                                                            {{ $rideDetailPage->booking_requested_status_label }}
                                                                        @endisset
                                                                    </td>
                                                                @elseif ($booking->status === '1')
                                                                    <td class="border border-slate-300 px-4 py-2 text-center">
                                                                        @isset($rideDetailPage->seat_booked_status_label)
                                                                            {{ $rideDetailPage->seat_booked_status_label }}
                                                                        @endisset
                                                                    </td>
                                                                @elseif ($booking->status === '3')
                                                                    <td class="border border-slate-300 px-4 py-2 text-center">
                                                                        @isset($rideDetailPage->booking_denied_status_label)
                                                                            {{ $rideDetailPage->booking_denied_status_label }}
                                                                        @endisset
                                                                    </td>
                                                                @endif
                                                            @endif

                                                        </tr>
                                                    @endif
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>  --}}
                                {{-- </div> --}}
                            </div>
                            @foreach ($ride->bookings->where('status', '<>', 3)->where('status', '<>', 4)->where('user_id', auth()->user()->id) as $booking)
                                @if ($booking->status !== '3')
                                    @if (strtotime($ride->date) > strtotime('today') ||
                                            (strtotime($ride->date) == strtotime('today') && strtotime($ride->time) > strtotime('now')))
                                        {{-- @php
                                            if ($cancelSetting) {
                                                // Calculate the cancellation deadline
                                                $cancellationDeadline = strtotime('+' . $cancelSetting->passenger_cancel_hours . ' hours', strtotime($booking->booked_on));
                                            }
                                        @endphp --}}
                                        <div class="flex justify-end">
                                            <a @if ($postRidePage->cancellation_policy_label2 == $booking->type) href="javascript:void(0);" onclick="toggleModalCard('card-modal', '{{ $booking->id }}', '{{ $selectedLanguage->abbreviation }}')"
                                                @else
                                                    href="{{ route('booking.cancel', ['lang' => $selectedLanguage->abbreviation, 'id' => $booking->id]) }}" @endif
                                                class="">
                                                <label for="instant-booking"
                                                    class="inline-flex items-center justify-center space-x-3 w-fit button-exp-fill rounded cursor-pointer peer-checked:border-blue-500 peer-checked:border-2 peer-checked:text-blue-500 hover:border-2 hover:border-blue-500">
                                                    <span class="font-medium text-xl">
                                                        Cancel booking
                                                    </span>
                                                </label>
                                            </a>
                                        </div>
                                        {{-- @if (isset($cancelSetting) && $cancelSetting && isset($cancellationDeadline))
                                            @if (strtotime('now') < $cancellationDeadline)
                                            @endif
                                        @endif --}}
                                    @endif
                                @endif
                            @endforeach
                        @elseif (
                            $ride->seats -
                                $ride->bookings()->where('status', '<>', 3)->where('status', '<>', 4)->whereHas('passenger', function ($query) {
                                        $query->whereNull('deleted_at');
                                    })->sum('seats') !=
                                0)
                            @if ($ride->status !== '2')
                                <div class="flex justify-end">
                                    <a href="{{ route('booking', ['lang' => $selectedLanguage->abbreviation, 'id' => $ride->id, 'rideDetailId' => $ride->rideDetail[0]->id]) }}"
                                        class="">
                                        <label for="instant-booking"
                                            class="inline-flex items-center justify-center space-x-3 w-fit pr-8 button-exp-fill rounded cursor-pointer peer-checked:border-blue-500 peer-checked:border-2 peer-checked:text-blue-500 hover:border-2 hover:border-blue-500">
                                            @isset($ride->booking_method->features_setting_id)
                                                @if ($ride->booking_method->features_setting_id === $postRidePage->booking_option1->features_setting_id)
                                                    <img class="w-10 h-10 rounded-full"
                                                        src="{{ asset('home_page_icons/' . $postRidePage->booking_option1->icon) }}"
                                                        alt="">
                                                @elseif ($ride->booking_method->features_setting_id === $postRidePage->booking_option2->features_setting_id)
                                                    <img class="w-10 h-10 rounded-full"
                                                        src="{{ asset('home_page_icons/' . $postRidePage->booking_option2->icon) }}"
                                                        alt="">
                                                @endif
                                                <span class="font-medium text-xl">
                                                    {{ $ride->booking_method->name }}
                                                </span>
                                            @endisset
                                        </label>
                                    </a>
                                </div>
                            @endif
                        @endif
                    </div>
                    {{-- @if (auth()->check() && count($ride->bookings->where('status', 1)->where('user_id', '!=', auth()->user()->id)) > 0)
                        <div class="bg-white rounded-lg overflow-hidden shadow-3xl">
                            <h3 class="bg-primary text-white py-2 px-4 text-2xl xl:text-3xl">My co-passengers</h3>
                            <a href="{{ route('my_passengers', ['lang' => $selectedLanguage->abbreviation, 'departure' => $ride->departure, 'destination' => $ride->destination, 'id' => $ride->id]) }}">
                                <div class="space-y-4 p-4">
                                    @foreach ($ride->bookings->where('status', 1)->where('user_id', '!=', auth()->user()->id) as $booking)
                                        @if ($booking->passenger)
                                            <div class="flex items-center space-x-2 w-full">
                                                <img class="w-12 h-12 rounded-full object-cover" src="{{ $booking->passenger->profile_image }}" alt="">
                                                <div class="text-center">
                                                    <p class="font-semibold leading-4 text-base mb-0 ">{{ $booking->passenger->first_name }} {{ $booking->passenger->last_name }}</p>
                                                    <div class="flex items-center space-x-2">
                                                        @php
                                                            // Calculate the age based on the driver's date of birth
                                                            $dob = \Carbon\Carbon::parse($booking->passenger->dob);
                                                            $age = $dob->diffInYears(\Carbon\Carbon::now());
                                                        @endphp
                                                        <p class="text-gray-700 leading-4 mt-2 text-base">Age: <span>{{ $age }}</span></p>
                                                        <p class="text-gray-700 leading-4 mt-2 text-base">Gender: <span>{{ $booking->passenger->gender }}</span></p>
                                                        @php
                                                            $user_id = $booking->passenger->id;

                                                            // Assuming $ratings is a collection
                                                            $filteredRatings = $ratings->where('status', 1)->where('type', '2')->filter(function ($rating) use ($user_id) {
                                                                return $rating->booking->user_id === $user_id;
                                                            });

                                                            // Filter out non-numeric values for the columns and then calculates the average
                                                            $VehicleRatings = $filteredRatings->filter(function ($rating) {
                                                                return is_numeric($rating->vehicle_condition);
                                                            });
                                                            $VehicleCondition = $VehicleRatings->avg('vehicle_condition');
                                                            $ConsciousRatings = $filteredRatings->filter(function ($rating) {
                                                                return is_numeric($rating->conscious);
                                                            });
                                                            $conscious = $ConsciousRatings->avg('conscious');
                                                            $Comfort = $filteredRatings->filter(function ($rating) {
                                                                return is_numeric($rating->comfort);
                                                            });
                                                            $comfort = $Comfort->avg('comfort');
                                                            $Communication = $filteredRatings->filter(function ($rating) {
                                                                return is_numeric($rating->communication);
                                                            });
                                                            $communication = $Communication->avg('communication');
                                                            $Attitude = $filteredRatings->filter(function ($rating) {
                                                                return is_numeric($rating->attitude);
                                                            });
                                                            $attitude = $Attitude->avg('attitude');
                                                            $Hygiene = $filteredRatings->filter(function ($rating) {
                                                                return is_numeric($rating->hygiene);
                                                            });
                                                            $hygiene = $Hygiene->avg('hygiene');
                                                            $Respect = $filteredRatings->filter(function ($rating) {
                                                                return is_numeric($rating->respect);
                                                            });
                                                            $respect = $Respect->avg('respect');
                                                            $Safety = $filteredRatings->filter(function ($rating) {
                                                                return is_numeric($rating->safety);
                                                            });
                                                            $safety = $Safety->avg('safety');
                                                            $Timeliness = $filteredRatings->filter(function ($rating) {
                                                                return is_numeric($rating->timeliness);
                                                            });
                                                            $timeliness = $Timeliness->avg('timeliness');

                                                            // Calculate averages for each rating category
                                                            $validAverages = [];
                                                            $validAverages[] = $conscious;
                                                            $validAverages[] = $comfort;
                                                            $validAverages[] = $communication;
                                                            $validAverages[] = $attitude;
                                                            $validAverages[] = $hygiene;
                                                            $validAverages[] = $respect;
                                                            $validAverages[] = $safety;
                                                            $validAverages[] = $timeliness;

                                                            // Filter out non-empty averages
                                                            $validAverages = array_filter($validAverages, function ($average) {
                                                                return !is_null($average);
                                                            });

                                                            // Calculate total average
                                                            $totalAverage = count($validAverages) > 0 ? array_sum($validAverages) / count($validAverages) : null;
                                                        @endphp
                                                        <p class="text-gray-700 leading-4 mt-2 text-base">Review: <span>{{ number_format($totalAverage, 1) }}</span></p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </a>
                        </div>
                    @elseif (count($ride->bookings->where('status', 1)) > 0)
                        <div class="bg-white rounded-lg overflow-hidden shadow-3xl">
                            <h3 class="bg-primary text-white py-2 px-4 text-2xl xl:text-3xl">My co-passengers</h3>
                            <a href="{{ route('my_passengers', ['lang' => $selectedLanguage->abbreviation, 'departure' => $ride->departure, 'destination' => $ride->destination, 'id' => $ride->id]) }}">
                                <div class="space-y-4 p-4">
                                    @foreach ($ride->bookings->where('status', 1) as $booking)
                                        @if ($booking->passenger)
                                            <div class="flex items-center space-x-2 w-full">
                                                <img class="w-12 h-12 rounded-full object-cover" src="{{ $booking->passenger->profile_image }}" alt="">
                                                <div class="text-center">
                                                    <p class="font-semibold leading-4 text-base mb-0 ">{{ $booking->passenger->first_name }} {{ $booking->passenger->last_name }}</p>
                                                    <div class="flex items-center space-x-2">
                                                        @php
                                                            // Calculate the age based on the driver's date of birth
                                                            $dob = \Carbon\Carbon::parse($booking->passenger->dob);
                                                            $age = $dob->diffInYears(\Carbon\Carbon::now());
                                                        @endphp
                                                        <p class="text-gray-700 leading-4 mt-2 text-base">Age: <span>{{ $age }}</span></p>
                                                        <p class="text-gray-700 leading-4 mt-2 text-base">Gender: <span>{{ $booking->passenger->gender }}</span></p>
                                                        @php
                                                            $user_id = $booking->passenger->id;

                                                            // Assuming $ratings is a collection
                                                            $filteredRatings = $ratings->where('status', 1)->where('type', '2')->filter(function ($rating) use ($user_id) {
                                                                return $rating->booking->user_id === $user_id;
                                                            });

                                                            // Filter out non-numeric values for the columns and then calculates the average
                                                            $VehicleRatings = $filteredRatings->filter(function ($rating) {
                                                                return is_numeric($rating->vehicle_condition);
                                                            });
                                                            $VehicleCondition = $VehicleRatings->avg('vehicle_condition');
                                                            $ConsciousRatings = $filteredRatings->filter(function ($rating) {
                                                                return is_numeric($rating->conscious);
                                                            });
                                                            $conscious = $ConsciousRatings->avg('conscious');
                                                            $Comfort = $filteredRatings->filter(function ($rating) {
                                                                return is_numeric($rating->comfort);
                                                            });
                                                            $comfort = $Comfort->avg('comfort');
                                                            $Communication = $filteredRatings->filter(function ($rating) {
                                                                return is_numeric($rating->communication);
                                                            });
                                                            $communication = $Communication->avg('communication');
                                                            $Attitude = $filteredRatings->filter(function ($rating) {
                                                                return is_numeric($rating->attitude);
                                                            });
                                                            $attitude = $Attitude->avg('attitude');
                                                            $Hygiene = $filteredRatings->filter(function ($rating) {
                                                                return is_numeric($rating->hygiene);
                                                            });
                                                            $hygiene = $Hygiene->avg('hygiene');
                                                            $Respect = $filteredRatings->filter(function ($rating) {
                                                                return is_numeric($rating->respect);
                                                            });
                                                            $respect = $Respect->avg('respect');
                                                            $Safety = $filteredRatings->filter(function ($rating) {
                                                                return is_numeric($rating->safety);
                                                            });
                                                            $safety = $Safety->avg('safety');
                                                            $Timeliness = $filteredRatings->filter(function ($rating) {
                                                                return is_numeric($rating->timeliness);
                                                            });
                                                            $timeliness = $Timeliness->avg('timeliness');

                                                            // Calculate averages for each rating category
                                                            $validAverages = [];
                                                            $validAverages[] = $conscious;
                                                            $validAverages[] = $comfort;
                                                            $validAverages[] = $communication;
                                                            $validAverages[] = $attitude;
                                                            $validAverages[] = $hygiene;
                                                            $validAverages[] = $respect;
                                                            $validAverages[] = $safety;
                                                            $validAverages[] = $timeliness;

                                                            // Filter out non-empty averages
                                                            $validAverages = array_filter($validAverages, function ($average) {
                                                                return !is_null($average);
                                                            });

                                                            // Calculate total average
                                                            $totalAverage = count($validAverages) > 0 ? array_sum($validAverages) / count($validAverages) : null;
                                                        @endphp
                                                        <p class="text-gray-700 leading-4 mt-2 text-base">Review: <span>{{ number_format($totalAverage, 1) }}</span></p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </a>
                        </div>
                    @endif --}}
                </div>
            </div>
        </div>

        {{-- <div class="mt-4 mb-4 rounded-lg px-6 py-3 bg-blue-100 text-gray-600" role="alert">
            <p class="text-gray-800">
                @isset($rideDetailPage->driver_note_label)
                    {{ $rideDetailPage->driver_note_label }}
                @endisset
                <span class="text-gray-500">{{ $ride->notes }}</span>
            </p>
        </div> --}}
    </div>
    <div class="hidden overflow-x-hidden overflow-y-auto fixed inset-0 z-50 outline-none focus:outline-none justify-center items-center"
        id="chat-modal">
        <div class="relative w-auto my-6 mx-auto max-w-2xl">
            <!--content-->
            <div class="relative rounded-lg shadow border-0 flex flex-col w-full bg-white outline-none focus:outline-none">
                <!--header-->
                <div class="flex items-center justify-between p-4 border-b rounded-t">
                    <h3 class="can-edu-h3 mb-0">Select website language</h3>
                    <div>
                        <button type="button"
                            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-full border border-primary text-sm p-1 ml-auto inline-flex items-center"
                            data-modal-hide="defaultModal" onclick="toggleModal('chat-modal')">
                            <svg aria-hidden="true" class="w-5 h-5 text-primary" fill="currentColor" viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                    </div>
                </div>
                <!-- Modal body -->
                <div id="ridesharing_app" class="relative z-50" aria-labelledby="modal-title" role="dialog"
                    aria-modal="true">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                        <div
                            class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                            <div
                                class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-7xl">
                                <div class="bg-white w-full">
                                    <div class="flex items-center justify-between border-b pb-2 bg-secondary px-4 py-2">
                                        <h1 class="mb-0 text-white" id="modal-title">
                                            {{ $rideDetailPage->driver_chat_with ?? 'Chat with' }}
                                            {{ $ride->driver?->first_name }}</h1>
                                        <div>
                                            <button type="button"
                                                class="text-gray-100 bg-transparent rounded-full border border-white text-sm p-1 ml-auto inline-flex items-center"
                                                data-modal-hide="defaultModal" onclick="toggleModal('chat-modal')">
                                                <svg aria-hidden="true" class="w-3 h-3 text-gray-100" fill="currentColor"
                                                    viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd"
                                                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"></path>
                                                </svg>
                                                <span class="sr-only">Close modal</span>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                                        <div class="py-3 text-center sm:text-left">
                                            <div class="mt-2">
                                                <div class="panel-body">
                                                    {{-- <div style="font-weight:bold;color:#2563eb;margin-bottom:4px;">Ride Details</div> --}}
                                                    <chat-messages logged_in_user_id="{{ Auth::user()->id ?? null }}"
                                                        :messages="chats"
                                                        empty_chat_placeholder="{{ $rideDetailPage->chat_error_message }}"></chat-messages>
                                                </div>

                                            </div>
                                        </div>

                                        @php
                                            $allow_chat = false;
                                            $currentDateTime = now();
                                            $rideDateTime = \Carbon\Carbon::parse($ride->date . ' ' . $ride->time);
                                            $hoursDifference = $currentDateTime->diffInHours($rideDateTime);
                                            $allow_chat = false;
                                            if (auth()->user()) {
                                                $user_id = auth()->user()->id;
                                                $user = \App\Models\User::whereId($user_id)->first();
                                                $contact_limit = \App\Models\SiteSetting::value('user_per_day_limit');
                                                $contact_count = \App\Models\UserMessageCount::where(
                                                    'user_id',
                                                    $user->id,
                                                )
                                                    ->whereBetween('created_at', [
                                                        \Carbon\Carbon::today(),
                                                        \Carbon\Carbon::tomorrow(),
                                                    ])
                                                    ->first();

                                                if (
                                                    is_null($contact_count) ||
                                                    $contact_count->user_inbox_count < $contact_limit
                                                ) {
                                                    $allow_chat = true;
                                                } elseif (
                                                    in_array(
                                                        $ride->driver?->id,
                                                        explode(',', $contact_count->contact_user_id),
                                                    )
                                                ) {
                                                    $allow_chat = true;
                                                }
                                            }

                                        @endphp
                                        @if ($rideDateTime < $currentDateTime)
                                            @if ($hoursDifference <= 48)
                                                <div class="panel-footer">
                                                    <chat-form v-on:message-sent-event="addMessage"
                                                        allow_chat="{{ $allow_chat }}" :ride_id="{{ $ride->id }}"
                                                        :user="{{ auth()->user() }}"></chat-form>
                                                </div>
                                            @endif
                                        @elseif ($rideDateTime >= $currentDateTime)
                                            <div class="panel-footer">
                                                <chat-form v-on:message-sent-event="addMessage"
                                                    allow_chat="{{ $allow_chat }}" :ride_id="{{ $ride->id }}"
                                                    :user="{{ auth()->user() }}"
                                                    type_message_placeholder="Please avoid sharing any contact details such as phone numbers, email addresses, or website links. Do not offer or agree to communicate or arrange payments outside the ProximaRide platform."></chat-form>
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
    <div class="hidden opacity-25 fixed inset-0 z-40 bg-black" id="chat-modal-backdrop"></div>
    <div class="hidden overflow-x-hidden overflow-y-auto fixed inset-0 z-50 outline-none focus:outline-none justify-center items-center"
        id="modal-id3">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                <!--content-->
                <div
                    class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg w-full">
                    <button type="button" onclick="toggleModal1('modal-id3')"
                        class="absolute top-3 right-3 text-gray-400 hover:text-gray-500">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    <!--body-->
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="mt-4 text-center">
                            <div class="mt-2 w-full">
                                <p class="can-exp-p text-center modal-message"></p>
                            </div>
                        </div>
                    </div>
                    <!--footer-->
                    <div class="px-4 pb-6 pt-4  sm:flex sm:flex-row-reverse sm:px-6 justify-center">
                        <button
                            class="inline-flex w-full justinline-flex justify-center rounded bg-red-500 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-red-400 sm:ml-3 sm:w-24"
                            type="button" onclick="toggleModal1('modal-id3')">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="hidden overflow-x-hidden overflow-y-auto fixed inset-0 z-50 outline-none focus:outline-none justify-center items-center"
        id="card-modal">
        <div class="relative h-screen my-6 mx-auto flex items-center justify-center w-full">
            <!--content-->
            <div
                class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                <button type="button" onclick="toggleModalCard('card-modal')"
                    class="absolute top-3 right-3 text-gray-400 hover:text-gray-500">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start justify-center">
                        <!-- <div
                                        class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full sm:mx-0 sm:h-10 sm:w-10 bg-red-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                            class="bi bi-exclamation-lg text-white w-8 h-8" viewBox="0 0 16 16">
                                            <path
                                                d="M7.005 3.1a1 1 0 1 1 1.99 0l-.388 6.35a.61.61 0 0 1-1.214 0zM7 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0" />
                                        </svg>
                                    </div> -->
                    </div>
                    <div class="text-center sm:ml-4 sm:mt-0 sm:text-left">
                        <div class="">
                            <h3 class="text-3xl text-center font-FuturaMdCnBT font-medium text-gray-900 mb-4"
                                id="modal-title">
                                {{ $rideDetailPage->firm_cancellation_confirm_poup_heading ?? 'Heading' }}</h3>
                        </div>
                        <div class="mt-2 w-full">
                            <p class="can-exp-p text-center">
                                {{ $rideDetailPage->firm_cancellation_confirm_poup_text ?? 'This ride has the Firm cancellation policy. While you can cancel your booking, you will not get any refunds. Are you sure you want to cancel? This is irreversible' }}
                                <br>
                                {{ $rideDetailPage->firm_cancellation_confirm_poup_sub_text ?? '' }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="px-4 pb-6 pt-4 flex items-center space-x-2 sm:space-x-4 sm:px-6 justify-center">
                    <a id="delete-card-link" href="#"
                        class="inline-flex w-full justify-center rounded bg-red-500 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white whitespace-nowrap hover:text-white hover:shadow-lg shadow-sm hover:bg-red-400 sm:ml-3 sm:w-24">{{ $rideDetailPage->firm_cancellation_confirm_poup_yes_label ?? 'Yes' }}</a>
                    <button type="button" onclick="toggleModalCard('card-modal')"
                        class="button-exp-fill sm:w-24">{{ $rideDetailPage->firm_cancellation_confirm_poup_no_label ?? 'No' }}</button>
                </div>
            </div>
        </div>
    </div>
    <div class="hidden opacity-25 fixed inset-0 z-40 bg-black" id="card-modal-backdrop"></div>

@endsection

@section('script')
    <script>
        function closeModal() {
            document.getElementById('my-modal').style.display = 'none';
        }

        function closeVerifyModal() {
            document.getElementById('verified_email_phone').style.display = 'none';
        }

        function openVerifyModal(value) {
            document.getElementById('verified_email_phone').style.display = 'block';
            document.getElementById('verify-popup-text').innerText = value;

        }

        function closePopupModal() {
            document.getElementById('my-chat-pop-modal').style.display = 'none';
        }

        function toggleModal1(modalID, message) {
            var modalElement = document.getElementById(modalID);
            var messageElement = modalElement.querySelector(".modal-message");

            // Set the message
            messageElement.innerText = message;

            // Toggle visibility
            modalElement.classList.toggle("hidden");
            modalElement.classList.toggle("flex");
        }

        function togglePopupModal1() {
            document.getElementById('my-chat-pop-modal').style.display = 'flex';

        }
    </script>


    <script>
        function toggleModalCard(modalId, cardId = null, lang = null) {
            let modal = document.getElementById(modalId);
            let backdrop = document.getElementById(modalId + "-backdrop");
            console.log('cardId', cardId);
            if (modal.classList.contains("hidden")) {
                modal.classList.remove("hidden");
                backdrop.classList.remove("hidden");

                // Update the delete link if cardId is provided
                if (cardId) {
                    let deleteLink = document.getElementById("delete-card-link");
                    var url = "{{ route('booking.cancel', ['lang' => ':lang', 'id' => ':bookingId']) }}";

                    url = url.replace(":lang", lang);
                    url = url.replace(":bookingId", cardId);

                    deleteLink.href = url;
                }
            } else {
                modal.classList.add("hidden");
                backdrop.classList.add("hidden");
            }
        }
    </script>
@endsection
