<?php

namespace App\Imports;

use App\Models\Step3PageSetting;
use App\Models\Step3PageSettingDetail;
use App\Models\Language;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class Step3PageSettingImport implements ToCollection, WithHeadingRow, WithValidation
{
    protected $languageId;

    public function __construct($languageId)
    {
        $this->languageId = $languageId;
    }

    protected function sheetFields(): array
    {
        // All fields expected in the sheet (as per UI requirements)
        return [
            'name','meta_keywords','meta_description','main_heading','main_label','required_label',
            'make_label','make_error','make_placeholder',
            'model_label','model_error','model_placeholder',
            'vehicle_type_label','vehicle_type_error','vehicle_type_placeholder',
            'color_label','color_error',
            'license_label','license_error',
            'year_label','year_error',
            'fuel_label','fuel_error','electric_option_label','hybrid_option_label','gas_option_label',
            'driver_license_label','driver_license_error','mobile_driver_choose_file_label',
            'photo_label','photo_error','photo_detail_label','mobile_photo_choose_file_label',
            'skip_button_label','skip_vehicle_info','skip_license','next_button_label',
            'vehicle_type_placeholder','logout_button_label','sub_heading','sub_main_label','vehicle_section_heading','liecense_section_heading'
        ];
    }

    protected function persistableFields(): array
    {
        // Only actual DB columns (exclude non-existent like driver_license_label, driver_license_sub_label)
        return [
            'name','meta_keywords','meta_description','main_heading','main_label','required_label',
            'make_label','make_error','make_placeholder',
            'model_label','model_error','model_placeholder',
            'vehicle_type_label','vehicle_type_error','vehicle_type_placeholder',
            'color_label','color_error',
            'license_label','license_error',
            'year_label','year_error',
            'fuel_label','fuel_error','electric_option_label','hybrid_option_label','gas_option_label',
            'driver_license_error','mobile_driver_choose_file_label',
            'photo_label','photo_error','photo_detail_label','mobile_photo_choose_file_label',
            'skip_button_label','next_button_label','logout_button_label',
            'sub_heading','sub_main_label','liecense_section_heading','vehicle_section_heading',
            'skip_vehicle_info','skip_license'
        ];
    }

    public function collection(Collection $rows)
    {
        $setting = Step3PageSetting::first() ?? Step3PageSetting::create([]);
        if ($rows->isEmpty()) return;
        $firstRow = $rows->first();
        $keys = array_keys($firstRow->toArray());
        $isSingle = isset($keys[0]) && in_array('field_name', $keys) && (in_array('value', $keys) || in_array('translation_value', $keys));

        $data = [];
        if ($isSingle) {
            foreach ($rows as $row) {
                $k = strtolower(trim($row['field_name'] ?? ''));
                if (!in_array($k, $this->sheetFields())) continue;
                $data[$k] = $row['translation_value'] ?? $row['value'] ?? null;
            }
        } else {
            $data = $firstRow->toArray();
        }

        $payload = [
            'step3_page_setting_id' => $setting->id,
            'language_id' => $this->languageId,
        ];
        foreach ($this->persistableFields() as $f) { $payload[$f] = $data[$f] ?? null; }

        Step3PageSettingDetail::updateOrCreate(
            ['step3_page_setting_id' => $setting->id, 'language_id' => $this->languageId],
            $payload
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
            'main_label' => 'required|string',
            'required_label' => 'required|string',
            'make_label' => 'required|string',
            'make_error' => 'required|string',
            'model_label' => 'required|string',
            'model_error' => 'required|string',
            'vehicle_type_label' => 'required|string',
            'vehicle_type_error' => 'required|string',
            'color_label' => 'required|string',
            'color_error' => 'required|string',
            'license_error' => 'required|string',
            'year_label' => 'required|string',
            'year_error' => 'required|string',
            'fuel_label' => 'required|string',
            'fuel_error' => 'required|string',
            'electric_option_label' => 'required|string',
            'hybrid_option_label' => 'required|string',
            'gas_option_label' => 'required|string',
            'driver_license_error' => 'required|string',
            'mobile_driver_choose_file_label' => 'required|string',
            'photo_label' => 'required|string',
            'photo_error' => 'required|string',
            'photo_detail_label' => 'required|string',
            'mobile_photo_choose_file_label' => 'required|string',
            'skip_button_label' => 'required|string',
            'next_button_label' => 'required|string',
            'logout_button_label' => 'required|string',
            'sub_heading' => 'required|string',
            'sub_main_label' => 'required|string',
            'liecense_section_heading' => 'required|string',
            'vehicle_section_heading' => 'required|string',
            'skip_vehicle_info' => 'required|string',
            'skip_license' => 'required|string',
        ];
    }
}


