<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'image' => $this->image,
            'agency' => $this->agency,
            'added_by' => $this->added_by,
            'added_on' => $this->added_on,
            'article_detail' => ArticleDetailResource::collection($this->whenLoaded('articleDetail')),
        ];
    }
}