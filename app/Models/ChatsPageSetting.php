<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatsPageSetting extends Model
{
    use HasFactory;

    public $table = "chats_page_setting";

    protected $guarded = [];

    public function chatsPageSettingDetail(): HasMany
    {
        return $this->hasMany(ChatsPageSettingDetail::class);
    }
}
