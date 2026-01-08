<?php

namespace App\Models;

use App\Models\Language;
use App\Models\PayoutOptionSetting;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayoutOptionSettingDetail extends Model
{
    use HasFactory;
    public $table = "payout_option_setting_detail";
    protected $guarded = [];

    public function payoutOptionSetting(): BelongsTo
    {
        return $this->belongsTo(PayoutOptionSetting::class ,'payout_opt_setting_id' ,'id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
