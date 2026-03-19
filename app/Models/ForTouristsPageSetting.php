<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ForTouristsPageSetting extends Model
{
    use HasFactory;

    public $table = "for_tourists_page_setting";

    protected $guarded = [];

    public function forTouristsPageSettingDetail(): HasMany
    {
        return $this->hasMany('App\Models\ForTouristsPageSettingDetail', 'for_tourists_page_id');
    }
}
