@extends('layouts.template')

@section('content')

<div class="container mx-auto my-14">
    <div class="w-full md:w-[70%] mx-auto px-4 md:px-0 ">
        <div class="bg-white border rounded p-4 border-gray-200 w-full shadow  pb-8">
        <div class="pb-2">
            <h1 class="mb-0">Review</h1>
        </div>
        <form method="POST" action="{{ route('review_driver.store', ['id' => $booking->id]) }}" enctype="multipart/form-data">
            @csrf
            <div class="space-y-4 mt-4">
                <div>
                    <div class="flex items-center space-x-2">
                        <div class="w-16 h-16 md:w-24 md:h-24 bg-gray-50 border rounded-full overflow-hidden">
                            <img class="w-full h-full object-contain rounded-full" src="{{ $ride->driver?->profile_image }}" alt="">
                        </div>
                    <p class="text-black text-2xl lg:text-3xl font-FuturaMdCnBT">
                        @if ($ride->driver?->type === '2')
                            {{ $ride->driver?->last_name }}
                        @elseif ($ride->driver?->type === '3')
                            {{ $ride->driver?->first_name }} {{ $ride->driver?->last_name }}
                        @else
                            {{ $ride->driver?->first_name }}
                        @endif
                    </p>
                    </div>
                    <div class="mt-6">
                        <label for="meeting" class="text-gray-900 font-medium text-lg mb-2"></label>
                        <textarea id="meeting" rows="5" name="review"
                            class="block p-2.5 w-full text-gray-900 bg-white rounded border border-gray-300 focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 mt-2"
                            placeholder="In the 'Passenger Remarks' section, you can include specific feedback, comments, or compliments about the passenger's behavior during the ride">{{ old('review') }}</textarea>
                        @error('review')
                          <div class="relative tooltip -bottom-4 group-hover:flex">
                            <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full md:w-1/2 rounded" >
                                <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                            </div>
                          </div>
                        @enderror
                    </div>
                </div>
                <div>
                    <ul class="space-y-2">
                        <li>
                            <div class="flex items-center">
                                <p class="text-black text-2xl lg:text-3xl font-FuturaMdCnBT">Other ratings</p>
                            </div>
                        </li>
                        <li>
                            <div class="flex flex-col sm:flex-col md:flex-row lg:flex-row md:items-center gap-1">
                                <div class="flex items-center gap-1 order-2 md:order-1">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <div class="relative">
                                            <label for="number-of-vehicle-condition-rating-{{ $i }}">
                                                <input id="number-of-vehicle-condition-rating-{{ $i }}" name="vehicle_condition" type="radio" value="{{ $i }}" class="hidden" {{ old('vehicle_condition') == $i ? 'checked' : '' }} onchange="vehicle_condition_rating_selected(this)" data-parsley-required="true" data-parsley-trigger="blur focusout change" data-parsley-required-message="Please select the available seats." data-parsley-errors-container="#parsley-seats-error">
                                                <img src="{{ old('vehicle_condition') >= $i ? asset('assets/11-review-full-star.png') : asset('assets/11-review-full-star-grey.png') }}" class="w-5 h-5 mt-0.5 cursor-pointer vehicle-condition-rating-image vehicle-condition-rating-unselect-{{ $i }}" alt="">
                                            </label>
                                        </div>
                                    @endfor
                                </div>
                                <p class="md:ml-2 text-black md:w-[50%] text-left order-1 md:order-2">Condition of the vehicle</p>
                            </div>
                        </li>
                        <li>
                            <div class="flex flex-col sm:flex-col md:flex-row lg:flex-row md:items-center gap-1">
                                <div class="flex items-center gap-1 order-2 md:order-1">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <div class="relative">
                                            <label for="number-of-conscious-rating-{{ $i }}">
                                                <input id="number-of-conscious-rating-{{ $i }}" name="conscious" type="radio" value="{{ $i }}" class="hidden" {{ old('conscious') == $i ? 'checked' : '' }} onchange="conscious_rating_selected(this)" data-parsley-required="true" data-parsley-trigger="blur focusout change" data-parsley-required-message="Please select the available seats." data-parsley-errors-container="#parsley-seats-error">
                                                <img src="{{ old('conscious') >= $i ? asset('assets/11-review-full-star.png') : asset('assets/11-review-full-star-grey.png') }}" class="w-5 h-5 mt-0.5 cursor-pointer conscious-rating-image conscious-rating-unselect-{{ $i }}" alt="">
                                            </label>
                                        </div>
                                    @endfor
                                </div>
                                <p class="md:ml-2 text-black md:w-[50%] text-left order-1 md:order-2">Conscious to passengers wellness</p>
                            </div>
                        </li>
                        <li>
                            <div class="flex flex-col sm:flex-col md:flex-row lg:flex-row md:items-center gap-1">
                                <div class="flex items-center gap-1 order-2 md:order-1">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <div class="relative">
                                            <label for="number-of-comfort-rating-{{ $i }}">
                                                <input id="number-of-comfort-rating-{{ $i }}" name="comfort" type="radio" value="{{ $i }}" class="hidden" {{ old('comfort') == $i ? 'checked' : '' }} onchange="comfort_rating_selected(this)" data-parsley-required="true" data-parsley-trigger="blur focusout change" data-parsley-required-message="Please select the available seats." data-parsley-errors-container="#parsley-seats-error">
                                                <img src="{{ old('comfort') >= $i ? asset('assets/11-review-full-star.png') : asset('assets/11-review-full-star-grey.png') }}" class="w-5 h-5 mt-0.5 cursor-pointer comfort-rating-image comfort-rating-unselect-{{ $i }}" alt="">
                                            </label>
                                        </div>
                                    @endfor
                                </div>
                                <p class="md:ml-2 text-black md:w-[50%] text-left order-1 md:order-2">Comfort</p>
                            </div>
                        </li>
                        <li>
                            <div class="flex flex-col sm:flex-col md:flex-row lg:flex-row md:items-center gap-1">
                                <div class="flex items-center gap-1 order-2 md:order-1">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <div class="relative">
                                            <label for="number-of-communication-rating-{{ $i }}">
                                                <input id="number-of-communication-rating-{{ $i }}" name="communication" type="radio" value="{{ $i }}" class="hidden" {{ old('communication') == $i ? 'checked' : '' }} onchange="communication_rating_selected(this)" data-parsley-required="true" data-parsley-trigger="blur focusout change" data-parsley-required-message="Please select the available seats." data-parsley-errors-container="#parsley-seats-error">
                                                <img src="{{ old('communication') >= $i ? asset('assets/11-review-full-star.png') : asset('assets/11-review-full-star-grey.png') }}" class="w-5 h-5 mt-0.5 cursor-pointer communication-rating-image communication-rating-unselect-{{ $i }}" alt="">
                                            </label>
                                        </div>
                                    @endfor
                                </div>
                                <p class="md:ml-2 text-black md:w-[50%] text-left order-1 md:order-2">Communication</p>
                            </div>
                        </li>
                        <li>
                            <div class="flex flex-col sm:flex-col md:flex-row lg:flex-row md:items-center gap-1">
                                <div class="flex items-center gap-1 order-2 md:order-1">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <div class="relative">
                                            <label for="number-of-attitude-rating-{{ $i }}">
                                                <input id="number-of-attitude-rating-{{ $i }}" name="attitude" type="radio" value="{{ $i }}" class="hidden" {{ old('attitude') == $i ? 'checked' : '' }} onchange="attitude_rating_selected(this)" data-parsley-required="true" data-parsley-trigger="blur focusout change" data-parsley-required-message="Please select the available seats." data-parsley-errors-container="#parsley-seats-error">
                                                <img src="{{ old('attitude') >= $i ? asset('assets/11-review-full-star.png') : asset('assets/11-review-full-star-grey.png') }}" class="w-5 h-5 mt-0.5 cursor-pointer attitude-rating-image attitude-rating-unselect-{{ $i }}" alt="">
                                            </label>
                                        </div>
                                    @endfor
                                </div>
                                <p class="md:ml-2 text-black md:w-[50%] text-left order-1 md:order-2">Overall attitude</p>
                            </div>
                        </li>
                        <li>
                            <div class="flex flex-col sm:flex-col md:flex-row lg:flex-row md:items-center gap-1">
                                <div class="flex items-center gap-1 order-2 md:order-1">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <div class="relative">
                                            <label for="number-of-hygiene-rating-{{ $i }}">
                                                <input id="number-of-hygiene-rating-{{ $i }}" name="hygiene" type="radio" value="{{ $i }}" class="hidden" {{ old('hygiene') == $i ? 'checked' : '' }} onchange="hygiene_rating_selected(this)" data-parsley-required="true" data-parsley-trigger="blur focusout change" data-parsley-required-message="Please select the available seats." data-parsley-errors-container="#parsley-seats-error">
                                                <img src="{{ old('hygiene') >= $i ? asset('assets/11-review-full-star.png') : asset('assets/11-review-full-star-grey.png') }}" class="w-5 h-5 mt-0.5 cursor-pointer hygiene-rating-image hygiene-rating-unselect-{{ $i }}" alt="">
                                            </label>
                                        </div>
                                    @endfor
                                </div>
                                <p class="md:ml-2 text-black md:w-[50%] text-left order-1 md:order-2">Personal hygiene</p>
                            </div>
                        </li>
                        <li>
                            <div class="flex flex-col sm:flex-col md:flex-row lg:flex-row md:items-center gap-1">
                                <div class="flex items-center gap-1 order-2 md:order-1">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <div class="relative">
                                            <label for="number-of-respect-rating-{{ $i }}">
                                                <input id="number-of-respect-rating-{{ $i }}" name="respect" type="radio" value="{{ $i }}" class="hidden" {{ old('respect') == $i ? 'checked' : '' }} onchange="respect_rating_selected(this)" data-parsley-required="true" data-parsley-trigger="blur focusout change" data-parsley-required-message="Please select the available seats." data-parsley-errors-container="#parsley-seats-error">
                                                <img src="{{ old('respect') >= $i ? asset('assets/11-review-full-star.png') : asset('assets/11-review-full-star-grey.png') }}" class="w-5 h-5 mt-0.5 cursor-pointer respect-rating-image respect-rating-unselect-{{ $i }}" alt="">
                                            </label>
                                        </div>
                                    @endfor
                                </div>
                                <p class="md:ml-2 text-black md:w-[50%] text-left order-1 md:order-2">Respect and courtesy</p>
                            </div>
                        </li>
                        <li>
                            <div class="flex flex-col sm:flex-col md:flex-row lg:flex-row md:items-center gap-1">
                                <div class="flex items-center gap-1 order-2 md:order-1">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <div class="relative">
                                            <label for="number-of-safety-rating-{{ $i }}">
                                                <input id="number-of-safety-rating-{{ $i }}" name="safety" type="radio" value="{{ $i }}" class="hidden" {{ old('safety') == $i ? 'checked' : '' }} onchange="safety_rating_selected(this)" data-parsley-required="true" data-parsley-trigger="blur focusout change" data-parsley-required-message="Please select the available seats." data-parsley-errors-container="#parsley-seats-error">
                                                <img src="{{ old('safety') >= $i ? asset('assets/11-review-full-star.png') : asset('assets/11-review-full-star-grey.png') }}" class="w-5 h-5 mt-0.5 cursor-pointer safety-rating-image safety-rating-unselect-{{ $i }}" alt="">
                                            </label>
                                        </div>
                                    @endfor
                                </div>
                                <p class="md:ml-2 text-black md:w-[50%] text-left order-1 md:order-2">Safety</p>
                            </div>
                        </li>
                        <li>
                            <div class="flex flex-col sm:flex-col md:flex-row lg:flex-row md:items-center gap-1">
                                <div class="flex items-center gap-1 order-2 md:order-1">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <div class="relative">
                                            <label for="number-of-timeliness-rating-{{ $i }}">
                                                <input id="number-of-timeliness-rating-{{ $i }}" name="timeliness" type="radio" value="{{ $i }}" class="hidden" {{ old('timeliness') == $i ? 'checked' : '' }} onchange="timeliness_rating_selected(this)" data-parsley-required="true" data-parsley-trigger="blur focusout change" data-parsley-required-message="Please select the available seats." data-parsley-errors-container="#parsley-seats-error">
                                                <img src="{{ old('timeliness') >= $i ? asset('assets/11-review-full-star.png') : asset('assets/11-review-full-star-grey.png') }}" class="w-5 h-5 mt-0.5 cursor-pointer timeliness-rating-image timeliness-rating-unselect-{{ $i }}" alt="">
                                            </label>
                                        </div>
                                    @endfor
                                </div>
                                <p class="md:ml-2 text-black md:w-[50%] text-left order-1 md:order-2">Timeliness</p>
                            </div>
                        </li>
                        @error('conscious')
                            <li>
                                <div class="relative tooltip -bottom-4 group-hover:flex">
                                    <div role="tooltip" class="relative tooltiptext -top-2 z-10 leading-none transition duration-150 ease-in-out shadow-lg p-2 flex bg-red-500 text-gray-600 w-full rounded" >
                                        <p class="text-white leading-none text-sm lg:text-base">{{ $message }}</p>
                                    </div>
                                </div>
                            </li>
                        @enderror
                    </ul>
                </div>
                <div class="md:col-span-2 f00lex justify-center">
                    <button type="submit" class="button-exp-fill w-32">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@section('script')
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<script>
    function vehicle_condition_rating_selected(th) {
        var rating = $(th).val();

        for (i = 1; i <= rating; i++) {
            // Change the image source for selected ratings
            $(".vehicle-condition-rating-image.vehicle-condition-rating-unselect-" + i).attr('src', '{{ asset("assets/11-review-full-star.png") }}');
        }

        for (i = parseInt(rating) + 1; i <= 7; i++) {
            if (rating == 7) continue;
            // Change the image source back to unselected for remaining ratings
            $(".vehicle-condition-rating-image.vehicle-condition-rating-unselect-" + i).attr('src', '{{ asset("assets/11-review-full-star-grey.png") }}');
        }
    }

    function conscious_rating_selected(th) {
        var rating = $(th).val();

        for (i = 1; i <= rating; i++) {
            // Change the image source for selected ratings
            $(".conscious-rating-image.conscious-rating-unselect-" + i).attr('src', '{{ asset("assets/11-review-full-star.png") }}');
        }

        for (i = parseInt(rating) + 1; i <= 7; i++) {
            if (rating == 7) continue;
            // Change the image source back to unselected for remaining ratings
            $(".conscious-rating-image.conscious-rating-unselect-" + i).attr('src', '{{ asset("assets/11-review-full-star-grey.png") }}');
        }
    }

    function comfort_rating_selected(th) {
        var rating = $(th).val();

        for (i = 1; i <= rating; i++) {
            // Change the image source for selected ratings
            $(".comfort-rating-image.comfort-rating-unselect-" + i).attr('src', '{{ asset("assets/11-review-full-star.png") }}');
        }

        for (i = parseInt(rating) + 1; i <= 7; i++) {
            if (rating == 7) continue;
            // Change the image source back to unselected for remaining ratings
            $(".comfort-rating-image.comfort-rating-unselect-" + i).attr('src', '{{ asset("assets/11-review-full-star-grey.png") }}');
        }
    }

    function communication_rating_selected(th) {
        var rating = $(th).val();

        for (i = 1; i <= rating; i++) {
            // Change the image source for selected ratings
            $(".communication-rating-image.communication-rating-unselect-" + i).attr('src', '{{ asset("assets/11-review-full-star.png") }}');
        }

        for (i = parseInt(rating) + 1; i <= 7; i++) {
            if (rating == 7) continue;
            // Change the image source back to unselected for remaining ratings
            $(".communication-rating-image.communication-rating-unselect-" + i).attr('src', '{{ asset("assets/11-review-full-star-grey.png") }}');
        }
    }

    function attitude_rating_selected(th) {
        var rating = $(th).val();

        for (i = 1; i <= rating; i++) {
            // Change the image source for selected ratings
            $(".attitude-rating-image.attitude-rating-unselect-" + i).attr('src', '{{ asset("assets/11-review-full-star.png") }}');
        }

        for (i = parseInt(rating) + 1; i <= 7; i++) {
            if (rating == 7) continue;
            // Change the image source back to unselected for remaining ratings
            $(".attitude-rating-image.attitude-rating-unselect-" + i).attr('src', '{{ asset("assets/11-review-full-star-grey.png") }}');
        }
    }

    function hygiene_rating_selected(th) {
        var rating = $(th).val();

        for (i = 1; i <= rating; i++) {
            // Change the image source for selected ratings
            $(".hygiene-rating-image.hygiene-rating-unselect-" + i).attr('src', '{{ asset("assets/11-review-full-star.png") }}');
        }

        for (i = parseInt(rating) + 1; i <= 7; i++) {
            if (rating == 7) continue;
            // Change the image source back to unselected for remaining ratings
            $(".hygiene-rating-image.hygiene-rating-unselect-" + i).attr('src', '{{ asset("assets/11-review-full-star-grey.png") }}');
        }
    }

    function respect_rating_selected(th) {
        var rating = $(th).val();

        for (i = 1; i <= rating; i++) {
            // Change the image source for selected ratings
            $(".respect-rating-image.respect-rating-unselect-" + i).attr('src', '{{ asset("assets/11-review-full-star.png") }}');
        }

        for (i = parseInt(rating) + 1; i <= 7; i++) {
            if (rating == 7) continue;
            // Change the image source back to unselected for remaining ratings
            $(".respect-rating-image.respect-rating-unselect-" + i).attr('src', '{{ asset("assets/11-review-full-star-grey.png") }}');
        }
    }

    function safety_rating_selected(th) {
        var rating = $(th).val();

        for (i = 1; i <= rating; i++) {
            // Change the image source for selected ratings
            $(".safety-rating-image.safety-rating-unselect-" + i).attr('src', '{{ asset("assets/11-review-full-star.png") }}');
        }

        for (i = parseInt(rating) + 1; i <= 7; i++) {
            if (rating == 7) continue;
            // Change the image source back to unselected for remaining ratings
            $(".safety-rating-image.safety-rating-unselect-" + i).attr('src', '{{ asset("assets/11-review-full-star-grey.png") }}');
        }
    }

    function timeliness_rating_selected(th) {
        var rating = $(th).val();

        for (i = 1; i <= rating; i++) {
            // Change the image source for selected ratings
            $(".timeliness-rating-image.timeliness-rating-unselect-" + i).attr('src', '{{ asset("assets/11-review-full-star.png") }}');
        }

        for (i = parseInt(rating) + 1; i <= 7; i++) {
            if (rating == 7) continue;
            // Change the image source back to unselected for remaining ratings
            $(".timeliness-rating-image.timeliness-rating-unselect-" + i).attr('src', '{{ asset("assets/11-review-full-star-grey.png") }}');
        }
    }
</script>

@endsection
