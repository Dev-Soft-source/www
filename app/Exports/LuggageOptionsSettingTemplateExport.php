<?php

namespace App\Exports;

use App\Models\FeaturesSetting;
use App\Models\FeaturesSettingDetail;
use App\Models\Language;
use App\Models\PostRidePageSetting;
use App\Models\PostRidePageSettingDetail;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class LuggageOptionsSettingTemplateExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths
{
    protected $format;

    /** @var \Illuminate\Support\Collection|null */
    protected $languages;

    /**
     * @param string $format - 'single_column', 'multi_column', or 'all_languages'
     * @param \Illuminate\Support\Collection|array|null $languages
     */
    public function __construct($format = 'single_column', $languages = null)
    {
        $this->format = $format;
        $this->languages = $languages ? collect($languages) : null;
    }

    public static function getTranslatableFieldsWithDefaults(): array
    {
        $fields = [
            'luggage_option1', 'luggage_option1_tooltip', 'luggage_option1_icon',
            'luggage_option2', 'luggage_option2_tooltip', 'luggage_option2_icon',
            'luggage_option3', 'luggage_option3_tooltip', 'luggage_option3_icon',
            'luggage_option4', 'luggage_option4_tooltip', 'luggage_option4_icon',
            'luggage_option5', 'luggage_option5_tooltip', 'luggage_option5_icon',
            'luggage_option5_label',
        ];
        return array_fill_keys($fields, '');
    }

    public function collection(): Collection
    {
        if ($this->format === 'single_column') {
            return $this->singleColumnFormat();
        }
        if ($this->format === 'all_languages') {
            return $this->allLanguagesFormat();
        }
        return $this->multiColumnFormat();
    }

    protected function singleColumnFormat(): Collection
    {
        $fields = static::getTranslatableFieldsWithDefaults();
        $defaults = $this->getDefaultFileFieldValues();
        $rows = [];
        foreach ($fields as $field => $default) {
            $rows[] = ['field_name' => $field, 'translation_value' => $defaults[$field] ?? $default];
        }
        return new Collection($rows);
    }

    protected function allLanguagesFormat(): Collection
    {
        $languages = $this->languages ?? Language::orderBy('id')->get();
        $fields = array_keys(static::getTranslatableFieldsWithDefaults());
        $slugMap = [
            1 => 'no_luggage', 2 => 'small_luggage', 3 => 'medium_luggage', 4 => 'large_luggage', 5 => 'xl_luggage',
        ];
        $features = [];
        foreach ($slugMap as $i => $slug) {
            $features[$i] = FeaturesSetting::where('slug', $slug)->first();
        }
        $postRide = PostRidePageSetting::first();
        $postDetailsByLang = [];
        if ($postRide) {
            foreach (PostRidePageSettingDetail::where('post_ride_page_setting_id', $postRide->id)->get() as $d) {
                $postDetailsByLang[$d->language_id] = $d;
            }
        }
        $detailByLang = [];
        foreach ($features as $i => $fs) {
            if (!$fs) {
                continue;
            }
            foreach (FeaturesSettingDetail::where('features_setting_id', $fs->id)->get() as $d) {
                $detailByLang[$d->language_id][$i] = $d;
            }
        }
        $rows = [];
        foreach ($fields as $fieldKey) {
            $row = [$fieldKey];
            foreach ($languages as $lang) {
                $value = '';
                if (preg_match('/^luggage_option(\d)_icon$/', $fieldKey, $m)) {
                    $idx = (int) $m[1];
                    $d = $detailByLang[$lang->id][$idx] ?? null;
                    $value = $d && isset($d->icon) ? ($d->icon ?? '') : '';
                } elseif (preg_match('/^luggage_option(\d)$/', $fieldKey, $m)) {
                    $idx = (int) $m[1];
                    $d = $detailByLang[$lang->id][$idx] ?? null;
                    $value = $d && isset($d->name) ? ($d->name ?? '') : '';
                } else {
                    $postD = $postDetailsByLang[$lang->id] ?? null;
                    $value = $postD && isset($postD->$fieldKey) ? ($postD->$fieldKey ?? '') : '';
                }
                $row[] = $value;
            }
            $rows[] = $row;
        }
        return collect($rows);
    }

    protected function multiColumnFormat(): Collection
    {
        $fields = static::getTranslatableFieldsWithDefaults();
        $defaults = $this->getDefaultFileFieldValues();
        $row = $fields;
        foreach ($defaults as $k => $v) {
            if (array_key_exists($k, $row)) {
                $row[$k] = $v;
            }
        }
        return new Collection([$row]);
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
        if (!$lang) {
            return $defaults;
        }
        foreach ($map as $field => $slug) {
            $setting = FeaturesSetting::where('slug', $slug)->first();
            if (!$setting) {
                continue;
            }
            $detail = FeaturesSettingDetail::where('features_setting_id', $setting->id)
                ->where('language_id', $lang->id)
                ->first();
            if ($detail && !empty($detail->icon)) {
                $defaults[$field] = $detail->icon;
            }
        }
        return $defaults;
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
        if ($this->format === 'single_column') {
            return ['A' => 28, 'B' => 80];
        }
        if ($this->format === 'all_languages') {
            $totalCols = ($this->languages ?? Language::orderBy('id')->get())->count() + 1;
            $widths = [];
            for ($colIndex = 1; $colIndex <= $totalCols; $colIndex++) {
                $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
                $widths[$col] = $colIndex === 1 ? 28 : 25;
            }
            return $widths;
        }
        return ['A' => 28, 'B' => 25];
    }
}
