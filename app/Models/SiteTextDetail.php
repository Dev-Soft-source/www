<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class SiteTextDetail extends Model
{
    use HasFactory;
    private const CACHE_VERSION_KEY = 'site-text-detail:keyed-by-slug:version';

    public $table = "site_text_detail";

    protected $fillable = [
        'slug_id',
        'language_id',
        'name',
        'icon',
    ];

    protected static function booted(): void
    {
        static::saved(fn () => static::bustKeyedBySlugCache());
        static::deleted(fn () => static::bustKeyedBySlugCache());
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    public function siteText(): BelongsTo
    {
        return $this->belongsTo(SiteText::class, 'slug_id');
    }

    /**
     * Get all SiteTextDetail records for a specific language, organized by SiteText slug
     * Returns an array like [slug => detail->name]
     * Includes all SiteText records, even if no SiteTextDetail exists for the language
     * Falls back to default language (language_id = 1) if name is null or empty
     *
     * @param int $languageId
     * @return array
     */
    public static function getByLanguageKeyedBySlug(int $languageId, int $defaultLanguageId): array
    {
        $cacheKey = implode(':', [
            'site-text-detail:keyed-by-slug',
            'v' . static::getCacheVersion(),
            'language-' . $languageId,
            'default-' . $defaultLanguageId,
        ]);

        return Cache::rememberForever($cacheKey, function () use ($languageId, $defaultLanguageId) {
            $siteTexts = SiteText::all()->keyBy('id');

            $details = static::whereIn('language_id', [$languageId, $defaultLanguageId])
                ->with('siteText')
                ->get()
                ->groupBy('slug_id');

            $result = [];
            foreach ($siteTexts as $siteText) {
                $slugDetails = $details->get($siteText->id);

                if ($slugDetails) {
                    $detail = $slugDetails->firstWhere('language_id', $languageId);
                    $defaultDetail = $slugDetails->firstWhere('language_id', $defaultLanguageId);

                    $name = null;
                    if ($detail && !empty($detail->name)) {
                        $name = $detail->name;
                    } elseif ($defaultDetail && !empty($defaultDetail->name)) {
                        $name = $defaultDetail->name;
                    }

                    $result[$siteText->slug] = $name;
                } else {
                    $result[$siteText->slug] = null;
                }
            }

            return $result;
        });
    }

    public static function bustKeyedBySlugCache(): void
    {
        Cache::forever(self::CACHE_VERSION_KEY, uniqid('', true));
    }

    protected static function getCacheVersion(): string
    {
        return (string) Cache::rememberForever(self::CACHE_VERSION_KEY, fn () => '1');
    }
}
