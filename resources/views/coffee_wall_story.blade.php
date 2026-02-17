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
                    <h1 class="text-2xl md:text-3xl font-FuturaMdCnBT text-stone-800 mb-2">Coffee on the Wall — A Story of Generosity and Dignity</h1>
                    <!-- <p class="text-stone-600 text-sm">by Rajni</p> -->
                </div>
            </header>

            {{-- Story body --}}
            <article class="bg-white rounded-2xl border border-stone-200/80 shadow-sm overflow-hidden">
                <div class="story-body p-6 md:p-10 text-stone-700 text-[1.0625rem] leading-relaxed overflow-hidden">
                    <div class="float-right w-60 h-40 md:w-68 md:h-58 ml-4 rounded-xl overflow-hidden shadow-md bg-white/80 flex items-center justify-center p-1 shrink-0">
                        <img src="{{ asset('images/Coffee_On_the_Wall.png') }}" alt="Coffee on the Wall" class="w-full h-full object-contain" loading="eager">
                    </div>
                    <p>One day, in a well‑known coffee shop in a small town near Venice, Italy, two friends were enjoying their coffee when a man entered and sat at a nearby table.</p>

                    <p>The man called the waiter and said, “Two cups of coffee — and put one on the wall.”</p>

                    <p>They watched as the waiter served him only one cup of coffee, yet the man paid for two. After he left, the waiter attached a small sign to the wall that read “A Cup of Coffee.”</p>

                    <p>Soon after, two more customers ordered coffee with “one on the wall” in the same way. They were served their own drinks, paid for an extra cup, and the waiter posted another “A Cup of Coffee” sign on the wall. </p>

                    <p>Curious, the friends finished their drinks and left the café. A few days later, they returned. As they were sitting, a poorly dressed man entered and placed his order by saying, “One cup of coffee from the wall.”</p>

                    <p>With respect and dignity, the waiter served him a coffee. The man drank it and left without paying. The friends saw the waiter remove one of the “A Cup of Coffee” notices from the wall and toss it away.</p>

                    <p>At that moment, the purpose of those cups on the wall became clear. The people of that town had created a way to help someone in need quietly and respectfully — so that someone could enjoy a cup of coffee without begging or embarrassment.</p>

                    <p>And indeed, that may be one of the most beautiful walls you could ever see.</p>

                </div>

                <footer class="px-6 md:px-10 py-6 bg-stone-50/80 border-t border-stone-200/80">
                    <p class="text-stone-500 text-sm mb-6">
                        This story has been shared online in many places as an example of quiet generosity; the exact original source is not known.
                    </p>
                    <div class="flex justify-center">
                        <a href="{{ route('coffee_on_wall', ['lang' => $selectedLanguage->abbreviation ?? 'en']) }}" class="inline-flex items-center justify-center bg-primary text-white px-6 py-3 rounded-xl font-medium hover:opacity-90 transition-opacity shadow-sm">
                            Back to Coffee on the Wall
                        </a>
                    </div>
                </footer>
            </article>
        </div>
    </div>
@endsection
