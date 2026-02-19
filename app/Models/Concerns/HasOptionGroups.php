<?php

namespace App\Models\Concerns;

use App\Models\FeaturesSetting;
use Illuminate\Support\Collection;

trait HasOptionGroups
{
    protected function getOptionColumns(string $prefix, bool $is_prefix = true): array
    {
        // return collect($this->getAttributes())
        //     ->keys()
        //     ->filter(fn ($key) => preg_match('/^' . preg_quote($prefix, '/') . '_option\d+$/', $key))
        //     ->toArray();
        if ($is_prefix) {

            $suffixes = ['option', 'label']; // add all suffixes you want

            // Join them as a regex alternation: option|label|type
            $suffixPattern = implode('|', $suffixes);

            return collect($this->getAttributes())
                ->keys()
                ->filter(fn($key) => preg_match('/^' . preg_quote($prefix, '/') . '_(' . $suffixPattern . ')\d+$/', $key))
                ->toArray();
        } else {
            return collect($this->getAttributes())
                ->keys()
                ->filter(fn ($key) => $key === $prefix)
                ->toArray();
        }
    }

    protected function getOptionIds(string $prefix, bool $is_prefix = true): array
    {
        return collect($this->getOptionColumns($prefix, $is_prefix))
            ->map(fn($column) => $this->{$column})
            ->filter()
            ->values()
            ->toArray();
    }

    public function loadOptionGroup(
        string $prefix,
        int $selectedLangId,
        int $defaultLangId,
        bool $is_prefix = true
    ): Collection {

        $ids = $this->getOptionIds($prefix, $is_prefix);

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


    public function mapOptionColumnsToDetails(string $prefix, $selectedLangId, $defaultLangId, $is_prefix = true)
    {
        $collection = $this->loadOptionGroup($prefix, $selectedLangId, $defaultLangId, $is_prefix);

        foreach ($this->getOptionColumns($prefix, $is_prefix) as $column) {
            $id = $this->{$column};
            $this->{$column} = $collection->firstWhere('features_setting_id', $id);
        }

        return $collection; // optional
    }

    /**
     * Optimized method to batch load multiple option groups in a single query
     * instead of multiple separate queries.
     * 
     * @param array $prefixes Array of prefixes to load (e.g., ['luggage', 'payment_method'])
     * @param int $selectedLangId Selected language ID
     * @param int $defaultLangId Default language ID
     * @param bool $is_prefix Whether the columns use prefix pattern (true) or exact match (false)
     * @return Collection Collection of all loaded option groups
     */
    public function mapMultipleOptionColumnsToDetails(
        array $prefixes,
        int $selectedLangId,
        int $defaultLangId,
        bool $is_prefix = true
    ): Collection {
        $allIds = [];
        $columnToId = [];

        // Collect all IDs from all prefixes
        foreach ($prefixes as $prefix) {
            $columns = $this->getOptionColumns($prefix, $is_prefix);
            foreach ($columns as $column) {
                $id = $this->{$column};
                if ($id) {
                    $allIds[] = $id;
                    $columnToId[$column] = $id;
                }
            }
        }

        // Single query to load all option groups
        if (empty($allIds)) {
            return collect();
        }

        $allIds = array_unique($allIds);
        $settings = FeaturesSetting::whereIn('id', $allIds)
            ->with(['featuresSettingDetail' => function ($query) use ($selectedLangId, $defaultLangId) {
                $query->whereIn('language_id', [$selectedLangId, $defaultLangId])
                    ->orderByRaw("FIELD(language_id, ?, ?)", [$selectedLangId, $defaultLangId]);
            }])
            ->get();

        // Process and cache results by ID
        $settingsMap = $settings->mapWithKeys(function ($setting) use ($selectedLangId, $defaultLangId) {
            $detailsByLang = $setting->featuresSettingDetail->keyBy('language_id');
            $selected = $detailsByLang->get($selectedLangId);
            $default = $detailsByLang->get($defaultLangId);

            if (!$selected) {
                return [$setting->id => $default];
            }
            if (!$default) {
                return [$setting->id => $selected];
            }

            // Merge fields: if selected field is null or empty string, take from default
            foreach ($default->getAttributes() as $key => $value) {
                if (!isset($selected->$key) || $selected->$key === '' || $selected->$key === null) {
                    $selected->$key = $value;
                }
            }

            return [$setting->id => $selected];
        })->filter();

        // Map back to model properties
        foreach ($columnToId as $column => $id) {
            if ($settingsMap->has($id)) {
                $this->{$column} = $settingsMap->get($id);
            }
        }

        return $settingsMap->values();
    }
}
