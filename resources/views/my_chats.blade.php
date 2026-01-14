@extends('layouts.template')

@section('content')
<div class="relative z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true" id="confirmationModal">
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
            <div class="relative transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
                <button type="button" onclick="closeModal()" class="absolute top-3 right-3 text-gray-400 hover:text-gray-500">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
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
                    <div class="mt-10 text-center sm:text-left">
                        <div class="mt-2">
                            <p class="text-lg text-center text-black" id="modal-message">{{ $chatsPage->delete_messages_label??'Are you sure you want to delete this chat?' }}</p>
                        </div>
                    </div>
                </div>
                <div class="px-4 pb-6 pt-4 flex items-center space-x-2 sm:space-x-4 sm:px-6 justify-center" id="modal-buttons">
                    <button onclick="proceedWithDelete()" class="inline-flex justify-center rounded bg-red-500 px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-red-600 sm:ml-3 whitespace-nowrap">
                        {{ $successMessage->delete_button?? 'Delete'}} 
                    </button>
                    <button onclick="closeModal()" class="button-exp-fill whitespace-nowrap">
                        {{ $successMessage->cancel_button?? 'Cancel'}} 
                        
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container mx-auto my-14 p-4" data-page="my-chats">
    <div class="bg-white border rounded p-4 border-gray-200 w-full shadow max-w-4xl mx-auto">
        <div class="flex justify-between items-center">
            <h1 class="mb-0">{{ $chatsPage->main_heading ?? "Chats"}}</h1>
            <a class="button-exp-fill" href="{{ route('old_chats', ['lang' => optional($selectedLanguage)->abbreviation]) }}">{{ $chatsPage->old_messages_heading ?? "Old messages"}}</a>
        </div>

        <div class="flex flex-col space-y-4 mt-4 relative">
            
            @if ($chats && $chats->count() > 0)
                @foreach ($chats as $chat)
                    @php
                        $hasUnread = isset($chat['unread_count']) && $chat['unread_count'] > 0;
                    @endphp
                    <div class="relative w-full">
                        <div class="absolute top-3 right-4 w-auto h-auto flex items-center bg-primary text-white rounded-full p-1 z-10">
                            <button type="button" onclick="confirmDelete('{{ route('delete_chats', $chat) }}')"
                            class="text-gray-800 font-bold">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 text-white">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        @if ($chat['sender']['id'] == $user_id)
                        <a href="{{ route('chat_detail', ['lang' => $selectedLanguage->abbreviation,'id' => $chat['ride_id'],'passenger' => $chat['receiver']['id']]) }}">
                        @else
                        <a href="{{ route('chat_detail', ['lang' => $selectedLanguage->abbreviation,'id' => $chat['ride_id'],'passenger' => $chat['sender']['id']]) }}">
                        @endif
                            <div class="border rounded p-4 cursor-pointer relative {{ $hasUnread ? 'bg-blue-50 border-l-4 border-l-primary' : '' }} hover:bg-gray-50 transition-colors">

                                <!-- Display sender's information -->
                                <div class="flex justify-between items-end">
                                    <div class="flex gap-3 items-center">
                                        @if($chat['sender']['id'] == $user_id)
                                            <div class="relative flex-shrink-0">
                                                <img class="w-10 h-10 rounded-full object-cover" src="{{ $chat['receiver']['profile_image'] }}" alt="">
                                            </div>
                                            <div>
                                                <span class="{{ $hasUnread ? 'font-semibold' : '' }}">{{ $chat['receiver']['first_name'] }}</span>
                                                <p class="text-sm {{ $hasUnread ? 'text-gray-800' : 'text-gray-500' }} text-left">{{ $chat['message'] }}</p>
                                            </div>
                                        @else
                                            <div class="relative flex-shrink-0">
                                                <img class="w-10 h-10 rounded-full object-cover" src="{{ $chat['sender']['profile_image'] }}" alt="">
                                                @if ($hasUnread)
                                                    <span class="absolute -top-1 -right-1 h-3 w-3 bg-primary rounded-full border-2 border-white"></span>
                                                @endif
                                            </div>
                                            <div>
                                                <span class="{{ $hasUnread ? 'font-semibold' : '' }}">{{ $chat['sender']['first_name'] }}</span>
                                                @if ($hasUnread)
                                                    <span class="ml-2 bg-red-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $chat['unread_count'] }}</span>
                                                @endif
                                                <p class="text-sm {{ $hasUnread ? 'text-gray-800' : 'text-gray-500' }} text-left">{{ $chat['message'] }}</p>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="flex items-end">
                                        <p class="text-sm text-gray-500 text-left">
                                            {{ \Carbon\Carbon::parse($chat['created_at'])->format('h:i A, M d, Y') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            @else
                <p>{{ $chatsPage->no_messages_label ?? "No chats"}}</p>
            @endif
        </div>
    </div>
</div>
@endsection
@section('script')
<script>
    let currentDeleteUrl = '';
    
    function confirmDelete(deleteUrl) {
        currentDeleteUrl = deleteUrl;
        document.getElementById('confirmationModal').classList.remove('hidden');
    }
    
    function proceedWithDelete() {
        if (currentDeleteUrl) {
            window.location.href = currentDeleteUrl;
        }
    }
    
    function closeModal() {
        document.getElementById('confirmationModal').classList.add('hidden');
    }
</script>
@endsection
