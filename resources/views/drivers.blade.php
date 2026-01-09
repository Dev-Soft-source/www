@extends('layouts.template')

@section('content')

<div class="w-full col-span-12 md:col-span-9 container mx-auto px-4 md:px-8 xl:px-0 my-14">
    <div class="flex items-center space-x-2">
        <h1 class="mb-0 font-FuturaMdCnBT">
            @isset($driverPage->main_heading)
                {{ $driverPage->main_heading }}
            @endisset
        </h1>
    </div>
    <div class="grid grid-cols-12 gap-6 mt-4">
        <div class="col-span-12 lg:col-span-7">
            <div class="text-justify">
                <p class="text-lg text-gray-900">
                    @isset($driverPage->sub_heading)
                        {{ $driverPage->sub_heading }}
                    @endisset
                </p>
                <div class="">
                    @isset($driverPage->page_description)
                        {!! $driverPage->page_description !!}
                    @endisset
                </div>
            </div>
        </div>

        <div class="col-span-12 lg:col-span-5">
            @if ($video)
                @php
                    // $video->link contains the YouTube video URL
                    $youtubeUrl = $video->link;
                    // Extract the video ID from the URL
                    parse_str(parse_url($youtubeUrl, PHP_URL_QUERY), $query);
                    $videoId = $query['v'] ?? '';
                @endphp

                @if (!empty($videoId))
                    {{-- Embed the YouTube video using an iframe --}}
                    <iframe class="mx-auto rounded-md 2xl:w-[560px] md:w-[500px] lg:w-full h-[300px] w-full md:h-[315px]" 
                        src="https://www.youtube.com/embed/{{ $videoId }}">
                    </iframe>
                @else
                    <p>Invalid YouTube video URL</p>
                @endif
            @endif
        </div>

    </div>
</div>

@endsection