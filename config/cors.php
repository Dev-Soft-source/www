<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => [
        'api/*', 
        'sanctum/csrf-cookie',
        'flag_icons*',  // Allow CORS for flag icons (matches flag_icons/ and flag_icons/*)
        'home_page_icons*',  // Allow CORS for home page icons
        'storage*',  // Allow CORS for storage files
        '*',  // Allow CORS for all paths (for development - restrict in production)
    ],

    'allowed_methods' => ['*'],

    'allowed_origins' => array_filter(explode(',', env('CORS_ALLOWED_ORIGINS', sprintf(
        '%s%s',
        'http://localhost:8000,http://127.0.0.1:8000,http://localhost:3000,http://127.0.0.1:3000,http://localhost:5173,http://127.0.0.1:5173,http://localhost:8080,http://127.0.0.1:8080',
        env('APP_URL') ? ','.parse_url(env('APP_URL'), PHP_URL_SCHEME).'://'.parse_url(env('APP_URL'), PHP_URL_HOST) : ''
    )))),

    'allowed_origins_patterns' => [
        '#^http://localhost:\d+$#',  // Allow any localhost port (for Flutter web development)
        '#^http://127\.0\.0\.1:\d+$#', // Allow any 127.0.0.1 port (for Flutter web development)
    ],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
