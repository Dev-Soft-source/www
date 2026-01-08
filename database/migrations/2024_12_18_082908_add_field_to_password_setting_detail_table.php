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
        Schema::table('password_setting_detail', function (Blueprint $table) {
            $table->text('mobile_password_description_text')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('password_setting_detail', function (Blueprint $table) {

          $table->dropColumn('mobile_password_description_text');

        });
    }
};
