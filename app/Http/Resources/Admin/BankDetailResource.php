<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class BankDetailResource extends JsonResource
{
    public function toArray($request)
    {
        if ($this->resource === null) {
            return [];
        }
        return [
            'id' => $this->id,
            'random_id' => $this->random_id,
            'bank_id' => $this->bank_id,
            'bank_title' => $this->bank_title,
            'acc_no' => $this->acc_no,
            'iban' => $this->iban,
            'branch' => $this->branch,
            'address' => $this->address,
            'status' => $this->status,
            'paypal_email' => $this->paypal_email,
            'set_default' => $this->set_default,
            'bank' => $this->bank ?? null,
        ];
    }
}