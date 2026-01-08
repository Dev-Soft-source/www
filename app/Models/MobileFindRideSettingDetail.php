<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MobileFindRideSettingDetail extends Model
{
    use HasFactory;
    public $table = "mobile_find_ride_setting_detail";
    protected $guarded = [];

    public function mobileFindRideSetting(): BelongsTo
    {
        return $this->belongsTo(MobileFindRideSetting::class, 'find_ride_setting_id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
