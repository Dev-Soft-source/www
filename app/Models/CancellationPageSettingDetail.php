<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CancellationPageSettingDetail extends Model
{
    use HasFactory;
    public $table = "cancellation_page_setting_detail";
    protected $guarded = [];

    public function cancellationPageSetting(): BelongsTo
    {
        return $this->belongsTo(CancellationPageSetting::class,'cancellation_page_id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
