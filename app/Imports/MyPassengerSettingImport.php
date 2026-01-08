<?php

namespace App\Imports;

use App\Models\MyPassengerSetting;
use App\Models\MyPassengerSettingDetail;
use App\Models\Language;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class MyPassengerSettingImport implements ToCollection, WithHeadingRow, WithValidation
{
    protected $languageId;

    public function __construct($languageId)
    {
        $this->languageId = $languageId;
    }

    protected function fieldsList(): array
    {
        return [
            'remove_ride_btn_label','chat_passenger_btn_label','main_heading','total_amount_label','my_fare_label','booking_fee_label','seat_booked_label','review_profile_label','age','gender','co_passenger_main_heading','web_back_button_label','no_show_passenger_label','web_i_reviewed_label','web_reviewd_label','revert_no_show_passenger_label'
        ];
    }

    public function collection(Collection $rows)
    {
        $setting = MyPassengerSetting::first() ?? MyPassengerSetting::create([]);
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

        MyPassengerSettingDetail::updateOrCreate(
            [
                'my_passenger_setting_id' => $setting->id,
                'language_id' => $this->languageId,
            ],
            [
                'my_passenger_setting_id' => $setting->id,
                'language_id' => $this->languageId,
                $fieldName => $value,
            ]
        );
    }

    protected function processMultiColumnFormat($setting, $row)
    {
        $fields = [
            'my_passenger_setting_id' => $setting->id,
            'language_id' => $this->languageId,
        ];
        foreach ($this->fieldsList() as $f) {
            $fields[$f] = $row[$f] ?? null;
        }

        MyPassengerSettingDetail::updateOrCreate(
            [
                'my_passenger_setting_id' => $setting->id,
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
            'chat_passenger_btn_label' => 'required|string',
            'main_heading' => 'required|string',
            'total_amount_label' => 'required|string',
            'my_fare_label' => 'required|string',
            'booking_fee_label' => 'required|string',
            'review_profile_label' => 'required|string',
            'age' => 'required|string',
            'gender' => 'required|string',
            'co_passenger_main_heading' => 'required|string',
            'web_back_button_label' => 'required|string',
            'no_show_passenger_label' => 'required|string',
            'web_i_reviewed_label' => 'required|string',
            'web_reviewd_label' => 'required|string',
            'revert_no_show_passenger_label' => 'required|string',
        ];
    }
}


