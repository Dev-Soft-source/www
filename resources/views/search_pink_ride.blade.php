@extends('px.search_template')

@section('style')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        /* Shared style for all checkbox & radio inputs */
        .form-check-input {
            margin-top: 0.1rem; /* mt-2 */
            width: 1.25rem;       /* w-4 */
            height: 1.25rem;      /* h-4 */
            cursor: pointer;
            background-color: #ffffff; /* bg-white */
            border-width: 1px;
            border-color: #d1d5db;     /* border-gray-300 */
            border-radius: 0.25rem;    /* rounded */
        }

        .form-check-input:focus {
            outline: 2px solid transparent;
            outline-offset: 2px;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5); /* approx focus:ring-blue-500 focus:ring-2 */
        }
        /* FAQ - Pink Rides: themed bar (blue) and smooth expand/collapse */
        .pink-ride-faq {
            border: 1px solid rgba(3, 105, 161, 0.3);
            border-radius: 0.75rem;
            overflow: hidden;
            box-shadow: 0 4px 14px rgba(3, 105, 161, 0.08);
        }

        .pink-ride-faq__header {
            background: linear-gradient(135deg, rgb(255, 255, 255) 0%, rgb(172, 186, 194) 100%);
            color: #fff;
            padding: 1rem 1.25rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.15);
        }

        .pink-ride-faq__body {
            background: #f0f9ff;
            padding: 0.75rem;
        }

        .pink-ride-faq__item {
            margin-bottom: 0.5rem;
            border-radius: 0.5rem;
            overflow: hidden;
            border: 1px solid rgba(3, 105, 161, 0.2);
        }

        .pink-ride-faq__item:last-child {
            margin-bottom: 0;
        }

        .pink-ride-faq__question {
            width: 100%;
            text-align: left;
            padding: 0.875rem 1rem;
            font-size: 1rem;
            font-weight: 500;
            color: #1f2937;
            background: #fff;
            border: none;
            cursor: pointer;
            transition: background-color 0.25s ease, color 0.25s ease;
            font-family: inherit;
            border-radius: 0.5rem;
        }

        .pink-ride-faq__question:hover {
            background: #eff6ff;
            color: #0369a1;
        }

        .pink-ride-faq__question[aria-expanded="true"] {
            background: #0369a1;
            color: #fff;
            border-radius: 0.5rem 0.5rem 0 0;
        }

        .pink-ride-faq__question[aria-expanded="true"]:hover {
            background: #0284c7;
            color: #fff;
        }

        .pink-ride-faq__answer {
            overflow: hidden;
            height: 0;
            transition: height 0.35s ease-out;
        }

        .pink-ride-faq__answer-inner {
            padding: 1rem 1.25rem;
            background: #fff;
            border: 1px solid rgba(3, 105, 161, 0.2);
            border-top: none;
            border-radius: 0 0 0.5rem 0.5rem;
            font-size: 0.9375rem;
            line-height: 1.6;
            color: #374151;
        }
    </style>
@endsection

@section('title-header')
    <div class="border-b border-gray-400">
        <div class="flex gap-2">
            <div class="bg-white rounded-md p-1 h-16 w-16 flex justify-center items-center">
                <img class="" src="{{ asset('/images/pinkrider.png') }}" alt="">
            </div>
            <h1 class="text-pink-500">
                {{ $findRidePage->pink_ride_page_heading ?? 'Search for ProximaRide' }}
            </h1>
        </div>
    </div>

    <p class="mt-6">
        {{ $findRidePage->pink_ride_page_label ?? 'As a North American first, Pink Rides is a dedicated community for female passengers and drivers, built on mutual respect, comfort, and peace of mind.' }}
    </p>

    <div class="mt-4 p-4 bg-pink-200 border-l-4 border-pink-500 rounded">
        <p class="text-gray-900 font-medium">
            @isset($findRidePage->pink_ride_description)
                {!! $findRidePage->pink_ride_description !!}
            @endisset
        </p>
    </div>
@endsection

@section('faq')
    <div class="mt-6 grid grid-cols-1 lg:grid-cols-1 gap-x-0 lg:gap-x-4 gap-4">
        <div class="pink-ride-faq">
            <div class="pink-ride-faq__header">
                <h3 class="text-primary text-xl xl:text-2xl font-FuturaMdCnBT text-center mb-0 font-medium">
                    {{ $findRidePage->pink_ride_page_faq_heading ?? 'FAQs on Pink Rides' }}
                </h3>
            </div>
            <div class="pink-ride-faq__body">
                @foreach ($pinkRideFaqs as $pinkRideFaq)
                    <div class="pink-ride-faq__item">
                        <button type="button" class="pink-ride-faq__question font-FuturaMdCnBT focus:outline-none focus:ring-2 focus:ring-[#0369a1] focus:ring-offset-1" aria-expanded="false" onclick="togglePinkRideFaq(this)">
                            {{ $pinkRideFaq->question }}
                        </button>
                        <div class="pink-ride-faq__answer" role="region" aria-hidden="true">
                            <div class="pink-ride-faq__answer-inner">
                                {!! $pinkRideFaq->answer !!}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        function togglePinkRideFaq(button) {
            const answer = button.nextElementSibling;
            const isOpen = button.getAttribute('aria-expanded') === 'true';
            if (isOpen) {
                answer.style.height = answer.scrollHeight + 'px';
                answer.offsetHeight;
                answer.style.height = '0';
                button.setAttribute('aria-expanded', 'false');
                answer.setAttribute('aria-hidden', 'true');
            } else {
                answer.style.height = answer.scrollHeight + 'px';
                button.setAttribute('aria-expanded', 'true');
                answer.setAttribute('aria-hidden', 'false');
                answer.addEventListener('transitionend', function onEnd() {
                    answer.removeEventListener('transitionend', onEnd);
                    if (button.getAttribute('aria-expanded') === 'true') {
                        answer.style.height = 'auto';
                    }
                }, { once: true });
            }
        }
    </script>
@endsection
