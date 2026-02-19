<?php

namespace App\Imports;

use App\Models\ProfilePhotoSetting;
use App\Models\ProfilePhotoSettingDetail;
use App\Models\Language;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ProfilePhotoSettingImport implements ToCollection, WithHeadingRow, WithValidation
{
    /** @var int|null When null, import expects all_languages format (Field Name + one column per language). */
    protected $languageId;

    public function __construct($languageId = null)
    {
        $this->languageId = $languageId;
    }

    protected function fields(): array
    {
        return [
            'name','mobile_upload_photo_tooltip','mobile_upload_new_image_button_text','main_heading','save_button_text','upload_profile_photo_placeholder','choose_file_placeholder','images_option_placeholder','photo_error','mobile_indicate_required_field_label','sub_heading_text'
        ];
    }

    public function collection(Collection $rows)
    {
        $setting = ProfilePhotoSetting::first() ?? ProfilePhotoSetting::create([]);
        if ($rows->isEmpty()) {
            Log::warning('No rows found in Profile Photo Excel file');
            return;
        }

        $firstRow = $rows->first();
        $keys = array_keys($firstRow->toArray());

        $isAllLanguages = $this->languageId === null
            && (in_array('field_name', $keys) || in_array('field name', $keys))
            && count($keys) > 1;

        if ($isAllLanguages) {
            $this->processAllLanguagesFormat($setting, $rows);
            Log::info('Profile Photo Settings Excel import (all languages) completed successfully');
            return;
        }

        $isSingle = in_array('field_name', $keys) && (in_array('value', $keys) || in_array('translation_value', $keys));

        $data = [];
        if ($isSingle && $this->languageId !== null) {
            foreach ($rows as $row) {
                $k = strtolower(trim($row['field_name'] ?? ''));
                if (!$k || !in_array($k, $this->fields())) continue;
                $data[$k] = $row['translation_value'] ?? $row['value'] ?? null;
            }
            $this->applyData($setting, $data);
        } elseif ($this->languageId !== null) {
            $data = $firstRow->toArray();
            $this->applyData($setting, $data);
        }
    }

    protected function processAllLanguagesFormat(ProfilePhotoSetting $setting, Collection $rows): void
    {
        $firstRow = $rows->first();
        $headers = array_keys($firstRow->toArray());
        $fieldNameKey = in_array('field_name', $headers) ? 'field_name' : (in_array('field name', $headers) ? 'field name' : 'Field Name');
        $languageColumns = array_diff($headers, [$fieldNameKey]);
        $languages = Language::orderBy('id')->get();
        $nameToId = $languages->mapWithKeys(fn ($lang) => [Str::lower($lang->name) => $lang->id])->toArray();
        $validFields = $this->fields();

        foreach ($rows as $row) {
            $row = $row->toArray();
            $fieldName = isset($row[$fieldNameKey]) ? strtolower(trim((string) $row[$fieldNameKey])) : null;
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
                $detail = ProfilePhotoSettingDetail::firstOrCreate(
                    [
                        'profile_photo_setting_id' => $setting->id,
                        'language_id' => $languageId,
                    ],
                    []
                );
                $detail->$fieldName = $value;
                $detail->save();
            }
        }
    }

    protected function applyData(ProfilePhotoSetting $setting, array $data): void
    {
        $payload = [
            'profile_photo_setting_id' => $setting->id,
            'language_id' => $this->languageId,
        ];
        foreach ($this->fields() as $f) {
            $payload[$f] = $data[$f] ?? null;
        }
        ProfilePhotoSettingDetail::updateOrCreate(
            ['profile_photo_setting_id' => $setting->id, 'language_id' => $this->languageId],
            $payload
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


