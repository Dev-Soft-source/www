@php
    $errorPage = $errorPage ?? null;
    $currentLanguage = session('selectedLanguage') ?: app()->getLocale();
    $homeUrl = route('home', ['lang' => $currentLanguage ?: null]);
    $contactUrl = route('contact_us', ['lang' => $currentLanguage ?: null]);
@endphp
<!doctype html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/css/web.css">
    <style>
        .error {
            width: 12rem;
            height: 13rem;
            transform: matrix3d(1.25, -0.5, 0, 0, 0, 1, 0, 0, 0, 0, 1, 0, 0, 52, 0, 1);
        }

        .error_outer {
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            /* top: 4%; */
        }

        @media only screen and (min-width: 320px) and (max-width:785px) {
            .error {
                width: 7rem;
                height: 8rem;
            }

            /* .error_outer{
    top: 23%;
 } */
        }

        @media only screen and (min-width: 1920px) {

            /* .error_outer{
    top: 25%;
 } */
            .mb_100 {
                margin-bottom: 20rem !important;
            }
        }

        @media only screen and (min-width: 768px) and (max-width: 980px) {
            .error {
                width: 9rem;
                height: 10rem;
                transform: matrix3d(1.25, -0.5, 0, 0, 0, 1, 0, 0, 0, 0, 1, 0, 0, 52, 0, 1);
            }

            /* .error_outer{
    top: 16%;
 } */
        }
    </style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    screens: {
                        'desktop': '1920px',
                    },
                    keyframes: {
                        wiggle: {
                            '0%, 100%': {
                                transform: 'rotate(-4deg)'
                            },
                            '50%': {
                                transform: 'rotate(4deg)'
                            },
                        }
                    },
                    animation: {
                        'wiggle': 'wiggle 3s ease-in-out infinite',
                        'bounce': 'bounce 3s ease-in-out infinite',
                    },

                }
            }
        }
    </script>
</head>

<body>
    <div class="min-h-screen bg-gray-900 flex items-center justify-center px-4">
        <div class="max-w-2xl w-full text-center">
            <!-- GIF Container -->
            <div class="mb-8">
                <img 
                    src="{{ asset('assets/404/final2.gif') }}" 
                    alt="404 Not Found" 
                    class="mx-auto max-w-full h-auto rounded-lg shadow-2xl"
                    style="max-height: 400px;"
                >
            </div>
            
            <!-- Text Content -->
            <div class="mb-8 font-FuturaMdCnBT">
                <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">
                    {{ optional($errorPage)->error_404_heading ?? "Ho-ho-hold on! This page doesn't exist." }}
                </h1>
                <p class="text-gray-300 text-lg md:text-2xl">
                    {{ optional($errorPage)->error_404_paragraph_1 ?? "Kind of like this skinny Santa, cruising in a Tesla and eating a fat-free donut… in the middle of summer." }}
                    <br class="hidden md:block">
                    <br class="hidden md:block">
                    {{ optional($errorPage)->error_404_paragraph_2 ?? "The page you’re looking for may have been moved, removed, renamed - or maybe it never existed in the first place." }}
                </p>
            </div>
            
            <!-- Buttons -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a 
                    aria-label="{{ optional($errorPage)->error_404_back_home_btn ?? 'Back to Homepage' }}"
                    href="{{ $homeUrl }}"
                    class="button-exp-fill px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-300 w-full sm:w-auto">
                    {{ optional($errorPage)->error_404_back_home_btn ?? 'Back to Homepage' }}
                </a>
                <a 
                    aria-label="{{ optional($errorPage)->error_404_contact_btn ?? 'Contact us' }}"
                    href="{{ $contactUrl }}"
                    class="button-exp-fill px-8 py-3 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors duration-300 w-full sm:w-auto">
                    {{ optional($errorPage)->error_404_contact_btn ?? 'Contact us' }}
                </a>
            </div>
        </div>
    </div>
</body>

</html>
