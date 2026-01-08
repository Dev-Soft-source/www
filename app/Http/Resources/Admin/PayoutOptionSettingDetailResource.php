<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class PayoutOptionSettingDetailResource extends JsonResource
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
            'payout_opt_setting_id' => $this->payout_opt_setting_id,
            'language_id' => $this->language_id,
            'bank_detail_heading' => $this->bank_detail_heading,
            'mobile_indicate_required_field_label' => $this->mobile_indicate_required_field_label,
            'paypal_detail_heading' => $this->paypal_detail_heading,
            'main_heading' => $this->main_heading,
            'web_bank_transfer_description' => $this->web_bank_transfer_description,
            'web_paypal_transfer_description' => $this->web_paypal_transfer_description,
            'web_payout_method_label' => $this->web_payout_method_label,
            'web_payout_method_placeholder' => $this->web_payout_method_placeholder,
            'bank_name_label' => $this->bank_name_label,
            'bank_name_placeholder' => $this->bank_name_placeholder,
            'bank_title_label' => $this->bank_title_label,
            'dbank_title_placeholder' => $this->did_not_get_booking_checkbox_label,
            'account_number_label' => $this->account_number_label,
            'account_number_placeholder' => $this->account_number_placeholder,
            'branch_label' => $this->branch_label,
            'branch_placholder' => $this->branch_placholder,
            'address_label' => $this->address_label,
            'address_placeholder' => $this->address_placeholder,
            'admin_sent_amount_placeholder' => $this->admin_sent_amount_placeholder,
            'set_default_checkbox_label' => $this->set_default_checkbox_label,
            'verify_button_text' => $this->verify_button_text,
            'paypal_account_heading' => $this->paypal_account_heading,
            'paypal_account_sub_heading' => $this->paypal_account_sub_heading,
            'mobile_paypal_indicate_required_label' => $this->mobile_paypal_indicate_required_label,
            'paypal_email_label' => $this->paypal_email_label,
            'paypal_email_placeholder' => $this->paypal_email_placeholder,
            'paypal_set_default_checkbox_label' => $this->paypal_set_default_checkbox_label,
            'bank_account_heading' => $this->bank_account_heading,
            'update_btn_label' => $this->update_btn_label,
            'save_btn_label' => $this->save_btn_label,
            'institution_number_label' => $this->institution_number_label,
            'branch_address_label' => $this->branch_address_label,
            'branch_number_label' => $this->branch_number_label,
            'branch_address_placeholder' => $this->branch_address_placeholder,
            'account_address_placeholder' => $this->account_address_placeholder,
            'branch_placeholder' => $this->branch_placeholder,
            'bank_title_placeholder' => $this->bank_title_placeholder,
            'institution_number_placeholder' => $this->institution_number_placeholder,
            'branch_number_placeholder' => $this->branch_number_placeholder,


            'bank_error' => $this->bank_error,
            'institute_no_error' => $this->institute_no_error,
            'branch_error' => $this->branch_error,
            'branch_address_error' => $this->branch_address_error,
            'branch_no_error' => $this->branch_no_error,
            'bank_title_error' => $this->bank_title_error,
            'acc_no_error' => $this->acc_no_error,
            'address_error' => $this->address_error,
            
            
            'payout_option_setting' => new PayoutOptionSettingResource($this->whenLoaded('payoutOptionSetting')),
            'language' => new LanguageResource($this->whenLoaded('language')),
        ];
    }
}
