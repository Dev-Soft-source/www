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
            $table->text('co_passenger_label')->nullable()->after('ride_features_label');
            $table->text('payment_method_label')->nullable()->after('co_passenger_label');
            $table->text('luggage_label')->nullable()->after('payment_method_label');
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
            $table->dropColumn(['co_passenger_label', 'payment_method_label','luggage_label']);
        });
    }
};
