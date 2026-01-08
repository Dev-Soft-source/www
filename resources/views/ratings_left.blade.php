@extends('layouts.template')

@section('content')

<div class="grid grid-cols-12 gap-4 md:container md:mx-auto  my-6 md:my-10 xl:my-14 px-4 xl:px-0">
    @include('layouts.inc.profile_sidebar')

    <div class="bg-white border rounded p-4 border-gray-200 w-full col-span-12 lg:col-span-9 shadow">
        <div class=" mb-4">
            <h1 class="mb-0">{{ $reviewSettingPage->review_left_label ?? "Reviews I left"}}</h1>
        </div>
        <div class="space-y-4 pb-2 max-h-[53rem] overflow-y-auto pr-2">
            @php $displayLimit = 5; @endphp
            @foreach ($ratings as $index => $rating)
                <div class=" even:bg-gray-100 odd:bg-white rounded border border-gray-100 shadow p-4 md:p-6 {{ $index >= $displayLimit ? 'hidden-rating hidden' : '' }}">
                    <div class='flex items-start md:justify-center space-x-4'>
                        <div class="md:w-2/12">
                            <div class="w-16 h-16 md:w-28 md:h-28 bg-gray-50 border mx-auto rounded-full overflow-hidden">
                                @isset($rating->ride->driver->profile_image)
                                    <img class="w-full h-full object-contain"
                                        src="{{ $rating->ride->driver->profile_image }}"
                                        alt="">
                                @endisset
                            </div>
                        </div>
                        <div class="flex-auto md:w-10/12">
                            <a
                                @if ($rating->type === '2')
                                    href="{{ route('review_left_passenger.index', ['lang' => $selectedLanguage->abbreviation, 'id' => $rating->id]) }}"
                                @else
                                    href="{{ route('review_left.index', ['lang' => $selectedLanguage->abbreviation, 'id' => $rating->id]) }}"
                                @endif>
                                <div class="flex justify-between">
                                    <div>
                                        <h6 class='card-title leading-7 m-0'>
                                            @isset($rating->ride->driver->type)
                                                @if ($rating->ride->driver->type === '2')
                                                    {{ $rating->ride->driver->last_name }}
                                                @elseif ($rating->ride->driver->type === '3')
                                                    {{ $rating->ride->driver->first_name }} {{ $rating->ride->driver->last_name }}
                                                @else
                                                    {{ $rating->ride->driver->first_name }}
                                                @endisset
                                            @endif
                                        </h6>
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
                                        <p class="text-black line-clamp-2 text-left">
                                            {{ $rating->review }}
                                        </p>
                                        @if ($rating->replies)
                                            @foreach ($rating->replies as $reply)
                                                <p class="text-base text-black">
                                                {{ $reviewSettingPage->replied_label ?? "Replied"}}: <span class="">{{ $reply->reply }}</span>
                                                </p>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
            @if (count($ratings) > 2)
                <div class="flex justify-center">
                    <button id="viewAllButton" class="button-exp-fill">
                        {{ $reviewSettingPage->see_all_review_label ?? "See all reviews"}}
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
