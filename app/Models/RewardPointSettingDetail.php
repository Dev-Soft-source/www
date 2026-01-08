<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RewardPointSettingDetail extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function rewardPointSetting(): BelongsTo
    {
        return $this->belongsTo(RewardPointSetting::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
