<?php

namespace App\Models;

use App\Models\Concerns\HasOptionGroups;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\HasLanguageFallback;

class PostRidePageSettingDetail extends Model
{
    use HasFactory, HasOptionGroups, HasLanguageFallback;

    public $table = "post_ride_page_setting_detail";
    protected $guarded = [];

    public function postRidePageSetting(): BelongsTo
    {
        return $this->belongsTo(PostRidePageSetting::class,'post_ride_page_setting_id' , 'id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }


    
}
