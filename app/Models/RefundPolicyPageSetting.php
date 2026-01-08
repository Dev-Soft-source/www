<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RefundPolicyPageSetting extends Model
{
    use HasFactory;
    public $table = "refund_policy_page_setting";

    protected $guarded = [];

    public function refundPolicyPageSettingDetail(): HasMany
    {
        return $this->hasMany(RefundPolicyPageSettingDetail::class,'refund_policy_page_id');
    }
}
