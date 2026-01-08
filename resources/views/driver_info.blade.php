@extends('layouts.template')

@section('content')

<div class="container mx-auto my-10 md:my-14 px-4 md:px-0">
    <div class="bg-white border rounded py-4 border-gray-200 w-full md:w-[70%] mx-auto shadow">
        <div class=" px-4">
        <div class="pb-2 flex flex-col md:flex-row items-center justify-between">
            <h1 class="mb-0">Driver info</h1>
        </div>
        <div class="flex pb-2 justify-between">
            <div class="flex items-start">
                <img class="w-16 h-16 rounded-full object-cover mr-3 mt-2" src="{{ $ride->driver?->profile_image }}" alt="">
                <div>
                    <h3 class="mb-0 text-2xl xl:text-3xl">{{ $ride->driver?->first_name }} {{ $ride->driver?->last_name }}</h3>
                    <p class="mb-0">Joined: {{ $ride->driver?->created_at->format('F d, Y') }}</p>
                    @php
                        // Calculate the age based on the driver's date of birth
                        $dob = \Carbon\Carbon::parse($ride->driver?->dob);
                        $age = $dob->diffInYears(\Carbon\Carbon::now());
                    @endphp
                    <p class="mb-0">Age: {{ $age }}, {{ ucfirst($ride->driver?->gender) }}</p>
                </div>
            </div>
        </div>
        <div class="pb-2">
            <h3 class="mb-0 text-2xl xl:text-3xl">Mini bio</h3>
            <p>{{ $ride->driver?->about }}</p>
        </div>
        </div>
        <div class="bg-blue-50 p-4 flex justify-center my-4">
          <div class="w-full md:w-3/4 flex justify-center items-center">
            <div class="flex flex-col justify-center items-center w-48 my-4">
                <img class="w-12 h-12 object-contain" src="{{ asset('assets/passengerdriven.png') }}" alt="">
                <p class="text-xl font-semibold">
                    {{  $ride->driver?->rides()
                        ->where('status', '!=', 2)
                        ->where(function ($query) {
                            $query->whereDate('rides.date', '<', now()->toDateString())
                                ->orWhere(function ($query) {
                                    $query->whereDate('rides.date', '=', now()->toDateString())
                                        ->whereTime('rides.time', '<=', now()->toTimeString());
                                });
                        })
                        ->get()
                        ->flatMap(function ($ride) {
                            return $ride->bookings()->pluck('seats');
                        })
                        ->sum()
                    }}
                </p>
                <h4 class="text-black">Passengers driven</h4>
            </div>
            <div class="bg-gray-500 w-0.5 h-20"></div>
            <div class="flex flex-col justify-center items-center w-48 my-4">
                <img class="w-12 h-12 object-contain" src="{{ asset('assets/ridestaken.png') }}" alt="">
                <p class="text-xl font-semibold">
                    {{  $ride->driver?->rides()
                            ->where('status', '!=', 2)
                            ->where(function ($query) {
                                $query->whereDate('rides.date', '<', now()->toDateString())
                                    ->orWhere(function ($query) {
                                        $query->whereDate('rides.date', '=', now()->toDateString())
                                            ->whereTime('rides.time', '<=', now()->toTimeString());
                                    });
                            })
                            ->count()
                    }}
                </p>
                <h4 class="text-black">Rides taken</h4>
            </div>
            <div class="bg-gray-500 w-0.5 h-20"></div>
            <div class="flex flex-col justify-center items-center w-48 my-4">
                <img class="w-12 h-12 object-contain" src="{{ asset('assets/kmshared.png') }}" alt="">
                <p class="text-xl font-semibold">0</p>
                <h4 class="text-black">KM shared</h4>
            </div>
          </div>
        </div>
        <div class="pb-2 px-4">
            <h3 class="mb-0 text-2xl xl:text-3xl">Vehicle info</h3>
            <div class="flex items-center gap-6">
                <div class="w-32 h-28 bg-gray-50 border">
                    <img class="w-full h-full object-contain" src="{{ $ride->car_image }}" alt="">
                </div>
                <div>
                    <h4 class="mb-0">{{ $ride->year }} {{ $ride->model }} {{ $ride->vehicle_type }}</h4>
                    <p class="mb-0">{{ $ride->license_no }}</p>
                    <p class="mb-0">{{ $ride->car_type }}</p>
                </div>
            </div>
        </div>
        <div class="pb-2 px-4">
            <h3 class="mb-0 text-2xl xl:text-3xl">{{ $ratings->count() }} Reviews</h3>
            <div class="space-y-4 mt-4">
                @php $displayLimit = 2; @endphp
                @foreach ($ratings as $index => $rating)
                    @if ($rating->from)
                    <div class="even:bg-gray-100 odd:bg-white rounded border border-gray-100 shadow p-4 md:p-6 {{ $index >= $displayLimit ? 'hidden-rating hidden' : '' }}">
                        <a
                            @if ($rating->type === '2')
                                href="{{ route('review.index', ['lang' => $selectedLanguage->abbreviation, 'id' => $rating->id]) }}"
                            @else
                                href="{{ route('review_passenger.index', ['lang' => $selectedLanguage->abbreviation, 'id' => $rating->id]) }}"
                            @endif>
                            <div class='flex items-start justify-center space-x-4'>
                                <div class="flex-initial">
                                    <div class="mt-1 w-16 h-16 md:w-28 md:h-28 bg-gray-50 border mx-auto rounded-full overflow-hidden">
                                        <img class="w-full h-full object-contain rounded-full"
                                            src="{{ $rating->from->profile_image }}"
                                            alt="">
                                        </div>
                                    </div>
                                    <div class="flex-auto">
                                        <div class="flex justify-between">
                                            <h6 class='card-title leading-7 m-0'>{{ $rating->from->first_name }}</h6>
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
                                        </div>
                                        <p class="text-gray-500">
                                            {{ $rating->review }}
                                        </p>
                                    </div>
                                </div>
                        </a>
                    </div>
                    @endif
                @endforeach
                @if (count($ratings) > 2)
                    <div class="flex justify-end">
                        <button type="button" id="viewAllButton" class="button-exp-fill">
                            See all reviews
                        </button>
                    </div>
                @endif
            </div>
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
