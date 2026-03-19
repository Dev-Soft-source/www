<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\Admin\CommunityGuidelinesPageSettingResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CommunityGuidelinesPageSettingDetailResource extends JsonResource
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
            'community_guidelines_page_setting_id' => $this->community_guidelines_page_id,
            'language_id' => $this->language_id,
            'name' => $this->name,
            'meta_keywords' => $this->meta_keywords,
            'meta_description' => $this->meta_description,
            'main_heading' => $this->main_heading,
            'main_text' => $this->main_text,
            'community_guidelines_page_setting' => new CommunityGuidelinesPageSettingResource($this->whenLoaded('communityGuidelinesPageSetting')),
            'language' => new LanguageResource($this->whenLoaded('language')),
        ];
    }
}
