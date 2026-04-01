<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class FeaturesSetting extends Model
{
    use HasFactory;
    private const OPTION_GROUPS_VERSION_KEY = 'features:option-groups:version';

    public $table = "features_setting";

    protected $guarded = [];

    protected static function booted(): void
    {
        static::saved(fn () => static::bustOptionGroupsCache());
        static::deleted(fn () => static::bustOptionGroupsCache());
    }

    public static function getOptionGroupsCacheVersion(): string
    {
        return (string) Cache::rememberForever(self::OPTION_GROUPS_VERSION_KEY, fn () => '1');
    }

    public static function bustOptionGroupsCache(): void
    {
        Cache::forever(self::OPTION_GROUPS_VERSION_KEY, uniqid('', true));
    }

    /**
     * Use after bulk/query-builder updates when we want to force fresh option-group caches.
     */
    public static function forgetOptionGroupsCache(): void
    {
        static::bustOptionGroupsCache();
    }

    public function featuresSettingDetail(): HasMany
    {
        return $this->hasMany(FeaturesSettingDetail::class,'features_setting_id');
    }

    /**
     * Admin / API grouping by features_setting.id (stable product IDs).
     *
     * @return list<array{key: string, title: string, ids: list<int>}>
     */
    public static function adminFeatureGroups(): array
    {
        return [
            [
                'key' => 'ride_features',
                'title' => "Ride's features",
                'ids' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 47],
            ],
            [
                'key' => 'ride_luggages',
                'title' => "Ride's luggages",
                'ids' => [26, 27, 28, 29, 30],
            ],
            [
                'key' => 'ride_smokings',
                'title' => "Ride's smokings",
                'ids' => [21, 22],
            ],
            [
                'key' => 'ride_pets',
                'title' => "Ride's pets",
                'ids' => [23, 24, 25],
            ],
            [
                'key' => 'ride_booking_methods',
                'title' => "Ride's booking methods",
                'ids' => [31, 32],
            ],
            [
                'key' => 'ride_payment_methods',
                'title' => "Ride's payment methods",
                'ids' => [33, 34, 35],
            ],
            [
                'key' => 'ride_vehicle_types',
                'title' => "Ride's vehicle types",
                'ids' => [38, 39, 40, 41, 42, 43, 44, 45, 46],
            ],
        ];
    }

    /** IDs used for the main "ride features" option group in APIs (excludes merged legacy slots 8, 9, 11). */
    public static function rideFeaturesSettingIds(): array
    {
        return self::adminFeatureGroups()[0]['ids'];
    }
}
