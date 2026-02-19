<?php

namespace App\Exports;

use App\Models\FeaturesSetting;
use App\Models\FeaturesSettingDetail;
use App\Models\Language;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class FeaturesSettingTemplateExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithColumnWidths
{
    protected $format;

    /** @var \Illuminate\Support\Collection|null */
    protected $languages;

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

    public function __construct($format = 'single_column', $languages = null)
    {
        $this->format = $format;
        $this->languages = $languages ? collect($languages) : null;
    }

    public function collection(): Collection
    {
        if ($this->format === 'all_languages') {
            return $this->allLanguagesFormat();
        }

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

    /**
     * All-languages format: one row per field (feature_name, feature_icon), first column = field name, then one column per language.
     */
    protected function allLanguagesFormat(): Collection
    {
        $languages = $this->languages ?? Language::orderBy('id')->get();
        $features = $this->getFeatureFields();
        $slugs = array_values(array_intersect_key($this->slugMap, array_flip($features)));
        $settings = FeaturesSetting::whereIn('slug', $slugs)->with('featuresSettingDetail')->get()->keyBy('slug');

        $rows = [];
        foreach ($features as $feature) {
            $slug = $this->slugMap[$feature] ?? null;
            if (!$slug) continue;
            $setting = $settings->get($slug);
            $detailsByLang = [];
            if ($setting && $setting->relationLoaded('featuresSettingDetail')) {
                foreach ($setting->featuresSettingDetail as $d) {
                    $detailsByLang[$d->language_id] = $d;
                }
            }

            $nameRow = [$feature . '_name'];
            foreach ($languages as $lang) {
                $detail = $detailsByLang[$lang->id] ?? null;
                $nameRow[] = $detail ? ($detail->name ?? '') : '';
            }
            $rows[] = $nameRow;

            if (!in_array($feature, ['passenger_features_option4', 'passenger_features_option5', 'passenger_features_option6', 'passenger_features_option7'])) {
                $iconRow = [$feature . '_icon'];
                foreach ($languages as $lang) {
                    $detail = $detailsByLang[$lang->id] ?? null;
                    $iconRow[] = $detail ? ($detail->icon ?? '') : '';
                }
                $rows[] = $iconRow;
            }
        }
        return collect($rows);
    }

    public function headings(): array
    {
        if ($this->format === 'single_column') {
            return ['Field Name', 'Translation Value'];
        }
        if ($this->format === 'all_languages') {
            $languages = $this->languages ?? Language::orderBy('id')->get();
            return array_merge(['Field Name'], $languages->pluck('name')->toArray());
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

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 12],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ],
        ];
    }

    public function columnWidths(): array
    {
        if ($this->format === 'all_languages') {
            $totalCols = ($this->languages ?? Language::orderBy('id')->get())->count() + 1;
            $widths = [];
            for ($colIndex = 1; $colIndex <= $totalCols; $colIndex++) {
                $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
                $widths[$col] = $colIndex === 1 ? 40 : 30;
            }
            return $widths;
        }
        return [];
    }
}

