<?php

namespace App\Models;

use App\Models\Concerns\HasOptionGroups;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\HasLanguageFallback;

class PostRidePageSettingDetail extends Model
{
    use HasFactory, HasOptionGroups, HasLanguageFallback;

    public $table = "post_ride_page_setting_detail";
    protected $guarded = [];

    public function postRidePageSetting(): BelongsTo
    {
        return $this->belongsTo(PostRidePageSetting::class,'post_ride_page_setting_id' , 'id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    public function getFeatureOptionsListAttribute(): array
    {
        $features = [];

        for ($i = 1; $i <= 20; $i++) {
            $optionKey = 'features_option' . $i;

            if (!isset($this->{$optionKey})) {
                continue;
            }

            $option = $this->{$optionKey};

            $features[] = [
                'key' => $optionKey,
                'id' => $option->features_setting_id ?? null,
                'name' => $option->name ?? $option->label ?? null,
                'label' => $option->label ?? $option->name ?? null,
                'icon' => $option->icon ?? null,
                'tooltip' => $option->tooltip ?? $this->{$optionKey . '_tooltip'} ?? null,
            ];
        }

        return $features;
    }
}
