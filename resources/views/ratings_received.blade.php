@extends('layouts.template')

@section('content')

<div class="grid grid-cols-12 gap-4 md:container md:mx-auto  my-6 md:my-10 xl:my-14 px-4 xl:px-0">
    @include('layouts.inc.profile_sidebar')

    <div class="bg-white border rounded p-4 border-gray-200 w-full col-span-12 lg:col-span-9 shadow">
        @if (session('success'))
            

            <div id="myModal" class="relative z-50" id="delete_message_confirmation" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                                <div onclick="closeModal()"  class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                                                <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                                                    <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                                                        <div class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
                                                            <button type="button" onclick="closeModal()"  class="absolute top-3 right-3 text-gray-400 hover:text-gray-500">
                                                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                                </svg>
                                                            </button>
                                                            <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                                                                <div class="sm:flex sm:items-start justify-center">
                                                                    <!-- <div
                                                                        class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full sm:mx-0 sm:h-10 sm:w-10 bg-green-500">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-lg text-white w-8 h-8" viewBox="0 0 16 16">
                                                                            <path d="M7.005 3.1a1 1 0 1 1 1.99 0l-.388 6.35a.61.61 0 0 1-1.214 0zM7 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0"/>
                                                                        </svg>
                                                                    </div> -->
                                                                </div>
                                                                <div class="text-center sm:ml-4 sm:mt-0 sm:text-left">
                                                                    <div class="mt-2 w-full">
                                                                        <p class="can-exp-p text-center">{{ session('success') }}</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="px-4 pb-6 pt-4 flex items-center space-x-2 sm:space-x-4 sm:px-6 justify-center">
                                                            <input type="hidden" id="notificationId" value="3094">
                                                                <a href="#" onclick="closeModal()"  class="button-exp-fill">Close </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
        @endif

        <div class="mb-2">
            <h1 class="mb-0">{{ $reviewSettingPage->review_received_label ?? "Reviews I received"}}</h1>
        </div>
        <div class="space-y-4 pb-2 max-h-[53rem] overflow-y-auto pr-2">
            @php $displayLimit = 4; @endphp
            @foreach ($allRatings as $index => $rating)
                @if ($rating->from)
                    <div class="relative even:bg-gray-100 odd:bg-white {{ $index >= $displayLimit ? 'hidden-rating hidden' : '' }}">

                        <a
                            @if ($rating->type === '2')
                                href="{{ route('review.index', ['lang' => $selectedLanguage->abbreviation, 'id' => $rating->id]) }}"
                            @else
                                href="{{ route('review_passenger.index', ['lang' => $selectedLanguage->abbreviation, 'id' => $rating->id]) }}"
                            @endif>
                          <div class="rounded border border-gray-100 shadow p-4 md:p-6">
                            <div class='flex items-start md:justify-center space-x-4'>
                                <div class="">
                                    <div class="w-16 h-16 md:w-28 md:h-28 bg-gray-50 border rounded-full mx-auto overflow-hidden">
                                        <img class="w-full h-full object-contain"
                                        src="{{ $rating->from->profile_image }}"
                                        alt="">
                                    </div>
                                </div>
                                <div class="flex-auto w-[70%]">
                                    <div class="flex justify-between">
                                        <div class="">
                                            <h6 class='card-title leading-7 m-0'>{{ $rating->from->first_name }}</h6>
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

                                        </div>
                                    </div>
                                    @if ($rating->replies)
                                        @foreach ($rating->replies as $reply)
                                            <p class="text-base text-black">
                                                <span class="text-primary">{{ $reviewSettingPage->replied_label ?? "Replied"}}:</span> <span class="">{{ $reply->reply }}</span>
                                            </p>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                            <div class="flex justify-end mt-2">
                                @if ($rating->reply_deadline)
                                    <a href="{{ route('review_reply', ['lang' => $selectedLanguage->abbreviation, 'id' => $rating->id]) }}" class='button-exp-fill h-full'>{{ $reviewSettingPage->reply_label ?? "Reply"}}</a>
                                @endif
                            </div>
                          </div>
                        </a>
                    </div>
                @endif
            @endforeach
            @if (count($allRatings) > 2)
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
function closeModal() {
    const modal = document.getElementById('myModal');
    if (modal) {
        modal.classList.add('hidden');
    }
}
</script>

@endsection
