<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TermsOfUsePageSettingDetail extends Model
{
    use HasFactory;

    public $table = "terms_of_use_page_setting_detail";
    protected $guarded = [];

    public function termsOfUsePageSetting(): BelongsTo
    {
        return $this->belongsTo(TermsOfUsePageSetting::class,'terms_use_page_id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    public static function getByLanguageWithFallback($selectedLangId, $defaultLangId)
    {
        return self::whereIn('language_id', [$selectedLangId, $defaultLangId])
            ->orderByRaw("FIELD(language_id, ?, ?)", [$selectedLangId, $defaultLangId])
            ->first();
    }
}
