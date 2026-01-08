<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MobileLoginSetting extends Model
{
    use HasFactory;

    public $table = "mobile_login_setting";

    protected $guarded = [];

    public function mobileLoginSettingDetail(): HasMany
    {
        return $this->hasMany(MobileLoginSettingDetail::class);
    }
}
