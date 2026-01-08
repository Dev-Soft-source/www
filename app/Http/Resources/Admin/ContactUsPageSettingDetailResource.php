<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class ContactUsPageSettingDetailResource extends JsonResource
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
            'contact_us_page_setting_id' => $this->contact_page_setting_id,
            'language_id' => $this->language_id,
            'name' => $this->name,
            'meta_keywords' => $this->meta_keywords,
            'meta_description' => $this->meta_description,
            'main_heading' => $this->main_heading,
            'mailing_address_label' => $this->mailing_address_label,
            'telephone_label' => $this->telephone_label,
            'name_email_placeholder' => $this->name_email_placeholder,
            'message_placeholder' => $this->message_placeholder,
            'submit_button_label' => $this->submit_button_label,
            'placeholder_name' => $this->placeholder_name,
            'placeholder_email' => $this->placeholder_email,
            'placeholder_phone' => $this->placeholder_phone,
            'placeholder_message' => $this->placeholder_message,
            'required_feilds_text' => $this->required_feilds_text,
            'contact_us_page_setting' => new ContactUsPageSettingResource($this->whenLoaded('contactUsPageSetting')),
            'language' => new LanguageResource($this->whenLoaded('language')),
        ];
    }
}
