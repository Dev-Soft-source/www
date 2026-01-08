<?php

namespace App\Imports;

use App\Models\MyPhoneSetting;
use App\Models\MyPhoneSettingDetail;
use App\Models\Language;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class MyPhoneSettingImport implements ToCollection, WithHeadingRow, WithValidation
{
    protected $languageId;

    public function __construct($languageId)
    {
        $this->languageId = $languageId;
    }

    protected function fieldsList(): array
    {
        return [
            'phone_no_description_text','unverified_number_label','main_heading','mobile_verify_button_text','web_send_verification_code_button_text','delete_button_text','mobile_country_code_label','country_code_placeholder','mobile_phone_number_label','phone_number_placeholder','save_phoneno_button_text','send_verification_code_button_text','verify_phone_number_heading','otp_code_description','enter_code_label','verify_phone_number_label','second_text','request_code_text','resend_code_btn_label','set_as_default_label','default_verified_number_label','verified_number_label','phone_no_description_text1','phone_number_label_web','country_code_label_web','country_id_label_web','add_another_phone_number_title'
        ];
    }

    public function collection(Collection $rows)
    {
        $setting = MyPhoneSetting::first() ?? MyPhoneSetting::create([]);
        if ($rows->isEmpty()) return;

        $firstRow = $rows->first();
        $keys = array_keys($firstRow->toArray());
        $isSingleColumn = isset($keys[0]) && (in_array('field_name', $keys) && (in_array('value', $keys) || in_array('translation_value', $keys)));

        if ($isSingleColumn) {
            foreach ($rows as $row) $this->processSingleColumnFormat($setting, $row);
        } else {
            $this->processMultiColumnFormat($setting, $firstRow);
        }
    }

    protected function processSingleColumnFormat($setting, $row)
    {
        $fieldName = $row['field_name'] ?? null;
        $value = $row['translation_value'] ?? $row['value'] ?? null;
        if (empty($fieldName) || $value === null || $value === '') return;
        $fieldName = strtolower(trim($fieldName));
        if (!in_array($fieldName, $this->fieldsList())) return;

        MyPhoneSettingDetail::updateOrCreate(
            [
                'phone_no_setting_id' => $setting->id,
                'language_id' => $this->languageId,
            ],
            [
                'phone_no_setting_id' => $setting->id,
                'language_id' => $this->languageId,
                $fieldName => $value,
            ]
        );
    }

    protected function processMultiColumnFormat($setting, $row)
    {
        $fields = [
            'phone_no_setting_id' => $setting->id,
            'language_id' => $this->languageId,
        ];
        foreach ($this->fieldsList() as $f) {
            $fields[$f] = $row[$f] ?? null;
        }

        MyPhoneSettingDetail::updateOrCreate(
            [
                'phone_no_setting_id' => $setting->id,
                'language_id' => $this->languageId,
            ],
            $fields
        );
    }

    public function rules(): array
    {
        $language = Language::find($this->languageId);
        if (!$language || $language->is_default != '1') return [];
        return [
            'unverified_number_label' => 'required|string',
            'main_heading' => 'required|string',
            'mobile_verify_button_text' => 'required|string',
            'web_send_verification_code_button_text' => 'required|string',
            'delete_button_text' => 'required|string',
            'country_code_placeholder' => 'required|string',
            'mobile_phone_number_label' => 'required|string',
            'phone_number_placeholder' => 'required|string',
            'save_phoneno_button_text' => 'required|string',
            'send_verification_code_button_text' => 'required|string',
            'add_another_phone_number_title' => 'required|string',
        ];
    }
}


