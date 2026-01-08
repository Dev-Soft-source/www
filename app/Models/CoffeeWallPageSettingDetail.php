<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CoffeeWallPageSettingDetail extends Model
{
    use HasFactory;
    public $table = "coffee_wall_setting_detail";
    protected $guarded = [];

    public function coffeeWallPageSetting(): BelongsTo
    {
        return $this->belongsTo(CoffeeWallPageSetting::class,'coffee_wall_setting_id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
