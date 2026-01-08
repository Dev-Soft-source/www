<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class CoffeeWalletResource extends JsonResource
{
    public function toArray($request)
    {
        if ($this->resource === null) {
            return [];
        }
        return [
            'id' => $this->id,
            'name' => $this->name ?? null,
            'email' => $this->email ?? null,
            'phone' => $this->phone,
            'ride_id' => $this->ride_id,
            'booking_id' => $this->booking_id,
            'user_id' => $this->user_id,
            'dr_amount' => $this->dr_amount ?? 0.0,
            'cr_amount' => $this->cr_amount ?? 0.0,
        ];
    }
}