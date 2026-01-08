<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CancellationPageSetting extends Model
{
    use HasFactory;
    public $table = "cancellation_page_setting";

    protected $guarded = [];

    public function cancellationPageSettingDetail(): HasMany
    {
        return $this->hasMany(CancellationPageSettingDetail::class,'cancellation_page_id');
    }
}
