<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BillingAddressSetting extends Model
{
    use HasFactory;
    public $table = "billing_address_setting";

    protected $guarded = [];

    public function billingAddressSettingDetail(): HasMany
    {
        return $this->hasMany(BillingAddressSettingDetail::class,'billing_add_setting_id' ,'id');
    }
}
