<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProfilePhotoGuidelinesPageSetting extends Model
{
    use HasFactory;

    public $table = "profile_photo_guidelines_page_setting";

    protected $guarded = [];

    public function profilePhotoGuidelinesPageSettingDetail(): HasMany
    {
        return $this->hasMany(ProfilePhotoGuidelinesPageSettingDetail::class, 'profile_photo_guidelines_page_id');
    }
}
