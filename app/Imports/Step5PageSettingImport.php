<?php

namespace App\Imports;

use App\Models\Step5PageSetting;
use App\Models\Step5PageSettingDetail;
use App\Models\Language;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class Step5PageSettingImport implements ToCollection, WithHeadingRow, WithValidation
{
    /** @var int|null When null, import is all_languages format (Field Name + one column per language). */
    protected $languageId;

    public function __construct($languageId = null)
    {
        $this->languageId = $languageId;
    }

    protected function fields(): array
    {
        return [
            'name', 'meta_keywords', 'meta_description', 'main_heading', 'main_label',
            'sub_main_label', 'required_label', 'driver_license_label', 'driver_license_error',
            'driver_license_sub_label', 'photo_detail_label', 'mobile_photo_choose_file_label',
            'skip_license', 'next_button_label', 'liecense_section_heading',
        ];
    }

    public function collection(Collection $rows)
    {
        $setting = Step5PageSetting::first() ?? Step5PageSetting::create([]);
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

        $isSingle = isset($keys[0]) && in_array('field_name', $keys) && (in_array('value', $keys) || in_array('translation_value', $keys));

        $data = [];
        if ($isSingle) {
            foreach ($rows as $row) {
                $k = strtolower(trim($row['field_name'] ?? ''));
                if (!in_array($k, $this->fields())) continue;
                $data[$k] = $row['translation_value'] ?? $row['value'] ?? null;
            }
        } else {
            $data = $firstRow->toArray();
        }

        $payload = [
            'step5_page_setting_id' => $setting->id,
            'language_id' => $this->languageId,
        ];
        foreach ($this->fields() as $f) { $payload[$f] = $data[$f] ?? null; }

        Step5PageSettingDetail::updateOrCreate(
            ['step5_page_setting_id' => $setting->id, 'language_id' => $this->languageId],
            $payload
        );
    }

    /**
     * Process all_languages format: each row = one field, columns = Field Name, then one per language (by header name).
     */
    protected function processAllLanguagesFormat(Step5PageSetting $setting, Collection $rows): void
    {
        $firstRow = $rows->first();
        $headers = array_keys($firstRow->toArray());

        $fieldNameKey = in_array('field_name', $headers) ? 'field_name' : 'field name';
        $languageColumns = array_diff($headers, [$fieldNameKey]);

        $languages = Language::orderBy('id')->get();
        $nameToId = $languages->mapWithKeys(function ($lang) {
            return [Str::lower($lang->name) => $lang->id];
        })->toArray();

        $validFields = $this->fields();

        foreach ($rows as $row) {
            $row = $row->toArray();
            $fieldName = $row[$fieldNameKey] ?? null;
            $fieldName = is_string($fieldName) ? strtolower(trim($fieldName)) : $fieldName;
            if (empty($fieldName) || !in_array($fieldName, $validFields, true)) {
                continue;
            }

            foreach ($languageColumns as $col) {
                $langKey = Str::lower(trim($col));
                if (!isset($nameToId[$langKey])) {
                    continue;
                }
                $languageId = $nameToId[$langKey];
                $value = $row[$col] ?? null;

                $detail = Step5PageSettingDetail::firstOrNew([
                    'step5_page_setting_id' => $setting->id,
                    'language_id' => $languageId,
                ]);
                $detail->$fieldName = $value;
                $detail->save();
            }
        }
    }

    public function rules(): array
    {
        if ($this->languageId === null) {
            return [];
        }
        $language = Language::find($this->languageId);
        if (!$language || $language->is_default != '1') return [];
        return [
            'name' => 'required|string',
            'meta_keywords' => 'required|string',
            'meta_description' => 'required|string',
            'main_heading' => 'required|string',
            'main_label' => 'required|string',
            'sub_main_label' => 'required|string',
            'required_label' => 'required|string',
            'driver_license_label' => 'required|string',
            'driver_license_error' => 'required|string',
            'driver_license_sub_label' => 'required|string',
            'photo_detail_label' => 'required|string',
            'mobile_photo_choose_file_label' => 'required|string',
            'skip_license' => 'required|string',
            'next_button_label' => 'required|string',
            'liecense_section_heading' => 'required|string',
        ];
    }
}


