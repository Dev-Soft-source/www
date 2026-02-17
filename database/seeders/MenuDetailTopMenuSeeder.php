<?php

namespace Database\Seeders;

use App\Models\MenuDetail;
use Illuminate\Database\Seeder;

/**
 * Seeds Top Menu (menu_id 1) for all languages from languages table:
 * 1=English, 6=French, 7=Arabic, 9=Spanish, 10=Tagalog, 12=Chinese,
 * 13=Hindi, 14=Urdu, 15=Russian, 16=Ukrainian.
 */
class MenuDetailTopMenuSeeder extends Seeder
{
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
                'language_id' => 6,
                'menu_items' => [
                    ['id' => 1, 'link' => 'students', 'name' => 'Étudiants'],
                    ['id' => 2, 'link' => 'post_ride', 'name' => 'Publier un trajet'],
                    ['id' => 3, 'link' => 'search_ride', 'name' => 'Trouver un trajet'],
                ],
            ],
            [
                'menu_id' => 1,
                'language_id' => 7,
                'menu_items' => [
                    ['id' => 1, 'link' => 'students', 'name' => 'الطلاب'],
                    ['id' => 2, 'link' => 'post_ride', 'name' => 'نشر رحلة'],
                    ['id' => 3, 'link' => 'search_ride', 'name' => 'البحث عن رحلة'],
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
            [
                'menu_id' => 1,
                'language_id' => 10,
                'menu_items' => [
                    ['id' => 1, 'link' => 'students', 'name' => 'Mga estudyante'],
                    ['id' => 2, 'link' => 'post_ride', 'name' => 'Mag-post ng biyahe'],
                    ['id' => 3, 'link' => 'search_ride', 'name' => 'Maghanap ng biyahe'],
                ],
            ],
            [
                'menu_id' => 1,
                'language_id' => 12,
                'menu_items' => [
                    ['id' => 1, 'link' => 'students', 'name' => '学生'],
                    ['id' => 2, 'link' => 'post_ride', 'name' => '发布行程'],
                    ['id' => 3, 'link' => 'search_ride', 'name' => '搜索行程'],
                ],
            ],
            [
                'menu_id' => 1,
                'language_id' => 13,
                'menu_items' => [
                    ['id' => 1, 'link' => 'students', 'name' => 'छात्र'],
                    ['id' => 2, 'link' => 'post_ride', 'name' => 'सवारी पोस्ट करें'],
                    ['id' => 3, 'link' => 'search_ride', 'name' => 'सवारी खोजें'],
                ],
            ],
            [
                'menu_id' => 1,
                'language_id' => 14,
                'menu_items' => [
                    ['id' => 1, 'link' => 'students', 'name' => 'طالب علم'],
                    ['id' => 2, 'link' => 'post_ride', 'name' => 'سواری پوسٹ کریں'],
                    ['id' => 3, 'link' => 'search_ride', 'name' => 'سواری تلاش کریں'],
                ],
            ],
            [
                'menu_id' => 1,
                'language_id' => 15,
                'menu_items' => [
                    ['id' => 1, 'link' => 'students', 'name' => 'Студенты'],
                    ['id' => 2, 'link' => 'post_ride', 'name' => 'Опубликовать поездку'],
                    ['id' => 3, 'link' => 'search_ride', 'name' => 'Найти поездку'],
                ],
            ],
            [
                'menu_id' => 1,
                'language_id' => 16,
                'menu_items' => [
                    ['id' => 1, 'link' => 'students', 'name' => 'Студенти'],
                    ['id' => 2, 'link' => 'post_ride', 'name' => 'Опублікувати поїздку'],
                    ['id' => 3, 'link' => 'search_ride', 'name' => 'Знайти поїздку'],
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
