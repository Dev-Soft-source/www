@extends('layouts.template')

@section('content')

<div class="container mx-auto my-14 p-4">
    <div class="bg-white border rounded p-4 border-gray-200 w-full shadow max-w-4xl mx-auto">
        <div class="flex justify-between items-center">
            <h1 class="mb-0">{{ $chatsPage->old_chat_page_main_heading ?? "Old Chats"}}</h1>
            {{-- <a href="{{ route('old_chats', ['lang' => optional($selectedLanguage)->abbreviation]) }}">Old messages</a> --}}
        </div>

        <div class="flex flex-col space-y-2 mt-4">
            @php
                $currentDateTime = now();
            @endphp
            @forelse ($chats as $userAndRideId => $latestMessage)
                <!-- Extract user ID and ride ID from the key -->
                @php
                    list($userId, $rideId) = explode('_', $userAndRideId);
                    $user = \App\Models\User::withTrashed()->find($userId);
                    // Check if ride exists before accessing its properties
                    $hoursDifference = 0;
                    if ($latestMessage->ride && $latestMessage->ride->date && $latestMessage->ride->time) {
                        // Combine ride date and time into a single DateTime object
                        $rideDateTime = \Carbon\Carbon::parse($latestMessage->ride->date . ' ' . $latestMessage->ride->time);
                        // Calculate the difference in hours between the current time and the ride time
                        $hoursDifference = $currentDateTime->diffInHours($rideDateTime);
                    }
                @endphp
                @if ($user && $latestMessage->ride && $latestMessage->ride->date && $latestMessage->ride->time && (strtotime($latestMessage->ride->date) < strtotime('today') || (strtotime($latestMessage->ride->date) == strtotime('today') && strtotime($latestMessage->ride->time) < strtotime('now'))))
                    @if ($hoursDifference > 48 && $latestMessage->rideDetail)
                        <a href="{{ route('chat', ['lang' => $selectedLanguage->abbreviation,'departure' => $latestMessage->rideDetail->departure ?? '','destination' => $latestMessage->rideDetail->destination ?? '','id' => $latestMessage->ride_id,'passenger' => $user->id]) }}">
                            <div class="border p-4 cursor-pointer rounded">
                                <!-- Display sender's information -->
                                <div class="flex items-end justify-between gap-2">
                                    <div class="flex gap-3 items-center">
                                        <img class="w-10 h-10 rounded-full" src="{{ $user->profile_image ?? asset('assets/image-placeholder.png') }}" alt="">
                                        <div>
                                            {{ $user->first_name ?? 'User' }}
                                            <p class="text-sm text-gray-500">{{ $latestMessage->message }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-end">
                                        <p class="text-sm text-gray-500 text-left">
                                            {{ $latestMessage->created_at->format('h:i A, M d, Y') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endif
                @endif
            @empty
                <p>{{ $chatsPage->old_chat_page_no_messages_label ?? "No chats"}}</p>
            @endforelse
        </div>
    </div>
</div>

@endsection
