<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * luggage_checkbox_label1 exists on fresh installs (2023_11_08_065840_create_post_ride_page_setting).
     * This migration only adds the column when it is missing (e.g. legacy or partial schemas).
     */
    public function up(): void
    {
        if (!Schema::hasColumn('post_ride_page_setting_detail', 'luggage_checkbox_label1')) {
            Schema::table('post_ride_page_setting_detail', function (Blueprint $table) {
                $table->text('luggage_checkbox_label1')->nullable();
            });
        }
    }

    /**
     * Intentionally empty: dropping here would remove a column that may predate this migration.
     */
    public function down(): void
    {
    }
};
