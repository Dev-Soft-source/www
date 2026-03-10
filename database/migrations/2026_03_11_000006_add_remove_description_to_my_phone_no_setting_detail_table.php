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
            $table->text('remove_description')->nullable()->after('primary_number_label');
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
            $table->dropColumn('remove_description');
        });
    }
};
