<?php

use App\Models\FooterSetting;
use App\Models\Language;
use App\Models\MenuDetail;
use App\Models\NotificationMessage;
use App\Models\SiteText;
use App\Models\SiteTextDetail;
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

if (!function_exists('trans_choice_string')) {
    // Use trans_choice_string helper
    function trans_choice_string($string, $count, $replace = [])
    {
        $replace = array_merge(['count' => $count], $replace);
        
        // Laravel has internal function to parse plural forms
        $translator = app('translator');
        return $translator->choice($string, $count, $replace);
    }
}

if (!function_exists('getTranslatedText')) {
    /**
     * Get a translated sentence/text by slug for the given language.
     * Falls back to language_id 1 (English) if no row exists for the requested language.
     * Placeholders in the stored text (e.g. {year}) are replaced using the $replacements array.
     *
     * @param string $slug Unique key for the text (e.g. 'footer_tagline', 'footer_copyright')
     * @param int|object $lang Language id or Language model instance
     * @param array $replacements Optional key => value to replace {key} in the text (e.g. ['year' => date('Y')])
     * @param string $default Default string if no record found
     * @return string
     */
    function getTranslatedText($slug, $lang, array $replacements = [], $default = '')
    {
        $languageId = is_object($lang) ? (int) $lang->id : (int) $lang;
        if ($languageId < 1) {
            $languageId = 1;
        }

        // $row = SiteText::where('slug', $slug)
        //     ->where('language_id', $languageId)
        //     ->first();


        // if (!$row) {
        //     $row = SiteText::where('slug', $slug)
        //         ->where('language_id', 1)
        //         ->first();
        // }

        // $text = ($row && $row->text !== null && $row->text !== '') ? $row->text : $default;

        $siteText = SiteTextDetail::getByLanguageKeyedBySlug($languageId, 1);
        $text = $siteText[$slug];

        foreach ($replacements as $key => $value) {
            $text = str_replace('{' . $key . '}', (string) $value, $text);
        }

        return $text;
    }
}

if (!function_exists('getFooterSetting')) {
    /**
     * Get footer menu settings for the given language.
     * Returns an object with footerSettingDetail (array of 4 sections: Useful links, How it works, Contact us, Terms).
     * If no menu_detail exists for the language, falls back to language_id 1 (English).
     *
     * @param int|object $lang Language id or Language model instance
     * @return object { footerSettingDetail: array }
     */
    function getFooterSetting($lang)
    {
        $languageId = is_object($lang) ? (int) $lang->id : (int) $lang;
        if ($languageId < 1) {
            $languageId = 1;
        }

        $footerSetting = FooterSetting::first();
        $footerSettingDetail = [];

        $menuIds = $footerSetting
            ? array_filter([
                $footerSetting->menu1,
                $footerSetting->menu2,
                $footerSetting->menu3,
                $footerSetting->menu4,
            ])
            : [2, 3, 4, 5];

        if (empty($menuIds)) {
            return (object) ['footerSettingDetail' => []];
        }

        if (!$footerSetting) {
            $footerSetting = (object) ['footerSettingDetail' => []];
        }

        foreach ($menuIds as $menuId) {
            $menu = \App\Models\Menu::find($menuId);
            if (!$menu) {
                $footerSettingDetail[] = (object) ['sectionTitle' => '', 'menuItems' => []];
                continue;
            }

            $detail = MenuDetail::where('menu_id', $menuId)
                ->where('language_id', $languageId)
                ->first();

            if (!$detail) {
                $detail = MenuDetail::where('menu_id', $menuId)
                    ->where('language_id', 1)
                    ->first();
            }

            $menuItems = $detail && !empty($detail->menu_items) ? $detail->menu_items : [];
            $sectionTitle = ($detail && !empty($detail->section_title)) ? $detail->section_title : $menu->name;
            $footerSettingDetail[] = (object) [
                'sectionTitle' => $sectionTitle,
                'menuItems' => $menuItems,
            ];
        }
        
        $footerSetting->footerSettingDetail = $footerSettingDetail;
        return $footerSetting;
    }
}

