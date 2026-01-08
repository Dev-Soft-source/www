<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MyVehicleSettingDetail extends Model
{
    use HasFactory;
    public $table = "my_vehicle_setting_detail";
    protected $guarded = [];

    public function myVehicleSetting(): BelongsTo
    {
        return $this->belongsTo(MyVehicleSetting::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
