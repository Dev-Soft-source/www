<?php

namespace App\Models;

use App\Models\Language;
use App\Models\PaymentSetting;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentSettingDetail extends Model
{
    use HasFactory;
    public $table = "payment_option_setting_detail";
    protected $guarded = [];

    public function paymentSetting(): BelongsTo
    {
        return $this->belongsTo(PaymentSetting::class ,'payment_opt_setting_id' ,'id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
