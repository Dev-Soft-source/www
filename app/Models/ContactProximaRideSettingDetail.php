<?php

namespace App\Models;

use App\Models\Language;
use Illuminate\Database\Eloquent\Model;
use App\Models\ContactProximaRideSetting;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ContactProximaRideSettingDetail extends Model
{
    use HasFactory;
    public $table = "contact_proximaride_setting_detail";
    protected $guarded = [];

    public function contactProximaRideSetting(): BelongsTo
    {
        return $this->belongsTo(ContactProximaRideSetting::class ,'contact_pr_setting_id' ,'id');
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
