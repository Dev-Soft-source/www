<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TermsOfUsePageSetting extends Model
{
    use HasFactory;
    public $table = "terms_of_use_page_setting";

    protected $guarded = [];

    public function termsOfUsePageSettingDetail(): HasMany
    {
        return $this->hasMany(TermsOfUsePageSettingDetail::class,'terms_use_page_id');
    }
}
