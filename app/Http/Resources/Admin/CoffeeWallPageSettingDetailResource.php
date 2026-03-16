<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class CoffeeWallPageSettingDetailResource extends JsonResource
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
            'coffee_wall_page_setting_id' => $this->coffee_wall_setting_id,
            'language_id' => $this->language_id,
            'name' => $this->name,
            'meta_keywords' => $this->meta_keywords,
            'meta_description' => $this->meta_description,
            'main_heading' => $this->main_heading,
            'required_field_label' => $this->required_field_label,
            'main_text' => $this->main_text,
            'agree_terms_label' => $this->agree_terms_label,
            'agree_terms_error' => $this->agree_terms_error,
            'custom_amount_label' => $this->custom_amount_label,
            'pay_button_label' => $this->pay_button_label,
            'notify_coffee_used_label' => $this->notify_coffee_used_label,
            'select_payment_method_label' => $this->select_payment_method_label,
            'credit_card_label' => $this->credit_card_label,
            'paypal_label' => $this->paypal_label,
            'donation_acknowledgment_label' => $this->donation_acknowledgment_label,
            'frequency_label' => $this->frequency_label,
            'email_label' => $this->email_label,
            'name_label' => $this->name_label,
            'phone_label' => $this->phone_label,
            'monthly_label' => $this->monthly_label,
            'quarterly_label' => $this->quarterly_label,
            'semi_annually_label' => $this->semi_annually_label,
            'annually_label' => $this->annually_label,
            'designation_label' => $this->designation_label,
            'designation_option1' => $this->designation_option1,
            'designation_option2' => $this->designation_option2,
            'designation_option3' => $this->designation_option3,
            'designation_option4' => $this->designation_option4,
            'contact_infomation_label' => $this->contact_infomation_label,
            'faq_donors_label' => $this->faq_donors_label,
            'faq_donors_1_question' => $this->faq_donors_1_question,
            'faq_donors_1_answer' => $this->faq_donors_1_answer,
            'faq_donors_2_question' => $this->faq_donors_2_question,
            'faq_donors_2_answer' => $this->faq_donors_2_answer,
            'faq_donors_3_question' => $this->faq_donors_3_question,
            'faq_donors_3_answer' => $this->faq_donors_3_answer,
            'faq_donors_4_question' => $this->faq_donors_4_question,
            'faq_donors_4_answer' => $this->faq_donors_4_answer,
            'faq_beneficiary_label' => $this->faq_beneficiary_label,
            'faq_beneficiary_1_question' => $this->faq_beneficiary_1_question,
            'faq_beneficiary_1_answer' => $this->faq_beneficiary_1_answer,
            'faq_beneficiary_2_question' => $this->faq_beneficiary_2_question,
            'faq_beneficiary_2_answer' => $this->faq_beneficiary_2_answer,
            'faq_beneficiary_3_question' => $this->faq_beneficiary_3_question,
            'faq_beneficiary_3_answer' => $this->faq_beneficiary_3_answer,
            'faq_beneficiary_4_question' => $this->faq_beneficiary_4_question,
            'faq_beneficiary_4_answer' => $this->faq_beneficiary_4_answer,
            'language' => $this->when($this->relationLoaded('language'), function() {
                return [
                    'id' => $this->language->id ?? null,
                    'name' => $this->language->name ?? null,
                ];
            }),
        ];
    }
}
