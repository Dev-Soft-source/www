<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeaturesSettingDetail extends Model
{
    use HasFactory;

    public $table = "features_setting_detail";
    protected $guarded = [];

    public function featuresSetting(): BelongsTo
    {
        return $this->belongsTo(FeaturesSetting::class,'features_setting_id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
