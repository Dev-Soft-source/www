<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferralSystemSetting extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = [
        'id',
        'p_2_p_booking_credit',
        'p_2_s_booking_credit',
        'p_2_d_booking_credit',
        'd_2_p_reward_point',
        'd_2_s_reward_point',
        'd_2_d_rewad_point',
        's_2_p_reward_point',
        's_2_s_reward_point',
        's_2_d_reward_point',
    ];
}
