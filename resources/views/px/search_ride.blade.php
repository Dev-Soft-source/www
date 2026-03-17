@extends('px.search_template')

@section('style')
@endsection

@section('search-header')
    <div class="text-center mb-4">
        <h1 class="font-FuturaMdCnBT">
            {{ $findRidePage->main_heading ?? 'Search PX Rides' }}
        </h1>
    </div>
@endsection
