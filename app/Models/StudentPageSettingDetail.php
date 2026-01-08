<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentPageSettingDetail extends Model
{
    use HasFactory;

    public $table = "student_page_setting_detail";
    protected $guarded = [];

    public function studentPageSetting(): BelongsTo
    {
        return $this->belongsTo(StudentPageSetting::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
