<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class ExtraCareFaqDetailResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'extra_care_faq_id' => $this->extra_care_faq_id,
            'language_id' => $this->language_id,
            'question' => $this->question,
            'answer' => $this->answer,
            'extraCareFaq' => new ExtraCareFaqResource($this->whenLoaded('extraCareFaq')),
            'language' => new LanguageResource($this->whenLoaded('language')),
        ];
    }
}