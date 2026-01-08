<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MobileResetPasswordSetting extends Model
{
    use HasFactory;

    public $table = "mobile_reset_password_setting";

    protected $guarded = [];

    public function mobileResetPasswordSettingDetail(): HasMany
    {
        return $this->hasMany(MobileResetPasswordSettingDetail::class,'reset_page_id');
    }
}
