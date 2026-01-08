<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class LoginPageSettingTemplateExport implements FromCollection, WithHeadings, ShouldAutoSize
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
            $data = [];
            foreach ($fields as $field) {
                $data[] = ['field_name' => $field, 'translation_value' => ''];
            }
            return new Collection($data);
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
            'name','meta_keywords','meta_description','main_heading','continue_label','new_verification_email_btn_label','or_label','email_label','email_error','email_placeholder','password_label','password_error','password_placeholder','forgot_password_label','submit_button_label','signup_label','no_account_label','signup_link_label','now_label','language_label','protect_account_heading','protect_account_text','remember_me_text','close_modal_error_message'
        ];
    }
}


