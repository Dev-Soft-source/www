<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class CancelRideSettingResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'driver_cancel_hours' => $this->driver_cancel_hours,
            'passenger_cancel_hours' => $this->passenger_cancel_hours,
        ];
    }
}