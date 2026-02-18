<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\HasLanguageFallback;

class ContactUsPageSettingDetail extends Model
{
    use HasFactory, HasLanguageFallback;

    public $table = "contact_us_page_setting_detail";
    protected $guarded = [];

    public function contactUsPageSetting(): BelongsTo
    {
        return $this->belongsTo(ContactUsPageSetting::class,'contact_page_setting_id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

}
