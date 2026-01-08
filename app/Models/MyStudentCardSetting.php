<?php

namespace App\Models;

use App\Models\MyStudentCardSettingDetail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MyStudentCardSetting extends Model
{
    use HasFactory;
    public $table = "my_student_card_setting";

    protected $guarded = [];

    public function myStudentSettingDetail(): HasMany
    {
        return $this->hasMany(MyStudentCardSettingDetail::class,'student_card_setting_id' ,'id');
    }
}
