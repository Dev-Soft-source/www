@extends('layouts.template')

@section('content')

<div class="container mx-auto my-14">
    <div class="w-full md:w-[70%] mx-auto px-4 md:px-0 ">
    <div class="bg-white border rounded p-4 border-gray-200 w-full shadow pb-8">
        <div class="pb-2">
            <h1 class="mb-0">Review</h1>
        </div>
        <div class="space-y-4 mt-4">
            <div>
                <div class="flex items-center space-x-2">
                  <div class="w-16 h-16 md:w-24 md:h-24 bg-gray-50 border rounded-full overflow-hidden">
                    <img class="w-full h-full object-contain rounded-full" src="{{ $rating->from->profile_image }}" alt="">
                  </div>
                    <p class="text-black text-2xl lg:text-3xl font-FuturaMdCnBT">{{ $rating->from->first_name }}</p>
                </div>
                <div class="mt-6">
                    <label for="meeting" class="text-gray-900 font-medium text-lg mb-2"></label>
                    <textarea id="meeting" rows="5" name="review"
                        class="block p-2.5 w-full text-gray-900 bg-white rounded border border-gray-300 focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 mt-2" readonly>{{ $rating->review }}</textarea>
                </div>
            </div>
            <div>
                <ul class="space-y-2">
                    <li>
                        <div class="flex items-center">
                            <p class="text-black text-2xl lg:text-3xl font-FuturaMdCnBT">Review criteria</p>
                        </div>
                    </li>
                    <li>
                        <div class="flex flex-col sm:flex-col md:flex-row lg:flex-row md:items-center gap-1">
                            <div class="flex items-center gap-1 order-2 md:order-1">
                                @for ($i = 1; $i <= 5; $i++)
                                    <div class="relative">
                                        @if ($i <= $rating->conscious)
                                            <img src="{{ asset('assets/11-review-full-star.png') }}" class="w-5 h-5 mt-0.5" alt="">
                                        @else
                                            <img src="{{ asset('assets/11-review-full-star-grey.png') }}" class="w-5 h-5 mt-0.5" alt="">
                                        @endif
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
                                        @if ($i <= $rating->comfort)
                                            <img src="{{ asset('assets/11-review-full-star.png') }}" class="w-5 h-5 mt-0.5" alt="">
                                        @else
                                            <img src="{{ asset('assets/11-review-full-star-grey.png') }}" class="w-5 h-5 mt-0.5" alt="">
                                        @endif
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
                                        @if ($i <= $rating->communication)
                                            <img src="{{ asset('assets/11-review-full-star.png') }}" class="w-5 h-5 mt-0.5" alt="">
                                        @else
                                            <img src="{{ asset('assets/11-review-full-star-grey.png') }}" class="w-5 h-5 mt-0.5" alt="">
                                        @endif
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
                                        @if ($i <= $rating->attitude)
                                            <img src="{{ asset('assets/11-review-full-star.png') }}" class="w-5 h-5 mt-0.5" alt="">
                                        @else
                                            <img src="{{ asset('assets/11-review-full-star-grey.png') }}" class="w-5 h-5 mt-0.5" alt="">
                                        @endif
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
                                        @if ($i <= $rating->hygiene)
                                            <img src="{{ asset('assets/11-review-full-star.png') }}" class="w-5 h-5 mt-0.5" alt="">
                                        @else
                                            <img src="{{ asset('assets/11-review-full-star-grey.png') }}" class="w-5 h-5 mt-0.5" alt="">
                                        @endif
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
                                        @if ($i <= $rating->respect)
                                            <img src="{{ asset('assets/11-review-full-star.png') }}" class="w-5 h-5 mt-0.5" alt="">
                                        @else
                                            <img src="{{ asset('assets/11-review-full-star-grey.png') }}" class="w-5 h-5 mt-0.5" alt="">
                                        @endif
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
                                        @if ($i <= $rating->safety)
                                            <img src="{{ asset('assets/11-review-full-star.png') }}" class="w-5 h-5 mt-0.5" alt="">
                                        @else
                                            <img src="{{ asset('assets/11-review-full-star-grey.png') }}" class="w-5 h-5 mt-0.5" alt="">
                                        @endif
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
                                        @if ($i <= $rating->timeliness)
                                            <img src="{{ asset('assets/11-review-full-star.png') }}" class="w-5 h-5 mt-0.5" alt="">
                                        @else
                                            <img src="{{ asset('assets/11-review-full-star-grey.png') }}" class="w-5 h-5 mt-0.5" alt="">
                                        @endif
                                    </div>
                                @endfor
                            </div>
                            <p class="md:ml-2 text-black md:w-[50%] text-left order-1 md:order-2">Timeliness</p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    </div>
</div>

@endsection
