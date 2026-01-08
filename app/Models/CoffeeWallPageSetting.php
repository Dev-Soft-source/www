<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CoffeeWallPageSetting extends Model
{
    use HasFactory;
    public $table = "coffee_wall_setting";

    protected $guarded = [];

    public function coffeeWallPageSettingDetail(): HasMany
    {
        return $this->hasMany(CoffeeWallPageSettingDetail::class,'coffee_wall_setting_id');
    }
}
