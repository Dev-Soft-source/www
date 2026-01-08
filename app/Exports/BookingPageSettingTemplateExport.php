<?php

namespace App\Exports;

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

    public function __construct($format = 'single_column')
    {
        $this->format = $format;
    }

    public function collection()
    {
        if ($this->format === 'single_column') {
            return $this->singleColumnFormat();
        }
        
        return $this->multiColumnFormat();
    }

    protected function singleColumnFormat()
    {
        return collect([
            ['name', 'Booking Page'],
            ['meta_keywords', 'booking, ride, travel'],
            ['meta_description', 'Book your ride easily'],
            ['main_heading', 'Book Your Ride'],
            ['seats_available_label', 'Seats Available'],
            ['seats_available_info_text', 'Number of seats available for this ride'],
            ['cancellation_policy_label', 'Cancellation Policy'],
            ['pricing_label', 'Pricing'],
            ['seat_label', 'Seat'],
            ['booking_fee_label', 'Booking Fee'],
            ['booking_label', 'Booking'],
            ['paypal_label', 'PayPal'],
            ['ride_features_label', 'Ride Features'],
            ['required_fields', '* Required Fields'],
            ['total_label', 'Total'],
            ['message_to_driver_label', 'Message to Driver'],
            ['message_driver_placeholder', 'Type your message here...'],
            ['book_seat_button_label', 'Book Seat'],
            ['like_to_pay_label', 'How would you like to pay?'],
            ['credit_card_label', 'Credit Card'],
            ['select_card_label', 'Select Card'],
            ['add_card_label', 'Add New Card'],
            ['pay_label', 'Pay'],
            ['luggage_label', 'Luggage'],
            ['payment_method_label', 'Payment Method'],
            ['co_passenger_label', 'Co-Passenger'],
            ['coffee_from_wall_label', 'Use Coffee from Wall'],
            ['coffee_from_wall_tooltip', 'This will deduct amount from your coffee wall balance'],
            ['payable_amount_label', 'Payable Amount'],
            ['coffee_from_amount_wall_tooltip', 'Amount deducted from coffee wall'],
            ['tax_label', 'Tax'],
            ['booking_disclaimer_on_time', 'Please be on time for your ride'],
            ['booking_disclaimer_pink_ride', 'This is a pink ride (women only)'],
            ['booking_disclaimer_extra_care_ride', 'This is an extra care ride'],
            ['booking_disclaimer_firm', 'Firm booking - non-refundable'],
            ['booking_term_agree_text', 'I agree to the terms and conditions'],
            ['booking_pink_ride_term_agree_text', 'I agree to the pink ride policy'],
            ['booking_extra_care_ride_term_agree_text', 'I agree to the extra care ride policy'],
            ['firm_cancellation_label_price_section', 'Non-refundable'],
            ['firm_discount_label_price_section', 'Firm Discount'],
            ['firm_your_price_label_price_section', 'Your Price'],
            ['booking_cancellation_limit_exceed', 'Cancellation time limit exceeded'],
        ]);
    }

    protected function multiColumnFormat()
    {
        return collect([
            [
                'Booking Page',
                'booking, ride, travel',
                'Book your ride easily',
                'Book Your Ride',
                'Seats Available',
                'Number of seats available for this ride',
                'Cancellation Policy',
                'Pricing',
                'Seat',
                'Booking Fee',
                'Booking',
                'PayPal',
                'Ride Features',
                '* Required Fields',
                'Total',
                'Message to Driver',
                'Type your message here...',
                'Book Seat',
                'How would you like to pay?',
                'Credit Card',
                'Select Card',
                'Add New Card',
                'Pay',
                'Luggage',
                'Payment Method',
                'Co-Passenger',
                'Use Coffee from Wall',
                'This will deduct amount from your coffee wall balance',
                'Payable Amount',
                'Amount deducted from coffee wall',
                'Tax',
                'Please be on time for your ride',
                'This is a pink ride (women only)',
                'This is an extra care ride',
                'Firm booking - non-refundable',
                'I agree to the terms and conditions',
                'I agree to the pink ride policy',
                'I agree to the extra care ride policy',
                'Non-refundable',
                'Firm Discount',
                'Your Price',
                'Cancellation time limit exceeded',
            ]
        ]);
    }

    public function headings(): array
    {
        if ($this->format === 'single_column') {
            return ['Field Name', 'Translation Value'];
        }
        
        return [
            'name',
            'meta_keywords',
            'meta_description',
            'main_heading',
            'seats_available_label',
            'seats_available_info_text',
            'cancellation_policy_label',
            'pricing_label',
            'seat_label',
            'booking_fee_label',
            'booking_label',
            'paypal_label',
            'ride_features_label',
            'required_fields',
            'total_label',
            'message_to_driver_label',
            'message_driver_placeholder',
            'book_seat_button_label',
            'like_to_pay_label',
            'credit_card_label',
            'select_card_label',
            'add_card_label',
            'pay_label',
            'luggage_label',
            'payment_method_label',
            'co_passenger_label',
            'coffee_from_wall_label',
            'coffee_from_wall_tooltip',
            'payable_amount_label',
            'coffee_from_amount_wall_tooltip',
            'tax_label',
            'booking_disclaimer_on_time',
            'booking_disclaimer_pink_ride',
            'booking_disclaimer_extra_care_ride',
            'booking_disclaimer_firm',
            'booking_term_agree_text',
            'booking_pink_ride_term_agree_text',
            'booking_extra_care_ride_term_agree_text',
            'firm_cancellation_label_price_section',
            'firm_discount_label_price_section',
            'firm_your_price_label_price_section',
            'booking_cancellation_limit_exceed',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 12,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F46E5'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    public function columnWidths(): array
    {
        if ($this->format === 'single_column') {
            return [
                'A' => 45,
                'B' => 60,
            ];
        }
        
        $widths = [];
        foreach (range('A', 'AP') as $col) {
            $widths[$col] = 25;
        }
        
        return $widths;
    }
}

