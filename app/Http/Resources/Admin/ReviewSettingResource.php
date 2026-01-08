<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class ReviewSettingResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'leave_review_days' => $this->leave_review_days,
            'respond_review_days' => $this->respond_review_days,
        ];
    }
}