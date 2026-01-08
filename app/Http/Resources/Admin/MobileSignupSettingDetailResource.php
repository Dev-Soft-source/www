<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class MobileSignupSettingDetailResource extends JsonResource
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
            'mobile_signup_setting_id' => $this->mobile_signup_setting_id,
            'language_id' => $this->language_id,
            'main_heading' => $this->main_heading,
            'or_label' => $this->or_label,
            'first_name_label' => $this->first_name_label,
            'first_name_placeholder' => $this->first_name_placeholder,
            'last_name_label' => $this->last_name_label,
            'last_name_placeholder' => $this->last_name_placeholder,
            'email_label' => $this->email_label,
            'email_placeholder' => $this->email_placeholder,
            'password_label' => $this->password_label,
            'password_placeholder' => $this->password_placeholder,
            'confirm_password_label' => $this->confirm_password_label,
            'confirm_password_placeholder' => $this->confirm_password_placeholder,
            'agree_terms_label' => $this->agree_terms_label,
            'button_label' => $this->button_label,
            'signin_label' => $this->signin_label,
            'mobile_signup_setting' => new MobileSignupSettingResource($this->whenLoaded('mobileSignupSetting')),
            'language' => new LanguageResource($this->whenLoaded('language')),
        ];
    }
}
