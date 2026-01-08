<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class StudentPageSettingDetailResource extends JsonResource
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
            'student_page_setting_id' => $this->student_page_setting_id,
            'language_id' => $this->language_id,
            'name' => $this->name,
            'meta_keywords' => $this->meta_keywords,
            'meta_description' => $this->meta_description,
            'main_heading' => $this->main_heading,
            'sub_heading' => $this->sub_heading,
            'page_image' => $this->page_image,
            'page_description' => $this->page_description,
            'student_page_setting' => new StudentPageSettingResource($this->whenLoaded('studentPageSetting')),
            'language' => new LanguageResource($this->whenLoaded('language')),
        ];
    }
}
