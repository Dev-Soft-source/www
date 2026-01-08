<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ProfilePageSettingDetail;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProfilePageSetting extends Model
{
    use HasFactory;
    public $table = "profile_page_setting";

    protected $guarded = [];

    public function profilePageSettingDetail(): HasMany
    {
        return $this->hasMany(ProfilePageSettingDetail::class);
    }
}
