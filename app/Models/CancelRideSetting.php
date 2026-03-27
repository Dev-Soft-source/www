<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class CancelRideSetting extends Model
{
    use HasFactory;
    public $timestamps = false;
    private const CACHE_KEY = 'settings:cancel-ride:first';

    protected $fillable = [
        'id',
        'driver_cancel_hours',
        'passenger_cancel_hours',
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
