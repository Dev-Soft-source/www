<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\HasLanguageFallback;

class HomePageSettingDetail extends Model
{
    use HasFactory, HasLanguageFallback;

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
