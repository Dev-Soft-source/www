<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class Step1PageSettingDetailResource extends JsonResource
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
            'step1_page_setting_id' => $this->step1_page_setting_id,
            'language_id' => $this->language_id,
            'name' => $this->name,
            'meta_keywords' => $this->meta_keywords,
            'meta_description' => $this->meta_description,
            'main_heading' => $this->main_heading,
            'required_label' => $this->required_label,
            'first_name_label' => $this->first_name_label,
            'first_name_error' => $this->first_name_error,
            'last_name_label' => $this->last_name_label,
            'last_name_error' => $this->last_name_error,
            'gender_label' => $this->gender_label,
            'gender_error' => $this->gender_error,
            'male_option_label' => $this->male_option_label,
            'female_option_label' => $this->female_option_label,
            'prefer_option_label' => $this->prefer_option_label,
            'dob_label' => $this->dob_label,
            'dob_error' => $this->dob_error,
            'country_label' => $this->country_label,
            'country_error' => $this->country_error,
            'state_label' => $this->state_label,
            'state_error' => $this->state_error,
            'city_label' => $this->city_label,
            'city_error' => $this->city_error,
            'zip_code_label' => $this->zip_code_label,
            'zip_code_error' => $this->zip_code_error,
            'bio_label' => $this->bio_label,
            'bio_error' => $this->bio_error,
            'button_label' => $this->button_label,
            'logout_button_label' => $this->logout_button_label,
            'bio_placeholder' => $this->bio_placeholder,
            'step1_page_setting' => new Step1PageSettingResource($this->whenLoaded('step1PageSetting')),
            'language' => new LanguageResource($this->whenLoaded('language')),
        ];
    }
}
