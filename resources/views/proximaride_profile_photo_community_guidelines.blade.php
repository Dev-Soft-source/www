@extends('layouts.template')

@section('content')
    <div class="container mx-auto my-6 md:my-10 xl:my-14 px-4 xl:px-0 pt-safe">
        <div class="pb-4 pt-4 md:pt-2 hideheader1">
            <h1 class="mb-0 text-left font-FuturaMdCnBT">
                @isset($profilePhotoGuidelinesPage->main_heading)
                    {{ $profilePhotoGuidelinesPage->main_heading }}
                @endisset
            </h1>
        </div>
        <div class="pb-2 content">
            @isset($profilePhotoGuidelinesPage->main_text)
                {!! $profilePhotoGuidelinesPage->main_text !!}
            @endisset
            <h2 class="text-2xl md:text-3xl font-FuturaMdCnBT text-blue-600 mb-4 mt-8 text-center">{{ $profilePhotoGuidelinesPage->example_label ?? 'Here is a Good Example' }}</h2>
            <div class="flex justify-center mb-6">
                <img class="max-w-full h-auto rounded-lg shadow-md" src="{{ asset('home_page_icons/1749147041-Profile Photo Guidelines-2.jpg') }}" alt="Good profile photo example" style="max-width: 400px;">
            </div>
        </div>
    </div>
@endsection
