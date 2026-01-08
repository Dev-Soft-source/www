<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MobilePostRideSettingDetail extends Model
{
    use HasFactory;
    public $table = "mobile_post_ride_setting_detail";
    protected $guarded = [];

    public function mobilePostRideSetting(): BelongsTo
    {
        return $this->belongsTo(MobilePostRideSetting::class, 'post_ride_setting_id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
