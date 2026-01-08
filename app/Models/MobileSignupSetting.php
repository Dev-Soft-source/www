<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MobileSignupSetting extends Model
{
    use HasFactory;

    public $table = "mobile_signup_setting";

    protected $guarded = [];

    public function mobileSignupSettingDetail(): HasMany
    {
        return $this->hasMany(MobileSignupSettingDetail::class);
    }
}
