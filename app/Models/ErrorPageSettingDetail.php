<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\HasLanguageFallback;

class ErrorPageSettingDetail extends Model
{
    use HasLanguageFallback;

    public $table = 'error_page_setting_detail';

    protected $guarded = [];

    public function errorPageSetting(): BelongsTo
    {
        return $this->belongsTo(ErrorPageSetting::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
