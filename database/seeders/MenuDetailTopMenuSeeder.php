<?php

namespace Database\Seeders;

use App\Models\MenuDetail;
use Illuminate\Database\Seeder;

class MenuDetailTopMenuSeeder extends Seeder
{
    /** Seeds Top Menu (menu_id 1) for English (language_id 1) and Spanish (language_id 9). */
    public function run(): void
    {
        $rows = [
            [
                'menu_id' => 1,
                'language_id' => 1,
                'menu_items' => [
                    ['id' => 1, 'link' => 'students', 'name' => 'Students'],
                    ['id' => 2, 'link' => 'post_ride', 'name' => 'Post a Ride'],
                    ['id' => 3, 'link' => 'search_ride', 'name' => 'Find a Ride'],
                ],
            ],
            [
                'menu_id' => 1,
                'language_id' => 9,
                'menu_items' => [
                    ['id' => 1, 'link' => 'students', 'name' => 'Estudiantes'],
                    ['id' => 2, 'link' => 'post_ride', 'name' => 'Publicar un viaje'],
                    ['id' => 3, 'link' => 'search_ride', 'name' => 'Buscar un viaje'],
                ],
            ],
        ];

        foreach ($rows as $row) {
            MenuDetail::updateOrCreate(
                [
                    'menu_id' => $row['menu_id'],
                    'language_id' => $row['language_id'],
                ],
                ['menu_items' => $row['menu_items']]
            );
        }
    }
}
