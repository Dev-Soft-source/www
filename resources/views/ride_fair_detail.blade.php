@extends('layouts.template')

@section('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endsection

@section('content')

<div class="grid grid-cols-12 gap-4 container mx-auto my-14">
    @include('layouts.inc.profile_sidebar')

    <div class="bg-white rounded pt-0 md:p-4 w-full col-span-12 md:col-span-9">
        <div class="flex flex-wrap" id="tabs-id">
            <div class="w-full">
                <div class="relative flex flex-col min-w-0 break-words bg-white w-full mb-6 shadow-lg rounded">
                    <div class="px-4 py-5 flex-auto">
                        <div class="tab-content tab-space">
                            <div class="block" id="tab-profile">
                                <div class="pb-2">
                                    <h1 class="mb-0">Ride fair details</h1>
                                </div>
                                <div class="space-y-4">
                                    @if (!empty($getAvailableBalance) && count($getAvailableBalance) > 0)
                                        @foreach ($getAvailableBalance as $balance)
                                            @foreach ($balance->ride->bookings->where('status', '!=' ,'3') as $booking)
                                                <div class="relative">
                                                    <a href="{{ route('ride_fair_details', ['lang' => $selectedLanguage->abbreviation, 'id' => $balance->ride_id]) }}">
                                                        <div class="bg-white rounded-lg shadow-3xl border-[3px] border-solid  border-gray-100 " id="ride-29">
                                                            <div class="border-gray-300 flex items-center justify-between space-x-2 p-4">
                                                                <p>Passenger</p>
                                                                <p>{{ $booking->passenger->first_name }} {{ $booking->passenger->last_name }}</p>
                                                            </div>
                                                            <div class="border-t border-gray-300 flex items-center justify-between space-x-2 p-4">
                                                                <p>Fare</p>
                                                                <p>{{ (($booking->booking_transaction_sum[0]->booking_transaction_sum ?? 0) - ($booking->booking_cancel_transaction_sum[0]->booking_cancel_transaction_sum ?? 0)) - (($booking->booking_credit_sum[0]->booking_credit_sum ?? 0) - ($booking->booking_credit_cancel_sum[0]->booking_credit_cancel_sum ?? 0)) }}</p>
                                                            </div>
                                                            <div class="border-t border-gray-300 flex items-center justify-between space-x-2 p-4">
                                                                <p>Booking fee</p>
                                                                <p>{{ ($booking->booking_credit_sum[0]->booking_credit_sum ?? 0) - ($booking->booking_credit_cancel_sum[0]->booking_credit_cancel_sum ?? 0) }}</p>
                                                            </div>
                                                            <div class="border-t border-gray-300 flex items-center justify-between space-x-2 p-4">
                                                                <p>Total</p>
                                                                <p>{{ ($booking->booking_transaction_sum[0]->booking_transaction_sum ?? 0) - ($booking->booking_cancel_transaction_sum[0]->booking_cancel_transaction_sum ?? 0) }}</p>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                            @endforeach
                                        @endforeach
                                    @else
                                        <p>No paid out found</p>
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
