<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EditProfilePageSettingDetail extends Model
{
    use HasFactory;
    public $table = "edit_profile_page_setting_detail";
    protected $guarded = [];

    public function editProfilePageSetting(): BelongsTo
    {
        return $this->belongsTo(EditProfilePageSetting::class ,'edit_profile_id' ,'id');
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
