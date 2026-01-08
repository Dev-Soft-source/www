<?php

namespace App\Models;

use App\Models\DriverSettingDetail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DriverSetting extends Model
{
    use HasFactory;
    public $table = "my_driver_license_setting";

    protected $guarded = [];

    public function driverSettingDetail(): HasMany
    {
        return $this->hasMany(DriverSettingDetail::class , 'driver_lic_setting_id' ,'id');
    }
}
