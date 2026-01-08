<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Step1PageSettingDetail extends Model
{
    use HasFactory;

    public $table = "step1_page_setting_detail";
    protected $guarded = [];

    public function step1PageSetting(): BelongsTo
    {
        return $this->belongsTo(Step1PageSetting::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
