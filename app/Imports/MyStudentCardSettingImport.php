<?php

namespace App\Imports;

use App\Models\MyStudentCardSetting;
use App\Models\MyStudentCardSettingDetail;
use App\Models\Language;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class MyStudentCardSettingImport implements ToCollection, WithHeadingRow, WithValidation
{
    protected $languageId;

    public function __construct($languageId)
    {
        $this->languageId = $languageId;
    }

    protected function fieldsList(): array
    {
        return [
            'mobile_indicate_required_field_label','student_card_description_text','main_heading','student_card_image_placeholder','choose_file_image_placeholder','mobile_image_type_placeholder','expiry_date_label','month_placeholder','year_placeholder','upload_button_text','update_button_text','upload_new_image_btn_label'
        ];
    }

    public function collection(Collection $rows)
    {
        $setting = MyStudentCardSetting::first() ?? MyStudentCardSetting::create([]);
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

        MyStudentCardSettingDetail::updateOrCreate(
            [
                'student_card_setting_id' => $setting->id,
                'language_id' => $this->languageId,
            ],
            [
                'student_card_setting_id' => $setting->id,
                'language_id' => $this->languageId,
                $fieldName => $value,
            ]
        );
    }

    protected function processMultiColumnFormat($setting, $row)
    {
        $fields = [
            'student_card_setting_id' => $setting->id,
            'language_id' => $this->languageId,
        ];
        foreach ($this->fieldsList() as $f) {
            $fields[$f] = $row[$f] ?? null;
        }

        MyStudentCardSettingDetail::updateOrCreate(
            [
                'student_card_setting_id' => $setting->id,
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
            'mobile_indicate_required_field_label' => 'required|string',
            'student_card_description_text' => 'required|string',
            'main_heading' => 'required|string',
            'student_card_image_placeholder' => 'required|string',
            'choose_file_image_placeholder' => 'required|string',
            'mobile_image_type_placeholder' => 'required|string',
            'month_placeholder' => 'required|string',
            'year_placeholder' => 'required|string',
            'upload_button_text' => 'required|string',
            'update_button_text' => 'required|string',
            'upload_new_image_btn_label' => 'required|string',
        ];
    }
}


