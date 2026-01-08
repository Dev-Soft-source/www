<?php

namespace App\Models;

use App\Models\Language;
use App\Models\MyEmailSetting;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MyEmailSettingDetail extends Model
{
    use HasFactory;
    public $table = "my_email_address_setting_detail";
    protected $guarded = [];

    public function myEmailSetting(): BelongsTo
    {
        return $this->belongsTo(MyEmailSetting::class,'email_address_setting_id' ,'id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
