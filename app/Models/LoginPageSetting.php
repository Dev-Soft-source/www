<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LoginPageSetting extends Model
{
    use HasFactory;

    public $table = "login_page_setting";

    protected $guarded = [];

    public function loginPageSettingDetail(): HasMany
    {
        return $this->hasMany(LoginPageSettingDetail::class);
    }
}
