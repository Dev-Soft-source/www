<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class ErrorPageSettingDetailResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'error_page_setting_id' => $this->error_page_setting_id,
            'language_id' => $this->language_id,
            'error_404_heading' => $this->error_404_heading,
            'error_404_paragraph_1' => $this->error_404_paragraph_1,
            'error_404_paragraph_2' => $this->error_404_paragraph_2,
            'error_404_back_home_btn' => $this->error_404_back_home_btn,
            'error_404_contact_btn' => $this->error_404_contact_btn,
            'language' => $this->when($this->relationLoaded('language'), function () {
                return $this->language ? ['id' => $this->language->id, 'name' => $this->language->name] : null;
            }),
        ];
    }
}
