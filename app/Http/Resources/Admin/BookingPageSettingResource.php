<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class BookingPageSettingResource extends JsonResource
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
            'id' => $this->id ?? null,
            'created_at' => $this->created_at ?? null,
            'booking_page_setting_detail' => $this->when(
                $this->relationLoaded('bookingPageSettingDetail'), 
                BookingPageSettingDetailResource::collection($this->bookingPageSettingDetail ?? [])
            ),
        ];
    }
}
