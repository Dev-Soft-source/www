<?php

namespace App\Models;

use App\Models\MyPhoneSetting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Traits\HasLanguageFallback;

class MyReviewSettingDetail extends Model
{
    use HasFactory, HasLanguageFallback;
    
    public $table = "my_review_setting_detail";
    protected $guarded = [];

    public function myReviewSetting(): BelongsTo
    {
        return $this->belongsTo(MyReviewSetting::class, 'my_review_setting_id','id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    public function getAlreadyReviewedLabelAttribute(): ?string
    {
        return $this->attributes['already_reviewed_label']
            ?? $this->attributes['already_reveiwed_label']
            ?? null;
    }

}
