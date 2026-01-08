<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReferralPageSetting extends Model
{
    use HasFactory;

    public $table = "referral_page_settings";

    protected $guarded = [];

    public function referralPageSettingDetail(): HasMany
    {
        return $this->hasMany(ReferralPageSettingDetail::class,'referral_page_setting_id');
    }
}
