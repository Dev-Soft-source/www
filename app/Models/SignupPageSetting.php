<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SignupPageSetting extends Model
{
    use HasFactory;

    public $table = "signup_page_setting";

    protected $guarded = [];

    public function signupPageSettingDetail(): HasMany
    {
        return $this->hasMany(SignupPageSettingDetail::class);
    }
}
