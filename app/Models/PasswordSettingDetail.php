<?php

namespace App\Models;

use App\Models\Language;
use App\Models\PasswordSetting;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PasswordSettingDetail extends Model
{
    use HasFactory;
    public $table = "password_setting_detail";
    protected $guarded = [];

    public function passwordSetting(): BelongsTo
    {
        return $this->belongsTo(PasswordSetting::class);
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
