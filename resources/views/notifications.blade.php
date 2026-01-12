{{-- @extends('layouts.app')

@section('content')

@endsection --}}
@extends('layouts.template')

@section('content')
<div class="relative z-50 hidden" id="delete_message_confirmation" aria-labelledby="modal-title" role="dialog" aria-modal="true">
  <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
  <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
      <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
          <div
              class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
              <button type="button" onclick="closeModal()" class="absolute top-3 right-3 text-gray-400 hover:text-gray-500">
                  <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
              </button>
              <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                  <div class="sm:flex sm:items-start justify-center">
                      <!-- <div
                          class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full sm:mx-0 sm:h-10 sm:w-10 bg-green-500">
                          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-lg text-white w-8 h-8" viewBox="0 0 16 16">
                              <path d="M7.005 3.1a1 1 0 1 1 1.99 0l-.388 6.35a.61.61 0 0 1-1.214 0zM7 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0"/>
                          </svg>
                      </div> -->
                  </div>
                  <div class="text-center sm:ml-4 sm:mt-0 sm:text-left">
                      <div class="">
                          <h3 class="text-3xl text-center font-FuturaMdCnBT font-medium text-gray-900 mb-4" id="modal-title">{!! session('heading') !!}</h3>
                      </div>
                      <div class="mt-2 w-full">
                          <p class="can-exp-p text-center">{{ $notificationPage->notification_delete_text?? ' Are you sure you want to delete?'}}</p>
                      </div>
                  </div>
              </div>
              <div class="px-4 pb-6 pt-4 flex items-center space-x-2 sm:space-x-4 sm:px-6 justify-center">
                <input type="hidden" id="notificationId">
                  <a href="#" onclick="closeModal()"
                      class="button-exp-fill">{{ $successMessage->cancel_button ??'Close'}} </a>
                      <a href="#" onclick="delete_notification()"
                      class="button-exp-fill">{{ $successMessage->delete_button?? 'Yes'}} </a>
              </div>
          </div>
      </div>
  </div>
</div>
    <div class="container mx-auto p-4">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl md:text-3xl font-bold font-FuturaMdCnBT text-gray-900">{{ $notificationPage->all_notifications_heading ?? 'All Notifications' }}</h1>
            <div class="flex items-center gap-3">
                @if ($notifications && $notifications->where('is_read', 0)->count() > 0)
                    <button type="button" onclick="markAllAsRead()" class="text-primary hover:text-primary/80 text-sm font-medium flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Mark all as read
                    </button>
                    <span class="bg-red-500 text-white text-sm px-3 py-1 rounded-full">{{ $notifications->where('is_read', 0)->count() }} unread</span>
                @endif
            </div>
        </div>

        <div class="bg-white p-4 rounded-lg shadow">
            @if ($notifications && $notifications->count() > 0)
                <ul class="divide-y divide-gray-100">
                    @foreach ($notifications as $notification)
                        @if ($notification->from)
                            <li class="relative {{ $notification->is_read == 0 ? 'bg-blue-50 border-l-4 border-l-primary' : 'bg-white' }} hover:bg-gray-50 transition-colors">
                                <button type="button" onclick="openModal('{{ $notification->id }}')" class="button-exp-fill absolute top-4 right-4 text-sm py-1 px-3">
                                    Delete
                                </button>
                                @php
                                    $hasChatTarget = !empty($notification->ride_id) && !empty($notification->posted_by);
                                    $targetUrl = $hasChatTarget
                                        ? route('chat_detail', ['lang' => optional($selectedLanguage)->abbreviation, 'id' => $notification->ride_id, 'passenger' => $notification->posted_by])
                                        : route('my_chats', ['lang' => optional($selectedLanguage)->abbreviation]);
                                @endphp
                                <a href="javascript:void(0);" onclick="markNotificationAsReadAndRedirect({{ $notification->id }}, '{{ $targetUrl }}')" class="block">
                                    <div class="flex gap-3 items-start px-4 py-4 pr-24">
                                        <div class="flex-shrink-0 relative">
                                            <img class="w-12 h-12 rounded-full object-cover"
                                                src="{{ $notification->category == 'system' ? asset('assets/favicon.png') : $notification->from->profile_image }}"
                                                alt="{{ $notification->category == 'system' ? 'System' : $notification->from->first_name }}">
                                            @if ($notification->is_read == 0)
                                                <span class="absolute -top-1 -right-1 h-3 w-3 bg-primary rounded-full border-2 border-white"></span>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="font-medium text-gray-900 {{ $notification->is_read == 0 ? 'font-semibold' : '' }}">
                                                @if ($notification->category == 'system')
                                                    {{ config('app.name') }}
                                                @else
                                                    {{ $notification->from->first_name }}
                                                @endif
                                            </p>
                                            <p class="text-gray-600 mt-1 {{ $notification->is_read == 0 ? 'text-gray-800' : '' }}">{{ $notification->message }}</p>
                                            <p class="text-sm text-gray-400 mt-2 flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                {{ \Carbon\Carbon::parse($notification->added_on)->format('M d, Y') }} at {{ \Carbon\Carbon::parse($notification->added_on)->format('h:i A') }}
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            </li>
                        @endif
                    @endforeach
                </ul>
            @else
                <div class="text-center py-12">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <p class="mt-4 text-gray-500 font-medium">No notifications found.</p>
                    <p class="text-sm text-gray-400 mt-1">You're all caught up!</p>
                </div>
            @endif
        </div>
    </div>
@endsection
@section('script')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        function openModal(id){
            $('#delete_message_confirmation').removeClass('hidden');
            $('#notificationId').val(id);
        }

        function closeModal(id){
            $('#delete_message_confirmation').addClass('hidden');
            $('#notificationId').val();
        }

        function delete_notification() {
            $.ajax({
                url: "{{ route('delete_notifications', ['lang' => $selectedLanguage->abbreviation]) }}",
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: $('#notificationId').val()
                },
                success: function(response) {
                    window.location.reload();
                },
                error: function(xhr) {
                    console.error(xhr);
                    alert('Failed to delete notification');
                }
            });
        }

        function markNotificationAsReadAndRedirect(notificationId, redirectUrl) {
            $.ajax({
                url: "{{ route('web.read_notifications') }}",
                type: 'GET',
                data: { id: notificationId },
                success: function(response) {
                    window.location.href = redirectUrl;
                },
                error: function(xhr) {
                    window.location.href = redirectUrl;
                }
            });
        }

        function markAllAsRead() {
            $.ajax({
                url: "{{ route('web.mark_all_notifications_read') }}",
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    window.location.reload();
                },
                error: function(xhr) {
                    console.error(xhr);
                    alert('Failed to mark notifications as read');
                }
            });
        }
    </script>
@endsection
