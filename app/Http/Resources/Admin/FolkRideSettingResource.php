<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class FolkRideSettingResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'average_rating' => $this->average_rating,
            'driver_age' => $this->driver_age,
            'verfiy_phone' => $this->verfiy_phone,
            'verify_email' => $this->verify_email,
            'driver_license' => $this->driver_license,
            'profile_complete' => $this->profile_complete,
            'extra_rides_trip_limit' => $this->extra_rides_trip_limit,
        ];
    }
}