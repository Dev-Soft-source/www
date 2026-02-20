<?php

namespace App\Exports;

use App\Models\Step4PageSetting;
use App\Models\Language;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class Step5PageSettingTemplateExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $format;

    /** @var \Illuminate\Support\Collection|null */
    protected $languages;

    /** @var \App\Models\Step4PageSetting|null */
    protected $existingData;

    /**
     * @param string $format 'single_column', 'multi_column', or 'all_languages'
     * @param \Illuminate\Support\Collection|array|null $languages For all_languages format
     * @param \App\Models\Step4PageSetting|null $existingData For all_languages (with step4PageSettingDetail loaded)
     */
    public function __construct($format = 'single_column', $languages = null, $existingData = null)
    {
        $this->format = $format;
        $this->languages = $languages ? collect($languages) : null;
        $this->existingData = $existingData;
    }

    protected function fields(): array
    {
        return [
            'name','meta_keywords','meta_description','main_heading','main_label',
            'country_code_label','country_code_error','phone_label','phone_error',
            'skip_button_label','verify_button_label','verify_code_label','enter_code_label',
            'request_code_label','second_label','save_button_label','send_button_label','logout_button_label'
        ];
    }

    public function collection(): Collection
    {
        if ($this->format === 'all_languages') {
            return $this->allLanguagesFormat();
        }

        $fields = $this->fields();
        $values = [];
        if ($setting = Step4PageSetting::with('step4PageSettingDetail')->first()) {
            $detail = optional($setting->step4PageSettingDetail)->first();
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

        $row = [];
        foreach ($fields as $f) { $row[$f] = $values[$f] ?? ''; }
        return new Collection([$row]);
    }

    /**
     * All-languages format: one row per field, first column = field name, then one column per language.
     */
    protected function allLanguagesFormat(): Collection
    {
        $languages = $this->languages ?? Language::orderBy('id')->get();
        $fields = $this->fields();
        $detailsByLang = [];
        if ($this->existingData && $this->existingData->relationLoaded('step4PageSettingDetail')) {
            foreach ($this->existingData->step4PageSettingDetail as $d) {
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
        return array_map(fn($f) => ucwords(str_replace('_', ' ', $f)), $this->fields());
    }
}


