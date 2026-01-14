<div class="hideheader ">

    <div class="relative z-50 hidden" id="delete_message_confirmation" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                <div class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
                    <button type="button" onclick="closeNotificationModal()" class="absolute top-3 right-3 text-gray-400 hover:text-gray-500">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start justify-center">
                        </div>
                        <div class="text-center sm:ml-4 sm:mt-0 sm:text-left">
                            <div class="">
                                <h3 class="text-3xl text-center font-FuturaMdCnBT text-gray-900 mb-4" id="modal-title">{!! session('heading') !!}</h3>
                            </div>
                            <div class="mt-2 w-full">
                                <p class="can-exp-p text-center font-FuturaMdCnBT">{{ $notificationPage->notification_delete_text ?? ' Are you sure you want to delete?'}}</p>
                            </div>
                        </div>
                    </div>
                    <div class="px-4 pb-6 pt-4 flex items-center space-x-2 sm:space-x-4 sm:px-6 justify-center">
                      <input type="hidden" id="notificationId">
                        <a href="#" onclick="closeNotificationModal()" class="button-exp-fill font-FuturaMdCnBT">{{ $successMessage->cancel_button ?? 'Close'}}</a>
                        <a href="#" onclick="delete_notification()" class="button-exp-fill bg-red-500 hover:bg-red-500 border-red-500 hover:border-red-500 font-FuturaMdCnBT">{{ $successMessage->delete_button ?? 'Yes'}}</a>
                    </div>
                </div>
            </div>
        </div>
      </div>
