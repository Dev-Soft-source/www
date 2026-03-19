<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\Admin\DisclaimerPageSettingResource;
use Illuminate\Http\Resources\Json\JsonResource;

class DisclaimerPageSettingDetailResource extends JsonResource
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
            'disclaimer_page_setting_id' => $this->disclaimer_page_id,
            'language_id' => $this->language_id,
            'name' => $this->name,
            'meta_keywords' => $this->meta_keywords,
            'meta_description' => $this->meta_description,
            'main_heading' => $this->main_heading,
            'main_text' => $this->main_text,
            'disclaimer_page_setting' => new DisclaimerPageSettingResource($this->whenLoaded('disclaimerPageSetting')),
            'language' => new LanguageResource($this->whenLoaded('language')),
        ];
    }
}
