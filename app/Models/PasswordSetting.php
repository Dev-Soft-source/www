<?php

namespace App\Models;

use App\Models\PasswordSettingDetail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PasswordSetting extends Model
{
    use HasFactory;
    public $table = "password_setting";

    protected $guarded = [];

    public function passwordSettingDetail(): HasMany
    {
        return $this->hasMany(PasswordSettingDetail::class);
    }
}
