<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Step2PageSettingDetail extends Model
{
    use HasFactory;

    public $table = "step2_page_setting_detail";
    protected $guarded = [];

    public function step2PageSetting(): BelongsTo
    {
        return $this->belongsTo(Step2PageSetting::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
