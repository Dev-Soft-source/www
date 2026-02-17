<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
        CREATE TABLE `jobs`  (
          `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
          `queue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
          `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
          `attempts` tinyint UNSIGNED NOT NULL,
          `reserved_at` int UNSIGNED NULL DEFAULT NULL,
          `available_at` int UNSIGNED NOT NULL,
          `created_at` int UNSIGNED NOT NULL,
          PRIMARY KEY (`id`) USING BTREE,
          INDEX `jobs_queue_index`(`queue` ASC) USING BTREE
        ) ENGINE = InnoDB AUTO_INCREMENT = 11196 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;
SQL);
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS `jobs`');
    }
};
