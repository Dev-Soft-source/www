@extends('layouts.template')

@section('content')

<div class="container mx-auto my-6 md:my-10 xl:my-14 px-4 xl:px-0 pt-safe">
    <div class="pb-4 pt-4 md:pt-2 hideheader1">
        <h1 class="mb-0 text-left">
            @isset($disputePolicyPage->main_heading)
                {{ $disputePolicyPage->main_heading }}
            @endisset
        </h1>
    </div>
    <div class="pb-2">
        @isset($disputePolicyPage->main_text)
            {!! $disputePolicyPage->main_text !!}
        @endisset
    </div>
</div>

@endsection