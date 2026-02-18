<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\HasLanguageFallback;

class TermsAndConditionPageSettingDetail extends Model
{
    use HasFactory, HasLanguageFallback;

    public $table = "terms_and_condition_page_setting_detail";
    protected $guarded = [];

    public function termsAndConditionPageSetting(): BelongsTo
    {
        return $this->belongsTo(TermsAndConditionPageSetting::class,'terms_page_id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

}
