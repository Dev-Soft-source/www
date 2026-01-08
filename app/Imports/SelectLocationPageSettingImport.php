<?php

namespace App\Imports;

use App\Models\SelectLocationSetting;
use App\Models\SelectLocationSettingDetail;
use App\Models\Language;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class SelectLocationPageSettingImport implements ToCollection, WithHeadingRow, WithValidation
{
    protected $languageId;

    public function __construct($languageId)
    {
        $this->languageId = $languageId;
    }

    protected function fields(): array
    {
        return [
            'select_origin_label','search_origin_label','no_origin_label',
            'select_destination_label','search_destination_label','no_destination_label',
            'select_country_label','search_country_label','no_country_label',
            'select_state_label','select_state_first_label','search_state_label','no_state_label',
            'select_city_label','search_city_label','no_city_label'
        ];
    }

    public function collection(Collection $rows)
    {
        $setting = SelectLocationSetting::first() ?? SelectLocationSetting::create([]);
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
            'location_setting_id' => $setting->id,
            'language_id' => $this->languageId,
        ];
        foreach ($this->fields() as $f) { $payload[$f] = $data[$f] ?? null; }

        SelectLocationSettingDetail::updateOrCreate(
            ['location_setting_id' => $setting->id, 'language_id' => $this->languageId],
            $payload
        );
    }

    public function rules(): array
    {
        $language = Language::find($this->languageId);
        if (!$language || $language->is_default != '1') return [];
        return [
            'select_origin_label' => 'required|string',
            'search_origin_label' => 'required|string',
            'no_origin_label' => 'required|string',
            'select_destination_label' => 'required|string',
            'search_destination_label' => 'required|string',
            'no_destination_label' => 'required|string',
            'select_country_label' => 'required|string',
            'search_country_label' => 'required|string',
            'no_country_label' => 'required|string',
            'select_state_label' => 'required|string',
            'select_state_first_label' => 'required|string',
            'search_state_label' => 'required|string',
            'no_state_label' => 'required|string',
            'select_city_label' => 'required|string',
            'search_city_label' => 'required|string',
            'no_city_label' => 'required|string',
        ];
    }
}


