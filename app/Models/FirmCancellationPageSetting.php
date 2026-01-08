<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FirmCancellationPageSetting extends Model
{
    use HasFactory;
    public $table = "firm_cancellation_page_setting";

    protected $guarded = [];

    public function CancellationPageSettingDetail(): HasMany
    {
        return $this->hasMany(FirmCancellationPageSettingDetail::class,'firm_cancellation_id');
    }
}
