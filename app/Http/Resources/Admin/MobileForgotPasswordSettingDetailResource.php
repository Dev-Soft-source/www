<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class MobileForgotPasswordSettingDetailResource extends JsonResource
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
            'forgot_password_page_setting_id' => $this->forgot_page_id,
            'language_id' => $this->language_id,
            'main_heading' => $this->main_heading,
            'main_label' => $this->main_label,
            'button_label' => $this->button_label,
            'mobile_forgot_password_setting' => new MobileForgotPasswordSettingResource($this->whenLoaded('mobileForgotPasswordSetting')),
            'language' => new LanguageResource($this->whenLoaded('language')),
        ];
    }
}
