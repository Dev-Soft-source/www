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
        Schema::table('my_student_card_setting_detail', function (Blueprint $table) {
            $table->string('update_button_text')->nullable();
            $table->string('upload_new_image_btn_label')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('my_student_card_setting_detail', function (Blueprint $table) {
            $table->dropColumn('update_button_text');
            $table->dropColumn('upload_new_image_btn_label');
        });
    }
};
