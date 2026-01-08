<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Travel your way with ProximaRide: women-only Pink Rides, Extra-Care rides with verified drivers, customizable options, fair prices—and no booking fee for students.">
    <meta name="keywords" content="ridesharing, rideshare, women-only rides, pink rides, extra care rides, safe rides, affordable rides, student rides, no booking fee, carpool, ProximaRide">
    <meta name="author" content="ProximaRide">
    <meta name="robots" content="index, follow">
    

    <link rel="icon" type="image/x-icon" href="/assets/favicon.png">

    <title>{{ config('app.name', 'ProximaRide') }}</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">
    <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"
  />
    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <!-- Scripts -->
    <script src="{{ asset('js/web.js') }}" defer></script>
    <style>
        .tooltip .tooltiptext {
            width: fit-content;
            padding-left: 10px;
            padding-right: 10px;
        }
        .tooltiptext::after {
            content: "";
             border-width: 8px;
             border-style: solid;
             border-color: transparent transparent #2563eb transparent;
             position: absolute;
             top: -15px;
             left: 45%;
        }
    </style>
</head>

<body>
    <div class="font-sans text-gray-900 antialiased">
        @if (Request::url() == route('admin.login'))
            {{ $slot }}
        @else
            <div id="canexp-app">
                {{ $slot }}
            </div>
        @endif
    </div>
</body>

</html>