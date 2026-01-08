<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SelectLocationSettingDetail extends Model
{
    use HasFactory;

    public $table = "select_location_setting_detail";
    protected $guarded = [];

    public function selectLocationSetting(): BelongsTo
    {
        return $this->belongsTo(SelectLocationSetting::class,'location_setting_id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
