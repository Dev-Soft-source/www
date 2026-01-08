<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class Step4PageSettingDetailResource extends JsonResource
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
            'step4_page_setting_id' => $this->step4_page_setting_id,
            'language_id' => $this->language_id,
            'name' => $this->name,
            'meta_keywords' => $this->meta_keywords,
            'meta_description' => $this->meta_description,
            'main_heading' => $this->main_heading,
            'main_label' => $this->main_label,
            'country_code_label' => $this->country_code_label,
            'country_code_error' => $this->country_code_error,
            'phone_label' => $this->phone_label,
            'phone_error' => $this->phone_error,
            'skip_button_label' => $this->skip_button_label,
            'verify_button_label' => $this->verify_button_label,
            'verify_code_label' => $this->verify_code_label,
            'enter_code_label' => $this->enter_code_label,
            'request_code_label' => $this->request_code_label,
            'second_label' => $this->second_label,
            'save_button_label' => $this->save_button_label,
            'send_button_label' => $this->send_button_label,
            'logout_button_label' => $this->logout_button_label,
            'step4_page_setting' => new Step4PageSettingResource($this->whenLoaded('step4PageSetting')),
            'language' => new LanguageResource($this->whenLoaded('language')),
        ];
    }
}
