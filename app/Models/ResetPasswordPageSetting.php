<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ResetPasswordPageSetting extends Model
{
    use HasFactory;

    public $table = "reset_password_page_setting";

    protected $guarded = [];

    public function resetPasswordPageSettingDetail(): HasMany
    {
        return $this->hasMany(ResetPasswordPageSettingDetail::class,'reset_pass_page_id');
    }
}
