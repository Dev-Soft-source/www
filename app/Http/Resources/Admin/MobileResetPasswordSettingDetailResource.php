<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class MobileResetPasswordSettingDetailResource extends JsonResource
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
            'reset_password_page_setting_id' => $this->reset_page_id,
            'language_id' => $this->language_id,
            'main_heading' => $this->main_heading,
            'main_label' => $this->main_label,
            'password_label' => $this->password_label,
            'password_placeholder' => $this->password_placeholder,
            'confirm_password_label' => $this->confirm_password_label,
            'confirm_password_placeholder' => $this->confirm_password_placeholder,
            'button_label' => $this->button_label,
            'mobile_reset_password_setting' => new MobileResetPasswordSettingResource($this->whenLoaded('mobileResetPasswordSetting')),
            'language' => new LanguageResource($this->whenLoaded('language')),
        ];
    }
}