<nav class="hidden md:block bg-white sticky top-0 z-40 w-full shadow-md flex-initial" id="navbar">
    <div class="flex justify-between items-center container mx-auto px-4 xl:px-0">
        <div class="flex gap-x-5 justify-between items-center">
            <a href="{{ route('home', ['lang' => optional($selectedLanguage)->abbreviation]) }}">
                <img class="h-14" src="/assets/Proximaride logo.png" alt="">
            </a>
            <div>
                <ul class="inline-flex space-x-3 lg:space-x-6 text-blue-600 mt-2 font-FuturaMdCnBT">
                    @php
                        $studentPage = App\Models\StudentPageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                    @endphp
                    <li>
                        <a href="{{ route('students', ['lang' => optional($selectedLanguage)->abbreviation]) }}" class="flex gap-x-1 group items-center whitespace-nowrap">
                            <div>
                                @isset($studentPage->page_image)
                                    <img class="w-5 h-5 object-contain mt-1" src="{{ asset('home_page_icons/' . $studentPage->page_image)}}" alt="">
                                @endisset
                            </div>
                            <p class="text-blue-600">Students</p>
                        </a>
                    </li>
                    @php
                        $postRidePage = App\Models\PostRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                    @endphp
                    <li class="flex gap-x-1 items-center-">
                        <a href="{{ route('post_ride', ['lang' => optional($selectedLanguage)->abbreviation]) }}" class="flex gap-x-1 group items-center whitespace-nowrap">
                            <div>
                                @isset($postRidePage->navbar_icon)
                                    <img class="w-5 h-5 object-contain" src="{{ asset('home_page_icons/' . $postRidePage->navbar_icon)}}" alt="">
                                @endisset
                            </div>
                            <p class="text-blue-600">Post a Ride</p>
                        </a>
                    </li>
                    @php
                        $findRidePage = App\Models\FindRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                    @endphp
                    <li class="flex gap-x-1 items-center">
                        <a href="{{ route('search_ride', ['lang' => optional($selectedLanguage)->abbreviation]) }}" class="flex gap-x-1 group items-center whitespace-nowrap">
                            <div class="">
                                @isset($findRidePage->navbar_icon)
                                    <img class="w-5 h-5 object-contain" src="{{ asset('home_page_icons/' . $findRidePage->navbar_icon)}}" alt="">
                                @endisset
                            </div>
                            <p class="text-blue-600">Find a Ride</p>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="flex items-center justify-end space-x-2 w-1/2">
            @if(auth()->check())
                <div class="relative">
                    <a href="{{ route('coffee_on_wall', ['lang' => optional($selectedLanguage)->abbreviation]) }}" class="px-2 py-1.5 button-exp-no-fill ml-2 flex gap-2 items-center">
                        <span>Coffee on the Wall</span>
                    </a>
                </div>
                <div class="relative">
                    <div class="flex items-center gap-1">
                        <button id="dropdownNotificationButton" data-dropdown-toggle="dropdown_notification" class="flex items-center gap-1" type="button">
                            <div class="relative">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                                </svg>
                                <div class="absolute -top-3 -right-2 p-1 bg-red-500 rounded-full text-white text-[7pt] z-10 h-5 w-5 flex items-center justify-center">
                                    {{ $notifications->where('is_read',0)->count() }}
                                </div>
                            </div>
                        </button>
                    </div>
                    <!-- Dropdown menu -->
                    <div id="dropdown_notification" class="animate__animated animate__fadeIn absolute right-4 z-30 hidden bg-white divide-y divide-gray-100 rounded shadow w-80 top-10">
                        <div class="p-4">
                            <a href="{{ route('notifications', ['lang' => $selectedLanguage->abbreviation]) }}" class="w-full text-center font-FuturaMdCnBT bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                              View All
                            </a>
                          </div>
                          <ul class="max-h-[400px] overflow-y-auto rounded-lg shadow-lg" aria-labelledby="dropdownNotificationButton">
                                @if ($notifications && $notifications->count() > 0)
                                    @foreach ($notifications as $notification)
                                        <li class="relative {{ $notification->is_read == 0 ? 'bg-blue-50' : 'bg-white' }}">
                                            <button onclick="openModal('{{ $notification->id }}')" class="text-gray-400 hover:text-gray-500 transition-colors bg-primary h-5 w-5 rounded-full flex items-center justify-center absolute top-3 right-2 z-10" aria-label="Delete notification">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                             @if ($notification->from || ($notification->category == 'system' && $notification->notification_type == 'welcome'))
                                             <a @if ($notification->type == '1')
                                                 @if($notification->departure && $notification->destination)
                                                 href="{{ route('my_ride_detail', ['lang' => $selectedLanguage->abbreviation, 'departure' => $notification->departure , 'destination' => $notification->destination , 'id' => $notification->ride_id]) }}"
                                                 @endif
                                                 @elseif ($notification->type == '2')
                                                     href="{{ route('ride_detail', [
                                                         'lang' => $selectedLanguage->abbreviation ?? 'en',
                                                         'departure' => $notification->departure ?? 'unknown',
                                                         'destination' => $notification->destination ?? 'unknown',
                                                         'id' => $notification->ride_id ?? 0
                                                     ]) }}"
                                                @elseif ($notification->type == null)
                                                    @php
                                                        // Check if it's a welcome/system notification
                                                        if ($notification->category == 'system' && $notification->notification_type == 'welcome') {
                                                            $targetUrl = route('notifications', ['lang' => optional($selectedLanguage)->abbreviation]);
                                                        } else {
                                                            $hasChatTarget = !empty($notification->ride_id) && !empty($notification->posted_by);
                                                            $targetUrl = $hasChatTarget
                                                                ? route('chat_detail', ['lang' => optional($selectedLanguage)->abbreviation, 'id' => $notification->ride_id, 'passenger' => $notification->posted_by])
                                                                : route('my_chats', ['lang' => optional($selectedLanguage)->abbreviation]);
                                                        }
                                                    @endphp
                                                    href="javascript:void(0);"
                                                    onclick="markNotificationAsReadAndRedirect({{ $notification->id }}, '{{ $targetUrl }}')"
                                                @endif
                                                 class="block border-b border-gray-100 last:border-0 hover:bg-gray-50 transition-colors duration-150">
                                                    <div class="flex gap-3 p-4 relative">
                                                        <div class="flex-shrink-0 relative">
                                                            <img class="w-10 h-10 rounded-full object-cover"
                                                                src="{{ $notification->category == 'system' ? asset('assets/favicon.png') : ($notification->from ? $notification->from->profile_image : asset('assets/favicon.png')) }}"
                                                                alt="{{ $notification->category == 'system' ? 'System' : ($notification->from ? $notification->from->first_name : 'System') }}'s profile">
                                                            @if ($notification->is_read == 0)
                                                                <span class="absolute -top-1 -right-1 h-3 w-3 bg-primary rounded-full border-2 border-white"></span>
                                                            @endif
                                                        </div>
                                                        <div class="flex-1 min-w-0">
                                                            <p class="text-sm {{ $notification->is_read == 0 ? 'font-semibold text-primary' : 'font-medium text-gray-800' }} whitespace-pre-line">{{ $notification->message }}</p>
                                                            <p class="text-xs text-gray-400 mt-2 flex items-center">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                </svg>
                                                                {{ \Carbon\Carbon::parse($notification->added_on)->format('M d, Y \a\t h:i A') }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </a>
                                            @endif
                                        </li>
                                    @endforeach
                                @else
                                    <li class="p-6 text-center">
                                        <div class="text-gray-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                            </svg>
                                            <p class="mt-2 text-sm font-medium text-gray-500">No notifications yet</p>
                                            <p class="text-xs text-gray-400 mt-1">We'll notify you when something arrives</p>
                                        </div>
                                    </li>
                                @endif
                            </ul>
                    </div>
                </div>
                <div class="relative">
                    <div class="border-l pl-3 flex items-center gap-1">
                        <button id="dropdownProfileButton" data-dropdown-toggle="dropdown_profile" class="pr-2 flex items-center gap-1" type="button">
                            <span class="text-lg text-gray-800 font-FuturaMdCnBT">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</span>
                            <div class="h-10 w-10 bg-gray-500 rounded-full flex items-center justify-center"><img class="h-10 w-10 rounded-full object-cover" src="{{ auth()->user()->profile_image }}" alt="">
                            </div>
                        </button>
                    </div>
                    <!-- Dropdown menu -->
                    <div id="dropdown_profile" class="animate__animated animate__fadeIn absolute right-4 z-30 hidden bg-white divide-y divide-gray-100 rounded shadow w-40">
                        <ul class="py-2 text-sm text-gray-700 font-FuturaMdCnBT" aria-labelledby="dropdownProfileButton">
                            <li>
                                <a href="{{ route('profile', ['lang' => optional($selectedLanguage)->abbreviation]) }}" class="flex gap-2 items-center px-4 py-2 hover:bg-gray-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    My Profile
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('my_rides', ['lang' => optional($selectedLanguage)->abbreviation]) }}" class="flex gap-2 items-center px-4 py-2 hover:bg-gray-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-car-front w-6 h-6 p-0.5" viewBox="0 0 16 16">
                                        <path d="M4 9a1 1 0 1 1-2 0 1 1 0 0 1 2 0Zm10 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0ZM6 8a1 1 0 0 0 0 2h4a1 1 0 1 0 0-2H6ZM4.862 4.276 3.906 6.19a.51.51 0 0 0 .497.731c.91-.073 2.35-.17 3.597-.17 1.247 0 2.688.097 3.597.17a.51.51 0 0 0 .497-.731l-.956-1.913A.5.5 0 0 0 10.691 4H5.309a.5.5 0 0 0-.447.276Z"/>
                                        <path d="M2.52 3.515A2.5 2.5 0 0 1 4.82 2h6.362c1 0 1.904.596 2.298 1.515l.792 1.848c.075.175.21.319.38.404.5.25.855.715.965 1.262l.335 1.679c.033.161.049.325.049.49v.413c0 .814-.39 1.543-1 1.997V13.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-1.338c-1.292.048-2.745.088-4 .088s-2.708-.04-4-.088V13.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-1.892c-.61-.454-1-1.183-1-1.997v-.413a2.5 2.5 0 0 1 .049-.49l.335-1.68c.11-.546.465-1.012.964-1.261a.807.807 0 0 0 .381-.404l.792-1.848ZM4.82 3a1.5 1.5 0 0 0-1.379.91l-.792 1.847a1.8 1.8 0 0 1-.853.904.807.807 0 0 0-.43.564L1.03 8.904a1.5 1.5 0 0 0-.03.294v.413c0 .796.62 1.448 1.408 1.484 1.555.07 3.786.155 5.592.155 1.806 0 4.037-.084 5.592-.155A1.479 1.479 0 0 0 15 9.611v-.413c0-.099-.01-.197-.03-.294l-.335-1.68a.807.807 0 0 0-.43-.563 1.807 1.807 0 0 1-.853-.904l-.792-1.848A1.5 1.5 0 0 0 11.18 3H4.82Z"/>
                                    </svg>
                                    My Rides
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('my_chats', ['lang' => optional($selectedLanguage)->abbreviation]) }}" class="flex gap-2 items-center px-4 py-2 hover:bg-gray-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6 p-0.5 text-primary">
                                        <path fill-rule="evenodd" d="M12 2.25c-2.429 0-4.817.178-7.152.521C2.87 3.061 1.5 4.795 1.5 6.741v6.018c0 1.946 1.37 3.68 3.348 3.97.877.129 1.761.234 2.652.316V21a.75.75 0 0 0 1.28.53l4.184-4.183a.39.39 0 0 1 .266-.112c2.006-.05 3.982-.22 5.922-.506 1.978-.29 3.348-2.023 3.348-3.97V6.741c0-1.947-1.37-3.68-3.348-3.97A49.145 49.145 0 0 0 12 2.25ZM8.25 8.625a1.125 1.125 0 1 0 0 2.25 1.125 1.125 0 0 0 0-2.25Zm2.625 1.125a1.125 1.125 0 1 1 2.25 0 1.125 1.125 0 0 1-2.25 0Zm4.875-1.125a1.125 1.125 0 1 0 0 2.25 1.125 1.125 0 0 0 0-2.25Z" clip-rule="evenodd" />
                                    </svg>
                                    My Chats
                                </a>
                            </li>
                            <li>
                                <a href="#" type="button" onclick="toggleModal('logout-modal')" class="flex gap-2 items-center px-4 py-2 hover:bg-gray-100 text-black">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Sign out
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            @else
                <div class="relative">
                    <a href="{{ route('coffee_on_wall', ['lang' => optional($selectedLanguage)->abbreviation]) }}" class="px-2 py-1.5 button-exp-no-fill ml-1 flex gap-2 items-center">
                        <span>Coffee on the Wall</span>
                    </a>
                </div>
                <div class="relative">
                    <a href="{{ route('login', ['lang' => optional($selectedLanguage)->abbreviation]) }}" class="px-2 py-1.5 button-exp-no-fill ml-1 flex gap-2 items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                        <span>Log in / Sign up</span>
                    </a>
                </div>
            @endif
        </div>
    </div>
