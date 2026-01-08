<?php

namespace App\Models;

use App\Models\ProfileSettingDetail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProfileSetting extends Model
{
    use HasFactory;
    public $table = "profile_setting";

    protected $guarded = [];

    public function profileSettingDetail(): HasMany
    {
        return $this->hasMany(ProfileSettingDetail::class);
    }
}
