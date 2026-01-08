<?php

namespace App\Models;

use App\Models\Language;
use App\Models\ProfileSetting;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfileSettingDetail extends Model
{
    use HasFactory;
    public $table = "profile_setting_detail";
    protected $guarded = [];

    public function profileSetting(): BelongsTo
    {
        return $this->belongsTo(ProfileSetting::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
