<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingPageSettingDetail extends Model
{
    use HasFactory;

    public $table = "booking_page_setting_detail";
    protected $guarded = [];

    public function bookingPageSetting(): BelongsTo
    {
        return $this->belongsTo(BookingPageSetting::class,'booking_page_setting_id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
