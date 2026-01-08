<?php

namespace App\Imports;

use App\Models\PasswordSetting;
use App\Models\PasswordSettingDetail;
use App\Models\Language;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class PasswordSettingImport implements ToCollection, WithHeadingRow, WithValidation
{
    protected $languageId;

    public function __construct($languageId)
    {
        $this->languageId = $languageId;
    }

    protected function sheetFields(): array
    {
        // Exact fields the sheet should contain (no 'name') and in required order
        return [
            'main_heading',
            'mobile_indicate_required_field_label',
            'password_description_text',
            'current_password_label',
            'current_password_placeholder',
            'current_password_error',
            'new_password_label',
            'new_password_placeholder',
            'new_password_error',
            'confirm_new_password_label',
            'confirm_new_password_placeholder',
            'confirm_new_password_error',
            'mobile_password_description_text',
            'update_button_text',
        ];
    }

    protected function persistableFields(): array
    {
        // Actual DB columns in password_setting_detail, matching the sheet fields
        return [
            'main_heading',
            'mobile_indicate_required_field_label',
            'password_description_text',
            'current_password_label',
            'current_password_placeholder',
            'current_password_error',
            'new_password_label',
            'new_password_placeholder',
            'new_password_error',
            'confirm_new_password_label',
            'confirm_new_password_placeholder',
            'confirm_new_password_error',
            'mobile_password_description_text',
            'update_button_text',
        ];
    }

    public function collection(Collection $rows)
    {
        $setting = PasswordSetting::first() ?? PasswordSetting::create([]);
        if ($rows->isEmpty()) return;

        $firstRow = $rows->first();
        $keys = array_keys($firstRow->toArray());
        $isSingleColumn = isset($keys[0]) && (in_array('field_name', $keys) && (in_array('value', $keys) || in_array('translation_value', $keys)));

        if ($isSingleColumn) {
            $data = [];
            foreach ($rows as $row) {
                $name = strtolower(trim($row['field_name'] ?? ''));
                if (!$name || !in_array($name, $this->sheetFields())) continue;
                $data[$name] = $row['translation_value'] ?? $row['value'] ?? null;
            }
            $this->applyData($setting, $data);
        } else {
            $this->applyData($setting, $firstRow->toArray());
        }
    }

    protected function applyData($setting, array $data): void
    {
        $payload = [
            'password_setting_id' => $setting->id,
            'language_id' => $this->languageId,
        ];
        foreach ($this->persistableFields() as $f) { $payload[$f] = $data[$f] ?? null; }

        PasswordSettingDetail::updateOrCreate(
            [
                'password_setting_id' => $setting->id,
                'language_id' => $this->languageId,
            ],
            $payload
        );
    }

    public function rules(): array
    {
        $language = Language::find($this->languageId);
        if (!$language || $language->is_default != '1') return [];
        return [
            'password_description_text' => 'required|string',
            'mobile_indicate_required_field_label' => 'required|string',
            'main_heading' => 'required|string',
            'current_password_label' => 'required|string',
            'new_password_label' => 'required|string',
            'confirm_new_password_label' => 'required|string',
            'update_button_text' => 'required|string',
        ];
    }
}


