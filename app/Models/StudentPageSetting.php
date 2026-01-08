<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentPageSetting extends Model
{
    use HasFactory;

    public $table = "student_page_setting";

    protected $guarded = [];

    public function studentPageSettingDetail(): HasMany
    {
        return $this->hasMany(StudentPageSettingDetail::class);
    }
}
