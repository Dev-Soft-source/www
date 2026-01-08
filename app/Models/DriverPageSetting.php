<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DriverPageSetting extends Model
{
    use HasFactory;

    public $table = "driver_page_setting";

    protected $guarded = [];

    public function driverPageSettingDetail(): HasMany
    {
        return $this->hasMany(DriverPageSettingDetail::class);
    }
}
