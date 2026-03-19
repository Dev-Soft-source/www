<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\Admin\CommunityGuidelinesPageSettingDetailResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CommunityGuidelinesPageSettingResource extends JsonResource
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
            'community_guidelines_page_setting_detail' => CommunityGuidelinesPageSettingDetailResource::collection($this->whenLoaded('communityGuidelinesPageSettingDetail')),
        ];
    }
}
