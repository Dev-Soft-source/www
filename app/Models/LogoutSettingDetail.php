<?php

namespace App\Models;

use App\Models\Language;
use App\Models\LogoutSetting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LogoutSettingDetail extends Model
{
    use HasFactory;
    public $table = "logout_setting_detail";
    protected $guarded = [];

    public function logoutSetting(): BelongsTo
    {
        return $this->belongsTo(LogoutSetting::class,'logout_setting_id' ,'id');
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
