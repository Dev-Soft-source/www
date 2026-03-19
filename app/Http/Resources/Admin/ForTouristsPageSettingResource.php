<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\Admin\ForTouristsPageSettingDetailResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ForTouristsPageSettingResource extends JsonResource
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
            'created_at' => $this->created_at,
            'for_tourists_page_setting_detail' => ForTouristsPageSettingDetailResource::collection($this->whenLoaded('forTouristsPageSettingDetail')),
        ];
    }
}
