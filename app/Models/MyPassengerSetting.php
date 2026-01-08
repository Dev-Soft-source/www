<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MyPassengerSetting extends Model
{
    use HasFactory;
    public $table = "my_passenger_setting";

    protected $guarded = [];

    public function myPassengerSettingDetail(): HasMany
    {
        return $this->hasMany(MyPassengerSettingDetail::class,'my_passenger_setting_id' ,'id');
    }
}
