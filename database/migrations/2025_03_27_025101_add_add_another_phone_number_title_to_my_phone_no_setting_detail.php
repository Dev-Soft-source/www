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
            $table->text('add_another_phone_number_title')->nullable();
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
            $table->dropColumn(['add_another_phone_number_title']);
        });
    }
};
