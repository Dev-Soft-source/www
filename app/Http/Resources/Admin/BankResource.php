<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class BankResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'bank_to_bank_fee' => $this->bank_to_bank_fee,
            'other_bank_fee' => $this->other_bank_fee,
            'created_at' => $this->created_at,
        ];
    }
}
