<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class AdminResource extends JsonResource
{
    public function toArray($request)
    {
        if (!$this->resource) {
            return [];
        }
        
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'admin_email' => $this->admin_email,
            'password' => $this->password,
            'created_at' => $this->created_at ? date('m/d/Y H:i:s', strtotime($this->created_at)) : null,
            'updated_at' => $this->updated_at ? date('m/d/Y H:i:s', strtotime($this->updated_at)) : null,
        ];
    }
}
