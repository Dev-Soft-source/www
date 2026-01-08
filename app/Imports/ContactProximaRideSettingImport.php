<?php

namespace App\Imports;

use App\Models\ContactProximaRideSetting;
use App\Models\ContactProximaRideSettingDetail;
use App\Models\Language;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ContactProximaRideSettingImport implements ToCollection, WithHeadingRow, WithValidation
{
    protected $languageId;
    // Friendly headings used by admins → internal keys
    protected $aliases = [
        'main heading' => 'main_heading',
        'meta description' => 'meta_description',
        'meta keywords' => 'meta_keywords',
        'required indicate' => 'mobile_indicate_required_field_label',
        'your full name label' => 'your_full_name_label',
        'name placeholder' => 'your_full_name_placeholder',
        'your message label' => 'your_message_label',
        'message placeholder' => 'message_placeholder', // not persisted
        'your email label' => 'your_email_address_label',
        'email placeholder' => 'your_email_address_placeholder',
        'your phone label' => 'your_phone_label',
        'phone placeholder' => 'your_phone_placeholder',
        'submit button' => 'submit_button_text',
    ];

    public function __construct($languageId)
    {
        $this->languageId = $languageId;
    }

    public function collection(Collection $rows)
    {
        Log::info('Starting Contact ProximaRide Settings Excel import for language ID: ' . $this->languageId);

        $setting = ContactProximaRideSetting::first();
        if (!$setting) {
            $setting = ContactProximaRideSetting::create([]);
        }

        if ($rows->isEmpty()) {
            return;
        }

        $firstRow = $rows->first();
        $keys = array_keys($firstRow->toArray());

        $isSingleColumn = isset($keys[0]) && (in_array('field_name', $keys) && (in_array('value', $keys) || in_array('translation_value', $keys)));

        if ($isSingleColumn) {
            foreach ($rows as $row) {
                $this->processSingleColumnFormat($setting, $row);
            }
        } else {
            $this->processMultiColumnFormat($setting, $firstRow);
        }
    }

    protected function processSingleColumnFormat($setting, $row)
    {
        $fieldName = $row['field_name'] ?? null;
        $value = $row['translation_value'] ?? $row['value'] ?? null;
        if (empty($fieldName) || $value === null || $value === '') return;

        // Normalize using aliases when present
        $normalized = strtolower(trim($fieldName));
        if (isset($this->aliases[$normalized])) {
            $fieldName = $this->aliases[$normalized];
        }

        // Allow these 13 logical fields in the sheet; we will only persist DB-backed ones
        $sheetFields = [
            'main_heading',
            'meta_description',
            'meta_keywords',
            'mobile_indicate_required_field_label', // Required Indicate
            'your_full_name_label',
            'your_full_name_placeholder', // Name placeholder
            'your_message_label',
            'message_placeholder',
            'your_email_address_label',
            'your_email_address_placeholder',
            'your_phone_label',
            'your_phone_placeholder',
            'submit_button_text',
        ];
        // Actual DB columns we can persist
        $persistable = [
            'main_heading',
            'mobile_indicate_required_field_label',
            'your_full_name_label',
            'your_full_name_placeholder',
            'your_phone_label',
            'your_phone_placeholder',
            'your_email_address_label',
            'your_email_address_placeholder',
            'your_message_label',
            'submit_button_text',
        ];
        if (!in_array($fieldName, $sheetFields)) {
            Log::warning('Skipping unknown field for Contact Proxima import: ' . $fieldName);
            return;
        }
        if (!in_array($fieldName, $persistable)) {
            Log::info('Ignoring non-persisted sheet field for Contact Proxima import: ' . $fieldName);
            return;
        }

        $detail = ContactProximaRideSettingDetail::where('contact_pr_setting_id', $setting->id)
            ->where('language_id', $this->languageId)
            ->first();

        if ($detail) {
            $detail->$fieldName = $value;
            $detail->save();
        } else {
            ContactProximaRideSettingDetail::create([
                'contact_pr_setting_id' => $setting->id,
                'language_id' => $this->languageId,
                $fieldName => $value,
            ]);
        }
    }

    protected function processMultiColumnFormat($setting, $row)
    {
        // Build case-insensitive map of row
        $raw = $row->toArray();
        $lowerMap = [];
        foreach ($raw as $k => $v) { $lowerMap[strtolower($k)] = $v; }
        $get = function(string $primary, array $alts = []) use ($lowerMap) {
            $candidates = array_merge([$primary], $alts);
            foreach ($candidates as $k) {
                $lk = strtolower($k);
                if (array_key_exists($lk, $lowerMap)) return $lowerMap[$lk];
            }
            return null;
        };

        $fields = [
            'contact_pr_setting_id' => $setting->id,
            'language_id' => $this->languageId,
            // Persist only DB-backed columns
            'main_heading' => $get('main_heading', ['Main heading']),
            'mobile_indicate_required_field_label' => $get('mobile_indicate_required_field_label', ['Required Indicate']),
            'your_full_name_label' => $get('your_full_name_label', ['Your Full Name Label']),
            'your_full_name_placeholder' => $get('your_full_name_placeholder', ['Name placeholder']),
            'your_phone_label' => $get('your_phone_label', ['Your Phone Label']),
            'your_phone_placeholder' => $get('your_phone_placeholder', ['Phone placeholder']),
            'your_email_address_label' => $get('your_email_address_label', ['Your Email Label']),
            'your_email_address_placeholder' => $get('your_email_address_placeholder', ['Email placeholder']),
            'your_message_label' => $get('your_message_label', ['Your Message Label']),
            // message placeholder intentionally ignored (no DB column)
            'submit_button_text' => $get('submit_button_text', ['Submit Button']),
        ];

        ContactProximaRideSettingDetail::updateOrCreate(
            [
                'contact_pr_setting_id' => $setting->id,
                'language_id' => $this->languageId,
            ],
            $fields
        );
    }

    public function rules(): array
    {
        $language = Language::find($this->languageId);
        if (!$language || $language->is_default != '1') return [];
        return [
            'main_heading' => 'required|string',
            'mobile_indicate_required_field_label' => 'required|string',
            'your_full_name_label' => 'required|string',
            'your_phone_label' => 'required|string',
            'your_email_address_label' => 'required|string',
            'your_message_label' => 'required|string',
            'submit_button_text' => 'required|string',
        ];
    }
}


