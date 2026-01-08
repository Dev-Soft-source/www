<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PrivacyPolicyPageSetting extends Model
{
    use HasFactory;

    public $table = "privacy_policy_page_setting";

    protected $guarded = [];

    public function privacyPolicyPageSettingDetail(): HasMany
    {
        return $this->hasMany(PrivacyPolicyPageSettingDetail::class,'privacy_page_id');
    }
}
