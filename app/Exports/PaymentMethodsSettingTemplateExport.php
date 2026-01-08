<?php

namespace App\Exports;

use App\Models\Language;
use App\Models\FeaturesSetting;
use App\Models\FeaturesSettingDetail;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PaymentMethodsSettingTemplateExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $format;

    public function __construct($format = 'single_column')
    {
        $this->format = $format;
    }

    public function collection(): Collection
    {
        $fields = $this->getFields();

        // Prefill icon fields from English (default) language if available
        $prefill = $this->getPrefillDefaults();

        if ($this->format === 'single_column') {
            $rows = [];
            foreach ($fields as $field) {
                $rows[] = ['field_name' => $field, 'translation_value' => $prefill[$field] ?? ''];
            }
            return new Collection($rows);
        }
        $row = [];
        foreach ($fields as $field) { $row[$field] = $prefill[$field] ?? ''; }
        return new Collection([$row]);
    }

    public function headings(): array
    {
        if ($this->format === 'single_column') return ['Field Name', 'Translation Value'];
        return array_map(fn($f) => ucwords(str_replace('_', ' ', $f)), $this->getFields());
    }

    protected function getFields(): array
    {
        return [
            'booking_option1','booking_option1_tooltip','booking_option1_icon',
            'booking_option2','booking_option2_tooltip','booking_option2_icon',
            'cancellation_policy_label1','cancellation_policy_label1_tooltip',
            'cancellation_policy_label2','cancellation_policy_label2_tooltip',
            'payment_methods_option1','payment_methods_option1_tooltip','payment_methods_option1_icon',
            'payment_methods_option2','payment_methods_option2_tooltip','payment_methods_option2_icon',
            'payment_methods_option3','payment_methods_option3_tooltip','payment_methods_option3_icon',
            'vehicle_type_convertible_text','vehicle_type_hatchback_text','vehicle_type_coupe_text','vehicle_type_minivan_text','vehicle_type_sedan_text','vehicle_type_station_wagon_text','vehicle_type_suv_text','vehicle_type_truck_text','vehicle_type_van_text',
        ];
    }

    protected function getPrefillDefaults(): array
    {
        $defaults = [];
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


