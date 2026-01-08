@extends('layouts.template')

@section('content')

<div class="container mx-auto my-6 md:my-10 xl:my-14 px-4 xl:px-0 pt-safe">
    <div class="pb-4 pt-4 md:pt-2 hideheader1">
        <h1 class="mb-0 text-left">
            @isset($refundPolicyPage->main_heading)
                {{ $refundPolicyPage->main_heading }}
            @endisset
        </h1>
    </div>
    <div class="pb-2">
        @isset($refundPolicyPage->main_text)
            {!! $refundPolicyPage->main_text !!}
        @endisset
    </div>
</div>

@endsection