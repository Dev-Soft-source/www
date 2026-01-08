<?php

use App\Models\Language;
use Illuminate\Support\Facades\Session;

if (!function_exists('getAllLanguages')) {
    function getAllLanguages()
    {
        return Language::get();
    }
}

if (!function_exists('getDefaultLanguage')) {
    function getDefaultLanguage($isWeb = false)
    {
        $lang = '';
        $webLanguage = Session::get('webLanguage');
        if ($isWeb && isset($webLanguage) && !empty($webLanguage)) {
            $lang = Language::where('id', $webLanguage)->first();
        } else {
            $lang = Language::whereIsDefault(1)->first();
        }

        return $lang ? $lang : Language::first();
    }
}

if (!function_exists("updateLangByAbber")) {
    function updateLangByAbber($abbreviation)
    {
        $language = Language::where('abbreviation', $abbreviation)->first();
        if (!$language) {
            $language = getDefaultLanguage(true);
        }
        Session::put('webLanguage', $language->id);
        return 1;
    }
}

if (!function_exists('numberToWords')) {
    function numberToWords($number)
    {
        if (!is_numeric($number)) {
            return $number;
        }

        $f = new \NumberFormatter('en', \NumberFormatter::SPELLOUT);
        $words = $f->format($number);
        return ucfirst($words);
    }
}

if (!function_exists('normalizePhoneNumber')) {
    /**
     * Normalize phone number to E.164 format using Laravel-Phone package
     * Handles inputs like: +1234567890, 001234567890, 0111234567890, 1234567890
     * 
     * @param string $phoneNumber The input phone number
     * @param string $countryDialCode The country dial code (e.g., "+1") or ISO code
     * @return string Normalized phone number in E.164 format
     */
    function normalizePhoneNumber($phoneNumber, $countryDialCode = null)
    {
        if (!$phoneNumber) {
            return '';
        }

        try {
            // If countryDialCode starts with +, extract the country ISO code
            $country = null;
            if ($countryDialCode) {
                if (str_starts_with($countryDialCode, '+')) {
                    // Try to determine country from dial code
                    $dialCode = str_replace('+', '', $countryDialCode);
                    $countryModel = \App\Models\Country::where('dial_code', '+' . $dialCode)->first();
                    $country = $countryModel ? $countryModel->iso_code : 'US';
                } else {
                    $country = $countryDialCode;
                }
            }

            // Use Laravel-Phone to parse and format
            $phone = phone($phoneNumber, $country ?: 'US');
            return $phone->formatE164();
        } catch (\Exception $e) {
            // Fallback to original logic if Laravel-Phone fails
            $cleaned = preg_replace('/[^0-9+]/', '', $phoneNumber);
            
            if (str_starts_with($cleaned, '+')) {
                return $cleaned;
            }
            
            if (str_starts_with($cleaned, '00')) {
                return '+' . substr($cleaned, 2);
            }
            
            if (str_starts_with($cleaned, '011')) {
                return '+' . substr($cleaned, 3);
            }
            
            if ($countryDialCode) {
                $dialCode = str_replace('+', '', $countryDialCode);
                return '+' . $dialCode . $cleaned;
            }
            
            return '+1' . $cleaned;
        }
    }
}

if (!function_exists('formatPhoneForDisplay')) {
    /**
     * Format phone number for display using Laravel-Phone package
     * 
     * @param string $phoneNumber E.164 format phone number
     * @return string Formatted phone number for display
     */
    function formatPhoneForDisplay($phoneNumber)
    {
        if (!$phoneNumber) {
            return $phoneNumber;
        }

        try {
            // Use Laravel-Phone to format for display
            $phone = phone($phoneNumber);
            return $phone->formatInternational();
        } catch (\Exception $e) {
            // Fallback to original logic
            if (!str_starts_with($phoneNumber, '+')) {
                return $phoneNumber;
            }

            $number = substr($phoneNumber, 1);
            
            if (str_starts_with($number, '1') && strlen($number) === 11) {
                return '+1 (' . substr($number, 1, 3) . ') ' . substr($number, 4, 3) . '-' . substr($number, 7);
            }
            
            if (strlen($number) >= 10) {
                return '+' . substr($number, 0, -10) . ' ' . substr($number, -10);
            }
            
            return $phoneNumber;
        }
    }
}

if (!function_exists('validatePhoneNumber')) {
    /**
     * Validate phone number format using Laravel-Phone package
     * 
     * @param string $phoneNumber The phone number to validate
     * @param string $country Country code for validation (optional)
     * @return bool True if valid, false otherwise
     */
    function validatePhoneNumber($phoneNumber, $country = null)
    {
        if (!$phoneNumber) {
            return false;
        }

        try {
            // Use Laravel-Phone to validate
            $phone = phone($phoneNumber, $country ?: 'US');
            return $phone->isValid();
        } catch (\Exception $e) {
            // Fallback to basic validation
            $cleaned = preg_replace('/[^0-9+]/', '', $phoneNumber);
            $digitCount = strlen(preg_replace('/[^0-9]/', '', $cleaned));
            
            return $digitCount >= 7 && $digitCount <= 15;
        }
    }
}

