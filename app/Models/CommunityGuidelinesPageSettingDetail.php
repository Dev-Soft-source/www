<?php

namespace App\Models;

use App\Models\Traits\HasLanguageFallback;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommunityGuidelinesPageSettingDetail extends Model
{
    use HasFactory, HasLanguageFallback;

    public $table = "community_guidelines_page_setting_detail";

    protected $guarded = [];

    public function communityGuidelinesPageSetting(): BelongsTo
    {
        return $this->belongsTo(CommunityGuidelinesPageSetting::class, 'community_guidelines_page_id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
