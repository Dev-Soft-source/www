<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CommunityGuidelinesPageSetting extends Model
{
    use HasFactory;

    public $table = "community_guidelines_page_setting";

    protected $guarded = [];

    public function communityGuidelinesPageSettingDetail(): HasMany
    {
        return $this->hasMany('App\Models\CommunityGuidelinesPageSettingDetail', 'community_guidelines_page_id');
    }
}
