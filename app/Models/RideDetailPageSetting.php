<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RideDetailPageSetting extends Model
{
    use HasFactory;

    public $table = "ride_detail_page_setting";

    protected $guarded = [];

    public function rideDetailPageSettingDetail(): HasMany
    {
        return $this->hasMany(RideDetailPageSettingDetail::class,'ride_detail_page_id');
    }
}
