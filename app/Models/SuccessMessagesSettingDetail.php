<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\HasLanguageFallback;

class SuccessMessagesSettingDetail extends Model
{
    use HasFactory, HasLanguageFallback;

    public $table = "success_messages_setting_detail";
    protected $guarded = [];

    public function successMessagesSetting(): BelongsTo
    {
        return $this->belongsTo(SuccessMessagesSetting::class,'messages_setting_id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

}
