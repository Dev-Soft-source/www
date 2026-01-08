<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MobileSignupSettingDetail extends Model
{
    use HasFactory;

    public $table = "mobile_signup_setting_detail";
    protected $guarded = [];

    public function mobileSignupSetting(): BelongsTo
    {
        return $this->belongsTo(MobileSignupSetting::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
