<?php

namespace App\Imports;

use App\Models\MyReviewSetting;
use App\Models\MyReviewSettingDetail;
use App\Models\Language;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class MyReviewSettingImport implements ToCollection, WithHeadingRow, WithValidation
{
    protected $languageId;

    public function __construct($languageId)
    {
        $this->languageId = $languageId;
    }

    protected function fieldsList(): array
    {
        return [
            'review_left_label','review_received_label','main_heading','replied_label','response_label','reply_label','no_more_data_label','no_left_message','no_received_message','reply_heading_label','reply_placeholder','see_all_review_label','reply_submit_button_label','review_label'
        ];
    }

    public function collection(Collection $rows)
    {
        $setting = MyReviewSetting::first() ?? MyReviewSetting::create([]);
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

        MyReviewSettingDetail::updateOrCreate(
            [
                'my_review_setting_id' => $setting->id,
                'language_id' => $this->languageId,
            ],
            [
                'my_review_setting_id' => $setting->id,
                'language_id' => $this->languageId,
                $fieldName => $value,
            ]
        );
    }

    protected function processMultiColumnFormat($setting, $row)
    {
        $fields = [
            'my_review_setting_id' => $setting->id,
            'language_id' => $this->languageId,
        ];
        foreach ($this->fieldsList() as $f) {
            $fields[$f] = $row[$f] ?? null;
        }

        MyReviewSettingDetail::updateOrCreate(
            [
                'my_review_setting_id' => $setting->id,
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
            'review_left_label' => 'required|string',
            'review_received_label' => 'required|string',
            'main_heading' => 'required|string',
            'replied_label' => 'required|string',
            'response_label' => 'required|string',
            'reply_label' => 'required|string',
            'no_left_message' => 'required|string',
            'no_received_message' => 'required|string',
            'see_all_review_label' => 'required|string',
        ];
    }
}


