<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TermsAndConditionPageSetting extends Model
{
    use HasFactory;

    public $table = "terms_and_condition_page_setting";

    protected $guarded = [];

    public function termsAndConditionPageSettingDetail(): HasMany
    {
        return $this->hasMany(TermsAndConditionPageSettingDetail::class,'terms_page_id');
    }
}
