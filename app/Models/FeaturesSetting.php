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

    public function featuresSettingDetail(): HasMany
    {
        return $this->hasMany(FeaturesSettingDetail::class,'features_setting_id');
    }
    
}
