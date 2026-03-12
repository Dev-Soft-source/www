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
                          <p class="can-exp-p text-center">{{ $siteText['notification_delete_text'] }}</p>
                      </div>
                  </div>
              </div>
              <div class="px-4 pb-6 pt-4 flex items-center space-x-2 sm:space-x-4 sm:px-6 justify-center">
                <input type="hidden" id="notificationId">
                  <a href="#" onclick="closeModal()" class="button-exp-fill">{{ $successMessage->cancel_button ??'Close'}} </a>
                  <a href="#" onclick="delete_notification()" class="button-exp-fill">{{ $successMessage->delete_button?? 'Yes'}} </a>
              </div>
          </div>
      </div>
  </div>
</div>

{{-- Modal: open general ProximaRide message --}}
<div id="general-message-modal" class="relative z-50 hidden" aria-labelledby="general-message-modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeGeneralMessageModal()"></div>
    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
                <div class="bg-white px-4 pb-4 pt-5 sm:p-6">
                    <button type="button" onclick="closeGeneralMessageModal()" class="absolute top-3 right-3 text-gray-400 hover:text-gray-500">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-primary/10 sm:mx-0 sm:h-10 sm:w-10">
                            <img src="{{ asset('assets/favicon.png') }}" alt="" class="h-6 w-6 w-8 h-8 object-contain">
                        </div>
                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left flex-1">
                            <h3 id="general-message-modal-title" class="text-xl font-semibold leading-6 text-gray-700">{{ config('app.name') }}</h3>
                            <p id="general-message-modal-date" class="text-sm text-gray-500 mt-1"></p>
                            <div class="mt-3">
                                <p id="general-message-modal-body" class="text-gray-700 whitespace-pre-wrap"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" onclick="closeGeneralMessageModal()" class="button-exp-fill w-full sm:ml-3 sm:w-auto">{{ $siteText['close_btn_text'] ??'Close' }}</button>
                </div>
            </div>
        </div>
    </div>
