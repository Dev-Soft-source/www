<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class ReviewSetting extends Model
{
    use HasFactory;
    public $timestamps = false;
    private const CACHE_KEY = 'settings:review:first';

    protected $fillable = [
        'id',
        'leave_review_days',
        'respond_review_days',
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
}
