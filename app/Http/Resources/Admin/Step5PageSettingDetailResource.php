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
            'country_code_label' => $this->country_code_label,
            'country_code_error' => $this->country_code_error,
            'phone_label' => $this->phone_label,
            'phone_error' => $this->phone_error,
            'skip_button_label' => $this->skip_button_label,
            'skip_phone_number_label' => $this->skip_phone_number_label,
            'verify_button_label' => $this->verify_button_label,
            'verify_code_label' => $this->verify_code_label,
            'enter_code_label' => $this->enter_code_label,
            'request_code_label' => $this->request_code_label,
            'second_label' => $this->second_label,
            'save_button_label' => $this->save_button_label,
            'send_button_label' => $this->send_button_label,
            'logout_button_label' => $this->logout_button_label,
            'whatsapp_not_available_title' => $this->whatsapp_not_available_title,
            'whatsapp_not_available_message' => $this->whatsapp_not_available_message,
            'whatsapp_success_title' => $this->whatsapp_success_title,
            'whatsapp_success_message' => $this->whatsapp_success_message,
            'whatsapp_error_title' => $this->whatsapp_error_title,
            'whatsapp_error_message' => $this->whatsapp_error_message,
            'whatsapp_warning_title' => $this->whatsapp_warning_title,
            'whatsapp_warning_message' => $this->whatsapp_warning_message,
            'step5_page_setting' => new Step5PageSettingResource($this->whenLoaded('step5PageSetting')),
            'language' => new LanguageResource($this->whenLoaded('language')),
        ];
        
    }
}
