<?php

namespace App\Models;

use App\Models\Traits\HasLanguageFallback;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DisclaimerPageSettingDetail extends Model
{
    use HasFactory, HasLanguageFallback;

    public $table = "disclaimer_page_setting_detail";

    protected $guarded = [];

    public function disclaimerPageSetting(): BelongsTo
    {
        return $this->belongsTo(DisclaimerPageSetting::class, 'disclaimer_page_id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
