<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

class LocationCache
{
    private const VERSION_CACHE_KEY = 'location-cache:version';

    public static function key(string $suffix): string
    {
        $version = Cache::rememberForever(self::VERSION_CACHE_KEY, fn () => '1');

        return 'location:v' . $version . ':' . $suffix;
    }

    public static function bust(): void
    {
        Cache::forever(self::VERSION_CACHE_KEY, uniqid('', true));
    }
}
