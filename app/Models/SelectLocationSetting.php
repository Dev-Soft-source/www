<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SelectLocationSetting extends Model
{
    use HasFactory;

    public $table = "select_location_setting";

    protected $guarded = [];

    public function selectLocationSettingDetail(): HasMany
    {
        return $this->hasMany(SelectLocationSettingDetail::class,'location_setting_id');
    }
}
