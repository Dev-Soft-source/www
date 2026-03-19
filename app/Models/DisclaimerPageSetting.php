<?php

namespace App\Models;

use App\Models\DisclaimerPageSettingDetail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DisclaimerPageSetting extends Model
{
    use HasFactory;

    public $table = "disclaimer_page_setting";

    protected $guarded = [];

    public function disclaimerPageSettingDetail(): HasMany
    {
        return $this->hasMany(DisclaimerPageSettingDetail::class, 'disclaimer_page_id');
    }
}
