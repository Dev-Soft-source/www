<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ErrorPageSetting extends Model
{
    public $table = 'error_page_setting';

    protected $guarded = [];

    public function errorPageSettingDetail(): HasMany
    {
        return $this->hasMany(ErrorPageSettingDetail::class);
    }
}
