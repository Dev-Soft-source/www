<?php

namespace App\Imports;

use App\Models\ProfileSetting;
use App\Models\ProfileSettingDetail;
use App\Models\Language;
use App\Exports\ProfileSettingTemplateExport;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ProfileSettingImport implements ToCollection, WithHeadingRow, WithValidation
{
    /** @var int|null When set, import is for a single language. When null, import is all_languages format. */
    protected $languageId;

    public function __construct($languageId = null)
    {
        $this->languageId = $languageId;
    }

    protected function fields(): array
    {
        return array_keys(ProfileSettingTemplateExport::getTranslatableFieldsWithDefaults());
    }

    public function collection(Collection $rows)
    {
        $setting = ProfileSetting::first() ?? ProfileSetting::create([]);
        if ($rows->isEmpty()) return;

        $firstRow = $rows->first();
        $keys = array_keys($firstRow->toArray());

        // All-languages format: first column is field_name, rest are language columns
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
            'profile_setting_id' => $setting->id,
            'language_id' => $this->languageId,
        ];
        foreach ($this->fields() as $f) { $payload[$f] = $data[$f] ?? null; }

        ProfileSettingDetail::updateOrCreate(
            ['profile_setting_id' => $setting->id, 'language_id' => $this->languageId],
            $payload
        );
    }

    /**
     * Process all_languages format: each row = one field, columns = Field Name, then one per language (by header name).
     */
    protected function processAllLanguagesFormat(ProfileSetting $setting, Collection $rows): void
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
            if (empty($fieldName) || !in_array($fieldName, $validFields, true)) {
                continue;
            }

            foreach ($languageColumns as $col) {
                $langKey = Str::lower(trim((string) $col));
                if (!isset($nameToId[$langKey])) {
                    continue;
                }
                $languageId = $nameToId[$langKey];
                $value = $row[$col] ?? null;

                $detail = ProfileSettingDetail::firstOrCreate(
                    [
                        'profile_setting_id' => $setting->id,
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

    public function rules(): array
    {
        if ($this->languageId === null) {
            return [];
        }
        $language = Language::find($this->languageId);
        if (!$language || $language->is_default != '1') return [];
        return [
            'profile_photo_label' => 'required|string',
            'my_vehicles_label' => 'required|string',
            'main_heading' => 'required|string',
            'password_label' => 'required|string',
            'my_phone_number_label' => 'required|string',
            'my_email_address_label' => 'required|string',
            'my_driver_license_label' => 'required|string',
            'my_student_card_label' => 'required|string',
            'referrals_label' => 'required|string',
        ];
    }
}


