<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Language extends Model
{
    use HasFactory;
    private const ALL_CACHE_KEY = 'settings:languages:all';

    protected $fillable = [
        'name',
        'abbreviation',
        'native_name',
        'is_default',
        'direction',
        'flag_icon',
    ];

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget(self::ALL_CACHE_KEY));
        static::deleted(fn () => Cache::forget(self::ALL_CACHE_KEY));
    }

    public static function getAllCached()
    {
        return Cache::rememberForever(self::ALL_CACHE_KEY, fn () => static::all());
    }


    public function getFlagIconAttribute($value)
    {
        // You can perform any transformation you need here
        if ($value) {
            // For example, prepend the base URL to the image path
            return rtrim(config('app.url'), '/') . '/flag_icons/' . $value;
        }

        return null;
    }

    public static function resolveLanguage($abbr = null)
    {
        return self::when($abbr, function ($query) use ($abbr) {
            $query->where('abbreviation', $abbr);
        })
            ->first()
            ?? self::where('is_default', 1)->first();
    }
    
    public static function resolveLanguageByID($langID = null)
    {
        return self::when($langID, function ($query) use ($langID) {
            $query->where('id', $langID);
        })
            ->first()
            ?? self::where('is_default', 1)->first();
    }
}
