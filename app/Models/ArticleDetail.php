<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArticleDetail extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    public static function getByLanguageWithFallback($articleId, $selectedLangId, $defaultLangId)
    {
        return self::where('article_id', $articleId)
            ->whereIn('language_id', [$selectedLangId, $defaultLangId])
            ->orderByRaw("FIELD(language_id, ?, ?)", [$selectedLangId, $defaultLangId])
            ->first();
    }
}
