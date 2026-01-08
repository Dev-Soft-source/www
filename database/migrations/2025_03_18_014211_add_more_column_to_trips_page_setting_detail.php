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
        Schema::table('trips_page_setting_detail', function (Blueprint $table) {
            $table->text('remove_passenger_text')->nullable();
            $table->text('block_temporarily_label')->nullable();
            $table->text('block_permanently_label')->nullable();
            $table->text('remove_day_placeholder')->nullable();
            $table->text('driver_remove_reason_label')->nullable();
            $table->text('passenger_remove_reason_label')->nullable();
            $table->text('passenger_cancel_sure_message')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trips_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn(['remove_passenger_text']);
            $table->dropColumn(['block_temporarily_label']);
            $table->dropColumn(['block_permanently_label']);
            $table->dropColumn(['remove_day_placeholder']);
            $table->dropColumn(['driver_remove_reason_label']);
            $table->dropColumn(['passenger_remove_reason_label']);
            $table->dropColumn(['passenger_cancel_sure_message']);

        });
    }
};
