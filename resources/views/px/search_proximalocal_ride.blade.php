@extends('px.search_template')

@section('style')
<style>
        /* FAQ - Extra+ Rides: themed bar (blue) and smooth expand/collapse */
        .proximalocal-ride-fag {
            border: 1px solid rgba(3, 105, 161, 0.3);
            border-radius: 0.75rem;
            overflow: hidden;
            box-shadow: 0 4px 14px rgba(3, 105, 161, 0.08);
        }

        .proximalocal-ride-fag__header {
            background: linear-gradient(135deg, rgb(255, 255, 255) 0%, rgb(172, 186, 194) 100%);
            color: #fff;
            padding: 1rem 1.25rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.15);
        }

        .proximalocal-ride-fag__body {
            background: #f0f9ff;
            padding: 0.75rem;
        }

        .proximalocal-ride-fag__item {
            margin-bottom: 0.5rem;
            border-radius: 0.5rem;
            overflow: hidden;
            border: 1px solid rgba(3, 105, 161, 0.2);
        }

        .proximalocal-ride-fag__item:last-child {
            margin-bottom: 0;
        }

        .proximalocal-ride-fag__question {
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

        .proximalocal-ride-fag__question:hover {
            background: #eff6ff;
            color: #0369a1;
        }

        .proximalocal-ride-fag__question[aria-expanded="true"] {
            background: #0369a1;
            color: #fff;
            border-radius: 0.5rem 0.5rem 0 0;
        }

        .proximalocal-ride-fag__question[aria-expanded="true"]:hover {
            background: #0284c7;
            color: #fff;
        }

        .proximalocal-ride-fag__answer {
            overflow: hidden;
            height: 0;
            transition: height 0.35s ease-out;
        }

        .proximalocal-ride-fag__answer-inner {
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
                <img class="" src="{{asset('/images/proximaridelocal.png')}}" alt="">
            </div>
            <h1 class="">
                Search ProximaLocal Rides
            </h1>
        </div>
    </div>
    <p class="mt-6">
        ProximaLocal Rides are designed for passengers and drivers taking short trips, usually under $15 per seat. With no booking fee on these rides, travel locally is simpler, more convenient, and cost-effective. Whether you're commuting across town or running errands nearby, ProximaLocal Rides make every short-distance journey easy and affordable.
    </p>
@endsection

@section('faq')
    <div class="mt-6 grid grid-cols-1 lg:grid-cols-1 gap-x-0 lg:gap-x-4 gap-4">
        <div class="proximalocal-ride-fag">
            <div class="flex flex-col items-center justify-center proximalocal-ride-fag__header">
                <h3 class="text-primary text-xl xl:text-2xl font-FuturaMdCnBT text-center mb-0 font-medium">
                    {{ $findRidePage->proximalocal_ride_faqs_heading ?? 'FAQs on the ProximaLocal Rides' }}
                </h3>
            </div>
            <div class="proximalocal-ride-fag__body">
                @foreach ($proximaLocalRideFaqs as $proximaLocalRideFaq)
                    <div class="proximalocal-ride-fag__item">
                        <button type="button"
                            class="proximalocal-ride-fag__question font-FuturaMdCnBT focus:outline-none focus:ring-2 focus:ring-[#0369a1] focus:ring-offset-1"
                            aria-expanded="false" onclick="toggleFolkRideFaq(this)">
                            {{ $proximaLocalRideFaq->question }}
                        </button>
                        <div class="proximalocal-ride-fag__answer" role="region" aria-hidden="true">
                            <div class="proximalocal-ride-fag__answer-inner">
                                {!! $proximaLocalRideFaq->answer !!}
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
