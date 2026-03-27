<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class ReferralSystemSetting extends Model
{
    use HasFactory;
    public $timestamps = false;
    private const CACHE_KEY = 'settings:referral-system:first';

    protected $fillable = [
        'id',
        'p_2_p_booking_credit',
        'p_2_s_booking_credit',
        'p_2_d_booking_credit',
        'd_2_p_reward_point',
        'd_2_s_reward_point',
        'd_2_d_rewad_point',
        's_2_p_reward_point',
        's_2_s_reward_point',
        's_2_d_reward_point',
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
