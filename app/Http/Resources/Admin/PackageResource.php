<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class PackageResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'frequency' => $this->frequency,
            'price' => $this->price,
            'package_detail' => PackageDetailResource::collection($this->whenLoaded('packageDetail')),
        ];
    }
}