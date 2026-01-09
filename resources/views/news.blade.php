@extends('layouts.template')

@section('content')

<div class="container mx-auto  my-6 md:my-10 xl:my-14 px-4 xl:px-0">
    <div class="container mx-auto">
        <h1 class="text-blue-600 text-center mb-0 font-FuturaMdCnBT">
            Media
        </h1>
        <div class="grid lg:grid-cols-4 md:grid-cols-2 sm:grid-cols-1 gap-4 xl:gap-6 mt-4 md:mt-6 xl:mt-10">
            @foreach ($articles as $article)
                <div class="rounded bg-white shadow-3xl">
                    <div class="p-4">
                        <div>
                            <p class="text-2xl font-FuturaMdCnBT">{{ $article->articleDetail[0]->title }}</p>
                            <p class="lg:text-lg md:text-base text-base">Agency: {{ $article->agency }}</p>
                            <p class="lg:text-lg md:text-base text-base">Added by: {{ $article->added_by }}</p>
                        </div>
                        <div class="flex justify-center items-center">
                            <a href="{{ route('news_detail', ['lang' => optional($selectedLanguage)->abbreviation, 'id' => $article->id]) }}" class="bg-primary text-white py-2 px-3 rounded">
                                Read article                          
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

@endsection