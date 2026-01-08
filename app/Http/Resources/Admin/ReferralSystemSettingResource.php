<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class ReferralSystemSettingResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'p_2_p_booking_credit' => $this->p_2_p_booking_credit,
            'p_2_s_booking_credit' => $this->p_2_s_booking_credit,
            'p_2_d_booking_credit' => $this->p_2_d_booking_credit,
            'd_2_p_reward_point' => $this->d_2_p_reward_point,
            'd_2_s_reward_point' => $this->d_2_s_reward_point,
            'd_2_d_rewad_point' => $this->d_2_d_rewad_point,
            's_2_p_reward_point' => $this->s_2_p_reward_point,
            's_2_s_reward_point' => $this->s_2_s_reward_point,
            's_2_d_reward_point' => $this->s_2_d_reward_point,
        ];
    }
}