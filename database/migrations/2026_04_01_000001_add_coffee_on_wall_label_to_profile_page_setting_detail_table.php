<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profile_page_setting_detail', function (Blueprint $table) {
            $table->string('coffee_on_wall_label')->nullable()->after('contact_proximaride_label');
        });
    }

    public function down(): void
    {
        Schema::table('profile_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn('coffee_on_wall_label');
        });
    }
};
