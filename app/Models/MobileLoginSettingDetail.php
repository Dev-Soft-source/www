<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MobileLoginSettingDetail extends Model
{
    use HasFactory;

    public $table = "mobile_login_setting_detail";
    protected $guarded = [];

    public function mobileLoginSetting(): BelongsTo
    {
        return $this->belongsTo(MobileLoginSetting::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
