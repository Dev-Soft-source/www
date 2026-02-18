<?php

namespace App\Models\Concerns;

use App\Models\FeaturesSetting;
use Illuminate\Support\Collection;

trait HasOptionGroups
{
    protected function getOptionColumns(string $prefix): array
    {
        // return collect($this->getAttributes())
        //     ->keys()
        //     ->filter(fn ($key) => preg_match('/^' . preg_quote($prefix, '/') . '_option\d+$/', $key))
        //     ->toArray();

        $suffixes = ['option', 'label']; // add all suffixes you want

        // Join them as a regex alternation: option|label|type
        $suffixPattern = implode('|', $suffixes);

        return collect($this->getAttributes())
            ->keys()
            ->filter(fn ($key) => preg_match('/^' . preg_quote($prefix, '/') . '_(' . $suffixPattern . ')\d+$/', $key))
            ->toArray();
    }

    protected function getOptionIds(string $prefix): array
    {
        return collect($this->getOptionColumns($prefix))
            ->map(fn ($column) => $this->{$column})
            ->filter()
            ->values()
            ->toArray();
    }

    public function loadOptionGroup(
    string $prefix,
    int $selectedLangId,
    int $defaultLangId
): Collection {

    $ids = $this->getOptionIds($prefix);

    if (empty($ids)) {
        return collect();
    }

    $settings = FeaturesSetting::whereIn('id', $ids)
        ->with(['featuresSettingDetail' => function ($query) use ($selectedLangId, $defaultLangId) {
            $query->whereIn('language_id', [$selectedLangId, $defaultLangId])
                  ->orderByRaw("FIELD(language_id, ?, ?)", [$selectedLangId, $defaultLangId]);
        }])
        ->get();

    return $settings->map(function ($setting) use ($selectedLangId, $defaultLangId) {
        // Group details by language_id
        $detailsByLang = $setting->featuresSettingDetail->keyBy('language_id');

        $selected = $detailsByLang->get($selectedLangId);
        $default  = $detailsByLang->get($defaultLangId);

        if (!$selected) {
            // fallback entirely to default
            return $default;
        }

        if (!$default) {
            // no default available, just return selected as-is
            return $selected;
        }

        // Merge fields: if selected field is null or empty string, take from default
        foreach ($default->getAttributes() as $key => $value) {
            if (!isset($selected->$key) || $selected->$key === '' || $selected->$key === null) {
                $selected->$key = $value;
            }
        }

        return $selected;
    })->filter()->values();
}


    public function mapOptionColumnsToDetails(string $prefix, $selectedLangId, $defaultLangId)
    {
        $collection = $this->loadOptionGroup($prefix, $selectedLangId, $defaultLangId);

        foreach ($this->getOptionColumns($prefix) as $column) {
            $id = $this->{$column};
            $this->{$column} = $collection->firstWhere('features_setting_id', $id);
        }

        return $collection; // optional
    }
}
