<?php

namespace App\Models;

use App\Models\Traits\HasLanguageFallback;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ForTouristsPageSettingDetail extends Model
{
    use HasFactory, HasLanguageFallback;

    public $table = "for_tourists_page_setting_detail";

    protected $guarded = [];

    public function forTouristsPageSetting(): BelongsTo
    {
        return $this->belongsTo(ForTouristsPageSetting::class, 'for_tourists_page_id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
