<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class SelectLocationPageSettingDetailResource extends JsonResource
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
            'select_location_page_setting_id' => $this->location_page_setting_id,
            'language_id' => $this->language_id,
            'select_location_page_setting' => new SelectLocationPageSettingResource($this->whenLoaded('selectLocationSetting')),
            'select_origin_label' => $this->select_origin_label,
            'search_origin_label' => $this->search_origin_label,
            'no_origin_label' => $this->no_origin_label,
            'select_destination_label' => $this->select_destination_label,
            'search_destination_label' => $this->search_destination_label,
            'no_destination_label' => $this->no_destination_label,
            'select_country_label' => $this->select_country_label,
            'search_country_label' => $this->search_country_label,
            'no_country_label' => $this->no_country_label,
            'select_state_label' => $this->select_state_label,
            'select_state_first_label' => $this->select_state_first_label,
            'search_state_label' => $this->search_state_label,
            'no_state_label' => $this->no_state_label,
            'select_city_label' => $this->select_city_label,
            'search_city_label' => $this->search_city_label,
            'no_city_label' => $this->no_city_label,
            'language' => new LanguageResource($this->whenLoaded('language')),
        ];
    }
}
