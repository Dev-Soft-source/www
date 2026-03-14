<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class CostSharingPageSettingDetailResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'cost_sharing_page_setting_id' => $this->cost_sharing_page_id,
            'language_id' => $this->language_id,
            'name' => $this->name,
            'meta_keywords' => $this->meta_keywords,
            'meta_description' => $this->meta_description,
            'main_heading' => $this->main_heading,
            'main_text' => $this->main_text,
            'cost_sharing_page_setting' => new CostSharingPageSettingResource($this->whenLoaded('costSharingPageSetting')),
            'language' => new LanguageResource($this->whenLoaded('language')),
        ];
    }
}
