<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Illuminate\Support\Collection;

class BillingAddressSettingTemplateExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths
{
    protected $format;

    /**
     * Constructor to set template format
     * @param string $format - 'single_column' or 'multi_column'
     */
    public function __construct($format = 'single_column')
    {
        $this->format = $format;
    }

    /**
     * @return Collection
     */
    public function collection()
    {
        if ($this->format === 'single_column') {
            return $this->singleColumnFormat();
        }
        
        return $this->multiColumnFormat();
    }

    /**
     * Single column format with field names and sample values
     */
    protected function singleColumnFormat()
    {
        return collect([
            // Main heading
            ['main_heading', 'Add a New Card'],
            
            // Required field indicators
            ['mobile_indicate_required_field_label', '* Indicates required fields'],
            ['indicate_field_label', '* Indicates required fields'],
            
            // Card name fields
            ['name_on_card_label', "Cardholder's Name"],
            ['name_on_card_placeholder', "Cardholder's Name"],
            ['card_name_placeholder', "Cardholder's Name"],
            
            // Card number fields
            ['card_number_label', 'Card Number'],
            ['card_number_placeholder', 'Card Number'],
            
            // Card type fields
            ['mobile_card_type_label', 'Card Type'],
            ['mobile_card_type_placholder', 'Select'],
            ['select_card_type_text', 'Select'],
            
            // Expiry date fields (Mobile)
            ['mobile_expiry_date_label', 'Expiry Date'],
            ['mobile_month_placeholder', 'Month'],
            ['mobile_year_placeholder', 'Year'],
            
            // Expiry date fields (Web)
            ['web_expiry_month_label', 'Expiry Month'],
            ['web_expiry_month_placeholder', 'MM/YY'],
            ['expiry_month_placeholder', 'MM/YY'],
            
            // Security code (CVV/CVC)
            ['security_code_label', 'CVV / CVC'],
            ['security_code_palceholder', 'A 3- or 4-digit number printed on your card (usually on the back).'],
            ['cvc_placeholder', 'A 3- or 4-digit number printed on your card (usually on the back).'],
            
            // Billing address section
            ['mobile_billing_address_label', 'Billing Address'],
            
            // Street address fields
            ['mobile_street_name_label', 'Street Address (number and name)'],
            ['mobile_street_name_placeholder', '(including apartment or unit number if applicable:)'],
            
            // House/Apartment number
            ['mobile_house_number_label', 'Apartment / Suite / Unit Number (optional)'],
            ['mobile_house_number_placeholder', 'Apartment / Suite / Unit Number'],
            
            // City fields
            ['mobile_city_label', 'City'],
            ['mobile_city_placeholder', 'City'],
            
            // Province/State fields
            ['mobile_province_label', 'Province'],
            ['mobile_province_placeholder', 'Province'],
            
            // Country fields
            ['mobile_country_label', 'Country'],
            ['mobile_country_placeholder', 'Country'],
            
            // Postal code fields
            ['mobile_postal_code_label', 'Postal Code / ZIP Code'],
            ['mobile_postal_code_placeholder', 'Postal Code / ZIP Code'],
            
            // Primary card option
            ['mobile_primary_card_placeholder', 'Set as Primary Card'],
            
            // Save button
            ['save_button_text', 'Add Card'],
        ]);
    }

    /**
     * Multi-column format with all fields in header and one row for values
     */
    protected function multiColumnFormat()
    {
        return collect([
            [
                // main_heading
                'Add a New Card',
                
                // mobile_indicate_required_field_label
                '* Indicates required fields',
                
                // indicate_field_label
                '* Indicates required fields',
                
                // name_on_card_label
                "Cardholder's Name",
                
                // name_on_card_placeholder
                "Cardholder's Name",
                
                // card_name_placeholder
                "Cardholder's Name",
                
                // card_number_label
                'Card Number',
                
                // card_number_placeholder
                'Card Number',
                
                // mobile_card_type_label
                'Card Type',
                
                // mobile_card_type_placholder
                'Select',
                
                // select_card_type_text
                'Select',
                
                // mobile_expiry_date_label
                'Expiry Date',
                
                // mobile_month_placeholder
                'Month',
                
                // mobile_year_placeholder
                'Year',
                
                // web_expiry_month_label
                'Expiry Month',
                
                // web_expiry_month_placeholder
                'MM/YY',
                
                // expiry_month_placeholder
                'MM/YY',
                
                // security_code_label
                'CVV / CVC',
                
                // security_code_palceholder
                'A 3- or 4-digit number printed on your card (usually on the back).',
                
                // cvc_placeholder
                'A 3- or 4-digit number printed on your card (usually on the back).',
                
                // mobile_billing_address_label
                'Billing Address',
                
                // mobile_street_name_label
                'Street Address (number and name)',
                
                // mobile_street_name_placeholder
                '(including apartment or unit number if applicable:)',
                
                // mobile_house_number_label
                'Apartment / Suite / Unit Number (optional)',
                
                // mobile_house_number_placeholder
                'Apartment / Suite / Unit Number',
                
                // mobile_city_label
                'City',
                
                // mobile_city_placeholder
                'City',
                
                // mobile_province_label
                'Province',
                
                // mobile_province_placeholder
                'Province',
                
                // mobile_country_label
                'Country',
                
                // mobile_country_placeholder
                'Country',
                
                // mobile_postal_code_label
                'Postal Code / ZIP Code',
                
                // mobile_postal_code_placeholder
                'Postal Code / ZIP Code',
                
                // mobile_primary_card_placeholder
                'Set as Primary Card',
                
                // save_button_text
                'Add Card',
            ]
        ]);
    }

    /**
     * Headings for the Excel file
     */
    public function headings(): array
    {
        if ($this->format === 'single_column') {
            return ['Field Name', 'Translation Value'];
        }
        
        // Multi-column format - field names as headers (exact order as in database)
        return [
            'main_heading',
            'mobile_indicate_required_field_label',
            'indicate_field_label',
            'name_on_card_label',
            'name_on_card_placeholder',
            'card_name_placeholder',
            'card_number_label',
            'card_number_placeholder',
            'mobile_card_type_label',
            'mobile_card_type_placholder',
            'select_card_type_text',
            'mobile_expiry_date_label',
            'mobile_month_placeholder',
            'mobile_year_placeholder',
            'web_expiry_month_label',
            'web_expiry_month_placeholder',
            'expiry_month_placeholder',
            'security_code_label',
            'security_code_palceholder',
            'cvc_placeholder',
            'mobile_billing_address_label',
            'mobile_street_name_label',
            'mobile_street_name_placeholder',
            'mobile_house_number_label',
            'mobile_house_number_placeholder',
            'mobile_city_label',
            'mobile_city_placeholder',
            'mobile_province_label',
            'mobile_province_placeholder',
            'mobile_country_label',
            'mobile_country_placeholder',
            'mobile_postal_code_label',
            'mobile_postal_code_placeholder',
            'mobile_primary_card_placeholder',
            'save_button_text',
        ];
    }

    /**
     * Style the worksheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the header row
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 12,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F46E5'], // Indigo color
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    /**
     * Set column widths
     */
    public function columnWidths(): array
    {
        if ($this->format === 'single_column') {
            return [
                'A' => 40,  // field_name column
                'B' => 50,  // value column
            ];
        }
        
        // For multi-column, set all columns to 20
        $widths = [];
        foreach (range('A', 'AI') as $col) {
            $widths[$col] = 20;
        }
        
        return $widths;
    }
}

