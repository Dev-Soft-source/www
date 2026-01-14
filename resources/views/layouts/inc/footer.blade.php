<footer class="py-10 md:py-14 w-full bg-indigo-800 px-4 sm:px-8 flex-initial hidefooter">
    <div class="container mx-auto">

      <div class="grid lg:grid-cols-6 md:grid-cols-4 grid-cols-2 gap-8 pb-7 md:pb-14">
        <div class="col-span-2 md:col-span-4 lg:col-span-2">
          <a
                @if (Route::currentRouteName() === 'step1to5' || Route::currentRouteName() === 'step2to5' || Route::currentRouteName() === 'step3to5' || Route::currentRouteName() === 'step4to5' || Route::currentRouteName() === 'step5to5')
                    href=""
                @else
                    href="{{ route('home', ['lang' => optional($selectedLanguage)->abbreviation]) }}"
                @endif>
                <img class="h-20 mx-auto" src="/assets/PROXIMARIDE.png" alt="">
            </a>
            <p class="text-white mt-8 text-center">Ride with Purpose. Powered by Community Values.</p>
        </div>
  
        <div>
            <p class="font-FuturaBdCnBT text-white text-lg">Useful links</p>
                
            <ul class="text-white space-y-2 mt-2">
              @if(auth()->check())
                <li><a
                  @if (Route::currentRouteName() === 'step1to5' || Route::currentRouteName() === 'step2to5' || Route::currentRouteName() === 'step3to5' || Route::currentRouteName() === 'step4to5' || Route::currentRouteName() === 'step5to5')
                      href=""
                  @else
                      href="{{ route('profile', ['lang' => optional($selectedLanguage)->abbreviation]) }}"
                  @endif
                  class="text-white">My Profile</a></li>
                <li><a class="text-white" href="{{ route('my_rides', ['lang' => optional($selectedLanguage)->abbreviation]) }}">My Rides</a></li>
              @else
                <li><a class="text-white" href="{{ route('signup', ['lang' => optional($selectedLanguage)->abbreviation]) }}">Sign up</a></li>
                <li><a class="text-white" href="{{ route('login', ['lang' => optional($selectedLanguage)->abbreviation]) }}">Log in</a></li>
              @endif
                <li><a
                  @if (Route::currentRouteName() === 'step1to5' || Route::currentRouteName() === 'step2to5' || Route::currentRouteName() === 'step3to5' || Route::currentRouteName() === 'step4to5' || Route::currentRouteName() === 'step5to5')
                      href=""
                  @else
                      href="{{ route('post_ride', ['lang' => optional($selectedLanguage)->abbreviation]) }}"
                  @endif
                  class="text-white">Post a Ride</a></li>
                <li><a
                  @if (Route::currentRouteName() === 'step1to5' || Route::currentRouteName() === 'step2to5' || Route::currentRouteName() === 'step3to5' || Route::currentRouteName() === 'step4to5' || Route::currentRouteName() === 'step5to5')
                      href=""
                  @else
                      href="{{ route('search_ride', ['lang' => optional($selectedLanguage)->abbreviation]) }}"
                  @endif
                  class="text-white">Find a Ride</a></li>
            </ul>
        </div>

        <div>
            <p class="font-FuturaBdCnBT text-white text-lg">How it works</p>
                
            <ul class="text-white space-y-2 mt-2">
                <li><a
                  href="{{ route('drivers', ['lang' => optional($selectedLanguage)->abbreviation]) }}"
                  class="text-white">For drivers</a></li>
                <li><a
                  href="{{ route('passengers', ['lang' => optional($selectedLanguage)->abbreviation]) }}"
                  class="text-white">For passengers</a></li>
                <li><a
                  href="{{ route('students', ['lang' => optional($selectedLanguage)->abbreviation]) }}"
                  class="text-white">For students</a></li>
                {{-- <li><a class="text-white" href="#">Help</a></li> --}}
            </ul>
        </div>

        <div>
            <p class="font-FuturaBdCnBT text-white text-lg">Contact us</p>
                
            <ul class="text-white space-y-2 mt-2">
                <li><a class="text-white" href="{{ route('contact_us', ['lang' => optional($selectedLanguage)->abbreviation]) }}">Contact us / Support</a></li>
                {{-- <li><a class="text-white" href="#">Partners</a></li> --}}
                <li><a class="text-white" href="{{ route('news', ['lang' => optional($selectedLanguage)->abbreviation]) }}">Media</a></li>
            </ul>
        </div>

        <div>
            <p class="font-FuturaBdCnBT text-white text-lg">Terms</p>
                
            <ul class="text-white space-y-2 mt-2">
                <li><a class="text-white" href="{{ route('terms_conditions', ['lang' => optional($selectedLanguage)->abbreviation]) }}">Terms and conditions</a></li>
                <li><a class="text-white" href="{{ route('terms_use', ['lang' => optional($selectedLanguage)->abbreviation]) }}">Terms of use</a></li>
                <li><a class="text-white" href="{{ route('privacy_policy', ['lang' => optional($selectedLanguage)->abbreviation]) }}">Privacy policy</a></li>
            </ul>
        </div>

      </div>
      <hr>
      @php
        $settingPage = App\Models\SiteSetting::first();
      @endphp
      <div class="flex items-center gap-4 justify-center mb-3 mt-4">
        <a aria-label="Candian Riders" target="_blank" href="{{ $settingPage->facebook }}" class="flex justify-center items-center bg-gray-50 border-2 border-gray-300 hover:border-blue-900 rounded-full h-10 w-10">
          <img class="h-5" src="/assets/icons/facebook.png" alt="facebook icon">
        </a>
        <a aria-label="Candian Riders" target="_blank" href="{{ $settingPage->instagram }}" class="flex justify-center items-center bg-gray-50 border-2 border-gray-300 hover:border-blue-900 rounded-full h-10 w-10">
          <img class="h-4" src="/assets/icons/instagaram canexp.png" alt="instagram icon">
        </a>
        <a aria-label="Candian Riders" target="_blank" href="{{ $settingPage->youtube }}" class="flex justify-center items-center bg-gray-50 border-2 border-gray-300 hover:border-blue-900 rounded-full h-10 w-10">
          <img class="h-4" src="/assets/icons/youtube.png" alt="youtube icon">
        </a>
        <a aria-label="Candian Riders" target="_blank" href="{{ $settingPage->twitter }}" class="flex justify-center items-center bg-gray-50 border-2 border-gray-300 hover:border-blue-900 rounded-full h-10 w-10">
          <img class="h-4" src="/assets/icons/twitter.png" alt="twiiter icon">
        </a>
        {{-- <a aria-label="Candian Riders" target="_blank" href="#" class="flex justify-center items-center bg-gray-50 border-2 border-gray-300 hover:border-blue-900 rounded-full h-10 w-10">
          <img class="h-5" src="/assets/icons/google.png" alt="google icon">
        </a> --}}
      </div>
      <div class="flex flex-col sm:flex-col md:flex-row lg:flex-row gap-4 justify-center items-center py-2 text-white">
        <p class="text-white">© ProximaRide 2024. All rights reserved</p>
        <div class="relative">
            <button id="dropdownDesktopButton" data-dropdown-toggle="dropdown_desktop" class="min-w-fit px-3 py-1.5 border border-white rounded flex gap-2 items-center bg-white/10 hover:bg-white/20 transition-colors" type="button">
                <img class="h-4" src="{{ $selectedLanguage->flag_icon ?? 'assets/flag.png' }}" alt="">
                <span class="truncate text-white">{{ $selectedLanguage->name ?? 'Eng' }}</span>
            </button>
            <!-- Dropdown menu -->
            <div id="dropdown_desktop" class="animate__animated animate__fadeIn absolute bottom-full right-0 mb-2 z-30 hidden bg-white divide-y divide-gray-100 rounded shadow w-32">
                <ul class="py-2 text-sm text-gray-700" aria-labelledby="dropdownDesktopButton">
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
      </div>

    </div>
</footer>