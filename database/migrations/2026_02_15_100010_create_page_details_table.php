<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
        CREATE TABLE `page_details`  (
          `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
          `page_id` bigint UNSIGNED NULL DEFAULT NULL,
          `language_id` bigint UNSIGNED NULL DEFAULT NULL,
          `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
          `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
          `created_at` timestamp NULL DEFAULT NULL,
          `updated_at` timestamp NULL DEFAULT NULL,
          PRIMARY KEY (`id`) USING BTREE,
          INDEX `page_details_page_id_foreign`(`page_id` ASC) USING BTREE,
          INDEX `page_details_language_id_foreign`(`language_id` ASC) USING BTREE,
          CONSTRAINT `page_details_language_id_foreign` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
          CONSTRAINT `page_details_page_id_foreign` FOREIGN KEY (`page_id`) REFERENCES `pages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE = InnoDB AUTO_INCREMENT = 51 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;
SQL);
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS `page_details`');
    }
};
