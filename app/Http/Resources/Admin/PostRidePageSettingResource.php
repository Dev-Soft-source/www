<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class PostRidePageSettingResource extends JsonResource
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
            'post_ride_page_setting_detail' => PostRidePageSettingDetailResource::collection($this->whenLoaded('postRidePageSettingDetail')),
            'post_ride_page_setting_sub_detail' => PostRidePageSettingSubDetailResource::collection($this->whenLoaded('postRidePageSettingSubDetail')),
        ];
    }
}
