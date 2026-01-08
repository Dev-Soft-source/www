<?php

namespace App\Exports;

use App\Models\FeaturesSetting;
use App\Models\FeaturesSettingDetail;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class FeaturesSettingTemplateExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $format;

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

    public function __construct($format = 'single_column')
    {
        $this->format = $format;
    }

    public function collection(): Collection
    {
        $defaultLang = \App\Models\Language::where('is_default', '1')->first();
        $features = $this->getFeatureFields();

        if ($this->format === 'single_column') {
            $rows = [];
            foreach ($features as $feature) {
                $nameField = $feature . '_name';
                $rows[] = ['field_name' => $nameField, 'translation_value' => ''];
                
                // Only add icon field if not a passenger feature 4-7
                if (!in_array($feature, ['passenger_features_option4', 'passenger_features_option5', 'passenger_features_option6', 'passenger_features_option7'])) {
                    $iconField = $feature . '_icon';
                    $iconValue = $this->getDefaultIconValue($feature, $defaultLang?->id);
                    $rows[] = ['field_name' => $iconField, 'translation_value' => $iconValue ?? ''];
                }
            }
            return new Collection($rows);
        }

        // Multi-column format
        $row = [];
        foreach ($features as $feature) {
            $nameField = $feature . '_name';
            $row[$nameField] = '';
            
            if (!in_array($feature, ['passenger_features_option4', 'passenger_features_option5', 'passenger_features_option6', 'passenger_features_option7'])) {
                $iconField = $feature . '_icon';
                $row[$iconField] = $this->getDefaultIconValue($feature, $defaultLang?->id) ?? '';
            }
        }
        return new Collection([$row]);
    }

    public function headings(): array
    {
        if ($this->format === 'single_column') {
            return ['Field Name', 'Translation Value'];
        }

        $headings = [];
        foreach ($this->getFeatureFields() as $feature) {
            $headings[] = ucwords(str_replace('_', ' ', $feature)) . ' Name';
            if (!in_array($feature, ['passenger_features_option4', 'passenger_features_option5', 'passenger_features_option6', 'passenger_features_option7'])) {
                $headings[] = ucwords(str_replace('_', ' ', $feature)) . ' Icon';
            }
        }
        return $headings;
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

    protected function getDefaultIconValue($feature, $defaultLangId)
    {
        if (!$defaultLangId || in_array($feature, ['passenger_features_option4', 'passenger_features_option5', 'passenger_features_option6', 'passenger_features_option7'])) {
            return null;
        }

        $slug = $this->slugMap[$feature] ?? null;
        if (!$slug) return null;

        $featureSetting = FeaturesSetting::whereSlug($slug)->first();
        if (!$featureSetting) return null;

        $detail = FeaturesSettingDetail::whereFeaturesSettingId($featureSetting->id)
            ->whereLanguageId($defaultLangId)
            ->first();

        return $detail?->icon;
    }
}

