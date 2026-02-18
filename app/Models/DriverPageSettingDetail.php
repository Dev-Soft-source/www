<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\HasLanguageFallback;

class DriverPageSettingDetail extends Model
{
    use HasFactory, HasLanguageFallback;

    public $table = "driver_page_setting_detail";
    protected $guarded = [];

    public function driverPageSetting(): BelongsTo
    {
        return $this->belongsTo(DriverPageSetting::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }


}
