<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MobileFindRideSetting extends Model
{
    use HasFactory;
    public $table = "mobile_find_ride_setting";

    protected $guarded = [];

    public function mobileFindRideSettingDetail(): HasMany
    {
        return $this->hasMany(MobileFindRideSettingDetail::class, 'find_ride_setting_id');
    }
}
