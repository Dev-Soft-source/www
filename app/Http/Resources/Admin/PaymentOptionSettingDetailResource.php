<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentOptionSettingDetailResource extends JsonResource
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
            'payment_opt_setting_id' => $this->payment_opt_setting_id,
            'language_id' => $this->language_id,
            'mobile_card_name_label' => $this->mobile_card_name_label,
            'mobile_default_card_tab' => $this->mobile_default_card_tab,
            'mobile_card_number_label' => $this->mobile_card_number_label,
            'main_heading' => $this->main_heading,
            'mobile_expiry_date_label' => $this->mobile_expiry_date_label,
            'delete_card_button_text' => $this->delete_card_button_text,
            'add_new_card_button_text' => $this->add_new_card_button_text,
            'no_payment_message' => $this->no_payment_message,
            'set_primary_card_label' => $this->set_primary_card_label,
            'select_card_type_text' => $this->select_card_type_text,
            'payment_option_setting' => new PaymentOptionSettingResource($this->whenLoaded('paymentOptionSetting')),
            'language' => new LanguageResource($this->whenLoaded('language')),
        ];
    }
}
