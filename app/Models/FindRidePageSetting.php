<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FindRidePageSetting extends Model
{
    use HasFactory;

    public $table = "find_ride_page_setting";

    protected $guarded = [];

    public function findRidePageSettingDetail(): HasMany
    {
        return $this->hasMany(FindRidePageSettingDetail::class);
    }
}
