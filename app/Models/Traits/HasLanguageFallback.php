<?php

namespace App\Models\Traits;

trait HasLanguageFallback
{
    public static function getByLanguageWithFallback($selectedLangId, $defaultLangId, $conditions = [])
    {
        $query = static::whereIn('language_id', [$selectedLangId, $defaultLangId]);

        if (!empty($conditions)) {
            $query->where($conditions);
        }

        $rows = $query->get()->keyBy('language_id');

        $selected = $rows[$selectedLangId] ?? null;
        $default  = $rows[$defaultLangId] ?? null;

        if (!$default) {
            return null;
        }

        if (!$selected) {
            return $default;
        }

        $selectedData = $selected->toArray();
        $defaultData  = $default->toArray();

        $merged = array_merge(
            $defaultData,
            array_filter($selectedData, function ($value) {
                return $value !== null && $value !== '';
            })
        );
        return new static($merged);
    }
}
