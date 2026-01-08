<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ThankyouPageSetting extends Model
{
    use HasFactory;

    public $table = "thankyou_page_setting";

    protected $guarded = [];

    public function thankyouPageSettingDetail(): HasMany
    {
        return $this->hasMany(ThankyouPageSettingDetail::class);
    }
}
