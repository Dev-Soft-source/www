<?php

namespace App\Imports;

use App\Models\ProfilePhotoSetting;
use App\Models\ProfilePhotoSettingDetail;
use App\Models\Language;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ProfilePhotoSettingImport implements ToCollection, WithHeadingRow, WithValidation
{
    protected $languageId;

    public function __construct($languageId)
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
            'profile_photo_setting_id' => $setting->id,
            'language_id' => $this->languageId,
        ];
        foreach ($this->fields() as $f) { $payload[$f] = $data[$f] ?? null; }

        ProfilePhotoSettingDetail::updateOrCreate(
            ['profile_photo_setting_id' => $setting->id, 'language_id' => $this->languageId],
            $payload
        );
    }

    public function rules(): array
    {
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


