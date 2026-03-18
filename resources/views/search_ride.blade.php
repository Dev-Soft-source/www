@extends('px.search_template')

@section('style')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
        /* Shared style for all checkbox & radio inputs */
        .form-check-input {
            margin-top: 0.1rem; /* mt-2 */
            width: 1.25rem;       /* w-4 */
            height: 1.25rem;      /* h-4 */
            cursor: pointer;
            background-color: #ffffff; /* bg-white */
            border-width: 1px;
            border-color: #d1d5db;     /* border-gray-300 */
            border-radius: 0.25rem;    /* rounded */
        }

        .form-check-input:focus {
            outline: 2px solid transparent;
            outline-offset: 2px;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5); /* approx focus:ring-blue-500 focus:ring-2 */
        }
    </style>
@endsection

@section('search-header')
    <div class="text-center mb-4">
        <h1 class="font-FuturaMdCnBT">
            {{ $findRidePage->main_heading ?? 'Search PX Rides' }}
        </h1>
    </div>
@endsection
