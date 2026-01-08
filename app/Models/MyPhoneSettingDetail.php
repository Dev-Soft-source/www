<?php

namespace App\Models;

use App\Models\Language;
use App\Models\MyPhoneSetting;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MyPhoneSettingDetail extends Model
{
    use HasFactory;
    public $table = "my_phone_no_setting_detail";
    protected $guarded = [];

    public function myPhoneSetting(): BelongsTo
    {
        return $this->belongsTo(MyPhoneSetting::class, 'phone_no_setting_id','id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
