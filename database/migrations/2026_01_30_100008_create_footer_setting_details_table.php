<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'EOS'
CREATE TABLE `footer_setting_details`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `footer_setting_id` bigint UNSIGNED NULL DEFAULT NULL,
  `language_id` bigint UNSIGNED NULL DEFAULT NULL,
  `main_text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `section_1_title` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `section_2_title` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `section_3_title` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `section_4_title` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `copy_right_text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `footer_setting_details_footer_setting_id_foreign`(`footer_setting_id` ASC) USING BTREE,
  INDEX `footer_setting_details_language_id_foreign`(`language_id` ASC) USING BTREE,
  CONSTRAINT `footer_setting_details_footer_setting_id_foreign` FOREIGN KEY (`footer_setting_id`) REFERENCES `footer_settings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `footer_setting_details_language_id_foreign` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 11 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC
EOS);
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS `footer_setting_details`');
    }
};
