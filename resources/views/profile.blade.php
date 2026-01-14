@extends('layouts.template')

@section('content')

    <div class="grid grid-cols-12 gap-4 md:container md:mx-auto  my-6 md:my-10 xl:my-14 px-4 xl:px-0">
        @include('layouts.inc.profile_sidebar')

        <div class="bg-white border rounded py-4 border-gray-200 w-full col-span-12 lg:col-span-9 shadow">
            @if (session('message'))
                <div id="my-modal" class="relative z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                    <!-- Backdrop with transition -->
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-0 transition-opacity duration-300 ease-in-out z-10" id="modal-backdrop"></div>
                
                    <!-- Modal container with transition -->
                    <div class="fixed inset-0 flex items-center justify-center p-4 z-20 opacity-0 scale-95 transition-all duration-300 ease-in-out" id="modal-container">
                        <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 w-full">
                            <!-- Modal content with transition -->
                            <div class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-border">
                                <button type="button" onclick="closeModal()" class="absolute top-3 right-3 text-gray-400 hover:text-gray-500">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                                    <div class="sm:flex sm:items-start justify-center">
                                        <!-- <div class="mx-auto h-16 w-16 flex-shrink-0 flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="4" stroke="currentColor" class="w-12 h-12 text-greenXS">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                            </svg>
                                        </div> -->
                                    </div>
                                    <div class="mt-2 w-full">
                                        <p class="can-exp-p text-center">{!! session('message') !!}</p>
                                    </div>
                                </div>
                                <div class="px-4 pb-6 pt-4 sm:flex sm:flex-row-reverse sm:px-6 justify-center">
                                    <button onclick="closeModal()"
                                        class="inline-flex w-full justify-center rounded bg-greenXS px-3 py-2 font-FuturaMdCnBT text-lg font-medium text-white hover:text-white hover:shadow-lg shadow-sm hover:bg-greenXS sm:ml-3 sm:w-24 transition-colors duration-200">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    // Function to show modal with transitions
                    function showModal() {
                        const modal = document.getElementById('my-modal');
                        const backdrop = document.getElementById('modal-backdrop');
                        const container = document.getElementById('modal-container');
                
                        modal.classList.remove('hidden');
                
                        // Trigger reflow to enable transitions
                        void modal.offsetWidth;
                
                        backdrop.classList.remove('bg-opacity-0');
                        backdrop.classList.add('bg-opacity-75');
                
                        container.classList.remove('opacity-0', 'scale-95');
                        container.classList.add('opacity-100', 'scale-100');
                    }
                
                    // Function to close modal with transitions
                    function closeModal() {
                        const backdrop = document.getElementById('modal-backdrop');
                        const container = document.getElementById('modal-container');
                
                        backdrop.classList.remove('bg-opacity-75');
                        backdrop.classList.add('bg-opacity-0');
                
                        container.classList.remove('opacity-100', 'scale-100');
                        container.classList.add('opacity-0', 'scale-95');
                
                        // Wait for transition to complete before hiding
                        setTimeout(() => {
                            document.getElementById('my-modal').classList.add('hidden');
                        }, 300);
                    }
                
                    // Auto-show modal if there's a message
                    @if(session('message'))
                        document.addEventListener('DOMContentLoaded', showModal);
                    @endif
                </script>
            @endif
            <div class="flex px-4 pb-2 justify-between">
                <div class="flex items-center">
                    <img class="w-16 h-16 rounded-full object-cover mr-3" src="{{ $user->profile_image }}" alt="">
                    <div>
                        <h3 class="mb-0 text-2xl">{{ $user->first_name }} {{ $user->last_name }}</h3>
                    </div>
                </div>
                <a href="{{ route('profile.edit', ['lang' => $selectedLanguage->abbreviation]) }}"
                    class="button-exp-fill h-fit mt-3">
                    @isset($editProfilePage->edit_profile_text)
                        {{ $editProfilePage->edit_profile_text }}
                    @endisset
                </a>
            </div>
            <div class="px-4 pb-2">
                <h3 class="mb-0 text-2xl">
                    @isset($editProfilePage->min_bio_label)
                        {{ $editProfilePage->min_bio_label }}
                    @endisset
                </h3>
                <p>{{ $user->about }}</p>
            </div>
            <div class="bg-blue-50 p-4 flex justify-center my-4">
                <div class="w-full md:w-3/4 flex justify-center items-center">
                    <div class="flex flex-col justify-center items-center w-48 my-4">
                        @isset($editProfilePage->passenger_driven_icon)
                            <img class="w-12 h-12 object-contain" src="{{ asset('home_page_icons/' . $editProfilePage->passenger_driven_icon) }}"
                                alt="">
                        @endisset
                        <p class="text-xl font-semibold">
                            {{ $user->rides()->where('status', '!=', 2)->where(function ($query) {
                                    $query->whereDate('rides.date', '<', now()->toDateString())->orWhere(function ($query) {
                                        $query->whereDate('rides.date', '=', now()->toDateString())->whereTime('rides.time', '<=', now()->toTimeString());
                                    });
                                })->get()->flatMap(function ($ride) {
                                    return $ride->bookings()->pluck('seats');
                                })->sum() }}
                        </p>
                        <h4 class="text-black">
                            @isset($editProfilePage->passenger_driven_label)
                                {{ $editProfilePage->passenger_driven_label }}
                            @endisset
                        </h4>
                    </div>
                    <div class="bg-gray-500 w-0.5 h-20"></div>
                    <div class="flex flex-col justify-center items-center w-48 my-4">
                        @isset($editProfilePage->rides_taken_icon)
                            <img class="w-12 h-12 object-contain" src="{{ asset('home_page_icons/' . $editProfilePage->rides_taken_icon) }}" alt="">
                        @endisset
                        <p class="text-xl font-semibold">
                            {{ $user->rides()->where('status', '!=', 2)->where(function ($query) {
                                    $query->whereDate('rides.date', '<', now()->toDateString())->orWhere(function ($query) {
                                        $query->whereDate('rides.date', '=', now()->toDateString())->whereTime('rides.time', '<=', now()->toTimeString());
                                    });
                                })->count() }}
                        </p>
                        <h4 class="text-black">
                            @isset($editProfilePage->rides_taken_label)
                                {{ $editProfilePage->rides_taken_label }}
                            @endisset
                        </h4>
                    </div>
                    <div class="bg-gray-500 w-0.5 h-20"></div>
                    <div class="flex flex-col justify-center items-center w-48 my-4">
                        @isset($editProfilePage->km_shared_icon)
                            <img class="w-12 h-12 object-contain" src="{{ asset('home_page_icons/' . $editProfilePage->km_shared_icon) }}" alt="">
                        @endisset
                        <p class="text-xl font-semibold">0</p>
                        <h4 class="text-black">
                            @isset($editProfilePage->km_shared_label)
                                {{ $editProfilePage->km_shared_label }}
                            @endisset
                        </h4>
                    </div>
                </div>
            </div>
            @if (count($ratings) > 0)
                <div class="pb-2">
                    <h3 class="mb-0 px-4 text-2xl">{{ $ratings->count() }}
                        @isset($editProfilePage->review_label)
                            {{ $editProfilePage->review_label }}
                        @endisset
                    </h3>
                    <div class="space-y-4 py-2 px-4 max-h-[32rem] overflow-y-auto">
                        @php $displayLimit = 2; @endphp
                        @foreach ($ratings as $index => $rating)
                            @if ($rating->from)
                                <div
                                    class="even:bg-gray-100 odd:bg-white rounded border border-gray-100 shadow p-4 md:p-6 {{ $index >= $displayLimit ? 'hidden-rating hidden' : '' }}">
                                    <a @if ($rating->type === '2') href="{{ route('review.index', ['lang' => $selectedLanguage->abbreviation, 'id' => $rating->id]) }}"
                                @else
                                    href="{{ route('review_passenger.index', ['lang' => $selectedLanguage->abbreviation, 'id' => $rating->id]) }}" @endif
                                        class='card-title leading-7 m-0'>
                                        <div class='flex items-start justify-center space-x-4'>
                                            <div class="flex-initial">
                                                <div class="mt-1 w-16 h-16 bg-gray-50 border mx-auto rounded-full overflow-hidden">
                                                    <img class="w-full h-full object-contain rounded-full"
                                                        src="{{ $rating->from->profile_image }}" alt="">
                                                </div>
                                            </div>
                                            <div class="flex-auto">
                                                <div class="flex justify-between">
                                                    <h6 class='card-title leading-7 m-0 text-2xl'>{{ $rating->from->first_name }}
                                                    </h6>
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
                                                            <img src="{{ asset('assets/11-review-full-star.png') }}"
                                                                class="w-5 h-5 mt-0.5" alt="">
                                                        @endfor

                                                        {{-- Fractional star --}}
                                                        @if ($fraction > 0)
                                                            {{-- Determine which half star image to use --}}
                                                            @if ($fraction >= 0.75)
                                                                <img src="{{ asset('assets/11-review-4.75-stars.png') }}"
                                                                    class="w-5 h-5 mt-0.5" alt="">
                                                            @elseif ($fraction >= 0.25)
                                                                <img src="{{ asset('assets/11-review-4.5-stars.png') }}"
                                                                    class="w-5 h-5 mt-0.5" alt="">
                                                            @else
                                                                <img src="{{ asset('assets/11-review-4.25-stars.png') }}"
                                                                    class="w-5 h-5 mt-0.5" alt="">
                                                            @endif
                                                            {{-- Remaining grey stars --}}
                                                            @for ($i = $fullStars + 1; $i < 5; $i++)
                                                                <img src="{{ asset('assets/11-review-full-star-grey.png') }}"
                                                                    class="w-5 h-5 mt-0.5" alt="">
                                                            @endfor
                                                        @else
                                                            {{-- Remaining grey stars --}}
                                                            @for ($i = $fullStars; $i < 5; $i++)
                                                                <img src="{{ asset('assets/11-review-full-star-grey.png') }}"
                                                                    class="w-5 h-5 mt-0.5" alt="">
                                                            @endfor
                                                        @endif
                                                    </div>
                                                </div>
                                                <p class="text-gray-500 line-clamp-2 text-left">
                                                    {{ $rating->review }}
                                                </p>
                                                @if ($rating->replies)
                                                    @foreach ($rating->replies as $reply)
                                                        <p class="text-base text-gray-500">
                                                            @isset($editProfilePage->reply_label)
                                                                {{ $editProfilePage->reply_label }}
                                                            @endisset
                                                            <span class="">{{ $reply->reply }}</span>
                                                        </p>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endif
                        @endforeach
                        @if (count($ratings) > 2)
                            <div class="flex justify-end">
                                <button id="viewAllButton" class="button-exp-fill">
                                    @isset($editProfilePage->link_review_label)
                                        {{ $editProfilePage->link_review_label }}
                                    @endisset
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <div class="pb-2 px-4">
                    <!-- <h3 class="mb-0 text-2xl">There are no reviews yet</h3> -->
                </div>
            @endif
        </div>
    </div>

@endsection

@section('script')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
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
