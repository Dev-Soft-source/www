@extends('layouts.template')

@section('content')
<div class="container mx-auto p-4">
  <h1 class="text-2xl font-bold mb-4">All Notifications</h1>

  <!-- Filters -->
  <div class="bg-white p-4 rounded-lg shadow mb-4">
    <form action="{{ route('notifications', ['lang' => $selectedLanguage->abbreviation]) }}" method="GET" class="flex flex-col sm:flex-col md:flex-row lg:flex-row gap-4">

      <div>
        <label for="booking_type" class="block text-sm font-medium text-gray-700">Booking Type</label>
        <select name="booking_type" id="booking_type" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
          <option value="">All</option>
          @if($bookingOptions)
            <option value="instant_booking" {{ request('booking_type') == 'instant_booking' ? 'selected' : '' }}>{{ $bookingOptions->booking_option1 }}</option>
            <option value="request_to_book" {{ request('booking_type') == 'request_to_book' ? 'selected' : '' }}>{{ $bookingOptions->booking_option2 }}</option>
          @endif
        </select>
      </div>

      <div>
        <label for="payment_method" class="block text-sm font-medium text-gray-700">Payment Method</label>
        <select name="payment_method" id="payment_method" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
          <option value="">All</option>
          @if($paymentMethodOptions)
            <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>{{ $paymentMethodOptions->payment_methods_option1 }}</option>
            <option value="online_payment" {{ request('payment_method') == 'online_payment' ? 'selected' : '' }}>{{ $paymentMethodOptions->payment_methods_option2 }}</option>
            <option value="secured_cash" {{ request('payment_method') == 'secured_cash' ? 'selected' : '' }}>{{ $paymentMethodOptions->payment_methods_option3 }}</option>
            <option value="another_payment" {{ request('payment_method') == 'another_payment' ? 'selected' : '' }}>{{ $paymentMethodOptions->payment_methods_option4 }}</option>
          @endif
        </select>
      </div>

      <div class="self-end">
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">Apply</button>
      </div>
    </form>
  </div>

  <div class="bg-white p-4 rounded-lg shadow">
    @if ($notifications && $notifications->count() > 0)
      <ul>
        @foreach ($notifications as $notification)
          @if ($notification->from)
            <a
              @if ($notification->type == '1')
                href="{{ route('my_ride_detail', ['lang' => $selectedLanguage->abbreviation, 'departure' => $notification->departure, 'destination' => $notification->destination, 'id' => $notification->ride_id]) }}"
              @elseif ($notification->type == '2')
                href="{{ route('ride_detail', ['lang' => $selectedLanguage->abbreviation, 'departure' => $notification->departure, 'destination' => $notification->destination, 'id' => $notification->ride_id]) }}"
              @elseif ($notification->type == null)
                @php
                  $hasChatTarget = !empty($notification->ride_id) && !empty($notification->posted_by);
                @endphp
                @if($hasChatTarget)
                  href="{{ route('chat_detail', ['lang' => optional($selectedLanguage)->abbreviation, 'id' => $notification->ride_id, 'passenger' => $notification->posted_by]) }}"
                @else
                  href="{{ route('my_chats', ['lang' => optional($selectedLanguage)->abbreviation]) }}"
                @endif
              @endif
            >
              <li class="flex gap-2 items-center px-4 py-2 hover:bg-gray-100">
                <img class="w-10 h-10 rounded-full" src="{{ $notification->from->profile_image }}" alt="">
                <div>
                  <p class="{{ $notification->is_read == 0 ? 'font-semibold text-primary' : 'font-medium text-gray-800' }}">{{ $notification->message }}</p>
                  <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($notification->added_on)->format('M d, Y h:i A') }}</p>
                </div>
              </li>
            </a>
          @endif
        @endforeach
      </ul>
    @else
      <p class="text-gray-500">No notifications found.</p>
    @endif
  </div>
</div>
@endsection
