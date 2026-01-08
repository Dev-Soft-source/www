@extends('layouts.template')

@section('content')

<div class="grid grid-cols-12 gap-4 container mx-auto my-14">
    @include('layouts.inc.profile_sidebar')

    <div class="bg-white border rounded p-4 border-gray-200 w-full col-span-12 md:col-span-9 shadow">
        <div class="border-b pb-2">
            <h1 class="mb-0 heading1">Balance & Transactions</h1>
        </div>

        <div class="mt-4 w-full">
            <p class="text-lg py-2">${{ $user->balance }} CAD</p>
        </div>

        <div class="border-b py-2 mt-4">
            <h2 class="heading2">All Transactions</h2>
        </div>

        <div class="overflow-auto">
            <table class="border border-collapse overflow-auto w-full mt-6">
                <thead class="">
                    <tr>
                        <th class="p-2 border text-lg">#ID</th>
                        <th class="p-2 border text-lg">User</th>
                        <th class="p-2 border text-lg">Transactions details</th>
                        <th class="p-2 border text-lg">Amount</th>
                        <th class="p-2 border text-lg">On date</th>
                    </tr>
                </thead>
                <tbody class="">
                    @foreach ($transactions as $transaction)
                        <tr>
                            <td class="p-2 border">{{ $transaction->id }}</td>
                            <td class="p-2 border">{{ $transaction->booking->passenger->first_name }} {{ $transaction->booking->passenger->last_name }}</td>
                            <td class="p-2 border">
                                @if ($transaction->type == 1)
                                    Debited for booking a ride
                                @elseif ($transaction->type == 2)
                                    Credited on ride completion
                                @elseif ($transaction->type == 3)
                                    Amount refunded on ride cancellation
                                @elseif ($transaction->type == 4)
                                    Debited booking price for the ride
                                @elseif ($transaction->type == 5)
                                    Amount refunded on ride completion
                                @elseif ($transaction->type == 6)
                                    Amount refunded on seat(s) cancelled
                                @elseif ($transaction->type == 7)
                                    Credited on seat(s) cancelled by passenger <br>(late notice)
                                @endif
                            </td>
                            <td class="p-2 border">${{ $transaction->price }} CAD</td>
                            <td class="p-2 border">{{ $transaction->on_date }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
    
@endsection