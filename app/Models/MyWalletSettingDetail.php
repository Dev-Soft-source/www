<?php

namespace App\Models;

use App\Models\Language;
use App\Models\MyWalletSetting;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MyWalletSettingDetail extends Model
{
    use HasFactory;
    public $table = "my_wallet_setting_detail";
    protected $guarded = [];

    public function myWalletSetting(): BelongsTo
    {
        return $this->belongsTo(MyWalletSetting::class ,'wallet_setting_id' , 'id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
