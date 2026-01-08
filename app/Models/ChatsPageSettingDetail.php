<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatsPageSettingDetail extends Model
{
    use HasFactory;

    public $table = "chats_page_setting_detail";
    protected $guarded = [];

    public function chatsPageSetting(): BelongsTo
    {
        return $this->belongsTo(ChatsPageSetting::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