</nav>

<!--responsive nav-->

<nav class="bg-gray-50 border-gray-200 md:hidden">
    <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto px-4">
        <a href="{{ route('home', ['lang' => optional($selectedLanguage)->abbreviation]) }}" class="flex items-center">
            <img class="h-14" src="{{asset('/assets/logo (6).png')}}" alt="">
        </a>
        <div class="flex gap-2 items-center">
            @if (auth()->check())
                <div class="">
                    <button data-collapse-toggle="profilebar-default" type="button" class="pr-2 border-r flex items-center gap-1" aria-controls="profilebar-default" aria-expanded="false">
                        <div class="h-10 w-10 bg-gray-500 rounded-full flex items-center justify-center"><img class="rounded-full h-full" src="{{ auth()->user()->profile_image }}" alt=""></div>
                    </button>
                </div>
            @endif

            <div class="relative">
                <button id="dropdownMobileButton" data-dropdown-toggle="dropdown_mobile" class="px-2 py-1.5 border border-gray-300 rounded ml-2 flex gap-2 items-center" type="button"><img class="h-4" src="{{ $selectedLanguage->flag_icon ?? 'assets/flag.png' }}" alt=""><span>{{ $selectedLanguage->name ?? 'Eng' }}</span></button>
                <!-- Dropdown menu -->
                <div id="dropdown_mobile" class="absolute right-0 z-30 hidden bg-white divide-y divide-gray-100 rounded shadow w-32">
                    <ul class="py-2 text-sm text-gray-700" aria-labelledby="dropdownMobileButton">
                        @foreach ($languages as $language)
                            @php
                                $languageParameter = 'lang';
                                $currentRoute = app('router')->getCurrentRoute();
                                $routeParams = $currentRoute->parameters();
                                $routeParams['lang'] = $language->abbreviation;
                                $queryParameters = request()->query();
                                $routeParams = array_merge($routeParams, $queryParameters);
                                if ($currentRoute->getName() === 'news_detail') {
                                    $languageUrl = route('news', ['lang' => $language->abbreviation]);
                                } else {
                                    $languageUrl = route($currentRoute->getName(), $routeParams);
                                }
                            @endphp
                            <li>
                                <a href="{{ $languageUrl }}"
                                    class="flex gap-2 items-center px-4 py-2 hover:bg-gray-100 @isset($selectedLanguage){{ $selectedLanguage->name === $language->name ? 'text-primary font-medium' : 'text-gray-700 font-normal' }}@endisset">
                                    <img class="h-4" src="{{ $language->flag_icon }}" alt="">
                                    {{ $language->name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <button data-collapse-toggle="navbar-default" type="button" class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200" aria-controls="navbar-default" aria-expanded="false">
                <span class="sr-only">Open main menu</span>
                <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 17 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h15M1 7h15M1 13h15"/>
                </svg>
            </button>
        </div>
    </div>
    <div class="hidden w-full md:block md:w-auto" id="navbar-default">
        <ul class="font-FuturaMdCnBT text-blue-600 flex flex-col space-y-2 mt-2 pb-4 border-t pt-2 md:flex-row md:space-x-8 md:mt-0 md:border-0">
            <li>
                <a href="{{ route('students', ['lang' => optional($selectedLanguage)->abbreviation]) }}" class="flex gap-x-1 group items-center px-4 py-2 hover:bg-blue-600 hover:text-white rounded-md">
                    @isset($studentPage->page_image)
                        <img class="w-5 h-5 object-contain mt-1" src="{{ asset('home_page_icons/' . $studentPage->page_image)}}" alt="">
                    @endisset
                    <p>Students</p>
                </a>
            </li>
            <li>
                <a href="{{ route('post_ride', ['lang' => optional($selectedLanguage)->abbreviation]) }}" class="flex gap-x-1 group items-center px-4 py-2 hover:bg-blue-600 hover:text-white rounded-md">
                    @isset($postRidePage->navbar_icon)
                        <img class="w-5 h-5 object-contain" src="{{ asset('home_page_icons/' . $postRidePage->navbar_icon)}}" alt="">
                    @endisset
                    <p>Post a Ride</p>
                </a>
            </li>
            <li>
                <a href="{{ route('search_ride', ['lang' => optional($selectedLanguage)->abbreviation]) }}" class="flex gap-x-1 group items-center px-4 py-2 hover:bg-blue-600 hover:text-white rounded-md">
                    @isset($findRidePage->navbar_icon)
                        <img class="w-5 h-5 object-contain" src="{{ asset('home_page_icons/' . $findRidePage->navbar_icon)}}" alt="">
                    @endisset
                    <p>Find a Ride</p>
                </a>
            </li>
            <li>
                <div class="flex items-center gap-2 justify-center mt-2">
                    <a href="{{ route('coffee_on_wall', ['lang' => optional($selectedLanguage)->abbreviation]) }}" class="button-exp-no-fill">
                        <span>Coffee on the Wall</span>
                    </a>
                    @if(!Auth::check())
                        <a href="{{ route('login', ['lang' => optional($selectedLanguage)->abbreviation]) }}" class="button-exp-no-fill flex items-center justify-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                            <span>Log in / Sign up</span>
                        </a>
                    @endif
                </div>
            </li>
        </ul>
    </div>

    <div class="hidden w-full md:block md:w-auto" id="profilebar-default">
        <ul class="font-FuturaMdCnBT text-blue-600 flex flex-col space-y-2 mt-2 pb-4 border-t pt-2 md:flex-row md:space-x-8 md:mt-0 md:border-0">
            <li>
                <a href="{{ route('profile', ['lang' => optional($selectedLanguage)->abbreviation]) }}" class="flex gap-2 items-center px-4 py-2 hover:bg-gray-100">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    My Profile
                </a>
            </li>
            <li>
                <a href="{{ route('my_rides', ['lang' => optional($selectedLanguage)->abbreviation]) }}" class="flex gap-2 items-center px-4 py-2 hover:bg-gray-100">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-car-front w-6 h-6 p-0.5" viewBox="0 0 16 16">
                        <path d="M4 9a1 1 0 1 1-2 0 1 1 0 0 1 2 0Zm10 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0ZM6 8a1 1 0 0 0 0 2h4a1 1 0 1 0 0-2H6ZM4.862 4.276 3.906 6.19a.51.51 0 0 0 .497.731c.91-.073 2.35-.17 3.597-.17 1.247 0 2.688.097 3.597.17a.51.51 0 0 0 .497-.731l-.956-1.913A.5.5 0 0 0 10.691 4H5.309a.5.5 0 0 0-.447.276Z"/>
                        <path d="M2.52 3.515A2.5 2.5 0 0 1 4.82 2h6.362c1 0 1.904.596 2.298 1.515l.792 1.848c.075.175.21.319.38.404.5.25.855.715.965 1.262l.335 1.679c.033.161.049.325.049.49v.413c0 .814-.39 1.543-1 1.997V13.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-1.338c-1.292.048-2.745.088-4 .088s-2.708-.04-4-.088V13.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-1.892c-.61-.454-1-1.183-1-1.997v-.413a2.5 2.5 0 0 1 .049-.49l.335-1.68c.11-.546.465-1.012.964-1.261a.807.807 0 0 0 .381-.404l.792-1.848ZM4.82 3a1.5 1.5 0 0 0-1.379.91l-.792 1.847a1.8 1.8 0 0 1-.853.904.807.807 0 0 0-.43.564L1.03 8.904a1.5 1.5 0 0 0-.03.294v.413c0 .796.62 1.448 1.408 1.484 1.555.07 3.786.155 5.592.155 1.806 0 4.037-.084 5.592-.155A1.479 1.479 0 0 0 15 9.611v-.413c0-.099-.01-.197-.03-.294l-.335-1.68a.807.807 0 0 0-.43-.563 1.807 1.807 0 0 1-.853-.904l-.792-1.848A1.5 1.5 0 0 0 11.18 3H4.82Z"/>
                    </svg>
                    My Rides
                </a>
            </li>
            <li>
                <a href="{{ route('my_chats', ['lang' => optional($selectedLanguage)->abbreviation]) }}" class="flex gap-2 items-center px-4 py-2 hover:bg-gray-100">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6 p-0.5 text-primary">
                        <path fill-rule="evenodd" d="M12 2.25c-2.429 0-4.817.178-7.152.521C2.87 3.061 1.5 4.795 1.5 6.741v6.018c0 1.946 1.37 3.68 3.348 3.97.877.129 1.761.234 2.652.316V21a.75.75 0 0 0 1.28.53l4.184-4.183a.39.39 0 0 1 .266-.112c2.006-.05 3.982-.22 5.922-.506 1.978-.29 3.348-2.023 3.348-3.97V6.741c0-1.947-1.37-3.68-3.348-3.97A49.145 49.145 0 0 0 12 2.25ZM8.25 8.625a1.125 1.125 0 1 0 0 2.25 1.125 1.125 0 0 0 0-2.25Zm2.625 1.125a1.125 1.125 0 1 1 2.25 0 1.125 1.125 0 0 1-2.25 0Zm4.875-1.125a1.125 1.125 0 1 0 0 2.25 1.125 1.125 0 0 0 0-2.25Z" clip-rule="evenodd" />
                    </svg>
                    My Chats
                </a>
            </li>
            <li>
                <a type="button" onclick="toggleModal('logout-modal')" class="flex gap-2 items-center px-4 py-2 hover:bg-gray-100">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Sign out
                </a>
            </li>
        </ul>
    </div>
</nav>
</div>
<script>
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

function markNotificationAsRead(notificationId) {
    $.ajax({
        url: "{{ route('web.read_notifications') }}",
        type: 'GET',
        data: { id: notificationId }
    });
}
</script>
