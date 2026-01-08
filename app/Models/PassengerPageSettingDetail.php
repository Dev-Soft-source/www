<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PassengerPageSettingDetail extends Model
{
    use HasFactory;

    public $table = "passenger_page_setting_detail";
    protected $guarded = [];

    public function passengerPageSetting(): BelongsTo
    {
        return $this->belongsTo(PassengerPageSetting::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
