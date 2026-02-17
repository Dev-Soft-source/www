<?php

namespace Database\Seeders;

use App\Models\SiteText;
use Illuminate\Database\Seeder;

class SiteTextSeeder extends Seeder
{
    /**
     * Seeds common site texts (footer tagline, copyright) for English (1) and Spanish (9).
     * Use {year} in text to be replaced by getTranslatedText(..., ['year' => date('Y')]).
     */
    public function run(): void
    {
        $rows = [
            ['slug' => 'footer_tagline', 'language_id' => 1, 'text' => 'Ride with Purpose. Powered by Community Values.'],
            ['slug' => 'footer_tagline', 'language_id' => 9, 'text' => 'Conduce con propósito. Impulsado por los valores de la comunidad.'],
            ['slug' => 'footer_copyright', 'language_id' => 1, 'text' => '© ProximaRide {year}. All rights reserved'],
            ['slug' => 'footer_copyright', 'language_id' => 9, 'text' => '© ProximaRide {year}. Todos los derechos reservados'],
            ['slug' => 'nav_coffee_on_wall', 'language_id' => 1, 'text' => 'Coffee on the Wall'],
            ['slug' => 'nav_coffee_on_wall', 'language_id' => 9, 'text' => 'Café en la pared'],
            ['slug' => 'nav_view_all', 'language_id' => 1, 'text' => 'View All'],
            ['slug' => 'nav_view_all', 'language_id' => 9, 'text' => 'Ver todo'],
        ];

        foreach ($rows as $row) {
            SiteText::updateOrCreate(
                [
                    'slug' => $row['slug'],
                    'language_id' => $row['language_id'],
                ],
                ['text' => $row['text']]
            );
        }
    }
}
