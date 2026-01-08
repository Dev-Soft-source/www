<?php

namespace App\Models;

use App\Models\PaymentSettingDetail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentSetting extends Model
{
    use HasFactory;
    public $table = "payment_option_setting";

    protected $guarded = [];

    public function paymentSettingDetail(): HasMany
    {
        return $this->hasMany(PaymentSettingDetail::class ,'payment_opt_setting_id' ,'id' );
    }
}
