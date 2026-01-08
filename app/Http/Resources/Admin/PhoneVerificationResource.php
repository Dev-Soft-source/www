<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class PhoneVerificationResource extends JsonResource
{
    public function toArray($request)
    {
        if ($this->resource === null) {
            return [];
        }
        return [
            'id' => $this->id,
            'random_id' => $this->random_id,
            'verification_code' => $this->verification_code,
            'phone' => $this->phone ?? null,
        ];
    }
}