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
        Schema::table('mobile_post_ride_setting_detail', function (Blueprint $table) {
            $table->text('recurring_trips_placeholder')->after('recurring_trips_label')->nullable();
            $table->text('existing_label')->after('add_vehicle_label')->nullable();
            $table->text('car_type_label')->after('liscense_label')->nullable();
            $table->text('gas_car_label')->after('hybrid_car_label')->nullable();
            $table->text('cancellation_policy_label')->after('payment_methods_label')->nullable();
            $table->text('cancellation_policy_label1')->after('cancellation_policy_label')->nullable();
            $table->text('cancellation_policy_label2')->after('cancellation_policy_label1')->nullable();
            $table->text('disclaimers_description1')->after('disclaimers_description')->nullable();
            $table->text('disclaimers_description2')->after('disclaimers_description1')->nullable();
            $table->text('disclaimers_description3')->after('disclaimers_description2')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mobile_post_ride_setting_detail', function (Blueprint $table) {
            //
        });
    }
};
