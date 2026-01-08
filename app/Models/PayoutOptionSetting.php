<?php

namespace App\Models;

use App\Models\PayoutOptionSettingDetail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PayoutOptionSetting extends Model
{
    use HasFactory;
    public $table = "payout_option_setting";

    protected $guarded = [];

    public function payoutOptionSettingDetail(): HasMany
    {
        return $this->hasMany(PayoutOptionSettingDetail::class ,'payout_opt_setting_id' ,'id');
    }
}
