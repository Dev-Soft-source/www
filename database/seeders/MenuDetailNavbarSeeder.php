<?php

namespace Database\Seeders;

use App\Models\MenuDetail;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeds Profile dropdown (menu_id 6) and Guest nav (menu_id 7) for all languages:
 * 1=English, 6=French, 7=Arabic, 9=Spanish, 10=Tagalog, 12=Chinese,
 * 13=Hindi, 14=Urdu, 15=Russian, 16=Ukrainian.
 */
class MenuDetailNavbarSeeder extends Seeder
{
    public function run(): void
    {
        if (DB::table('menus')->whereIn('id', [6, 7])->count() < 2) {
            DB::table('menus')->insertOrIgnore([
                ['id' => 6, 'name' => 'Profile dropdown', 'is_top_menu' => 0, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 7, 'name' => 'Guest nav', 'is_top_menu' => 0, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        $profileMenuId = 6;
        $guestMenuId = 7;

        $data = [
            1 => [
                'profile' => [
                    ['id' => 1, 'link' => 'profile', 'name' => 'My Profile'],
                    ['id' => 2, 'link' => 'my_rides', 'name' => 'My Rides'],
                    ['id' => 3, 'link' => 'my_chats', 'name' => 'My Chats'],
                    ['id' => 4, 'link' => 'logout', 'name' => 'Sign out'],
                ],
                'guest' => [
                    ['id' => 1, 'link' => 'coffee_on_wall', 'name' => 'Coffee on the Wall'],
                    ['id' => 2, 'link' => 'login', 'name' => 'Log in / Sign up'],
                ],
            ],
            6 => [
                'profile' => [
                    ['id' => 1, 'link' => 'profile', 'name' => 'Mon profil'],
                    ['id' => 2, 'link' => 'my_rides', 'name' => 'Mes trajets'],
                    ['id' => 3, 'link' => 'my_chats', 'name' => 'Mes discussions'],
                    ['id' => 4, 'link' => 'logout', 'name' => 'Déconnexion'],
                ],
                'guest' => [
                    ['id' => 1, 'link' => 'coffee_on_wall', 'name' => 'Café sur le mur'],
                    ['id' => 2, 'link' => 'login', 'name' => 'Connexion / Inscription'],
                ],
            ],
            7 => [
                'profile' => [
                    ['id' => 1, 'link' => 'profile', 'name' => 'ملفي الشخصي'],
                    ['id' => 2, 'link' => 'my_rides', 'name' => 'رحلاتي'],
                    ['id' => 3, 'link' => 'my_chats', 'name' => 'محادثاتي'],
                    ['id' => 4, 'link' => 'logout', 'name' => 'تسجيل الخروج'],
                ],
                'guest' => [
                    ['id' => 1, 'link' => 'coffee_on_wall', 'name' => 'القهوة على الحائط'],
                    ['id' => 2, 'link' => 'login', 'name' => 'تسجيل الدخول / التسجيل'],
                ],
            ],
            9 => [
                'profile' => [
                    ['id' => 1, 'link' => 'profile', 'name' => 'Mi perfil'],
                    ['id' => 2, 'link' => 'my_rides', 'name' => 'Mis viajes'],
                    ['id' => 3, 'link' => 'my_chats', 'name' => 'Mis chats'],
                    ['id' => 4, 'link' => 'logout', 'name' => 'Cerrar sesión'],
                ],
                'guest' => [
                    ['id' => 1, 'link' => 'coffee_on_wall', 'name' => 'Café en la pared'],
                    ['id' => 2, 'link' => 'login', 'name' => 'Iniciar sesión / Registrarse'],
                ],
            ],
            10 => [
                'profile' => [
                    ['id' => 1, 'link' => 'profile', 'name' => 'Aking profile'],
                    ['id' => 2, 'link' => 'my_rides', 'name' => 'Aking mga biyahe'],
                    ['id' => 3, 'link' => 'my_chats', 'name' => 'Aking mga chat'],
                    ['id' => 4, 'link' => 'logout', 'name' => 'Mag-sign out'],
                ],
                'guest' => [
                    ['id' => 1, 'link' => 'coffee_on_wall', 'name' => 'Kape sa dingding'],
                    ['id' => 2, 'link' => 'login', 'name' => 'Mag-log in / Mag-sign up'],
                ],
            ],
            12 => [
                'profile' => [
                    ['id' => 1, 'link' => 'profile', 'name' => '我的资料'],
                    ['id' => 2, 'link' => 'my_rides', 'name' => '我的行程'],
                    ['id' => 3, 'link' => 'my_chats', 'name' => '我的聊天'],
                    ['id' => 4, 'link' => 'logout', 'name' => '退出登录'],
                ],
                'guest' => [
                    ['id' => 1, 'link' => 'coffee_on_wall', 'name' => '墙上的咖啡'],
                    ['id' => 2, 'link' => 'login', 'name' => '登录/注册'],
                ],
            ],
            13 => [
                'profile' => [
                    ['id' => 1, 'link' => 'profile', 'name' => 'मेरा प्रोफाइल'],
                    ['id' => 2, 'link' => 'my_rides', 'name' => 'मेरी सवारी'],
                    ['id' => 3, 'link' => 'my_chats', 'name' => 'मेरी चैट'],
                    ['id' => 4, 'link' => 'logout', 'name' => 'साइन आउट'],
                ],
                'guest' => [
                    ['id' => 1, 'link' => 'coffee_on_wall', 'name' => 'दीवार पर कॉफी'],
                    ['id' => 2, 'link' => 'login', 'name' => 'लॉग इन / साइन अप'],
                ],
            ],
            14 => [
                'profile' => [
                    ['id' => 1, 'link' => 'profile', 'name' => 'میرا پروفائل'],
                    ['id' => 2, 'link' => 'my_rides', 'name' => 'میری سواریاں'],
                    ['id' => 3, 'link' => 'my_chats', 'name' => 'میری چیٹس'],
                    ['id' => 4, 'link' => 'logout', 'name' => 'سائن آؤٹ'],
                ],
                'guest' => [
                    ['id' => 1, 'link' => 'coffee_on_wall', 'name' => 'دیوار پر کافی'],
                    ['id' => 2, 'link' => 'login', 'name' => 'لاگ ان / سائن اپ'],
                ],
            ],
            15 => [
                'profile' => [
                    ['id' => 1, 'link' => 'profile', 'name' => 'Мой профиль'],
                    ['id' => 2, 'link' => 'my_rides', 'name' => 'Мои поездки'],
                    ['id' => 3, 'link' => 'my_chats', 'name' => 'Мои чаты'],
                    ['id' => 4, 'link' => 'logout', 'name' => 'Выйти'],
                ],
                'guest' => [
                    ['id' => 1, 'link' => 'coffee_on_wall', 'name' => 'Кофе на стене'],
                    ['id' => 2, 'link' => 'login', 'name' => 'Вход / Регистрация'],
                ],
            ],
            16 => [
                'profile' => [
                    ['id' => 1, 'link' => 'profile', 'name' => 'Мій профіль'],
                    ['id' => 2, 'link' => 'my_rides', 'name' => 'Мої поїздки'],
                    ['id' => 3, 'link' => 'my_chats', 'name' => 'Мої чати'],
                    ['id' => 4, 'link' => 'logout', 'name' => 'Вийти'],
                ],
                'guest' => [
                    ['id' => 1, 'link' => 'coffee_on_wall', 'name' => 'Кава на стіні'],
                    ['id' => 2, 'link' => 'login', 'name' => 'Вхід / Реєстрація'],
                ],
            ],
        ];

        foreach ($data as $languageId => $menus) {
            MenuDetail::updateOrCreate(
                ['menu_id' => $profileMenuId, 'language_id' => $languageId],
                ['menu_items' => $menus['profile']]
            );
            MenuDetail::updateOrCreate(
                ['menu_id' => $guestMenuId, 'language_id' => $languageId],
                ['menu_items' => $menus['guest']]
            );
        }
    }
}
