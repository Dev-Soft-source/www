<?php

namespace App\Imports;

use App\Models\DriverPageSetting;
use App\Models\DriverPageSettingDetail;
use App\Models\Language;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class DriverPageSettingImport implements ToCollection, WithHeadingRow, WithValidation
{
    protected $languageId;

    public function __construct($languageId)
    {
        $this->languageId = $languageId;
    }

    public function collection(Collection $rows)
    {
        Log::info('Starting Driver Page Settings Excel import for language ID: ' . $this->languageId);

        $setting = DriverPageSetting::first();
        if (!$setting) $setting = DriverPageSetting::create([]);
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

        $allowed = ['name','meta_keywords','meta_description','main_heading','sub_heading','page_description'];
        if (!in_array($fieldName, $allowed)) return;

        $detail = DriverPageSettingDetail::where('driver_page_setting_id', $setting->id)
            ->where('language_id', $this->languageId)
            ->first();

        if ($detail) {
            $detail->$fieldName = $value;
            $detail->save();
        } else {
            DriverPageSettingDetail::create([
                'driver_page_setting_id' => $setting->id,
                'language_id' => $this->languageId,
                $fieldName => $value,
            ]);
        }
    }

    protected function processMultiColumnFormat($setting, $row)
    {
        $fields = [
            'driver_page_setting_id' => $setting->id,
            'language_id' => $this->languageId,
            'name' => $row['name'] ?? null,
            'meta_keywords' => $row['meta_keywords'] ?? null,
            'meta_description' => $row['meta_description'] ?? null,
            'main_heading' => $row['main_heading'] ?? null,
            'sub_heading' => $row['sub_heading'] ?? null,
            'page_description' => $row['page_description'] ?? null,
        ];

        DriverPageSettingDetail::updateOrCreate(
            [
                'driver_page_setting_id' => $setting->id,
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
            'name' => 'required|string',
            'meta_keywords' => 'required|string',
            'meta_description' => 'required|string',
            'main_heading' => 'required|string',
            'sub_heading' => 'required|string',
            'page_description' => 'required|string',
        ];
    }
}


