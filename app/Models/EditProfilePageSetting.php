<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EditProfilePageSetting extends Model
{
    use HasFactory;

    use HasFactory;

    public $table = "edit_profile_page_setting";

    protected $guarded = [];

    public function editProfilePageSettingDetail(): HasMany
    {
        return $this->hasMany(EditProfilePageSettingDetail::class ,'edit_profile_id' ,'id');
    }
}
