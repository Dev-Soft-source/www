<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\HasLanguageFallback;

class RefundPolicyPageSettingDetail extends Model
{
    use HasFactory, HasLanguageFallback;

    public $table = "refund_policy_page_setting_detail";
    protected $guarded = [];

    public function refundPolicyPageSetting(): BelongsTo
    {
        return $this->belongsTo(RefundPolicyPageSetting::class,'refund_policy_page_id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

}
