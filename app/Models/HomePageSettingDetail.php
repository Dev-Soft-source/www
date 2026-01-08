<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HomePageSettingDetail extends Model
{
    use HasFactory;

    public $table = "home_page_setting_detail";
    protected $guarded = [];


    public function homePageSetting(): BelongsTo
    {
        return $this->belongsTo(HomePageSetting::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
