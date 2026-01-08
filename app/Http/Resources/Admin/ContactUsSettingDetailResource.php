<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class ContactUsSettingDetailResource extends JsonResource
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
            'contact_pr_setting_id' => $this->contact_pr_setting_id,
            'language_id' => $this->language_id,
            'mobile_indicate_required_field_label' => $this->mobile_indicate_required_field_label,
            'your_full_name_label' => $this->your_full_name_label,
            'your_full_name_placeholder' => $this->your_full_name_placeholder,
            'main_heading' => $this->main_heading,
            'your_phone_label' => $this->your_phone_label,
            'your_phone_placeholder' => $this->your_phone_placeholder,
            'your_email_address_label' => $this->your_email_address_label,
            'your_email_address_placeholder' => $this->your_email_address_placeholder,
            'your_message_label' => $this->your_message_label,
            'submit_button_text' => $this->submit_button_text,
            'language' => $this->when($this->relationLoaded('language'), function() {
                return [
                    'id' => $this->language->id ?? null,
                    'name' => $this->language->name ?? null,
                ];
            }),
        ];
    }
}
