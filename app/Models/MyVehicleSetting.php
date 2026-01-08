<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MyVehicleSetting extends Model
{
    use HasFactory;
    public $table = "my_vehicle_setting";

    protected $guarded = [];

    public function myVehicleSettingDetail(): HasMany
    {
        return $this->hasMany(MyVehicleSettingDetail::class);
    }
}
