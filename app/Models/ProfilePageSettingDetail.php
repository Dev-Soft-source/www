<?php

namespace App\Models;

use App\Models\Language;
use App\Models\ProfilePageSetting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProfilePageSettingDetail extends Model
{
    use HasFactory;
    public $table = "profile_page_setting_detail";
    protected $guarded = [];

    public function profilePageSetting(): BelongsTo
    {
        return $this->belongsTo(ProfilePageSetting::class);
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
