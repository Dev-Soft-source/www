{{-- @php
        $sitePage = App\Models\SiteSetting::first();
@endphp --}}

<div class="profile-sidebar-container flex flex-col col-span-12 lg:col-span-3 relative">
    <button id="profile-sidebar-toggle"
        class="profile-sidebar-toggle bg-white border rounded p-4 border-gray-200 w-full shadow lg:hidden">
        <ul class="divide-y">
            <li class=" group transition-all ease-in-out flex items-center space-x-3 text-blue-600">
                <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 17 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h15M1 7h15M1 13h15"></path>
                </svg>
                <p class="text-blue-600">
                    {{ auth()->user()->first_name }} profile
                </p>
            </li>
        </ul>
    </button>

    <div id="profile-sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 hidden z-40 lg:hidden"></div>
    <div id="profile-sidebar"
        class="profile-sidebar fixed top-0 left-0 h-full overflow-y-auto bg-white w-72 lg:w-full transform -translate-x-full lg:translate-x-0 lg:static lg:h-auto transition-transform duration-300 z-40">
        <button id="profile-sidebar-close"
            class="profile-sidebar-close text-gray-500 text-xl absolute top-4 right-4 hover:text-red-500 lg:hidden">
            &times;
        </button>
        <div
            class="profile-sidebar-menu bg-white border rounded p-4 border-gray-200 w-full col-span-12 lg:col-span-3 shadow">
            <ul class="divide-y">
                <li class="py-2 group transition-all ease-in-out">
                    <a href="{{ Route::currentRouteName() === 'profile' ? '#' : route('profile', ['lang' => $selectedLanguage->abbreviation]) }}"
                        class="text-xl md:text-2xl flex items-center gap-2 text-black font-FuturaMdCnBT">
                        <!-- <svg class="{{ Route::currentRouteName() === 'profile' || Route::currentRouteName() === 'profile.edit' ? 'block' : 'hidden group-hover:block' }}"
                            xmlns="http://www.w3.org/2000/svg" width="9" height="12" viewBox="0 0 9 18">
                            <path id="Icon_ionic-md-arrow-dropup" data-name="Icon ionic-md-arrow-dropup"
                                d="M9,22.5l9-9,9,9Z" transform="translate(22.5 -9) rotate(90)" fill="currentcolor" />
                        </svg> -->
                        <span>{{ auth()->user()->first_name }} profile</span>
                    </a>
                </li>
            </ul>

            <ul class="">
                <li class="flex items-center space-x-1 text-xl md:text-2xl mb-2 font-FuturaMdCnBT ">
                    {{-- @isset($sitePage->menu_icon_profile_setting)
                        <img class="w-5 h-5 object-contain mt-1" src="{{ asset('home_page_icons/' . $sitePage->menu_icon_profile_setting)}}" alt="">
                    @endisset --}}
                    @isset($ProfilePage->profile_setting_label)
                        <span>{{ $ProfilePage->profile_setting_label }}</span>
                    @endisset
                </li>
                <li class="py-2 group transition-all ease-in-out">
                    <a href="{{ Route::currentRouteName() === 'profile.photo' ? '#' : route('profile.photo', ['lang' => $selectedLanguage->abbreviation]) }}"
                        class="flex items-center gap-2 hover:text-blue-600 active:text-blue-600 focus:text-blue-600 font-FuturaMdCnBT text-base md:text-lg bg-primary/10 border p-2 rounded">
                        <svg class="{{ Route::currentRouteName() === 'profile.photo' ? 'block' : 'hidden group-hover:block' }}"
                            xmlns="http://www.w3.org/2000/svg" width="9" height="12" viewBox="0 0 9 18">
                            <path id="Icon_ionic-md-arrow-dropup" data-name="Icon ionic-md-arrow-dropup"
                                d="M9,22.5l9-9,9,9Z" transform="translate(22.5 -9) rotate(90)" fill="currentcolor" />
                        </svg>
                        {{-- @isset($sitePage->profile_setting_profile_photo)
                            <img class="w-5 h-5 object-contain mt-1" src="{{ asset('home_page_icons/' . $sitePage->profile_setting_profile_photo)}}" alt="">
                        @endisset --}}
                        <span>
                            @isset($ProfileSetting->profile_photo_label)
                                {{ $ProfileSetting->profile_photo_label }}
                            @endisset
                        </span>
                    </a>
                </li>
                <li class="py-2 group transition-all ease-in-out">
                    <a href="{{ Route::currentRouteName() === 'profile.vehicle' ? '#' : route('profile.vehicle', ['lang' => $selectedLanguage->abbreviation]) }}"
                        class="flex items-center gap-2 hover:text-blue-600 active:text-blue-600 focus:text-blue-600 font-FuturaMdCnBT text-base md:text-lg bg-primary/10 border p-2 rounded">
                        <svg class="{{ Route::currentRouteName() === 'profile.vehicle' || Route::currentRouteName() === 'profile.vehicle.create' || Route::currentRouteName() === 'profile.vehicle.edit' ? 'block' : 'hidden group-hover:block' }}"
                            xmlns="http://www.w3.org/2000/svg" width="9" height="12" viewBox="0 0 9 18">
                            <path id="Icon_ionic-md-arrow-dropup" data-name="Icon ionic-md-arrow-dropup"
                                d="M9,22.5l9-9,9,9Z" transform="translate(22.5 -9) rotate(90)" fill="currentcolor" />
                        </svg>
                        {{-- @isset($sitePage->profile_setting_my_vehicle)
                            <img class="w-5 h-5 object-contain mt-1" src="{{ asset('home_page_icons/' . $sitePage->profile_setting_my_vehicle)}}" alt="">
                        @endisset --}}
                        <span>
                            @isset($ProfileSetting->my_vehicles_label)
                                {{ $ProfileSetting->my_vehicles_label }}
                            @endisset
                        </span>
                    </a>
                </li>
                <li class="py-2 group transition-all ease-in-out">
                    <a href="{{ Route::currentRouteName() === 'password' ? '#' : route('password', ['lang' => $selectedLanguage->abbreviation]) }}"
                        class="flex items-center gap-2 hover:text-blue-600 active:text-blue-600 focus:text-blue-600 font-FuturaMdCnBT text-base md:text-lg bg-primary/10 border p-2 rounded">
                        <svg class="{{ Route::currentRouteName() === 'password' ? 'block' : 'hidden group-hover:block' }}"
                            xmlns="http://www.w3.org/2000/svg" width="9" height="12" viewBox="0 0 9 18">
                            <path id="Icon_ionic-md-arrow-dropup" data-name="Icon ionic-md-arrow-dropup"
                                d="M9,22.5l9-9,9,9Z" transform="translate(22.5 -9) rotate(90)" fill="currentcolor" />
                        </svg>
                        {{-- @isset($sitePage->profile_setting_password)
                            <img class="w-5 h-5 object-contain mt-1" src="{{ asset('home_page_icons/' . $sitePage->profile_setting_password)}}" alt="">
                        @endisset --}}
                        <span>
                            @isset($ProfileSetting->password_label)
                                {{ $ProfileSetting->password_label }}
                            @endisset
                        </span>
                    </a>
                </li>
                <li class="py-2 group transition-all ease-in-out">
                    <a href="{{ Route::currentRouteName() === 'phone' ? '#' : route('phone', ['lang' => $selectedLanguage->abbreviation]) }}"
                        class="flex items-center gap-2 hover:text-blue-600 active:text-blue-600 focus:text-blue-600 font-FuturaMdCnBT text-base md:text-lg bg-primary/10 border p-2 rounded">
                        <svg class="{{ Route::currentRouteName() === 'phone' ? 'block' : 'hidden group-hover:block' }}"
                            xmlns="http://www.w3.org/2000/svg" width="9" height="12" viewBox="0 0 9 18">
                            <path id="Icon_ionic-md-arrow-dropup" data-name="Icon ionic-md-arrow-dropup"
                                d="M9,22.5l9-9,9,9Z" transform="translate(22.5 -9) rotate(90)" fill="currentcolor" />
                        </svg>
                        {{-- @isset($sitePage->profile_setting_my_phone_number)
                            <img class="w-5 h-5 object-contain mt-1" src="{{ asset('home_page_icons/' . $sitePage->profile_setting_my_phone_number)}}" alt="">
                        @endisset --}}
                        <span>
                            @isset($ProfileSetting->my_phone_number_label)
                                {{ $ProfileSetting->my_phone_number_label }}
                            @endisset
                        </span>
                    </a>
                </li>
                <li class="py-2 group transition-all ease-in-out">
                    <a href="{{ Route::currentRouteName() === 'email' ? '#' : route('email', ['lang' => $selectedLanguage->abbreviation]) }}"
                        class="flex items-center gap-2 hover:text-blue-600 active:text-blue-600 focus:text-blue-600 font-FuturaMdCnBT text-base md:text-lg bg-primary/10 border p-2 rounded">
                        <svg class="{{ Route::currentRouteName() === 'email' ? 'block' : 'hidden group-hover:block' }}"
                            xmlns="http://www.w3.org/2000/svg" width="9" height="12" viewBox="0 0 9 18">
                            <path id="Icon_ionic-md-arrow-dropup" data-name="Icon ionic-md-arrow-dropup"
                                d="M9,22.5l9-9,9,9Z" transform="translate(22.5 -9) rotate(90)" fill="currentcolor" />
                        </svg>
                        {{-- @isset($sitePage->profile_setting_my_email_address)
                            <img class="w-5 h-5 object-contain mt-1" src="{{ asset('home_page_icons/' . $sitePage->profile_setting_my_email_address)}}" alt="">
                        @endisset --}}
                        <span>
                            @isset($ProfileSetting->my_email_address_label)
                                {{ $ProfileSetting->my_email_address_label }}
                            @endisset
                        </span>
                    </a>
                </li>
                <li class="py-2 group transition-all ease-in-out">
                    <a href="{{ Route::currentRouteName() === 'driver.verify' ? '#' : route('driver.verify', ['lang' => $selectedLanguage->abbreviation]) }}"
                        class="flex items-center gap-2 hover:text-blue-600 active:text-blue-600 focus:text-blue-600 font-FuturaMdCnBT text-base md:text-lg bg-primary/10 border p-2 rounded">
                        <svg class="{{ Route::currentRouteName() === 'driver.verify' ? 'block' : 'hidden group-hover:block' }}"
                            xmlns="http://www.w3.org/2000/svg" width="9" height="12" viewBox="0 0 9 18">
                            <path id="Icon_ionic-md-arrow-dropup" data-name="Icon ionic-md-arrow-dropup"
                                d="M9,22.5l9-9,9,9Z" transform="translate(22.5 -9) rotate(90)" fill="currentcolor" />
                        </svg>
                        {{-- @isset($sitePage->profile_setting_my_drivers_license)
                            <img class="w-5 h-5 object-contain mt-1" src="{{ asset('home_page_icons/' . $sitePage->profile_setting_my_drivers_license)}}" alt="">
                        @endisset --}}
                        <span>
                            @isset($ProfileSetting->my_driver_license_label)
                                {{ $ProfileSetting->my_driver_license_label }}
                            @endisset
                        </span>
                    </a>
                </li>
                <li class="py-2 group transition-all ease-in-out">
                    <a href="{{ Route::currentRouteName() === 'student.verify' ? '#' : route('student.verify', ['lang' => $selectedLanguage->abbreviation]) }}"
                        class="flex items-center gap-2 hover:text-blue-600 active:text-blue-600 focus:text-blue-600 font-FuturaMdCnBT text-base md:text-lg bg-primary/10 border p-2 rounded">
                        <svg class="{{ Route::currentRouteName() === 'student.verify' ? 'block' : 'hidden group-hover:block' }}"
                            xmlns="http://www.w3.org/2000/svg" width="9" height="12" viewBox="0 0 9 18">
                            <path id="Icon_ionic-md-arrow-dropup" data-name="Icon ionic-md-arrow-dropup"
                                d="M9,22.5l9-9,9,9Z" transform="translate(22.5 -9) rotate(90)" fill="currentcolor" />
                        </svg>
                        {{-- @isset($sitePage->profile_setting_my_student_card)
                            <img class="w-5 h-5 object-contain mt-1" src="{{ asset('home_page_icons/' . $sitePage->profile_setting_my_student_card)}}" alt="">
                        @endisset --}}
                        <span>
                            @isset($ProfileSetting->my_student_card_label)
                                {{ $ProfileSetting->my_student_card_label }}
                            @endisset
                        </span>
                    </a>
                </li>
                <li class="py-2 group transition-all ease-in-out">
                    <a href="{{ Route::currentRouteName() === 'profile.referrals' ? '#' : route('profile.referrals', ['lang' => $selectedLanguage->abbreviation]) }}"
                        class="flex items-center gap-2 hover:text-blue-600 active:text-blue-600 focus:text-blue-600 font-FuturaMdCnBT text-base md:text-lg bg-primary/10 border p-2 rounded">
                        <svg class="{{ Route::currentRouteName() === 'profile.referrals' ? 'block' : 'hidden group-hover:block' }}"
                            xmlns="http://www.w3.org/2000/svg" width="9" height="12" viewBox="0 0 9 18">
                            <path id="Icon_ionic-md-arrow-dropup" data-name="Icon ionic-md-arrow-dropup"
                                d="M9,22.5l9-9,9,9Z" transform="translate(22.5 -9) rotate(90)" fill="currentcolor" />
                        </svg>
                        {{-- @isset($sitePage->profile_setting_referrals)
                            <img class="w-5 h-5 object-contain mt-1" src="{{ asset('home_page_icons/' . $sitePage->profile_setting_referrals)}}" alt="">
                        @endisset --}}
                        <span>
                            @isset($ProfileSetting->referrals_label)
                                {{ $ProfileSetting->referrals_label }}
                            @endisset
                        </span>
                    </a>
                </li>
            </ul>

            <ul class="">
                <li class="py-1 group transition-all ease-in-out">
                    <a href="{{ Route::currentRouteName() === 'passenger_wallet_rides' ? '#' : route('passenger_wallet_rides', ['lang' => $selectedLanguage->abbreviation]) }}"
                        class="text-xl md:text-2xl mb-2 font-FuturaMdCnBT flex items-center gap-2 text-black">
                        <svg class="{{ Route::currentRouteName() === 'passenger_wallet_rides' ? 'block' : 'hidden group-hover:block' }}"
                            xmlns="http://www.w3.org/2000/svg" width="9" height="12" viewBox="0 0 9 18">
                            <path id="Icon_ionic-md-arrow-dropup" data-name="Icon ionic-md-arrow-dropup"
                                d="M9,22.5l9-9,9,9Z" transform="translate(22.5 -9) rotate(90)" fill="currentcolor" />
                        </svg>
                        {{-- @isset($sitePage->menu_icon_my_wallet)
                            <img class="w-5 h-5 object-contain mt-1" src="{{ asset('home_page_icons/' . $sitePage->menu_icon_my_wallet)}}" alt="">
                        @endisset --}}
                        <span>
                            @isset($ProfilePage->my_wallet_label)
                                {{ $ProfilePage->my_wallet_label }}
                            @endisset
                        </span>
                    </a>
                </li>
            </ul>

            <ul class="md:py-0.5">
                <li class="group transition-all ease-in-out">
                    <a href="{{ Route::currentRouteName() === 'my_cards' ? '#' : route('my_cards', ['lang' => $selectedLanguage->abbreviation]) }}"
                        class="text-xl md:text-2xl mb-2 font-FuturaMdCnBT flex items-center gap-2 text-black">
                        <svg class="{{ Route::currentRouteName() === 'my_cards' ? 'block' : 'hidden group-hover:block' }}"
                            xmlns="http://www.w3.org/2000/svg" width="9" height="12" viewBox="0 0 9 18">
                            <path id="Icon_ionic-md-arrow-dropup" data-name="Icon ionic-md-arrow-dropup"
                                d="M9,22.5l9-9,9,9Z" transform="translate(22.5 -9) rotate(90)" fill="currentcolor" />
                        </svg>
                        {{-- @isset($sitePage->menu_icon_payment_option)
                            <img class="w-5 h-5 object-contain mt-1" src="{{ asset('home_page_icons/' . $sitePage->menu_icon_payment_option)}}" alt="">
                        @endisset --}}
                        <span>
                            @isset($ProfilePage->payment_options_label)
                                {{ $ProfilePage->payment_options_label }}
                            @endisset
                        </span>
                    </a>
                </li>
            </ul>
            <ul class="md:py-0.5">
                <li class="group transition-all ease-in-out">
                    <a href="{{ Route::currentRouteName() === 'payout' ? '#' : route('payout', ['lang' => $selectedLanguage->abbreviation]) }}"
                        class="text-xl md:text-2xl mb-2 font-FuturaMdCnBT flex items-center gap-2 text-black">
                        <svg class="hidden group-hover:block" xmlns="http://www.w3.org/2000/svg" width="9"
                            height="12" viewBox="0 0 9 18">
                            <path id="Icon_ionic-md-arrow-dropup" data-name="Icon ionic-md-arrow-dropup"
                                d="M9,22.5l9-9,9,9Z" transform="translate(22.5 -9) rotate(90)" fill="currentcolor" />
                        </svg>
                        <span>
                            @isset($ProfilePage->payout_options_label)
                                {{ $ProfilePage->payout_options_label }}
                            @endisset
                        </span>
                    </a>
                </li>
            </ul>

            <ul class="">
                <li class="flex space-x-1 items-center text-xl md:text-2xl mb-2 font-FuturaMdCnBT ">
                    {{-- @isset($sitePage->menu_icon_my_reviews)
                        <img class="w-5 h-5 object-contain mt-1" src="{{ asset('home_page_icons/' . $sitePage->menu_icon_my_reviews)}}" alt="">
                    @endisset --}}
                    @isset($ProfilePage->my_reviews_label)
                        <span>{{ $ProfilePage->my_reviews_label }}</span>
                    @endisset
                </li>
                <li class="py-2 group transition-all ease-in-out">
                    <a href="{{ Route::currentRouteName() === 'ratings.received' ? '#' : route('ratings.received', ['lang' => $selectedLanguage->abbreviation]) }}"
                        class="flex items-center gap-2 hover:text-blue-600 active:text-blue-600 focus:text-blue-600 font-FuturaMdCnBT text-base md:text-lg bg-primary/10 border p-2 rounded">
                        <svg class="{{ Route::currentRouteName() === 'ratings.received' || Route::currentRouteName() === 'ratings.receivedByPassengers' ? 'block' : 'hidden group-hover:block' }}"
                            xmlns="http://www.w3.org/2000/svg" width="9" height="12" viewBox="0 0 9 18">
                            <path id="Icon_ionic-md-arrow-dropup" data-name="Icon ionic-md-arrow-dropup"
                                d="M9,22.5l9-9,9,9Z" transform="translate(22.5 -9) rotate(90)" fill="currentcolor" />
                        </svg>
                        <span>
                            @isset($reviewSetting->review_received_label)
                                {{ $reviewSetting->review_received_label }}
                            @endisset
                        </span>
                    </a>
                </li>
                <li class="py-2 group transition-all ease-in-out">
                    <a href="{{ Route::currentRouteName() === 'ratings.left' ? '#' : route('ratings.left', ['lang' => $selectedLanguage->abbreviation]) }}"
                        class="flex items-center gap-2 hover:text-blue-600 active:text-blue-600 focus:text-blue-600 font-FuturaMdCnBT text-base md:text-lg bg-primary/10 border p-2 rounded">
                        <svg class="{{ Route::currentRouteName() === 'ratings.left' || Route::currentRouteName() === 'ratings.leftToPassengers' ? 'block' : 'hidden group-hover:block' }}"
                            xmlns="http://www.w3.org/2000/svg" width="9" height="12" viewBox="0 0 9 18">
                            <path id="Icon_ionic-md-arrow-dropup" data-name="Icon ionic-md-arrow-dropup"
                                d="M9,22.5l9-9,9,9Z" transform="translate(22.5 -9) rotate(90)" fill="currentcolor" />
                        </svg>
                        <span>
                            @isset($reviewSetting->review_left_label)
                                {{ $reviewSetting->review_left_label }}
                            @endisset
                        </span>
                    </a>
                </li>
            </ul>

           
            {{-- <ul class="md:py-0.5">
                <li class="group transition-all ease-in-out">
                    <a href="{{ route('terms_conditions', ['lang' => $selectedLanguage->abbreviation]) }}"
                        class="text-xl md:text-2xl mb-2 font-medium font-FuturaMdCnBT flex items-center gap-2 text-black">
                        <svg class="{{ Route::currentRouteName() === 'terms_conditions' ? 'block' : 'hidden group-hover:block' }}"
                            xmlns="http://www.w3.org/2000/svg" width="9" height="12" viewBox="0 0 9 18">
                            <path id="Icon_ionic-md-arrow-dropup" data-name="Icon ionic-md-arrow-dropup"
                                d="M9,22.5l9-9,9,9Z" transform="translate(22.5 -9) rotate(90)" fill="currentcolor" />
                        </svg>
                        <span>
                            @isset($ProfilePage->terms_condition_label)
                                {{ $ProfilePage->terms_condition_label }}
                            @endisset
                        </span>
                    </a>
                </li>
            </ul> --}}

            {{-- <ul class="md:py-0.5">
                <li class="group transition-all ease-in-out">
                    <a href="{{ route('privacy_policy', ['lang' => $selectedLanguage->abbreviation]) }}"
                        class="text-xl md:text-2xl mb-2 font-medium font-FuturaMdCnBT flex items-center gap-2 text-black">
                        <svg class="{{ Route::currentRouteName() === 'privacy_policy' ? 'block' : 'hidden group-hover:block' }}"
                            xmlns="http://www.w3.org/2000/svg" width="9" height="12" viewBox="0 0 9 18">
                            <path id="Icon_ionic-md-arrow-dropup" data-name="Icon ionic-md-arrow-dropup"
                                d="M9,22.5l9-9,9,9Z" transform="translate(22.5 -9) rotate(90)" fill="currentcolor" />
                        </svg>
                        <span>
                            @isset($ProfilePage->privacy_policy_label)
                                {{ $ProfilePage->privacy_policy_label }}
                            @endisset
                        </span>
                    </a>
                </li>
            </ul> --}}
{{-- 
            <ul class="md:py-0.5">
                <li class="group transition-all ease-in-out">
                    <a href="{{ route('contact_us', ['lang' => $selectedLanguage->abbreviation]) }}"
                        class="text-xl md:text-2xl mb-2 font-medium font-FuturaMdCnBT flex items-center gap-2 text-black">
                        <svg class="{{ Route::currentRouteName() === 'contact_us' ? 'block' : 'hidden group-hover:block' }}"
                            xmlns="http://www.w3.org/2000/svg" width="9" height="12" viewBox="0 0 9 18">
                            <path id="Icon_ionic-md-arrow-dropup" data-name="Icon ionic-md-arrow-dropup"
                                d="M9,22.5l9-9,9,9Z" transform="translate(22.5 -9) rotate(90)" fill="currentcolor" />
                        </svg>
                        <span>
                            @isset($ProfilePage->contact_proximaride_label)
                                {{ $ProfilePage->contact_proximaride_label }}
                            @endisset
                        </span>
                    </a>
                </li>
            </ul> --}}

            <ul class="md:py-0.5 divide-y">
                <li class="group transition-all ease-in-out">
                    <button type="button" onclick="toggleModal('logout-modal')"
                        class="text-xl md:text-2xl mb-2 font-FuturaMdCnBT flex items-center gap-2 text-black">
                        <svg class="hidden group-hover:block" xmlns="http://www.w3.org/2000/svg" width="9"
                            height="12" viewBox="0 0 9 18">
                            <path id="Icon_ionic-md-arrow-dropup" data-name="Icon ionic-md-arrow-dropup"
                                d="M9,22.5l9-9,9,9Z" transform="translate(22.5 -9) rotate(90)" fill="currentcolor" />
                        </svg>
                        {{-- @isset($sitePage->menu_icon_log_out)
                            <img class="w-5 h-5 object-contain mt-1" src="{{ asset('home_page_icons/' . $sitePage->menu_icon_log_out)}}" alt="">
                        @endisset --}}
                        <span>
                            @isset($ProfilePage->logout_label)
                                {{ $ProfilePage->logout_label }}
                            @endisset
                        </span>
                    </button>
                </li>
            </ul>

            <ul class="md:py-0.5 divide-y">
                <li class="group transition-all ease-in-out">
                    <a href="{{ Route::currentRouteName() === 'close_account' ? '#' : route('close_account', ['lang' => $selectedLanguage->abbreviation]) }}"
                        class="text-xl md:text-2xl mb-2 font-FuturaMdCnBT flex items-center gap-2 text-black">
                        <svg class="{{ Route::currentRouteName() === 'close_account' ? 'block' : 'hidden group-hover:block' }}"
                            xmlns="http://www.w3.org/2000/svg" width="9" height="12" viewBox="0 0 9 18">
                            <path id="Icon_ionic-md-arrow-dropup" data-name="Icon ionic-md-arrow-dropup"
                                d="M9,22.5l9-9,9,9Z" transform="translate(22.5 -9) rotate(90)" fill="currentcolor" />
                        </svg>
                        {{-- @isset($sitePage->meanu_icon_close_your_account)
                            <img class="w-5 h-5 object-contain mt-1" src="{{ asset('home_page_icons/' . $sitePage->meanu_icon_close_your_account)}}" alt="">
                        @endisset --}}
                        <span>
                            @isset($ProfilePage->colse_your_contact_label)
                                {{ $ProfilePage->colse_your_contact_label }}
                            @endisset
                        </span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>



<script>
    document.addEventListener('DOMContentLoaded', () => {
        const toggle = document.getElementById('profile-sidebar-toggle');
        const close = document.getElementById('profile-sidebar-close');
        const sidebar = document.getElementById('profile-sidebar');
        const overlay = document.getElementById('profile-sidebar-overlay');

        const toggleSidebar = (show) => {
            sidebar.classList.toggle('-translate-x-full', !show);
            overlay.classList.toggle('hidden', !show);
        };

        toggle.addEventListener('click', () => toggleSidebar(true));
        close.addEventListener('click', () => toggleSidebar(false));
        overlay.addEventListener('click', () => toggleSidebar(false));
    });
</script>
