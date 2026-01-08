<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class Step3PageSettingDetailResource extends JsonResource
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
            'step3_page_setting_id' => $this->step3_page_setting_id,
            'language_id' => $this->language_id,
            'name' => $this->name,
            'meta_keywords' => $this->meta_keywords,
            'meta_description' => $this->meta_description,
            'main_heading' => $this->main_heading,
            'main_label' => $this->main_label,
            'required_label' => $this->required_label,
            'make_label' => $this->make_label,
            'make_error' => $this->make_error,
            'make_placeholder' => $this->make_placeholder,
            'model_label' => $this->model_label,
            'model_error' => $this->model_error,
            'model_placeholder' => $this->model_placeholder,
            'vehicle_type_label' => $this->vehicle_type_label,
            'vehicle_type_error' => $this->vehicle_type_error,
            'color_label' => $this->color_label,
            'color_error' => $this->color_error,
            'license_label' => $this->license_label,
            'license_error' => $this->license_error,
            'year_label' => $this->year_label,
            'year_error' => $this->year_error,
            'fuel_label' => $this->fuel_label,
            'fuel_error' => $this->fuel_error,
            'electric_option_label' => $this->electric_option_label,
            'hybrid_option_label' => $this->hybrid_option_label,
            'gas_option_label' => $this->gas_option_label,
            // 'driver_license_label' => $this->driver_license_label,
            'driver_license_error' => $this->driver_license_error,
            // 'driver_license_sub_label' => $this->driver_license_sub_label,
            'mobile_driver_choose_file_label' => $this->mobile_driver_choose_file_label,
            'photo_label' => $this->photo_label,
            'photo_error' => $this->photo_error,
            'photo_detail_label' => $this->photo_detail_label,
            'mobile_photo_choose_file_label' => $this->mobile_photo_choose_file_label,
            'skip_button_label' => $this->skip_button_label,
            'next_button_label' => $this->next_button_label,
            'logout_button_label' => $this->logout_button_label,
            'vehicle_type_placeholder' => $this->vehicle_type_placeholder,
            'sub_heading' => $this->sub_heading,
            'sub_main_label' => $this->sub_main_label,
            'liecense_section_heading' => $this->liecense_section_heading,
            'vehicle_section_heading' => $this->vehicle_section_heading,
            'skip_vehicle_info' => $this->skip_vehicle_info,
            'skip_license' => $this->skip_license,
            'step3_page_setting' => new Step3PageSettingResource($this->whenLoaded('step3PageSetting')),
            'language' => new LanguageResource($this->whenLoaded('language')),
        ];
    }
}
