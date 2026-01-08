<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MobilePostRideSetting extends Model
{
    use HasFactory;
    public $table = "mobile_post_ride_setting";

    protected $guarded = [];

    public function mobilePostRideSettingDetail(): HasMany
    {
        return $this->hasMany(MobilePostRideSettingDetail::class, 'post_ride_setting_id');
    }
}
