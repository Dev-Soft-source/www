<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MobileForgotPasswordSetting extends Model
{
    use HasFactory;

    public $table = "mobile_forgot_password_setting";

    protected $guarded = [];

    public function mobileForgotPasswordSettingDetail(): HasMany
    {
        return $this->hasMany(MobileForgotPasswordSettingDetail::class,'forgot_page_id');
    }
}
