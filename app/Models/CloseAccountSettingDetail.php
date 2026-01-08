<?php

namespace App\Models;

use App\Models\Language;
use App\Models\CloseAccountSetting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CloseAccountSettingDetail extends Model
{
    use HasFactory;
    public $table = "close_my_account_setting_detail";
    protected $guarded = [];

    public function closeAccountSetting(): BelongsTo
    {
        return $this->belongsTo(CloseAccountSetting::class,'close_acc_setting_id','id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
