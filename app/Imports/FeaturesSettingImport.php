<?php

namespace App\Imports;

use App\Models\FeaturesSetting;
use App\Models\FeaturesSettingDetail;
use App\Models\Language;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class FeaturesSettingImport implements ToCollection, WithHeadingRow, WithValidation
{
    protected $languageId;

    protected $slugMap = [
        'features_option1' => 'pink_rides',
        'features_option2' => 'extra_care_rides',
        'features_option3' => 'wi_fi',
        'driver_features_option4' => 'driver_features_option4',
        'driver_features_option5' => 'driver_features_option5',
        'driver_features_option6' => 'driver_features_option6',
        'driver_features_option7' => 'driver_features_option7',
        'features_option8' => 'heating',
        'features_option9' => 'ac',
        'features_option10' => 'bike_rack',
        'features_option11' => 'ski_rack',
        'features_option12' => 'winter_tires',
        'features_option13' => 'star5_passenger',
        'features_option14' => 'star4_passenger',
        'features_option15' => 'star3_passenger',
        'features_option16' => 'with_review_passenger',
        'passenger_features_option4' => 'passenger_features_option4',
        'passenger_features_option5' => 'passenger_features_option5',
        'passenger_features_option6' => 'passenger_features_option6',
        'passenger_features_option7' => 'passenger_features_option7',
    ];

    /** @var int|null When set, import is for a single language. When null, import is all_languages format. */
    public function __construct($languageId = null)
    {
        $this->languageId = $languageId;
    }

    protected function getFeatureFields(): array
    {
        return [
            'features_option1',
            'features_option2',
            'features_option3',
            'driver_features_option4',
            'driver_features_option5',
            'driver_features_option6',
            'driver_features_option7',
            'features_option8',
            'features_option9',
            'features_option10',
            'features_option11',
            'features_option12',
            'features_option13',
            'features_option14',
            'features_option15',
            'features_option16',
            'passenger_features_option4',
            'passenger_features_option5',
            'passenger_features_option6',
            'passenger_features_option7',
        ];
    }

    public function collection(Collection $rows)
    {
        if ($rows->isEmpty()) return;
        $firstRow = $rows->first();
        $keys = array_keys($firstRow->toArray());

        $isAllLanguages = $this->languageId === null
            && (in_array('field_name', $keys) || in_array('field name', $keys))
            && count($keys) > 1;

        if ($isAllLanguages) {
            $this->processAllLanguagesFormat($rows);
            return;
        }

        $isSingle = isset($keys[0]) && in_array('field_name', $keys) && (in_array('value', $keys) || in_array('translation_value', $keys));

        $data = [];
        if ($isSingle) {
            foreach ($rows as $row) {
                $fieldName = strtolower(trim($row['field_name'] ?? ''));
                $value = $row['translation_value'] ?? $row['value'] ?? null;
                
                // Handle format: features_option1_name, features_option1_icon
                if (preg_match('/^(.+)_(name|icon)$/', $fieldName, $matches)) {
                    $featureKey = $matches[1];
                    $fieldType = $matches[2];
                    if (!isset($data[$featureKey])) {
                        $data[$featureKey] = [];
                    }
                    $data[$featureKey][$fieldType] = $value;
                } elseif (in_array($fieldName, $this->getFeatureFields())) {
                    // Legacy format: just the feature name means the name field
                    if (!isset($data[$fieldName])) {
                        $data[$fieldName] = [];
                    }
                    $data[$fieldName]['name'] = $value;
                }
            }
        } else {
            // Multi-column format
            foreach ($this->getFeatureFields() as $feature) {
                $nameKey = $feature . '_name';
                $iconKey = $feature . '_icon';
                $data[$feature] = [
                    'name' => $firstRow[$nameKey] ?? $firstRow[$feature] ?? null,
                    'icon' => $firstRow[$iconKey] ?? null,
                ];
            }
        }

        // Process each feature
        foreach ($this->getFeatureFields() as $feature) {
            if (!isset($data[$feature])) continue;
            
            $slug = $this->slugMap[$feature] ?? null;
            if (!$slug) continue;

            $featureSetting = FeaturesSetting::whereSlug($slug)->first();
            if (!$featureSetting) {
                $featureSetting = FeaturesSetting::create(['slug' => $slug]);
            }

            $name = $data[$feature]['name'] ?? null;
            $icon = $data[$feature]['icon'] ?? null;

            // Passenger features 4-7 don't have icons
            if (in_array($feature, ['passenger_features_option4', 'passenger_features_option5', 'passenger_features_option6', 'passenger_features_option7'])) {
                FeaturesSettingDetail::updateOrCreate(
                    ['features_setting_id' => $featureSetting->id, 'language_id' => $this->languageId],
                    ['name' => $name]
                );
            } else {
                FeaturesSettingDetail::updateOrCreate(
                    ['features_setting_id' => $featureSetting->id, 'language_id' => $this->languageId],
                    ['name' => $name, 'icon' => $icon]
                );
            }
        }
    }

    /**
     * Process all_languages format: each row = one field (feature_name or feature_icon), columns = Field Name, then one per language.
     */
    protected function processAllLanguagesFormat(Collection $rows): void
    {
        $firstRow = $rows->first();
        $headers = array_keys($firstRow->toArray());
        $fieldNameKey = in_array('field_name', $headers) ? 'field_name' : 'field name';
        $languageColumns = array_diff($headers, [$fieldNameKey]);

        $languages = Language::orderBy('id')->get();
        $nameToId = $languages->mapWithKeys(fn ($lang) => [Str::lower($lang->name) => $lang->id])->toArray();
        $validFeatures = $this->getFeatureFields();

        foreach ($rows as $row) {
            $row = $row->toArray();
            $fieldName = $row[$fieldNameKey] ?? null;
            if (empty($fieldName)) continue;

            $fieldName = strtolower(trim((string) $fieldName));
            $feature = null;
            $type = null;
            if (preg_match('/^(.+)_name$/', $fieldName, $m)) {
                $feature = $m[1];
                $type = 'name';
            } elseif (preg_match('/^(.+)_icon$/', $fieldName, $m)) {
                $feature = $m[1];
                $type = 'icon';
            }
            if (!$feature || !in_array($feature, $validFeatures, true)) continue;
            if ($type === 'icon' && in_array($feature, ['passenger_features_option4', 'passenger_features_option5', 'passenger_features_option6', 'passenger_features_option7'])) continue;

            $slug = $this->slugMap[$feature] ?? null;
            if (!$slug) continue;

            $featureSetting = FeaturesSetting::whereSlug($slug)->first();
            if (!$featureSetting) {
                $featureSetting = FeaturesSetting::create(['slug' => $slug]);
            }

            foreach ($languageColumns as $col) {
                $langKey = Str::lower(trim((string) $col));
                if (!isset($nameToId[$langKey])) continue;
                $languageId = $nameToId[$langKey];
                $value = $row[$col] ?? null;

                $detail = FeaturesSettingDetail::firstOrCreate(
                    ['features_setting_id' => $featureSetting->id, 'language_id' => $languageId],
                    $type === 'name' ? ['name' => $value] : ['icon' => $value]
                );
                if (!$detail->wasRecentlyCreated) {
                    if ($type === 'name') $detail->name = $value;
                    else $detail->icon = $value;
                    $detail->save();
                }
            }
        }
    }

    public function rules(): array
    {
        if ($this->languageId === null) {
            return [];
        }
        $language = Language::find($this->languageId);
        if (!$language || $language->is_default != '1') return [];
        
        $rules = [];
        foreach ($this->getFeatureFields() as $feature) {
            if (in_array($feature, ['passenger_features_option4', 'passenger_features_option5', 'passenger_features_option6', 'passenger_features_option7'])) {
                $rules[$feature . '_name'] = 'required|string';
            } else {
                $rules[$feature . '_name'] = 'required|string';
            }
        }
        return $rules;
    }
}