if (!function_exists('getTopMenuSetting')) {
    /**
     * Get Top Menu (menu_id 1) items for the given language.
     * Returns an object with topMenuItems (array of {id, link, name}).
     * If no menu_detail exists for the language, falls back to language_id 1 (English).
     * If no Top Menu data exists, returns default items (Students, Post a Ride, Find a Ride).
     *
     * @param int|object $lang Language id or Language model instance
     * @return object { topMenuItems: array }
     */
    function getTopMenuSetting($lang)
    {
        $languageId = is_object($lang) ? (int) $lang->id : (int) $lang;
        if ($languageId < 1) {
            $languageId = 1;
        }

        $menuId = 1; // Top Menu
        $detail = MenuDetail::where('menu_id', $menuId)
            ->where('language_id', $languageId)
            ->first();

        if (!$detail) {
            $detail = MenuDetail::where('menu_id', $menuId)
                ->where('language_id', 1)
                ->first();
        }

        $topMenuItems = ($detail && !empty($detail->menu_items)) ? $detail->menu_items : [
            ['id' => 1, 'link' => 'students', 'name' => 'Students'],
            ['id' => 2, 'link' => 'post_ride', 'name' => 'Post a Ride'],
            ['id' => 3, 'link' => 'search_ride', 'name' => 'Find a Ride'],
        ];

        return (object) ['topMenuItems' => $topMenuItems];
    }
}

if (!function_exists('getNavbarMenuSetting')) {
    /**
     * Get navbar menu items for profile dropdown (menu_id 6) and guest nav (menu_id 7).
     * Returns profileDropdownItems (My Profile, My Rides, My Chats, Sign out) and guestNavItems (Coffee on the Wall, Log in / Sign up).
     * If no menu_detail for the language, falls back to language_id 1. Use link 'logout' for Sign out (no route).
     *
     * @param int|object $lang Language id or Language model instance
     * @return object { profileDropdownItems: array, guestNavItems: array }
     */
    function getNavbarMenuSetting($lang)
    {
        $languageId = is_object($lang) ? (int) $lang->id : (int) $lang;
        if ($languageId < 1) {
            $languageId = 1;
        }

        $defaultProfile = [
            ['id' => 1, 'link' => 'profile', 'name' => 'My Profile'],
            ['id' => 2, 'link' => 'my_rides', 'name' => 'My Rides'],
            ['id' => 3, 'link' => 'my_chats', 'name' => 'My Chats'],
            ['id' => 4, 'link' => 'logout', 'name' => 'Sign out'],
        ];
        $defaultGuest = [
            ['id' => 1, 'link' => 'coffee_on_wall', 'name' => 'Coffee on the Wall'],
            ['id' => 2, 'link' => 'login', 'name' => 'Log in / Sign up'],
        ];

        foreach ([6 => 'profileDropdownItems', 7 => 'guestNavItems'] as $menuId => $key) {
            $detail = MenuDetail::where('menu_id', $menuId)
                ->where('language_id', $languageId)
                ->first();
            if (!$detail) {
                $detail = MenuDetail::where('menu_id', $menuId)
                    ->where('language_id', 1)
                    ->first();
            }
            $items = ($detail && !empty($detail->menu_items)) ? $detail->menu_items : ($menuId === 6 ? $defaultProfile : $defaultGuest);
            $$key = $items;
        }

        return (object) [
            'profileDropdownItems' => $profileDropdownItems,
            'guestNavItems' => $guestNavItems,
        ];
    }
}

if (!function_exists('convertNumberToWordsFallback')) {
    /**
     * Fallback function to convert numbers to words without NumberFormatter
     * Supports numbers 0-999
     */
    function convertNumberToWordsFallback($number)
    {
        $number = (int)$number;
        
        $ones = [
            '', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine',
            'ten', 'eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen',
            'seventeen', 'eighteen', 'nineteen'
        ];
        
        $tens = [
            '', '', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety'
        ];
        
        if ($number == 0) {
            return 'Zero';
        }
        
        if ($number < 20) {
            return ucfirst($ones[$number]);
        }
        
        if ($number < 100) {
            $ten = floor($number / 10);
            $one = $number % 10;
            $result = $tens[$ten];
            if ($one > 0) {
                $result .= '-' . $ones[$one];
            }
            return ucfirst($result);
        }
        
        if ($number < 1000) {
            $hundred = floor($number / 100);
            $remainder = $number % 100;
            $result = $ones[$hundred] . ' hundred';
            if ($remainder > 0) {
                $result .= ' ' . convertNumberToWordsFallback($remainder);
            }
            return ucfirst($result);
        }
        
        // For numbers 1000+, just return the number as a string
        return (string)$number;
    }
}

