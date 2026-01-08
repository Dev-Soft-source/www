<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class PinkRideSettingResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'female' => $this->female,
            'verfiy_phone_passenger' => $this->verfiy_phone_passenger,
            'verfiy_phone' => $this->verfiy_phone,
            'verify_email' => $this->verify_email,
            'driver_license' => $this->driver_license,
            'profile_complete' => $this->profile_complete,
        ];
    }
}