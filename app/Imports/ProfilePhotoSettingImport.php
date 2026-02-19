<?php

namespace App\Imports;

use App\Models\ProfilePhotoSetting;
use App\Models\ProfilePhotoSettingDetail;
use App\Models\Language;
use App\Exports\ProfilePhotoSettingTemplateExport;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ProfilePhotoSettingImport implements ToCollection, WithHeadingRow, WithValidation
{
    /** @var int|null When set, import is for a single language. When null, import is all_languages format. */
    protected $languageId;

    public function __construct($languageId = null)
    {
        $this->languageId = $languageId;
    }

    protected function fields(): array
    {
        return array_keys(ProfilePhotoSettingTemplateExport::getTranslatableFieldsWithDefaults());
    }

    public function collection(Collection $rows)
    {
        $setting = ProfilePhotoSetting::first() ?? ProfilePhotoSetting::create([]);
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
            'profile_photo_setting_id' => $setting->id,
            'language_id' => $this->languageId,
        ];
        foreach ($this->fields() as $f) { $payload[$f] = $data[$f] ?? null; }

        ProfilePhotoSettingDetail::updateOrCreate(
            ['profile_photo_setting_id' => $setting->id, 'language_id' => $this->languageId],
            $payload
        );
    }

    /**
     * Process all_languages format: each row = one field, columns = Field Name, then one per language (by header name).
     */
    protected function processAllLanguagesFormat(ProfilePhotoSetting $setting, Collection $rows): void
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

                $detail = ProfilePhotoSettingDetail::firstOrCreate(
                    [
                        'profile_photo_setting_id' => $setting->id,
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
            'name' => 'required|string',
            'mobile_upload_photo_tooltip' => 'required|string',
            'mobile_upload_new_image_button_text' => 'required|string',
            'main_heading' => 'required|string',
            'save_button_text' => 'required|string',
            'upload_profile_photo_placeholder' => 'required|string',
            'choose_file_placeholder' => 'required|string',
            'images_option_placeholder' => 'required|string',
            'photo_error' => 'required|string',
            'mobile_indicate_required_field_label' => 'required|string',
            'sub_heading_text' => 'required|string',
        ];
    }
}


