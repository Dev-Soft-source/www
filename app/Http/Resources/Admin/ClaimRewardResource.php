<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class ClaimRewardResource extends JsonResource
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
            'point' => $this->point,
            'request_date' => $this->request_date,
            'status' => $this->status,
            'approved_date' => $this->approved_date,
        ];
    }
}