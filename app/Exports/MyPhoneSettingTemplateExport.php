<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class MyPhoneSettingTemplateExport implements FromCollection, WithHeadings, ShouldAutoSize
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
            'phone_no_description_text','unverified_number_label','main_heading','mobile_verify_button_text','web_send_verification_code_button_text','delete_button_text','mobile_country_code_label','country_code_placeholder','mobile_phone_number_label','phone_number_placeholder','save_phoneno_button_text','send_verification_code_button_text','verify_phone_number_heading','otp_code_description','enter_code_label','verify_phone_number_label','second_text','request_code_text','resend_code_btn_label','set_as_default_label','default_verified_number_label','verified_number_label','phone_no_description_text1','phone_number_label_web','country_code_label_web','country_id_label_web','add_another_phone_number_title'
        ];
    }
}


