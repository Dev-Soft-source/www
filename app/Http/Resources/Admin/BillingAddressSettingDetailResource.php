<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class BillingAddressSettingDetailResource extends JsonResource
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
            'billing_add_setting_id' => $this->billing_add_setting_id,
            'language_id' => $this->language_id,
            'name_on_card_label' => $this->name_on_card_label,
            'web_expiry_month_label' => $this->web_expiry_month_label,
            'name_on_card_placeholder' => $this->name_on_card_placeholder,
            'mobile_indicate_required_field_label' => $this->mobile_indicate_required_field_label,
            'card_number_label' => $this->card_number_label,
            'main_heading' => $this->main_heading,
            'card_number_placeholder' => $this->card_number_placeholder,
            'mobile_card_type_label' => $this->mobile_card_type_label,
            'mobile_card_type_placholder' => $this->mobile_card_type_placholder,
            'mobile_expiry_date_label' => $this->mobile_expiry_date_label,
            'mobile_month_placeholder' => $this->mobile_month_placeholder,
            'mobile_year_placeholder' => $this->mobile_year_placeholder,
            'web_expiry_month_placeholder' => $this->web_expiry_month_placeholder,
            'security_code_label' => $this->security_code_label,
            'security_code_palceholder' => $this->security_code_palceholder,
            'mobile_billing_address_label' => $this->mobile_billing_address_label,
            'mobile_street_name_label' => $this->mobile_street_name_label,
            'mobile_street_name_placeholder' => $this->mobile_street_name_placeholder,
            'mobile_house_number_label' => $this->mobile_house_number_label,
            'mobile_house_number_placeholder' => $this->mobile_house_number_placeholder,
            'mobile_city_label' => $this->mobile_city_label,
            'mobile_city_placeholder' => $this->mobile_city_placeholder,
            'mobile_province_label' => $this->mobile_province_label,
            'mobile_province_placeholder' => $this->mobile_province_placeholder,
            'mobile_country_label' => $this->mobile_country_label,
            'mobile_country_placeholder' => $this->mobile_country_placeholder,
            'mobile_postal_code_label' => $this->mobile_postal_code_label,
            'mobile_postal_code_placeholder' => $this->mobile_postal_code_placeholder,
            'mobile_primary_card_placeholder' => $this->mobile_primary_card_placeholder,
            'save_button_text' => $this->save_button_text,
            'select_card_type_text' => $this->select_card_type_text,
            'indicate_field_label' => $this->indicate_field_label,
            'expiry_month_placeholder' => $this->expiry_month_placeholder,
            'card_number_placeholder' => $this->card_number_placeholder,
            'cvc_placeholder' => $this->cvc_placeholder,
            'card_name_placeholder' => $this->card_name_placeholder,
            // 'edit_card_button_text' => $this->edit_card_button_text,
            'language' => $this->when($this->relationLoaded('language'), function() {
                return [
                    'id' => $this->language->id ?? null,
                    'name' => $this->language->name ?? null,
                ];
            }),
        ];
    }
}
