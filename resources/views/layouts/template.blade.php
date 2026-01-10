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
    <title>{{ config('app.name', 'Home') }}</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    @yield('style')
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
            border-color: transparent transparent #ef4444 transparent;
            position: absolute;
            top: -15px;
            left: 45%;
        }

        #notification-container {
            position: fixed;
            bottom: 10px;
            right: 10px;
            width: 300px;
        }

        .notification {
            background: #fff;
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            /* IE and Edge */
            scrollbar-width: none;
            /* Firefox */
        }

        /* Smooth scroll behavior for error navigation */
        html {
            scroll-behavior: smooth;
        }

        /* Safe area support for mobile devices with notches */
        .pt-safe {
            padding-top: max(2.5rem, env(safe-area-inset-top));
        }

        @media (min-width: 768px) {
            .pt-safe {
                padding-top: max(3.5rem, env(safe-area-inset-top));
            }
        }

        /* Global Placeholder Styling - All placeholders must be identical */
        input::placeholder,
        textarea::placeholder {
            font-style: italic !important;
            font-size: 0.9375rem !important; /* 15px - smaller than text-lg (18px) but not too small */
            color: rgb(107 114 128) !important; /* gray-500 */
        }

        /* Ensure consistency across all input types */
        input[type="text"]::placeholder,
        input[type="email"]::placeholder,
        input[type="password"]::placeholder,
        input[type="tel"]::placeholder,
        input[type="number"]::placeholder,
        input[type="date"]::placeholder,
        input[type="time"]::placeholder,
        textarea::placeholder {
            font-style: italic !important;
            font-size: 0.9375rem !important;
            color: rgb(107 114 128) !important;
        }
    </style>
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "ItemList",
      "itemListElement": [
        {
          "@type": "SiteNavigationElement",
          "position": 1,
          "name": "Find a Ride",
          "url": "{{ url(route('search_ride', ['lang' => optional($selectedLanguage)->abbreviation], false)) }}"
        },
        {
          "@type": "SiteNavigationElement",
          "position": 2,
          "name": "Post a Ride",
          "url": "{{ url(route('post_ride', ['lang' => optional($selectedLanguage)->abbreviation], false)) }}"
        },
        {
          "@type": "SiteNavigationElement",
          "position": 3,
          "name": "ProximaRide for Students",
          "url": "{{ url(route('students', ['lang' => optional($selectedLanguage)->abbreviation], false)) }}"
        },
        {
          "@type": "SiteNavigationElement",
          "position": 4,
          "name": "Coffee on the Wall",
          "url": "{{ url(route('coffee_on_wall', ['lang' => optional($selectedLanguage)->abbreviation], false)) }}"
        },
        {
          "@type": "SiteNavigationElement",
          "position": 5,
          "name": "Pink Rides",
          "url": "{{ url(route('pink_ride', ['lang' => optional($selectedLanguage)->abbreviation], false)) }}"
        },
        {
          "@type": "SiteNavigationElement",
          "position": 6,
          "name": "Extra-Care Rides",
          "url": "{{ url(route('folk_ride', ['lang' => optional($selectedLanguage)->abbreviation], false)) }}"
        },
        {
          "@type": "SiteNavigationElement",
          "position": 7,
          "name": "Short-Distance Ridesharing",
          "url": "{{ url(route('pink_ride', ['lang' => optional($selectedLanguage)->abbreviation], false)) }}"
        },
        {
          "@type": "SiteNavigationElement",
          "position": 8,
          "name": "ProximaRide for Tourists",
          "url": "{{ url(route('news', ['lang' => optional($selectedLanguage)->abbreviation], false)) }}"
        }
      ]
    }
    </script>
</head>

