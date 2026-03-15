@extends('layouts.template')

@section('title', optional($thankyouPage)->welcome_page_title ?? 'Welcome to ProximaRide')

@section('content')
<div class="mx-auto max-w-3xl px-4 py-8 sm:py-12">
    <div class="bg-white rounded-lg shadow-md border border-gray-100 overflow-hidden">
        <div class="border-b-2 border-black bg-white px-4 py-6 sm:px-6 flex justify-center">
            <a href="{{ route('home', ['lang' => optional($selectedLanguage)->abbreviation]) }}">
                <img src="{{ asset('assets/PROXIMARIDE.png') }}" alt="ProximaRide" class="h-20 sm:h-24">
            </a>
        </div>
        <div class="px-4 py-6 sm:px-6 sm:py-8 space-y-4 text-gray-900">
            <h1 class="text-2xl sm:text-3xl font-bold font-FuturaMdCnBT">{{ $greeting_message ?? 'Hi' }} {{ $data['first_name'] }},</h1>
            <p class="text-base text-gray-700">{{ optional($thankyouPage)->welcome_paragraph_1 ?? "Thanks for signing up to ProximaRide, and welcome!" }}</p>
            <p class="text-base text-gray-700">{{ optional($thankyouPage)->welcome_paragraph_2 ?? "I'm Erman, a dad and the founder, and glad that you decided to join us. I started ProximaRide because I wanted to make ridesharing safer, more affordable and more reliable for people like my daughter, who travels to her school in Ottawa (from Montreal) every week. Everyone should arrive at their destination safely, just like her." }}</p>
            <p class="text-base text-gray-700">{{ optional($thankyouPage)->welcome_paragraph_3 ?? "Don't worry, we don't send a lot of emails; just the essentials. So, just relax and enjoy the ride." }}</p>
            <p class="text-base text-gray-700">{{ optional($thankyouPage)->welcome_paragraph_4 ?? "We are always here to answer any queries that you may have so feel free to contact us. And remember - sharing is caring." }}</p>
            <p class="text-base text-gray-700">{{ optional($thankyouPage)->welcome_paragraph_5 ?? "By the way, have you completed your profile yet? If not yet, click the button below to do so; it's only four easy steps and only takes a couple of minutes." }}</p>
            <div class="pt-2 flex justify-center">
                <a href="{{ route('welcomeRoute', ['email' => $data['email']]) }}" class="inline-flex items-center justify-center rounded-lg bg-primary px-6 py-3 font-FuturaMdCnBT text-base text-white hover:bg-blue-600 shadow-sm">
                    {{ optional($thankyouPage)->welcome_complete_profile_btn ?? "Complete my profile" }}
                </a>
            </div>
            <p class="text-base text-gray-700 pt-6">
                <span class="font-normal">{{ optional($thankyouPage)->welcome_closing_line1 ?? "Again, thank you for joining, and welcome" }}</span><br>
                <strong>{{ optional($thankyouPage)->welcome_closing_line2 ?? "Erman, ProximaRide Founder" }}</strong><br>
                {{ optional($thankyouPage)->welcome_closing_team_text ?? "And the entire " . config('app.name') . " Team" }}
            </p>
        </div>
        <div class="border-t border-gray-200 px-4 py-4 sm:px-6 flex flex-wrap items-center justify-center gap-4 sm:gap-6">
            <a href="{{ route('contact_us', optional($selectedLanguage)->abbreviation ?? 'en') }}" class="font-FuturaMdCnBT font-semibold text-gray-900 hover:text-primary">{{ optional($thankyouPage)->welcome_footer_help_contact ?? 'Help & Contact' }}</a>
            <a href="{{ route('terms_use', optional($selectedLanguage)->abbreviation ?? 'en') }}" class="font-FuturaMdCnBT font-semibold text-gray-900 hover:text-primary">{{ optional($thankyouPage)->welcome_footer_terms_use ?? 'Terms of Use' }}</a>
            <a href="{{ route('coffee_on_wall', optional($selectedLanguage)->abbreviation ?? 'en') }}" class="font-FuturaMdCnBT font-semibold text-gray-900 hover:text-primary">{{ optional($thankyouPage)->welcome_footer_coffee_on_wall ?? 'Coffee on the Wall' }}</a>
        </div>
    </div>
</div>
@endsection
