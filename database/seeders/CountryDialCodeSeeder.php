<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountryDialCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $countries = [
            ['name' => 'United States', 'dial_code' => '+1', 'iso_code' => 'US'],
            ['name' => 'Canada', 'dial_code' => '+1', 'iso_code' => 'CA'],
            ['name' => 'United Kingdom', 'dial_code' => '+44', 'iso_code' => 'GB'],
            ['name' => 'Australia', 'dial_code' => '+61', 'iso_code' => 'AU'],
            ['name' => 'Germany', 'dial_code' => '+49', 'iso_code' => 'DE'],
            ['name' => 'France', 'dial_code' => '+33', 'iso_code' => 'FR'],
            ['name' => 'Spain', 'dial_code' => '+34', 'iso_code' => 'ES'],
            ['name' => 'Italy', 'dial_code' => '+39', 'iso_code' => 'IT'],
            ['name' => 'Japan', 'dial_code' => '+81', 'iso_code' => 'JP'],
            ['name' => 'South Korea', 'dial_code' => '+82', 'iso_code' => 'KR'],
            ['name' => 'China', 'dial_code' => '+86', 'iso_code' => 'CN'],
            ['name' => 'India', 'dial_code' => '+91', 'iso_code' => 'IN'],
            ['name' => 'Brazil', 'dial_code' => '+55', 'iso_code' => 'BR'],
            ['name' => 'Mexico', 'dial_code' => '+52', 'iso_code' => 'MX'],
            ['name' => 'Argentina', 'dial_code' => '+54', 'iso_code' => 'AR'],
            ['name' => 'Russia', 'dial_code' => '+7', 'iso_code' => 'RU'],
            ['name' => 'South Africa', 'dial_code' => '+27', 'iso_code' => 'ZA'],
            ['name' => 'Egypt', 'dial_code' => '+20', 'iso_code' => 'EG'],
            ['name' => 'Nigeria', 'dial_code' => '+234', 'iso_code' => 'NG'],
            ['name' => 'Kenya', 'dial_code' => '+254', 'iso_code' => 'KE'],
            ['name' => 'Saudi Arabia', 'dial_code' => '+966', 'iso_code' => 'SA'],
            ['name' => 'UAE', 'dial_code' => '+971', 'iso_code' => 'AE'],
            ['name' => 'Turkey', 'dial_code' => '+90', 'iso_code' => 'TR'],
            ['name' => 'Israel', 'dial_code' => '+972', 'iso_code' => 'IL'],
            ['name' => 'Poland', 'dial_code' => '+48', 'iso_code' => 'PL'],
            ['name' => 'Netherlands', 'dial_code' => '+31', 'iso_code' => 'NL'],
            ['name' => 'Belgium', 'dial_code' => '+32', 'iso_code' => 'BE'],
            ['name' => 'Switzerland', 'dial_code' => '+41', 'iso_code' => 'CH'],
            ['name' => 'Austria', 'dial_code' => '+43', 'iso_code' => 'AT'],
            ['name' => 'Sweden', 'dial_code' => '+46', 'iso_code' => 'SE'],
            ['name' => 'Norway', 'dial_code' => '+47', 'iso_code' => 'NO'],
            ['name' => 'Denmark', 'dial_code' => '+45', 'iso_code' => 'DK'],
            ['name' => 'Finland', 'dial_code' => '+358', 'iso_code' => 'FI'],
            ['name' => 'Ireland', 'dial_code' => '+353', 'iso_code' => 'IE'],
            ['name' => 'Portugal', 'dial_code' => '+351', 'iso_code' => 'PT'],
        ];

        foreach ($countries as $country) {
            DB::table('countries')->updateOrInsert(
                ['name' => $country['name']],
                $country
            );
        }
    }
}