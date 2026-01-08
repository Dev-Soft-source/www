<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResetPasswordPageSettingDetail extends Model
{
    use HasFactory;

    public $table = "reset_password_page_setting_detail";
    protected $guarded = [];

    public function resetPasswordPageSetting(): BelongsTo
    {
        return $this->belongsTo(ResetPasswordPageSetting::class,'reset_pass_page_id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
