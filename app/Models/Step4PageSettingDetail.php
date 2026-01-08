<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Step4PageSettingDetail extends Model
{
    use HasFactory;

    public $table = "step4_page_setting_detail";
    protected $guarded = [];

    public function step4PageSetting(): BelongsTo
    {
        return $this->belongsTo(Step4PageSetting::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
