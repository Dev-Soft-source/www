<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class ForgotPasswordPageSettingDetailResource extends JsonResource
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
            'forgot_password_page_setting_id' => $this->forgot_pass_page_id,
            'language_id' => $this->language_id,
            'name' => $this->name,
            'meta_keywords' => $this->meta_keywords,
            'meta_description' => $this->meta_description,
            'main_heading' => $this->main_heading,
            'main_label' => $this->main_label,
            'email_error' => $this->email_error,
            'button_label' => $this->button_label,
            'forgot_password_page_setting' => new ForgotPasswordPageSettingResource($this->whenLoaded('forgotPasswordPageSetting')),
            'language' => new LanguageResource($this->whenLoaded('language')),
        ];
    }
}
