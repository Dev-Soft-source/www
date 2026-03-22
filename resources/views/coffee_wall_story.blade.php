@extends('layouts.template')

@section('title', 'Coffee on the Wall – Story')

@section('style')
<style>
    .story-body p { margin-bottom: 1.25em; }
    .story-body p:first-of-type::first-letter { font-size: 3.25rem; line-height: 1; float: left; margin: 0.1em 0.12em 0 0; font-family: Georgia, serif; color: #92400e; }
    .story-pull { padding-left: 1.25rem; margin: 2rem 0; font-style: italic; color: #57534e; }
</style>
@endsection

@section('content')
    <div class="min-h-screen bg-amber-50/40">
        <div class="container mx-auto px-4 py-8 md:py-12 max-w-2xl">

            {{-- Hero: title --}}
            <header class="rounded-2xl overflow-hidden bg-gradient-to-br from-amber-100 to-stone-100 border border-amber-200/60 shadow-sm mb-2">
                <div class="pt-8 pb-2 px-6">
                    <!-- <p class="text-amber-800/80 text-xs uppercase tracking-widest font-medium mb-1">A story of kindness</p> -->
                    <h1 class="text-2xl md:text-3xl font-FuturaMdCnBT text-stone-800 mb-2">@isset($coffeeWallPage->main_story_heading) {{ $coffeeWallPage->main_story_heading }} @endisset</h1>
                    <!-- <p class="text-stone-600 text-sm">by Rajni</p> -->
                </div>
            </header>

            {{-- Story body --}}
            <article class="bg-white rounded-2xl border border-stone-200/80 shadow-sm overflow-hidden">
                <div class="story-body p-6 md:p-10 text-stone-700 text-[1.0625rem] leading-relaxed overflow-hidden">
                    <div class="float-right w-60 h-40 md:w-68 md:h-58 ml-4 rounded-xl overflow-hidden shadow-md bg-white/80 flex items-center justify-center p-1 shrink-0">
                        <img src="{{ asset('images/Coffee_On_the_Wall.png') }}" alt="Coffee on the Wall" class="w-full h-full object-contain" loading="eager">
                    </div>
                    @isset($coffeeWallPage->main_story_text)
                        {!! $coffeeWallPage->main_story_text !!}
                    @endisset

                </div>

                <footer class="px-6 md:px-10 py-6 bg-stone-50/80 border-t border-stone-200/80">
                    <p class="text-stone-500 text-sm mb-6">
                        @isset($coffeeWallPage->footer_story_text)
                            {!! $coffeeWallPage->footer_story_text !!}
                        @endisset
                    </p>
                    <div class="flex justify-center mb-8">
                        <a href="{{ route('coffee_on_wall', ['lang' => $selectedLanguage->abbreviation ?? 'en']) }}" class="button-exp-fill">
                            @isset($coffeeWallPage->back_to_coffee_on_wall_button_text) {{ $coffeeWallPage->back_to_coffee_on_wall_button_text }} @endisset
                        </a>
                    </div>
                </footer>
            </article>
        </div>
    </div>
@endsection