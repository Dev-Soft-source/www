<?php

namespace App\Models;

use App\Models\MyEmailSettingDetail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MyEmailSetting extends Model
{
    use HasFactory;
    public $table = "my_email_address_setting";

    protected $guarded = [];

    public function myEmailSettingDetail(): HasMany
    {
        return $this->hasMany(MyEmailSettingDetail::class,'email_address_setting_id' ,'id');
    }
}
