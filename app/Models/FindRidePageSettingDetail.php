<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FindRidePageSettingDetail extends Model
{
    use HasFactory;

    public $table = "find_ride_page_setting_detail";
    protected $guarded = [];

    public function findRidePageSetting(): BelongsTo
    {
        return $this->belongsTo(FindRidePageSetting::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
