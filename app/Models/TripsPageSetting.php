<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TripsPageSetting extends Model
{
    use HasFactory;

    public $table = "trips_page_setting";

    protected $guarded = [];

    public function tripsPageSettingDetail(): HasMany
    {
        return $this->hasMany(TripsPageSettingDetail::class);
    }
}
