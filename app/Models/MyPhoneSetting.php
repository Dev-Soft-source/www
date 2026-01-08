<?php

namespace App\Models;

use App\Models\MyPhoneSettingDetail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MyPhoneSetting extends Model
{
    use HasFactory;
    public $table = "my_phone_no_setting";

    protected $guarded = [];

    public function myPhoneSettingDetail(): HasMany
    {
        return $this->hasMany(MyPhoneSettingDetail::class ,'phone_no_setting_id','id');
    }
}
