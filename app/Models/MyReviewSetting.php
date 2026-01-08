<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MyReviewSetting extends Model
{
    use HasFactory;
    public $table = "my_review_setting";

    protected $guarded = [];

    public function reviewSettingDetail(): HasMany
    {
        return $this->hasMany(MyReviewSettingDetail::class,'my_review_setting_id' ,'id');
    }
}
