<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class VideoDetailResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'video_id' => $this->video_id,
            'language_id' => $this->language_id,
            'link' => $this->link,
            'video' => new VideoResource($this->whenLoaded('video')),
            'language' => new LanguageResource($this->whenLoaded('language')),
        ];
    }
}