<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class PinkRideFaqDetailResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'pink_ride_faq_id' => $this->pink_ride_faq_id,
            'language_id' => $this->language_id,
            'question' => $this->question,
            'answer' => $this->answer,
            'pinkRideFaq' => new PinkRideFaqResource($this->whenLoaded('pinkRideFaq')),
            'language' => new LanguageResource($this->whenLoaded('language')),
        ];
    }
}