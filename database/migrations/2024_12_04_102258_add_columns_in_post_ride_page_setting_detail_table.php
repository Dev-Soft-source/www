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
        Schema::table('post_ride_page_setting_detail', function (Blueprint $table) {
            $table->text('recurring_type_label')->after('recurring_label')->nullable();
            $table->text('recurring_trips_label')->after('recurring_type_label')->nullable();
            $table->text('recurring_trips_placeholder')->after('recurring_trips_label')->nullable();
            $table->text('existing_label')->after('add_vehicle_label')->nullable();
            $table->text('car_type_label')->after('liscense_label')->nullable();
            $table->text('cancellation_policy_label')->after('payment_methods_label')->nullable();
            $table->text('cancellation_policy_label1')->after('cancellation_policy_label')->nullable();
            $table->text('cancellation_policy_label2')->after('cancellation_policy_label1')->nullable();
            $table->text('app_disclaimers_description1')->after('disclaimers_label')->nullable();
            $table->text('app_disclaimers_description2')->after('app_disclaimers_description1')->nullable();
            $table->text('app_disclaimers_description3')->after('app_disclaimers_description2')->nullable();
            $table->text('app_disclaimers_description4')->after('app_disclaimers_description3')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('post_ride_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn(['app_disclaimers_description4', 'app_disclaimers_description3', 'app_disclaimers_description2', 'app_disclaimers_description1',
                'cancellation_policy_label2', 'cancellation_policy_label1', 'cancellation_policy_label', 'car_type_label', 'existing_label',
                'recurring_trips_placeholder', 'recurring_trips_label', 'recurring_type_label'
            ]);
        });
    }
};
