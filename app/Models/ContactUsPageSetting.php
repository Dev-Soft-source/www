<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContactUsPageSetting extends Model
{
    use HasFactory;

    public $table = "contact_us_page_setting";

    protected $guarded = [];

    public function contactUsPageSettingDetail(): HasMany
    {
        return $this->hasMany(ContactUsPageSettingDetail::class,'contact_page_setting_id');
    }
}
