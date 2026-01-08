<?php

namespace App\Models;

use App\Models\Language;
use App\Models\ProfilePhotoSetting;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfilePhotoSettingDetail extends Model
{
    use HasFactory;
    public $table = "profile_photo_setting_detail";
    protected $guarded = [];

    public function profilePhotoSetting(): BelongsTo
    {
        return $this->belongsTo(ProfilePhotoSetting::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
