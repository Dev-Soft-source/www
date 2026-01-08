<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    public function toArray($request)
    {
        if ($this->resource === null) {
            return [];
        }
        return [
            'id' => $this->id,
            'random_id' => $this->random_id,
            'type' => $this->type,
            'price' => $this->price,
            'on_date' => $this->on_date,
            'passenger_first_name' => $this->booking->passenger->first_name ?? null,
            'passenger_last_name' => $this->booking->passenger->last_name ?? null,
            'passenger_email' => $this->booking->passenger->email ?? null,
            'driver_first_name' => $this->booking->ride->driver->first_name ?? null,
            'driver_last_name' => $this->booking->ride->driver->last_name ?? null,
            'driver_email' => $this->booking->ride->driver->email ?? null,
            'ride_id' => $this->booking->ride_id ?? null,
        ];
    }
}
