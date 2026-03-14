<?php

namespace App\Models;

use App\Models\Traits\HasLanguageFallback;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CostSharingPageSettingDetail extends Model
{
    use HasFactory, HasLanguageFallback;

    public $table = "cost_sharing_page_setting_detail";

    protected $guarded = [];

    public function costSharingPageSetting(): BelongsTo
    {
        return $this->belongsTo(CostSharingPageSetting::class, 'cost_sharing_page_id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
