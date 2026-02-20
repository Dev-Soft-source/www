<?php

namespace App\Exports;

use App\Models\TermsOfUsePageSetting;
use App\Models\Language;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class TermsOfUsePageSettingTemplateExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $format;

    /** @var \Illuminate\Support\Collection|null */
    protected $languages;

    /** @var \App\Models\TermsOfUsePageSetting|null */
    protected $existingData;

    /**
     * @param string $format 'single_column', 'multi_column', or 'all_languages'
     * @param \Illuminate\Support\Collection|array|null $languages For all_languages format
     * @param \App\Models\TermsOfUsePageSetting|null $existingData For all_languages (with termsOfUsePageSettingDetail loaded)
     */
    public function __construct($format = 'single_column', $languages = null, $existingData = null)
    {
        $this->format = $format;
        $this->languages = $languages ? collect($languages) : null;
        $this->existingData = $existingData;
    }

    public function collection(): Collection
    {
        if ($this->format === 'all_languages') {
            return $this->allLanguagesFormat();
        }

        $fields = $this->getFields();
        $values = [];
        if ($setting = TermsOfUsePageSetting::with('termsOfUsePageSettingDetail')->first()) {
            $detail = optional($setting->termsOfUsePageSettingDetail)->first();
            if ($detail) {
                foreach ($fields as $f) { $values[$f] = $detail->{$f} ?? ''; }
            }
        }

        if ($this->format === 'single_column') {
            $rows = [];
            foreach ($fields as $field) {
                $rows[] = ['field_name' => $field, 'translation_value' => $values[$field] ?? ''];
            }
            return new Collection($rows);
        }
        $row = array_fill_keys($fields, '');
        foreach ($fields as $f) { $row[$f] = $values[$f] ?? ''; }
        return new Collection([$row]);
    }

    /**
     * All-languages format: one row per field, first column = field name, then one column per language.
     */
    protected function allLanguagesFormat(): Collection
    {
        $languages = $this->languages ?? Language::orderBy('id')->get();
        $fields = $this->getFields();
        $detailsByLang = [];
        if ($this->existingData && $this->existingData->relationLoaded('termsOfUsePageSettingDetail')) {
            foreach ($this->existingData->termsOfUsePageSettingDetail as $d) {
                $detailsByLang[$d->language_id] = $d;
            }
        }

        $rows = [];
        foreach ($fields as $fieldKey) {
            $row = [$fieldKey];
            foreach ($languages as $lang) {
                $detail = $detailsByLang[$lang->id] ?? null;
                $value = $detail && isset($detail->$fieldKey) ? ($detail->$fieldKey ?? '') : '';
                $row[] = $value;
            }
            $rows[] = $row;
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
        return array_map(fn($f) => ucwords(str_replace('_', ' ', $f)), $this->getFields());
    }

    protected function getFields(): array
    {
        return ['name','meta_keywords','meta_description','main_heading','main_text'];
    }
}


