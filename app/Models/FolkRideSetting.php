<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class FolkRideSetting extends Model
{
    use HasFactory;
    public $timestamps = false;
    private const CACHE_KEY = 'settings:folk-ride:first';

    protected $fillable = [
        'id',
        'average_rating',
        'driver_age',
        'verfiy_phone',
        'verify_email',
        'driver_license',
        'extra_rides_trip_limit',
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

    public function requiresVerifiedPhone(): bool
    {
        return (string) $this->verfiy_phone === '1';
    }

    public function requiresVerifiedEmail(): bool
    {
        return (string) $this->verify_email === '1';
    }

    public function requiresDriverLicense(): bool
    {
        return (string) $this->driver_license === '1';
    }
}
