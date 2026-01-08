<?php

namespace App\Imports;

use App\Models\FeaturesSetting;
use App\Models\FeaturesSettingDetail;
use App\Models\Language;
use Illuminate\Support\Collection;
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

    public function __construct($languageId)
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

    public function rules(): array
    {
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

