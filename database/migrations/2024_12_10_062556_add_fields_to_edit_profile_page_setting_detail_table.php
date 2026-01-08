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
        Schema::table('edit_profile_page_setting_detail', function (Blueprint $table) {
            $table->string('joined_label')->nullable();
            $table->string('passenger_label')->nullable();
            $table->string('year_old_label')->nullable();
            $table->string('vehicle_info_label')->nullable();
            $table->string('replied_label')->nullable();
            $table->string('response_label')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('edit_profile_page_setting_detail', function (Blueprint $table) {

            $table->dropColumn('joined_label');
            $table->dropColumn('passenger_label');
            $table->dropColumn('year_old_label');
            $table->dropColumn('vehicle_info_label');
            $table->dropColumn('replied_label');
            $table->dropColumn('response_label');

        });
    }
};
