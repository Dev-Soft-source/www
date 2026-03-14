<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class CostSharingPageSettingResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'created_at' => $this->created_at,
            'cost_sharing_page_setting_detail' => CostSharingPageSettingDetailResource::collection($this->whenLoaded('costSharingPageSettingDetail')),
        ];
    }
}
