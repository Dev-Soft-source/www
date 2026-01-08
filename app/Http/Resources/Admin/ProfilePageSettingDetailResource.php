<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class ProfilePageSettingDetailResource extends JsonResource
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
            'profile_page_setting_id' => $this->profile_page_setting_id,
            'language_id' => $this->language_id,
            'name' => $this->name,
            'profile_setting_label' => $this->profile_setting_label,
            'my_wallet_label' => $this->my_wallet_label,
            'main_heading' => $this->main_heading,
            'payment_options_label' => $this->payment_options_label,
            'payout_options_label' => $this->payout_options_label,
            'my_reviews_label' => $this->my_reviews_label,
            'terms_condition_label' => $this->terms_condition_label,
            'privacy_policy_label' => $this->privacy_policy_label,
            'terms_of_use_label' => $this->terms_of_use_label,
            'refund_policy_label' => $this->refund_policy_label,
            'cancellation_policy_label' => $this->cancellation_policy_label,
            'dispute_policy_label' => $this->dispute_policy_label,
            'contact_proximaride_label' => $this->contact_proximaride_label,
            'logout_label' => $this->logout_label,
            'colse_your_contact_label' => $this->colse_your_contact_label,
            'profile_page_setting' => new ProfilePageSettingResource($this->whenLoaded('profilePageSetting')),
            'language' => new LanguageResource($this->whenLoaded('language')),
        ];
    }
}
