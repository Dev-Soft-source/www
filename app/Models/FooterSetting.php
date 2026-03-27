<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class FooterSetting extends Model
{
    use HasFactory;
    private const CACHE_KEY = 'settings:footer:first';

    protected $fillable = [
        'facebook_icon',
        'insta_icon',
        'youtube_icon',
        'twitter_icon',
        'menu1',
        'menu2',
        'menu3',
        'menu4',
    ];

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget(self::CACHE_KEY));
        static::deleted(fn () => Cache::forget(self::CACHE_KEY));
    }

    public static function getCached(): ?self
    {
        return Cache::rememberForever(self::CACHE_KEY, fn () => static::query()->first());
    }

    public function menu1Relation()
    {
        return $this->belongsTo(Menu::class, 'menu1');
    }

    public function menu2Relation()
    {
        return $this->belongsTo(Menu::class, 'menu2');
    }

    public function menu3Relation()
    {
        return $this->belongsTo(Menu::class, 'menu3');
    }

    public function menu4Relation()
    {
        return $this->belongsTo(Menu::class, 'menu4');
    }
}
