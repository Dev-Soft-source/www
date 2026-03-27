<?php

namespace App\Models\Traits;

use Illuminate\Support\Facades\Cache;

trait HasLanguageFallback
{
    public static function bootHasLanguageFallback(): void
    {
        static::saved(fn () => static::bustLanguageFallbackCache());
        static::deleted(fn () => static::bustLanguageFallbackCache());
    }

    public static function getByLanguageWithFallback($selectedLangId, $defaultLangId, $conditions = [])
    {
        $cachePayload = Cache::rememberForever(
            static::languageFallbackCacheKey($selectedLangId, $defaultLangId, $conditions),
            function () use ($selectedLangId, $defaultLangId, $conditions) {
                $query = static::whereIn('language_id', [$selectedLangId, $defaultLangId]);

                if (!empty($conditions)) {
                    $query->where($conditions);
                }

                $rows = $query->get()->keyBy('language_id');

                $selected = $rows[$selectedLangId] ?? null;
                $default = $rows[$defaultLangId] ?? null;

                if (!$default) {
                    return null;
                }

                if (!$selected) {
                    return $default->toArray();
                }

                $selectedData = $selected->toArray();
                $defaultData = $default->toArray();

                return array_merge(
                    $defaultData,
                    array_filter($selectedData, function ($value) {
                        return $value !== null && $value !== '';
                    })
                );
            }
        );

        return $cachePayload === null ? null : new static($cachePayload);
    }

    protected static function bustLanguageFallbackCache(): void
    {
        Cache::forever(static::languageFallbackVersionKey(), uniqid('', true));
    }

    protected static function languageFallbackCacheKey($selectedLangId, $defaultLangId, $conditions): string
    {
        $version = Cache::rememberForever(static::languageFallbackVersionKey(), fn () => '1');
        $normalizedConditions = static::normalizeLanguageFallbackConditions($conditions);

        return implode(':', [
            'language-fallback',
            str_replace('\\', '.', static::class),
            'v' . $version,
            'selected-' . (string) $selectedLangId,
            'default-' . (string) $defaultLangId,
            md5(json_encode($normalizedConditions)),
        ]);
    }

    protected static function languageFallbackVersionKey(): string
    {
        return 'language-fallback:version:' . str_replace('\\', '.', static::class);
    }

    protected static function normalizeLanguageFallbackConditions($conditions)
    {
        if (!is_array($conditions)) {
            return $conditions;
        }

        $isAssociative = array_keys($conditions) !== range(0, count($conditions) - 1);

        if ($isAssociative) {
            ksort($conditions);
        }

        foreach ($conditions as $key => $value) {
            $conditions[$key] = static::normalizeLanguageFallbackConditions($value);
        }

        return $conditions;
    }
}
