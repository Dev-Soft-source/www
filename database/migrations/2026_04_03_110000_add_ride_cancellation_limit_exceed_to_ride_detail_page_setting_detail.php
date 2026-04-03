<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ride_detail_page_setting_detail', function (Blueprint $table) {
            $table->text('ride_cancellation_limit_exceed')->nullable()->after('ride_cancelled_label');
        });
    }

    public function down(): void
    {
        Schema::table('ride_detail_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn('ride_cancellation_limit_exceed');
        });
    }
};
