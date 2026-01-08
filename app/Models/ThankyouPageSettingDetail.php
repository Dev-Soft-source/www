<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ThankyouPageSettingDetail extends Model
{
    use HasFactory;

    public $table = "thankyou_page_setting_detail";
    protected $guarded = [];

    public function thankyouPageSetting(): BelongsTo
    {
        return $this->belongsTo(ThankyouPageSetting::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
