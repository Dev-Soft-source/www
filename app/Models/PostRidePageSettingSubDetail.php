<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostRidePageSettingSubDetail extends Model
{
    use HasFactory;

    public $table = "post_ride_page_setting_sub_detail";
    protected $guarded = [];

    public function postRidePageSetting(): BelongsTo
    {
        return $this->belongsTo(PostRidePageSetting::class,'post_ride_page_id' , 'id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }}
