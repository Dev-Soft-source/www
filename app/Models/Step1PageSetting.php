<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Step1PageSetting extends Model
{
    use HasFactory;

    public $table = "step1_page_setting";

    protected $guarded = [];

    public function step1PageSettingDetail(): HasMany
    {
        return $this->hasMany(Step1PageSettingDetail::class);
    }
}
