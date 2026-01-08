<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CloseAccountSettingDetail;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CloseAccountSetting extends Model
{
    use HasFactory;
    public $table = "close_my_account_setting";

    protected $guarded = [];

    public function closeAccountSettingDetail(): HasMany
    {
        return $this->hasMany(CloseAccountSettingDetail::class ,'close_acc_setting_id','id');
    }
}
