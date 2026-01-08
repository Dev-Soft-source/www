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
        Schema::table('my_passenger_setting_detail', function (Blueprint $table) {
           $table->text('co_passenger_main_heading')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('my_passenger_setting_detail', function (Blueprint $table) {

          $table->dropColumn('co_passenger_main_heading');

        });
    }
};
