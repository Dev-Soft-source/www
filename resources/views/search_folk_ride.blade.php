@extends('px.search_template')

@section('style')
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
        /* FAQ - Extra+ Rides: themed bar (blue) and smooth expand/collapse */
        .folk-ride-faq {
            border: 1px solid rgba(3, 105, 161, 0.3);
            border-radius: 0.75rem;
            overflow: hidden;
            box-shadow: 0 4px 14px rgba(3, 105, 161, 0.08);
        }

        .folk-ride-faq__header {
            background: linear-gradient(135deg, rgb(255, 255, 255) 0%, rgb(172, 186, 194) 100%);
            color: #fff;
            padding: 1rem 1.25rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.15);
        }

        .folk-ride-faq__body {
            background: #f0f9ff;
            padding: 0.75rem;
        }

        .folk-ride-faq__item {
            margin-bottom: 0.5rem;
            border-radius: 0.5rem;
            overflow: hidden;
            border: 1px solid rgba(3, 105, 161, 0.2);
        }

        .folk-ride-faq__item:last-child {
            margin-bottom: 0;
        }

        .folk-ride-faq__question {
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

        .folk-ride-faq__question:hover {
            background: #eff6ff;
            color: #0369a1;
        }

        .folk-ride-faq__question[aria-expanded="true"] {
            background: #0369a1;
            color: #fff;
            border-radius: 0.5rem 0.5rem 0 0;
        }

        .folk-ride-faq__question[aria-expanded="true"]:hover {
            background: #0284c7;
            color: #fff;
        }

        .folk-ride-faq__answer {
            overflow: hidden;
            height: 0;
            transition: height 0.35s ease-out;
        }

        .folk-ride-faq__answer-inner {
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
                <img class="" src="{{ asset('/images/heart.png') }}" alt="">
            </div>
            <h1 class="">
                {{ $findRidePage->extra_care_ride_page_label ?? 'Search for Extra+ Rides' }}
            </h1>
        </div>
    </div>
    <div class="mt-4 p-4 bg-[#d4f3d4] border-l-4 border-green-500 rounded">
        <p class="text-gray-900 font-medium">
            {{ $findRidePage->extra_ride_description ?? 'I understand that Extra+ Rides are for our most respectful drivers and passengers. I promise to be courteous, polite, and considerate in order to maintain the high standard of these rides.' }}
        </p>
    </div>
@endsection

@section('faq')
    <div class="mt-6 grid grid-cols-1 lg:grid-cols-1 gap-x-0 lg:gap-x-4 gap-4">
        <div class="folk-ride-faq">
            <div class="flex flex-col items-center justify-center folk-ride-faq__header">
                <h3 class="text-primary text-xl xl:text-2xl font-FuturaMdCnBT text-center mb-0 font-medium">
                    {{ $findRidePage->extra_care_ride_faqs_heading ?? 'FAQs on the Extra+ Rides' }}
                </h3>
            </div>
            <div class="folk-ride-faq__body">
                @foreach ($extraCareFaqs as $extraCareFaq)
                    <div class="folk-ride-faq__item">
                        <button type="button"
                            class="folk-ride-faq__question font-FuturaMdCnBT focus:outline-none focus:ring-2 focus:ring-[#0369a1] focus:ring-offset-1"
                            aria-expanded="false" onclick="toggleFolkRideFaq(this)">
                            {{ $extraCareFaq->question }}
                        </button>
                        <div class="folk-ride-faq__answer" role="region" aria-hidden="true">
                            <div class="folk-ride-faq__answer-inner">
                                {!! $extraCareFaq->answer !!}
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
        function toggleFolkRideFaq(button) {
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
                }, {
                    once: true
                });
            }
        }
    </script>
@endsection
