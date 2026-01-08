<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ForgotPasswordPageSetting extends Model
{
    use HasFactory;

    public $table = "forgot_password_page_setting";

    protected $guarded = [];

    public function forgotPasswordPageSettingDetail(): HasMany
    {
        return $this->hasMany(ForgotPasswordPageSettingDetail::class,'forgot_pass_page_id');
    }
}
