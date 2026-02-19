<?php

namespace App\Imports;

use App\Models\CoffeeWallPageSetting;
use App\Models\CoffeeWallPageSettingDetail;
use App\Models\Language;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class CoffeeWallPageSettingImport implements ToCollection, WithHeadingRow, WithValidation
{
    /** @var int|null When null, import expects all_languages format (Field Name + one column per language). */
    protected $languageId;

    public function __construct($languageId = null)
    {
        $this->languageId = $languageId;
    }

    public function collection(Collection $rows)
    {
        $setting = CoffeeWallPageSetting::first();
        if (!$setting) {
            $setting = CoffeeWallPageSetting::create([]);
        }

        if ($rows->isEmpty()) {
            Log::warning('No rows found in Coffee Wall Excel file');
            return;
        }

        $firstRow = $rows->first();
        $keys = array_keys($firstRow->toArray());

        $isAllLanguages = $this->languageId === null
            && (in_array('field_name', $keys) || in_array('field name', $keys))
            && count($keys) > 1;

        if ($isAllLanguages) {
            $this->processAllLanguagesFormat($setting, $rows);
            Log::info('Coffee Wall Page Settings Excel import (all languages) completed successfully');
            return;
        }

        $isSingleColumn = in_array('field_name', $keys) && (in_array('value', $keys) || in_array('translation_value', $keys));

        if ($isSingleColumn && $this->languageId !== null) {
            foreach ($rows as $row) {
                $this->processSingleColumnFormat($setting, $row);
            }
        } else {
            if ($this->languageId !== null) {
                $this->processMultiColumnFormat($setting, $firstRow);
            }
        }
    }

    protected function processAllLanguagesFormat(CoffeeWallPageSetting $setting, Collection $rows): void
    {
        $firstRow = $rows->first();
        $headers = array_keys($firstRow->toArray());
        $fieldNameKey = in_array('field_name', $headers) ? 'field_name' : 'field name';
        $languageColumns = array_diff($headers, [$fieldNameKey]);
        $languages = Language::orderBy('id')->get();
        $nameToId = $languages->mapWithKeys(fn ($lang) => [Str::lower($lang->name) => $lang->id])->toArray();
        $validFields = array_keys(\App\Exports\CoffeeWallPageSettingTemplateExport::getTranslatableFieldsWithDefaults());

        foreach ($rows as $row) {
            $row = $row->toArray();
            $fieldName = $row[$fieldNameKey] ?? null;
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
                $detail = CoffeeWallPageSettingDetail::firstOrCreate(
                    [
                        'coffee_wall_setting_id' => $setting->id,
                        'language_id' => $languageId,
                    ],
                    [$fieldName => $value]
                );
                if (!$detail->wasRecentlyCreated) {
                    $detail->$fieldName = $value;
                    $detail->save();
                }
            }
        }
    }

    protected function processSingleColumnFormat($setting, $row)
    {
        $fieldName = $row['field_name'] ?? null;
        $value = $row['translation_value'] ?? $row['value'] ?? null;

        if (empty($fieldName) || $value === null || $value === '') {
            return;
        }

        $detail = CoffeeWallPageSettingDetail::where('coffee_wall_setting_id', $setting->id)
            ->where('language_id', $this->languageId)
            ->first();

        if ($detail) {
            $detail->$fieldName = $value;
            $detail->save();
        } else {
            CoffeeWallPageSettingDetail::create([
                'coffee_wall_setting_id' => $setting->id,
                'language_id' => $this->languageId,
                $fieldName => $value,
            ]);
        }
    }

    protected function processMultiColumnFormat($setting, $row)
    {
        $fields = [
            'coffee_wall_setting_id' => $setting->id,
            'language_id' => $this->languageId,
            'name' => $row['name'] ?? null,
            'meta_keywords' => $row['meta_keywords'] ?? null,
            'meta_description' => $row['meta_description'] ?? null,
            'main_heading' => $row['main_heading'] ?? null,
            'required_field_label' => $row['required_field_label'] ?? null,
            'main_text' => $row['main_text'] ?? null,
            'agree_terms_label' => $row['agree_terms_label'] ?? null,
            'custom_amount_label' => $row['custom_amount_label'] ?? null,
            'pay_button_label' => $row['pay_button_label'] ?? null,
            'frequency_label' => $row['frequency_label'] ?? null,
            'email_label' => $row['email_label'] ?? null,
            'name_label' => $row['name_label'] ?? null,
            'phone_label' => $row['phone_label'] ?? null,
            'designation_label' => $row['designation_label'] ?? null,
            'designation_option1' => $row['designation_option1'] ?? null,
            'designation_option2' => $row['designation_option2'] ?? null,
            'designation_option3' => $row['designation_option3'] ?? null,
            'designation_option4' => $row['designation_option4'] ?? null,
            'monthly_label' => $row['monthly_label'] ?? null,
            'quarterly_label' => $row['quarterly_label'] ?? null,
            'semi_annually_label' => $row['semi_annually_label'] ?? null,
            'annually_label' => $row['annually_label'] ?? null,
        ];

        CoffeeWallPageSettingDetail::updateOrCreate(
            [
                'coffee_wall_setting_id' => $setting->id,
                'language_id' => $this->languageId,
            ],
            $fields
        );
    }

    public function rules(): array
    {
        if ($this->languageId === null) {
            return [];
        }
        $language = Language::find($this->languageId);
        if (!$language || $language->is_default != '1') {
            return [];
        }
        return [
            'name' => 'required|string',
            'meta_keywords' => 'required|string',
            'meta_description' => 'required|string',
            'main_heading' => 'required|string',
            'required_field_label' => 'required|string',
            'main_text' => 'required|string',
            'agree_terms_label' => 'required|string',
            'custom_amount_label' => 'required|string',
            'pay_button_label' => 'required|string',
            'frequency_label' => 'required|string',
            'email_label' => 'required|string',
            'name_label' => 'required|string',
            'phone_label' => 'required|string',
            'designation_label' => 'required|string',
            'designation_option1' => 'required|string',
            'designation_option2' => 'required|string',
            'designation_option3' => 'required|string',
            'designation_option4' => 'required|string',
            'monthly_label' => 'required|string',
            'quarterly_label' => 'required|string',
            'semi_annually_label' => 'required|string',
            'annually_label' => 'required|string',
        ];
    }
}


