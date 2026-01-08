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
        Schema::table('my_phone_no_setting_detail', function (Blueprint $table) {
            $table->string('phone_number_label_web')->nullable();
            $table->string('country_code_label_web')->nullable();
            $table->string('country_id_label_web')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('my_phone_no_setting_detail', function (Blueprint $table) {

            $table->dropColumn('country_code_label_web');
            $table->dropColumn('phone_number_label_web');
            $table->dropColumn('country_id_label_web');
        });
    }
};
