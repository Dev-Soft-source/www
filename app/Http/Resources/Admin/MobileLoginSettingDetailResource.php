<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class MobileLoginSettingDetailResource extends JsonResource
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
            'mobile_login_setting_id' => $this->mobile_login_setting_id,
            'language_id' => $this->language_id,
            'main_heading' => $this->main_heading,
            'email_label' => $this->email_label,
            'email_placeholder' => $this->email_placeholder,
            'password_label' => $this->password_label,
            'password_placeholder' => $this->password_placeholder,
            'submit_button_label' => $this->submit_button_label,
            'forgot_password_label' => $this->forgot_password_label,
            'or_label' => $this->or_label,
            'signup_label' => $this->signup_label,
            'mobile_login_setting' => new MobileLoginSettingResource($this->whenLoaded('mobileLoginSetting')),
            'language' => new LanguageResource($this->whenLoaded('language')),
        ];
    }
}
