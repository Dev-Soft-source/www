<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class HomePageSettingResource extends JsonResource
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
            'facebook_image_path' => $this->facebook_image_path,
            'created_at' => $this->created_at,
            'home_page_setting_detail' => HomePageSettingDetailResource::collection($this->whenLoaded('homePageSettingDetail')),
        ];
    }
}
