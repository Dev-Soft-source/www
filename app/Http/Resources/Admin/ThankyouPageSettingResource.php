<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class ThankyouPageSettingResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'created_at' => $this->created_at,
            'thankyou_page_setting_detail' => ThankyouPageSettingDetailResource::collection($this->whenLoaded('thankyouPageSettingDetail')),
        ];
    }
}
