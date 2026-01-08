<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BillingAddressSettingDetail extends Model
{
    use HasFactory;
    public $table = "billing_address_setting_detail";
    protected $guarded = [];

    public function billingAddressSetting(): BelongsTo
    {
        return $this->belongsTo(BillingAddressSetting::class,'billing_add_setting_id' ,'id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
