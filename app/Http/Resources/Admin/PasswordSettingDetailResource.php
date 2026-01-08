<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class PasswordSettingDetailResource extends JsonResource
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
            'password_setting_id' => $this->password_setting_id,
            'language_id' => $this->language_id,
            'password_description_text' => $this->password_description_text,
            'mobile_indicate_required_field_label' => $this->mobile_indicate_required_field_label,
            'current_password_label' => $this->current_password_label,
            'main_heading' => $this->main_heading,
            'current_password_placeholder' => $this->current_password_placeholder,
            'current_password_error' => $this->current_password_error,
            'new_password_label' => $this->new_password_label,
            'new_password_placeholder' => $this->new_password_placeholder,
            'new_password_error' => $this->new_password_error,
            'confirm_new_password_label' => $this->confirm_new_password_label,
            'confirm_new_password_placeholder' => $this->confirm_new_password_placeholder,
            'confirm_new_password_error' => $this->confirm_new_password_error,
            'mobile_password_description_text' => $this->mobile_password_description_text,
            'update_button_text' => $this->update_button_text,
            'password_setting' => new PasswordSettingResource($this->whenLoaded('passwordSetting')),
            'language' => new LanguageResource($this->whenLoaded('language')),
        ];
    }
}
