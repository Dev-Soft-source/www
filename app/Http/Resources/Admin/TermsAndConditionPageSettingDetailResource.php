<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class TermsAndConditionPageSettingDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'contact_us_page_setting_id' => $this->terms_page_id,
            'language_id' => $this->language_id,
            'name' => $this->name,
            'meta_keywords' => $this->meta_keywords,
            'meta_description' => $this->meta_description,
            'main_heading' => $this->main_heading,
            'main_text' => $this->main_text,
            'terms_and_condition_page_setting' => new TermsAndConditionPageSettingResource($this->whenLoaded('termsAndConditionPageSetting')),
            'language' => new LanguageResource($this->whenLoaded('language')),
        ];
    }
}
