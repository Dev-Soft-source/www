<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MyPassengerSettingDetail extends Model
{
    use HasFactory;
    public $table = "my_passenger_setting_detail";
    protected $guarded = [];

    public function myPassengerSetting(): BelongsTo
    {
        return $this->belongsTo(MyPassengerSetting::class,'my_passenger_setting_id' ,'id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
