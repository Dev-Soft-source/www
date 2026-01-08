<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ContactProximaRideSettingDetail;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ContactProximaRideSetting extends Model
{
    use HasFactory;
    public $table = "contact_proximaride_setting";

    protected $guarded = [];

    public function contactProximaRideSettingDetail(): HasMany
    {
        return $this->hasMany(ContactProximaRideSettingDetail::class,'contact_pr_setting_id' ,'id');
    }
}