</div>

    <div class="container mx-auto p-4">
        <div class="flex items-center justify-between">
            <h1 class="font-FuturaMdCnBT text-primary mt-6">{{ $siteText['all_notifications_heading'] }}</h1>
            <div class="flex items-center gap-3">
                @if ($notifications && $notifications->where('is_read', 0)->count() > 0)
                    <div class="inline-flex items-center gap-3 border-2 border-blue-600 rounded-lg px-4 py-2 bg-gray-50">
                        <button type="button" onclick="markAllAsRead()" class="text-gray-800 hover:text-primary text-[1.3125rem] font-medium flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            {{ $notificationsPageSetting->mark_all_as_read_button_label }}
                        </button>
                        <span class="text-gray-800 text-[1.3125rem] bg-blue-600 text-white px-2 py-1 rounded-lg font-medium">{{ $notifications->where('is_read', 0)->count() }} {{ $notificationsPageSetting->unread_label }}</span>
                    </div>
                @endif
            </div>
        </div>

        <div id="notifications-info-bar" class="mb-4 rounded-lg overflow-hidden transition-all duration-300 ease-in-out">
            <button type="button" onclick="toggleNotificationsInfoBar()" class="w-full flex items-center justify-between gap-3 bg-teal-500 hover:bg-teal-600 text-white px-4 py-3 text-left transition-colors cursor-pointer" aria-expanded="false" aria-controls="notifications-info-bar-content">
                <span class="font-medium">{{ $notificationsPageSetting->info_bar_title }}</span>
                <svg id="notifications-info-bar-icon" class="w-5 h-5 flex-shrink-0 transition-transform duration-300 ease-in-out" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div id="notifications-info-bar-content" class="grid grid-rows-[0fr] transition-[grid-template-rows] duration-300 ease-in-out" role="region">
                <div class="overflow-hidden">
                    <div class="bg-white p-4 rounded-b-lg shadow border border-t-0 border-gray-200 text-gray-700 space-y-3">
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-lg bg-teal-500 text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M4 9a1 1 0 1 1-2 0 1 1 0 0 1 2 0Zm10 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0ZM6 8a1 1 0 0 0 0 2h4a1 1 0 1 0 0-2H6ZM4.862 4.276 3.906 6.19a.51.51 0 0 0 .497.731c.91-.073 2.35-.17 3.597-.17 1.247 0 2.688.097 3.597.17a.51.51 0 0 0 .497-.731l-.956-1.913A.5.5 0 0 0 10.691 4H5.309a.5.5 0 0 0-.447.276Z"/>
                                    <path d="M2.52 3.515A2.5 2.5 0 0 1 4.82 2h6.362c1 0 1.904.596 2.298 1.515l.792 1.848c.075.175.21.319.38.404.5.25.855.715.965 1.262l.335 1.679c.033.161.049.325.049.49v.413c0 .814-.39 1.543-1 1.997V13.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-1.338c-1.292.048-2.745.088-4 .088s-2.708-.04-4-.088V13.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-1.892c-.61-.454-1-1.183-1-1.997v-.413a2.5 2.5 0 0 1 .049-.49l.335-1.68c.11-.546.465-1.012.964-1.261a.807.807 0 0 0 .381-.404l.792-1.848ZM4.82 3a1.5 1.5 0 0 0-1.379.91l-.792 1.847a1.8 1.8 0 0 1-.853.904.807.807 0 0 0-.43.564L1.03 8.904a1.5 1.5 0 0 0-.03.294v.413c0 .796.62 1.448 1.408 1.484 1.555.07 3.786.155 5.592.155 1.806 0 4.037-.084 5.592-.155A1.479 1.479 0 0 0 15 9.611v-.413c0-.099-.01-.197-.03-.294l-.335-1.68a.807.807 0 0 0-.43-.563 1.807 1.807 0 0 1-.853-.904l-.792-1.848A1.5 1.5 0 0 0 11.18 3H4.82Z"/>
                                </svg>
                            </div>
                            <p class="flex-1 pt-1">{{ $notificationsPageSetting->info_paragraph_ride }}</p>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-lg bg-teal-500 text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                            </div>
                            <p class="flex-1 pt-1">{{ $notificationsPageSetting->info_paragraph_inbox }}</p>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-lg bg-teal-500 text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0018 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 00-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                                </svg>
                            </div>
                            <p class="flex-1 pt-1">{{ $notificationsPageSetting->info_paragraph_general }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="rounded-lg overflow-hidden bg-white shadow border border-gray-100">
            <div class="p-4">
                @if ($notifications && $notifications->count() > 0)
                    <ul class="divide-y divide-gray-100">
                                @foreach ($notifications as $notification)
                                    @if ($notification->from || ($notification->category == 'system' && $notification->notification_type == 'welcome'))
                                        @php
                                            $rowBg = $notification->is_read == 0
                                                ? 'bg-blue-50 border-l-4 border-l-primary'
                                                : ($loop->iteration % 2 === 1 ? 'bg-white' : 'bg-gray-50');
                                        @endphp
                                        <li class="relative {{ $rowBg }} hover:bg-gray-100 transition-colors">
                                            <button type="button" onclick="openModal('{{ $notification->id }}')" class="button-exp-fill absolute top-4 right-4 text-sm py-2 px-3">
                                                {{ $notificationsPageSetting->delete_button_label }}
                                            </button>
                                            @php
                                                // 1. Ride details: type 1 = my ride, type 2 = other's ride
                                                if ($notification->type == '1' && $notification->departure && $notification->destination) {
                                                    $targetUrl = route('my_ride_detail', ['lang' => optional($selectedLanguage)->abbreviation, 'departure' => $notification->departure, 'destination' => $notification->destination, 'id' => $notification->ride_id]);
                                                    $isGeneralUpdate = false;
                                                } elseif ($notification->type == '2' && $notification->departure && $notification->destination) {
                                                    $targetUrl = route('ride_detail', ['lang' => optional($selectedLanguage)->abbreviation, 'departure' => $notification->departure ?? 'unknown', 'destination' => $notification->destination ?? 'unknown', 'id' => $notification->ride_id ?? 0]);
                                                    $isGeneralUpdate = false;
                                                } elseif ($notification->category == 'system' && $notification->notification_type == 'welcome') {
                                                    $targetUrl = route('welcome_message', ['lang' => optional($selectedLanguage)->abbreviation]);
                                                    $isGeneralUpdate = false;
                                                } else {
                                                    $hasChatTarget = !empty($notification->ride_id) && !empty($notification->posted_by);
                                                    $isGeneralUpdate = $notification->category == 'system' && !$hasChatTarget;
                                                    $targetUrl = $hasChatTarget
                                                        ? route('chat_detail', ['lang' => optional($selectedLanguage)->abbreviation, 'id' => $notification->ride_id, 'passenger' => $notification->posted_by])
                                                        : route('my_chats', ['lang' => optional($selectedLanguage)->abbreviation]);
                                                }
                                            @endphp
                                            <a href="javascript:void(0);" class="block notification-link" data-id="{{ $notification->id }}" data-general="{{ $isGeneralUpdate ? '1' : '0' }}" data-target="{{ $targetUrl }}" @if($isGeneralUpdate) data-message="{{ e($notification->message) }}" data-added-on="{{ $notification->added_on ? \Carbon\Carbon::parse($notification->added_on)->format('M d, Y \a\t h:i A') : '' }}" @endif>
                                                <div class="flex gap-3 items-start px-4 py-4 pr-24">
                                                    <div class="flex-shrink-0 relative">
                                                        @php
                                                            $defaultImage = asset('assets/image-placeholder.png');
                                                            if ($notification->category == 'system') {
                                                                $imageSrc = asset('assets/favicon.png');
                                                            } elseif ($notification->from && !empty(trim($notification->from->profile_image ?? ''))) {
                                                                $imageSrc = $notification->from->profile_image;
                                                            } else {
                                                                $imageSrc = $defaultImage;
                                                            }
                                                        @endphp
                                                        <img class="w-12 h-12 rounded-full object-cover"
                                                            src="{{ $imageSrc }}"
                                                            alt="{{ $notification->category == 'system' ? 'System' : ($notification->from ? $notification->from->first_name : 'System') }}"
                                                            onerror="this.onerror=null; this.src='{{ $defaultImage }}';">
                                                        @if ($notification->is_read == 0)
                                                            <span class="absolute -top-1 -right-1 h-3 w-3 bg-primary rounded-full border-2 border-white"></span>
                                                        @endif
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <p class="font-medium text-gray-900 {{ $notification->is_read == 0 ? 'font-semibold' : '' }}">
                                                            @if ($notification->category == 'system')
                                                                {{ config('app.name') }}
                                                            @else
                                                                {{ $notification->from ? $notification->from->first_name : 'System' }}
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
                        <p class="mt-4 text-gray-500 font-medium">{{ $notificationsPageSetting->no_notifications_found_label }}</p>
                        <p class="text-sm text-gray-400 mt-1">{{ $notificationsPageSetting->caught_up_label }}</p>
                    </div>
                @endif
            </div>
        </div>

    </div>
@endsection
@section('script')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        let notificationsInfoBarOpen = false;

        function toggleNotificationsInfoBar() {
            notificationsInfoBarOpen = !notificationsInfoBarOpen;
            const content = document.getElementById('notifications-info-bar-content');
            const icon = document.getElementById('notifications-info-bar-icon');
            const btn = document.querySelector('#notifications-info-bar button[aria-controls="notifications-info-bar-content"]');
            if (notificationsInfoBarOpen) {
                content.classList.remove('grid-rows-[0fr]');
                content.classList.add('grid-rows-[1fr]');
                icon.style.transform = 'rotate(180deg)';
                if (btn) btn.setAttribute('aria-expanded', 'true');
            } else {
                content.classList.remove('grid-rows-[1fr]');
                content.classList.add('grid-rows-[0fr]');
                icon.style.transform = 'rotate(0deg)';
                if (btn) btn.setAttribute('aria-expanded', 'false');
            }
        }

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

        function openGeneralMessageModal(message, addedOn) {
            document.getElementById('general-message-modal-body').textContent = message || '';
            document.getElementById('general-message-modal-date').textContent = addedOn || '';
            document.getElementById('general-message-modal').classList.remove('hidden');
        }

        function closeGeneralMessageModal() {
            document.getElementById('general-message-modal').classList.add('hidden');
        }

        function markNotificationAsReadOnly(notificationId, callback) {
            $.ajax({
                url: "{{ route('web.read_notifications') }}",
                type: 'GET',
                data: { id: notificationId },
                success: function(response) {
                    if (typeof callback === 'function') callback();
                    else window.location.reload();
                },
                error: function(xhr) {
                    if (typeof callback === 'function') callback();
                    else window.location.reload();
                }
            });
        }

        $(document).on('click', '.notification-link', function(e) {
            e.preventDefault();
            var $link = $(this);
            const id = $link.data('id');
            const isGeneral = $link.data('general') === 1 || $link.data('general') === '1';
            const target = $link.data('target');
            if (isGeneral) {
                var message = $link.attr('data-message') || '';
                var addedOn = $link.attr('data-added-on') || '';
                openGeneralMessageModal(message, addedOn);
                markNotificationAsReadOnly(id, function() {
                    var $li = $link.closest('li');
                    $li.removeClass('bg-blue-50 border-l-4 border-l-primary').addClass('bg-gray-50');
                    $li.find('.font-semibold').removeClass('font-semibold');
                    $li.find('.text-gray-800').removeClass('text-gray-800');
                    $li.find('.absolute.-top-1.-right-1').remove();
                });
            } else {
                markNotificationAsReadAndRedirect(id, target);
            }
        });

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
