<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class PinkRideFaqResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'pink_ride_faq_detail' => PinkRideFaqDetailResource::collection($this->whenLoaded('pinkRideFaqDetail')),
        ];
    }
}
