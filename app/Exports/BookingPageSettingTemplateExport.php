<?php

namespace App\Exports;

use App\Models\BookingPageSetting;
use App\Models\Language;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Illuminate\Support\Collection;

class BookingPageSettingTemplateExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths
{
    protected $format;

    /** @var \Illuminate\Support\Collection|null */
    protected $languages;

    /** @var \App\Models\BookingPageSetting|null */
    protected $existingData;

    /**
     * @param string $format - 'single_column', 'multi_column', or 'all_languages'
     * @param \Illuminate\Support\Collection|array|null $languages - For all_languages format
     * @param \App\Models\BookingPageSetting|null $existingData - For all_languages (with bookingPageSettingDetail loaded)
     */
    public function __construct($format = 'single_column', $languages = null, $existingData = null)
    {
        $this->format = $format;
        $this->languages = $languages ? collect($languages) : null;
        $this->existingData = $existingData;
    }

    public static function getTranslatableFieldsWithDefaults(): array
    {
        return [
            'name' => 'Booking Page',
            'meta_keywords' => 'booking, ride, travel',
            'meta_description' => 'Book your ride easily',
            'main_heading' => 'Book Your Ride',
            'seats_available_label' => 'Seats Available',
            'seats_available_info_text' => 'Number of seats available for this ride',
            'cancellation_policy_label' => 'Cancellation Policy',
            'pricing_label' => 'Pricing',
            'seat_label' => 'Seat',
            'booking_fee_label' => 'Booking Fee',
            'booking_label' => 'Booking',
            'paypal_label' => 'PayPal',
            'ride_features_label' => 'Ride Features',
            'required_fields' => '* Required Fields',
            'total_label' => 'Total',
            'message_to_driver_label' => 'Message to Driver',
            'message_driver_placeholder' => 'Type your message here...',
            'book_seat_button_label' => 'Book Seat',
            'like_to_pay_label' => 'How would you like to pay?',
            'credit_card_label' => 'Credit Card',
            'select_card_label' => 'Select Card',
            'add_card_label' => 'Add New Card',
            'pay_label' => 'Pay',
            'luggage_label' => 'Luggage',
            'payment_method_label' => 'Payment Method',
            'co_passenger_label' => 'Co-Passenger',
            'coffee_from_wall_label' => 'Use Coffee from Wall',
            'coffee_from_wall_tooltip' => 'This will deduct amount from your coffee wall balance',
            'payable_amount_label' => 'Payable Amount',
            'coffee_from_amount_wall_tooltip' => 'Amount deducted from coffee wall',
            'tax_label' => 'Tax',
            'booking_disclaimer_on_time' => 'Please be on time for your ride',
            'booking_disclaimer_pink_ride' => 'This is a pink ride (women only)',
            'booking_disclaimer_extra_care_ride' => 'This is an extra care ride',
            'booking_disclaimer_firm' => 'Firm booking - non-refundable',
            'booking_term_agree_text' => 'I agree to the terms and conditions',
            'booking_pink_ride_term_agree_text' => 'I agree to the pink ride policy',
            'booking_extra_care_ride_term_agree_text' => 'I agree to the extra care ride policy',
            'firm_cancellation_label_price_section' => 'Non-refundable',
            'firm_discount_label_price_section' => 'Firm Discount',
            'firm_your_price_label_price_section' => 'Your Price',
            'booking_cancellation_limit_exceed' => 'Cancellation time limit exceeded',
        ];
    }

    public function collection()
    {
        if ($this->format === 'single_column') {
            return $this->singleColumnFormat();
        }
        if ($this->format === 'all_languages') {
            return $this->allLanguagesFormat();
        }
        return $this->multiColumnFormat();
    }

    protected function singleColumnFormat()
    {
        $fields = static::getTranslatableFieldsWithDefaults();
        return collect($fields)->map(fn ($value, $key) => [$key, $value])->values();
    }

    protected function allLanguagesFormat()
    {
        $languages = $this->languages ?? Language::orderBy('id')->get();
        $fields = static::getTranslatableFieldsWithDefaults();
        $detailsByLang = [];
        if ($this->existingData && $this->existingData->relationLoaded('bookingPageSettingDetail')) {
            foreach ($this->existingData->bookingPageSettingDetail as $d) {
                $detailsByLang[$d->language_id] = $d;
            }
        }
        $rows = [];
        foreach ($fields as $fieldKey => $defaultValue) {
            $row = [$fieldKey];
            foreach ($languages as $lang) {
                $detail = $detailsByLang[$lang->id] ?? null;
                $value = $detail && isset($detail->$fieldKey) ? ($detail->$fieldKey ?? '') : $defaultValue;
                $row[] = $value;
            }
            $rows[] = $row;
        }
        return collect($rows);
    }

    protected function multiColumnFormat()
    {
        return collect([array_values(static::getTranslatableFieldsWithDefaults())]);
    }

    public function headings(): array
    {
        if ($this->format === 'single_column') {
            return ['Field Name', 'Translation Value'];
        }
        if ($this->format === 'all_languages') {
            $languages = $this->languages ?? Language::orderBy('id')->get();
            return array_merge(['Field Name'], $languages->pluck('name')->toArray());
        }
        return array_keys(static::getTranslatableFieldsWithDefaults());
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 12],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ],
        ];
    }

    public function columnWidths(): array
    {
        if ($this->format === 'single_column') {
            return ['A' => 45, 'B' => 60];
        }
        if ($this->format === 'all_languages') {
            $totalCols = ($this->languages ?? Language::orderBy('id')->get())->count() + 1;
            $widths = [];
            for ($colIndex = 1; $colIndex <= $totalCols; $colIndex++) {
                $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
                $widths[$col] = $colIndex === 1 ? 45 : 30;
            }
            return $widths;
        }
        $fields = static::getTranslatableFieldsWithDefaults();
        $widths = [];
        foreach (range(1, count($fields)) as $i) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
            $widths[$col] = 25;
        }
        return $widths;
    }
}
