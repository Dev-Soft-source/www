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
        Schema::table('step2_page_setting_detail', function (Blueprint $table) {
            $table->text('mobile_photo_label')->nullable();
            $table->text('mobile_choose_file_label')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('step2_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn(['mobile_choose_file_label', 'mobile_photo_label']);
        });
    }
};
