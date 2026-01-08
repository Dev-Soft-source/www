<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class ReferralPageSettingDetailResource extends JsonResource
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
            'referral_page_setting_id' => $this->referral_page_setting_id,
            'language_id' => $this->language_id,
            'name' => $this->name,
            'meta_keywords' => $this->meta_keywords,
            'meta_description' => $this->meta_description,
            'main_heading' => $this->main_heading,
            'your_referral_url_label' => $this->your_referral_url_label,
            'referral_description' => $this->referral_description,
            'my_referral_text' => $this->my_referral_text,
            'account_id_label' => $this->account_id_label,
            'user_label' => $this->user_label,
            'registered_on_label' => $this->registered_on_label,
            'no_referral_user_found_message' => $this->no_referral_user_found_message,
            'language' => $this->when($this->relationLoaded('language'), function() {
                return [
                    'id' => $this->language->id ?? null,
                    'name' => $this->language->name ?? null,
                ];
            }),
        ];
    }
}
