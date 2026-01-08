<?php

namespace App\Models;

use App\Models\LogoutSettingDetail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LogoutSetting extends Model
{
    use HasFactory;
    public $table = "logout_setting";

    protected $guarded = [];

    public function logoutSettingDetail(): HasMany
    {
        return $this->hasMany(LogoutSettingDetail::class ,'logout_setting_id' ,'id');
    }
}
