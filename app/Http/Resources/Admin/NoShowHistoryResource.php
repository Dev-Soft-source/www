<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class NoShowHistoryResource extends JsonResource
{
    public function toArray($request)
    {
        if ($this->resource === null) {
            return [];
        }
        return [
            'id' => $this->id,
            'ride_id' => $this->ride_id,
            'booking_id' => $this->booking_id,
            'user_id' => $this->user_id,
            'type' => $this->type,
            'status' => $this->status,
            'user' => $this->user ?? null,
            'ride' => $this->ride ?? null,
        ];
    }
}