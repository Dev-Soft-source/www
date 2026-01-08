<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrivacyPolicyPageSettingDetail extends Model
{
    use HasFactory;

    public $table = "privacy_policy_page_setting_detail";
    protected $guarded = [];

    public function privacyPolicyPageSetting(): BelongsTo
    {
        return $this->belongsTo(PrivacyPolicyPageSetting::class,'privacy_page_id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