<body>
    <div class="ridesharing font-sans text-gray-900 antialiased flex flex-col h-full min-h-screen ">
        @include('layouts.inc.navbar')

        <div id="notification-container"></div>

        <div class="flex-auto">
            @yield('content')
        </div>

        {{-- <div class="hidden md:block">
            <button type="button" onclick="toggleModal('modal-id')" class="hideLanguageIcon">
                <div class="fixed bottom-14 right-3 z-30">
                    <span
                        class="inline-flex h-14 w-14 items-center justify-center rounded-full bg-gray-600/50 cursor-pointer">
                        <span class="font-semibold text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M10.5 21l5.25-11.25L21 21m-9-3h7.5M3 5.621a48.474 48.474 0 016-.371m0 0c1.12 0 2.233.038 3.334.114M9 5.25V3m3.334 2.364C11.176 10.658 7.69 15.08 3 17.502m9.334-12.138c.896.061 1.785.147 2.666.257m-4.589 8.495a18.023 18.023 0 01-3.827-5.802">
                                </path>
                            </svg>
                        </span>
                    </span>
                </div>
            </button>
        </div> --}}
        
        <button type="button" data-mdb-ripple="true" data-mdb-ripple-color="light"
            class="hideTopIcon inline-block p-3 w-10 h-10 z-20 fixed bottom-2 right-5 bg-gray-600/50 text-white font-medium text-xs leading-tight uppercase rounded-full shadow-md hover:bg-gray-600 hover:shadow-lg focus:bg-gray-600 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-gray-600 active:shadow-lg transition duration-150 ease-in-out"
            id="btn-back-to-top">
            <svg aria-hidden="true" focusable="false" data-prefix="fas" class="w-4 h-4" role="img"
                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                <path fill="currentColor"
                    d="M34.9 289.5l-22.2-22.2c-9.4-9.4-9.4-24.6 0-33.9L207 39c9.4-9.4 24.6-9.4 33.9 0l194.3 194.3c9.4 9.4 9.4 24.6 0 33.9L413 289.4c-9.5 9.5-25 9.3-34.3-.4L264 168.6V456c0 13.3-10.7 24-24 24h-32c-13.3 0-24-10.7-24-24V168.6L69.2 289.1c-9.3 9.8-24.8 10-34.3.4z">
                </path>
            </svg>
        </button>

        @include('layouts.inc.footer')
    </div>

    <div class="hidden overflow-x-hidden overflow-y-auto fixed inset-0 z-50 outline-none focus:outline-none justify-center items-center"
        id="modal-id">
        <div class="relative w-full my-6 mx-auto max-w-2xl animate__animated animate__fadeIn">
            <!--content-->
            <div
                class="relative animate__animated animate__fadeIn rounded-lg shadow border-0 flex flex-col w-full bg-white outline-none focus:outline-none max-w-2xl w-full">
                <!--header-->
                <div class="flex items-center justify-between p-4 border-b rounded-t">
                    <h3 class="can-edu-h3 mb-0">Select website language</h3>
                    <div>
                        <button type="button"
                            class="mt-1 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-full border border-primary text-sm p-1 ml-auto inline-flex items-center"
                            data-modal-hide="defaultModal" onclick="toggleModal('modal-id')">
                            <svg aria-hidden="true" class="w-5 h-5 text-primary" fill="currentColor" viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                    </div>
                </div>
                <!-- Modal body -->
                <div class="p-6 space-y-6">
                    <div class="flex flex-wrap gap-8">
                        @foreach ($languages as $language)
                            @php
                                $languageParameter = 'lang';
                                $currentRoute = app('router')->getCurrentRoute();

                                // Get the parameters from the current route
                                $routeParams = $currentRoute->parameters();

                                // Add lang parameter
                                $routeParams['lang'] = $language->abbreviation;

                                // Preserve existing query parameters
                                $queryParameters = request()->query();
                                $routeParams = array_merge($routeParams, $queryParameters);

                                // Generate the URL
                                if ($currentRoute->getName() === 'news_detail') {
                                    $languageUrl = route('news', ['lang' => $language->abbreviation]);
                                } else {
                                    $languageUrl = route($currentRoute->getName(), $routeParams);
                                }
                            @endphp
                            <div>
                                <a href="{{ $languageUrl }}" class="space-y-2">
                                    <img class="mx-auto" src="{{ $language->flag_icon }}"
                                        style="width: 32px; height: 32px;">
                                    <span
                                        class="pt-2 @isset($selectedLanguage){{ $selectedLanguage->name === $language->name ? 'text-primary' : 'text-gray-700' }}@endisset">{{ $language->name }}</span>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="hidden opacity-25 fixed inset-0 z-40 bg-black" id="modal-id-backdrop"></div>

    @php
        $logoutPage = App\Models\LogoutSettingDetail::where('language_id', $selectedLanguage->id)->first();
    @endphp
    <div class="hidden overflow-x-hidden overflow-y-auto fixed inset-0 z-50 outline-none focus:outline-none justify-center items-center"
        id="logout-modal">
        <div class="relative h-screen my-6 mx-auto flex items-center justify-center w-full w-full">
            <!--content-->
            <div
                class="relative animate__animated animate__fadeIn transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                <button type="button" onclick="toggleModal('logout-modal')"
                    class="absolute top-3 right-3 text-gray-400 hover:text-gray-500">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start justify-center">
                        <!-- <div
                            class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full sm:mx-0 sm:h-10 sm:w-10 bg-green-500">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-lg text-white w-8 h-8" viewBox="0 0 16 16">
                                <path d="M7.005 3.1a1 1 0 1 1 1.99 0l-.388 6.35a.61.61 0 0 1-1.214 0zM7 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0"/>
                            </svg>
                        </div> -->
                    </div>
                    <div class="text-center sm:ml-4 sm:mt-0 sm:text-left">
                        <div class="">
                            <h3 class="text-3xl text-center font-FuturaMdCnBT text-gray-900 mb-4"
                                id="modal-title">{!! session('heading') !!}</h3>
                        </div>
                        <div class="mt-2 w-full">
                            <p class="can-exp-p text-center">
                                {{ $logoutPage->confirmation_message_heading ?? 'Are you sure you want to log out?' }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="px-4 pb-6 pt-4 flex items-center space-x-2 sm:space-x-4 sm:px-6 justify-center">
                    <a href="{{ route('logout', ['lang' => optional($selectedLanguage)->abbreviation]) }}"
                    class="button-exp-fill min-w-24 bg-greenXS hover:bg-greenXS text-white text-center flex items-center justify-center">
                        {{ $logoutPage->confirmation_yes_label ?? 'Yes' }}
                    </a>

                    <button type="button" onclick="toggleModal('logout-modal')"
                        class="button-exp-fill min-w-24">
                        {{ $logoutPage->confirmation_no_label ?? 'No, stay logged in' }}
                    </button>
                </div>

            </div>
        </div>
    </div>
    <div class="hidden opacity-25 fixed inset-0 z-40 bg-black" id="logout-modal-backdrop"></div>

    <script src="https://unpkg.com/@popperjs/core@2.9.1/dist/umd/popper.min.js" charset="utf-8"></script>
    <script type="text/javascript">
        function toggleModal(modalID) {
            document.getElementById(modalID).classList.toggle("hidden");
            document.getElementById(modalID + "-backdrop").classList.toggle("hidden");
            document.getElementById(modalID).classList.toggle("flex");
            document.getElementById(modalID + "-backdrop").classList.toggle("flex");
        }
    </script>

<script>
    window.addEventListener('scroll', function() {
        if (window.scrollY > 0) {
            document.getElementById("navbar").style.padding = "4px 0";
            document.getElementById("navbar").style.transition = "all 500ms ease-in-out";
        } else {
            document.getElementById("navbar").style.padding = "8px 0";
            document.getElementById("navbar").style.transition = "all 500ms ease-in-out";
        }
    });

    const dropdownMobileButton = document.getElementById('dropdownMobileButton');
    const dropdownDesktopButton = document.getElementById('dropdownDesktopButton');
    const dropdownProfileButton = document.getElementById('dropdownProfileButton');
    const dropdownNotificationButton = document.getElementById('dropdownNotificationButton');
    const dropdownMobile = document.getElementById('dropdown_mobile');
    const dropdownDesktop = document.getElementById('dropdown_desktop');
    const dropdownProfile = document.getElementById('dropdown_profile');
    const dropdownNotification = document.getElementById('dropdown_notification');

    // Function to close all dropdowns
    function closeAllDropdowns() {
        if (dropdownMobile) dropdownMobile.classList.add('hidden');
        if (dropdownDesktop) dropdownDesktop.classList.add('hidden');
        if (dropdownProfile) dropdownProfile.classList.add('hidden');
        if (dropdownNotification) dropdownNotification.classList.add('hidden');
    }

    // Toggle dropdowns
    if (dropdownMobileButton) {
        dropdownMobileButton.addEventListener('click', function(event) {
            const wasHidden = dropdownMobile.classList.contains('hidden');
            closeAllDropdowns();
            if (wasHidden) {
                dropdownMobile.classList.remove('hidden');
            }
            event.stopPropagation();
        });
    }

    if (dropdownDesktopButton) {
        dropdownDesktopButton.addEventListener('click', function(event) {
            const wasHidden = dropdownDesktop.classList.contains('hidden');
            closeAllDropdowns();
            if (wasHidden) {
                dropdownDesktop.classList.remove('hidden');
            }
            event.stopPropagation();
        });
    }

    if (dropdownProfileButton) {
        dropdownProfileButton.addEventListener('click', function(event) {
            const wasHidden = dropdownProfile.classList.contains('hidden');
            closeAllDropdowns();
            if (wasHidden) {
                dropdownProfile.classList.remove('hidden');
            }
            event.stopPropagation();
        });
    }

    if (dropdownNotificationButton) {
        dropdownNotificationButton.addEventListener('click', function(event) {
            const wasHidden = dropdownNotification.classList.contains('hidden');
            closeAllDropdowns();
            if (wasHidden) {
                dropdownNotification.classList.remove('hidden');
            }
            event.stopPropagation();
        });
    }

    // Close all dropdowns when clicking outside
    document.addEventListener('click', function(event) {
        const clickedInsideAnyDropdown = 
            (dropdownMobileButton && (dropdownMobileButton.contains(event.target) || (dropdownMobile && dropdownMobile.contains(event.target)))) ||
            (dropdownDesktopButton && (dropdownDesktopButton.contains(event.target) || (dropdownDesktop && dropdownDesktop.contains(event.target)))) ||
            (dropdownProfileButton && (dropdownProfileButton.contains(event.target) || (dropdownProfile && dropdownProfile.contains(event.target)))) ||
            (dropdownNotificationButton && (dropdownNotificationButton.contains(event.target) || (dropdownNotification && dropdownNotification.contains(event.target))));

        if (!clickedInsideAnyDropdown) {
            closeAllDropdowns();
        }
    });

    // Prevent clicks within dropdowns from closing them
    if (dropdownMobile) {
        dropdownMobile.addEventListener('click', function(event) {
            event.stopPropagation();
        });
    }
    if (dropdownDesktop) {
        dropdownDesktop.addEventListener('click', function(event) {
            event.stopPropagation();
        });
    }
    if (dropdownProfile) {
        dropdownProfile.addEventListener('click', function(event) {
            event.stopPropagation();
        });
    }
    if (dropdownNotification) {
        dropdownNotification.addEventListener('click', function(event) {
            // Only prevent closing the dropdown for specific elements (like delete buttons and "View All")
            // Allow notification links to close the dropdown by not stopping propagation
            const target = event.target.closest('button[onclick*="openModal"], .button-exp-fill, button[onclick*="openModal"] svg, button[onclick*="openModal"] path');
            if (target) {
                event.stopPropagation();
            }
            // For notification links (a tags with href), don't stop propagation to allow dropdown to close
        });
    }

    // Toggle the navbar
    function toggleNavbar() {
        const navbar = document.getElementById("navbar-default");
        const profilebar = document.getElementById("profilebar-default");
        if (navbar.classList.contains('hidden')) {
            navbar.classList.remove('hidden');
            if (profilebar && !profilebar.classList.contains('hidden')) {
                profilebar.classList.add('hidden');
            }
        } else {
            navbar.classList.add('hidden');
        }
    }

    // Toggle the profile bar
    function toggleProfilebar() {
        const profilebar = document.getElementById("profilebar-default");
        const navbar = document.getElementById("navbar-default");
        if (profilebar.classList.contains('hidden')) {
            profilebar.classList.remove('hidden');
            if (navbar && !navbar.classList.contains('hidden')) {
                navbar.classList.add('hidden');
            }
        } else {
            profilebar.classList.add('hidden');
        }
    }

    // Add event listeners for navbar and profile bar toggles
    const toggleButton = document.querySelector('[data-collapse-toggle="navbar-default"]');
    if (toggleButton) {
        toggleButton.addEventListener("click", toggleNavbar);
    }

    const toggleProfileButton = document.querySelector('[data-collapse-toggle="profilebar-default"]');
    if (toggleProfileButton) {
        toggleProfileButton.addEventListener("click", toggleProfilebar);
    }

    // Back-to-top button
    const btn = document.getElementById('btn-back-to-top');
    if (btn) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 500) {
                btn.classList.add('show');
            } else {
                btn.classList.remove('show');
            }
        });

        btn.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    // Auto-scroll to first error field on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Add slight delay to ensure all error elements are rendered
        setTimeout(function() {
            scrollToFirstErrorField();
        }, 200);
    });

    function scrollToFirstErrorField() {
        // Find the first error element (validation errors)
        const errorSelectors = [
            '.tooltip:not(.hidden)'
        ];
        
        let firstError = null;
        let firstErrorPosition = Infinity;
        
        // Check each selector to find the first error
        for (const selector of errorSelectors) {
            const elements = document.querySelectorAll(selector);
            for (const element of elements) {
                // Skip if element is hidden or not visible
                if (element.offsetParent === null || !isElementVisible(element)) continue;
                
                // Find the associated form field
                let targetField = null;
                let errorPosition = 0;
                
                if (element.classList.contains('tooltip') || element.querySelector('.tooltip')) {
                    // For tooltip errors, find the input field
                    const parent = element.parentElement;
                    targetField = parent ? parent.querySelector('input, select, textarea') : null;
                    console.log(element, parent, targetField)
                } else if (element.classList.contains('border-red-500') || element.classList.contains('is-invalid')) {
                    // The element itself is the field
                    targetField = element;
                } else if (element.tagName && ['INPUT', 'SELECT', 'TEXTAREA'].includes(element.tagName)) {
                    // Element is already a form field
                    targetField = element;
                } else {
                    // For other error elements, try to find nearby input
                    const container = element.closest('div, .form-group, .field-group');
                    if (container) {
                        targetField = container.querySelector('input, select, textarea');
                        // If no field in container, look for the previous field
                        if (!targetField) {
                            targetField = container.querySelector('label');
                            if (targetField) {
                                const labelFor = targetField.getAttribute('for');
                                if (labelFor) {
                                    targetField = document.getElementById(labelFor);
                                }
                            }
                        }
                    }
                }
                
                if (targetField) {
                    errorPosition = targetField.getBoundingClientRect().top + window.pageYOffset;
                    if (errorPosition < firstErrorPosition) {
                        firstError = targetField;
                        firstErrorPosition = errorPosition;
                    }
                }
            }
        }
        
        // If we found an error field, scroll to it
        if (firstError) {
            // Find the field's container or label to get the complete field area
            let fieldContainer = firstError.closest('.mt-2, .form-group, div');
            let targetElement = firstError;
            
            // Look for the label associated with this field to include it in the scroll target
            const label = fieldContainer ? fieldContainer.querySelector('label') : null;
            if (label) {
                targetElement = label;
            } else {
                // If no label in container, check if there's a label with 'for' attribute
                const fieldId = firstError.getAttribute('id');
                if (fieldId) {
                    const associatedLabel = document.querySelector(`label[for="${fieldId}"]`);
                    if (associatedLabel) {
                        targetElement = associatedLabel;
                    }
                }
            }
            
            // Calculate position to put the target element at the very top
            const navbarHeight = document.getElementById('navbar') ? document.getElementById('navbar').offsetHeight : 0;
            const targetPosition = targetElement.getBoundingClientRect().top + window.pageYOffset - navbarHeight;
            
            window.scrollTo({
                top: Math.max(0, targetPosition),
                behavior: 'smooth'
            });
            
            // Focus the input field for better UX (with delay to avoid scroll interruption)
            setTimeout(() => {
                if (firstError.focus) {
                    firstError.focus();
                }
            }, 500);
        }
    }
    
    // Helper function to check if element is actually visible
    function isElementVisible(element) {
        return element.offsetWidth > 0 && element.offsetHeight > 0 && 
               getComputedStyle(element).visibility !== 'hidden' &&
               getComputedStyle(element).display !== 'none';
    }

    // Global function to scroll to first error (can be called after AJAX submissions)
    window.scrollToFirstError = function() {
        scrollToFirstErrorField();
    };
