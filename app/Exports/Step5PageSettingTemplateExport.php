<?php

namespace App\Exports;

use App\Models\Step4PageSetting;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class Step5PageSettingTemplateExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $format;

    public function __construct($format = 'single_column')
    {
        $this->format = $format;
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

    public function headings(): array
    {
        if ($this->format === 'single_column') return ['Field Name', 'Translation Value'];
        return array_map(fn($f) => ucwords(str_replace('_', ' ', $f)), $this->fields());
    }
}


