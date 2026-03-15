<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('booking_page_setting_detail', function (Blueprint $table) {
            $table->text('booking_disclaimer_firm_tooltip')->nullable()->after('booking_disclaimer_firm');
        });
    }

    public function down()
    {
        Schema::table('booking_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn('booking_disclaimer_firm_tooltip');
        });
    }
};
