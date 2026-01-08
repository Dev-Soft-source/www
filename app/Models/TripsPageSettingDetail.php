<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TripsPageSettingDetail extends Model
{
    use HasFactory;

    public $table = "trips_page_setting_detail";
    protected $guarded = [];

    public function tripsPageSetting(): BelongsTo
    {
        return $this->belongsTo(TripsPageSetting::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
