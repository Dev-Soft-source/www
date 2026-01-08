<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class ExtraCareFaqResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'extra_care_faq_detail' => ExtraCareFaqDetailResource::collection($this->whenLoaded('extraCareFaqDetail')),
        ];
    }
}
