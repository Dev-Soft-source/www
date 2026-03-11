<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NotificationsPageSetting extends Model
{
    use HasFactory;
    public $table = "notifications_page_setting";
    protected $guarded = [];
    public function notificationsPageSettingDetail(): HasMany
    {
        return $this->hasMany(NotificationsPageSettingDetail::class);
    }
}