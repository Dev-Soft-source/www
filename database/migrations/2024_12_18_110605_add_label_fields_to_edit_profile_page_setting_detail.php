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
            $table->text('prefer_no_to_say_label')->nullable();
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

            $table->dropColumn('prefer_no_to_say_label');

        });
    }
};
