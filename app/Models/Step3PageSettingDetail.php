<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\HasLanguageFallback;

class Step3PageSettingDetail extends Model
{
    use HasFactory, HasLanguageFallback;

    public $table = "step3_page_setting_detail";
    protected $guarded = [];

    public function step3PageSetting(): BelongsTo
    {
        return $this->belongsTo(Step3PageSetting::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

}
