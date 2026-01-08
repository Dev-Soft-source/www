<?php

namespace App\Imports;

use App\Models\EditProfilePageSetting;
use App\Models\EditProfilePageSettingDetail;
use App\Models\Language;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class EditProfilePageSettingImport implements ToCollection, WithHeadingRow, WithValidation
{
    protected $languageId;

    public function __construct($languageId)
    {
        $this->languageId = $languageId;
    }

    public function collection(Collection $rows)
    {
        Log::info('Starting Edit Profile Page Settings Excel import for language ID: ' . $this->languageId);

        $setting = EditProfilePageSetting::first();
        if (!$setting) $setting = EditProfilePageSetting::create([]);
        if ($rows->isEmpty()) return;

        $firstRow = $rows->first();
        $keys = array_keys($firstRow->toArray());
        $isSingleColumn = isset($keys[0]) && (in_array('field_name', $keys) && (in_array('value', $keys) || in_array('translation_value', $keys)));

        if ($isSingleColumn) {
            foreach ($rows as $row) $this->processSingleColumnFormat($setting, $row);
        } else {
            $this->processMultiColumnFormat($setting, $firstRow);
        }
    }

    protected function allowedFields(): array
    {
        return [
            'name','min_bio_label','passenger_driven_label','main_heading','rides_taken_label','km_shared_label','review_label','reply_label','link_review_label','review_heading','edit_profile_text','first_name_label','first_name_placeholder','last_name_label','last_name_placeholder','gender_label','male_label','female_label','prefer_no_to_say_label','dob_label','dob_placeholder','country_label','country_placeholder','state_label','state_placeholder','city_label','city_placeholder','address_label','address_placeholder','zip_label','mini_bio_label','mini_bio_placeholder','govt_id_label','govt_id_text','image_placeholder','choose_file_placeholder','image_option_placeholder','new_image_button_text','save_button_text','joined_label','passenger_label','vehicle_info_label','year_old_label','replied_label','response_label','reply_heading_label','reply_placeholder','reply_submit_button_label','profile_label'
        ];
    }

    protected function processSingleColumnFormat($setting, $row)
    {
        $fieldName = $row['field_name'] ?? null;
        $value = $row['translation_value'] ?? $row['value'] ?? null;
        if (empty($fieldName) || $value === null || $value === '') return;
        $fieldName = strtolower(trim($fieldName));
        if (!in_array($fieldName, $this->allowedFields())) return;

        $detail = EditProfilePageSettingDetail::where('edit_profile_id', $setting->id)
            ->where('language_id', $this->languageId)
            ->first();

        if ($detail) {
            $detail->$fieldName = $value;
            $detail->save();
        } else {
            EditProfilePageSettingDetail::create([
                'edit_profile_id' => $setting->id,
                'language_id' => $this->languageId,
                $fieldName => $value,
            ]);
        }
    }

    protected function processMultiColumnFormat($setting, $row)
    {
        $fields = [
            'edit_profile_id' => $setting->id,
            'language_id' => $this->languageId,
        ];
        foreach ($this->allowedFields() as $f) {
            $fields[$f] = $row[$f] ?? null;
        }

        EditProfilePageSettingDetail::updateOrCreate(
            [
                'edit_profile_id' => $setting->id,
                'language_id' => $this->languageId,
            ],
            $fields
        );
    }

    public function rules(): array
    {
        $language = Language::find($this->languageId);
        if (!$language || $language->is_default != '1') return [];
        // minimal required according to service
        return [
            'min_bio_label' => 'required|string',
            'passenger_driven_label' => 'required|string',
            'main_heading' => 'required|string',
            'rides_taken_label' => 'required|string',
        ];
    }
}


