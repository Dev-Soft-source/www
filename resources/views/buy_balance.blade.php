@extends('layouts.template')

@section('content')
    <div class="grid grid-cols-12 gap-4 container mx-auto p-4 my-14">
        @include('layouts.inc.profile_sidebar')

        @php
            $cardSelected = false;
        @endphp

        <div class="bg-white border border-gray-200 rounded p-4 lg:p-4 w-full col-span-12 lg:col-span-9">
            <div class="flex flex-wrap mt-4" id="tabs-id">
                <div class="w-full">
                    <form id="submitForm" method="POST" action="{{ route('store_top_up_balance') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <h1>{{ $paymentSettingDetail->top_up_my_balance_head ?? 'Top up my balance' }}</h1>
                        <div class="grid grid-cols-1 lg:grid-cols-1 gap-4">
                            <div class="col-span-1">
                                <div class="space-y-4">
                                    <div class="bg-white rounded-lg overflow-hidden shadow-3xl">
                                        <div class="bg-white p-4">
                                            @if (session('message'))
                                                <div class="mt-4 mb-4 rounded-lg px-6 py-3 bg-red-100 text-gray-600"
                                                    role="alert">
                                                    {{ session('message') }}
                                                </div>
                                            @endif

                                            <div class="space-y-4 mb-4">
                                                <div class="w-full md:w-1/2">
                                                    <label for="seats"
                                                        class="block mb-2 font-medium text-gray-900">{{ $paymentSettingDetail->purchase_amount_label ?? 'Purchase amount' }}
                                                        <span class="text-red-500">*</span></label>
                                                    <input type="number" id="dr_amount" step="any" name="dr_amount"
                                                        value="{{ old('dr_amount') }}"
                                                        placeholder="{{ $paymentSettingDetail->purchase_amount_placeholder ?? 'Enter the amount you want to add' }}"
                                                        class="block mt-1 border p-1.5 w-full rounded text-base md:text-lg border-gray-300 focus:ring-none focus:outline-none focus:border-blue-600">
                                                    @error('dr_amount')
                                                        <div class="tooltip-error shadow-lg">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="space-y-4 mt-8">
                                                <div class="w-full md:w-1/2">
                                                    <x-payment-list :cards="$cards" :paymentSettingDetail="$paymentSettingDetail" />
                                                </div>
                                            </div>
                                            <div class="flex justify-center items-center mt-4 md:w-1/2 w-full">
                                                <button id="submitButton" class="bg-greenXS hover:bg-greenXS text-white text-sm rounded font-FuturaMdCnBT hover:font-FuturaMdCnBT px-5 py-3 hover:text-white text-center focus:bg-greenXS focus:text-white active:text-white active:bg-greenXS" type="submit">
                                                    {{ $paymentSettingDetail->buy_btn_text ?? 'Buy' }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')

    <script>
        


    </script>
@endsection
