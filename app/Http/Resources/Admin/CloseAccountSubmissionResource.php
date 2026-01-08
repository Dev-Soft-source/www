<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class CloseAccountSubmissionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'reasons' => $this->reasons,
            'recommend' => $this->recommend,
            'improve_message' => $this->improve_message,
            'close_account_reason' => $this->close_account_reason,
            'created_at' => date('m/d/Y H:i:s', strtotime($this->created_at)),
            'updated_at' => date('m/d/Y H:i:s', strtotime($this->updated_at)),
        ];
    }
}
