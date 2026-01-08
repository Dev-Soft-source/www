<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class CloseAccountSettingDetailResource extends JsonResource
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
            'close_acc_setting_id' => $this->close_acc_setting_id,
            'language_id' => $this->language_id,
            'warning_text' => $this->warning_text,
            'mobile_indicate_required_field_label' => $this->mobile_indicate_required_field_label,
            'closing_account_label' => $this->closing_account_label,
            'main_heading' => $this->main_heading,
            'apply_reason_label' => $this->apply_reason_label,
            'reason_label' => $this->reason_label,
            'not_say_checkbox_label' => $this->not_say_checkbox_label,
            'check_box_validation_message' => $this->check_box_validation_message,
            'customer_service_checkbox_label' => $this->customer_service_checkbox_label,
            'technical_issue_checkbox_label' => $this->technical_issue_checkbox_label,
            'dont_use_checkbox_label' => $this->dont_use_checkbox_label,
            'another_account_checkbox_label' => $this->another_account_checkbox_label,
            'did_not_get_booking_checkbox_label' => $this->did_not_get_booking_checkbox_label,
            'did_not_find_ride_checkbox_label' => $this->did_not_find_ride_checkbox_label,
            'did_not_find_destination_checkbox_label' => $this->did_not_find_destination_checkbox_label,
            'other_checkbox_label' => $this->other_checkbox_label,
            'recommend_heading' => $this->recommend_heading,
            'yes_checkbox_label' => $this->yes_checkbox_label,
            'no_checkbox_label' => $this->no_checkbox_label,
            'prefer_not_checkbox_label' => $this->prefer_not_checkbox_label,
            'why_closing_account_label' => $this->why_closing_account_label,
            'why_closing_account_placeholder' => $this->why_closing_account_placeholder,
            'improve_label' => $this->improve_label,
            'improve_placeholder' => $this->improve_placeholder,
            'close_my_account_checkbox' => $this->close_my_account_checkbox,
            'close_my_account_checkbox_error' => $this->close_my_account_checkbox_error,
            'close_account_button_text' => $this->close_account_button_text,
            'difficulties_making_receiving_payments_label' => $this->difficulties_making_receiving_payments_label,
            'web_closing_account_reason_label' => $this->web_closing_account_reason_label,
            'web_irreversible_label' => $this->web_irreversible_label,
            'close_account_sure_message_text' => $this->close_account_sure_message_text,
            'close_it_button_label' => $this->close_it_button_label,
            'take_me_back_button_label' => $this->take_me_back_button_label,
            'language' => $this->when($this->relationLoaded('language'), function() {
                return [
                    'id' => $this->language->id ?? null,
                    'name' => $this->language->name ?? null,
                ];
            }),
        ];

    }
}
