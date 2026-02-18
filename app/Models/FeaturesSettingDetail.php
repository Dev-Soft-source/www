<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\HasLanguageFallback;

class FeaturesSettingDetail extends Model
{
    use HasFactory, HasLanguageFallback;

    public $table = "features_setting_detail";
    protected $guarded = [];

    /**
     * Appended attributes: default_name and default_icon from the row where language_id = 1
     * (same features_setting_id). Use for empty/null fallback.
     */
    protected $appends = ['default_name', 'default_icon'];

    public function featuresSetting(): BelongsTo
    {
        return $this->belongsTo(FeaturesSetting::class,'features_setting_id');
    }
    

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }


    /** @var self|null Cached default-language detail (language_id = 1) for this instance */
    protected ?self $_defaultLanguageDetail = null;

    /**
     * Get the detail row for default language (language_id = 1), same feature. Cached per instance.
     */
    protected function getDefaultLanguageDetail(): ?self
    {
        if ($this->_defaultLanguageDetail === null && $this->features_setting_id !== null) {
            $this->_defaultLanguageDetail = $this->language_id === 1
                ? $this
                : static::query()
                    ->where('features_setting_id', $this->features_setting_id)
                    ->where('language_id', 1)
                    ->first();
        }

        return $this->_defaultLanguageDetail;
    }

    /**
     * Name from the default language (language_id = 1). Use for null/empty fallback.
     */
    public function getDefaultNameAttribute(): ?string
    {
        $default = $this->getDefaultLanguageDetail();

        return $default?->name;
    }

    /**
     * Icon from the default language (language_id = 1). Use for null/empty fallback.
     */
    public function getDefaultIconAttribute(): ?string
    {
        $default = $this->getDefaultLanguageDetail();

        return $default?->icon;
    }

    /**
     * Name with fallback to default (language_id = 1) when null or empty.
     */
    public function getDisplayNameAttribute(): ?string
    {
        $name = $this->name ?? $this->default_name;

        return $name !== '' && $name !== null ? $name : $this->default_name;
    }

    /**
     * Icon with fallback to default (language_id = 1) when null or empty.
     */
    public function getDisplayIconAttribute(): ?string
    {
        $icon = $this->icon ?? $this->default_icon;

        return $icon !== '' && $icon !== null ? $icon : $this->default_icon;
    }
}