if (!function_exists('numberToWords')) {
    function numberToWords($number)
    {
        if (!is_numeric($number)) {
            return $number;
        }

        // Check if NumberFormatter class is available (requires intl extension)
        if (class_exists('NumberFormatter')) {
            try {
                $f = new \NumberFormatter('en', \NumberFormatter::SPELLOUT);
                $words = $f->format($number);
                return ucfirst($words);
            } catch (\Exception $e) {
                // Fall through to fallback implementation
            }
        }
        
        // Fallback implementation if NumberFormatter is not available
        return convertNumberToWordsFallback($number);
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

if (!function_exists('isNorthAmericanNumber')) {
    /**
     * Check if a phone number is North American (+1)
     * 
     * @param string $phoneNumber E.164 format phone number
     * @return bool True if North American (+1), false otherwise
     */
    function isNorthAmericanNumber($phoneNumber)
    {
        if (!$phoneNumber) {
            return false;
        }
        
        // Check if phone number starts with +1
        return str_starts_with($phoneNumber, '+1');
    }
}

if (!function_exists('formatDepartureDateTime')) {
    /**
     * Format departure date and time with locale support
     * 
     * @param string|null $date The departure date
     * @param object|null $selectedLanguage The selected language object
     * @param object|null $rideDetailPage The ride detail page settings object
     * @return array Returns array with 'dateLabel', 'timeLabel', 'date', 'time' keys
     */
    function formatDepartureDateTime($date, $selectedLanguage = null, $rideDetailPage = null)
    {
        // Get date locale from selected language or fallback to app locale
        $dateLocale = optional($selectedLanguage)->locale
            ?? optional($selectedLanguage)->abbreviation
            ?? app()->getLocale();

        // Parse the date with locale support
        $departureAt = $date
            ? \Carbon\Carbon::parse($date)->locale($dateLocale)
            : null;

        // Format the date in translated format
        $departureDateLabel = $departureAt
            ? $departureAt->translatedFormat('F d, Y')
            : 'N/A';

        // Format the time
        $departureTime = $departureAt
            ? $departureAt->format('h:i A')
            : null;

        // Handle special cases for noon and midnight
        $departureTimeLabel = $departureTime === '12:00 PM'
            ? (optional($rideDetailPage)->noon_label ?? 'Noon')
            : ($departureTime === '12:00 AM' 
                ? (optional($rideDetailPage)->midnight_label ?? 'Midnight') 
                : $departureTime);

        return [
            'dateLabel' => $departureDateLabel,
            'timeLabel' => $departureTimeLabel ?? 'N/A',
            'date' => $departureAt,
            'time' => $departureTime,
        ];
    }
}

if (!function_exists('getNotificationMessageText')) {
    /**
     * Resolve a notification message template by slug for the given user/language.
     * Stored templates can contain placeholders like {first_name}, {seats}, {code}.
     */
    function getNotificationMessageText(string $slug, $langOrUser = null, array $replacements = [], string $default = ''): string
    {
        $language = null;

        if (is_object($langOrUser) && isset($langOrUser->lang)) {
            $language = Language::where('abbreviation', $langOrUser->lang)->first();
        } elseif (is_object($langOrUser) && isset($langOrUser->id) && get_class($langOrUser) === Language::class) {
            $language = $langOrUser;
        } elseif (is_numeric($langOrUser)) {
            $language = Language::find((int) $langOrUser);
        } elseif (is_string($langOrUser) && $langOrUser !== '') {
            $language = Language::where('abbreviation', $langOrUser)->first();
        }

        if (!$language) {
            $language = getDefaultLanguage(true);
        }

        $defaultLanguage = Language::where('is_default', 1)->first() ?: $language;
        $template = NotificationMessage::with('details')
            ->where('slug', $slug)
            ->first();

        $text = $default;
        if ($template) {
            $detail = $template->details->firstWhere('language_id', $language->id)
                ?: $template->details->firstWhere('language_id', $defaultLanguage?->id);
            $text = trim((string) ($detail?->message ?? '')) ?: $default;
        }

        foreach ($replacements as $key => $value) {
            $text = str_replace('{' . $key . '}', (string) $value, $text);
        }

        return $text;
    }
}
