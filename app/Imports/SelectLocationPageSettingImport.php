<?php

namespace App\Imports;

use App\Exports\SelectLocationPageSettingTemplateExport;
use App\Models\SelectLocationSetting;
use App\Models\SelectLocationSettingDetail;
use App\Models\Language;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class SelectLocationPageSettingImport implements ToCollection, WithHeadingRow, WithValidation
{
    /** @var int|null When set, import is for a single language. When null, import is all_languages format. */
    protected $languageId;

    public function __construct($languageId = null)
    {
        $this->languageId = $languageId;
    }

    protected function fields(): array
    {
        return array_keys(SelectLocationPageSettingTemplateExport::getTranslatableFieldsWithDefaults());
    }

    public function collection(Collection $rows)
    {
        $setting = SelectLocationSetting::first() ?? SelectLocationSetting::create([]);
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

    protected function processAllLanguagesFormat(SelectLocationSetting $setting, Collection $rows): void
    {
        $firstRow = $rows->first();
        $headers = array_keys($firstRow->toArray());
        $fieldNameKey = in_array('field_name', $headers) ? 'field_name' : 'field name';
        $languageColumns = array_diff($headers, [$fieldNameKey]);

        $languages = Language::orderBy('id')->get();
        $nameToId = $languages->mapWithKeys(fn($lang) => [Str::lower($lang->name) => $lang->id])->toArray();
        $validFields = $this->fields();

        foreach ($rows as $row) {
            $row = $row->toArray();
            $fieldName = $row[$fieldNameKey] ?? null;
            if (empty($fieldName) || !in_array($fieldName, $validFields, true)) continue;

            foreach ($languageColumns as $col) {
                $langKey = Str::lower(trim((string) $col));
                if (!isset($nameToId[$langKey])) continue;
                $languageId = $nameToId[$langKey];
                $value = $row[$col] ?? null;

                $detail = SelectLocationSettingDetail::firstOrCreate(
                    ['location_setting_id' => $setting->id, 'language_id' => $languageId],
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


