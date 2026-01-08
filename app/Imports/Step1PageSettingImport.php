<?php

namespace App\Imports;

use App\Models\Step1PageSetting;
use App\Models\Step1PageSettingDetail;
use App\Models\Language;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class Step1PageSettingImport implements ToCollection, WithHeadingRow, WithValidation
{
    protected $languageId;

    public function __construct($languageId)
    {
        $this->languageId = $languageId;
    }

    protected function fields(): array
    {
        return [
            'name','meta_keywords','meta_description','main_heading','required_label','first_name_label','last_name_label','gender_label','male_option_label','female_option_label','prefer_option_label','dob_label','country_label','state_label','city_label','zip_code_label','bio_label','button_label',
            // extended fields present in service (persisted columns only)
            'first_name_error','last_name_error','gender_error','dob_error','country_error','state_error','city_error','zip_code_error','bio_error','logout_button_label','bio_placeholder'
        ];
    }

    public function collection(Collection $rows)
    {
        $setting = Step1PageSetting::first() ?? Step1PageSetting::create([]);
        if ($rows->isEmpty()) return;
        $firstRow = $rows->first();
        $keys = array_keys($firstRow->toArray());
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
            'step1_page_setting_id' => $setting->id,
            'language_id' => $this->languageId,
        ];
        foreach ($this->fields() as $f) { $payload[$f] = $data[$f] ?? null; }

        Step1PageSettingDetail::updateOrCreate(
            ['step1_page_setting_id' => $setting->id, 'language_id' => $this->languageId],
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
            'required_label' => 'required|string',
            'first_name_label' => 'required|string',
            'last_name_label' => 'required|string',
            'gender_label' => 'required|string',
            'male_option_label' => 'required|string',
            'female_option_label' => 'required|string',
            'prefer_option_label' => 'required|string',
            'dob_label' => 'required|string',
            'country_label' => 'required|string',
            'state_label' => 'required|string',
            'city_label' => 'required|string',
            'zip_code_label' => 'required|string',
            'bio_label' => 'required|string',
            'button_label' => 'required|string',
        ];
    }
}


