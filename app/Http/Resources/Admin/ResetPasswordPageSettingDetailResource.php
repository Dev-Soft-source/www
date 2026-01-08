<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class ResetPasswordPageSettingDetailResource extends JsonResource
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
            'reset_password_page_setting_id' => $this->reset_pass_page_id,
            'language_id' => $this->language_id,
            'name' => $this->name,
            'meta_keywords' => $this->meta_keywords,
            'meta_description' => $this->meta_description,
            'main_heading' => $this->main_heading,
            'main_label' => $this->main_label,
            'password_label' => $this->password_label,
            'password_placeholder' => $this->password_placeholder,
            'confirm_password_label' => $this->confirm_password_label,
            'confirm_password_error' => $this->confirm_password_error,
            'confirm_password_placeholder' => $this->confirm_password_placeholder,
            'button_label' => $this->button_label,
            'reset_password_page_setting' => new ResetPasswordPageSettingResource($this->whenLoaded('resetPasswordPageSetting')),
            'language' => new LanguageResource($this->whenLoaded('language')),
        ];
    }
}
