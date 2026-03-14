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
            $table->string('upload_another_image_btn_label')->nullable()->after('upload_new_image_btn_label');
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
            $table->dropColumn('upload_another_image_btn_label');
        });
    }
};
