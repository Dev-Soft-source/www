<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Service
    |--------------------------------------------------------------------------
    |
    | Specify which service you would like to use for your application.
    |
    | Available Services: 'ipapi', 'ipgeolocation', 'maxmind_database', 'maxmind_api', 'ipdata', 'ipfinder'
    |
    */

    'service' => 'ipapi',

    /*
    |--------------------------------------------------------------------------
    | Cache
    |--------------------------------------------------------------------------
    |
    | Specify how long the location should be cached in minutes.
    | Set to false to disable caching.
    |
    */

    'cache' => 'none',
    'cache_tags' => [],

    /*
    |--------------------------------------------------------------------------
    | Service Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for each service is listed below.
    |
    */

    'services' => [
        'ipapi' => [
            'class' => \Torann\GeoIP\Services\IPApi::class,
            'secure' => true,
            'key' => env('IPAPI_KEY'),
            'continent_path' => storage_path('app/continents.json'),
        ],

        'ipgeolocation' => [
            'class' => \Torann\GeoIP\Services\IPGeolocation::class,
            'key' => env('IPGEOLOCATION_KEY'),
        ],

        'maxmind_database' => [
            'class' => \Torann\GeoIP\Services\MaxMindDatabase::class,
            'database_path' => storage_path('app/geoip.mmdb'),
            'license_key' => env('MAXMIND_LICENSE_KEY'),
            'update_url' => 'https://download.maxmind.com/app/geoip_download?edition_id=GeoLite2-City&license_key={LICENSE_KEY}&suffix=tar.gz',
        ],

        'maxmind_api' => [
            'class' => \Torann\GeoIP\Services\MaxMindWebService::class,
            'user_id' => env('MAXMIND_USER_ID'),
            'license_key' => env('MAXMIND_LICENSE_KEY'),
        ],

        'ipdata' => [
            'class' => \Torann\GeoIP\Services\IPData::class,
            'key' => env('IPDATA_KEY'),
        ],

        'ipfinder' => [
            'class' => \Torann\GeoIP\Services\IPFinder::class,
            'key' => env('IPFINDER_KEY'),
        ],
    ],
];
