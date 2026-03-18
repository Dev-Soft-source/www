<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MediaSetting extends Model
{
    use HasFactory;

    protected $table = 'media_setting';

    protected $guarded = [];

    public function mediaSettingDetail(): HasMany
    {
        return $this->hasMany(MediaSettingDetail::class, 'media_setting_id');
    }
}

