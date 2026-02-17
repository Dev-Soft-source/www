<?php

namespace Database\Seeders;

use App\Models\MenuDetail;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuDetailNavbarSeeder extends Seeder
{
    /**
     * Seeds Profile dropdown (menu_id 6) and Guest nav (menu_id 7) for English and Spanish.
     * Ensures menus 6 and 7 exist, then seeds menu_detail.
     */
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

        $profileEn = [
            ['id' => 1, 'link' => 'profile', 'name' => 'My Profile'],
            ['id' => 2, 'link' => 'my_rides', 'name' => 'My Rides'],
            ['id' => 3, 'link' => 'my_chats', 'name' => 'My Chats'],
            ['id' => 4, 'link' => 'logout', 'name' => 'Sign out'],
        ];
        $profileEs = [
            ['id' => 1, 'link' => 'profile', 'name' => 'Mi perfil'],
            ['id' => 2, 'link' => 'my_rides', 'name' => 'Mis viajes'],
            ['id' => 3, 'link' => 'my_chats', 'name' => 'Mis chats'],
            ['id' => 4, 'link' => 'logout', 'name' => 'Cerrar sesión'],
        ];
        $guestEn = [
            ['id' => 1, 'link' => 'coffee_on_wall', 'name' => 'Coffee on the Wall'],
            ['id' => 2, 'link' => 'login', 'name' => 'Log in / Sign up'],
        ];
        $guestEs = [
            ['id' => 1, 'link' => 'coffee_on_wall', 'name' => 'Café en la pared'],
            ['id' => 2, 'link' => 'login', 'name' => 'Iniciar sesión / Registrarse'],
        ];

        MenuDetail::updateOrCreate(
            ['menu_id' => $profileMenuId, 'language_id' => 1],
            ['menu_items' => $profileEn]
        );
        MenuDetail::updateOrCreate(
            ['menu_id' => $profileMenuId, 'language_id' => 9],
            ['menu_items' => $profileEs]
        );
        MenuDetail::updateOrCreate(
            ['menu_id' => $guestMenuId, 'language_id' => 1],
            ['menu_items' => $guestEn]
        );
        MenuDetail::updateOrCreate(
            ['menu_id' => $guestMenuId, 'language_id' => 9],
            ['menu_items' => $guestEs]
        );
    }
}
