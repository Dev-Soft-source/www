<?php

namespace App\Imports;

use App\Models\Step2PageSetting;
use App\Models\Step2PageSettingDetail;
use App\Models\Language;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class Step2PageSettingImport implements ToCollection, WithHeadingRow, WithValidation
{
    protected $languageId;

    public function __construct($languageId)
    {
        $this->languageId = $languageId;
    }

    protected function fields(): array
    {
        return [
            'name','meta_keywords','meta_description','main_heading',
            'photo_error','photo_placeholder','mobile_photo_label','mobile_choose_file_label',
            'photo_label','skip_button_label','next_button_label','logout_button_label','sub_heading_text'
        ];
    }

    public function collection(Collection $rows)
    {
        $setting = Step2PageSetting::first() ?? Step2PageSetting::create([]);
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
            'step2_page_setting_id' => $setting->id,
            'language_id' => $this->languageId,
        ];
        foreach ($this->fields() as $f) { $payload[$f] = $data[$f] ?? null; }

        Step2PageSettingDetail::updateOrCreate(
            ['step2_page_setting_id' => $setting->id, 'language_id' => $this->languageId],
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
            'photo_error' => 'required|string',
            'photo_placeholder' => 'required|string',
            'photo_label' => 'required|string',
            'skip_button_label' => 'required|string',
            'next_button_label' => 'required|string',
        ];
    }
}


