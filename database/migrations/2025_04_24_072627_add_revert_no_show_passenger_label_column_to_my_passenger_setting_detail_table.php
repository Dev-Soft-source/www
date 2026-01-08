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
            $table->text('revert_no_show_passenger_label')->nullable();
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
            $table->dropColumn('revert_no_show_passenger_label');
        });
    }
};
