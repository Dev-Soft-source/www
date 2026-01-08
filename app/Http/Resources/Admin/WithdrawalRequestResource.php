<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class WithdrawalRequestResource extends JsonResource
{
    public function toArray($request)
    {
        if ($this->resource === null) {
            return [];
        }
        return [
            'id' => $this->id,
            'first_name' => $this->driver->first_name ?? null,
            'last_name' => $this->driver->last_name ?? null,
            'amount' => $this->total_amount,
            'bank_detail' => $this->driver->bankDetail ?? null,
            'user_id' => $this->user_id ?? null,
            'booking_id' => $this->booking_id ?? null,
            'bookings' => $this->bookings ?? [],
        ];
    }
}