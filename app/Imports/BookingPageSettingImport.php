<?php

namespace App\Imports;

use App\Models\BookingPageSetting;
use App\Models\BookingPageSettingDetail;
use App\Models\Language;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class BookingPageSettingImport implements ToCollection, WithHeadingRow, WithValidation
{
    protected $languageId;

    public function __construct($languageId)
    {
        $this->languageId = $languageId;
    }

    public function collection(Collection $rows)
    {
        Log::info('Starting Booking Page Settings Excel import for language ID: ' . $this->languageId);
        Log::info('Total rows to process: ' . $rows->count());
        
        // Get or create booking page setting
        $bookingPageSetting = BookingPageSetting::first();
        if (!$bookingPageSetting) {
            $bookingPageSetting = BookingPageSetting::create([]);
        }

        Log::info('Booking Page Setting ID: ' . $bookingPageSetting->id);

        // Check format by looking at first row keys
        if ($rows->isEmpty()) {
            Log::warning('No rows found in Excel file');
            return;
        }

        $firstRow = $rows->first();
        $keys = array_keys($firstRow->toArray());
        Log::info('Excel columns detected: ' . json_encode($keys));

        // Single column format check: has 'field_name' and ('value' OR 'translation_value')
        $isSingleColumn = isset($keys[0]) && 
                          (in_array('field_name', $keys) && 
                          (in_array('value', $keys) || in_array('translation_value', $keys)));

        if ($isSingleColumn) {
            Log::info('Detected SINGLE COLUMN format');
            foreach ($rows as $index => $row) {
                Log::info("Processing row {$index}: " . json_encode($row->toArray()));
                $this->processSingleColumnFormat($bookingPageSetting, $row);
            }
        } else {
            Log::info('Detected MULTI COLUMN format');
            // Multi-column format - only process first data row
            $this->processMultiColumnFormat($bookingPageSetting, $firstRow);
        }
        
        Log::info('Booking Page Settings Excel import completed successfully');
    }

    protected function processSingleColumnFormat($bookingPageSetting, $row)
    {
        // Get field name
        $fieldName = $row['field_name'] ?? null;
        
        // Get value - check both 'value' and 'translation_value'
        $value = $row['translation_value'] ?? $row['value'] ?? null;

        // Skip if field name or value is empty
        if (empty($fieldName) || empty($value)) {
            Log::warning("Skipping row - Field: {$fieldName}, Value: {$value}");
            return;
        }

        Log::info("Processing field: {$fieldName} = {$value}");

        // Get existing record or prepare for creation
        $detail = BookingPageSettingDetail::where('booking_page_setting_id', $bookingPageSetting->id)
            ->where('language_id', $this->languageId)
            ->first();

        if ($detail) {
            // Update existing record - update only this specific field
            $detail->$fieldName = $value;
            $detail->save();
            Log::info("Updated existing record - Field: {$fieldName}");
        } else {
            // Create new record with this field
            BookingPageSettingDetail::create([
                'booking_page_setting_id' => $bookingPageSetting->id,
                'language_id' => $this->languageId,
                $fieldName => $value,
            ]);
            Log::info("Created new record with field: {$fieldName}");
        }
    }

    protected function processMultiColumnFormat($bookingPageSetting, $row)
    {
        // Map all fields
        $fields = [
            'booking_page_setting_id' => $bookingPageSetting->id,
            'language_id' => $this->languageId,
            'name' => $row['name'] ?? null,
            'meta_keywords' => $row['meta_keywords'] ?? null,
            'meta_description' => $row['meta_description'] ?? null,
            'main_heading' => $row['main_heading'] ?? null,
            'seats_available_label' => $row['seats_available_label'] ?? null,
            'seats_available_info_text' => $row['seats_available_info_text'] ?? null,
            'cancellation_policy_label' => $row['cancellation_policy_label'] ?? null,
            'pricing_label' => $row['pricing_label'] ?? null,
            'seat_label' => $row['seat_label'] ?? null,
            'booking_fee_label' => $row['booking_fee_label'] ?? null,
            'booking_label' => $row['booking_label'] ?? null,
            'paypal_label' => $row['paypal_label'] ?? null,
            'ride_features_label' => $row['ride_features_label'] ?? null,
            'required_fields' => $row['required_fields'] ?? null,
            'total_label' => $row['total_label'] ?? null,
            'message_to_driver_label' => $row['message_to_driver_label'] ?? null,
            'message_driver_placeholder' => $row['message_driver_placeholder'] ?? null,
            'book_seat_button_label' => $row['book_seat_button_label'] ?? null,
            'like_to_pay_label' => $row['like_to_pay_label'] ?? null,
            'credit_card_label' => $row['credit_card_label'] ?? null,
            'select_card_label' => $row['select_card_label'] ?? null,
            'add_card_label' => $row['add_card_label'] ?? null,
            'pay_label' => $row['pay_label'] ?? null,
            'luggage_label' => $row['luggage_label'] ?? null,
            'payment_method_label' => $row['payment_method_label'] ?? null,
            'co_passenger_label' => $row['co_passenger_label'] ?? null,
            'coffee_from_wall_label' => $row['coffee_from_wall_label'] ?? null,
            'coffee_from_wall_tooltip' => $row['coffee_from_wall_tooltip'] ?? null,
            'payable_amount_label' => $row['payable_amount_label'] ?? null,
            'coffee_from_amount_wall_tooltip' => $row['coffee_from_amount_wall_tooltip'] ?? null,
            'tax_label' => $row['tax_label'] ?? null,
            'booking_disclaimer_on_time' => $row['booking_disclaimer_on_time'] ?? null,
            'booking_disclaimer_pink_ride' => $row['booking_disclaimer_pink_ride'] ?? null,
            'booking_disclaimer_extra_care_ride' => $row['booking_disclaimer_extra_care_ride'] ?? null,
            'booking_disclaimer_firm' => $row['booking_disclaimer_firm'] ?? null,
            'booking_term_agree_text' => $row['booking_term_agree_text'] ?? null,
            'booking_pink_ride_term_agree_text' => $row['booking_pink_ride_term_agree_text'] ?? null,
            'booking_extra_care_ride_term_agree_text' => $row['booking_extra_care_ride_term_agree_text'] ?? null,
            'firm_cancellation_label_price_section' => $row['firm_cancellation_label_price_section'] ?? null,
            'firm_discount_label_price_section' => $row['firm_discount_label_price_section'] ?? null,
            'firm_your_price_label_price_section' => $row['firm_your_price_label_price_section'] ?? null,
            'booking_cancellation_limit_exceed' => $row['booking_cancellation_limit_exceed'] ?? null,
        ];

        // Update or create the booking page setting detail
        BookingPageSettingDetail::updateOrCreate(
            [
                'booking_page_setting_id' => $bookingPageSetting->id,
                'language_id' => $this->languageId,
            ],
            $fields
        );
    }

    public function rules(): array
    {
        $language = Language::find($this->languageId);
        
        if (!$language) {
            return [];
        }

        // Required fields for default language only
        $rules = [];
        
        if ($language->is_default == '1') {
            $rules = [
                'name' => 'required|string',
                'meta_keywords' => 'required|string',
                'meta_description' => 'required|string',
                'main_heading' => 'required|string',
                'seats_available_label' => 'required|string',
                'seats_available_info_text' => 'required|string',
                'cancellation_policy_label' => 'required|string',
                'pricing_label' => 'required|string',
                'seat_label' => 'required|string',
                'booking_fee_label' => 'required|string',
                'booking_label' => 'required|string',
                'paypal_label' => 'required|string',
                'ride_features_label' => 'required|string',
                'required_fields' => 'required|string',
                'total_label' => 'required|string',
                'message_to_driver_label' => 'required|string',
                'message_driver_placeholder' => 'required|string',
                'book_seat_button_label' => 'required|string',
                'like_to_pay_label' => 'required|string',
                'credit_card_label' => 'required|string',
                'select_card_label' => 'required|string',
                'add_card_label' => 'required|string',
                'pay_label' => 'required|string',
                'co_passenger_label' => 'required|string',
                'payment_method_label' => 'required|string',
                'luggage_label' => 'required|string',
                'coffee_from_wall_label' => 'required|string',
                'coffee_from_wall_tooltip' => 'required|string',
                'payable_amount_label' => 'required|string',
                'coffee_from_amount_wall_tooltip' => 'required|string',
                'tax_label' => 'required|string',
                'booking_disclaimer_on_time' => 'required|string',
                'booking_disclaimer_pink_ride' => 'required|string',
                'booking_disclaimer_extra_care_ride' => 'required|string',
                'booking_disclaimer_firm' => 'required|string',
                'booking_term_agree_text' => 'required|string',
                'booking_pink_ride_term_agree_text' => 'required|string',
                'booking_extra_care_ride_term_agree_text' => 'required|string',
                'firm_cancellation_label_price_section' => 'required|string',
                'firm_discount_label_price_section' => 'required|string',
                'firm_your_price_label_price_section' => 'required|string',
                'booking_cancellation_limit_exceed' => 'required|string',
            ];
        }

        return $rules;
    }
}

