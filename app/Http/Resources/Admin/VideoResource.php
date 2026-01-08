<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class VideoResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'page' => $this->page,
            'video_detail' => VideoDetailResource::collection($this->whenLoaded('videoDetail')),
        ];
    }
}