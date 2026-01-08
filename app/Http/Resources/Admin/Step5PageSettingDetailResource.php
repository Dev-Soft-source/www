<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class Step5PageSettingDetailResource extends JsonResource
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
            'step5_page_setting_id' => $this->step5_page_setting_id,
            'language_id' => $this->language_id,
            'name' => $this->name,
            'meta_keywords' => $this->meta_keywords,
            'meta_description' => $this->meta_description,
            'main_heading' => $this->main_heading,
            'main_label' => $this->main_label,
            'sub_main_label' => $this->sub_main_label,
            'required_label' => $this->required_label,
            'driver_license_label' => $this->driver_license_label,
            'driver_license_sub_label' => $this->driver_license_sub_label,
            'driver_license_error' => $this->driver_license_error,
            'photo_detail_label' => $this->photo_detail_label,
            'mobile_photo_choose_file_label' => $this->mobile_photo_choose_file_label,
            'skip_license' => $this->skip_license,
            'next_button_label' => $this->next_button_label,
            'liecense_section_heading' => $this->liecense_section_heading,
            'step5_page_setting' => new Step5PageSettingResource($this->whenLoaded('step4PageSetting')),
            'language' => new LanguageResource($this->whenLoaded('language')),
        ];
    }
}