<?php

namespace App\Models;

use App\Models\Concerns\HasOptionGroups;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FindRidePageSettingDetail extends Model
{
    use HasFactory, HasOptionGroups;

    public $table = "find_ride_page_setting_detail";
    protected $guarded = [];

    public function findRidePageSetting(): BelongsTo
    {
        return $this->belongsTo(FindRidePageSetting::class);
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
