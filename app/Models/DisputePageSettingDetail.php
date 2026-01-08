<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DisputePageSettingDetail extends Model
{
    use HasFactory;
    public $table = "dispute_page_setting_detail";
    protected $guarded = [];

    public function disputePageSetting(): BelongsTo
    {
        return $this->belongsTo(DisputePageSetting::class,'dispute_page_id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
