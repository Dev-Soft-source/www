<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
        CREATE TABLE `footer_settings`  (
          `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
          `facebook_icon` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
          `insta_icon` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
          `youtube_icon` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
          `twitter_icon` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
          `menu1` bigint UNSIGNED NULL DEFAULT NULL,
          `menu2` bigint UNSIGNED NULL DEFAULT NULL,
          `menu3` bigint UNSIGNED NULL DEFAULT NULL,
          `menu4` bigint UNSIGNED NULL DEFAULT NULL,
          `created_at` timestamp NULL DEFAULT NULL,
          `updated_at` timestamp NULL DEFAULT NULL,
          PRIMARY KEY (`id`) USING BTREE,
          INDEX `footer_settings_menu1_foreign`(`menu1` ASC) USING BTREE,
          INDEX `footer_settings_menu2_foreign`(`menu2` ASC) USING BTREE,
          INDEX `footer_settings_menu3_foreign`(`menu3` ASC) USING BTREE,
          INDEX `footer_settings_menu4_foreign`(`menu4` ASC) USING BTREE,
          CONSTRAINT `footer_settings_menu1_foreign` FOREIGN KEY (`menu1`) REFERENCES `menus` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
          CONSTRAINT `footer_settings_menu2_foreign` FOREIGN KEY (`menu2`) REFERENCES `menus` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
          CONSTRAINT `footer_settings_menu3_foreign` FOREIGN KEY (`menu3`) REFERENCES `menus` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
          CONSTRAINT `footer_settings_menu4_foreign` FOREIGN KEY (`menu4`) REFERENCES `menus` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;
SQL);
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS `footer_settings`');
    }
};
