<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class ProfilePhotoGuidelinesPageSettingDetailResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'profile_photo_guidelines_page_setting_id' => $this->profile_photo_guidelines_page_id,
            'language_id' => $this->language_id,
            'name' => $this->name,
            'meta_keywords' => $this->meta_keywords,
            'meta_description' => $this->meta_description,
            'main_heading' => $this->main_heading,
            'main_text' => $this->main_text,
            'example_label' => $this->example_label,
            'profile_photo_guidelines_page_setting' => new ProfilePhotoGuidelinesPageSettingResource($this->whenLoaded('profilePhotoGuidelinesPageSetting')),
            'language' => new LanguageResource($this->whenLoaded('language')),
        ];
    }
}
