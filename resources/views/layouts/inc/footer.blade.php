@php
    $footerSetting = getFooterSetting($selectedLanguage ?? 1);
    $footerSettingDetail = $footerSetting->footerSettingDetail ?? [];
    $langAbbr = optional($selectedLanguage)->abbreviation ?? 'en';
    $isStepRoute = in_array(Route::currentRouteName(), ['step1to5', 'step2to5', 'step3to5', 'step4to5', 'step5to5']);
@endphp
<footer class="py-10 md:py-14 w-full bg-sky-700 px-4 sm:px-8 flex-initial hidefooter">
    <div class="container mx-auto">

      <div class="grid lg:grid-cols-5 md:grid-cols-3 grid-cols-2 gap-8 pb-7 md:pb-14">
        <div class="col-span-2 md:col-span-4 lg:col-span-2">
          <a
                @if ($isStepRoute)
                    href=""
                @else
                    href="{{ route('home', ['lang' => $langAbbr]) }}"
                @endif>
                <img class="h-20 mx-auto" src="/assets/PROXIMARIDE.png" alt="">
            </a>
            <p class="text-white mt-8 text-center">{{ getTranslatedText('footer_tagline', $selectedLanguage ?? 1, [], 'Ride with Purpose. Powered by Community Values.') }}</p>
        </div>

        @foreach ($footerSettingDetail as $section)
        <div>
            <p class="font-FuturaBdCnBT text-white text-lg">{{ $section->sectionTitle }}</p>
            <ul class="text-white space-y-2 mt-2">
                @foreach ($section->menuItems as $item)
                    @php
                        $link = $item['link'] ?? '';
                        $name = $item['name'] ?? '';
                        $authOnly = in_array($link, ['profile', 'my_rides']);
                        $guestOnly = in_array($link, ['signup', 'login']);
                        $show = (!$authOnly && !$guestOnly) || ($authOnly && auth()->check()) || ($guestOnly && !auth()->check());
                    @endphp
                    @if ($show && $link && \Illuminate\Support\Facades\Route::has($link))
                        <li>
                            <a class="text-white"
                                @if ($isStepRoute)
                                    href=""
                                @else
                                    href="{{ route($link, ['lang' => $langAbbr]) }}"
                                @endif
                            >{{ $name }}</a>
                        </li>
                    @endif
                @endforeach
            </ul>
        </div>
        @endforeach

      </div>
      <hr>
      @php
        $settingPage = App\Models\SiteSetting::getCached();
      @endphp
      {{-- social link list --}}
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
      </div>
      <div class="flex flex-row items-center pb-7 md:pb-14 relative">
        {{-- copyright --}}
        <div class="w-full flex justify-center">
          <p class="text-white">{!! getTranslatedText('footer_copyright', $selectedLanguage->id ?? 1, ['year' => date('Y')], '© ProximaRide ' . date('Y') . '. All rights reserved') !!}</p>
        </div> 
        {{-- multi language list --}}
        <div class="relative lg:w-1/2 flex justify-end lg:absolute lg:right-0">
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