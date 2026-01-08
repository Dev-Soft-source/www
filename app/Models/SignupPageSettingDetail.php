<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SignupPageSettingDetail extends Model
{
    use HasFactory;

    public $table = "signup_page_setting_detail";
    protected $guarded = [];

    public function signupPageSetting(): BelongsTo
    {
        return $this->belongsTo(SignupPageSetting::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
