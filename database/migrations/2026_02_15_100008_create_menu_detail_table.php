<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
        CREATE TABLE `menu_detail`  (
          `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
          `menu_id` bigint UNSIGNED NULL DEFAULT NULL,
          `language_id` bigint UNSIGNED NULL DEFAULT NULL,
          `menu_items` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
          PRIMARY KEY (`id`) USING BTREE,
          INDEX `menu_detail_menu_id_foreign`(`menu_id` ASC) USING BTREE,
          INDEX `menu_detail_language_id_foreign`(`language_id` ASC) USING BTREE,
          CONSTRAINT `menu_detail_language_id_foreign` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
          CONSTRAINT `menu_detail_menu_id_foreign` FOREIGN KEY (`menu_id`) REFERENCES `menus` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE = InnoDB AUTO_INCREMENT = 51 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;
SQL);
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS `menu_detail`');
    }
};
