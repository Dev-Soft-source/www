<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PostRidePageSetting extends Model
{
    use HasFactory;

    public $table = "post_ride_page_setting";

    protected $guarded = [];

    public function postRidePageSettingDetail(): HasMany
    {
        return $this->hasMany(PostRidePageSettingDetail::class ,'post_ride_page_setting_id' , 'id');
    }
     public function postRidePageSettingSubDetail(): HasMany
    {
        return $this->hasMany(PostRidePageSettingSubDetail::class ,'post_ride_page_id' , 'id');
    }
}
