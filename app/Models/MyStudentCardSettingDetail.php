<?php

namespace App\Models;

use App\Models\Language;
use App\Models\MyStudentCardSetting;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MyStudentCardSettingDetail extends Model
{
    use HasFactory;
    public $table = "my_student_card_setting_detail";
    protected $guarded = [];

    public function myStudentSetting(): BelongsTo
    {
        return $this->belongsTo(MyStudentCardSetting::class,'student_card_setting_id' ,'id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
