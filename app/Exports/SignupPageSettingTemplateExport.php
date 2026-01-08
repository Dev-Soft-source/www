<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SignupPageSettingTemplateExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $format;

    public function __construct($format = 'single_column')
    {
        $this->format = $format;
    }

    public function collection(): Collection
    {
        $fields = $this->getFields();
        if ($this->format === 'single_column') {
            $rows = [];
            foreach ($fields as $field) {
                $rows[] = ['field_name' => $field, 'translation_value' => ''];
            }
            return new Collection($rows);
        }
        $row = array_fill_keys($fields, '');
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
            'name','meta_keywords','meta_description','main_heading','or_label','required_label',
            'first_name_label','first_name_error','first_name_placeholder',
            'last_name_label','last_name_error','last_name_placeholder',
            'email_label','email_error','email_placeholder',
            'password_label','password_error','password_placeholder',
            'confirm_password_label','confirm_password_error','confirm_password_placeholder',
            'agree_terms_error','phone_number_label','phone_number_option1','phone_number_option2',
            'agree_terms_label','button_label','after_button_label','signin_label',
            'app_main_heading','app_agree_terms_part1_label','app_agree_terms_link1_label','app_agree_terms_link2_label','app_agree_terms_part2_label','app_agree_terms_link3_label','app_agree_terms_part3_label',
            'no_account_label','signin_link_label','now_label','language_label'
        ];
    }
}


