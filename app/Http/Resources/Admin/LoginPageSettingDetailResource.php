<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class LoginPageSettingDetailResource extends JsonResource
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
            'login_page_setting_id' => $this->login_page_setting_id,
            'language_id' => $this->language_id,
            'name' => $this->name,
            'meta_keywords' => $this->meta_keywords,
            'meta_description' => $this->meta_description,
            'main_heading' => $this->main_heading,
            'continue_label' => $this->continue_label,
            'new_verification_email_btn_label' => $this->new_verification_email_btn_label,
            'or_label' => $this->or_label,
            'email_label' => $this->email_label,
            'email_error' => $this->email_error,
            'email_placeholder' => $this->email_placeholder,
            'password_label' => $this->password_label,
            'password_error' => $this->password_error,
            'password_placeholder' => $this->password_placeholder,
            'forgot_password_label' => $this->forgot_password_label,
            'submit_button_label' => $this->submit_button_label,
            'signup_label' => $this->signup_label,
            'no_account_label' => $this->no_account_label,
            'signup_link_label' => $this->signup_link_label,
            'now_label' => $this->now_label,
            'language_label' => $this->language_label,
            'protect_account_heading' => $this->protect_account_heading,
            'protect_account_text' => $this->protect_account_text,
            'close_modal_error_message' => $this->close_modal_error_message,
            'remember_me_text' => $this->remember_me_text,
            'login_page_setting' => new LoginPageSettingResource($this->whenLoaded('loginPageSetting')),
            'language' => new LanguageResource($this->whenLoaded('language')),
        ];
    }
}
