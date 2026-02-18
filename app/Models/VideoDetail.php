<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VideoDetail extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function video(): BelongsTo
    {
        return $this->belongsTo(Video::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    public static function getByLanguageWithFallback($videoId, $selectedLangId, $defaultLangId)
    {
        // First try selected language with non-null link
        $video = self::where('video_id', $videoId)
            ->where('language_id', $selectedLangId)
            ->whereNotNull('link')
            ->first();

        if ($video) {
            return $video;
        }

        // Fallback to default language
        return self::where('video_id', $videoId)
            ->where('language_id', $defaultLangId)
            ->whereNotNull('link')
            ->first();
    }
}
