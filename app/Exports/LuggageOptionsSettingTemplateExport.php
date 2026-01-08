<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use App\Models\Language;
use App\Models\FeaturesSetting;
use App\Models\FeaturesSettingDetail;

class LuggageOptionsSettingTemplateExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $format;

    public function __construct($format = 'single_column')
    {
        $this->format = $format;
    }

    public function collection(): Collection
    {
        $fields = $this->getFields();
        $defaults = $this->getDefaultFileFieldValues();
        if ($this->format === 'single_column') {
            $rows = [];
            foreach ($fields as $field) {
                $rows[] = ['field_name' => $field, 'translation_value' => $defaults[$field] ?? ''];
            }
            return new Collection($rows);
        }
        $row = array_fill_keys($fields, '');
        foreach ($defaults as $k => $v) if (array_key_exists($k, $row)) $row[$k] = $v;
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
            'luggage_option1','luggage_option1_tooltip','luggage_option1_icon',
            'luggage_option2','luggage_option2_tooltip','luggage_option2_icon',
            'luggage_option3','luggage_option3_tooltip','luggage_option3_icon',
            'luggage_option4','luggage_option4_tooltip','luggage_option4_icon',
            'luggage_option5','luggage_option5_tooltip','luggage_option5_icon',
            'luggage_option5_label',
        ];
    }

    protected function getDefaultFileFieldValues(): array
    {
        $map = [
            'luggage_option1_icon' => 'no_luggage',
            'luggage_option2_icon' => 'small_luggage',
            'luggage_option3_icon' => 'medium_luggage',
            'luggage_option4_icon' => 'large_luggage',
            'luggage_option5_icon' => 'xl_luggage',
        ];
        $defaults = [];
        $lang = Language::where('is_default', '1')->first();
        if (!$lang) return $defaults;
        foreach ($map as $field => $slug) {
            $setting = FeaturesSetting::where('slug', $slug)->first();
            if (!$setting) continue;
            $detail = FeaturesSettingDetail::where('features_setting_id', $setting->id)
                ->where('language_id', $lang->id)
                ->first();
            if ($detail && !empty($detail->icon)) $defaults[$field] = $detail->icon;
        }
        return $defaults;
    }
}