</script>

    <script src="https://www.gstatic.com/firebasejs/8.3.2/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.3.2/firebase-messaging.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/firebase-messaging-sw.js')
                .then(function(registration) {
                    console.log('Service Worker registration successful with scope: ', registration.scope);
                }).catch(function(err) {
                    console.log('Service Worker registration failed: ', err);
                });
        }

        // Your web app's Firebase configuration
        // TODO: Replace with your new Firebase project (proxima-ride-app-devop) web app config
        const firebaseConfig = {
            apiKey: "AIzaSyBt3Y5R24dI1V-qArWRVVXwSvrwrvreyf0",
            authDomain: "proxima-ride-app-devop.firebaseapp.com",
            projectId: "proxima-ride-app-devop",
            storageBucket: "proxima-ride-app-devop.firebasestorage.app",
            messagingSenderId: "785619130237",
            appId: "1:785619130237:web:20f9ee0f705e60e4b5de14"
        };

        // Initialize Firebase
        firebase.initializeApp(firebaseConfig);
        const messaging = firebase.messaging();

        // Check if user is authenticated (Example: Adjust this to your own authentication check)
        const userIsLoggedIn = {!! json_encode(auth()->check()) !!}; // Assuming you have an auth check in your Laravel blade

        if (userIsLoggedIn) {
            initFirebaseMessagingRegistration();
        }

        function initFirebaseMessagingRegistration() {
            messaging.requestPermission().then(function() {
                return messaging.getToken()
            }).then(function(token) {
                axios.post("{{ route('fcmToken') }}", {
                    _method: "PATCH",
                    token
                }).then(({
                    data
                }) => {
                    console.log(data);
                }).catch(({
                    response: {
                        data
                    }
                }) => {
                    console.error(data);
                });

            }).catch(function(err) {
                console.log(`Token Error :: ${err}`);
            });
        }

        // Foreground message handler
        messaging.onMessage(function(payload) {
            console.log('Message received. ', payload);
            // Display notification in the DOM or use the Notification API
            const notificationTitle = '';
            const notificationOptions = {
                body: payload.notification.body,
                icon: payload.notification.icon
            };

            if (Notification.permission === 'granted') {
                new Notification(notificationTitle, notificationOptions);
            }

            // Optionally, display the notification in a div
            const notificationContainer = document.getElementById('notification-container');
            if (notificationContainer) {
                const notificationElement = document.createElement('div');
                notificationElement.classList.add('notification');
                notificationElement.innerHTML = `
                    <p>${notificationOptions.body}</p>
                `;
                notificationContainer.appendChild(notificationElement);
            }
        });
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>

    @auth
    <script>
        // Initialize Pusher for real-time chat updates
        (function() {
            const authUserId = {{ auth()->id() }};
            const pusherKey = '{{ config('broadcasting.connections.pusher.key') }}';
            const pusherCluster = '{{ config('broadcasting.connections.pusher.options.cluster') }}';

            if (pusherKey && authUserId) {
                const pusher = new Pusher(pusherKey, {
                    cluster: pusherCluster,
                    forceTLS: true
                });

                // Subscribe to the user's chat channel
                const channel = pusher.subscribe('chat.' + authUserId);

                // Listen for new messages
                channel.bind('message.event', function(data) {
                    console.log('Real-time message received:', data);

                    // Check if we're on the My Chats (inbox) page
                    const isInboxPage = window.location.pathname.includes('/my-chats') ||
                                        document.querySelector('[data-page="my-chats"]') !== null;

                    if (isInboxPage) {
                        // Refresh the inbox page to show new messages
                        // Use a small delay to avoid too frequent refreshes
                        if (!window.inboxRefreshPending) {
                            window.inboxRefreshPending = true;
                            setTimeout(function() {
                                window.location.reload();
                            }, 1000);
                        }
                    }

                    // Update notification count in bell icon
                    const countElement = document.querySelector('.absolute.-top-3.-right-2');
                    if (countElement) {
                        const currentCount = parseInt(countElement.textContent) || 0;
                        countElement.textContent = currentCount + 1;
                        countElement.style.display = 'flex';
                    }
                });
            }
        })();
    </script>
    @endauth

    <script>
        function openModal(id) {
            $('#delete_message_confirmation').removeClass('hidden');
            $('#notificationId').val(id);
        }

        function closeNotificationModal(id) {
            $('#delete_message_confirmation').addClass('hidden');
            $('#notificationId').val();
        }

        function delete_notification() {

            // console.log();
            $.ajax({
                url: "{{ route('delete_notifications', ['lang' => $selectedLanguage->abbreviation]) }}",
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: $('#notificationId').val()
                },
                success: function(response) {
                    // alert('Notification deleted successfully');
                    // $('#notification-' + id).remove(); // Remove from DOM
                    window.location.reload();
                },
                error: function(xhr) {
                    console.error(xhr);
                    alert('Failed to delete notification');
                }
            });
        }

        function markNotificationAsRead(notificationId) {
            // Call the API to mark notification as read
            console.log('Marking notification as read:', notificationId);
            $.ajax({
                url: "{{ route('web.read_notifications') }}",
                type: 'GET',
                data: {
                    id: notificationId
                },
                success: function(response) {
                    // Update the notification count in the bell icon
                    updateNotificationCount();
                    window.location.reload();
                },
                error: function(xhr) {
                    console.error('Failed to mark notification as read:', xhr);
                }
            });
        }

        function updateNotificationCount() {
            // Get the current count from the bell icon
            const countElement = document.querySelector('.absolute.-top-3.-right-2');
            if (countElement) {
                const currentCount = parseInt(countElement.textContent);
                if (currentCount > 0) {
                    countElement.textContent = currentCount - 1;
                    // Hide the count badge if it reaches 0
                    if (currentCount - 1 === 0) {
                        countElement.style.display = 'none';
                    }
                }
            }
        }
    </script>
    @yield('script')
</body>

</html>
