<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HomePageSetting extends Model
{
    use HasFactory;

    public $table = "home_page_setting";

    protected $guarded = [];

    public function homePageSettingDetail(): HasMany
    {
        return $this->hasMany(HomePageSettingDetail::class);
    }
}
