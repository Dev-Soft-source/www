<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FirmCancellationPageSettingDetail extends Model
{
    use HasFactory;
    public $table = "firm_cancellation_setting_detail";
    protected $guarded = [];

    public function CancellationPageSetting(): BelongsTo
    {
        return $this->belongsTo(FirmCancellationPageSetting::class,'firm_cancellation_id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
