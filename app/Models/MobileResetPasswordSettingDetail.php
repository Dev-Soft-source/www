<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MobileResetPasswordSettingDetail extends Model
{
    use HasFactory;

    public $table = "mobile_reset_password_setting_detail";
    protected $guarded = [];

    public function mobileResetPasswordSetting(): BelongsTo
    {
        return $this->belongsTo(MobileResetPasswordSetting::class,'reset_page_id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
