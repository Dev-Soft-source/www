<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class BookingCreditResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'credits_buy' => $this->credits_buy,
            'credits_get' => $this->credits_get,
            'credits_price' => $this->credits_price,
        ];
    }
}