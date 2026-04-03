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
                                class="inline-flex w-full justify-center rounded bg-red-500 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-red-400 sm:ml-3 sm:w-24">{{ $successMessage->popup_close_btn_text ?? 'Close' }}</a>
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
                                class="inline-flex w-full justify-center rounded bg-greenXS px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-greenXS sm:ml-3 sm:w-24">{{ $successMessage->popup_close_btn_text ?? 'Close' }}</a>
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
                        class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg w-full modal-border1">
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
    @php
        $seatPrice = ($ride->detail->price ?? 0) / 100;
    @endphp
    <div class="container mx-auto my-10 xl:my-14 px-4 xl:px-0">
        @php
            $note = null;
            if ($ride->isCancelled()) {
                $note = ['bg' => 'red', 'text' => $rideDetailPage->ride_canceller_by_driver ?? 'This ride was cancelled by the driver'];
            } elseif ($ride->isCompleted()) {
                $note = ['bg' => 'blue', 'text' => $rideDetailPage->ride_completed_text ?? 'This ride was completed'];
            } elseif ($ride->getRemainingSeats() == 0) {
                $note = ['bg' => 'blue', 'text' => $rideDetailPage->all_seats_booked_label ?? 'All seats are booked'];
            }
        @endphp

        @if ($note)
            <div class="mt-4 rounded-lg px-6 py-3 bg-{{ $note['bg'] }}-100 text-gray-600" role="alert">
                {{ $note['text'] }}
            </div>
        @endif
        <h1>{{ $rideDetailPage->main_heading ?? 'Ride Details Page' }}</h1>
        <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-y-4 md:gap-4">
            <div class="col-span-2">
                @if (strtotime($ride->date) > strtotime('today') || (strtotime($ride->date) == strtotime('today') && strtotime($ride->time) > strtotime('now')))
                    @if ($ride->bookings()->requested()->count() > 0)
                        <div class="bg-white rounded-xl overflow-hidden shadow-2xl mb-6 border-2 border-amber-400 booking-request-frame ring-2 ring-amber-300 ring-offset-2">
                            <h3 class="bg-primary text-white py-2 px-4 text-2xl xl:text-3xl ">
                                {{ $rideDetailPage->booking_request_main_heading ?? 'You have the following booking requests' }}
                            </h3>
                            <div class="bg-amber-50/30 p-2 py-4 md:p-4 space-y-3">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @php
                                        $isMultiStopsRide = $ride->rideStopSegments()->count() ? true : false ;
                                    @endphp
                                    @foreach ($ride->bookings()->requested()->get() as $booking)
                                        @if ($booking->passenger)
                                            <div class="border-2 border-amber-200 rounded-xl shadow-lg bg-white booking-request-card animate__animated animate__fadeInUp overflow-hidden">
                                                <div class="border-b border-slate-300 px-4 py-2 font-FuturaMdCnBT text-2xl hidden">
                                                    {{ $rideDetailPage->booking_request_heading ?? 'Booking request' }}
                                                </div>
                                                <div class="p-4">
                                                    <div class="flex items-start">
                                                        <img class="w-12 2xl:w-16 h-12 2xl:h-16 rounded-full object-cover mr-2 2xl:mr-3 mt-2 border-2 border-gray-300"
                                                            src="{{ $booking->passenger->profile_image }}" alt="">
                                                        <div>
                                                            <p class="mb-0 font-semibold">
                                                                {{ $booking->passenger->first_name }}
                                                                {{ $booking->passenger->last_name }}</p>
                                                            @if($isMultiStopsRide && $booking->from_stop_id)
                                                            <span class="text-gray-700 text-sm">{{$booking->fromStop->label}} → {{$booking->toStop->label}}</span>
                                                            @else
                                                            <span class="text-gray-700 text-sm">FULL</span>
                                                            @endif
                                                            <div class="flex flex-row items-center text-sm text-gray-600">
                                                                <span class="mr-2">
                                                                    <svg width="16px" height="16px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M11 2C11 1.44772 11.4477 1 12 1C12.0161 1 12.0322 1.00038 12.0481 1.00114C12.0632 1.00051 12.0785 1.00026 12.0938 1.00039C14.1199 1.01765 16.1091 1.59455 17.8374 2.67665C19.8492 3.93625 21.3996 5.8128 22.2571 8.02608C23.1146 10.2394 23.2332 12.6706 22.5951 14.9569C21.9571 17.2431 20.5967 19.2616 18.717 20.711C16.8373 22.1604 14.5393 22.9629 12.1659 22.9988C9.79262 23.0346 7.47138 22.3017 5.54884 20.9097C3.6263 19.5177 2.2056 17.5411 1.49889 15.2752C0.891759 13.3285 0.839553 11.258 1.33799 9.29413C1.47385 8.75881 2.05308 8.48753 2.57385 8.67143C3.09462 8.85532 3.36505 9.4271 3.24007 9.96506C2.8794 11.5176 2.93657 13.1446 3.4147 14.6776C3.99248 16.5302 5.15399 18.1461 6.72579 19.2842C8.29758 20.4223 10.1953 21.0214 12.1357 20.9921C14.076 20.9629 15.9548 20.3068 17.4916 19.1218C19.0283 17.9368 20.1405 16.2866 20.6622 14.4174C21.1838 12.5483 21.0869 10.5606 20.3858 8.75108C19.6847 6.94158 18.4172 5.40739 16.7724 4.37759C15.6232 3.65806 14.3329 3.21174 13 3.06259V5C13 5.55229 12.5523 6 12 6C11.4477 6 11 5.55229 11 5V2Z" fill="#666666"></path> <path fill-rule="evenodd" clip-rule="evenodd" d="M5.31205 3.92537C4.91543 3.66095 4.38731 3.71325 4.05025 4.05031C3.71318 4.38738 3.66089 4.9155 3.9253 5.31212L9.30753 13.3855C9.50568 13.6827 9.80929 14.101 10.283 14.3927C10.6282 14.6053 11.1764 14.8627 11.8475 14.871C12.5576 14.8799 13.2918 14.6077 13.9497 13.9498C14.6076 13.2919 14.8798 12.5577 14.871 11.8475C14.8626 11.1764 14.6053 10.6283 14.3927 10.2831C14.1009 9.80935 13.6826 9.50574 13.3854 9.30759L5.31205 3.92537ZM10.9716 12.2761L8.36291 8.36297L12.276 10.9717C12.5267 11.1388 12.6362 11.2449 12.6897 11.3319C12.7963 11.5049 12.8689 11.6951 12.8711 11.8724C12.8728 12.0107 12.8361 12.235 12.5355 12.5356C12.2349 12.8362 12.0106 12.8729 11.8723 12.8712C11.695 12.869 11.5048 12.7964 11.3318 12.6898C11.2448 12.6362 11.1388 12.5267 10.9716 12.2761Z" fill="#666666"></path> </g></svg>
                                                                </span>
                                                                <span>{{ getDateFormatedString($booking->booked_on) }}</span>
                                                            </div>
                                                            <div class="flex items-center 2xl:space-x-1 mt-2">
                                                                @for ($i = 1; $i <= $booking->seats; $i++)
                                                                    <div
                                                                        class="relative w-8 xl:w-9 2xl:w-10 h-8 xl:h-9 2xl:h-10 mt-0.5">
                                                                        <img src="{{ asset('assets/seat-hover-1.png') }}"
                                                                            class="w-8 xl:w-9 2xl:w-10 h-8 xl:h-9 2xl:h-10 mt-0.5 cursor-pointer"
                                                                            alt="">
                                                                        <span
                                                                            class="absolute left-3 xl:left-3.5 2xl:left-4 top-3 xl:top-3.5 2xl:top-3 text-sm 2xl:text-base text-green-300">{{ $i }}</span>
                                                                    </div>
                                                                @endfor
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="flex items-center justify-center mt-4 gap-2">
                                                        @auth
                                                            <button type="button"
                                                                class="button-exp-fill w-36 booking-decline-btn"
                                                                data-reject-url="{{ route('reject_booking_request', ['lang' => app()->getLocale(), 'id' => $booking->id, 'email' => auth()->user()->email]) }}">
                                                                {{ $rideDetailPage->request_reject_label ?? 'Decline' }}
                                                            </button>
                                                            <button type="button"
                                                                class="bg-greenXS hover:bg-greenXS button-exp-fill w-42 booking-approve-btn"
                                                                data-accept-url="{{ route('accept_booking_request', ['lang' => app()->getLocale(), 'id' => $booking->id, 'email' => auth()->user()->email]) }}">
                                                                {{ $rideDetailPage->request_accept_label ?? 'Approve Booking' }}
                                                            </button>
                                                        @else
                                                            <span
                                                                class="text-gray-500 text-sm">{{ $rideDetailPage->request_reject_label ?? 'Reject' }}
                                                                / {{ $rideDetailPage->request_accept_label ?? 'Accept' }}
                                                                ({{ __('Sign in to respond') }})</span>
                                                        @endauth
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                @endif

                @if (strtotime($ride->date) > strtotime('today') || (strtotime($ride->date) == strtotime('today') && strtotime($ride->time) > strtotime('now')))
                    @if (count($ride->bookings->where('status', 1)->where('secured_cash', 1)) > 0)
                        <div class="bg-white rounded-lg overflow-hidden shadow-3xl mb-6">
                            <h3 class="bg-primary text-white py-2 px-4 text-2xl xl:text-3xl">
                                {{ $rideDetailPage->secured_cash_heading ?? 'Secured-cash payments' }}</h3>
                            <div class="space-y-4 p-4">
                                @foreach ($ride->bookings->where('status', 1)->where('secured_cash', 1) as $booking)
                                    @php
                                        $dateTimeString = '' . $ride->date . ' ' . $ride->time . '';
                                        $targetTimestamp = strtotime($dateTimeString);
                                        $currentTimestamp = time();
                                        $diffInSeconds = $targetTimestamp - $currentTimestamp;
                                        $diffInMinutes = $diffInSeconds / 60;
                                    @endphp

                                    @if ($booking->passenger)
                                        <div class="flex items-center justify-between w-full no-scrollbar overflow-x-auto">
                                            <div class="flex items-center space-x-2">
                                                <div class="w-12 h-12 rounded-full">
                                                    <img class="w-full h-full rounded-full object-cover"
                                                        src="{{ $booking->passenger->profile_image }}" alt="">
                                                </div>
                                                <div
                                                    class="text-center flex flex-row md:flex-col items-center md:items-start space-x-2 md:space-x-0">
                                                    <p class="font-semibold leading-4 text-base mb-0 whitespace-nowrap">
                                                        {{ $booking->passenger->first_name }}</p>
                                                    <div class="flex items-center space-x-2">
                                                        <p
                                                            class="text-gray-700 leading-4 md:mt-2 text-base whitespace-nowrap">
                                                            Age:
                                                            <span>{{ $booking->passenger->getAge() }}</span>
                                                        </p>
                                                        <p
                                                            class="text-gray-700 leading-4 md:mt-2 text-base whitespace-nowrap">
                                                            |</p>
                                                        <p
                                                            class="text-gray-700 leading-4 md:mt-2 text-base whitespace-nowrap">
                                                            {{ $rideDetailPage->web_gender_label ?? 'Gender' }}:
                                                            <span>{{ ucfirst($booking->passenger->gender) }}</span>
                                                        </p>
                                                        <p
                                                            class="text-gray-700 leading-4 md:mt-2 text-base whitespace-nowrap">
                                                            |</p>
                                                        <p
                                                            class="text-gray-700 leading-4 md:mt-2 text-base whitespace-nowrap">
                                                            @if ($booking->passenger->hasPassengerRatings())
                                                                {{ $rideDetailPage->review_label ?? 'Review' }}:
                                                                <span>{{ number_format($booking->passenger->getPassengerAverageRating(), 1) }}</span>
                                                            @else
                                                                {{ $rideDetailPage->no_reviews_label ?? 'No Reviews' }}
                                                            @endif
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="ml-20 md:ml-0">
                                                @if ($diffInMinutes <= 30)
                                                    <button
                                                        class="bg-greenXS hover:bg-greenXS text-white text-base md:text-lg rounded font-FuturaMdCnBT hover:font-FuturaMdCnBT px-5 py-2 border border-greenXS hover:border-greenXS hover:text-white text-center focus:bg-greenXS focus:text-white active:text-white active:bg-greenXS send_code"
                                                        data-booking-id="{{ $booking->id }}"
                                                        data-booking-secured-cash-attempt="{{ $booking->secured_cash_attempt_count }}"
                                                        data-setting-secured-cash-attempt="{{ $siteSetting->secured_cash_attempt }}">
                                                        {{ $rideDetailPage->enter_code_label ?? 'Enter payment code' }}
                                                    </button>
                                                @endif

                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endif
                <div class="bg-white rounded-lg shadow-3xl">
                    <div class="flex flex-col sm:flex-col lg:flex-row  justify-between gap-4 p-4">
                        <div class="route-info">
                            <x-px.route-info 
                                :ride="$ride" 
                            />
                        </div>
                        <div class="ride-seat-info flex flex-col items-end gap-2">
                            @php
                                $ride_status = null;
                                if ($ride->isCompleted()) {
                                    $ride_status = [
                                        'label' => $rideDetailPage->ride_completed_label,
                                        'class' => 'bg-green-100 text-green-600',
                                    ];
                                } elseif ($ride->isCancelled()) {
                                    $ride_status = [
                                        'label' => $rideDetailPage->ride_cancelled_label,
                                        'class' => 'bg-red-100 text-red-600',
                                    ];
                                }
                            @endphp
                            @if($ride_status)
                                <p class="w-fit px-2 py-1 rounded text-right text-sm {{ $ride_status['class'] }}">
                                    {{ $ride_status['label'] }}
                                </p>
                            @endif

                            <p class="font-medium text-2xl text-right">
                                {{ str_replace(':count', $ride->seats, $rideDetailPage->total_seats_label ?? 'Total :count seats') }}
                            </p>
                            <div class="flex items-center gap-2 text-primary justify-end">
                                @if (isset($firm_cancellation_discount) && $firm_cancellation_discount != '' && $ride->isFirmCancellation())
                                    <span class="line-through">
                                        ${{ number_format((float) $seatPrice, 2) }}
                                    </span>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
                                    </svg>
                                    <span>
                                        ${{ $seatPrice - ($seatPrice * $firm_cancellation_discount) / 100 }}
                                    </span>
                                @else
                                    ${{ number_format((float) $seatPrice, 2) }}
                                @endif

                                <small>
                                    {{ $rideDetailPage->per_seat_label ?? 'per seat' }}
                                </small>
                                @if (isset($firm_cancellation_discount) && $firm_cancellation_discount != '' && $ride->isFirmCancellation())
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                        class="bi bi-info-circle-fill text-black" viewBox="0 0 16 16"
                                        data-tippy-content="{!! nl2br($rideFeatureOptions['cancellation']['firm']->tooltip) ??
                                            'This ride has the Firm cancellation policy, so its booking price is reduced by 10%' !!}">
                                        <path
                                            d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
                                    </svg>
                                @endif

                            </div>
                        </div>
                    </div>
                    <div class="border-t border-gray-300 grid grid-cols-2 divide-x divide-gray-300">
                        <div class="flex items-baseline p-4">
                            <h4 class="font-medium text-xl xl:text-2xl text-left text-black font-FuturaMdCnBT">
                                @isset($rideDetailPage->seats_left_label)
                                    {{ $rideDetailPage->seats_left_label }}:
                                @endisset                                
                            </h4>
                            <p class="text-xl text-primary font-normal ml-2">{{ intval($ride->seats) -intval($ride->bookings()->where('status', '<>', 3)->where('status', '<>', 4)->whereHas('passenger', function ($query) {$query->whereNull('deleted_at');})->sum('seats')) }}</p>
                        </div>
                        <div class="flex flex-wrap items-center gap-3 p-4 items-baseline">
                            <h4 class="text-black text-xl xl:text-2xl">
                                {{ $rideDetailPage->booking_price_label ?? 'Booking Price' }}:
                            </h4>
                            <p class="text-lg text-primary font-normal">${{ number_format(floatval($ride->detail->price/100), 2) }}
                                @isset($rideDetailPage->per_seat_label)
                                    {{ $rideDetailPage->per_seat_label }}
                                @endisset
                            </p>
                        </div>
                    </div>
                    <div class="border-t border-gray-300 grid sm:grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-gray-300">
                        <div class="p-4 items-baseline">
                            <div class="flex flex-wrap items-end gap-3">
                                @php
                                    $payment_method = $ride->resolvePaymentMethodOption($rideFeatureOptions['payment_method'] ?? []);
                                @endphp
                                <h4 class="font-medium text-xl xl:text-2xl text-left text-black font-FuturaMdCnBT">
                                    {{ $rideDetailPage->payment_method_label ?? 'Payment Method' }}:
                                </h4>
                                <p class="text-lg text-primary font-normal inline-block cursor-pointer" data-tippy-content="{{ optional($payment_method)->tooltip }}">
                                    {{ optional($payment_method)->name }}
                                </p>
                            </div>
                        </div>

                        <div class="p-4 items-baseline">
                            <div class="flex flex-wrap items-end gap-3">
                                @php
                                    $booking_method = $ride->resolveBookingMethodOption($rideFeatureOptions['booking_method'] ?? []);
                                @endphp
                                <h4 class="text-black text-xl xl:text-2xl font-FuturaMdCnBT">
                                    {{ $rideDetailPage->booking_method_label ?? 'Booking Method' }}:
                                </h4>
                                <p class="text-lg text-primary font-normal inline-block cursor-pointer"
                                    data-tippy-content="{{ optional($booking_method)->tooltip }}">
                                    {{ optional($booking_method)->name }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-gray-300 grid sm:grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-gray-300">
                        <div class="p-4 flex items-center">
                            <h4 class="text-black text-xl xl:text-2xl font-FuturaMdCnBT">
                                @php
                                    $bookedSeatsCount = $ride->getBookedSeats();
                                @endphp
                                {{ $rideDetailPage->booked_on_column_label ?? 'Booked' }}: 
                            </h4>
                            <p class="text-primary font-normal text-lg ml-2">
                                {{ $bookedSeatsCount }}
                                {{ $bookedSeatsCount == 1
                                    ? ($rideDetailPage->seat_on_column_label ?? 'seat')
                                    : ($rideDetailPage->ride_seat_label ?? 'seats') }}
                            </p>
                        </div>
                        <div class="p-4">
                            <div class="flex items-center justify-between">
                                <h4 class="text-black text-xl xl:text-2xl font-FuturaMdCnBT">
                                    {{ $rideDetailPage->mobile_seat_fare_label ?? 'Fare' }}: </h4>
                                <p class="text-primary ">
                                    ${{ number_format($ride->getMobileSeatFareTotal(), 2) }}
                                </p>
                            </div>
                            <div class="flex items-center justify-between">
                                <h4 class="text-black text-xl xl:text-2xl font-FuturaMdCnBT">
                                    {{ $rideDetailPage->mobile_seat_booking_fee_label ?? 'Booking fee' }}: </h4>
                                <p class="text-primary ">
                                    ${{ number_format($ride->getMobileSeatBookingFeeTotal(), 2) }}
                                </p>
                            </div>
                            <div class="flex items-center justify-between">
                                <h4 class="text-black text-xl xl:text-2xl font-FuturaMdCnBT">
                                    {{ $rideDetailPage->mobile_seat_total_amount_label ?? 'Total amount' }}: </h4>
                                <p class="text-primary ">
                                    ${{ number_format($ride->getMobileSeatTotalAmount(), 2) }}
                                </p>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="bg-white rounded-lg overflow-hidden shadow-3xl mt-4">
                    <h3 class="bg-primary text-white py-2 px-4 text-2xl xl:text-3xl">
                        {{ $rideDetailPage->ride_features_label ?? 'Ride features' }}</h3>
                    <div class="bg-white p-4 space-y-3">
                        @include('partials.ride_preference_items', [ 'ride' => $ride ])
                        @include('partials.ride_feature_items', [ 'features' => $ride->features ])
                    </div>
                </div>

                
            </div>
            <div class="col-span-1">
                <div class="space-y-4">
                    {{-- @if (count($ride->bookings->where('status', 1)) > 0) --}}
                        <div class="bg-white rounded-lg overflow-hidden shadow-3xl">
                            <h3 class="bg-primary text-white py-2 px-4 text-2xl xl:text-3xl">
                                @if ($ride->isCancelled())
                                    {{ $rideDetailPage->review_passanger_label ?? 'Review my passengers' }}
                                @else
                                    {{ $rideDetailPage->ride_co_passenger_heading ?? 'My passengers' }}
                                @endif
                            </h3>
                            <a href="{{ route('my_passengers', ['lang' => $selectedLanguage->abbreviation, 'ride_id' => $ride->id]) }}">
                                <div class="grid divide-y">
                                    @foreach ($ride->bookings->whereIn('status', [\App\Models\Booking::STATUS_BOOKED, \App\Models\Booking::STATUS_COMPLETED]) as $booking)
                                        @if ($booking->passenger)
                                            <div class="flex items-center p-4 space-x-2 w-full no-scrollbar overflow-x-auto gap-2">
                                                <div class="w-12 h-12 rounded-full flex-shrink-0">
                                                    @if (auth()->user())
                                                        @php
                                                            $uuid = $booking
                                                                ->where('user_id', auth()->user()->id)
                                                                // ->where('status', 3)
                                                                ->pluck('uuid')
                                                                ->first();
                                                        @endphp
                                                    @endif
                                                    @php
                                                        $profile_image = $booking->passenger->profile_image ?? null;
                                                    @endphp
                                                    @if ($ride->isCompleted() && isset($uuid))
                                                        <a href="{{ route('review_passenger', ['lang' => $selectedLanguage->abbreviation, 'id' => $uuid]) }}">
                                                            <img class="w-full h-full rounded-full object-cover" src="{{ $profile_image }}" alt="">
                                                        </a>
                                                    @else
                                                        <img class="w-full h-full rounded-full object-cover" src="{{ $profile_image }}" alt="">
                                                    @endif
                                                </div>
                                                <div class="text-center flex-auto flex flex-row md:flex-col items-center md:items-start space-x-2 md:space-x-0">
                                                    <p class="font-semibold leading-4 text-md mb-0 whitespace-nowrap">
                                                        {{ $booking->passenger->first_name }} </p>
                                                    <div class="flex items-center space-x-2">
                                                        <p
                                                            class="text-gray-700 leading-4 md:mt-2 text-base whitespace-nowrap">
                                                            {{ $rideDetailPage->passenger_age_label ?? 'Age' }}:
                                                            <span>{{ $booking->passenger->getAge() }}</span></p>
                                                        <p class="text-gray-700 leading-4 md:mt-2 text-base whitespace-nowrap">|</p>
                                                        <p
                                                            class="text-gray-700 leading-4 md:mt-2 text-base whitespace-nowrap">
                                                            {{-- {{ $rideDetailPage->passenger_gender_label ?? 'Gender' }}: --}}
                                                            <span>{{ ucfirst($booking->passenger->gender) }}</span></p>
                                                        <p class="text-gray-700 leading-4 md:mt-2 text-base whitespace-nowrap">|</p>
                                                        <p
                                                            class="text-gray-700 leading-4 md:mt-2 text-base whitespace-nowrap">
                                                            @if ($booking->passenger->hasPassengerRatings())
                                                                {{ $rideDetailPage->review_label ?? 'Review' }}:
                                                                <span>{{ number_format($booking->passenger->getPassengerAverageRating(), 1) }}</span>
                                                            @else
                                                                {{ $rideDetailPage->no_reviews_label ?? 'No Reviews' }}
                                                            @endif
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </a>
                        </div>
                    {{-- @endif --}}
                    <div class="bg-white rounded-lg overflow-hidden shadow-3xl">
                        <h3 class="bg-primary text-white py-2 px-4 text-2xl xl:text-3xl">
                            {{ $rideDetailPage->vehicle_info_label ?? 'Vehicle info' }}</h3>
                        <div class="flex items-start space-x-2 p-4 w-full">
                            <div class="w-20 h-20 rounded-full overflow-hidden">
                                <img class="w-full h-full object-cover rounded-full" src="{{ $ride->car_image }}" alt="">
                            </div>
                            <div class="text-center">
                                @php
                                    $vehicleParts = array_filter([
                                        $ride->year,
                                        $ride->make,
                                        $ride->model,
                                        ucfirst($ride->color ?: optional($ride->vehicle)->color),
                                    ]);
                                @endphp
                                <p class="text-md font-semibold">{{ implode(' | ', $vehicleParts) }}</p>
                                <p class="font-semibold text-xl text-left text-black">
                                    {{ $ride->license_no }}
                                </p>
                                @if ($ride->vehicle_type_label)
                                    <p class="text-md">{{ $ride->vehicle_type_label }}</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    @php
                        $cancellation_method = $ride->resolveBookingTypeOption($rideFeatureOptions['cancellation'] ?? []);
                    @endphp
                    <div class="bg-white rounded-lg overflow-hidden shadow-3xl">
                        <h3 class="bg-primary text-white py-2 px-4 text-2xl xl:text-3xl">
                            {{ $rideDetailPage->cancellation_policy_label ?? 'Cancellation policy' }}</h3>
                        <div class="flex items-center space-x-2 p-4 w-full">
                            <div class="flex items-center justify-between w-full">
                                <p class="text-left text-md font-semibold">
                                    {{ optional($cancellation_method)->name }}
                                </p>
                            </div>
                        </div>
                    </div>
                    @if (strtotime($ride->date) > strtotime('today') ||
                            (strtotime($ride->date) == strtotime('today') && strtotime($ride->time) > strtotime('now')))
                        @if (!$ride->isCancelled())
                            <div class="flex w-full gap-4">
                                <a href="{{ route('edit_ride', ['lang' => $selectedLanguage->abbreviation, 'id' => $ride->id]) }}"
                                    class="button-exp-fill flex-1 text-center">
                                    {{ $rideDetailPage->edit_ride_btn_label ?? 'Edit ride' }}
                                </a>
                                <a id="cancelRideBtn" href="#"
                                    class="inline-flex flex-1 justify-center rounded bg-red-500 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-red-400">
                                    {{ $rideDetailPage->cancel_ride_btn_label ?? 'Cancel ride' }}
                                </a>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div id="bookingModal" class="{{ $errors->any() ? '' : 'hidden' }} fixed z-50 inset-0 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 transition-opacity">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>​
            <div
                class="inline-block animate__animated animate__fadeIn align-bottom bg-white rounded-md text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-border">
                <form method="POST" action="{{ route('secured_cash_code') }}">
                    @csrf
                    @method('PUT')
                    <div class="bg-white px-4 py-6 sm:p-6">
                        <div class="sm:flex sm:items-start w-full">
                            {{-- <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M13 7H7v6h6V7z" />
                            </svg>
                        </div> --}}
                            <div class=" text-center sm:mt-0 sm:text-left w-full md:w-3/4">
                                <h3 class="text-primary font-FuturaMdCnBT" id="modal-title">
                                    {{ $rideDetailPage->enter_code_label ?? 'Enter the Secured-cash payment code' }}
                                </h3>
                                <div class="mt-2">
                                    <input type="hidden" name="booking_id" id="booking-id"
                                        value="{{ old('booking_id') }}">
                                    <input type="text" name="code" placeholder="{{ $rideDetailPage->enter_secured_placeholder ?? 'Enter secured-cash payment code' }}"
                                        class="mt-2 p-2 border border-gray-300 rounded w-full"
                                        value="{{ old('code') }}">
                                    @error('code')
                                        <div class="relative tooltip -bottom-4 group-hover:flex">
                                            <div role="tooltip"
                                                class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded">
                                                <p class="text-white leading-none text-sm lg:text-base">{{ $message }}
                                                </p>
                                            </div>
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="px-4 mt-4 sm:px-6 flex items-center justify-center gap-2">
                            <button type="button" class="button-exp-fill w-auto" id="closeModal">
                                {{ $siteText['close_btn_text'] ?? 'Close' }}
                            </button>
                            <button type="submit" class="button-exp-fill w-auto">
                                {{ $siteText['submit_btn_text'] ?? 'Submit' }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Decline booking request: confirmation modal --}}
    <div id="declineConfirmModal" class="hidden fixed inset-0 z-50 w-screen overflow-y-auto" aria-modal="true"
        role="dialog">
        <div class="relative flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                onclick="closeBookingModal('declineConfirmModal')"></div>
            <div
                class="relative animate__animated animate__fadeIn z-20 transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg mx-auto modal-border1">
                <button type="button" onclick="closeBookingModal('declineConfirmModal')"
                    class="absolute top-2 right-2 p-1 rounded-full hover:bg-gray-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <p class="text-left text-gray-800">Are you sure you want to decline this booking request? This action
                        cannot be undone.</p>
                </div>
                <div class="px-4 pb-6 pt-4 flex flex-wrap justify-center gap-2 sm:gap-3">
                    <button type="button" id="declineConfirmYes"
                        class="inline-flex justify-center rounded bg-red-500 px-4 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:bg-red-400 sm:w-auto">Yes,
                        decline</button>
                    <button type="button" onclick="closeBookingModal('declineConfirmModal')"
                        class="inline-flex justify-center rounded border border-gray-300 bg-white px-4 py-2 font-FuturaMdCnBT text-lg font-medium text-gray-700 hover:bg-gray-50 sm:w-auto">No,
                        take me back</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Approve booking request: confirmation modal --}}
    <div id="approveConfirmModal" class="hidden fixed inset-0 z-50 w-screen overflow-y-auto" aria-modal="true"
        role="dialog">
        <div class="relative flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                onclick="closeBookingModal('approveConfirmModal')"></div>
            <div
                class="relative animate__animated animate__fadeIn z-20 transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg mx-auto modal-border">
                <button type="button" onclick="closeBookingModal('approveConfirmModal')"
                    class="absolute top-2 right-2 p-1 rounded-full hover:bg-gray-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <p class="text-left text-gray-800">Are you sure you want to approve this booking request?</p>
                </div>
                <div class="px-4 pb-6 pt-4 flex flex-wrap justify-center gap-2 sm:gap-3">
                    <button type="button" id="approveConfirmYes"
                        class="inline-flex justify-center rounded bg-greenXS px-4 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:opacity-90 sm:w-auto">Yes,
                        approve it!</button>
                    <button type="button" onclick="closeBookingModal('approveConfirmModal')"
                        class="inline-flex justify-center rounded border border-gray-300 bg-white px-4 py-2 font-FuturaMdCnBT text-lg font-medium text-gray-700 hover:bg-gray-50 sm:w-auto">No,
                        take me back!</button>
                </div>
            </div>
        </div>
    </div>

    @if (session('decline_success_message'))
        <div id="declineSuccessModal" class="fixed inset-0 z-50 w-screen overflow-y-auto" aria-modal="true"
            role="dialog">
            <div
                class="relative flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                    onclick="closeBookingModal('declineSuccessModal')"></div>
                <div
                    class="relative animate__animated animate__fadeIn z-20 transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg mx-auto modal-border">
                    <button type="button" onclick="closeBookingModal('declineSuccessModal')"
                        class="absolute top-2 right-2 p-1 rounded-full hover:bg-gray-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <p class="text-left text-gray-800">{{ session('decline_success_message') }}</p>
                    </div>
                    <div class="px-4 pb-6 pt-4">
                        <button type="button" onclick="closeBookingModal('declineSuccessModal')"
                            class="inline-flex justify-center rounded bg-primary px-4 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:bg-blue-600 w-32">{{ $successMessage->popup_close_btn_text ?? 'Close' }}</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if (session('approve_success_message'))
        <div id="approveSuccessModal" class="fixed inset-0 z-50 w-screen overflow-y-auto" aria-modal="true"
            role="dialog">
            <div
                class="relative flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                    onclick="closeBookingModal('approveSuccessModal')"></div>
                <div
                    class="relative animate__animated animate__fadeIn z-20 transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg mx-auto modal-border">
                    <button type="button" onclick="closeBookingModal('approveSuccessModal')"
                        class="absolute top-2 right-2 p-1 rounded-full hover:bg-gray-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <p class="text-left text-gray-800">{{ session('approve_success_message') }}</p>
                    </div>
                    <div class="px-4 pb-6 pt-4">
                        <button type="button" onclick="closeBookingModal('approveSuccessModal')"
                            class="inline-flex justify-center rounded bg-primary px-4 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:bg-blue-600 w-32">{{ $successMessage->popup_close_btn_text ?? 'Close' }}</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Cancel ride: confirmation modal (same style as message popup) --}}
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

    {{-- Cancel ride: result modal (success / error message + Close) --}}
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
                        <button type="button" id="cancelRideResultClose" class="inline-flex w-full justify-center rounded bg-red-500 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-red-400 sm:ml-3 sm:w-24">{{ $messages->popup_close_btn_text ?? 'Close' }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="securedCashAttemptError" class="hidden fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="relative flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeModal()"></div>
            <div
                class="relative animate__animated animate__fadeIn z-20 transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
                <button onclick="closeModal()" class="absolute top-2 right-2 p-1 rounded-full hover:bg-gray-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="w-full">
                        <p class="can-exp-p text-center">{{ $successMessage->too_many_secured_cash_attempt_message }}</p>
                    </div>
                </div>
                <div class="px-4 pb-6 pt-4  sm:flex sm:flex-row-reverse sm:px-6 justify-center">
                    <a href=""
                        class="inline-flex w-full justify-center rounded bg-greenXS px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-greenXS sm:ml-3 sm:w-24">{{ $successMessage->popup_close_btn_text ?? 'Close' }}</a>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('style')
    <style>
        .booking-request-frame {
            animation: booking-request-pulse 2.5s ease-in-out 2;
        }

        @keyframes booking-request-pulse {

            0%,
            100% {
                box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.35);
            }

            50% {
                box-shadow: 0 0 0 12px rgba(245, 158, 11, 0);
            }
        }

        .booking-request-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .booking-request-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        }
    </style>
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

        document.addEventListener('DOMContentLoaded', function() {
            var pendingRejectUrl = null;
            var pendingAcceptUrl = null;

            document.querySelectorAll('.booking-decline-btn').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    pendingRejectUrl = this.getAttribute('data-reject-url');
                    var modal = document.getElementById('declineConfirmModal');
                    if (modal) {
                        modal.classList.remove('hidden');
                        modal.style.display = 'block';
                    }
                });
            });

            document.querySelectorAll('.booking-approve-btn').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    pendingAcceptUrl = this.getAttribute('data-accept-url');
                    var modal = document.getElementById('approveConfirmModal');
                    if (modal) {
                        modal.classList.remove('hidden');
                        modal.style.display = 'block';
                    }
                });
            });

            var declineConfirmYes = document.getElementById('declineConfirmYes');
            if (declineConfirmYes) {
                declineConfirmYes.addEventListener('click', function() {
                    if (pendingRejectUrl) {
                        window.location.href = pendingRejectUrl;
                    }
                });
            }

            var approveConfirmYes = document.getElementById('approveConfirmYes');
            if (approveConfirmYes) {
                approveConfirmYes.addEventListener('click', function() {
                    if (pendingAcceptUrl) {
                        window.location.href = pendingAcceptUrl;
                    }
                });
            }
        });

        // Rest of your existing JavaScript...
        document.addEventListener('DOMContentLoaded', function() {
            const sendCodeButtons = document.querySelectorAll('.send_code');
            const modal = document.getElementById('bookingModal');
            const attemptModal = document.getElementById('securedCashAttemptError');
            const bookingIdField = document.getElementById('booking-id');
            const closeModal = document.getElementById('closeModal');
            const hasErrors = {{ $errors->any() ? 'true' : 'false' }};
            const oldBookingId = "{{ old('booking_id') }}";

            if (hasErrors && oldBookingId) {
                bookingIdField.value = oldBookingId;
                modal.classList.remove('hidden');
            }

            sendCodeButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const bookingId = this.getAttribute('data-booking-id');
                    const bookingSecuredCashAttempt = this.getAttribute(
                        'data-booking-secured-cash-attempt');
                    const settingSecuredCashAttempt = this.getAttribute(
                        'data-setting-secured-cash-attempt');
                    if (bookingSecuredCashAttempt == settingSecuredCashAttempt) {
                        attemptModal.classList.remove('hidden');
                    } else {
                        bookingIdField.value = bookingId;
                        modal.classList.remove('hidden');
                    }
                });
            });

            closeModal.addEventListener('click', function() {
                modal.classList.add('hidden');
            });

            window.addEventListener('click', function(event) {
                if (event.target == modal) {
                    modal.classList.add('hidden');
                }
            });
        });
    </script>
    <script>
        const cancelRideMyRidesUrl = "{{ route('my_rides', ['lang' => $selectedLanguage->abbreviation]) }}";

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
                const bookedSeats =
                    {{ $ride->bookings()->where('status', '<>', 3)->where('status', '<>', 4)->whereHas('passenger', function ($query) {$query->whereNull('deleted_at');})->sum('seats') }};

                if (bookedSeats === 0) {
                    const confirmModal = document.getElementById('cancelRideConfirmModal');
                    if (confirmModal) {
                        confirmModal.classList.remove('hidden');
                        confirmModal.style.display = 'block';
                    }
                } else {
                    window.location.href =
                        "{{ route('ride.cancel', ['lang' => $selectedLanguage->abbreviation, 'id' => $ride->id]) }}";
                }
            });

            const cancelRideConfirmYes = document.getElementById('cancelRideConfirmYes');
            if (cancelRideConfirmYes) {
                cancelRideConfirmYes.addEventListener('click', function() {
                    closeBookingModal('cancelRideConfirmModal');

                    fetch("{{ route('update_cancel_ride', $ride->id) }}", {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                                'X-HTTP-Method-Override': 'PUT'
                            },
                            body: JSON.stringify({})
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


