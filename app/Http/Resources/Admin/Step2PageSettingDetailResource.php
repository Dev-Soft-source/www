<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class Step2PageSettingDetailResource extends JsonResource
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
            'step2_page_setting_id' => $this->step2_page_setting_id,
            'language_id' => $this->language_id,
            'name' => $this->name,
            'meta_keywords' => $this->meta_keywords,
            'meta_description' => $this->meta_description,
            'main_heading' => $this->main_heading,
            'sub_heading_text' => $this->sub_heading_text,
            'photo_error' => $this->photo_error,
            'photo_placeholder' => $this->photo_placeholder,
            'mobile_photo_label' => $this->mobile_photo_label,
            'mobile_choose_file_label' => $this->mobile_choose_file_label,
            'photo_label' => $this->photo_label,
            'skip_button_label' => $this->skip_button_label,
            'next_button_label' => $this->next_button_label,
            'logout_button_label' => $this->logout_button_label,
            'step2_page_setting' => new Step2PageSettingResource($this->whenLoaded('step2PageSetting')),
            'language' => new LanguageResource($this->whenLoaded('language')),
        ];
    }
}
