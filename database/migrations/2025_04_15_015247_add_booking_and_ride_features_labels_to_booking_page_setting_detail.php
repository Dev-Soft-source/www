<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('booking_page_setting_detail', function (Blueprint $table) {
            $table->text('booking_label')->nullable()->after('pay_label');
            $table->text('ride_features_label')->nullable()->after('booking_label');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('booking_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn(['booking_label', 'ride_features_label']);
        });
    }
};
