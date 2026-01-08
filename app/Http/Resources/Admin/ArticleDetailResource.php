<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class ArticleDetailResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'article_id' => $this->article_id,
            'language_id' => $this->language_id,
            'title' => $this->title,
            'description' => $this->description,
            'article' => new ArticleResource($this->whenLoaded('article')),
            'language' => new LanguageResource($this->whenLoaded('language')),
        ];
    }
}