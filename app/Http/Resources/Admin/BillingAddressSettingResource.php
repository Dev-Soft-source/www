<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class BillingAddressSettingResource extends JsonResource
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
            'billing_address_setting_detail' => $this->when(
                $this->relationLoaded('billingAddressSettingDetail'), 
                BillingAddressSettingDetailResource::collection($this->billingAddressSettingDetail ?? [])
            ),
        ];
    }
}
