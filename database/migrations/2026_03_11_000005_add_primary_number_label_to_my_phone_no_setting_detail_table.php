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
            $table->text('primary_number_label')->nullable()->after('set_as_default_label');
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
            $table->dropColumn('primary_number_label');
        });
    }
};
