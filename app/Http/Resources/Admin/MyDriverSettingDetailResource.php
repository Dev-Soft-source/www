<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class MyDriverSettingDetailResource extends JsonResource
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
            'driver_lic_setting_id' => $this->driver_lic_setting_id,
            'language_id' => $this->language_id,
            'web_upload_image_placeholder' => $this->web_upload_image_placeholder,
            'mobile_indicate_required_field_label' => $this->mobile_indicate_required_field_label,
            'driver_license_description_text' => $this->driver_license_description_text,
            'main_heading' => $this->main_heading,
            'upload_button_text' => $this->upload_button_text,
            'driver_license_label' => $this->driver_license_label,
            'update_button_text' => $this->update_button_text,
            'upload_new_image_btn_label' => $this->upload_new_image_btn_label,
            'mobile_image_type_placeholder' => $this->mobile_image_type_placeholder,
            'mobile_choose_file_image_placeholder' => $this->mobile_choose_file_image_placeholder,
            'mobile_driver_license_image_placeholder' => $this->mobile_driver_license_image_placeholder,
            'my_driver_license_setting' => new MyDriverSettingResource($this->whenLoaded('myDriverSetting')),
            'language' => new LanguageResource($this->whenLoaded('language')),
        ];
    }
}
