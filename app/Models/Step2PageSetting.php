<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Step2PageSetting extends Model
{
    use HasFactory;

    public $table = "step2_page_setting";

    protected $guarded = [];

    public function step2PageSettingDetail(): HasMany
    {
        return $this->hasMany(Step2PageSettingDetail::class);
    }
}
