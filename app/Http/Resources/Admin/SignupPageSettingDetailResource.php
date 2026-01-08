<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class SignupPageSettingDetailResource extends JsonResource
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
            'signup_page_setting_id' => $this->signup_page_setting_id,
            'language_id' => $this->language_id,
            'name' => $this->name,
            'meta_keywords' => $this->meta_keywords,
            'meta_description' => $this->meta_description,
            'main_heading' => $this->main_heading,
            'or_label' => $this->or_label,
            'required_label' => $this->required_label,
            'first_name_label' => $this->first_name_label,
            'first_name_error' => $this->first_name_error,
            'first_name_placeholder' => $this->first_name_placeholder,
            'last_name_label' => $this->last_name_label,
            'last_name_error' => $this->last_name_error,
            'last_name_placeholder' => $this->last_name_placeholder,
            'email_label' => $this->email_label,
            'email_error' => $this->email_error,
            'email_placeholder' => $this->email_placeholder,
            'password_label' => $this->password_label,
            'password_error' => $this->password_error,
            'password_placeholder' => $this->password_placeholder,
            'confirm_password_label' => $this->confirm_password_label,
            'confirm_password_error' => $this->confirm_password_error,
            'confirm_password_placeholder' => $this->confirm_password_placeholder,
            'agree_terms_error' => $this->agree_terms_error,
            'phone_number_label' => $this->phone_number_label,
            'phone_number_option1' => $this->phone_number_option1,
            'phone_number_option2' => $this->phone_number_option2,
            'agree_terms_label' => $this->agree_terms_label,
            'button_label' => $this->button_label,
            'after_button_label' => $this->after_button_label,
            'signin_label' => $this->signin_label,
            'app_main_heading' => $this->app_main_heading,
            'app_agree_terms_part1_label' => $this->app_agree_terms_part1_label,
            'app_agree_terms_link1_label' => $this->app_agree_terms_link1_label,
            'app_agree_terms_link2_label' => $this->app_agree_terms_link2_label,
            'app_agree_terms_part2_label' => $this->app_agree_terms_part2_label,
            'app_agree_terms_link3_label' => $this->app_agree_terms_link3_label,
            'app_agree_terms_part3_label' => $this->app_agree_terms_part3_label,
            'no_account_label' => $this->no_account_label,
            'signin_link_label' => $this->signin_link_label,
            'now_label' => $this->now_label,
            'language_label' => $this->language_label,
            'signup_page_setting' => new SignupPageSettingResource($this->whenLoaded('signupPageSetting')),
            'language' => new LanguageResource($this->whenLoaded('language')),
        ];
    }
}
