<?php

namespace App\Models;

use App\Models\Traits\HasLanguageFallback;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfilePhotoGuidelinesPageSettingDetail extends Model
{
    use HasFactory, HasLanguageFallback;

    public $table = "profile_photo_guidelines_page_setting_detail";

    protected $guarded = [];

    public function profilePhotoGuidelinesPageSetting(): BelongsTo
    {
        return $this->belongsTo(ProfilePhotoGuidelinesPageSetting::class, 'profile_photo_guidelines_page_id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
