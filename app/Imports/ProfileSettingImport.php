<?php

namespace App\Imports;

use App\Models\ProfileSetting;
use App\Models\ProfileSettingDetail;
use App\Models\Language;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ProfileSettingImport implements ToCollection, WithHeadingRow, WithValidation
{
    protected $languageId;

    public function __construct($languageId)
    {
        $this->languageId = $languageId;
    }

    protected function fields(): array
    {
        return [
            'profile_photo_label','my_vehicles_label','main_heading','password_label','my_phone_number_label','my_email_address_label','my_driver_license_label','my_student_card_label','referrals_label'
        ];
    }

    public function collection(Collection $rows)
    {
        $setting = ProfileSetting::first() ?? ProfileSetting::create([]);
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
            'profile_setting_id' => $setting->id,
            'language_id' => $this->languageId,
        ];
        foreach ($this->fields() as $f) { $payload[$f] = $data[$f] ?? null; }

        ProfileSettingDetail::updateOrCreate(
            ['profile_setting_id' => $setting->id, 'language_id' => $this->languageId],
            $payload
        );
    }

    public function rules(): array
    {
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


