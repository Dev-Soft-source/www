<?php

namespace App\Models;

use App\Models\DriverSetting;
use App\Models\Language;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DriverSettingDetail extends Model
{
    use HasFactory;
    public $table = "my_driver_license_setting_detail";
    protected $guarded = [];

    public function driverSetting(): BelongsTo
    {
        return $this->belongsTo(DriverSetting::class , 'driver_lic_setting_id' ,'id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
