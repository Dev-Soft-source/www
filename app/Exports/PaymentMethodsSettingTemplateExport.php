<?php

namespace App\Exports;

use App\Models\Language;
use App\Models\FeaturesSetting;
use App\Models\FeaturesSettingDetail;
use App\Models\PostRidePageSettingDetail;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class PaymentMethodsSettingTemplateExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths
{
    protected $format;

    /** @var \Illuminate\Support\Collection|null */
    protected $languages;

    public function __construct($format = 'single_column', $languages = null, $existingData = null)
    {
        $this->format = $format;
        $this->languages = $languages ? collect($languages) : null;
    }

    public static function getTranslatableFieldsWithDefaults(): array
    {
        $fields = [
            'booking_option1','booking_option1_tooltip','booking_option1_icon',
            'booking_option2','booking_option2_tooltip','booking_option2_icon',
            'cancellation_policy_label1','cancellation_policy_label1_tooltip',
            'cancellation_policy_label2','cancellation_policy_label2_tooltip',
            'payment_methods_option1','payment_methods_option1_tooltip','payment_methods_option1_icon',
            'payment_methods_option2','payment_methods_option2_tooltip','payment_methods_option2_icon',
            'payment_methods_option3','payment_methods_option3_tooltip','payment_methods_option3_icon',
            'vehicle_type_convertible_text','vehicle_type_hatchback_text','vehicle_type_coupe_text','vehicle_type_minivan_text','vehicle_type_sedan_text','vehicle_type_station_wagon_text','vehicle_type_suv_text','vehicle_type_truck_text','vehicle_type_van_text',
        ];
        return array_fill_keys($fields, '');
    }

    public function collection(): Collection
    {
        if ($this->format === 'single_column') return $this->singleColumnFormat();
        if ($this->format === 'all_languages') return $this->allLanguagesFormat();
        return $this->multiColumnFormat();
    }

    protected function singleColumnFormat(): Collection
    {
        $prefill = $this->getPrefillDefaults();
        $fields = static::getTranslatableFieldsWithDefaults();
        $data = [];
        foreach ($fields as $field => $default) {
            $data[] = ['field_name' => $field, 'translation_value' => $prefill[$field] ?? $default];
        }
        return new Collection($data);
    }

    protected function allLanguagesFormat(): Collection
    {
        $languages = $this->languages ?? Language::orderBy('id')->get();
        $fields = array_keys(static::getTranslatableFieldsWithDefaults());
        $slugIds = FeaturesSetting::whereIn('slug', [
            'instant','manual','cash','online','secured','standard','firm',
            'convertible','hatchback','coupe','minivan','sedan','station_wagon','suv','truck','van'
        ])->pluck('id', 'slug')->toArray();
        $detailsByFeatureLang = FeaturesSettingDetail::whereIn('features_setting_id', array_values($slugIds))
            ->get()
            ->groupBy('features_setting_id')
            ->map(fn ($items) => $items->keyBy('language_id'));
        $featureIdToSlug = array_flip($slugIds);
        $postDetailsByLang = PostRidePageSettingDetail::all()->keyBy('language_id');

        $fieldToSource = $this->getFieldToSourceMap();

        $rows = [];
        foreach ($fields as $fieldKey) {
            $row = [$fieldKey];
            foreach ($languages as $lang) {
                $value = $this->getValueForFieldAndLanguage(
                    $fieldKey,
                    $lang->id,
                    $fieldToSource,
                    $slugIds,
                    $detailsByFeatureLang,
                    $featureIdToSlug,
                    $postDetailsByLang
                );
                $row[] = $value;
            }
            $rows[] = $row;
        }
        return collect($rows);
    }

    protected function getFieldToSourceMap(): array
    {
        return [
            'booking_option1' => ['type' => 'feature_name', 'slug' => 'instant'],
            'booking_option1_tooltip' => ['type' => 'post_ride', 'column' => 'booking_option1_tooltip'],
            'booking_option1_icon' => ['type' => 'feature_icon', 'slug' => 'instant'],
            'booking_option2' => ['type' => 'feature_name', 'slug' => 'manual'],
            'booking_option2_tooltip' => ['type' => 'post_ride', 'column' => 'booking_option2_tooltip'],
            'booking_option2_icon' => ['type' => 'feature_icon', 'slug' => 'manual'],
            'cancellation_policy_label1' => ['type' => 'feature_name', 'slug' => 'standard'],
            'cancellation_policy_label1_tooltip' => ['type' => 'post_ride', 'column' => 'cancellation_policy_label1_tooltip'],
            'cancellation_policy_label2' => ['type' => 'feature_name', 'slug' => 'firm'],
            'cancellation_policy_label2_tooltip' => ['type' => 'post_ride', 'column' => 'cancellation_policy_label2_tooltip'],
            'payment_methods_option1' => ['type' => 'feature_name', 'slug' => 'cash'],
            'payment_methods_option1_tooltip' => ['type' => 'post_ride', 'column' => 'payment_methods_option1_tooltip'],
            'payment_methods_option1_icon' => ['type' => 'feature_icon', 'slug' => 'cash'],
            'payment_methods_option2' => ['type' => 'feature_name', 'slug' => 'online'],
            'payment_methods_option2_tooltip' => ['type' => 'post_ride', 'column' => 'payment_methods_option2_tooltip'],
            'payment_methods_option2_icon' => ['type' => 'feature_icon', 'slug' => 'online'],
            'payment_methods_option3' => ['type' => 'feature_name', 'slug' => 'secured'],
            'payment_methods_option3_tooltip' => ['type' => 'post_ride', 'column' => 'payment_methods_option3_tooltip'],
            'payment_methods_option3_icon' => ['type' => 'feature_icon', 'slug' => 'secured'],
            'vehicle_type_convertible_text' => ['type' => 'feature_name', 'slug' => 'convertible'],
            'vehicle_type_hatchback_text' => ['type' => 'feature_name', 'slug' => 'hatchback'],
            'vehicle_type_coupe_text' => ['type' => 'feature_name', 'slug' => 'coupe'],
            'vehicle_type_minivan_text' => ['type' => 'feature_name', 'slug' => 'minivan'],
            'vehicle_type_sedan_text' => ['type' => 'feature_name', 'slug' => 'sedan'],
            'vehicle_type_station_wagon_text' => ['type' => 'feature_name', 'slug' => 'station_wagon'],
            'vehicle_type_suv_text' => ['type' => 'feature_name', 'slug' => 'suv'],
            'vehicle_type_truck_text' => ['type' => 'feature_name', 'slug' => 'truck'],
            'vehicle_type_van_text' => ['type' => 'feature_name', 'slug' => 'van'],
        ];
    }

    protected function getValueForFieldAndLanguage(string $fieldKey, int $languageId, array $fieldToSource, array $slugIds, $detailsByFeatureLang, array $featureIdToSlug, $postDetailsByLang): string
    {
        $source = $fieldToSource[$fieldKey] ?? null;
        if (!$source) return '';

        if ($source['type'] === 'feature_name' || $source['type'] === 'feature_icon') {
            $slug = $source['slug'];
            $featureId = $slugIds[$slug] ?? null;
            if (!$featureId) return '';
            $byLang = $detailsByFeatureLang[$featureId] ?? null;
            if (!$byLang) return '';
            $detail = $byLang[$languageId] ?? null;
            if (!$detail) return '';
            return $source['type'] === 'feature_icon' ? ($detail->icon ?? '') : ($detail->name ?? '');
        }

        if ($source['type'] === 'post_ride') {
            $post = $postDetailsByLang[$languageId] ?? null;
            if (!$post || !isset($post->{$source['column']})) return '';
            return (string) $post->{$source['column']};
        }

        return '';
    }

    protected function multiColumnFormat(): Collection
    {
        $prefill = $this->getPrefillDefaults();
        $fields = static::getTranslatableFieldsWithDefaults();
        $row = [];
        foreach ($fields as $field => $default) {
            $row[$field] = $prefill[$field] ?? $default;
        }
        return new Collection([$row]);
    }

    public function headings(): array
    {
        if ($this->format === 'single_column') return ['Field Name', 'Translation Value'];
        if ($this->format === 'all_languages') {
            $languages = $this->languages ?? Language::orderBy('id')->get();
            return array_merge(['Field Name'], $languages->pluck('name')->toArray());
        }
        return array_map(fn ($f) => ucwords(str_replace('_', ' ', $f)), array_keys(static::getTranslatableFieldsWithDefaults()));
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
        if ($this->format === 'single_column') return ['A' => 40, 'B' => 80];
        if ($this->format === 'all_languages') {
            $totalCols = ($this->languages ?? Language::orderBy('id')->get())->count() + 1;
            $widths = [];
            for ($colIndex = 1; $colIndex <= $totalCols; $colIndex++) {
                $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
                $widths[$col] = $colIndex === 1 ? 40 : 25;
            }
            return $widths;
        }
        return ['A' => 40, 'B' => 25];
    }

    protected function getFields(): array
    {
        return array_keys(static::getTranslatableFieldsWithDefaults());
    }

    protected function getPrefillDefaults(): array
    {
        $defaults = array_fill_keys($this->getFields(), '');
        $defaultLang = Language::where('is_default', '1')->first();
        if (!$defaultLang) return $defaults;

        $slugToField = [
            'instant' => 'booking_option1_icon',
            'manual' => 'booking_option2_icon',
            'cash' => 'payment_methods_option1_icon',
            'online' => 'payment_methods_option2_icon',
            'secured' => 'payment_methods_option3_icon',
        ];
        foreach ($slugToField as $slug => $field) {
            $fs = FeaturesSetting::where('slug', $slug)->first();
            if (!$fs) continue;
            $detail = FeaturesSettingDetail::where('features_setting_id', $fs->id)
                ->where('language_id', $defaultLang->id)->first();
            if ($detail && $detail->icon) $defaults[$field] = $detail->icon;
        }
        return $defaults;
    }
}


