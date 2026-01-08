<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeaturesSetting extends Model
{
    use HasFactory;

    public $table = "features_setting";

    protected $guarded = [];

    public function featuresSettingDetail(): HasMany
    {
        return $this->hasMany(FeaturesSettingDetail::class,'features_setting_id');
    }
}
