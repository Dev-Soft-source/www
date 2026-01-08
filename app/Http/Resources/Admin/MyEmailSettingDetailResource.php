<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class MyEmailSettingDetailResource extends JsonResource
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
            'email_address_setting_id' => $this->email_address_setting_id,
            'language_id' => $this->language_id,
            'email_description_text' => $this->email_description_text,
            'email_label' => $this->email_label,
            'main_heading' => $this->main_heading,
            'email_placeholder' => $this->email_placeholder,
            'update_button_text' => $this->update_button_text,
            'save_btn_label' => $this->save_btn_label,
            'confirm_email_label' => $this->confirm_email_label,
            'new_email_label' => $this->new_email_label,
            'current_email_label' => $this->current_email_label,
            'email_address_setting' => new MyEmailSettingResource($this->whenLoaded('myEmailSetting')),
            'language' => new LanguageResource($this->whenLoaded('language')),
        ];
    }
}
