<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReferralPageSettingDetail extends Model
{
    use HasFactory;

    public $table = "referral_page_setting_details";
    protected $guarded = [];

    public function referralPageSetting(): BelongsTo
    {
        return $this->belongsTo(ReferralPageSetting::class,'referral_page_setting_id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
