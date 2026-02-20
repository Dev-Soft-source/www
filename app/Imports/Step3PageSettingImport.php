<?php

namespace App\Imports;

use App\Exports\Step3PageSettingTemplateExport;
use App\Models\Step3PageSetting;
use App\Models\Step3PageSettingDetail;
use App\Models\Language;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class Step3PageSettingImport implements ToCollection, WithHeadingRow, WithValidation
{
    /** @var int|null When set, import is for a single language. When null, import is all_languages format. */
    protected $languageId;

    public function __construct($languageId = null)
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

        $isAllLanguages = $this->languageId === null
            && (in_array('field_name', $keys) || in_array('field name', $keys))
            && count($keys) > 1;

        if ($isAllLanguages) {
            $this->processAllLanguagesFormat($setting, $rows);
            return;
        }

        $isSingle = in_array('field_name', $keys) && (in_array('value', $keys) || in_array('translation_value', $keys));
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

    protected function processAllLanguagesFormat(Step3PageSetting $setting, Collection $rows): void
    {
        $firstRow = $rows->first();
        $headers = array_keys($firstRow->toArray());
        $fieldNameKey = in_array('field_name', $headers) ? 'field_name' : 'field name';
        $languageColumns = array_diff($headers, [$fieldNameKey]);

        $languages = Language::orderBy('id')->get();
        $nameToId = $languages->mapWithKeys(fn($lang) => [Str::lower($lang->name) => $lang->id])->toArray();
        $validFields = $this->persistableFields();

        foreach ($rows as $row) {
            $row = $row->toArray();
            $fieldName = $row[$fieldNameKey] ?? null;
            if (empty($fieldName) || !in_array($fieldName, $validFields, true)) continue;

            foreach ($languageColumns as $col) {
                $langKey = Str::lower(trim((string) $col));
                if (!isset($nameToId[$langKey])) continue;
                $languageId = $nameToId[$langKey];
                $value = $row[$col] ?? null;

                $detail = Step3PageSettingDetail::firstOrCreate(
                    ['step3_page_setting_id' => $setting->id, 'language_id' => $languageId],
                    [$fieldName => $value]
                );
                if (!$detail->wasRecentlyCreated) {
                    $detail->$fieldName = $value;
                    $detail->save();
                }
            }
        }
    }

    public function rules(): array
    {
        if ($this->languageId === null) return [];
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


