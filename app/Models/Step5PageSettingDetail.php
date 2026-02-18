<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\HasLanguageFallback;

class Step5PageSettingDetail extends Model
{
    use HasFactory, HasLanguageFallback;

    public $table = "step5_page_setting_detail";
    protected $guarded = [];

    public function step5PageSetting(): BelongsTo
    {
        return $this->belongsTo(Step5PageSetting::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

}