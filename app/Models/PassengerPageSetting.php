<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PassengerPageSetting extends Model
{
    use HasFactory;

    public $table = "passenger_page_setting";

    protected $guarded = [];

    public function passengerPageSettingDetail(): HasMany
    {
        return $this->hasMany(PassengerPageSettingDetail::class);
    }
}
