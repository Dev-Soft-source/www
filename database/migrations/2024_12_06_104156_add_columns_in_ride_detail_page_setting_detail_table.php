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
        Schema::table('ride_detail_page_setting_detail', function (Blueprint $table) {
            $table->text('pickup_dropoff_info_heading')->after('per_seat_label')->nullable();
            $table->text('pickup_label')->after('pickup_dropoff_info_heading')->nullable();
            $table->text('dropoff_label')->after('pickup_label')->nullable();
            $table->text('description_label')->after('dropoff_label')->nullable();
            $table->text('booking_type_label')->after('payment_method_label')->nullable();
            $table->text('cancellation_policy_label')->after('booking_type_label')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ride_detail_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn(['cancellation_policy_label', 'booking_type_label', 'description_label', 'dropoff_label', 'pickup_label', 'pickup_dropoff_info_heading']);
        });
    }
};
