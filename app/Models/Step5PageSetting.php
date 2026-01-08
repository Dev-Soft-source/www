<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Step5PageSetting extends Model
{
    use HasFactory;

    public $table = "step5_page_setting";

    protected $guarded = [];

    public function step5PageSettingDetail(): HasMany
    {
        return $this->hasMany(Step5PageSettingDetail::class);
    }
}