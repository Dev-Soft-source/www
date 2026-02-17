<?php

namespace Database\Seeders;

use App\Models\MenuDetail;
use Illuminate\Database\Seeder;

class MenuDetailSpanishSeeder extends Seeder
{
    /** Spanish language_id = 9. Inserts footer menu items for Useful links, How it works, Contact us, Terms. */
    public function run(): void
    {
        $languageId = 9;

        $rows = [
            [
                'menu_id' => 2,
                'section_title' => 'Enlaces útiles',
                'menu_items' => [
                    ['id' => 1, 'link' => 'profile', 'name' => 'Mi perfil'],
                    ['id' => 2, 'link' => 'my_rides', 'name' => 'Mis viajes'],
                    ['id' => 3, 'link' => 'signup', 'name' => 'Registrarse'],
                    ['id' => 4, 'link' => 'login', 'name' => 'Iniciar sesión'],
                    ['id' => 5, 'link' => 'post_ride', 'name' => 'Publicar un viaje'],
                    ['id' => 6, 'link' => 'search_ride', 'name' => 'Buscar un viaje'],
                ],
            ],
            [
                'menu_id' => 3,
                'section_title' => 'Cómo funciona',
                'menu_items' => [
                    ['id' => 7, 'link' => 'drivers', 'name' => 'Para conductores'],
                    ['id' => 8, 'link' => 'passengers', 'name' => 'Para pasajeros'],
                    ['id' => 9, 'link' => 'students', 'name' => 'Para estudiantes'],
                ],
            ],
            [
                'menu_id' => 4,
                'section_title' => 'Contáctenos',
                'menu_items' => [
                    ['id' => 10, 'link' => 'contact_us', 'name' => 'Contáctenos / Soporte'],
                    ['id' => 11, 'link' => 'news', 'name' => 'Medios'],
                ],
            ],
            [
                'menu_id' => 5,
                'section_title' => 'Términos',
                'menu_items' => [
                    ['id' => 12, 'link' => 'terms_conditions', 'name' => 'Términos y condiciones'],
                    ['id' => 13, 'link' => 'terms_use', 'name' => 'Términos de uso'],
                    ['id' => 14, 'link' => 'privacy_policy', 'name' => 'Política de privacidad'],
                ],
            ],
        ];

        foreach ($rows as $row) {
            $sectionTitle = $row['section_title'];
            $menuItems = $row['menu_items'];
            unset($row['section_title'], $row['menu_items']);
            MenuDetail::updateOrCreate(
                [
                    'menu_id' => $row['menu_id'],
                    'language_id' => $languageId,
                ],
                ['section_title' => $sectionTitle, 'menu_items' => $menuItems]
            );
        }
    }
}
