<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class StateResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'country_id' => $this->country_id,
            'ride_limit' => $this->ride_limit,
            'tax' => $this->tax,
            'country' => $this->country->name,
            'created_at' => $this->created_at,
        ];
    }
}
