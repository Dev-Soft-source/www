<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/x-icon" href="/assets/favicon.png">
    <title>{{ config('app.name', 'Home') }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
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
            background-color: #F5F5F5;
        }

        ::-webkit-scrollbar {
            width: 10px;
            background-color: #F5F5F5;
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
        window.passenger = @json($passenger->id); // Pass $ride to JavaScript
        window.rideDetails = @json([
            'departure' => $ride->rideDetail[0]->departure ?? '',
            'destination' => $ride->rideDetail[0]->destination ?? '',
            'date' => $ride->date ?? '',
            'time' => $ride->time ?? ''
        ]);
    </script>
    <script>window.isOnChatDetailPage = true;</script>
    <script src="{{ asset('js/web.js') }}" defer></script>
</head>

<body>
    <div id="ridesharing_app" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center">
                <div
                    class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-4xl">
                    <div class="bg-white w-full">
                        <div class="">
                            <div class="text-center sm:text-left">


                                <div
                                    class="flex items-center justify-between w-full border-b px-4 py-2 bg-primary text-white">
                                    <h1 class="mb-0 text-white" id="modal-title">
                                        {{ $chatsPage->driver_chat_with ?? 'Chat with' }} {{ $passenger->first_name }}
                                    </h1>
                                    <a href="{{ route('my_chats', ['lang' => app()->getLocale()]) }}"
                                        class="h-fit block text-gray-100 bg-transparent rounded-full border border-gray-100 text-sm p-1 ml-auto">
                                        <svg aria-hidden="true" class="w-3 h-3 text-gray-100" fill="currentColor"
                                            viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd"
                                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                clip-rule="evenodd">
                                            </path>
                                        </svg>
                                    </a>
                                </div>
                                <div class="px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
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
                                            $contact_count = \App\Models\UserMessageCount::where('user_id', $user->id)
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
                                                in_array($passenger->id, explode(',', $contact_count->contact_user_id))
                                            ) {
                                                $allow_chat = true;
                                            }
                                        }

                                    @endphp
                                    <div class="text-center sm:text-left">
                                        {{-- <div class="ride-details-heading" style="font-weight:bold;color:#2563eb;margin-bottom:4px;">Ride Details</div>
                                        <div style="display:flex;justify-content:center;margin-bottom:8px;">
                                            <span class="bg-blue-100 p-2 rounded-md border-gray-200 w-fit whitespace-pre-wrap">
                                                @if(isset($ride) && $ride)
                                                    {{ $ride->departure ?? '-' }} to {{ $ride->destination ?? '-' }}
                                                    @if(!empty($ride->date))
                                                        {{ \Carbon\Carbon::parse($ride->date)->format('M d, Y') }}
                                                    @endif
                                                    @if(!empty($ride->time))
                                                        at {{ \Carbon\Carbon::parse($ride->time)->format('h:i A') }}
                                                    @endif
                                                @else
                                                    - Ride details not available -
                                                @endif
                                            </span>
                                        </div> --}}
                                        <div class="mt-2">
                                            <div class="panel-body">
                                                <chat-messages 
                                                    :logged_in_user_id="{{ Auth::user()->id }}"
                                                    current_lang="{{ $selectedLanguage->abbreviation }}"
                                                    empty_chat_placeholder="{{ $chatsPage->empty_chat_placeholder ?? 'No messages yet' }}"
                                                    :messages="messages">
                                                </chat-messages>
                                            </div>
                                            @php
                                                $currentDateTime = now();
                                                // Combine ride date and time into a single DateTime object
                                                $rideDateTime = \Carbon\Carbon::parse($ride->date . ' ' . $ride->time);
                                                // Calculate the difference in hours between the current time and the ride time
                                                $hoursDifference = $currentDateTime->diffInHours($rideDateTime);
                                            @endphp
                                            @if (strtotime($ride->date) < strtotime('today') ||
                                                    (strtotime($ride->date) == strtotime('today') && strtotime($ride->time) < strtotime('now')))
                                                @if ($hoursDifference <= 48)
                                                    <div class="panel-footer">
                                                        <chat-form allow_chat="{{ $allow_chat }}"
                                                            v-on:message-sent-event="addMessage"
                                                            :ride_id="{{ $ride->id }}" :user="{{ auth()->user() }}"
                                                            type_message_placeholder = "Please avoid sharing any contact details such as phone numbers, email addresses, or website links. Do not offer or agree to communicate or arrange payments outside the ProximaRide platform."></chat-form>
                                                    </div>
                                        </div>
                                        @endif
                                    @else
                                        <div class="panel-footer">
                                            <chat-form allow_chat="{{ $allow_chat }}"
                                                v-on:message-sent-event="addMessage" :ride_id="{{ $ride->id }}"
                                                :user="{{ auth()->user() }}"
                                                type_message_placeholder = "Please avoid sharing any contact details such as phone numbers, email addresses, or website links. Do not offer or agree to communicate or arrange payments outside the ProximaRide platform."></chat-form>
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
</body>

</html>
