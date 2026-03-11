<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\HasLanguageFallback;

class NotificationsPageSettingDetail extends Model
{
    use HasFactory, HasLanguageFallback;

    public $table = "notifications_page_setting_detail";
    protected $guarded = [];

    public function notificationsPageSetting(): BelongsTo
    {
        return $this->belongsTo(NotificationsPageSetting::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
