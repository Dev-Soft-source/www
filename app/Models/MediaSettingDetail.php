<?php

namespace App\Models;

use App\Models\Traits\HasLanguageFallback;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MediaSettingDetail extends Model
{
    use HasFactory, HasLanguageFallback;

    protected $table = 'media_setting_detail';

    protected $guarded = [];

    public function mediaSetting(): BelongsTo
    {
        return $this->belongsTo(MediaSetting::class, 'media_setting_id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}

