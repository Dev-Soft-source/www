<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RideDetailPageSettingDetail extends Model
{
    use HasFactory;

    public $table = "ride_detail_page_setting_detail";
    protected $guarded = [];

    public function rideDetailPageSetting(): BelongsTo
    {
        return $this->belongsTo(RideDetailPageSetting::class,'ride_detail_page_id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
