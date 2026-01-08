<?php

namespace App\Models;

use App\Models\MyWalletSettingDetail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MyWalletSetting extends Model
{
    use HasFactory;
    public $table = "my_wallet_setting";

    protected $guarded = [];

    public function myWalletSettingDetail(): HasMany
    {
        return $this->hasMany(MyWalletSettingDetail::class,'wallet_setting_id' , 'id');
    }
}
