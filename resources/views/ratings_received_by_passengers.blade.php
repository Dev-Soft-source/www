@extends('layouts.template')

@section('content')

<div class="grid grid-cols-12 gap-4 md:container md:mx-auto  my-6 md:my-10 xl:my-14 px-4 xl:px-0my-14">
    @include('layouts.inc.profile_sidebar')

    <div class="bg-white border rounded p-4 border-gray-200 w-full col-span-12 md:col-span-9 shadow">
        <div class="border-b pb-2">
            <h1 class="mb-0">Reviews I received</h1>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4 mt-4">
            <div>
                <p class="text-lg">Reviewed by {{ count($ratings) }}
                    @if (count($ratings) == 1)
                        passenger
                    @else
                        passengers
                    @endif
                </p>
            </div>
            <div>
                <ul class="space-y-2">
                    <li>
                        <div class="flex items-center space-x-1">
                            @php
                                $fullStars = floor($VehicleCondition);
                                $fraction = $VehicleCondition - $fullStars;
                            @endphp

                            {{-- Full yellow stars --}}
                            @for ($i = 1; $i <= $fullStars; $i++)
                                <img src="{{ asset('assets/11-review-full-star.png') }}" class="w-5 h-5 mt-0.5 cursor-pointer" alt="">
                            @endfor

                            {{-- Fractional star --}}
                            @if ($fraction > 0)
                                {{-- Determine which half star image to use --}}
                                @if ($fraction >= 0.75)
                                    <img src="{{ asset('assets/11-review-4.75-stars.png') }}" class="w-5 h-5 mt-0.5 cursor-pointer" alt="">
                                @elseif ($fraction >= 0.25)
                                    <img src="{{ asset('assets/11-review-4.5-stars.png') }}" class="w-5 h-5 mt-0.5 cursor-pointer" alt="">
                                @else
                                    <img src="{{ asset('assets/11-review-4.25-stars.png') }}" class="w-5 h-5 mt-0.5 cursor-pointer" alt="">
                                @endif
                                {{-- Remaining grey stars --}}
                                @for ($i = $fullStars + 1; $i < 5; $i++)
                                    <img src="{{ asset('assets/11-review-full-star-grey.png') }}" class="w-5 h-5 mt-0.5 cursor-pointer" alt="">
                                @endfor
                            @else
                                {{-- Remaining grey stars --}}
                                @for ($i = $fullStars; $i < 5; $i++)
                                    <img src="{{ asset('assets/11-review-full-star-grey.png') }}" class="w-5 h-5 mt-0.5 cursor-pointer" alt="">
                                @endfor
                            @endif
                            <p class="pl-2 text-gray-900">Condition of the vehicle @if ($VehicleCondition) ({{ number_format(floatval($VehicleCondition), 1) }}) @endif</p>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center space-x-1">
                            @php
                                $fullStars = floor($conscious);
                                $fraction = $conscious - $fullStars;
                            @endphp

                            {{-- Full yellow stars --}}
                            @for ($i = 1; $i <= $fullStars; $i++)
                                <img src="{{ asset('assets/11-review-full-star.png') }}" class="w-5 h-5 mt-0.5 cursor-pointer" alt="">
                            @endfor

                            {{-- Fractional star --}}
                            @if ($fraction > 0)
                                {{-- Determine which half star image to use --}}
                                @if ($fraction >= 0.75)
                                    <img src="{{ asset('assets/11-review-4.75-stars.png') }}" class="w-5 h-5 mt-0.5 cursor-pointer" alt="">
                                @elseif ($fraction >= 0.25)
                                    <img src="{{ asset('assets/11-review-4.5-stars.png') }}" class="w-5 h-5 mt-0.5 cursor-pointer" alt="">
                                @else
                                    <img src="{{ asset('assets/11-review-4.25-stars.png') }}" class="w-5 h-5 mt-0.5 cursor-pointer" alt="">
                                @endif
                                {{-- Remaining grey stars --}}
                                @for ($i = $fullStars + 1; $i < 5; $i++)
                                    <img src="{{ asset('assets/11-review-full-star-grey.png') }}" class="w-5 h-5 mt-0.5 cursor-pointer" alt="">
                                @endfor
                            @else
                                {{-- Remaining grey stars --}}
                                @for ($i = $fullStars; $i < 5; $i++)
                                    <img src="{{ asset('assets/11-review-full-star-grey.png') }}" class="w-5 h-5 mt-0.5 cursor-pointer" alt="">
                                @endfor
                            @endif
                            <p class="pl-2 text-gray-900">Conscious to passengers wellness @if ($conscious) ({{ number_format(floatval($conscious), 1) }}) @endif</p>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center space-x-1">
                            @php
                                $fullStars = floor($comfort);
                                $fraction = $comfort - $fullStars;
                            @endphp

                            {{-- Full yellow stars --}}
                            @for ($i = 1; $i <= $fullStars; $i++)
                                <img src="{{ asset('assets/11-review-full-star.png') }}" class="w-5 h-5 mt-0.5 cursor-pointer" alt="">
                            @endfor

                            {{-- Fractional star --}}
                            @if ($fraction > 0)
                                {{-- Determine which half star image to use --}}
                                @if ($fraction >= 0.75)
                                    <img src="{{ asset('assets/11-review-4.75-stars.png') }}" class="w-5 h-5 mt-0.5 cursor-pointer" alt="">
                                @elseif ($fraction >= 0.25)
                                    <img src="{{ asset('assets/11-review-4.5-stars.png') }}" class="w-5 h-5 mt-0.5 cursor-pointer" alt="">
                                @else
                                    <img src="{{ asset('assets/11-review-4.25-stars.png') }}" class="w-5 h-5 mt-0.5 cursor-pointer" alt="">
                                @endif
                                {{-- Remaining grey stars --}}
                                @for ($i = $fullStars + 1; $i < 5; $i++)
                                    <img src="{{ asset('assets/11-review-full-star-grey.png') }}" class="w-5 h-5 mt-0.5 cursor-pointer" alt="">
                                @endfor
                            @else
                                {{-- Remaining grey stars --}}
                                @for ($i = $fullStars; $i < 5; $i++)
                                    <img src="{{ asset('assets/11-review-full-star-grey.png') }}" class="w-5 h-5 mt-0.5 cursor-pointer" alt="">
                                @endfor
                            @endif

                            <p class="pl-2 text-gray-900">Comfort @if ($comfort) ({{ number_format(floatval($comfort), 1) }}) @endif</p>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center space-x-1">
                            @php
                                $fullStars = floor($communication);
                                $fraction = $communication - $fullStars;
                            @endphp

                            {{-- Full yellow stars --}}
                            @for ($i = 1; $i <= $fullStars; $i++)
                                <img src="{{ asset('assets/11-review-full-star.png') }}" class="w-5 h-5 mt-0.5 cursor-pointer" alt="">
                            @endfor

                            {{-- Fractional star --}}
                            @if ($fraction > 0)
                                {{-- Determine which half star image to use --}}
                                @if ($fraction >= 0.75)
                                    <img src="{{ asset('assets/11-review-4.75-stars.png') }}" class="w-5 h-5 mt-0.5 cursor-pointer" alt="">
                                @elseif ($fraction >= 0.25)
                                    <img src="{{ asset('assets/11-review-4.5-stars.png') }}" class="w-5 h-5 mt-0.5 cursor-pointer" alt="">
                                @else
                                    <img src="{{ asset('assets/11-review-4.25-stars.png') }}" class="w-5 h-5 mt-0.5 cursor-pointer" alt="">
                                @endif
                                {{-- Remaining grey stars --}}
                                @for ($i = $fullStars + 1; $i < 5; $i++)
                                    <img src="{{ asset('assets/11-review-full-star-grey.png') }}" class="w-5 h-5 mt-0.5 cursor-pointer" alt="">
                                @endfor
                            @else
                                {{-- Remaining grey stars --}}
                                @for ($i = $fullStars; $i < 5; $i++)
                                    <img src="{{ asset('assets/11-review-full-star-grey.png') }}" class="w-5 h-5 mt-0.5 cursor-pointer" alt="">
                                @endfor
                            @endif

                            <p class="pl-2 text-gray-900">Communication @if ($communication) ({{ number_format(floatval($communication), 1) }}) @endif</p>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center space-x-1">
                            @php
                                $fullStars = floor($attitude);
                                $fraction = $attitude - $fullStars;
                            @endphp

                            {{-- Full yellow stars --}}
                            @for ($i = 1; $i <= $fullStars; $i++)
                                <img src="{{ asset('assets/11-review-full-star.png') }}" class="w-5 h-5 mt-0.5 cursor-pointer" alt="">
                            @endfor

                            {{-- Fractional star --}}
                            @if ($fraction > 0)
                                {{-- Determine which half star image to use --}}
                                @if ($fraction >= 0.75)
                                    <img src="{{ asset('assets/11-review-4.75-stars.png') }}" class="w-5 h-5 mt-0.5 cursor-pointer" alt="">
                                @elseif ($fraction >= 0.25)
                                    <img src="{{ asset('assets/11-review-4.5-stars.png') }}" class="w-5 h-5 mt-0.5 cursor-pointer" alt="">
                                @else
                                    <img src="{{ asset('assets/11-review-4.25-stars.png') }}" class="w-5 h-5 mt-0.5 cursor-pointer" alt="">
                                @endif
                                {{-- Remaining grey stars --}}
                                @for ($i = $fullStars + 1; $i < 5; $i++)
                                    <img src="{{ asset('assets/11-review-full-star-grey.png') }}" class="w-5 h-5 mt-0.5 cursor-pointer" alt="">
                                @endfor
                            @else
                                {{-- Remaining grey stars --}}
                                @for ($i = $fullStars; $i < 5; $i++)
                                    <img src="{{ asset('assets/11-review-full-star-grey.png') }}" class="w-5 h-5 mt-0.5 cursor-pointer" alt="">
                                @endfor
                            @endif
                            <p class="pl-2 text-gray-900">Overall attitude @if ($attitude) ({{ number_format(floatval($attitude), 1) }}) @endif</p>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center space-x-1">
                            @php
                                $fullStars = floor($hygiene);
                                $fraction = $hygiene - $fullStars;
                            @endphp

                            {{-- Full yellow stars --}}
                            @for ($i = 1; $i <= $fullStars; $i++)
                                <img src="{{ asset('assets/11-review-full-star.png') }}" class="w-5 h-5 mt-0.5 cursor-pointer" alt="">
                            @endfor

                            {{-- Fractional star --}}
                            @if ($fraction > 0)
                                {{-- Determine which half star image to use --}}
                                @if ($fraction >= 0.75)
                                    <img src="{{ asset('assets/11-review-4.75-stars.png') }}" class="w-5 h-5 mt-0.5 cursor-pointer" alt="">
                                @elseif ($fraction >= 0.25)
                                    <img src="{{ asset('assets/11-review-4.5-stars.png') }}" class="w-5 h-5 mt-0.5 cursor-pointer" alt="">
                                @else
                                    <img src="{{ asset('assets/11-review-4.25-stars.png') }}" class="w-5 h-5 mt-0.5 cursor-pointer" alt="">
                                @endif
                                {{-- Remaining grey stars --}}
                                @for ($i = $fullStars + 1; $i < 5; $i++)
                                    <img src="{{ asset('assets/11-review-full-star-grey.png') }}" class="w-5 h-5 mt-0.5 cursor-pointer" alt="">
                                @endfor
                            @else
                                {{-- Remaining grey stars --}}
                                @for ($i = $fullStars; $i < 5; $i++)
                                    <img src="{{ asset('assets/11-review-full-star-grey.png') }}" class="w-5 h-5 mt-0.5 cursor-pointer" alt="">
                                @endfor
                            @endif
                            <p class="pl-2 text-gray-900">Personal hygiene @if ($hygiene) ({{ number_format(floatval($hygiene), 1) }}) @endif</p>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center space-x-1">
                            @php
                                $fullStars = floor($respect);
                                $fraction = $respect - $fullStars;
                            @endphp

                            {{-- Full yellow stars --}}
                            @for ($i = 1; $i <= $fullStars; $i++)
                                <img src="{{ asset('assets/11-review-full-star.png') }}" class="w-5 h-5 mt-0.5 cursor-pointer" alt="">
                            @endfor

                            {{-- Fractional star --}}
                            @if ($fraction > 0)
                                {{-- Determine which half star image to use --}}
                                @if ($fraction >= 0.75)
                                    <img src="{{ asset('assets/11-review-4.75-stars.png') }}" class="w-5 h-5 mt-0.5 cursor-pointer" alt="">
                                @elseif ($fraction >= 0.25)
                                    <img src="{{ asset('assets/11-review-4.5-stars.png') }}" class="w-5 h-5 mt-0.5 cursor-pointer" alt="">
                                @else
                                    <img src="{{ asset('assets/11-review-4.25-stars.png') }}" class="w-5 h-5 mt-0.5 cursor-pointer" alt="">
                                @endif
                                {{-- Remaining grey stars --}}
                                @for ($i = $fullStars + 1; $i < 5; $i++)
                                    <img src="{{ asset('assets/11-review-full-star-grey.png') }}" class="w-5 h-5 mt-0.5 cursor-pointer" alt="">
                                @endfor
                            @else
                                {{-- Remaining grey stars --}}
                                @for ($i = $fullStars; $i < 5; $i++)
                                    <img src="{{ asset('assets/11-review-full-star-grey.png') }}" class="w-5 h-5 mt-0.5 cursor-pointer" alt="">
                                @endfor
                            @endif
                            <p class="pl-2 text-gray-900">Respect and courtesy @if ($respect) ({{ number_format(floatval($respect), 1) }}) @endif</p>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center space-x-1">
                            @php
                                $fullStars = floor($safety);
                                $fraction = $safety - $fullStars;
                            @endphp

                            {{-- Full yellow stars --}}
                            @for ($i = 1; $i <= $fullStars; $i++)
                                <img src="{{ asset('assets/11-review-full-star.png') }}" class="w-5 h-5 mt-0.5 cursor-pointer" alt="">
                            @endfor

                            {{-- Fractional star --}}
                            @if ($fraction > 0)
                                {{-- Determine which half star image to use --}}
                                @if ($fraction >= 0.75)
                                    <img src="{{ asset('assets/11-review-4.75-stars.png') }}" class="w-5 h-5 mt-0.5 cursor-pointer" alt="">
                                @elseif ($fraction >= 0.25)
                                    <img src="{{ asset('assets/11-review-4.5-stars.png') }}" class="w-5 h-5 mt-0.5 cursor-pointer" alt="">
                                @else
                                    <img src="{{ asset('assets/11-review-4.25-stars.png') }}" class="w-5 h-5 mt-0.5 cursor-pointer" alt="">
                                @endif
                                {{-- Remaining grey stars --}}
                                @for ($i = $fullStars + 1; $i < 5; $i++)
                                    <img src="{{ asset('assets/11-review-full-star-grey.png') }}" class="w-5 h-5 mt-0.5 cursor-pointer" alt="">
                                @endfor
                            @else
                                {{-- Remaining grey stars --}}
                                @for ($i = $fullStars; $i < 5; $i++)
                                    <img src="{{ asset('assets/11-review-full-star-grey.png') }}" class="w-5 h-5 mt-0.5 cursor-pointer" alt="">
                                @endfor
                            @endif
                            <p class="pl-2 text-gray-900">Safety @if ($safety) ({{ number_format(floatval($safety), 1) }}) @endif</p>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center space-x-1">
                            @php
                                $fullStars = floor($timeliness);
                                $fraction = $timeliness - $fullStars;
                            @endphp

                            {{-- Full yellow stars --}}
                            @for ($i = 1; $i <= $fullStars; $i++)
                                <img src="{{ asset('assets/11-review-full-star.png') }}" class="w-5 h-5 mt-0.5 cursor-pointer" alt="">
                            @endfor

                            {{-- Fractional star --}}
                            @if ($fraction > 0)
                                {{-- Determine which half star image to use --}}
                                @if ($fraction >= 0.75)
                                    <img src="{{ asset('assets/11-review-4.75-stars.png') }}" class="w-5 h-5 mt-0.5 cursor-pointer" alt="">
                                @elseif ($fraction >= 0.25)
                                    <img src="{{ asset('assets/11-review-4.5-stars.png') }}" class="w-5 h-5 mt-0.5 cursor-pointer" alt="">
                                @else
                                    <img src="{{ asset('assets/11-review-4.25-stars.png') }}" class="w-5 h-5 mt-0.5 cursor-pointer" alt="">
                                @endif
                                {{-- Remaining grey stars --}}
                                @for ($i = $fullStars + 1; $i < 5; $i++)
                                    <img src="{{ asset('assets/11-review-full-star-grey.png') }}" class="w-5 h-5 mt-0.5 cursor-pointer" alt="">
                                @endfor
                            @else
                                {{-- Remaining grey stars --}}
                                @for ($i = $fullStars; $i < 5; $i++)
                                    <img src="{{ asset('assets/11-review-full-star-grey.png') }}" class="w-5 h-5 mt-0.5 cursor-pointer" alt="">
                                @endfor
                            @endif
                            <p class="pl-2 text-gray-900">Timeliness @if ($timeliness) ({{ number_format(floatval($timeliness), 1) }}) @endif</p>
                        </div>
                    </li>
                </ul>
                <ul class="mt-3 border-t pt-3 space-y-2">
                    <li>
                        <div class="flex items-center space-x-1">
                            @php
                                $fullStars = floor($totalAverage);
                                $fraction = $totalAverage - $fullStars;
                            @endphp

                            {{-- Full yellow stars --}}
                            @for ($i = 1; $i <= $fullStars; $i++)
                                <img src="{{ asset('assets/11-review-full-star.png') }}" class="w-5 h-5 mt-0.5 cursor-pointer" alt="">
                            @endfor

                            {{-- Fractional star --}}
                            @if ($fraction > 0)
                                {{-- Determine which half star image to use --}}
                                @if ($fraction >= 0.75)
                                    <img src="{{ asset('assets/11-review-4.75-stars.png') }}" class="w-5 h-5 mt-0.5 cursor-pointer" alt="">
                                @elseif ($fraction >= 0.25)
                                    <img src="{{ asset('assets/11-review-4.5-stars.png') }}" class="w-5 h-5 mt-0.5 cursor-pointer" alt="">
                                @else
                                    <img src="{{ asset('assets/11-review-4.25-stars.png') }}" class="w-5 h-5 mt-0.5 cursor-pointer" alt="">
                                @endif
                                {{-- Remaining grey stars --}}
                                @for ($i = $fullStars + 1; $i < 5; $i++)
                                    <img src="{{ asset('assets/11-review-full-star-grey.png') }}" class="w-5 h-5 mt-0.5 cursor-pointer" alt="">
                                @endfor
                            @else
                                {{-- Remaining grey stars --}}
                                @for ($i = $fullStars; $i < 5; $i++)
                                    <img src="{{ asset('assets/11-review-full-star-grey.png') }}" class="w-5 h-5 mt-0.5 cursor-pointer" alt="">
                                @endfor
                            @endif
                            <p class="pl-2 text-gray-900">Average @if ($totalAverage) ({{ number_format(floatval($totalAverage), 1) }}) @endif</p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        <div class="space-y-4">
            @php $displayLimit = 2; @endphp
            @foreach ($allRatings as $index => $rating)
                @if ($rating->from)
                    <div class="bg-white rounded shadow p-0 md:p-6 {{ $index >= $displayLimit ? 'hidden-rating hidden' : '' }}">
                        <div class='flex items-center justify-center space-x-4'>
                            <div class="w-2/12">
                                <div>
                                    <img class="w-full h-full object-cover"
                                    src="{{ $rating->from->profile_image }}"
                                    alt="">
                                </div>
                            </div>
                            <div class="w-10/12">
                                <div class="flex justify-between">
                                    <h6 class='card-title leading-7 m-0'>{{ $rating->from->first_name }}</h6>
                                    <a href="{{ route('review_passenger.index', ['lang' => $selectedLanguage->abbreviation, 'id' => $rating->id]) }}" class='card-title leading-7 m-0'>View</a>
                                </div>
                                <div class="flex justify-between">
                                    <div class="flex space-x-1">
                                        @php
                                            // Format average rating with one decimal place
                                            $formattedAverageRating = $rating->average_rating;

                                            $fullStars = floor($formattedAverageRating);
                                            $fraction = $formattedAverageRating - $fullStars;
                                        @endphp

                                        {{-- Full yellow stars --}}
                                        @for ($i = 1; $i <= $fullStars; $i++)
                                            <img src="{{ asset('assets/11-review-full-star.png') }}" class="w-5 h-5 mt-0.5" alt="">
                                        @endfor

                                        {{-- Fractional star --}}
                                        @if ($fraction > 0)
                                            {{-- Determine which half star image to use --}}
                                            @if ($fraction >= 0.75)
                                                <img src="{{ asset('assets/11-review-4.75-stars.png') }}" class="w-5 h-5 mt-0.5" alt="">
                                            @elseif ($fraction >= 0.25)
                                                <img src="{{ asset('assets/11-review-4.5-stars.png') }}" class="w-5 h-5 mt-0.5" alt="">
                                            @else
                                                <img src="{{ asset('assets/11-review-4.25-stars.png') }}" class="w-5 h-5 mt-0.5" alt="">
                                            @endif
                                            {{-- Remaining grey stars --}}
                                            @for ($i = $fullStars + 1; $i < 5; $i++)
                                                <img src="{{ asset('assets/11-review-full-star-grey.png') }}" class="w-5 h-5 mt-0.5" alt="">
                                            @endfor
                                        @else
                                            {{-- Remaining grey stars --}}
                                            @for ($i = $fullStars; $i < 5; $i++)
                                                <img src="{{ asset('assets/11-review-full-star-grey.png') }}" class="w-5 h-5 mt-0.5" alt="">
                                            @endfor
                                        @endif
                                    </div>
                                    @if ($rating->reply_deadline)
                                        <a href="{{ route('review_reply', ['lang' => $selectedLanguage->abbreviation, 'id' => $rating->id]) }}" class='card-title leading-7 m-0'>Reply</a>
                                    @endif
                                </div>
                                <p class="text-gray-500">
                                    {{ $rating->review }}
                                </p>
                                @if ($rating->replies)
                                    @foreach ($rating->replies as $reply)
                                        <p class="text-base text-gray-500">
                                            Replied: <span class="">{{ $reply->reply }}</span>
                                        </p>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
            @if (count($ratings) > 2)
                <div class="flex justify-end">
                    <button id="viewAllButton" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        See all reviews
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection

@section('script')

<script>

document.addEventListener("DOMContentLoaded", function () {
    const viewAllButton = document.getElementById("viewAllButton");
    const hiddenRatings = document.querySelectorAll(".hidden-rating");

    // Function to toggle visibility of hidden ratings
    function toggleHiddenRatings() {
        hiddenRatings.forEach(rating => {
            rating.classList.remove("hidden");
        });
        viewAllButton.style.display = "none"; // Hide the "View All" button after revealing all ratings
    }

    viewAllButton.addEventListener("click", toggleHiddenRatings);
});

</script>

@endsection
