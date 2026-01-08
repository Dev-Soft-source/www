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
        Schema::table('student_page_setting_detail', function (Blueprint $table) {
            $table->text('page_image')->after('sub_heading')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('student_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn(['page_image']);
        });
    }
};
