<?php

namespace App\Imports;

use App\Models\Step4PageSetting;
use App\Models\Step4PageSettingDetail;
use App\Models\Language;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class Step5PageSettingImport implements ToCollection, WithHeadingRow, WithValidation
{
    protected $languageId;

    public function __construct($languageId)
    {
        $this->languageId = $languageId;
    }

    protected function fields(): array
    {
        return [
            'name','meta_keywords','meta_description','main_heading','main_label',
            'country_code_label','country_code_error','phone_label','phone_error',
            'skip_button_label','verify_button_label','verify_code_label','enter_code_label',
            'request_code_label','second_label','save_button_label','send_button_label','logout_button_label'
        ];
    }

    public function collection(Collection $rows)
    {
        $setting = Step4PageSetting::first() ?? Step4PageSetting::create([]);
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
            'step4_page_setting_id' => $setting->id,
            'language_id' => $this->languageId,
        ];
        foreach ($this->fields() as $f) { $payload[$f] = $data[$f] ?? null; }

        Step4PageSettingDetail::updateOrCreate(
            ['step4_page_setting_id' => $setting->id, 'language_id' => $this->languageId],
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
            'main_label' => 'required|string',
            'phone_label' => 'required|string',
            'save_button_label' => 'required|string',
            'send_button_label' => 'required|string',
            'logout_button_label' => 'required|string',
        ];
    }
}


