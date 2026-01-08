<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DisputePageSetting extends Model
{
    use HasFactory;
    public $table = "dispute_page_setting";

    protected $guarded = [];

    public function disputePageSettingDetail(): HasMany
    {
        return $this->hasMany(DisputePageSettingDetail::class,'dispute_page_id');
    }
}
