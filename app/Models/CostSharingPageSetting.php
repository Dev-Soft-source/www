<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CostSharingPageSetting extends Model
{
    use HasFactory;

    public $table = "cost_sharing_page_setting";

    protected $guarded = [];

    public function costSharingPageSettingDetail(): HasMany
    {
        return $this->hasMany(CostSharingPageSettingDetail::class, 'cost_sharing_page_id');
    }
}
