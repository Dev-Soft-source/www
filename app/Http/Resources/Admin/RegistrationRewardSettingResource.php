<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class RegistrationRewardSettingResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'passenger_credit_booking' => $this->passenger_credit_booking,
            'driver_reward_point' => $this->driver_reward_point,
            'student_reward_point' => $this->student_reward_point,
        ];
    }
}