<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class ThankyouPageSettingDetailResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'thankyou_page_setting_id' => $this->thankyou_page_setting_id,
            'language_id' => $this->language_id,
            'name' => $this->name,
            'meta_keywords' => $this->meta_keywords,
            'meta_description' => $this->meta_description,
            'forget_close_btn_label' => $this->forget_close_btn_label,
            'forget_password_message' => $this->forget_password_message,
            'rest_password_btn_label' => $this->rest_password_btn_label,
            'good_bye_btn_label' => $this->good_bye_btn_label,
            'close_account_message' => $this->close_account_message,
            'account_close_heading' => $this->account_close_heading,
            'login_btn_label' => $this->login_btn_label,
            'done_btn_label' => $this->done_btn_label,
            'instant_booking_message' => $this->instant_booking_message,
            'manual_booking_message' => $this->manual_booking_message,
            'top_up_message' => $this->top_up_message,
            'language' => $this->when($this->relationLoaded('language'), function() {
                return [
                    'id' => $this->language->id ?? null,
                    'name' => $this->language->name ?? null,
                ];
            }),
        ];
    }
}
