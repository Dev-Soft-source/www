<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class LogoutSettingDetailResource extends JsonResource
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
            'logout_setting_id' => $this->logout_setting_id,
            'language_id' => $this->language_id,
            'confirmation_message_heading' => $this->confirmation_message_heading,
            'main_heading' => $this->main_heading,
            'confirmation_no_label' => $this->confirmation_no_label,
            'confirmation_yes_label' => $this->confirmation_yes_label,
            'logout_setting' => new LogoutSettingResource($this->whenLoaded('logoutSetting')),
            'language' => new LanguageResource($this->whenLoaded('language')),
        ];
    }
}
