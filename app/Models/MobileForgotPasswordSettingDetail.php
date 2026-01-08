<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MobileForgotPasswordSettingDetail extends Model
{
    use HasFactory;

    public $table = "mobile_forgot_password_setting_detail";
    protected $guarded = [];

    public function mobileForgotPasswordSetting(): BelongsTo
    {
        return $this->belongsTo(MobileForgotPasswordSetting::class,'forgot_page_id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
