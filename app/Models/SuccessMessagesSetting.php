<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SuccessMessagesSetting extends Model
{
    use HasFactory;

    public $table = "success_messages_setting";

    protected $guarded = [];

    public function successMessagesSettingDetail(): HasMany
    {
        return $this->hasMany(SuccessMessagesSettingDetail::class,'messages_setting_id');
    }
}
