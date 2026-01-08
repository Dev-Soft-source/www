<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BookingPageSetting extends Model
{
    use HasFactory;

    public $table = "booking_page_setting";

    protected $guarded = [];

    public function bookingPageSettingDetail(): HasMany
    {
        return $this->hasMany(BookingPageSettingDetail::class,'booking_page_setting_id');
    }
}
