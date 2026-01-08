<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class MyPhoneSettingDetailResource extends JsonResource
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
            'phone_no_setting_id' => $this->phone_no_setting_id,
            'language_id' => $this->language_id,
            'main_heading' => $this->main_heading,
            'phone_no_description_text' => $this->phone_no_description_text,
            'unverified_number_label' => $this->unverified_number_label,
            'mobile_verify_button_text' => $this->mobile_verify_button_text,
            'delete_button_text' => $this->delete_button_text,
            'web_send_verification_code_button_text' => $this->web_send_verification_code_button_text,
            'mobile_country_code_label' => $this->mobile_country_code_label,
            'country_code_label_web' => $this->country_code_label_web,
            'country_code_placeholder' => $this->country_code_placeholder,
            'mobile_phone_number_label' => $this->mobile_phone_number_label,
            'country_id_label_web' => $this->country_id_label_web,
            'phone_number_label_web' => $this->phone_number_label_web,
            'phone_number_placeholder' => $this->phone_number_placeholder,
            'save_phoneno_button_text' => $this->save_phoneno_button_text,
            'send_verification_code_button_text' => $this->send_verification_code_button_text,
            'verify_phone_number_heading' => $this->verify_phone_number_heading,
            'otp_code_description' => $this->otp_code_description,
            'enter_code_label' => $this->enter_code_label,
            'verify_phone_number_label' => $this->verify_phone_number_label,
            'second_text' => $this->second_text,
            'request_code_text' => $this->request_code_text,
            'resend_code_btn_label' => $this->resend_code_btn_label,
            'set_as_default_label' => $this->set_as_default_label,
            'default_verified_number_label' => $this->default_verified_number_label,
            'verified_number_label' => $this->verified_number_label,
            'phone_no_description_text1' => $this->phone_no_description_text1,
            'add_another_phone_number_title' => $this->add_another_phone_number_title,
            'my_phone_no_setting' => new MyPhoneSettingResource($this->whenLoaded('myPhoneSetting')),
            'language' => new LanguageResource($this->whenLoaded('language')),
        ];
    }
}
