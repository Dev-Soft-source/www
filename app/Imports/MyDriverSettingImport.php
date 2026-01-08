<?php

namespace App\Imports;

use App\Models\DriverSetting;
use App\Models\DriverSettingDetail;
use App\Models\Language;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class MyDriverSettingImport implements ToCollection, WithHeadingRow, WithValidation
{
    protected $languageId;

    public function __construct($languageId)
    {
        $this->languageId = $languageId;
    }

    protected function fieldsList(): array
    {
        return [
            'mobile_indicate_required_field_label','driver_license_description_text','main_heading','driver_license_label','web_upload_image_placeholder','mobile_driver_license_image_placeholder','mobile_choose_file_image_placeholder','mobile_image_type_placeholder','upload_button_text','upload_new_image_btn_label','update_button_text'
        ];
    }

    public function collection(Collection $rows)
    {
        $setting = DriverSetting::first();
        if (!$setting) $setting = DriverSetting::create([]);
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

        $detail = DriverSettingDetail::where('driver_lic_setting_id', $setting->id)
            ->where('language_id', $this->languageId)
            ->first();

        if ($detail) {
            $detail->$fieldName = $value;
            $detail->save();
        } else {
            DriverSettingDetail::create([
                'driver_lic_setting_id' => $setting->id,
                'language_id' => $this->languageId,
                $fieldName => $value,
            ]);
        }
    }

    protected function processMultiColumnFormat($setting, $row)
    {
        $fields = [
            'driver_lic_setting_id' => $setting->id,
            'language_id' => $this->languageId,
        ];
        foreach ($this->fieldsList() as $f) {
            $fields[$f] = $row[$f] ?? null;
        }

        DriverSettingDetail::updateOrCreate(
            [
                'driver_lic_setting_id' => $setting->id,
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
            'driver_license_description_text' => 'required|string',
            'main_heading' => 'required|string',
            'driver_license_label' => 'required|string',
            'web_upload_image_placeholder' => 'required|string',
            'mobile_driver_license_image_placeholder' => 'required|string',
            'mobile_image_type_placeholder' => 'required|string',
        ];
    }
}

