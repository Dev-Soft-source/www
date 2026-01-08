<?php

namespace App\Models;

use App\Models\ProfilePhotoSettingDetail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProfilePhotoSetting extends Model
{
    use HasFactory;
    public $table = "profile_photo_setting";

    protected $guarded = [];

    public function profilePhotoSettingDetail(): HasMany
    {
        return $this->hasMany(ProfilePhotoSettingDetail::class);
    }
}
